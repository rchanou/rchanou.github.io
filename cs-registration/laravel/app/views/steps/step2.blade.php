@extends('master')

<!-- HEADER -->
@section('backButton')
<a href="step1" class="arrow" onclick="$('#loadingModal').modal();"><span>{{$strings['backButton']}}</span></a>
@stop

@section('headerTitle')
{{$strings['step2HeaderTitle']}}
@stop

@section('languagesDropdown')

@stop
<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')
{{$strings['step2PageTitle']}}
@stop
<!-- END PAGE TITLE -->

@section('facebook_integration')
<!-- BEGIN FACEBOOK INTEGRATION -->
<script>
    function statusChangeCallback(response) {
        //console.log(response);
        if (response.status === 'connected') {
            autofillFormFields();
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

    Date.prototype.toDateInputValue = (function() {
        var local = new Date(this);
        local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
        return local.toJSON().slice(0,10);
    });

    function autofillFormFields() {
        FB.api('/me', function(response) {
            $('#firstname').val(response.first_name);
            $('#lastname').val(response.last_name);
            $('#email').val(response.email);
            $('#birthdate').val(new Date(response.birthday).toDateInputValue());
            $('#facebookprofile').attr("src","https://graph.facebook.com/" + response.id + "/picture?width=9999&height=9999");
            $('#facebookProfileURL').attr("value","https://graph.facebook.com/" + response.id + "/picture?width=9999&height=9999");
            $('#facebookprofile').show();
            switch(response.gender)
            {
                case 'male':
                    $('#male').prop('checked', true);
                    break;
                case 'female':
                    $('#female').prop('checked', true);
                    break;
                default:
                    $('#other').prop('checked', true);
                    break;
            }
        });
    }


</script>
<!-- END FACEBOOK INTEGRATION -->
@stop

<!-- PAGE CONTENT -->
@section('content')

{{ Form::open(array('action' => 'RegistrationController@postStep2', 'files' => 'true', 'style' => '')) }}

@if (count($errors) > 0) <!-- TODO: Move to errors.blade.php -->

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

<div class="row">
    <div class="col-sm-6">



        @if ($settings['showPicture'])
            <div class="centered">
            {{ Form::label('yourpicture', $strings['yourPicture'] . ':')}}<br/>
            <input type="file" capture="camera" accept="image/*" id="cameraInput" name="cameraInput" style="display: inline; color: white"><br/>
                <!-- BEGIN FACEBOOK PROFILE IMAGE -->
                <div class="centered">
                    @if (Input::old("facebookProfileURL") !== "#" && Input::old("facebookProfileURL") !== null)
                    <img id="facebookprofile" src="{{Input::old('facebookProfileURL')}}" style="display: inline" class="facebookProfilePicture"><br/>
                    <input type="hidden" name="facebookProfileURL" id="facebookProfileURL" value="{{Input::old('facebookProfileURL')}}">
                    @else
                    <img id="facebookprofile" src="#" style="display: none" class="facebookProfilePicture"><br/>
                    <input type="hidden" name="facebookProfileURL" id="facebookProfileURL" value="#">
                    @endif
                </div>
                <!-- END FACEBOOK PROFILE IMAGE -->
            <img id="mostRecentPicture" src="#" class="cameraPicture" style="display: none"><p/>
                {{ Form::button($strings['switchToFacebookPic'], array('class'=>'btn btn-primary',
                'id'=>'switchToFacebookPicButton', 'style'=>'display: none')) }}

            </div>
        @endif

        <div class="row">
            <div class="col-sm-6">
                @if ($settings['showFirstName'])
                <div class="centered">
                    {{ Form::label('firstname', $strings['firstname'] . ':') }}
                    @if($settings['requireFirstName'])
                    <span class="requiredAsterisk">*</span><br/>{{ Form::text('firstname','',array('class'=>'required')) }}<p/>
                    @else
                    <br/>{{ Form::text('firstname') }}<p/>
                    @endif
                </div>
                @endif
            </div>
            <div class="col-sm-6">
                @if ($settings['showLastName'])
                <div class="centered">
                    {{ Form::label('lastname', $strings['lastname'] . ':') }}
                    @if($settings['requireLastName'])
                    <span class="requiredAsterisk">*</span><br/>{{ Form::text('lastname','',array('class'=>'required')) }}<p/>
                    @else
                    <br/>{{ Form::text('lastname') }}<p/>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                @if ($settings['CfgRegAddShow'])
                <div class="centered">
                    {{ Form::label('Address', $strings['Address'] . ':') }}
                    @if($settings['CfgRegAddReq'])
                    <span class="requiredAsterisk">*</span><br/>{{ Form::text('Address','',array('class'=>'required')) }}<p/>
                    @else
                    <br/>{{ Form::text('Address') }}<p/>
                    @endif
                </div>
                @endif
            </div>
            <div class="col-sm-6">
                @if ($settings['CfgRegAddShow'])
                <div class="centered">
                    {{ Form::label('Address2', $strings['Address2'] . ':') }}
                    <br/>{{ Form::text('Address2') }}<p/>
                </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                @if ($settings['CfgRegCntryShow'])
                <div class="centered">
                    {{ Form::label('country', $strings['countries'] . ':') }}
                    @if ($settings['CfgRegCntryReq'])
                    <span class="requiredAsterisk">*</span><br/>
                    {{ Form::select('Country', $settings['countries'], 'United States', array('style' => 'color: black; height: 26px', 'class' => 'required','id'=>'country') ) }}
                    @else
                    <br/>{{ Form::select('Country', $settings['countries'], 'United States' ,array('style' => 'color: black; height: 26px', 'id'=>'country') ) }}
                    @endif
                </div>
                @endif
            </div>
        </div>
        <div class="row" style="margin-top: 10px;">
            @if ($settings['CfgRegCityShow'])
            <div class="{{$addressColumnClass}}">
                <div class="centered">
                    {{ Form::label('city', $strings['city'] . ':',array('id'=>'cityLabel')) }}
                    @if ($settings['CfgRegCityReq'])
                    <span class="requiredAsterisk">*</span><br/>
                    <input type="text" name="City" id="city" class="required">
                    @else
                    <br/><input type="text" name="City" id="city">
                    @endif
                </div>
            </div>
            @endif
            @if ($settings['CfgRegStateShow'])
            <div class="{{$addressColumnClass}}">
                <div class="centered">
                    {{ Form::label('state', $strings['states'] . ':',array('id'=>'stateLabel')) }}
                    @if ($settings['CfgRegStateReq'])
                    <span class="requiredAsterisk">*</span><br/>
                    <input type="text" name="State" id="state" class="required">
                    @else
                    <br/><input type="text" name="State" id="state">
                    @endif
                </div>
            </div>
            @endif
            @if ($settings['CfgRegZipShow'])
            <div class="{{$addressColumnClass}}">
                <div class="centered">
                    <div>
                        {{ Form::label('Zip', $strings['Zip'] . ':') }}
                        @if($settings['CfgRegZipReq'])
                        <span class="requiredAsterisk">*</span><br/>{{ Form::text('Zip','',array('class'=>'required')) }}<p/>
                        @else
                        <br/>{{ Form::text('Zip') }}<p/>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>


        <p/>


    </div>
    <div class="col-sm-6">


        @if ($settings['CfgRegRcrNameShow'])
        <div class="centered">
            {{ Form::label('racername', $strings['racername'] . ':') }}
            @if($settings['CfgRegRcrNameReq'])
            <span class="requiredAsterisk">*</span><br/>{{ Form::text('racername','',array('class'=>'required')) }}<p/>
            @else
            <br/>{{ Form::text('racername') }}<p/>
            @endif
        </div>
        @endif

        <div class="centered">
            {{ Form::label('gender', $strings['gender'] . ':') }} <span class="requiredAsterisk">*</span><br/>

            {{ $strings['Male'] }} {{ Form::radio('gender', 'male', true,array('id'=>'male')) }}
            {{ $strings['Female'] }} {{ Form::radio('gender', 'female',false, array('id'=>'female')) }}
            {{ $strings['Other'] }} {{ Form::radio('gender', 'other',false, array('id'=>'other')) }}<p/>
        </div>


        @if ($settings['showBirthDate'])
        <div class="centered">
            {{ Form::label('birthdate', $strings['birthdate'] . ':') }}
            @if($settings['requireBirthDate'])
            <span class="requiredAsterisk">*</span><br/><input style="line-height: normal !important" type="date" name="birthdate" id="birthdate" class="required" value="{{Input::old('birthdate')}}"><p/>
            @else
            <br/><input style="line-height: normal !important" type="date" name="birthdate" id="birthdate"><p/>
            @endif
        </div>
        @endif

        @if ($settings['CfgRegPhoneShow'])
        <div class="centered">
            {{ Form::label('mobilephone', $strings['mobilephone'] . ':') }}
            @if($settings['CfgRegPhoneReq'])
            <span class="requiredAsterisk">*</span><br/>{{ Form::text('mobilephone','',array('class'=>'required')) }}<p/>
            @else
            <br/>{{ Form::text('mobilephone') }}<p/>
            @endif
            @if( Config::has('config.showTextingWaiver') && Config::get('config.showTextingWaiver') && Config::has('config.textingWaiver') )
                {{Config::get('config.textingWaiver')}}
            @endif
        </div>
        @endif

        @if ($settings['CfgRegSrcShow'])
        <div class="centered">
            {{ Form::label('howdidyouhearaboutus', $strings['howdidyouhearaboutus'] . ':') }}<br/>
            {{ Form::select('howdidyouhearaboutus', $settings['dropdownOptions'], '',array('style' => 'color: black;') ) }}<p/>
        </div>
        @endif

        @if ($settings['CfgRegEmailShow'])
            <div class="centered">
            {{ Form::label('email', $strings['email'] . ':') }} <!-- TODO: Clean this up -->
                @if($settings['CfgRegEmailReq'] && $settings['CfgRegDisblEmlForMinr'] == false)
                <span class="requiredAsterisk" id="emailasterisk">*</span><br/>{{ Form::text('email','',array('class'=>'required')) }}<p/>
                @elseif($settings['CfgRegEmailReq'] && $settings['CfgRegDisblEmlForMinr'])
                <span class="requiredAsterisk" id="emailasterisk">*</span><br/>{{ Form::text('email','',array('class'=>'required','disabled'=>'disabled')) }}<p/>
                @elseif($settings['CfgRegDisblEmlForMinr'])
                <br/>{{ Form::text('email','',array('disabled'=>'disabled')) }}<p/>
                @else
                <br/>{{ Form::text('email') }}<p/>
                @endif

                <!-- For communication with JavaScript -->
                @if($settings['CfgRegEmailReq'])
                    <input type="hidden" name="emailRequired" id="emailRequired" value="true">
                @else
                    <input type="hidden" name="emailRequired" id="emailRequired" value="false">
                @endif

                @if($settings['CfgRegDisblEmlForMinr'])
                    <input type="hidden" name="disableEmailIfMinor" id="disableEmailIfMinor" value="true">
                @else
                    <input type="hidden" name="disableEmailIfMinor" id="disableEmailIfMinor" value="false">
                @endif

            {{str_replace('##TRACKNAME##',$settings['BusinessName'],$strings['emailText'])}}<p/>

            {{ Form::checkbox('consenttoemail', 'true',false) }} {{ Form::label('consenttoemail', $strings['emailsOptIn']) }}
            </div>
        @endif

        <input type="hidden" name="isMinor" id="isMinor" value="false">
        <input type="hidden" name="minorAge" id="minorAge" value={{$settings['AgeNeedParentWaiver']}}>

    </div>
</div>

@stop
<!-- END PAGE CONTENT -->

<!-- FOOTER -->

@section('leftFooterButton')
{{ Form::reset($strings['step2Clear'], array('class'=>'leftButton btn btn-danger btn-lg', 'id'=>'resetButton')) }}
@stop

@section('rightFooterButton')
{{ Form::submit($strings['step2Submit'], array('class'=>'rightButton btn btn-success btn-lg')) }}
{{ Form::close() }}
@stop

<!-- END FOOTER -->

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
    @parent

    <!-- CAMERA CAPTURE SCRIPT AND MINOR EMAIL CONTROLS -->
    <script>
    $(document).ready(function() {
        $("#cameraInput").on("change",pictureCaptured);
        $("#resetButton").on("click",resetPictures);
        $("#switchToFacebookPicButton").on("click",switchToFacebookPic)
        if ($('#facebookprofile').attr("src") != "#")
        {
            $('#facebookprofile').show();
            $('#mostRecentPicture').hide();
        }
        var disableEmailIfMinor = $('#disableEmailIfMinor').val();

        if (disableEmailIfMinor == "true")
        {
            checkAge();
            $("#birthdate").change(checkAge);
        }
        else
        {
            $('#email').prop('disabled',false);
            updateAge();
            $("#birthdate").change(updateAge);
        }

/*      console.log("Camera input value: " + $("#cameraInput").val());
        console.log("Picture taken: " + $('#mostRecentPicture').attr("src"));
        console.log("Facebook: " + $('#facebookprofile').attr("src"));
        console.log("Facebook form value: " + $('#facebookProfileURL').attr("value"));*/

    });

    function updateAge()
    {
        var minorAge = $('#minorAge').val();
        var birthdate = $("#birthdate").val();
        var today = moment().format("YYYY-MM-DD");
        var age = moment().diff(moment(birthdate,"YYYY-MM-DD"),'years');
        if (age >= minorAge)
        {
            $('#isMinor').val("false");
        }
        else
        {
            $('#isMinor').val("true");
        }
    }

    function checkAge()
    {
        var emailRequired = $('#emailRequired').val();
        var disableEmailIfMinor = $('#disableEmailIfMinor').val();
        var minorAge = $('#minorAge').val();

/*        console.log("Email required? " + emailRequired);
        console.log("Disable e-mail if minor? " + disableEmailIfMinor);
        console.log("A minor is younger than " + minorAge);*/

        var birthdate = $("#birthdate").val();
        var today = moment().format("YYYY-MM-DD");
        var age = moment().diff(moment(birthdate,"YYYY-MM-DD"),'years');

/*        console.log("Birth date changed");
        console.log("Date value = " + birthdate); //YYYY-MM-DD
        console.log("Today = " + today);
        console.log("Age = " + age);*/

        if (age >= minorAge)
        {
            //console.log("Adult!");
            $('#isMinor').val("false");
            $('#email').prop('disabled',false);
            //console.log("isMinor = " + $('#isMinor').val());
            if(emailRequired == "true")
            {
                $('#email').addClass('required');
                $('#emailasterisk').show();
                var email = new LiveValidation('email');
                email.add( Validate.Presence,
                    { failureMessage: "{{$strings['required']}}" } )
                    .add( Validate.Email );
                email.enable();
            }
        }
        else
        {
            //console.log("Minor!");
            $('#isMinor').val("true");
            //console.log("isMinor = " + $('#isMinor').val());
            if (disableEmailIfMinor == "true")
            {
                $('#email').prop('disabled',true);
                $('#email').removeClass('required');
                $('#emailasterisk').hide();
                $('#email').val("");
                var email = new LiveValidation('email');
                email.disable();
                email.remove( Validate.Presence,
                    { failureMessage: "{{$strings['required']}}" } )
                    .remove( Validate.Email );
            }
        }
    }

    function pictureCaptured(event) {
    if(event.target.files.length == 1 &&
        event.target.files[0].type.indexOf("image/") == 0) {
        $("#mostRecentPicture").attr("src",URL.createObjectURL(event.target.files[0]));
        $('#facebookprofile').hide();
        $('#facebookProfileURL').attr("value","#");
        $('#mostRecentPicture').show();
        if ($('#facebookprofile').attr("src") != "#")
        {
            $('#switchToFacebookPicButton').show();
        }
/*        console.log("Camera input value: " + $("#cameraInput").val());
        console.log("Picture taken: " + $('#mostRecentPicture').attr("src"));
        console.log("Facebook: " + $('#facebookprofile').attr("src"));
        console.log("Facebook form value: " + $('#facebookProfileURL').attr("value"));*/
        }
    }

    function resetPictures(event) {
        $("#mostRecentPicture").attr("src","#");
        $('#facebookprofile').hide();
        $('#mostRecentPicture').hide();
        if ($('#facebookprofile').attr("src") != "#")
        {
            $('#switchToFacebookPicButton').show();
        }
        $('#facebookProfileURL').attr("value","#");

/*        console.log("Camera input value: " + $("#cameraInput").val());
        console.log("Picture taken: " + $('#mostRecentPicture').attr("src"));
        console.log("Facebook: " + $('#facebookprofile').attr("src"));
        console.log("Facebook form value: " + $('#facebookProfileURL').attr("value"));*/
    }

    function switchToFacebookPic(event) {
        $("#mostRecentPicture").attr("src","#");
        $('#facebookprofile').show();
        $('#facebookProfileURL').attr("value",$('#facebookprofile').attr("src"));
        $('#switchToFacebookPicButton').hide();
        $('#mostRecentPicture').hide();
        $("#cameraInput").val('');

/*        console.log("Camera input value: " + $("#cameraInput").val());
        console.log("Picture taken: " + $('#mostRecentPicture').attr("src"));
        console.log("Facebook: " + $('#facebookprofile').attr("src"));
        console.log("Facebook form value: " + $('#facebookProfileURL').attr("value"));*/

    }
    </script>
    <!-- END CAMERA CAPTURE SCRIPT -->

    <!-- LIVE VALIDATION SCRIPT -->
    <script>
        $(document).ready(function() {

            /* Live Validation does not work on date input types
            if ($('#birthdate').length > 0 && $('#birthdate').hasClass('required'))
            {
                var birthdate = new LiveValidation('birthdate');
                birthdate.add( Validate.Presence,
                    { failureMessage: "Required"} );
            }*/


            if ($('#mobilephone').length > 0 && $('#mobilephone').hasClass('required'))
            {
                var mobilephone = new LiveValidation('mobilephone');
                mobilephone.add( Validate.Presence,
                    { failureMessage: "{{$strings['required']}}" } );
            }


            if ($('#firstname').length > 0 && $('#firstname').hasClass('required'))
            {
                var firstname = new LiveValidation('firstname');
                firstname.add( Validate.Presence,
                    { failureMessage: "{{$strings['required']}}" } );
            }

            if ($('#lastname').length > 0 && $('#lastname').hasClass('required'))
            {
                var lastname = new LiveValidation('lastname');
                lastname.add( Validate.Presence,
                    { failureMessage: "{{$strings['required']}}" } );

            }

            if ($('#racername').length > 0 && $('#racername').hasClass('required'))
            {
                var racername = new LiveValidation('racername');
                racername.add( Validate.Presence,
                    { failureMessage: "{{$strings['required']}}" } );
            }


            if ($('#email').length > 0 && $('#email').hasClass('required'))
            {
                var email = new LiveValidation('email');
                email.add( Validate.Presence,
                    { failureMessage: "{{$strings['required']}}" } )
                    .add( Validate.Email );
            }
            else if ($('#email').length > 0 )
            {
                var email = new LiveValidation('email');
                email.add( Validate.Email, { failureMessage: "{{$strings['mustBeAValidEmailAddress']}}" } );
            }

            if ($('#Address').length > 0 && $('#Address').hasClass('required'))
            {
                var Address = new LiveValidation('Address');
                Address.add( Validate.Presence,
                    { failureMessage: "{{$strings['required']}}" } );
            }

            if ($('#state').length > 0 && $('#state').hasClass('required'))
            {
                var state = new LiveValidation('state');
                state.add( Validate.Presence,
                    { failureMessage: "{{$strings['required']}}" } );
            }

            if ($('#Zip').length > 0 && $('#Zip').hasClass('required'))
            {
                var Zip = new LiveValidation('Zip');
                Zip.add( Validate.Presence,
                    { failureMessage: "{{$strings['required']}}" } );
            }

            if ($('#city').length > 0 && $('#city').hasClass('required'))
            {
                var city = new LiveValidation('city');
                city.add( Validate.Presence,
                    { failureMessage: "{{$strings['required']}}" } );
            }
        });


    </script>
    <!-- END LIVE VALIDATION SCRIPT -->

    <!-- UNITED STATES AND CANADA DROPDOWN SCRIPT -->
    <script>
        $(function()
            {
                $('#country').change(function()
                {
                    var selectedCountry = $('#country').val();
                    if (selectedCountry == 'United States')
                    {
                        $('#stateLabel').text('State');
                    }
                    else if (selectedCountry == 'Canada')
                    {
                        $('#stateLabel').text('Province/Territory');
                    }
                    else
                    {
                        $('#stateLabel').text('State/Province/Territory');
                    }
                });
            }
        );
    </script>
    <!-- END UNITED STATES AND CANADA DROPDOWN SCRIPT -->

@stop
<!-- END JAVASCRIPT INCLUDES -->