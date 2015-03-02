var React = require('react/addons');
var LineupEditor = require('./channel-lineup-editor');
var SettingsEditor = require('./channel-settings-editor');
var Popup = require('../../components/popup');


$(document).ready(() => {
  if (window.location.hostname === '192.168.111.29'){
    config.apiURL = 'https://vm-122.clubspeedtiming.com/api/index.php';

    //config.apiURL = 'https://uksydney.clubspeedtiming.com/api/index.php';
    //config.privateKey = '173123as821731872!';

    //config.apiURL = 'https://ekwigan.clubspeedtiming.com/api/index.php';
    //config.privateKey = 'HX8ykkTzH3ShDjI8mUS8';
  } else if (window.location.hostname !== 'ekwigan.clubspeedtiming.com'){
    var lineupTab = $('[href*=#panel_tab2_slidelineup_channel]');
    lineupTab.css('display', 'none');
    var settingsTab = $('[href*=#panel_tab2_channelsettings_channel]');
    settingsTab.css('display', 'none');
    window.show = () => {
      lineupTab.css('display', 'block');
      settingsTab.css('display', 'block');
    };
  }

  config.apiURL = config.apiURL + '/';



  var channelListRequest = $.get(config.apiURL + 'screenTemplate.json?deleted=false&key=' + config.privateKey);

  var trackListRequest = $.get(config.apiURL + 'tracks/index.json?key=' + config.apiKey);

  $.when(channelListRequest, trackListRequest)
  .done((res, trackRes) => {
    if (!res || !res[1] || !res[1] === 'success' || !trackRes || !trackRes[1] || !trackRes[1] === 'success'){
      console.error('An error occurred while trying to load data. You may be disconnected.');
      return;
    }

    var trackList = trackRes[0].tracks.map(track => ({ label: track.name, value: ~~track.id }) );

    res[0].channels.forEach((channel, i) => {
      try {
        var lineupContainer = document.getElementById('panel_tab2_slidelineup_channel' + channel.screenTemplateId);
        React.render(
          <LineupEditor {...channel} config={config} trackList={trackList} />,
          lineupContainer
        );

        var container = $('#panel_tab4_channel' + channel.screenTemplateId);
        React.render(
          <SettingsEditor channel={channel} trackList={trackList}

            onSave={updatedChannel => {
              React.unmountComponentAtNode(lineupContainer);
              React.render(
                <LineupEditor {...updatedChannel} config={config} trackList={trackList} />,
                lineupContainer
              );

              var node = document.createElement('div');
              document.body.appendChild(node);
              React.render(
                <Popup
                  message='Channel successfully saved!'
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
              React.unmountComponentAtNode('panel_tab2_slidelineup_channel' + channel.screenTemplateId);
              React.unmountComponentAtNode('panel_tab2_channelsettings_channel' + channel.screenTemplateId);
              $('[href="#panel_tab4_channel' + channel.screenTemplateId + '"]').remove();
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

            onError={() => {
              var node = document.createElement('div');
              document.body.appendChild(node);
              React.render(
                <Popup
                  message='An error occurred while trying to perform this action.'
                  alertClass='alert-danger'
                  onFadeComplete={e => {
                    React.unmountComponentAtNode(node);
                    document.body.removeChild(node);
                  }}
                />,
                node
              );
            }}
          />,
          document.getElementById('panel_tab2_channelsettings_channel' + channel.screenTemplateId)
        );
      } catch(ex){
        console.error(ex);
      }
    });
  });

  React.initializeTouchEvents(true);
});
