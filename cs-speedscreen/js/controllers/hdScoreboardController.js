/** hdScoreboardController
 *
 * NOTE: THIS VERSION OF THE HD SCOREBOARD IS NOW DEPRECATED AND PENDING DELETION.
 * See "newHDScoreboardController.js" instead.
 *
 * This angular controller handles the model logic corresponding to the HD Scoreboard slide (hdscoreboard.html).
 *
 * It has two primary modes: "simulator" and "live". If it's in simulation mode, it simulates a race using liveScoreboardSimulator.js
 * and displays the simulated race on the screen. If it's in live mode, it periodically polls the track (defaults to 1) for
 * its current scoreboard and future racer lineup. If there are no races running, nothing is shown.
 * If racers are coming up, they are shown on the main screen.
 * If a race is running, and no racers are coming up, that race's scoreboard is shown full screen.
 * If a race is running AND racers are coming up, the scoreboard is automatically minimized to make room for a listing
 * of the next race's racers.
 *
 */
speedScreenDemoApp.controller('hdScoreboardController', function($scope, $routeParams, speedScreenServices,
                                                                 SocketIOService, $timeout, $interval) {

    // ###################
    // # OPERATING MODES #
    // ###################

    var mode = "live";
    //var mode = "simulation";

    var usingNewAPI = false;
    //var usingNewAPI = true;

    $scope.mode = mode;
    $scope.currentTrack = 1; //The track to pull data from
    $scope.slidePanelVisible = false; //Used by the view to configure itself based on the visibility of the Coming Next side panel

    // ## EXPERIMENTAL - FUTURE FEATURE
    //Request live track data from the venue
    speedScreenServices.getTracks().success(function (data) {
        $scope.tracks = {};
        for(var key in data.tracks) //For every track at the venue
        {
            $scope.tracks[ data.tracks[key].id ] = data.tracks[key].name; //Package the data nicely
        }
        //$scope.currentTrackName = $scope.tracks[$scope.currentTrack];
        //New data format: {"1":"Rental","2":"Oval","3":"Race"}
    });
    // ## END EXPERIMENTAL - FUTURE FEATURE


    // #####################
    // # BOTTOM STATUS BAR #
    // #####################

    var validStatusBarModes = ["Today","Week","Month"]; //Array of valid "Top Times" time periods
    var currentStatusBarMode = 0; //Index of first top time to display

    $scope.statusBarMode = validStatusBarModes[currentStatusBarMode]; //The current top time mode

    //Initially populate the view with today's top times
    speedScreenServices.getFastestLapTimes_Day(5,1).success(function (data) {
        $scope.fastestTimesThisWeek = data.fastest;
        $scope.fastestTimeLabel = "Today";
    });

    // #################
    // # TIMING STATES #
    // #################

    var minimumFinalResultsDisplayTimeMs = 60000; //How long to show the Final Results screen before switching to the Next Racers lineup
    var timeFinalResultsScreenStarted = 0; //The time the Final Results
    $scope.finalResultScreenTimeMs = 0;


    //Then, on a timer, alternate between all the top times
    $interval(function()
        {
            currentStatusBarMode++; //Move on to the next top time
            if (currentStatusBarMode == validStatusBarModes.length) //Wrap around if necessary
            {
                currentStatusBarMode = 0;
            }

            $scope.statusBarMode = validStatusBarModes[currentStatusBarMode];

            if (validStatusBarModes[currentStatusBarMode] == "Week") //Top times of the week
            {
                speedScreenServices.getFastestLapTimes_Week(5,1).success(function (data) {
                    $scope.fastestTimesThisWeek = data.fastest;
                    $scope.fastestTimeLabel = "This Week";
                });

            }
            else if (validStatusBarModes[currentStatusBarMode] == "Month") //Top times of the month
            {
                speedScreenServices.getFastestLapTimes_Month(5,1).success(function (data) {
                    $scope.fastestTimesThisWeek = data.fastest;
                    $scope.fastestTimeLabel = "This Month";
                });

            }
            else if (validStatusBarModes[currentStatusBarMode] == "Today") //Top times today
            {
                speedScreenServices.getFastestLapTimes_Day(5,1).success(function (data) {
                    $scope.fastestTimesThisWeek = data.fastest;
                    $scope.fastestTimeLabel = "Today";
                });

            }
        }
    ,15000);

    // ################
    // ## KEY MODELS ##
    // ################

    var racers = {}; //The racers on the current scoreboard, including race data, used by the view to display.
    var nextRacers = {}; //The next racers in the heat that is coming up. Used by the view to display.

    var oldRacers = {}; //The previous state of the racers variable. Used to determine the nature of changes between laps.

    var lastRaceScoreboard = null; //A copy of the most recent scoreboard state
    var lastRaceRacers = null; //A copy of the most recent racer state
    var lastNextRacers = null; //A copy of the most recent snapshot of the next racers coming up

    $scope.lastRaceScoreboard = lastRaceScoreboard;
    $scope.lastRaceRacers = lastRaceRacers;
    $scope.lastNextRacers = lastNextRacers;
    $scope.lastRaceID = -1;
    $scope.lastNextRaceID = -1;

    $scope.racers = racers;
    $scope.nextRacers = nextRacers;


    // ##########################
    // # Isotope initialization #
    // ##########################

    //HACK: Timeout with unspecified time is needed here to ensure that Isotope sees the change made by Angular
    $timeout((function(){

        var $container = $('#container'); //Main div containing the elements to be arranged

        $container.isotope({
            itemSelector : '.racerBox', //Each discrete element to be arranged
            layoutMode: 'masonryHorizontal',
            resizesContainer : false, //Needed to prevent inexplicable behavior
            masonryHorizontal: {
                rowHeight: 160
            },
            getSortData : {
                position : function ( $elem ) {
                    return parseInt( $elem.find('.position').text(), 10 ); //Sorts by racer position
                }
            },
            sortBy : 'position',
            animationEngine: 'css' //TODO: Investigate pros and cons vs jquery
        });

    }));


    if (mode == "simulation") //Start the simulation object if a simulation is desired
    {
        var liveScoreboard = new LiveScoreboardSimulator();
        var currentRacers = liveScoreboard.getRacers();
        setInterval(function(){liveScoreboard.generateNextLaps()},minLapTime_Seconds/2);
        setInterval(function(){liveScoreboard.processNextLaps()},frequencyOfScoreboardUpdates_Milliseconds/4);
    }

    $interval(function() //Once per second, instruct Isotope to check for any changes to its elements
        {
            //####################
            //# SIMULATION LOGIC #
            //####################
            if (mode == "simulation") //And if we're running a simulation, update the $scope variables used by the view
            {
                oldRacers = JSON.parse(JSON.stringify(racers)); //Copy of racers

                liveScoreboard.updateRacers(racers);
                liveScoreboard.updateRacers(nextRacers);

                var fastestTime = 99999;
                var fastestRacer;
                for (var currentRacer in racers)
                {
                    if (racers.hasOwnProperty(currentRacer))
                    {
                        racers[currentRacer].isFastest = false;
                        if (parseFloat(racers[currentRacer].fastest_lap_time) < parseFloat(fastestTime))
                        {
                            fastestTime = racers[currentRacer].fastest_lap_time;
                            fastestRacer = currentRacer;
                        }

                        racers[currentRacer].positionStatus = 'neutral'; //Default
                        if (racers[currentRacer].lap_num == 0)
                        {
                            racers[currentRacer].positionStatus = 'neutral';
                        }
                        if (currentRacer in oldRacers && racers[currentRacer].position < oldRacers[currentRacer].position)
                        {
                            racers[currentRacer].positionStatus = 'wentUp';
                        }
                        else if (currentRacer in oldRacers && racers[currentRacer].position > oldRacers[currentRacer].position)
                        {
                            racers[currentRacer].positionStatus = 'wentDown';
                        }
                        else if (currentRacer in oldRacers)
                        {
                            racers[currentRacer].positionStatus = oldRacers[currentRacer].positionStatus;
                        }

                        racers[currentRacer].racerLapNumberIncreased = false;
                        if (currentRacer in oldRacers && racers[currentRacer].lap_num > oldRacers[currentRacer].lap_num)
                        {
                            racers[currentRacer].racerLapNumberIncreased = true; //If a racer's lap number has changed, mark them as just having completed a lap
                            if (racers[currentRacer].position == oldRacers[currentRacer].position)
                            {
                                racers[currentRacer].positionStatus = 'neutral';
                                oldRacers[currentRacer].positionStatus = 'neutral';
                            }
                        }

                        racers[currentRacer].beatBestLap = false;
                        if (currentRacer in oldRacers && racers[currentRacer].fastest_lap_time < oldRacers[currentRacer].fastest_lap_time)
                        {
                            racers[currentRacer].beatBestLap = true; //If a racer's lap number has changed, mark them as just having completed a lap
                        }
                    }
                }
                racers[fastestRacer].isFastest = true;

            }
            $timeout(function() //HACK: Two back-to-back resets are needed for now. Isotope author is working on this issue.
            {

                $('#container').isotope( 'reloadItems' );
                $('#container').isotope( 'reLayout' );
                $('#container').isotope({ sortBy : 'position' });

                $('#container').isotope( 'reloadItems' );
                $('#container').isotope( 'reLayout' );
                $('#container').isotope({ sortBy : 'position' });
            });
        }
        , 1000);

    if (mode == "live") //If we're working with live data from a track
    {
        $scope.scoreboardState = "no_races"; //Assume there is no race running for now
    }
    else
    {
        $scope.scoreboardState = "race_simulation"; //Otherwise, let the view know we're running a simulation
        //$scope.scoreboardState = "driver_lineup"; //Otherwise, let the view know we're running a simulation
    }

    $scope.currentScoreboard = {}; //The actual JSON data received from the track for the current scoreboard
    $scope.nextRace = {}; //The actual JSON data received from the track for the next heat lineup

    if (mode == "live") //If we're working with live data from a track
    {
        pollForScoreboard(); //Poll the track for the latest live data

        //Experimental SocketIO stuff. Ignore for now.
        $scope.socketIO = "no data yet";
        /*SocketIOService.on('scoreboard', function (data) {
            console.log("Receiving data from socket.io");
            console.log(JSON.stringify(data));
            $scope.socketIO = data;
        });*/
    }

    /* ## EXPERIMENTAL GRAPH DATA
    if ($scope.scoreboardState != "race_results_graph")// && lastRaceScoreboard.race.id !== undefined)
    {
        speedScreenServices.getRaceDetails('44717').success(function (data) {//lastRaceScoreboard.race.id).success(function (data) {
            if (data.race.laps !== undefined) //If lap data was recorded, produce Google graphs
            {
                $scope.chartData = convertRaceDetailsToGoogleChartFormat(data);
                google.setOnLoadCallback(drawChart($scope.chartData,'chart_div'));
            }
        });
    }*/


    // #########################
    // # LIVE SCOREBOARD LOGIC #
    // #########################

    function pollForScoreboard()
    {
        console.log("finalResultScreenTimeMs = " + $scope.finalResultScreenTimeMs);
        //Request live scoreboard data from the track
        speedScreenServices.getScoreboardData().success(function (data) {
            $scope.currentScoreboard = data;
        });

        //Request live next heat data from the track
        if (usingNewAPI)
        {
            speedScreenServices.getNextHeatTest().success(function (data) {
                $scope.nextRace = data;
            });
        }
        else
        {
            speedScreenServices.getNextHeat().success(function (data) {
                $scope.nextRace = data;
            });
        }





        //If the data is not available, try polling again later
        if (jQuery.isEmptyObject($scope.currentScoreboard) || jQuery.isEmptyObject($scope.nextRace))
        {
            $timeout(pollForScoreboard,1000);
            return; //TODO: This seems hacky and error-prone. Repair. Try wrapping everything in a success. Handle failure.
            //TODO: Understand jQuery.isEmptyObject. There's nothing wrong with an empty nextRace... or is there?
        }

        if ($scope.nextRace.hasOwnProperty("error") === false)
        {
            lastNextRacers = JSON.parse(JSON.stringify($scope.nextRace)); //Copy of the last Next Racers lineup seen.
        }
        console.log(lastNextRacers);

        if ($scope.currentScoreboard.hasOwnProperty("race"))
        {
            var minutes = parseInt(($scope.currentScoreboard.race.duration*60 - $scope.currentScoreboard.race.race_time_in_seconds)/60);
            var seconds = parseInt(($scope.currentScoreboard.race.duration*60 - $scope.currentScoreboard.race.race_time_in_seconds)%60);
            //console.log($scope.currentScoreboard.race.duration*60);
            ///console.log($scope.currentScoreboard.race.race_time_in_seconds);
            ///console.log("Minutes: " + minutes);
            ///console.log("Seconds: " + seconds);
            //console.log($scope.currentScoreboard.race.race_time_in_seconds - 70382.397);
            //console.log($scope.currentScoreboard.race.race_time_in_seconds/60000); //70790.494
            //console.log($scope.currentScoreboard.race.race_time_in_seconds/60);
            //console.log($scope.currentScoreboard.race.race_time_in_seconds/60/20);
            //var minutes = parseInt($scope.currentScoreboard.race.race_time_in_seconds/-1000/60);
            //var seconds = parseInt($scope.currentScoreboard.race.race_time_in_seconds/-1000%60);
            if (seconds < 0)
            {
                seconds = 0;
            }

            $scope.currentTime = (minutes < 10 ? "0" + minutes : minutes) + ":" +
                (seconds  < 10 ? "0" + seconds : seconds);
            if ($scope.currentTime == "00:00")
            {
                $scope.currentTime = "";
            }
            //$scope.currentTime = $scope.currentScoreboard.race.race_time_in_seconds/-1000;
        }

        if ($scope.scoreboardState != "race_results")
        {
            $scope.finalResultScreenTimeMs = 0;
        }

        if ($scope.currentScoreboard.hasOwnProperty("error")) //If there is no race currently running
        {
            if ($scope.nextRace.hasOwnProperty("error")) //And there is no race coming up
            {
                if (lastRaceScoreboard === null) //If we've never seen a race
                {
                    $scope.scoreboardState = "no_races"; //Let the view know to be in "No Races" mode

                    if ($('.comingUpNextBox').hasClass('visibleComingUpNextBox')) //Hide the driver lineup box
                    {
                        $scope.slidePanelVisible = false;

                        $timeout(function(){
                            $(".comingUpNextBox").toggleClass('hiddenComingUpNextBox visibleComingUpNextBox');

                            $('#container').isotope( 'reloadItems' );
                            $('#container').isotope( 'reLayout' );
                            $('#container').isotope({ sortBy : 'position' });
                        });
                    }
                }
                else //If there is no race running, but we remember the state of the last one
                {
                    if ($scope.scoreboardState != "race_results") //If we weren't showing the race results yet
                    {
                        timeFinalResultsScreenStarted = new Date(); //Remember the time we started to show them
                        $scope.finalResultScreenTimeMs = 0;
                    }
                    else //If we were already showing the race results
                    {
                        $scope.finalResultScreenTimeMs = new Date() - timeFinalResultsScreenStarted; //Update how long it's been showing
                    }
                    $scope.scoreboardState = "race_results"; //Let the view know to be in "Race Results" mode

                    if ($('.comingUpNextBox').hasClass('visibleComingUpNextBox')) //Hide the driver lineup box
                    {
                        $scope.slidePanelVisible = false;

                        $timeout(function(){
                            $(".comingUpNextBox").toggleClass('hiddenComingUpNextBox visibleComingUpNextBox');

                            $('#container').isotope( 'reloadItems' );
                            $('#container').isotope( 'reLayout' );
                            $('#container').isotope({ sortBy : 'position' });
                        });
                    }
                }

            }
            else //If there is a race coming up, and no race running now
            {
                if (lastRaceScoreboard == null || $scope.finalResultScreenTimeMs > minimumFinalResultsDisplayTimeMs) //If we don't have a previous race to display or we have shown it long enough
                {
                    lastRaceScoreboard = null; //Forget the last scoreboard
                    $scope.finalResultScreenTimeMs = 0;

                    $scope.scoreboardState = "driver_lineup";  //Switch the view to Driver Lineup mode

                    if ($('.comingUpNextBox').hasClass('visibleComingUpNextBox')) //Hide the driver lineup box
                    {
                        $scope.slidePanelVisible = false;
                        $timeout(function(){
                            $(".comingUpNextBox").toggleClass('hiddenComingUpNextBox visibleComingUpNextBox');


                            $('#container').isotope( 'reloadItems' );
                            $('#container').isotope( 'reLayout' );
                            $('#container').isotope({ sortBy : 'position' });
                        });
                    }
                }
                else //If we do have a previous race to display
                {
                    if ($scope.scoreboardState != "race_results") //If we weren't showing the race results yet
                    {
                        timeFinalResultsScreenStarted = new Date(); //Remember the time we started to show them
                        $scope.finalResultScreenTimeMs = 0;
                    }
                    else //If we were already showing the race results
                    {
                        $scope.finalResultScreenTimeMs = new Date() - timeFinalResultsScreenStarted; //Update how long it's been showing
                    }
                    $scope.scoreboardState = "race_results"; //Let the view know to be in "Race Results" mode

                    if ($('.comingUpNextBox').hasClass('hiddenComingUpNextBox')) //Show the driver lineup box
                    {
                        $scope.slidePanelVisible = true;
                        $timeout(function(){
                            $(".comingUpNextBox").toggleClass('hiddenComingUpNextBox visibleComingUpNextBox');

                            $('#container').isotope( 'reloadItems' );
                            $('#container').isotope( 'reLayout' );
                            $('#container').isotope({ sortBy : 'position' });
                        });

                    }
                }


                nextRacers = {};
                for(var key in $scope.nextRace.race.racers) //For every racer in the heat coming up
                {
                    var currentRacer = $scope.nextRace.race.racers[key];
                    nextRacers[currentRacer.id] = currentRacer; //Package them nicely into an array
                }
                $scope.nextRacers = nextRacers; //And send that array to the view for rendering
            }
        }
        else //If there is a race currently running
        {
            $scope.scoreboardState = "race_ongoing";

            oldRacers = JSON.parse(JSON.stringify(racers)); //Make a copy of the current racer state
            racers = {};

            for(var key in $scope.currentScoreboard.scoreboard) //Package the racer data for the view to display
            {
                var currentRacer = $scope.currentScoreboard.scoreboard[key];
                racers[currentRacer.racer_id] = currentRacer;
            }

            $scope.racers = racers;
            lastRaceRacers = JSON.parse(JSON.stringify(racers)); //Copy of racers
            lastRaceScoreboard = JSON.parse(JSON.stringify($scope.currentScoreboard)); //Copy of scoreboard
            $scope.lastRaceRacers = lastRaceRacers;
            $scope.lastRaceScoreboard = lastRaceScoreboard;


            //$scope.lastRaceID = $scope.currentScoreboard.race.id;
            //console.log($scope.lastRaceID + " is the last race that has run or is running.");
            //$scope.lastNextRaceID = $scope.nextRace.race.id;
            //console.log($scope.lastNextRaceID + " is the last race that we've seen in the Next Racers section.");

            //TODO: If there are no racers on the scoreboard yet, switch to a racer lineup view if appropriate
            //TODO: Assuming this works okay, I need to make sure I don't show next racers for any heat that has passed... what a mess.
            if (racers.size === 0 && (!isEmpty($scope.nextRacers) || !isEmpty($scope.lastNextRacers)))
            {
                if (isEmpty($scope.nextRacers))
                {
                    $scope.nextRacers = $scope.lastNextRacers;
                }

                $scope.scoreboardState = "driver_lineup";
            }
            else
            {
                if ($scope.nextRace.hasOwnProperty("error") === false) //If there are racers coming up
                {

                    if ($('.comingUpNextBox').hasClass('hiddenComingUpNextBox')) //Show the driver lineup box
                    {
                        $scope.slidePanelVisible = true;
                        $timeout(function(){
                            $(".comingUpNextBox").toggleClass('hiddenComingUpNextBox visibleComingUpNextBox');

                            $('#container').isotope( 'reloadItems' );
                            $('#container').isotope( 'reLayout' );
                            $('#container').isotope({ sortBy : 'position' });
                        });

                    }
                    nextRacers = {};
                    for(var key in $scope.nextRace.race.racers)
                    {
                        var currentRacer = $scope.nextRace.race.racers[key];
                        nextRacers[currentRacer.id] = currentRacer;
                    }
                    $scope.nextRacers = nextRacers;
                }
                else //If there are no races coming up
                {
                    if ($('.comingUpNextBox').hasClass('visibleComingUpNextBox')) //Hide the driver lineup box
                    {
                        $scope.slidePanelVisible = false;
                        $timeout(function(){
                            $(".comingUpNextBox").toggleClass('hiddenComingUpNextBox visibleComingUpNextBox');

                            $('#container').isotope( 'reloadItems' );
                            $('#container').isotope( 'reLayout' );
                            $('#container').isotope({ sortBy : 'position' });
                        });
                    }
                    nextRacers = {};
                    $scope.nextRacers = nextRacers;
                }
            }

        }

        // ################################
        // # LIVE SCOREBOARD CHANGE LOGIC #
        // ################################

        var fastestTime = 99999;
        var mostLaps = -1;
        var fastestRacer;
        for (var currentRacer in racers) //For every racer
        {
            if (racers.hasOwnProperty(currentRacer))
            {
                //Experimental


                //racers[currentRacer].fastest_lap_time = parseInt(racers[currentRacer].fastest_lap_time);

                if (racers[currentRacer].lap_num > mostLaps)
                {
                    mostLaps = racers[currentRacer].lap_num;
                }

                //End Experimental

                racers[currentRacer].isFastest = false;
                if (racers[currentRacer].fastest_lap_time < fastestTime) //Determine the fastest lap time
                {
                    fastestTime = racers[currentRacer].fastest_lap_time;
                    fastestRacer = currentRacer;
                }

                racers[currentRacer].positionStatus = 'neutral'; //Default state - didn't go up or down
                if (racers[currentRacer].lap_num == 0)
                {
                    racers[currentRacer].positionStatus = 'neutral'; //If it's the first practice lap, note no change in state
                }
                if (currentRacer in oldRacers && racers[currentRacer].position < oldRacers[currentRacer].position)
                {
                    racers[currentRacer].positionStatus = 'wentUp'; //If a racer went up in position, make a note of it
                }
                else if (currentRacer in oldRacers && racers[currentRacer].position > oldRacers[currentRacer].position)
                {
                    racers[currentRacer].positionStatus = 'wentDown'; //If a racer went down in position, make a note of it
                }
                else if (currentRacer in oldRacers)
                {
                    racers[currentRacer].positionStatus = oldRacers[currentRacer].positionStatus; //Remember their previous state
                }

                racers[currentRacer].racerLapNumberIncreased = false;
                if (currentRacer in oldRacers && racers[currentRacer].lap_num > oldRacers[currentRacer].lap_num)
                {
                    racers[currentRacer].racerLapNumberIncreased = true; //If a racer's lap number has changed, mark them as just having completed a lap
                    if (racers[currentRacer].position == oldRacers[currentRacer].position)
                    {
                        racers[currentRacer].positionStatus = 'neutral';
                        oldRacers[currentRacer].positionStatus = 'neutral';
                    }
                }

                racers[currentRacer].beatBestLap = false;
                if (currentRacer in oldRacers && racers[currentRacer].fastest_lap_time < oldRacers[currentRacer].fastest_lap_time)
                {
                    racers[currentRacer].beatBestLap = true; //If a racer's lap number has changed, mark them as just having completed a lap
                }
            }
        }
        if (fastestRacer in racers)
        {
            racers[fastestRacer].isFastest = true; //Make a note of the fastest racer
        }
        if ($scope.currentScoreboard.hasOwnProperty("race") && $scope.currentScoreboard.race.hasOwnProperty("race_by") && $scope.currentScoreboard.race.race_by == "position")
        {
            for (var currentRacer in racers) //For every racer
            {
                if (racers.hasOwnProperty(currentRacer) && racers[currentRacer].lap_num < mostLaps)
                {
                    racers[currentRacer].gap = parseInt(racers[currentRacer].gap);
                }
            }
        }



        $timeout(pollForScoreboard,1000);

    }


    /**
     *  This function is used during simulation mode to test the slide-out next racer panel.
     *  If properly functional, making the panel appear and re-appear should cause the scoreboard
     *  portion of the screen to automatically resize. This is accomplished by the scoreboard object
     *  watching the "slidePanelVisible" variable to determine which styling classes to have.
     */
    $(document).ready(function(){
        $("#clickMe").click(function() {

            console.log("Side panel toggled");
            if ($('.comingUpNextBox').hasClass('hiddenComingUpNextBox')) //Show the driver lineup box
            {
                $scope.slidePanelVisible = true;
                $timeout(function(){
                    $(".comingUpNextBox").toggleClass('hiddenComingUpNextBox visibleComingUpNextBox');

                    $('#container').isotope( 'reloadItems' );
                    $('#container').isotope( 'reLayout' );
                    $('#container').isotope({ sortBy : 'position' });
                });

            }
            else
            {
                $scope.slidePanelVisible = false;
                $timeout(function(){
                    $(".comingUpNextBox").toggleClass('hiddenComingUpNextBox visibleComingUpNextBox');

                    $('#container').isotope( 'reloadItems' );
                    $('#container').isotope( 'reLayout' );
                    $('#container').isotope({ sortBy : 'position' });
                });
            }

        });
    });
});

/**
 * This custom filter allows objects to be sorted by the specified field.
 * This functionality isn't built into AngularJS. Go figure!
 */
speedScreenDemoApp.filter('orderObjectBy', function() {
    return function(items, field, reverse) {
        var filtered = [];
        angular.forEach(items, function(item) {
            filtered.push(item);
        });
        filtered.sort(function (a, b) {
            return (parseInt(a[field]) > parseInt(b[field]));
        });
        if(reverse) filtered.reverse();
        return filtered;
    };
});

//TODO: Evaluate this
function isEmpty(object) { for(var i in object) { return true; } return false; };

Object.size = function(obj) {
    var size = 0, key;
    for (key in obj) {
        if (obj.hasOwnProperty(key)) size++;
    }
    return size;
};

