@extends('master')

<!-- PAGE TITLE -->
@section('title')
{{$strings['str_resetPasswordFormTitle']}}
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
        {{$strings['str_passwordResetForm']}}
    </div>
    <!-- PASSWORD RESET FORM -->
        @if($userNeedsToSubmitForm)
            <div class="loginToAccount">
                {{Form::open(array('action' => 'ResetPasswordController@resetPasswordSubmission','id' => 'requestPasswordResetForm'))}}
                    <div class="formHeader">{{$strings['str_pleaseChooseANewPassword']}}</div>
                    <label for="newpassword"><strong>{{$strings['str_newPassword']}}:</strong> <span class="requiredAsterisk">*</span></label> <input type="password" id="newpassword" name="newpassword" class="required validatePresence"><br/><br/>
                    <label for="confirmnewpassword"><strong>{{$strings['str_confirmNewPassword']}}:</strong> <span class="requiredAsterisk">*</span></label> <input type="password" id="confirmnewpassword" name="confirmnewpassword" class="required validatePresence"><br/><br/>
                    <div class="rightAligned">
                        <button type="submit" id="resetMyPasswordButton" class="formButton">{{$strings['str_resetMyPassword']}}</button>
                    </div>
                <input type="hidden" name="email" value="{{$email}}">
                <input type="hidden" name="token" value="{{$authToken}}">

                {{ Form::close() }}
            </div>
        @endif
        @if(isset($resetSuccessful) && $resetSuccessful)
        <div class="alert alert-success alert-dismissable centered" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">{{$strings['str_close']}}</span></button>
            {{$strings['str_successPasswordResetFinal']}} <p/>{{$strings['str_redirectingToSearchPage']}}
            <script type="text/javascript">
                var timer = setTimeout(function(){ window.location='{{action('Step1Controller@entry')}}';}, 5000);
            </script>
        </div>
        @elseif(isset($resetSuccessful) && !$resetSuccessful)
            <div class="alert alert-danger alert-dismissable centered" role="alert">
                <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">{{$strings['str_close']}}</span></button>
                {{$strings['str_invalidResetLink']}} <p/>{{$strings['str_please']}} {{link_to('resetpassword',$strings['str_requestANewPasswordLink'])}}.
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

    $.validator.addMethod("requiredField",$.validator.methods.required,"{{$strings['str_thisFieldIsRequired']}}");
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
                equalTo: '{{$strings['str_passwordsDoNotMatch']}}'
            }
        }

    });
});
</script>

@stop
<!-- END JAVASCRIPT INCLUDES -->

