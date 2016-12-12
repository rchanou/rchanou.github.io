@extends('master-responsive')

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
    > {{link_to('checkout',$strings['str_checkout'],array('class' => 'disableDoubleClick'))}}
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

    @if(Session::has('message'))
        <div class="alert alert-success fadeAway" role="alert">
            {{Session::get('message')}}
        </div>
    @endif

    @if(Session::has('authenticated'))

        <div class="raceResult">

            @if(count($cart)>0)
            <h3>{{$strings['str_races']}}</h3>
            <div class="table-responsive">
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
                </div>

            @if($settings['giftCardSalesEnabled'])
                <h3>{{$strings['str_products']}}</h3>
                <div class="table-responsive">
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
                </div>
            @endif
                    <h3>{{$strings['str_summary']}}</h3>

                <strong>{{$strings['str_order']}} {{$strings['str_subtotal']}}:</strong> {{$moneyFormatter->formatCurrency($virtualCheck->checkSubtotal, $currency)}}<br/>
                <strong>{{$strings['str_order']}} {{$strings['str_tax']}}:</strong> {{$moneyFormatter->formatCurrency($virtualCheck->checkTax, $currency)}}<br/>
                <strong>{{$strings['str_order']}} {{$strings['str_total']}}:</strong> {{$moneyFormatter->formatCurrency($virtualCheck->checkTotal, $currency)}}<br/>
                    
                @if($settings['brokerFieldEnabled'] && Session::get('brokerNameSource') != 'url')
                <div class="well-sm">
                    <form action="{{URL::action('CartController@applyBrokerName')}}" method="POST">
                        @if(Session::has('brokerName') && Session::get('brokerNameSource') == 'form')
                            <strong>{{$strings['str_affiliateCode']}}</strong> {{Session::get('brokerName')}}<br/>
                            {{$strings['str_wantToUpdateIt']}}
                            <br/>
                            <input type="text" name="brokerName" placeholder="{{$strings['str_updateAffiliateCode']}}">
                        @else
                            {{$strings['str_haveAnAffiliateCode']}}
                            <br/>
                            <input type="text" name="brokerName" placeholder="{{$strings['str_enterAffiliateCode']}}">
                        @endif

                        <button>{{$strings['str_apply']}}</button>
                    </form>
                </div>
                @endif
                <br/>
                <form action="{{URL::action('CheckoutController@entry')}}" id="checkoutForm" autocomplete="off">
                    <a class="btn formButton formButton-responsive" style="margin-bottom: 5px;" href="{{URL::to('step2')}}">{{$strings['str_addMoreRaces']}}</a>
                    <button class="btn formButton formButton-responsive" style="margin-bottom: 5px;" id="checkoutButton">{{$strings['str_proceedToCheckout']}}</button>
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
<script>
    window.onunload = function(){}; // prevent firefox's back button showing a disabled button / loading modal
    $(".disableDoubleClick").one("click", function() {
        $(this).click(function () { return false; });
    });
    $("#checkoutForm").validate({
        submitHandler: function(form) {
            $("#checkoutButton").prop('disabled', true);
            $('#loadingModal').modal();
            form.submit();
        }
    });
</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->

