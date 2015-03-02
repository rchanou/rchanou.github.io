@extends('master')

<!-- HEADER -->

<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')
{{$strings['str_errorTitle']}}
@stop
<!-- END PAGE TITLE -->


<!-- PAGE CONTENT -->
@section('steps')
<div class="steps">
    {{link_to('step1',$strings['str_seeTheLineup'])}} > {{$strings['str_chooseARace']}} > {{$strings['str_reviewYourOrder']}}
        @if(Session::has('authenticated') && Session::has('cart') && count(Session::get('cart')) > 0)
        > {{link_to('checkout',$strings['str_checkout'])}}
        @else
        > {{$strings['str_checkout']}}
        @endif
</div>
@stop

@section('content')

<div class="mainBodyContent">
    <div class="centered">
        <h2>{{$strings['str_error']}}</h2>
        <img src="{{asset($images['disconnected'])}}"><p/>
        <h4>{{$strings['str_errorMessage']}} {{$code}}<br/>
        {{$strings['str_pleaseTryAgainLater']}}</h4>
    </div>
</div>

@stop
<!-- END PAGE CONTENT -->

<!-- FOOTER -->

<!-- END FOOTER -->

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

