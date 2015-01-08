@extends('master')

@section('title')
Detailed Payments Report
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
Detailed Payments Report
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="{{URL::to('reports')}}">Reports</a>
<a href="#" class="current">Detailed Payments</a>
@stop

@section('content')
    <div class="container-fluid">
      <div class="row">
      <div class="col-sm-2"></div>
       <div class="col-sm-8">
        <div class="widget-box">
          <div class="widget-title">
            <span class="icon">
              <i class="fa fa-calendar"></i>
            </span>
            <h5>Report Date Range</h5>
          </div>
          <div class="widget-content">
              <div class="row">
              {{ Form::open(array('action'=>'PaymentsReportController@index','files'=>false, 'class' => 'form-horizontal', 'id' => 'dateForm')) }}
                <div class="col-12-sm">
                    <table class="table table-bordered table-striped table-hover text-center">
                      <thead>
                        <tr>
                            <th>Start Date</th>
                            <th>End Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                            <td>
                                <input type="date" name="start" id="start" value="{{$start}}">
                            </td>
                            <td>
                                <input type="date" name="end" id="end" value="{{$end}}">
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
       <div class="col-sm-2"></div>
      </div>
      <div class="row">
        <div class="col-sm-12">

            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                @if(isset($start) && isset($end) && $start != "" && $end != "")
                    <h5>Detailed Payments Report - From {{$start}} to {{$end}}</h5>
                @elseif (isset($start) && $start != "")
                    <h5>Detailed Payments Report - {{$start}}</h5>
                @else
                    <h5>Detailed Payments Report - Today</h5>
                @endif
                <span class="icon pull-right" style="font-size: 12px; line-height: 12px;">
                    <a href="{{URL::to('reports/payments/export/csv')}}"><button type="button">Export to CSV</button></a>
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
    });
</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->