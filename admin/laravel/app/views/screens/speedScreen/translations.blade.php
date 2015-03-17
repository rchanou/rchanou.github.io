@extends('master')

@section('title')
Translations
@stop

@section('css_includes')
    @parent

@stop

@section('pageHeader')
Translations
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#">Speed Screens</a>
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
                <!-- Translations for Speedscreen -->
                 <div class="col-sm-12">
                     <div class="widget-box">
                            <div class="widget-title">
                                  <span class="icon">
                                    <i class="fa fa-align-justify"></i>
                                  </span>
                                  <h5>Translations - Speedscreen</h5>
                            </div>
                            <div>
                                <div class="widget-content">
                                    <div class="row">
                                        <div class="col-sm-12">
                                        <label>Culture: </label>
                                        <select name="cultureDropdown" id="cultureDropdown">
                                        @foreach($supportedCultures as $cultureKey => $cultureName)
                                            <option value="{{$cultureKey}}" @if($cultureKey == $currentCulture)selected="selected"@endif>
                                            {{$cultureName}}
                                            </option>
                                        @endforeach
                                        </select>
                                        <p/>
                                        @if(isset($translations['en-US']))
                                            @foreach($supportedCultures as $cultureKey => $cultureName)
                                            {{ Form::open(array('action'=>'ChannelController@updateTranslations','files' => false, 'class' => 'form-horizontal')) }}
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
                                                    <input type="hidden" name="namespace" value="Speedscreen">
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

                 <!-- Translations for Scoreboard slide -->

                  <div class="col-sm-12">
                      <div class="widget-box">
                          <div class="widget-title">
                                  <span class="icon">
                                    <i class="fa fa-align-justify"></i>
                                  </span>
                              <h5>Translations - Scoreboard Slide</h5>
                          </div>
                          <div>
                              <div class="widget-content">
                                  <div class="row">
                                      <div class="col-sm-12">
                                          <label>Culture: </label>
                                          <select name="cultureDropdown_scoreboard" id="cultureDropdown_scoreboard">
                                              @foreach($supportedCultures as $cultureKey => $cultureName)
                                                  <option value="{{$cultureKey}}" @if($cultureKey == $currentCulture)selected="selected"@endif>
                                                      {{$cultureName}}
                                                  </option>
                                              @endforeach
                                          </select>
                                          <p/>
                                          @if(isset($translations_scoreboard['en-US']))
                                              @foreach($supportedCultures as $cultureKey => $cultureName)
                                                  {{ Form::open(array('action'=>'ChannelController@updateTranslations','files' => false, 'class' => 'form-horizontal')) }}
                                                  <div id="{{$cultureKey}}_box_scoreboard" class="translationsBox_scoreboard">
                                                      <table class="table table-bordered table-striped table-hover text-center">
                                                          <thead>
                                                          <tr>
                                                              <th>String</th>
                                                              <th>Value</th>
                                                          </tr>
                                                          </thead>
                                                          <tbody>
                                                          @foreach($translations_scoreboard['en-US'] as $translationsKey => $translationsValue)
                                                              <tr>
                                                                  <td class="col-sm-3 col-xs-3"><label>{{$translationsKey}}</label></td>
                                                                  <td class="col-sm-9 col-xs-9">
                                                                      <input type="text"
                                                                             class="text-center wideInput"
                                                                             name="trans[id_{{isset($translations_scoreboard[$cultureKey][$translationsKey]['id']) ? $translations_scoreboard[$cultureKey][$translationsKey]['id'] : 'new_' . $translationsKey}}]"
                                                                             placeholder="{{$translationsValue['value']}}"
                                                                             value="{{isset($translations_scoreboard[$cultureKey][$translationsKey]['value']) ? $translations_scoreboard[$cultureKey][$translationsKey]['value'] : ""}}">
                                                                  </td>
                                                              </tr>
                                                          @endforeach
                                                          </tbody>
                                                      </table>
                                                      <input type="hidden" name="cultureKey" value="{{$cultureKey}}">
                                                      <input type="hidden" name="namespace" value="Scoreboard">
                                                      {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                                                      {{ Form::close() }}
                                                  </div>
                                              @endforeach
                                          @else
                                              <div class="alert alert-warning">
                                                  Unable to reach the Translations database for the default language for the Scoreboard slide. If the issue persists, please contact support.
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

        //Speedscreen translations
        $('.translationsBox').hide(); //Hide all translations boxes
        var selectedCulture = $( "#cultureDropdown option:selected" ).val();
        var currentCulture = '{{$currentCulture}}';
        $('#' + selectedCulture + '_box').show();

        $('#cultureDropdown').change(function()
        {
            $('.translationsBox').hide(); //Hide all translations options boxes

            //Show the currently selected box
            var selectedCulture = $( "#cultureDropdown option:selected" ).val();
            $('#' + selectedCulture + '_box').show();
        });

        //Scoreboard slide translations
        $('.translationsBox_scoreboard').hide(); //Hide all translations boxes
        var selectedCulture_scoreboard = $( "#cultureDropdown_scoreboard option:selected" ).val();
        var currentCulture = '{{$currentCulture}}';
        $('#' + selectedCulture_scoreboard + '_box_scoreboard').show();

        $('#cultureDropdown_scoreboard').change(function()
        {
            $('.translationsBox_scoreboard').hide(); //Hide all translations options boxes

            //Show the currently selected box
            var selectedCulture_scoreboard = $( "#cultureDropdown_scoreboard option:selected" ).val();
            $('#' + selectedCulture_scoreboard + '_box_scoreboard').show();
        });
    });
    </script>
@stop
<!-- END JAVASCRIPT INCLUDES -->