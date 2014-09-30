@extends('master')

<!-- HEADER -->

<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')
Disabled! - Online Booking
@stop
<!-- END PAGE TITLE -->


<!-- PAGE CONTENT -->
@section('steps')
<div class="steps">
    <div style="visibility: hidden">{{link_to('step1','See the Lineup')}} > Choose a Race > Review Your Order
        @if(Session::has('authenticated') && Session::has('cart') && count(Session::get('cart')) > 0)
        > {{link_to('checkout','Checkout')}}
        @else
        > Checkout
        @endif
    </div>
</div>
@stop

@section('content')

<div class="mainBodyContent">
    <div class="centered">
        <h2>Online Registration Disabled!</h2>
        <img src="{{asset($images['disconnected'])}}"><p/>
        <h4>Online registration has been temporarily disabled.<br/>
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

@stop
<!-- END JAVASCRIPT INCLUDES -->

