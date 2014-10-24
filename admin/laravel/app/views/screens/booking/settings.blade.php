@extends('master')

@section('title')
Manage Bookings
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
Dashboard
@stop

@section('breadcrumb')
<a href="dashboard" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#" class="current">Online Bookings</a>
<a href="#" class="current">Settings</a>
@stop

@section('content')
  <div class="row">
    <div class="col-sm-12">
        <div class="alert alert-info">
            This feature is under development and will allow editing of Online Bookings settings.
        </div>
    </div>
</div>

@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent
{{ HTML::script('js/react-with-addons.min.js') }}
{{ HTML::script('js/moment-with-locales.min.js') }}
{{ HTML::script('js/lodash.min.js') }}
{{ HTML::script('js/react/booking/manage/main.js') }}
@stop
<!-- END JAVASCRIPT INCLUDES -->