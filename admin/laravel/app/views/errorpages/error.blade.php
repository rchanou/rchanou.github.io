@extends('master')

@section('title')
Dashboard
@stop

@section('pageHeader')
Error
@stop

@section('breadcrumb')
    <a href="{{URL::to('dashboard')}}" title="Go to Home" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
    <a href="#" class="current">Error</a>
@stop

@section('content')

    <div class="row">
        <div class="col-xs-12 col-sm-3"></div>
        <div class="col-xs-12 col-sm-6 text-center">
            <h1>Error!</h1>
            <img src="{{asset('img/disconnected.png')}}"><p/>
            <div class="alert alert-danger alert-block">
            An error has occurred. Please try again later. If the issue persists, contact Club Speed support.<p/>
            @if(isset($code))<strong>Error code: {{$code}}</strong>@endif
            </div>
        </div>
        <div class="col-xs-12 col-sm-3"></div>
    </div>

@stop

    <!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent
<script>
    var errorInfo = {{$errorInfo}};
    console.log("Error information:");
    console.log(errorInfo);
</script>
@stop
    <!-- END JAVASCRIPT INCLUDES -->