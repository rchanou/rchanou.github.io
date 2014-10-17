@extends('master')

<!-- PAGE TITLE -->
@section('title')
Checkout
@stop
<!-- END PAGE TITLE -->


<!-- PAGE CONTENT -->
@section('steps')
<div class="steps">
    {{link_to('step1','See the Lineup')}} >
    @if(Session::has('lastSearch'))
    {{link_to('step2','Choose a Race')}} >
    @else
    Choose a Race >
    @endif
    @if(Session::has('authenticated'))
    {{link_to('cart','Review Your Order')}}
    @else
    Review Your Order
    @endif
    > <em>Checkout</em>
    </div>
@stop

@section('content')
<div class="mainBodyContent">
    <div class="mainBodyHeader">
        Checkout
    </div>

    @if($localCartHasExpiredItem)
    <div class="alert alert-danger alert-dismissable" role="alert">
        <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        One or more items in your cart have expired and have been removed.
    </div>
    @endif

    <div class="checkoutForm" id="checkoutForm">
        <form id="checkoutAndPaymentForm" action="pay" method="POST">

            <div class="formHeader">Payment Information</div>

            <!-- First name -->
            <label for="firstName">
                <strong>
                    First Name: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="firstName" name="firstName" class="required" value="{{Input::old('firstName')}}"><br/>

            <!-- Last name -->
            <label for="lastName">
                <strong>
                    Last Name: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="lastName" name="lastName" class="required" value="{{Input::old('lastName')}}"><br/>

            <!-- Credit card number -->
            <label for="number">
                <strong>
                    Credit Card Number: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="number" name="number" class="required"><br/>

            <!-- CVV -->
            <label for="cvv">
                <strong>
                    CVV: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="4" type="text" id="cvv" name="cvv" class="required smallerFormInput"><br/>

            <!-- Expiration month and year -->
            <label for="expiryMonth">
                <strong>
                    Expiration Month: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="2" type="text" id="expiryMonth" name="expiryMonth" class="required monthInput" placeholder="MM"><br/>
            <label for="expiryYear">
                <strong>
                    Expiration Year: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="4" type="text" id="expiryYear" name="expiryYear" class="required smallerFormInput" placeholder="YYYY"><br/>

            <!-- Address line 1 -->
            <label for="address1">
                <strong>
                    Address line 1: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="address1" name="address1" class="required" value="{{Input::old('address1')}}"><br/>

            <!-- Address line 2 -->
            <label for="address2">
                <strong>
                    Address line 2:
                </strong>
            </label>
            <input maxlength="255" type="text" id="address2" name="address2" value="{{Input::old('address2')}}"><br/>

            <!-- City -->
            <label for="city">
                <strong>
                    City: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="city" name="city" class="required" value="{{Input::old('city')}}"><br/>

            <!-- State/Province/Territory -->
            <label for="state">
                <strong>
                    State/Province/Territory: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="state" name="state" class="required" value="{{Input::old('state')}}"><br/>

            <!-- Postal/zip code -->
            <label for="postcode">
                <strong>
                    Postal/zip code: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="postcode" name="postcode" class="required" value="{{Input::old('postcode')}}"><br/>

            <!-- Country -->
            <label for="country">
                <strong>
                    Country: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="country" name="country" class="required" value="{{Input::old('country')}}"><br/>

            <!-- Phone -->
            <label for="phone">
                <strong>
                    Phone:
                </strong>
            </label>
            <input maxlength="255" type="text" id="phone" name="phone" value="{{Input::old('phone')}}"><br/>

            <!-- Email -->
            <label for="email">
                <strong>
                    Email: <span class="requiredAsterisk">*</span>
                </strong>
            </label>
            <input maxlength="255" type="text" id="email" name="email" class="emailFormElement required" value="{{Input::old('email')}}"><br/>

            <div class="formHeader">Order Summary</div>

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
                    </tr>
                @endforeach
                </table>
            @endif

            <div class="rightAligned">
                <strong>Subtotal:</strong> ${{number_format($virtualCheck->checkSubtotal,2)}}<br/>
                <strong>Tax:</strong> ${{number_format($virtualCheck->checkTax,2)}}<br/>
                <strong>Total:</strong> ${{number_format($virtualCheck->checkTotal,2)}}<br/>
            </div>

            <input type="hidden" name="expectedSubtotal" value="{{$virtualCheck->checkSubtotal}}">
            <input type="hidden" name="expectedTax" value="{{$virtualCheck->checkTax}}">
            <input type="hidden" name="expectedTotal" value="{{$virtualCheck->checkTotal}}">

            <div class="rightAligned">
                <button type="submit" class="formButton" id="makePaymentButton">Make Payment</button>
            </div>

        </form>
    </div>
</div>

<!-- Checkout form validation -->
<script>
$().ready( function() {

    $.validator.addMethod("requiredField",$.validator.methods.required,"This field is required.");
    $.validator.addClassRules("required", {requiredField: true});

    $.validator.addMethod("mustBeValidEmail",$.validator.methods.email,"Must be a valid e-mail.");
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
@stop
<!-- END PAGE CONTENT -->

<!-- FOOTER -->

<!-- END FOOTER -->