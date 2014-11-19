@extends('master')

@section('title')
Manage Bookings
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
	{{ HTML::style('css/select2-bootstrap.css') }}
@stop

@section('pageHeader')
Manage Bookings
@stop

@section('breadcrumb')
  <a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
  <a href="#" class="current">Online Bookings</a>
  <a href="#" class="current">Manage Bookings</a>
@stop

@section('content')
<div class="row">
    <div class="col-xs-12">
        @if ($currentOnlineBookingState == 'disabled_manually')
        <div class="alert alert-warning">
            <p>(Note: Online Booking is <strong>current disabled</strong> because the "Enable Online Booking" setting is not checked.</p>
            To access Online Booking while it's disabled (for testing), <a href="{{'http://' . $_SERVER['HTTP_HOST'] . '/booking/step1?key=' . md5(Config::get('config.privateKey'))}}">use this link</a>.
        </div>
        @endif
        @if ($currentOnlineBookingState == 'disabled_dummypayments')
        <div class="alert alert-warning">
            <p>(Note: Online Booking is <strong>current disabled</strong> because the site is using the Dummy payment processor.)</p>
            To access Online Booking while it's disabled (for testing), <a href="{{'http://' . $_SERVER['HTTP_HOST'] . '/booking/step1?key=' . md5(Config::get('config.privateKey'))}}">use this link</a>.
        </div>
        @endif
    </div>
</div>
  <div id="main">Loading Booking Admin...</div>
@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent
  {{ HTML::script('js/react/build/booking/manage/main.min.js') }}
@stop
<!-- END JAVASCRIPT INCLUDES -->

<!-- RENDER THE UI -->