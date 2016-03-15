<?php

namespace ClubSpeed\Documentation\API;

class DocRESTful Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id = 'restful';
        $this->header = 'RESTful';
        $this->calls['restful'] = array(
          'type' => 'info',
          'id' => 'restful',
          'header' => 'Routes',
          'header_icon' => 'info-sign',
          'usage' => <<<EOS
<p>
  The ClubSpeed API maintains a RESTful set of interfaces.
</p>
<p>
  For almost every resource detailed below (with a few exceptions, such as resetting passwords and processing payments),
  the following calls can be made. Wherever <code class="prettyprint">:variable</code> is included in a URL, assume that you should replace
  the variable with the corresponding value.
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
                <tr>
                    <td>GET</td>
                    <td>/api/index.php/resource/:id1/:id2</td>
                    <td>Get single record by composite key</td>
                    <td></td>
                    <td>Object representation of record</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
EOS
        );
    }
}
