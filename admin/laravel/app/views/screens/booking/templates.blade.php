@extends('master')

@section('title')
  Booking Templates
@stop

@section('css_includes')
  @parent
  {{ HTML::style('css/bootstrap3-wysihtml5.min.css') }}
@stop

@section('pageHeader')
  Booking Templates
@stop

@section('breadcrumb')
  <a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
  <a href="#" class="current">Online Bookings</a>
  <a href="#" class="current">Templates</a>
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
        @if ($currentOnlineBookingState == 'disabled_manually')
        <div class="alert alert-warning">
            <p>(Note: Online Booking is <strong>current disabled</strong> because the "Enable Online Booking" setting is not checked.</p>
            To access Online Booking while it's disabled (for testing), <a href="{{'http://' . $_SERVER['HTTP_HOST'] . '/booking/step1?key=' . md5(Config::get('config.privateKey'))}}">use this link</a>.
        </div>
        @endif
        @if ($currentOnlineBookingState == 'disabled_dummypayments')
        <div class="alert alert-warning">
            <p>(Note: Online Booking is <strong>current disabled</strong> because the site is using the Dummy payment processor.)</p>
            To access Online Booking while it's disabled (for testing), <a href="{{'http://' . $_SERVER['HTTP_HOST'] . '/booking/step1?key=' . md5(Config::get('config.privateKey'))}}">use this link</a>.
        </div>
        @endif
        <div class="widget-box">
          <div class="widget-title">
            <span class="icon">
              <i class="fa fa-align-justify"></i>
            </span>
            <h5>Template Editor</h5>
          </div>
          <div class="widget-content">
            <div class="row">
              <label for="templateDropdown"><!-- class="col-xs-12 col-sm-3 col-lg-2" -->
                Template:
              </label><!--  class="col-xs-12 col-sm-9 col-lg-10" -->
              <select name="templateDropdown" id="templateDropdown">
                @foreach($templates as $template)
                <option value="{{$template->name}}" @if($template->name == $currentTemplate)selected="selected"@endif>
                  {{$template->displayName}}
                  @if(false && $template->name == $currentTemplate)<em>(current)</em>@endif
                </option>
                @endforeach
              </select>
              <p></p>
            </div>
            {{ Form::open(array('action'=>'BookingController@updateTemplates','files' => false, 'class' => 'form-horizontal')) }}
              @foreach($templates as $template)
                <div id="box_{{$template->name}}" class="row templateBox">
                  <div class="col-xs-8">
                    <textarea class="col-xs-12 editor" style="overflow: hidden; resize: none;" name="{{$template->name}}" id="editor_{{$template->name}}">{{$template->value}}</textarea>
                    {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                  </div>
                  <div class="col-xs-4">
                    @if($template->note)
                      <p class="alert alert-info">
                        {{$template->note}}
                      </p>
                    @endif
                  </div>
                </div>﻿
              @endforeach
            {{ Form::close() }}
          </div>
        </div>
      </div>
    </div>
  </div>
@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
  @parent
  {{ HTML::script('js/bootstrap3-wysihtml5.all.js') }}
  {{ HTML::script('js/jquery.autosize.min.js') }}
  <script>
    var MIN_EDITOR_HEIGHT = 150;

    function resizeEditorByName(name){
      // resize textarea (source view)
      $('#editor_' + name).trigger('autosize.destroy');
      $('#editor_' + name).autosize();

      // resize iframe (rich text view)
      var iframe = $('#box_' + name).find('iframe')[0];
      if (iframe){
        var newHeight = Math.max(MIN_EDITOR_HEIGHT, iframe.contentWindow.document.body.scrollHeight) + 'px'; // need some arbitrary min. height else you'll get some funny biz
        iframe.style.height = newHeight;
      }
    }

    $(document).ready(function(){
      // animate message popup for response to any previous form submit action
      window.setTimeout(function() {
        $(".fadeAway").fadeTo(500, 0).slideUp(500, function(){
          $(this).remove();
        });
      }, 5000);

      // create html editor for each template
      @foreach($templates as $template)
        if ({{$template->isHtml? 'true': 'false'}}){
          $('#editor_{{$template->name}}').wysihtml5({
            toolbar: {
              html: {{$template->isHtml? 'true': 'false'}},
              link: {{$template->isHtml? 'true': 'false'}},
              image: {{$template->isHtml? 'true': 'false'}},
              color: true
            },
            events: {
              load: function(){
                // find editor body generated by wysihtml5
                var editor = $('#box_{{$template->name}}').find('.wysihtml5-sandbox').contents().find('body');

                resizeEditorByName('{{$template->name}}');

                // register wysiwyg editor resize on key
                editor.on("keyup", function() {
                  resizeEditorByName('{{$template->name}}');
                });

                // init source html editor textarea resizing
                $('#editor_' + name).autosize();

                // hide editor scrollbar
                editor.css('overflow', 'hidden');
              },
              blur: function(){
                resizeEditorByName('{{$template->name}}');
              },
              focus: function(){
                resizeEditorByName('{{$template->name}}');
              },
              change_view: function(){
                resizeEditorByName('{{$template->name}}');
              },
              paste: function(){
                resizeEditorByName('{{$template->name}}');
              }
            }
          });
        } else {
          $('#editor_{{$template->name}}').autosize();
          /*$('#editor_{{$template->name}}').keydown(function(){
            console.log('inputi');
            $(this).autosize();
          });*/
        }
      @endforeach

      // set up hiding/showing of templates based on selected template in dropdown
      function showSelectedTemplate(){
        $('.templateBox').hide();
        var boxToShow = $('#templateDropdown option:selected').val();
        $('#box_' + boxToShow).show();
      }

      showSelectedTemplate();

      $('#templateDropdown')
        .select2({ width: 300 })
        .change(function(){
          showSelectedTemplate();
        });

      $('[title]').each(function(){
        $(this).tooltip();
      });
    });
  </script>
@stop
<!-- END JAVASCRIPT INCLUDES -->