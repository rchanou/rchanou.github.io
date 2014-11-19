@extends('master')

<!-- PAGE TITLE -->
@section('title')
Shopping Cart - Online Booking
@stop
<!-- END PAGE TITLE -->

<!-- PAGE CONTENT -->
@section('steps')
<div class="steps">
    {{link_to('step1','See the Lineup')}} > {{link_to('step2','Choose a Race')}} > <em>Review Your Order</em>
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
    <div class="mainBodyHeader">Shopping Cart</div><p/>

    @if($itemAddedToCart !== null)
        <div class="alert alert-success fadeAway" role="alert">
            {{$itemAddedToCart}} has been added to your shopping cart.
        </div>
    @endif

    @if($itemRemovedFromCart !== null)
        <div class="alert alert-success fadeAway" role="alert">
            {{$itemRemovedFromCart}} has been removed from your shopping cart.
        </div>
    @endif

    @if($failureToAddToCart !== null)
    <div class="alert alert-danger alert-dismissable" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        Unable to add your item to cart. The item may no longer be available.
    </div>
    @endif

    @if($localCartHasExpiredItem)
    <div class="alert alert-danger alert-dismissable" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        One or more items in your cart have expired and have been removed.
    </div>
    @endif

    @if(Session::has('authenticated'))

        <div class="raceResult">

            @if(count($cart)>0)
            <table class="table">
                <thead>
                    <tr>
                    <th><strong>Race Name</strong></th>
                    <th><strong>Racers</strong></th>
                    <th><strong>Start Time</strong></th>
                    <th><strong>Price</strong></th>
                    <th><strong>Subtotal</strong></th>
                    <th><strong>Tax</strong></th>
                    <th><strong>Total</strong></th>
                    <th><strong>Remove</strong></th>
                    </tr>
                </thead>
            @foreach($cart as $cartItemId => $cartItem)
                <tr> {{-- TODO: Filter by type --}}
                    <td>{{$cartItem['name']}}</td>
                    <td>{{$cartItem['quantity']}}</td>
                    <td>{{date(Config::get('config.dateFormat') . ' H:i',strtotime($cartItem['startTime']))}}</td>
                    <td>{{$moneyFormatter->formatCurrency($virtualCheckDetails[$cartItemId]->unitPrice, $currency)}}</td>
                    <td>{{$moneyFormatter->formatCurrency($virtualCheckDetails[$cartItemId]->checkDetailSubtotal, $currency)}}</td>
                    <td>{{$moneyFormatter->formatCurrency($virtualCheckDetails[$cartItemId]->checkDetailTax, $currency)}}</td>
                    <td>{{$moneyFormatter->formatCurrency($virtualCheckDetails[$cartItemId]->checkDetailTotal, $currency)}}</td>
                    <td><a href="cart?action=delete&item={{$cartItemId}}">X</a></td>
                </tr>
            @endforeach
            </table>

            <strong>Order Subtotal:</strong> {{$moneyFormatter->formatCurrency($virtualCheck->checkSubtotal, $currency)}}<br/>
            <strong>Order Tax:</strong> {{$moneyFormatter->formatCurrency($virtualCheck->checkTax, $currency)}}<br/>
            <strong>Order Total:</strong> {{$moneyFormatter->formatCurrency($virtualCheck->checkTotal, $currency)}}<br/>

            <form action="{{URL::action('CheckoutController@entry')}}">
                <button class="formButton">Proceed to Checkout</button>
            </form>
            @else
                    Your cart is empty.
            @endif
        </div>

    @else
        You must be {{link_to('login','logged in')}} to view the cart.
    @endif
    </div>

</div>
@stop
<!-- END PAGE CONTENT -->

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent
@stop
<!-- END JAVASCRIPT INCLUDES -->

