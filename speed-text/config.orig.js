/*
Setup instructions:
- Rename config.orig.js to config.js and put in API Private Key and URL
- Run migration (no harm will come running multiple times if you are unsure)
- Install FireDaemon Service/check for error
- Enable app in admin panel
*/

var config = {}

config.clubSpeedApi = {};

config.clubSpeedApi.url = 'http://TRACKNAME.clubspeedtiming.com/api/index.php';
config.clubSpeedApi.key = '';

config.settingPollingInterval = 60000;
config.racePollingInterval = 60000;
config.debug = false;
config.disableSending = false;
config.databaseFilename = './db.json';

config.textMessaging = {};
config.textMessaging.featureIsEnabled = false;
config.textMessaging.textingIsEnabled = false;
config.textMessaging.heatsPriorToSend = 3;
config.textMessaging.cutoffHour = 8;

config.textMessaging.from = [];
config.textMessaging.message = '';
config.textMessaging.provider = '';

config.textMessaging.providerOptions = {};

module.exports = config;