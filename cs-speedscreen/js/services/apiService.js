angular.module('speedscreen.services')
    .factory('apiService',['$http', '$routeParams', 'globalSettings', 'apiDriverPolling', function($http, $routeParams, globalSettings, apiDriverPolling)
    {
        var apiURL = globalSettings.getAPIURL();
        var apiKey = globalSettings.getAPIKey();
        var currentChannel = $routeParams.channel_id == null ? 1 : $routeParams.channel_id;

        var apiDriverName = globalSettings.getAPIDriver();
        var apiDrivers = {
            'polling' : apiDriverPolling //The old Speed Screen's polling method
        };
        var apiDriver = apiDrivers[apiDriverName];

        return {
            verifyConnectivityToServerAndFetchSettings: function(timeout) {
                var config = {};
                if (timeout) {
                    config.timeout = timeout;
                }
                return $http.get(apiURL + '/settings.json?namespace=Speedscreen&key=' + apiKey, config);
            },
            getChannelLineUp: function() {
                return apiDriver.getChannelLineUp(currentChannel);
            },
            getScoreboard: function(track) {
                return apiDriver.getScoreboard(track);
            },
            getCurrentRaceId: function(track) {
                return $http.get(apiURL + '/races/current_race_id.js?track=' + track + '&key=' + apiKey);
            },
            getTranslations: function() {
                return $http.get(apiURL + '/translations?&namespace=Speedscreen&key=' + apiKey);
            },
            checkIfBackgroundWasUploaded: function () {
                return $http.head('http://' + window.location.hostname + '/assets/cs-speedscreen/images/background_1080p.jpg');
            }
        };
    }]);
