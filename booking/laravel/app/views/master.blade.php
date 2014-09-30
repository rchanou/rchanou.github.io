<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />

    <!-- BEGIN CSS INCLUDES -->
    @section('css_includes')
    {{ HTML::style('css/vendors/bootstrap.min.css') }}
    {{ HTML::style('css/vendors/jquery-ui/jquery-ui.min.css') }}
    {{ HTML::style('css/vendors/jquery-ui/jquery-ui.theme.min.css') }}
    {{ HTML::style('css/vendors/bootstrap-theme.min.css') }}

    <?php $bookingStylesURL = 'css/booking.css?' . time() //To prevent caching ?>
    {{ HTML::style($bookingStylesURL) }}

    {{ HTML::script('js/vendors/jquery-2.1.0.min.js')}} <!-- Needed earlier -->

    <!-- Custom CSS style if present in /assets -->
    <?php $customStylesURL = '/css/custom-styles.css?' . time() //To prevent caching ?>
    {{ ((remoteFileExists(Config::get('config.assetsURL') . $customStylesURL)) ? HTML::style(Config::get('config.assetsURL') . $customStylesURL) : '') }}
    @show
    <!-- END CSS INCLUDES -->

    <title>@yield('title', 'Club Speed Online Booking')</title>
</head>

<body>

<!-- BEGIN FACEBOOK INTEGRATION -->
    @section('facebook_integration')
        <script>
            //Loads Facebook SDK
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_us/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

            //Initializes Facebook integration
            window.fbAsyncInit = function() {
                FB.init({
                    appId      : '296582647086963',
                    cookie     : true,
                    xfbml      : true,
                    version    : 'v2.0'
                });

                //Determines the user's login state
                FB.getLoginStatus(function(response) {
                    statusChangeCallback(response);
                });

            };

            //Callback when Facebook login status is established
            function statusChangeCallback(response) {
            }
        </script>
    @show
<!-- END FACEBOOK INTEGRATION -->

    <div class="container">
        <div id="mainPageWrapper">
            <div>
        <!-- BEGIN GLOBAL HEADER INCLUDE -->
        @include('header')
        <!-- END GLOBAL HEADER INCLUDE -->

        <!-- BEGIN ERRORS INCLUDE -->
        @include('errors', array('errors' => $errors))
        <!-- END ERRORS INCLUDE -->

        <!-- START CONTENT INCLUDE -->
        @yield('content')
        <!-- END CONTENT INCLUDE -->

        <!-- START GLOBAL FOOTER INCLUDE -->
        @include('footer')
        <!-- END GLOBAL FOOTER INCLUDE -->

            </div>
        </div>
    </div>

    <!-- BEGIN JAVASCRIPT INCLUDES -->
    @section('js_includes')
    {{ HTML::script('js/vendors/moment-with-langs.min.js')}}
    {{ HTML::script('js/vendors/bootstrap.min.js')}}
    {{ HTML::script('js/vendors/jquery.validate.min.js') }}
    {{ HTML::script('js/vendors/modernizr-latest.js') }}
    {{ HTML::script('js/vendors/jquery-ui/jquery-ui.min.js') }}

    <!-- Custom JS if present in /assets -->
    {{ ((remoteFileExists(Config::get('config.assetsURL') . '/js/custom-js.js')) ? HTML::script(Config::get('config.assetsURL') . '/js/custom-js.js') : '') }}

    <script>
        if (!Modernizr.inputtypes.date) {
            $('input[type=date]').datepicker({
                // Consistent format with the HTML5 picker
                dateFormat: 'yy-mm-dd',
                changeYear: true
            });
        }
    </script>
    @show
    <!-- END JAVASCRIPT INCLUDES -->

</body>

</HTML>

<?php
//Returns true if a remote file exists, false otherwise
function remoteFileExists($url) {
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

?>