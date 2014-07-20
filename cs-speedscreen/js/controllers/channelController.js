/**
 * Club Speed Speed Screen - Channel Controller
 *
 * This AngularJS controller handles the entirety of the Speed Screen logic:
 *
 * 1. Ask a track for its channel lineup. Defaults to Channel 1, but can be specified as a URL parameter:
 * (Ex. cs-speedscreen/#/ or cs-speedscreen/#/1 or cs-speedscreen/#/2)
 *
 * 2. Parse the channel lineup, only accepting (as of 4/15/2014) slide types "image" and "scoreboard".
 *
 * 3. If there is no "scoreboard" slide set to 86400000 duration (infinite):
 * 3a. Loop through each slide in the parsed order, for the appropriate duration per-slide. (Skip the scoreboard if no race is happening)
 * 3b. Poll, once per second, the track to see if there is a race on-going.
 * 3c. If there is a race on-going, immediately skip to the scoreboard slide, and stay there until:
 *      - The race is complete, AND
 *      - The slide has been idle for the time specified, after the race has completed
 *
 * 4. Else, if there is a "scoreboard" slide set to 86400000 duration (infinite):
 * 4a. Remove any other slides that the user may have accidentally included.
 * 4b. Only display the "scoreboard" slide.
 *
 * 5. Once per minute, poll the track for any changes to the channel lineup. If there are any, make the appropriate changes.
 *
 * BONUS FEATURE: If the channel in the URL is set to "scoreboard" instead of a number, the Speed Screen won't bother polling for
 * a channel configuration and will instead just permanently stay on the scoreboard.
 */

//TODO: Before release, get the stored procedure fixed for sorting racers by position in position races
//TODO: Background image... meant for scoreboard?

speedScreenDemoApp.controller('channelController', function($scope, $timeout, $interval, $routeParams, speedScreenServices, globalVars, $sce) {

    //#################
    //# CONFIGURATION #
    //#################
    var apiURL = config.apiURL;
    var apiKey = config.apiKey;
    $scope.showTimer = defaultFor(config.showTimer, true);
    $scope.showHeatNumberInsteadOfRaceTime = defaultFor(config.showHeatNumberInsteadOfRaceTime, false);

//    If testing a specific track is desired:
/*    var apiURL = 'http://ftikcincinnati.clubspeedtiming.com/api/index.php';
    var apiKey = 'cs-dev';*/

    var timeBetweenChannelUpdatesMs = 60000;

    //#######################
    //# KEY STATE VARIABLES #
    //#######################
    var slides = [];
    var scoreboardSlide = 0; //Index
    var hasScoreboard = false;
    var channelHash = "";
    var currentTimeOut = undefined;
    var currentChannel = new Channel();
    var scoreboardIsCurrentlyIdle = false;
    var scoreboardTotalIdleTimeMs = 0;
    var scoreboardStartedBeingIdleTimeMs = 0;
    var raceWasJustHappening = false;
    var disableAnimations = false;
    if ($routeParams.channel_options == 'disableAnimations')
    {
        disableAnimations = true;
    }

    //################################
    //# INITIALIZATION AND EXECUTION #
    //################################
    $interval(function(){pollForRaces();},1000);
    currentChannel.initializeSlides($routeParams.channel_id); //Passed in via the URL, defaults to 1
    currentChannel.checkForOnGoingRace();
    currentChannel.run();

    // ##############
    // # KEY MODELS #
    // ##############
    function Channel()
    {
        //#######################
        //# KEY STATE VARIABLES #
        //#######################
        this.nextSlide = 0;
        this.currentSlide = 0;
        this.currentTrack = 1;
        this.apiKey = apiKey;
        this.apiURL = apiURL;

        //Notify all slides of the current API URL and API key
        globalVars.setApiKey(this.apiKey);
        globalVars.setApiURL(this.apiURL);

        /**
         * This function is called when the Channel first executes, and is re-called whenever it is time
         * to switch to a different slide. It handles the logic described above at the top of this page.
         * Its default behavior is to:
         *  - Tell the view what the current slide's information is
         *  - Queue up the next slide
         * It makes exceptions based on whether or not a race is running.
         */
        Channel.prototype.run = function()
        {
            if (slides.length == 0 || slides[this.nextSlide] == undefined) //If there are no slides available or something changed, check again in 5 seconds
            {
                cancelTimeouts();
                currentTimeOut = $timeout(function(){currentChannel.run();},5000);
                return;
            }

            if (globalVars.getStop() !== undefined && globalVars.isTimeToStop()) //If a slide has requested that its timeouts/intervals be killed
            {
                var stop = globalVars.getStop(); //Stop the previous slide's timeouts and intervals
                stop();
                globalVars.resetStop();
            }

            //console.log(slides);
            //console.log('Current resourceURL: ' + slides[this.nextSlide].resourceURL);

            //If we're about to switch over to a scoreboard slide, but there is no race going on and the scoreboard isn't showing race results, skip to the next slide
            if (slides[this.nextSlide].resourceURL == "pages/newhdscoreboard.html" && !globalVars.isRaceOnGoing() && !scoreboardIsCurrentlyIdle)
            {
                //DEBUG: console.log("No race currently running. Skipping past scoreboard screen that would have been next.");

                //Let's skip the scoreboard slide and just go on to the next one
                this.currentSlide = this.nextSlide;
                this.nextSlide++;
                if (this.nextSlide == slides.length)
                {
                    this.nextSlide = 0;
                }

                //Send that new slide's information into the view
                $scope.track_id = this.currentTrack;
                $scope.type = slides[this.nextSlide].type;
                $scope.resourceURL = slides[this.nextSlide].resourceURL;
                $scope.resourceURLTrusted = $sce.trustAsResourceUrl($scope.resourceURL);
                if (slides[this.nextSlide].orientation == "portrait") //Used for image mode auto-resizing
                {
                    $scope.landscape = 0;
                }
                else
                {
                    $scope.landscape = 1;
                }

                var currentDuration = slides[this.nextSlide].durationMs; //How long to show current slide before switching

                //Put the next slide in the queue
                this.currentSlide = this.nextSlide;
                this.nextSlide++;
                if (this.nextSlide == slides.length)
                {
                    this.nextSlide = 0;
                }

                //Swap to the next slide once the current slide's duration has expired
                cancelTimeouts();
                currentTimeOut = $timeout(function(){currentChannel.run();},currentDuration);
            }
            else //If we're clear to just switch to the next slide
            {
                //Inform the view of its new slide and its contents
                $scope.track_id = this.currentTrack;
                $scope.type = slides[this.nextSlide].type;
                $scope.resourceURL = slides[this.nextSlide].resourceURL;
                $scope.resourceURLTrusted = $sce.trustAsResourceUrl($scope.resourceURL);
                if (slides[this.nextSlide].orientation == "portrait") //Used for image mode auto-resizing
                {
                    $scope.landscape = 0;
                }
                else
                {
                    $scope.landscape = 1;
                }

                var currentDuration = slides[this.nextSlide].durationMs; //How long to show current slide before switching

                //Put the next slide in the queue
                this.currentSlide = this.nextSlide;
                this.nextSlide++;
                if (this.nextSlide == slides.length)
                {
                    this.nextSlide = 0;
                }

                //Swap to the next slide once the current slide's duration has expired
                cancelTimeouts();
                currentTimeOut = $timeout(function(){currentChannel.run();},currentDuration);
            }
        };

        /**
         * This function queries the track for its Channel lineup, defaulting to Channel 1.
         * It then uses the information to create the equivalent slides for use by this app, only accepting "image" and "scoreboard" types.
         * If a "scoreboard" slide exists with a postRaceIdleTime of 86400000, it is interpreted to be an infinite slide.
         * All other slides are removed, and only the scoreboard slide is used.
         * It will then re-query the track every minute, and if any Channel lineup changes have occurred, puts them into effect.
         */
        Channel.prototype.initializeSlides = function(channel_id)
        {
            if (channel_id == "scoreboard") //If the user would like the channel just on the scoreboard
            {
                //DEBUG: console.log("No need to poll for channel lineup. Scoreboard-only mode activated for Track 1.");

                var desiredBackgroundURL = "";
/*                var overwrittenBackgroundURL = $('<img src="http://127.0.0.1/assets/cs-speedscreen/images/background_1080p.jpg"/>');

                alert(overwrittenBackgroundURL.attr('width'));
                if (overwrittenBackgroundURL.attr('width') > 0)
                {
                    alert("BANANA");
                    desiredBackgroundURL = 'http://127.0.0.1/assets/cs-speedscreen/images/background_1080p.jpg';
                    $('.hdScoreboard').css('background-image','url(' + desiredBackgroundURL + ')');
                    $('.hdScoreboard').css('background-size','100% 100%');
                }*/

                //Manually set the Speed Screen to display the scoreboard infinitely, ignoring Club Speed settings
                slides = [];
                slides.push(new Slide("html","pages/newhdscoreboard.html",86400000,"",this.apiURL,this.apiKey, 1, desiredBackgroundURL));
                hasScoreboard = true;
                scoreboardSlide = slides.length - 1;
                this.currentTrack = 1;
                globalVars.setTrackID(this.currentTrack);
                $timeout(function(){currentChannel.initializeSlides($routeParams.channel_id);},timeBetweenChannelUpdatesMs);
                return;
            }
            else if (channel_id == "polandminiscoreboard") //If the user would like the channel just on the poland miniscoreboard
            {
                //DEBUG: console.log("No need to poll for channel lineup. Scoreboard-only mode activated for Track 1.");

                //Manually set the Speed Screen to display the scoreboard infinitely, ignoring Club Speed settings
                slides = [];
                slides.push(new Slide("html","pages/polandminiscoreboard.html",86400000,"",this.apiURL,this.apiKey, 1, ""));
                hasScoreboard = true;
                scoreboardSlide = slides.length - 1;
                this.currentTrack = 1;
                globalVars.setTrackID(this.currentTrack);
                $timeout(function(){currentChannel.initializeSlides($routeParams.channel_id);},timeBetweenChannelUpdatesMs);
                return;
            }
            //DEBUG: console.log("Polling for new channel lineup...");

            //Poll the requested channel, defaulting to Channel 1
            channel_id = defaultFor(channel_id,1);
            speedScreenServices.getSpeedScreenInfo(channel_id).success(function (data) {
                if (data.length == 0) //If no data was returned, remove all slides and do nothing
                {
                    //DEBUG: console.log("Empty channel returned!");

                    cancelTimeouts();
                    slides = [];
                    hasScoreboard = false;
                    this.nextSlide = 0;
                    this.currentSlide = 0;
                    raceWasJustHappening = false;
                    scoreboardTotalIdleTimeMs = 0;
                    scoreboardStartedBeingIdleTimeMs = 0;
                    globalVars.setFirstTimeScoreboardLoaded(true);
                    $scope.type = "none";
                    currentChannel.run();
                }
                else //If channel data was returned
                {
                    //DEBUG: console.log(data);
                    //DEBUG: console.log("Hash pulled: " + data.hash + " from channel " + channel_id);

                    if (channelHash === "") //If it's our first time polling the channel
                    {
                        //DEBUG: console.log("First time pulling the hash.");

                        channelHash = data.hash; //Remember the hash for future channel calls
                        var screens = data.lineup;
                        for(var i = 0; i < screens.length; i++) //Parse all the screen settings and add them to the Speed Screen
                        {
                            var currentScreen = screens[i];
                            //DEBUG: console.log(currentScreen);
                            if (currentScreen.type == 'image')
                            {
                                slides.push(new Slide('image',currentScreen.options.url,currentScreen.options.duration,'landscape'));
                            }
                            else if (currentScreen.type == 'scoreboard')
                            {
                                if (currentScreen.options.postRaceIdleTime == 86400000) //If the scoreboard should last forever, make it so
                                {
                                    slides = [];
                                    slides.push(new Slide("html","pages/newhdscoreboard.html",currentScreen.options.postRaceIdleTime,"",this.apiURL,this.apiKey, currentScreen.options.trackId, data.options.backgroundImageUrl));
                                    scoreboardSlide = slides.length - 1;
                                    this.currentTrack = currentScreen.options.trackId;
                                    globalVars.setTrackID(this.currentTrack);
                                    $timeout(function(){currentChannel.initializeSlides($routeParams.channel_id);},timeBetweenChannelUpdatesMs);
                                    return;
                                }
                                slides.push(new Slide("html","pages/newhdscoreboard.html",currentScreen.options.postRaceIdleTime,"",this.apiURL,this.apiKey, currentScreen.options.trackId, data.options.backgroundImageUrl));
                                hasScoreboard = true;
                                scoreboardSlide = slides.length - 1;
                                this.currentTrack = currentScreen.options.trackId;
                                globalVars.setTrackID(this.currentTrack);
                            }
                            else if (currentScreen.type == 'url')
                            {
                                var formattedURL = currentScreen.options.url;
                                if (formattedURL.indexOf('?') == -1)
                                {
                                    formattedURL = formattedURL + '?';
                                }
                                formattedURL = formattedURL + '&slideTimeout=' + currentScreen.options.duration;
                                formattedURL = formattedURL + '&disableAnimations=' + (disableAnimations ? '1' : '0');
                                slides.push(new Slide('iframehtml',formattedURL,currentScreen.options.duration,'landscape'));
                            }
                        }
                    }
                    else //If this is not the first time we've polled for a channel lineup
                    {
                        if (channelHash == data.hash) //If it has the same hash, nothing has changed
                        {
                            //DEBUG: console.log("Same hash as previously seen. No changes to be made.");
                        }
                        else //If it has a different hash, we need to reload the settings and reset the Speed Screen
                        {
                            //DEBUG: console.log("HASH CHANGED. NEW SPEED SCREEN CONFIGURATION NEEDS TO BE LOADED");

                            cancelTimeouts();
                            slides = [];
                            hasScoreboard = false;
                            this.nextSlide = 0;
                            this.currentSlide = 0;
                            raceWasJustHappening = false;
                            scoreboardTotalIdleTimeMs = 0;
                            scoreboardStartedBeingIdleTimeMs = 0;
                            globalVars.setFirstTimeScoreboardLoaded(true);
                            channelHash = data.hash;
                            var screens = data.lineup;
                            for(var i = 0; i < screens.length; i++) //Parse all the screen settings and add them to the Speed Screen
                            {
                                var currentScreen = screens[i];
                                //DEBUG: console.log(currentScreen);
                                if (currentScreen.type == 'image')
                                {
                                    slides.push(new Slide('image',currentScreen.options.url,currentScreen.options.duration,'landscape'));
                                }
                                else if (currentScreen.type == 'scoreboard')
                                {
                                    if (currentScreen.options.postRaceIdleTime == 86400000) //If the scoreboard should last forever, make it so
                                    {
                                        slides = [];
                                        slides.push(new Slide("html","pages/newhdscoreboard.html",currentScreen.options.postRaceIdleTime,"",this.apiURL,this.apiKey, currentScreen.options.trackId, data.options.backgroundImageUrl));
                                        scoreboardSlide = slides.length - 1;
                                        this.currentTrack = currentScreen.options.trackId;
                                        globalVars.setTrackID(this.currentTrack);
                                        $timeout(function(){currentChannel.initializeSlides($routeParams.channel_id);},timeBetweenChannelUpdatesMs);
                                        return;
                                    }
                                    slides.push(new Slide("html","pages/newhdscoreboard.html",currentScreen.options.postRaceIdleTime,"",this.apiURL,this.apiKey, currentScreen.options.trackId, data.options.backgroundImageUrl));
                                    hasScoreboard = true;
                                    scoreboardSlide = slides.length - 1;
                                    this.currentTrack = currentScreen.options.trackId;
                                }
                                else if (currentScreen.type == 'url')
                                {
                                    var formattedURL = currentScreen.options.url;
                                    if (formattedURL.indexOf('?') == -1)
                                    {
                                        formattedURL = formattedURL + '?';
                                    }
                                    formattedURL = formattedURL + '&slideTimeout=' + currentScreen.options.duration;
                                    formattedURL = formattedURL + '&disableAnimations=' + (disableAnimations ? '1' : '0');
                                    slides.push(new Slide('iframehtml',formattedURL,currentScreen.options.duration,'landscape'));
                                }
                            }
                            cancelTimeouts();
                            currentTimeOut = currentChannel.run();
                        }
                    }

                }

            }).error(function (data) { //In case of any error fetching channel data, at least make the Speed Screen show the scoreboard for track 1
                slides.push(new Slide("html","pages/newhdscoreboard.html",86400000,"",this.apiURL,this.apiKey, 1));
            });
            $timeout(function(){currentChannel.initializeSlides($routeParams.channel_id);},timeBetweenChannelUpdatesMs);
        };

        /**
         * This function checks if a race is currently going on, and handles transitioning to or away from the scoreboard as
         * appropriate. If a race is happening, the Speed Screen immediately switches to the scoreboard screen.
         * If there is no race happening, the Speed Screen skips over the scoreboard screen.
         * If the scoreboard is set to infinite time, then it just always displays.
         */
        Channel.prototype.checkForOnGoingRace = function()
        {
            /*DEBUG: console.log("Is there a race running? " + (globalVars.isRaceOnGoing() ? "Yes" : "No"));
            console.log("Additional debug info:");
            console.log("Slides length: " + slides.length);*/

            if (slides.length > 0) //If we have any slides at all
            {
                /*DEBUG: console.log("Slide's durationMs: " + slides[this.currentSlide].durationMs);
                console.log("Slide's resource URL: " + slides[this.currentSlide].resourceURL);
                console.log("globalVars.firstTimeScoreboardLoaded() = " + globalVars.firstTimeScoreboardLoaded());
                console.log("scoreboardTotalIdleTimeMs = " + scoreboardTotalIdleTimeMs);
                console.log("###############################################");*/

                if (globalVars.isRaceOnGoing() && slides[this.currentSlide].durationMs != 86400000) //If a race is going on and the scoreboard isn't set to infinite
                {
                    //DEBUG: console.log("A RACE IS ONGOING - FORCING SPEED SCREEN TO SCOREBOARD");

                    //Force the Speed Screen to switch to the scoreboard
                    raceWasJustHappening = true;
                    if (slides[this.nextSlide].resourceURL != 'pages/newhdscoreboard.html')
                    {
/*                        if (slides[scoreboardSlide].backgroundURL != undefined && slides[scoreboardSlide].backgroundURL != "")
                        {
                            $('.hdScoreboard').css('background-image','url(' + slides[scoreboardSlide].backgroundURL + ')');
                            $('.hdScoreboard').css('background-size','100% 100%');
                        }*/
                        this.currentSlide = scoreboardSlide;
                        this.nextSlide = scoreboardSlide;
                        var currentDuration = slides[this.nextSlide].durationMs;

//                        $scope.track_id = slides[this.nextSlide].trackID;
//                        globalVars.setTrackID(slides[this.nextSlide].trackID);
                        $scope.type = slides[this.nextSlide].type;
                        $scope.resourceURL = slides[this.nextSlide].resourceURL;
                        $scope.resourceURLTrusted = $sce.trustAsResourceUrl($scope.resourceURL);

                        cancelTimeouts();
                        currentTimeOut = $timeout(function(){currentChannel.run();},currentDuration);
                        $timeout(function(){currentChannel.checkForOnGoingRace();},1500);
                        return;
                    }
                }
                else
                {
                    //If the screen is currently on the scoreboard and it's not set to finite and a race isn't happening
                    if (slides[this.currentSlide].resourceURL == 'pages/newhdscoreboard.html' && slides[this.currentSlide].durationMs != 86400000 && !globalVars.firstTimeScoreboardLoaded())
                    {
                        //DEBUG: console.log("TRYING TO SKIP SCOREBOARD SLIDE SINCE THERE ARE NO RACES");

                        if (raceWasJustHappening) //If the race just ended, start recording the idle time
                        {
                            raceWasJustHappening = false;
                            scoreboardTotalIdleTimeMs = 0;
                            scoreboardStartedBeingIdleTimeMs = new Date();
                            //DEBUG: console.log("SCOREBOARD JUST STARTED BEING IDLE");
                            scoreboardIsCurrentlyIdle = true;
                        }
                        scoreboardTotalIdleTimeMs = new Date() - scoreboardStartedBeingIdleTimeMs;

                        //DEBUG: console.log("SCOREBOARD HAS BEEN IDLE FOR " + scoreboardTotalIdleTimeMs);

                        //If the scoreboard has been idle long enough, switch to the next slide
                        if (scoreboardTotalIdleTimeMs > slides[this.currentSlide].durationMs)
                        {
                            scoreboardIsCurrentlyIdle = false;
                            /*DEBUG: console.log("THE SCOREBOARD HAS BEEN IDLE FOR LONGER THAN " + slides[this.currentSlide].durationMs);
                            console.log("Moving on to the next slide...");*/
                            scoreboardTotalIdleTimeMs = 0;
                            scoreboardStartedBeingIdleTimeMs = 0;

                            this.nextSlide++;
                            if (this.nextSlide == slides.length)
                            {
                                this.nextSlide = 0;
                            }
                            this.currentSlide = this.nextSlide;
                            var currentDuration = slides[this.nextSlide].durationMs;
                            $scope.track_id = slides[this.nextSlide].trackID;
                            $scope.type = slides[this.nextSlide].type;
                            $scope.resourceURL = slides[this.nextSlide].resourceURL;
                            $scope.resourceURLTrusted = $sce.trustAsResourceUrl($scope.resourceURL);
                            if (slides[this.nextSlide].orientation == "portrait")
                            {
                                $scope.landscape = 0;
                            }
                            else
                            {
                                $scope.landscape = 1;
                            }
                            cancelTimeouts();
                            currentTimeOut = $timeout(function(){currentChannel.run();},currentDuration);
                            $timeout(function(){currentChannel.checkForOnGoingRace();},1500);
                            return;
                        }
                    }
                }
            }
            $timeout(function(){currentChannel.checkForOnGoingRace();},1500);
         };
    }

    /**
     * This represents a slide of the Speed Screen.
     * @param type "image" or "html"
     * @param resourceURL Points to a specific html page or image URL.
     * @param durationMs How long the slide should show, in milliseconds. 86400000 means forever.
     * @param orientation Used for intelligent image resizing. "portrait" or "landscape"
     * @param apiURL The URL of the API to fetch. This allows multiple APIs to exist in the same Speed Screen if desired.
     * @param apiKey The key to the above API.
     * @param trackID If applicable, the track ID associated with the slide.
     * @param backgroundURL If applicable, the background image URL to use for the slide
     * @constructor
     */
    function Slide(type,resourceURL,durationMs,orientation, apiURL, apiKey, trackID, backgroundURL)
    {
        this.type = type;
        this.resourceURL = resourceURL;
        this.durationMs = durationMs;
        this.orientation = orientation;
        this.apiURL = apiURL;
        this.apiKey = apiKey;
        this.trackID = trackID;
        this.backgroundURL = backgroundURL;
    }

    //#####################
    //# UTILITY FUNCTIONS #
    //#####################

    /**
     * Used for cancelling the next queued slide from appearing.
     */
    function cancelTimeouts()
    {
        if (angular.isDefined(currentTimeOut))
        {
            $timeout.cancel(currentTimeOut);
            currentTimeOut = undefined;
        }
    }

    /**
     * Polls the track for any on-going races, and figures out whether or not a race is happening.
     */
    function pollForRaces()
    {
        if (hasScoreboard)
        {
            //DEBUG: console.log("Channel is polling track " + globalVars.getCurrentTrack() + " for races...");
            speedScreenServices.getScoreboardData(globalVars.getCurrentTrack()).success(function (data) {
                //DEBUG: console.log(data);
                if (Object.size(data.scoreboard) === 0)
                {
                    globalVars.setRaceIsOnGoing(false);
                }
                else
                {
                    globalVars.setRaceIsOnGoing(true);
                }
            });
        }
    }

    /**
     * Determines the size of an object.
     * @param obj
     * @returns {number}
     */
    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };
});

/**
 * This factory stores key global variables that the Speed Screen must use to communicate with specific screens.
 * This facilitates inter-controller communication in AngularJS.
 */
speedScreenDemoApp.factory('globalVars', function() {
    var globalVars = {};
    var stop = undefined; //Function to call to "clean up" a screen before moving on to the next.
    var timeToStop = false;
    var apiKey = undefined;
    var apiURL = undefined;
    var currentTrack = 1;
    var raceIsOnGoing = false;
    var track_id = 1;
    var firstTimeScoreboardLoaded = true;

    globalVars.getStop = function() { return stop; }
    globalVars.setStop = function(newStop) { stop = newStop; }
    globalVars.resetStop = function() { stop = undefined; isTimeToStop = false; }
    globalVars.isTimeToStop = function() { return timeToStop; }
    globalVars.setTimeToStop = function (newValue) { timeToStop = newValue; }

    globalVars.setApiKey = function(newApiKey) { apiKey = newApiKey; }
    globalVars.getApiKey = function() { return apiKey; }
    globalVars.setApiURL = function(newApiURL) { apiURL = newApiURL; }
    globalVars.getApiURL = function() { return apiURL; }

    globalVars.getCurrentTrack = function() { return currentTrack; }
    globalVars.setCurrentTrack = function(newTrack) { currentTrack = newTrack;}

    globalVars.setRaceIsOnGoing = function(newStatus) { raceIsOnGoing = newStatus; }
    globalVars.isRaceOnGoing = function() { return raceIsOnGoing; }

    globalVars.setTrackID = function(new_track_id) { track_id = new_track_id; }
    globalVars.getTrackID = function() { return track_id; }

    globalVars.setFirstTimeScoreboardLoaded = function(newStatus) { firstTimeScoreboardLoaded = newStatus; }
    globalVars.firstTimeScoreboardLoaded = function() { return firstTimeScoreboardLoaded; }

    return globalVars;
});

/**
 * Adds default parameter functionality to JavaScript. Woohoo!
 * @param arg
 * @param val
 * @returns {*}
 */
function defaultFor(arg, val)
{ return typeof arg !== 'undefined' ? arg : val; }