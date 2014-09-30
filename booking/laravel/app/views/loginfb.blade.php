@extends('master')

<!-- PAGE TITLE -->
@section('title')
Facebook Login - Online Booking
@stop
<!-- END PAGE TITLE -->

<!-- PAGE CONTENT -->
<!-- BEGIN FACEBOOK INTEGRATION -->
@section('facebook_integration')
@parent
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

        Date.prototype.toDateInputValue = (function() {
            var local = new Date(this);
            local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
            return local.toJSON().slice(0,10);
        });

        //Callback when Facebook login status is established
        function statusChangeCallback(response) {
            if (response.hasOwnProperty('authResponse') && response.authResponse.hasOwnProperty('accessToken'))
            {
                $('#facebookToken').val(response.authResponse.accessToken);
            }
            if (response.status === 'connected') //If the user successfully logged in
            {
                FB.api('/me', function(response) {
                    $('#facebookId').val(response.id);
                    $('#email').val(response.email);
                    $('#firstname').val(response.first_name);
                    $('#lastname').val(response.last_name);
                    $('#birthdate').val(new Date(response.birthday).toDateInputValue());
                    $('#gender').val(response.gender);
                    $('#loginfbconfirm').submit();

                });
            }
            else //If the user did not successfully log in
            {
                $('#facebookError').show();
            }
        }
    </script>
@show
<!-- END FACEBOOK INTEGRATION -->

@section('steps')
<div class="steps">
    {{link_to('step1','See the Lineup')}} > <em>Choose a Race</em> >
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
        Connecting to Facebook...
    </div>

    <div class="redirectHeader centered">
        Redirecting in a moment...
    </div>

    <div class="centered" id="facebookError" style="display: none">
        <div class="alert alert-danger alert-dismissable" role="alert">
            <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
            There was a problem connecting to your Facebook account. Please go back and try again, or <a href="step2?create={{$heatId}}#{{$heatId}}"">create a new account</a> to book your race.
        </div>
    </div>

    <div style="display: none">
        <form id="loginfbconfirm" action="loginfbconfirm" method="POST">
            <input type="hidden" id="facebookId" name="facebookId" value="">
            <input type="hidden" id="facebookToken" name="facebookToken" value="">
            <input type="hidden" id="facebookAllowEmail" name="facebookAllowEmail" value="1"> <!-- Deprecated -->
            <input type="hidden" id="facebookAllowPost" name="facebookAllowPost" value="1"> <!-- Deprecated -->
            <input type="hidden" id="facebookEnabled" name="facebookEnabled" value="1"> <!-- Deprecated -->
            <input type="hidden" id="email" name="email" value="">
            <input type="hidden" id="firstname" name="firstname" value="">
            <input type="hidden" id="lastname" name="lastname" value="">
            <input type="hidden" id="birthdate" name="birthdate" value="">
            <input type="hidden" id="gender" name="gender" value="">
            <input type="hidden" name="heatId" value="{{$heatId}}">
            <input type="hidden" name="quantity" value="{{$quantity}}">
        </form>
    </div>
</div>
@stop
<!-- END PAGE CONTENT -->

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent

@stop
<!-- END JAVASCRIPT INCLUDES -->

