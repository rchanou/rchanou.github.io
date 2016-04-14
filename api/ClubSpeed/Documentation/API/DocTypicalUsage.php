<?php

namespace ClubSpeed\Documentation\API;

class DocTypicalUsage Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id     = 'typical-usage';
        $this->header = 'Typical Usage';
        // $this->url    = 'racers';
        // $this->root = '/api/index.php/racers';
        $this->calls['booking'] = $this->booking();
    }

    private function booking() {
        return array(
            'header' => 'Online Booking',
            'header_icon' => 'info-sign',
            'id' => 'booking',
            'type' => 'info',
            'usage' => <<<EOS
<p>
    This section is a collection and description of the API methods 
    which are expected to be called in order to use a custom front-end with ClubSpeed's Online booking.
</p>
<ol>
    <li>
        <a href="#booking-create">Create an Online Booking</a>
        <ul>
            <li>
                The Online Booking is considered to be a container for available reservations for a selected race.
                Typically, this portion would already be created through the new ClubSpeed admin panel.
            </li>
        </ul>
    </li>
    <li>
        <a href="#booking-availability-list">Get a List of Available Online Bookings</a>
        <ul>
            <li>
                If the Booking containers are already created, then the result from this call should be used
                to show customers which spaces are available for booking.
            </li>
            <li>
                When collecting these, special consideration should be taken for the <code>heatId</code> in the response,
                as it will be needed when the customer checks out by using <a href=#process-payment-process>Process Payment</a>.
            </li>
        </ul>
    </li>
    <li>
        Customer Access and Authentication
        <ul>
            <li><a href=#racers-login>Standard Login</a></li>
            <li><a href=#racers-fb-login>Facebook Login</a></li>
            <li><a href=#racers-create>New Standard Customer</a></li>
        </ul>
    </li>
    <li>
        <a href=#reservations-create>Create an Online Booking Reservation</a>
        <ul>
            <li>
                A temporary booking reservation should be made at the point a customer adds a race to their cart.
                As the expectation is to have the cart maintained by the front-end, making a reservation
                will prevent accidental overbooking.
            </li>
            <li>
                These temporary reservations can be upgraded to permanence
                after the final purchase is made,
                or deleted if the item is removed from the cart.
            </li>
        </ul>
    </li>
    <li>
        <a href=#check-totals-virtual>Create a Virtual Check</a>
        <ul>
            <li>
                This virtual check should be used to calculate
                Totals, Subtotals, and Taxes for the upcoming check and line items
                without creating the permanent check in the database.
            </li>
        </ul>
    </li>
    <li>
        <a href=#check-totals-create>Create a Permanent Check</a>
        <ul>
            <li>
                The permanent check should be created at the point
                the customer commits to purchasing all items in the cart.
            </li>
        </ul>
    </li>
    <li>
        <a href=#check-totals-single>Load the Permanent Check data by CheckID</a>
        <ul>
            <li>
                This should be done to ensure data integrity with the database,
                and to ensure all Check calculations have been made by the server logic.
            </li>
        </ul>
    </li>
    <li>
        <a href=#process-payment-process>Process Payment</a>
    </li>
    <li>
        <a href=#reservations-update>Update each Reservation to be Permanent</a>
        <ul>
            <li>
                Once the purchase has been made, any temporary reservations for the cart
                should be upgraded to be permanent. 
            </li>
        </ul>
    </li>
</ol>
EOS
        );
    }

    private function fbLogin() {
        return array(
            'info' => array(
                'access' => 'Private',
                'access_icon' => 'lock',
                'url' => $this->root . '/fb_login',
                'verb' => 'POST',
                'verb_icon' => 'export',
                'required' => array(
                    'email',
                    'facebookId',
                    'facebookToken',
                    'facebookAllowEmail',
                    'facebookAllowPost',
                    'facebookEnabled'
                ),
                'available' => array(
                    'facebookExpiresIn'
                ),
                'unavailable' => array(
                )
            ),
            'header' => 'Facebook Login',
            'header_icon' => 'export',
            'id' => 'fb-login',
            'type' => 'update',
            'examples' => array(
                'request' => <<<EOS
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/racers/fb_login HTTP/1.1
{
  "email": "bob@clubspeed.com",
  "facebookId": "652712592679",
  "facebookToken":"AVNAWIVYANIWVUDBAWKUGDVBAWIDVYNLAWDVHNAWILDVHUNAWIULDVHNLAWIDVHUNAWUILDVHNAWILDVHN",
  "facebookExpiresIn": 9999,
  "facebookAllowEmail": true,
  "facebookAllowPost": true,
  "facebookEnabled": true
}
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "customerId": 1000001
}
EOS
            )
        );
    }

    private function create() {
        return array(
            'info' => array(
                'access' => 'Private',
                'access_icon' => 'lock',
                'url' => $this->root . '/create',
                'verb' => 'POST',
                'verb_icon' => 'export',
                'required' => array(
                    'racername',
                    'email',
                    'password',
                    'donotemail',
                    'firstname',
                    'lastname',
                    'birthdate',
                    'gender',
                    'mobilephone',
                    'Address',
                    'Address2',
                    'City',
                    'Country',
                    'howdidyouhearaboutus',
                    'State',
                    'Zip'
                ),
                'available' => array(
                ),
                'unavailable' => array(
                )
            ),
            'usage' => <<<EOS
<p>
    Parameter requirements will vary from track to track, based on sp_admin control panel settings.
</p>
<p>
    If a password is provided, it will be hashed before being placed in the database.
</p>
EOS
,
            'header' => 'Create Customer',
            'header_icon' => 'plus',
            'id' => 'create',
            'type' => 'create',
            'examples' => array(
                'request' => <<<EOS
POST https://{$_SERVER['SERVER_NAME']}/api/index.php/racers/create HTTP/1.1
{
  "racername": "JimBobbyJoe",
  "email": "bob@clubspeed.com",
  "password": "bobssupersecretpassword",
  "donotemail": false,
  "firstname": "Jim",
  "lastname": "Joe",
  "birthdate": "1952-01-01",
  "gender": "male",
  "mobilephone": "123-456-7890",
  "Address": "123 Somewhere St",
  "Address2": "Apartment 1",
  "City": "Timbucktu",
  "Country": "Mali",
  "howdidyouhearaboutus": 1,
  "State": "CA",
  "Zip": 12345
}
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "customerId": 1000001
}
EOS
            )
        );
    }

//     [create] => Array
//         (
//             [info] => Array
//                 (
//                     [access] => Private
//                     [access_icon] => lock
//                     [url] => /api/index.php/reservations
//                     [verb] => POST
//                     [verb_icon] => export
//                     [required] => Array
//                         (
//                             [0] => onlineBookingsId
//                             [1] => quantity
//                         )

//                     [available] => Array
//                         (
//                             [0] => customersId
//                             [1] => sessionId
//                             [2] => createdAt
//                             [3] => expiresAt
//                             [4] => onlineBookingReservationStatusId
//                         )

//                     [unavailable] => Array
//                         (
//                             [0] => onlineBookingReservationsId
//                         )

//                 )

//             [examples] => Array
//                 (
//                     [request] => POST http://{$_SERVER['SERVER_NAME']}/api/index.php/reservations HTTP/1.1
// {
//   "onlineBookingsId": 2,
//   "quantity": 2,
//   "sessionId": "my_session_id"
// }
//                     [response] => HTTP/1.1 200 OK
// {
//   "onlineBookingReservationsId": 5
// }
//                 )

//             [header] => Create
//             [header_icon] => plus
//             [id] => create
//             [type] => create
//         )
}