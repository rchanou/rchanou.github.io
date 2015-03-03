/*
Setup instructions:
- Run migration
- Switch featureIsEnabled flag in admin panel (accessible only to Support user)
- Install FireDaemon service
- View Facebook Log and ensure no errors
*/

var config = {}

/* Modify these settings */

config.clubSpeedApiUrl = ''; // http://TRACKNAME.clubspeedtiming.com/api/index.php
config.clubSpeedApiKey = ''; // PRIVATE KEY from C:\ClubSpeedApps\API\config.php
config.debug = false;

/* Should not have to be modified */

config.settingPollingInterval = 60000;
config.racePollingInterval = 60000;
config.recordsToProcess = 50;
config.databaseFilename = './db.json';

/* These are overridden periodically (see settingPollingInterval above) from the Database's "Settings" table */

config.facebook = {};
config.facebook.featureIsEnabled = false;
config.facebook.postingIsEnabled = false;
config.facebook.message = ''; // I just came in {{ordinalFinishPosition}} at the track!
config.facebook.link = ''; // http://www.clubspeed.com/customer={{customerId}}&heat={{heatId}}
config.facebook.photoUrl = ''; // http://placehold.it/350x150
config.facebook.name = '';
config.facebook.description = '';
config.facebook.caption = '';

module.exports = config;