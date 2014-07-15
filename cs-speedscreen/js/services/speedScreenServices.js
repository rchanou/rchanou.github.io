angular.module('speedScreenDemoApp.services', [])
    .factory('speedScreenServices',['$http','globalVars', function($http, globalVars)
    {

        var apiURL = globalVars.getApiURL();
        var apiKey = globalVars.getApiKey();

        function updateAPIInfo()
        {
            apiURL = globalVars.getApiURL();
            apiKey = globalVars.getApiKey();
        }

        return {
            getFastestLapTimes_Day: function(limit,track) {
                updateAPIInfo();
                var limitString = '';
                if (typeof limit !== 'undefined')
                {
                    limitString = '&limit=' + limit;
                }
                var trackString = '';
                if (typeof track !== 'undefined')
                {
                    trackString = '&track=' + track;
                }
                //console.log(apiURL + '/races/fastest.json?range=day&key=' + apiKey + limitString + trackString);
                return $http.get(apiURL + '/races/fastest.json?range=day&key=' + apiKey + limitString + trackString);
            },
            getFastestLapTimes_Week: function(limit,track) {
                updateAPIInfo();
                var limitString = '';
                if (typeof limit !== 'undefined')
                {
                    limitString = '&limit=' + limit;
                }
                var trackString = '';
                if (typeof track !== 'undefined')
                {
                    trackString = '&track=' + track;
                }
                return $http.get(apiURL + '/races/fastest.json?range=week&key=' + apiKey + limitString + trackString)
            },
            getFastestLapTimes_Month: function(limit,track) {
                updateAPIInfo();
                var limitString = '';
                if (typeof limit !== 'undefined')
                {
                    limitString = '&limit=' + limit;
                }
                var trackString = '';
                if (typeof track !== 'undefined')
                {
                    trackString = '&track=' + track;
                }
                return $http.get(apiURL + '/races/fastest.json?range=month&key=' + apiKey + limitString + trackString);
            },
            getTopRPMScores: function() {
                updateAPIInfo();
                return $http.get(apiURL + '/racers/toprpm.json?key=' + apiKey);
            },
            getRaceDetails: function(race_id)
            {
                updateAPIInfo();
                return $http.get(apiURL + '/races/' + race_id +  '.json?key=' + apiKey);
            },
            searchForRacer: function(racer_search_string)
            {
                updateAPIInfo();
                return $http.get(apiURL + '/racers/search.js?query='+racer_search_string+'&key=' + apiKey);
            },
            getRacerInfo: function(racer_id)
            {
                updateAPIInfo();
                return $http.get(apiURL + '/racers/' + racer_id + '.json?&key=' + apiKey);
            },
            getPastRaces: function(racer_id)
            {
                updateAPIInfo();
                return $http.get(apiURL + '/racers/' + racer_id + '/races.json?&key=' + apiKey);
            },
            getScoreboardData: function(track_id)
            {
                updateAPIInfo();
                track_id = defaultFor(track_id,1);
                //console.log(apiURL + '/races/scoreboard.json?&track_id=' + track_id + '&key=' + apiKey);
                return $http.get(apiURL + '/races/scoreboard.json?&track_id=' + track_id + '&key=' + apiKey + '&suppress_response_codes=true');

            },
            getNextHeat: function (track_id)
            {
                updateAPIInfo();
                track_id = defaultFor(track_id,1);
                //console.log(apiURL + '/races/next.json?&track_id=1&key=' + apiKey);
                return $http.get(apiURL + '/races/next.json?&track_id=' + track_id + '&key=' + apiKey + '&suppress_response_codes=true');
            },
            getTracks: function ()
            {
                updateAPIInfo();
                return $http.get(apiURL + '/tracks/index.json?key=' + apiKey);
            },
            getSpeedScreenInfo: function (channel_id)
            {
                updateAPIInfo();
                channel_id = defaultFor(channel_id,1);
                return $http.get(apiURL + '/channel/' + channel_id + '.json?key=' + apiKey);
            }
        };
    }])
    .factory('SocketIOService', function ($rootScope) {
        /*var socket = io.connect('http://192.168.111.161:8080');
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
                    socket = io.connect('http://192.168.111.161:8080');
                }
            }
        };*/
    });

function defaultFor(arg, val)
{ return typeof arg !== 'undefined' ? arg : val; }