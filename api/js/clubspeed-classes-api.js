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
(function(w, z, $, undefined) {
    var clubspeed = w.clubspeed = (w.clubspeed || {}); // implement or collect pointer for the clubspeed namespace
    clubspeed.classes = clubspeed.classes || {}; // implement or collect pointer for the clubspeed.classes namespace

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
            var sw = new z.classes.StopwatchStack(); // make a self-contained stopwatch class for internal timing
            var log = z.log;

            var getMostImproved = function(range, limit, year) {
                return sendRequest({ 
                    api: "racers/most_improved_rpm.json",
                    type: "GET",
                    data: {
                        range: range || getMostImproved.defaults.range,
                        limit: z.convert(limit, z.types.number) || getMostImproved.defaults.limit,
                        year: z.convert(year, z.types.number) || getMostImproved.defaults.year
                    }
                });
            }.extend({
                defaults: {
                    range: "month",
                    limit: 10,
                    year: undefined
                }
            });

            var getNextRacers = function(track, offset) {
                return sendRequest({
                    api: "races/next.json",
                    type: "GET",
                    data: {
                        track: z.convert(track, z.types.number) || getNextRacers.defaults.track,
                        offset: z.convert(offset, z.types.number) || getNextRacers.defaults.offset
                    }
                });
            }.extend({
                defaults: {
                    track: 1,
                    offset: 0
                }
            });

            var getRaceDetails = function(raceId) {
                return sendRequest({
                    api: "races/" + raceId + ".json",
                    type: "GET"
                });
            };

            var getTopProskill = function(limit, range, gender) {
                var data = {
                    limit: z.convert(limit, z.types.number) || getTopProskill.defaults.limit
                }
                if (z.check.exists(gender)) {
                    // note: supplying $.ajax() with a null or undefined parameter
                    // results in the key still being added to the url call
                    // this will result in an API error as of 7/18/2014 (DL)
                    data.gender = gender.toString().charAt(0).toLowerCase();
                }
                return sendRequest({
                    api: "racers/toprpm.json",
                    type: "GET",
                    data: data
                })
            }.extend({
                defaults: {
                    limit: 10,
                }
            });

            var getTopTimes = function(range, limit) {
                return sendRequest({
                    api: "races/fastest.json",
                    type: "GET",
                    data: {
                        range: range || getTopTimes.defaults.range,
                        limit: limit || getTopTimes.defaults.limit
                    }
                });
            }.extend({
                defaults: {
                    range: "week",
                    limit: 10
                }
            });

            var getTracks = function() {
                return sendRequest({
                    api: "tracks/index.json",
                    type: "GET"
                });
            };

            function apiSuccessHandler(data, textStatus, xhr) {
                z.assert(function() { return z.equals(data, xhr.responseJSON); });
                z.assert(function() { return z.equals(textStatus, "success"); });
                z.assert(function() { return z.equals(xhr.status, 200); });
                log.debug("  ---  Return success from: " + xhr.url + " (" + xhr.status + ")");
                log.debug(xhr);
            }
            function apiFailHandler(xhr, textStatus, errorThrown) {
                log.error("  ---  Return failure from: " + xhr.url + " (" + xhr.status + "): " + textStatus);
                log.error(xhr);
            }
            function apiThenHandler(data, textStatus, xhr) {
                return data; // for automated piping purposes -- problematic?
            }
            function apiAlwaysHandler(dataOrXhr, textStatus, xhrOrErrorThrown) {
                // sw.stop();
            }
            function buildRequest(requestInfo) {
                requestInfo.async = z.check.exists(requestInfo.async) ? z.convert(requestInfo.async, z.types.boolean) : true; // default async to true
                requestInfo.type = requestInfo.type || "GET"; // use default type of "GET"
                requestInfo.url = self.data.url + requestInfo.api;
                requestInfo.data = requestInfo.data || {};
                requestInfo.data.key = self.data.key;
                requestInfo.beforeSend = function(xhr, settings) {
                    xhr.url = settings.url; // store the url for debugging purposes
                }
                return requestInfo;
            }

            function sendRequest(requestInfo) {
                var builtInfo = buildRequest(requestInfo);
                var fullPath = builtInfo.url + (builtInfo.data ? ("?" + $.param(builtInfo.data)) : "");
                log.debug("  ---   Sending request to: " + fullPath);
                var sw = new z.classes.StopwatchWrapper(  "  ---              Calling: " + fullPath); // declare a separate stopwatch for each call
                return $.ajax(builtInfo)
                    .done(apiSuccessHandler)
                    .fail(apiFailHandler)
                    .then(apiThenHandler)
                    // .always(apiAlwaysHandler)
                    .always(function() {
                        sw.stop();
                    });
            }

            // default setup and assertions
            (function() {
                z.assert.exists(setup);
                z.assert.exists(setup.url);
                self.data = {
                    key: setup.key || 'cs-dev', // fallback to cs-dev
                    url: setup.url
                }
            })();

            return (function(apiObj) {
                // expose the internal functions as pointers on a new object to be sent back up the chain to be the ApiService class
                z.defineProperty(apiObj, "getMostImproved", { get: function() { return getMostImproved; }, writeable: false });
                z.defineProperty(apiObj, "getNextRacers", { get: function() { return getNextRacers; }, writeable: false });
                z.defineProperty(apiObj, "getRaceDetails", { get: function() { return getRaceDetails; }, writeable: false });
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
