var React = require('react/addons');
var LineupEditor = require('./channel-lineup-editor');
var SettingsEditor = require('./channel-settings-editor');
var Popup = require('../../components/popup');


var global = {
  domain: ''
};

$(document).ready(() => {
  if (__DEV__){
    config.apiURL = 'https://vm-122.clubspeedtiming.com/api/index.php/';
    config.origin = 'https://vm-122.clubspeedtiming.com';
  } else {
    config.apiURL = window.location.origin + '/api/index.php/';
    config.origin = window.location.origin;

    /*var lineupTab = $('[href*=#panel_tab2_slidelineup_channel]');
    lineupTab.css('display', 'none');
    var settingsTab = $('[href*=#panel_tab2_channelsettings_channel]');
    settingsTab.css('display', 'none');
    window.show = () => {
      lineupTab.css('display', 'block');
      settingsTab.css('display', 'block');
    };*/
  }

  var trackListRequest = $.get(config.apiURL + 'tracks/index.json?key=' + config.apiKey);
  trackListRequest.then(trackRes => {
    if (!trackRes){
      console.error('An error occurred while trying to load data. You may be disconnected.', trackRes);
      return;
    }

    var trackList = trackRes.tracks.map(track => ({ label: track.name, value: ~~track.id }) );

    channels.forEach((channel, i) => {
      //if (i !== 0) return;

      //try {
        var lineupContainer = document.getElementById('panel_tab2_slidelineup_channel' + channel.channelId);
        React.render(
          <LineupEditor
            channelId={channel.channelId}
            initialChannelData={channel.channelData}
            config={config}
            trackList={trackList}
          />,
          lineupContainer
        );

        var container = $('#panel_tab4_channel' + channel.channelId);
        React.render(
          <SettingsEditor channel={channel} trackList={trackList}

            onSave={updatedChannel => {
              React.unmountComponentAtNode(lineupContainer);
              React.render(
                <LineupEditor {...updatedChannel} initialChannelData={updatedChannel.channelData} config={config} trackList={trackList} />,
                lineupContainer
              );

              var node = document.createElement('div');
              document.body.appendChild(node);
              React.render(
                <Popup
                  message='Channel settings successfully saved!'
                  alertClass='alert-success'
                  onDone={e => {
                    React.unmountComponentAtNode(node);
                    document.body.removeChild(node);
                  }}
                />,
                node
              );
            }}

            onDelete={() => {
              React.unmountComponentAtNode(document.getElementById('panel_tab2_slidelineup_channel' + channel.channelId));
              React.unmountComponentAtNode(document.getElementById('panel_tab2_channelsettings_channel' + channel.channelId));
              $('[href="#panel_tab4_channel' + channel.channelId + '"]').remove();
              container.html('');
              React.render(
                <Popup
                  message='Channel successfully deleted!'
                  alertClass='alert-success'
                  onDone={e => {
                    React.unmountComponentAtNode(container[0]);
                    container.remove();
                  }}
                />,
                container[0]
              );
            }}

            onError={res => {
              var node = document.createElement('div');
              document.body.appendChild(node);

              try {
                if (res.responseJSON.error.message.indexOf('duplicate') !== -1){
                  var message = 'Cannot save. Channel number already taken. Please enter a different channel number.';
                } else {
                  var message = 'An error occurred while trying to perform this action.';
                }
              } catch(ex){
                var message = 'An error occurred while trying to perform this action.';
              }

              React.render(
                <Popup
                  message={message}
                  alertClass='alert-danger'
                  onDone={e => {
                    React.unmountComponentAtNode(node);
                    document.body.removeChild(node);
                  }}
                />,
                node
              );
            }}
          />,
          document.getElementById('panel_tab2_channelsettings_channel' + channel.channelId)
        );
      /*} catch(ex){
        console.log(ex.stack);
        console.error(ex);
      }*/
    });
  });

  React.initializeTouchEvents(true);
});
