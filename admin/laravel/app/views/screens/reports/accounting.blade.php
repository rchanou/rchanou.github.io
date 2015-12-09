@extends('master')

@section('title')
Accounting Export
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
    <style type="text/css">
		.popover-content {
		height: 200px;
		overflow-y: scroll;
		}
		</style>
@stop

@section('pageHeader')
Accounting Export
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="{{URL::to('reports')}}">Reports</a>
<a href="#" class="current">Accounting Export</a>
@stop

@section('content')
    <div class="container-fluid">
      <div class="row">
      <div class="col-sm-1"></div>
       <div class="col-sm-10">
        <div class="widget-box">
          <div class="widget-title">
            <span class="icon">
              <i class="fa fa-calendar"></i>
            </span>
            <h5>Export Date Range</h5>
          </div>
          <div class="widget-content">
              <div class="row">
              {{ Form::open(array('action'=>'AccountingReportController@index','files'=>false, 'class' => 'form-horizontal', 'id' => 'dateForm')) }}
                <div class="col-12-sm">
                    <table class="table table-bordered table-striped table-hover text-center">
                      <thead>
                        <tr>
                            <th>Start Date/Time</th>
                            <th>End Date/Time</th>
                            <th>Replacements <i class="fa fa-question-circle tip" title="<strong>Instructions</strong>" data-container="body" data-toggle="popover" data-placement="left" data-html="true" data-content="
                              <p><strong>Overview</strong></p>
<p>Club Speed provides a flexible export system that allows for custom field name replacements at export time.</p>

<p>Mappings between Club Speed Account Name and your custom name are created by matching the AccountNumber field and then attaching any variables  in the 'Replacements' section and then running the report to save/process.</p>

<p>Account Numbers for custom accounts can be updated in <a href='/sp_admin/'>the registration panel</a> under 'Settings &gt; Advanced Settings &gt; Product Classes'</p>

<p><strong>Usage Examples</strong></p>

<p>Variables may be appended by separating them with the pipe character '|' and the format: VARIABLE=VALUE.</p>

<p>Example: ##CASH_PAYMENT##=4010|Class=My Class</p>

<p>This maps the ##CASH_PAYMENT## account to account number 4010 and also assigns the variable 'Class' the value of 'My Class'.</p>

<p>Each time you run the export, the Replacement Mappings are saved.</p>

<p>The exported date format default may be overridden with the 'Date_Format' variable using PHP date() options.</p>

<p>Example: ##CASH_PAYMENT##=4010|Date_Format=d/m/Y</p>

<p><strong>Pre-defined Account Names</strong></p>
<ul>
	<li>##CASH_PAYMENT##</li>
	<li>##COMPLIMENTARY_PAYMENT##</li>
	<li>##GIFT_CARD_&_FAST_CASH_PAYMENT##</li>
	<li>##_EXTERNAL_PAYMENT##</li>
	<li>##ITEM_DISCOUNT##</li>
	<li>##TAXES##</li>
	<li>##PREPAYMENTS##</li>
	<li>##PREPAYMENTS_USED##</li>
	<li>##EXPENSES##</li>
	<li>##GIFTCARDS##</li>
	<li>##GRATUITY##</li>
	<li>##FEE##</li>
	<li>##CHECK_DISCOUNT##</li>
  <li><em>Plus any custom payment types and accounts</em></li>
</ul>

<p><strong>Quickbooks Variables</strong></p>
<ul>
	<li>Class</li>
</ul>

<p>Example: ##CASH_PAYMENT##=4010|Class=My Class</p>

<p><strong>SAGE Variables</strong></p>
<ul>
	<li>Tax Code</li>
  <li>Type</li>
  <li>Bank</li>
  <li>Ref</li>
  <li>Dept</li>
</ul>

<p>Example: ##CASH_PAYMENT##=4010|Tax Code=T1|Type=CR|Bank=1203|Ref=Cash|Dept=1</p>
"></i>
                            </th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                            <td>
                                <input type="date" name="start" id="start" value="{{$start}}"><br/>
                                {{Form::select('startHour', array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'), $startHour, array('class' => 'normal'))}}
                                :
                                {{Form::select('startMinute', array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59'), $startMinute, array('class' => 'normal'))}}
                            </td>
                            <td>
                                <input type="date" name="end" id="end" value="{{$end}}"><br/>
                                {{Form::select('endHour', array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'), $endHour, array('class' => 'normal'))}}
                                :
                                {{Form::select('endMinute', array('00', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36', '37', '38', '39', '40', '41', '42', '43', '44', '45', '46', '47', '48', '49', '50', '51', '52', '53', '54', '55', '56', '57', '58', '59'), $endMinute, array('class' => 'normal'))}}
                            </td>
                            <td>
                                <textarea name="fieldMappings">{{$fieldMappings}}</textarea>
                            </td>
                        </tr>
                      </tbody>
                    </table>
                </div>
                  <div class="col-sm-12">
                       <button type="button" class="btn btn-danger" onclick="$(':input','#dateForm').not(':button, :submit, :reset, :hidden, :radio, :checkbox').val('');">Clear</button>
                       {{ Form::submit('Run Report', array('class' => 'btn btn-info')) }}
                       <span class="help-block text-left">Leaving both dates blank will generate a report for today. Leaving the end date blank will generate a report just for the start date.</span>
                  </div>
              </div>
              {{ Form::close() }}
          </div>
        </div>
       </div>
       <div class="col-sm-1"></div>
      </div>
      <div class="row">
        <div class="col-sm-12">

            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                @if(isset($startFormatted) && isset($endFormatted) && $startFormatted != "" && $endFormatted != "")
                    <h5>Accounting Export - From {{$startFormatted}} to {{$endFormatted}}</h5>
                @elseif (isset($startFormatted) && $startFormatted != "")
                    <h5>Accounting Export - {{$start}}</h5>
                @else
                    <h5>Accounting Export - Today</h5>
                @endif
                <span class="icon pull-right" style="font-size: 12px; line-height: 12px;">
                    <a href="{{URL::to('reports/accounting/export/sage')}}"><button type="button">Export to Sage50</button></a> 
                    <a href="{{URL::to('reports/accounting/export/iif')}}"><button type="button">Export to Quickbooks</button></a> 
                    <a href="{{URL::to('reports/accounting/export/csv')}}"><button type="button">Export to CSV</button></a>
                </span>
              </div>
              <div class="widget-content">
                  <div class="row" style="overflow: auto">
                    <div class="col-12-lg">
                        @if(count($report) > 0 )
                            <table class="table table-bordered table-striped table-hover text-center">
                                <thead>
                                    <tr>
                                    @foreach($report[0] as $reportColumnName => $reportColumnValue)
                                        <th>{{$reportColumnName}}</th>
                                    @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report as $currentReportColumn)
                                    <tr>
                                        @foreach($currentReportColumn as $currentReportValue)
                                        <td>{{$currentReportValue}}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                                    <tr>
                                        <td><strong>Grand Total</strong></td>
                                        <td></td>
                                        <td><strong>{{$total_debits}}</strong></td>
                                        <td><strong>{{$total_credits}}</strong></td>
                                    </tr>
                              </tbody>
                            </table>
                        @elseif ($report === null)
                            <div class="alert alert-danger">
                            Unable to reach the Club Speed server. Please try again later.
                            </div>
                        @else
                            <div class="alert alert-info">
                            There is no data in this report. Please try another date range.
                            </div>
                        @endif
                    </div>
                  </div>
              </div>
            </div>

        </div>
      </div>
    </div>
@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent
<script>
    /**
     * This function allows a Date object to convert itself to the same
     * date format expected by an HTML5 date input.
     * @type {Function}
     */
    Date.prototype.toDateInputValue = (function() {
        var local = new Date(this);
        local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
        return local.toJSON().slice(0,10);
    });

    //When the document loads, default the date picker to today's date if it's blank
    $(document).ready(function() {
        if ($('#start').val() == "")
        {
            $('#start').val(new Date().toDateInputValue());
        }
				$('body').on('click', function (e) {

				// Close popover when clicking outside of it
				$('[data-toggle="popover"]').each(function () {
								//the 'is' for buttons that trigger popups
								//the 'has' for icons within a button that triggers a popup
								if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
										$(this).popover('hide');
								}
						});
				});
    });
</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->