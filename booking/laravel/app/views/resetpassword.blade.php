@extends('master')

<!-- PAGE TITLE -->
@section('title')
Reset My Password - Online Booking
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
    @if(Session::has('authenticated') && Session::has('cart') && count(Session::get('cart')) > 0)
    > {{link_to('checkout','Checkout')}}
    @else
    > Checkout
    @endif
</div>
@stop

@section('content')
<div class="mainBodyContent">
    <div class="mainBodyHeader">
        Password Reset / Claim Account
    </div>
    <!-- PASSWORD RESET REQUEST FORM-->
        @if(!isset($resetRequestSuccessful))
            <div class="loginToAccount">
                <form action="resetpassword" id="requestResetTokenForm" method="POST">
                    <div class="formHeader">Enter your e-mail address below to reset your password</div>
                    <label for="EmailAddress"><strong>Email Address:</strong> <span class="requiredAsterisk">*</span></label> <input type="text" id="EmailAddress" name="EmailAddress" class="required mustBeValidEmail"><br/>
                    <div class="rightAligned">
                        <button type="submit" id="resetMyPasswordButton" class="formButton">Reset My Password</button>
                    </div>
                </form>
            </div>
        @elseif($resetRequestSuccessful == true)
            <div class="alert alert-success alert-dismissable" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                Success! If your e-mail address has an account, a password reset link has been sent to it. Please open that e-mail and click the link inside to continue.
            </div>
        @endif
</div>
@stop
<!-- END PAGE CONTENT -->

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent

<!-- Request password reset token form validation -->
<script>
$().ready( function() {

    $.validator.addMethod("requiredField",$.validator.methods.required,"This field is required.");
    $.validator.addClassRules("required", {requiredField: true});

    $.validator.addMethod("mustBeValidEmail",$.validator.methods.email,"Must be a valid e-mail.");
    $.validator.addClassRules("emailFormElement", {mustBeValidEmail: true});

    $("#requestResetTokenForm").validate({
        submitHandler: function(form) {
            $("#resetMyPasswordButton").prop('disabled', true);
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
<!-- END JAVASCRIPT INCLUDES -->

