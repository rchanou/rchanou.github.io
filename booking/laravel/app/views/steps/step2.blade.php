@extends('master')

<!-- PAGE TITLE -->
@section('title')
    {{$strings['str_step2Title']}}
@stop
<!-- END PAGE TITLE -->

<!-- PAGE CONTENT -->
@section('steps')
<div class="steps">
    {{link_to('step1',$strings['str_seeTheLineup'])}} > <em>{{$strings['str_chooseARace']}}</em> >
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

    <div class="mainBodyHeader row">
        <div class="col-xs-3 yesterdayArrow text-left">
            <a href="?start={{$previousDay}}&heatType={{$heatType}}&numberOfParticipants={{$numberOfParticipants}}"><-- {{$previousDayDisplay}}</a>
        </div>
        <div class="col-xs-6">
            <em>{{$strings['str_availableRaces']}}</em>
        </div>
        <div class="col-xs-3 tomorrowArrow text-right">
            <a href="?start={{$nextDay}}&heatType={{$heatType}}&numberOfParticipants={{$numberOfParticipants}}">{{$nextDayDisplay}} --></a>
        </div>
{{--        <span class="yesterdayArrow"><a href="?start={{$previousDay}}&heatType={{$heatType}}&numberOfParticipants={{$numberOfParticipants}}"><-- {{$previousDayDisplay}}</a></span>
        <span><em>Available Races</em></span>
        <span class="tomorrowArrow"><a href="?start={{$nextDay}}&heatType={{$heatType}}&numberOfParticipants={{$numberOfParticipants}}">{{$nextDayDisplay}} --></a></span>--}}
    </div>

    <!-- Race results -->
    @if($races !== null)
        @if(count($races) == 0)
        <div class="noRaceResults centered">
            {{$strings['str_noRacesFound']}}
        </div>
        @endif
        @foreach($races as $race)
        <div class="raceResult" id="{{$race->heatId}}">
            <div class="raceResultHeader row">
                <div class="raceName ellipsis col-xs-6">{{$race->heatDescription}}</div>
                <div class="raceDate col-xs-6 text-right">{{date($settings['dateDisplayFormat'] . ' ' . $settings['timeDisplayFormat'],strtotime($race->heatStartsAt))}}</div>
            </div>

            <div class="spotsAvailable">{{$race->heatSpotsAvailableOnline}} {{$strings['str_spotsAvailableOnline']}}</div>

            <div class="raceResultFooter row">
                <div class="racePrices ellipsis col-xs-9">{{$numberOfParticipants}} {{$strings['str_drivers']}}

                     x {{$moneyFormatter->formatCurrency($race->products[0]->price1, $currency)}} {{$strings['str_each']}} = {{$moneyFormatter->formatCurrency($numberOfParticipants * $race->products[0]->price1, $currency)}}
                </div>
                <div class="raceBookButtonArea col-xs-3 text-right">
                    @if($authenticated != null)
                        <a href="cart?action=add&heatId={{$race->heatId}}&quantity={{$numberOfParticipants}}"><button type="button" class="formButton">{{$strings['str_bookIt']}}</button></a>
                    @else
                        <button type="button" class="formButton" data-toggle="collapse" data-target="#loginOptions_{{$race->heatId}}">{{$strings['str_bookIt']}}</button>
                    @endif
                </div>
            </div>
        </div>
        <div class="loginOptions collapse out centered" id="loginOptions_{{$race->heatId}}">
            <em>{{$strings['str_youMustBeLoggedIn']}}</em><br/>
            <button type="button" class="regularButton" data-toggle="collapse" data-target="#createAccount_{{$race->heatId}}" onclick="$('#loginToAccount_{{$race->heatId}}').collapse('hide')">{{$strings['str_createANewAccount']}}</button>

                @if($settings['enableFacebook'])
                <a href="https://www.facebook.com/dialog/oauth?client_id=296582647086963&redirect_uri={{str_replace('step2','loginfb',Request::url())}}&scope=public_profile,email,user_birthday,publish_actions&state={{$race->heatId}}!{{$numberOfParticipants}}">
                    <button type="button" class="regularButton">{{$strings['str_loginWithFacebook']}}</button>
                </a>
                @endif

            <button type="button" class="regularButton" data-toggle="collapse" data-target="#loginToAccount_{{$race->heatId}}" onclick="$('#createAccount_{{$race->heatId}}').collapse('hide')">{{$strings['str_loginToExistingAccount']}}</button>

            <!-- ACCOUNT CREATION FORM -->
            <div class="createAccount collapse out" data-toggle="false" id="createAccount_{{$race->heatId}}">

                @if (isset($createAccountErrors) && array_key_exists($race->heatId,$createAccountErrors) && count( $createAccountErrors[$race->heatId] ) > 0 )
                <div class="alert alert-danger alert-dismissable accountError" role="alert">
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">{{$strings['str_close']}}</span></button>
                    @foreach ($createAccountErrors[$race->heatId] as $message)
                    {{ $message }}<br/>
                    @endforeach
                </div>
                @endif

                <!-- Validation for create account form -->
                <script>
                    $(document).ready(function() {
                        var currentForm = $('#accountCreationForm_{{$race->heatId}}');
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
                                     equalTo: '#EmailAddress_{{$race->heatId}}'
                                 },
                                  PasswordConfirmation: {
                                      equalTo: '#Password_{{$race->heatId}}'
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
                <form action="createaccount" class="accountCreationForm" id="accountCreationForm_{{$race->heatId}}" method="POST">

                    <div class="formHeader">{{$strings['str_accountInformation']}}</div>

                    <!-- E-mail and e-mail confirmation -->
                    @if($settings['emailShown'])
                        <label for="EmailAddress_{{$race->heatId}}">
                            <strong>
                                {{$strings['str_emailAddress']}}:
                                @if($settings['emailRequired']) <span class="requiredAsterisk">*</span> @endif
                            </strong>
                        </label>
                        @if($settings['emailRequired'])
                            <input maxlength="255" type="text" id="EmailAddress_{{$race->heatId}}" name="EmailAddress" class="required mustBeValidEmail"><br/>
                        @else
                            <input maxlength="255" type="text" id="EmailAddress_{{$race->heatId}}" name="EmailAddress" class="mustBeValidEmail"><br/>
                        @endif
                        <label for="EmailAddressConfirmation_{{$race->heatId}}">
                            <strong>
                                {{$strings['str_confirmEmail']}}:
                                @if($settings['emailRequired']) <span class="requiredAsterisk">*</span> @endif
                            </strong>
                        </label>
                        @if($settings['emailRequired'])
                            <input maxlength="255" type="text" id="EmailAddressConfirmation_{{$race->heatId}}" name="EmailAddressConfirmation" class="required mustBeValidEmail"><br/>
                        @else
                            <input maxlength="255" type="text" id="EmailAddressConfirmation_{{$race->heatId}}" name="EmailAddressConfirmation" class="mustBeValidEmail"><br/>
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
                        <label for="Password_{{$race->heatId}}">
                                <strong>
                                    {{$strings['str_password']}}:
                                    @if($settings['passwordRequired']) <span class="requiredAsterisk">*</span> @endif
                                </strong>
                        </label>
                        @if($settings['passwordRequired'])
                            <input maxlength="255" type="password" id="Password_{{$race->heatId}}" name="Password" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="255" type="password" id="Password_{{$race->heatId}}" name="Password"><br/>
                        @endif
                        <label for="PasswordConfirmation_{{$race->heatId}}">
                            <strong>
                                {{$strings['str_confirmPassword']}}:
                                @if($settings['passwordRequired']) <span class="requiredAsterisk">*</span> @endif
                            </strong>
                        </label>
                        @if($settings['passwordRequired'])
                            <input maxlength="255" type="password" id="PasswordConfirmation_{{$race->heatId}}" name="PasswordConfirmation" class="required"><br/>
                        @else
                            <input maxlength="255" type="password" id="PasswordConfirmation_{{$race->heatId}}" name="PasswordConfirmation"><br/>
                        @endif
                    @endif

                    <div class="formHeader">{{$strings['str_personalInformation']}}</div>

                    <!-- Company -->
                    @if($settings['companyShown'])
                        <label for="Company_{{$race->heatId}}"><strong>{{$strings['str_company']}}: @if($settings['companyRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['companyRequired'])
                            <input maxlength="50" type="text" id="Company_{{$race->heatId}}" name="Company" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="50" type="text" id="Company_{{$race->heatId}}" name="Company"><br/>
                        @endif
                    @endif

                    <!-- First Name -->
                    @if($settings['firstNameShown'])
                        <label for="FName_{{$race->heatId}}"><strong>{{$strings['str_firstName']}}: @if($settings['firstNameRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['firstNameRequired'])
                            <input maxlength="50" type="text" id="FName_{{$race->heatId}}" name="FName" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="50" type="text" id="FName_{{$race->heatId}}" name="FName"><br/>
                        @endif
                    @endif

                    <!-- Last Name -->
                    @if($settings['lastNameShown'])
                        <label for="LName_{{$race->heatId}}"><strong>{{$strings['str_lastName']}}: @if($settings['lastNameRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['lastNameRequired'])
                            <input maxlength="50" type="text" id="LName_{{$race->heatId}}" name="LName" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="50" type="text" id="LName_{{$race->heatId}}" name="LName"><br/>
                        @endif
                    @endif

                    <!-- Racer Name -->
                    @if($settings['racerNameShown'])
                    <label for="RacerName_{{$race->heatId}}"><strong>{{$strings['str_racerName']}}: @if($settings['racerNameRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['racerNameRequired'])
                            <input maxlength="100" type="text" id="RacerName_{{$race->heatId}}" name="RacerName" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="100" type="text" id="RacerName_{{$race->heatId}}" name="RacerName"><br/>
                        @endif
                    @endif

                    <!-- Birth Date -->
                    @if($settings['birthDateShown'])

                        <label for="BirthDate_{{$race->heatId}}"><strong>{{$strings['str_birthDate']}}: @if($settings['birthDateRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        <span class="inputMarginAdjustment"><input class="inputLineHeightAdjustment" type="date" name="BirthDate" id="BirthDate_{{$race->heatId}}"><br/></span>
                    @endif

                    <!-- Gender -->
                    @if($settings['genderShown'])
                        <label for=""><strong>{{$strings['str_gender']}}: @if($settings['genderRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                            <span class="inputGenderMarginAdjustment">{{$strings['str_male']}} <input type="radio" name="Gender" id="Gender_{{$race->heatId}}_male" value="male" checked="checked">
                            {{$strings['str_female']}} <input type="radio" name="Gender" id="Gender_{{$race->heatId}}_male" value="female">
                            {{$strings['str_other']}} <input type="radio" name="Gender" id="Gender_{{$race->heatId}}_male" value="other"></span>
                    @endif

                    <!-- SourceID -->
                    @if($settings['whereDidYouHearAboutUsShown'])
                        <label for="SourceID_{{$race->heatId}}"><strong>{{$strings['str_whereDidYouHearAboutUs']}} @if($settings['whereDidYouHearAboutUsRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['whereDidYouHearAboutUsRequired'])
                            {{ Form::select("SourceID", $settings['dropdownOptions'], Input::old("SourceID",'0'),array('style' => 'color: black', 'class'=>'required') ) }}<br/>
                        @else
                            {{ Form::select("SourceID", $settings['dropdownOptions'], Input::old("SourceID",'0'),array('style' => 'color: black') ) }}<br/>
                        @endif
                    @endif

                    <!-- Address and Address2 -->
                    @if($settings['addressShown'])
                        <label for="Address_{{$race->heatId}}"><strong>{{$strings['str_addressLine1']}}: @if($settings['addressRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['addressRequired'])
                            <input maxlength="80" type="text" id="Address_{{$race->heatId}}" name="Address" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="80" type="text" id="Address_{{$race->heatId}}" name="Address"><br/>
                        @endif
                        <label for="Address2_{{$race->heatId}}"><strong>{{$strings['str_addressLine2']}}:</strong></label> <input maxlength="255" type="text" id="Address2_{{$race->heatId}}" name="Address2"><br/>
                    @endif

                    <!-- City -->
                    @if($settings['cityShown'])
                        <label for="City_{{$race->heatId}}"><strong>{{$strings['str_city']}}: @if($settings['cityRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['cityRequired'])
                            <input maxlength="80" type="text" id="City_{{$race->heatId}}" name="City" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="80" type="text" id="City_{{$race->heatId}}" name="City"><br/>
                        @endif
                    @endif

                    <!-- State -->
                    @if($settings['stateShown'])
                        <label for="State_{{$race->heatId}}"><strong>{{$strings['str_state']}}: @if($settings['stateRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['stateRequired'])
                            <input maxlength="50" type="text" id="State_{{$race->heatId}}" name="State" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="50" type="text" id="State_{{$race->heatId}}" name="State"><br/>
                        @endif
                    @endif

                    <!-- Zip -->
                    @if($settings['zipShown'])
                        <label for="Zip_{{$race->heatId}}"><strong>{{$strings['str_postalCode']}}: @if($settings['zipRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['zipRequired'])
                            <input maxlength="15" type="text" id="Zip_{{$race->heatId}}" name="Zip" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="15" type="text" id="Zip_{{$race->heatId}}" name="Zip"><br/>
                        @endif
                    @endif

                    <!-- Country -->
                    @if($settings['countryShown'])
                        <label for="Country_{{$race->heatId}}"><strong>{{$strings['str_country']}}: @if($settings['countryRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['countryRequired'])
                            <input maxlength="50" type="text" id="Country_{{$race->heatId}}" name="Country" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="50" type="text" id="Country_{{$race->heatId}}" name="Country"><br/>
                        @endif
                    @endif

                    <!-- Cell -->
                    @if($settings['cellShown'])
                        <label for="Cell_{{$race->heatId}}"><strong>{{$strings['str_cell']}}: @if($settings['cellRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['cellRequired'])
                            <input maxlength="50" type="text" id="Cell_{{$race->heatId}}" name="Cell" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="50" type="text" id="Cell_{{$race->heatId}}" name="Cell"><br/>
                        @endif
                    @endif

                    <!-- License Number -->
                    @if($settings['licenseNumberShown'])
                        <label for="LicenseNumber_{{$race->heatId}}"><strong>{{$strings['str_licenseNumber']}}: @if($settings['licenseNumberRequired'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['licenseNumberRequired'])
                            <input maxlength="100" type="text" id="LicenseNumber_{{$race->heatId}}" name="LicenseNumber" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="100" type="text" id="LicenseNumber_{{$race->heatId}}" name="LicenseNumber"><br/>
                        @endif
                    @endif

                    <!-- Custom1 -->
                    @if($settings['custom1Shown'])
                        <label for="Custom1_{{$race->heatId}}"><strong>{{$settings['CustomText1']}}: @if($settings['custom1Required'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['custom1Required'])
                            <input maxlength="50" type="text" id="Custom1_{{$race->heatId}}" name="Custom1" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="50" type="text" id="Custom1_{{$race->heatId}}" name="Custom1"><br/>
                        @endif
                    @endif

                    <!-- Custom2 -->
                    @if($settings['custom2Shown'])
                        <label for="Custom2_{{$race->heatId}}"><strong>{{$settings['CustomText2']}}: @if($settings['custom2Required'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['custom2Required'])
                            <input maxlength="50" type="text" id="Custom2_{{$race->heatId}}" name="Custom2" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="50" type="text" id="Custom2_{{$race->heatId}}" name="Custom2"><br/>
                        @endif
                    @endif

                    <!-- Custom3 -->
                    @if($settings['custom3Shown'])
                        <label for="Custom3_{{$race->heatId}}"><strong>{{$settings['CustomText3']}}: @if($settings['custom3Required'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['custom3Required'])
                            <input maxlength="50" type="text" id="Custom3_{{$race->heatId}}" name="Custom3" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="50" type="text" id="Custom3_{{$race->heatId}}" name="Custom3"><br/>
                        @endif
                    @endif

                    <!-- Custom4 -->
                    @if($settings['custom4Shown'])
                        <label for="Custom4_{{$race->heatId}}"><strong>{{$settings['CustomText4']}}: @if($settings['custom4Required'])<span class="requiredAsterisk">*</span> @endif</strong></label>
                        @if($settings['custom4Required'])
                            <input maxlength="50" type="text" id="Custom4_{{$race->heatId}}" name="Custom4" class="required validatePresence_{{$race->heatId}}"><br/>
                        @else
                            <input maxlength="50" type="text" id="Custom4_{{$race->heatId}}" name="Custom4"><br/>
                        @endif
                    @endif

                    <input type="hidden" name="heatId" value="{{$race->heatId}}">
                    <input type="hidden" name="numberOfParticipants" value="{{$numberOfParticipants}}">
                    <input type="hidden" name="pageSource" value="step2">
                    <div class="rightAligned">
                        <button type="submit" class="formButton">{{$strings['str_createAccount']}}</button>
                    </div>
                </form>
            </div>
            <!-- END ACCOUNT CREATION FORM -->

            <!-- LOGIN FORM -->
            <div class="loginToAccount collapse out" data-toggle="false" id="loginToAccount_{{$race->heatId}}">
                @if (isset($loginToAccountErrors) && array_key_exists($race->heatId,$loginToAccountErrors) && count( $loginToAccountErrors[$race->heatId] ) > 0 )
                <div class="alert alert-danger alert-dismissable accountError" role="alert">
                    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">{{$strings['str_close']}}</span></button>
                    @foreach ($loginToAccountErrors[$race->heatId] as $message)
                    {{ $message }}<br/>
                    @endforeach
                </div>
                @endif

                <form action="login" class="loginForm" method="POST">
                    <div class="formHeader">{{$strings['str_loginToYourExistingAccount']}}</div>
                    <label for="loginEmail_{{$race->heatId}}"><strong>{{$strings['str_emailAddress']}}: <span class="requiredAsterisk">*</span></strong></label> <input type="text" name="EmailAddress" id="loginEmail_{{$race->heatId}}" class="required mustBeValidEmail"><br/>
                    <label for="loginPassword_{{$race->heatId}}"><strong>{{$strings['str_password']}}: <span class="requiredAsterisk">*</span></strong></label> <input type="password" name="Password" id="loginPassword_{{$race->heatId}}" class="required"><br/>
                    <input type="hidden" name="heatId" value="{{$race->heatId}}">
                    <input type="hidden" name="numberOfParticipants" value="{{$numberOfParticipants}}">
                    <input type="hidden" name="pageSource" value="step2">
                    <div class="rightAligned">
                        {{link_to('resetpassword',$strings['str_resetPassword'])}} <button type="submit" class="formButton">{{$strings['str_login']}}</button>
                    </div>
                    <div class="alert alert-info">
                        {{$strings['str_alreadyRegisteredTextPart1']}} {{link_to('resetpassword',$strings['str_resetPassword'])}} {{$strings['str_alreadyRegisteredTextPart2']}}
                    </div>
                </form>
            </div>
            <!-- END LOGIN FORM -->
        </div>

        @endforeach
    @endif
</div>
@stop
<!-- END PAGE CONTENT -->

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent

    <!-- Login form validation -->
    <script>
    $(document).ready(function() {
        $.validator.addMethod("requiredField",$.validator.methods.required,"{{$strings['str_thisFieldIsRequired']}}");
        $.validator.addClassRules("required", {requiredField: true});

        $.validator.addMethod("mustBeValidEmail",$.validator.methods.email,"{{$strings['str_mustBeAValidEmail']}}");
        $.validator.addClassRules("emailFormElement", {mustBeValidEmail: true});

        var loginForms = $('.loginForm');
        loginForms.each(function() {
            var currentForm = $(this);
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
                 errorElement: 'div'
             });
        });
    });
    </script>

<!-- This script allows for the page to be returned to with a Login or Create Account form for a specific heat pre-opened -->
<!-- Example: /step2?create=63929#63929 will pre-open the Create Account section of heatId 63929 and will snap to it -->
<!-- Example: /step2?login=63929#63929 will pre-open the Login to Existing Account section of heatId 63929 and will snap to it -->
<script>

    //Extends jQuery to be able to extract GET URL parameters
    (function($) {
        $.QueryString = (function(a) {
            if (a == "") return {};
            var b = {};
            for (var i = 0; i < a.length; ++i)
            {
                var p=a[i].split('=');
                if (p.length != 2) continue;
                b[p[0]] = decodeURIComponent(p[1].replace(/\+/g, " "));
            }
            return b;
        })(window.location.search.substr(1).split('&'))
    })(jQuery);

    $( document ).ready(function() {
        if (typeof $.QueryString["login"] != "undefined")
        {
            var heatId = $.QueryString["login"];
            var loginOptionsDivId = "#loginOptions_" + heatId;
            var loginToAccountDivId = "#loginToAccount_" + heatId;
            $(loginOptionsDivId).collapse('show');
            $(loginToAccountDivId).collapse('show');
        }
        else if (typeof $.QueryString["create"] != "undefined")
        {
            var heatId = $.QueryString["create"];
            var loginOptionsDivId = "#loginOptions_" + heatId;
            var createAccountDivId = "#createAccount_" + heatId;
            $(loginOptionsDivId).collapse('show');
            $(createAccountDivId).collapse('show');
        }
    });


</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->