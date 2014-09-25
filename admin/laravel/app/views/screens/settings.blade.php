@extends('master')

@section('title')
Speed Screen Settings
@stop

@section('pageHeader')
Speed Screen Settings
@stop

@section('breadcrumb')
    <a href="dashboard" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
    <a href="#" class="current">Speed Screen Settings</a>
@stop

@section('content')
		<div class="container-fluid">
      <div class="col-xs-12">
        @if (Session::has("message"))
        <div class="alert alert-success">
            <p>{{ Session::get("message") }}</p>
        </div>
        @endif
         @if (Session::has("error"))
        <div class="alert alert-danger">
            <p>{{ Session::get("error") }}</p>
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
                <div class="form-actions">
                    {{ Form::submit('Upload', array('class' => 'btn btn-info')) }}
                </div>
            {{ Form::close() }}
          </div>
        </div>						
      </div>
    </div>
@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent <!-- This includes the original parent's javascript -->
@stop