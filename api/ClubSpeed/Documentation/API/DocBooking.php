<?php

namespace ClubSpeed\Documentation\API;

class DocBooking Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'booking';
        $this->header          = 'Booking';
        $this->url             = 'booking';

        // $this->stuff = $this->parseCalls($this->id);

        $this->info            = $this->info();
        $this->calls['create'] = $this->create();
        $this->calls['list']   = $this->all(); // list is a reserved keyword in php
        $this->calls['single'] = $this->single();
        $this->calls['match']  = $this->match();
        $this->calls['search'] = $this->search();
        $this->calls['update'] = $this->update(); // this adds a delete section. why???
        $this->calls['delete'] = $this->delete();
        $this->expand(); // expand before delete is added

        // $this->model = $this->parseModel($this->id);
        // $this->parseCalls($this->id);
        // $this->expand();

        // print_r($this);
        // die();
    }

    // private function model() {
    //     $this->parseModel($this->id);
    //     // $model = json_decode(file_get_contents(__DIR__ . "/models/" . $this->id . ".json"), true);
    //     // print_r($model);
    //     // die();
    // }

    private function info() {
        return array(
            array(
                  'name'        => 'onlineBookingsId'
                , 'type'        => 'Integer'
                , 'default'     => '{Generated}'
                , 'create'      => 'unavailable'
                , 'update'      => 'unavailable'
                , 'description' => 'The ID for the booking.'
            )
            , array(
                  'name'        => 'heatId'
                , 'type'        => 'Integer'
                , 'create'      => 'required'
                , 'update'      => 'available'
                , 'description' => 'The ID of the heat for the booking.'
            )
            , array(
                  'name'        => 'productsId'
                , 'type'        => 'Integer'
                , 'create'      => 'required'
                , 'update'      => 'available'
                , 'description' => "The ID of the product for the booking."
            )
            , array(
                  'name'        => 'isPublic'
                , 'type'        => 'Boolean'
                , 'default'     => "true"
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => "The flag indicating whether or not to make this booking available to the online booking interface."
            )
            , array(
                  'name'        => 'quantityTotal'
                , 'type'        => 'Integer'
                , 'create'      => 'required'
                , 'update'      => 'available'
                , 'description' => "The number of available bookings. This must be a positive integer."
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
POST http://{$_SERVER['SERVER_NAME']}/api/index.php/booking HTTP/1.1
{
    "heatId": 2,
    "productsId": 8,
    "quantityTotal": 5
}
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
    "onlineBookingsId": 1
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
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/booking HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "bookings": [
    {
      "onlineBookingsId": 1,
      "heatId": 2,
      "productsId": 8,
      "isPublic": true,
      "quantityTotal": 5
    },
    {
      "onlineBookingsId": 2,
      "heatId": 3,
      "productsId": 11,
      "isPublic": false,
      "quantityTotal": 5
    },
    {
      "onlineBookingsId": 3,
      "heatId": 5,
      "productsId": 2,
      "isPublic": true,
      "quantityTotal": 3
    },
    {
      "onlineBookingsId": 4,
      "heatId": 2,
      "productsId": 8,
      "isPublic": true,
      "quantityTotal": 5
    }
  ]
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
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/booking/1 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "bookings": [
    {
      "onlineBookingsId": 1,
      "heatId": 2,
      "productsId": 8,
      "isPublic": true,
      "quantityTotal": 5
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
            ),
            'examples' => array(
                'request' => <<<EOS
GET http://{$_SERVER['SERVER_NAME']}/api/index.php/booking?heatId=2 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "bookings": [
    {
      "onlineBookingsId": 1,
      "heatId": 2,
      "productsId": 8,
      "isPublic": true,
      "quantityTotal": 5
    },
    {
      "onlineBookingsId": 4,
      "heatId": 2,
      "productsId": 8,
      "isPublic": true,
      "quantityTotal": 5
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
            ),
            'examples' => array(
                'request' => <<<EOS
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/booking?filter=quantityTotal %gte; 5 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "bookings": [
    {
      "onlineBookingsId": 1,
      "heatId": 2,
      "productsId": 8,
      "isPublic": true,
      "quantityTotal": 5
    },
    {
      "onlineBookingsId": 2,
      "heatId": 3,
      "productsId": 11,
      "isPublic": false,
      "quantityTotal": 5
    },
    {
      "onlineBookingsId": 4,
      "heatId": 2,
      "productsId": 8,
      "isPublic": true,
      "quantityTotal": 5
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
            ),
            'examples' => array(
                'request' => <<<EOS
PUT https://{$_SERVER['SERVER_NAME']}/api/index.php/booking/1 HTTP/1.1
{
    "isPublic": false
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
            ),
            'examples' => array(
                'request' => <<<EOS
DELETE https://{$_SERVER['SERVER_NAME']}/api/index.php/booking/1 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
EOS
            )
        );
    }
}