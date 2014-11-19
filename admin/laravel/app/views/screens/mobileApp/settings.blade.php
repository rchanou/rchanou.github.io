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
Mobile App Settings (Under Construction)
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#" class="current">Mobile App</a>
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
            
            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                <h5>General Settings</h5>
              </div>
              <div class="widget-content">
                  <div class="row">
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
    </div>
{{ Form::close() }}
@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent

@stop
<!-- END JAVASCRIPT INCLUDES -->