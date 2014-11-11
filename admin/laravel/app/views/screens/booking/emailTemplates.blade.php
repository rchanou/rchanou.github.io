@extends('master')

@section('title')
  E-mail Templates
@stop

@section('css_includes')
  @parent
  {{ HTML::style('css/bootstrap3-wysihtml5.min.css') }}
@stop

@section('pageHeader')
  E-mail Templates
@stop

@section('breadcrumb')
  <a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
  <a href="#" class="current">Online Bookings</a>
  <a href="#" class="current">E-mail Templates</a>
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
        <div class="widget-box">
          <div class="widget-title">
            <span class="icon">
              <i class="fa fa-align-justify"></i>
            </span>
            <h5>E-mail Template Editor</h5>
          </div>
          <div class="widget-content">
            <div class="row">
              <label for="emailTemplateDropdown"><!-- class="col-xs-12 col-sm-3 col-lg-2" -->
                E-mail Template:
              </label><!--  class="col-xs-12 col-sm-9 col-lg-10" -->
              <select name="emailTemplateDropdown" id="emailTemplateDropdown">
                @foreach($emailTemplates as $emailTemplate)
                <option value="{{$emailTemplate->name}}" @if($emailTemplate->name == $currentEmailTemplate)selected="selected"@endif>
                  {{$emailTemplate->displayName}} @if(false && $emailTemplate->name == $currentEmailTemplate)<em>(current)</em>@endif
                </option>
                @endforeach
              </select>
              <p></p>
            </div>
            {{ Form::open(array('action'=>'BookingController@updateEmailTemplates','files' => false, 'class' => 'form-horizontal')) }}
              <div class="row">
                {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
              </div>
              @foreach($emailTemplates as $emailTemplate)
                <div id="{{$emailTemplate->name}}_box" class="row emailTemplateBox">
                  <textarea class="editor col-xs-8" style="height: 1080px;"  name="{{$emailTemplate->name}}" id="editor_{{$emailTemplate->name}}">
                    {{$emailTemplate->value}}
                  </textarea>
                  <div class="col-xs-4">
                    @if($emailTemplate->note)
                      <p class="alert alert-info">
                        {{$emailTemplate->note}}
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
  <script>
    function autoResize(element){
      console.log('before resize', element);

      var newHeight = element.contentWindow.document.body.scrollHeight + 'px';
      console.log('resize to ' + newHeight, element);

      element.height = newHeight;
      element.style.height = newHeight;
      element.style.backgroundColor = 'blue';

      console.log('autoResize', element, element.style.height);
    }

    $(document).ready(function(){
      // animate message popup from any previous actions
      window.setTimeout(function() {
        $(".fadeAway").fadeTo(500, 0).slideUp(500, function(){
          $(this).remove();
        });
      }, 5000);

      // create html editor for each template
      @foreach($emailTemplates as $emailTemplate)
        var editor_{{$emailTemplate->name}};

        $('#editor_{{$emailTemplate->name}}').wysihtml5({
          toolbar: {
            html: {{$emailTemplate->isHtml? 'true': 'false'}},
            link: {{$emailTemplate->isHtml? 'true': 'false'}},
            image: {{$emailTemplate->isHtml? 'true': 'false'}},
            color: true
          },
          events: {
            load: function(){
              //editor_{{$emailTemplate->name}};
              //var editor = $('#editor_{{$emailTemplate->name}}').data('wysihtml5');
              //editor_{{$emailTemplate->name}} = new wysihtml5.Editor('editor_{{$emailTemplate->name}}');
                //$(editor_{{$emailTemplate->name}}.composer.iframe).wysihtml5_size_matters();
              //console.log('load');
              //$('#editor_{{$emailTemplate->name}}').autosize();
            },
            blur: function(){
              autoResize(document.getElementsByTagName('iframe')[0]);
              //$('.wysihtml5-sandbox').height(300 + Math.random()*100);
              //$('.editor').height(300 + Math.random()*100);
              //console.log('change', $('.wysihtml5-sandbox'), $('iframe').height());
              //$('iframe').height(300 + Math.random()*100);
              //console.log('change', $('#editor_{{$emailTemplate->name}}').data('wysihtml5'), editor_{{$emailTemplate->name}});
             // $('#editor_{{$emailTemplate->name}}').autosize();
            },
            paste: function(){
              autoResize(document.getElementsByTagName('iframe')[0]);
            }
          }
        });
        //editor_{{$emailTemplate->name}} = new wysihtml5.Editor('editor_{{$emailTemplate->name}}');
      @endforeach

      // set up hiding/showing of templates based on selected template in dropdown
      function showSelectedTemplate(){
        $('.emailTemplateBox').hide();
        var boxToShow = $('#emailTemplateDropdown option:selected').val();
        $('#' + boxToShow + '_box').show();
      }

      showSelectedTemplate();

      $('#emailTemplateDropdown')
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