@extends('master')

<!-- HEADER -->

<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')
Disconnected! - Online Booking
@stop
<!-- END PAGE TITLE -->


<!-- PAGE CONTENT -->
@section('steps')
<div class="steps">
    {{link_to('step1','See the Lineup')}} > Choose a Race > Review Your Order
    @if(Session::has('authenticated') && Session::has('cart') && count(Session::get('cart')) > 0)
    > {{link_to('checkout','Checkout')}}
    @else
    > Checkout
    @endif
</div>
@stop

@section('content')

<div class="mainBodyContent">
    <div class="centered">
        <h2>Disconnected!</h2>
        <img src="{{asset($images['disconnected'])}}"><p/>
        <h4>Unable to reach the track's server.<br/>
        Please try again later!</h4>
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

