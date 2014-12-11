@extends('master')

@section('title')
Online Bookings Settings
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
Online Bookings Settings
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#" class="current">Online Bookings</a>
<a href="#" class="current">Settings</a>
@stop

@section('content')
{{ Form::open(array('action'=>'BookingController@updateSettings','files'=>false, 'class' => 'form-horizontal')) }}

    <div class="container-fluid">
      <div class="row">


          <div class="col-xs-12">
            @if ($currentOnlineBookingState == 'disabled_manually')
            <div class="alert alert-warning">
                <p>(Note: Online Booking is <strong>current disabled</strong> because the "Enable Online Booking" setting is not checked.</p>
                To access Online Booking while it's disabled (for testing), <a href="{{'https://' . $_SERVER['HTTP_HOST'] . '/booking/step1?key=' . md5(Config::get('config.privateKey'))}}">use this link</a>.
            </div>
            @endif
            @if ($currentOnlineBookingState == 'disabled_dummypayments')
            <div class="alert alert-warning">
                <p>(Note: Online Booking is <strong>current disabled</strong> because the site is using the Dummy payment processor.)</p>
                To access Online Booking while it's disabled (for testing), <a href="{{'https://' . $_SERVER['HTTP_HOST'] . '/booking/step1?key=' . md5(Config::get('config.privateKey'))}}">use this link</a>.
            </div>
            @endif
            @if ($currentOnlineBookingState == 'missing_translations')
            <div class="alert alert-warning">
                <p>(Note: Online Booking is <strong>missing some translations</strong> for the current culture. They will default to English (US).)</p>
                Please proceed to the <a href="{{URL::to('translations')}}">Translations section</a> and update those translations.
            </div>
            @endif
            @if (Session::has("message"))
            <div class="alert alert-success fadeAway">
                <p>{{ Session::get("message") }}</p>
            </div>
            @endif
            @if (Session::has("error"))
            <div class="alert alert-danger">
                <p>{{ Session::get("error") }}</p>
            </div>
            @endif
            @if (Session::has("errors"))
            <div class="alert alert-danger"> <!-- Errors from Laravel validation -->
                <ul>
                @foreach ($errors->all('<li>:message</li>') as $message)
                    {{ $message }}
                @endforeach
                </ul>
            </div>
            @endif

            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                <h5>Customer Fields</h5>
              </div>
              <div class="widget-content">
                  <div class="row">
                      <div class="col-sm-6">
                          <table class="table table-bordered table-striped table-hover text-center">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th style="width: 33%">Shown</th>
                                        <th style="width: 33%">Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Racer Name</td>
                                        <td><input id="racerNameShown" name="racerNameShown" type="checkbox" {{$isChecked['racerNameShown']}}></td>
                                        <td><input id="racerNameRequired" name="racerNameRequired" type="checkbox" {{$isChecked['racerNameRequired']}}></td>
                                    </tr>
                                    <tr>
                                        <td>Gender</td>
                                        <td><input id="genderShown" name="genderShown" type="checkbox" {{$isChecked['genderShown']}}></td>
                                        <td><input id="genderRequired" name="genderRequired" type="checkbox" {{$isChecked['genderRequired']}}></td>
                                    </tr>
                                    <tr>
                                        <td>Birthdate</td>
                                        <td><input id="birthDateShown" name="birthDateShown" type="checkbox" {{$isChecked['birthDateShown']}}></td>
                                        <td><input id="birthDateRequired" name="birthDateRequired" type="checkbox" {{$isChecked['birthDateRequired']}}></td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td><input id="emailShown" name="emailShown" type="checkbox" {{$isChecked['emailShown']}}></td>
                                        <td><input id="emailRequired" name="emailRequired" type="checkbox" {{$isChecked['emailRequired']}}></td>
                                    </tr>
                                    <tr>
                                        <td>Cell</td>
                                        <td><input id="cellShown" name="cellShown" type="checkbox" {{$isChecked['cellShown']}}></td>
                                        <td><input id="cellRequired" name="cellRequired" type="checkbox" {{$isChecked['cellRequired']}}></td>
                                    </tr>
                                    <tr>
                                        <td>Company</td>
                                        <td><input id="companyShown" name="companyShown" type="checkbox" {{$isChecked['companyShown']}}></td>
                                        <td><input id="companyRequired" name="companyRequired" type="checkbox" {{$isChecked['companyRequired']}}></td>
                                    </tr>
                                    <tr>
                                        <td>License Number</td>
                                        <td><input id="licenseNumberShown" name="licenseNumberShown" type="checkbox" {{$isChecked['licenseNumberShown']}}></td>
                                        <td><input id="licenseNumberRequired" name="licenseNumberRequired" type="checkbox" {{$isChecked['licenseNumberRequired']}}></td>
                                    </tr>
                                    <tr>
                                        <td>"Where Did You Hear About Us?"</td>
                                        <td><input id="whereDidYouHearAboutUsShown" name="whereDidYouHearAboutUsShown" type="checkbox" {{$isChecked['whereDidYouHearAboutUsShown']}}></td>
                                        <td><input id="whereDidYouHearAboutUsRequired" name="whereDidYouHearAboutUsRequired" type="checkbox" {{$isChecked['whereDidYouHearAboutUsRequired']}}></td>
                                    </tr>
                                    <tr>
                                        <td>Country</td>
                                        <td><input id="countryShown" name="countryShown" type="checkbox" {{$isChecked['countryShown']}}></td>
                                        <td><input id="countryRequired" name="countryRequired" type="checkbox" {{$isChecked['countryRequired']}}></td>
                                    </tr>
                              </tbody>
                          </table>
                      </div>
                      <div class="col-sm-6">
                          <table class="table table-bordered table-striped table-hover text-center">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th style="width: 33%;">Shown</th>
                                        <th style="width: 33%;">Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Address</td>
                                        <td><input id="addressShown" name="addressShown" type="checkbox" {{$isChecked['addressShown']}}></td>
                                        <td><input id="addressRequired" name="addressRequired" type="checkbox" {{$isChecked['addressRequired']}}></td>
                                    </tr>
                                    <tr>
                                        <td>City</td>
                                        <td><input id="cityShown" name="cityShown" type="checkbox" {{$isChecked['cityShown']}}></td>
                                        <td><input id="cityRequired" name="cityRequired" type="checkbox" {{$isChecked['cityRequired']}}></td>
                                    </tr>
                                    <tr>
                                        <td>State</td>
                                        <td><input id="stateShown" name="stateShown" type="checkbox" {{$isChecked['stateShown']}}></td>
                                        <td><input id="stateRequired" name="stateRequired" type="checkbox" {{$isChecked['stateRequired']}}></td>
                                    </tr>
                                    <tr>
                                        <td>Postal Code</td>
                                        <td><input id="zipShown" name="zipShown" type="checkbox" {{$isChecked['zipShown']}}></td>
                                        <td><input id="zipRequired" name="zipRequired" type="checkbox" {{$isChecked['zipRequired']}}></td>
                                    </tr>
                                    <tr>
                                        <td>Custom 1</td>
                                        <td><input id="custom1Shown" name="custom1Shown" type="checkbox" {{$isChecked['custom1Shown']}}></td>
                                        <td><input id="custom1Required" name="custom1Required" type="checkbox" {{$isChecked['custom1Required']}}></td>
                                    </tr>
                                    <tr>
                                        <td>Custom 2</td>
                                        <td><input id="custom2Shown" name="custom2Shown" type="checkbox" {{$isChecked['custom2Shown']}}></td>
                                        <td><input id="custom2Required" name="custom2Required" type="checkbox" {{$isChecked['custom2Required']}}></td>
                                    </tr>
                                    <tr>
                                        <td>Custom 3</td>
                                        <td><input id="custom3Shown" name="custom3Shown" type="checkbox" {{$isChecked['custom3Shown']}}></td>
                                        <td><input id="custom3Required" name="custom3Required" type="checkbox" {{$isChecked['custom3Required']}}></td>
                                    </tr>
                                    <tr>
                                        <td>Custom 4</td>
                                        <td><input id="custom4Shown" name="custom4Shown" type="checkbox" {{$isChecked['custom4Shown']}}></td>
                                        <td><input id="custom4Required" name="custom4Required" type="checkbox" {{$isChecked['custom4Required']}}></td>
                                    </tr>
                                </tbody>
                          </table>
                      </div>
                      <div class="col-sm-12">
                           <div class="form-actions" style="margin-bottom: 10px;">
                               {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                           </div>
                      </div>
                  </div>
              </div>
            </div>

            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                <h5>General Settings</h5>
              </div>
              <div class="widget-content">
                  <div class="row">
                      <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Enable Online Booking</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <input type="checkbox" id="registrationEnabled" name="registrationEnabled" {{$isChecked['registrationEnabled']}}>
                                    <span class="help-block text-left">If checked, Online Booking is turned on and available on the Internet (unless the payment processor isn't set up).
                                    @if(!$isChecked['registrationEnabled'])
                                    <div class="alert alert-info">To access Online Booking while it's disabled (for testing), <a href="{{'https://' . $_SERVER['HTTP_HOST'] . '/booking/step1?key=' . md5(Config::get('config.privateKey'))}}">use this link</a>.</div>
                                    @endif
                                    </span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Enable Facebook Login</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <input type="checkbox" id="enableFacebook" name="enableFacebook" {{$isChecked['enableFacebook']}}>
                                    <span class="help-block text-left">If checked, users may register and login via Facebook.</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Force Account Creation</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <input type="checkbox" id="forceRegistrationIfAuthenticatingViaThirdParty" name="forceRegistrationIfAuthenticatingViaThirdParty" {{$isChecked['forceRegistrationIfAuthenticatingViaThirdParty']}}>
                                    <span class="help-block text-left">If checked, users are required to create a Club Speed account even if authenticating via a third party (ex. Facebook).</span>
                                </div>
                            </div>
                            @if(isset($isChecked['autoAddRacerToHeat']) && $bookingSettings['supportsCacheClearing'])
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Auto Add Racers To Heat</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <input type="checkbox" id="autoAddRacerToHeat" name="autoAddRacerToHeat" {{$isChecked['autoAddRacerToHeat']}}>
                                    <span class="help-block text-left">If checked, racers are automatically added to heats after placing an order online.</span>
                                </div>
                            </div>
                            @elseif(isset($isChecked['autoAddRacerToHeat']) && !$bookingSettings['supportsCacheClearing'])
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Auto Add Racers To Heat</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <span class="help-block text-left" style="color: #c20000">This setting is not currently supported by your server.</span>
                                </div>
                            </div>
                            @endif
                            @if(isset($bookingSettings['sendReceiptCopyTo']))
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Send Receipt Copies To</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <input type="text" style="width: 100%" id="sendReceiptCopyTo" name="sendReceiptCopyTo" value="{{$bookingSettings['sendReceiptCopyTo']}}">
                                    <span class="help-block text-left">A comma-separated list of e-mail addresses to BCC a receipt to whenever an Online Booking order is placed.</span>
                                </div>
                            </div>
                            @endif
                            @if(isset($bookingSettings['showLanguageDropdown']))
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Show Language Dropdown</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <input type="checkbox" id="showLanguageDropdown" name="showLanguageDropdown" {{$isChecked['showLanguageDropdown']}}>
                                    <span class="help-block text-left">If checked, a dropdown menu that allows changing the current language is shown at the top of the page. Only languages that have <a href="{{URL::to('booking/translations')}}">translations</a> available will be displayed as options.</span>
                                </div>
                            </div>
                            @else
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Show Language Dropdown</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <span class="help-block text-left" style="color: #c20000">This setting is not currently supported by your server.</span>
                                </div>
                            </div>
                            @endif
                            @if(isset($bookingSettings['dateDisplayFormat']))
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Date Display Format</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <input type="text" id="dateDisplayFormat" name="dateDisplayFormat" value="{{$bookingSettings['dateDisplayFormat']}}">
                                    <i class="fa fa-question-circle tip"
                                        data-container="body" data-toggle="popover" data-placement="top" data-html="true"
                                        data-content="
                                            <div class='text-center'><strong>Valid characters</strong></div>
                                            <table class='table table-condensed table-mini'>
                                                <thead>
                                                    <tr>
                                                        <th>Character</th>
                                                        <th>Description</th>
                                                        <th>Example</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>d</td>
                                                        <td>Day of the month, 2 digits with leading zeros</td>
                                                        <td>01 to 31</td>
                                                    </tr>
                                                    <tr>
                                                        <td>D</td>
                                                        <td>A textual representation of a day, three letters</td>
                                                        <td>Mon through Sun</td>
                                                    </tr>
                                                    <tr>
                                                        <td>l (lowercase L)</td>
                                                        <td>A full textual representation of the day of the week</td>
                                                        <td>Sunday through Saturday</td>
                                                    </tr>
                                                    <tr>
                                                        <td>m</td>
                                                        <td>Numeric representation of a month, with leading zeros</td>
                                                        <td>01 through 12</td>
                                                    </tr>
                                                    <tr>
                                                        <td>M</td>
                                                        <td>A short textual representation of a month, three letters</td>
                                                        <td>Jan through Dec</td>
                                                    </tr>
                                                    <tr>
                                                        <td>F</td>
                                                        <td>A full textual representation of a month, such as January or March</td>
                                                        <td>January through December</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Y</td>
                                                        <td>A full numeric representation of a year, 4 digits</td>
                                                        <td>Examples: 1999 or 2003</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class='table-mini'>
                                            Also usable: Spaces, dashes, commas, and forward slashes.
                                            </div>
                                        ">
                                    </i>
                                    <span class="help-block text-left">
                                    Current format: {{date($bookingSettings['dateDisplayFormat'])}}
                                    <br/>The format in which to display dates.
                                    </span>
                                </div>
                            </div>
                            @else
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Date Display Format</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <span class="help-block text-left" style="color: #c20000">This setting is not currently supported by your server.</span>
                                </div>
                            </div>
                            @endif
                            @if(isset($bookingSettings['timeDisplayFormat']))
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Time Display Format</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <input type="text" id="timeDisplayFormat" name="timeDisplayFormat" value="{{$bookingSettings['timeDisplayFormat']}}">
                                    <i class="fa fa-question-circle tip"
                                        data-container="body" data-toggle="popover" data-placement="top" data-html="true"
                                        data-content="
                                            <div class='text-center'><strong>Valid characters</strong></div>
                                            <table class='table table-condensed table-mini'>
                                                <thead>
                                                    <tr>
                                                        <th>Character</th>
                                                        <th>Description</th>
                                                        <th>Example</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>g</td>
                                                        <td>12-hour format of an hour without leading zeros</td>
                                                        <td>1 through 12</td>
                                                    </tr>
                                                    <tr>
                                                        <td>H</td>
                                                        <td>24-hour format of an hour with leading zeros</td>
                                                        <td>00 through 23</td>
                                                    </tr>
                                                    <tr>
                                                        <td>i</td>
                                                        <td>Minutes with leading zeros</td>
                                                        <td>00 to 59</td>
                                                    </tr>
                                                    <tr>
                                                        <td>s</td>
                                                        <td>Seconds, with leading zeros</td>
                                                        <td>00 through 59</td>
                                                    </tr>
                                                    <tr>
                                                        <td>a</td>
                                                        <td>Lowercase Ante meridiem and Post meridiem</td>
                                                        <td>am or pm</td>
                                                    </tr>
                                                    <tr>
                                                        <td>A</td>
                                                        <td>Uppercase Ante meridiem and Post meridiem</td>
                                                        <td>AM or PM</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <div class='table-mini'>
                                            Also usable: Spaces, dashes, colons, commas, and forward slashes.
                                            </div>
                                        ">
                                    </i>
                                    <span class="help-block text-left">
                                    Current format: {{date($bookingSettings['timeDisplayFormat'])}}
                                    <br/>The format in which to display times.
                                    </span>
                                </div>
                            </div>
                            @else
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Time Display Format</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <span class="help-block text-left" style="color: #c20000">This setting is not currently supported by your server.</span>
                                </div>
                            </div>
                            @endif
                      </div>
                      <div class="col-sm-6">
                          @if(isset($isChecked['showTermsAndConditions']))
                          <div class="form-group">
                              <label class="col-sm-4 col-md-4 col-lg-4 control-label">Show Terms & Conditions</label>
                              <div class="col-sm-8 col-md-8 col-lg-8">
                                  <input type="checkbox" id="showTermsAndConditions" name="showTermsAndConditions" {{$isChecked['showTermsAndConditions']}}>
                                  <span class="help-block text-left">If checked, Terms & Conditions are shown and the user must check an "I agree" checkbox to place an order.</span>
                              </div>
                          </div>
                          @endif
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Reservation Timeout</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <input type="text" id="reservationTimeout" name="reservationTimeout" value="{{$bookingSettings['reservationTimeout']/60}}">
                                    <span class="help-block text-left">The time until an unpaid online reservation expires, in minutes.</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Earliest Booking Time Window</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <input type="text" id="bookingAvailabilityWindowBeginningInSeconds" name="bookingAvailabilityWindowBeginningInSeconds" value="{{$bookingSettings['bookingAvailabilityWindowBeginningInSeconds']/60}}">
                                    <span class="help-block text-left">The window of time that defines the earliest a booking may be available online, in minutes. (Example: If set to 120, no booking may be made for a race happening in less than two hours.)</span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Latest Booking Time Window</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <input type="text" id="bookingAvailabilityWindowEndingInSeconds" name="bookingAvailabilityWindowEndingInSeconds" value="{{$bookingSettings['bookingAvailabilityWindowEndingInSeconds']/86400}}">
                                    <span class="help-block text-left">The window of time that defines the latest a booking may be available online, in days. (Example: If set to 90, no booking may be made for a race happening more than 90 days from now.)</span>
                                </div>
                            </div>
                            @if(isset($bookingSettings['maxRacersForDropdown']))
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Maximum Number of Racers</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <input type="text" id="maxRacersForDropdown" name="maxRacersForDropdown" value="{{$bookingSettings['maxRacersForDropdown']}}">
                                    <span class="help-block text-left">The maximum number of racers that can selected in the dropdown on Step 1 of Online Booking.</span>
                                </div>
                            </div>
                            @else
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Maximum Number of Racers</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <span class="help-block text-left" style="color: #c20000">This setting is not currently supported by your server.</span>
                                </div>
                            </div>
                            @endif
                            @if(isset($bookingSettings['currency']))
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Currency</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    {{Form::select('currency',$supportedCurrencies,$bookingSettings['currency'])}}
                                    <span class="help-block text-left">
                                    <?php
                                        if (isset($bookingSettings['numberFormattingLocale']) && isset($bookingSettings['currency']))
                                        {
                                             $locale = $bookingSettings['numberFormattingLocale'];
                                             $moneyFormatter = new NumberFormatter($locale,  NumberFormatter::CURRENCY);
                                             $currency = $bookingSettings['currency'];
                                             $currencyExample = $moneyFormatter->formatCurrency(1234.56, $currency);
                                        }
                                    ?>
                                    Current format: {{$currencyExample}}
                                    <br/>
                                    The currency to be used when displaying prices.
                                    </span>
                                </div>
                            </div>
                            @else
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Currency</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <span class="help-block text-left" style="color: #c20000">This setting is not currently supported by your server.</span>
                                </div>
                            </div>
                            @endif
                            @if(isset($bookingSettings['numberFormattingLocale']))
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Number Formatting Locale</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    {{Form::select('numberFormattingLocale',$supportedNumberLocales,$bookingSettings['numberFormattingLocale'])}}
                                    <span class="help-block text-left">
                                    <?php
                                        if (isset($bookingSettings['numberFormattingLocale']) && isset($bookingSettings['currency']))
                                        {
                                             $locale = $bookingSettings['numberFormattingLocale'];
                                             $moneyFormatter = new NumberFormatter($locale,  NumberFormatter::DECIMAL);
                                             $currency = $bookingSettings['currency'];
                                             $numberFormattingExample = $moneyFormatter->formatCurrency(1234.56, $currency);
                                        }
                                    ?>
                                    Current format: {{$numberFormattingExample}}
                                    <br/>
                                    The locale to use in order to format numbers.
                                    </span>
                                </div>
                            </div>
                            @else
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Number Formatting Locale</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    <span class="help-block text-left" style="color: #c20000">
                                    This setting is not currently supported by your server.
                                    </span>
                                </div>
                            </div>
                            @endif
                      </div>
                      <div class="col-sm-12">
                           <div class="form-actions" style="margin-bottom: 10px;">
                               {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                           </div>
                      </div>
                  </div>
              </div>
            </div>
          </div>


     </div>

    {{ Form::close() }}

     <div class="row">
        <div class="col-xs-12">
            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                <h5>Background Image</h5>
              </div>
              <div class="widget-content nopadding">
                {{ Form::open(array('action'=>'BookingController@updateImage','files'=>true, 'class' => 'form-horizontal')) }}
                    @if(!empty($background_image_url))
                    <div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-2 control-label">Current Image</div><div class="col-sm-9 col-md-9 col-lg-10"><a href="{{$background_image_url}}" target="_blank"><img src="{{$background_image_url}}" width="192" height="108" style="border: 1px solid #ddd; padding: 5px; margin: 1em;" /></a></div>
                    </div>
                    @endif
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">{{ Form::label('image','Select an Image',array('id'=>'','class'=>'')) }}</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            {{ Form::file('image','',array('id'=>'','class'=>'')) }}
                            <span class="help-block text-left">Image must be a JPG. Recommended size: 1920x1080 pixels.</span>
                        </div>
                    </div>
                    <div class="form-actions">
                        <input type="hidden" name="filename" value="background.jpg">
                        {{ Form::submit('Upload', array('class' => 'btn btn-info')) }}
                    </div>
                {{ Form::close() }}
              </div>
            </div>
        </div>
     </div>

     <div class="row">
        <div class="col-xs-12">
            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                <h5>Header Image</h5>
              </div>
              <div class="widget-content nopadding">
                {{ Form::open(array('action'=>'BookingController@updateImage','files'=>true, 'class' => 'form-horizontal')) }}
                    @if(!empty($header_image_url))
                    <div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-2 control-label">Current Image</div><div class="col-sm-9 col-md-9 col-lg-10"><a href="{{$header_image_url}}" target="_blank"><img src="{{$header_image_url}}" width="305" height="45" style="border: 1px solid #ddd; padding: 5px; margin: 1em;" /></a></div>
                    </div>
                    @endif
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">{{ Form::label('image','Select an Image',array('id'=>'','class'=>'')) }}</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            {{ Form::file('image','',array('id'=>'','class'=>'')) }}
                            <span class="help-block text-left">Image must be a JPG. Recommended size: 610x90 pixels.</span>
                        </div>
                    </div>
                    <div class="form-actions">
                        <input type="hidden" name="filename" value="header.jpg">
                        {{ Form::submit('Upload', array('class' => 'btn btn-info')) }}
                    </div>
                {{ Form::close() }}
              </div>
            </div>
        </div>
     </div>

     <div class="row">
        <div class="col-xs-12">
            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                <h5>Custom CSS</h5>
              </div>
              <div class="widget-content nopadding">
                {{ Form::open(array('action'=>'BookingController@updateFile','files'=>true, 'class' => 'form-horizontal')) }}
                    @if(!empty($custom_css_url))
                    <div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-2 control-label">Current CSS</div><div class="col-sm-9 col-md-9 col-lg-10 control-label" style="text-align: left;"><a href="{{$custom_css_url}}" target="_blank">custom-styles.css</a></div>
                    </div>
                    @endif
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">{{ Form::label('customfile','Select a CSS file',array('id'=>'','class'=>'')) }}</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            {{ Form::file('customfile','',array('id'=>'','class'=>'')) }}
                            <span class="help-block text-left">This CSS file will be added at the end of all other CSS files on the page.</span>
                        </div>
                    </div>
                    <div class="form-actions">
                        <input type="hidden" name="filename" value="custom-styles.css">
                        <input type="hidden" name="filetype" value="css">
                        {{ Form::submit('Upload', array('class' => 'btn btn-info')) }}
                    </div>
                {{ Form::close() }}
              </div>
            </div>
        </div>
     </div>

     <div class="row">
        <div class="col-xs-12">
            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                <h5>Custom JS</h5>
              </div>
              <div class="widget-content nopadding">
                {{ Form::open(array('action'=>'BookingController@updateFile','files'=>true, 'class' => 'form-horizontal')) }}
                    @if(!empty($custom_js_url))
                    <div class="row">
                        <div class="col-sm-3 col-md-3 col-lg-2 control-label">Current JS</div><div class="col-sm-9 col-md-9 col-lg-10 control-label" style="text-align: left;"><a href="{{$custom_js_url}}" target="_blank">custom-js.js</a></div>
                    </div>
                    @endif
                    <div class="form-group">
                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">{{ Form::label('customfile','Select a JS file',array('id'=>'','class'=>'')) }}</label>
                        <div class="col-sm-9 col-md-9 col-lg-10">
                            {{ Form::file('customfile','',array('id'=>'','class'=>'')) }}
                            <span class="help-block text-left">This JS file will be added at the end of all other JS files on the page.</span>
                        </div>
                    </div>
                    <div class="form-actions">
                        <input type="hidden" name="filename" value="custom-js.js">
                        <input type="hidden" name="filetype" value="js">
                        {{ Form::submit('Upload', array('class' => 'btn btn-info')) }}
                    </div>
                {{ Form::close() }}
              </div>
            </div>
        </div>
     </div>


    </div>

@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent
<script>

    $(document).ready(function () {

        window.setTimeout(function() {
          $(".fadeAway").fadeTo(500, 0).slideUp(500, function(){
              $(this).remove();
          });
        }, 5000);

        //If a customer field is not shown, make sure it is not required
        $('#racerNameShown').on('ifUnchecked',function (event) {
            $('#racerNameRequired').iCheck('uncheck');
        });
    
        $('#genderShown').on('ifUnchecked',function (event) {
            $('#genderRequired').iCheck('uncheck');
        });
    
        $('#birthDateShown').on('ifUnchecked',function (event) {
            $('#birthDateRequired').iCheck('uncheck');
        });
    
        $('#emailShown').on('ifUnchecked',function (event) {
            $('#emailRequired').iCheck('uncheck');
        });
    
        $('#cellShown').on('ifUnchecked',function (event) {
            $('#cellRequired').iCheck('uncheck');
        });
    
        $('#companyShown').on('ifUnchecked',function (event) {
            $('#companyRequired').iCheck('uncheck');
        });
    
        $('#licenseNumberShown').on('ifUnchecked',function (event) {
            $('#licenseNumberRequired').iCheck('uncheck');
        });
    
        $('#whereDidYouHearAboutUsShown').on('ifUnchecked',function (event) {
            $('#whereDidYouHearAboutUsRequired').iCheck('uncheck');
        });
    
        $('#countryShown').on('ifUnchecked',function (event) {
            $('#countryRequired').iCheck('uncheck');
        });
    
        $('#addressShown').on('ifUnchecked',function (event) {
            $('#addressRequired').iCheck('uncheck');
        });
    
        $('#cityShown').on('ifUnchecked',function (event) {
            $('#cityRequired').iCheck('uncheck');
        });
    
        $('#stateShown').on('ifUnchecked',function (event) {
            $('#stateRequired').iCheck('uncheck');
        });
    
        $('#zipShown').on('ifUnchecked',function (event) {
            $('#zipRequired').iCheck('uncheck');
        });
    
        $('#custom1Shown').on('ifUnchecked',function (event) {
            $('#custom1Required').iCheck('uncheck');
        });
    
        $('#custom2Shown').on('ifUnchecked',function (event) {
            $('#custom2Required').iCheck('uncheck');
        });
        
        $('#custom3Shown').on('ifUnchecked',function (event) {
            $('#custom3Required').iCheck('uncheck');
        });
                
        $('#custom4Shown').on('ifUnchecked',function (event) {
            $('#custom4Required').iCheck('uncheck');
        });
                      
        //If a customer field is required, make sure it is shown
        $('#racerNameRequired').on('ifChecked',function (event) {
            $('#racerNameShown').iCheck('check');
        });
    
        $('#genderRequired').on('ifChecked',function (event) {
            $('#genderShown').iCheck('check');
        });
    
        $('#birthDateRequired').on('ifChecked',function (event) {
            $('#birthDateShown').iCheck('check');
        });
    
        $('#emailRequired').on('ifChecked',function (event) {
            $('#emailShown').iCheck('check');
        });
    
        $('#cellRequired').on('ifChecked',function (event) {
            $('#cellShown').iCheck('check');
        });
    
        $('#companyRequired').on('ifChecked',function (event) {
            $('#companyShown').iCheck('check');
        });
    
        $('#licenseNumberRequired').on('ifChecked',function (event) {
            $('#licenseNumberShown').iCheck('check');
        });
    
        $('#whereDidYouHearAboutUsRequired').on('ifChecked',function (event) {
            $('#whereDidYouHearAboutUsShown').iCheck('check');
        });
    
        $('#countryRequired').on('ifChecked',function (event) {
            $('#countryShown').iCheck('check');
        });
    
        $('#addressRequired').on('ifChecked',function (event) {
            $('#addressShown').iCheck('check');
        });
    
        $('#cityRequired').on('ifChecked',function (event) {
            $('#cityShown').iCheck('check');
        });
    
        $('#stateRequired').on('ifChecked',function (event) {
            $('#stateShown').iCheck('check');
        });
    
        $('#zipRequired').on('ifChecked',function (event) {
            $('#zipShown').iCheck('check');
        });
    
        $('#custom1Required').on('ifChecked',function (event) {
            $('#custom1Shown').iCheck('check');
        });
    
        $('#custom2Required').on('ifChecked',function (event) {
            $('#custom2Shown').iCheck('check');
        });
        
        $('#custom3Required').on('ifChecked',function (event) {
            $('#custom3Shown').iCheck('check');
        });
                
        $('#custom4Required').on('ifChecked',function (event) {
            $('#custom4Shown').iCheck('check');
        }); 
    });

</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->