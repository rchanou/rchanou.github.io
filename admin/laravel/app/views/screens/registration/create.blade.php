@extends('master')

@section('title')
Create a Registration Application
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
Create a Registration Application
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#">Registration</a>
<a href="#" class="current">Create a Registration Application</a>
@stop

@section('content')
  {{ Form::open(array('files'=> false, 'class' => 'form-horizontal')) }}
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
              <h5>Create a Registraton Application</h5>
            </div>

            <div class="widget-content">
              <div class="row">
                 <div class="col-sm-12">
                   <div class="alert alert-info">
                      <p>Use this section to create a standalone Windows EXE that you may run from a PC or Laptop to turn it into a Registration Kiosk.</p>
                  </div>
                   <div class="form-group">
                     <label class="col-sm-4 col-md-4 col-lg-4 control-label">Camera Type</label>
                     <div class="col-sm-8 col-md-8 col-lg-8">
                       {{Form::select('cameraDropdown', array(
                       	'nocamera' => 'No Camera',
                        'localcam' => 'Local Camera',
                        'reg1' => 'Reg1',
                        'reg2' => 'Reg2',
                        'reg3' => 'Reg3',
                        'reg4' => 'Reg4',
                        'reg5' => 'Reg5',
                        'reg6' => 'Reg6',
                        'reg7' => 'Reg7',
                        'reg8' => 'Reg8',
                        'reg9' => 'Reg9',
                        'reg10' => 'Reg10'
                        ), 'localcam', array('id' => 'cameraDropdown'))}}
                       <span class="help-block text-left">
                       <strong>No Camera:</strong> Do not use a camera.<br/>
                       <strong>Local Camera:</strong> Built-in or USB camera attached to the Computer.<br/>
                       <strong>Reg#:</strong> Uses an IP-based camera. Ex. Setup in the <a href="/sp_admin" target="_blank">SP_Admin</a> "Control Panel > Reg1" under the "url" setting. "camip" is set at the end of the URL as "?camip=ip.add.re.ss/path/to/image.jpg".
                           <br/><strong><em>If using an IP-based camera, we may need to add the name of the image ("image.url") to a whitelist in the API. If so, contact the devs.</em></strong>
                       </span>
                     </div>
                   </div>
                 </div>
                <div class="col-sm-12">
                  <div class="form-actions">
                    {{ Form::submit('Create Registration Application', array('id' => 'createApplicationButton', 'class' => 'btn btn-info')) }}
                  </div>
                </div>
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
		$('#createApplicationButton').click(function(e) {
			e.preventDefault();			

			var cameraType = $('#cameraDropdown').val();
			var regUrl = 'http://'+location.hostname+(location.port ? ':' + location.port: '') + '/cs-registration/';
			if(cameraType === 'nocamera') {
				// Do nothing
			} else if(cameraType === 'localcam') {
				regUrl += '?localcam=1';
			} else {
				regUrl += '?terminal=' + cameraType;
			}
			regUrl = encodeURIComponent(regUrl);

			var createUrl = '/admin/registration/deploy?targetUrl=' + regUrl + '&appName=registration-' + cameraType;
			window.location = createUrl;
		});

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