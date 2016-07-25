@extends('master')

@section('title')
Speed Screen Channels
@stop

@section('css_includes')
@parent
{{ HTML::style('css/select2-bootstrap.css') }}
{{ HTML::style('css/jquery.ui.ie.css') }}
{{ HTML::style('css/jquery-ui.css') }}
<style>
  .slide:hover {
    border: 3px solid #3498db;
  }
</style>
<!--{{ HTML::style('css/bootstrap-timepicker.min.css') }}-->
@stop

@section('pageHeader')
Speed Screen Channels
@stop

@section('breadcrumb')
    <a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
    <a href="#" class="current">Speed Screen Channels</a>
@stop

@section('content')
    <div class="row">
      <div class="row">
        <div class="col-sm-12">
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

          <div class="tabbable inline tabs-left">
            <!-- Channel list tabs on left side -->
            <ul class="nav nav-tabs tab-green">
                <?php $currentChannelCount = 0;?>
                @foreach($listOfChannels as $currentChannel)
                  <?php $currentChannelCount++;?>
                  <li{{((Session::has('selectLastChannel') && $currentChannelCount == count($listOfChannels)) || (!Session::has('selectLastChannel') && $currentChannelCount == 1)) ? ' class="active"' : ''}}>
                    <a data-toggle="tab" href="#panel_tab4_channel{{$currentChannel->channelId}}">
                      {{$currentChannel->channelData->name}} (#{{$currentChannel->channelNumber}})
                    </a>
                  </li>
                @endforeach
                <li>
                  {{ Form::open(array('action' => 'ChannelController@createChannel')) }}
                    <?php $newChannelNumber = count($listOfChannels) > 0 ? max(array_map(function($channel){ return $channel->channelNumber; }, $listOfChannels)) + 1 : 1; ?>
                    <input type="hidden" value="{{$newChannelNumber}}" name="newChannelNumber" />
                    {{ Form::button('<i class="fa fa-plus" style="font-size: 1.5em;"></i>',
                      array(
                        'type' => 'submit',
                        'class' => 'btn btn-info',
                        'style' => 'width: 100%',
                        'data-toggle' => 'tooltip',
                        'title' => 'Create New Channel',
                        'onClick' => '(function(){
                          this.value = "Creating New Channel...";
                          this.innerHTML = "Creating New Channel...";
                          this.disabled = true;
                          this.parentNode.submit();
                        }).bind(this)()'
                      )
                    ) }}
                  {{ Form::close() }}
                </li>
            </ul>

            <!-- Channel content on right side -->
            <div class="tab-content">
                <?php $currentChannelCount = 0;?>
                @foreach($listOfChannels as $currentChannel)
                <?php $currentChannelCount++; ?>
                <div id="panel_tab4_channel{{$currentChannel->channelId}}" class="tab-pane channel-tab{{((Session::has('selectLastChannel') && $currentChannelCount == count($listOfChannels)) || (!Session::has('selectLastChannel') && $currentChannelCount == 1)) ? ' active' : ''}}">
                    <!-- Channel header -->
                    <div class="row">
                        <div class="col-sm-12">
                            <h2>{{$currentChannel->channelData->name}} (#{{$currentChannel->channelNumber}})  <a href="" target="_blank" class="channelPreviewLink" data-toggle="tooltip" data-placement="top" title="Preview Channel"><i class="fa fa-external-link-square"></i></a></h2>
                        </div>
                    </div>
                    <div class="tabbable inline">
                        <!-- Channel options tabs -->
                        <ul class="nav nav-tabs tab-bricky">
                            <li class={{(!Session::has('selectLastChannel') || $currentChannelCount !== count($listOfChannels))? "active": ""}}>
                              <a data-toggle="tab" href="#panel_tab2_deploy_channel{{$currentChannel->channelId}}">  Download  </a>
                            </li>
                            <li class="">
                              <a data-toggle="tab" href="#panel_tab2_slidelineup_channel{{$currentChannel->channelId}}">  Slide Lineups  </a>
                            </li>
                            <li class="{{(Session::has('selectLastChannel') && $currentChannelCount == count($listOfChannels))? "active": ""}}">
                              <a data-toggle="tab" href="#panel_tab2_channelsettings_channel{{$currentChannel->channelId}}">  Channel Settings  </a>
                            </li>
                            <li class="">
                              <a data-toggle="tab" href="#panel_tab2_deploy_channel{{$currentChannel->channelId}}_beta">  Download (BETA)  </a>
                            </li>
                        </ul>
                        <!-- Content of all channel tabs -->
                        <div class="tab-content" style="overflow-x: hidden;">

                            <!-- Deploy tab -->
                            <div id="panel_tab2_deploy_channel{{$currentChannel->channelId}}" class="tab-pane {{(!Session::has('selectLastChannel') || $currentChannelCount !== count($listOfChannels))? 'active': ''}}">
                                <div class="alert alert-info">
                                    <p> This tool will create a downloadable application that will launch the Speed Screen Channel with the settings below.</p>
                                </div>
                                <form action="{{action('DeployController@deploy')}}" method="post" class="form-horizontal">
                                    <input type="hidden" name="channelId" class="channelId" value="{{$currentChannel->channelId}}" />
                                    <input type="hidden" name="channelNumber" class="channelNumber" value="{{$currentChannel->channelNumber}}" />
                                    <div class="form-group" style="display: none;">
                                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Channel URL</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input name="channelUrl" id="channelUrl" type="text" class="form-control input-sm channel-url-input">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Target Monitor</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <select name="targetMonitor" class="targetMonitor" tabindex="-1" style="min-width: 200px;">
                                                <option value="1">Monitor 1 (Default)</option>
                                                @for($i=2; $i <= $numberOfMonitors; $i++)
                                                <option value="{{$i}}">Monitor {{$i}}</option>
                                                @endfor
                                            </select>
                                            <span class="help-block text-left">Start the channel application on this monitor (useful for multi-monitor PCs)</span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Disable Animations</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input class="channel-animations-checkbox" type="checkbox">
                                            <span class="help-block text-left">If checked, animations will be disabled in most slides.</span>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <button type="submit" class="btn btn-info">Create Application</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Slide lineup tab -->
                            <div id="panel_tab2_slidelineup_channel{{$currentChannel->channelId}}" class="tab-pane">
                                <div class="alert alert-info">
                                    Loading...
                                </div>
                            </div>

                            <!-- Channel Settings tab -->
                            <div id="panel_tab2_channelsettings_channel{{$currentChannel->channelId}}" class="tab-pane {{(Session::has('selectLastChannel') && $currentChannelCount == count($listOfChannels))? 'active': ''}}">
                              <div class="alert alert-info">
                                Loading...
                              </div>
                            </div>

                            <!-- Deploy BETA tab -->
                            <div id="panel_tab2_deploy_channel{{$currentChannel->channelId}}_beta" class="tab-pane">
                                <div class="alert alert-warning">
                                    <p>We are currently testing the next generation of our Speed Screen application which allows real-time updating of settings and channel selection as well as multi-monitor support.</p>
                                    <p>Please only use this application with the direction of our support personnel.</p>
                                    <p><a href="/admin/speedscreenV2/speedscreen-setup-v2.0.0-beta.2.exe" target="_blank"><button type="button" class="btn btn-warning">Download Speed Screen Application Beta</button></a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent <!-- This includes the original parent's javascript -->
<script language="javascript">
  /**
      Helper method to set callbacks to fire after the delay call
      has not been fired for the provided wait parameter.

      @param {function} callback The callback to execute after the method has not been called for the specified wait time.
      @param {number} wait The number of milliseconds to wait between function calls before executing the callback.
      @returns {void}
  */
  var delay = function(callback, wait) {
    var timeout = null;
    return function() {
      var context = this;
      var args = Array.prototype.slice.call(arguments);
      var finish = function() {
        timeout = null;
        callback.apply(context, args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(finish, wait);
    }
  };

  /**
      Creates a new Channel.
      @class
      @param {object} opts The array of objects to provide on channel creation.
  */
  function Channel(opts) {
    var self = this;
    if (!opts) opts = {};
    self.url = opts.url || 'http://'+location.hostname+(location.port ? ':' + location.port: '') + '/cs-speedscreen/#/' + opts.channelNumber;
    self.disableAnimations = opts.disableAnimations || false;
    self.elems = opts.elems || {};

    self.elems.channelPreviewLink.attr('href',self.url);

  }

  /**
      Outputs a string representation of the channel object's relevant url.

      @returns {string} A string representation of the channel object's relevant url.
  */
  Channel.prototype.toString = function() {
    return this.url + (this.disableAnimations ? "/disableAnimations" : "");
  };

  /**
      Handler for when a disable animations checkbox has been changed.

      @param {Channel} channel The channel object which needs to store the change.
      @param {JQuery} disableAnimationsCheckbox The jQuery object representation of the disable animations checkbox.
      @param {Event} evt The event which is causing disableAnimationsChanged to be called.
      @returns {void}
  */
  function disableAnimationsChanged(channel, disableAnimationsCheckbox, evt) {
    channel.disableAnimations = !!($(disableAnimationsCheckbox).prop('checked')); // need the $ here? double check
    channel.elems.urlInput.val(channel.toString());
    channel.elems.channelPreviewLink.attr('href',channel.toString());
  }

  /**
      Handler for when a channel input url has changed.

      @param {Channel} channel The channel object which needs to store the change.
      @param {JQuery} channelInput The jQuery object representation of the channel url input.
      @param {Event} evt The event which is causing channelUrlChanged to be called.
      @returns {void}
  */
  function channelUrlChanged(channel, channelInput, evt) {
    channel.url = channel.elems.urlInput.val().replace('/disableAnimations','');
    channel.elems.channelPreviewLink.attr('href',channel.toString());
  }


  /**
      Inits all channel objects in javascript, loading the default urls into each channel url input,
      as well as connecting functions to each input and disable animations checkbox events.

      @returns {void}
  */
  function init() {
    window.viewChannels = {};
    $('.channel-tab').each(function() { // PLACEHOLDER - CSS NAME NEEDS TO BE UPDATED
      var channelUrlInput = $(this).find('.channel-url-input:first');
      var channelPreviewLink = $(this).find('.channelPreviewLink:first');
      var channelAnimationsCheckbox = $(this).find('.channel-animations-checkbox:first');
      var channelNumber = $(this).find('.channelNumber').val();
      var channel = window.viewChannels[$(this).attr('id')] = new Channel({
        url: channelUrlInput.value || ''
        , disableAnimations: (channelAnimationsCheckbox != null ? !!channelAnimationsCheckbox.prop('checked') : false)
        , elems: {
          animationsCheckbox: $(channelAnimationsCheckbox),
          urlInput:           $(channelUrlInput),
          channelPreviewLink: $(channelPreviewLink)
        }
        , channelNumber: channelNumber

      });
      if (channelUrlInput != null) {
        //// use this if we need a delay
        // channelUrlInput.keyup(delay(function(e) {
        //   channelUrlChanged(channel, channelUrlInput, e);
        // }, 500));
        channelUrlInput.keyup(function(e) {
          channelUrlChanged(channel, e);
        });
      }

      if (channelAnimationsCheckbox != null) {

          channelAnimationsCheckbox.on('ifChanged', function(event) {
              var self = this;
              disableAnimationsChanged(channel, self, event);
          });
      }
      channelUrlInput.val(channel.toString()); // set default
    });
  }

  $(function() {
    init();
  });

  var channels = {{json_encode($listOfChannels)}}; // used by lineup editors loaded by main below

  $(function () {
      $('[data-toggle="tooltip"]').tooltip()
  })

</script>

{{ HTML::script('js/react/build/speedScreen/manage/main.min.js') }}

@stop
