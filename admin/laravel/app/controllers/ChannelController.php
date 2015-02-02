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

			/*if (!file_exists($this->slide_image_directory) && getenv('SERVER_ADDR') === '192.168.111.165'){
				// Ronnie's debugging directory
				$this->slide_image_directory = '\\\\192.168.111.122\\c$\\ClubSpeed\\wwwroot\\SP_Admin\\ScreenImages';
			}
			*/

			// Video uploader data
			$this->video_directory = __DIR__
			. DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . '..'
			. DIRECTORY_SEPARATOR . 'assets'
			. DIRECTORY_SEPARATOR . 'videos';

			/*
			if (!file_exists($this->video_directory) && getenv('SERVER_ADDR') == '192.168.111.165'){
				$this->video_directory = '\\\\192.168.111.122\\c$\\clubspeedapps\\assets\\videos';
			}
			*/
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
			$session = Session::all();
			if (!(isset($session["authenticated"]) && $session["authenticated"]))
			{
				$messages = new Illuminate\Support\MessageBag;
				$messages->add('errors', "You must login before viewing the admin panel.");
				return Redirect::to('/login')->withErrors($messages)->withInput();
			}

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

				return Response::json(array('message' => 'Image uploaded successfully!'), 200);
			}
		}

		public function updateImage()
		{
			$session = Session::all();
			if (!(isset($session["authenticated"]) && $session["authenticated"]))
			{
				$messages = new Illuminate\Support\MessageBag;
				$messages->add('errors', "You must login before viewing the admin panel.");
				return Redirect::to('/login')->withErrors($messages)->withInput();
			}

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

				return Response::json(array('message' => 'Video uploaded successfully!'), 200);
			}
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
