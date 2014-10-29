<?php

namespace ClubSpeed\Documentation\API;

class DocReservations Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id              = 'reservations';
        $this->header          = 'Reservations';
        $this->url             = 'reservations';
        $this->info            = $this->info();
        $this->calls['create'] = $this->create();
        $this->calls['list']   = $this->all(); // list is a reserved keyword in php
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
                  'name'        => 'onlineBookingReservationsId'
                , 'type'        => 'Integer'
                , 'default'     => '{Generated}'
                , 'description' => 'The ID for the reservation.'
            )
            , array(
                  'name'        => 'onlineBookingsId'
                , 'type'        => 'Integer'
                , 'create'      => 'required'
                , 'update'      => 'available'
                , 'description' => 'The ID of the booking for the reservation.'
            )
            , array(
                  'name'        => 'customersId'
                , 'type'        => 'Integer'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => "The ID of the customer for the reservation."
            )
            , array(
                  'name'        => 'sessionId'
                , 'type'        => 'String'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => "The session ID for the reservation, used for documentation and debugging purposes."
            )
            , array(
                  'name'        => 'quantity'
                , 'type'        => 'Integer'
                , 'create'      => 'required'
                , 'update'      => 'available'
                , 'description' => "The quantity of reservations to hold. This number must be a positive integer."
            )
            , array(
                  'name'        => 'createdAt'
                , 'type'        => 'DateTime'
                , 'default'     => '{Now}'
                , 'create'      => 'available'
                , 'description' => "The number of available bookings. This must be a positive integer."
            )
            , array(
                  'name'        => 'expiresAt'
                , 'type'        => 'DateTime'
                , 'default'     => '{Now + default defined in ControlPanel}'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => "The number of available bookings. This must be a positive integer."
            )
            , array(
                  'name'        => 'onlineBookingReservationStatusId'
                , 'type'        => 'Integer'
                , 'default'     => '1'
                , 'create'      => 'available'
                , 'update'      => 'available'
                , 'description' => ''
                  ."\n<p>"
                  ."\n  The ID for the status of the reservation. Statuses should be set to permanent while the underlying kart is open, and set to permanent after the purchase has been made."
                  ."\n</p>"
                  ."\n<ol>"
                  ."\n  <li>Temporary</li>"
                  ."\n  <li>Permanent</li>"
                  ."\n</ol>"
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
POST http://{$_SERVER['SERVER_NAME']}/api/index.php/reservations HTTP/1.1
{
  "onlineBookingsId": 2,
  "quantity": 2,
  "sessionId": "my_session_id"
}
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "onlineBookingReservationsId": 5
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
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/reservations HTTP/1.1
EOS
                , 'response' => <<<EOS
{
  "reservations": [
    {
      "onlineBookingReservationsId": 5,
      "onlineBookingsId": 2,
      "customersId": null,
      "sessionId": "my_session_id",
      "quantity": 2,
      "createdAt": "2014-10-23 15:06:01.220",
      "expiresAt": "2014-10-23 15:36:01.220",
      "onlineBookingReservationStatusId": 1
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
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/reservations/5 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "reservations": [
    {
      "onlineBookingReservationsId": 5,
      "onlineBookingsId": 2,
      "customersId": null,
      "sessionId": "my_session_id",
      "quantity": 2,
      "createdAt": "2014-10-23 15:06:01.220",
      "expiresAt": "2014-10-23 15:36:01.220",
      "onlineBookingReservationStatusId": 1
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
GET http://{$_SERVER['SERVER_NAME']}/api/index.php/reservations?session_id=my_session_id HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "reservations": [
    {
      "onlineBookingReservationsId": 5,
      "onlineBookingsId": 2,
      "customersId": null,
      "sessionId": "my_session_id",
      "quantity": 2,
      "createdAt": "2014-10-23 15:06:01.220",
      "expiresAt": "2014-10-23 15:36:01.220",
      "onlineBookingReservationStatusId": 1
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
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/reservations?filter=createdAt \$gt 2014-10-23 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "reservations": [
    {
      "onlineBookingReservationsId": 5,
      "onlineBookingsId": 2,
      "customersId": null,
      "sessionId": "my_session_id",
      "quantity": 2,
      "createdAt": "2014-10-23 15:06:01.220",
      "expiresAt": "2014-10-23 15:36:01.220",
      "onlineBookingReservationStatusId": 1
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
PUT https://{$_SERVER['SERVER_NAME']}/api/index.php/reservations/5 HTTP/1.1
{
    "quantity": 1
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
DELETE https://{$_SERVER['SERVER_NAME']}/api/index.php/reservations/5 HTTP/1.1
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
EOS
            )
        );
    }
}