<HTML>
    <head>
    </head>
    <body>
    <script type="text/javascript">
        window.onload = function() {
            document.getElementById("l").src = "{{$url}}"; //Opens the iPhone app's password reset page

            setTimeout(function() {
                window.location = "{{URL::action('ResetPasswordController@resetPasswordRequest')}}"; //Fallback: On-site reset page
            }, 500);

        };
    </script>
    <iframe id="l" width="1" height="1" style="visibility:hidden"></iframe>
    </body>
</HTML>
