<?php

namespace ClubSpeed\Documentation\API;

class DocRacers Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id     = 'racers';
        $this->header = 'Racers';
        $this->url    = 'racers';
        $this->root = '/api/index.php/racers';
        $this->calls['create'] = $this->create();
        $this->calls['login'] = $this->login();
        $this->calls['fb-login'] = $this->fbLogin();
    }

    private function create() {
        return array(
            'info' => array(
                'access' => 'Private',
                'access_icon' => 'lock',
                'subroute' => '/create',
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
POST http://{$_SERVER['SERVER_NAME']}/api/index.php/racers/create HTTP/1.1
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
  "customerId": 1000001,
  "token": "1bf4e5844129d1fa84110e7aca16ca50576182c72bc3111dba60d615d0f9eb03"
}
EOS
            )
        );
    }

    private function login() {
        return array(
            'info' => array(
                'access' => 'Private',
                'access_icon' => 'lock',
                'url' => $this->root . '/login',
                'subroute' => '/login',
                'verb' => 'POST',
                'verb_icon' => 'export',
                'required' => array(
                    'username',
                    'password'
                ),
                'available' => array(
                ),
                'unavailable' => array(
                )
            ),
            'header' => 'Login',
            'header_icon' => 'export',
            'id' => 'login',
            'type' => 'update',
            'examples' => array(
                'request' => <<<EOS
POST http://{$_SERVER['SERVER_NAME']}/api/index.php/racers/login HTTP/1.1
{
    "username": "bob@clubspeed.com",
    "password": "bobssupersecretpassword"
}
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
{
  "customerId": 1000001,
  "token": "1bf4e5844129d1fa84110e7aca16ca50576182c72bc3111dba60d615d0f9eb03"
}
EOS
            )
        );
    }

    private function fbLogin() {
        return array(
            'info' => array(
                'access' => 'Private',
                'access_icon' => 'lock',
                'url' => $this->root . '/fb_login',
                'subroute' => '/fb_login',
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
                    'facebookExpiresIn',
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
                'unavailable' => array(
                )
            ),
            'header' => 'Facebook Login',
            'header_icon' => 'export',
            'id' => 'fb-login',
            'type' => 'update',
            'examples' => array(
                'request' => <<<EOS
POST http://{$_SERVER['SERVER_NAME']}/api/index.php/racers/fb_login HTTP/1.1
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
  "customerId": 1000001,
  "token": "1bf4e5844129d1fa84110e7aca16ca50576182c72bc3111dba60d615d0f9eb03"
}
EOS
            ),
            'usage' => <<<EOS
<p>
    Note that Facebook Login has the capability to upsert a customer record, if one does not exist.
    If this call is intended to be used to create customer records where they don't exist,
    then parameters taken from the <a href="#racers-create">Customer Create</a> call should be included as parameters here.
</p>
EOS
        );
    }
}