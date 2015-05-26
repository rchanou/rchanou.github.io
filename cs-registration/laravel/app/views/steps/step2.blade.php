@extends('master')

<!-- HEADER -->
@section('backButton')
<a href="{{$step1URL}}" class="arrow" onclick="$('#loadingModal').modal();"><span>{{$strings['str_backButton']}}</span></a>
@stop

@section('headerTitle')
{{$strings['str_step2HeaderTitle']}}
@stop

@section('languagesDropdown')

@stop
<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')
{{$strings['str_step2PageTitle']}}
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
        //console.log(response);
        if (response != null && response.hasOwnProperty('authResponse') && typeof response.authResponse != "undefined" && response.authResponse.hasOwnProperty('accessToken'))
        {
            $('#facebookToken').val(response.authResponse.accessToken);
        }
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
            $('#facebookId').val(response.id);
            $('#firstname').val(response.first_name);
            $('#lastname').val(response.last_name);
            $('#email').val(response.email);
            if (typeof response.birthday != "undefined")
            {
                $('#birthdate').val(new Date(response.birthday).toDateInputValue());
            }
            $('#facebookprofile').attr("src","https://graph.facebook.com/" + response.id + "/picture?width=9999&height=9999");
            $('#facebookProfileURL').attr("value","https://graph.facebook.com/" + response.id + "/picture?width=9999&height=9999");
            $('#cameraInputIPCam_currentSnapshot').attr("src","https://graph.facebook.com/" + response.id + "/picture?width=9999&height=9999");
            $('#cameraInputIPCam_currentSnapshotURL').attr("value","https://graph.facebook.com/" + response.id + "/picture?width=9999&height=9999");
            $('#cameraInputIPCam_currentSnapshot').show();

            var c = document.getElementById('cameraInputLocalCam_currentSnapshot');
            @if (Session::has('localcam'))
            var ctx = c.getContext('2d');
            var img = new Image;
            img.setAttribute('crossOrigin', 'anonymous');
            img.onload = function(){
                ctx.drawImage(img,20,0,180,180);
            };
            img.src = "https://graph.facebook.com/" + response.id + "/picture?width=9999&height=9999";
            @endif

            $('#cameraInputLocalCam_currentSnapshotURL').attr("value","https://graph.facebook.com/" + response.id + "/picture?width=9999&height=9999");
            $('#cameraInputLocalCam_currentSnapshot').show();
            $('#facebookprofile').show();
            $('#switchToFacebookIPCamButton').hide();
            $('#switchToFacebookLocalCamButton').hide();
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
    @endif
<!-- END FACEBOOK INTEGRATION -->
@stop

<!-- PAGE CONTENT -->
@section('content')

{{ Form::open(array('action' => 'Step2Controller@postStep2', 'files' => 'true', 'style' => '')) }}

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

<div class="row formArea">
    <div class="col-sm-6">

        @if ($settings['Reg_CaptureProfilePic'] && !Session::has('ipcam') && !Session::has('localcam'))
            <div class="centered">
            {{ Form::label('yourpicture', $strings['str_yourPicture'] . ':')}}<br/>
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
                {{ Form::button($strings['str_switchToFacebookPic'], array('class'=>'btn btn-primary',
                'id'=>'switchToFacebookPicButton', 'style'=>'display: none')) }}

            </div>
        @endif

        <!-- BEGIN IP CAMERA CODE -->
        @if (Session::has('ipcam'))

        <div class="centered">
            <div class="row">
                <div class="col-sm-6">
                    {{$strings['str_liveImageFromCamera']}}<br/>
                    <img name="cameraInputIPCam" id="cameraInputIPCam" src="http://{{Session::get('ipCamURL')}}?count=0"
                         onload="setTimeout('refreshCameraImage()',200)" onabort="setTimeout('refreshCameraImage()',5000)">
                    <br/>
                    <button class="btn btn-primary" type="button" id="captureIPCamSnapshotButton" onclick="updateIPCamSnapshot()" style="margin-top: 10px;">{{$strings['str_takeAPicture']}}</button>

                </div>
                <div class="col-sm-6">
                    {{$strings['str_currentProfilePicture']}}<br/>
                    @if (Input::old("facebookProfileURL") !== "#" && Input::old("facebookProfileURL") !== null && Input::old("facebookProfileURL") !== "" && substr(Input::old("cameraInputIPCam_currentSnapshotURL"),0,26) == "https://graph.facebook.com")
                    <img name="cameraInputIPCam_currentSnapshot" id="cameraInputIPCam_currentSnapshot" src="{{Input::old('cameraInputIPCam_currentSnapshotURL')}}"><br/>
                    <input type="hidden" name="facebookProfileURL" id="facebookProfileURL" value="{{Input::old('facebookProfileURL')}}">
                    <input type="hidden" name="cameraInputIPCam_currentSnapshotBase64" id="cameraInputIPCam_currentSnapshotBase64" value="">
                    <input type="hidden" name="cameraInputIPCam_currentSnapshotURL" id="cameraInputIPCam_currentSnapshotURL" value="{{Input::old('cameraInputIPCam_currentSnapshotURL')}}">
                    @else
                    <img name="cameraInputIPCam_currentSnapshot" id="cameraInputIPCam_currentSnapshot" src="{{Input::old('cameraInputIPCam_currentSnapshotBase64')}}"><br/>
                    <input type="hidden" name="facebookProfileURL" id="facebookProfileURL" value="{{Input::old('facebookProfileURL')}}">
                    <input type="hidden" name="cameraInputIPCam_currentSnapshotBase64" id="cameraInputIPCam_currentSnapshotBase64" value="{{Input::old('cameraInputIPCam_currentSnapshotBase64')}}">
                    <input type="hidden" name="cameraInputIPCam_currentSnapshotURL" id="cameraInputIPCam_currentSnapshotURL" value="{{Input::old('cameraInputIPCam_currentSnapshotURL')}}">
                    @endif
                    @if (Input::old("facebookProfileURL") !== "#" && Input::old("facebookProfileURL") !== null && Input::old("facebookProfileURL") !== "" && (substr(Input::old("cameraInputIPCam_currentSnapshotURL"),0,5) == "data:"))
                    <button class="btn btn-primary" type="button" id="switchToFacebookIPCamButton" onclick="switchToFacebookIPCam()" style="margin-top: 10px;">{{$strings['str_switchToFacebookPicture']}}</button>
                    @else
                    <button class="btn btn-primary" type="button" id="switchToFacebookIPCamButton" onclick="switchToFacebookIPCam()" style="display: none; margin-top: 10px;">{{$strings['str_switchToFacebookPicture']}}</button>
                    @endif
                </div>
            </div>


        </div>

        @endif
        <!-- END IP CAMERA CODE -->

        <!-- BEGIN LOCAL CAMERA CODE -->
        @if (Session::has('localcam'))

            <div class="centered">
                <div class="row">
                    <div class="col-sm-6">
                        {{$strings['str_liveImageFromCamera']}}<br/>
                        <video id="cameraInputLocalCam" width="240" height="180" autoplay="autoplay" style="border: 1px solid red; width: 240px; height: 180px;">
                            Your browser does not support the video tag.
                        </video>
                        <br/>
                        <button class="btn btn-primary" type="button" id="captureLocalCamSnapshotButton" onclick="updateLocalCamSnapshot()" style="margin-top: 10px;">{{$strings['str_takeAPicture']}}</button>

                    </div>
                    <div class="col-sm-6">
                        {{$strings['str_currentProfilePicture']}}<br/>
                        @if (Input::old("facebookProfileURL") !== "#" && Input::old("facebookProfileURL") !== null && Input::old("facebookProfileURL") !== "" && substr(Input::old("cameraInputLocalCam_currentSnapshotURL"),0,26) == "https://graph.facebook.com")
                            <canvas style="width: 240px; height: 180px;" width="240" height="180" name="cameraInputLocalCam_currentSnapshot" id="cameraInputLocalCam_currentSnapshot"></canvas><br/>
                            <input type="hidden" name="facebookProfileURL" id="facebookProfileURL" value="{{Input::old('facebookProfileURL')}}">
                            <input type="hidden" name="cameraInputLocalCam_currentSnapshotBase64" id="cameraInputLocalCam_currentSnapshotBase64" value="">
                            <input type="hidden" name="cameraInputLocalCam_currentSnapshotURL" id="cameraInputLocalCam_currentSnapshotURL" value="{{Input::old('cameraInputLocalCam_currentSnapshotURL')}}">
                        @else
                            <canvas style="width: 240px; height: 180px;" width="240" height="180" name="cameraInputLocalCam_currentSnapshot" id="cameraInputLocalCam_currentSnapshot"></canvas><br/>
                            <input type="hidden" name="facebookProfileURL" id="facebookProfileURL" value="{{Input::old('facebookProfileURL')}}">
                            <input type="hidden" name="cameraInputLocalCam_currentSnapshotBase64" id="cameraInputLocalCam_currentSnapshotBase64" value="{{Input::old('cameraInputLocalCam_currentSnapshotBase64')}}">
                            <input type="hidden" name="cameraInputLocalCam_currentSnapshotURL" id="cameraInputLocalCam_currentSnapshotURL" value="{{Input::old('cameraInputLocalCam_currentSnapshotURL')}}">
                        @endif
                        @if (Input::old("facebookProfileURL") !== "#" && Input::old("facebookProfileURL") !== null && Input::old("facebookProfileURL") !== "" && (substr(Input::old("cameraInputLocalCam_currentSnapshotURL"),0,5) == "data:"))
                            <button class="btn btn-primary" type="button" id="switchToFacebookLocalCamButton" onclick="switchToFacebookLocalCam()" style="margin-top: 10px;">{{$strings['str_switchToFacebookPicture']}}</button>
                        @else
                            <button class="btn btn-primary" type="button" id="switchToFacebookLocalCamButton" onclick="switchToFacebookLocalCam()" style="display: none; margin-top: 10px;">{{$strings['str_switchToFacebookPicture']}}</button>
                        @endif
                    </div>
                </div>


            </div>
        @endif
        <!-- END LOCAL CAMERA CODE -->

        <div class="row" style="margin-top: 10px;">
            <div class="col-sm-6">
                @if ($settings['showFirstName'])
                <div class="centered">
                    {{ Form::label('firstname', $strings['str_firstname'] . ':') }}
                    @if($settings['requireFirstName'])
                    <span class="requiredAsterisk">*</span><br/>{{ Form::text('firstname',Input::old('firstname',''),array('class'=>'required','maxlength'=>'50', 'autocapitalize'=>'words')) }}<p/>
                    @else
                    <br/>{{ Form::text('firstname',Input::old('firstname',''),array('maxlength'=>'50', 'autocapitalize'=>'words')) }}<p/>
                    @endif
                </div>
                @endif
            </div>
            <div class="col-sm-6">
                @if ($settings['showLastName'])
                <div class="centered">
                    {{ Form::label('lastname', $strings['str_lastname'] . ':') }}
                    @if($settings['requireLastName'])
                    <span class="requiredAsterisk">*</span><br/>{{ Form::text('lastname',Input::old('lastname',''),array('class'=>'required','maxlength'=>'50', 'autocapitalize'=>'words')) }}<p/>
                    @else
                    <br/>{{ Form::text('lastname',Input::old('lastname',''),array('maxlength'=>'50', 'autocapitalize'=>'words')) }}<p/>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                @if ($settings['CfgRegAddShow'])
                <div class="centered">
                    {{ Form::label('Address', $strings['str_Address'] . ':') }}
                    @if($settings['CfgRegAddReq'])
                    <span class="requiredAsterisk">*</span><br/>{{ Form::text('Address',Input::old('Address',''),array('class'=>'required','maxlength'=>'80')) }}<p/>
                    @else
                    <br/>{{ Form::text('Address',Input::old('Address',''),array('maxlength'=>'80')) }}<p/>
                    @endif
                </div>
                @endif
            </div>
            <div class="col-sm-6">
                @if ($settings['CfgRegAddShow'])
                <div class="centered">
                    {{ Form::label('Address2', $strings['str_Address2'] . ':') }}
                    <br/>{{ Form::text('Address2',Input::old('Address2',''),array('maxlength'=>'255')) }}<p/>
                </div>
                @endif
            </div>
        </div>
        <div class="row">
            @if ($settings['CfgRegCityShow'])
            <div class="col-sm-6">
                <div class="centered">
                    {{ Form::label('city', $strings['str_city'] . ':',array('id'=>'cityLabel')) }}
                    @if ($settings['CfgRegCityReq'])
                    <span class="requiredAsterisk">*</span><br/>
                    <input type="text" name="City" id="city" class="required" maxlength="80" value="{{Input::old('City','')}}">
                    @else
                    <br/><input type="text" name="City" id="city" maxlength="80" value="{{Input::old('City','')}}">
                    @endif
                </div>
            </div>
            @endif
            @if ($settings['CfgRegStateShow'])
            <div class="col-sm-6">
                <div class="centered">
                    {{ Form::label('state', $strings['str_states'] . ':',array('id'=>'stateLabel')) }}
                    @if ($settings['CfgRegStateReq'])
                    <span class="requiredAsterisk">*</span><br/>
                    <input type="text" name="State" id="state" class="required" maxlength="50" value="{{Input::old('State','')}}">
                    @else
                    <br/><input type="text" name="State" id="state" maxlength="50" value="{{Input::old('State','')}}">
                    @endif
                </div>
            </div>
            @endif
            @if ($settings['CfgRegZipShow'])
                @if( $settings['CfgRegCityShow'] && $settings['CfgRegStateShow'] )
                    <div class="col-sm-6" style="margin-top: 10px;">
                @else
                    <div class="col-sm-6">
                @endif
                <div class="centered">
                    <div>
                        {{ Form::label('Zip', $strings['str_Zip'] . ':') }}
                        @if($settings['CfgRegZipReq'])
                            @if($settings['zipValidated'])
                                <span class="requiredAsterisk">*</span><br/>{{ Form::text('Zip',Input::old('Zip',''),array('class'=>'required validated','maxlength'=>'15')) }}<p/>
                            @else
                                <span class="requiredAsterisk">*</span><br/>{{ Form::text('Zip',Input::old('Zip',''),array('class'=>'required','maxlength'=>'15')) }}<p/>
                            @endif
                        @else
                        <br/>{{ Form::text('Zip',Input::old('Zip',''),array('maxlength'=>'15')) }}<p/>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
        <div class="row">
            <div class="col-sm-12">
                @if ($settings['CfgRegCntryShow'])
                <div class="centered">
                    {{ Form::label('country', $strings['str_countries'] . ':') }}
                    @if ($settings['CfgRegCntryReq'])
                    <span class="requiredAsterisk">*</span><br/>
                    {{ Form::select('Country', $settings['countries'], isset($settings['defaultCountry'])? $settings['defaultCountry']
                      : ( Config::has('config.defaultCountry') ? Config::get('config.defaultCountry') : Input::old('Country','United States') ),
                      array('style' => 'color: black; height: 26px', 'class' => 'required','id'=>'country') ) }}
                    @else
                    <br/>
                    {{ Form::select('Country', $settings['countries'], isset($settings['defaultCountry'])? $settings['defaultCountry']
                      : ( Config::has('config.defaultCountry') ? Config::get('config.defaultCountry') : Input::old('Country','United States') ),
                      array('style' => 'color: black; height: 26px', 'id'=>'country') ) }}
                    @endif
                </div>
                @endif
            </div>
        </div>

        <p/>


    </div>
    <div class="col-sm-6">


        <div class="row">
            <div class="col-sm-6">
                @if ($settings['CfgRegRcrNameShow'])
                <div class="centered">
                    {{ Form::label('racername', $strings['str_racername'] . ':') }}
                    @if($settings['CfgRegRcrNameReq'])
                    <span class="requiredAsterisk">*</span><br/>{{ Form::text('racername',Input::old('racername',''),array('class'=>'required','maxlength'=>'100')) }}<p/>
                    @else
                    <br/>{{ Form::text('racername',Input::old('racername',''),array('maxlength'=>'100')) }}<p/>
                    @endif
                </div>
                @endif
            </div>
            <div class="col-sm-6">
                @if ($settings['genderShown'])
                <div class="centered">
                    {{ Form::label('gender', $strings['str_gender'] . ':') }} @if ($settings['genderRequired'])<span class="requiredAsterisk">*</span>@endif<br/>

                    {{ $strings['str_Male'] }} {{ Form::radio('gender', 'male', true,array('id'=>'male')) }}
                    {{ $strings['str_Female'] }} {{ Form::radio('gender', 'female',false, array('id'=>'female')) }}
                    {{ $strings['str_Other'] }} {{ Form::radio('gender', 'other',false, array('id'=>'other')) }}<p/>
                </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                @if ($settings['showBirthDate'])
                <div class="centered">
                    {{ Form::label('birthdate', $strings['str_birthdate'] . ':') }}
                    @if($settings['requireBirthDate'])
                    <span class="requiredAsterisk">*</span><br/><input style="line-height: normal !important" type="date" name="birthdate" id="birthdate" class="required" value="{{Input::old('birthdate')}}"><p/>
                    @else
                    <br/><input style="line-height: normal !important" type="date" name="birthdate" id="birthdate" value="{{Input::old('birthdate')}}"><p/>
                    @endif
                </div>
                @endif
            </div>
            <div class="col-sm-6">
                @if ($settings['CfgRegSrcShow'])
                <div class="centered">
                    {{ Form::label('howdidyouhearaboutus', $strings['str_howdidyouhearaboutus'] . ':') }}
                    @if($settings['CfgRegSrcReq'])
                    <span class="requiredAsterisk">*</span><br/>{{ Form::select('howdidyouhearaboutus', $settings['dropdownOptions'], Input::old('howdidyouhearaboutus','0'),array('style' => 'color: black; max-width: 170px;', 'class'=>'required') ) }}<p/>
                    @else
                    <br/>{{ Form::select('howdidyouhearaboutus', $settings['dropdownOptions'], Input::old('howdidyouhearaboutus','0'),array('style' => 'color: black; max-width: 170px;') ) }}<p/>
                    @endif
                    <br/>
                </div>
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-sm-6">
                @if ($settings['cfgRegCustTxt1Show'])
                <div class="centered">
                    {{ Form::label('Custom1', $strings['str_Custom1'] . ':') }}
                    @if($settings['cfgRegCustTxt1req'])
                    <span class="requiredAsterisk">*</span><br/>{{ Form::text('Custom1',Input::old('Custom1',''),array('maxlength'=>'50','class'=>'required')) }}<p/>
                    @else
                    <br/>{{ Form::text('Custom1',Input::old('Custom1',''),array('maxlength'=>'50')) }}<p/>
                    @endif
                </div>
                @endif
            </div>
            <div class="col-sm-6">
                @if ($settings['cfgRegCustTxt2Show'])
                <div class="centered">
                    {{ Form::label('Custom2', $strings['str_Custom2'] . ':') }}
                    @if($settings['cfgRegCustTxt2req'])
                    <span class="requiredAsterisk">*</span><br/>{{ Form::text('Custom2',Input::old('Custom2',''),array('maxlength'=>'50','class'=>'required')) }}<p/>
                    @else
                    <br/>{{ Form::text('Custom2',Input::old('Custom2',''),array('maxlength'=>'50')) }}<p/>
                    @endif
                </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                @if ($settings['cfgRegCustTxt3Show'])
                <div class="centered">
                    {{ Form::label('Custom3', $strings['str_Custom3'] . ':') }}
                    @if($settings['cfgRegCustTxt3req'])
                    <span class="requiredAsterisk">*</span><br/>{{ Form::text('Custom3',Input::old('Custom3',''),array('maxlength'=>'50','class'=>'required')) }}<p/>
                    @else
                    <br/>{{ Form::text('Custom3',Input::old('Custom3',''),array('maxlength'=>'50')) }}<p/>
                    @endif
                </div>
                @endif
            </div>
            <div class="col-sm-6">
                @if ($settings['cfgRegCustTxt4Show'])
                <div class="centered">
                    {{ Form::label('Custom4', $strings['str_Custom4'] . ':') }}

                    @if($settings['cfgRegCustTxt4req'])
                    <span class="requiredAsterisk">*</span><br/>{{ Form::text('Custom4',Input::old('Custom4',''),array('maxlength'=>'50','class'=>'required')) }}<p/>
                    @else
                    <br/>{{ Form::text('Custom4',Input::old('Custom4',''),array('maxlength'=>'50')) }}<p/>
                    @endif
                </div>
                @endif
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                @if ($settings['CfgRegValidateGrp'])
                <div class="centered">
                    {{ Form::label('eventgroupid', $strings['str_imherefor'] . ':') }}
                    <span class="requiredAsterisk">*</span><br/>
                    {{ Form::select('eventgroupid', $settings['eventGroupIDOptions'], Input::old('eventgroupid',''),array('style' => 'color: black;') ) }}<p/>
                </div>
                @endif
            </div>
            <div class="col-sm-6">
                @if ($settings['CfgRegDrvrLicShow'])
                <div class="centered">
                    {{ Form::label('LicenseNumber', $strings['str_LicenseNumber'] . ':') }}
                    @if($settings['CfgRegDrvrLicReq'])
                    <span class="requiredAsterisk">*</span><br/>{{ Form::text('LicenseNumber',Input::old('LicenseNumber',''),array('maxlength'=>'100','class'=>'required')) }}<p/>
                    @else
                    <br/>{{ Form::text('LicenseNumber',Input::old('LicenseNumber',''),array('maxlength'=>'100')) }}<p/>
                    @endif
                </div>
                @endif
            </div>

        </div>

        @if ($settings['CfgRegPhoneShow'])
        <div class="centered">
            {{ Form::label('mobilephone', $strings['str_mobilephone'] . ':') }}
            @if($settings['CfgRegPhoneReq'])
            <span class="requiredAsterisk">*</span><br/>{{ Form::text('mobilephone',Input::old('mobilephone',''),array('maxlength'=>'50','class'=>'required')) }}<p/>
            @else
            <br/>{{ Form::text('mobilephone',Input::old('mobilephone',''), array('maxlength'=>'50')) }}<p/>
            @endif
            @if( isset($settings['showTextingWaiver']) && $settings['showTextingWaiver'] && $settings['textingWaiver'] && $settings['textingWaiver'] != '' )
                {{$settings['textingWaiver']}}
            @elseif( Config::has('config.showTextingWaiver') && Config::get('config.showTextingWaiver') && Config::has('config.textingWaiver') )
                {{Config::get('config.textingWaiver')}}
            @endif
        </div>
        @endif



        @if ($settings['CfgRegEmailShow'])
            <div class="centered">
            {{ Form::label('email', $strings['str_email'] . ':') }} <!-- TODO: Clean this up -->
                @if($settings['CfgRegEmailReq'] && $settings['CfgRegDisblEmlForMinr'] == false)
                <span class="requiredAsterisk" id="emailasterisk">*</span><br/>{{ Form::text('email',Input::old('email',''),array('maxlength'=>'255','class'=>'required')) }}<p/>
                @elseif($settings['CfgRegEmailReq'] && $settings['CfgRegDisblEmlForMinr'])
                <span class="requiredAsterisk" id="emailasterisk">*</span><br/>{{ Form::text('email',Input::old('email',''),array('maxlength'=>'255','class'=>'required','disabled'=>'disabled')) }}<p/>
                @elseif($settings['CfgRegDisblEmlForMinr'])
                <br/>{{ Form::text('email',Input::old('email',''),array('maxlength'=>'255','disabled'=>'disabled')) }}<p/>
                @else
                <br/>{{ Form::text('email',Input::old('email',''),array('maxlength'=>'255')) }}<p/>
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

            {{str_replace('##TRACKNAME##',$settings['BusinessName'], isset($settings['emailText']) ? $settings['emailText'] : $strings['str_emailText'])}}<p/>

            {{ Form::checkbox('consenttoemail', 'true',false) }} {{ Form::label('consenttoemail', $strings['str_emailsOptIn']) }}
            </div>
        @endif

        <input type="hidden" name="isMinor" id="isMinor" value="false">
        <input type="hidden" name="minorAge" id="minorAge" value={{$settings['AgeNeedParentWaiver']}}>
        <input type="hidden" name="ageAllowedToRegister" id="ageAllowedToRegister" value="{{$settings['AgeAllowOnlineReg']}}">
        <input type="hidden" name="tooYoungToRegister" id="tooYoungToRegister" value="0">

        <input type="hidden" id="facebookId" name="facebookId" value="">
        <input type="hidden" id="facebookToken" name="facebookToken" value="">
        <input type="hidden" id="facebookAllowEmail" name="facebookAllowEmail" value="1"> <!-- Deprecated -->
        <input type="hidden" id="facebookAllowPost" name="facebookAllowPost" value="1"> <!-- Deprecated -->
        <input type="hidden" id="facebookEnabled" name="facebookEnabled" value="1"> <!-- Deprecated -->

        <input type="hidden" id="screenSize" name="screenSize" value="large">


    </div>
</div>

@stop
<!-- END PAGE CONTENT -->

<!-- FOOTER -->

@section('leftFooterButton')
{{ Form::reset($strings['str_step2Clear'], array('class'=>'leftButton btn btn-danger btn-lg', 'id'=>'resetButton')) }}
@stop

@section('rightFooterButton')
{{ Form::submit($strings['str_step2Submit'], array('class'=>'rightButton btn btn-success btn-lg', 'id'=>'submitButton')) }}
{{ Form::close() }}
@stop

<!-- END FOOTER -->

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
    @parent

    <!-- CAMERA CAPTURE SCRIPT AND MINOR EMAIL CONTROLS -->
    <script>
    $(document).ready(function() {


        var currentScreenWidth = Math.max(document.documentElement.clientWidth, window.innerWidth || 0);
        if (currentScreenWidth < 768)
        {
            $("#screenSize").val('small');
        }

        $('#submitButton').click(function() {setTimeout(function() {$('#submitButton').prop('disabled', true);},1)});
        $('input').focus(function() {$('#submitButton').prop('disabled', false);});

        $("#cameraInput").on("change",pictureCaptured);
        $("#resetButton").on("click",resetPictures);
        $("#switchToFacebookPicButton").on("click",switchToFacebookPic);
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
        var ageAllowedToRegister = $('#ageAllowedToRegister').val();
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
        if (age < ageAllowedToRegister)
        {
            $('#submitButton').addClass('disabled');
            $('#submitButton').val("{{$strings['str_step2SubmitCannot']}}");
            $('#tooYoungToRegister').val("1");
        }
        else
        {
            if ($('#submitButton').hasClass('disabled'))
            {
                $('#submitButton').removeClass('disabled');
                $('#submitButton').val("{{$strings['str_step2Submit']}}");
                $('#tooYoungToRegister').val("0");
            }
        }
    }

    function checkAge()
    {
        var emailRequired = $('#emailRequired').val();
        var disableEmailIfMinor = $('#disableEmailIfMinor').val();
        var minorAge = $('#minorAge').val();
        var ageAllowedToRegister = $('#ageAllowedToRegister').val();


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
                    { failureMessage: "{{$strings['str_required']}}" } )
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
                    { failureMessage: "{{$strings['str_required']}}" } )
                    .remove( Validate.Email );
            }
        }
        if (age < ageAllowedToRegister)
        {
            $('#submitButton').addClass('disabled');
            $('#submitButton').val("{{$strings['str_step2SubmitCannot']}}");
            $('#tooYoungToRegister').val("1");
        }
        else
        {
            if ($('#submitButton').hasClass('disabled'))
            {
                $('#submitButton').removeClass('disabled');
                $('#submitButton').val("{{$strings['str_step2Submit']}}");
                $('#tooYoungToRegister').val("0");
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

    <!-- IP CAMERA SCRIPT -->
    <script>
        //setInterval('refreshCameraImage()',200);
        var count = 1;
        function refreshCameraImage()
        {
            document.cameraInputIPCam.src = 'http://{{Session::get('ipCamURL')}}?count=' + count;
            count = count + 1;
        }

        function updateIPCamSnapshot()
        {
            var ipCamURL = {{Session::get('ipCamURL') == null ? '""' : '"' . Session::get('ipCamURL') . '"'}};

            //Test version: $.getJSON("http://192.168.111.122/api/shot/shot.php?base64=" + ipCamURL + "&callback=?",
            $.getJSON("http://{{$_SERVER['HTTP_HOST']}}/api/shot/shot.php?base64=" + ipCamURL + "&callback=?",
                function( data ) {
                    $('#cameraInputIPCam_currentSnapshotBase64').val(data.image);
                    document.cameraInputIPCam_currentSnapshot.src = data.image;//'http://{{Session::get('ipCamURL')}}?count=' + count;
                    document.getElementById('cameraInputIPCam_currentSnapshotURL').value = 'data:'; //TODO: Change
                    $('#cameraInputIPCam_currentSnapshot').show();
                    if  (!($('#facebookProfileURL').attr("value") == "#" || $('#facebookProfileURL').attr("value") == ""))
                    {
                        $('#switchToFacebookIPCamButton').show();
                    }
                    count = count + 1;
                });

        }

        function switchToFacebookIPCam(event) {
            $('#switchToFacebookIPCamButton').hide();
            var profilePicURL = $('#facebookProfileURL').val();
            $('#cameraInputIPCam_currentSnapshot').attr("src",profilePicURL);
            $('#cameraInputIPCam_currentSnapshotURL').attr("value",profilePicURL);
            $('#cameraInputIPCam_currentSnapshotBase64').value = "";
        }

    </script>


    <!-- END IP CAMERA SCRIPT -->

    <!-- LOCAL CAMERA SCRIPT -->
    <script>

        @if (Session::has('localcam'))
            $(document).ready(function(){
                init();
            });

            function init()
            {
                if(navigator.webkitGetUserMedia)
                {
                    console.log('navigator.webkitGetUserMedia detected');
                    navigator.webkitGetUserMedia({video:true}, onSuccess, onFail);
                    //console.log(document.getElementById('cameraInputLocalCam'));
                }
                else
                {
                    console.log('webRTC not available');
                }
            }

            function onSuccess(stream)
            {
                console.log("Call succeeded.");
                document.getElementById('cameraInputLocalCam').src = URL.createObjectURL(stream);
                console.log(URL.createObjectURL(stream));
            }

            function onFail()
            {
                console.log('could not connect stream');
            }

            function updateLocalCamSnapshot()
            {
                var c = document.getElementById('cameraInputLocalCam_currentSnapshot');
                var v = document.getElementById('cameraInputLocalCam');
                c.getContext('2d').drawImage(v, 0, 0, 240, 180);
                $('#cameraInputLocalCam_currentSnapshotURL').val(c.toDataURL("image/jpeg", 1.0));
                $('#cameraInputLocalCam_currentSnapshot').show();
                if  (!($('#facebookProfileURL').attr("value") == "#" || $('#facebookProfileURL').attr("value") == ""))
                {
                    $('#switchToFacebookLocalCamButton').show();
                }
            }

        function switchToFacebookLocalCam(event) {
            $('#switchToFacebookLocalCamButton').hide();
            var profilePicURL = $('#facebookProfileURL').val();
            $('#cameraInputLocalCam_currentSnapshot').attr("src",profilePicURL);
            var c = document.getElementById('cameraInputLocalCam_currentSnapshot');
            var ctx = c.getContext('2d');
            ctx.clearRect(0, 0, 240, 180);
            var img = new Image;
            img.setAttribute('crossOrigin', 'anonymous');
            img.onload = function(){
                ctx.drawImage(img,20,0,180,180);
            };
            img.src = $('#facebookProfileURL').val();

            $('#cameraInputLocalCam_currentSnapshotURL').attr("value",profilePicURL);
            $('#cameraInputLocalCam_currentSnapshotBase64').value = "";
        }
        @endif

    </script>
    <!-- END LOCAL CAMERA SCRIPT -->

    <!-- Datepicker swapping -->
    <script>
        if (!Modernizr.inputtypes.date) {
            $('input[type=date]').datepicker({
                // Consistent format with the HTML5 picker
                dateFormat: 'yy-mm-dd'
            });
        }
    </script>

    <!-- LIVE VALIDATION SCRIPT -->
    <script>
        $(document).ready(function() {

            var email = new LiveValidation('email');
            var emailValidationAdded = 'none';
            var emailRequiredParams = { failureMessage: "{{$strings['str_required']}}" };
            var emailValidParams = { failureMessage: "{{$strings['str_mustBeAValidEmailAddress']}}" };
            var emailValidationWasRemoved = false;

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
                    { failureMessage: "{{$strings['str_required']}}" } );
            }


            if ($('#firstname').length > 0 && $('#firstname').hasClass('required'))
            {
                var firstname = new LiveValidation('firstname');
                firstname.add( Validate.Presence,
                    { failureMessage: "{{$strings['str_required']}}" } );
            }

            if ($('#lastname').length > 0 && $('#lastname').hasClass('required'))
            {
                var lastname = new LiveValidation('lastname');
                lastname.add( Validate.Presence,
                    { failureMessage: "{{$strings['str_required']}}" } );

            }

            if ($('#racername').length > 0 && $('#racername').hasClass('required'))
            {
                var racername = new LiveValidation('racername');
                racername.add( Validate.Presence,
                    { failureMessage: "{{$strings['str_required']}}" } );
            }


            if ($('#email').length > 0 && $('#email').hasClass('required'))
            {
                email.add( Validate.Presence,
                        emailRequiredParams )
                    .add( Validate.Email, emailValidParams  );
                emailValidationAdded = 'both';
            }
            else if ($('#email').length > 0 )
            {
                email.add( Validate.Email, emailValidParams );
                emailValidationAdded = 'validity';
            }

            if ($('#Address').length > 0 && $('#Address').hasClass('required'))
            {
                var Address = new LiveValidation('Address');
                Address.add( Validate.Presence,
                    { failureMessage: "{{$strings['str_required']}}" } );
            }

            if ($('#state').length > 0 && $('#state').hasClass('required'))
            {
                var state = new LiveValidation('state');
                state.add( Validate.Presence,
                    { failureMessage: "{{$strings['str_required']}}" } );
            }

            if ($('#Zip').length > 0 && $('#Zip').hasClass('required'))
            {
                var Zip = new LiveValidation('Zip');
                if ($('#Zip').hasClass('validated'))
                {
                    Zip.add( Validate.Format,
                            {
                                pattern: /(^\d{5}$)|(^\d{5}-\d{4}$)/,
                                failureMessage: "{{$strings['str_invalidZipCode']}}"
                            }
                    );
                }
                else
                {
                    Zip.add( Validate.Presence,
                            { failureMessage: "{{$strings['str_required']}}" } );
                }
            }

            if ($('#city').length > 0 && $('#city').hasClass('required'))
            {
                var city = new LiveValidation('city');
                city.add( Validate.Presence,
                    { failureMessage: "{{$strings['str_required']}}" } );
            }

            if ($('#Custom1').length > 0 && $('#Custom1').hasClass('required'))
            {
                var Custom1 = new LiveValidation('Custom1');
                Custom1.add( Validate.Presence,
                    { failureMessage: "{{$strings['str_required']}}" } );
            }
            if ($('#Custom2').length > 0 && $('#Custom2').hasClass('required'))
            {
                var Custom2 = new LiveValidation('Custom2');
                Custom2.add( Validate.Presence,
                    { failureMessage: "{{$strings['str_required']}}" } );
            }
            if ($('#Custom3').length > 0 && $('#Custom3').hasClass('required'))
            {
                var Custom3 = new LiveValidation('Custom3');
                Custom3.add( Validate.Presence,
                    { failureMessage: "{{$strings['str_required']}}" } );
            }
            if ($('#Custom4').length > 0 && $('#Custom4').hasClass('required'))
            {
                var Custom4 = new LiveValidation('Custom4');
                Custom4.add( Validate.Presence,
                    { failureMessage: "{{$strings['str_required']}}" } );
            }
            if ($('#LicenseNumber').length > 0 && $('#LicenseNumber').hasClass('required'))
            {
                var LicenseNumber = new LiveValidation('LicenseNumber');
                LicenseNumber.add( Validate.Presence,
                    { failureMessage: "{{$strings['str_required']}}" } );
            }
            <!-- END LIVE VALIDATION SCRIPT -->

            <!-- INITIAL STATE LABEL LOADING -->

            var selectedCountry = $('#country').val();

            if (selectedCountry == 'United States')
            {
                $('#stateLabel').text('{{$strings['str_State']}}');
            }
            else if (selectedCountry == 'Canada')
            {
                $('#stateLabel').text('{{$strings['str_Province/Territory']}}');
            }
            else if (typeof selectedCountry !== 'undefined')
            {
                $('#stateLabel').text('{{$strings['str_State/Territory']}}');
            }

            <!-- UNITED STATES AND CANADA DROPDOWN SCRIPT -->
            $('#country').change(function()
            {
                var selectedCountry = $('#country').val();
                if (selectedCountry == 'United States')
                {
                    $('#stateLabel').text('{{$strings['str_State']}}');
                }
                else if (selectedCountry == 'Canada')
                {
                    $('#stateLabel').text('{{$strings['str_Province/Territory']}}');
                    if (emailValidationAdded == 'both')
                    {
                        email.remove( Validate.Presence,
                                emailRequiredParams )
                            .remove( Validate.Email, emailValidParams  );
                        emailValidationWasRemoved = true;
                        $('#emailasterisk').hide();
                        $('#email').focus();
                    }
                    else if (emailValidationAdded == 'validity')
                    {
                        email.remove( Validate.Email, emailValidParams );
                        emailValidationWasRemoved = true;
                        $('#emailasterisk').hide();
                        $('#email').focus();
                    }
                }
                else
                {
                    $('#stateLabel').text('{{$strings['str_State/Territory']}}');
                }

                if (selectedCountry != 'Canada' && emailValidationWasRemoved)
                {
                    if (emailValidationAdded == 'both')
                    {
                        email.add( Validate.Presence,
                                emailRequiredParams )
                            .add( Validate.Email, emailValidParams  );
                        emailValidationWasRemoved = false;
                        $('#emailasterisk').show();
                        $('#email').focus();
                    }
                    else if (emailValidationAdded == 'validity')
                    {
                        email.add( Validate.Email, emailValidParams );
                        emailValidationWasRemoved = false;
                        $('#emailasterisk').show();
                        $('#email').focus();
                    }
                }
            });
            <!-- END UNITED STATES AND CANADA DROPDOWN SCRIPT -->
        });

    </script>


@stop
<!-- END JAVASCRIPT INCLUDES -->
