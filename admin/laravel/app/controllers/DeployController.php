<?php

require_once(app_path().'/includes/includes.php');

class DeployController extends BaseController
{
    public function deploy() //For Speed Screens
    {
        $channelUrl = isset($_REQUEST['channelUrl']) ? $_REQUEST['channelUrl'] : 1 ;
        $channelId = isset($_REQUEST['channelId']) ? $_REQUEST['channelId'] : 1 ;
        $channelNumber = isset($_REQUEST['channelNumber']) ? $_REQUEST['channelNumber'] : 1 ;
        $targetMonitor = isset($_REQUEST['targetMonitor']) ? $_REQUEST['targetMonitor'] : 1;

        $contents = View::make('speedscreenCreation/createChannelOnSpecificScreen',
            array('channelUrl' => $channelUrl,
                'channelId' => $channelId,
                'channelNumber' => $channelNumber,
                'targetMonitor' => $targetMonitor));

        $response = Response::make($contents);

        $response->header('Content-Type', 'application/x-msdownload');
        return $response;
    }

    public function deployGenericExe() //Multi-purpose template
    {
        $targetUrl = isset($_REQUEST['targetUrl']) ? $_REQUEST['targetUrl'] : '';
        $targetMonitor = isset($_REQUEST['targetMonitor']) ? $_REQUEST['targetMonitor'] : 1;
        $appName = isset($_REQUEST['appName']) ? $_REQUEST['appName'] : 'app';
        $contents = View::make('genericExeCreation/createGenericExe',
                        array('targetUrl' => $targetUrl,
                              'targetMonitor' => $targetMonitor,
                              'appName' => $appName)
                              );
        $response = Response::make($contents);

        $response->header('Content-Type', 'application/x-msdownload');
        return $response;
    }
}
