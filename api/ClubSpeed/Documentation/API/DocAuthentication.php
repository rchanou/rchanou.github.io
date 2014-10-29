<?php

namespace ClubSpeed\Documentation\API;

class DocAuthentication Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id = 'authentication';
        $this->header = 'Authentication';
        $this->calls['authentication'] = $this->authenticationTypes();
    }

    private function authenticationTypes() {
      return array(
            'type' => 'info'
            , 'id' => 'authentication-types'
            , 'header' => 'Authentication Types'
            , 'header_icon' => 'info-sign'
            , 'usage' => <<<EOS
<p>
  The ClubSpeed API requires one of three types of authentication:
</p>
<ol>
  <li>Public key authentication</li>
  <li>Private key authentication</li>
  <li>Basic authentication</li>
</ol>
<p>
  Public and private key authentication are handled by including the <strong>key</strong> query string in any API call:
</p>
<pre class="prettyprint">/api/index.php/resource?key=TODAYS_PUBLIC_KEY</pre>
<pre class="prettyprint">/api/index.php/resource?key=MY_SECRET_PRIVATE_KEY</pre>
<p>
  <a href="http://tools.ietf.org/html/rfc2617#section-2">Basic authentication</a> can also be used
  by including a basic authorization header where the data following the word Basic
  is a ClubSpeed username and password concatenated with a colon for a delimiter and converted to base64.
</p>
<pre class="prettyprint">
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/resource HTTP/1.1
Authorization: Basic QWxhZGRpbjpvcGVuIHNlc2FtZQ==
</pre>
<p>
  For the purposes of authentication, the ClubSpeed API has two levels: Public and Private.
  Public and Private keys line up respectively, and Basic authentication has Private access.
</p>
EOS
        );
    }
}