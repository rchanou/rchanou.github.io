angular.module('speedscreen.services', [])
    .factory('globalSettings', function($location) {
    var globalSettings = {};

    var apiDriver = 'polling';
    var apiURL = 'http://' + window.location.hostname + '/api/index.php'; //Production value

    var trackOverride = null;
    var apiKey = 'cs-dev';
    var channelSource = 'old';

    if (typeof $location.search().trackOverride != 'undefined')
    {
        trackOverride = $location.search().trackOverride;
        apiURL = 'http://' + trackOverride + '.clubspeedtiming.com/api/index.php';
    }

    globalSettings.getAPIDriver = function() { return apiDriver; };
    globalSettings.setAPIDriver = function(newAPIDriver) { apiDriver = newAPIDriver; };
    globalSettings.getAPIURL = function() { return apiURL; };
    globalSettings.getAPIKey = function() { return apiKey; };
    globalSettings.getChannelSource = function() { return channelSource; };
    globalSettings.setChannelSource = function(newChannelSource) { channelSource = newChannelSource; };
    globalSettings.getTrackOverride = function() {return trackOverride};
    return globalSettings;
});