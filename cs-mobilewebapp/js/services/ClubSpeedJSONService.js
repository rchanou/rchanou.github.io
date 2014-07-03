//This service allows the Club Speed mobile web app to access race and racer data via JSON
angular.module('clubSpeedOnlineApp.services', [])
    .factory('ClubSpeedJSONService',['$http', function($http)
    {
        var apiURL = '/api/index.php';
        var apiKey = 'cs-dev';
        var track = 1; //TODO: Propagate this out to all methods
        return {
            getFastestLapTimes_Day: function(limit) {
                var limitString = '';
                if (typeof limit !== 'undefined')
                {
                    limitString = '&limit=' + limit;
                }
                return $http.get(apiURL + '/races/fastest.json?range=day&track=' + track + '&key=' + apiKey + limitString);
            },
            getFastestLapTimes_Week: function() {
                return $http.get(apiURL + '/races/fastest.json?range=week&track=' + track + '&key=' + apiKey) //TODO: Revert this, multitrack support
            },
            getFastestLapTimes_Month: function() {
                return $http.get(apiURL + '/races/fastest.json?range=month&track=' + track + '&key=' + apiKey);
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
            getScoreboardData: function()
            {
                return $http.get(apiURL + '/races/scoreboard.json?&track_id=1&key=' + apiKey);
            },
            getTracks: function()
            {
                return $http.get(apiURL + '/tracks/index.json?&key=' + apiKey);
            }
            //http://97.67.180.38:8080/api/index.php/tracks/index.json?key=9c55d6518880c1abf100a24da2546368
        };
    }])

    //TODO: Eliminate this is not going with socket.io
    .factory('SocketIOService', function ($rootScope) {
/*var socket = io.connect('http://192.168.111.142:8080');
        return {
            on: function (eventName, callback) {
                socket.on(eventName, function () {
                    var args = arguments;
                    $rootScope.$apply(function () {
                        callback.apply(socket, args);
                    });
                });
            },
            emit: function (eventName, data, callback) {
                socket.emit(eventName, data, function () {
                    var args = arguments;
                    $rootScope.$apply(function () {
                        if (callback) {
                            callback.apply(socket, args);
                        }
                    });
                })
            },
            disconnect: function()
            {
                if (socket.socket.connected)
                {
                    socket.disconnect();
                }
            },
            connect: function()
            {
                if (socket.socket.connected == false)
                {
                    socket = io.connect('http://192.168.111.142:8080');
                }
            }
        };*/
    });