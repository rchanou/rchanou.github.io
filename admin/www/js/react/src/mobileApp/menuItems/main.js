var React = require('react/addons');
var MenuItemEditor = require('./menu-item-editor');

$(document).ready(function(){

  if (window.location.hostname === '192.168.111.205' || window.location.hostname === '192.168.111.170'){
    config.apiURL = 'https://vm-122.clubspeedtiming.com/api/index.php';
  }

  config.apiURL = config.apiURL + '/';

  React.initializeTouchEvents(true);

  React.render(<MenuItemEditor config={config} />, document.getElementById('main'));
});
