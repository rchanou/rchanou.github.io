angular.module('speedscreen.services')
    .factory('apiDriverPolling',['$http','globalSettings', function($http,globalSettings)
    {
        var apiURL = globalSettings.getAPIURL();
        var apiKey = globalSettings.getAPIKey();

        return {
            getChannelLineUp: function(channel_number) {
                var channelSource = globalSettings.getChannelSource();
                if (channelSource == 'old')
                {
                    return $http.get(apiURL + '/channel/' + channel_number + '.json?key=' + apiKey);
                }
                else// if (channelSource == 'new')
                {
                    return $http.get(apiURL + '/speedscreenchannels.json?where={%22channelNumber%22:' + channel_number + '}&key=' + apiKey);
                }
            },
            getScoreboard: function(track) {
                return $http.get(apiURL + '/races/scoreboard.json?track_id=' + track + '&key=' + apiKey);
            }
        };
    }]);