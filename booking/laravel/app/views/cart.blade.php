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
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            {{$itemAddedToCart}} has been added to your shopping cart.
        </div>
    @endif

    @if($itemRemovedFromCart !== null)
        <div class="alert alert-success alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
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
                    <th><strong>Price (Each)</strong></th>
                    <th><strong>Subtotal</strong></th>
                    <th><strong>Tax</strong></th>
                    <th><strong>Total</strong></th>
                    <th><strong>Remove</strong></th>
                    </tr>
                </thead>
            @foreach($cart as $cartItemId => $cartItem)
                <tr>
                    <td>{{$cartItem['name']}}</td>
                    <td>{{$cartItem['quantity']}}</td>
                    <td>{{date('Y/m/d H:i',strtotime($cartItem['startTime']))}}</td>
                    <td>${{number_format($virtualCheckDetails[$cartItemId]->unitPrice,2)}}</td>
                    <td>${{number_format($virtualCheckDetails[$cartItemId]->checkDetailSubtotal,2)}}</td>
                    <td>${{number_format($virtualCheckDetails[$cartItemId]->checkDetailTax,2)}}</td>
                    <td>${{number_format($virtualCheckDetails[$cartItemId]->checkDetailTotal,2)}}</td>
                    <td><a href="cart?action=delete&item={{$cartItemId}}">X</a></td>
                </tr>
            @endforeach
            </table>

            <strong>Order Subtotal:</strong> ${{number_format($virtualCheck->checkSubtotal,2)}}<br/>
            <strong>Order Tax:</strong> ${{number_format($virtualCheck->checkTax,2)}}<br/>
            <strong>Order Total:</strong> ${{number_format($virtualCheck->checkTotal,2)}}<br/>

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
<script>
console.log({{json_encode($cart)}}) //TODO: Remove
</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->

