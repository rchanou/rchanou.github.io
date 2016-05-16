<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1">

    <!-- BEGIN CSS INCLUDES -->
    @section('css_includes')
    {{ HTML::style('css/vendors/bootstrap.min.css') }}
    {{ HTML::style('css/vendors/jquery-ui/jquery-ui.min.css') }}
    {{ HTML::style('css/vendors/jquery-ui/jquery-ui.theme.min.css') }}
    {{ HTML::style('css/vendors/bootstrap-theme.min.css') }}

    <?php $bookingStylesURL = 'css/booking.css?v=1.7'; //To prevent caching ?>
    {{ HTML::style($bookingStylesURL) }}

    {{ HTML::script('js/vendors/jquery-2.1.0.min.js')}} <!-- Needed earlier -->

    <!-- Custom CSS style if present in /assets -->
    <?php $customStylesURL = str_replace('http://','https://', Config::get('config.assetsURL')) . '/css/custom-styles.css?' . time() //To prevent caching ?>
    {{ (remoteFileExists(Config::get('config.assetsURL') . '/css/custom-styles.css?') ? HTML::style($customStylesURL) : '') }}
    @show

    <?php $backgroundURL = str_replace('http://','https://', Config::get('config.assetsURL') . '/images/background.jpg'); ?>
    @if(remoteFileExists(Config::get('config.assetsURL') . '/images/background.jpg'))
        <style>
        body {
            background-image:url('{{$backgroundURL}}');
        }
        </style>
    @endif
    <!-- END CSS INCLUDES -->

    <title>@yield('title', 'Club Speed Online Booking')</title>
</head>

<body class="body-responsive">

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
        <div class="row">
        @include('languagedropdown-responsive')
            <div id="mainPageWrapper-responsive">

            <!-- BEGIN GLOBAL HEADER INCLUDE -->
            @include('header-responsive')
            <!-- END GLOBAL HEADER INCLUDE -->

            <!-- BEGIN ERRORS INCLUDE -->
            @include('errors', array('errors' => $errors))
            <!-- END ERRORS INCLUDE -->

            <!-- START CONTENT INCLUDE -->
            @yield('content')
            <!-- END CONTENT INCLUDE -->

            <!-- START GLOBAL FOOTER INCLUDE -->
            @include('footer-responsive')
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
    <?php $customJavaScriptURL = str_replace('http://','https://', Config::get('config.assetsURL')) . '/js/custom-js.js?v=1.2'; //To prevent caching ?>
    {{ ((remoteFileExists(Config::get('config.assetsURL') . '/js/custom-js.js?')) ? HTML::script($customJavaScriptURL) : '') }}

    <script>
        if (!Modernizr.inputtypes.date) {
            $('input[type=date]').datepicker({
                // Consistent format with the HTML5 picker
                dateFormat: 'yy-mm-dd',
                changeYear: true,
                yearRange: "-100:+0"
            });
        }

    </script>

    <script>
        $(document).ready(function () {

            window.setTimeout(function() {
              $(".fadeAway").fadeTo(500, 0).slideUp(500, function(){
                  $(this).remove();
              });
            }, 5000);
        });
    </script>
    @show
    <!-- END JAVASCRIPT INCLUDES -->

</body>

</HTML>