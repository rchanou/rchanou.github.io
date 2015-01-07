clubSpeedOnlineApp.controller('liveScoreboardController', function($scope, $routeParams, $location, ClubSpeedJSONService, globalVars) {

    ClubSpeedJSONService.getSettings().success(function (data) {
        for (var i = 0; i < Object.size(data.settings); i++) {
            if ((data.settings[i].name == 'defaultTrack')) {
                $scope.defaultTrack = data.settings[i].value;
                break;
            }
        }

        $scope.currentTrackId = defaultFor($routeParams.desiredTrack, defaultFor($scope.defaultTrack, 1));

        ClubSpeedJSONService.getTracks().success(function (data) {
            $scope.tracks = data.tracks;

            for (var i = 0; i < Object.size($scope.tracks); i++) {
                if ($scope.tracks[i].id == $scope.currentTrackId) {
                    $scope.currentTrackName = $scope.tracks[i].name;
                    break;
                }
            }

        });


    function LiveScoreboard()
    {
        this.scoreboardRows = {};
    }

    LiveScoreboard.prototype.receiveLatestScoreboard = function(newScoreboardUnformatted)
    {
        var scoreboardRows = newScoreboardUnformatted["scoreboard"];
        if (newScoreboardUnformatted.error != 'undefined' && newScoreboardUnformatted.error != null )
        {
            if (newScoreboardUnformatted.error.code == 412)
            {
                $scope.raceRunning = 0;
            }
        }
        if (typeof(scoreboardRows) == 'undefined' || scoreboardRows == null)
        {
            return;
        }
        $scope.raceRunning = 1;
        $scope.race = newScoreboardUnformatted.race;
        var newScoreboard = {};

        var i;
        for (i=0; i < scoreboardRows.length; ++i)
        {
            newScoreboard[scoreboardRows[i].racer_id] = scoreboardRows[i];
        }

        for (var currentRacerID in newScoreboard) //For each racer in the newer scoreboard
        {
            newScoreboard[currentRacerID].currentState = 'neutral'; //Set the default currentState
            newScoreboard[currentRacerID].currentState_Speed = 'neutral';
            newScoreboard[currentRacerID].racerFinishedALap = 0;
            newScoreboard[currentRacerID].racerGotABetterTime = 0;
            newScoreboard[currentRacerID].racerJustEnteredRace = 1;
            if (newScoreboard[currentRacerID].gap == ".000")
            {
                newScoreboard[currentRacerID].gap = "-";
            }
        }

        for (var currentRacerID in this.scoreboardRows) //For each racer in the older scoreboard
        {

            var oldRow = this.scoreboardRows[currentRacerID];
            var newRow = newScoreboard[oldRow.racer_id];
            var newRowState;

            if (newRow !== undefined) //If the current racer is represented in the new scoreboard
            {
                //Compare the new row for that racer with the old row
                newRowState = this.compareWithScoreboardRow(oldRow, newRow);

                //Set the state of the new row appropriately (ex. the racer went up or down in position)
                newRow.currentState = newRowState;
                newRow.racerJustEnteredRace = 0;
            }
        }

        //Overwrite the old scoreboard with the new
        this.scoreboardRows = {};
        for (var currentRacerID in newScoreboard) //For each racer in the newer scoreboard
        {
            this.scoreboardRows[currentRacerID] = newScoreboard[currentRacerID]; //Replace it with the newer racer info
        }

        //Determine which racer went purple (fastest)
        this.determineFastestRacer();

        //Sort the scoreboard by position
        var sortedRows = [];
        for (var currentRacerID in newScoreboard) //For each racer in the newer scoreboard
        {
            sortedRows[newScoreboard[currentRacerID].position - 1] = newScoreboard[currentRacerID];
        }

        this.scoreboardRows = sortedRows;

        $scope.lastRaceDetails = JSON.parse(JSON.stringify(newScoreboardUnformatted.race));
        $scope.lastScoreboardRows = JSON.parse(JSON.stringify(this.scoreboardRows));
    };

    LiveScoreboard.prototype.determineFastestRacer = function()
    {
        var fastestLapTimeSoFar = 99999;
        var fastestRacerID = -1;
        for (var currentRacerID in this.scoreboardRows)
        {
            if (this.scoreboardRows[currentRacerID].fastest_lap_time < fastestLapTimeSoFar) //If they have the fastest lap time so far
            {
                fastestRacerID = this.scoreboardRows[currentRacerID].racer_id; //Remember them
                fastestLapTimeSoFar = this.scoreboardRows[currentRacerID].fastest_lap_time; //Update the fastest lap time
            }
        }

        if (fastestRacerID != -1) //If we found a fastest racer
        {
            this.scoreboardRows[fastestRacerID].currentState_Speed = 'fastestOverall'; //Set their state to the fastest overall
        }
    };

    LiveScoreboard.prototype.compareWithScoreboardRow = function (oldScoreboardRow, newScoreboardRow) {
        if (oldScoreboardRow.lap_num != newScoreboardRow.lap_num)
        {
            newScoreboardRow.racerFinishedALap = 1;
        }
        if (oldScoreboardRow.fastest_lap_time > newScoreboardRow.fastest_lap_time)
        {
            newScoreboardRow.racerGotABetterTime = 1;
        }
        if (oldScoreboardRow.racer_id !== newScoreboardRow.racer_id) {
            return 'differentIDs';
        }
        else {
            if (oldScoreboardRow.position > newScoreboardRow.position) {
                return 'wentUp';
            }
            else if (oldScoreboardRow.position < newScoreboardRow.position) {
                return 'wentDown';
            }
        }
        return 'neutral';
    };

    var currentScoreboard = new LiveScoreboard();

    $scope.currentScoreboard = currentScoreboard;


    //Used to route to a specific track
    $scope.goToScoreboardTrack = function ( desiredTrack ) {
        $location.path( '/livescoreboard/' + desiredTrack );
    };

    function pollForScoreboard()
    {
        ClubSpeedJSONService.getScoreboardData($scope.currentTrackId).success(function (data) {
            currentScoreboard.receiveLatestScoreboard(data);
        });

        globalVars.setScoreboardUpdateTimeout(setTimeout(pollForScoreboard,1000));
    }

    pollForScoreboard();

    });

    Object.size = function(obj) {
        var size = 0, key;
        for (key in obj) {
            if (obj.hasOwnProperty(key)) size++;
        }
        return size;
    };
});



