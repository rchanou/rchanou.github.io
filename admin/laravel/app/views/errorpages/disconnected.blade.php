@extends('master')

@section('title')
Dashboard
@stop

@section('pageHeader')
Disconnected
@stop

@section('breadcrumb')
    <a href="{{URL::to('dashboard')}}" title="Go to Home" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
    <a href="#" class="current">Disconnected</a>
@stop

@section('content')

    <div class="row">
        <div class="col-xs-12 col-sm-3"></div>
        <div class="col-xs-12 col-sm-6 text-center">
            <h1>Disconnected!</h1>
            <img src="{{asset("img/disconnected.png")}}"><p/>
            <div class="alert alert-danger alert-block">
            Unable to reach the Club Speed server. Please try again later. If the issue persists, contact Club Speed support.<p/>
            </div>
        </div>
        <div class="col-xs-12 col-sm-3"></div>
    </div>

@stop