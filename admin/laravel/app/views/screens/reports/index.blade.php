@extends('master')

@section('title')
Reports
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
Reports
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#" class="current">Reports</a>
@stop

@section('content')
    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-12">

            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                <h5>Sales</h5>
              </div>
              <div class="widget-content">
                  <div class="row">
                    <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                        <a href="{{URL::to('reports/payments')}}">
                            <i class="fa fa-suitcase fa-4x"></i><p/>
                            <strong>Detailed Payments Report</strong><p/>
                        </a>
                            <em>Breakdown of payments in a date range for each check</em>

                    </div>
                    <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                        <a href="{{URL::to('reports/detailed-sales')}}">
                            <i class="fa fa-list fa-4x"></i><p/>
                            <strong>Detailed Sales Report</strong><p/>
                        </a>
                            <em>Line items of every check in a date range</em>
                    </div>
                    <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                        <a href="{{URL::to('reports/summary-payments')}}">
                            <i class="fa fa-university fa-4x"></i><p/>
                            <strong>Summary Payments Report</strong><p/>
                        </a>
                            <em>Summary of payments in a date range by tender</em>
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

@stop
<!-- END JAVASCRIPT INCLUDES -->