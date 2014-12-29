<?php

require_once(app_path().'/includes/includes.php');

class ChannelController extends BaseController
{

		public $image_directory;
		public $image_filename;
		public $image_path;
		public $image_url;

		// Possible to do this globally?
		public function __construct() {
			View::share('controller', __CLASS__);
			$this->image_directory = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'assets' . DIRECTORY_SEPARATOR . 'cs-speedscreen' . DIRECTORY_SEPARATOR . 'images';
			$this->image_filename = 'background_1080p.jpg';
			$this->image_path = $this->image_directory . DIRECTORY_SEPARATOR . $this->image_filename;
			$this->image_url = Config::get('config.assetsURL') . '/../cs-speedscreen/images/background_1080p.jpg';
		}

    public function index()
    {
        $session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }

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
			$session = Session::all();
			if (!(isset($session["authenticated"]) && $session["authenticated"]))
			{
				$messages = new Illuminate\Support\MessageBag;
				$messages->add('errors', "You must login before viewing the admin panel.");

				//Redirect to the previous page with an appropriate error message
				return Redirect::to('/login')->withErrors($messages)->withInput();
			}

			return View::make('/screens/speedScreen');
		}

		public function settings()
    {
        $session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }

        return View::make('/screens/settings',
            array('background_image_url' => is_file($this->image_path) ? $this->image_url : null));
    }

		public function settingsSubmit()
		{
				$session = Session::all();
        if (!(isset($session["authenticated"]) && $session["authenticated"]))
        {
            $messages = new Illuminate\Support\MessageBag;
            $messages->add('errors', "You must login before viewing the admin panel.");

            //Redirect to the previous page with an appropriate error message
            return Redirect::to('/login')->withErrors($messages)->withInput();
        }

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



}
