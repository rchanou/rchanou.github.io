@extends('master')

<!-- HEADER -->
@section('backButton')
@stop

@section('headerTitle')
{{$strings['str_step4HeaderTitle']}}
@stop

@section('languagesDropdown')

@stop
<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')
{{$strings['str_step4PageTitle']}}
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
        if (response.status === 'connected') {
            $('#facebookLikeBox').show(); //Only shows the Facebook Like Box if the user is logged in
        }
    }

    // This function is called when someone finishes with the Login
    // Button.  See the onlogin handler attached to it in the sample
    // code below.
    function checkLoginState() {
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
    }

    window.fbAsyncInit = function() {
        FB.init({
            //appId      : '1407866362829010',
            appId      : '296582647086963',
            cookie     : true,  // enable cookies to allow the server to access
            // the session
            xfbml      : true,  // parse social plugins on this page
            version    : 'v2.0' // use version 2.0
        });

        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });

    };

    // Load the SDK asynchronously
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/{{$currentCultureFB}}/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

</script>
<!-- END FACEBOOK INTEGRATION -->
    @endif
@stop


<!-- PAGE CONTENT -->
@section('content')

<div class="row">
    <div class="col-sm-12" style="text-align: center; font-size: 18px">
        {{$strings['str_registrationCompleteMessage']}}<p/>
        @if (array_key_exists('FacebookPageURL', $settings) && $settings['FacebookPageURL'] != "" && $settings['Reg_EnableFacebook'])
        <div id="facebookLikeBox" class="fb-like-box" style="display: none" data-href="{{$settings['FacebookPageURL']}}" data-colorscheme="dark" data-show-faces="true" data-header="true" data-stream="false" data-show-border="false"></div>
        @else
        @endif
        <p/>
        <a href="{{Session::has('ipcam') ? 'step1' . '?&terminal=' . Session::get('ipcam') : 'step1' }}" onclick="$('#loadingModal').modal();">
        <img src="{{$images['completeRegistration']}}" class="center-block" style="margin-top: 10px;">
        <div class="text-center" style="font-size: 20px;">{{$strings['str_completeRegistration']}}</div>
        </a>
    </div>
</div>

<script type="text/javascript">
    var timer = setTimeout(function(){ window.location='{{Session::has('ipcam') ? 'step1' . '?&terminal=' . Session::get('ipcam') : 'step1' }}';}, 15000);
</script>
@stop
<!-- END PAGE CONTENT -->