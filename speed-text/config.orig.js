/*
Setup instructions: (all from C:\ClubSpeedApps\speed-text)
- Rename config.orig.js to config.js and put in API Private Key and API URL (can find this in C:\ClubSpeedApps\api\config.php)
- Run migration (no harm will come running multiple times if you are unsure): https://TRACKNAME.clubspeedtiming.com/api/migrations/201502091516%20-%20SpeedText%20Settings.php
- Drag XML file in directory into Fire Daemon to setup service
- Enable "Feature for Track" checkbox in admin panel: https://TRACKNAME.clubspeedtiming.com/admin/speedtext/settings
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
config.textMessaging.isEnabled = false;
config.textMessaging.textingIsEnabled = false;
config.textMessaging.heatsPriorToSend = 3;
config.textMessaging.cutoffHour = 8;

config.textMessaging.from = [];
config.textMessaging.message = '';
config.textMessaging.provider = '';

config.textMessaging.providerOptions = {};

module.exports = config;