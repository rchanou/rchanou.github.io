/**
    ClubSpeed ApiService class definition.

    Library Requirements:
        1. zutil
            a. assert
            b. base
            c. check
            d. convert
            e. log
        2. jQuery
*/
;(function(w, z, $, undefined) {
    var clubspeed = w.clubspeed = (w.clubspeed || {}); // implement or collect pointer for the clubspeed namespace
    clubspeed.classes = clubspeed.classes || {}; // implement or collect pointer for the clubspeed.classes namespace

    var check = z.check;
    var exists = z.check.exists;
    var convert = z.convert;

    /**
        A wrapper class used to make api calls to ClubSpeed servers.

        @class Contains an api interface.
     */
    var ApiService = (function() {

        /**
            Creates a new ApiService class.

            @constructor
            @param object setup A required object containing values to use to set up the api.
            @param string setup.url A required string representing the base url for the api.
            @param [string] setup.key The api key to use.
        */
        function ApiService(setup) {
            var self = this;
            var log = z.log;
            var events = new z.classes.Events();

            var getMostImproved = function(options) {
                var data = {
                    range: options.range || getMostImproved.defaults.range,
                    limit: z.convert(options.limit, z.types.number) || getMostImproved.defaults.limit,
                };
                if (z.check.exists(options.year)) {
                    data.year = z.convert(options.year, z.types.number);
                }
                return sendRequest({
                    api: "racers/most_improved_rpm.json",
                    type: "GET",
                    data: data,
                    // cache: z.coalesce(options.cache, true) // default to true
                });
            }.extend({
                defaults: {
                    range: "month",
                    limit: 10
                },
                poll: function(options) {
                    return _poll(getMostImproved, options);
                }
            });

            var getNextRace = function(options) {
                return sendRequest({
                    api: "races/next.json",
                    type: "GET",
                    data: {
                        track: z.convert(options.track, z.types.number) || getNextRace.defaults.track,
                        offset: z.convert(options.offset, z.types.number) || getNextRace.defaults.offset
                    }
                });
            }.extend({
                defaults: {
                    track: 1,
                    offset: 0
                },
                poll: function(options) {
                    return _poll(getNextRace, options);
                }
            });

            var getPreviousRace = function(options) {
                return sendRequest({
                    api: "races/previous.json",
                    type: "GET",
                    data: {
                        track: z.convert(options.track, z.types.number) || getPreviousRace.defaults.track,
                        offset: z.convert(options.offset, z.types.number) || getPreviousRace.defaults.offset
                    }
                });
            }.extend({
                defaults: {
                    track: 1,
                    offset: 0
                },
                poll: function(options) {
                    return _poll(getPreviousRace, options);
                }
            });

            var getRaceDetails = function(options) {
                return sendRequest({
                    api: "races/" + options.raceId + ".json",
                    type: "GET",
                    // cache: z.coalesce(options.cache, true) // default to true
                });
            }.extend({
                poll: function(options) {
                    return _poll(getRaceDetails, options);
                }
            });

            var getSchedule = function(options) {
                var data = {
                    // no items needed!
                };
                return sendRequest({
                    api: "races/upcoming.json",
                    type: "GET",
                    data: {
                        track: options.track || getSchedule.defaults.track
                    }
                });
            }.extend({
                defaults: {
                    track: 1 // move this from a default!!
                },
                poll: function(options) {
                    return _poll(getSchedule, options);
                }
            });

            var getTopProskill = function(options) {
                var data = {
                    limit: z.convert(options.limit, z.types.number) || getTopProskill.defaults.limit
                }
                if (z.check.exists(options.gender)) {
                    // note: supplying $.ajax() with a null or undefined parameter
                    // results in the key still being added to the url call
                    // this will result in an API error as of 7/18/2014
                    // make sure gender as a parameter exists before adding anything (DL)
                    data.gender = options.gender.toString().charAt(0).toLowerCase();
                }
                return sendRequest({
                    api: "racers/toprpm.json",
                    type: "GET",
                    data: data
                });
            }.extend({
                defaults: {
                    limit: 10,
                },
                poll: function(options) {
                    return _poll(getTopProskill, options);
                }
            });

            var getTopTimes = function(options) {
                var data = {
                    track: options.track || getTopTimes.defaults.track,
                    range: options.range || getTopTimes.defaults.range,
                    limit: options.limit || getTopTimes.defaults.limit
                };
                if (z.check.exists(options.speed_level)) {
                    var speed_level = z.convert(options.speed_level, z.types.number);
                    // if (speed_level > 0) // taking out for testing
                        data.speed_level = speed_level;
                }
                return sendRequest({
                    api: "races/fastest.json",
                    type: "GET",
                    data: data
                });
            }.extend({
                defaults: {
                    range: "week",
                    track: 1, // note that topTimes api can technically NOT have a trackId, and will return a combination of all tracks
                    limit: 10
                },
                poll: function(options) {
                    return _poll(getTopTimes, options);
                }
            });

            var getTracks = function() {
                return sendRequest({
                    api: "tracks/index.json",
                    type: "GET"
                });
            }.extend({
                poll: function(options) {
                    return _poll(getPreviousRace, options);
                }
            });

            /**
                DEFINITION TBD
            */
            var _innerPoll = function(method, options, timeout) {
                setTimeout(function() {
                    log.debug("  ---     Polling for data:");
                    method(options).then(
                        function(data) {
                            // success pipe
                            if (options.callback.length === 1) {
                                // assume sync
                                var ret = options.callback(data);
                                if (!exists(ret) || convert.toBoolean(ret)) {
                                    // callback function returned either undefined/nothing
                                    // or a truthy value -- assume polling should continue
                                    // until a falsy value other than undefined or null is returned
                                    _innerPoll(method, options, timeout);
                                }
                            }
                            else {
                                // assume async
                                options.callback(data, function(ret) {
                                    if (!exists(ret) || convert.toBoolean(ret))
                                        _innerPoll(method, options, timeout);
                                });
                            }
                        },
                        function(data) {
                            if (!options.errback)
                                return _innerPoll(method, options, timeout); // assume we should keep polling if no errback provided
                            if (options.errback.length === 1) {
                                var ret = options.errback(data);
                                if (!exists(ret) || convert.toBoolean(ret))
                                    return _innerPoll(method, options, timeout);
                            }
                            else {
                                options.errback(data, function(ret) {
                                    if (!exists(ret) || convert.toBoolean(ret))
                                        return _innerPoll(method, options, timeout);
                                });
                            }
                        }
                    );
                }, timeout);
            }
            var _poll = function(method, options) {
                // run assertions and set defaults/overrides
                z.assert.isFunction(method); // required
                var timeout = options.timeout || 5000;
                options = options || {};

                // call _innerPoll to prevent assertions/defaults from running every time
                _innerPoll(method, options, timeout);
            }

            function apiSuccessHandler(data, textStatus, xhr) {
                log.debug("  ---  Return success from: " + xhr.url + " (" + xhr.status + ")");
                log.debug(xhr);
            }
            function apiFailHandler(xhr, textStatus, errorThrown) {
                log.error("  ---  Return failure from: " + xhr.url + " (" + xhr.status + "): " + textStatus);
                log.error(xhr);
            }
            function apiThenHandler(data, textStatus, xhr) {
                data.url = xhr.url;
                return data;
            }
            function apiAlwaysHandler(dataOrXhr, textStatus, xhrOrErrorThrown) {
                // sw.stop();
            }
            function buildRequest(requestInfo) {
                var r = {};
                r.async = z.check.exists(requestInfo.async) ? z.convert(requestInfo.async, z.types.boolean) : true; // default async to true
                r.type = requestInfo.type || "GET"; // use default type of "GET"
                r.url = self.data.url + requestInfo.api;
                r.data = requestInfo.data || {};
                r.data.key = self.data.key;
                // note: don't send requestInfo.cache -- this will cause jQuery to add a Math.random() to the query string
                r.beforeSend = function(xhr, settings) {
                    xhr.url = settings.url; // store the url for debugging purposes
                }
                return r;
            }

            function sendRequest(requestInfo) {
                var builtInfo = buildRequest(requestInfo);
                var fullPath = builtInfo.url + (builtInfo.data ? ("?" + $.param(builtInfo.data)) : "");
                log.debug("  ---   Sending request to: " + fullPath);
                var sw = new z.classes.StopwatchWrapper(  "  ---        API call time: " + fullPath); // declare a separate stopwatch for each call
                var request = $.ajax(builtInfo);
                request.done(apiSuccessHandler);
                request.fail(apiFailHandler);
                request.then(apiThenHandler);
                request.always(function() {
                    sw.stop();
                });
                return request;
            }

            // default setup and assertions
            (function() {
                z.assert.exists(setup);
                z.assert.exists(setup.url);
                self.data = {
                    key: setup.key || 'cs-dev', // fallback to cs-dev
                    url: setup.url,
                };
            })();

            return (function(apiObj) {
                // expose the internal functions as pointers on a new object to be sent back up the chain to be the ApiService class
                z.defineProperty(apiObj, "getMostImproved", { get: function() { return getMostImproved; }, writeable: false });
                z.defineProperty(apiObj, "getNextRace", { get: function() { return getNextRace; }, writeable: false });
                z.defineProperty(apiObj, "getPreviousRace", { get: function() { return getPreviousRace; }, writeable: false });
                z.defineProperty(apiObj, "getRaceDetails", { get: function() { return getRaceDetails; }, writeable: false });
                z.defineProperty(apiObj, "getSchedule", { get: function() { return getSchedule; }, writeable: false });
                z.defineProperty(apiObj, "getTopProskill", { get: function() { return getTopProskill; }, writeable: false });
                z.defineProperty(apiObj, "getTopTimes", { get: function() { return getTopTimes; }, writeable: false });
                z.defineProperty(apiObj, "getTracks", { get: function() { return getTracks; }, writeable: false });
                return apiObj;
            })({});

        }

        return ApiService;

    })();

    clubspeed.classes.ApiService = ApiService;

})(window || this /* object on which to declare clubspeed */, z /* zUtil */, $ /* jquery */);
