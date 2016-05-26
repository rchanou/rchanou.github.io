<?php

namespace ClubSpeed\Documentation\API;

class DocQueryOperations Extends DocAPIBase {

    public function __construct() {
        parent::__construct();

        $this->id = 'query-operations';
        $this->header = 'Query Operations';
        $this->calls['versioning'] = array(
          'type' => 'info',
          'id' => 'versioning',
          'header' => 'Versioning',
          'header_icon' => 'info-sign',
          'preface' => <<<EOS
<p>
  At the current point in time, the ClubSpeed API has added a modified querying system
  in order to improve functionality available with each standard resource endpoint (excluding
  extensions such as processing payments and resetting passwords). This includes improved support
  for complex filtering logic, and adds pagination and ordering to the system.
</p>
<p>
  For simplicity, we will refer to the new calls as <code class="prettyprint">V2</code> methods,
  and previously existing methods as <code class="prettyprint">V1</code>.
</p>
<p>
  The new V2 querying syntax, as well as pagination and ordering will all be available with all resource calls.
  The original V1 syntax for Record Filtering and Property Matching will remain intact 
  for previously existing calls in order to prevent breaking changes. However,
  it will <em>not</em> work for any newly added calls, and we <em>strongly</em> suggest using the new syntax
  to avoid potential future breaking changes, and in order to support fuller querying capabilities.
</p>
<p>
  Each call specified below will notate which version it falls under, which will indicate
  which type of querying will be available to the call.
</p>
EOS
        );
        $this->calls['column-selection'] = array(
            'type' => 'info'
            , 'id' => 'column-selection'
            , 'header' => 'Column Selection'
            , 'header_icon' => 'info-sign'
            , 'preface' => <<<EOS
<p>
  The ClubSpeed API has functionality for selecting specific columns / properties
  for <span class="glyphicon glyphicon-save"></span>&nbsp;GET operations.
  If only certain properties of a JSON object are desired, the client
  can handle their selection by appending the following query string
  to any <span class="glyphicon glyphicon-save"></span>&nbsp;GET operation:
</p>
<code class="prettyprint">/api/index.php/resource?select=column1, column2, column3</code>
<br>
<br>
<p>
  For example, if we want to select <code class="prettyprint">paymentId</code>,
  <code class="prettyprint">payDate</code>, <code class="prettyprint">payAmount</code>,
  and <code class="prettyprint">payTax</code> from the Payments resource,
  the following call could be made.
</p>
<pre class="prettyprint">
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/payments?select=paymentId,payDate,payAmount,payTax HTTP/1.1
</pre>
<pre class="prettyprint">
HTTP/1.1 200 OK
[
  {
    "paymentId": 5048,
    "payDate": "2016-03-07T11:46:22.00",
    "payAmount": 2,
    "payTax": 0.14
  },
  {
    "paymentId": 5049,
    "payDate": "2016-03-07T11:46:25.00",
    "payAmount": 2,
    "payTax": 0.14
  }
]
</pre>
EOS
        );

        $this->calls['record-filtering-v2'] = array(
            'type'          => 'info'
            , 'id'          => 'record-filtering-v2'
            , 'header'      => 'Record Filtering'
            , 'header_icon' => 'info-sign'
            , 'preface'       => <<<EOS
<p>
  The suggested syntax for ClubSpeed API filtering for <span class="glyphicon glyphicon-save"></span>&nbsp;GET operations
  uses a JSON object syntax to declare the desired filter. The syntax is as below, and <em>must</em> be valid, parseable JSON.
  Note that the examples given in this documentation will not be URI encoded for readability,
  but JSON encoding then URI encoding is the suggested way of sending this information.
</p>
<code class="prettyprint">/api/index.php/resource?where={ "column1": "value" }</code><br>
<code class="prettyprint">/api/index.php/resource?where={ "column1": { "comparator": "value" }</code><br>
<br>
<p>
  The syntax we use here is modelled and influenced by other libraries such as
  <a href="http://docs.sequelizejs.com/en/latest/docs/querying/#where">Sequelize</a>
  and <a href="https://docs.mongodb.org/manual/reference/operator/query/">MongoDB</a>.
</p>
<br>
<h4>
  Available Comparison Operators
</h4>
<div class="row">
  <div class="col-xs-12">
    <table class="table">
      <thead>
        <tr>
          <th>Property</th>
          <th>Example</th>
          <th>Parsed</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>\$lt</td>
          <td><pre class="prettyprint">{"col":{"\$lt":1}}</pre></td>
          <td><pre class="prettyprint">[col] < 1</pre></td>
          <td></td>
        </tr>
        <tr>
          <td>\$lte</td>
          <td><pre class="prettyprint">{"col":{"\$lte":1}}</pre></td>
          <td><pre class="prettyprint">[col] <= 1</pre></td>
          <td></td>
        </tr>
        <tr>
          <td>\$eq</td>
          <td><pre class="prettyprint">{"col":{"\$eq":1}}</pre></td>
          <td><pre class="prettyprint">[col] = 1</pre></td>
          <td>If a value is used in place of a comparison object, then an implicit \$eq will be applied to it (see first example).</td>
        </tr>
        <tr>
          <td>\$gt</td>
          <td><pre class="prettyprint">{"col":{"\$gt":1}}</pre></td>
          <td><pre class="prettyprint">[col] > 1</pre></td>
          <td></td>
        </tr>
        <tr>
          <td>\$gte</td>
          <td><pre class="prettyprint">{"col":{"\$gte":1}}</pre></td>
          <td><pre class="prettyprint">[col] >= 1</pre></td>
          <td></td>
        </tr>
        <tr>
          <td>\$ne, \$neq</td>
          <td><pre class="prettyprint">{"col":{"\$ne":1}}</pre></td>
          <td><pre class="prettyprint">[col] != 1</pre></td>
          <td></td>
        </tr>
        <tr>
          <td>\$is</td>
          <td><pre class="prettyprint">{"col":{"\$is":null}}</pre></td>
          <td><pre class="prettyprint">[col] IS NULL</pre></td>
          <td>To be used with NULL</td>
        </tr>
        <tr>
          <td>\$isnot</td>
          <td><pre class="prettyprint">{"col":{"\$isnot":null}}</pre></td>
          <td><pre class="prettyprint">[col] IS NOT NULL</pre></td>
          <td>To be used with NULL</td>
        </tr>
        <tr>
          <td>\$lk, \$like</td>
          <td><pre class="prettyprint">{"col":{"\$lk":"s%"}}</pre></td>
          <td><pre class="prettyprint">[col] LIKE 's%'</pre></td>
          <td></td>
        </tr>
        <tr>
          <td>\$nlk, \$notlike</td>
          <td><pre class="prettyprint">{"col":{"\$nlk":"s%"}}</pre></td>
          <td><pre class="prettyprint">[col] NOT LIKE 's%'</pre></td>
          <td></td>
        </tr>
        <tr>
          <td>\$in</td>
          <td><pre class="prettyprint">{"col":{"\$in":[1,2]}}</pre></td>
          <td><pre class="prettyprint">[col] IN (1, 2)</pre></td>
          <td>To be used with arrays of values</td>
        </tr>
        <tr>
          <td>\$nin, \$notin</td>
          <td><pre class="prettyprint">{"col":{"\$nin":[1,2]}}</pre></td>
          <td><pre class="prettyprint">[col] NOT IN (1, 2)</pre></td>
          <td>To be used with arrays of values</td>
        </tr>
        <tr>
          <td>\$has, \$contains</td>
          <td><pre class="prettyprint">{"col":{"\$has":"s"}}</pre></td>
          <td><pre class="prettyprint">[col] LIKE '%s%'</pre></td>
          <td>Automatically surrounds value with % signs, then uses LIKE operator</td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<h4>
  Available Logical Operators
</h4>
<div class="row">
  <div class="col-xs-12">
    <table class="table">
      <thead>
        <tr>
          <th>Property</th>
          <th>Example</th>
          <th>Parsed</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>\$and</td>
          <td><pre class="prettyprint">{"\$and": [{"col1":1},{"col2":2}]}</pre></td>
          <td><pre class="prettyprint">[col1] = 1 AND [col2] = 2</pre></td>
          <td>Note that if multiple comparators are provided in the same object, then an implicit \$and grouping will be applied to them. See below for examples.</td>
        </tr>
        <tr>
          <td>\$or</td>
          <td><pre class="prettyprint">{"\$or": [{"col1":1},{"col2":2}]}</pre></td>
          <td><pre class="prettyprint">[col1] = 1 OR [col2] = 2</pre></td>
          <td></td>
        </tr>
        <tr>
          <td>\$not</td>
          <td><pre class="prettyprint">{"\$not": { "col": 1 } }</pre></td>
          <td><pre class="prettyprint">NOT ( [col] = 1 )</pre></td>
          <td></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>
<div class="row">
  <div class="col-xs-12">
    <h4>
      Examples
    </h4>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?where={ "amount": 43.2 } </pre>
    <pre> [amount] = 43.2 </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?where={ "amount": 43.2, "type": 2 } </pre>
    <pre> [amount] = 43.2 AND [type] = 2 </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?where={ "amount": null } </pre>
    <pre> [amount] IS NULL </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?where={ "amount": { "\$gte": 43.2 } } </pre>
    <pre> [amount] >= 43.2 </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?where={ "amount": { "\$gte": 43.2, "\$lte": 55.7 } } </pre>
    <pre> ([amount] >= 43.2 AND [amount] <= 55.7) </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?where={ "amount": 43.2, "timestamp": { \$gte: "2016-01-01T00:00:00" } } </pre>
    <pre> ([amount] = 43.2 AND [timestamp] >= '2016-01-01T00:00:00') </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?where={ "\$or": [ { "amount": 43.2 }, { "userId" : { "\$nin": [ 1, 2, 3 ] } } ] } </pre>
    <pre> ([amount] = 43.2) OR ([userId] NOT IN (1, 2, 3)) </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?where={ "\$or": [ { "amount": 43.2, "type": 2 }, { "userId" : { "\$nin": [ 1, 2, 3 ] } } ] } </pre>
    <pre> ([amount] = 43.2 AND [type] = 2) OR ([userId] NOT IN (1, 2, 3)) </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?where={ "notes": { "\$has": "Last Tuesday" } } </pre>
    <pre> [notes] LIKE '%Last Tuesday%' </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?where={
  "\$not": {
    "\$or": [
      { "transaction": null },
      { "\$and": [
        { "payType": 3 },
        { "payStatus": { "\$neq": 2 } }
      ]}
    ]
  }
}</pre>
    <pre>NOT (
  [transaction] IS NULL
  OR (
    [payType] = 3
    AND [payStatus] != 2
  )
)</pre>
  </div>
</div>
EOS
        );

        $this->calls['ordering'] = array(
            'type'          => 'info'
            , 'id'          => 'ordering'
            , 'header'      => 'Ordering'
            , 'header_icon' => 'info-sign'
            , 'preface'     => <<<EOS
<p>
  ClubSpeed API calls have the ability to apply orders
  for <span class="glyphicon glyphicon-save"></span>&nbsp;GET operations,
  by supplying a comma delimited string of columns and their directions.
</p>
<p>
  The syntax is as below:
</p>
<code class="prettyprint">/api/index.php/resource?order=column1</code>
<br>
<code class="prettyprint">/api/index.php/resource?order=column1 DIRECTION</code>
<br>
<code class="prettyprint">/api/index.php/resource?order=column1 DIRECTION, column2 DIRECTION</code>
<br>
<br>
<p>
  Note that direction can be one of either <code class="prettyprint">ASC</code> or <code class="prettyprint">DESC</code>.
  If a direction is not provided, then <code class="prettyprint">ASC</code> is used as a default.
</p>
<div class="row">
  <div class="col-xs-12">
    <h4>
      Examples
    </h4>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?order=column1 </pre>
    <pre> ORDER BY [column1] ASC </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?order=column1 DESC </pre>
    <pre> ORDER BY [column1] DESC </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?order=column1,column2 </pre>
    <pre> ORDER BY [column1] ASC, [column2] ASC </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?order=column1 ASC, column2 DESC </pre>
    <pre> ORDER BY [column1] ASC, [column2] DESC </pre>
  </div>
</div>
EOS
        );

        $this->calls['pagination'] = array(
            'type'          => 'info'
            , 'id'          => 'pagination'
            , 'header'      => 'Pagination / Limiting'
            , 'header_icon' => 'info-sign'
            , 'preface'       => <<<EOS
<p>
  ClubSpeed API calls have multiple parameters which can be used to paginate results
  for <span class="glyphicon glyphicon-save"></span>&nbsp;GET operations.
</p>
<p>
  The syntax is as below:
</p>
<code class="prettyprint">/api/index.php/resource?limit=NUMBER</code>
<br>
<code class="prettyprint">/api/index.php/resource?skip=NUMBER</code>
<br>
<br>
<p>
  <code class="prettyprint">limit</code> will take up to the provided number of records.
  If the limit goes beyond the available number of records,
  then all available records will be returned. If no limit is provided,
  then a default of 100 will be used. Note that for V1 calls,
  this limit is <em>not</em> imposed for backwards compatibility
  with existing code.
</p>
<p>
  <code class="prettyprint">skip</code> will offset by up to the provided number of records.
  Note that if the offset moves beyond the available number of records,
  an empty result will be returned. This parameter is entirely optional.
</p>
<p>
Note that <code class="prettyprint">offset</code> can be used as an alias for <code class="prettyprint">skip</code>,
and <code class="prettyprint">take</code> can be used as an alias for <code class="prettyprint">limit</code>
</p>
<p>
  These pagination parameters work in tandem with <a href="#query-operations-record-filtering-v2">Record Filtering</a>
  and <a href="#query-operations-ordering">Ordering</a>.
</p>
<div class="row">
  <div class="col-xs-12">
    <h4>
      Examples
    </h4>
  </div>
  <div class="col-xs-12">
    <pre class="prettyprint"> ?limit=50 </pre>
  </div>
  <div class="col-xs-12">
    <pre class="prettyprint"> ?limit=50&skip=50 </pre>
  </div>
  <div class="col-xs-12">
    <pre class="prettyprint"> ?where={"amount":{"\$gte":50}}&skip=50&take=10 </pre>
  </div>
  <div class="col-xs-12">
    <pre class="prettyprint"> ?limit=10&order=recordId DESC </pre>
  </div>
</div>
EOS
        );

        $this->calls['response-types'] = array(
            'type'          => 'info'
            , 'id'          => 'response-types'
            , 'header'      => 'Response Types'
            , 'header_icon' => 'info-sign'
            , 'preface'       => <<<EOS
<p>
  For any response which returns a response body, the output can be formatted as either JSON or XML.
</p>
<p>
  In order to pick the response body content type, you can do one of two things:
</p>
<ol>
  <li>Supply the <code class="prettyprint">Accept</code> header</li>
  <li>Add <code class="prettyprint">.json</code> or <code class="prettyprint">.xml</code> to the resource route</li>
</ol>
<p>
  We recommend using the <code class="prettyprint">Accept</code> header for both performance and standards compliance, but both methods are functional.
</p>
<br>
<p>
  For example, the next two code blocks show HTTP calls which request a <code class="prettyprint">JSON</code> encoded response body
</p>
<pre class="prettyprint">
GET /api/index.php/payments HTTP/1.1
Accept: application/json
</pre>
<pre class="prettyprint">
GET /api/index.php/payments.json HTTP/1.1
</pre>
<p>
  And the response would look similar to this:
</p>
<pre class="prettyprint">
[
  {
    "paymentId": 1,
    "payDate": "2006-05-24T16:58:18.00",
    "payAmount": 20,
    "payTax": 0
  },
  {
    "paymentId": 2,
    "payDate": "2006-05-24T16:59:29.00",
    "payAmount": 20,
    "payTax": 0
  }
]
</pre>
<br>
<p>
  Whereas the next two code blocks show HTTP calls which request an <code class="prettyprint">XML</code> encoded response body
</p>
<pre class="prettyprint">
GET /api/index.php/payments HTTP/1.1
Accept: application/xml
</pre>
<pre class="prettyprint">
GET /api/index.php/payments.xml HTTP/1.1
</pre>
<p>
  And the response would look similar to this:
</p>
<pre class="prettyprint">
HTTP/1.1 200 OK
Content-Type: application/xml
&lt;?xml version="1.0"?&gt;
&lt;response&gt;
    &lt;item&gt;
        &lt;paymentId&gt;1&lt;/paymentId&gt;
        &lt;payDate&gt;2006-05-24T16:58:18.00&lt;/payDate&gt;
        &lt;payAmount&gt;20&lt;/payAmount&gt;
        &lt;payTax&gt;0&lt;/payTax&gt;
    &lt;/item&gt;
    &lt;item&gt;
        &lt;paymentId&gt;2&lt;/paymentId&gt;
        &lt;payDate&gt;2006-05-24T16:59:29.00&lt;/payDate&gt;
        &lt;payAmount&gt;20&lt;/payAmount&gt;
        &lt;payTax&gt;0&lt;/payTax&gt;
    &lt;/item&gt;
&lt;/response&gt;
</pre>
EOS
        );

                $this->calls['record-filtering'] = array(
            'type' => 'info'
            , 'id' => 'record-filtering'
            , 'header' => 'Record Filtering (V1)'
            , 'header_icon' => 'info-sign'
            , 'preface' => <<<EOS
<p>
  The ClubSpeed API has functionality for filtering data sets by using grouped comparators
  for <span class="glyphicon glyphicon-save"></span>&nbsp;GET operations.
  If only certain records from the database are desired, the client
  may filter out those records by appending any of the following query strings
  to any <span class="glyphicon glyphicon-save"></span>&nbsp;GET operation.
  This type of call can be used in tandem with <a href=#query-operations-column-selection>Column Selection</a> 
  but may <i>not</i> be used at the same time as <a href=#query-operations-property-matching>Property Matching</a>.
</p>
<p>
  Note that this method of record filtering is <em>only</em> available to any call <strong>marked as V1</strong>,
  and will not be supported on any new endpoints moving forward.
</p>
<p>
  The syntax is as below:
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
  <li><code class="prettyprint">IN</code></li>
  <li><code class="prettyprint">%lt; ( equivalent to &lt; )</code></li>
  <li><code class="prettyprint">%lte; ( equivalent to &lt;= )</code></li>
  <li><code class="prettyprint">%gt; ( equivalent to &gt; )</code></li>
  <li><code class="prettyprint">%gte; ( equivalent to &gt;= )</code></li>
  <li><code class="prettyprint">%eq; ( equivalent to = )</code></li>
  <li><code class="prettyprint">%neq; ( equivalent to != )</code></li>
  <li><code class="prettyprint">\$lt ( equivalent to &lt; )</code></li>
  <li><code class="prettyprint">\$lte ( equivalent to &lt;= )</code></li>
  <li><code class="prettyprint">\$gt ( equivalent to &gt; )</code></li>
  <li><code class="prettyprint">\$gte ( equivalent to &gt;= )</code></li>
  <li><code class="prettyprint">\$eq ( equivalent to = )</code></li>
  <li><code class="prettyprint">\$neq ( equivalent to != )</code></li>
  <li><code class="prettyprint">\$in ( equivalent to IN )</code></li>
</ul>
<h4>
  Available Connectors
</h4>
<ul>
  <li><code class="prettyprint">AND</code></li>
  <li><code class="prettyprint">OR</code></li>
</ul>
<div class="row">
  <div class="col-xs-12">
    <h4>
      Examples
    </h4>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?filter=amount \$eq 43.2 </pre>
    <pre> [amount] = 43.2 </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?filter=amount = 43.2 AND type = 2 </pre>
    <pre> [amount] = 43.2 AND [type] = 2 </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?filter=amount IS NULL </pre>
    <pre> [amount] IS NULL </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?filter=amount >= 43.2 </pre>
    <pre> [amount] >= 43.2 </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?filter=amount >= 43.2 AND amount <= 55.7 </pre>
    <pre> [amount] >= 43.2 AND [amount] <= 55.7 </pre>
  </div>
  <div class="col-xs-12" style="margin-bottom:15px;">
    <pre class="prettyprint"> ?filter=amount = 43.2 OR timestamp >= 2016-01-01T00:00:00 </pre>
    <pre> [amount] = 43.2 OR [timestamp] >= '2016-01-01T00:00:00' </pre>
  </div>
</div>
EOS
        );

        $this->calls['property-matching'] = array(
            'type'          => 'info'
            , 'id'          => 'property-matching'
            , 'header'      => 'Property Matching (V1)'
            , 'header_icon' => 'info-sign'
            , 'preface'       => <<<EOS
<p>
  Property matching is a shortcut version of the V1 record filtering.
  The functionality is the same, but can only be used for matching property values by equivalence.
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
GET https://{$_SERVER['SERVER_NAME']}/api/index.php/screenTemplateDetail?screenTemplateId=3&select=screenTemplateId,screenTemplateDetailId,trackNo,timeInSecond,seq HTTP/1.1
EOS
            , 'response' => <<<EOS
HTTP/1.1 200 OK
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
