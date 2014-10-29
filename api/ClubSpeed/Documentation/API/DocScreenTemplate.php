<?php

namespace ClubSpeed\Documentation\API;

class DocScreenTemplate Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'screen-template';
        $this->header          = 'Screen Template';
        $this->url             = 'screenTemplate';
        $this->info            = $this->info();
        $this->calls['create'] = $this->create();
        $this->calls['list']   = $this->all();
        $this->calls['single'] = $this->single();
        $this->calls['match']  = $this->match();
        $this->calls['search'] = $this->search();
        $this->calls['update'] = $this->update();
        $this->calls['delete'] = $this->delete();
        $this->expand();
    }

    private function info() {
        return array(
            array(
                  'name'        => 'screenTemplateId'
                , 'type'        => 'Integer'
                , 'default'     => '{Generated}'
                , 'description' => 'The ID for the ScreenTemplate record.'
            )
            , array(
                  'name'        => 'screenTemplateName'
                , 'type'        => 'String'
                , 'default'     => ''
                , 'create'      => 'required'
                , 'update'      => 'available'
                , 'description' => 'The name of the ScreenTemplate record.'
            )
            , array(
                  'name'        => 'showScoreboard'
                , 'type'        => 'Boolean'
                , 'default'     => 'false'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => 'Flag indication of whether to integrate the scoreboard with this screen.'
            )
            , array(
                  'name'        => 'postRaceIdleTime'
                , 'type'        => 'Integer'
                , 'default'     => ''
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => 'The time in seconds for which to show the scoreboard after the race has completed.'
            )
            , array(
                  'name'        => 'trackId'
                , 'type'        => 'Integer'
                , 'default'     => '1'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => 'The trackId to use with the scoreboard. If the showScoreboard flag is set to true, then trackId will default to 1 if not provided.'
            )
            , array(
                  'name'        => 'deleted'
                , 'type'        => 'Boolean'
                , 'default'     => 'false'
                , 'create'      => 'unavailable'
                , 'update'      => 'available'
                , 'description' => 'Flag indication of whether the screenTemplate record is considered to be deleted.'
            )
            , array(
                  'name'        => 'startPosition'
                , 'type'        => 'Integer'
                , 'default'     => '1'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => '(DEPRECATED?)'
            )
            , array(
                  'name'        => 'sizeX'
                , 'type'        => 'Integer'
                , 'default'     => '800'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => 'The width in pixels of the target resolution.'
            )
            , array(
                  'name'        => 'sizeY'
                , 'type'        => 'Integer'
                , 'default'     => '600'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => 'The height in pixels of the target resolution.'
            )
        );
    }

    private function create() {
        return array(
            'examples' => array(
                'request' => <<<EOS
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/screenTemplate HTTP/1.1
{
    "screenTemplateName": "TestScreen1"
}
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "screenTemplateId": 19
}
EOS
            )
        );
    }

    private function all() {
        return array(
            'info' => array(
                'access' => 'private'
            ),
            'examples' => array(
                'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/screenTemplate HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "channels": [
    {
      "screenTemplateId": 15,
      "screenTemplateName": "TestScreenFromAPI1",
      "showScoreboard": null,
      "postRaceIdleTime": 45,
      "trackId": null,
      "deleted": false,
      "startPosition": 1,
      "sizeX": 1024,
      "sizeY": 768
    },
    {
      "screenTemplateId": 17,
      "screenTemplateName": "TestScreenFromAPI1",
      "showScoreboard": null,
      "postRaceIdleTime": null,
      "trackId": null,
      "deleted": false,
      "startPosition": 1,
      "sizeX": 800,
      "sizeY": 600
    },
    {
      "screenTemplateId": 19,
      "screenTemplateName": "TestScreen1",
      "showScoreboard": false,
      "postRaceIdleTime": null,
      "trackId": null,
      "deleted": false,
      "startPosition": 1,
      "sizeX": 800,
      "sizeY": 600
    }
  ]
}
EOS
            )
        );
    }

    private function single() {
        return array(
            'examples' => array(
                'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/screenTemplate/19 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "channels": [
    {
      "screenTemplateId": 19,
      "screenTemplateName": "TestScreen1",
      "showScoreboard": false,
      "postRaceIdleTime": null,
      "trackId": null,
      "deleted": false,
      "startPosition": 1,
      "sizeX": 800,
      "sizeY": 600
    }
  ]
}
EOS
            )
        );
    }

    private function match() {
        return array(
            'info' => array(
                'access' => 'private'
            )
            , 'examples' => array(
                'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/screenTemplate?sizeX=800 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "channels": [
    {
      "screenTemplateId": 17,
      "screenTemplateName": "TestScreenFromAPI1",
      "showScoreboard": null,
      "postRaceIdleTime": null,
      "trackId": null,
      "deleted": false,
      "startPosition": 1,
      "sizeX": 800,
      "sizeY": 600
    },
    {
      "screenTemplateId": 19,
      "screenTemplateName": "TestScreen1",
      "showScoreboard": false,
      "postRaceIdleTime": null,
      "trackId": null,
      "deleted": false,
      "startPosition": 1,
      "sizeX": 800,
      "sizeY": 600
    }
  ]
}
EOS
            )
        );
    }

    private function search() {
        return array(
            'info' => array(
                'access' => 'private'
            )
            , 'examples' => array(
                'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/checkDetails?filter=postRaceIdleTime IS NOT NULL HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "channels": [
    {
      "screenTemplateId": 15,
      "screenTemplateName": "TestScreenFromAPI1",
      "showScoreboard": null,
      "postRaceIdleTime": 45,
      "trackId": null,
      "deleted": false,
      "startPosition": 1,
      "sizeX": 1024,
      "sizeY": 768
    }
  ]
}
EOS
            )
        );
    }

    private function update() {
        return array(
            'info' => array(
                'access' => 'private'
            )
            , 'examples' => array(
                'request' => <<<EOS
PUT https://{$_SERVER['SERVER_NAME']}/api/index.php/screenTemplate/19 HTTP/1.1
{
    "sizeX": 1920,
    "sizeY": 1080
}
EOS
          , 'response' => <<<EOS
HTTP/1.1 200 OK
EOS
            )
        );
    }

    private function delete() {
        return array(
            'info' => array(
                'access' => 'private'
            )
            , 'examples' => array(
                'request' => <<<EOS
DELETE https://{$_SERVER['SERVER_NAME']}/api/index.php/screenTemplate/19 HTTP/1.1
EOS
          , 'response' => <<<EOS
HTTP/1.1 200 OK
EOS
            )
        );
    }
}