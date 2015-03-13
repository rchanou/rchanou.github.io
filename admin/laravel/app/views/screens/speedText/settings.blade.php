@extends('master')

@section('title')
SpeedText Messaging Settings
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
    {{ HTML::style('css/bootstrap-tokenfield.min.css') }}
@stop

@section('pageHeader')
SpeedText Messaging Settings
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#">SpeedText</a>
<a href="#" class="current">SpeedText Messaging Settings</a>
@stop

@section('content')
  {{ Form::open(array('action'=>'SpeedTextController@updateSettings', 'files'=> false, 'class' => 'form-horizontal')) }}
    <div class="container-fluid">
      <div class="row">
        <div class="col-xs-12">
          @if($settings['provider'] === 'twilio' && empty($settings['sid']))
          	<div class="alert alert-danger">
                <p>You do not have a text messaging account with Twilio saved. Please <a href="http://www.twilio.com" target="_blank">create an account</a> and save your API credentials in the "Provider Options" section below.</p>
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
              <h5>General Settings</h5>
            </div>

            <div class="widget-content">
              <div class="row">
                @if(!isset($settings) || count($settings) == 0)
                  <div class="col-sm-12">
                    <div class="alert alert-warning">
                      <p>Unable to access SpeedText messaging settings. Please try again later, or contact <a href="mailto:support@clubspeed.com">support@clubspeed.com</a> if the issue persists.</p>
                    </div>
                  </div>
                @else
                  @if (!(isset($settings['isEnabled']) && $settings['isEnabled']))
                    <div class="col-sm-12">
                      <div class="alert alert-warning">
                        This feature is not currently installed. Please contact <a href="mailto:support@clubspeed.com">support@clubspeed.com</a> for installation and training.
                      </div>
                    </div>
                  @endif

                  @if ((isset($settings['isEnabled']) && $settings['isEnabled']) || $user === 'support')
                    <div class="col-sm-6">
                      @if($user === 'support')
                        <div class="form-group">
                          <label class="col-sm-4 col-md-4 col-lg-4 control-label">
                            <img src="{{asset('img/support_only.png')}}" style="cursor: help" title="This setting is only visible to Club Speed support staff.">
                            Enable Feature for Track
                          </label>
                        <div class="col-sm-8 col-md-8 col-lg-8">
                            <input type="checkbox" id="isEnabled" name="isEnabled" {{$isChecked['isEnabled']}}>
                            <span class="help-block text-left">Enable once the text messaging service is installed and configured.</span>
                          </div>
                        </div>
                        @endif

                        @if(isset($settings['textingIsEnabled']))
                          <div class="form-group">
                            <label class="col-sm-4 col-md-4 col-lg-4 control-label">Enable Auto Sending</label>
                            <div class="col-sm-8 col-md-8 col-lg-8">
                              <input type="checkbox" id="textingIsEnabled" name="textingIsEnabled" {{$isChecked['textingIsEnabled']}}>
                              <span class="help-block text-left">If checked, automatic sending of text messages will be enabled.</span>
                            </div>
                          </div>
                        @endif

                        @if(isset($settings['heatsPriorToSend']))
                          <div class="form-group">
                            <label class="col-sm-4 col-md-4 col-lg-4 control-label">Cutoff Hour</label>
                            <div class="col-sm-8 col-md-8 col-lg-8">
                              {{Form::select('cutoffHour', array(
                                '0' => '12 AM', '1' => '1 AM', '2' => '2 AM', '3' => '3 AM', '4' => '4 AM', '5' => '5 AM', '6' => '6 AM',
                                '7' => '7 AM', '8' => '8 AM', '9' => '9 AM', '10' => '10 AM', '11' => '11 AM', '12' => '12 PM',
                                '13' => '1 PM', '14' => '2 PM', '15' => '3 PM', '16' => '4 PM', '17' => '5 PM', '18' => '6 PM',
                                '19' => '7 PM', '20' => '8 PM', '21' => '9 PM', '22' => '10 PM', '23' => '11 PM'
                              ), $settings['cutoffHour'])}}
                              <span class="help-block text-left">Text messages will not be sent earlier than this time each day. Typically set to an hour before opening time.</span>
                              <!--input type="number" style="width: 50%" class="form-control" id="heatsPriorToSend" name="heatsPriorToSend" value="{{$settings['heatsPriorToSend']}}">
                              <span class="help-block text-left">Text reminders will be sent this many heats before.</span-->
                            </div>
                          </div>
                        @endif
                    </div>

                    <div class="col-sm-6">
                      @if(isset($settings['heatsPriorToSend']))
                        <div class="form-group">
                          <label class="col-sm-4 col-md-4 col-lg-4 control-label">Send X Races Prior</label>
                          <div class="col-sm-8 col-md-8 col-lg-8">
                            <input type="number" style="width: 50%" class="form-control" id="heatsPriorToSend" name="heatsPriorToSend" value="{{$settings['heatsPriorToSend']}}">
                            <span class="help-block text-left">Text reminders will be sent this many heats before.</span>
                          </div>
                        </div>
                      @endif

                      @if(isset($settings['message']))
                        <div class="form-group">
                          <label class="col-sm-4 col-md-4 col-lg-4 control-label">Message</label>
                          <div class="col-sm-8 col-md-8 col-lg-8">
                            <textarea style="width: 100%" id="message" name="message">{{$settings['message']}}</textarea>
                            <span class="help-block text-left">This is the message that will be sent.</span>
                          </div>
                        </div>
                      @endif

                      @if(isset($settings['provider']))
                        <div class="form-group">
                          <label class="col-sm-4 col-md-4 col-lg-4 control-label">Provider</label>
                          <div class="col-sm-8 col-md-8 col-lg-8">
                            {{Form::select('provider', $supportedProviders, $settings['provider'], array('id' => 'providerDropdown'))}}
                            <span class="help-block text-left">
                              The service provider for text messaging.
                            </span>
                          </div>
                        </div>
                      @endif
                    </div>

                    <div class="col-sm-12">
                      <div class="form-actions">
                        {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                      </div>
                    </div>
                  @endif
                @endif
              </div>
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
              <h5>Provider Settings</h5>
            </div>

            <div class="widget-content">
              <div class="alert alert-info" id="providerChangeAlert" style="display:none;">
                The provider was changed. Save changes to view settings for the new provider.
              </div>

              <div class="row" id="providerForm">
                <div class="col-sm-6">
                  @foreach($firstColumnProviderOptions as $option)
                    @if(isset($settings[$option]))
                      <?php
                        $detail;
                        foreach($providerOptionDetails as $d){
                          if ($d['key'] === $option){
                            $detail = $d;
                          }
                        }
                      ?>
                      <div class="form-group">
                        <label class="col-sm-4 col-md-4 col-lg-4 control-label">{{$detail['label']}}</label>
                        <div class="col-sm-8 col-md-8 col-lg-8">
                          <input type="text" class="form-control" id="{{$detail['key']}}" name="{{$detail['key']}}" value="{{$settings[$detail['key']]}}">
                          <span class="help-block text-left">
                            {{$detail['tip']}}
                          </span>
                        </div>
                      </div>
                    @endif
                  @endforeach
                </div>

                <div class="col-sm-6">
                  @foreach($secondColumnProviderOptions as $option)
                    @if(isset($settings[$option]))
                      <?php
                        $detail;
                        foreach($providerOptionDetails as $d){
                          if ($d['key'] === $option){
                            $detail = $d;
                          }
                        }
                      ?>
                      <div class="form-group">
                        <label class="col-sm-4 col-md-4 col-lg-4 control-label">{{$detail['label']}}</label>
                        <div class="col-sm-8 col-md-8 col-lg-8">
                          <input type="text" class="form-control" id="{{$detail['key']}}" name="{{$detail['key']}}" value="{{$settings[$detail['key']]}}">
                          <span class="help-block text-left">
                            {{$detail['tip']}}
                          </span>
                        </div>
                      </div>
                    @endif
                  @endforeach
                </div>

                <div class="col-sm-12">
                  <div class="form-actions">
                    {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                  </div>
                </div>
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
  {{ HTML::script('js/bootstrap-tokenfield.min.js') }}
  <script>
    $('#providerDropdown').change(function(e){
      if (e.val !== '{{$settings["provider"]}}'){
        $('#providerForm').css('display', 'none');
          $('#providerChangeAlert').css('display', 'block');
      } else {
        $('#providerForm').css('display', 'block');
          $('#providerChangeAlert').css('display', 'none');
      }
    });

    $(document).ready(function () {
        window.setTimeout(function() {
          $(".fadeAway").fadeTo(500, 0).slideUp(500, function(){
              $(this).remove();
          });
        }, 5000);

        $("#from").tokenfield({ createTokensOnBlur: true });
    });

  </script>
@stop
<!-- END JAVASCRIPT INCLUDES -->
