@extends('master')

<!-- HEADER -->
@section('backButton')
    <a href="{{$step1URL}}" class="arrow" onclick="$('#loadingModal').modal();"><span>{{$strings['str_backButton']}}</span></a>
@stop

@section('headerTitle')
{{$strings['str_checkInFinal']}}
@stop

@section('languagesDropdown')

@stop
<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')

@stop
<!-- END PAGE TITLE -->

@section('facebook_integration')
    <?php
    $facebookEnabled = false;
    if (Session::has('settings'))
    {
        $settings = Session::get('settings');
        $facebookEnabled = isset($settings['Reg_EnableFacebook']) ? $settings['Reg_EnableFacebook'] : false;
    }
    ?>
    @if($facebookEnabled)
<!-- BEGIN FACEBOOK INTEGRATION -->
<script>
    function statusChangeCallback(response) {
        if (response != null && response.hasOwnProperty('authResponse') && typeof response.authResponse != "undefined" && response.authResponse.hasOwnProperty('accessToken'))
        {
            $('#facebookToken').val(response.authResponse.accessToken);
        }
        if (response.status === 'connected') {
            getFacebookInfo();
        }
    }

    function checkLoginState() {
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
    }

    window.fbAsyncInit = function() {
        FB.init({
            appId      : '296582647086963',
            cookie     : true,
            xfbml      : true,
            version    : 'v2.0'
        });

        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });

    };

    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/{{$currentCultureFB}}/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    function getFacebookInfo() {
        FB.api('/me', function(response) {
            $('#facebookId').val(response.id);
    });
    }


</script>
    @endif
<!-- END FACEBOOK INTEGRATION -->
@stop

<!-- PAGE CONTENT -->
@section('content')

{{ Form::open(array('action' => 'CheckInController@postCheckInFinal', 'files' => 'true', 'style' => '', 'autocomplete' => 'off')) }}

@if (count($errors) > 0)

    <script>
        var errorMessages = {{$errors->toJson()}};
        var errorString = "";
        for (var key in errorMessages) {
            if (errorMessages.hasOwnProperty(key)) {
                errorString = errorString + errorMessages[key] + "\n";
            }
        }
        alert(errorString);
    </script>

@endif


<div class="row formArea">
    <div class="col-sm-2">
    </div>
    <div class="col-sm-8">
        <div class="centered">
            @if ($settings['CfgRegValidateGrp'])
                <div class="centered">
                    {{ Form::label('eventgroupid', $strings['str_imherefor'] . ':') }}
                    <span class="requiredAsterisk">*</span><br/>
                    {{ Form::select('eventgroupid', $settings['eventGroupIDOptions'], Input::old('eventgroupid',''),array('style' => 'color: black;') ) }}<p/>
                </div>
            @else
                <div class="centered">
                    {{ Form::label('eventgroupid', $strings['str_imherefor'] . ':') }}
                    <span class="requiredAsterisk">*</span><br/>
                    {{ Form::select('eventgroupid', array(-1 => $strings['str_walkIn']), Input::old('eventgroupid',''),array('style' => 'color: black;') ) }}<p/>
                </div>
            @endif
        </div>
    </div>
    <div class="col-sm-2">
    </div>
</div>

        <input type="hidden" id="facebookId" name="facebookId" value="">
        <input type="hidden" id="facebookToken" name="facebookToken" value="">
@stop
<!-- END PAGE CONTENT -->

<!-- FOOTER -->

@section('leftFooterButton')
        <a href="{{$step1URL}}" id="idisagreeButton" class="btn btn-danger btn-lg leftButton" onclick="$('#loadingModal').modal();">{{$strings['str_cancel']}}</a>
@stop

@section('rightFooterButton')
{{ Form::submit($strings['str_checkInSubmit'], array('class'=>'rightButton btn btn-success btn-lg', 'id'=>'submitButton', 'onclick' => "$('#loadingModal').modal();")) }}
{{ Form::close() }}
@stop

<!-- END FOOTER -->