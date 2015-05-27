var config = require('./config');
var jf = require('jsonfile');
var request = require('request');
var util = require('util');

var messageCount = 0; // Number of messages sent

var db = jf.readFileSync(config.databaseFilename, { throws: false });
if(db === null) { // Handle first run
	db = {
		lastProcessedHeatNo: 0
	};
	jf.writeFileSync(config.databaseFilename, db);
}

getSettings();
setTimeout(sendTexts, 10000); // Give settings time to pull on the first load

// Kick off the master "Send All the Texts" process
function sendTexts() {
	if(config.textMessaging.isEnabled === false) {
		log('Text messaging feature is disabled', 'INFO');
		setTimeout(sendTexts, config.racePollingInterval);
		return;
	} else if(config.textMessaging.textingIsEnabled === false) {
		log('Text messaging sending is disabled', 'INFO');
		setTimeout(sendTexts, config.racePollingInterval);
		return;
	}

	var upcomingHeatsUrl = config.clubSpeedApi.url + '/races/upcoming.json?key=' + config.clubSpeedApi.key;

	if(typeof config.trackId !== 'undefined') {
		upcomingHeatsUrl = upcomingHeatsUrl + '&track=' + config.trackId;
	} else {
		upcomingHeatsUrl = upcomingHeatsUrl + '&track=1';
	}

	log('Getting upcoming heats from: ' + upcomingHeatsUrl, 'INFO');

	// Get upcoming heats -- http://TRACK_DOMAIN.clubspeedtiming.com/api/index.php/races/upcoming.json?key=cs-dev
	request({ url: upcomingHeatsUrl, json: true }, function (err, response, body) {
		if(err) {
			log('Error getting upcoming heats: ' + util.inspect(err), 'ERROR');
		} else if(!err && response.statusCode == 200) {
			if(body.hasOwnProperty('races')) findHeatToSendFor(body.races);
		} else {
			log('Error getting upcoming heats: ' + util.inspect(response), 'ERROR');
		}
	});
	
	setTimeout(sendTexts, config.racePollingInterval);
}

// Given a list of heats, find the heat we want to send to (if we haven't already sent messages for it)
function findHeatToSendFor(heats) {
	log('Received upcoming heats: ' + util.inspect(heats), 'DEBUG');
	
	// Find the Nth one that happens today
	if(heats.length >= config.textMessaging.heatsPriorToSend) {
		var heatToSend = heats[config.textMessaging.heatsPriorToSend - 1];
		log('Found heat #' + heatToSend.race.id + ' that is ' + config.textMessaging.heatsPriorToSend + ' heat(s) from now potentially eligible for sending', 'INFO');
		log(util.inspect(heatToSend), 'DEBUG');
		
		/* Determine if heat happens "tomorrow" (if so, do not send) */
		var now = new Date();
		var cutoff = new Date();
		cutoff.setHours(config.textMessaging.cutoffHour); // SET HOURS HERE
		cutoff.setMinutes(0);
		cutoff.setSeconds(0);
		if(now - cutoff > 0) {
			cutoff.setDate(cutoff.getDate() + 1); // Adjust for end of week
		}
		
		var heatStartsAt = new Date(heatToSend.race.starts_at_iso);

		if(heatStartsAt > cutoff) {
			log('Not sending because heat starts after the cutoff time', 'DEBUG');
			return null;
		}
		/* END *tomorrow* check */
		
		if(db.lastProcessedHeatNo !== parseInt(heatToSend.race.id)) {
			if(heatToSend.race.racers.length === 0) {
				log('No racers to send to for Heat #' + heatToSend.race.id + ' at ' + heatToSend.race.starts_at, 'INFO');
				return null;
			}
			
			log('Found ' + heatToSend.race.racers.length + ' racers to process', 'DEBUG');
			db.lastProcessedHeatNo = parseInt(heatToSend.race.id);
			findRacerNumbersToSendTo(heatToSend.race.racers);
		} else {
			log('Already processed ' + heatToSend.race.id, 'DEBUG');
		}
	} else {
		log('Number of upcoming heats ('+heats.length+') is less than heats in advance ('+config.textMessaging.heatsPriorToSend+')', 'DEBUG');
	}
	
	return null;
}

// Find the customers that we should send text messages to
function findRacerNumbersToSendTo(racers) {
	var mobileNumbers = [];
	
	racers.forEach(function(racer) {
		// Get customer's numbers -- http://TRACK_NAME.clubspeedtiming.com/api/index.php/customers/CUST_ID_HERE.json?key=cs-dev
		var customerUrl = config.clubSpeedApi.url + '/customers/' + racer.id + '.json?key=' + config.clubSpeedApi.key;
		request({ url: customerUrl, json: true }, function (err, response, body) {
			if(err) {
				log('Error getting customer: ' + util.inspect(err), 'ERROR');
			} else if(!err && response.statusCode == 200) {
				//if(body.hasOwnProperty('races')) findHeatToSendFor(body.races);
				log('Found customer: ' + util.inspect(body), 'DEBUG');
				var customer = body.customers[0];
				var customerPhone = customer.mobilephone || customer.phoneNumber || null;
				if(customerPhone === null) {
					log('No phone number found, cannot send to customer ' + customer.firstname + ' ' + customer.lastname + ' ID: ' + customer.customerId, 'INFO');
				} else {
					sendMessage(customerPhone, config.textMessaging.message, config.textMessaging.from);
				}
			} else {
				log('Error getting customer: ' + util.inspect(response), 'ERROR');
			}
		});
	});
	
	jf.writeFileSync(config.databaseFilename, db); // TODO: Needs to be wrapped in a promise on the sendMessage calls all completing
}

function getSettings() {
	var url = config.clubSpeedApi.url + '/settings.json?namespace=SpeedText&key=' + config.clubSpeedApi.key;
	log('Getting settings from: ' + url, 'DEBUG');
	log('Existing settings (prior to updating): ' + util.inspect(config), 'DEBUG');
	
	request({ url: url, json: true }, function (error, response, body) {
		if (!error && response.statusCode == 200) {
			body.settings.forEach(function(setting) {
				config.textMessaging[setting.name] = castValueToType(setting.value, setting.type);
			});
			log('Settings changed to: ' + util.inspect(config), 'DEBUG');
			
		} else if(error) {
			log(JSON.stringify(error), 'ERROR');
			log(JSON.stringify(response), 'ERROR');
			log(body, 'ERROR');
		}
	})

	setTimeout(getSettings, config.settingPollingInterval);
}

function castValueToType(value, type) {
	switch(type) {
		case 'Boolean':
			value = value == '1' ? true : false;
			break;
		case 'Integer':
			value = parseInt(value);
			break;
		case 'JSON':
			value = JSON.parse(value);
			break;
		case 'String':
		default:
			value = value; // No conversion
	}
	return value;
}

function sendMessage(to, message, fromArray) {
	if(to.length == 0)      return log('Cannot send, "to" number is empty: "' + to + '"', 'ERROR');
	if(message.length == 0) return log('Cannot send, message is empty: "' + message + '"', 'ERROR');

	switch(config.textMessaging.provider) {
		case 'twilio':
			sendTwilioMessage(to, message, fromArray, config.textMessaging.providerOptions);
			break;
		case 'bulksms':
			sendBulkSMSMessage(to, message, fromArray, config.textMessaging.providerOptions);
			break;
		default:
			log('Unsupported text messaging provider: ' + config.textMessaging.provider, 'ERROR');
	}
}

function sendTwilioMessage(to, message, fromArray, opts) {
	var twilioClient = require('twilio')(opts.sid, opts.token);	
	
	// Remove anything not a +, <space> or number
	to = to.replace(/[^0-9\s\+]/g,'');
	
	// If country code override is set, and string doesn't start with a +, append one.
	// Also realize that country codes can be 1, 2 or 3 (or more?) digits.
	//
	// Three cases to cover (using +61 Australia as an example):
	//   1. 1231234 (append +61)
	//   2. 611231234 (append +)
	//   3. +611231234 (pass through)
	if(config.prependCountryCode && to.charAt(0) !== '+') {
		var countryCode = config.prependCountryCode.substring(1); // Returns just "61"
		if(to.indexOf(countryCode) === 0) { // Number starts with "61", needs a "+"
			to = '+'.concat(to);
		} else { // Number does not start with 61, needs "+61" (and 0 removed if in Australia)
			if(config.prependCountryCode === '+61' && to.charAt(0) == '0') to = to.substring(1); // Remove leading 0 in Australia -- others?
			to = config.prependCountryCode.concat(to);
		}
	}
	
	var fromNumber = (typeof fromArray === 'string') ? fromArray : fromArray[messageCount % fromArray.length];
	messageCount++; // Increment the message counter

	log('Attempting to send "' + message + '" to "' + to + '", from "' + fromNumber + '"', 'INFO');
	
	if(config.disableSending) {
		log('Sending disabled in config file!', 'INFO');
		return;
	}
	twilioClient.messages.create({
			body: message,
			to: to,
			from: fromNumber
	}, function(err, message) {
			if(err) {
				log(JSON.stringify(err), 'ERROR');
			} else {
				log('Message sent! ' + JSON.stringify(message));
			}
	});
}

function sendBulkSMSMessage(to, message, fromArray, opts) {
		to = to.replace(/\D/g,'');
	
		log('Attempting to send "' + message + '" to "' + to + '"', 'INFO');
		
		if(config.disableSending) return log('Sending disabled in config file!', 'INFO');
		if(opts.username.length == 0) return log('No BulkSMS username given', 'ERROR');
		if(opts.password.length == 0) return log('No BulkSMS password given', 'ERROR');
		
		var url = opts.url || 'http://usa.bulksms.com/eapi/submission/send_sms/2/2.0';
		var formData = {
			username: opts.username,
			password: opts.password,
			message: message,
			msisdn: to
		}

		request.post({url: url, form: formData }, function(err, httpResponse, body) {
				if(err) return log(JSON.stringify(err), 'ERROR');
				
				log(body);
		});

}

function log(message, level) {
	var loggingLevel = level || 'INFO';

	if(level == 'DEBUG' && config.debug !== true) return;

	var url = config.clubSpeedApi.url + '/logs.json?key=' + config.clubSpeedApi.key;
	var message = '[' + loggingLevel + '] ' + message;

	request({ url: url, json: true, method: 'POST', body: { terminal: 'SpeedText', message: message} }, function (error, response, body) {
		if (error || response.statusCode !== 200) {
			console.log('ERROR: ', util.inspect(error), util.inspect(response), util.inspect(body));
		}
	})
	
	console.log(Date().toString() +  ' ' + message);
}