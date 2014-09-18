<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

    <!-- BEGIN CSS INCLUDES -->
    @section('css_includes')
    <link rel="stylesheet" href="css/vendors/bootstrap.min.css" />
    <link rel="stylesheet" href="css/vendors/bootstrap-theme.min.css" />
    <link rel="stylesheet" href="css/cs-registration.css?<?php echo time(); ?>" />

    <?php
        if (file_exists('css/custom-styles.css')) //Used for tracks to overwrite css
        {
            echo '<link rel="stylesheet" href="css/custom-styles.css?' . time() .'" />';
        }
    ?>
    @show
    <!-- END CSS INCLUDES -->

    <title>@yield('title', 'Registration Kiosk')</title>
</head>

<body style="background-image:url({{$images['bg_image']}})">
@section('facebook_integration')
<!-- BEGIN FACEBOOK INTEGRATION -->
<script>
    // This is called with the results from from FB.getLoginStatus().
    function statusChangeCallback(response) {
        //console.log('statusChangeCallback');
        //console.log(response);
        // The response object is returned with a status field that lets the
        // app know the current login status of the person.
        // Full docs on the response object can be found in the documentation
        // for FB.getLoginStatus().

        if (response.status === 'connected') {
            // Logged into your app and Facebook.
            FB.logout(function(response) {
                //console.log("User logged off Facebook");
            });
        }
    }

    window.fbAsyncInit = function() {
        FB.init({
            //appId      : '1407866362829010', 296582647086963
            appId      : '296582647086963',
            cookie     : true,  // enable cookies to allow the server to access
            // the session
            xfbml      : true,  // parse social plugins on this page
            version    : 'v2.0' // use version 2.0
        });

        // Now that we've initialized the JavaScript SDK, we call
        // FB.getLoginStatus().  This function gets the state of the
        // person visiting this page and can return one of three states to
        // the callback you provide.  They can be:
        //
        // 1. Logged into your app ('connected')
        // 2. Logged into Facebook, but not your app ('not_authorized')
        // 3. Not logged into Facebook and can't tell if they are logged into
        //    your app or not.
        //
        // These three cases are handled in the callback function.

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
<script src="js/vendors/jquery-2.1.0.min.js"></script>
<script src="js/vendors/moment-with-langs.min.js"></script>
<script src="js/vendors/bootstrap.min.js"></script>
<script src="js/vendors/holder.js"></script> <!-- TODO: Eliminate eventually -->
<script src="js/vendors/livevalidation.min.js"></script>
<script src="js/vendors/dropdown.js"></script>
<script src="js/vendors/modernizr.js"></script>

@show
<!-- END JAVASCRIPT INCLUDES -->

</body>

</HTML>