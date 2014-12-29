@extends('master')

@section('title')
Gift Card Reports
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
    Gift Card Reports
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#">Gift Cards</a>
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
                <h5>Gift Card Reports</h5>
              </div>
              <div class="widget-content">
                  <div class="row">
                    <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                        <a href="{{URL::to('giftcards/reports/balance')}}">
                            <i class="fa fa-usd fa-4x"></i><p/>
                            <strong>Balance Report</strong><p/>
                        </a>
                            <em>A list of gift card balances</em>
                    </div>
                    <div class="col-lg-3 col-sm-6 text-center paymentReportBox">
                        <a href="{{URL::to('giftcards/reports/transactions')}}">
                            <i class="fa fa-list fa-4x"></i><p/>
                            <strong>Transaction History Report</strong><p/>
                        </a>
                            <em>Transaction histories for a list of gift cards</em>
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