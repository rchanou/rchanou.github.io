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
<a href="#" class="current">Registrations</a>
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
                      <div class="col-sm-6">
                        <div class="alert alert-info">More settings are coming soon! In the meanwhile, please proceed to <a href="{{'http://' . $_SERVER['HTTP_HOST'] . '/sp_admin'}}" target="_blank">sp_admin</a> to edit the other registration settings.</div>
                      </div>
                      <div class="col-sm-12">
                           <div class="form-actions" style="margin-bottom: 10px;">
                               {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                           </div>
                      </div>
                      @else
                      <div class="alert alert-info">No custom settings are currently available to edit on this page. In the meanwhile, please proceed to <a href="{{'http://' . $_SERVER['HTTP_HOST'] . '/sp_admin'}}" target="_blank">sp_admin</a> to edit your registration settings.</div>
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

        window.setTimeout(function() {
          $(".fadeAway").fadeTo(500, 0).slideUp(500, function(){
              $(this).remove();
          });
        }, 5000);

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