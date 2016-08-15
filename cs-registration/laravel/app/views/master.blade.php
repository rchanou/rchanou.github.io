<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <!-- BEGIN CSS INCLUDES -->
    @section('css_includes')
        {{ HTML::style('css/vendors/bootstrap.min.css') }}
        {{ HTML::style('css/vendors/jquery-ui/jquery-ui.min.css') }}
        {{ HTML::style('css/vendors/jquery-ui/jquery-ui.theme.min.css') }}
        {{ HTML::style('css/vendors/bootstrap-theme.min.css') }}
        {{ HTML::style('css/vendors/bootstrap-theme.min.css') }}

    <link rel="stylesheet" href="{{asset("css/cs-registration.css?" . time())}}" />

    <?php

        function remoteFileExists($url) {
            $url = str_replace('https:', 'http:' , $url);
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_NOBODY, true);
            $result = curl_exec($curl);
            $ret = false;
            if ($result !== false) {
                $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                if ($statusCode == 200) {
                    $ret = true;
                }
            }
            curl_close($curl);
            return $ret;
        }

        if (file_exists('css/custom-styles.css')) //Deprecated method used for tracks to overwrite css
        {
            echo '<link rel="stylesheet" href="' . 'http://' . $_SERVER['HTTP_HOST'] . '/cs-registration/css/custom-styles.css?' . time() .'" />';
        }

    ?>

    <?php $customStylesURL = 'http://' . $_SERVER['HTTP_HOST'] . '/assets/cs-registration/css/custom-styles.css?' . time(); //To prevent caching ?>
    {{ (remoteFileExists($customStylesURL) ? HTML::style($customStylesURL) : '') }}
    @show
    <!-- END CSS INCLUDES -->

    <title>@yield('title', 'Registration Kiosk')</title>
</head>

<body style="background-image:url({{asset($images['bg_image'])}})">
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
<script src="{{asset("js/vendors/js.cookie.js")}}"></script>

<script>

    // Workaround for FB bug: https://developers.facebook.com/bugs/1518419788457961/
    Cookies.remove('fbsr_296582647086963',{ path: '/', domain: '.clubspeedtiming.com' });
    Cookies.remove('fblo_296582647086963',{ path: '/', domain: '.clubspeedtiming.com' });
    Cookies.remove('fbm_296582647086963',{ path: '/', domain: '.clubspeedtiming.com' });

    function statusChangeCallback(response) {
        if (response.status === 'connected') {
            FB.logout(function(response) {
                // Workaround for FB bug: https://developers.facebook.com/bugs/1518419788457961/
                Cookies.remove('fbsr_296582647086963',{ path: '/', domain: '.clubspeedtiming.com' });
                Cookies.remove('fblo_296582647086963',{ path: '/', domain: '.clubspeedtiming.com' });
                Cookies.remove('fbm_296582647086963',{ path: '/', domain: '.clubspeedtiming.com' });
            });
        }
    }

    window.fbAsyncInit = function() {
        FB.init({
            appId      : '296582647086963',
            cookie     : true,
            xfbml      : true,
            version    : 'v2.6'
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

</script>
<!-- END FACEBOOK INTEGRATION -->
    @endif
@show

<div class="container">

<!-- BEGIN GLOBAL HEADER INCLUDE -->
@include('header')
<!-- END GLOBAL HEADER INCLUDE -->

<!-- START CONTENT INCLUDE -->

@yield('content')

<!-- END CONTENT INCLUDE -->

<!-- START GLOBAL FOOTER INCLUDE -->
@include('footer')
<!-- END GLOBAL FOOTER INCLUDE -->

</div>

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
<script src="{{asset("js/vendors/jquery-2.1.0.min.js")}}"></script>
<script src="{{asset("js/vendors/moment-with-langs.min.js")}}"></script>
<script src="{{asset("js/vendors/bootstrap.min.js")}}"></script>
<script src="{{asset("js/vendors/holder.js")}}"></script> <!-- TODO: Eliminate eventually -->
<script src="{{asset("js/vendors/livevalidation.min.js")}}"></script>
<script src="{{asset("js/vendors/dropdown.js")}}"></script>

{{ HTML::script('js/vendors/modernizr-latest.js') }}
{{ HTML::script('js/vendors/jquery-ui/jquery-ui.min.js') }}

    <!-- Custom JS if present in /assets -->
    <?php $customJavaScriptURL = 'http://' . $_SERVER['HTTP_HOST'] . '/assets/cs-registration/js/custom-js.js?' . time(); //To prevent caching ?>
    {{ ((remoteFileExists($customJavaScriptURL)) ? HTML::script($customJavaScriptURL) : '') }}

    <script>
        if (!Modernizr.inputtypes.date) {
            $('input[type=date]').datepicker({
                // Consistent format with the HTML5 picker
                dateFormat: 'yy-mm-dd',
                changeYear: true,
                yearRange: "1900:2012",
                defaultDate: '-21y'
            });
        }
    </script>

@show
<!-- END JAVASCRIPT INCLUDES -->

</body>

</HTML>