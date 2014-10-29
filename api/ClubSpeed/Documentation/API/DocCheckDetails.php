<?php

namespace ClubSpeed\Documentation\API;

class DocCheckDetails Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'check-details';
        $this->header          = 'Check Details';
        $this->url             = 'checkDetails';
        $this->info            = $this->info();
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
                  'name'        => 'checkDetailsId'
                , 'type'        => 'Integer'
                , 'default'     => '{Generated}'
                , 'create'      => 'unavailable'
                , 'update'      => 'unavailable'
                , 'description' => 'The ID for the CheckDetails record.'
            )
            , array(
                  'name'        => 'checkId'
                , 'type'        => 'Integer'
                , 'default'     => ''
                , 'create'      => 'required'
                , 'update'      => 'unavailable'
                , 'description' => 'The ID of the parent Check. This CheckID must already exist.'
            )
            , array(
                  'name'        => 'productId'
                , 'type'        => 'Integer'
                , 'default'     => ''
                , 'create'      => 'required'
                , 'update'      => 'unavailable'
                , 'description' => 'The ID of the product.'
            )
            , array(
                  'name'        => 'productName'
                , 'type'        => 'String'
                , 'default'     => '{Lookup}'
                , 'create'      => 'unavailable'
                , 'update'      => 'unavailable'
                , 'description' => 'The name for the underlying product.'
            )
            , array(
                  'name'        => 'status'
                , 'type'        => 'Integer'
                , 'default'     => '1'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => ""
                    ."\n<span>"
                    ."\n  The status of the CheckDetails."
                    ."\n</span>"
                    ."\n<ol>"
                    ."\n  <li>IsNew</li>"
                    ."\n  <li>HasVoided</li>"
                    ."\n  <li>CannotDeleted</li>"
                    ."\n</ol>"
                    ."\n<span>Note that when creating a new CheckDetails record, this will always be set to 1.</span>"
            )
            , array(
                  'name'        => 'type'
                , 'type'        => 'Integer'
                , 'default'     => '1'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => ''
                    ."\n<span>"
                    ."\n  The type for the CheckDetails."
                    ."\n</span>"
                    ."\n<ol>"
                    ."\n  <li>RegularItem</li>"
                    ."\n  <li>PointItem</li>"
                    ."\n  <li>FoodItem</li>"
                    ."\n  <li>ReservationItem</li>"
                    ."\n  <li>GameCardItem</li>"
                    ."\n  <li>MembershipItem</li>"
                    ."\n  <li>GiftCardItem</li>"
                    ."\n  <li>EntitleItem</li>"
                    ."\n</ol>"
            )
            , array(
                  'name'        => 'qty'
                , 'type'        => 'Integer'
                , 'icon'        => 'warning-sign orange'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'default'     => '0'
                , 'description' => 'The quantity of the product to be added to check details. Either qty or cadetQty must be provided and greater than zero.'
            )
            , array(
                  'name'        => 'cadetQty'
                , 'type'        => 'Integer'
                , 'icon'        => 'warning-sign orange'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'default'     => '0'
                , 'description' => 'The cadet quantity of the product to be added to check details. Either qty or cadetQty must be provided and greater than zero.'
            )
            , array(
                  'name'        => 'createdDate'
                , 'type'        => 'DateTime'
                , 'default'     => '{Date.Now}'
                , 'create'      => 'unavailable'
                , 'update'      => 'unavailable'
                , 'description' => 'The timestamp indicating when the CheckDetails record was created.'
            )
        );
    }

    private function create() {
        return array(
            'info' => array(
                'access' => 'private'
            )
            , 'examples' => array(
                'request' => <<<EOS
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/checkDetails HTTP/1.1
{
    "checkId": 2288,
    "type": 1,
    "productId": 8,
    "qty": 5
}
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
    "checkDetailId": 7558
}
EOS
            )
        );
    }

    private function single() {
        return array(
            'info' => array(
                'access' => 'private'
            ),
            'examples' => array(
                'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/checkDetails/7556 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
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
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/checkDetails?qty=5&productId=43 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
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
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/checkDetails?filter=3%3CqtyANDqty%3C%3D5ANDcreatedDate%3E2014-08-01 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
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
PUT https://{$_SERVER['SERVER_NAME']}/api/index.php/checkDetails/7564 HTTP/1.1
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
DELETE https://{$_SERVER['SERVER_NAME']}/api/index.php/checkDetails/7560 HTTP/1.1
EOS
          , 'response' => <<<EOS
HTTP/1.1 200 OK
EOS
            )
        );
    }
}