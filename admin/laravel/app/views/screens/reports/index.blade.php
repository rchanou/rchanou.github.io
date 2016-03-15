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
                    <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                        <a href="{{URL::to('reports/broker-codes')}}">
                            <i class="fa fa-filter fa-4x"></i><p/>
                            <strong>Broker/Affiliate Codes Report</strong><p/>
                        </a>
                            <em>Summary of check totals grouped by Broker/Affiliate Code</em>
                    </div>
                  </div>
                  <div class="row">
                  	<div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                        <a href="{{URL::to('reports/accounting')}}">
                            <i class="fa fa-dollar fa-4x"></i><p/>
                            <strong>Accounting Export</strong><p/>
                        </a>
                        <em>View and export journal entries for various accounting software</em>
                    </div>
                    <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                        <a href="{{URL::to('reports/event-rep-sales')}}">
                            <i class="fa fa-users fa-4x"></i><p/>
                            <strong>Event Rep Sales</strong><p/>
                        </a>
                        <em>Event sales by representative</em>
                    </div>
                    <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                        <a href="{{URL::to('reports/marketing-source-performance')}}">
                            <i class="fa fa-shopping-cart fa-4x"></i><p/>
                            <strong>Marketing Source Performance</strong><p/>
                        </a>
                        <em>Sales totals from your marketing campaigns</em>
                    </div>
                    <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                        <a href="{{URL::to('reports/social')}}">
                            <i class="fa fa-facebook-square fa-4x"></i><p/>
                            <strong>Social Media Usage</strong><p/>
                        </a>
                        <em>View active customers in the last 60 days (length of token) that have linked their accounts with Facebook</em>
                    </div>
                  </div>
                  <div class="row">
                  	<div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                        <a href="{{URL::to('reports/sales-by-pos-and-class')}}">
                            <i class="fa fa-bar-chart fa-4x"></i><p/>
                            <strong>Sales By POS and Class</strong><p/>
                        </a>
                        <em>Sales, grouped by Point of Sale and Accounting Class</em>
                    </div>
                  </div>
              </div>
            </div>

        </div>
          @if($serverHasEurekas)
          <div class="col-sm-12">
              <div class="widget-box">
                  <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-cutlery"></i>
                </span>
                      <h5>Eurekas Sales</h5>
                  </div>
                  <div class="widget-content">
                      <div class="row">
                          <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                              <a href="{{URL::to('reports/eurekas-payments')}}">
                                  <i class="fa fa-suitcase fa-4x"></i><p/>
                                  <strong>Detailed Payments Report</strong><p/>
                              </a>
                              <em>Breakdown of payments in a date range for each check</em>

                          </div>
                          <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                              <a href="{{URL::to('reports/eurekas-detailed-sales')}}">
                                  <i class="fa fa-list fa-4x"></i><p/>
                                  <strong>Detailed Sales Report</strong><p/>
                              </a>
                              <em>Line items of every check in a date range</em>
                          </div>
                          <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                              <a href="{{URL::to('reports/eurekas-summary-payments')}}">
                                  <i class="fa fa-university fa-4x"></i><p/>
                                  <strong>Summary Payments Report</strong><p/>
                              </a>
                              <em>Summary of payments in a date range by tender</em>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
          @endif
          
					@if(@$user == 'support')
          <div class="col-sm-12">
              <div class="widget-box">
                  <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-cog"></i>
                </span>
                      <h5>Logs</h5>
                  </div>
                  <div class="widget-content">
                      <div class="row">
                          <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                              <a href="{{URL::to('reports/logs')}}">
                                  <i class="fa fa-list fa-4x"></i><p/>
                                  <strong>All Log Entries</strong><p/>
                              </a>
                              <em>View and search all log entries from Club Speed</em>

                          </div>
                      </div>
                  </div>
              </div>
          </div>
          @endif

          @if($serverHasEMV)
              <div class="col-sm-12">
                  <div class="widget-box">
                      <div class="widget-title">
                        <span class="icon">
                          <i class="fa fa-credit-card"></i>
                        </span>
                        <h5>EMV Terminals</h5>
                      </div>
                      <div class="widget-content">
                          <div class="row">
                              @foreach($ingenicoTerminals as $terminal)
                              <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                                      <i class="fa fa-calculator fa-4x"></i><p/>
                                      <strong>{{strtoupper($terminal->namespace)}}</strong><p/>
                                  <em>
                                      <strong>
                                      <a href="{{URL::to('reports/emv/configuration/' . strtolower($terminal->namespace))}}" target="_blank">Configuration Report</a>
                                      <br/>
                                      <a href="{{URL::to('reports/emv/chip/' . strtolower($terminal->namespace))}}" target="_blank">Chip Report</a>
                                      </strong>
                                  </em>
                              </div>
                              @endforeach
                          </div>
                      </div>
                  </div>
              </div>
          @endif

      </div>
    </div>
@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent

@stop
<!-- END JAVASCRIPT INCLUDES -->