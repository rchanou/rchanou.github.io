@extends('master')

@section('title')
Translations
@stop

@section('css_includes')
    @parent
    <style>
        .currentCulture {
            background-color: #dff0d8 !important;
        }
        .select2-choice {
            height: 33px !important; line-height: 33px !important;
        }
    </style>
@stop

@section('pageHeader')
Translations
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#">Registration</a>
<a href="#" class="current">Translations</a>
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
                          <h5>Dropdown Languages</h5>
                      </div>
                      <div>
                          <div class="widget-content">
                              <div class="row">
                                  @if(isset($enabledCultures))
                                  {{ Form::open(array('action'=>'RegistrationController@updateDropdownLanguages','files' => false, 'class' => 'form-horizontal')) }}
                                  <div class="col-sm-4">
                                      <table class="table table-bordered table-striped table-hover text-center">
                                          <thead>
                                          <tr>
                                              <th>Culture</th>
                                              <th style="width: 50%">Enabled?</th>
                                          </tr>
                                          </thead>
                                          <tbody>
                                      @foreach($supportedCulturesSplit[0] as $cultureKey => $cultureName)
                                            <tr>
                                                <td @if($cultureKey == $currentCulture)class="currentCulture"@endif>{{$cultureName}}</td>
                                                <td @if($cultureKey == $currentCulture)class="currentCulture"@endif><input type="checkbox" id="{{$cultureKey}}" name="{{$cultureKey}}" {{isset($enabledCultures[$cultureKey]) ? 'checked' : ''}} {{$cultureKey == 'en-US' ? 'checked disabled' : ''}}></td>
                                            </tr>
                                      @endforeach
                                          </tbody>
                                      </table>
                                  </div>
                                  <div class="col-sm-4">
                                      <table class="table table-bordered table-striped table-hover text-center">
                                          <thead>
                                          <tr>
                                              <th>Culture</th>
                                              <th style="width: 50%">Enabled?</th>
                                          </tr>
                                          </thead>
                                          <tbody>
                                          @foreach($supportedCulturesSplit[1] as $cultureKey => $cultureName)
                                              <tr>
                                                  <td @if($cultureKey == $currentCulture)class="currentCulture"@endif>{{$cultureName}}</td>
                                                  <td @if($cultureKey == $currentCulture)class="currentCulture"@endif><input type="checkbox" id="{{$cultureKey}}" name="{{$cultureKey}}" {{isset($enabledCultures[$cultureKey]) ? 'checked' : ''}} {{$cultureKey == 'en-US' ? 'checked disabled' : ''}}></td>
                                              </tr>
                                          @endforeach
                                          </tbody>
                                      </table>
                                  </div>
                                  <div class="col-sm-4">
                                      <table class="table table-bordered table-striped table-hover text-center">
                                          <thead>
                                          <tr>
                                              <th>Culture</th>
                                              <th style="width: 50%">Enabled?</th>
                                          </tr>
                                          </thead>
                                          <tbody>
                                          @foreach($supportedCulturesSplit[2] as $cultureKey => $cultureName)
                                              <tr>
                                                  <td @if($cultureKey == $currentCulture)class="currentCulture"@endif>{{$cultureName}}</td>
                                                  <td @if($cultureKey == $currentCulture)class="currentCulture"@endif><input type="checkbox" id="{{$cultureKey}}" name="{{$cultureKey}}" {{isset($enabledCultures[$cultureKey]) ? 'checked' : ''}} {{$cultureKey == 'en-US' ? 'checked disabled' : ''}}></td>
                                              </tr>
                                          @endforeach
                                          </tbody>
                                      </table>
                                  </div>
                                  <div class="col-sm-12">
                                      <div class="alert alert-info">
                                          If enabled above, languages will appear as options in a dropdown menu on the top-right of the Registration page.
                                          Translations must be defined below for any checked language before they will display. Missing translations default to English (US).<p/>
                                          <p/>The culture highlighted in green denotes the default culture shown at registration.
                                      </div>
                                  </div>
                                  {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                                  {{Form::close()}}
                                  @else
                                      <div class="alert alert-warning">
                                      This feature is not enabled. Please contact <a href="mailto:support@clubspeed.com">Club Speed</a> for activation and training.
                                      </div>
                                  @endif
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
             <div class="col-sm-12">
                 <div class="widget-box">
                        <div class="widget-title">
                              <span class="icon">
                                <i class="fa fa-align-justify"></i>
                              </span>
                              <h5>Translations</h5>
                        </div>
                        <div>
                            <div class="widget-content">
                                <div class="row">
                                    <div class="col-sm-12">
                                    <label>Culture: </label>
                                    <select name="cultureDropdown" id="cultureDropdown">
                                    @foreach($supportedCultures as $cultureKey => $cultureName)
                                        <option value="{{$cultureKey}}" @if($cultureKey == $currentCulture)selected="selected"@endif>
                                        {{$cultureName}} @if($cultureKey == $currentCulture)<em>(default)</em>@endif
                                        </option>
                                    @endforeach
                                    </select>
                                    <a href="" class="btn btn-success" id="setCultureButton" data-culture="{{$currentCulture}}">Set this culture to default</a>
                                    <a href="javascript:" class="btn btn-default" id="setCultureButtonDisabled" style="height: 30px; line-height: 16px; margin-left: 10px;" data-culture="{{$currentCulture}}">Currently your default</a>
                                    <p/>
                                    @if(isset($translations['en-US']))
                                        @foreach($supportedCultures as $cultureKey => $cultureName)
                                        {{ Form::open(array('action'=>'RegistrationController@updateTranslations','files' => false, 'class' => 'form-horizontal')) }}
                                            <div id="{{$cultureKey}}_box" class="translationsBox">
                                                <table class="table table-bordered table-striped table-hover text-center">
                                                    <thead>
                                                        <tr>
                                                            <th>String</th>
                                                            <th>Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                 @foreach($translations['en-US'] as $translationsKey => $translationsValue)
                                                     <tr>
                                                        <td class="col-sm-3 col-xs-3"><label>{{$translationsKey}}</label></td>
                                                        <td class="col-sm-9 col-xs-9">
                                                        <input type="text"
                                                        class="text-center wideInput"
                                                        name="trans[id_{{isset($translations[$cultureKey][$translationsKey]['id']) ? $translations[$cultureKey][$translationsKey]['id'] : 'new_' . $translationsKey}}]"
                                                        placeholder="{{$translationsValue['value']}}"
                                                        value="{{isset($translations[$cultureKey][$translationsKey]['value']) ? $translations[$cultureKey][$translationsKey]['value'] : ""}}">
                                                        </td>
                                                     </tr>
                                                 @endforeach
                                                        </tbody>
                                                    </table>
                                             <input type="hidden" name="cultureKey" value="{{$cultureKey}}">
                                            {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                                            {{ Form::close() }}
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="alert alert-warning">
                                            Unable to reach the Translations database for the default language. If the issue persists, please contact support.
                                        </div>
                                    @endif
                                    </div>
                                </div>
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
            $('.translationsBox').hide(); //Hide all payment options boxes

            //Show the currently selected box
            var selectedCulture = $( "#cultureDropdown option:selected" ).val();
            $('#' + selectedCulture + '_box').show();

            var setCultureButton = $('#setCultureButton');
            var setCultureButtonDisabled = $('#setCultureButtonDisabled');
            setCultureButton.data('culture',selectedCulture);
            setCultureButton.attr('href',window.location.href + '/update/culture/' + selectedCulture)

            if (selectedCulture != currentCulture)
            {
                setCultureButton.show();
                setCultureButtonDisabled.hide();
            }
            else
            {
                setCultureButton.hide();
                setCultureButtonDisabled.show();
            }
        });
    });
    </script>
@stop
<!-- END JAVASCRIPT INCLUDES -->