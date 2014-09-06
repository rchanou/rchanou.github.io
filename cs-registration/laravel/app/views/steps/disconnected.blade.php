@extends('master')

<!-- HEADER -->
@section('backButton')
<a href="{{Session::has('ipcam') ? 'step1' . '?&terminal=' . Session::get('ipcam') : 'step1' }}" class="arrow" onclick="$('#loadingModal').modal();"><span>Back</span></a>
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

<div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6">
        <p/>
        <a href="{{Session::has('ipcam') ? 'step1' . '?&terminal=' . Session::get('ipcam') : 'step1' }}" onclick="$('#loadingModal').modal();">
            <img src="images/redhelmet_disconnect.png" class="center-block" style="margin-top: 80px;">
            <div class="text-center" style="font-size: 20px;">{{$strings['str_disconnectedMessage']}}</div>
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

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent
<script>
    var errorInfo = {{json_encode(Session::get('errorInfo'))}};
    console.log("Error information:");
    console.log(errorInfo);
</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->
