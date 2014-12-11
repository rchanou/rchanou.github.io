@extends('master')

<!-- PAGE TITLE -->
@section('title')
{{$strings['str_resetPasswordTitle']}}
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
    @if(Session::has('authenticated') && Session::has('cart') && count(Session::get('cart')) > 0)
    > {{link_to('checkout',$strings['str_checkout'])}}
    @else
    > {{$strings['str_checkout']}}
    @endif
</div>
@stop

@section('content')
<div class="mainBodyContent">
    <div class="mainBodyHeader">
        {{$strings['str_passwordResetClaimAccount']}}
    </div>
    <!-- PASSWORD RESET REQUEST FORM-->
        @if(!isset($resetRequestSuccessful))
            <div class="loginToAccount">
                <form action="resetpassword" id="requestResetTokenForm" method="POST">
                    <div class="formHeader">{{$strings['str_enterEmailToResetPassword']}}</div>
                    <label for="EmailAddress"><strong>{{$strings['str_emailAddress']}}:</strong> <span class="requiredAsterisk">*</span></label> <input type="text" id="EmailAddress" name="EmailAddress" class="required mustBeValidEmail"><br/>
                    <div class="rightAligned">
                        <button type="submit" id="resetMyPasswordButton" class="formButton">{{$strings['str_resetMyPassword']}}</button>
                    </div>
                </form>
            </div>
        @elseif($resetRequestSuccessful == true)
            <div class="alert alert-success alert-dismissable" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">{{$strings['str_close']}}</span></button>
                {{$strings['str_successPasswordReset']}}
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

    $.validator.addMethod("requiredField",$.validator.methods.required,"{{$strings['str_thisFieldIsRequired']}}");
    $.validator.addClassRules("required", {requiredField: true});

    $.validator.addMethod("mustBeValidEmail",$.validator.methods.email,"{{$strings['str_mustBeAValidEmail']}}");
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

