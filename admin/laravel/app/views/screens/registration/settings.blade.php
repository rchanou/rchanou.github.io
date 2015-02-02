@extends('master')

@section('title')
Registrations Settings
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
Registrations Settings
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#">Registrations</a>
<a href="#" class="current">Settings</a>
@stop

@section('content')
{{ Form::open(array('action'=>'RegistrationController@updateSettings','files'=>false, 'class' => 'form-horizontal')) }}

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

            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                <h5>Customer Fields</h5>
              </div>
              <div class="widget-content">
                  <div class="row">
                      @if (count($isChecked) > 0)
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
                                    @if(isset($isChecked['genderShown']) && isset($isChecked['genderRequired']))
                                    <tr>
                                        <td>Gender</td>
                                        <td><input id="genderShown" name="genderShown" type="checkbox" {{$isChecked['genderShown']}}></td>
                                        <td><input id="genderRequired" name="genderRequired" type="checkbox" {{$isChecked['genderRequired']}}></td>
                                    </tr>
                                    @endif
                              </tbody>
                          </table>
                      </div>
                      <div class="col-sm-12">
                        <div class="form-actions" style="margin-bottom: 10px;">
                          {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                        </div>
                      </div>
                  </div>
                  @else
                  <div class="alert alert-info">
                    No custom settings are currently available to edit on this page.
                    In the meanwhile, please proceed to
                    <a href="{{'http://' . $_SERVER['HTTP_HOST'] . '/sp_admin'}}" target="_blank">
                      sp_admin
                    </a>
                    to edit your registration settings.
                  </div>
                  @endif
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

                  @if (!(isset($registrationSettings['defaultCountry'])))
                    <div class="alert alert-info">
                      Contact Club Speed if you wish to activate this section of the Registration Settings page.
                      In the meantime,
                        <a href="{{'http://' . $_SERVER['HTTP_HOST'] . '/sp_admin'}}" target="_blank">
                          click here
                        </a>
                      to go to the older sp_admin panel to edit more Registration settings.
                    </div>
                  @else

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="col-sm-4 col-md-4 col-lg-4 control-label">Enable Facebook</label>
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="checkbox" id="Reg_EnableFacebook" name="Reg_EnableFacebook" {{$isChecked['Reg_EnableFacebook']}}>
                        <span class="help-block text-left">
                          If checked, Facebook integration on the site is enabled and a Facebook link will appear in Step 1.
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-4 col-md-4 col-lg-4 control-label">Facebook URL</label>
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <input class="form-control" type="text" id="FacebookPageURL" name="FacebookPageURL" value="{{$mainEngineSettings['FacebookPageURL']}}">
                        <span class="help-block text-left">
                          If Facebook integration is enabled and the user logs in to Facebook while registering, a "Like Us On Facebook" widget will show linking to this page.
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-4 col-md-4 col-lg-4 control-label">Enable Signature(s)</label>
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="checkbox" id="CfgRegUseMsign" name="CfgRegUseMsign" {{$isChecked['CfgRegUseMsign']}}>
                        <span class="help-block text-left">
                          If checked, signatures are enabled.
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-4 col-md-4 col-lg-4 control-label">Minimum Age to Register Online</label>
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <input class="form-control" type="number" style="width:50%;" id="AgeAllowOnlineReg" name="AgeAllowOnlineReg" value="{{$mainEngineSettings['AgeAllowOnlineReg']}}">
                        <span class="help-block text-left">
                          Minimum age required to register. This is independent of minor age.
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-4 col-md-4 col-lg-4 control-label">Adult Age</label>
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <input class="form-control" type="number" style="width:50%;" id="AgeNeedParentWaiver" name="AgeNeedParentWaiver" value="{{$mainEngineSettings['AgeNeedParentWaiver']}}">
                        <span class="help-block text-left">
                          Any registrant under this age is considered a minor. (Example: If set to 18, registrants under 18 will be considered minors.)
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-4 col-md-4 col-lg-4 control-label">Allow Minors to Sign</label>
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="checkbox" id="cfgRegAllowMinorToSign" name="cfgRegAllowMinorToSign" {{$isChecked['cfgRegAllowMinorToSign']}}>
                        <span class="help-block text-left">
                          If checked, minors are allowed to sign. Otherwise, they just accept and are done.
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-4 col-md-4 col-lg-4 control-label">Disable E-mail Field for Minors</label>
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="checkbox" id="CfgRegDisblEmlForMinr" name="CfgRegDisblEmlForMinr" {{$isChecked['CfgRegDisblEmlForMinr']}}>
                        <span class="help-block text-left">
                          If checked, minors may NOT enter an e-mail address.
                        </span>
                      </div>
                    </div>
                  </div>

                  <div class="col-sm-6">
                    <div class="form-group">
                      <label class="col-sm-4 col-md-4 col-lg-4 control-label">Capture Profile Pic</label>
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="checkbox" id="Reg_CaptureProfilePic" name="Reg_CaptureProfilePic" {{$isChecked['Reg_CaptureProfilePic']}}>
                        <span class="help-block text-left">
                          If checked, the profile picture will be enabled.
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-4 col-md-4 col-lg-4 control-label">Default Country</label>
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <input class="form-control" type="text" id="defaultCountry" name="defaultCountry" value="{{$registrationSettings['defaultCountry']}}">
                        <span class="help-block text-left">
                          This is the country that is selected by default.
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-4 col-md-4 col-lg-4 control-label">Enable Waiver Step</label>
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="checkbox" id="enableWaiverStep" name="enableWaiverStep" {{$isChecked['enableWaiverStep']}}>
                        <span class="help-block text-left">
                          If disabled, the waiver is never shown, and Step 2 skips to Step 4.
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-4 col-md-4 col-lg-4 control-label">Show Texting Waiver</label>
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <input type="checkbox" id="showTextingWaiver" name="showTextingWaiver" {{$isChecked['showTextingWaiver']}}>
                        <span class="help-block text-left">
                          If checked, a texting waiver message will appear next to the mobile phone field.
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-4 col-md-4 col-lg-4 control-label">Texting Waiver</label>
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <textarea class="form-control" id="textingWaiver" name="textingWaiver">{{$registrationSettings['textingWaiver']}}</textarea>
                        <span class="help-block text-left">
                          This waiver message will appear next to the mobile phone field if "Show Texting Waiver" is checked.
                        </span>
                      </div>
                    </div>

                    <div class="form-group">
                      <label class="col-sm-4 col-md-4 col-lg-4 control-label">E-mail Checkbox Text</label>
                      <div class="col-sm-8 col-md-8 col-lg-8">
                        <textarea class="form-control" id="emailText" name="emailText" >{{$registrationSettings['emailText']}}</textarea>
                        <span class="help-block text-left">
                          This message will appear under the e-mail checkbox in Step 2. If left empty, a default message will appear.
                        </span>
                      </div>
                    </div>

                  </div>

                  <div class="col-sm-12">
                    <div class="form-actions" style="margin-bottom: 10px;">
                      {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                    </div>
                  </div>

                  @endif
                </div>
              </div>
            </div>
          </div>
     </div>

    </div>


{{ Form::close() }}
@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent
<script>

    $(document).ready(function () {

        //If a customer field is not shown, make sure it is not required

        $('#genderShown').on('ifUnchecked',function (event) {
            $('#genderRequired').iCheck('uncheck');
        });

        //If a customer field is required, make sure it is shown

        $('#genderRequired').on('ifChecked',function (event) {
            $('#genderShown').iCheck('check');
        });

    });

</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->
