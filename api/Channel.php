<?php

class Channel
{
    public $restler;
    private $channelImageUrl;

    function __construct(){
        // header('Access-Control-Allow-Origin: *');
        $this->host = empty($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == 'off' ? 'http://' . $_SERVER['HTTP_HOST'] : 'https://' . $_SERVER['HTTP_HOST'];
        $this->channelImageUrl = empty($GLOBALS['channelImageUrl']) ? $this->host . '/sp_admin/ScreenImages/' : $this->host . $GLOBALS['channelImageUrl']; // Set if not defined in configuration
        $this->channelSlideUrl = empty($GLOBALS['channelSlideUrl']) ? $this->host . '/api/slides/' : $this->host . $GLOBALS['channelSlideUrl']; // Set if not defined in configuration
        $this->speedScreenBackgroundUrl = $this->host . '/assets/cs-speedscreen/images/background_1080p.jpg';
    }

    public function index($channelId, $sub = null) {
        die('GETINDEX' . $channelId);
    }

    public function html($htmlId) {
        die('GETHTML' . $htmlId);

        // Get screen
        $tsql = "SELECT Text0 FROM ScreenTemplateDetail WHERE ID = ?";
        $tsql_params = array(&$htmlId);
        $slide = $this->run_query($tsql, $tsql_params);
        return $slide[0];
    }

    public function get($channelId) {
        if (!\ClubSpeed\Security\Authenticate::publicAccess()) {
            throw new RestException(401, "Invalid authorization!");
        }

        if ($channelId == 'all')
        {
            $tsql = "SELECT * FROM ScreenTemplate WHERE deleted = 0";
            $screens = $this->run_query($tsql);
            $output = array();
            foreach($screens as $currentScreen)
            {
                $channelId = $currentScreen["TemplateID"];
                $channelName = $currentScreen["TemplateName"];
                $output[$channelId] = array('channelId' => $channelId,
                                            'channelName' => $channelName);
            }
            return $output;
        }

        if(!is_numeric($channelId)) throw new RestException(412,'Channel ID must be numeric');

        // Base path for video URLs
        $baseVideoUrl = $this->channelSlideUrl . 'video.html?videoUrl=';

        // Get screen
        $tsql = "SELECT * FROM ScreenTemplate WHERE templateid = ? AND deleted = 0";
        $tsql_params = array(&$channelId);
        $screen = $this->run_query($tsql, $tsql_params);
        if(!isset($screen[0]))
        {
            throw new RestException(404, "Channel not found!");
        }
        $screen = $screen[0];

        // Get slides
        $tsql = "SELECT * FROM ScreenTemplateDetail WHERE templateid = ? and enable = 1 ORDER BY seq, ID";
        $tsql_params = array(&$channelId);
        $slides = $this->run_query($tsql, $tsql_params);

        // Setup output
        $output = array();
        $output['name'] = $screen['TemplateName'];
        $output['options'] = array(
            'backgroundImageUrl' => $this->host . '/assets/cs-speedscreen/images/background_1080p.jpg',
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
                    'trackId'           => (int)$screen['ScoreBoardTrackNo'],
                    'postRaceIdleTime'  => $screen['IdleTime']*1000
                )
            );
        }

        // Add slides if the screen idle time is not "Infinite" (86400)
        if($screen['IdleTime'] !== 86400) {
            // die(print_r($slides));
            foreach($slides as $slide) {
                switch($slide['TypeID']) {
                    // Text slide - 1
                    case 1:
                        $url = $this->channelSlideUrl
                            . 'text.html'
                            . '?line0='         . $slide['Text0']
                            . '&line1='         . $slide['Text1']
                            . '&line2='         . $slide['Text2']
                            . '&line3='         . $slide['Text3']
                            . '&line4='         . $slide['Text4']
                            . '&line5='         . $slide['Text5']
                            . '&line6='         . $slide['Text6']
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'duration'          => $slide['TimeInSecond']*1000,
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'type'              => 'text',
                                'line0'             => $slide['Text0'],
                                'line1'             => $slide['Text1'],
                                'line2'             => $slide['Text2'],
                                'line3'             => $slide['Text3'],
                                'line4'             => $slide['Text4'],
                                'line5'             => $slide['Text5'],
                                'line6'             => $slide['Text6']
                            )
                        );
                        break;

                    // Image slide - 5
                    case 2:
                        $output['lineup'][] = array(
                            'type' => 'image',
                            'options' => array(
                                'url'       => $this->channelImageUrl . $slide['Text0'],
                                'duration'  => $slide['TimeInSecond']*1000
                            )
                        );
                        break;

                    // Top Time of the Day - 5
                    case 3:
                        //?trackId=1&dateSpan=day&backgroundUrl=http://www.w8themes.com/wp-content/uploads/2013/11/White-Background-Wallpaper.jpg

                        // Build path to Top Time TODO Get Background Url
                        $url = $this->channelSlideUrl
                            . 'top-times.html'
                            . '?trackId='       . $slide['TrackNo']
                            . '&speedLevel='    . $slide['Text1']
                            . '&range='         . 'day'
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'startAtPosition'   => (int)$slide['Text0'],
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'speedLevel'        => (int)$slide['Text1'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'type'              => 'topTimeOfDay'
                            )
                        );
                        break;

                    // Top Time of the Week - 5
                    case 4:
                        //?trackId=1&dateSpan=week&backgroundUrl=http://www.w8themes.com/wp-content/uploads/2013/11/White-Background-Wallpaper.jpg

                        // Build path to Top Time TODO Get Background Url
                        $url = $this->channelSlideUrl
                            . 'top-times.html'
                            . '?trackId='       . $slide['TrackNo']
                            . '&speedLevel='    . $slide['Text1']
                            . '&range='         . 'week'
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'startAtPosition'   => (int)$slide['Text0'],
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'speedLevel'        => (int)$slide['Text1'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'type'              => 'topTimeOfWeek'
                            )
                        );
                        break;

                    // Top Time of the Month - 5
                    case 5:
                        //?trackId=1&dateSpan=month&backgroundUrl=http://www.w8themes.com/wp-content/uploads/2013/11/White-Background-Wallpaper.jpg
                        $url = $this->channelSlideUrl
                            . 'top-times.html'
                            . '?trackId='       . $slide['TrackNo']
                            . '&speedLevel='    . $slide['Text1']
                            . '&range='         . 'month'
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'startAtPosition'   => (int)$slide['Text0'],
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'speedLevel'        => (int)$slide['Text1'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'type'              => 'topTimeOfMonth'
                            )
                        );
                        break;

                    // Top Time of the Year - 4
                    case 6:
                        $url = $this->channelSlideUrl
                            . 'top-times.html'
                            . '?trackId='       . $slide['TrackNo']
                            . '&speedLevel='    . $slide['Text1']
                            . '&range='         . 'year'
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'startAtPosition'   => (int)$slide['Text0'],
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'speedLevel'        => (int)$slide['Text1'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'type'              => 'topTimeOfYear'
                            )
                        );
                        break;

                    // Top RPM Overall - 5
                    case 7:
                        // TrackNo = trackId
                        // Text0 = startAtPosition (for 1-10 stores 1, for 11-20 stores 11, etc)
                        // Text1 = speedLevel

                        $url = $this->channelSlideUrl
                            . 'top-proskill.html'
                            . '?trackId='       . (int)$slide['TrackNo']
                            . '&speedLevel='    . (int)$slide['Text1']
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'startAtPosition'   => (int)$slide['Text0'],
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'speedLevel'        => (int)$slide['Text1'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'type'              => 'topProskill'
                            )
                        );
                        break;

                    // Most Improved RPM of the Month - 4
                    case 8:
                        // TrackNo = trackId
                        // Text0 = startAtPosition (for 1-10 stores 1, for 11-20 stores 11, etc)
                        // Text1 = speedLevel

                        // original
                        // $output['lineup'][] = array('type' => 'mostImprovedRPMOfMonth', 'options' => array('startAtPosition' => (int)$slide['Text0'], 'duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo']));

                        $url = $this->channelSlideUrl
                            . 'most-improved-proskill.html'
                            . '?trackId='       . (int)$slide['TrackNo']
                            . '&speedLevel='    . (int)$slide['Text1']
                            . '&range='         . 'month'
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'startAtPosition'   => (int)$slide['Text0'],
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'speedLevel'        => (int)$slide['Text1'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'type'              => 'mostImprovedRPMOfMonth'
                            )
                        );
                        break;

                    // Most Improved RPM of the Year - 3
                    case 9:
                        // TrackNo = trackId
                        // Text0 = startAtPosition (for 1-10 stores 1, for 11-20 stores 11, etc)
                        // Text1 = speedLevel

                        // original
                        // $output['lineup'][] = array('type' => 'mostImprovedRPMOfYear', 'options' => array('startAtPosition' => (int)$slide['Text0'], 'duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo']));

                        $url = $this->channelSlideUrl
                            . 'most-improved-proskill.html'
                            . '?trackId='       . (int)$slide['TrackNo']
                            . '&speedLevel='    . (int)$slide['Text1']
                            . '&range='         . 'year'
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'startAtPosition'   => (int)$slide['Text0'],
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'speedLevel'        => (int)$slide['Text1'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'type'              => 'mostImprovedRPMOfYear'
                            )
                        );
                        break;

                    // Schedule - 4
                    case 10:
                        // TrackNo = trackId
                        // Text1 = speedLevel

                        // original
                        // $output['lineup'][] = array('type' => 'schedule', 'options' => array('duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo']));

                        $url = $this->channelSlideUrl
                            . 'schedule.html'
                            . '?trackId='       . (int)$slide['TrackNo']
                            . '&speedLevel='    . (int)$slide['Text1']
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'speedLevel'        => (int)$slide['Text1'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'type'              => 'schedule'
                            )
                        );
                        break;

                    // 11 Video - 3 TODO: Not implemented
                    // 12 Camera TODO: Not implemented

                    // Next Racers
                    case 13: // Next Racers - 5 (and with picture)
                    case 17: // Next Heat (with Picture) - 4

                        $slideUrl = ($slide['Text2'] == 1) ? 'up-next-pictures.html' : 'up-next.html';
                        $url = $this->channelSlideUrl
                            .  $slideUrl
                            . '?trackId='       . $slide['TrackNo']
                            . '&speedLevel='    . $slide['Text1']
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);

                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'speedLevel'        => (int)$slide['Text1'],
                                'showPicture'       => $slide['Text2'] == 1 ? true : false,
                                'showLineUpNumber'  => $slide['Text3'] == 1 ? true : false,
                                'showKartNumber'    => $slide['Text4'] == 1 ? true : false,
                                'rowsPerPage'       => (int)$slide['Text5'],
                                'type'              => 'nextRacers'
                            )
                        );
                        break;

                    // Next, Next Racers - 5
                    case 14:
                        // TrackNo = trackId
                        // Text1 = speedLevel

                        //original
                        // $url = $this->channelSlideUrl . 'up-next.html?offset=1&trackId=' . $slide['TrackNo'] . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        // $output['lineup'][] = array('type' => 'url', 'options' => array('url' => $url, 'duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo'], 'backgroundUrl' => $this->speedScreenBackgroundUrl, 'type' => 'nextNextRacers'));

                        $url = $this->channelSlideUrl
                            . 'up-next.html'
                            . '?trackId='       . (int)$slide['TrackNo']
                            . '&speedLevel='    . (int)$slide['Text1']
                            . '&offset='        . 1
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'speedLevel'        => (int)$slide['Text1'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'type'              => 'schedule'
                            )
                        );

                        break;

                    // Last Winner with Picture - 4
                    case 15:
                        // TrackNo = trackId
                        // Text1 = speedLevel

                        // original
                        // $output['lineup'][] = array('type' => 'lastWinnerWithPicture', 'options' => array('duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo']));

                        $url = $this->channelSlideUrl
                            . 'last-winner.html'
                            . '?trackId='       . (int)$slide['TrackNo']
                            . '&speedLevel='    . (int)$slide['Text1']
                            . '&offset='        . 0
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'speedLevel'        => (int)$slide['Text1'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'type'              => 'lastWinnerWithPicture'
                            )
                        );

                        break;

                    // Top Time of Day (with Picture) - 4
                    case 16:
                        // TrackNo = trackId
                        // Text1 = speedLevel

                        // original
                        // $output['lineup'][] = array('type' => 'topTimeOfDayWithPicture', 'options' => array('duration' => $slide['TimeInSecond']*1000, 'trackId' => (int)$slide['TrackNo']));

                        $url = $this->channelSlideUrl
                            . 'top-times-pictures.html'
                            . '?trackId='       . (int)$slide['TrackNo']
                            . '&speedLevel='    . (int)$slide['Text1']
                            . '&range='         . 'day'
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'speedLevel'        => (int)$slide['Text1'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'type'              => 'topTimeOfDayWithPicture'
                            )
                        );
                        break;

                    // Next Heat (with Picture) - 4
                    //case 17: // see case 13 fallthrough

                    // HTML - 5
                    case 18:
                        // It's a VIDEO URL (.m4v, .mp4, .webm, .ogv)
                        if(filter_var($slide['Text0'], FILTER_VALIDATE_URL) && (substr($slide['Text0'], -4) == '.m4v' || substr($slide['Text0'], -4) == '.mp4' || substr($slide['Text0'], -5) == '.webm' || substr($slide['Text0'], -4) == '.ogv')) {

                            // Build path to video
                            $videoUrl = $baseVideoUrl . urlencode($slide['Text0']);
                            $output['lineup'][] = array(
                                'type' => 'url',
                                'options' => array(
                                    'url'           => $videoUrl,
                                    'originalUrl'   => $slide['Text0'],
                                    'duration'      => $slide['TimeInSecond']*1000
                                )
                            );

                        // It's a URL
                        } elseif(filter_var($slide['Text0'], FILTER_VALIDATE_URL)){

                            $output['lineup'][] = array(
                                'type' => 'url',
                                'options' => array(
                                    'url'       => $slide['Text0'],
                                    'original'  => $slide['Text0'],
                                    'duration'  => $slide['TimeInSecond']*1000
                                )
                            );

                        // It's a HTML block
                        } else {

                            $output['lineup'][] = array(
                                'id' => $slide['ID'],
                                'type' => 'html',
                                'options' => array(
                                    'html'      => $slide['Text0'],
                                    'duration'  => $slide['TimeInSecond']*1000
                                )
                            );
                        }
                        break;

                    // 19 Flash: Not implemented

                    // Event Screen - 3
                    case 20:
                        /*
                            Event procedures:
                            1. GetLastRanHeatNoByTrack @TrackNo
                            2. GetCurrentRunningEvent @HeatNo
                            3. GetEventScorePerRound @EventNo
                            4. Massage data from #3 into table
                        */

                        $output['lineup'][] = array(
                            'type' => 'eventScreen',
                            'options' => array(
                                'duration'  => $slide['TimeInSecond']*1000,
                                'trackId'   => (int)$slide['TrackNo']
                            )
                        );

                        // new event screen format not yet implemented (2014-07-29 DL)
                        // $url = $this->channelSlideUrl
                        //     . 'event-results.html'
                        //     . '?trackId='       . (int)$slide['TrackNo']
                        //     . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        // $output['lineup'][] = array(
                        //     'type' => 'url',
                        //     'options' => array(
                        //         'url'               => $url,
                        //         'duration'          => $slide['TimeInSecond']*1000,
                        //         'trackId'           => (int)$slide['TrackNo'],
                        //         'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                        //         'type'              => 'eventScreen'
                        //     )
                        // );
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

                        // original
                        // $output['lineup'][] = array('type' => 'previousRaceResults', 'options' => array('startAtPosition' => (int)$slide['Text0'], 'duration' => $slide['TimeInSecond']*1000));

                        $url = $this->channelSlideUrl
                            . 'previous.html'
                            . '?trackId='       . $slide['TrackNo']
                            . '&speedLevel='    . $slide['Text1']
                            . '&offset='        . 0
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'speedLevel'        => (int)$slide['Text1'],
                                'type'              => 'previousRaceResults'
                            )
                        );
                        break;

                    // Previous, Previous Race Results - 3
                    case 23:

                        // original
                        // $output['lineup'][] = array('type' => 'previousPreviousRaceResults', 'options' => array('startAtPosition' => (int)$slide['Text0'], 'duration' => $slide['TimeInSecond']*1000));

                        $url = $this->channelSlideUrl
                            . 'previous.html'
                            . '?trackId='       . $slide['TrackNo']
                            . '&speedLevel='    . $slide['Text1']
                            . '&offset='        . 1
                            . '&backgroundUrl=' . urlencode($this->speedScreenBackgroundUrl);
                        $output['lineup'][] = array(
                            'type' => 'url',
                            'options' => array(
                                'url'               => $url,
                                'duration'          => $slide['TimeInSecond']*1000,
                                'trackId'           => (int)$slide['TrackNo'],
                                'backgroundUrl'     => $this->speedScreenBackgroundUrl,
                                'speedLevel'        => (int)$slide['Text1'],
                                'type'              => 'previousPreviousRaceResults'
                            )
                        );
                        break;
                }
            }
        }

        // Hash the lineup
        $output['hash'] = md5(serialize($output));

        // Include speedscreen version
        $version = new Version();
        $output['speedscreenVersion'] = $version->speedscreenVersion;
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
