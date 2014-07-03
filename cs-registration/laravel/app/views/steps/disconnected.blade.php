@extends('master')

<!-- HEADER -->
@section('backButton')
<a href="step1" class="arrow" onclick="$('#loadingModal').modal();"><span>Back</span></a>
@stop

@section('headerTitle')
Unable to Connect
@stop

@section('languagesDropdown')

@stop
<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')
Unable to Connect
@stop
<!-- END PAGE TITLE -->

<!-- PAGE CONTENT -->
@section('content')

<div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
        <p/>
        <a href="step1" onclick="$('#loadingModal').modal();">
            <img src="images/redhelmet_disconnect.png" class="center-block" style="margin-top: 80px;">
            <div class="text-center" style="font-size: 20px;">Unable to connect to Club Speed. <br/>Please try again in a few minutes. <br/>If the issue persists, contact Club Speed support.</div>
        </a>
    </div>
    <div class="col-sm-3"></div>
</div>
<div class="row">
    <div class="col-sm-12 centered">
        <img src="images/clubspeed.png" style="padding-top: 10px; margin-top: 220px">
    </div>
</div>
@stop
<!-- END PAGE CONTENT -->