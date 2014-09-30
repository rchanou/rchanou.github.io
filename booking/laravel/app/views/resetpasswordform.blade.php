@extends('master')

<!-- PAGE TITLE -->
@section('title')
Pick A New Password - Online Booking
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
        Password Reset Form
    </div>
    <!-- PASSWORD RESET FORM -->
        @if($userNeedsToSubmitForm)
            <div class="loginToAccount">
                {{Form::open(array('action' => 'ResetPasswordController@resetPasswordSubmission','id' => 'requestPasswordResetForm'))}}
                    <div class="formHeader">Please choose a new password</div>
                    <label for="newpassword"><strong>New Password:</strong> <span class="requiredAsterisk">*</span></label> <input type="password" id="newpassword" name="newpassword" class="required validatePresence"><br/><br/>
                    <label for="confirmnewpassword"><strong>Confirm New Password:</strong> <span class="requiredAsterisk">*</span></label> <input type="password" id="confirmnewpassword" name="confirmnewpassword" class="required validatePresence"><br/><br/>
                    <div class="rightAligned">
                        <button type="submit" id="resetMyPasswordButton" class="formButton">Reset My Password</button>
                    </div>
                <input type="hidden" name="email" value="{{$email}}">
                <input type="hidden" name="token" value="{{$authToken}}">

                {{ Form::close() }}
            </div>
        @endif
        @if(isset($resetSuccessful) && $resetSuccessful)
        <div class="alert alert-success alert-dismissable centered" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            Success! Your password has been reset. <p/>Redirecting to the search page.
            <script type="text/javascript">
                var timer = setTimeout(function(){ window.location='{{action('Step1Controller@entry')}}';}, 5000);
            </script>
        </div>
        @elseif(isset($resetSuccessful) && !$resetSuccessful)
            <div class="alert alert-danger alert-dismissable centered" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                The authorization link you used is invalid or expired. <p/>Please {{link_to('resetpassword','request a new password link')}}.
            </div>
        @endif
</div>
@stop
<!-- END PAGE CONTENT -->

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent
<!-- Request password reset form validation -->
<script>
$().ready( function() {

    $.validator.addMethod("requiredField",$.validator.methods.required,"This field is required.");
    $.validator.addClassRules("required", {requiredField: true});

    $("#requestPasswordResetForm").validate({
        submitHandler: function(form) {
            $("#resetMyPasswordButton").prop('disabled', true);
            $('#loadingModal').modal();
            form.submit();
        },
        errorPlacement: function(error,element) {
            error.addClass("formError");
            error.insertAfter(element);
        },
        errorElement: 'div',
        rules: {
            confirmnewpassword: {
                equalTo: '#newpassword'
            }
        },
        messages:
        {
            confirmnewpassword: {
                equalTo: 'Passwords do not match'
            }
        }

    });
});
</script>

@stop
<!-- END JAVASCRIPT INCLUDES -->

