var React = require('react/addons');
var LogTable = require('./facebook-log-table');

$(document).ready(function(){

  /*if (window.location.hostname === '192.168.111.205'){
    config.apiURL = 'https://vm-122.clubspeedtiming.com/api/index.php';
  }

  config.apiURL = config.apiURL + '/';
*/
  React.initializeTouchEvents(true);

  React.render(<LogTable />, document.getElementById('main'));
});
