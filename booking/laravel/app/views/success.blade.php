@extends('master')

<!-- HEADER -->

<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')
Success! - Online Booking
@stop
<!-- END PAGE TITLE -->


<!-- PAGE CONTENT -->
@section('steps')
<div class="steps">
    <div>{{link_to('step1','See the Lineup')}} > Choose a Race > Review Your Order
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
        <h2>Success!</h2>
        <img src="{{asset($images['success'])}}"><p/>
        <h4>Thank you for your order<br/>
            We'll see you on the track!</h4>

            Your payment confirmation number is {{$checkId}}.<p/>

            Please print this page for your records.<br/>
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

