<?php

namespace ClubSpeed\Documentation\API;

class DocRESTful Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id = 'restful';
        $this->header = 'RESTful';
        $this->calls['routes'] = array(
          'type' => 'info',
          'id' => 'routes',
          'header' => 'Routes',
          'header_icon' => 'info-sign',
          'preface' => <<<EOS
<p>
  The ClubSpeed API implements a RESTful set of interfaces.
</p>
<p>
  For almost every resource detailed below (with a few exceptions,
  such as resetting passwords, processing payments, and read-only resources),
  the following calls can be made.
</p>
<div class="row">
    <div class="col-xs-12">
        <table class="table">
            <thead>
                <tr>
                    <th>Method</th>
                    <th>Route</th>
                    <th>Action</th>
                    <th>Request Body</th>
                    <th>Response Body</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>GET</td>
                    <td>/api/index.php/resource</td>
                    <td>List all records</td>
                    <td></td>
                    <td>Object or array representation of records</td>
                </tr>
                <tr>
                    <td>GET</td>
                    <td>/api/index.php/resource/:id</td>
                    <td>Get single record</td>
                    <td></td>
                    <td>Object representation of record</td>
                </tr>
                <tr>
                    <td>POST</td>
                    <td>/api/index.php/resource</td>
                    <td>Create single record</td>
                    <td>Object to be created</td>
                    <td>ID of the created object</td>
                </tr>
                <tr>
                    <td>PUT</td>
                    <td>/api/index.php/resource/:id</td>
                    <td>Update single record</td>
                    <td>Object containing updates</td>
                    <td></td>
                </tr>
                <tr>
                    <td>DELETE</td>
                    <td>/api/index.php/resource/:id</td>
                    <td>Delete single record</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>GET</td>
                    <td>/api/index.php/resource/count</td>
                    <td>Count of records</td>
                    <td></td>
                    <td>Number representing record count</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<p>
  Wherever a word prefixed with <code class="prettyprint">:</code> appears in a URL, such as
  <code class="prettyprint">:variable</code>, you should replace
  <code class="prettyprint">:variable</code> with its corresponding value.
</p>
<p>
  If a resource is noted to be read-only,
  then you should assume that only <code class="prettyprint">GET</code> methods are available.
</p>
<br>
<p>
  Also note that some resources make use of composite primary keys.
  When a composite key is being used, then each piece of the composite key
  will correspond to one <code class="prettyprint">:id</code> in the route,
  placed in the order they appear in the documentation.
</p>
<p>
  For example, to
  <code class="prettyprint">GET</code>,
  <code class="prettyprint">PUT</code>,
  or <code class="prettyprint">DELETE</code>
  a <a href="#heat-details">HeatDetail</a> record,
  the relevant route will be:
  <code class="prettyprint">/api/index.php/heatdetails/:heatId/:customerId</code>
</p>
EOS
        );

        $this->calls['status-codes'] = array(
          'type' => 'info',
          'id' => 'status-codes',
          'header' => 'Status Codes',
          'header_icon' => 'info-sign',
          'preface' => <<<EOS
<p>
  An HTTP response containing a status code in the 2XX range, typically <code class="prettyprint">200 OK</code>,
  indicates that the ClubSpeed API call was made successfully.
</p>
<pre class="prettyprint">
HTTP/1.1 200 OK
</pre>
<br>
<p>
  Any other response codes, typically in the 4XX or 5XX range, should be considered a failed API call.
</p>
<p>
  In the case of a failed API call, the response body will contain an error in the following format,
  where error.code is a copy of the HTTP status code, and error.message contains a readable error message
  indicating an accidental logical error
  (attempting to finalize an unbalanced check, not providing a required field on create, etc)
  or an expected server error.
</p>
<pre class="prettyprint">
HTTP/1.1 500 Internal Server Error
{
  "error": {
    "code": 500,
    "message": "Internal Server Error: Unable to make connection to mail server!"
  }
}
</pre>

EOS
        );
    }
}
