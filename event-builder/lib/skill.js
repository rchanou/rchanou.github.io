/*participants = [
	{ id: 123, skill: [25, 25/3], rank: 1 },
	{ id: 456, skill: [25, 25/3], rank: 2 },
	{ id: 789, skill: [25, 25/3], rank: 3 },
	{ id: 234, skill: [25, 25/3], rank: 4 },
	];*/

trueskill = require("trueskill");

exports.calculate = function(participants, opts) {
	// The player ranking (their "level") is mu-3*sigma, so the default skill
	// value corresponds to a level of 0.

	// console.log(alice.skill);
	// console.log(Math.round(alice.skill[0] - (3 * alice.skill[1])));
	
	trueskill.AdjustPlayers(participants);
	return participants;
};