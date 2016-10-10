<?php

namespace ClubSpeed\Documentation\API;

class DocUsers Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id      = 'users';
        $this->header  = 'Users';
        $this->url     = 'users';
        $this->info    = $this->info();
        $this->version = 'V2';
        $this->json    = $this->json();
        $this->preface = $this->preface();
    }

    private function preface() {
        return <<<EOS
<h4>Description</h4>
<p>
    The <code class="prettyprint">Users</code> resource represents employees and system users.
</p>
EOS;
    }

    private function json() {
        return <<<EOS
{
    "userId": 6,
    "firstname": "Steve",
    "lastname": "Stevens",
    "username": "the_steve",
    "cardId": null,
    "enabled": true,
    "email": "test@somewhere.com",
    "phone": "1234567890",
    "deleted": false,
    "empStartDate": "2010-00-00T00:00:00.00",
    "isSystemUser": false
}
EOS;
    }

    private function info() {
        return array(
            array(
                "name" => "userId",
                "type" => "Integer",
                "default" => "{Generated}",
                "required" => false,
                "description" => "The primary key for the record"
            ),
            array(
                "name" => "cardId",
                "type" => "Integer",
                "default" => "",
                "required" => false,
                "description" => "The card id for the user"
            ),
            array(
                "name" => "deleted",
                "type" => "Boolean",
                "default" => "false",
                "required" => false,
                "description" => "A flag indicating whether the user has been deleted"
            ),
            array(
                "name" => "email",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The email address for the user"
            ),
            array(
                "name" => "empStartDate",
                "type" => "DateTime",
                "default" => "",
                "required" => false,
                "description" => "The date at which the user started employment"
            ),
            array(
                "name" => "enabled",
                "type" => "Boolean",
                "default" => "true",
                "required" => false,
                "description" => "A flag indicating whether the user is considered enabled"
            ),
            array(
                "name" => "firstname",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The first name of the user"
            ),
            array(
                "name" => "lastname",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The last name of the user"
            ),
            array(
                "name" => "isSystemUser",
                "type" => "Boolean",
                "default" => "false",
                "required" => false,
                "description" => "A flag indicating whether the user is a system / application user, or a track employee"
            ),
            array(
                "name" => "password",
                "type" => "String",
                "default" => "",
                "required" => true,
                "description" => "The password for the user. Note that this field can be created and updated, but not read"
            ),
            array(
                "name" => "phoneNumber",
                "type" => "String",
                "default" => "",
                "required" => false,
                "description" => "The phone number for the user"
            ),
            array(
                "name" => "username",
                "type" => "String",
                "default" => "",
                "required" => true,
                "description" => "The username for the user. To be used with logins"
            ),
            array(
                "name" => "webPassword",
                "type" => "String",
                "default" => "{User.password}",
                "required" => true,
                "description" => "The password of the user to be used for logging in to a web portal, such as the admin panel or posting to <code class=\"prettyprint\">/users/login</code>. Will use <code class=\"prettyprint\">User.password</code> as a default, if not <code class=\"prettyprint\">webPassword</code> is not provided during a create. Note that this field can be created and updated, but not read"
            )
        );
    }
}
