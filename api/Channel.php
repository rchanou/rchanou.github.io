<?php

class Channel
{
    public $restler;
		private $channelImageUrl;

    function __construct(){
        header('Access-Control-Allow-Origin: *');
        $this->host = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ? 'http://' . $_SERVER['HTTP_HOST'] : 'https://' . $_SERVER['HTTP_HOST'];
        $this->channelImageUrl = empty($GLOBALS['channelImageUrl']) ? $this->host . '/sp_admin/ScreenImages/' : $this->host . $GLOBALS['channelImageUrl']; // Set if not defined in configuration
				$this->channelSlideUrl = empty($GLOBALS['channelSlideUrl']) ? $this->host . '/api/slides/' : $this->host . $GLOBALS['channelSlideUrl']; // Set if not defined in configuration
				$this->speedScreenBackgroundUrl = $this->host . '/PrivateWWW/SpeedScreen.gif';
    }

    protected function index($channelId, $sub = null) {
			die('GETINDEX' . $channelId);
		}

		protected function html($htmlId) {
			die('GETHTML' . $htmlId);
			
			// Get screen
			$tsql = "SELECT Text0 FROM ScreenTemplateDetail WHERE ID = ?";
			$tsql_params = array(&$htmlId);
			$slide = $this->run_query($tsql, $tsql_params);
			return $slide[0];
		}

		protected function get($channelId)
    {								
				if(!is_numeric($channelId)) throw new RestException(412,'Channel ID must be numeric');
				
				// Base path for video URLs
        $baseVideoUrl = $this->channelSlideUrl . 'video.html?videoUrl=';
				
				// Get screen
				$tsql = "SELECT * FROM ScreenTemplate WHERE templateid = ? AND deleted = 0";
				$tsql_params = array(&$channelId);
        $screen = $this->run_query($tsql, $tsql_params);
				$screen = $screen[0];

        // Get slides
				$tsql = "SELECT * FROM ScreenTemplateDetail WHERE templateid = ? and enable = 1 ORDER BY seq";
        $tsql_params = array(&$channelId);
        $slides = $this->run_query($tsql, $tsql_params);

				// Setup output
				$output = array();
				$output['name'] = $screen['TemplateName'];
				$output['options'] = array(
					'backgroundImageUrl' => $this->host . '/privatewww/speedscreen.gif',
					'sizeX' => $screen['SizeX'],
					'sizeY' => $screen['SizeY']
					);
				$output['lineup'] = array();

				// Add channel-wide scoreboard
				// Note: Scoreboards can be specified "channel-wide" here or as slide type "21: newScoreboard" (below)
				if($screen['ShowScoreboard'] == 1) {
					$output['lineup'][] = array(
						'type' => 'scoreboard',
						'options' => array(
							'trackId' => (int)$screen['ScoreBoardTrackNo'],
							'postRaceIdleTime' => $screen['IdleTime']*1000
							)
						);
				}

				// Add slides if the screen idle time is not "Infinite" (86400)		
				if($screen['IdleTime'] !== 86400) { 
					foreach($slides as $slide) {
						switch($slide['TypeID']) {
							// Text slide - 1
							case 1:
								$output['lineup'][] = array('type' => 'text', 'options' => array('line0' => $slide['Text0'], 'line1' => $slide['Text1'], 'line2' => $slide['Text2'], 'line3' => $slide['Text3'], 'line4' => $slide['Text4'], 'line5' => $slide['Text5'], 'line6' => $slide['Text6'], 'duration' => $slide['TimeInSecond']*1000));
								break;						

							// Image slide - 5
							case 2:
								$output['lineup'][] = array('type' => 'image', 'options' => array('url' => $this->channelImageUrl . $slide['Text0'], 'duration' => $slide['TimeInSecond']*1000));
								break;							

							// Top Time of the Day - 5
							case 3:
								//?trackId=1&dateSpan=day&backgroundUrl=http://www.w8themes.com/wp-content/uploads/2013/11/White-Background-Wallpaper.jpg
								
								// Build path to Top Time TODO Get Background Url
								$url = $this->channelSlideUrl . 'top-times.html?trackId=' . $slide['TrackNo'] . '&speedLevel=' . $slide['Text1'] . '&range=day&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
								$output['lineup'][] = array('type' => 'url', 'options' => array('url' => $url, 'startAtPosition' => (int)$slide['Text0'], 'duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo'], 'speedLevel' =>  (int)$slide['Text1'], 'backgroundUrl' => $this->speedScreenBackgroundUrl, 'type' => 'topTimeOfDay'));
								break;

							// Top Time of the Week - 5
							case 4:
								//?trackId=1&dateSpan=week&backgroundUrl=http://www.w8themes.com/wp-content/uploads/2013/11/White-Background-Wallpaper.jpg
								
								// Build path to Top Time TODO Get Background Url
								$url = $this->channelSlideUrl . 'top-times.html?trackId=' . $slide['TrackNo'] . '&speedLevel=' . $slide['Text1'] . '&range=week&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
								$output['lineup'][] = array('type' => 'url', 'options' => array('url' => $url, 'startAtPosition' => (int)$slide['Text0'], 'duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo'], 'speedLevel' =>  (int)$slide['Text1'], 'backgroundUrl' => $this->speedScreenBackgroundUrl, 'type' => 'topTimeOfWeek'));
								break;

							// Top Time of the Month - 5
							case 5:								
								//?trackId=1&dateSpan=month&backgroundUrl=http://www.w8themes.com/wp-content/uploads/2013/11/White-Background-Wallpaper.jpg
								
								// Build path to Top Time TODO Get Background Url
								$url = $this->channelSlideUrl . 'top-times.html?trackId=' . $slide['TrackNo'] . '&speedLevel=' . $slide['Text1'] . '&range=month&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
								$output['lineup'][] = array('type' => 'url', 'options' => array('url' => $url, 'startAtPosition' => (int)$slide['Text0'], 'duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo'], 'speedLevel' =>  (int)$slide['Text1'], 'backgroundUrl' => $this->speedScreenBackgroundUrl, 'type' => 'topTimeOfMonth'));
								break;

							// Top Time of the Year - 4
							case 6:
								$url = $this->channelSlideUrl . 'top-times.html?trackId=' . $slide['TrackNo'] . '&speedLevel=' . $slide['Text1'] . '&range=year&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
								$output['lineup'][] = array('type' => 'url', 'options' => array('url' => $url, 'startAtPosition' => (int)$slide['Text0'], 'duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo'], 'speedLevel' =>  (int)$slide['Text1'], 'backgroundUrl' => $this->speedScreenBackgroundUrl, 'type' => 'topTimeOfYear'));
								break;

							// Top RPM Overall - 5
							case 7:
								$output['lineup'][] = array('type' => 'topRPM', 'options' => array('startAtPosition' => (int)$slide['Text0'], 'duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo']));
								break;

							// Most Improved RPM of the Month - 4
							case 8:
								$output['lineup'][] = array('type' => 'mostImprovedRPMOfMonth', 'options' => array('startAtPosition' => (int)$slide['Text0'], 'duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo']));
								break;

							// Most Improved RPM of the Year - 3 
							case 9:
								$output['lineup'][] = array('type' => 'mostImprovedRPMOfYear', 'options' => array('startAtPosition' => (int)$slide['Text0'], 'duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo']));
								break;

							// Schedule - 4
							case 10:
								$output['lineup'][] = array('type' => 'schedule', 'options' => array('duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo']));
								break;

							// 11 Video - 3 TODO: Not implemented
							// 12 Camera TODO: Not implemented

							// Next Racers - 5
							case 13:
								$url = $this->channelSlideUrl . 'up-next.html?trackId=' . $slide['TrackNo'] . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
								$output['lineup'][] = array('type' => 'url', 'options' => array('url' => $url, 'duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo'], 'backgroundUrl' => $this->speedScreenBackgroundUrl, 'type' => 'nextRacers'));
								break;						

							// Next, Next Racers - 5
							case 14:
								$url = $this->channelSlideUrl . 'up-next.html?offset=1&trackId=' . $slide['TrackNo'] . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
								$output['lineup'][] = array('type' => 'url', 'options' => array('url' => $url, 'duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo'], 'backgroundUrl' => $this->speedScreenBackgroundUrl, 'type' => 'nextNextRacers'));
								break;

							// Last Winner with Picture - 4
							case 15:
								$output['lineup'][] = array('type' => 'lastWinnerWithPicture', 'options' => array('duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo']));
								break;			

							// Top Time of Day (with Picture) - 4
							case 16:
								$output['lineup'][] = array('type' => 'topTimeOfDayWithPicture', 'options' => array('duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo']));
								break;

							// Next Heat (with Picture) - 4
							case 17:
								$url = $this->channelSlideUrl . 'up-next-pictures.html?trackId=' . $slide['TrackNo'] . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
								$output['lineup'][] = array('type' => 'url', 'options' => array('url' => $url, 'duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo'], 'backgroundUrl' => $this->speedScreenBackgroundUrl, 'type' => 'upNextWithPicture'));
								break;

							// HTML - 5
							case 18:
								// It's a VIDEO URL (.m4v, .mp4, .webm, .ogv)
								if(filter_var($slide['Text0'], FILTER_VALIDATE_URL) && (substr($slide['Text0'], -4) == '.m4v' || substr($slide['Text0'], -4) == '.mp4' || substr($slide['Text0'], -5) == '.webm' || substr($slide['Text0'], -4) == '.ogv')) {


									// Build path to video
									$videoUrl = $baseVideoUrl . urlencode($slide['Text0']);
									$output['lineup'][] = array('type' => 'url', 'options' => array('url' => $videoUrl, 'originalUrl' => $slide['Text0'], 'duration' => $slide['TimeInSecond']*1000));
								
								
								// It's a URL
								} elseif(filter_var($slide['Text0'], FILTER_VALIDATE_URL)){ 
									
									$output['lineup'][] = array('type' => 'url', 'options' => array('url' => $slide['Text0'], 'original' => $slide['Text0'], 'duration' => $slide['TimeInSecond']*1000));
								
								// It's a HTML block
								} else {
									
									$output['lineup'][] = array('id' => $slide['ID'], 'type' => 'html', 'options' => array('html' => $slide['Text0'], 'duration' => $slide['TimeInSecond']*1000));
								}
								break;


							// 19 Flash: Not implemented

							// Event Screen - 3
							case 20:
								$output['lineup'][] = array('type' => 'eventScreen', 'options' => array('duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo']));
								break;

							// New Scoreboard - 5
							case 21:								
								// Ensure we haven't already added this scoreboard "channel-wide" above			
								if($screen['ShowScoreboard'] == 0 ||
									($screen['ShowScoreboard'] == 1 && (int)$screen['ScoreBoardTrackNo'] != (int)$slide['TrackNo'])) {
									$output['lineup'][] = array(
										'type' => 'scoreboard',
										'options' => array(
											'trackId' => (int)$slide['TrackNo'],
											'postRaceIdleTime' => $slide['TimeInSecond']*1000
											)
										);
								}
								break;

							// Previous Race Results - 4
							case 22:
								$output['lineup'][] = array('type' => 'previousRaceResults', 'options' => array('startAtPosition' => (int)$slide['Text0'], 'duration' => $slide['TimeInSecond']*1000));
								break;					

							// Previous, Previous Race Results - 3
							case 23:
								$output['lineup'][] = array('type' => 'previousPreviousRaceResults', 'options' => array('startAtPosition' => (int)$slide['Text0'], 'duration' => $slide['TimeInSecond']*1000));
								break;
						}
					}
				}

				// Hash the lineup
				$output['hash'] = md5(serialize($output));

        return $output;
    }

    private function run_query($tsql, $params = array()) {
        $tsql_original = $tsql . ' ';

        // Connect
        try {
            $conn = new PDO( "sqlsrv:server=(local) ; Database=ClubSpeedV8", "", "");
            $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );

            // Prepare statement
            $stmt = $conn->prepare($tsql);

            // Execute statement
            $stmt->execute($params);

            // Put in array
            $output = $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch(Exception $e) {
            die('Exception Message:'  . $e->getMessage()  . '<br/>(Line: '. $e->getLine() . ')' . '<br/>Passed query: ' . $tsql_original . '<br/>Parameters passed: ' . print_r($params,true));
        }

        return $output;
    }
}