<?php

namespace ClubSpeed\Documentation\API;

class DocScreenTemplate Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id = 'screen-template';
        $this->header = 'Screen Template';
        $this->url = 'screenTemplate';
        $this->info = $this->info();
        $this->calls['create'] = $this->create();
        $this->calls['single'] = $this->single();
        $this->calls['match']  = $this->match(); // leave match out for now?
        $this->calls['search'] = $this->search(); // leave search out for now?
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
                , 'create'      => 'unavailable'
                , 'update'      => 'unavailable'
                , 'description' => 'The ID for the ScreenTemplate record.'
            )
            , array(
                  'name'        => 'screenTemplateName'
                , 'type'        => 'String'
                , 'default'     => ''
                , 'create'      => 'available'
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
POST https://mytrack.clubspeedtiming.com/api/index.php/checkDetails HTTP/1.1
Content-Length: 79
Content-Type: application/json
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
{
    "checkId": 2288,
    "type": 1,
    "productId": 8,
    "qty": 5
}
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
Date: Mon, 15 Sep 2014 20:27:02 GMT
Content-Length: 27
Content-Type: application/json
{
    "checkDetailId": 7558
}
EOS
            )
        );
    }

    private function single() {
        return array(
            'examples' => array(
                'request' => <<<EOS
GET https://mytrack.clubspeedtiming.com/api/index.php/checkDetails/7556 HTTP/1.1
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
Accept-Language: en-US,en;q=0.8
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
Date: Mon, 15 Sep 2014 20:33:32 GMT
Content-Length: 245
Content-Type: application/json
{
    "checkDetails": [
        {
            "checkDetailId": 7556,
            "checkId": 2288,
            "status": 1,
            "type": 1,
            "productId": 8,
            "productName": "",
            "createdDate": "2014-09-15",
            "qty": 5,
            "cadetQty": 0
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
GET https://mytrack.clubspeedtiming.com/api/index.php/checkDetails?qty=5&productId=43 HTTP/1.1
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
Accept-Language: en-US,en;q=0.8
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
Date: Mon, 15 Sep 2014 21:03:01 GMT
Content-Length: 521
Content-Type: application/json
{
    "checkDetails": [
        {
            "checkDetailId": 2564,
            "checkId": 645,
            "status": 3,
            "type": 4,
            "productId": 43,
            "productName": "Online 20 Min Arrive n Drive",
            "createdDate": "2014-01-23",
            "qty": 5,
            "cadetQty": 0
        },
        {
            "checkDetailId": 5254,
            "checkId": 1537,
            "status": 3,
            "type": 4,
            "productId": 43,
            "productName": "Online 20 Min Arrive n Drive",
            "createdDate": "2014-04-25",
            "qty": 5,
            "cadetQty": 0
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
GET https://mytrack.clubspeedtiming.com/api/index.php/checkDetails?filter=3%3CqtyANDqty%3C%3D5ANDcreatedDate%3E2014-08-01 HTTP/1.1
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
Accept-Language: en-US,en;q=0.8
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
Date: Mon, 15 Sep 2014 21:22:00 GMT
Content-Length: 683
Content-Type: application/json
{
    "checkDetails": [
        {
            "checkDetailId": 7556,
            "checkId": 2288,
            "status": 1,
            "type": 1,
            "productId": 8,
            "productName": "",
            "createdDate": "2014-09-15",
            "qty": 5,
            "cadetQty": 0
        },
        {
            "checkDetailId": 7557,
            "checkId": 2288,
            "status": 1,
            "type": 1,
            "productId": 8,
            "productName": "",
            "createdDate": "2014-09-15",
            "qty": 5,
            "cadetQty": 0
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
PUT https://mytrack.clubspeedtiming.com/api/index.php/checkDetails/7564 HTTP/1.1
Content-Length: 86
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
{
    "status": 2,
    "type": 2, 
    "productId": 11,
    "qty": 0,
    "cadetQty": 3
}
EOS
          , 'response' => <<<EOS
HTTP/1.1 200 OK
Date: Mon, 15 Sep 2014 22:42:24 GMT
Content-Length: 0
Content-Type: text/html
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
DELETE https://mytrack.clubspeedtiming.com/api/index.php/checkDetails/7560 HTTP/1.1
Content-Length: 0
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
EOS
          , 'response' => <<<EOS
HTTP/1.1 200 OK
Date: Mon, 15 Sep 2014 23:35:16 GMT
Content-Length: 0
Content-Type: text/html
EOS
            )
        );
    }
}