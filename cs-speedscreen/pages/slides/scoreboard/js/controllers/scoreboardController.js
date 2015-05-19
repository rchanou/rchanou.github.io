/* Scoreboard Controller

 This is the heart of the Scoreboard slide. It can be operated independently from a Speed Screen.

 URL format and route parameters: .../scoreboard/#/{track_id}/{theme}
 Defaults: {track_id} = 1, {theme} = classic
 Themes: 'classic', 'big'

 Optional/traditional URL parameters: (Use 0 for false, 1 for true)
 - backgroundUrl: Background image for classic theme. Defaults to images/backgrounds/default.jpg
 - pollingInterval: Milliseconds between polling the scoreboard. Defaults to 1000.
 - headerEnabled: Whether or not the header is visible. Only applies to the big theme. Defaults to true.
 - showHeatNumber: Whether to show the heat number for a race. Defaults to true.
 - showHeatTime: Whether to show the time of a heat for a race. Defaults to false. (Tracks run late often.)
 - showHeaderTimer: Whether or not to show the time remaining in a race. Defaults to true.
 - locale: The language to use. Defaults to 'en-US'. Used in translations and localization.
 - highlightFastestRacer: Whether to change the color of a racer's best lap time if they are the fastest racer. Defaults to true.
 - fastestRacerColor: Which color to highlight a fastest racer with. Hex format, *without* the pound sign. Defaults to 00FF00.
 - textLabelsColor: Which color to use for text labels. Hex format, *without* the pound sign. Defaults to FFFFFF.
 - textDataColor: Which color to use for text data. Hex format, *without* the pound sign. Defaults to FFD700.
 - racersPerPage: How many racers to show per page before paginating. Defaults to 10.
 - timePerPage: If paginating, how much time to spend on each page, in milliseconds. Defaults to 10000.
 - nextRacerTabEnabled: Whether or not the next racer tab is enabled. Defaults to true.
 - demo: Whether to be in regular mode or a demo mode. Defaults to false, and if true, creates 20 demo racers.
 - filterRacers: A range of positions to show on the screen. Ex. "6-10" will make it so only racers in positions 6 through 10 show. Default: off.
 - finalResultsTime: How long to show final results before showing the next race. Default: 15000 milliseconds
 - showSequenceNumber: Whether to show sequence numbers instead of heat numbers. Default: true
 - showLapEstimation: Whether to show an overlay estimating how long until a lap is completed. Default: false

 The parameters above will most often be set by the Speed Screen application when rendering the slide in an iframe.

 TODO:
 - Non-polling driver (pending back-end technology)
 */

scoreboardApp.controller('scoreboardController', function($scope, $interval, $timeout, $routeParams, $location, apiServices) {

    // #################
    // # VARIABLE INIT #
    // #################

    //Key models
    $scope.racersOnScoreboard = {}; //Rendered to the screen
    $scope.oldRacers = {}; //Used to detect state changes (racer got a faster lap, etc)
    $scope.racersFromPreviousRace = {}; //Used to store final results

    //Key state variables
    $scope.scoreboardState = "loading"; //Possible states: "loading", "no_races", "waiting_for_first_lap", "race_running", "last_results"
    $scope.mostRecentHeatID = 0;
    $scope.needToFetchFinalResults = true;
    $scope.headerTimer = "";
    $scope.currentPage = 0;
    $scope.nextRaceDetails = null;
    $scope.nextRacerReady = false;
    $scope.showingFinalResults = false;
    $scope.lastActiveRaceLapCount = null;

    $scope.minutes = 0;
    $scope.seconds = 0;
    $scope.lastOfficialMinutes = 0;
    $scope.lastOfficialSeconds = 0;

    //Default sizes for racer boxes
    $scope.racerBoxWidth = 100;
    $scope.racerBoxHeight = 20;
    $scope.racerBoxHeaderHeight = 10;
    $scope.racerBoxFontSize = 3;

    //API polling throttling
    $scope.numberOfPendingScoreboardCalls = 0;
    $scope.numberOfPendingNextRaceCalls = 0;

    //URL and route configuration parameters
    $scope.track_id = $routeParams.track_id == null ? 1 : $routeParams.track_id; //Track ID to watch for races
    $scope.theme = $routeParams.theme == null ? 'classic' : $routeParams.theme; //Possible themes: 'big','classic'
    var backgroundImage = typeof $location.search().backgroundUrl == "undefined" ? "http://" + window.location.hostname + "/cs-speedscreen/pages/slides/scoreboard/images/backgrounds/default.jpg" : $location.search().backgroundUrl;
    var pollingIntervalMs = typeof $location.search().pollingInterval == "undefined" ? 1000 : $location.search().pollingInterval; //Milliseconds
    $scope.classicThemeHeaderEnabled = true; //Can be made a configuration setting at a later date, if desired
    $scope.classicThemeTopTimesEnabled = true; //Can be made a configuration setting at a later date, if desired
    $scope.bigThemeHeaderEnabled = typeof $location.search().headerEnabled == "undefined" ? true : ($location.search().headerEnabled === '1');
    $scope.showHeatNumber = typeof $location.search().showHeatNumber == "undefined" ? true : ($location.search().showHeatNumber === '1');
    $scope.showHeatTime = typeof $location.search().showHeatTime == "undefined" ? false : ($location.search().showHeatTime === '1');
    $scope.showHeaderTimer = typeof $location.search().showHeaderTimer == "undefined" ? true : ($location.search().showHeaderTimer === '1');
    $scope.locale = typeof $location.search().locale == "undefined" ? 'en-US' : $location.search().locale;
    $scope.highlightFastestRacer = typeof $location.search().highlightFastestRacer == "undefined" ? true : ($location.search().highlightFastestRacer === '1');
    $scope.fastestRacerColor = typeof $location.search().fastestRacerColor == "undefined" ? '#00FF00' : ('#' + $location.search().fastestRacerColor);
    $scope.textLabelsColor = typeof $location.search().textLabelsColor == "undefined" ? '#FFFFFF' : ('#' + $location.search().textLabelsColor);
    $scope.textDataColor = typeof $location.search().textDataColor == "undefined" ? '#FFD700' : ('#' + $location.search().textDataColor);
    $scope.racersPerPage = typeof $location.search().racersPerPage == "undefined" ? 10 : $location.search().racersPerPage < 5 ? 5 : $location.search().racersPerPage ;
    $scope.timePerPageMs = typeof $location.search().timePerPage == "undefined" ? 10000 : $location.search().timePerPage;
    $scope.nextRacerTabEnabled = typeof $location.search().nextRacerTabEnabled == "undefined" ? true : ($location.search().nextRacerTabEnabled === '1');
    $scope.demoMode = typeof $location.search().demo == "undefined" ? false : ($location.search().demo === '1');
    $scope.filterRacers = typeof $location.search().filterRacers == "undefined" ? false : ($location.search().filterRacers);
    $scope.finalResultsTime = typeof $location.search().finalResultsTime == "undefined" ? 15000 : ($location.search().finalResultsTime);
    $scope.showSequenceNumber = typeof $location.search().showSequenceNumber == "undefined" ? true : ($location.search().showSequenceNumber === '1');
    $scope.showLapEstimation = typeof $location.search().showLapEstimation == "undefined" ? false : ($location.search().showLapEstimation === '1');

    //Dynamic CSS style for scoreboard background
    $scope.backgroundImage = "{'background-image': 'url(\"" + backgroundImage + " \")', 'background-size': 'cover', 'background-repeat': 'no-repeat', 'background-position': 'center'}";

    //Dynamic CSS styles for various text colors
    $scope.fastestRacerStyle = $scope.highlightFastestRacer ? "{'color': '" + $scope.fastestRacerColor + "'}" : "{'color': '" + $scope.textDataColor + "'}";
    $scope.textLabelsColorStyle = "{'color': '" + $scope.textLabelsColor + "'}";
    $scope.textDataColorStyle = "{'color': '" + $scope.textDataColor + "'}";

    //Intervals
    $scope.topTimesInterval = null;
    $scope.nextRacerTabInterval = null;
    $scope.timerInterpolationInterval = null;
    $scope.scoreboardPollingInterval = null;
    $scope.paginationInterval = null;

    //Clear all intervals upon leaving this slide
    $scope.$on("$destroy", function() {
        $interval.cancel($scope.topTimesInterval);
        $interval.cancel($scope.nextRacerTabInterval);
        $interval.cancel($scope.timerInterpolationInterval);
        $interval.cancel($scope.scoreboardPollingInterval);
        $interval.cancel($scope.paginationInterval);
        $scope.topTimesInterval = null;
        $scope.nextRacerTabInterval = null;
        $scope.timerInterpolationInterval = null;
        $scope.scoreboardPollingInterval = null;
        $scope.paginationInterval = null;
    });

    // ################
    // # TRANSLATIONS #
    // ################

    $scope.strings = translations["en-US"]; //Default

    apiServices.getTranslations().success(function(data, status, headers, config) {
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
            $scope.strings = translations[$scope.validLocale];

            $scope.validLocale = translations.hasOwnProperty($scope.locale) ? $scope.locale : "en-US";
            $scope.strings = translations[$scope.validLocale];

            if (!translations.hasOwnProperty($scope.locale))
            {
                console.log('Locale was not supported. Defaulted to en-US.');
            }
        }
    }).error(function(data, status, headers, config) {
        console.log("Unable to fetch translations from server - defaulting to en-US.");
    });

    // ################
    // # ISOTOPE INIT #
    // ################

    //Initialize Isotope animation engine
    $timeout( function() {
        $('.raceRunning').isotope({
            itemSelector: '.racerBox',
            layoutMode: 'masonryHorizontal',
            masonryHorizontal: {
                columnWidth: '.racerBox'
            },
            getSortData: {
                position: '.position parseInt'
            },
            sortBy : 'position'
        });

        $timeout(function() {

            $('.raceRunning').isotope('updateSortData').isotope();
            $('.raceRunning').isotope('reloadItems');
        });
    });

    // ##################
    // # TOP TIMES INIT #
    // ##################

    //If in classic mode, queue up a rotation of Top Times queries
    if ($scope.theme == 'classic' && $scope.classicThemeTopTimesEnabled)
    {
        var validStatusBarModes = ["Today","Week","Month"]; //Array of valid "Top Times" time periods
        var currentStatusBarMode = 0; //Index of first top time to display

        //Initially populate the view with today's top times
        apiServices.getFastestLapTimes('day').success(function (data) {
            $scope.fastestTimesThisWeek = data.fastest;
            $scope.fastestTimeLabel = $scope.strings["str_today"];
        });

        //Then, on a timer, alternate between all the top times
        $scope.topTimesInterval = $interval(function()
            {
                currentStatusBarMode++; //Move on to the next top time
                if (currentStatusBarMode == validStatusBarModes.length) //Wrap around if necessary
                {
                    currentStatusBarMode = 0;
                }

                $scope.statusBarMode = validStatusBarModes[currentStatusBarMode];

                if (validStatusBarModes[currentStatusBarMode] == "Week") //Top times of the week
                {
                    apiServices.getFastestLapTimes('week').success(function (data) {
                        $scope.fastestTimesThisWeek = data.fastest;
                        $scope.fastestTimeLabel = $scope.strings["str_thisWeek"];
                    });

                }
                else if (validStatusBarModes[currentStatusBarMode] == "Month") //Top times of the month
                {
                    apiServices.getFastestLapTimes('month').success(function (data) {
                        $scope.fastestTimesThisWeek = data.fastest;
                        $scope.fastestTimeLabel = $scope.strings["str_thisMonth"];
                    });

                }
                else if (validStatusBarModes[currentStatusBarMode] == "Today") //Top times today
                {
                    apiServices.getFastestLapTimes('day').success(function (data) {
                        $scope.fastestTimesThisWeek = data.fastest;
                        $scope.fastestTimeLabel = $scope.strings["str_today"];
                    });
                }
            }
            ,15000);
    }

    // #######################
    // # NEXT RACER TAB INIT #
    // #######################

    //If in classic mode, queue up regular queries for the next racers tab
    if ($scope.theme == 'classic' && $scope.nextRacerTabEnabled)
    {
        $scope.nextRacerTabInterval = $interval(function() {
            if ($scope.numberOfPendingNextRaceCalls == 0)
            {
                $scope.numberOfPendingNextRaceCalls++;
                apiServices.getNextRace().success(function (data) {
                    $scope.numberOfPendingNextRaceCalls--;
                    if (typeof data.race != "undefined")
                    {
                        $scope.nextRaceDetails = data.race;
                    }
                    else
                    {
                        $scope.nextRaceDetails = null;
                        $scope.nextRacerReady = false;
                    }
                }).error(function (data, status, headers, config) {
                    $scope.numberOfPendingNextRaceCalls--;
                });
            }

        },5000);
    }

    // ############################
    // # TIMER INTERPOLATION INIT #
    // ############################

    $scope.timerInterpolationInterval = $interval(function() {
        if ($scope.scoreboardState == 'race_running' && Object.size($scope.racersOnScoreboard) > 0)
        {
            if ($scope.seconds == 0) {
                $scope.minutes -= 1;
                $scope.seconds = 59;
            }
            else {
                $scope.seconds -= 1;
            }
            if ($scope.minutes < 0) {
                $scope.seconds = 0;
            }
            if ($scope.seconds < 0) {
                $scope.seconds = 0;
            }
        }
    },1000);

    // #################################
    // # CORE SCOREBOARD POLLING LOGIC #
    // #################################

    $scope.scoreboardPollingInterval = $interval( function() //On an interval, try to query Club Speed for scoreboard data
    {
        if ($scope.numberOfPendingScoreboardCalls == 0) //If there aren't any pending API calls
        {
            apiServices.getActiveRaceLapCount($scope.track_id).success(function (data)
                {
                    if (!angular.equals($scope.lastActiveRaceLapCount, data))
                    {
                        $scope.numberOfPendingScoreboardCalls++;
                        $scope.lastActiveRaceLapCount = data;
                        apiServices.getScoreboard().success(function (data) { //Query Club Speed's API for the latest scoreboard
                            $scope.numberOfPendingScoreboardCalls--;
                            processScoreboardData(data);

                        }).error(function (data, status, headers, config) {
                            //console.log('Error reaching Club Speed API - Scoreboard call');
                            $scope.scoreboardState = 'disconnected';
                            $scope.numberOfPendingScoreboardCalls--;
                        });
                    }
                    else
                    {
                        processScoreboardData($scope.oldData);
                    }
                }
            );

        }

    },pollingIntervalMs);

    function processScoreboardData(data)
    {
        if ($scope.showingFinalResults) //If still showing final results, show older data instead
        {
            data = $scope.oldData;
        }
        $scope.oldData = JSON.parse(JSON.stringify(data));

        if ($scope.demoMode) //If we're in demo-mode, overwrite the data with test data
        {
            data = generateAndUpdateDemoData();
        }

        if (data.hasOwnProperty('race')) //If there's a race happening
        {
            //Package some basic data
            $scope.race = data; //Used by the view to show race info
            $scope.nextRace = {};
            $scope.mostRecentHeatID = data.race.id;
            $scope.needToFetchFinalResults = true;

            if (data.hasOwnProperty('scoreboard') && data.scoreboard.length == 0 && !$scope.showingFinalResults) //If racers haven't crossed the loop yet
            {
                //Set the state data as appropriate
                $scope.scoreboardState = "waiting_for_first_lap";
                $scope.racersOnScoreboard = {};
                $scope.oldRacers = {};
                $scope.racersFromPreviousRace = {};
            }
            else //If racers have crossed the loop, format the scoreboard and process racing data for the view
            {
                if (typeof $scope.racersOnScoreboard == "undefined") //Error case prevention
                {
                    $scope.racersOnScoreboard = {};
                }

                $scope.scoreboardState = "race_running";

                $scope.oldRacers = JSON.parse(JSON.stringify($scope.racersOnScoreboard)); //Copying the racer state before it changes

                for(var key in data.scoreboard) //Package the racer data for the view to display
                {
                    var currentRacer = data.scoreboard[key];
                    $scope.racersOnScoreboard[currentRacer.racer_id] = currentRacer;
                }
                $scope.racersFromPreviousRace = JSON.parse(JSON.stringify($scope.racersOnScoreboard)); //Copying latest racer state to memory

                // #################
                // # RACE ANALYSIS #
                // #################

                var mostLapsCompleted = 0;
                var fastestRacer;
                var fastestTime = 9999;
                //Determine some statistics about each racer
                for (var currentRacer in $scope.racersOnScoreboard)
                {
                    if ($scope.racersOnScoreboard.hasOwnProperty(currentRacer))
                    {
                        //Determine the highest number of laps
                        if (parseInt($scope.racersOnScoreboard[currentRacer].lap_num) > parseInt(mostLapsCompleted))
                        {
                            mostLapsCompleted = $scope.racersOnScoreboard[currentRacer].lap_num;
                        }

                        //Determine the fastest lap time
                        $scope.racersOnScoreboard[currentRacer].isFastest = false;
                        if (parseFloat($scope.racersOnScoreboard[currentRacer].fastest_lap_time) < parseFloat(fastestTime))
                        {
                            fastestTime = $scope.racersOnScoreboard[currentRacer].fastest_lap_time;
                            fastestRacer = currentRacer; //And who made that fastest lap time
                        }

                        //If a racer just finished a lap, make their lap number and most recent lap time flash, and record the time of the latest passing
                        $scope.racersOnScoreboard[currentRacer].racerLapNumberIncreased = false;
                        if (currentRacer in $scope.oldRacers && parseInt($scope.racersOnScoreboard[currentRacer].lap_num) > parseInt($scope.oldRacers[currentRacer].lap_num))
                        {
                            $scope.racersOnScoreboard[currentRacer].racerLapNumberIncreased = true;
                            $scope.racersOnScoreboard[currentRacer].lastPassing = Date.now();
                            $scope.racersOnScoreboard[currentRacer].timeSinceLastPassing = 0;
                        }
                        else
                        {
                            if (typeof $scope.oldRacers[currentRacer] != "undefined")
                            {
                                $scope.racersOnScoreboard[currentRacer].lastPassing = $scope.oldRacers[currentRacer].lastPassing;
                            }
                        }

                        //Determine how much it's been for a racer since their last lap
                        if (typeof $scope.racersOnScoreboard[currentRacer].lastPassing != 'undefined')
                        {
                            $scope.racersOnScoreboard[currentRacer].timeSinceLastPassing = Date.now() - $scope.racersOnScoreboard[currentRacer].lastPassing;
                        }
                        else
                        {
                            $scope.racersOnScoreboard[currentRacer].timeSinceLastPassing = 0;
                        }

                        //If a racer just beat their own overall best lap, flag them to flash their recent lap purple
                        $scope.racersOnScoreboard[currentRacer].beatBestLap = false;
                        if (currentRacer in $scope.oldRacers && parseFloat($scope.racersOnScoreboard[currentRacer].fastest_lap_time) < parseFloat($scope.oldRacers[currentRacer].fastest_lap_time))
                        {
                            $scope.racersOnScoreboard[currentRacer].beatBestLap = true;
                        }

                        //Determine whether the racer went up, went down, or stayed the same
                        $scope.racersOnScoreboard[currentRacer].positionStatus = 'neutral'; //Default state - didn't go up or down
                        if ($scope.racersOnScoreboard[currentRacer].lap_num == 0)
                        {
                            $scope.racersOnScoreboard[currentRacer].positionStatus = 'neutral'; //If it's the first practice lap, note no change in state
                        }
                        if (currentRacer in $scope.oldRacers && $scope.racersOnScoreboard[currentRacer].position < $scope.oldRacers[currentRacer].position)
                        {
                            $scope.racersOnScoreboard[currentRacer].positionStatus = 'wentUp'; //If a racer went up in position, make a note of it
                        }
                        else if (currentRacer in $scope.oldRacers && $scope.racersOnScoreboard[currentRacer].position > $scope.oldRacers[currentRacer].position)
                        {
                            $scope.racersOnScoreboard[currentRacer].positionStatus = 'wentDown'; //If a racer went down in position, make a note of it
                        }
                        else if (currentRacer in $scope.oldRacers)
                        {
                            $scope.racersOnScoreboard[currentRacer].positionStatus = $scope.oldRacers[currentRacer].positionStatus; //Remember their previous state
                        }

                        //If a racer just finished a lap, make their lap number and most recent lap time flash
                        $scope.racersOnScoreboard[currentRacer].racerLapNumberIncreased = false;
                        if (currentRacer in $scope.oldRacers && $scope.racersOnScoreboard[currentRacer].lap_num > $scope.oldRacers[currentRacer].lap_num)
                        {
                            $scope.racersOnScoreboard[currentRacer].racerLapNumberIncreased = true;
                            if ($scope.racersOnScoreboard[currentRacer].position == $scope.oldRacers[currentRacer].position)
                            {
                                $scope.racersOnScoreboard[currentRacer].positionStatus = 'neutral';
                                $scope.oldRacers[currentRacer].positionStatus = 'neutral';
                            }
                        }
                    }
                }
                //If the race is by position
                if (data.race.win_by == "position")
                {
                    for (var currentRacer in $scope.racersOnScoreboard) //Format their gaps to be lap times if they're behind at least a lap
                    {
                        if ($scope.racersOnScoreboard.hasOwnProperty(currentRacer))
                        {
                            if (parseInt($scope.racersOnScoreboard[currentRacer].lap_num) < parseInt(mostLapsCompleted)) //If they're behind in laps
                            {
                                $scope.racersOnScoreboard[currentRacer].gap = parseInt($scope.racersOnScoreboard[currentRacer].gap) + $scope.strings["str_lapAbbreviation"]; //Format their gap to be an actual lap number
                            }
                        }
                    }
                }
                if (fastestRacer in $scope.racersOnScoreboard)
                {
                    $scope.racersOnScoreboard[fastestRacer].isFastest = true; //Make a note of the fastest racer by having their best lap time be written in a special color
                }

                if (data.race.race_by == 'minutes') //Format the timer as appropriate
                {
                    var minutes = parseInt((data.race.duration*60 - data.race.race_time_in_seconds)/60);
                    var seconds = parseInt((data.race.duration*60 - data.race.race_time_in_seconds)%60);

                    if ($scope.lastOfficialMinutes != minutes || $scope.lastOfficialSeconds != seconds) //If we receive a new set of official times, overwrite our interpolated ones
                    {
                        $scope.lastOfficialMinutes = minutes;
                        $scope.lastOfficialSeconds = seconds;
                        $scope.minutes = minutes;
                        $scope.seconds = seconds;
                    }

                    if ($scope.minutes < 0){ $scope.minutes = 0; }
                    if ($scope.seconds < 0){ $scope.seconds = 0; }

                    $scope.headerTimer = ($scope.minutes < 10 ? "0" + $scope.minutes : $scope.minutes) + ":" +
                    ($scope.seconds  < 10 ? "0" + $scope.seconds : $scope.seconds);
                    if ($scope.headerTimer == "00:00")
                    {
                        $scope.headerTimer = "";
                    }
                }
                else if (data.race.race_by == 'laps') //Format the laps remaining as appropriate
                {
                    $scope.mostLapsCompleted = mostLapsCompleted;

                    if ($scope.mostLapsCompleted == undefined || parseInt($scope.mostLapsCompleted) > parseInt(data.race.duration))
                    {
                        $scope.mostLapsCompleted = data.race.duration;
                    }
                    var lapsRemaining = data.race.duration - ($scope.mostLapsCompleted < 0 ? 0 : $scope.mostLapsCompleted);
                    if (lapsRemaining < 0)
                    {
                        lapsRemaining = 0;
                    }
                    $scope.lapsRemaining = lapsRemaining;

                    $scope.headerTimer = $scope.lapsRemaining + 'L';
                }

                // #####################################
                // # FILTERING SPECIFIC SETS OF RACERS #
                // #####################################

                if ($scope.filterRacers !== false) //If we only want to show a specific range of racers on this screen
                {
                    var filters = $scope.filterRacers.split("-");
                    var firstRacerPosition = parseInt(filters[0]);
                    var lastRacerPosition = parseInt(filters[1]);
                    lastRacerPosition = lastRacerPosition > Object.size($scope.racersOnScoreboard) ? Object.size($scope.racersOnScoreboard) : lastRacerPosition;
                    var filteredRacers = {};
                    for (var key in $scope.racersOnScoreboard) //Sort racers by position, filtering by desired range
                    {
                        if ($scope.racersOnScoreboard.hasOwnProperty(key) &&
                            ($scope.racersOnScoreboard[key].position) >= firstRacerPosition &&
                            ($scope.racersOnScoreboard[key].position) <= lastRacerPosition)
                        {
                            filteredRacers[$scope.racersOnScoreboard[key].position] = $scope.racersOnScoreboard[key];
                        }
                    }
                    $scope.racersOnScoreboard = filteredRacers;
                }

                adjustTemplateDynamically(data); //Resize scoreboard elements based on template, number of racers, and settings

                // ####################################
                // # PAGINATION DURING AN ACTIVE RACE #
                // ####################################

                var numOfRacers = Object.size($scope.racersOnScoreboard);
                if (numOfRacers > $scope.racersPerPage)  //If we need to paginate
                {
                    var sortedRacers = {};
                    for (var key in $scope.racersOnScoreboard) //Sort racers by position
                    {
                        if ($scope.racersOnScoreboard.hasOwnProperty(key)) {
                            sortedRacers[$scope.racersOnScoreboard[key].position] = $scope.racersOnScoreboard[key];
                        }
                    }

                    var i = 0;
                    var firstIndexToInclude = $scope.currentPage*$scope.racersPerPage;
                    var lastIndexToInclude = parseInt(firstIndexToInclude) + parseInt($scope.racersPerPage);
                    var filteredRacers = {};

                    for (var key in sortedRacers) //Filter out the racers that need to be paginated
                    {
                        if (sortedRacers.hasOwnProperty(key) && i >= firstIndexToInclude && i < lastIndexToInclude && i < numOfRacers) {
                            filteredRacers[i] = sortedRacers[key];
                        }
                        i++;
                    }

                    $scope.racers = filteredRacers;

                    if ($scope.paginationInterval == null) //Start flipping through each page of racers on a timer
                    {
                        $scope.paginationInterval = $interval(function () {
                            var currentPage = $scope.currentPage;
                            currentPage++;
                            if (currentPage >= Object.size($scope.racersOnScoreboard)/$scope.racersPerPage)
                            {
                                currentPage = 0;
                            }
                            $scope.currentPage = currentPage;
                        },$scope.timePerPageMs);
                    }
                }
                else {

                    $interval.cancel($scope.paginationInterval);
                    $scope.paginationInterval = null;
                    $scope.currentPage = 0;

                    $scope.racers = $scope.racersOnScoreboard;
                }

                $timeout(function() { //Forces Isotope to see any new racers and their data

                    $('.raceRunning').isotope('updateSortData').isotope();
                    $('.raceRunning').isotope('reloadItems');
                });
            }
        }
        else //If there is no race happening
        {
            if (Object.size($scope.racersFromPreviousRace) == 0) //If there are no previous races to show
            {
                $scope.scoreboardState = "no_races"; //Show nothing

                $interval.cancel($scope.paginationInterval);
                $scope.paginationInterval = null;
                $scope.currentPage = 0;
            }
            else //If there was a previous race to show
            {
                if ($scope.needToFetchFinalResults) //Fetch the final results if they haven't yet been fetched
                {
                    $scope.needToFetchFinalResults = false;
                    apiServices.getFinalResults($scope.mostRecentHeatID).success(function (data) {
                        var racers = {};
                        for(var key in data.scoreboard) //Package the racer data for the view to display
                        {
                            var currentRacer = data.scoreboard[key];
                            racers[currentRacer.racer_id] = currentRacer;
                        }

                        var mostLapsCompleted = 0;
                        //If the race is by position
                        if (data.race.win_by == "position")
                        {
                            //Determine the maximum number of laps
                            for (var currentRacer in racers)
                            {
                                if (racers.hasOwnProperty(currentRacer)) {
                                    //Determine the highest number of laps
                                    if (parseInt(racers[currentRacer].lap_num) > parseInt(mostLapsCompleted)) {
                                        mostLapsCompleted = racers[currentRacer].lap_num;
                                    }
                                }
                            }
                            for (var currentRacer in racers) //Format their gaps to be lap times if they're behind at least a lap
                            {
                                if (racers.hasOwnProperty(currentRacer))
                                {
                                    if (parseInt(racers[currentRacer].lap_num) < parseInt(mostLapsCompleted)) //If they're behind in laps
                                    {
                                        racers[currentRacer].gap = parseInt(racers[currentRacer].gap) + $scope.strings["str_lapAbbreviation"]; //Format their gap to be an actual lap number
                                    }
                                }
                            }
                        }
                        $scope.racersFromPreviousRace = JSON.parse(JSON.stringify(racers));
                        $scope.racers = $scope.racersFromPreviousRace;
                        $scope.scoreboardState = "last_results"; //Show the last results
                        $scope.showingFinalResults = true;

                        $timeout(function() {
                            $scope.showingFinalResults = false;
                        },$scope.finalResultsTime);

                        $timeout(function() { //Forces Isotope to see any new racers and their data

                            $('.raceRunning').isotope('updateSortData').isotope();
                            $('.raceRunning').isotope('reloadItems');
                        });

                    }).error(function (data, status, headers, config) {
                        //console.log('Error reaching Club Speed API - Scoreboard call');
                    });
                }
                else
                {
                    $scope.racers = $scope.racersFromPreviousRace;
                    $scope.scoreboardState = "last_results"; //Show the last results

                    // #########################################
                    // # PAGINATION DURING A LAST RESULTS PAGE #
                    // #########################################

                    var racers = $scope.racers;
                    var numOfRacers = Object.size(racers);
                    if (numOfRacers > $scope.racersPerPage)  //If we need to paginate
                    {
                        var sortedRacers = {};
                        for (var key in racers) //Sort racers by position
                        {
                            if (racers.hasOwnProperty(key)) {
                                sortedRacers[racers[key].position] = racers[key];
                            }
                        }

                        var i = 0;
                        var firstIndexToInclude = $scope.currentPage*$scope.racersPerPage;
                        var lastIndexToInclude = parseInt(firstIndexToInclude) + parseInt($scope.racersPerPage);
                        var filteredRacers = {};

                        for (var key in sortedRacers) //Filter out the racers that need to be paginated
                        {
                            if (sortedRacers.hasOwnProperty(key) && i >= firstIndexToInclude && i < lastIndexToInclude && i < numOfRacers) {
                                filteredRacers[i] = sortedRacers[key];
                            }
                            i++;
                        }

                        $scope.racers = filteredRacers;
                        if ($scope.paginationInterval == null)  //Start flipping through each page of racers on a timer
                        {
                            $scope.paginationInterval = $interval(function () {
                                var currentPage = $scope.currentPage;
                                currentPage++;
                                if (currentPage >= Object.size($scope.racersOnScoreboard)/$scope.racersPerPage)
                                {
                                    currentPage = 0;
                                }
                                $scope.currentPage = currentPage;
                            },$scope.timePerPageMs);
                        }
                    }
                    else {
                        $interval.cancel($scope.paginationInterval);
                        $scope.paginationInterval = null;
                        $scope.currentPage = 0;

                        $scope.racers = racers;
                    }

                    $timeout(function() { //Forces Isotope to see any new racers and their data

                        $('.raceRunning').isotope('updateSortData').isotope();
                        $('.raceRunning').isotope('reloadItems');
                    });
                }
            }
        }
    }

    //Based on the number of racers per page, which panels are visible, and current theme, formats the scoreboard as appropriate
    function adjustTemplateDynamically(data)
    {
        $scope.marginSize = 0.5;

        var numberOfRacers = Object.size($scope.racersOnScoreboard);

        if (numberOfRacers <= 5)
        {
            //"Big" template metrics
            $scope.racerBoxWidth = 99;

            if($scope.bigThemeHeaderEnabled)
            {
                $scope.racerBoxHeight = 19;
                $scope.racerBoxHeaderHeight = $scope.racerBoxHeight / 2;
                $scope.racerBoxHeight = $scope.racerBoxHeight - 11/5 - ($scope.racerBoxHeaderHeight+1)/5;
            }
            else
            {
                $scope.racerBoxHeight = 19;
            }

            $scope.racerBoxFontSize = 3;

            //"Classic" template metrics
            $scope.raceRunningClassicWidth = 100;
            $scope.raceRunningClassicHeight = 100;

            if($scope.classicThemeHeaderEnabled)
            {
                $scope.raceRunningClassicHeight -= 8;
            }
            if ($scope.classicThemeTopTimesEnabled)
            {
                $scope.raceRunningClassicHeight -= 15;
            }

            $scope.racerBoxClassicWidth = 99;
            if($scope.nextRacerTabEnabled && $scope.nextRaceDetails != null)
            {
                $scope.racerBoxClassicWidth -= 20;
                $scope.adjustLineHeights = false;
            }
            else
            {
                $scope.adjustLineHeights = false;
            }

            var availableSpaceForRacerBoxes = $scope.raceRunningClassicHeight - $scope.marginSize*(5)*2 - $scope.marginSize;
            $scope.racerBoxClassicHeight = availableSpaceForRacerBoxes / 5;
            $scope.racerBoxClassicFontSize = 11*($scope.racerBoxClassicHeight/20);
            if($scope.nextRacerTabEnabled && $scope.nextRaceDetails != null)
            {
                $scope.racerBoxClassicFontSize *= 1;
            }
        }
        else if (numberOfRacers > 5)
        {
            //"Big" template metrics
            $scope.racerBoxWidth = 99;
            $scope.racerBoxHeight = 100/ (numberOfRacers <= $scope.racersPerPage ? numberOfRacers : $scope.racersPerPage) -1;
            $scope.racerBoxHeight = $scope.racerBoxHeight > 19 ? 19 : $scope.racerBoxHeight;
            if($scope.bigThemeHeaderEnabled)
            {
                $scope.racerBoxHeaderHeight = $scope.racerBoxHeight / 2;
                //$scope.racerBoxHeight = $scope.racerBoxHeight - $scope.racerBoxHeight / ((numberOfRacers <= $scope.racersPerPage ? numberOfRacers : $scope.racersPerPage)-1); //Minus a half-height box
                //$scope.racerBoxHeight = $scope.racerBoxHeight > 19-19/5 ? 19-19/5 : $scope.racerBoxHeight;

                $scope.racerBoxHeight = $scope.racerBoxHeight - 11/(numberOfRacers <= $scope.racersPerPage ? numberOfRacers : $scope.racersPerPage)
                - ($scope.racerBoxHeaderHeight+1)/(numberOfRacers <= $scope.racersPerPage ? numberOfRacers : $scope.racersPerPage);
            }
            $scope.racerBoxFontSize = 3*($scope.racerBoxHeight/19);

            //"Classic" template metrics
            $scope.raceRunningClassicWidth = 99;
            $scope.raceRunningClassicHeight = 100;
            if($scope.classicThemeHeaderEnabled)
            {
                $scope.raceRunningClassicHeight -= 8;
            }
            if ($scope.classicThemeTopTimesEnabled)
            {
                $scope.raceRunningClassicHeight -= 15;
            }

            var numOfRacersPerColumn = Math.ceil(numberOfRacers/2.0);
            numOfRacersPerColumn = numOfRacersPerColumn > Math.ceil($scope.racersPerPage / 2.0) ? Math.ceil($scope.racersPerPage / 2.0) : numOfRacersPerColumn;
            numOfRacersPerColumn = numOfRacersPerColumn < 5 ? 5 : numOfRacersPerColumn;
            var availableSpaceForRacerBoxes = $scope.raceRunningClassicHeight - $scope.marginSize*(numOfRacersPerColumn)*2 - $scope.marginSize;

            if (numberOfRacers > 5 && $scope.racersPerPage > 5)
            {
                $scope.racerBoxClassicWidth = 49;
                if($scope.nextRacerTabEnabled && $scope.nextRaceDetails != null)
                {
                    $scope.racerBoxClassicWidth -= 10;
                }
            }
            else
            {
                $scope.racerBoxClassicWidth = 99;
                if($scope.nextRacerTabEnabled && $scope.nextRaceDetails != null)
                {
                    $scope.racerBoxClassicWidth -= 20;
                }
            }

            $scope.racerBoxClassicHeight = availableSpaceForRacerBoxes / numOfRacersPerColumn;


            $scope.racerBoxClassicFontSize = 10*($scope.racerBoxClassicHeight/20);
            if($scope.nextRacerTabEnabled && $scope.nextRaceDetails != null)
            {
                $scope.racerBoxClassicFontSize *= 0.8;
                $scope.adjustLineHeights = true;
            }
            else
            {
                $scope.adjustLineHeights = false;
            }
        }

        if ($scope.nextRaceDetails != null)
        {
            $scope.nextRacerReady = true;
        }
    }

    // ##################
    // # DEMO MODE DATA #
    // ##################

    if ($scope.demoMode)
    {
        $scope.startingScoreboard = {
            "race": {
                "id": "19740",
                "track_id": "1",
                "track": "Track 1",
                "starts_at": "2015-01-23 18:30:00",
                "finish_time": "1969-12-31 19:00:00",
                "heat_type_id": "22",
                "heat_status_id": "1",
                "speed_level_id": "3",
                "speed_level": "Youth",
                "win_by": "laptime",
                "race_by": "laps",
                "duration": "10",
                "race_name": "Test Race",
                "race_time_in_seconds": 273.176
            },
            "scoreboard": [
                {
                    "position": "1",
                    "nickname": "Test Racer",
                    "average_lap_time": "72.363",
                    "fastest_lap_time": "40.073",
                    "last_lap_time": "40.073",
                    "rpm": "1200",
                    "first_name": "daniel",
                    "last_name": "ramkissoon",
                    "racer_id": "1051960",
                    "lap_num": "3",
                    "kart_num": "101",
                    "gap": ".000",
                    "ambtime": "2619425438"
                }
            ]
        };

        $scope.racerToAdd = {
            "position": "1",
            "nickname": "Test Racer",
            "average_lap_time": "72.363",
            "fastest_lap_time": "40.073",
            "last_lap_time": "40.073",
            "rpm": "1200",
            "first_name": "daniel",
            "last_name": "ramkissoon",
            "racer_id": "1051960",
            "lap_num": "3",
            "kart_num": "101",
            "gap": ".000",
            "ambtime": "2619425438"
        };

        function generateAndUpdateDemoData()
        {
            if ($scope.startingScoreboard.scoreboard.length < 20)
            {
                $scope.racerToAdd.position = (parseInt($scope.racerToAdd.position) + 1).toString();
                $scope.racerToAdd.nickname = "Test Racer " + $scope.racerToAdd.position;
                $scope.racerToAdd.racer_id = (parseInt($scope.racerToAdd.racer_id) + 1).toString();
                $scope.racerToAdd.kart_num = (parseInt($scope.racerToAdd.kart_num) + 1).toString();
                $scope.racerToAdd.fastest_lap_time = (parseFloat($scope.racerToAdd.fastest_lap_time) + 10.01).toFixed(3).toString();
                $scope.racerToAdd.last_lap_time = (parseFloat($scope.racerToAdd.last_lap_time) + 10.01).toFixed(3).toString();

                $scope.racerToAdd.gap = (parseFloat($scope.racerToAdd.gap) + 10.01).toFixed(3).toString();

                var racerToAdd = JSON.parse(JSON.stringify($scope.racerToAdd));
                $scope.startingScoreboard.scoreboard.push(racerToAdd);
            }

            $timeout(function() { //Forces Isotope to see any new racers and their data

                $('.raceRunning').isotope('updateSortData').isotope();
                $('.raceRunning').isotope('reloadItems');
            });

            return $scope.startingScoreboard;
        }
    }

    // #####################
    // # UTILITY FUNCTIONS #
    // #####################

    //Merges two JavaScript objects
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

    //Determines the size of a JavaScript object
    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };

});