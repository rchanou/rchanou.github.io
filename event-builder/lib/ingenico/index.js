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
	this.opts.LEGALTEXT     = opts.provider.options.LEGALTEXT     || '';
	
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
	
	// INTERACTIVEGETSIGRESP OPTIONS
	this.opts.SIGTYPE = opts.provider.options.SIGTYPE || 'INITIALS'; // "Initials" prompts non-initials?
	
	request = request.defaults({
		url: 'http://' + this.opts.host + ':' + this.opts.port,
		method: 'POST',
		timeout: 2*60*1000 + (this.opts.timeout * 1000), // "User Interactive" timeout from Ingenico is unreliable. Setting 2 min above Ingenico's to be safe.
		headers: { 'Content-Type': 'text/plain' }
		});
}

Ingenico.prototype.signature = function signature(signature) {
	var self = this;
	var req = '<TRANSACTION>' +
	'￼￼<INTERACTIVETIMEOUT>' + parseInt(self.opts.timeout) + '</INTERACTIVETIMEOUT>' +
	'  <TRANSACTIONTYPE>INTERACTIVEGETSIG</TRANSACTIONTYPE>' +
	'  <LEGALTEXT>' + signature.text + '</LEGALTEXT>' +
	'  <SIGTYPE>' + self.opts.SIGTYPE + '</SIGTYPE>' +
	'</TRANSACTION>';

	return new Promise(function(resolve, reject) {
		request({ body: req, headers: { 'Content-Length': req.length } }, function(err, response, body) {
			if(err) {
				console.log('SIG REQUEST ERROR', err, self.opts);
				return reject({ message: 'Could not contact the Ingenico device at: ' + self.opts.host, error: err });
			}
			if(self.opts.debug) console.log('RAW BODY', body);

			toJson(body, function (err, result) {
				if(self.opts.debug) console.log('PARSED BODY', JSON.stringify(result));
				if(err) {
					console.log('SIG PARSING ERROR', err);
					return reject({ message: err.code, error: err });
				}
				
				if(result.TRANRESP.TRANSUCCESS[0] === 'FALSE') {
					return resolve({
						'message': result.TRANRESP.TRANRESPMESSAGE[0],
						'code': result.TRANRESP.hasOwnProperty('TRANRESPERRCODE') ? result.TRANRESP.TRANRESPERRCODE[0] : null,
						'success': false,
						'signatureJSON': null,
						'_original': result
						});		
				} else if(result.TRANRESP.TRANSUCCESS[0] === 'TRUE') {
					
					// Remove first and last "(" then turn into proper object format
					var signatureJSON = [];
					var lastCoordinate = null;

					// Format Ingenico signature coordinates into array of (X, Y, isLastCoordinateInSegment 0 | 1)
					var coordinates = result.TRANRESP.SIGDATA[0].substring(1, result.TRANRESP.SIGDATA[0].length - 1).split(')(').map(function(ele) {
						return ele.split(',');
					});
					
					coordinates.forEach(function(coordinate, i) {
						// If lastCoordinate is null then we're starting a new stroke; Set lastCoordinate and return
						if(lastCoordinate === null) {
							lastCoordinate = coordinate;
							return;
						}
						
						// Take last coordinate and this coordinate and put in signatureJSON
						signatureJSON.push({"lx": parseInt(lastCoordinate[0]), "ly": parseInt(lastCoordinate[1]), "mx": parseInt(coordinate[0]), "my": parseInt(coordinate[1])});
						
						// If this coordinate is last one in the stroke, set lastCoordinate to null so we start a new stroke
						lastCoordinate = (parseInt(coordinate[2]) === 1) ? null : coordinate;
					});
					
					return resolve({
						'success': true,
						'signatureJSON': signatureJSON,
						'_original': result
						});
				} else {
					return resolve({
						'success': false,
						'signatureJSON': null,
						'_original': result
						});
				}
			});
		});
	});
}

Ingenico.prototype.submitTransaction = function submitTransaction(order, creditCard, prospect, other) {
	var self = this;
	var req = '<TRANSACTION>' +
			'  <TRANSACTIONTYPE>INTERACTIVECREDITAUTH</TRANSACTIONTYPE>' +
			'￼￼<INTERACTIVETIMEOUT>' + parseInt(self.opts.timeout) + '</INTERACTIVETIMEOUT>' +
			'￼￼<CREDITAMT>'     + convertToDecimal(order.amount)   + '</CREDITAMT>' +
			'￼￼<LEGALTEXT>' + this.opts.LEGALTEXT + '</LEGALTEXT>' +
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
	console.log(req);
	return new Promise(function(resolve, reject) {
		request({ body: req, headers: { 'Content-Length': req.length } }, function(err, response, body) {
			if(err) {
				console.log('CHARGE REQUEST ERROR', err, self.opts);
				return reject({ message: 'Could not contact the Ingenico device at: ' + self.opts.host, error: err });
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
						'transactionId': retrieveTransactionId(result),
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

function retrieveTransactionId(result) {
	// Use result.TRANRESP.TRANSARMORTOKEN[0], if it's not there, use result.TRANRESP.CCSYSTEMCODE[0], else null
	if(result && result.TRANRESP && result.TRANRESP.TRANSARMORTOKEN && result.TRANRESP.TRANSARMORTOKEN.length > 0) {
		return result.TRANRESP.TRANSARMORTOKEN[0];
	} else if(result && result.TRANRESP && result.TRANRESP.CCSYSTEMCODE && result.TRANRESP.CCSYSTEMCODE.length > 0) {
		return result.TRANRESP.CCSYSTEMCODE[0];
	} else {
		return null;
	}

}


Ingenico.prototype.refundTransaction = function refundTransaction(order) {
	var self = this;
	var req = '<TRANSACTION>' +
    '  <TRANSACTIONTYPE>INTERACTIVEISSUECREDIT</TRANSACTIONTYPE>' +
		'  <LEGALTEXT>' + this.opts.LEGALTEXT + '</LEGALTEXT>' +
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
				return reject({ message: 'Could not contact the Ingenico device at: ' + self.opts.host, error: err });
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
						'transactionId': retrieveTransactionId(result),
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