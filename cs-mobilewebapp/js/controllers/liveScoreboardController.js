//TODO: Description
clubSpeedOnlineApp.controller('liveScoreboardController', function($scope, $routeParams, SocketIOService, ClubSpeedJSONService) {

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


        //console.log(" ");
        //console.log("=== receiveLatestScoreboard BEGINS ===");
        //console.log("Now iterating through any existing rows in the old scoreboard");
        //for (i = 0; i < scoreboardRows.length; ++i) //For each old row in the older scoreboard
        for (var currentRacerID in this.scoreboardRows) //For each racer in the older scoreboard
        {

            var oldRow = this.scoreboardRows[currentRacerID];
            var newRow = newScoreboard[oldRow.racer_id];
            //console.log("Current old row: " + JSON.stringify(oldRow));
            //console.log("Current new row: " + JSON.stringify(newRow));
            var newRowState;

            if (newRow !== undefined) //If the current racer is represented in the new scoreboard
            {
                //Compare the new row for that racer with the old row
                newRowState = this.compareWithScoreboardRow(oldRow, newRow);
                //console.log("newRowState=" + newRowState);

                //Set the state of the new row appropriately (ex. the racer went up or down in position)

                newRow.currentState = newRowState;
                newRow.racerJustEnteredRace = 0;
            }
        }

        //Overwrite the old scoreboard with the new
        this.scoreboardRows = {};
        //for (i=0; i < newScoreboard.length; ++i)
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
        //console.log("=== receiveLatestScoreboard ENDS ===");
        //console.log(" ");
    };

    LiveScoreboard.prototype.determineFastestRacer = function()
    {
        var fastestLapTimeSoFar = 99999;
        var fastestRacerID = -1;
        //for (i = 0; i < scoreboardRows.length; ++i) //For every racer
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
            //console.log("Fastest racer identified, setting state for: " + this.scoreboardRows[fastestRacerID].nickname);
            this.scoreboardRows[fastestRacerID].currentState_Speed = 'fastestOverall'; //Set their state to the fastest overall
        }
    };

    //TODO: Refactor and clean up.
    LiveScoreboard.prototype.compareWithScoreboardRow = function (oldScoreboardRow, newScoreboardRow) {
        //console.log("compareWithScoreboardRow, oldPosition = " + oldScoreboardRow.position + ", newPosition = " + newScoreboardRow.position)
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



    //TODO: Track support, race name, get race in session information

    var currentScoreboard = new LiveScoreboard();

    $scope.currentScoreboard = currentScoreboard;

    function pollForScoreboard()
    {
        ClubSpeedJSONService.getScoreboardData().success(function (data) {
            currentScoreboard.receiveLatestScoreboard(data);
            console.log(JSON.stringify(data));
        });
        setTimeout(pollForScoreboard,1000);
    }

    pollForScoreboard();

    //TODO: Eliminate this if no longer using socket.io
    /*
     SocketIOService.on('scoreboard', function (data) {
        //console.log("Receiving data from socket.io");
         //console.log(JSON.stringify(data));
        currentScoreboard.receiveLatestScoreboard(data);
    });
    */

});



