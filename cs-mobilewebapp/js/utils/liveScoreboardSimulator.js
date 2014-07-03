/*
    This file defines a Node.js server which will simulate a go kart race (by fastest lap time) and will
    periodically emit a Scoreboard object via socket.io that is compatible with the
    Club Speed live scoreboard page. This simulation maintains accurate timing statistics.

    To execute this file, install node.js, navigate to the file's directory in the
    Command Prompt, and type "node liveScoreboardSimulator.js"

    Known minor issues:
    * There is some inefficiency with emitting each racer's lap history. Not a biggie for simulation purposes.
*/

/* Configuration options */
var minLapTime_Seconds = 15; //The shortest possible lap time
var maxLapTime_Seconds = 25; //The longest possible lap time
var frequencyOfScoreboardUpdates_Milliseconds = 2000; //How frequently socket.io will emit updates
//To add or remove racers, edit the this.arrayOfRacers array below!

/*
    This constructor for the class LiveScoreboardSimulator just defines the racers,
    an empty scoreboard object which will eventually be populated and emitted, and
    starts an internal timer.
 */
function LiveScoreboardSimulator()
{
    this.arrayOfRacers = [];
    this.arrayOfRacers.push( //Feel free to add or remove racers here
        new Racer("Brian"),
        new Racer("Wes"),
        new Racer("Shakib"),
        new Racer("Maged"),
        new Racer("Eric"),
        new Racer("Christina"),
        new Racer("Chris"),
        new Racer("Ryan"),
        new Racer("Shingo"),
        new Racer("Gus (Woof!)"),
        new Racer("Mynameisverylongandcancauseproblems"),
        new Racer("Long names have to be handled properly")
    );
    this.scoreboard = [];
}

/*
    Just a simple getter for the scoreboard object.
 */
LiveScoreboardSimulator.prototype.getScoreboard = function getScoreboard()
{
    return this.scoreboard;
};


/*
    For every racer, this method generates what their next lap time will be.
 */
LiveScoreboardSimulator.prototype.generateNextLaps = function generateNextLaps()
{
    //For every racer
    for(var i = 0; i < this.arrayOfRacers.length; i++)
    {
        if (this.arrayOfRacers[i].readyForUpdate) //If they're ready for a new lap time, generate one
        {
            this.arrayOfRacers[i].timeOfLastLapTimeGeneration = new Date().getTime();
            this.arrayOfRacers[i].finishALap();
            this.arrayOfRacers[i].readyForUpdate = 0;
        }
    }

    this.arrayOfRacers.sort(sortByFastestLapTime); //Sort by fastest lap time
};

/*
    This function checks the status of all racers, and if it's time for them to cross the finish line,
    it inserts their latest lap into the scoreboard, and flags them to be ready to receive their next lap time.
    This method also updates every racer's position and gap times.
 */
LiveScoreboardSimulator.prototype.processNextLaps = function processNextLaps()
{
    for(var i = 0; i < this.arrayOfRacers.length; i++) //For every racer
    {
        this.timeSinceLastLapTimeGeneration = new Date().getTime() - this.arrayOfRacers[i].timeOfLastLapTimeGeneration;
        if (this.timeSinceLastLapTimeGeneration > this.arrayOfRacers[i].last_lap_time*1000 && this.arrayOfRacers[i].readyForUpdate == 0) //If it's time for a racer's lap to have completed
        {
            //Look for the current racer to update in the scoreboard
            var positionOfRacerInCurrentScoreboard = scoreboardContainsRacer(this.scoreboard,this.arrayOfRacers[i].nickname);
            if (positionOfRacerInCurrentScoreboard != -1) //If the racer is already in the scoreboard
            {
                this.scoreboard.splice(positionOfRacerInCurrentScoreboard,1); //Remove his old entry
            }
            this.scoreboard.push(this.arrayOfRacers[i]); //Update the racer's information
            this.arrayOfRacers[i].readyForUpdate = 1; //Flag the racer as being ready to receive his next lap
        }
    }

    //Update the scoreboard's stats
    this.scoreboard.sort(sortByFastestLapTime); //Re-sort the racers by lap time
    for(var i = 0; i < this.scoreboard.length; i++) //For every racer on the scoreboard
    {
        this.scoreboard[i].position = i+1; //Figure out their position
        if(this.scoreboard[i].position == 1) //And figure out their gap
        {
            this.scoreboard[i].gap = '-';
        }
        else
        {
            this.scoreboard[i].gap = Math.abs(this.scoreboard[0].fastest_lap_time - this.scoreboard[i].fastest_lap_time).toFixed(3);
        }
    }

};

/*
    Returns true if the racer is currently present in the scoreboard, and false otherwise.
 */
function scoreboardContainsRacer(scoreboard, nickname)
{
    for(var i = 0; i < scoreboard.length; i++)
    {
        if (scoreboard[i].nickname == nickname)
        {
            return i;
        }
    }
    return -1;
}


var startID = 1; //Used to give each racer a unique sequential ID and kart number.

/*
    Defines a racer and their properties, which for the most part have a 1-to-1 correspondence with the expected
    members that the scoreboard object will be expected to have for each racer.
 */
function Racer(nickname)
{
    this.position = -1;
    this.nickname = nickname;
    this.average_lap_time = 0;
    this.fastest_lap_time = '-';
    this.last_lap_time = -1;
    this.rpm = 1200;
    this.racer_id = startID;
    startID += 1;
    this.lap_num = -1;
    this.kart_num = 100 + startID;
    this.gap = -1;

    this.arrayOfLapTimes = []; //A history of every lap time the racer has had. Used to figure out average lap times.
    this.readyForUpdate = 1; //Is this racer ready to receive his or her next random lap time?
    this.timeOfLastLapTimeGeneration = 0; //How long it's been since the racer has received a lap time.
}

/*
    This function determines the racer's next random lap time, and calculates the new best lap time and average lap time.
    Tbis information, however, is not automatically inserted into the scoreboard, as it is generated in advance, before
    the lap time would actually occur. The simulator will check the racer periodically and see if their lap time is
    about to happen, and then will update the scoreboard "live".
 */
Racer.prototype.finishALap = function()
{
    var randomLapTime = minLapTime_Seconds + Math.floor((Math.random()*(maxLapTime_Seconds-minLapTime_Seconds))) + Math.random();
    this.lap_num += 1;
    if (this.lap_num == 1) //If this is the first real (non-warmup lap)
    {
        this.arrayOfLapTimes = []; //Forget the practice laps
        this.fastest_lap_time = randomLapTime.toFixed(3);
        this.average_lap_time = randomLapTime.toFixed(3);
    }
    this.arrayOfLapTimes.push(randomLapTime);
    this.last_lap_time = randomLapTime.toFixed(3);

    var sum = 0;
    for(var i = 0; i < this.arrayOfLapTimes.length; i++)
    {
        sum += this.arrayOfLapTimes[i];
    }

    this.average_lap_time = sum/this.arrayOfLapTimes.length;
    this.average_lap_time = this.average_lap_time.toFixed(3);

    if (this.fastest_lap_time == '-' || randomLapTime < this.fastest_lap_time)
    {
        this.fastest_lap_time = randomLapTime.toFixed(3);
    }
};

/*
    Helper function to be used by array.sort(); it sorts racers by their fastest lap time.
 */
function sortByFastestLapTime(racerA,racerB)
{
    if (racerA.fastest_lap_time < racerB.fastest_lap_time)
    {
        return -1;
    }
    else if (racerA.fastest_lap_time > racerB.fastest_lap_time)
    {
        return 1;
    }
    else
    {
        return 0;
    }
}

/* Socket.io connection and simulation initiation */

var io = require('socket.io').listen(8080); //Listen for socket.io connections on port 8080

var liveScoreboard; //Singleton. Only one liveScoreboard is created, regardless of number of connections.
io.sockets.on('connection', function (socket) {
    if(typeof(liveScoreboard) == 'undefined' || liveScoreboard == null) //If no liveScoreboard yet exists
    {
        liveScoreboard = new LiveScoreboardSimulator(); //Create one and start the simulation!
        setInterval(function(){liveScoreboard.generateNextLaps()},minLapTime_Seconds/2);
        setInterval(function(){liveScoreboard.processNextLaps()},frequencyOfScoreboardUpdates_Milliseconds/4);
    }
    setInterval
    (
        function()
        {
            socket.emit('scoreboard', { scoreboard: liveScoreboard.getScoreboard() }); //Periodically send updates on the scoreboard
        }
        , frequencyOfScoreboardUpdates_Milliseconds);
});