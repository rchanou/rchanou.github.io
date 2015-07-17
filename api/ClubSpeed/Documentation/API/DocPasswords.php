<?php

namespace ClubSpeed\Documentation\API;

class DocPasswords Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id                               = 'passwords';
        $this->header                           = 'Passwords';
        $this->url                              = 'passwords';
        $this->calls['password-reset-generate'] = $this->passwordResetGenerate();
        $this->calls['password-reset-consume']  = $this->passwordResetConsume();
        $this->expand();
    }

    private function passwordResetGenerate() {
        return array(
          'type' => 'create'
          , 'info' => array(
              'verb' => 'POST'
              , 'verb_icon' => 'export'
              , 'url' => '/api/index.php/' . $this->url
          )
          , 'id' => 'password-reset-generate'
          , 'header' => 'Generate Reset Token'
          , 'header_icon' => 'plus'
          , 'usage' => <<<EOS
<p>
  The method detailed below will generate and send a password reset token
  to a provided email address. This email will contain a provided url with appended token
  and instructions for the end-user to reset their password.
</p>
EOS
          , 'examples' => array(
              'request' => <<<EOS
POST http://{$_SERVER['SERVER_NAME']}/api/index.php/passwords HTTP/1.1
{
  "email": "bob@gmail.com",
  "url": "http://link/to/append/token/to.html"
}
EOS
              , 'response' => <<<EOS
HTTP/1.1 200 OK
EOS
            )
        );
    }

    private function passwordResetConsume() {
        return array(
          'type' => 'update'
          , 'info' => array(
              'verb' => 'PUT'
              , 'verb_icon' => 'pencil'
              , 'url' => '/api/index.php/' . $this->url
          )
          , 'id' => 'password-reset-consume'
          , 'header' => 'Consume Reset Token'
          , 'header_icon' => 'pencil'
          , 'usage' => <<<EOS
<p>
  The call detailed below will consume a password reset token
  and update the customer's password using a lookup based on the token.
</p>
EOS
          , 'examples' => array(
              'request' => <<<EOS
PUT http://{$_SERVER['SERVER_NAME']}/api/index.php/passwords HTTP/1.1
{
    "token": "81f6fde27692402fb2139f971b8accb29b1b20c6",
    "password": "some_new_password"
}
EOS
              , 'response' => <<<EOS
HTTP/1.1 200 OK
EOS
            )
        );
    }
}