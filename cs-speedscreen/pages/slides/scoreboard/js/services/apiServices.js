angular.module('scoreboardApp.services', [])
    .factory('apiServices',['$http','$routeParams', '$location', function($http,$routeParams,$location)
    {
        var apiURL = 'http://' + window.location.hostname + '/api/index.php'; //Production value
        var apiKey = 'cs-dev';

        var track_id = $routeParams.track_id == null ? 1 : $routeParams.track_id; //Original determination, executed once

        if (typeof $location.search().trackOverride != 'undefined')
        {
            apiURL = 'http://' + $location.search().trackOverride + '.clubspeedtiming.com/api/index.php';
        }

        return {
            getScoreboard: function()
            {
                var track_id = $routeParams.track_id == null ? 1 : $routeParams.track_id; //May have changed
                return $http.get(apiURL + '/races/scoreboard.json?&track_id=' + track_id + '&key=' + apiKey + '&suppress_response_codes=true');
            },
            getActiveRaceLapCount: function(track)
            {
                return $http.get(apiURL + '/activeRaceLapCount.json/' + track + '?&key=' + apiKey + '&suppress_response_codes=true');
            },
            getFastestLapTimes: function(range) {
                return $http.get(apiURL + '/races/fastest.json?exclude_employees=1&limit=4&range=' + range + '&track=' + track_id + '&key=' + apiKey);
            },
            getFinalResults: function(heat_id)
            {
                return $http.get(apiURL + '/races/scoreboard.json?&heat_id=' + heat_id + '&key=' + apiKey + '&suppress_response_codes=true');
            },
            getNextRace: function()
            {
                var track_id = $routeParams.track_id == null ? 1 : $routeParams.track_id; //May have changed
                return $http.get(apiURL + '/races/next.json?&track_id=' + track_id + '&offset=0&key=' + apiKey + '&suppress_response_codes=true');
            },
            getTranslations: function() {
                return $http.get(apiURL + '/translations?&namespace=Scoreboard&key=' + apiKey);
            }
        };
    }]);
