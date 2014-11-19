var React = require('react/addons');
var BookingAdmin = require('./booking-admin');

React.render(<BookingAdmin language={navigator? navigator.language: 'en-US'} />, document.getElementById('main'));