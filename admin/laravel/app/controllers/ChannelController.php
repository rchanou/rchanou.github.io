<?php

require_once(app_path().'/includes/includes.php');

class ChannelController extends BaseController
{

		public $image_directory;
		public $image_filename;
		public $image_path;
		public $image_url;
		public $slide_image_directory;

		// Possible to do this globally?
		public function __construct() {
			View::share('controller', __CLASS__);
			$this->image_directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'cs-speedscreen' . DIRECTORY_SEPARATOR . 'images';
			$this->image_filename = 'background_1080p.jpg';
			$this->image_path = $this->image_directory . DIRECTORY_SEPARATOR . $this->image_filename;
			$this->image_url = Config::get('config.assetsURL') . '/../cs-speedscreen/images/background_1080p.jpg';

			// Image uploader data
			$this->slide_image_directory = __DIR__
			. DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . 'ClubSpeed'
			. DIRECTORY_SEPARATOR . 'wwwroot'
			. DIRECTORY_SEPARATOR . 'SP_Admin'
			. DIRECTORY_SEPARATOR . 'ScreenImages';

			if (!file_exists($this->slide_image_directory) && getenv('SERVER_ADDR') === '192.168.111.205'){
				// Ronnie's debugging directory
				$this->slide_image_directory = '\\\\192.168.111.122\\c$\\ClubSpeed\\wwwroot\\SP_Admin\\ScreenImages';
			}

			$this->slide_image_directory = '\\\\192.168.111.122\\c$\\ClubSpeed\\wwwroot\\SP_Admin\\ScreenImages';

			// Video uploader data
			$this->video_directory = __DIR__
			. DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . 'assets'
			. DIRECTORY_SEPARATOR . 'videos';

			if (!file_exists($this->video_directory) && getenv('SERVER_ADDR') == '192.168.111.205'){
				$this->video_directory = '\\\\192.168.111.122\\c$\\clubspeedapps\\assets\\videos';
			}
		}

		private function return_bytes($val) {
			$val = trim($val);
			$last = strtolower($val[strlen($val)-1]);
			switch($last) {
				// The 'G' modifier is available since PHP 5.1.0
				case 'g':
				$val *= 1024;
				case 'm':
				$val *= 1024;
				case 'k':
				$val *= 1024;
			}

			return $val;
		}

		public function updateVideo()
		{
			//ini_set("max_file_uploads", 1);

			// Build the input for our validation
			$input = array('video' => Input::file('image'));
			$filename = Input::get('filename');


			// Within the ruleset, make sure we let the validator know that this
			$rules = array(
				'video' => 'required|max:' . min($this->return_bytes(ini_get("upload_max_filesize")), $this->return_bytes(ini_get("post_max_size")), $this->return_bytes(ini_get("memory_limit")))
			);

			// Now pass the input and rules into the validator
			$validator = Validator::make($input, $rules);

			// Check to see if validation fails or passes
			if ($validator->fails()) {
			//if (false) {
				// VALIDATION FAILED
				return Response::json($validator->messages(), 412);
			} else {
				// SAVE THE FILE...

				// Ensure the directory exists, if not, create it!
				if(!is_dir($this->video_directory)) mkdir($this->video_directory, null, true);

				// Move the file, overwriting if necessary
				//return Response::json($filename, 418);
				Input::file('image')->move($this->video_directory, $filename);

				// Fix permissions on Windows (works on 2003?). This is because by default the uploaded imaged
				// does not inherit permissions from the folder it is moved to. Instead, it retains the
				// permissions of the temporary folder.
				exec('c:\windows\system32\icacls.exe ' . $this->video_directory . DIRECTORY_SEPARATOR . $filename . ' /inheritance:e');

				return Response::json(array('message' => 'Video uploaded successfully!'), 200);
			}
		}

		public function updateImage()
		{
			// Build the input for our validation
			$input = array('image' => Input::file('image'));
			$filename = Input::get('filename');

			// Within the ruleset, make sure we let the validator know that this
			$rules = array(
				'image' => 'required|max:10000',
			);

			// Now pass the input and rules into the validator
			$validator = Validator::make($input, $rules);

			// Check to see if validation fails or passes
			if ($validator->fails()) {
				// VALIDATION FAILED
				return Response::json(array('error' => 'The provided file was not an image.'), 412);
			} else {
				// SAVE THE FILE...

				// Ensure the directory exists, if not, create it!
				if(!is_dir($this->slide_image_directory)) mkdir($this->slide_image_directory, null, true);

				// Move the file, overwriting if necessary
				Input::file('image')->move($this->slide_image_directory, $filename);

				// Fix permissions on Windows (works on 2003?). This is because by default the uploaded imaged
				// does not inherit permissions from the folder it is moved to. Instead, it retains the
				// permissions of the temporary folder.
				exec('c:\windows\system32\icacls.exe ' . $this->slide_image_directory . DIRECTORY_SEPARATOR . $filename . ' /inheritance:e');

				return Response::json(array('message' => 'Image uploaded successfully!'), 200);
			}
		}

    public function index()
    {
        $listOfChannels = CS_API::getListOfChannels();
        $apiCallFailed = ($listOfChannels === null);
        if ($apiCallFailed)
        {
            return Redirect::to('/disconnected');
        }

        if (count($listOfChannels) > 0)
        {
            $channelIds = array_keys(get_object_vars($listOfChannels));

            $channelLineups = $this->getAllChannelLineups($channelIds);
            $apiCallFailed = ($channelLineups === null);
            if ($apiCallFailed)
            {
                return Redirect::to('/disconnected');
            }
        }
        else
        {
            $channelLineups = array();
        }
        $numberOfMonitors = (Config::get('config.numberOfMonitors') == null ? 16 : Config::get('config.numberOfMonitors'));

        /*echo json_encode($channelDetails);
        die();*/
        return View::make('/screens/channel',
            array(
                'listOfChannels' => $listOfChannels,
                'channelLineups' => $channelLineups,
                'numberOfMonitors' => $numberOfMonitors
            ));
    }

		public function speedScreen()
		{
			return View::make('/screens/speedScreen');
		}


    public function translations()
    {
        $supportedCultures = array(
            'en-US' => 'English (US)',
            'en-GB' => 'English (UK)',
            'en-NZ' => 'English (NZ)',
            'en-AU' => 'English (AU)',
            'en-IE' => 'English (IE)',
            'en-CA' => 'English (CA)',
            'es-MX' => 'Español',
            'es-CR' => 'Español (CR)',
            'es-ES' => 'Castellano',
            'es-PR' => 'Español (PR)',
            'ru-RU' => 'Pусский язык',
            'fr-CA' => 'Français',
            'de-DE' => 'Deutsch',
            'nl-NL' => 'Nederlands',
            'pl-PL' => 'Język polski',
            'da-DK' => 'Dansk',
            'ar-AE' => 'العربية',
            'it-IT' => 'Italiano',
            'bg-BG' => 'български език',
            'sv-SE' => 'Svenska'
        );

        $currentCulture = "en-US";

        $translations = CS_API::getTranslations('Speedscreen');

        $translations_scoreboard = CS_API::getTranslations('Scoreboard');

        return View::make('/screens/speedScreen/translations',
            array('controller' => 'ChannelController',
                'supportedCultures' => $supportedCultures,
                'currentCulture' => $currentCulture,
                'translations' => $translations,
                'translations_scoreboard' => $translations_scoreboard
            )
        );
    }

    public function updateTranslations()
    {
        $input = Input::all();
        unset($input['_token']); //Removing Laravel's default form value
        $cultureKey = $input['cultureKey'];
        $namespace = $input['namespace'];
        unset($input['cultureKey']);
        unset($input['namespace']);

        //Format the missing string data as expected by Club Speed's API
        $updatedTranslations = array(); //Destined to a PUT
        $newTranslations = array(); //Destined to a POST
        foreach($input as $stringId => $stringValue)
        {
            if (isset($stringId))
            {
                if (!$this->contains($stringId,'new_'))
                {
                    $updatedTranslations[] = array(
                        'translationsId' => str_replace("id_","",$stringId),
                        'value' => $stringValue
                    );
                }
                else if ($stringValue != "")
                {
                    $newTranslations[] = array(
                        'name' => str_replace("id_new_","",$stringId),
                        'namespace' => $namespace,
                        'value' => $stringValue,
                        'defaultValue' => $stringValue,
                        'culture' => $cultureKey,
                        'comment' => '');
                }
            }
        }

        $result = null;
        if (count($updatedTranslations) > 0)
        {
            $result = CS_API::updateTranslationsBatch($updatedTranslations);

            $updateWasSuccessful = ($result !== null);
            if ($updateWasSuccessful === false)
            {
                return Redirect::to('speedScreen/translations')->with( array('error' => 'One or more translations could not be updated. Please try again.'));
            }
            else if ($updateWasSuccessful === null)
            {
                return Redirect::to('/disconnected');
            }
        }


        $result = null;
        if (count($newTranslations) > 0)
        {
            $result = CS_API::insertTranslationsBatch($newTranslations);

            $insertWasSuccessful = ($result !== null);
            if ($insertWasSuccessful === false)
            {
                return Redirect::to('speedScreen/translations')->with( array('error' => 'One or more translations could not be created. Please try again.'));
            }
            else if ($insertWasSuccessful === null)
            {
                return Redirect::to('/disconnected');
            }
        }

        //Standard success message
        return Redirect::to('speedScreen/translations')->with( array('message' => 'Translations updated successfully!'));
    }


		public function settings()
        {
            $speedscreenSettings = CS_API::getSettingsFromNewTableFor('Speedscreen');
            $speedscreenSettingsFormatted = array();
            $speedscreenSettingsIds = array();
            if (isset($speedscreenSettings->settings))
            {
                foreach($speedscreenSettings->settings as $setting)
                {
                    $speedscreenSettingsFormatted[$setting->name] = $setting->value;
                    $speedscreenSettingsIds[$setting->name] = $setting->settingsId;
                }
            }

            Session::put('speedscreenSettings',$speedscreenSettingsFormatted);
            Session::put('speedscreenSettingsIds',$speedscreenSettingsIds);

            $supportedLocales = array(
                'en-US' => 'English (US)',
                'en-GB' => 'English (UK)',
                'en-NZ' => 'English (NZ)',
                'en-AU' => 'English (AU)',
                'en-IE' => 'English (IE)',
                'en-CA' => 'English (CA)',
                'es-MX' => 'Español',
                'es-CR' => 'Español (CR)',
                'es-ES' => 'Castellano',
                'es-PR' => 'Español (PR)',
                'ru-RU' => 'Pусский язык',
                'fr-CA' => 'Français',
                'de-DE' => 'Deutsch',
                'nl-NL' => 'Nederlands',
                'pl-PL' => 'Język polski',
                'da-DK' => 'Dansk',
                'ar-AE' => 'العربية',
                'it-IT' => 'Italiano',
                'bg-BG' => 'български език',
                'sv-SE' => 'Svenska'
            );

            return View::make('/screens/settings',
                array('background_image_url' => is_file($this->image_path) ? $this->image_url : null,
                    'speedscreenSettings' => $speedscreenSettingsFormatted,
                    'supportedLocales' => $supportedLocales)
            );
        }

        public function updateSettings()
        {
            $input = Input::all();
            unset($input['_token']); //Removing Laravel's default form value
            $destination = '/channelSettings';

            //Begin data validation
            $rules = array(
                'channelUpdateFrequencyMs' => 'integer|min:1000',
                'racesPollingRateMs' => 'integer|min:200',
                'timeUntilRestartOnErrorMs' => 'integer|min:1000'
            );
            $messages = array(
                'channelUpdateFrequencyMs.integer' => 'The channel update frequency must be an integer.',
                'channelUpdateFrequencyMs.min' => 'The channel update frequency has to be at least 1000 milliseconds.',
                'racesPollingRateMs.integer' => 'The races polling rate must be an integer.',
                'racesPollingRateMs.min' => 'The races polling rate must be at least 200 milliseconds.',
                'timeUntilRestartOnErrorMs.integer' => 'The time to wait until restarting must be an integer.',
                'timeUntilRestartOnErrorMs.min' => 'The time to wait until restarting must be at least 1000 milliseconds.'
            );
            $validator = Validator::make($input, $rules, $messages);
            if ($validator->fails()) {
                return Redirect::to($destination)->withErrors($validator);
            } //End data validation

            //Begin formatting form input for processing
            $newSettings = array();
            foreach($input as $currentSettingName => $currentSettingValue)
            {
                $newSettings[$currentSettingName] = $currentSettingValue;
            }
            //End formatting

            //Identify the settings that actually changed and need to be sent to Club Speed
            $currentSettings = Session::get('speedscreenSettings',array());
            foreach($currentSettings as $currentSettingName => $currentSettingValue)
            {
                if (isset($newSettings[$currentSettingName]))
                {
                    if ($newSettings[$currentSettingName] == $currentSettingValue) //If the setting hasn't changed
                    {
                        unset($newSettings[$currentSettingName]); //Remove it from the list of new settings
                    }
                }
            }

            //Only send settings that already exist in the API due to migrations having already been run
            foreach($newSettings as $newSettingName => $newSettingValue)
            {
                if (!isset($currentSettings[$newSettingName]))
                {
                    unset($newSettings[$newSettingName]); //Remove any settings about to be sent that the API doesn't know about yet
                }
            }

            $newSettingsIds = Session::get('speedscreenSettingsIds');
            $result = CS_API::updateSettingsInNewTableFor('Speedscreen',$newSettings,$newSettingsIds);

            if ($result === false)
            {
                return Redirect::to($destination)->with( array('error' => 'One or more settings could not be updated. Please try again.'));
            }
            else if ($result === null)
            {
                return Redirect::to('/disconnected');
            }

            return Redirect::to($destination)->with( array('message' => 'Settings updated successfully!'));
        }

		public function settingsSubmit()
		{
				// Build the input for our validation
				$input = array('image' => Input::file('image'));

				// Within the ruleset, make sure we let the validator know that this
				$rules = array(
						'image' => 'required|max:10000',
				);

				// Now pass the input and rules into the validator
				$validator = Validator::make($input, $rules);

				// Check to see if validation fails or passes
				if ($validator->fails()) {
						// VALIDATION FAILED
						return Redirect::to('/channelSettings')->with('error', 'The provided file was not an image');
				} else {
						// SAVE THE FILE...

						// Ensure the directory exists, if not, create it!
						if(!is_dir($this->image_directory)) mkdir($this->image_directory, null, true);

						// Move the file, overwriting if necessary
						Input::file('image')->move($this->image_directory, $this->image_filename);

						// Fix permissions on Windows (works on 2003?). This is because by default the uplaoded imaged
						// does not inherit permissions from the folder it is moved to. Instead, it retains the
						// permissions of the temporary folder.
						exec('c:\windows\system32\icacls.exe ' . $this->image_path . ' /inheritance:e');

						return Redirect::to('/channelSettings')->with('message', 'Background uploaded successfully!');
				}

		}

    private function getAllChannelLineups($channelIds)
    {
        $output = array();
        foreach($channelIds as $channelId)
        {
            $output[$channelId] = CS_API::getDetailsOnChannel($channelId);
            $apiCallFailed = ($output[$channelId] === null);
            if ($apiCallFailed)
            {
                return null;
            }
            $output[$channelId] = $output[$channelId]->lineup;

        }
        return $output;
    }

		public function createChannel(){
			$result = CS_API::createChannel();
			if (isset($result) && $result != null){
				return Redirect::to('/channel')->with('selectLastChannel', true)->with('message', 'Channel successfully created!');
			} else {
				return Redirect::to('/channel')->with('error', 'An error occurred while trying to create the channel.');
			}
		}

    private static function contains(&$haystack, $needle)
    {
        $result = strpos($haystack, $needle);
        return $result !== false;
    }

}