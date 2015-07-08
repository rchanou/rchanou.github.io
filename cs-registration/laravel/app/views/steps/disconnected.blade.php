@extends('master')

<!-- HEADER -->
@section('backButton')
    <a href="{{$step1URL}}" class="arrow" onclick="$('#loadingModal').modal();"><span>Back</span></a>
@stop

@section('headerTitle')
{{$strings['str_Unable to Connect']}}
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

<div class="row" id="disconnectedInfo">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
        <p/>
            <img src="images/redhelmet_disconnect.png" class="center-block" style="margin-top: 80px;">
            <a href="{{$step1URL}}" onclick="$('#loadingModal').modal();">
            <div class="text-center" style="font-size: 20px;">{{$strings['str_disconnectedMessage']}}</div>
        </a>
    </div>
    <div class="col-sm-3"></div>
</div>

@stop
<!-- END PAGE CONTENT -->