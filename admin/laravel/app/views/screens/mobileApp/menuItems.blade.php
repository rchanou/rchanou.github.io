@extends('master')

@section('title')
  Mobile App Menu Item Editor
@stop

@section('css_includes')
  @parent
  {{ HTML::style('css/jquery.ui.ie.css') }}
  {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
  Mobile App Menu Item Editor
@stop

@section('breadcrumb')
  <a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
  <a href="#" class="current">Mobile App</a>
  <a href="#" class="current">Menu Items</a>
@stop

@section('content')
  <div id="main">Loading Mobile App Menu Item Editor...</div>
@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
  @parent
  {{ HTML::script('js/react/build/mobileApp/menuItems/main.min.js') }}
@stop
<!-- END JAVASCRIPT INCLUDES -->
