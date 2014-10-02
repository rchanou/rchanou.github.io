<?php

namespace ClubSpeed\Documentation\API;

class DocQueryOperations Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id = 'query-operations';
        $this->header = 'Query Operations';
        $this->calls['column-selection'] = array(
            'type' => 'info'
            , 'id' => 'column-selection'
            , 'header' => 'Column Selection'
            , 'header_icon' => 'info-sign'
            , 'usage' => <<<EOS
<p>
  The ClubSpeed API has functionality for pre-filtering properties
  for <span class="glyphicon glyphicon-save"></span>&nbsp;GET operations.
  If only certain properties of a JSON object are desired, the client
  can handle their selection by appending the following query string
  to any <span class="glyphicon glyphicon-save"></span>&nbsp;GET operation:
</p>
<code class="prettyprint">/api/index.php/resource?select=column1, column2, column3</code>
<br>
<br>
<p>
  For example, if we need a list of Online Bookings in order to see
  which types of products are currently available online,
  we could make the following call (note the URI encoding, and the select= portion of the query string): 
</p>
EOS
            , 'examples' => array(
                'request' => <<<EOS
GET https://mytrack.clubspeedtiming.com/api/index.php/booking?debug=1&select=heatId,%20productsId,%20productType HTTP/1.1
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
Accept-Language: en-US,en;q=0.8
EOS
                , 'response' => <<<EOS
HTTP/1.1 200 OK
Date: Tue, 16 Sep 2014 16:58:28 GMT
Content-Length: 262
Content-Type: application/json
{
  "bookings": [
    {
      "heatId": 2,
      "products": [
        {
          "productsId": 8,
          "productType": "MembershipItem"
        },
        {
          "productsId": 11,
          "productType": "MembershipItem"
        }
      ]
    }
  ]
}
EOS
            )
        );

        $this->calls['record-filtering'] = array(
            'type' => 'info'
            , 'id' => 'record-filtering'
            , 'header' => 'Record Filtering'
            , 'header_icon' => 'info-sign'
            , 'usage' => <<<EOS
<p>
  The ClubSpeed API has functionality for filtering data sets by using grouped comparators
  for <span class="glyphicon glyphicon-save"></span>&nbsp;GET operations.
  If only certain records from the database are desired, the client
  may filter out those records by appending any of the following query strings
  to any <span class="glyphicon glyphicon-save"></span>&nbsp;GET operation:
</p>
<code class="prettyprint">/api/index.php/resource?filter=column1 {comparator} value1</code><br>
<code class="prettyprint">/api/index.php/resource?filter=column1 {comparator} column2</code><br>
<code class="prettyprint">/api/index.php/resource?filter=column1 {comparator} value1 {connector} column2 {comparator} value2</code>
<br>
<h4>
  Available Comparators:
</h4>
<ul>
  <li><code class="prettyprint">&lt;</code></li>
  <li><code class="prettyprint">&lt;=</code></li>
  <li><code class="prettyprint">&gt;</code></li>
  <li><code class="prettyprint">&gt;=</code></li>
  <li><code class="prettyprint">=</code></li>
  <li><code class="prettyprint">!=</code></li>
  <li><code class="prettyprint">&lt;&gt;</code></li>
  <li><code class="prettyprint">IS</code></li>
  <li><code class="prettyprint">IS NOT</code></li>
  <li><code class="prettyprint">%lt; ( equivalent to &lt; )</code></li>
  <li><code class="prettyprint">%lte; ( equivalent to &lt;= )</code></li>
  <li><code class="prettyprint">%gt; ( equivalent to &gt; )</code></li>
  <li><code class="prettyprint">%gte; ( equivalent to &gt;= )</code></li>
  <li><code class="prettyprint">%eq; ( equivalent to = )</code></li>
  <li><code class="prettyprint">%neq; ( equivalent to != )</code></li>
</ul>
<h4>
  Available Connectors
</h4>
<ul>
  <li><code class="prettyprint">AND</code></li>
  <li><code class="prettyprint">OR</code></li>
</ul>
<p>
<br>
  For example, to collect all checkTotals records
  where the checkTax is greater than 17.00,
  and the openedDate is greater than or equal to 2014&#8209;09&#8209;24,
  we could make the following call 
  (note the URI encoding, and the filter= portion of the query string):
</p>
EOS
        , 'examples' => array(
            'request' => <<<EOS
GET https://mytrack.clubspeedtiming.com/api/index.php/checkTotals?&filter=checkTax%20%3E%2017.00%20AND%20openedDate%20%3E%3D%202014-09-24&select=checkId,%20openedDate,%20checkTotal,%20checkTax,%20checkSubtotal,%20checkDetailId,%20checkDetailSubtotal,%20checkDetailTax,%20checkDetailTotal HTTP/1.1
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
EOS
            , 'response' => <<<EOS
HTTP/1.1 200 OK
Date: Wed, 24 Sep 2014 23:44:06 GMT
Content-Length: 1014
Content-Type: application/json
{
  "checks": [
    {
      "checkId": 2356,
      "openedDate": "2014-09-24",
      "checkSubtotal": 85,
      "checkTax": 17.85,
      "checkTotal": 102.85,
      "details": [
        {
          "checkDetailId": 7601,
          "checkDetailSubtotal": 15,
          "checkDetailTax": 3.15,
          "checkDetailTotal": 18.15
        },
        {
          "checkDetailId": 7602,
          "checkDetailSubtotal": 70,
          "checkDetailTax": 14.7,
          "checkDetailTotal": 84.7
        }
      ]
    },
    {
      "checkId": 2357,
      "openedDate": "2014-09-24",
      "checkSubtotal": 85,
      "checkTax": 17.85,
      "checkTotal": 102.85,
      "details": [
        {
          "checkDetailId": 7603,
          "checkDetailSubtotal": 15,
          "checkDetailTax": 3.15,
          "checkDetailTotal": 18.15
        },
        {
          "checkDetailId": 7604,
          "checkDetailSubtotal": 70,
          "checkDetailTax": 14.7,
          "checkDetailTotal": 84.7
        }
      ]
    }
  ]
}
EOS
            )
        );

        $this->calls['property-matching'] = array(
            'type'          => 'info'
            , 'id'          => 'property-matching'
            , 'header'      => 'Property Matching'
            , 'header_icon' => 'info-sign'
            , 'usage'       => <<<EOS
<p>
  Property matching is a simpler version of this API's record filtering.
  The functionality is the same, but can only be used for matching property values exactly.
  This type of call may be used on <span class="glyphicon glyphicon-save"></span>&nbsp;GET operations,
  and can be used in tandem with <a href=#query-operations-column-selection>Column Selection</a> 
  but may <i>not</i> be used at the same time as <a href=#query-operations-record-filtering>Record Filtering</a>.
  The syntax is as below:
</p>
<code class="prettyprint">/api/index.php/resource?column1=value1</code><br>
<code class="prettyprint">/api/index.php/resource?column1=value1&column2=value2</code><br>
<br>
<p>
  For example, to collect all screenTemplateDetail records
  which have a parent screenTemplateId of 3, we could make the following call
  (note the screenTemplateId= portion of the query string):
</p>
EOS
        , 'examples' => array(
            'request' => <<<EOS
GET https://mytrack.clubspeedtiming.com/api/index.php/screenTemplateDetail?screenTemplateId=3&select=screenTemplateId,screenTemplateDetailId,trackNo,timeInSecond,seq HTTP/1.1
Authorization: Basic c29tZXVzZXI6c29tZXBhc3N3b3Jk
EOS
            , 'response' => <<<EOS
HTTP/1.1 200 OK
Date: Fri, 26 Sep 2014 16:34:27 GMT
Content-Length: 305
Content-Type: application/json
{
  "channelDetail": [
    {
      "screenTemplateDetailId": 21,
      "screenTemplateId": 3,
      "seq": 4,
      "timeInSecond": 30,
      "trackNo": 1
    },
    {
      "screenTemplateDetailId": 65,
      "screenTemplateId": 3,
      "seq": 3,
      "timeInSecond": 15,
      "trackNo": 1
    }
  ]
}
EOS
            )
        );
    }
}