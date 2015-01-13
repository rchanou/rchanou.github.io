@extends('master')

@section('title')
Speed Screen Channels
@stop

@section('css_includes')
@parent
{{ HTML::style('css/select2-bootstrap.css') }}
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
          <div class="tabbable inline tabs-left">
            <!-- Channel list tabs on left side -->
            <ul class="nav nav-tabs tab-green">
                <?php $currentChannelCount = 0;?>
                @foreach($listOfChannels as $currentChannel)
                <?php $currentChannelCount++;?>
                <li{{$currentChannelCount == 1 ? ' class="active"' : ''}}> <a data-toggle="tab" href="#panel_tab4_channel{{$currentChannel->channelId}}">{{$currentChannel->channelId}}. {{$currentChannel->channelName}} </a> </li>
                @endforeach
            </ul>

            <!-- Channel content on right side -->
            <div class="tab-content">
                <?php $currentChannelCount = 0;?>
                @foreach($listOfChannels as $currentChannel)
                <?php $currentChannelCount++; ?>
                <div id="panel_tab4_channel{{$currentChannel->channelId}}" class="tab-pane channel-tab{{$currentChannelCount == 1 ? ' active' : ''}}">
                    <!-- Channel header -->
                    <div class="row">
                        <div class="col-sm-10">
                            <h2>{{$currentChannel->channelId}}. {{$currentChannel->channelName}}</h2>
                        </div>
                        <div class="col-sm-2">
                            <a href="" target="_blank" class="channelPreviewLink"><input type="button" class="btn btn-success btn-block" value="Preview" style="margin-top: 1.5em"></a>
                        </div>
                    </div>
                    <div class="tabbable inline">
                        <!-- Channel options tabs -->
                        <ul class="nav nav-tabs tab-bricky">
                            <li class="active"> <a data-toggle="tab" href="#panel_tab2_deploy_channel{{$currentChannel->channelId}}"> Deploy </a> </li>
                            <li class="" style="display: none"> <a data-toggle="tab" href="#panel_tab2_slidelineup_channel{{$currentChannel->channelId}}"> Slide Lineup </a> </li>
                            <li class="" style="display: none"> <a data-toggle="tab" href="#panel_tab2_channelsettings_channel{{$currentChannel->channelId}}"> Channel Settings </a> </li>
                        </ul>
                        <!-- Content of all channel tabs -->
                        <div class="tab-content">

                            <!-- Deploy tab -->
                            <div id="panel_tab2_deploy_channel{{$currentChannel->channelId}}" class="tab-pane active">
                                <div class="alert alert-info">
                                    <p> This tool will create a downloadable application that will launch the Speed Screen Channel application with the settings below. For non-Windows devices, you may use <a href="https://www.google.com/chrome/browser/" target="_blank">Google Chrome</a> to load the Channel URL directly. </p>
                                </div>
                                <form action="{{action('DeployController@deploy')}}" method="post" class="form-horizontal">
                                    <input type="hidden" name="channelId" class="channelId" value="{{$currentChannel->channelId}}" />
                                    <div class="form-group">
                                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Channel URL</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <input name="channelUrl" id="channelUrl" type="text" class="form-control input-sm channel-url-input">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 col-md-3 col-lg-2 control-label">Target Monitor</label>
                                        <div class="col-sm-9 col-md-9 col-lg-10">
                                            <select name="targetMonitor" class="targetMonitor select2-offscreen" tabindex="-1">
                                                <option value="1">Monitor 1 (Default)</option>
                                                @for($i=2; $i <= $numberOfMonitors; $i++)
                                                <option value="{{$i}}">Monitor {{$i}}</option>
                                                @endfor
                                            </select>
                                            <span class="help-block text-left">Start the channel application on this monitor (useful for multi-monitor PCs)</span> </div>
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
                                    <p> This feature is under development and will allow editing of each slide in this channel. <p/>In the meanwhile, please proceed to the <a href="http://{{$_SERVER['HTTP_HOST']}}/sp_admin" target="_blank">current admin panel</a>. </p>
                                </div>
                                @foreach($channelLineups[$currentChannel->channelId] as $currentSlideIndex => $currentSlide)
                                <div class="widget-box collapsible" style="display: none;">
                                    <div class="widget-title"> <a href="#slide{{$currentSlideIndex+1}}_channel{{$currentChannel->channelId}}" data-toggle="collapse"> <span class="icon"><i class="fa fa-bar-chart-o"></i></span>
                                            <h5>{{$currentSlideIndex+1}}. {{$currentSlide->type}}</h5>
                                        </a> </div>
                                    <div class="collapse" id="slide{{$currentSlideIndex+1}}_channel{{$currentChannel->channelId}}">
                                        <div class="widget-content"></div>
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            <!-- Channel Settings tab -->
                            <div id="panel_tab2_channelsettings_channel{{$currentChannel->channelId}}" class="tab-pane">
                                <p>Placeholder for channel-wide settings such as "Channel Name" and "Delete" button.</p>
                                <button class="btn btn-dark-red">Delete Channel</button>
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
{{ HTML::script('js/react/build/speedScreen/manage/main.min.js') }}
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
  self.url = opts.url || 'http://'+location.hostname+(location.port ? ':' + location.port: '') + '/cs-speedscreen/#/' + opts.channelId;
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
  var channels = {};
  $('.channel-tab').each(function() { // PLACEHOLDER - CSS NAME NEEDS TO BE UPDATED
    var channelUrlInput = $(this).find('.channel-url-input:first');
    var channelPreviewLink = $(this).find('.channelPreviewLink:first');
    var channelAnimationsCheckbox = $(this).find('.channel-animations-checkbox:first');
    var channelId = $(this).find('.channelId').val();
    var channel = channels[$(this).attr('id')] = new Channel({
      url: channelUrlInput.value || ''
      , disableAnimations: (channelAnimationsCheckbox != null ? !!channelAnimationsCheckbox.prop('checked') : false)
      , elems: {
        animationsCheckbox: $(channelAnimationsCheckbox),
        urlInput:           $(channelUrlInput),
        channelPreviewLink: $(channelPreviewLink)
      }
      , channelId: channelId

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
</script>
@stop
