/*
Setup instructions:
- Run migration
- Switch featureIsEnabled flag in admin panel (accessible only to Support user)
*/

var config = {}

/* Modify these settings */

config.clubSpeedApiUrl = '';
config.clubSpeedApiKey = '';
config.debug = false;

config.settingPollingInterval = 60000;
config.racePollingInterval = 60000;
config.recordsToProcess = 3;
config.databaseFilename = './db.json';

/* These are overridden periodically (see settingPollingInterval above) from the Database's "Settings" table */

config.facebook = {};
config.facebook.featureIsEnabled = false;
config.facebook.postingIsEnabled = false;
config.facebook.message = 'I just came in {{ordinalFinishPosition}} at the track!';
config.facebook.link = 'http://www.clubspeed.com/customer={{customerId}}&heat={{heatId}}';
config.facebook.photoUrl = 'http://placehold.it/350x150';
config.facebook.name = '';
config.facebook.description = '';
config.facebook.caption = '';

module.exports = config;