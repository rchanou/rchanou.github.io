/* This angular controller handles the model logic corresponding to the HD Scoreboard slide (hdscoreboard.html).
*
* If it's in live mode, it periodically polls the track (defaults to 1) for
* its current scoreboard and future racer lineup. If there are no races running, nothing is shown, unless we have
* previous results to display.
* If racers are coming up, they are shown on the main screen.
* If a race is running, and no racers are coming up, that race's scoreboard is shown full screen.
* If a race is running AND racers are coming up, the scoreboard is automatically minimized to make room for a listing
* of the next race's racers. */

//TODO: Add a simulation mode.
//TODO: Re-enable "last_few_heats" feature when development time is available for it.
//TODO: Timers are off sometimes. API side.

 speedScreenDemoApp.controller('newHDScoreboardController',
    function($scope, $routeParams, speedScreenServices, SocketIOService, $timeout, $interval, globalVars)
    {
        //################################
        //# INITIALIZATION AND EXECUTION #
        //################################
        if (globalVars.getStop() !== undefined) //If the scoreboard requested that its timeouts and intervals be cleaned-up
        {
            var stop = globalVars.getStop(); //Stop the previous scoreboard's timeouts and intervals
            stop();
            globalVars.resetStop();
        }

        var HDScoreboard = new HDScoreboardModel(globalVars.getCurrentTrack());
        var intervalCalls = HDScoreboard.start();
        globalVars.setFirstTimeScoreboardLoaded(false);
        var disableNextRacers = defaultFor(config.disableNextRacers, true);
        var disableNextRacersTab = defaultFor(config.disableNextRacersTab, false);


        // ##################################
        // # HD SCOREBOARD MODEL DEFINITION #
        // ##################################

        function HDScoreboardModel(track_id)
        {
            //#######################
            //# KEY STATE VARIABLES #
            //#######################

            this.currentTrack = defaultFor(track_id,1);
            this.currentScoreboard = {};
            this.currentNextRace = {};
            this.lastHeatID = -1; //
            this.lastHeatsHistory = [];
            this.lastHeatsLimit = 1; //TODO: Experimental change. Used to maintain more history.
            this.nextRacersHistory = [];
            this.scoreboardState = "no_races"; //"no_races", "driver_lineup", "race_ongoing", "final_results", "last_few_heats"
            this.finalResultsMaxTimeMs = 60000; //TODO: Final results min time? MOVE TO CONFIG?
            this.finalResultsScreenTimeMs = 0;
            this.finalResultsTimeStartedMs = 0;
            this.slidePanelVisible = false;

            //TODO: Change these to member variables
            var validStatusBarModes = ["Today","Week","Month"]; //Array of valid "Top Times" time periods
            var currentStatusBarMode = 0; //Index of first top time to display

            // ################
            // ## KEY MODELS ##
            // ################

            this.racers = {}; //The racers on the current scoreboard, including race data, used by the view to display.
            this.nextRacers = {}; //The next racers in the heat that is coming up. Used by the view to display.

            this.oldRacers = {}; //The previous state of the racers variable. Used to determine the nature of changes between laps.
            this.lastRaceRacers = {};

            /** start()
             * This function initializes the HD Scoreboard. Specifically:
             *  - Once per second:
             *      - The latest scoreboard data is fetched from the track.
             *      - The latest scoreboard data is processed.
             *      - Isotope is instructed to refresh itself.
             *  - Once every fifteen seconds:
             *      - The latest top times is fetched and refreshed, rotating through "Today", "This Week", and "This Month"
             *  - Immediately:
             *      - The Isotope container is initialized.
             * @returns {Array} A list of all intervals and timeouts that need to be cleaned up when this slide is done.
             */
            HDScoreboardModel.prototype.start = function()
            {
                var intervalsToReturn = []; //Interval function calls that must be stopped prior to leaving screen

                //Periodically, update the scoreboard and refresh the Isotope view
                intervalsToReturn.push($interval( function()
                {
                    //console.log("MAIN HD SCOREBOARD LOOP");
                    HDScoreboard.getLatestScoreboardData();
                    HDScoreboard.processLatestScoreboardData();

                    $('#container').isotope( 'reloadItems' );
                    $('#container').isotope( 'reLayout' );
                    $('#container').isotope({ sortBy : 'position' });

                    $('#container').isotope( 'reloadItems' );
                    $('#container').isotope( 'reLayout' );
                    $('#container').isotope({ sortBy : 'position' });

                },250));

                intervalsToReturn.push($interval( function()
                {
                    HDScoreboard.getNextRacerDataAndFinalResults();
                },1000));

                // #####################
                // # BOTTOM STATUS BAR #
                // #####################

                $scope.statusBarMode = validStatusBarModes[currentStatusBarMode]; //The current top time mode

                //Initially populate the view with today's top times
                speedScreenServices.getFastestLapTimes_Day(4,globalVars.getCurrentTrack()).success(function (data) {
                    $scope.fastestTimesThisWeek = data.fastest;
                    $scope.fastestTimeLabel = "Today";
                });

                //Then, on a timer, alternate between all the top times
                intervalsToReturn.push($interval(function()
                    {
                        currentStatusBarMode++; //Move on to the next top time
                        if (currentStatusBarMode == validStatusBarModes.length) //Wrap around if necessary
                        {
                            currentStatusBarMode = 0;
                        }

                        $scope.statusBarMode = validStatusBarModes[currentStatusBarMode];

                        if (validStatusBarModes[currentStatusBarMode] == "Week") //Top times of the week
                        {
                            speedScreenServices.getFastestLapTimes_Week(4,globalVars.getCurrentTrack()).success(function (data) {
                                $scope.fastestTimesThisWeek = data.fastest;
                                $scope.fastestTimeLabel = "This Week";
                            });

                        }
                        else if (validStatusBarModes[currentStatusBarMode] == "Month") //Top times of the month
                        {
                            speedScreenServices.getFastestLapTimes_Month(4,globalVars.getCurrentTrack()).success(function (data) {
                                $scope.fastestTimesThisWeek = data.fastest;
                                $scope.fastestTimeLabel = "This Month";
                            });

                        }
                        else if (validStatusBarModes[currentStatusBarMode] == "Today") //Top times today
                        {
                            speedScreenServices.getFastestLapTimes_Day(4,globalVars.getCurrentTrack()).success(function (data) {
                                $scope.fastestTimesThisWeek = data.fastest;
                                $scope.fastestTimeLabel = "Today";
                            });

                        }
                    }
                    ,15000));


                //HACK: Timeout with unspecified time is needed here to ensure that Isotope sees the change made by Angular
                $timeout((function(){

                    var $container = $('#container'); //Main div containing the elements to be arranged

                    $container.isotope({
                        itemSelector : '.racerBox', //Each discrete element to be arranged
                        layoutMode: 'masonryHorizontal',
                        resizesContainer : false, //Needed to prevent inexplicable behavior
                        masonryHorizontal: {
                            //rowHeight: 160
                        },
                        getSortData : {
                            position : function ( $elem ) {
                                return parseInt( $elem.find('.position').text(), 10 ); //Sorts by racer position
                            }
                        },
                        sortBy : 'position',
                        animationEngine: 'css'
                    });

                }));

                return intervalsToReturn;
            };

            /** stop()
             *
             * This function is called when all timeouts and intervals need to be cleaned up, often prior to changing
             * to a different slide. It simply cancels all intervals and timeouts.
             */
            HDScoreboardModel.prototype.stop = function()
            {
                for(var i = 0; i < Object.size(intervalCalls); i++)
                {
                    if (angular.isDefined(intervalCalls[i]))
                    {
                        $interval.cancel(intervalCalls[i]);
                        intervalCalls[i] = undefined;
                    }
                }
                intervalCalls = [];
            };
            globalVars.setStop(this.stop);


            /** getLatestScoreboardData()
             * This function fetches the latest scoreboard data for the desired track.
             * This includes the current scoreboard, and information on the next racers coming up.
             */
            HDScoreboardModel.prototype.getLatestScoreboardData = function()
            {
                //console.log("HD Scoreboard is getting data for track " + globalVars.getCurrentTrack());
                speedScreenServices.getScoreboardData(globalVars.getCurrentTrack()).success(function (data) {
                    $scope.currentScoreboard = data;
                    //console.log(data);
                }).error(function (data, status, headers, config) {
                    $scope.currentScoreboard = data;
                });
            };


            HDScoreboardModel.prototype.getNextRacerDataAndFinalResults = function()
            {
                speedScreenServices.getNextHeat(globalVars.getCurrentTrack()).success(function (data) {
                    $scope.currentNextRace = data;
                    //console.log(data);
                }).error(function (data, status, headers, config) {
                    $scope.currentNextRace = data;
                });

                if (this.lastHeatID != -1)
                {
                    speedScreenServices.getScoreboardDataByHeatID(this.lastHeatID).success(function (data) {
                        $scope.lastHeatScoreboard = data;

                    }).error(function (data, status, headers, config) {
                        $scope.lastHeatScoreboard = data;
                    });
                }
            };

            /** processLatestScoreboardData()
             *
             * This function processes the latest scoreboard data in order to prepare it for display.
             * This is split up into four separate steps:
             *  - Update the scoreboard memory of past events, and racers that are queued up.
             *  - Determine the scoreboard state. (Is a race running? Are there racers coming up?)
             *  - Process the racer data. (Who has the best lap time? Did someone just move up or down in rank?)
             *  - Update the scoreboard view. (Set the $scope variables that the view needs to function.)
             */
            HDScoreboardModel.prototype.processLatestScoreboardData = function()
            {
                //console.log("processLatestScoreboardData");
                if (!jQuery.isEmptyObject($scope.currentScoreboard)/* && !jQuery.isEmptyObject($scope.currentNextRace)*/) //If we have data to work with
                {
                    this.updateScoreboardMemory();
                    this.determineScoreboardState();
                    this.processRacerData();
                    this.updateScoreboardView();
                }
            };

            /** updateScoreboardMemory()
             *
             * This function maintains the scoreboard's memory, which is vital to some of its functionality
             * The two main memory objects being updated are:
             *  - lastHeatsHistory: This is a history of previous races and their results. This is used to
             *                      display final race results since once a race is over, Club Speed stops transmitting it.
             *  - nextRacersHistory: This keeps track of every set of "racers coming up" that has been sent over from
             *                       Club Speed. Once the time has passed for a race to start, Club Speed stops transmitting
             *                       those "racers coming up". Unfortunately, tracks are often behind, so we need to remember
             *                       these ourselves.
             */
            HDScoreboardModel.prototype.updateScoreboardMemory = function()
            {
                /*console.log("Last Heats History:");
                console.log(this.lastHeatsHistory); //this.lastHeatsHistory.scoreboard size must be > 0
                console.log("Next Racers History:");
                console.log(this.nextRacersHistory);*/

                if ($scope.currentScoreboard.hasOwnProperty("race")) //If there is a race going on
                {
                    this.lastHeatID = $scope.currentScoreboard.race.id; //Remember its race id - it's the most recent one we've seen

                    var currentScoreboardRecorded = false; //Have we already recorded this race's scoreboard? Let's assume not for now.
                    for (var i = 0; i < Object.size(this.lastHeatsHistory); i++) //Check every scoreboard in our race history
                    {
                        if (this.lastHeatsHistory[i].race.id == this.lastHeatID) //If we find the same race is already in our history
                        {
                            this.lastHeatsHistory[i] = copyOf($scope.currentScoreboard); //Update its history with the latest data
                            currentScoreboardRecorded = true; //Yes, we have already recorded this race's scoreboard. It was not a new race.
                            break;
                        }
                    }

                    //TODO: Experimental change to prevent blank race histories
                    if (currentScoreboardRecorded == false && Object.size($scope.currentScoreboard.scoreboard) > 0) //If the current scoreboard is a new race we haven't seen yet
                    {
                        this.lastHeatsHistory.push(copyOf($scope.currentScoreboard)); //Add it to the history of races
                    }

                    if (Object.size(this.lastHeatsHistory) > this.lastHeatsLimit) //If we've recorded more races in our history than we'd like to
                    {
                        this.lastHeatsHistory.shift(); //Delete the oldest one
                    }

                    if (Object.size($scope.currentScoreboard.scoreboard) != 0) //If the current race has actually begun
                    {
                        if (Object.size(this.nextRacersHistory) > 0 ) //And we have any "racers coming up" in memory
                        {
                            if (this.nextRacersHistory[0].race.id == $scope.currentScoreboard.race.id) //If the "racers coming up" are actually racing right now
                            {
                                this.nextRacersHistory.shift(); //Remove them from the "racers coming up" memory, since they're already racing
                            }
                        }
                    }
                }
                if (typeof $scope.currentNextRace != "undefined" && $scope.currentNextRace.hasOwnProperty("race")) //If Club Speed has sent us a "racers coming up" object
                {
                    var currentNextRaceRecorded = false; //Have we already recorded this "racers coming up" object? Let's assume not for now.

                    for (var i = 0; i < Object.size(this.nextRacersHistory); i++) //Check every "racers coming up" object we've received
                    {
                        if (this.nextRacersHistory[i].race.id == $scope.currentNextRace.race.id) //If we've already seen this one
                        {
                            this.nextRacersHistory[i] = copyOf($scope.currentNextRace); //Update it with the latest data
                            currentNextRaceRecorded = true; //Yes, we've already recorded these "racers coming up". It was not for a new race.
                            break;
                        }
                    }

                    if (currentNextRaceRecorded == false) //If the current "racers coming up" were for a race we hadn't seen yet
                    {
                        this.nextRacersHistory.push(copyOf($scope.currentNextRace)); //Add it to our list of "racers coming up"
                    }

                    //TODO: I believe there is a bug where some old nextRacersHistories stick around. POTENTIAL FIX.
                    if (Object.size(this.nextRacersHistory) == 1 && this.nextRacersHistory[0].race.id < this.lastHeatID)
                    {
                        this.nextRacersHistory = [];
                    }

                    for(var i = Object.size(this.nextRacersHistory) - 1; i >= 0 ; --i) //Remove "racers coming up" for races that already happened
                    {
                        if (this.nextRacersHistory[i].race.id < this.lastHeatID)
                        {
                            this.nextRacersHistory.splice(i,1);
                        }
                    }

                }
                if ($scope.currentScoreboard.hasOwnProperty("error")) //If there is no race running
                {
                    if (Object.size(this.nextRacersHistory) > 0) //And we have items in the next racer history
                    {
                        if (this.nextRacersHistory[0].race.id == this.lastHeatID) //And the next racer history heat has already been seen
                        {
                            this.nextRacersHistory.splice(0,1); //Remove it; that race is already over
                        }
                    }
                }
            };

            /** determineScoreboardState()
             *
             * This function determines which state the scoreboard view should be in.
             * These can be:
             *  - "no_races"
             *  - "driver_lineup"
             *  - "race_ongoing"
             *  - "final_results"
             *  - "last_few_heats" (FUTURE FEATURE - temporarily disabled)
             *
             *  Additionally, the visibility of the sliding panel (which also lists racers coming up) is set.
             *
             */
            HDScoreboardModel.prototype.determineScoreboardState = function()
            {
                if ($scope.currentScoreboard.hasOwnProperty("error")) //If there are no races on-going
                {
                    if (Object.size(this.nextRacersHistory) === 0) //And there are no next racers currently in the queue
                    {
                        if (Object.size(this.lastHeatsHistory) === 0) //And there are no past heats currently in memory, show nothing
                        {
                            this.scoreboardState = "no_races";
                        }
                        else if (Object.size(this.lastHeatsHistory) === 1 ) //If there is a single past heat in memory, show it
                        {
                            if (this.scoreboardState == "final_results") //If we were already showing final_results
                            {
                                //Figure out how long they've been running, in case a racer lineup is soon to appear
                                this.finalResultsScreenTimeMs = new Date() - this.finalResultsTimeStartedMs;
                            }
                            else //If this is the first time showing the final results
                            {
                                //TODO: Maybe not here... Move on to the next state after the timer is up... We now have new slides we can switch to!
                                this.finalResultsTimeStartedMs = new Date(); //Remember when we first started showing it
                                this.finalResultsScreenTimeMs = 0; //Reset how long it's been displayed
                            }
                            this.scoreboardState = "final_results";
                        }
                        else //If there are two or more past heats in memory, show them
                        {
                            //TODO: If there's one non-empty heat, final_results. If there's more than one, last_few_heats
                            //FUTURE FEATURE: this.scoreboardState = "last_few_heats";
                            this.scoreboardState = "final_results";
                        }
                    }
                    else //If there are next racers currently in the queue
                    {
                        if (this.scoreboardState == "final_results") //If we were already showing final_results
                        {
                            //Figure out how long they've been running
                            this.finalResultsScreenTimeMs = new Date() - this.finalResultsTimeStartedMs;
                            if (this.finalResultsScreenTimeMs > this.finalResultsMaxTimeMs)
                            {
                                if (Object.size(this.nextRacersHistory[0].race.racers) > 0 && !disableNextRacers)
                                {
                                    this.scoreboardState = "driver_lineup";
                                }
                            }
                        }
                        else if (this.scoreboardState != "driver_lineup" && Object.size(this.lastHeatsHistory) > 0) //If this is the first time showing the final results
                        {
                            this.finalResultsTimeStartedMs = new Date(); //Remember when we first started showing it
                            this.finalResultsScreenTimeMs = 0; //Reset how long it's been displayed
                            this.scoreboardState = "final_results";
                        }
                        else
                        {
                            if (Object.size(this.nextRacersHistory[0].race.racers) > 0  && !disableNextRacers) //If there are racers in the "racers coming up" data
                            {
                                this.scoreboardState = "driver_lineup"; //Show them as a full screen driver lineup
                            }
                        }
                    }
                }
                else //If there is a race on-going
                {   //TODO: Check IDs since often the same ID will be in the heat history and next racers that may be currently running
                    if (Object.size($scope.currentScoreboard.scoreboard) === 0) //But no racer has yet crossed over the loop
                    {
                        if (Object.size(this.nextRacersHistory) === 0) //And there are no next racers currently in the queue
                        {
                            if (Object.size(this.lastHeatsHistory) === 0) //And there are no past heats currently in memory, show the current blank scoreboard
                            {
                                this.scoreboardState = "race_ongoing";
                            }
                            else if (Object.size(this.lastHeatsHistory) === 1 && this.lastHeatsHistory[0].race != undefined && this.lastHeatsHistory[0].race.id != $scope.currentScoreboard.race.id) //If there is a single past heat in memory, show it
                            //TODO: Bug. If the current ID race is in the race history, this is wrong.
                            {
                                this.scoreboardState = "final_results";
                            }
                            else //If there are two or more past heats in memory, show them
                            {
                                //TODO: If there's one non-empty heat, final_results. If there's more than one, last_few_heats
                                //FUTURE FEATURE: this.scoreboardState = "last_few_heats";
                                this.scoreboardState = "final_results";
                            }
                        }
                        else //If there are next racers currently in the queue, show them
                        {
                            if (Object.size(this.nextRacersHistory[0].race.racers) > 0  && !disableNextRacers)
                            {
                                this.scoreboardState = "driver_lineup";
                            }
                            else
                            {
                                this.scoreboardState = "race_ongoing";
                            }
                        }
                    }
                    else //If cars have crossed the finish line
                    {
                        this.scoreboardState = "race_ongoing"; //Show the race!
                    }
                }

                //Coming up next tab visibility
                if (this.scoreboardState == "driver_lineup" && !disableNextRacersTab) //If we have a full screen driver lineup, hide the sliding tab
                {
                    this.slidePanelVisible = false;
                    if ($('.comingUpNextBox').hasClass('visibleComingUpNextBox')) //Hide the driver lineup box
                    {
                        $timeout(function(){
                            $(".comingUpNextBox").toggleClass('hiddenComingUpNextBox visibleComingUpNextBox');

                            $('#container').isotope( 'reloadItems' );
                            $('#container').isotope( 'reLayout' );
                            $('#container').isotope({ sortBy : 'position' });
                        });
                    }
                }
                else if (Object.size(this.nextRacersHistory) > 0 && Object.size(this.nextRacersHistory[0].race.racers) > 0 && !disableNextRacersTab) //If we have racers coming up in any other view, show the sliding tab
                {
                    this.slidePanelVisible = true;
                    if ($('.comingUpNextBox').hasClass('hiddenComingUpNextBox')) //Show the driver lineup box
                    {
                        $timeout(function(){
                            $(".comingUpNextBox").toggleClass('hiddenComingUpNextBox visibleComingUpNextBox');

                            $('#container').isotope( 'reloadItems' );
                            $('#container').isotope( 'reLayout' );
                            $('#container').isotope({ sortBy : 'position' });
                        });

                    }
                }
            };

            /** updateScoreboardView()
             *
             * This function updates the scoreboard view (via $scope variables) with the information it
             * needs in order to display itself.
             *
             * It also updates the countdown (or countup) timer.
             *
             */
            HDScoreboardModel.prototype.updateScoreboardView = function()
            {
                //Scope variable pushing
                $scope.lastHeatID = this.lastHeatID;
                $scope.lastHeatsHistory = this.lastHeatsHistory;
                $scope.nextRacersHistory = this.nextRacersHistory;
                $scope.scoreboardState = this.scoreboardState;

                //console.log("Scoreboard state: " + this.scoreboardState);

                $scope.finalResultsScreenTimeMs = this.finalResultsScreenTimeMs;
                $scope.slidePanelVisible = this.slidePanelVisible;
                $scope.lastRaceRacers = this.lastRaceRacers;
                if (Object.size(this.nextRacersHistory) > 0)
                {
                    $scope.nextRace = this.nextRacersHistory[0]; //TODO: Likely will be wrong depending on the ID.
                }
                if (Object.size(this.lastHeatsHistory) > 0)
                {
                    $scope.lastRaceScoreboard = this.lastHeatsHistory[0]; //TODO: Likely will be wrong depending on the ID.
                }

                if (this.scoreboardState == "race_ongoing" && typeof $scope.currentScoreboard.race != "undefined")
                {
                    if ($scope.currentScoreboard.race.race_by == "minutes")
                    {
                        var minutes = parseInt(($scope.currentScoreboard.race.duration*60 - $scope.currentScoreboard.race.race_time_in_seconds)/60);
                        var seconds = parseInt(($scope.currentScoreboard.race.duration*60 - $scope.currentScoreboard.race.race_time_in_seconds)%60);

                    }
                    else //by laps
                    {
                        if ($scope.mostLaps == undefined || parseInt($scope.mostLaps) > parseInt($scope.currentScoreboard.race.duration))
                        {
                            $scope.mostLaps = $scope.currentScoreboard.race.duration;
                        }
                        var lapsRemaining = $scope.currentScoreboard.race.duration - $scope.mostLaps;
                        if (lapsRemaining < 0)
                        {
                            lapsRemaining = 0;
                        }
                        $scope.lapsRemaining = lapsRemaining;

                    }
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
                }

            };

            /** processRacerData()
             *
             * This function packages and analyzes racer data for display in the scoreboard view.
             * It includes the activation of flash effects (when finishing a lap or beating a personal best lap time),
             * along with the appearance of up (green) and down (red) arrows for when positions have changed.
             * Lastly, it sets the best lap time color to purple.
             */
            HDScoreboardModel.prototype.processRacerData = function()
            {

                if (Object.size(this.nextRacersHistory) > 0) //If we have racers coming up
                {
                    this.nextRacers = {};
                    for(var key in this.nextRacersHistory[0].race.racers) //For every racer in the heat coming up
                    {
                        var currentRacer = this.nextRacersHistory[0].race.racers[key];
                        this.nextRacers[currentRacer.id] = currentRacer; //Package them nicely into an array
                    }
                    $scope.nextRacers = this.nextRacers; //And send that array to the view for rendering
                }

                if (Object.size(this.lastHeatsHistory) > 0 && $scope.lastHeatScoreboard != undefined) //If we have a history of past races
                {
                    this.lastRaceRacers = {};

                    for(var key in $scope.lastHeatScoreboard.scoreboard) //Package the race data for the view to display
                    {
                        var currentRacer = $scope.lastHeatScoreboard.scoreboard[key];
                        this.lastRaceRacers[currentRacer.racer_id] = currentRacer;
                    }
                }

                if (this.scoreboardState == "race_ongoing") //If we have a race happening, let's update the scoreboard with cool stuff as it happens
                {
                    this.oldRacers = JSON.parse(JSON.stringify(this.racers)); //Make a copy of the current racer state

                    this.racers = {};

                    //Get the latest racer state
                    for(var key in $scope.currentScoreboard.scoreboard) //Package the racer data for the view to display
                    {
                        var currentRacer = $scope.currentScoreboard.scoreboard[key];
                        this.racers[currentRacer.racer_id] = currentRacer;
                    }

                    $scope.racers = this.racers;

                    var fastestTime = 99999;
                    var mostLaps = -1; //The greatest number of laps. Used for formatting position races.
                    var fastestRacer;
                    for (var currentRacer in this.racers) //For every racer
                    {
                        if (this.racers.hasOwnProperty(currentRacer))
                        {
                            //Determine the highest number of laps
                            if (parseInt(this.racers[currentRacer].lap_num) > parseInt(mostLaps))
                            {
                                mostLaps = this.racers[currentRacer].lap_num;
                            }

                            //Determine the fastest lap time
                            this.racers[currentRacer].isFastest = false;
                            if (parseFloat(this.racers[currentRacer].fastest_lap_time) < parseFloat(fastestTime))
                            {
                                fastestTime = this.racers[currentRacer].fastest_lap_time;
                                fastestRacer = currentRacer; //And who made that fastest lap time
                            }

                            //Determine whether the racer went up, went down, or stayed the same
                            this.racers[currentRacer].positionStatus = 'neutral'; //Default state - didn't go up or down
                            if (this.racers[currentRacer].lap_num == 0)
                            {
                                this.racers[currentRacer].positionStatus = 'neutral'; //If it's the first practice lap, note no change in state
                            }
                            if (currentRacer in this.oldRacers && this.racers[currentRacer].position < this.oldRacers[currentRacer].position)
                            {
                                this.racers[currentRacer].positionStatus = 'wentUp'; //If a racer went up in position, make a note of it
                            }
                            else if (currentRacer in this.oldRacers && this.racers[currentRacer].position > this.oldRacers[currentRacer].position)
                            {
                                this.racers[currentRacer].positionStatus = 'wentDown'; //If a racer went down in position, make a note of it
                            }
                            else if (currentRacer in this.oldRacers)
                            {
                                this.racers[currentRacer].positionStatus = this.oldRacers[currentRacer].positionStatus; //Remember their previous state
                            }

                            //If a racer just finished a lap, make their lap number and most recent lap time flash
                            this.racers[currentRacer].racerLapNumberIncreased = false;
                            if (currentRacer in this.oldRacers && this.racers[currentRacer].lap_num > this.oldRacers[currentRacer].lap_num)
                            {
                                this.racers[currentRacer].racerLapNumberIncreased = true;
                                if (this.racers[currentRacer].position == this.oldRacers[currentRacer].position)
                                {
                                    this.racers[currentRacer].positionStatus = 'neutral';
                                    this.oldRacers[currentRacer].positionStatus = 'neutral';
                                }
                            }

                            //If a racer just beat their own best lap, flag them to flash their recent lap purple
                            this.racers[currentRacer].beatBestLap = false;
                            if (currentRacer in this.oldRacers && parseFloat(this.racers[currentRacer].fastest_lap_time) < parseFloat(this.oldRacers[currentRacer].fastest_lap_time))
                            {
                                this.racers[currentRacer].beatBestLap = true;
                            }

                            //For poland track - racers that beat their PREVIOUS lap time have a green background until their next lap
                            if (currentRacer in this.oldRacers)
                            {
                                this.racers[currentRacer].lastCompletedLapWasBest = this.oldRacers[currentRacer].lastCompletedLapWasBest;
                            }
                            if (currentRacer in this.oldRacers && this.racers[currentRacer].last_lap_time < this.oldRacers[currentRacer].last_lap_time
                                && this.racers[currentRacer].lap_num > this.oldRacers[currentRacer].lap_num)
                            {
                                this.racers[currentRacer].lastCompletedLapWasBest = true;
                            }
                            else if (currentRacer in this.oldRacers && this.racers[currentRacer].lap_num > this.oldRacers[currentRacer].lap_num)
                            {
                                this.racers[currentRacer].lastCompletedLapWasBest = false;
                            }
                        }
                    }
                    if (fastestRacer in this.racers)
                    {
                        this.racers[fastestRacer].isFastest = true; //Make a note of the fastest racer by having their best lap time be written in purple text
                    }

                    $scope.mostLaps = mostLaps;

                    //If the race is by position
                    if ($scope.currentScoreboard.hasOwnProperty("race") && $scope.currentScoreboard.race.hasOwnProperty("win_by") && $scope.currentScoreboard.race.win_by == "position")
                    {
                        for (var currentRacer in this.racers) //For every racer
                        {
                            if (this.racers.hasOwnProperty(currentRacer) && parseInt(this.racers[currentRacer].lap_num) < parseInt(mostLaps)) //If they're behind in laps
                            {
                                this.racers[currentRacer].gap = parseInt(this.racers[currentRacer].gap) + "L"; //Format their gap to be an actual lap number
                            }
                        }
                    }
                }

            };
        }

        // ######################################
        // # END HD SCOREBOARD MODEL DEFINITION #
        // ######################################


        //#####################
        //# UTILITY FUNCTIONS #
        //#####################

        /**
         * Makes a deep copy of a JavaScript object.
         * @param object
         * @returns {*}
         */
        function copyOf(object)
        {
            return JSON.parse(JSON.stringify(object));
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

        /**
         * Enables debugging output for development.
         * This makes it so clicking the header can display debug information.
         */
        /*$(document).ready(function(){
            $("#clickMe").click(function() {

                console.log("Debug info toggled");
                $('#debugInfo').toggle();

            });
            $("#clickMe2").click(function() {

                console.log("Debug info toggled");
                $('#debugInfo').toggle();

            });
        });*/

    });

/**
 * Adds default parameter functionality to JavaScript. Woohoo!
 * @param arg
 * @param val
 * @returns {*}
 */
function defaultFor(arg, val)
{ return typeof arg !== 'undefined' ? arg : val; }

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
            return (parseInt(a[field]) > parseInt(b[field])) ? 1 : ((parseInt(a[field]) < parseInt(b[field])) ? -1 : 0);
        });
        if(reverse) filtered.reverse();
        return filtered;
    };
});
