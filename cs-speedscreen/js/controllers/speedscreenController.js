speedscreenApp.controller('speedscreenController', function($scope, $interval, $timeout, $q, $sce, $routeParams, $window, $location, apiService, globalSettings) {

    //Debugging tools
    $scope.debug = $routeParams.channel_options == 'debug' ? true : false; //If set, verbose debugging output is sent to the JavaScript console, along with debugMessages at the top of the screen
    $scope.debugMessage = ''; //If not empty, a message appears in a special debug panel at the top of the slide
    $scope.debugIntervalFunctions = []; //Tracks countdowns for debug messages
    $scope.debugCountdown = 0; //Holds the actual countdown number for debug messages
    $scope.diagnosticsMode = $routeParams.channel_options == 'diagnostics' ? true : false; //Shows a diagnostics screen instead

    //Translations
    $scope.locale = typeof $location.search().locale == "undefined" ? "en-US" : $location.search().locale;
    debugToConsole('Received locale of ' + $scope.locale);
    $scope.strings = translations["en-US"]; //Default

    //Settings
    $scope.settings = settings; //Default
    $scope.disableAnimations = $routeParams.channel_options == 'disableAnimations' ? true : false;

    /*
     ########################################
     # FETCHING TRANSLATIONS (IF AVAILABLE) #
     ########################################
     */

    apiService.getTranslations().success(function(data, status, headers, config) {
        var formattedTranslations = {};
        if (typeof data.translations != "undefined")
        {
            for(var i = 0; i < data.translations.length; i++)
            {
                var currentTranslation = data.translations[i];
                if (!formattedTranslations.hasOwnProperty(currentTranslation.culture))
                {
                    formattedTranslations[currentTranslation.culture] = JSON.parse(JSON.stringify(translations["en-US"])); //English is the fallback for missing strings
                }
                formattedTranslations[currentTranslation.culture][currentTranslation.name] = currentTranslation.value;
            }
            translations = mergeObjects(translations,formattedTranslations);
            debugToConsole(translations);
            $scope.strings = translations[$scope.validLocale];

            $scope.validLocale = translations.hasOwnProperty($scope.locale) ? $scope.locale : "en-US";
            $scope.strings = translations[$scope.validLocale];

            if (!translations.hasOwnProperty($scope.locale))
            {
                debugToConsole('Locale was not supported. Defaulted to en-US.');
            }
            $scope.locale = $scope.validLocale;
        }
    }).error(function(data, status, headers, config) {
        debugToConsole("Unable to fetch translations from server.");
    });

    //Screen state management
    $scope.state = 'loading';
    $scope.nextSlideTimeout = null; //The $timeout that will trigger for the next slide. Stored so it can be interrupted/cancelled.

    //Race state management
    $scope.raceState = 'none'; //Race states: 'none', 'startButtonPressed', 'firstLapStarted', 'running'
    $scope.showOverlaySlide = false;
    $scope.overlaySlideURL = '';
    $scope.overlayTimeout = null;

    //Loading screen text
    $scope.loadingHeaderText = $scope.strings['str_loadingSpeedScreen'];
    $scope.loadingStepText = $scope.strings['str_connectingToServer'];

    //API polling throttling
    $scope.numberOfPendingScoreboardCalls = 0;

    /*
     #######################
     # SPEED SCREEN LAUNCH #
     #######################

     Core logic:
     - Show a loading screen to the viewer.
     - Determine whether we are in Diagnostics mode or Live mode.
     - Try to connect to Club Speed's API. (Report error and restart if failure.)
     - Fetch settings and the channel configuration. (Report error and restart if failure.)
     - Launch the Channel.

     */

    $scope.backgroundImageURL = '';

    apiService.checkIfBackgroundWasUploaded().success(function(data, status, headers, config) {
        debugToConsole("Background image is present in assets folder. Using /assets/cs-speedscreen/images/background_1080p.jpg as default.");
        $scope.backgroundImageURL = 'http://' + window.location.hostname + '/assets/cs-speedscreen/images/background_1080p.jpg';
    }).error(function(data, status, headers, config) {
        debugToConsole("Background image is NOT present in assets folder. Using images/backgrounds/default.jpg as default.");
        $scope.backgroundImageURL = 'http://' + window.location.hostname + '/cs-speedscreen/images/backgrounds/default.jpg';
    });

    if(!$scope.diagnosticsMode) //If we're running a live Channel in production
    {
        //Pull all settings and determine which API driver to use
        $scope.loadingStepText = $scope.strings['str_gettingSettings'];

        apiService.verifyConnectivityToServerAndFetchSettings(). //Verify basic connectivity to the server by fetching settings
        success(function(data, status, headers, config) {
            var formattedSettings = {};
            if (typeof data.settings != "undefined" && data.settings.length > 0)
            {
                processAndUpdateSettings(data.settings);
            }
            else
            {
                debugToConsole("No settings found on server. Using defaults.");
            }

            //Set the API to the specified driver
            $scope.loadingStepText = $scope.strings['str_attachingAPI'];
            globalSettings.setAPIDriver($scope.settings['apiDriver']);

            //Fetch current Channel configuration
            $scope.loadingStepText = $scope.strings['str_fetchingChannelConfig'];
            apiService.getChannelLineUp().
            success(function(data, status, headers, config) {
                if (typeof data[0] != 'undefined' && typeof data[0].channelData != 'undefined')
                {
                    try
                    {
                        data = data[0].channelData;
                        data = JSON.parse(data);
                    }
                    catch(e)
                    {
                        debugToConsole(e);
                    }

                }
                else if (typeof data.name == 'undefined') //Error case: Invalid JSON (multiple causes)
                {
                    reportErrorAndRestart($scope.strings['str_noValidChannelData']);
                }
                if (data.length == 0) //Error case: Empty channel
                {
                    reportErrorAndRestart($scope.strings['str_channelHasNoSlides']);
                }
                else //Success
                {
                    $scope.loadingStepText = $scope.strings['str_loadingChannel'];

                    var channelData = formatChannelData(data);
                    $scope.channel = new Channel(channelData);
                    $scope.oldHash = channelData.hash; //Used to determine if the channel configuration changed
                    $scope.channel.start(); //Start the channel!
                }
            }).
            error(function(data, status, headers, config) { //Error case: Unable to hit Channel API call
                reportErrorAndRestart($scope.strings['str_unableToGetChannels']);
            });
        }).
        error(function(data, status, headers, config) { //Error case: Unable to hit Club Speed API
            debugToConsole('Unable to fetch settings!');
            reportErrorAndRestart($scope.strings['str_unableToConnect']);
        });
    }
    else //Diagnostics mode display
    {
        $scope.state = 'diagnostics';
        $scope.screenWidth = $window.outerWidth;
        $scope.screenHeight = $window.outerHeight;
        $scope.currentChannelNumber = $routeParams.channel_id == null ? 1 : $routeParams.channel_id;

        $scope.clubSpeedReachable = 'N/A';
        $scope.channelDataAvailable = 'N/A';
        $scope.regularTimelineSize = 'N/A';
        $scope.racesTimelineSize = 'N/A';
        $scope.settingsStatus = 'N/A';

        apiService.verifyConnectivityToServerAndFetchSettings().
        success(function(data, status, headers, config) {

            if (typeof data.settings != "undefined" && data.settings.length > 0)
            {
                processAndUpdateSettings(data.settings);
            }
            else
            {
                debugToConsole("No settings found on server. Using defaults.");
            }

            if (typeof data.settings != "undefined" && data.settings.length > 0)
            {
                $scope.settingsStatus = 'Good';
            }
            else
            {
                $scope.settingsStatus = 'Needs Migration';
            }

            $scope.clubSpeedReachable = 'Good';
            globalSettings.setAPIDriver('polling');

            $scope.loadingStepText = 'Fetching Channel configuration...';
            apiService.getChannelLineUp().
            success(function(data, status, headers, config) {
                if (typeof data[0] != 'undefined' && typeof data[0].channelData != 'undefined')
                {
                    try
                    {
                        data = data[0].channelData;
                        data = JSON.parse(data);
                    }
                    catch(e)
                    {
                        $scope.channelDataAvailable = 'Bad Data';
                    }
                }
                if (data.length == 0) //Error case: Empty channel
                {
                    $scope.channelDataAvailable = 'Empty';
                }
                else if (typeof data.name == 'undefined') //Error case: Invalid JSON (multiple causes)
                {
                    $scope.channelDataAvailable = 'Bad Data';
                }
                else //Success
                {
                    $scope.channelDataAvailable = 'Good';
                    $scope.loadingStepText = 'Loading Channel...';

                    var channelData = formatChannelData(data);
                    $scope.regularTimelineSize = channelData.timelines.regular.slides.length;
                    $scope.racesTimelineSize = channelData.timelines.races.slides.length;
                }
            }).
            error(function(data, status, headers, config) { //Error case: Unable to hit Channel API call
                $scope.channelDataAvailable = 'Error';
            });
        }).
        error(function(data, status, headers, config) { //Error case: Unable to hit Club Speed API
            $scope.clubSpeedReachable = 'Error';
            console.log(config);
        });
    }


    /*
     #################
     # CHANNEL LOGIC #
     #################
     */

    var Channel = function(channelLineup) {

        this.channelLineUp = channelLineup;

        $scope.currentTimeline = 'none';
        this.currentSlideIndex = 0;

        this.numberOfRegularTimelineSlides = this.channelLineUp.timelines.regular.slides.length;
        this.numberOfRacesTimelineSlides = this.channelLineUp.timelines.races.slides.length;

        this.channelUpdateFrequencyMs = $scope.settings['channelUpdateFrequencyMs'];
        this.checkForRacesFrequencyMs = $scope.settings['racesPollingRateMs'];

        $scope.listOfTracksWithARaceRunning = [];
        $scope.timeoutToSwitchBackToRegularTimeline = null;
        $scope.timeoutToSwitchToNextRaceSlide = null;

        debugToConsole("Channel instantiated - " + this.channelLineUp.name);
        debugToConsole(channelLineup);
    };

    Channel.prototype.start = function() {
        debugToConsole("Channel started");
        var thereIsAtLeastOneSlideToShow = (this.numberOfRacesTimelineSlides > 0) || (this.numberOfRegularTimelineSlides > 0);
        if (thereIsAtLeastOneSlideToShow)
        {
            debugToConsole("Channel has at least one slide");
            this.showFirstTimelineAndSlide();
            this.periodicallyCheckForChannelUpdates();
            if (this.numberOfRacesTimelineSlides > 0)
            {
                this.periodicallyCheckForRacesHappening();
            }
        }
        else
        {
            debugToConsole("Channel did not have any slides");
            reportErrorAndRestart($scope.strings['str_channelHasNoSlides']);
        }
    };

    Channel.prototype.periodicallyCheckForChannelUpdates = function() {
        $scope.channelUpdateInterval = $interval(function () {$scope.channel.checkForChannelUpdate();},this.channelUpdateFrequencyMs);
    };

    $scope.checkForRaces = null;
    Channel.prototype.periodicallyCheckForRacesHappening = function() {
        var self = this; //Nested calls can change the context of 'this'
        $scope.checkForRacesInterval = $interval($scope.checkForRaces = function () {
            if ($scope.numberOfPendingScoreboardCalls == 0) //If there aren't any pending API calls
            {
                $scope.numberOfPendingScoreboardCalls++;
                self.determineWhichTracksHaveRaces().then(function (tracks) {
                    $scope.numberOfPendingScoreboardCalls--;
                    $scope.listOfTracksWithARaceRunning = tracks;
                    debugToConsole("-------------------------------------------------");
                    debugToConsole("Periodic check for tracks that are running races: ");
                    debugToConsole($scope.listOfTracksWithARaceRunning);
                    if ($scope.listOfTracksWithARaceRunning.length <= 0) //If there are no races running
                    {
                        debugToConsole("There are no races running right now");
                        if ($scope.currentTimeline == 'races' && self.numberOfRegularTimelineSlides > 0) //If we're on the Races timeline and the Regular timeline exists
                        {
                            debugToConsole("Currently on the Races timeline - already queued to switch to regular timeline?");
                            //If we need to get ready to switch back to the regular timeline
                            if ($scope.timeoutToSwitchBackToRegularTimeline == null) {
                                Channel.prototype.abortAllSlideTimeouts();
                                debugToConsole("Not yet!");

                                var timeToRemainIdleMs = 15000;
                                var currentSlide = self.channelLineUp.timelines[$scope.currentTimeline]['slides'][self.currentSlideIndex];
                                if (currentSlide.type == 'scoreboard') {
                                    timeToRemainIdleMs = currentSlide.options.postRaceIdleTime;
                                }
                                else {
                                    timeToRemainIdleMs = currentSlide.options.duration;
                                }

                                $scope.timeoutToSwitchBackToRegularTimeline = $timeout(function () {
                                    Channel.prototype.abortAllSlideTimeouts();
                                    $scope.currentTimeline = 'regular';
                                    self.currentSlideIndex = 0;
                                    debugClearCountdownArea();
                                    if (!shouldShowSlide()) //If the current slide has a filter preventing it from being seen
                                    {
                                        tryToShowNextSlide(); //Try to skip to the next one
                                    }
                                    else {
                                        var durationOfCurrentSlideMs = $scope.channel.renderCurrentSlide();
                                        $scope.channel.queueUpNextSlide(durationOfCurrentSlideMs);
                                        $scope.timeoutToSwitchBackToRegularTimeline = null;
                                    }
                                }, timeToRemainIdleMs);

                                debugToConsole("Current slide will switch away to the Regular timeline in " + timeToRemainIdleMs / 1000 + " seconds.");
                                debugToCountdownArea('Time before switching to Regular timeline: ', timeToRemainIdleMs);
                            }
                            else {
                                debugToConsole("Already queued up regular timeline. Will happen after current slide finishes.");
                            }
                        }
                        else if ($scope.currentTimeline == 'races' && self.numberOfRegularTimelineSlides <= 0)//If we're on the Races timeline and there is no Regular timeline
                        {
                            debugToConsole("There are no slides in the Regular timeline to move to - staying at races timeline");

                            if ($scope.timeoutToSwitchToNextRaceSlide == null) //If we haven't queued up the next Races slide yet
                            {
                                var nextPotentialSlideIndex = self.findNextRacesSlideIndex(-1, self.currentSlideIndex);
                                if (nextPotentialSlideIndex == self.currentSlideIndex || nextPotentialSlideIndex == -1) {
                                    debugToConsole("No other slides to switch to.");
                                }
                                else {
                                    Channel.prototype.abortAllSlideTimeouts();
                                    debugToConsole("Found a valid next slide. Queueing up the next Racers timeline slide (" + nextPotentialSlideIndex + ")");

                                    var timeToRemainIdleMs = 15000;
                                    var currentSlide = self.channelLineUp.timelines[$scope.currentTimeline]['slides'][self.currentSlideIndex];
                                    if (currentSlide.type == 'scoreboard') {
                                        timeToRemainIdleMs = currentSlide.options.postRaceIdleTime;
                                    }
                                    else {
                                        timeToRemainIdleMs = currentSlide.options.duration;
                                    }

                                    $scope.timeoutToSwitchToNextRaceSlide = $timeout(function () {
                                        Channel.prototype.abortAllSlideTimeouts();
                                        $scope.currentTimeline = 'races';
                                        self.currentSlideIndex = self.findNextRacesSlideIndex(currentTrack, self.currentSlideIndex);
                                        debugToConsole("Switching to slide " + self.currentSlideIndex + " in the Races timeline");
                                        debugClearCountdownArea();
                                        if (!shouldShowSlide()) //If the current slide has a filter preventing it from being seen
                                        {
                                            tryToShowNextSlide(); //Try to skip to the next one
                                        }
                                        else {
                                            var durationOfCurrentSlideMs = $scope.channel.renderCurrentSlide();
                                            $scope.channel.queueUpNextSlide(durationOfCurrentSlideMs);
                                            $scope.timeoutToSwitchToNextRaceSlide = null;
                                        }
                                    }, timeToRemainIdleMs);

                                    debugToConsole("Current slide will switch to another Races timeline slide in " + timeToRemainIdleMs / 1000 + " seconds.");
                                    debugToCountdownArea('Time before switching to next Races timeline slide: ', timeToRemainIdleMs);
                                }
                            }
                            else {
                                debugToConsole("Already queued up next races timeline slide. Will happen after current slide finishes.");
                            }
                        }
                        else if ($scope.currentTimeline != 'races') {
                            debugToConsole("Not in the Races timeline - no action necessary");
                        }
                    }
                    else if ($scope.listOfTracksWithARaceRunning.length >= 1)//If there is at least one race running
                    {
                        debugToConsole("There is at least one race running right now");
                        if ($scope.currentTimeline != 'races') {
                            debugToConsole("Not on the races timeline yet - need to move there");
                            Channel.prototype.abortAllSlideTimeouts();
                            $scope.currentTimeline = 'races';
                            self.currentSlideIndex = $scope.channel.findNextScoreboardSlideWithRaceRunning();
                            if (self.currentSlideIndex == -1 && self.channelLineUp.timelines.races.slides.length == 1) {
                                self.currentSlideIndex = 0;
                            }
                            if (!shouldShowSlide()) //If the current slide has a filter preventing it from being seen
                            {
                                tryToShowNextSlide(); //Try to skip to the next one
                            }
                            else {
                                var durationOfCurrentSlideMs = $scope.channel.renderCurrentSlide();
                                debugToConsole("Moving speed screen immediately to slide " + self.currentSlideIndex + " of the Races timeline");
                                $scope.channel.queueUpNextSlide(durationOfCurrentSlideMs);
                            }
                        }
                        else {
                            debugToConsole("Already on the races timeline. Need to move to next slide?");

                            if ($scope.timeoutToSwitchToNextRaceSlide == null) //If we haven't queued up the next Races slide yet
                            {
                                var currentTrack = -1;
                                var currentSlide = self.channelLineUp.timelines[$scope.currentTimeline]['slides'][self.currentSlideIndex];
                                if (typeof currentSlide != "undefined" && currentSlide.type == 'scoreboard') {
                                    currentTrack = currentSlide.options.trackId;
                                }

                                var nextPotentialSlideIndex = self.findNextRacesSlideIndex(currentTrack, self.currentSlideIndex);
                                if (nextPotentialSlideIndex == self.currentSlideIndex || nextPotentialSlideIndex == -1) {
                                    debugToConsole("No other slides to switch to.");
                                }
                                else {
                                    Channel.prototype.abortAllSlideTimeouts();
                                    debugToConsole("Found a valid next slide. Queueing up the next Racers timeline slide (" + nextPotentialSlideIndex + ")");

                                    var timeToRemainIdleMs = 15000;
                                    if (currentSlide.type == 'scoreboard') {
                                        timeToRemainIdleMs = currentSlide.options.postRaceIdleTime;
                                    }
                                    else {
                                        timeToRemainIdleMs = currentSlide.options.duration;
                                    }

                                    debugToConsole("Current slide will switch to another Races timeline slide in " + timeToRemainIdleMs / 1000 + " seconds.");
                                    debugToCountdownArea('Time before switching to next Races timeline slide: ', timeToRemainIdleMs);

                                    $scope.timeoutToSwitchToNextRaceSlide = $timeout(function () {
                                        Channel.prototype.abortAllSlideTimeouts();
                                        $scope.currentTimeline = 'races';
                                        self.currentSlideIndex = self.findNextRacesSlideIndex(currentTrack, self.currentSlideIndex);
                                        debugToConsole("Switching to slide " + self.currentSlideIndex + " in the Races timeline");
                                        debugClearCountdownArea();
                                        if (!shouldShowSlide()) //If the current slide has a filter preventing it from being seen
                                        {
                                            tryToShowNextSlide(); //Try to skip to the next one
                                        }
                                        else {
                                            var durationOfCurrentSlideMs = $scope.channel.renderCurrentSlide();
                                            $scope.channel.queueUpNextSlide(durationOfCurrentSlideMs);
                                            $scope.timeoutToSwitchToNextRaceSlide = null;
                                        }
                                    }, timeToRemainIdleMs);


                                }
                            }
                            else {
                                debugToConsole("Already queued up next races timeline slide. Will happen after current slide finishes.");
                            }
                        }
                    }
                    debugToConsole("-------------------------------------------------");

                    updateRaceStateAndRunConditionalOverlays();
                });
            }
        },this.checkForRacesFrequencyMs);
    };

    Channel.prototype.findNextScoreboardSlideWithRaceRunning = function () {
        var racesSlides = $scope.channel.channelLineUp.timelines.races.slides;

        debugToConsole("Identifying the first Races scoreboard slide with a race running...");
        //Go through every slide in the Races timeline, looking for the first scoreboard with a race running
        for(var i = 0; i < racesSlides.length; i++)
        {
            if (racesSlides[i].type == 'scoreboard')
            {
                var hasARaceRunning = ($scope.listOfTracksWithARaceRunning.indexOf(racesSlides[i].options.trackId.toString()) > -1);
                if (hasARaceRunning)
                {
                    debugToConsole("Slide found! Index: " + i + ", Track: " + racesSlides[i].options.trackId.toString());
                    return i;
                }
            }
        }
        debugToConsole("No valid slide found! Returning -1");
        debugClearCountdownArea();
        return -1;
    };

    Channel.prototype.findNextRacesSlideIndex = function (currentTrackId, startingSlideIndex) {

        debugToConsole('Looking for the next Races slide to switch to from slide ' + startingSlideIndex);

        var racesSlides = this.channelLineUp.timelines.races.slides;
        var racesSlidesOriginalLength = racesSlides.length;
        var racesSlidesFront;
        var racesSlideBack;
        var racesSlidesReOrdered;
        var originalIndexOffset = 0;

        //Re-order the array to ensure that the next slide with a race *in-sequence to the current slide* gets queued up
        if (startingSlideIndex != 0)
        {
            racesSlidesFront = racesSlides.slice(startingSlideIndex,racesSlides.length);
            racesSlideBack = racesSlides.slice(0,startingSlideIndex);
            racesSlidesReOrdered = racesSlidesFront.concat(racesSlideBack);
            racesSlides = racesSlidesReOrdered;
            originalIndexOffset = startingSlideIndex;
        }

        //Go through every slide (except the first/current one), looking for the next one that is either not a scoreboard slide or is a scoreboard slide with a race running
        for(var i = 1; i < racesSlides.length; i++)
        {
            if  (
                racesSlides[i].type != 'scoreboard' ||
                (racesSlides[i].type == 'scoreboard' &&
                racesSlides[i].options.trackId != currentTrackId &&
                $scope.listOfTracksWithARaceRunning.indexOf(racesSlides[i].options.trackId.toString()) > -1))

            {
                debugToConsole("Slide found! Should switch to slide " + (i + originalIndexOffset)%racesSlidesOriginalLength);
                if (racesSlides[i].type == 'scoreboard')
                {
                    debugToConsole("(on track " + racesSlides[i].options.trackId + ")");
                }
                return (i + originalIndexOffset)%racesSlidesOriginalLength;
            }
        }
        debugToConsole("No valid slide found! Returning -1");
        debugClearCountdownArea();
        return -1;
    };

    Channel.prototype.checkForChannelUpdate = function() {
        debugToConsole("Checking channel line-up for updates");
        apiService.getChannelLineUp().
        success(function(data, status, headers, config) {
            if (typeof data[0] != 'undefined' && typeof data[0].channelData != 'undefined')
            {
                try
                {
                    data = data[0].channelData;
                    data = JSON.parse(data);
                }
                catch(e)
                {
                    debugToConsole(e);
                }

            }
            else if (typeof data.name == 'undefined') //Error case: Invalid JSON (multiple causes)
            {
                debugToConsole("New channel line-up had invalid JSON data");
                reportErrorAndRestart($scope.strings['str_noValidChannelData']);
            }
            if (data.length == 0)
            {
                debugToConsole("New channel line-up was empty");
                reportErrorAndRestart($scope.strings['str_channelHasNoSlides']);
            }
            else
            {
                if ($scope.oldHash != data.hash)
                {
                    debugToConsole("Hashes were different! Restarting...");
                    $window.location.reload(true);
                }
                else
                {
                    debugToConsole("No changes in the channel line-up");
                }
            }
        }).
        error(function(data, status, headers, config) {
            $interval.cancel($scope.checkForRacesInterval);
            $scope.checkForRacesInterval = null;
            reportErrorAndRestart($scope.strings['str_unableToGetChannels']);
        });
    };

    Channel.prototype.showFirstTimelineAndSlide = function() {

        debugToConsole("Channel is determining its first timeline and slide to show");
        var thereAreRacesSlidesToShow = (this.channelLineUp.timelines.races.slides.length > 0);
        if (thereAreRacesSlidesToShow)
        {
            debugToConsole("Channel has at least one races slide");
            $scope.listOfTracksWithARaceRunning = [];
            this.determineWhichTracksHaveRaces().then(function(tracks){
                $scope.listOfTracksWithARaceRunning = tracks;
                debugToConsole("Tracks that are running races:");
                debugToConsole($scope.listOfTracksWithARaceRunning);
                if ($scope.listOfTracksWithARaceRunning.length > 0)
                {
                    debugToConsole("There is at least one race running right now");
                    $scope.currentTimeline = 'races';
                    $scope.channel.currentSlideIndex = $scope.channel.findNextScoreboardSlideWithRaceRunning();
                    debugToConsole("Starting speed screen at slide " + $scope.channel.currentSlideIndex + " of the Races timeline");
                }
                else
                {
                    if ($scope.channel.channelLineUp.timelines.regular.slides.length > 0)
                    {
                        debugToConsole("There are no races running right now");
                        $scope.currentTimeline = 'regular';
                        this.currentSlideIndex = 0;
                        debugToConsole("Starting speed screen at slide 0 of the Regular timeline");
                    }
                    else
                    {
                        debugToConsole("There are no races running right now but we have nowhere else to go - staying on races timeline");
                        $scope.currentTimeline = 'races';
                        $scope.channel.currentSlideIndex = 0;
                        debugToConsole("Starting speed screen at slide " + $scope.channel.currentSlideIndex + " of the Races timeline");
                    }

                }
                if (!shouldShowSlide()) //If the current slide has a filter preventing it from being seen
                {
                    tryToShowNextSlide(); //Try to skip to the next one
                }
                else
                {
                    var durationOfCurrentSlideMs = $scope.channel.renderCurrentSlide();
                    debugToCountdownArea('Time until next slide: ', durationOfCurrentSlideMs);
                    $scope.channel.queueUpNextSlide(durationOfCurrentSlideMs);
                }
            });
        }
        else
        {
            debugToConsole("Channel has no races slides");
            $scope.currentTimeline = 'regular';
            this.currentSlideIndex = 0;


            if (!shouldShowSlide()) //If the current slide has a filter preventing it from being seen
            {
                tryToShowNextSlide(); //Try to skip to the next one
            }
            else
            {
                debugToConsole("Starting speed screen at slide 0 of the Regular timeline");
                var durationOfCurrentSlideMs = $scope.channel.renderCurrentSlide();
                $scope.channel.queueUpNextSlide(durationOfCurrentSlideMs);
            }
        }
    };

    Channel.prototype.showNextTimelineAndSlide = function() {
        debugToConsole("Time to show the next slide. Current timeline: " + $scope.currentTimeline);
        if($scope.currentTimeline == 'regular')
        {
            if (this.numberOfRegularTimelineSlides > 1)
            {
                this.currentSlideIndex++;
                if (this.currentSlideIndex >= this.numberOfRegularTimelineSlides)
                {
                    this.currentSlideIndex = 0;
                }

                if (!shouldShowSlide()) //If the current slide has a filter preventing it from being seen
                {
                    tryToShowNextSlide(); //Try to skip to the next one
                }
                else
                {
                    $scope.validSlideSearchStart = null;
                    debugToConsole("Moving speedscreen to slide " + this.currentSlideIndex + " of the Regular timeline");
                    var durationOfCurrentSlideMs = $scope.channel.renderCurrentSlide();
                    debugToCountdownArea('Time until next slide: ', durationOfCurrentSlideMs);
                    $scope.channel.queueUpNextSlide(durationOfCurrentSlideMs);
                }
            }
        }
        else if ($scope.currentTimeline == 'races')
        {
            //Do nothing; this logic is handled by the periodic 'Are any races happening?' check.
        }

    };


    /**
     *
     * @returns {number} The maximum duration (in milliseconds) of the current slide.
     */
    Channel.prototype.renderCurrentSlide = function() {
        var currentSlide = this.channelLineUp.timelines[$scope.currentTimeline]['slides'][this.currentSlideIndex];
        $scope.state = 'running';
        $scope.slideType = currentSlide.type;
        var processedURL = currentSlide.options.url;
        if (currentSlide.type == 'url' || currentSlide.type == 'scoreboard')
        {
            processedURL = appendLocaleAndBackgroundImg(processedURL);
        }
        $scope.slideURL = $sce.trustAsResourceUrl(processedURL);
        var slideDuration = currentSlide.options.duration != null ? currentSlide.options.duration : currentSlide.options.postRaceIdleTime;
        debugToConsole("Rendering slide (" + slideDuration + "ms) : ");
        debugToConsole(currentSlide);
        if ($scope.checkForRaces != null)
        {
            $scope.checkForRaces();
        }
        return slideDuration;
    };

    Channel.prototype.queueUpNextSlide = function(timeUntilNextSlideMs) {

        $scope.nextSlideTimeout = $timeout(function()
        {
            Channel.prototype.abortAllSlideTimeouts();
            debugClearCountdownArea();
            $scope.channel.showNextTimelineAndSlide();
        },timeUntilNextSlideMs);
    };

    Channel.prototype.abortAllSlideTimeouts = function()
    {
        $timeout.cancel($scope.timeoutToSwitchBackToRegularTimeline);
        $timeout.cancel($scope.timeoutToSwitchToNextRaceSlide);
        $timeout.cancel($scope.nextSlideTimeout);
        $scope.timeoutToSwitchBackToRegularTimeline = null;
        $scope.timeoutToSwitchToNextRaceSlide = null;
        $scope.nextSlideTimeout = null;

        $scope.showOverlaySlide = false;
        $scope.overlaySlideURL = '';
    };

    /**
     * Calls getCurrentRaceId on each track, and returns (deferred) an array of trackIds that currently has races running.
     * @returns {promise|*} A promise that eventually leads to an array of trackIds that have races running. (Ex. [1,3])
     */
    Channel.prototype.determineWhichTracksHaveRaces = function()
    {
        debugToConsole("Channel is determining at which tracks there are races running, if any");

        var tracksWithRacesSlides = this.channelLineUp.timelines.races.tracks;
        var numberOfTracks = tracksWithRacesSlides.length;
        var tracksWithRacesRunning = $q.defer();

        var currentRaceIdCallsToMake = [];
        for(var i = 0; i < numberOfTracks; i++) //Generate the needed API calls
        {
            var currentTrack = tracksWithRacesSlides[i];
            currentRaceIdCallsToMake.push(createCurrentRaceIdCall(currentTrack));
            debugToConsole("Channel has called getCurrentRaceId on track " + currentTrack);
        }
        $q.all(currentRaceIdCallsToMake).then(function(results){ //Once all calls complete, see which tracks have a race running
            debugToConsole("All getCurrentRaceId calls have completed");
            var tracksRunningRaces = [];
            for(var i = 0; i < results.length; i++)
            {
                var currentTrack = getQueryVariable(results[i].config.url,'track_id');
                var raceWasRunning = (typeof results[i].data.error == "undefined");

                if (raceWasRunning)
                {
                    debugToConsole("Track " + currentTrack + " did have a race running");
                    tracksRunningRaces.push(currentTrack);
                }
                else
                {
                    debugToConsole("Track " + currentTrack + " did NOT have a race running");
                    continue;
                }
            }
            tracksWithRacesRunning.resolve(tracksRunningRaces);
        }, function(errors)
        {
            $interval.cancel($scope.checkForRacesInterval);
            $scope.checkForRacesInterval = null;
            reportErrorAndRestart($scope.strings['str_unableToConnect']);
        });
        return tracksWithRacesRunning.promise;
    };

    /**
     * Wraps a getCurrentRaceId call, allowing lists of these calls to specific tracks to be generated.
     * @param track The track to call getCurrentRaceId on.
     * @returns {*} The result of the function call.
     */
    function createCurrentRaceIdCall(track)
    {
        return apiService.getCurrentRaceId(track);
    }


    function formatChannelData(data)
    {
        var isOldChannelFormat = (typeof data.lineup !== 'undefined');
        if (isOldChannelFormat)
        {
            var formattedData = {
                "name": data.name,
                "hash": data.hash,
                "options":
                {
                    "backgroundUrl": data.options.backgroundImageUrl
                },
                "timelines": {
                    "regular": {
                        "options":
                        {
                            "backgroundUrl": data.options.backgroundImageUrl
                        },
                        "slides": [
                        ]
                    },
                    "races": {
                        "options":
                        {
                            "backgroundUrl": data.options.backgroundImageUrl
                        },
                        "tracks": [],
                        "slides": []
                    }
                }
            };

            var tracksWithScoreboards = [];
            for(var i=0; i < data.lineup.length; i++)
            {
                if (data.lineup[i].type == "scoreboard")
                {
                    var trackAlreadyHasAScoreboard = (isInArray(data.lineup[i].options.trackId,tracksWithScoreboards));
                    if (!trackAlreadyHasAScoreboard) //Prevents duplicates due to the old channel.json format
                    {
                        tracksWithScoreboards.push(data.lineup[i].options.trackId);
                        data.lineup[i].options.url = appendLocaleAndBackgroundImg('pages/slides/scoreboard/#/' + (data.lineup[i].options.trackId));
                        formattedData.timelines.races.slides.push(data.lineup[i]);
                    }
                }
                else
                {
                    formattedData.timelines.regular.slides.push(data.lineup[i]);
                }
            }

            formattedData.timelines.races.tracks = tracksWithScoreboards;
            return formattedData;
        }
        else //If using the new channel format, just figure out which tracks have scoreboards
        {
            var tracksWithScoreboards = [];
            for(var i=0; i < data.timelines.races.slides.length; i++)
            {
                if (data.timelines.races.slides[i].type == "scoreboard")
                {
                    var trackAlreadyHasAScoreboard = (isInArray(data.timelines.races.slides[i].options.trackId,tracksWithScoreboards));
                    if (!trackAlreadyHasAScoreboard) //Prevents duplicates
                    {
                        tracksWithScoreboards.push(data.timelines.races.slides[i].options.trackId);
                        if (typeof data.timelines.races.slides[i].options.url == 'undefined')
                        {
                            data.timelines.races.slides[i].options.url = appendLocaleAndBackgroundImg('pages/slides/scoreboard/#/' + (data.timelines.races.slides[i].options.trackId));
                        }
                    }
                }
            }
            data.timelines.races.tracks = tracksWithScoreboards;
            return data;
        }
    }

    /*
     Checks if the slide meets any conditions required to display.

     Conditions are received in this format:

     "showConditions": {
     "repeatable": {
     "daysOfTheWeek": [],
     "daysOfTheMonth": [],
     "months": [],
     "startTime": "",
     "endTime": "",

     },
     "specific": {
     "startDate": "",
     "endDate": ""
     }
     }*/
    function shouldShowSlide()
    {
        var slide = $scope.channel.channelLineUp.timelines[$scope.currentTimeline]['slides'][$scope.channel.currentSlideIndex];
        var slideShouldBeShown = true;
        debugToConsole("-------------------------------------------------");
        debugToConsole('Checking for presence of slide conditions...');
        if (typeof slide != "undefined" && Object.size(slide.options.showConditions) > 0)
        {
            debugToConsole('Slide has show conditions:');
            debugToConsole(slide.options.showConditions);
            if (typeof slide.options.showConditions.repeatable != "undefined" && Object.size(slide.options.showConditions.repeatable) > 0)
            {
                debugToConsole('Slide has repeatable show conditions.');
                if (typeof slide.options.showConditions.repeatable.daysOfTheWeek != "undefined"
                    && slide.options.showConditions.repeatable.daysOfTheWeek.length > 0)
                {
                    debugToConsole('Slide has daysOfTheWeek show conditions:');
                    var daysOfTheWeek = slide.options.showConditions.repeatable.daysOfTheWeek;
                    debugToConsole(daysOfTheWeek);
                    var today = new Date();
                    var dayOfTheWeekToday = today.getDay() + 1;
                    debugToConsole('Today is day ' + dayOfTheWeekToday + ' of the week.');
                    if (daysOfTheWeek.indexOf(dayOfTheWeekToday) > -1)
                    {
                        debugToConsole('PASS: Today is a valid day of the week to show the slide.');
                    }
                    else
                    {
                        debugToConsole('FAIL: Today is NOT a valid day of the week to show the slide.');
                        slideShouldBeShown = false;
                    }

                }
                if (typeof slide.options.showConditions.repeatable.daysOfTheMonth != "undefined"
                    && slide.options.showConditions.repeatable.daysOfTheMonth.length > 0)
                {
                    debugToConsole('Slide has daysOfTheMonth show conditions:');
                    var daysOfTheMonth = slide.options.showConditions.repeatable.daysOfTheMonth;
                    debugToConsole(daysOfTheMonth);
                    var today = new Date();
                    var dateToday = today.getDate();
                    debugToConsole('Today is day ' + dateToday + ' of the month.');
                    if (daysOfTheMonth.indexOf(dateToday) > -1)
                    {
                        debugToConsole('PASS: Today is a valid day of the month to show the slide.');
                    }
                    else
                    {
                        debugToConsole('FAIL: Today is NOT a valid day of the month to show the slide.');
                        slideShouldBeShown = false;
                    }
                }
                if (typeof slide.options.showConditions.repeatable.months != "undefined"
                    && slide.options.showConditions.repeatable.months.length > 0)
                {
                    debugToConsole('Slide has months show conditions:');
                    var months = slide.options.showConditions.repeatable.months;
                    debugToConsole(months);
                    var today = new Date();
                    var monthToday = today.getMonth()+1;
                    debugToConsole('This month is ' + monthToday);
                    if (months.indexOf(monthToday) > -1)
                    {
                        debugToConsole('PASS: It is a valid month to show the slide.');
                    }
                    else
                    {
                        debugToConsole('FAIL: It is NOT a valid month to show the slide.');
                        slideShouldBeShown = false;
                    }
                }
                if (typeof slide.options.showConditions.repeatable.startTime != "undefined"
                    && slide.options.showConditions.repeatable.startTime.length > 0)
                {
                    debugToConsole('Slide has startTime show conditions:');
                    var startTime = slide.options.showConditions.repeatable.startTime;
                    debugToConsole(startTime);
                    var today = new Date();
                    var currentTime = pad(today.getHours()) + ':' + pad(today.getMinutes());
                    debugToConsole("The current time is " + currentTime);
                    if (currentTime >= startTime)
                    {
                        debugToConsole('PASS: It is after the start time for the slide.');
                    }
                    else
                    {
                        debugToConsole('FAIL: It is NOT yet time to start the slide.');
                        slideShouldBeShown = false;
                    }
                }
                if (typeof slide.options.showConditions.repeatable.endTime != "undefined"
                    && slide.options.showConditions.repeatable.endTime.length > 0)
                {
                    debugToConsole('Slide has endTime show conditions:');
                    var endTime = slide.options.showConditions.repeatable.endTime;
                    debugToConsole(endTime);
                    var today = new Date();
                    var currentTime = pad(today.getHours()) + ':' + pad(today.getMinutes());
                    debugToConsole("The current time is " + currentTime);
                    if (currentTime < endTime)
                    {
                        debugToConsole('PASS: It is before the end time for the slide.');
                    }
                    else
                    {
                        debugToConsole('FAIL: It is AFTER the end time for the slide.');
                        slideShouldBeShown = false;
                    }
                }
            }
            if (typeof slide.options.showConditions.specific != "undefined" && Object.size(slide.options.showConditions.specific) > 0)
            {
                debugToConsole('Slide has specific show conditions.');
                var today = isoDate();
                debugToConsole("Today is " + isoDate());
                if (typeof slide.options.showConditions.specific.startDate != "undefined"
                    && slide.options.showConditions.specific.startDate.length > 0)
                {
                    debugToConsole('Slide has startDate show conditions:');
                    var startDate = slide.options.showConditions.specific.startDate;
                    debugToConsole(startDate);
                    if (today >= startDate)
                    {
                        debugToConsole('PASS: It is after the start date for the slide.');
                    }
                    else
                    {
                        debugToConsole('FAIL: It is NOT yet the correct day and time to start the slide.');
                        slideShouldBeShown = false;
                    }
                }
                if (typeof slide.options.showConditions.specific.endDate != "undefined"
                    && slide.options.showConditions.specific.endDate.length > 0)
                {
                    debugToConsole('Slide has endDate show conditions:');
                    var endDate = slide.options.showConditions.specific.endDate;
                    debugToConsole(endDate);
                    if (today < endDate)
                    {
                        debugToConsole('PASS: It is before the end date for the slide.');
                    }
                    else
                    {
                        debugToConsole('FAIL: It is PAST the slide end date.');
                        slideShouldBeShown = false;
                    }
                }
            }
            else
            {
                debugToConsole('Slide actually had no valid show conditions!');
            }
        }
        else
        {
            debugToConsole('Slide does not have any show conditions.');
        }
        if (typeof slide != "undefined" && slideShouldBeShown)
        {
            debugToConsole('RESULT: Slide will be shown.');
            debugToConsole("-------------------------------------------------");
            return true;
        }
        else
        {
            debugToConsole('RESULT: Slide will NOT be shown.');
            debugToConsole("-------------------------------------------------");
            return false;
        }

        return true;
    }

    function tryToShowNextSlide()
    {
        debugToConsole("Trying to show next slide in the " + $scope.currentTimeline + " timeline.");
        if ($scope.currentTimeline == 'regular')
        {
            if ($scope.validSlideSearchStart == null) //Record where we've started our search
            {
                $scope.validSlideSearchStart = $scope.channel.currentSlideIndex - 1;
                if ($scope.validSlideSearchStart < 0)
                {
                    $scope.validSlideSearchStart = $scope.channel.numberOfRegularTimelineSlides - 1;
                }
            }

            if ($scope.channel.currentSlideIndex == $scope.validSlideSearchStart) //If we ended our search, report an error
            {
                reportErrorAndRestart($scope.strings['str_noValidSlidesToShow']);
            }
            else //Look for the next valid slide
            {
                debugToConsole("Current slide cannot be shown. Skipping to next slide.");
                $scope.channel.showNextTimelineAndSlide();
            }
        }
        else if ($scope.currentTimeline == 'races')
        {
            if ($scope.validRacesSlideSearchStart == null) //Record where we've started our search
            {
                $scope.validRacesSlideSearchStart = $scope.channel.currentSlideIndex - 1;
                if ($scope.validRacesSlideSearchStart < 0)
                {
                    $scope.validRacesSlideSearchStart = $scope.channel.numberOfRacesTimelineSlides - 1;
                }
            }

            if ($scope.channel.currentSlideIndex == $scope.validRacesSlideSearchStart) //If we ended our search, report an error
            {
                reportErrorAndRestart($scope.strings['str_noValidSlidesToShow']);
            }
            else //Look for the next valid slide
            {
                debugToConsole("Current slide cannot be shown. Skipping to next slide.");

                debugToConsole("Time to show the next slide. Current timeline: " + $scope.currentTimeline);
                if ($scope.channel.numberOfRacesTimelineSlides > 1)
                {
                    $scope.channel.currentSlideIndex++;
                    if ($scope.channel.currentSlideIndex >= $scope.channel.numberOfRacesTimelineSlides)
                    {
                        $scope.channel.currentSlideIndex = 0;
                    }

                    if (!shouldShowSlide()) //If the current slide has a filter preventing it from being seen
                    {
                        tryToShowNextSlide(); //Try to skip to the next one
                    }
                    else
                    {
                        $scope.validRacesSlideSearchStart = null;
                        debugToConsole("Moving speedscreen to slide " + $scope.channel.currentSlideIndex + " of the Races timeline");
                        var durationOfCurrentSlideMs = $scope.channel.renderCurrentSlide();
                        debugToCountdownArea('Time until next slide: ', durationOfCurrentSlideMs);
                        $scope.channel.queueUpNextSlide(durationOfCurrentSlideMs);
                    }
                }
            }
        }

    }

    function getScoreboard(track)
    {
        var deferred = $q.defer();
        deferred.resolve(apiService.getScoreboard(track));
        return deferred.promise;
    }

    function updateRaceStateAndRunConditionalOverlays()
    {
        var currentSlide = $scope.channel.channelLineUp.timelines[$scope.currentTimeline]['slides'][$scope.channel.currentSlideIndex];

        if (currentSlide.type == 'scoreboard' && typeof currentSlide.options.eventSlides != "undefined")
        {
            var promise = getScoreboard(currentSlide.options.trackId);
            promise.then(
                function(response)
                {
                    var data = response.data;
                    $scope.prevRaceState = $scope.raceState;

                    if (typeof data.error == "undefined")
                    {
                        if (data.race.race_time_in_seconds === null)
                        {
                            $scope.raceState = 'startButtonPressed';
                            if (typeof currentSlide.options.eventSlides.onRaceStart != "undefined" && $scope.prevRaceState == 'none')
                            {
                                debugToConsole("LAUNCH: onRaceStart overlay");
                                $scope.overlaySlideURL = $sce.trustAsResourceUrl(appendLocaleAndBackgroundImg(currentSlide.options.eventSlides.onRaceStart.url));
                                $scope.showOverlaySlide = true;
                                $timeout.cancel($scope.overlayTimeout);
                                $scope.overlayTimeout = $timeout(function(){
                                    $scope.showOverlaySlide = false;
                                    $scope.overlaySlideURL = '';
                                },currentSlide.options.eventSlides.onRaceStart.duration);
                            }
                        }
                        else
                        {
                            $scope.raceState = 'firstLapStarted';
                            if (typeof currentSlide.options.eventSlides.onFirstLapStart != "undefined" && $scope.prevRaceState == 'startButtonPressed')
                            {
                                debugToConsole("LAUNCH: onFirstLapStart overlay");
                                $scope.overlaySlideURL = $sce.trustAsResourceUrl(appendLocaleAndBackgroundImg(currentSlide.options.eventSlides.onFirstLapStart.url));
                                $scope.showOverlaySlide = true;
                                $timeout.cancel($scope.overlayTimeout);
                                $scope.overlayTimeout = $timeout(function(){
                                    $scope.showOverlaySlide = false;
                                    $scope.overlaySlideURL = '';
                                },currentSlide.options.eventSlides.onFirstLapStart.duration);
                            }
                        }

                        if (data.scoreboard.length > 0)
                        {
                            $scope.raceState = 'running';
                            if (typeof currentSlide.options.eventSlides.onFirstLapCompleted != "undefined" && $scope.prevRaceState == 'firstLapStarted')
                            {
                                debugToConsole("LAUNCH: onFirstLapCompleted overlay");
                                $scope.overlaySlideURL = $sce.trustAsResourceUrl(appendLocaleAndBackgroundImg(currentSlide.options.eventSlides.onFirstLapCompleted.url));
                                $scope.showOverlaySlide = true;
                                $timeout.cancel($scope.overlayTimeout);
                                $scope.overlayTimeout = $timeout(function(){
                                    $scope.showOverlaySlide = false;
                                    $scope.overlaySlideURL = '';
                                },currentSlide.options.eventSlides.onFirstLapCompleted.duration);
                            }
                        }
                    }
                    else
                    {
                        $scope.raceState = 'none';
                        if (typeof currentSlide.options.eventSlides.onRaceEnd != "undefined" && $scope.prevRaceState == 'running')
                        {
                            debugToConsole("LAUNCH: onRaceEnd overlay");
                            $scope.overlaySlideURL = $sce.trustAsResourceUrl(appendLocaleAndBackgroundImg(currentSlide.options.eventSlides.onRaceEnd.url));
                            $scope.showOverlaySlide = true;
                            $timeout.cancel($scope.overlayTimeout);
                            $scope.overlayTimeout = $timeout(function(){
                                $scope.showOverlaySlide = false;
                                $scope.overlaySlideURL = '';
                            },currentSlide.options.eventSlides.onRaceEnd.duration);
                        }
                    }
                },
                function(error)
                {
                    console.log(error);
                });

        }
    }

    function reportErrorAndRestart(message)
    {
        $scope.state = 'loading';
        Channel.prototype.abortAllSlideTimeouts();
        $interval.cancel($scope.channelUpdateInterval);

        $scope.loadingHeaderText = $scope.strings['str_speedScreenOffline'];
        $scope.loadingStepText = message;
        $scope.loadingRestartText = $scope.strings['str_restartingIn'];
        $scope.loadingRestartTime = $scope.settings['timeUntilRestartOnErrorMs']/1000;
        $scope.testingConnection = false;
        if (typeof($scope.restartIntervalFunction) == "undefined" || $scope.restartIntervalFunction == null) {
            $scope.restartIntervalFunction = $interval(function(){
                if ($scope.loadingRestartTime <= 1)
                {
                    if (!$scope.testingConnection)
                    {
                        console.log("Checking online connectivity...");
                        $scope.testingConnection = true;
                        apiService.verifyConnectivityToServerAndFetchSettings(4000).
                        success(function(data, status, headers, config) {
                            console.log("Internet connection is back up. Restarting HD Speed Screen...");
                            $interval.cancel($scope.restartIntervalFunction);
                            $scope.restartIntervalFunction = null;
                            $window.location.reload(true);                    }).
                        error(function(data, status, headers, config) {
                            $scope.testingConnection = false;
                            console.log("Internet is still offline. Resuming countdown to next restart.");
                            $scope.loadingRestartTime = $scope.settings['timeUntilRestartOnErrorMs']/1000;
                        });
                    }
                }
                else
                {
                    $scope.loadingRestartTime--;
                }
            },1000);
        }
    }

    // ##########################
    // # DEBUG OUTPUT FUNCTIONS #
    // ##########################

    function debugToConsole(message)
    {
        if ($scope.debug) {console.log(message);}
    }

    function debugToCountdownArea(message,timeMs)
    {
        if ($scope.debug)
        {
            for (var i = 0; i < $scope.debugIntervalFunctions.length; i++)
            {
                $interval.cancel($scope.debugIntervalFunctions[i]);
            }
            $scope.debugCountdown = timeMs/1000;
            $scope.debugIntervalFunctions.push($interval(function(){
                if ($scope.debugCountdown < 1)
                {
                    for (var i = 0; i < $scope.debugIntervalFunctions.length; i++)
                    {
                        $interval.cancel($scope.debugIntervalFunctions[i]);
                    }
                    $scope.debugIntervalFunctions = [];
                    $scope.debugMessage = '';
                }
                else
                {
                    $scope.debugCountdown--;
                    $scope.debugMessage = message + $scope.debugCountdown;
                }
            },1000));
        }
    }

    function debugClearCountdownArea()
    {
        if ($scope.debug)
        {
            for (var i = 0; i < $scope.debugIntervalFunctions.length; i++)
            {
                $interval.cancel($scope.debugIntervalFunctions[i]);
            }
            $scope.debugIntervalFunctions = [];
            $scope.debugMessage = '';
            $scope.debugCountdown = 0;
        }
    }

    // #####################
    // # UTILITY FUNCTIONS #
    // #####################

    function isInArray(value, array) {
        return array.indexOf(value.toString()) > -1;
    }

    function getQueryVariable(string,variable)
    {
        var query = string;
        query = query.split('?')[1];
        var vars = query.split("&");
        for (var i=0;i<vars.length;i++) {
            var pair = vars[i].split("=");
            if(pair[0] == variable){return pair[1];}
        }
        return(false);
    }

    //Determines the size of a JavaScript object
    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };

    //Pads a number to have a 0 in front if it's less than two digits
    function pad(number) {
        var r = String(number);
        if ( r.length === 1 ) {
            r = '0' + r;
        }
        return r;
    }

    function isoDate()
    {
        var date = new Date();

        return date.getFullYear()
            + '-' + pad( date.getMonth() + 1 )
            + '-' + pad( date.getDate() )
            + 'T' + pad( date.getHours() )
            + ':' + pad( date.getMinutes() );
    }

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

    function appendLocaleAndBackgroundImg(url)
    {
        if (url.indexOf("locale=") === -1)
        {
            if (url.indexOf('?') === -1)
            {
                url += "?";
            }
            url += "&locale=" + $scope.locale;
        }
        if (url.indexOf("backgroundUrl=") === -1)
        {
            if (url.indexOf('?') === -1)
            {
                url += "?";
            }
            url += "&backgroundUrl=" + $scope.backgroundImageURL;
        }
        if ($scope.disableAnimations)
        {
            if (url.indexOf('?') === -1)
            {
                url += "?";
            }
            url += "&disableAnimations=" + "1";
        }
        if (globalSettings.getTrackOverride() != null && url.indexOf('&trackOverride') === -1)
        {
            if (url.indexOf('?') === -1)
            {
                url += "?";
            }
            url += "&trackOverride=" + globalSettings.getTrackOverride();
        }
        var httpsRegEx = /https:/gi;
        var updatedURL = url.replace(httpsRegEx, 'http:');
        return updatedURL;
    }

    function processAndUpdateSettings(settings)
    {
        var formattedSettings = {};

        for(var i = 0; i < settings.length; i++)
        {
            var currentSetting = settings[i];
            formattedSettings[currentSetting.name] = currentSetting.value;
            if (currentSetting.type == 'Integer')
            {
                formattedSettings[currentSetting.name] = parseInt(currentSetting.value);
            }
        }
        $scope.settings = mergeObjects($scope.settings,formattedSettings);

        if ($scope.locale == 'en-US' && $scope.settings['defaultLocale'] !== 'en-US' && translations.hasOwnProperty($scope.settings['defaultLocale']))
        {
            debugToConsole("Admin panel specified default language override to " + $scope.settings['defaultLocale']);
            $scope.strings = translations[$scope.settings['defaultLocale']];
            $scope.locale = $scope.settings['defaultLocale'];
        }
        if ($scope.settings['channelSource'] == 'new')
        {
            debugToConsole("Using new Speed Screen channels.");
            globalSettings.setChannelSource("new");
        }
    }
});