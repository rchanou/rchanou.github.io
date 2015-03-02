@extends('master')

@section('title')
After Race Posting Settings
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
After Race Posting Settings
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#">Facebook</a>
<a href="#" class="current">After Race Posting Settings</a>
@stop

@section('content')
{{ Form::open(array('action'=>'FacebookController@updateAfterRaceSettings','files'=>false, 'class' => 'form-horizontal')) }}

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
                <h5>After Race Posting Settings</h5>
              </div>
              <div class="widget-content">
                  <div class="row">
                      @if(!isset($afterRaceSettings) || count($afterRaceSettings) == 0)
                        <div class="col-sm-12">
                          <div class="alert alert-warning">
                            <p>Unable to access after-race posting settings. Please try again later, or contact <a href="mailto:support@clubspeed.com">support@clubspeed.com</a> if the issue persists.</p>
                          </div>
                        </div>
                      @else
                        @if (!(isset($afterRaceSettings['featureIsEnabled']) && $afterRaceSettings['featureIsEnabled']))
                            <div class="col-sm-12">
                              <div class="alert alert-warning">
                                This feature is not currently installed. Please contact <a href="mailto:support@clubspeed.com">support@clubspeed.com</a> for installation and training.
                              </div>
                            </div>
                        @endif
                        @if ((isset($afterRaceSettings['featureIsEnabled']) && $afterRaceSettings['featureIsEnabled']) || Session::get('user') === 'support')
                            <div class="col-sm-6">
                                @if(Session::get('user') === 'support')
                                <div class="form-group">
                                  <label class="col-sm-4 col-md-4 col-lg-4 control-label"><img src="{{asset('img/support_only.png')}}" style="cursor: help" title="This setting is only visible to Club Speed support staff."> Enable Feature for Track</label>
                                  <div class="col-sm-8 col-md-8 col-lg-8">
                                    <input type="checkbox" id="featureIsEnabled" name="featureIsEnabled" {{$isChecked['featureIsEnabled']}}>
                                    <span class="help-block text-left">Enable once the Facebook service is installed and configured.</span>
                                  </div>
                                </div>
                                @endif
                                @if(isset($afterRaceSettings['postingIsEnabled']))
                                  <div class="form-group">
                                    <label class="col-sm-4 col-md-4 col-lg-4 control-label">Enable Facebook Posting After Race</label>
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                      <input type="checkbox" id="postingIsEnabled" name="postingIsEnabled" {{$isChecked['postingIsEnabled']}}>
                                      <span class="help-block text-left">If checked, Facebook postings are made immediately following a race.</span>
                                    </div>
                                  </div>
                                @endif

                                @if(isset($afterRaceSettings['link']))
                                  <div class="form-group">
                                    <label class="col-sm-4 col-md-4 col-lg-4 control-label">Link</label>
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                      <input type="text" style="width: 100%" id="link" name="link" value="{{$afterRaceSettings['link']}}">
                                      <span class="help-block text-left">Controls where the Facebook posting links to.</span>
                                    </div>
                                  </div>
                                @endif

                                @if(isset($afterRaceSettings['message']))
                                 <div class="form-group">
                                   <label class="col-sm-4 col-md-4 col-lg-4 control-label">Message</label>
                                   <div class="col-sm-8 col-md-8 col-lg-8">
                                      <input type="text" style="width: 100%" id="message" name="message" value="{{$afterRaceSettings['message']}}">
                                      <span class="help-block text-left">This is the message that will be shown at the top of the Facebook post.</span>
                                    </div>
                                  </div>
                                @endif

                                @if(isset($afterRaceSettings['photoUrl']))
                                  <div class="form-group">
                                    <label class="col-sm-4 col-md-4 col-lg-4 control-label">Photo URL</label>
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                      <input type="text" style="width: 100%" id="photoUrl" name="photoUrl" value="{{$afterRaceSettings['photoUrl']}}">
                                      <span class="help-block text-left">Optional, set this to the URL of the photo that will be shown.</span>
                                    </div>
                                  </div>
                                @endif
                                @if(isset($afterRaceSettings['name']))
                                  <div class="form-group">
                                    <label class="col-sm-4 col-md-4 col-lg-4 control-label">Name</label>
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                      <input type="text" style="width: 100%" id="name" name="name" value="{{$afterRaceSettings['name']}}">
                                      <span class="help-block text-left">Optional, set a name for the photo.</span>
                                    </div>
                                  </div>
                                @endif

                                @if(isset($afterRaceSettings['description']))
                                  <div class="form-group">
                                    <label class="col-sm-4 col-md-4 col-lg-4 control-label">Description</label>
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                      <input type="text" style="width: 100%" id="description" name="description" value="{{$afterRaceSettings['description']}}">
                                      <span class="help-block text-left">Optional, set a description for the photo.</span>
                                    </div>
                                  </div>
                                @endif

                                @if(isset($afterRaceSettings['caption']))
                                  <div class="form-group">
                                    <label class="col-sm-4 col-md-4 col-lg-4 control-label">Caption</label>
                                    <div class="col-sm-8 col-md-8 col-lg-8">
                                      <input type="text" style="width: 100%" id="caption" name="caption" value="{{$afterRaceSettings['caption']}}">
                                      <span class="help-block text-left">Optional, set a caption for the photo.</span>
                                    </div>
                                  </div>
                                @endif
                            </div>

                            <div class="col-sm-6">
                              <label class="control-label">Example</label><br/>
                              <img width='100%' src="https://{{$_SERVER['HTTP_HOST']}}/admin/img/facebook-after-race-sample.png"></img>

                              <div class="form-group">
                                <div class="alert alert-info">
                                  The following can be inserted into the Facebook fields:<br/><br/>
                                  <b>Finishing Position (1st, 2nd, etc):</b> &#123;&#123;ordinalFinishPosition&#125;&#125;<br/>
                                  <b>Finish Position (1, 2, etc):</b> &#123;&#123;finishPosition&#125;&#125;<br/>
                                  <b>Heat ID:</b> &#123;&#123;heatId&#125;&#125;<br/>
                                  <b>Customer ID:</b> &#123;&#123;customerId&#125;&#125;<br/>
                                  <b>Heat Type:</b> &#123;&#123;heatType&#125;&#125;
                                </div>
                              </div>
                            </div class="col-sm-6">

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
    });

</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->
