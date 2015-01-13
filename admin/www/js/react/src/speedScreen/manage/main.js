var React = require('react/addons');
var csp = require('js-csp');
var SpeedScreenAdmin = require('./speed-screen-admin');

$(document).ready(() => {

  if (window.location.hostname === '192.168.111.165'){
    config.apiURL = 'https://vm-122.clubspeedtiming.com/api/index.php';
  }

  config.apiURL = config.apiURL + '/';

  $.get(config.apiURL + 'screenTemplate.json?deleted=false&key=' + config.privateKey)
  .then(res => {
    res.channels.forEach((channel, i) => {
      React.render(
        <SpeedScreenAdmin {...channel} config={config} />,
        document.getElementById('panel_tab2_slidelineup_channel' + channel.screenTemplateId)
      );
    });
  });

  React.initializeTouchEvents(true);

  //React.render(<SpeedScreenAdmin config={config} />, document.getElementById('main'));
});
