var Promise = require("bluebird");
var request = require('request');
var toJson = require('xml2js').parseString;
var xml2js = require('xml2js');
Promise.longStackTraces();
var opts;

function Ingenico (opts) {
	this.opts = opts || {};
	this.opts.debug   = opts.provider.options.debug          || false;
	this.opts.host    = opts.provider.options.host           || '127.0.0.1';
	this.opts.port    = parseInt(opts.provider.options.port) || 986;
	this.opts.timeout = opts.provider.options.timeout        || 120;
	
	// GLOBAL OPTIONS
	this.opts.RECEIPTCOUNT  = opts.provider.options.RECEIPTCOUNT  || 0;
	this.opts.CONFIRMAMOUNT = opts.provider.options.CONFIRMAMOUNT || 'False';
	this.opts.SUPRESSUI     = opts.provider.options.SUPRESSUI     || 'False';
	this.opts.TAXAMOUNT     = opts.provider.options.TAXAMOUNT     || null;
	this.opts.CUSTIDENT     = opts.provider.options.CUSTIDENT     || null;
	
	// INTERACTIVEISSUECREDIT OPTIONS
	this.opts.INTERACTIVEISSUECREDITTYPE    = opts.provider.options.INTERACTIVEISSUECREDITTYPE    || 'SWIPEONLY';
	
	// INTERACTIVECREDITAUTH OPTIONS
	this.opts.INTERACTIVECREDITAUTHTYPE = opts.provider.options.INTERACTIVECREDITAUTHTYPE || 'SWIPEONLY';
	this.opts.SHOWAMT    = opts.provider.options.SHOWAMT    || 'False';
	this.opts.SIGPROMPT  = opts.provider.options.SIGPROMPT  || 'Yes';
	this.opts.REQUIREZIP = opts.provider.options.REQUIREZIP || 'False';
	this.opts.REQUIRECVV = opts.provider.options.REQUIRECVV || 'False';
	this.opts.REQUIREAVS = opts.provider.options.REQUIREAVS || 'False';
	this.opts.DISABLEZIP = opts.provider.options.DISABLEZIP || 'False';
	this.opts.DISABLECVV = opts.provider.options.DISABLECVV || 'False';
	this.opts.DISABLEAVS = opts.provider.options.DISABLEAVS || 'False';
	
	request = request.defaults({
		url: 'http://' + this.opts.host + ':' + this.opts.port,
		method: 'POST',
		timeout: (this.opts.timeout * 1000) + 2000,
		headers: { 'Content-Type': 'text/plain' }
		});
}

Ingenico.prototype.submitTransaction = function submitTransaction(order, creditCard, prospect, other) {
	var self = this;
	var req = '<TRANSACTION>' +
			'  <TRANSACTIONTYPE>INTERACTIVECREDITAUTH</TRANSACTIONTYPE>' +
			'￼￼<INTERACTIVETIMEOUT>' + parseInt(self.opts.timeout) + '</INTERACTIVETIMEOUT>' +
			'￼￼<CREDITAMT>'     + convertToDecimal(order.amount)   + '</CREDITAMT>' +
			'￼￼<RECEIPTCOUNT>'  + this.opts.RECEIPTCOUNT   + '</RECEIPTCOUNT>' +
			'￼￼<CONFIRMAMOUNT>' + this.opts.CONFIRMAMOUNT  + '</CONFIRMAMOUNT>' +
			'￼￼<SUPRESSUI>'  + this.opts.SUPRESSUI  + '</SUPRESSUI>' +
			'￼￼<TAXAMOUNT>'  + this.opts.TAXAMOUNT  + '</TAXAMOUNT>' +
			'￼￼<CUSTIDENT>'  + this.opts.CUSTIDENT  + '</CUSTIDENT>' +
			'￼￼<SHOWAMT>'    + this.opts.SHOWAMT    + '</SHOWAMT>' +
			'￼￼<SIGPROMPT>'  + this.opts.SIGPROMPT  + '</SIGPROMPT>' +
			'￼￼<REQUIREZIP>' + this.opts.REQUIREZIP + '</REQUIREZIP>' +
			'￼￼<REQUIRECVV>' + this.opts.REQUIRECVV + '</REQUIRECVV>' +
			'￼￼<REQUIREAVS>' + this.opts.REQUIREAVS + '</REQUIREAVS>' +
			'￼￼<DISABLEZIP>' + this.opts.DISABLEZIP + '</DISABLEZIP>' +
			'￼￼<DISABLECVV>' + this.opts.DISABLECVV + '</DISABLECVV>' +
			'￼￼<DISABLEAVS>' + this.opts.DISABLEAVS + '</DISABLEAVS>' +
			'￼￼<INTERACTIVECREDITAUTHTYPE>' + this.opts.INTERACTIVECREDITAUTHTYPE + '</INTERACTIVECREDITAUTHTYPE>' +
			'</TRANSACTION>';

	return new Promise(function(resolve, reject) {
		request({ body: req, headers: { 'Content-Length': req.length } }, function(err, response, body) {
			if(err) {
				console.log('CHARGE REQUEST ERROR', err, self.opts);
				return reject({ message: err.code + ' ' + this.opts.host, error: err });
			}
			if(self.opts.debug) console.log('RAW BODY', body);

			toJson(body, function (err, result) {
				if(self.opts.debug) console.log('PARSED BODY', JSON.stringify(result));
				if(err) {
					console.log('CHARGE PARSING ERROR', err);
					return reject({ message: err.code, error: err });
				}
				if(result.TRANRESP.TRANSUCCESS[0] === 'FALSE') {
					return resolve({
						'message': result.TRANRESP.TRANRESPMESSAGE[0],
						'code': result.TRANRESP.hasOwnProperty('TRANRESPERRCODE') ? result.TRANRESP.TRANRESPERRCODE[0] : null,
						'success': false,
						'transactionId': null,
						'_original': result
						});		
				} else if(result.TRANRESP.TRANSUCCESS[0] === 'TRUE') {
					return resolve({
						'success': true,
						'transactionId': result.TRANRESP.TRANSARMORTOKEN[0],
						'_original': result
						});
				} else {
					// Unsure if we can get to this case?
					return resolve({
						'success': false,
						'transactionId': null,
						'_original': result
						});
				}
			});
		});
	});
}

Ingenico.prototype.refundTransaction = function refundTransaction(order) {
	var self = this;
	var req = '<TRANSACTION>' +
    '  <TRANSACTIONTYPE>INTERACTIVEISSUECREDIT</TRANSACTIONTYPE>' +
    '  <CREDITAMT>' + convertToDecimal(order.amount) + '</CREDITAMT>' +
		'￼￼<INTERACTIVETIMEOUT>' + parseInt(self.opts.timeout) + '</INTERACTIVETIMEOUT>' +
		'￼￼<INTERACTIVEISSUECREDIT>' + this.opts.INTERACTIVEISSUECREDIT + '</INTERACTIVEISSUECREDIT>' +
		'￼￼<RECEIPTCOUNT>'  + this.opts.RECEIPTCOUNT   + '</RECEIPTCOUNT>' +
		'￼￼<CONFIRMAMOUNT>' + this.opts.CONFIRMAMOUNT  + '</CONFIRMAMOUNT>' +
		'￼￼<SUPRESSUI>'  + this.opts.SUPRESSUI  + '</SUPRESSUI>' +
		'￼￼<TAXAMOUNT>'  + this.opts.TAXAMOUNT  + '</TAXAMOUNT>' +
		'￼￼<CUSTIDENT>'  + this.opts.CUSTIDENT  + '</CUSTIDENT>' +
    '</TRANSACTION>';

	return new Promise(function(resolve, reject) {
		request({ body: req, headers: { 'Content-Length': req.length } }, function(err, response, body) {
			if(err) {
				console.log('REFUND REQUEST ERROR', err, self.opts);
				return reject({ message: err.code + ' ' + this.opts.host, error: err });
			}
			if(self.opts.debug) console.log('RAW BODY', body);
			
			toJson(body, function (err, result) {
				if(self.opts.debug) console.log('PARSED BODY', JSON.stringify(result));
				if(err) {
					console.log('REFUND PARSING ERROR', err);
					return reject({ message: err.code, error: err });
				}
				// {"TRANRESP":{"RESPTYPE":["CCCREDRESP"],"TRANSUCCESS":["FALSE"],"TRANRESPMESSAGE":["User Cancel or Timeout"],"TRANRESPERRCODE":["-100"]}}
				if(result.TRANRESP.TRANSUCCESS[0] === 'FALSE') {
					return resolve({
						'message': result.TRANRESP.TRANRESPMESSAGE[0],
						'code': result.TRANRESP.hasOwnProperty('TRANRESPERRCODE') ? result.TRANRESP.TRANRESPERRCODE[0] : null,
						'success': false,
						'transactionId': null,
						'_original': result
						});		
				} else if(result.TRANRESP.TRANSUCCESS[0] === 'TRUE') {
					return resolve({
						'success': true,
						'transactionId': result.TRANRESP.TRANSARMORTOKEN[0],
						'_original': result
						});
				} else {
					// Unsure if we can get to this case?
					return resolve({
						'success': false,
						'transactionId': null,
						'_original': result
						});
				}
						
			});
		});
	});
}

Ingenico.prototype.voidTransaction = function voidTransaction(order, creditCard, prospect, other) {
	throw new Error('VOID is not supported');
}

function convertToDecimal(amount) {
	return parseFloat(Math.round(amount * 100) / 100).toFixed(2);
}

module.exports = Ingenico;