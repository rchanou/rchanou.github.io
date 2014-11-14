var React = require('react/addons');
var BookingAdmin = require('./bookingadmin.js');
//var BookingAdmin = require('../../components/bookingadmin.js');


var config = config || {
	apiURL: 'https://192.168.111.122/api/index.php',
	apiKey: 'cs-dev',
	privateKey: 'cs-dev'
};

var window = window || {};

if (window && window.location && window.location.hostname == '192.168.111.165') {
	config.apiURL = 'https://192.168.111.122/api/index.php';
} else {
	console.log = function(){};
}
config.apiURL += '/';


React.render(<BookingAdmin config={config} language={navigator? navigator.language: 'en-US'} />, document.getElementById('main'));