var gridding = require('./lib/gridding.js');

/*
NOLA Grand Prix
15 - 60 Drivers
 - 8min Practice
 - Heat 1: 15 Drivers/group; 10laps Win by position
 - Heat 2: 15 Drivers/group; Inverted Start from heat 1; 10laps Win by position
 - Main Event: Top 10; Gridded by total points; 12laps Win by position
*/

/*
Super Series Timing & Scoring Formatting - I ran a mock Super Series event in the timing system on August 4th, and ran into several issues with the way the system creates grids from Round to Round. Here is a look at what the event should look like vs. what we actually saw from round to round:

- Round 1 [Practice] : Drivers to be randomly assigned to run group for practice session. [This happened correctly].
- Round 2 [Qualifying]: Drivers to be assigned to a run group based off ROUND 1 BEST LAPTIME. Bottom 25% on from Round 1 grouped together in the Group A, next 25% in Group B, Ect. [This happened correctly].
- Round 3 [Heat Race 1]: Drivers to be assigned to a run group with grid order based of ROUND 2 BEST LAPTIME. [This is where we ran into the issue of the system placing the fastest bunch of drivers into the same run group for the race, when instead we need the system to spread them out evenly over the four groups. ex. 'Fast Driver 1' from ROUND 2 would be the pole sitter in ROUND 3 - GROUP A. 'Fast Driver 2' from ROUND 2 would be pole sitter in ROUND 3 - GROUP B, ect.

- Round 4 [Heat Race 2]: Drivers to be assigned to a run group with grid order based of ROUND 2 FINISHING POSITION. [This is where we ran into the issue of the system placing the TOP FINISHING drivers from ROUND 3 into the same run group together for ROUND 4, when instead we need the system to spread them out evenly over the four groups. ex. 'Winning Driver 1' from ROUND 3 would be the pole sitter in ROUND 4 - GROUP A. 'Winning Driver 2' from ROUND 3 would be pole sitter in ROUND 4 - GROUP B, ect.

- Round 5 [Main Events]: Drivers to be assigned to run groups and gridded based on combined total points from Rounds 3 & 4. Bottom 25% of points earners on the Day grouped together in the D-Main, next 25% in the C-Main, Ect. System seemed to group and grid based on points from Round 4 only, instead of total points on the day.
*/

participantsPractice = [
	{ name: 'Wes', bestLaptime: 37.00, points: 4, startingPosition: 1, finishingPosition: 1 },
	{ name: 'John', bestLaptime: 33.00, points: 2, startingPosition: 2, finishingPosition: 2 },
	{ name: 'Pete', bestLaptime: 35.00, points: 2, startingPosition: 3, finishingPosition: 3 },
	{ name: 'Dan', bestLaptime: 34.00, points: 1, startingPosition: 4, finishingPosition: 4 },
	{ name: 'Max', bestLaptime: 37.40, points: 4, startingPosition: 5, finishingPosition: 1 },
	{ name: 'Brian', bestLaptime: 33.20, points: 8, startingPosition: 6, finishingPosition: 2 },
	{ name: 'Eric', bestLaptime: 35.50, points: 2, startingPosition: 7, finishingPosition: 3 },
	{ name: 'Shingo', bestLaptime: 34.20, points: 1, startingPosition: 8, finishingPosition: 4 },
	{ name: 'Glenda', bestLaptime: 37.40, points: 4, startingPosition: 9, finishingPosition: 1 },
	{ name: 'Sandy', bestLaptime: 33.20, points: 2, startingPosition: 10, finishingPosition: 2 },
	{ name: 'Josie', bestLaptime: 35.50, points: 2, startingPosition: 11, finishingPosition: 3 },
	{ name: 'Kayla', bestLaptime: 34.20, points: 1, startingPosition: 12, finishingPosition: 4 }
];

var nolaGP = [
	{ name: 'Practice', 
		heat: { winBy: 'lapTime', duration: 'minutes', durationValue: 8, maxDrivers: 15 },
		proceed: { from: { type: 'round', opts: ['previous', 'all', 1,2,3] }, gridBy: 'startingPosition', gridOptions: { inverted: false } },
		proceedFilter: { filter: 'topPercent', value: 100 },
		participants: participantsPractice },
		
	{ name: 'Round 1',
		heat: { winBy: 'position', duration: 'laps', durationValue: 10, maxDrivers: 15, scoringTemplate: [10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0] },
		proceed: { from: 'group', gridBy: 'startingPosition', gridOptions: { inverted: true } },
		proceedFilter: { filter: 'topPercent', value: 100 },
		participants: participantsPractice },
	
	{ name: 'Round 2',
		heat: { winBy: 'lapTime', duration: 'laps', durationValue: 10, maxDrivers: 15, scoringTemplate: [10, 9, 8, 7, 6, 5, 4, 3, 2, 1, 0] }, 
		proceed: { from: 'event', gridBy: 'mostPoints', gridOptions: { inverted: false } },
		proceedFilter: { filter: 'topNumber', value: 5 },
		participants: participantsPractice },
	
	{ name: 'Main Event',
		heat: { winBy: 'position', duration: 'laps', durationValue: 12, maxDrivers: 10, scoringTemplate: [1000, 900, 800, 700, 600, 500, 400, 300, 200, 100, 0] },
		participants: participantsPractice }
];

/*
var options = {
	inverted: true,
	filterName: 'topPercent',
	filterValue: 100,
	maxDrivers: 10
	};

roundLineup = gridding.create('bestLapTime', arrayOfDriverObjects, options);
*/

// PRACTICE
console.log('\n\nPRACTICE\n\n');
var opts = {
	maxDrivers: nolaGP[0].heat.maxDrivers
	};

console.log(gridding.create('noGrid', nolaGP[0].participants, opts));

// ROUND 1
console.log('\n\nROUND 1\n\n');
var opts = {
	inverted: false,
	filterName: nolaGP[0].proceedFilter.filter,
	filterValue: nolaGP[0].proceedFilter.value,
	maxDrivers: nolaGP[1].heat.maxDrivers
	};

console.log(gridding.create(nolaGP[0].proceed.gridBy, nolaGP[1].participants, opts));

// ROUND 2
console.log('\n\nROUND 2\n\n');
var opts = {
	inverted: true,
	filterName: nolaGP[1].proceedFilter.filter,
	filterValue: nolaGP[1].proceedFilter.value,
	maxDrivers: nolaGP[2].heat.maxDrivers
	};

console.log(gridding.create(nolaGP[1].proceed.gridBy, nolaGP[2].participants, opts));

// FINAL
console.log('\n\nFINAL\n\n');
var opts = {
	inverted: false,
	filterName: nolaGP[2].proceedFilter.filter,
	filterValue: nolaGP[2].proceedFilter.value,
	maxDrivers: nolaGP[3].heat.maxDrivers
	};

console.log(gridding.create(nolaGP[2].proceed.gridBy, nolaGP[3].participants, opts));

/*
The flow of programatically running an event:
1. Drivers added to event
2. Event created w/ drivers
  - This grabs the event template from DB
event.create(participants, eventTemplate);

3. First Round created with Heat(s)
  - Heats are run and results are placed into some array

4. When proceeding, next Round/Heat(s) are created
  - Event template with partial results is fed into event.proceed(eventTemplate)

At any time the "state" of an event can be pulled for point standings or upcoming grid lineups - event.inspect(eventTemplate)
*/

//Look at total number of drivers and event type
/*e = createEvent(nolaGP[0].participants, nolaGP);
console.log('\n\nFull event lineup');
console.log(e);*/

function createEvent(participants, eventTemplate, eventResults) {
	var eventLineup = [];
	var numTotalPeople = participants.length;

	// Load the event template and create each round of heat(s)
	eventTemplate.forEach(function(heatTemplate, roundNum) {

		// Determine how many people are in the next round
		var numPeopleInRound;

		if(roundNum == 0) { // Event opens with everyone -- in future could define custom template here also?
			
			numPeopleInRound = heatTemplate.participants.length;
			
			// TODO. This is a bit of a kludge
			var priorRound = eventTemplate[roundNum];

		} else { // Filtering could reduce the number of people, let's check
			var priorRound = eventTemplate[roundNum-1];
			var peopleInPriorRound = eventTemplate[roundNum-1].participants.length;
			
			numPeopleInRound = gridding.filterParticipantsInRound(priorRound.proceedFilter.filter, priorRound.proceedFilter.value, peopleInPriorRound);
			
		}

		/*
		Creating a Round
		1. Find out how many people are in this round (filter from prior)
		2. Find the split of the heats and gridding
		3. Match people to that heat/grid layout
		*/

		var opts = merge_options(priorRound.proceed.gridOptions, { maxDrivers: heatTemplate.heat.maxDrivers });
		var participantsGroupedInHeats = gridding.create(priorRound.proceed.gridBy, participants, opts);

		// Create the heats in this round w/ the gridding
		heatsInRound = [];
		for(var i = 0; i < participantsGroupedInHeats.length; i++) {
			var heat = clone(heatTemplate);
			heat.name = heat.name + ', Heat ' + (i + 1);
			heat.heat.maxDrivers = participantsGroupedInHeats[i].length;
			heatsInRound.push(heat);
		}
		eventLineup.push(heatsInRound);

	});
	
	// Cheating... :-)
	function clone(a) {
		 return JSON.parse(JSON.stringify(a));
	}

	return eventLineup;
}

function merge_options(obj1,obj2){
	var obj3 = {};
	for (var attrname in obj1) { obj3[attrname] = obj1[attrname]; }
	for (var attrname in obj2) { obj3[attrname] = obj2[attrname]; }
	return obj3;
}