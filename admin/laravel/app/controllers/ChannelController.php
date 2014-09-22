<?php

require_once(app_path().'/includes/includes.php');

class ChannelController extends BaseController
{

		// Possible to do this globally?
		public function __construct() {
			View::share('controller', __CLASS__);
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