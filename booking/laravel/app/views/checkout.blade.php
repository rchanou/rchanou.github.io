@extends('master')

<!-- PAGE TITLE -->
@section('title')
{{$strings['str_checkoutTitle']}}
@stop
<!-- END PAGE TITLE -->


<!-- PAGE CONTENT -->
@section('steps')
<div class="steps">
    {{link_to('step1',$strings['str_seeTheLineup'])}} >
    @if(Session::has('lastSearch'))
    {{link_to('step2',$strings['str_chooseARace'])}} >
    @else
    {{$strings['str_chooseARace']}} >
    @endif
    @if(Session::has('authenticated'))
    {{link_to('cart',$strings['str_reviewYourOrder'])}}
    @else
    {{$strings['str_reviewYourOrder']}}
    @endif
    > <em>{{$strings['str_checkout']}}</em>
    </div>
@stop

@section('content')
<div class="mainBodyContent">
    <div class="mainBodyHeader">
        {{$strings['str_checkout']}}
    </div>

    @if($localCartHasExpiredItem)
    <div class="alert alert-danger alert-dismissable" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">{{$strings['str_close']}}</span></button>
        {{$strings['str_itemExpired']}}
    </div>
    @endif

    <div class="checkoutForm" id="checkoutForm">
        <form id="checkoutAndPaymentForm" action="pay" method="POST">

            <div class="formHeader">{{$strings['str_paymentInformation']}}</div>

            @if(Session::has('debug'))
            <a href="#" id="testdata">Populate with test data</a><p/>

            <script>
                $(document).ready(function() {
                    $('#testdata').click(function () {
                        $('#firstName').val('TestFirstName');
                        $('#lastName').val('TestLastName');
                        $('#number').val('4111111111111111');
                        $('#cvv').val('162');
                        $('#expiryMonth').val('7');
                        $('#expiryYear').val('2015');
                        $('#address1').val('123 Billing St');
                        $('#address2').val('Billpartment 1');
                        $('#city').val('Billstown');
                        $('#state').val('CA');
                        $('#postcode').val('12345');
                        $('#country').val('US');
                        $('#phone').val('(555) 1234567');
                    });
                });
            </script>
            @endif

            <!-- First name -->
            <label for="firstName">
                <strong>
                    {{$strings['str_firstName']}}: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="firstName" name="firstName" class="required" value="{{Input::old('firstName')}}"><br/>

            <!-- Last name -->
            <label for="lastName">
                <strong>
                    {{$strings['str_lastName']}}: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="lastName" name="lastName" class="required" value="{{Input::old('lastName')}}"><br/>

            <!-- Credit card number -->
            <label for="number">
                <strong>
                    {{$strings['str_creditCardNumber']}}: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="number" name="number" class="required"><br/>

            <!-- CVV -->
            <label for="cvv">
                <strong>
                    {{$strings['str_cvv']}}: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="4" type="text" id="cvv" name="cvv" class="required smallerFormInput"><br/>

            <!-- Expiration month and year -->
            <label for="expiryMonth">
                <strong>
                    {{$strings['str_expirationMonth']}}: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="2" type="text" id="expiryMonth" name="expiryMonth" class="required monthInput" placeholder="MM"><br/>
            <label for="expiryYear">
                <strong>
                    {{$strings['str_expirationYear']}}: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="4" type="text" id="expiryYear" name="expiryYear" class="required smallerFormInput" placeholder="YYYY"><br/>

            <!-- Address line 1 -->
            <label for="address1">
                <strong>
                    {{$strings['str_addressLine1']}}: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="address1" name="address1" class="required" value="{{Input::old('address1')}}"><br/>

            <!-- Address line 2 -->
            <label for="address2">
                <strong>
                    {{$strings['str_addressLine2']}}:
                </strong>
            </label>
            <input maxlength="255" type="text" id="address2" name="address2" value="{{Input::old('address2')}}"><br/>

            <!-- City -->
            <label for="city">
                <strong>
                    {{$strings['str_city']}}: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="city" name="city" class="required" value="{{Input::old('city')}}"><br/>

            <!-- State/Province/Territory -->
            <label for="state">
                <strong>
                    {{$strings['str_state']}}: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="state" name="state" class="required" value="{{Input::old('state')}}"><br/>

            <!-- Postal/zip code -->
            <label for="postcode">
                <strong>
                    {{$strings['str_postalCode']}}: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="postcode" name="postcode" class="required" value="{{Input::old('postcode')}}"><br/>

            <!-- Country -->
            <label for="country">
                <strong>
                    {{$strings['str_country']}}: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            @if(isset($countries) && count($countries) > 0 && isset($settings['defaultPaymentCountry']))
                {{Form::select('country', $countries, $settings['defaultPaymentCountry'], array("style" => "width: 300px;"))}}
            @else
                <input maxlength="255" type="text" id="country" name="country" class="required" value="{{Input::old('country')}}"><br/>
            @endif
            <!-- Phone -->
            <label for="phone">
                <strong>
                    {{$strings['str_phone']}}:
                </strong>
            </label>
            <input maxlength="255" type="text" id="phone" name="phone" value="{{Input::old('phone')}}"><br/>

            <div class="formHeader">{{$strings['str_orderSummary']}}</div>

            @if(count($cart)>0)
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
                                </tr>
                            @endif
                        @endforeach
                    </table>
                @endif
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
                                </tr>
                            @endif
                        @endforeach
                    </table>
                @endif
            @endif

            <div class="rightAligned">
            @if($settings['brokerFieldEnabled'] && Session::get('brokerNameSource') != 'url' && Session::has('brokerName'))
                <strong>Affiliate code:</strong> {{Session::get('brokerName')}}<br/>
            @endif
            <strong>{{$strings['str_order']}} {{$strings['str_subtotal']}}:</strong> {{$moneyFormatter->formatCurrency($virtualCheck->checkSubtotal, $currency)}}<br/>
            <strong>{{$strings['str_order']}} {{$strings['str_tax']}}:</strong> {{$moneyFormatter->formatCurrency($virtualCheck->checkTax, $currency)}}<br/>
            <strong>{{$strings['str_order']}} {{$strings['str_total']}}:</strong> {{$moneyFormatter->formatCurrency($virtualCheck->checkTotal, $currency)}}<br/>
            </div>

            @if(isset($settings['showTermsAndConditions']) && $settings['showTermsAndConditions'])
            <div class="formHeader">{{$strings['str_termsAndConditions']}}</div>

            <div class="well" style="height: 200px; overflow: auto;">
            {{nl2br($settings['termsAndConditions'])}}
            </div>
            <div class="text-right">
            <input type="checkbox" name="iAgree" id="iAgree"><label for="iAgree" style="font-size: 14px;">{{$strings['str_iAgreeToTheTermsAndConditions']}}</label>
            </div>
            @endif

            <input type="hidden" name="expectedSubtotal" value="{{$virtualCheck->checkSubtotal}}">
            <input type="hidden" name="expectedTax" value="{{$virtualCheck->checkTax}}">
            <input type="hidden" name="expectedTotal" value="{{$virtualCheck->checkTotal}}">

            @if(isset($settings['showTermsAndConditions']) && $settings['showTermsAndConditions'])
            <div class="rightAligned">
                <button type="submit" class="formButton formButtonDisabled" disabled id="makePaymentButton">{{$strings['str_pleaseAgreeToTheTermsAndConditions']}}</button>
            </div>
            @else
            <div class="rightAligned">
                <button type="submit" class="formButton" id="makePaymentButton">{{$strings['str_makePayment']}}</button>
            </div>
            @endif

        </form>
    </div>
</div>

<!-- Checkout form validation -->
<script>
$().ready( function() {

    $.validator.addMethod("requiredField",$.validator.methods.required,"{{$strings['str_thisFieldIsRequired']}}");
    $.validator.addClassRules("required", {requiredField: true});

    $.validator.addMethod("mustBeValidEmail",$.validator.methods.email,"{{$strings['str_mustBeAValidEmail']}}");
    $.validator.addClassRules("emailFormElement", {mustBeValidEmail: true});

    $("#checkoutAndPaymentForm").validate({
        submitHandler: function(form) {
            $("#makePaymentButton").prop('disabled', true);
            $('#loadingModal').modal();
            form.submit();
        },
        errorPlacement: function(error,element) {
            error.addClass("formError");
            error.insertAfter(element);
        },
        errorElement: 'div'

    });
});
</script>

<!-- Terms & Conditions checkbox -->
<script>
    $('#iAgree').click(function()
    {
        if (this.checked)
        {
            $('#makePaymentButton').removeClass('formButtonDisabled');
            $('#makePaymentButton').prop('disabled',false);
            $('#makePaymentButton').text('{{$strings['str_makePayment']}}');
        }
        else
        {
            $('#makePaymentButton').addClass('formButtonDisabled');
            $('#makePaymentButton').prop('disabled',true);
            $('#makePaymentButton').text('{{$strings['str_pleaseAgreeToTheTermsAndConditions']}}');
        }
    });
</script>
@stop
<!-- END PAGE CONTENT -->

<!-- FOOTER -->

<!-- END FOOTER -->