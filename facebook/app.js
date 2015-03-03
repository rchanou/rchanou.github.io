var config = require('./config');
var jf = require('jsonfile');
var request = require('request');
var util = require('util');
var FB = require('fb');

// Modify Stored Proc to be disabled: GetFB_Customer
/*
USE [ClubspeedV8]
GO
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER OFF
GO

ALTER PROCEDURE [dbo].[GetFB_Customer]
(@CustID int) 
AS 
SELECT FB_CustId,FB_Customers_New.CustId,UId,Access_token,AllowEmail,AllowPost 
FROM [FB_Customers_New]
INNER JOIN [Customers] ON FB_Customers_New.CustID=Customers.CustID 
  WHERE FB_Customers_New.CustID = @CustID AND Enabled='True' AND Customers.Privacy4='True' AND 1 = 0
*/

// Cooler picture?
//http://pprlongisland.clubspeedtiming.com/api/shot/shot.php?url=pprlongisland.clubspeedtiming.com%2Fapi%2Fspeed-sheets%2Fposition-graph.html%3FHeatNo%3D8123%26CustID%3D1027600&w=190&h=230&rand=Math.random()

// Handle database for last processed file
var db = jf.readFileSync(config.databaseFilename, { throws: false });
if(db === null) { // Handle first run
	var d = new Date();
	var now = d.getTime(); // Now, local time in milliseconds
	db = {
		lastProcessedHeatFinishTime: now
	};
	jf.writeFileSync(config.databaseFilename, db);
}
log('Latest date processed ' + db.lastProcessedHeatFinishTime, 'DEBUG');

processFB();
getSettings();


function processFB() {

	/*// Disabled during testing
	if(config.facebook.featureIsEnabled === false) {
		log('Facebook Posting After Race feature is disabled', 'INFO');
		setTimeout(processFB, config.racePollingInterval);
		return;
	} else if(config.facebook.postingIsEnabled === false) {
		log('Facebook Posting After Race posting is disabled', 'INFO');
		setTimeout(processFB, config.racePollingInterval);
		return;
	}*/

	// Build query string
	var lastDateProcessedString = new Date(db.lastProcessedHeatFinishTime);
	var whereQuery = {
		heatFinishTime: {
			'$gt': toCSAPIFormat(lastDateProcessedString)
		}
	};

	var url = config.clubSpeedApiUrl + '/facebookRaces.js?where=' + JSON.stringify(whereQuery) + '&limit=' + config.recordsToProcess + '&key=' + config.clubSpeedApiKey;
	log('Checking for Facebook postings to make at: ' + url);
	
	request({ url: url, json: true }, function (error, response, body) {
		if(error) return log(util.inspect(error), 'ERROR');

		if (!error && response.statusCode == 200) {
			if(body.length === 0) return log('No Facebook postings found to process.');
			
			body.forEach(function(racer) {
				postToFacebook(racer);
			});
		}
	});
	
	setTimeout(processFB, config.racePollingInterval);
}


function postToFacebook(race) {
	race.ordinalFinishPosition = ordinalInWord(race.finishPosition);

// If Privacy4 is True Is a 1 or 0 // Convert data type? TODO

	var fbPost = {
		message: applyTemplate(config.facebook.message, race),
		link: applyTemplate(config.facebook.link, race),
		picture: applyTemplate(config.facebook.photoUrl, race),
		name: applyTemplate(config.facebook.name, race),
		description: applyTemplate(config.facebook.description, race),
		caption: applyTemplate(config.facebook.caption, race)
		};
	log('Would have posted this ' + util.inspect(fbPost) + ' for ' + util.inspect(race), 'DEBUG');
	log('Would have posted for ' + race.customerId + ' for heat # ' + race.heatId + ' this ' + util.inspect(fbPost), 'INFO');

	// Save last processed time of this record
	var currentFinishTime = new Date(race.heatFinishTime.replace('T', ' '));
	log('*****Comparing ' + currentFinishTime.getTime() + ' to ' + db.lastProcessedHeatFinishTime, 'INFO');
	if(currentFinishTime.getTime() >= db.lastProcessedHeatFinishTime) {		
		// Update our last posted race
		console.log('Updating last race to', currentFinishTime);
		db.lastProcessedHeatFinishTime = currentFinishTime.getTime() + 1000;
		jf.writeFileSync(config.databaseFilename, db);
	}
	
	FB.setAccessToken(race.token);
	/*FB.api('me/feed', 'post', fbPost, function (res) {
		if(!res || res.error) {
			log(!res ? 'error occurred' : res.error, 'ERROR');
			return;
		}
		log('Successfully posted id #: ' + res.id, 'INFO');
	});*/
}

function getSettings() {
	var url = config.clubSpeedApiUrl + '/settings.json?namespace=FacebookAfterRace&key=' + config.clubSpeedApiKey;
	log('Getting settings from: ' + url, 'DEBUG');
	log('Existing settings (prior to updating): ' + util.inspect(config), 'DEBUG');
	
	request({ url: url, json: true }, function (error, response, body) {
		if (!error && response.statusCode == 200) {
			body.settings.forEach(function(setting) {
				config.facebook[setting.name] = castValueToType(setting.value, setting.type);
			});
			log('Settings changed to: ' + util.inspect(config), 'DEBUG');
			
		} else if(error) {
			log(error, 'ERROR');
			log(response, 'ERROR');
			log(body, 'ERROR');
		}
	})

	setTimeout(getSettings, config.settingPollingInterval);
}

/**
 * Helper functions
 */

function twoDigits(d) {
    if(0 <= d && d < 10) return "0" + d.toString();
    if(-10 < d && d < 0) return "-0" + (-1*d).toString();
    return d.toString();
}

function toCSAPIFormat(date) {
    return date.getFullYear() + "-" + twoDigits(1 + date.getMonth()) + "-" + twoDigits(date.getDate()) + "T" + twoDigits(date.getHours()) + ":" + twoDigits(date.getMinutes()) + ":" + twoDigits(date.getSeconds() + "." + date.getMilliseconds());
};

function applyTemplate(str, placeholders) {
	for(var placeholder in placeholders) {
		var placeholderTag = '{{' + placeholder + '}}';
		//console.log('Looking for ' + placeholderTag + ' in ' + str + ' to replace ' + placeholders[placeholder]);
		str = str.replace(placeholderTag, placeholders[placeholder]);
	}
	//console.log('Modified string', str);
	return str;
}

function ordinalInWord( cardinal ) {
    var ordinals = [ 'zeroth', 'first', 'second', 'third', 'fourth', 'fifth', 'sixth', 'seventh', 'eighth', 'ninth', 'tenth', 'eleventh', 'twelfth', 'thirteenth', 'fourteenth', 'fifteenth', 'sixteenth', 'seventeenth', 'eighteenth', 'ninteenth', 'twentieth' ];
    var tens = {
        20: 'twenty',
        30: 'thirty',
        40: 'forty',
				50: 'fifty'
    };
    var ordinalTens = {
        30: 'thirtieth',
        40: 'fourtieth',
        50: 'fiftieth'
    };

    if( cardinal <= 20 ) {                    
        return ordinals[ cardinal ];
    }
    
    if( cardinal % 10 === 0 ) {
        return ordinalTens[ cardinal ];
    }
    // TODO: How to handle if an ordinal doesn't exist?
    return tens[ cardinal - ( cardinal % 10 ) ] + ordinals[ cardinal % 10 ];
}

function log(message, level) {
	var loggingLevel = level || 'INFO';

	if(level == 'DEBUG' && config.debug !== true) return;

	var url = config.clubSpeedApiUrl + '/logs.json?key=' + config.clubSpeedApiKey;
	var message = '[' + loggingLevel + '] ' + message;

	request({ url: url, json: true, method: 'POST', body: { terminal: 'Facebook', message: message} }, function (error, response, body) {
		if (error || response.statusCode !== 200) {
			//console.log('ERROR: ', util.inspect(error), util.inspect(response), util.inspect(body));
		}
	})
	
	console.log(Date().toString() +  ' ' + message);
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