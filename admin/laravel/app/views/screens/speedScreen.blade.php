@extends('master')

@section('title')
  Manage Speed Screen
@stop

@section('css_includes')
@parent
  {{ HTML::style('css/jquery.ui.ie.css') }}
  {{ HTML::style('css/jquery-ui.css') }}
  {{ HTML::style('css/select2-bootstrap.css') }}
@stop

@section('pageHeader')
  Manage Speed Screen
@stop

@section('breadcrumb')
  <a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
  <a href="#" class="current">Manage Speed Screen</a>
@stop

@section('content')
  <div id="main">Loading Speed Screen Admin...</div>
@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent
  {{ HTML::script('js/react/build/speedScreen/manage/main.min.js') }}
@stop
<!-- END JAVASCRIPT INCLUDES -->

<!-- RENDER THE UI -->
