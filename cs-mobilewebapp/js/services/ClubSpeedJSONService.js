//This service allows the Club Speed mobile web app to access race and racer data via JSON
angular.module('clubSpeedOnlineApp.services', [])
    .factory('ClubSpeedJSONService',['$http', function($http)
    {
        var apiURL, apiKey, track, excludeEmployees;
        if (typeof config !== "undefined")
        {
            apiURL = defaultFor(config.apiURL,'http://' + window.location.hostname + '/api/index.php');
            apiKey = defaultFor(config.apiKey,'cs-dev');
            track = defaultFor(config.track,1);
            excludeEmployees = defaultFor(config.excludeEmployees,true);
        }
        else //Backwards compatibility for installs that never had a config.js created
        {
            apiURL = 'http://' + window.location.hostname + '/api/index.php';
            apiKey = 'cs-dev';
            track = 1;
            excludeEmployees = true;
        }

        return {
            getFastestLapTimes_Day: function(track) {
                track = defaultFor(track,1);
                return $http.get(apiURL + '/races/fastest.json?range=day&track=' + track + (excludeEmployees ? '&exclude_employees=1' : '') + '&key=' + apiKey);
            },
            getFastestLapTimes_Week: function(track) {
                track = defaultFor(track,1);
                return $http.get(apiURL + '/races/fastest.json?range=week&track=' + track + (excludeEmployees ? '&exclude_employees=1' : '') + '&key=' + apiKey)
            },
            getFastestLapTimes_Month: function(track) {
                track = defaultFor(track,1);
                return $http.get(apiURL + '/races/fastest.json?range=month&track=' + track + (excludeEmployees ? '&exclude_employees=1' : '') + '&key=' + apiKey);
            },
            getTopRPMScores: function() {
                return $http.get(apiURL + '/racers/toprpm.json?key=' + apiKey);
            },
            getRaceDetails: function(race_id)
            {
                return $http.get(apiURL + '/races/' + race_id +  '.json?key=' + apiKey);
            },
            searchForRacer: function(racer_search_string)
            {
                return $http.get(apiURL + '/racers/search.js?query='+racer_search_string+'&key=' + apiKey);
            },
            getRacerInfo: function(racer_id)
            {
                return $http.get(apiURL + '/racers/' + racer_id + '.json?&key=' + apiKey);
            },
            getPastRaces: function(racer_id)
            {
                return $http.get(apiURL + '/racers/' + racer_id + '/races.json?&key=' + apiKey);
            },
            getScoreboardData: function(track)
            {
                return $http.get(apiURL + '/races/scoreboard.json?&track_id=' + track + '&key=' + apiKey);
            },
            getTracks: function()
            {
                return $http.get(apiURL + '/tracks/index.json?&key=' + apiKey);
            },
            getSettings: function()
            {
                return $http.get(apiURL + '/settings.json?namespace=MobileApp&key=' + apiKey);
            },
            getTranslations: function() {
                return $http.get(apiURL + '/translations?&namespace=MobileApp&key=' + apiKey);
            },
            getCurrentCulture: function() {
                return $http.get(apiURL + '/settings/get.json?group=MobileApp&setting=currentCulture&key=' + apiKey);
            }
        };
    }]);

/**
 * Adds default parameter functionality to JavaScript. Woohoo!
 * @param arg
 * @param val
 * @returns {*}
 */
function defaultFor(arg, val)
{ return typeof arg !== 'undefined' ? arg : val; }

function mergeObjects(dst) {
    angular.forEach(arguments, function(obj) {
        if (obj !== dst) {
            angular.forEach(obj, function(value, key) {
                if (dst[key] && dst[key].constructor && dst[key].constructor === Object) {
                    mergeObjects(dst[key], value);
                } else {
                    dst[key] = value;
                }
            });
        }
    });
    return dst;
};