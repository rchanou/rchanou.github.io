@extends('master')

@section('title')
Speed Screen Settings
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
Speed Screen Settings
@stop

@section('breadcrumb')
    <a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
    <a href="#" class="current">Speed Screen Settings</a>
@stop

@section('content')
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
            <h5>Speed Screen Background</h5>
          </div>
          <div class="widget-content nopadding">
              <div class="row">
            {{ Form::open(array('url'=>'channelSettingsSubmit','files'=>true, 'class' => 'form-horizontal')) }}
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
                  <div class="col-sm-12">
                    <div class="form-actions" style="margin-bottom: 10px;">
                        {{ Form::submit('Upload', array('class' => 'btn btn-info')) }}
                    </div>
                  </div>
            {{ Form::close() }}
              </div>
          </div>
        </div>
      </div>
     </div>
     <div class="row" style="display: none;">
      <div class="col-xs-12">
        <div class="widget-box">
            <div class="widget-title">
            <span class="icon">
              <i class="fa fa-align-justify"></i>
            </span>
                <h5>General Settings</h5>
            </div>
            <div class="widget-content nopadding">
                <div class="row">
                    @if(isset($speedscreenSettings) && count($speedscreenSettings) > 0)
                        {{ Form::open(array('url'=>'speedScreen/updateSettings','files'=>false, 'class' => 'form-horizontal')) }} <!-- TODO: Update this -->
                        <div class="col-sm-6">
                            @if(isset($speedscreenSettings['defaultLocale']))
                            <div class="form-group">
                                <label class="col-sm-4 col-md-4 col-lg-4 control-label">Default Locale</label>
                                <div class="col-sm-8 col-md-8 col-lg-8">
                                    {{Form::select('defaultLocale',$supportedLocales,$speedscreenSettings['defaultLocale'])}}
                                <span class="help-block text-left">
                                    The default language for all Speed Screens.
                                    May be overridden via the "locale" URL parameter.
                                    {{link_to('/speedScreen/translations','Translations')}} must be set for the chosen locale, or they will default to English.
                                </span>
                                </div>
                            </div>
                            @endif
                            @if(isset($speedscreenSettings['apiDriver']) && $user === 'support')
                                <div class="form-group">
                                    <label class="col-sm-4 col-md-4 col-lg-4 control-label">
                                        <img src="{{asset('img/support_only.png')}}" style="cursor: help" title="This setting is only visible to Club Speed support staff.">
                                        API Driver
                                    </label>
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                        <input type="text" id="apiDriver" name="apiDriver" value="{{$speedscreenSettings['apiDriver']}}">
                            <span class="help-block text-left">
                                Which driver for the Speed Screen to use. If 'polling', it will poll the API on a timer. If 'event' (not yet implemented), it'll use an event-based system.
                            </span>
                                    </div>
                                </div>
                            @endif
                            @if(isset($speedscreenSettings['channelUpdateFrequencyMs']) && $user === 'support')
                                <div class="form-group">
                                    <label class="col-sm-4 col-md-4 col-lg-4 control-label">
                                        <img src="{{asset('img/support_only.png')}}" style="cursor: help" title="This setting is only visible to Club Speed support staff.">
                                        Channel Update Frequency (Milliseconds)
                                    </label>
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                        <input type="text" id="channelUpdateFrequencyMs" name="channelUpdateFrequencyMs" value="{{$speedscreenSettings['channelUpdateFrequencyMs']}}">
                            <span class="help-block text-left">
                                How often the Speed Screen should check for a channel update, in milliseconds. If it detects a change, it will auto-restart.
                            </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="col-sm-6">
                            @if(isset($speedscreenSettings['racesPollingRateMs']) && $user === 'support')
                                <div class="form-group">
                                    <label class="col-sm-4 col-md-4 col-lg-4 control-label">
                                        <img src="{{asset('img/support_only.png')}}" style="cursor: help" title="This setting is only visible to Club Speed support staff.">
                                        Races Polling Frequency (Milliseconds)
                                    </label>
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                        <input type="text" id="racesPollingRateMs" name="racesPollingRateMs" value="{{$speedscreenSettings['racesPollingRateMs']}}">
                            <span class="help-block text-left">
                               How often the Speed Screen should check for races happening on any track that it needs to watch, in milliseconds. This is separate from the Scoreboard slide polling, and can be slower. It's used primarily to detect when a race has started or ended.
                            </span>
                                    </div>
                                </div>
                            @endif
                            @if(isset($speedscreenSettings['timeUntilRestartOnErrorMs']) && $user === 'support')
                                <div class="form-group">
                                    <label class="col-sm-4 col-md-4 col-lg-4 control-label">
                                        <img src="{{asset('img/support_only.png')}}" style="cursor: help" title="This setting is only visible to Club Speed support staff.">
                                        Time Until Restarting After An Error (Milliseconds)
                                    </label>
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                        <input type="text" id="timeUntilRestartOnErrorMs" name="timeUntilRestartOnErrorMs" value="{{$speedscreenSettings['timeUntilRestartOnErrorMs']}}">
                            <span class="help-block text-left">
                               How many milliseconds the Speed Screen should wait before restarting upon encountering any error.
                            </span>
                                    </div>
                                </div>
                            @endif
                            @if(isset($speedscreenSettings['channelSource']) && $user === 'support')
                                <div class="form-group">
                                    <label class="col-sm-4 col-md-4 col-lg-4 control-label">
                                        <img src="{{asset('img/support_only.png')}}" style="cursor: help" title="This setting is only visible to Club Speed support staff.">
                                        Channel Source
                                    </label>
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                        <input type="text" id="channelSource" name="channelSource" value="{{$speedscreenSettings['channelSource']}}">
                        <span class="help-block text-left">
                           Which channels the Speed Screen should use. Can be "old" (the original channels in sp_admin) or "new" (the new, separate set of channels).
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
                    @else
                        <div class="col-sm-12">
                            <div class="alert alert-warning" style="margin: 10px;">
                                This feature is not currently installed. Please contact <a href="mailto:support@clubspeed.com">support@clubspeed.com</a> for installation and training.
                            </div>
                        </div>
                    @endif
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
@parent <!-- This includes the original parent's javascript -->
@stop