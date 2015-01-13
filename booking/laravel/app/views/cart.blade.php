@extends('master')

<!-- PAGE TITLE -->
@section('title')
{{$strings['str_cartTitle']}}
@stop
<!-- END PAGE TITLE -->

<!-- PAGE CONTENT -->
@section('steps')
<div class="steps">
    {{link_to('step1',$strings['str_seeTheLineup'])}} > {{link_to('step2',$strings['str_chooseARace'])}} > <em>{{$strings['str_reviewYourOrder']}}</em>
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
    <div class="mainBodyHeader">{{$strings['str_shoppingCart']}}</div><p/>

    @if($itemAddedToCart !== null)
        <div class="alert alert-success fadeAway" role="alert">
            {{$itemAddedToCart}} {{$strings['str_hasBeenAddedToCart']}}
        </div>
    @endif

    @if($itemRemovedFromCart !== null)
        <div class="alert alert-success fadeAway" role="alert">
            {{$itemRemovedFromCart}} {{$strings['str_hasBeenRemovedFromCart']}}
        </div>
    @endif

    @if($failureToAddToCart !== null)
    <div class="alert alert-danger alert-dismissable" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">{{$strings['str_close']}}</span></button>
        {{$strings['str_unableToAddToCart']}}
    </div>
    @endif

    @if($localCartHasExpiredItem)
    <div class="alert alert-danger alert-dismissable" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">{{$strings['str_close']}}</span></button>
        {{$strings['str_itemExpired']}}
    </div>
    @endif

    @if(Session::has('authenticated'))

        <div class="raceResult">

            @if(count($cart)>0)
            <h3>{{$strings['str_races']}}</h3>
                @if($hasHeatItems)
                <table class="table">
                    <thead>
                        <tr>
                        <th><strong>{{$strings['str_raceName']}}</strong></th>
                        <th><strong>{{$strings['str_racers']}}</strong></th>
                        <th><strong>{{$strings['str_startTime']}}</strong></th>
                        <th><strong>{{$strings['str_price']}}</strong></th>
                        <th><strong>{{$strings['str_subtotal']}}</strong></th>
                        <th><strong>{{$strings['str_tax']}}</strong></th>
                        <th><strong>{{$strings['str_total']}}</strong></th>
                        <th><strong>{{$strings['str_remove']}}</strong></th>
                        </tr>
                    </thead>

                        @foreach($cart as $cartItemId => $cartItem)
                            @if($cartItem['type'] == 'heat')
                            <tr>
                                <td>{{$cartItem['name']}}</td>
                                <td>{{$cartItem['quantity']}}</td>
                                <td>{{date($settings['dateDisplayFormat'] . ' ' . $settings['timeDisplayFormat'],strtotime($cartItem['startTime']))}}</td>
                                <td>{{$moneyFormatter->formatCurrency($virtualCheckDetails[$cartItemId]->unitPrice, $currency)}}</td>
                                <td>{{$moneyFormatter->formatCurrency($virtualCheckDetails[$cartItemId]->checkDetailSubtotal, $currency)}}</td>
                                <td>{{$moneyFormatter->formatCurrency($virtualCheckDetails[$cartItemId]->checkDetailTax, $currency)}}</td>
                                <td>{{$moneyFormatter->formatCurrency($virtualCheckDetails[$cartItemId]->checkDetailTotal, $currency)}}</td>
                                <td class="text-center"><a href="cart?action=delete&item={{$cartItemId}}">X</a></td>
                            </tr>
                            @endif
                        @endforeach
                @else
                    {{$strings['str_noHeatItemsInCart']}}
                @endif
                </table>

            @if($settings['giftCardSalesEnabled'])
                <h3>{{$strings['str_products']}}</h3>
                @if($hasNonHeatItems)
                <table class="table">
                    <thead>
                    <tr>
                        <th><strong>{{$strings['str_productName']}}</strong></th>
                        <th><strong>{{$strings['str_quantity']}}</strong></th>
                        <th><strong>{{$strings['str_price']}}</strong></th>
                        <th><strong>{{$strings['str_subtotal']}}</strong></th>
                        <th><strong>{{$strings['str_tax']}}</strong></th>
                        <th><strong>{{$strings['str_total']}}</strong></th>
                        <th><strong>{{$strings['str_remove']}}</strong></th>
                    </tr>
                    </thead>
                        @foreach($cart as $cartItemId => $cartItem)
                            @if($cartItem['type'] == 'product')
                                <tr>
                                    <td>{{$cartItem['name']}}</td>
                                    <td>{{$cartItem['quantity']}}</td>
                                    <td>{{$moneyFormatter->formatCurrency($virtualCheckDetails[$cartItemId]->unitPrice, $currency)}}</td>
                                    <td>{{$moneyFormatter->formatCurrency($virtualCheckDetails[$cartItemId]->checkDetailSubtotal, $currency)}}</td>
                                    <td>{{$moneyFormatter->formatCurrency($virtualCheckDetails[$cartItemId]->checkDetailTax, $currency)}}</td>
                                    <td>{{$moneyFormatter->formatCurrency($virtualCheckDetails[$cartItemId]->checkDetailTotal, $currency)}}</td>
                                    <td class="text-center"><a href="cart?action=delete&item={{$cartItemId}}">X</a></td>
                                </tr>
                            @endif
                        @endforeach
                @else
                    {{$strings['str_noProductsInCart']}}
                @endif
                </table>
            @endif
                    <h3>{{$strings['str_summary']}}</h3>

                <strong>{{$strings['str_order']}} {{$strings['str_subtotal']}}:</strong> {{$moneyFormatter->formatCurrency($virtualCheck->checkSubtotal, $currency)}}<br/>
                <strong>{{$strings['str_order']}} {{$strings['str_tax']}}:</strong> {{$moneyFormatter->formatCurrency($virtualCheck->checkTax, $currency)}}<br/>
                <strong>{{$strings['str_order']}} {{$strings['str_total']}}:</strong> {{$moneyFormatter->formatCurrency($virtualCheck->checkTotal, $currency)}}<br/><br/>

                <form action="{{URL::action('CheckoutController@entry')}}">
                    <a class="btn formButton" href="{{URL::to('step2')}}">{{$strings['str_addMoreRaces']}}</a>
                    <button class="btn formButton">{{$strings['str_proceedToCheckout']}}</button>
                </form>
            @else
                    {{$strings['str_yourCartIsEmpty']}}
            @endif
        </div>

    @else
        {{$strings['str_youMustBe']}} {{link_to('login',$strings['str_loggedIn'])}} {{$strings['str_toViewTheCart']}}
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

