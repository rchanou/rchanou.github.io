/*
WHAT IS AN EVENT? A DEFINITION.

Event > Round(s) > Heat(s) > Group(s) > Racer(s)

- An event is made up of rounds.
- Each round has a heat (or multiple heats is all drivers cannot fit into one heat)
- Each heat is filled with a group of people gridded in a certain order

Note: Another, higher level, concept exists as a "Series". A Series contains multiple events, usually over a longer period of time such as months. The result of all events can determine the series winner.

Further Definition of Rounds
A round is any grouping of heats where the heat grid(s) can be determined with a single set of procession criteria (proceeding defined below). When a set of heats needs to be created based upon proceeding from the results of a prior set of heats, a new round is created.

Scoring
A scoring template defines how points are applied to racers in a heat when the race finishes. Ex. 10th = 10 points, 1st = 1 point OR 1st = 10 points, 10th = 1 point. Club Speed should include some defaults, but in practice people have some creative methods of applying points.

If there are more racers than rows in the points template, the value of the last row should be taken. Ex. "1st = 10pt, 2nd = 5pt, 3rd = 0pt". 4th place is not defined, but should take the point value of the last item in the list -- 0pts.

Points may also be manually altered after the conclusion of a race.

Proceeding Between Rounds
To proceed from one round to the next, new terms are introduced:
- Procession Filter: How many people get through from one round to the next (all or just a portion or racers -- by percentage, # of drivers or personal best lap within a percentage of the overall best lap)
- Proceed From: Where do they come from (the same group, entire round, selective rounds, entire event)
- Gridding: Once you have a group of racers, how are they lined up for the next heat?
- Tie Breakers: Ties may be ignored, broken on fastest lap of a certain race or most first place, second place, etc finishes
- Custom Function (Ex. only those with a certain laptime or faster)

Grid Sorting Options: X=DONE -=IN PROGRESS
X Random (In Event, Round(s), Group(s))
X Inverted (In Event, Round(s), Group(s))
X By Most Points (In Event, Round(s), Group(s))
X By Least Points (In Event, Round(s), Group(s))
X By Best LapTime (In Event, Round(s), Group(s))
X By Best Average LapTime (In Event, Round(s), Group(s))
X Magix-style Gridding (In Event, Round(s))
X User Template/Custom Function (In Event, Round(s))
- Previous Grid Position +- 1 (Needed?)
- Fair (Makes "fair" groups combining fastest and slowest into balanced teams)
- Balanced (Puts the top finishers each on pole of their own race)
X No auto gridding

Grid Type Options:
// TODO -- Add documentation

All Options Together:

Random - Last Round AKA UserDataType.LineupType.Random
Random - Last Round/Same Group AKA UserDataType.LineupType.RandomWithSameGroup
Starting Position - Last Round AKA UserDataType.LineupType.QualifierWithCurrentRound
*Starting Position - Last Round Inverted AKA UserDataType.LineupType.OppositeOrder
Starting Position - Last Round/Same Group AKA UserDataType.LineupType.QualifierWithSameGroup
Finishing Position - Last Round
Finishing Position - Last Round/Same Group
Most Points - In Event AKA UserDataType.LineupType.By_BestScoreInEvent
Most Points - Last Round AKA UserDataType.LineupType.By_Points
Most Points - Last Round/Same Group
Fair - In Event
Fair - Last Round
Fair - Last Round/Same Group
Balanced - In Event
Balanced - Last Round
Balanced - Last Round/Same Group
Best LapTime - In Event AKA UserDataType.LineupType.By_BestLapTimeInEvent
Best LapTime - Last Round AKA UserDataType.LineupType.By_BestLapTime
Best LapTime - Last Round/Same Group
Magic/Fair Gridding-style - This gridding type creates heats that allow every racer to race in every position. An attempt is made to put different racers in each heat so that each participant has the opportunity to race others.

This gridding type will create as many heats as there are racers. 24 racers = 24 heats. 10 racers = 10 heats. The number of racers per heat is controlled by the heat type being used.


Custom - AKA UserDataType.LineupType.UserTemplate
No gridding AKA UserDataType.LineupType.DoNotGrid

* This could be removed or converted over to "Starting Position - Last Round" with invert flag set
*** Include flag to "invert" which will reverse the order ***

GRIDDING LINEUP FOR NEXT ROUND

Select Participant Results From: Entire Event, Last Round
*Some lineups only consider last race run (Starting Position, Finishing Position)

Keep in Same Groups? Yes/No *Determined by last race run

Lineup (sorting) Type: Random, Starting Position, Finishing Position, Points, Fastest LapTime, Average LapTime, Magix, No gridding

Gridding Types:
- "Default" (gridded in order of sorting into most equal numbered groups)
- Balanced: P1 given to 1, 2, 3; P2 for 4, 5, 6; P3 for 7, 8, 9; P4 for 10 (in the case of a 10 person, 4 per group race)
- Magix (every racer races every grid position)
- Fair (most equal combination of best and worst people)

Invert the Lineup? Yes/No

Filter by: Number, Percent, bestLapWithinPercentOfFastest (Within 107% of fastest lap, as in F1)

Filter value: Integer *Default to 100


KART AUTO ASSIGNMENT
Karts may be assigned in four ways:
- No assignment: none
- Same kart: same
- Fair: fair
- Random: random

A pool of karts can be specified or the karts given on each participant will be used by default.
These options are passed in as options when gridding:

	opts.vehicleAssignmentType = opts.vehicleAssignmentType || 'none'; // none, random, same, fair
	opts.vehiclesAvailable = opts.vehiclesAvailable || []; // [1, 2, 3, 4]


IN PRACTICE

Applying the concepts above in programming and in an easy to use customer interface is tricky. Kart tracks rarely follow neat rules. Drivers show up late, are added/dropped between rounds. Heats are run out of order.

Creating Heats
Heats are created based on the event template. For an event with 20 people but races that only hold 10, multiple heats will be created in a round.

Caveat
Care must be taken to evenly distribute racers -- for instance, a 15 person event with heats that contain a max of 10 drivers should not be two heats of 10 and 5 but instead should be heats of 8 and 7.

USER TEMPLATE/CUSTOM GRIDDING
A custom gridding option needs to have the context of which round it is in and how people were lined up in prior heats. This allows for any type of gridding. It could also need to take into account strange circumstances such as "grid all women first and men second".

The context of the entire grid, customers, etc should likely be passed in via the options array. Perhaps the function to do the custom sort can also be an optional parameter to maintain flexibility. Because of the total flexibility of how we can grid people, a standard, consistent output is necessary...

A proposal on consistent format is: [p1, ..., pN] as an array of arrays representing the grid lineup for each round.

So for a three round event where Round 1: 1-3 race; Round 2: 4-6 race; Round 3: 7-9 race, the grid would be represented as:
[
[1, 2, 3], // Heat 1
[4, 3, 6], // Heat 2
[1, 8, 9]  // Heat 3
]

////
// THINGS THAT HAVE TO HAPPEN BEFORE YOU CAN GRID
////

FINDING HEATS TO GRID - findHeatsByGroupingTypeForEvent(currentRoundId, groupingType);
// groupingType = sameGroup, currentRound, wholeEvent

1. Find heat ids to get results (fastest lap, points, etc) for gridding for next round:
		A. Same Group
		B. Current Round
		C. Whole Event

A. Same Group -- Current Round or Current Event
Find the Heat IDs and customers in each group. The Group is judged by the last run race that is completed for each participant
[ { participants: [ 1, 2, 3 ], heats: [ 123, 124 ] }, { participants: [ 4, 5, 6 ], heats: [ 125, 126 ] } ]

B. Current Round
Find the heats in the current round
[ { participants: [ 1, 2, 3 ], heats: [ 123, 124 ] } ]

C. Whole Event
Find the heats from the entire event
[ { participants: [ 1, 2, 3 ], heats: [ 123, 124 ] } ]

***Be sure to only find heats that have been run and finished (Heat Status 2 or 3)

2. Get their results that we will be gridding by from a grouping of heat ids
getParticipantResultsForGridding(heatIds, options) {
// options = points, startingPosition, finishingPosition, bestLapTime
	
var heatIds = [ { participants: [ 1, 2, 3 ], heats: [ 123, 124 ] }, { participants: [ 4, 5, 6 ], heats: [ 125, 126 ] } ]
var options:
- fields: [ points (sum), finishingPosition (min), startingPosition: (min), bestLapTime (min)]

Returns an array of customer objects by group: (some of the below are optional depending on sort method)
- name/customer id
- points (summed from all prior in type)
- start position
- finish position (best from all prior in type)
- best laptime (best from all prior in type)
- average laptime (from all prior in type)
- Num 1sts, num 2nds, num 3rds (from all races considered)
- Optional: Other information such as gender, RPM (it's just ignored)

[ [
	{ participantId: 'Wes', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
	{ participantId: 'Glenda', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
	{ participantId: 'Max', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
	{ participantId: 'Tommy', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
	{ participantId: 'Shakib', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 }
],
[
	{ participantId: 'Wes', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
	{ participantId: 'Glenda', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
	{ participantId: 'Max', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
	{ participantId: 'Tommy', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
	{ participantId: 'Shakib', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 }
] ]

	// By points
	SELECT hd.CustID, SUM(hd.scores) AS TotalPoints FROM HeatMain hm
	LEFT JOIN HeatDetails hd ON hm.HeatNo = hd.HeatNo
	WHERE hm.HeatNo IN (379,380)
	AND hd.CustID IN (1,2,3,4)
	GROUP BY hd.CustID
	ORDER BY TotalPoints DESC

	// By starting position
	SELECT hd.CustID, MIN(LineUpPosition) as startingPosition FROM HeatMain hm
	LEFT JOIN HeatDetails hd ON hm.HeatNo = hd.HeatNo
	WHERE hm.HeatNo IN (379,380)
	GROUP BY hd.CustID
	ORDER BY startingPosition

	// By finishing position
	SELECT hd.CustID, MIN(FinishPosition) as finishingPosition FROM HeatMain hm
	LEFT JOIN HeatDetails hd ON hm.HeatNo = hd.HeatNo
	WHERE hm.HeatNo IN (379,380)
	GROUP BY hd.CustID
	ORDER BY finishingPosition
	
	// By average laptime
	// TODO
	
	// TO INCLUDE IN EVERY RESULT SET FOR TIE BREAKING //
	// Include Best LapTime (TODO: points are way too high -- summing all joined racing data rows -- Need subquery to return just best laptime <> 0 for each customer in heats?)
	SELECT hd.CustID, SUM(hd.scores) AS TotalPoints, MIN(rd.ltime) AS BestLapTime FROM HeatMain hm
	LEFT JOIN HeatDetails hd ON hm.HeatNo = hd.HeatNo
	LEFT JOIN RacingData rd ON hd.CustID = rd.CustId 
	WHERE hm.EventRound IN (379,380) AND rd.ltime <> 0
	GROUP BY hd.CustID
	ORDER BY TotalPoints DESC
}

3. Get gridding
Loop each heat and pass in the type of gridding with options and participants with results

Input:
[
	{ participantId: 'Wes', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
	{ participantId: 'Glenda', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
	{ participantId: 'Max', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
	{ participantId: 'Tommy', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
	{ participantId: 'Shakib', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 }
 ]


curl -i -X POST -H "Content-Type: application/json" -d '{ "participants": [{"name":"Wes","points":5,"bestAverageLapTime":31,"bestLapTime":35.234,"startingPosition":3,"finishingPosition":5},{"name":"Glenda","points":3,"bestAverageLapTime":33,"bestLapTime":33.234,"startingPosition":2,"finishingPosition":4},{"name":"Max","points":3,"bestAverageLapTime":35,"bestLapTime":33.536,"startingPosition":1,"finishingPosition":3},{"name":"Tommy","points":2,"bestAverageLapTime":34,"bestLapTime":36.234,"startingPosition":4,"finishingPosition":2},{"name":"Shakib","points":0,"bestAverageLapTime":32,"bestLapTime":31.234,"startingPosition":5,"finishingPosition":1}], "options": { "maxDrivers": 3 } }' http://192.168.111.103:8000/grid/bestLapTime

curl -i -X POST -H "Content-Type: application/json" -d '{"partcipiants":[{"name":"Shakib","points":39,"bestAverageLapTime":123.025,"bestLapTime":123,"startingPosition":1,"finishingPosition":1},{"name":"Shakib2","points":39,"bestAverageLapTime":123.025,"bestLapTime":123,"startingPosition":1,"finishingPosition":1}],"options":{"maxDrivers":10}}' http://192.168.111.103:8000/grid/bestLapTime


curl -i -X POST -H "Content-Type: application/json" -d '{ "participants": [{"name":"Wes","points":5,"bestAverageLapTime":31,"startingPosition":3,"finishingPosition":5},{"name":"Glenda","points":3,"bestAverageLapTime":33,"bestLapTime":33.234,"startingPosition":2,"finishingPosition":4},{"name":"Max","points":3,"bestAverageLapTime":35,"bestLapTime":33.536,"startingPosition":1,"finishingPosition":3},{"name":"Tommy","points":2,"bestAverageLapTime":34,"bestLapTime":36.234,"startingPosition":4,"finishingPosition":2},{"name":"Shakib","points":0,"bestAverageLapTime":32,"bestLapTime":31.234,"startingPosition":5,"finishingPosition":1}], "options": { "maxDrivers": 3 } }' http://127.0.0.1:8000/grid/balanced

Output: (array of heats with participants in grid order)
[[{"name":"Shakib","startingPosition":1,"originalData":{"name":"Shakib","points":0,"bestAverageLapTime":32,"bestLapTime":31.234,"startingPosition":5,"finishingPosition":1}},{"name":"Glenda","startingPosition":2,"originalData":{"name":"Glenda","points":3,"bestAverageLapTime":33,"bestLapTime":33.234,"startingPosition":2,"finishingPosition":4}},{"name":"Max","startingPosition":3,"originalData":{"name":"Max","points":3,"bestAverageLapTime":35,"bestLapTime":33.536,"startingPosition":1,"finishingPosition":3}}],[{"name":"Wes","startingPosition":1,"originalData":{"name":"Wes","points":5,"bestAverageLapTime":31,"bestLapTime":35.234,"startingPosition":3,"finishingPosition":5}},{"name":"Tommy","startingPosition":2,"originalData":{"name":"Tommy","points":2,"bestAverageLapTime":34,"bestLapTime":36.234,"startingPosition":4,"finishingPosition":2}}]]


__API DOCS__

POST to http://192.168.111.103/grid/:griddingType

Where :griddingType is:
- startingPosition
- finishingPosition
- bestLapTime
- bestAverageLapTime
- mostPoints
- randomized
- magix
- custom
- fair
- noGrid

Participants:
[
	{ participantId: 'Wes', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
	{ participantId: 'Glenda', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
	{ participantId: 'Max', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
	{ participantId: 'Tommy', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
	{ participantId: 'Shakib', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 }
 ]

Options:
maxDrivers: Default: Infinity
numHeatsPerParticipant: Default: 1
filterName: topNumber || topPercent
filterValue: Integer
inverted: true || false Default: false
customGrid: [[1,3,5]] (1st Driver P1; 3rd Driver P2, 5th Driver P3) (Array of heats in a "round" array)
gridType: fair, custom, magix, balanced, default

Filter examples:
topPercent, 50 (top 50%)
topNumber, 5 (top 5 participants)

Body of post is a JSON object:
{
	"participants": [ Array of Participant Objects as Documented Above ],
	"options": { Options as an Object }
}

*/

// TODO: Handle ties on # of firsts, etc or best laptime of previous round
// TODO: Handle multiple groups together (this means duplicate start/finish positions)

/**
 * CREATE A ROUND OF HEATS WITH PARTICIPANTS IN EACH HEAT
 */
exports.create = function(method, participantsToGrid, opts) {
	var results = [];

	// Handle missing participants
	/*if(typeof participantsToGrid !== "object" || participantsToGrid === null || participantsToGrid.length == 0)
		throw new Error('No participants given for gridding');*/

	// Set intelligent defaults
  opts = opts || {};
	opts.maxDrivers = opts.maxDrivers || 9999999;
	opts.numHeatsPerParticipant = opts.numHeatsPerParticipant || 1;
	opts.vehicleAssignmentType = opts.vehicleAssignmentType || 'none'; // none, random, same, fair
	opts.vehiclesAvailable = opts.vehiclesAvailable || []; // [1, 2, 3, 4] (Can also be an array of strings)
	opts.gridType = opts.gridType || 'default'; // default, balanced, fair, custom, magix (how to arrange people in grids)
	//Other options but not sure we need to define: filterName, filterValue, maxDrivers, 

	/**
	 * HOW GRIDDING A ROUND WORKS
	 * 1. Sort the participants according to a sorting method... end up with an array of participant objects
	 * 2. Filter these drivers if we want to reduce the results (Ex. Get rid of everyone but the "top 10")
	 * 3. Get the grid lineup template for the round (which participants #s are in which heats/positions)... end up with an array of heats that contain driver position #s
	 * 4. Match the drivers and grid together to return a lineup for the round
	 */

	switch(method) {

		/**
		 * Support for some legacy "griddingMethods" that were really a hardcoded pair
		 * of a particular griddingMethod & griddingType.
		 */
		
		case 'balanced': // Legacy call
			if(method == 'balanced') {
				method = 'mostPoints';
				opts.gridType = 'balanced';
			}
			
		case 'magix': // Legacy call
			if(method == 'magix') {
				method = 'noGrid';
				opts.gridType = 'magix';
			}

		case 'custom':
			if(method == 'custom') {
				var customGrid = opts.customGrid || [[]]; // TODO Validate this custom grid?
				method = 'noGrid';
				opts.gridType = 'custom';
			}

		case 'startingPosition':
		case 'finishingPosition':
		case 'bestLapTime':
		case 'bestAverageLapTime':
		case 'mostPoints':
		case 'randomized':
		case 'noGrid':
			var sortedDrivers = this[method](participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			var heatLineup = this.createGrid(opts.gridType, sortedDrivers.length, opts.maxDrivers, opts);
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;

		/* TODO
		case 'previousGridPositionPlusOne':
			var sortedDrivers = previousGridPositionPlusOne(participantsToGrid, opts);
			sortedDrivers = sortedDrivers.slice(0, this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers.length));
			var heatLineup = this.createGrid(opts.gridType, sortedDrivers.length, opts.maxDrivers);
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;*/

		case 'fairTeams': // Sort into teams
			method = 'mostPoints';
			opts.gridType = 'custom';

			var sortedDrivers = this[method](participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			opts.customGrid = this.fair(sortedDrivers.length, opts.maxDrivers);
			var heatLineup = this.createGrid(opts.gridType, sortedDrivers.length, opts.maxDrivers, opts);
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);

			// Massage array into better format
			var decoratedResults = [];
			results.forEach(function(group, groupNum) {
				decoratedResults[groupNum] = { teamIndex: groupNum, teamResults: {}, drivers: group || [] };

				var fastestDriver = null;
				var res = { vehicleId: null, points: null, bestAverageLapTime: null, bestLapTime: null, startingPosition: null, finishingPosition: null };
				group.forEach(function(driver, i) {
					driverOriginalData = driver.originalData;
					if(!fastestDriver || fastestDriver.bestLapTime > driverOriginalData.bestLapTime) {
						fastestDriver = driverOriginalData;
						res.vehicleId = driverOriginalData.vehicleId;
						res.bestLapTime = driverOriginalData.bestLapTime;
					}
					driver.startingPosition = i+1;
					res.bestAverageLapTime += driverOriginalData.bestAverageLapTime;
					res.points += driverOriginalData.points;
					res.startingPosition += driverOriginalData.startingPosition;
					res.finishingPosition += driverOriginalData.finishingPosition;
				});
				res.bestAverageLapTime = res.bestAverageLapTime / decoratedResults[groupNum].drivers.length;
				decoratedResults[groupNum].teamResults = res;
			});
			return decoratedResults;
			break;

		default:
			throw new Error('Invalid gridding sort method given: ' + method);
			break;
	}
	
	/**
	 * Handle assigning vehicles to participants
	 */

	// Good spot to handle kart assignment? No assignment, random, same, fair
	// This may present a problem in the 'meta' gridding types like Magix that give multiple heats per person
	if(opts.vehicleAssignmentType && opts.vehicleAssignmentType !== 'none') {
		
		// Create list of vehicles available from participants if one was not provided
		if(opts.vehiclesAvailable.length === 0 || (opts.vehicleAssignmentType === 'fair' || opts.vehicleAssignmentType === 'same')) {
			opts.vehiclesAvailable = []; // Reset the array
			sortedDrivers.forEach(function(participant) {
				if(participant.originalData.vehicleId) opts.vehiclesAvailable.push(participant.originalData.vehicleId);
			});
		}
		
		switch(opts.vehicleAssignmentType) {
			case 'random':
				// Improvement -- could potentially improve this by using all available karts up instead of resetting
				//                the array each time. This would allow karts to leave the pits as the previous
				//                round is coming in.
				results.forEach(function(group, groupNum) {
					var vehiclesAvailableCopy = JSON.parse(JSON.stringify(shuffle(opts.vehiclesAvailable)));
					group.forEach(function(participant, participantNum) {
						var vehicle = vehiclesAvailableCopy.splice(0, 1)[0];
						results[groupNum][participantNum].vehicleId = vehicle || null;
					});
				});
				break;
			case 'same':
				results.forEach(function(group, groupNum) {
					group.forEach(function(participant, participantNum) {
						results[groupNum][participantNum]['vehicleId'] = participant.originalData.vehicleId || null;
					});
				});
				break;
			case 'fair':
				var vehiclesAvailableCopy = JSON.parse(JSON.stringify(opts.vehiclesAvailable.reverse()));
				results.forEach(function(group, groupNum) {
					group.forEach(function(participant, participantNum) {
						var vehicle = vehiclesAvailableCopy.splice(0, 1)[0];
						results[groupNum][participantNum].vehicleId = vehicle || null;
					});
				});
				break;
			case 'nextAvailable':
				results.forEach(function(group, groupNum) {
					var vehiclesAvailableCopy = JSON.parse(JSON.stringify(opts.vehiclesAvailable));
					group.forEach(function(participant, participantNum) {
						var vehicle = vehiclesAvailableCopy.splice(0, 1)[0];
						results[groupNum][participantNum].vehicleId = vehicle || null;
					});
				});
				break;
			case 'nextAvailableWithoutReset':
				/*
				// IN PROGRESS
				- Start with copy of list
				- Loop the groups
				- Find next kart that's not assigned
				*/
				results.forEach(function(group, groupNum) {
					var vehiclesAvailableCopy = JSON.parse(JSON.stringify(opts.vehiclesAvailable));
					var vehiclesAssignedToGroup = [];
					group.forEach(function(participant, participantNum) {
						
						var vehicle = vehiclesAvailableCopy.splice(0, 1)[0];
						results[groupNum][participantNum].vehicleId = vehicle || null;
					});
				});
				break;
			default:
				throw new Error('Invalid vehicle assignment method given: ' + opts.vehicleAssignmentType);
				break;
		}
	}
	
	/**
	 * Handle creating multiple copies of a heat
	 */
	
	// Create copies if we've specificed more than one heat per participant
	var numHeats = opts.numHeatsPerParticipant;
	for(var i = 1; i < numHeats; i++) {
		opts.numHeatsPerParticipant = 0;
		var copy = this.create(method, participantsToGrid, opts);
		results = results.concat(copy);
	}
	
	return results;
}


/*
////
// Previous grid position + 1 (puts next person on pole) (or inverted)
////
function previousGridPositionPlusOne(array, opts) {
	
	// Include an error if positions aren't 1..N?
	// If we combine two groups you have two firsts, two seconds, etc. Doesn't make any sense
	
	console.log(array);
	
	var nextLineup = [];
	var inverted = opts.inverted || false;

	array.forEach(function(person) {
		person.startingPosition = person.startingPosition || array.length;
		if(inverted) {
			newPosition = person.startingPosition == 1 ? array.length : person.startingPosition - 1;
		} else {
			newPosition = person.startingPosition == array.length ? 1 : person.startingPosition + 1;
		}
		nextLineup.push({ participantId: person.participantId, startingPosition: newPosition, originalData: person });
	});
	nextLineup.sort(function(obj1, obj2) {
		return obj1.startingPosition - obj2.startingPosition;
	});
	return nextLineup;
}
*/

////
// OLDER Magix Lineup (only works when # racers per race == # races per driver)
////

function createOldMagixRoundLineup(numDriversTotal, numDriversPerHeat, numRoundsTotal) {
   // Find grid lineup
   var startingGrid = createStartingLineup(numDriversPerHeat, numDriversTotal);
   var numRoundsTotal = typeof numRoundsTotal === 'undefined' ? numDriversTotal : numRoundsTotal;

   var eventRounds = [];

   for(var currentRoundNumber = 1; currentRoundNumber <= numRoundsTotal; currentRoundNumber++) {
       // Create a copy of the starting grid to shift
       var roundLineup = startingGrid.slice(0);
       
       for(var i = currentRoundNumber; i > 1; i--) {
           
           // Take last position
           var lastPosition = roundLineup.pop();
           
           // Put it first
           roundLineup.unshift(lastPosition);
           
       }
       
       // Convert to the proper grid lineup... [ 1, null, 3, null, 2 ] -> [1, 5, 3]
       var modifiedRoundLineup = [];
       roundLineup.forEach(function(position, driverNum) {
           if(position == null) return;
           modifiedRoundLineup[position - 1] = driverNum + 1;
       });

       // Put into the lineup
       eventRounds.push(modifiedRoundLineup);
   }
   
   return eventRounds;
   
   function createStartingLineup(numDriversPerHeat, numDriversTotal) {
       var startingLineup = [1];
       var gapsRemaining = numDriversTotal - numDriversPerHeat;
   
       for(var nextDriverPosition = numDriversPerHeat; nextDriverPosition > 1; nextDriverPosition--) {
   
           var gapsToInsert = Math.ceil(gapsRemaining / nextDriverPosition);
           gapsRemaining = gapsRemaining - gapsToInsert;
   
           for(var i = gapsToInsert; i > 0; i--) {
               startingLineup.push(null);
           }
   
           startingLineup.push(nextDriverPosition);
   
       }
       
       for(var i = gapsRemaining; i > 0; i--) {
           startingLineup.push(null);
       }
       
       return startingLineup;
   
   }
   
}

////
// Magix Lineup
////
exports.magix = function magix(numDrivers, numDriversPerRace, numRacesPerDriver) {

	// Use older, perfectly balanced Magix logic when # racers per race == # races per driver
	if(numDriversPerRace == numRacesPerDriver) {
		var numRoundsTotal = (numDrivers * numRacesPerDriver) / numDriversPerRace; // The old method always has the same number of races as drivers
		return createOldMagixRoundLineup(numDrivers, numDriversPerRace, numRoundsTotal);
	}
 
  var grid = [];
  var targetPositionSummation = Math.ceil((numDriversPerRace/2)*numRacesPerDriver) + 1;
	var positions = [];
	var racers = [];

	// Create the grid lineup
	var gridLineup = this.createBalancedGrid(numDrivers*numRacesPerDriver, Math.min(numDrivers, numDriversPerRace));
	gridLineup.sort(function(a, b) { return b.length - a.length });
	
	// Create the list of positions we need to assign (and clean up gridLineup)
	gridLineup.forEach(function(race, index) {
		for(i = 1; i <= race.length; i++) {
			gridLineup[index][i-1] = null
			positions.push(i);
		}
	});

	// Assign the first round of positions -- this assigns pole to unique people
	positions.sort(function(a, b) { return a - b; });
	for(i = 0; i < numDrivers; i++) {
		racers[i] = { num: i+1, positions: [positions.shift()] }; // Give first position
	}

	// Assign the remaining positions, try to make everyone meet the target position summation
	for(i = 0; i < numDrivers; i++) {
		for(j = 1; j < numRacesPerDriver; j++) {			
			var total = racers[i].positions.reduce(function(a, b) { return a + b; });			
			var goal = Math.ceil((targetPositionSummation - total)/(numRacesPerDriver - j));
			var closest = null;
			
			var idx = positions.indexOf(goal);
			racers[i].positions.push(positions.splice(idx,1)[0]);
		}
	}

	// Add some randomization to racers and their positions
	racers.sort(function() { return 0.5 - Math.random() });


	/**
	 * Fit participants into grids
	 */

	// Fill the grid lineup with racers that have this position (that were not in previous round)
	var racersInPreviousRound = [];
  var gridLineupOriginal = JSON.parse(JSON.stringify(gridLineup));
	var racersOriginal = JSON.parse(JSON.stringify(racers));

	for(var i = 0; i < gridLineup.length; i++) {
		
		var count = 0; // Count our iterations so we know if we need to invoke super-kludge logic

		while(gridLineup[i].indexOf(null) !== -1) {
			// Find first null and try to fill.
			var positionIndex = gridLineup[i].indexOf(null);
			var positionToFill = positionIndex + 1;
			
			// Randomize racers
			racers.sort(function() { return 0.5 - Math.random() });
			
			// Find person in pool of racers, matching position that isn't in exclusion list
			var foundRacer = findRacerByPosition(positionToFill, racers, racersInPreviousRound, gridLineup[i]);
			
			// Assign racer (or handle conflict)
			if(foundRacer !== null) { 
				gridLineup[i][positionIndex] = foundRacer;
			} else { // BEGIN THE KLUDGE TO BRUTE FORCE OUR WAY THROUGH CONFLICTS
				var spotsToGiveBack = [];
				gridLineup[i].forEach(function(ele, idx) {
					gridLineup[i][idx] = null;
					racersInPreviousRound = [];
					spotsToGiveBack.push({ num: ele, position: (idx+1 )});
				});
				spotsToGiveBack.forEach(function(spotToGiveBack) {
					racers.forEach(function(racer, i) {
						if(racer.num == spotToGiveBack.num) {
							racers[i].positions.push(spotToGiveBack.position);
						}
					});
				});
			}
			
			// MOST SPECTACULAR HACK AND SUPER-KLUDGE EVER IF BRUTE FORCE ABOVE FAILS...
			if(count === gridLineup.length * (numDriversPerRace + 10)) { // We're giving up and starting all over
				i = 0;
				gridLineup = JSON.parse(JSON.stringify(gridLineupOriginal));
				racers = JSON.parse(JSON.stringify(racersOriginal));
				racers.sort(function() { return 0.5 - Math.random() });
				racersInPreviousRound = [];
			}
			
			count++;
		}
		
		// Handle the exclusion list (shouldn't race in back to back rounds unless our grid lineup is too small)
		if(numDriversPerRace * 2 > numDrivers) {
			racersInPreviousRound = []; // Our grid lineup is too small to not have racers run back to back
		} else if(i > 0) {
			racersInPreviousRound.splice(0, gridLineup[i-1].length); // Remove the previous round's excluded racers
		} 

  }
  
  return gridLineup;
	
	/**
	 * Find a racer with this starting position (and remove that position from him)
	 */
	function findRacerByPosition(startingPosition, availableRacers, excludedRacers, gridLineup) {
		var racerNumber;
		
		for(var idx = 0; idx < availableRacers.length; idx++) {
			positionLocation = availableRacers[idx].positions.indexOf(startingPosition);
	
			if(positionLocation !== -1 && excludedRacers.indexOf(availableRacers[idx].num) == -1) { 
				availableRacers[idx].positions.splice(positionLocation, 1); // Remove this position from the racer
				excludedRacers.push(availableRacers[idx].num);
				return availableRacers[idx].num; // Return the racer number with this position
			}
		}
	
		return null;
	}
}


function createBalancedGrid(numParticipants, maxParticipantsPerGroup) {
	
	// Handle max participants as an object... used for percentage
	if(typeof maxParticipantsPerGroup === 'object') {
		maxParticipantsPerGroup = Math.ceil(numParticipants * (maxParticipantsPerGroup.percent / 100));
	}
	
	maxParticipantsPerGroup = maxParticipantsPerGroup || numParticipants;
	var balancedGrid = balanceParticipants(numParticipants, maxParticipantsPerGroup); // returns [ 3, 3, 2, 2 ]
	var roundLineup = [];
	var participantNumber = 1;
	
	balancedGrid.forEach(function(numInHeat) {
		var heat = [];
		
		for(i = 0; i < numInHeat; i++) {
			heat.push(participantNumber);
			participantNumber++;
		}
		
		roundLineup.push(heat);
	});
	
	return roundLineup;
}


/**
 * Balanced lineup
 */

exports.balanced = function(numDriversTotal, numDriversPerHeat) {
	var results = [];
	var groups = this.createBalancedGrid(numDriversTotal, numDriversPerHeat);
	for(var i = 0; i < numDriversTotal; i++) {
		var groupNum = i % groups.length;
		if(typeof results[groupNum] === 'undefined') results[groupNum] = [];
		results[groupNum].push(i+1);
	}
	return results;
}

/**
 * Fair lineup -- Create even groups where the best are paired with the worst. P1 & P4 == P2 & P3
 * IMPORTANT -- We're making teams, not gridding a race. This is a departure from how this class works.
 */

exports.fair = function(numDriversTotal, numDriversPerHeat) {
	var fairGroups = [];
	var numGroups = Math.ceil(numDriversTotal / numDriversPerHeat);
	var groups = this.createBalancedGrid(numDriversTotal, numDriversPerHeat);

	var drivers = [];
	for(var i = 1; i <= numDriversTotal; i++) {
		drivers.push(i);
	}
	
	groups.forEach(function(group, groupNum) {
	    fairGroups[groupNum] = [];
	    
        /*
        // Original, less balanced algorithm
        for(var i = (numDriversPerHeat-1); i >= 0; i--) {
	        if(drivers.length <= 0) return;

	        var pct = (i / (numDriversPerHeat-1));
	        var pos = Math.floor(pct * (drivers.length-1));
	        fairGroups[groupNum].push(drivers.splice(pos, 1));
	    }*/

        // More balanced algorithm
        var positions = [];
        for(var i = (numDriversPerHeat-1); i >= 0; i--) {
            if(drivers.length === 0) return;

            var pct = (i / (numDriversPerHeat-1));
            positions.push(Math.floor(pct * (drivers.length-1)));
        }
        positions.forEach(function(pos) {
            fairGroups[groupNum].push(drivers.splice(pos, 1));
        });
	});

	// Give the sort order we expect (reversing the decrementing for loop above)
	fairGroups.forEach(function(group, i) {
		var group = fairGroups[i].reverse();
	});

	return fairGroups;
}


////
// Previous starting grid position (or inverted)
////
exports.startingPosition = function startingPosition(array, opts) {
	var nextLineup = [];
	var inverted = opts.inverted || false;

	array.sort(function(obj1, obj2) {
		obj1.startingPosition = isNumeric(obj1.startingPosition) ? obj1.startingPosition : 9999999;
		obj2.startingPosition = isNumeric(obj2.startingPosition) ? obj2.startingPosition : 9999999;
		result = obj1.startingPosition - obj2.startingPosition;
		if(result == 0) { // Break tie on best laptime
			result = obj1.bestLapTime - obj2.bestLapTime;
		}
		return result;
	});

	if(inverted) array.reverse();
	
	array.forEach(function(person, index) {
		newPosition = index + 1;
		nextLineup.push({ participantId: person.participantId, startingPosition: newPosition, originalData: person });
	});
	
	return nextLineup;
}


////
// Previous finishing position (or inverted)
////
exports.finishingPosition = function finishingPosition(array, opts) {
	var nextLineup = [];
	var inverted = opts.inverted || false;

	array.sort(function(obj1, obj2) {
		obj1.finishingPosition = isNumeric(obj1.finishingPosition) ? obj1.finishingPosition : 9999999;
		obj2.finishingPosition = isNumeric(obj2.finishingPosition) ? obj2.finishingPosition : 9999999;
		result = obj1.finishingPosition - obj2.finishingPosition;
		if(result == 0) { // Break tie on best laptime
			result = obj1.bestLapTime - obj2.bestLapTime;
		}
		return result;
	});

	if(inverted) array.reverse();

	array.forEach(function(person, index) {
		newPosition = index + 1;
		nextLineup.push({ participantId: person.participantId, startingPosition: newPosition, originalData: person });
	});
	
	return nextLineup;
}


////
// Best laptime (or inverted)
////
exports.bestLapTime = function bestLapTime(array, opts) {
	var inverted = opts.inverted || false;
	var nextLineup = [];
	
	// Sort
	array.sort(function(obj1, obj2) {
		obj1.bestLapTime = isNumeric(obj1.bestLapTime) ? obj1.bestLapTime : 9999999;
		obj2.bestLapTime = isNumeric(obj2.bestLapTime) ? obj2.bestLapTime : 9999999;
		return inverted ? obj2.bestLapTime - obj1.bestLapTime : obj1.bestLapTime - obj2.bestLapTime;
	});
	
	// Create lineup
	array.forEach(function(person, index) {
		nextLineup.push({ participantId: person.participantId, startingPosition: index + 1, originalData: person });
	});
	
	return nextLineup;
}

////
// Best average laptime (or inverted)
////
exports.bestAverageLapTime = function bestAverageLapTime(array, opts) {
	var inverted = opts.inverted || false;
	var nextLineup = [];
	
	// Sort
	array.sort(function(obj1, obj2) {
		obj1.bestAverageLapTime = isNumeric(obj1.bestAverageLapTime) ? obj1.bestAverageLapTime : 9999999;
		obj2.bestAverageLapTime = isNumeric(obj2.bestAverageLapTime) ? obj2.bestAverageLapTime : 9999999;
		return inverted ? obj2.bestAverageLapTime - obj1.bestAverageLapTime : obj1.bestAverageLapTime - obj2.bestAverageLapTime;
	});
	
	// Create lineup
	array.forEach(function(person, index) {
		nextLineup.push({ participantId: person.participantId, startingPosition: index + 1, originalData: person });
	});
	
	return nextLineup;
}

////
// Most points (or inverted)
////
exports.mostPoints = exports.mostPoints = function(array, opts) {
	var inverted = opts.inverted || false;
	var nextLineup = [];
	
	// Sort
	array.sort(function(obj1, obj2) {
		if(obj1.points !== 0) obj1.points = isNumeric(obj1.points) ? obj1.points : -9999999;
		if(obj2.points !== 0) obj2.points = isNumeric(obj2.points) ? obj2.points : -9999999;
		var result = inverted ? obj1.points - obj2.points : obj2.points - obj1.points;
		if(result == 0) { // Break tie on best laptime
			result = obj1.bestLapTime - obj2.bestLapTime; // inverted? obj2.bestLapTime - obj1.bestLapTime : 
		}
		return result;
	});
	
	// Create lineup
	array.forEach(function(person, index) {
		nextLineup.push({ participantId: person.participantId, startingPosition: index + 1, originalData: person });
	});

	return nextLineup;
}

////
// No gridding
////
exports.noGrid = function noGrid(array, opts) {
	var inverted = opts.inverted || false;
	var nextLineup = [];
	
	// Invert? Not for "custom", however -- for custom we allow the "custom" method to reverse the lineup of the heat, not the drivers
	if(inverted && opts.gridType !== 'custom') array.reverse();
	
	// Create lineup
	array.forEach(function(person, index) {
		nextLineup.push({ participantId: person.participantId, startingPosition: index + 1, originalData: person });
	});

	return nextLineup;
}

////
// Random
////
exports.randomized = function randomized(array, opts) {
	var nextLineup = [];
	
	// Randomzie array
	shuffle(array);
	
	// Create lineup
	array.forEach(function(person, index) {
		nextLineup.push({ participantId: person.participantId, startingPosition: index + 1, originalData: person });
	});
	
	return nextLineup;
}

exports.balanceParticipants = function(numParticipants, maxParticipantsPerGroup) {
	var numGroups = Math.ceil(numParticipants / maxParticipantsPerGroup);
	var participantGroupings = [];
	var participantsToAssign = numParticipants;

	for(var numGroupsToAssign = numGroups; numGroupsToAssign > 0; numGroupsToAssign--) {
		var groupSize = Math.ceil(participantsToAssign / numGroupsToAssign);
		participantGroupings.push(groupSize);
		participantsToAssign -= groupSize;
	}
	
	return participantGroupings;
}

exports.createGrid = function(gridType, numParticipants, maxParticipantsPerGroup, opts) {
	var gridLineup = [[]];

	switch(gridType) {
		case 'fair':
			gridLineup = this.fair(numParticipants, maxParticipantsPerGroup);
			break;
		case 'custom':
			if(opts.inverted === true) {
				opts.inverted = false;
				var invertedGridLineup = [];
				opts.customGrid.forEach(function(heat) {
					invertedGridLineup.push(heat.reverse());
				});
				gridLineup = invertedGridLineup;
			} else {
				gridLineup = opts.customGrid;
			}
			gridLineup = opts.customGrid;
			break;
		case 'magix':
			gridLineup = this.magix(numParticipants, maxParticipantsPerGroup, opts.numHeatsPerParticipant);
			opts.numHeatsPerParticipant = 1; // Overriding the heat copying functionality
			break;
		case 'balanced':
			gridLineup = this.balanced(numParticipants, maxParticipantsPerGroup);
			break;
		case 'default':
		default:
			gridLineup = this.createBalancedGrid(numParticipants, maxParticipantsPerGroup);
			break;
	}
	return gridLineup;
}

exports.createBalancedGrid = function(numParticipants, maxParticipantsPerGroup) {
	
	// Handle max participants as an object... used for percentage
	if(typeof maxParticipantsPerGroup === 'object') {
		maxParticipantsPerGroup = Math.ceil(numParticipants * (maxParticipantsPerGroup.percent / 100));
	}
	
	maxParticipantsPerGroup = maxParticipantsPerGroup || numParticipants;
	var balancedGrid = this.balanceParticipants(numParticipants, maxParticipantsPerGroup); // returns [ 3, 3, 2, 2 ]
	var roundLineup = [];
	var participantNumber = 1;
	
	balancedGrid.forEach(function(numInHeat) {
		var heat = [];
		
		for(i = 0; i < numInHeat; i++) {
			heat.push(participantNumber);
			participantNumber++;
		}
		
		roundLineup.push(heat);
	});
	
	return roundLineup;
}

exports.bindParticipantsToHeatLineup = function(sortedParticipants, roundOfHeats) {
	var results = [];
	
	roundOfHeats.forEach(function(heat) {
		var heatLineup = [];
		heat.forEach(function(participantIndex, heatIndex) {

			// If this participant doesn't exist, do not include them
			if(sortedParticipants[participantIndex - 1] === undefined) {
				return;
			}
			
			var participant = JSON.parse(JSON.stringify(sortedParticipants[participantIndex - 1])); // Make a copy of this object -- a hack?
			participant.startingPosition = heatIndex + 1;
			heatLineup.push(participant);
		});
		results.push(heatLineup);
	});

	return results;
}

exports.filterParticipantsInRound = function(filterName, filterValue, participants) {
	var numParticipantsInRound
	  , numParticipants = participants.length;
	
	if(typeof filterName === 'undefined' || typeof filterValue === 'undefined') filterName = 'default';
	
	switch(filterName) {
		case 'topNumber':
			numParticipantsInRound = filterValue > numParticipants ? numParticipants : filterValue;
			participants = participants.slice(0, numParticipantsInRound);
			break;
		case 'topPercent':
			numParticipantsInRound = Math.ceil(numParticipants * (filterValue/100));
			participants = participants.slice(0, numParticipantsInRound);
			break;
		case 'bestLapWithinPercentOfFastest':
			var bestLapTime = 999999
				, cutoffTime  = 999999;
	
			// Find best laptime
			participants.forEach(function(participant) {
				bestLapTime = (participant.bestLapTime < bestLapTime)
												? participant.bestLapTime
												: bestLapTime;
			});
			
			// Ex. 107% rule, times have to be within 107% of best laptime
			cutoffTime = (filterValue / 100) * bestLapTime;
			
			// Remove participants slower than the cutoff
			for (i = 0; i < participants.length; ++i) {
					if (participants[i].bestLapTime > cutoffTime) {
							participants.splice(i--, 1);
					}
			}
			break;
		default:
			// By default just return participants (unfiltered)
			participants = participants;
			break;
	}

	return participants;
}

// This probably belongs in another class. :-)
exports.assignPoints = function(participants, scoringTemplate) {
	participants.forEach(function (participant, index) {
		// Use the last point value in the template for remaining participants 
		//var points = (index + 1) > scoringTemplate.length ? scoringTemplate[scoringTemplate.length - 1] : scoringTemplate[index];
		var points = (index + 1) > scoringTemplate.length ? 0 : scoringTemplate[index];
		participants[index]['points'] = points;
	});
	
	return participants;
}

function isNumeric(n) {
  return !isNaN(parseFloat(n)) && isFinite(n);
}

function shuffle(array) {
	var currentIndex = array.length
		, temporaryValue
		, randomIndex;

	// While there remain elements to shuffle...
	while (0 !== currentIndex) {

		// Pick a remaining element...
		randomIndex = Math.floor(Math.random() * currentIndex);
		currentIndex -= 1;

		// And swap it with the current element.
		temporaryValue = array[currentIndex];
		array[currentIndex] = array[randomIndex];
		array[randomIndex] = temporaryValue;
	}

	return array;
}