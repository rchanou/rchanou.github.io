@extends('master')

@section('title')
Mobile App Settings
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
Mobile App Settings
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#">Mobile App</a>
<a href="#" class="current">Settings</a>
@stop

@section('content')
{{ Form::open(array('action'=>'MobileAppController@updateSettings','files'=>false, 'class' => 'form-horizontal')) }}

    <div class="container-fluid">
      <div class="row">


          <div class="col-xs-12">
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

            @if(isset($isCheckedControlPanel['racerNameShown']))
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
                                    <td><input id="racerNameShown" name="racerNameShown" type="checkbox" {{$isCheckedControlPanel['racerNameShown']}}></td>
                                    <td><input id="racerNameRequired" name="racerNameRequired" type="checkbox" {{$isCheckedControlPanel['racerNameRequired']}}></td>
                                </tr>
                                <tr>
                                    <td>Gender</td>
                                    <td><input id="genderShown" name="genderShown" type="checkbox" {{$isCheckedControlPanel['genderShown']}}></td>
                                    <td><input id="genderRequired" name="genderRequired" type="checkbox" {{$isCheckedControlPanel['genderRequired']}}></td>
                                </tr>
                                <tr>
                                    <td>Birthdate</td>
                                    <td><input id="birthDateShown" name="birthDateShown" type="checkbox" {{$isCheckedControlPanel['birthDateShown']}}></td>
                                    <td><input id="birthDateRequired" name="birthDateRequired" type="checkbox" {{$isCheckedControlPanel['birthDateRequired']}}></td>
                                </tr>
                                <tr>
                                    <td>Email</td>
                                    <td><input id="emailShown" name="emailShown" type="checkbox" {{$isCheckedControlPanel['emailShown']}}></td>
                                    <td><input id="emailRequired" name="emailRequired" type="checkbox" {{$isCheckedControlPanel['emailRequired']}}></td>
                                </tr>
                                <tr>
                                    <td>Cell</td>
                                    <td><input id="cellShown" name="cellShown" type="checkbox" {{$isCheckedControlPanel['cellShown']}}></td>
                                    <td><input id="cellRequired" name="cellRequired" type="checkbox" {{$isCheckedControlPanel['cellRequired']}}></td>
                                </tr>
                                <tr>
                                    <td>Company</td>
                                    <td><input id="companyShown" name="companyShown" type="checkbox" {{$isCheckedControlPanel['companyShown']}}></td>
                                    <td><input id="companyRequired" name="companyRequired" type="checkbox" {{$isCheckedControlPanel['companyRequired']}}></td>
                                </tr>
                                <tr>
                                    <td>License Number</td>
                                    <td><input id="licenseNumberShown" name="licenseNumberShown" type="checkbox" {{$isCheckedControlPanel['licenseNumberShown']}}></td>
                                    <td><input id="licenseNumberRequired" name="licenseNumberRequired" type="checkbox" {{$isCheckedControlPanel['licenseNumberRequired']}}></td>
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
                                    <td><input id="addressShown" name="addressShown" type="checkbox" {{$isCheckedControlPanel['addressShown']}}></td>
                                    <td><input id="addressRequired" name="addressRequired" type="checkbox" {{$isCheckedControlPanel['addressRequired']}}></td>
                                </tr>
                                <tr>
                                    <td>City</td>
                                    <td><input id="cityShown" name="cityShown" type="checkbox" {{$isCheckedControlPanel['cityShown']}}></td>
                                    <td><input id="cityRequired" name="cityRequired" type="checkbox" {{$isCheckedControlPanel['cityRequired']}}></td>
                                </tr>
                                <tr>
                                    <td>State</td>
                                    <td><input id="stateShown" name="stateShown" type="checkbox" {{$isCheckedControlPanel['stateShown']}}></td>
                                    <td><input id="stateRequired" name="stateRequired" type="checkbox" {{$isCheckedControlPanel['stateRequired']}}></td>
                                </tr>
                                <tr>
                                    <td>Postal Code</td>
                                    <td><input id="zipShown" name="zipShown" type="checkbox" {{$isCheckedControlPanel['zipShown']}}></td>
                                    <td><input id="zipRequired" name="zipRequired" type="checkbox" {{$isCheckedControlPanel['zipRequired']}}></td>
                                </tr>
                                <tr>
                                    <td>Country</td>
                                    <td><input id="countryShown" name="countryShown" type="checkbox" {{$isCheckedControlPanel['countryShown']}}></td>
                                    <td><input id="countryRequired" name="countryRequired" type="checkbox" {{$isCheckedControlPanel['countryRequired']}}></td>
                                </tr>
                                <tr>
                                    <td>"Where Did You Hear About Us?"</td>
                                    <td><input id="whereDidYouHearAboutUsShown" name="whereDidYouHearAboutUsShown" type="checkbox" {{$isCheckedControlPanel['whereDidYouHearAboutUsShown']}}></td>
                                    <td><input id="whereDidYouHearAboutUsRequired" name="whereDidYouHearAboutUsRequired" type="checkbox" {{$isCheckedControlPanel['whereDidYouHearAboutUsRequired']}}></td>
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
            @endif

            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                <h5>General Settings</h5>
              </div>
              <div class="widget-content">
                  <div class="row">
                      @if(isset($mobileSettings) && count($mobileSettings) > 0)
                          <div class="col-sm-6">
                             @if(isset($mobileSettings['enableFacebook']))
                             <div class="form-group">
                                 <label class="col-sm-4 col-md-4 col-lg-4 control-label">Enable Facebook Login</label>
                                 <div class="col-sm-8 col-md-8 col-lg-8">
                                     <input type="checkbox" id="enableFacebook" name="enableFacebook" {{$isChecked['enableFacebook']}}>
                                     <span class="help-block text-left">If checked, users may login via Facebook.</span>
                                 </div>
                             </div>
                             @endif
                             @if(isset($mobileSettings['forceLogin']))
                             <div class="form-group">
                                 <label class="col-sm-4 col-md-4 col-lg-4 control-label">Force Login</label>
                                 <div class="col-sm-8 col-md-8 col-lg-8">
                                     <input type="checkbox" id="forceLogin" name="forceLogin" {{$isChecked['forceLogin']}}>
                                     <span class="help-block text-left">If checked, users must login to use the application.</span>
                                 </div>
                             </div>
                             @endif
                          </div>
                          <div class="col-sm-6">
                               @if(isset($mobileSettings['defaultApiKey']))
                               <div class="form-group">
                                   <label class="col-sm-4 col-md-4 col-lg-4 control-label">Public API Key</label>
                                   <div class="col-sm-8 col-md-8 col-lg-8">
                                       <input type="text" style="width: 100%" id="defaultApiKey" name="defaultApiKey" value="{{$mobileSettings['defaultApiKey']}}">
                                       <span class="help-block text-left">If users aren't forced to login, this is the public API key used
                                        that allows the application to talk to the Club Speed server.</span>
                                   </div>
                               </div>
                               @endif
                               @if(isset($mobileSettings['defaultTrack']))
                                   <div class="form-group">
                                       <label class="col-sm-4 col-md-4 col-lg-4 control-label">Default Track</label>
                                       <div class="col-sm-8 col-md-8 col-lg-8">
                                           {{Form::select('defaultTrack',$listOfTracks,$mobileSettings['defaultTrack'])}}
                                           <span class="help-block text-left">The default track to show top times for in the mobile app.</span>
                                       </div>
                                   </div>
                               @endif
                          </div>
                          <div class="col-sm-12">
                               <div class="form-actions" style="margin-bottom: 10px;">
                                   {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                               </div>
                          </div>
                      @else
                          <div class="col-sm-12">
                              <div class="alert alert-warning">
                                  <p>Unable to access mobile app settings. Please try again later, or contact support if the issue persists.</p>
                              </div>
                          </div>
                      @endif
                  </div>
              </div>
            </div>
          </div>


     </div>

    {{ Form::close() }}

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