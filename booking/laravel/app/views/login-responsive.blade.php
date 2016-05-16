@extends('master-responsive')

<!-- PAGE TITLE -->
@section('title')
{{$strings['str_loginTitle']}}
@stop
<!-- END PAGE TITLE -->

<!-- PAGE CONTENT -->

@section('steps')
<div class="steps">
    {{link_to('step1',$strings['str_seeTheLineup'])}} >
    @if(Session::has('lastSearch'))
        {{link_to('step2',$strings['str_chooseARace'])}} >
    @else
        {{$strings['str_chooseARace']}} >
    @endif
    @if(Session::has('authenticated'))
    {{link_to('cart',$strings['str_reviewYourOrder'])}}
    @else
    {{$strings['str_reviewYourOrder']}}
    @endif
    @if(Session::has('authenticated') && Session::has('cart') && count(Session::get('cart')) > 0)
    > {{link_to('checkout',$strings['str_checkout'])}}
    @else
    > {{$strings['str_checkout']}}
    @endif
</div>
@stop

@section('content')
<div class="mainBodyContent">
    <div class="mainBodyHeader">
        {{$strings['str_login']}}
    </div>

<div class="loginOptions centered" id="loginOptions">

            <em>{{$strings['str_youMustBeLoggedIn']}}</em><br/>
            <button type="button" class="regularButton regularButton-responsive" data-toggle="collapse" data-target="#createAccount" onclick="$('#loginToAccount').collapse('hide')">{{$strings['str_createANewAccount']}}</button>

                @if($settings['enableFacebook'])
                    @if(isset($intent['heatId']))
                <a href="https://www.facebook.com/dialog/oauth?client_id=296582647086963&redirect_uri={{str_replace('login','loginfb',Request::url())}}&scope=public_profile,email,user_birthday,publish_actions&state={{$intent['heatId']}}!{{$intent['quantity']}}">
                    <button type="button" class="regularButton regularButton-responsive">{{$strings['str_loginWithFacebook']}}</button>
                    @elseif(isset($intent['productid']))
                <a href="https://www.facebook.com/dialog/oauth?client_id=296582647086963&redirect_uri={{str_replace('login','loginfb',Request::url())}}&scope=public_profile,email,user_birthday,publish_actions&state={{$intent['productId']}}|{{$intent['quantity']}}">
                    <button type="button" class="regularButton regularButton-responsive">{{$strings['str_loginWithFacebook']}}</button>
                    @else
                <a href="https://www.facebook.com/dialog/oauth?client_id=296582647086963&redirect_uri={{str_replace('login','loginfb',Request::url())}}&scope=public_profile,email,user_birthday,publish_actions&state={{$intent['productId']}}|{{$intent['quantity']}}">
                    <button type="button" class="regularButton regularButton-responsive">{{$strings['str_loginWithFacebook']}}</button>
                    @endif

                </a>
                @endif

            <button type="button" class="regularButton regularButton-responsive" data-toggle="collapse" data-target="#loginToAccount" onclick="$('#createAccount').collapse('hide')">{{$strings['str_loginToExistingAccount']}}</button>

            <!-- ACCOUNT CREATION FORM -->
            <div class="createAccount collapse out" data-toggle="false" id="createAccount">

                <!-- Validation for create account form -->
                <script>
                    $(document).ready(function() {
                        var currentForm = $('#accountCreationForm');
                        currentForm.validate({
                             submitHandler: function(form) {
                                 $(".formButton").prop('disabled', true);
                                 $('#loadingModal').modal();
                                 form.submit();
                             },
                             errorPlacement: function(error,element) {
                                 error.addClass("formError");
                                 error.insertAfter(element);
                             },
                             errorElement: 'div',
                             rules: {
                                 EmailAddressConfirmation: {
                                     equalTo: '#EmailAddress'
                                 },
                                  PasswordConfirmation: {
                                      equalTo: '#Password'
                                  }
                             },
                             messages:
                             {
                                 EmailAddressConfirmation: {
                                     equalTo: '{{$strings['str_emailsMustMatch']}}'
                                 },
                                   PasswordConfirmation: {
                                       equalTo: '{{$strings['str_passwordsMustMatch']}}'
                                   }
                             }
                         });
                    });
                </script>
                <form action="createaccount" class="accountCreationForm" id="accountCreationForm" method="POST">

                    @if(Session::has('debug'))
                    <a href="#" id="testdata">Populate with test data</a><p/>
                    <script>
                        $(document).ready(function() {
                            $('#testdata').click(function () {
                                $('#EmailAddress').val('testaccount' + Math.floor((Math.random() * 1000) + 1) + '@test.com');
                                $('#EmailAddressConfirmation').val($('#EmailAddress').val());
                                $('#Password').val('test');
                                $('#PasswordConfirmation').val('test');
                                $('#Company').val('Test Company');
                                $('#FName').val('Test');
                                $('#LName').val('Test');
                                $('#Address').val('Test');
                                $('#Address2').val('Test');
                                $('#RacerName').val('Test');
                                $('#City').val('Test');
                                $('#State').val('Test');
                                $('#Zip').val('Test');
                                $('#Country').val('Test');
                                $('#Cell').val('Test');
                                $('#LicenseNumber').val('Test');
                                $('#Custom1').val('Test');
                            });
                        });
                    </script>
                    @endif


                    <div class="formHeader">{{$strings['str_accountInformation']}}</div>

                    <!-- E-mail and e-mail confirmation -->
                    @if($settings['emailShown'])
                        <label for="EmailAddress">
                            <strong>
                                {{$strings['str_emailAddress']}}:
                                @if($settings['emailRequired']) <span class="requiredAsterisk">*</span> @endif
                            </strong>
                        </label>
                        @if($settings['emailRequired'])
                            <input maxlength="255" type="text" id="EmailAddress" name="EmailAddress" class="required mustBeValidEmail"><br/>
                        @else
                            <input maxlength="255" type="text" id="EmailAddress" name="EmailAddress" class="mustBeValidEmail"><br/>
                        @endif
                        <label for="EmailAddressConfirmation">
                            <strong>
                                {{$strings['str_confirmEmail']}}:
                                @if($settings['emailRequired']) <span class="requiredAsterisk">*</span> @endif
                            </strong>
                        </label>
                        @if($settings['emailRequired'])
                            <input maxlength="255" type="text" id="EmailAddressConfirmation" name="EmailAddressConfirmation" class="required mustBeValidEmail"><br/>
                        @else
                            <input maxlength="255" type="text" id="EmailAddressConfirmation" name="EmailAddressConfirmation" class="mustBeValidEmail"><br/>
                        @endif
                    @endif

                    <!-- Consent to e-mail marketing -->
                    @if($settings['emailShown'] && $settings['consentToMailShown'])
                        <span class="emailConsent">
                            <input type="checkbox" name="ConsentToMail" value="true">
                            <strong>{{$strings['str_iWantToReceiveSpecialOffers']}}</strong>
                        </span>
                    @endif

                    <!-- Password and password confirmation -->
                    @if($settings['passwordShown'])
                        <label for="Password">
                                <strong>
                                    {{$strings['str_password']}}:
                                    @if($settings['passwordRequired']) <span class="requiredAsterisk">*</span> @endif
                                </strong>
                        </label>
                        @if($settings['passwordRequired'])
                            <input maxlength="255" type="password" id="Password" name="Password" class="required validatePresence"><br/>
                        @else
                            <input maxlength="255" type="password" id="Password" name="Password"><br/>
                        @endif
                        <label for="PasswordConfirmation">
                            <strong>
                                {{$strings['str_confirmPassword']}}:
                                @if($settings['passwordRequired']) <span class="requiredAsterisk">*</span> @endif
                            </strong>
                        </label>
                        @if($settings['passwordRequired'])
                            <input maxlength="255" type="password" id="PasswordConfirmation" name="PasswordConfirmation" class="required"><br/>
                        @else
                            <input maxlength="255" type="password" id="PasswordConfirmation" name="PasswordConfirmation"><br/>
                        @endif
                    @endif

                    <div class="formHeader">{{$strings['str_personalInformation']}}</div>

                    <!-- Company -->
                    @if($settings['companyShown'])
                        <label for="Company"><strong>{{$strings['str_company']}}: @if($settings['companyRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['companyRequired'])
                            <input maxlength="50" type="text" id="Company" name="Company" class="required validatePresence"><br/>
                        @else
                            <input maxlength="50" type="text" id="Company" name="Company"><br/>
                        @endif
                    @endif

                    <!-- First Name -->
                    @if($settings['firstNameShown'])
                        <label for="FName"><strong>{{$strings['str_firstName']}}: @if($settings['firstNameRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['firstNameRequired'])
                            <input maxlength="50" type="text" id="FName" name="FName" class="required validatePresence"><br/>
                        @else
                            <input maxlength="50" type="text" id="FName" name="FName"><br/>
                        @endif
                    @endif

                    <!-- Last Name -->
                    @if($settings['lastNameShown'])
                        <label for="LName"><strong>{{$strings['str_lastName']}}: @if($settings['lastNameRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['lastNameRequired'])
                            <input maxlength="50" type="text" id="LName" name="LName" class="required validatePresence"><br/>
                        @else
                            <input maxlength="50" type="text" id="LName" name="LName"><br/>
                        @endif
                    @endif

                    <!-- Racer Name -->
                    @if($settings['racerNameShown'])
                    <label for="RacerName"><strong>{{$strings['str_racerName']}}: @if($settings['racerNameRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['racerNameRequired'])
                            <input maxlength="100" type="text" id="RacerName" name="RacerName" class="required validatePresence"><br/>
                        @else
                            <input maxlength="100" type="text" id="RacerName" name="RacerName"><br/>
                        @endif
                    @endif

                    <!-- Birth Date -->
                    @if($settings['birthDateShown'])

                        <label for="BirthDate"><strong>{{$strings['str_birthDate']}}: @if($settings['birthDateRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        <span class="inputMarginAdjustment"><input class="inputLineHeightAdjustment" type="date" name="BirthDate" id="BirthDate"><br/></span>
                    @endif

                    <!-- Gender -->
                    @if($settings['genderShown'])
                        <label for=""><strong>{{$strings['str_gender']}}: @if($settings['genderRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                            <span class="inputGenderMarginAdjustment">{{$strings['str_male']}} <input type="radio" name="Gender" id="Gender_male" value="male" checked="checked">
                            {{$strings['str_female']}} <input type="radio" name="Gender" id="Gender_male" value="female">
                            {{$strings['str_other']}} <input type="radio" name="Gender" id="Gender_male" value="other"></span>
                    @endif

                    <!-- SourceID -->
                    @if($settings['whereDidYouHearAboutUsShown'])
                        <label for="SourceID"><strong>{{$strings['str_whereDidYouHearAboutUs']}} @if($settings['whereDidYouHearAboutUsRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['whereDidYouHearAboutUsRequired'])
                            {{ Form::select("SourceID", $settings['dropdownOptions'], Input::old("SourceID",'0'),array('style' => 'color: black', 'class'=>'required') ) }}<br/>
                        @else
                            {{ Form::select("SourceID", $settings['dropdownOptions'], Input::old("SourceID",'0'),array('style' => 'color: black') ) }}<br/>
                        @endif
                    @endif

                    <!-- Address and Address2 -->
                    @if($settings['addressShown'])
                        <label for="Address"><strong>{{$strings['str_addressLine1']}}: @if($settings['addressRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['addressRequired'])
                            <input maxlength="80" type="text" id="Address" name="Address" class="required validatePresence"><br/>
                        @else
                            <input maxlength="80" type="text" id="Address" name="Address"><br/>
                        @endif
                        <label for="Address2"><strong>{{$strings['str_addressLine2']}}:</strong></label> <input maxlength="255" type="text" id="Address2" name="Address2"><br/>
                    @endif

                    <!-- City -->
                    @if($settings['cityShown'])
                        <label for="City"><strong>{{$strings['str_city']}}: @if($settings['cityRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['cityRequired'])
                            <input maxlength="80" type="text" id="City" name="City" class="required validatePresence"><br/>
                        @else
                            <input maxlength="80" type="text" id="City" name="City"><br/>
                        @endif
                    @endif

                    <!-- State -->
                    @if($settings['stateShown'])
                        <label for="State"><strong>{{$strings['str_state']}}: @if($settings['stateRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['stateRequired'])
                            <input maxlength="50" type="text" id="State" name="State" class="required validatePresence"><br/>
                        @else
                            <input maxlength="50" type="text" id="State" name="State"><br/>
                        @endif
                    @endif

                    <!-- Zip -->
                    @if($settings['zipShown'])
                        <label for="Zip"><strong>{{$strings['str_postalCode']}}: @if($settings['zipRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['zipRequired'])
                            <input maxlength="15" type="text" id="Zip" name="Zip" class="required validatePresence"><br/>
                        @else
                            <input maxlength="15" type="text" id="Zip" name="Zip"><br/>
                        @endif
                    @endif

                    <!-- Country -->
                    @if($settings['countryShown'])
                        <label for="Country"><strong>{{$strings['str_country']}}: @if($settings['countryRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['countryRequired'])
                            <input maxlength="50" type="text" id="Country" name="Country" class="required validatePresence"><br/>
                        @else
                            <input maxlength="50" type="text" id="Country" name="Country"><br/>
                        @endif
                    @endif

                    <!-- Cell -->
                    @if($settings['cellShown'])
                        <label for="Cell"><strong>{{$strings['str_cell']}}: @if($settings['cellRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['cellRequired'])
                            <input maxlength="50" type="text" id="Cell" name="Cell" class="required validatePresence"><br/>
                        @else
                            <input maxlength="50" type="text" id="Cell" name="Cell"><br/>
                        @endif
                    @endif

                    <!-- License Number -->
                    @if($settings['licenseNumberShown'])
                        <label for="LicenseNumber"><strong>{{$strings['str_licenseNumber']}}: @if($settings['licenseNumberRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['licenseNumberRequired'])
                            <input maxlength="100" type="text" id="LicenseNumber" name="LicenseNumber" class="required validatePresence"><br/>
                        @else
                            <input maxlength="100" type="text" id="LicenseNumber" name="LicenseNumber"><br/>
                        @endif
                    @endif

                    <!-- Custom1 -->
                    @if($settings['custom1Shown'])
                        <label for="Custom1"><strong>{{$settings['CustomText1']}}: @if($settings['custom1Required'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['custom1Required'])
                            <input maxlength="50" type="text" id="Custom1" name="Custom1" class="required validatePresence"><br/>
                        @else
                            <input maxlength="50" type="text" id="Custom1" name="Custom1"><br/>
                        @endif
                    @endif

                    <!-- Custom2 -->
                    @if($settings['custom2Shown'])
                        <label for="Custom2"><strong>{{$settings['CustomText2']}}: @if($settings['custom2Required'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['custom2Required'])
                            <input maxlength="50" type="text" id="Custom2" name="Custom2" class="required validatePresence"><br/>
                        @else
                            <input maxlength="50" type="text" id="Custom2" name="Custom2"><br/>
                        @endif
                    @endif

                    <!-- Custom3 -->
                    @if($settings['custom3Shown'])
                        <label for="Custom3"><strong>{{$settings['CustomText3']}}: @if($settings['custom3Required'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['custom3Required'])
                            <input maxlength="50" type="text" id="Custom3" name="Custom3" class="required validatePresence"><br/>
                        @else
                            <input maxlength="50" type="text" id="Custom3" name="Custom3"><br/>
                        @endif
                    @endif

                    <!-- Custom4 -->
                    @if($settings['custom4Shown'])
                        <label for="Custom4"><strong>{{$settings['CustomText4']}}: @if($settings['custom4Required'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['custom4Required'])
                            <input maxlength="50" type="text" id="Custom4" name="Custom4" class="required validatePresence"><br/>
                        @else
                            <input maxlength="50" type="text" id="Custom4" name="Custom4"><br/>
                        @endif
                    @endif

                    <input type="hidden" name="heatId" value="{{$intent['heatId']}}">
                    <input type="hidden" name="productId" value="{{$intent['productId']}}">
                    <input type="hidden" name="numberOfParticipants" value="{{$intent['quantity']}}">
                    <input type="hidden" name="pageSource" value="login">
                    <div class="rightAligned">
                        <button type="submit" class="formButton">{{$strings['str_createAccount']}}</button>
                    </div>
                </form>
            </div>
            <!-- END ACCOUNT CREATION FORM -->

            <!-- LOGIN FORM -->
            <div class="loginToAccount collapse out" data-toggle="false" id="loginToAccount">

                <form action="login" class="loginForm" method="POST">
                    <div class="formHeader">{{$strings['str_loginToYourExistingAccount']}}</div>
                    <label for="loginEmail"><strong>{{$strings['str_emailAddress']}}: <span class="requiredAsterisk">*</span></strong></label> <input type="text" name="EmailAddress" id="loginEmail" class="required mustBeValidEmail"><br/>
                    <label for="loginPassword"><strong>{{$strings['str_password']}}: <span class="requiredAsterisk">*</span></strong></label> <input type="password" name="Password" id="loginPassword" class="required"><br/>
                    <input type="hidden" name="heatId" value="{{$intent['heatId']}}">
                    <input type="hidden" name="productId" value="{{$intent['productId']}}">
                    <input type="hidden" name="numberOfParticipants" value="{{$intent['quantity']}}">
                    <input type="hidden" name="pageSource" value="login">
                    <div class="rightAligned">
                        {{link_to('resetpassword',$strings['str_resetPassword'])}} <button type="submit" class="formButton">{{$strings['str_login']}}</button>
                    </div>
                </form>
            </div>
            <!-- END LOGIN FORM -->
        </div>

</div>
@stop
<!-- END PAGE CONTENT -->

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent

@stop
<!-- END JAVASCRIPT INCLUDES -->

