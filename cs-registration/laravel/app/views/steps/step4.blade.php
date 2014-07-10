@extends('master')

<!-- HEADER -->
@section('backButton')
@stop

@section('headerTitle')
{{$strings['step4HeaderTitle']}}
@stop

@section('languagesDropdown')

@stop
<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')
{{$strings['step4PageTitle']}}
@stop
<!-- END PAGE TITLE -->

<!-- PAGE CONTENT -->
@section('content')

<div class="row">
    <div class="col-sm-3"></div>
    <div class="col-sm-6" style="text-align: center; font-size: 18px">
        {{$strings['registrationCompleteMessage']}}
        <p/>
        <a href="step1" onclick="$('#loadingModal').modal();">
        <img src="{{$images['completeRegistration']}}" class="center-block" style="margin-top: 80px;">
        <div class="text-center" style="font-size: 20px;">{{$strings['completeRegistration']}}</div>
        </a>
    </div>
    <div class="col-sm-3"></div>
</div>
<div class="row">
    <div class="col-sm-12 centered">
        <img src="{{$images['poweredByClubSpeed']}}" style="padding-top: 10px; margin-top: 132px">
    </div>
</div>

<script type="text/javascript">
    var timer = setTimeout(function(){ window.location='step1';}, 8000);
</script>
@stop
<!-- END PAGE CONTENT -->