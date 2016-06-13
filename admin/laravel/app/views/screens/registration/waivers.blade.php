@extends('master')

@section('title')
Waivers
@stop

@section('css_includes')
    @parent
    <style>
        .select2-choice {
            height: 33px !important; line-height: 33px !important;
        }
    </style>
@stop

@section('pageHeader')
Waivers
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#">Registration</a>
<a href="#" class="current">Waivers</a>
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
             <div class="col-sm-12">
                 <div class="widget-box">
                        <div class="widget-title">
                              <span class="icon">
                                <i class="fa fa-align-justify"></i>
                              </span>
                              <h5>Waivers</h5>
                        </div>
                        <div>
                            <div class="widget-content">
                                @if($useNewWaivers)
                                <div class="row">
                                    <div class="col-sm-12">
                                    <label>Culture: </label>
                                    <select style="min-width: 200px;" name="cultureDropdown" id="cultureDropdown">
                                    @foreach($supportedCultures as $cultureKey => $cultureName)
                                        <option value="{{$cultureKey}}" @if($cultureKey == $currentCulture)selected="selected"@endif>
                                        {{$cultureName}} @if($cultureKey == $currentCulture)<em>(default)</em>@endif
                                        </option>
                                    @endforeach
                                    </select>
                                    <p/>
                                    @if(isset($translations['en-US']))
                                        @foreach($supportedCultures as $cultureKey => $cultureName)
                                            <div class="row translationsBox" id="{{$cultureKey}}_box" style="display: none;">
                                                {{ Form::open(array('action'=>'RegistrationController@updateWaivers','files' => false, 'class' => 'form-horizontal')) }}
                                                @foreach($translations['en-US'] as $translationsKey => $translationsValue)
                                                    @if($translationsKey == 'str_WaiverAdult' || $translationsKey == 'str_WaiverChild')
                                                        <div class="col-sm-12">
                                                            <div class="form-group">
                                                                <label class="col-lg-12">
                                                                    @if($translationsKey == 'str_WaiverAdult')
                                                                        Adult Waiver
                                                                    @else
                                                                        Child Waiver
                                                                    @endif
                                                                </label>
                                                                <div class="col-lg-12"><textarea rows="20" class="wideInput" name="trans[id_{{isset($translations[$cultureKey][$translationsKey]['id']) ? $translations[$cultureKey][$translationsKey]['id'] : 'new_' . $translationsKey}}]">{{isset($translations[$cultureKey][$translationsKey]['value']) ? $translations[$cultureKey][$translationsKey]['value'] : ""}}</textarea></div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                                 <input type="hidden" name="cultureKey" value="{{$cultureKey}}">
                                                {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                                                {{ Form::close() }}
                                            </div>
                                            @endforeach
                                    @else
                                        <div class="alert alert-warning">
                                            Unable to reach the Translations database for the default language. If the issue persists, please contact <a href="mailto:support@clubspeed.com">support@clubspeed.com</a>.
                                        </div>
                                    @endif
                                    </div>
                                </div>
                                @elseif($useNewWaivers === false)
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="alert alert-warning">
                                            The <strong>Use New Waivers</strong> setting is disabled.<p/><p/>
                                            You can access your current waivers via <a href="{{'http://' . $_SERVER['HTTP_HOST'] . '/sp_admin'}}" target="_blank">sp_admin</a>.<p/><p/>
                                            To switch to the new waiver system and edit them here, enable <strong>Use New Waivers</strong> in the {{link_to('/registration/settings','Settings')}} page.
                                        </div>
                                    </div>
                                </div>
                                @elseif($useNewWaivers === null)
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="alert alert-warning">
                                           Your server needs to have the new waivers installed. Please contact <a href="mailto:support@clubspeed.com">support@clubspeed.com</a> to request this feature.<p/><p/>
                                            In the meanwhile, you can access your current waivers via <a href="{{'http://' . $_SERVER['HTTP_HOST'] . '/sp_admin'}}" target="_blank">sp_admin</a>.
                                        </div>
                                    </div>
                                </div>
                                @else
                                @endif
                            </div>
                        </div>
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
    $(document).ready(function(){
        $('.translationsBox').hide(); //Hide all payment options boxes
        var selectedCulture = $( "#cultureDropdown option:selected" ).val();
        var currentCulture = '{{$currentCulture}}';
        $('#' + selectedCulture + '_box').show();

        $('#cultureDropdown').change(function()
        {
            $('.translationsBox').hide(); //Hide all boxes

            //Show the currently selected box
            var selectedCulture = $( "#cultureDropdown option:selected" ).val();
            $('#' + selectedCulture + '_box').show();
        });
    });
    </script>
@stop
<!-- END JAVASCRIPT INCLUDES -->