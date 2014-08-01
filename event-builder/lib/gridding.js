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

Gridding Options: X=DONE -=IN PROGRESS
X Random (In Event, Round(s), Group(s))
X Round Robin (In Event, Round(s), Group(s))
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

Lineup Type: Random, Starting Position, Finishing Position, Points, Fastest LapTime, Average LapTime, Magix, No gridding

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


curl -i -X POST -H "Content-Type: application/json" -d '{ "participants": [{"name":"Wes","points":5,"bestAverageLapTime":31,"startingPosition":3,"finishingPosition":5},{"name":"Glenda","points":3,"bestAverageLapTime":33,"bestLapTime":33.234,"startingPosition":2,"finishingPosition":4},{"name":"Max","points":3,"bestAverageLapTime":35,"bestLapTime":33.536,"startingPosition":1,"finishingPosition":3},{"name":"Tommy","points":2,"bestAverageLapTime":34,"bestLapTime":36.234,"startingPosition":4,"finishingPosition":2},{"name":"Shakib","points":0,"bestAverageLapTime":32,"bestLapTime":31.234,"startingPosition":5,"finishingPosition":1}], "options": { "maxDrivers": 3 } }' http://127.0.0.1:8000/grid/bestLapTime

Output: (array of heats with participants in grid order)
[[{"name":"Shakib","startingPosition":1,"originalData":{"name":"Shakib","points":0,"bestAverageLapTime":32,"bestLapTime":31.234,"startingPosition":5,"finishingPosition":1}},{"name":"Glenda","startingPosition":2,"originalData":{"name":"Glenda","points":3,"bestAverageLapTime":33,"bestLapTime":33.234,"startingPosition":2,"finishingPosition":4}},{"name":"Max","startingPosition":3,"originalData":{"name":"Max","points":3,"bestAverageLapTime":35,"bestLapTime":33.536,"startingPosition":1,"finishingPosition":3}}],[{"name":"Wes","startingPosition":1,"originalData":{"name":"Wes","points":5,"bestAverageLapTime":31,"bestLapTime":35.234,"startingPosition":3,"finishingPosition":5}},{"name":"Tommy","startingPosition":2,"originalData":{"name":"Tommy","points":2,"bestAverageLapTime":34,"bestLapTime":36.234,"startingPosition":4,"finishingPosition":2}}]]


__API DOCS__

POST to http://192.168.111.103/grid/:griddingType

Where :griddingType is:
- roundRobin (Options: numGroups)
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
numGroups: Integer (valid for roundRobin)
inverted: true || false Default: false
customGrid: [[1,3,5]] (1st Driver P1; 3rd Driver P2, 5th Driver P3) (Array of heats in a "round" array)

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

	// Force opt_options to be an object
  opts = opts || {};
	opts.maxDrivers = opts.maxDrivers || 9999999;
	opts.numHeatsPerParticipant = opts.numHeatsPerParticipant || 1;
	opts.vehicleAssignmentType = opts.vehicleAssignmentType || 'none'; // none, random, same, fair
	opts.vehiclesAvailable = opts.vehiclesAvailable || []; // [1, 2, 3, 4] (Can also be an array of strings)
	//Other options but not sure we need to define: filterName, filterValue, maxDrivers, 

	// Take in type of sort method, all drivers, any options (Max Drivers, Sorting Options (inverted), etc)

	// To create the lineups for a round, there are a couple steps...
	// 1. Sort the participants according to a sorting method... end up with an array of participant objects
	// 2. Get the grid lineup template for the round (which participants #s are in which heats/positions)... end up with an array of heats that contain driver position #s
	// 3. Match #1 and #2 together and return a lineup

	switch(method) {
		/*case 'previousGridPositionPlusOne':
			var sortedDrivers = previousGridPositionPlusOne(participantsToGrid, opts);
			sortedDrivers = sortedDrivers.slice(0, this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers.length));
			var heatLineup = this.createBalancedGrid(sortedDrivers.length, opts.maxDrivers);
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;*/
			
		case 'roundRobin':
			var sortedDrivers = bestLapTime(participantsToGrid, opts); // TODO Make this by other items than lap time? module['bestLapTime'](participantsToGrid, opts)
			opts.numGroups = this.createBalancedGrid(sortedDrivers.length, opts.maxDrivers);
			results = roundRobin(sortedDrivers, opts);
			break;

		case 'startingPosition':
			var sortedDrivers = startingPosition(participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			var heatLineup = this.createBalancedGrid(sortedDrivers.length, opts.maxDrivers);
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;
			
		case 'finishingPosition':
			var sortedDrivers = finishingPosition(participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			var heatLineup = this.createBalancedGrid(sortedDrivers.length, opts.maxDrivers);
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;
			
		case 'bestLapTime':
			var sortedDrivers = bestLapTime(participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			var heatLineup = this.createBalancedGrid(sortedDrivers.length, opts.maxDrivers);
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;
			
		case 'bestAverageLapTime':
			var sortedDrivers = bestAverageLapTime(participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			var heatLineup = this.createBalancedGrid(sortedDrivers.length, opts.maxDrivers);
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;
			
		case 'mostPoints':
			var sortedDrivers = this.mostPoints(participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			var heatLineup = this.createBalancedGrid(sortedDrivers.length, opts.maxDrivers);
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;
		
		case 'randomized':
			var sortedDrivers = this.randomized(participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			var heatLineup = this.createBalancedGrid(sortedDrivers.length, opts.maxDrivers);
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;
			
		case 'magix':
			var sortedDrivers = this.noGrid(participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			var heatLineup = this.createMagixRoundLineup(sortedDrivers.length, opts.maxDrivers);			
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;

		case 'fair':
			var sortedDrivers = finishingPosition(participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			var heatLineup = this.fair(sortedDrivers.length, opts.maxDrivers);
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;
			
		case 'balanced':
			var sortedDrivers = finishingPosition(participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			var heatLineup = this.balanced(sortedDrivers.length, opts.maxDrivers);
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;
			
		case 'magixFair':
			var sortedDrivers = this.noGrid(participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			var heatLineup = this.createMagixFairRoundLineup(sortedDrivers.length, 12, opts.numHeatsPerParticipant); // numDriversTotal, numRoundsTotal, numRacesPerRacer
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;
		
		case 'custom':
			var inverted = opts.inverted || false;
			var sortedDrivers = this.noGrid(participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			var heatLineup = opts.customGrid || [[]]; // TODO Validate this custom grid?	
			
			if(inverted) {
				heatLineup.forEach(function(heat, i) {
					heatLineup[i] = heatLineup[i].reverse();
				});
			}
			
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;

		case 'noGrid':
			var sortedDrivers = this.noGrid(participantsToGrid, opts);
			sortedDrivers = this.filterParticipantsInRound(opts.filterName, opts.filterValue, sortedDrivers);
			var heatLineup = this.createBalancedGrid(sortedDrivers.length, opts.maxDrivers);
			results = this.bindParticipantsToHeatLineup(sortedDrivers, heatLineup);
			break;

		default:
			throw new Error('Invalid gridding method given: ' + method);
			break;
	}
	
	/**
	 * Handle assigning vehicles to participants
	 */

	// Good spot to handle kart assignment? No assignment, random, same, fair
	// This may present a problem in the 'meta' gridding types like Magix that give multiple heats per person
	if(opts.vehicleAssignmentType !== 'none') {
		
		// Create list of vehicles available from participants if one was not provided
		if(opts.vehiclesAvailable.length == 0) {
			sortedDrivers.forEach(function(participant) {
				if(typeof participant.originalData.vehicleId !== 'undefined') opts.vehiclesAvailable.push(participant.originalData.vehicleId);
			});
		}
		
		switch(opts.vehicleAssignmentType) {
			case 'random':
				opts.vehiclesAvailable = shuffle(opts.vehiclesAvailable);
				results.forEach(function(group, groupNum) {
					group.forEach(function(participant, participantNum) {
						var vehicle = opts.vehiclesAvailable.splice(0, 1)[0];
						results[groupNum][participantNum].vehicleId = vehicle; // if(typeof vehicle !== 'undefined')
					});
				});
				break;
			case 'same':
				results.forEach(function(group, groupNum) {
					group.forEach(function(participant, participantNum) {
						results[groupNum][participantNum]['vehicleId'] = participant.originalData.vehicleId;
					});
				});
				break;
			case 'fair':
				opts.vehiclesAvailable.reverse();
				results.forEach(function(group, groupNum) {
					group.forEach(function(participant, participantNum) {
						var vehicle = opts.vehiclesAvailable.splice(0, 1)[0];
						results[groupNum][participantNum].vehicleId = vehicle; // if(typeof vehicle !== 'undefined') 
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
// Round Robin
////
function roundRobin(array, opts) {
	
	var nextLineup = [];
	var inverted = opts.inverted || false;
	var numGroups = opts.numGroups.length || 1;

	array.forEach(function(person, personNum) {
		var groupNumber = personNum % numGroups;
		
		if(typeof nextLineup[groupNumber] === 'undefined') {
			nextLineup[groupNumber] = []
		}
		var newPosition = nextLineup[groupNumber].length + 1;
		nextLineup[groupNumber].push({ participantId: person.participantId, startingPosition: newPosition, originalData: person });
	});

	return nextLineup;
}


////
// Magix Fair Lineup
////
exports.createMagixFairRoundLineup = function(numDriversTotal, numRoundsTotal, numRacesPerRacer) {
	numDriversTotal = 22;
	numRoundsTotal = 12;
	numRacesPerRacer = 3;

	var eventRounds = [];
	
	var racersPerRace = (numDriversTotal * numRacesPerRacer) / numRoundsTotal;
	
	// Create the heats for the entire event
	var racesRemaining = numDriversTotal * numRacesPerRacer;
	for(var i = 0; i < numRoundsTotal; i++) {
		var racersInThisRound = Math.ceil(racesRemaining / (numRoundsTotal - i));
		racesRemaining -= racersInThisRound;
		eventRounds.push(racersInThisRound);
	}

	// Find total that summation of each racer's grid position should equal
	var targetPositionSum = Math.ceil((racersPerRace / 2) * numRacesPerRacer) + 1;
	
	// Create array of racers we are gridding
	var originalRacers = [];
	for(var i = 1; i <= numDriversTotal; i++) {
		originalRacers.push({ id: i, heats: [], positions: [], positionSum: 0, unassignedPositions: [] });
	}
	
	// Fill rounds with racers
	var racers = shuffle(originalRacers.slice(0)); // Randomize racers
	
	// This could be "subset sum", "bin packing"
	
	for(var eventRound = 0; eventRound < eventRounds.length; eventRound++) {
		var heatLineup = [];

		// Empty racers into slots and total up their heats, positions and position sum
		var numHeatsInRound = eventRounds[eventRound];
		var heat = new Array(numHeatsInRound);
		for(var gridPosition = 0; gridPosition < numHeatsInRound; gridPosition++) {
			if(racers.length > 0) {
				var racer = racers.shift();
				originalRacers[racer.id - 1].heats.push(eventRound);
				originalRacers[racer.id - 1].positions.push(gridPosition + 1);
				originalRacers[racer.id - 1].positionSum += (gridPosition + 1);
				heat[gridPosition] = (racer.id);
			} else {
			
				// When array is empty, TODO figure out who can be put into (not in last heat, if P1, no P1's). Randomize again?
				
				// Loop original racers, find first one who:
				// 1. Hasn't raced last heat (racer.heats.indexOf(eventRound - 1) === -1)
				// 2. If P1 then racer shouldn't have raced P1 before
				// 3. racer.positionSum < targetPositionSum
				
				// Loop original racers
				// targetPositionSum = 9
				// { id: 22, heats: [ 2 ], positions: [ 2 ], positionSum: 2 }
				// Add unassigned race positions 
				/*originalRacers.forEach(function(racer, index) {
					for(var i = racer.heats.length; i < numRacesPerRacer; i++) { // (numRacesPerRacer !== racer.heats.length + racer.unassignedPositions.length)
						var unassignedPosition = Math.ceil((targetPositionSum - racer.positionSum) / numRacesPerRacer - (racer.heats.length + racer.unassignedPositions.length));
						originalRacers[index].unassignedPositions.push(unassignedPosition);
						originalRacers[index].positionSum += unassignedPosition;
					}
				});*/
			}
		}
		
		eventRounds[eventRound] = heat;

	}
	
	///
	
	var availablePositions = [];
	eventRounds.forEach(function(heats) {
		for(i = 1; i <= heats.length; i++) {
			availablePositions.push(i);
		}
	});
	
	// Remove spots we are already using above
	availablePositions.splice(0, numDriversTotal);
	
	var approx = require('subset-sum');
	console.log('Subset Sum: ' + approx(availablePositions, targetPositionSum) + ' Target: ' + targetPositionSum);
	console.log(availablePositions);
	
	///
	
	var subset_sum = function(items, target) {
			var perms = [], layer = 0, depth = 4, attempts = 0, sum, perm,
			ss = function(items) {
					var item = items.shift();
					for (i = 0; i < items.length; i++) {
							attempts = attempts + 1;
							if (attempts <= items.length * items.length) {
									if (layer === 0) {
											perm = [items[0], items[i]];
									} else {
											perm = perms.shift();
											perm.push(items[0]);
									}
									sum = 0;
									for(j = 0;j < perm.length; j++){
											sum += perm[j];
									}
									perms.push(perm);
									if (sum == target){
											return perm;
									}
							} else {
									if (layer < depth) {
											attempts = 0;
											layer = layer + 1;
									} else {
											return null;
									}
							}
					}
					items.push(item);
					return ss(items);
			}
			return ss(items)
	}
	
	originalRacers.forEach(function(racer, i) {
		result = subset_sum(availablePositions, targetPositionSum - racer.positions[0]);
		originalRacers[i].positions = result.concat(originalRacers[i].positions);
		result.forEach(function(position) {
			var removed = availablePositions.splice(availablePositions.indexOf(position), 1);
		});
	});
	console.log(originalRacers);

	
	///
	
	//console.log(originalRacers);
	//console.log(eventRounds);
	
	return eventRounds;
	
	/****/
	
	var racerCount = numDriversTotal;
	var racesPerRacer = numRacesPerRacer;
	var totalRaces = numRoundsTotal;
	var racersPerRace = Math.floor((racerCount * racesPerRacer) / totalRaces);
	var targetTotalPosition = Math.ceil((racersPerRace / 2) * racesPerRacer);
	var _ = require('underscore');
	
	// Distribute slots across races.
	var grandTotalSlots = racerCount * racesPerRacer;
	var raceSlots = [];
	
	for(var i = 0; i < totalRaces; i++) {
		raceSlots.push(racersPerRace);
	}
	
	var raceNumber = 0
	
	_.each( raceSlots, function (race) {
		var sum = _.reduce(raceSlots, function(memo, num){ return memo + num; }, 0);
		if ( sum < grandTotalSlots) {
			raceSlots[raceNumber]++;
		}
		raceNumber++
	});
	
	//raceSlots = _.shuffle(raceSlots);
	
	//console.log('Race Slots');
	//console.log(raceSlots);
	
	var racers = [];
	
	//Set up racer array
	
	for( var i = 0; i < racerCount; i++) {
		
		var racer = {
			name : 'Racer ' + (i + 1),
			num: (i + 1),
			races : [],
			competitors : []
		}
		
		racers.push(racer);
	}
	
	
	var races = [];
	
	for ( var i = 0; i < totalRaces; i++) {
	
		var race = {
			raceNo : i + 1,
			slots :[]
		}
	
		races.push(race);
	}
	
	raceNumber = 0;
	
	_.each(races, function (race) {
	
		var eligibleDrivers = [];
		
		
		//Alternate list if we need to break previous competitor rule to fill races.
		var alternateDrivers = [];
		
		//Second alternate if we need to break the back to back rule.
		var secondAlternate = [];
		
		_.each(racers, function(racer) {
			
			//Check if racer is out of Races
			var outOfRaces = racer.races.length >= racesPerRacer;
			
			//Check if any competitors are in this race
			
			var prevCompetitorsInRace = false;
			
			_.each(race.slots, function(slot) {
				if (prevCompetitorsInRace == false) {
					prevCompetitorsInRace = _.contains(racer.competitors, slot.name);
				}
			});
	
			//Check if racer was in last race
			
			var inPrevRace = false;
			
			if( raceNumber > 0 ) {
				_.each(races[raceNumber - 1].slots, function(slot) {
					if( typeof slot != 'undefined') {
						if( slot.name == racer.name && inPrevRace == false) {
							inPrevRace = true;
						}
					}
				});
			}
			
			if( outOfRaces == false && prevCompetitorsInRace == false && inPrevRace == false ) {
				eligibleDrivers.push(racer);
			}
	
			if( outOfRaces == false && inPrevRace == false) {
				alternateDrivers.push(racer);
			}
	
			if( outOfRaces == false) {
				secondAlternate.push(racer);
			}
		}); 
	
		alternateDrivers = _.shuffle(alternateDrivers);
		
		secondAlternate = _.shuffle(secondAlternate);
		
		var numberOfSlots = raceSlots[raceNumber];
		
		if( eligibleDrivers.length < numberOfSlots ) {
		
			eligibleDrivers = _.union( eligibleDrivers, alternateDrivers);
		
		}
		
		if( eligibleDrivers.length < numberOfSlots ) {
		
			eligibleDrivers = _.union( eligibleDrivers, secondAlternate);
		
		}
		eligibleDrivers = _.shuffle(eligibleDrivers);
	
		for( var i = 0; i < numberOfSlots; i++) {
			race.slots.push(eligibleDrivers[i]);
			_.each(racers, function(racer) {
				if (typeof eligibleDrivers[i]  != 'undefined') { 
					if( eligibleDrivers[i].name == racer.name) {
						racer.races.push(race.raceNo);
					}
				}
			});
		}
			
			
		raceNumber++;
	});
	
	raceNumber = 0;
	
	_.each(races, function(race) {
		race.raceNo = raceNumber + 1;
		
		//$('#grid').append('<strong>Race ' + race.raceNo + '</strong><br/>');
		
		eventRounds[raceNumber] = [];
		
		_.each(race.slots, function(slot) {
			if( typeof slot != 'undefined') {
				//$('#grid').append(slot.name + '<br/>');
				eventRounds[raceNumber].push(slot.num)  
			} 
		});
	
		raceNumber++;
	});

	
	/****/
	
	
	return eventRounds;
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
 * Fair lineup
 */

// TODO -- Is this clear? We're making groups, not gridding people. This is a departure from how this class works.

exports.fair = function(numDriversTotal, numDriversPerHeat) {
	var fairGroups = [];
	var numGroups = numDriversTotal / numDriversPerHeat;
	var groups = this.createBalancedGrid(numDriversTotal, numGroups);
	
	// Calculate the middle of the array (we use this to pull from top, bottom or middle to fairly balance)
	var middle = groups.length % 2 === 1 ? Math.floor(groups.length / 2) : (groups.length / 2) - 0.5;

	for(var groupNum = 0; groupNum < numGroups; groupNum++) {
		
		fairGroups[groupNum] = [];
		var groupSize = numDriversPerHeat;		
		var participant;

		for(var j = 0; j < groupSize; j++) {
			if(j === middle) {
				// Pull from middle of array
				var middleElement = Math.floor(groups[groupNum].length / 2);
				participant = groups[j].splice(middleElement, 1)[0];
			} else if(j < middle) {
				// Pull from first of array
				participant = groups[j].splice(0, 1)[0];
			} else if(j > middle) {
				// Pull from last of array
				participant = groups[j].splice(-1, 1)[0];				
			}
			if(typeof participant !== 'undefined') fairGroups[groupNum].push(participant);
		}
	}
	
	return fairGroups;
}

exports.createMagixRoundLineup = function(numDriversTotal, numDriversPerHeat, numRoundsTotal) {
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
// Previous starting grid position (or inverted)
////
function startingPosition(array, opts) {
	var nextLineup = [];
	var inverted = opts.inverted || false;

	array.sort(function(obj1, obj2) {
		obj1.startingPosition = obj1.startingPosition || 9999999;
		obj2.startingPosition = obj2.startingPosition || 9999999;
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
function finishingPosition(array, opts) {
	var nextLineup = [];
	var inverted = opts.inverted || false;

	array.sort(function(obj1, obj2) {
		obj1.finishingPosition = obj1.finishingPosition || 9999999;
		obj2.finishingPosition = obj2.finishingPosition || 9999999;
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
function bestLapTime(array, opts) {
	var inverted = opts.inverted || false;
	var nextLineup = [];
	
	// Sort
	array.sort(function(obj1, obj2) {
		obj1.bestLapTime = obj1.bestLapTime || 9999999;
		obj2.bestLapTime = obj2.bestLapTime || 9999999;
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
function bestAverageLapTime(array, opts) {
	var inverted = opts.inverted || false;
	var nextLineup = [];
	
	// Sort
	array.sort(function(obj1, obj2) {
		obj1.bestAverageLapTime = obj1.bestAverageLapTime || 9999999;
		obj2.bestAverageLapTime = obj2.bestAverageLapTime || 9999999;
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
exports.mostPoints = function(array, opts) {
	var inverted = opts.inverted || false;
	var nextLineup = [];
	
	// Sort
	array.sort(function(obj1, obj2) {
		if(obj1.points !== 0) obj1.points = obj1.points || -9999999;
		if(obj2.points !== 0) obj2.points = obj2.points || -9999999;
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
exports.noGrid = function(array, opts) {
	var nextLineup = [];
	
	// Create lineup
	array.forEach(function(person, index) {
		nextLineup.push({ participantId: person.participantId, startingPosition: index + 1, originalData: person });
	});

	return nextLineup;
}

////
// Random
////
exports.randomized = function(array, opts) {
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