<?php

require_once(app_path().'/includes/includes.php');

class DeployController extends BaseController
{
    public function deploy()
    {
        $channelUrl = isset($_REQUEST['channelUrl']) ? $_REQUEST['channelUrl'] : 1 ;
        $channelId = isset($_REQUEST['channelId']) ? $_REQUEST['channelId'] : 1 ;
        $targetMonitor = isset($_REQUEST['targetMonitor']) ? $_REQUEST['targetMonitor'] : 1;

        return View::make('speedscreenCreation/createChannelOnSpecificScreen',
            array('channelUrl' => $channelUrl,
                  'channelId' => $channelId,
                  'targetMonitor' => $targetMonitor));
    }
} 