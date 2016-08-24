/* eslint-disable */

var expect = require("chai").expect;
var gridding = require("../lib/gridding.js");

// Setup some variables that we can test with
var participantsDefault = [
			 	{ participantId: 'Wes', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5, vehicleId: 1},
				{ participantId: 'Glenda', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4, vehicleId: 2 },
				{ participantId: 'Max', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3, vehicleId: 3 },
				{ participantId: 'Tommy', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2, vehicleId: 4 },
				{ participantId: 'Shakib', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1, vehicleId: 5 }
			 ];
var particpants = [];

var roundOfHeatsDefault = [[1,3,5],[2,4]];
var roundOfHeats = []

// Reset our array of participants and heats
beforeEach(function(){
  participants = JSON.parse(JSON.stringify(participantsDefault));
	roundOfHeats = JSON.parse(JSON.stringify(roundOfHeatsDefault));
})

describe("Participant Sorting Methods", function() {
	 
	 /*describe("#doesNotExist()", function() {
	
		it("should return an error", function() {			
			expect(gridding.create('doesNotExist')).to.throw(Error);
		});
	
	});*/
	 
	 describe("#noGrid()", function() {
	
		it("should return participants as they are given", function(){			
			var res = gridding.create('noGrid', participants.slice(0), { maxDrivers: 3 });
			expect(res).to.have.deep.property('[0][0].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][1].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][2].participantId', 'Max');
			expect(res).to.have.deep.property('[1][0].participantId', 'Tommy');
			expect(res).to.have.deep.property('[1][1].participantId', 'Shakib');
		});
		
		it("should return three copies", function(){			
			var res = gridding.create('noGrid', participants.slice(0), { maxDrivers: 3, numHeatsPerParticipant: 3 });
			expect(res).to.have.deep.property('[0][0].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][1].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][2].participantId', 'Max');
			expect(res).to.have.deep.property('[1][0].participantId', 'Tommy');
			expect(res).to.have.deep.property('[1][1].participantId', 'Shakib');
			expect(res).to.have.deep.property('[2][0].participantId', 'Wes');
			expect(res).to.have.deep.property('[2][1].participantId', 'Glenda');
			expect(res).to.have.deep.property('[2][2].participantId', 'Max');
			expect(res).to.have.deep.property('[3][0].participantId', 'Tommy');
			expect(res).to.have.deep.property('[3][1].participantId', 'Shakib');
			
			expect(res).to.have.length(6);
		});
	
	});
	
	describe("#custom()", function() {
	
		var participants = [
			 	{ participantId: 'Person 1', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
				{ participantId: 'Person 2', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
				{ participantId: 'Person 3', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
				{ participantId: 'Person 4', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
				{ participantId: 'Person 5', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 },
				{ participantId: 'Person 6', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
				{ participantId: 'Person 7', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
				{ participantId: 'Person 8', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
				{ participantId: 'Person 9', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
				{ participantId: 'Person 10', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 }
			 ];
	
		it("should return participants per the custom gridding order", function(){			
			var res = gridding.create('custom', participants.slice(0), { customGrid: [ [1,5,9,8,4], [2,6,10,7,3], [4,7,9,6,1], [3,8,10,5,2] ] });
			expect(res).to.have.deep.property('[0][0].participantId', 'Person 1');
			expect(res).to.have.deep.property('[0][1].participantId', 'Person 5');
			expect(res).to.have.deep.property('[1][0].participantId', 'Person 2');
			expect(res).to.have.deep.property('[3][4].participantId', 'Person 2');
		});
		
		it("should return participants per the custom gridding order (inverted)", function(){			
			var res = gridding.create('custom', participants.slice(0), { inverted: true, customGrid: [ [1,5,9,8,4], [2,6,10,7,3], [4,7,9,6,1], [3,8,10,5,2] ] });
			expect(res).to.have.deep.property('[0][0].participantId', 'Person 4');
			expect(res).to.have.deep.property('[0][1].participantId', 'Person 8');
			expect(res).to.have.deep.property('[1][0].participantId', 'Person 3');
			expect(res).to.have.deep.property('[3][4].participantId', 'Person 3');
			
			var res = gridding.createGrid('custom', participants.slice(0), 5, { inverted: true, customGrid: [ [1,5,9,8,4], [2,6,10,7,3], [4,7,9,6,1], [3,8,10,5,2] ] });
			expect(res).to.have.deep.property('[0][0]', 4);
			expect(res).to.have.deep.property('[3][4]', 3);
		});
		
		it("should return participants when there are fewer participants than intended", function(){			
			var res = gridding.create('custom', participants.slice(0), { customGrid: [ [1,5,11] ] });
			expect(res).to.have.deep.property('[0][0].participantId', 'Person 1');
			expect(res).to.have.deep.property('[0][1].participantId', 'Person 5');
			expect(res[0][2]).to.be.undefined;
		});
	
	});
	
	describe("#startingPosition()", function() {
	
		it("should correctly grid participants", function(){
			var res = gridding.create('startingPosition', participants.slice(0), { maxDrivers: 5, inverted: false });
			expect(res).to.have.deep.property('[0][0].participantId', 'Max');
			expect(res).to.have.deep.property('[0][1].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][2].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][3].participantId', 'Tommy');
			expect(res).to.have.deep.property('[0][4].participantId', 'Shakib');
		});
		
		it("should handle missing starting grid positions", function(){
			// Remove Glenda's best laptime
			delete participants[1].startingPosition;
			
			var res = gridding.create('startingPosition', participants.slice(0), { maxDrivers: 5, inverted: false });
			expect(res).to.have.deep.property('[0][0].participantId', 'Max');
			expect(res).to.have.deep.property('[0][4].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][1].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][2].participantId', 'Tommy');
			expect(res).to.have.deep.property('[0][3].participantId', 'Shakib');
		});
		
		it("should handle missing starting grid positions (inverted)", function(){
			// Remove Glenda's best laptime
			delete participants[1].startingPosition
			
			var res = gridding.create('startingPosition', participants.slice(0), { maxDrivers: 5, inverted: true });
			expect(res).to.have.deep.property('[0][1].participantId', 'Shakib');
			expect(res).to.have.deep.property('[0][2].participantId', 'Tommy');
			expect(res).to.have.deep.property('[0][3].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][0].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][4].participantId', 'Max');
		});
		
		it("should correctly grid participants (inverted)", function(){
			var res = gridding.create('startingPosition', participants.slice(0), { maxDrivers: 5, inverted: true });
			expect(res).to.have.deep.property('[0][0].participantId', 'Shakib');
			expect(res).to.have.deep.property('[0][1].participantId', 'Tommy');
			expect(res).to.have.deep.property('[0][2].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][3].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][4].participantId', 'Max');
		});
	
	});
	
	describe("#finishingPosition()", function() {
	
		it("should correctly grid participants", function(){
			var res = gridding.create('finishingPosition', participants.slice(0), { maxDrivers: 5, inverted: false });
			expect(res).to.have.deep.property('[0][0].participantId', 'Shakib');
			expect(res).to.have.deep.property('[0][1].participantId', 'Tommy');
			expect(res).to.have.deep.property('[0][2].participantId', 'Max');
			expect(res).to.have.deep.property('[0][3].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][4].participantId', 'Wes');
			
		});
		
		it("should handle missing finishing grid positions", function(){
			// Remove Glenda's best laptime
			delete participants[1].finishingPosition
			
			var res = gridding.create('finishingPosition', participants.slice(0), { maxDrivers: 5, inverted: false });
			expect(res).to.have.deep.property('[0][0].participantId', 'Shakib');
			expect(res).to.have.deep.property('[0][1].participantId', 'Tommy');
			expect(res).to.have.deep.property('[0][2].participantId', 'Max');
			expect(res).to.have.deep.property('[0][4].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][3].participantId', 'Wes');
		});
		
		it("should handle missing finishing grid positions (inverted)", function(){
			// Remove Glenda's best laptime
			delete participants[1].finishingPosition
			
			var res = gridding.create('finishingPosition', participants.slice(0), { maxDrivers: 5, inverted: true });
			expect(res).to.have.deep.property('[0][1].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][0].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][2].participantId', 'Max');
			expect(res).to.have.deep.property('[0][3].participantId', 'Tommy');
			expect(res).to.have.deep.property('[0][4].participantId', 'Shakib');
		});
		
		it("should correctly grid participants (inverted)", function(){
			var res = gridding.create('finishingPosition', participants.slice(0), { maxDrivers: 5, inverted: true });
			expect(res).to.have.deep.property('[0][0].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][1].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][2].participantId', 'Max');
			expect(res).to.have.deep.property('[0][3].participantId', 'Tommy');
			expect(res).to.have.deep.property('[0][4].participantId', 'Shakib');
		});
		
		it("should correctly balance participants (inverted with multiple heats)", function(){
			var res = gridding.create('finishingPosition', participants.slice(0), { maxDrivers: 3, inverted: true });
			expect(res).to.have.deep.property('[0][0].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][1].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][2].participantId', 'Max');
			expect(res).to.have.deep.property('[1][0].participantId', 'Tommy');
			expect(res).to.have.deep.property('[1][1].participantId', 'Shakib');
		});
	
	});
	
	describe("#bestLapTime()", function() {
	
		it("should correctly balance participants", function(){
			var res = gridding.create('bestLapTime', participants.slice(0), { maxDrivers: 5 });
			expect(res).to.have.deep.property('[0][0].participantId', 'Shakib');
			expect(res).to.have.deep.property('[0][1].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][2].participantId', 'Max');
			expect(res).to.have.deep.property('[0][3].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][4].participantId', 'Tommy');
		});
		
		it("should correctly handle missing laptime", function(){
			// Remove Glenda's best laptime
			delete participants[1].bestLapTime;

			var res = gridding.create('bestLapTime', participants.slice(0), { maxDrivers: 5 });
			expect(res).to.have.deep.property('[0][0].participantId', 'Shakib');
			expect(res).to.have.deep.property('[0][4].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][1].participantId', 'Max');
			expect(res).to.have.deep.property('[0][2].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][3].participantId', 'Tommy');
		});
		
		it("should correctly handle missing laptime (inverted)", function(){
			// Remove Glenda's best laptime
			delete participants[1].bestLapTime;
			
			var res = gridding.create('bestLapTime', participants.slice(0), { maxDrivers: 5, inverted: true });
			expect(res).to.have.deep.property('[0][1].participantId', 'Tommy');
			expect(res).to.have.deep.property('[0][2].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][3].participantId', 'Max');
			expect(res).to.have.deep.property('[0][0].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][4].participantId', 'Shakib');
		});
		
		it("should correctly balance participants (inverted)", function(){
			var res = gridding.create('bestLapTime', participants.slice(0), { maxDrivers: 5, inverted: true });
			expect(res).to.have.deep.property('[0][0].participantId', 'Tommy');
			expect(res).to.have.deep.property('[0][1].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][2].participantId', 'Max');
			expect(res).to.have.deep.property('[0][3].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][4].participantId', 'Shakib');
		});
	
	});
	
	describe("#bestAverageLapTime()", function() {
	
		it("should correctly balance participants", function(){
			var res = gridding.create('bestAverageLapTime', participants.slice(0), { maxDrivers: 3 });
			expect(res).to.have.deep.property('[0][0].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][1].participantId', 'Shakib');
			expect(res).to.have.deep.property('[0][2].participantId', 'Glenda');
			expect(res).to.have.deep.property('[1][0].participantId', 'Tommy');
			expect(res).to.have.deep.property('[1][1].participantId', 'Max');
		});
		
		it("should correctly missing laptime", function(){
			// Remove Glenda's best laptime
			delete participants[1].bestAverageLapTime;
			
			var res = gridding.create('bestAverageLapTime', participants.slice(0), { maxDrivers: 5 });
			expect(res).to.have.deep.property('[0][0].participantId', 'Wes');
			expect(res).to.have.deep.property('[0][1].participantId', 'Shakib');
			expect(res).to.have.deep.property('[0][4].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][2].participantId', 'Tommy');
			expect(res).to.have.deep.property('[0][3].participantId', 'Max');
		});
		
		it("should correctly balance participants missing laptime (inverted)", function() {
			// Remove Glenda's best laptime
			delete participants[1].bestAverageLapTime;
			
			var res = gridding.create('bestAverageLapTime', participants.slice(0), { maxDrivers: 5, inverted: true });
			expect(res).to.have.deep.property('[0][1].participantId', 'Max');
			expect(res).to.have.deep.property('[0][2].participantId', 'Tommy');
			expect(res).to.have.deep.property('[0][0].participantId', 'Glenda');
			expect(res).to.have.deep.property('[0][3].participantId', 'Shakib');
			expect(res).to.have.deep.property('[0][4].participantId', 'Wes');
		});
		
		it("should correctly balance participants (inverted)", function(){
			var res = gridding.create('bestAverageLapTime', participants.slice(0), { maxDrivers: 3, inverted: true });
			expect(res).to.have.deep.property('[0][0].participantId', 'Max');
			expect(res).to.have.deep.property('[0][1].participantId', 'Tommy');
			expect(res).to.have.deep.property('[0][2].participantId', 'Glenda');
			expect(res).to.have.deep.property('[1][0].participantId', 'Shakib');
			expect(res).to.have.deep.property('[1][1].participantId', 'Wes');
		});
	
	});
	 
	 describe("#mostPoints()", function() {		 
		 it("should order participants by most points and break ties", function(){
				 var res = gridding.create('mostPoints', participants.slice(0), { maxDrivers: 5 });
				 expect(res).to.have.deep.property('[0][0].participantId', 'Wes');
				 expect(res).to.have.deep.property('[0][1].participantId', 'Glenda');
				 expect(res).to.have.deep.property('[0][2].participantId', 'Max');
				 expect(res).to.have.deep.property('[0][3].participantId', 'Tommy');
				 expect(res).to.have.deep.property('[0][4].participantId', 'Shakib');
		 });
		 
		 it("should order participants by most points and break ties with missing points", function(){
				 // Remove Glenda's best laptime
				delete participants[1].points;
				 
				 var res = gridding.create('mostPoints', participants.slice(0), { maxDrivers: 5 });
				 expect(res).to.have.deep.property('[0][0].participantId', 'Wes');
				 expect(res).to.have.deep.property('[0][4].participantId', 'Glenda');
				 expect(res).to.have.deep.property('[0][1].participantId', 'Max');
				 expect(res).to.have.deep.property('[0][2].participantId', 'Tommy');
				 expect(res).to.have.deep.property('[0][3].participantId', 'Shakib');
		 });
		 
		 it("should order participants by least points with missing points (inverted)", function(){
				 // Remove Glenda's best laptime
				 delete participants[1].points;
				 
				 var res = gridding.mostPoints(participants.slice(0), { inverted: true });
				 expect(res).to.have.deep.property('[1].participantId', 'Shakib');
				 expect(res).to.have.deep.property('[2].participantId', 'Tommy');
				 expect(res).to.have.deep.property('[0].participantId', 'Glenda');
				 expect(res).to.have.deep.property('[3].participantId', 'Max');
				 expect(res).to.have.deep.property('[4].participantId', 'Wes');
		 });

		 it("should order participants by least points and break ties (inverted)", function(){
				 var res = gridding.mostPoints(participants.slice(0), { inverted: true });
				 expect(res).to.have.deep.property('[0].participantId', 'Shakib');
				 expect(res).to.have.deep.property('[1].participantId', 'Tommy');
				 expect(res).to.have.deep.property('[2].participantId', 'Glenda');
				 expect(res).to.have.deep.property('[3].participantId', 'Max');
				 expect(res).to.have.deep.property('[4].participantId', 'Wes');
		 });
   });
	 
	 describe("#randomize()", function() {
			 
			 it("should contain all five participaints", function(){
           var res = gridding.randomized(participants.slice(0), {});
           expect(res).to.have.length(5);
					 expect(res).to.have.deep.property('[0].participantId');
					 expect(res).to.have.deep.property('[1].participantId');
					 expect(res).to.have.deep.property('[2].participantId');
					 expect(res).to.have.deep.property('[3].participantId');
					 expect(res).to.have.deep.property('[4].participantId');
       });
			 
			 it("should return five copies that are different", function(){			
					var res = gridding.create('randomized', participants.slice(0), { numHeatsPerParticipant: 5 });
					expect(res).to.have.length(5);
					expect(res[0]).not.to.deep.equal(res[1]);
				});
				
				/*
				Test for scenario Chris needed to understand
				it("should group and copy correctly for randomized", function() {
					var twelveParticipants = [
						{ participantId: 'Person 1', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
						{ participantId: 'Person 2', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
						{ participantId: 'Person 3', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
						{ participantId: 'Person 4', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
						{ participantId: 'Person 5', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 },
						{ participantId: 'Person 6', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
						{ participantId: 'Person 7', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
						{ participantId: 'Person 8', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
						{ participantId: 'Person 9', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
						{ participantId: 'Person 10', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 },
						{ participantId: 'Person 11', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
						{ participantId: 'Person 12', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 }
					 ];
					 
					var res = gridding.create('randomized', twelveParticipants, { maxDrivers: 5, numHeatsPerParticipant: 5 });
					console.log(res);
				});*/

   });
	 
	 describe("#magix()", function() {
			 
			 it("should create the correct lineup (old style) # racers == # heats", function(){
           var lineup = participants.slice(0);
					 var res1 = gridding.magix(lineup.length, 3, 3); // numDrivers, numDriversPerRace, numRacesPerDriver
					 expect(res1).to.deep.equal([[1,5,3], [2,1,4], [3,2,5], [4,3,1], [5,4,2]]);
       });
			 
			 /*
			 // TODO -- Need to write tests for "subset sum" style magix (below commented out)
			 it("should create the correct lineup (new style) # racers !== # heats", function(){
           var lineup = participants.slice(0);
					 var res1 = gridding.magix(lineup.length, 3, 2); // numDrivers, numDriversPerRace, numRacesPerDriver
					 console.log(res1);
					 //[ [ 4, 3, 2 ], [ 2, 4, 1 ], [ 3, 5 ], [ 1, 5 ] ]
					 //[ [ 3, 5, 1 ], [ 1, 5, 2 ], [ 2, 4 ], [ 4, 3 ] ]
					 expect(res1).to.deep.equal([[1,5,3], [2,1,4], [3,2,5], [4,3,1], [5,4,2]]);
       });
			 */

			 it("should assign the right people", function(){
           var lineup = participants.slice(0);
					 var res2 = gridding.create('magix', lineup, { maxDrivers: 3, numHeatsPerParticipant: 3 });
					 expect(res2).to.have.deep.property('[0][0].participantId', 'Wes');
					 expect(res2).to.have.deep.property('[0][1].participantId', 'Shakib');
					 expect(res2).to.have.deep.property('[0][2].participantId', 'Max');
					 
					 expect(res2).to.have.deep.property('[4][0].participantId', 'Shakib');
					 expect(res2).to.have.deep.property('[4][1].participantId', 'Tommy');
					 expect(res2).to.have.deep.property('[4][2].participantId', 'Glenda');
					 
       });
			 
			 it("should assign the right starting positions", function(){
           var lineup = participants.slice(0);
					 var res2 = gridding.create('magix', lineup, { maxDrivers: 3 });

					 expect(res2).to.have.deep.property('[0][0].startingPosition', 1);
					 expect(res2).to.have.deep.property('[0][1].startingPosition', 2);
					 expect(res2).to.have.deep.property('[0][2].startingPosition', 3);
       });

   });
	 
	 /*describe("#magixFair()", function() {
			 
			 var participants = [
			 	{ participantId: 'Person 1', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
				{ participantId: 'Person 2', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
				{ participantId: 'Person 3', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
				{ participantId: 'Person 4', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
				{ participantId: 'Person 5', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 },
				{ participantId: 'Person 6', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
				{ participantId: 'Person 7', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
				{ participantId: 'Person 8', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
				{ participantId: 'Person 9', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
				{ participantId: 'Person 10', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 },
				{ participantId: 'Person 11', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
				{ participantId: 'Person 12', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
				{ participantId: 'Person 13', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
				{ participantId: 'Person 14', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
				{ participantId: 'Person 15', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 },
				{ participantId: 'Person 16', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
				{ participantId: 'Person 17', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
				{ participantId: 'Person 18', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
				{ participantId: 'Person 19', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
				{ participantId: 'Person 20', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 }
			 ];

			var lineup = participants;
			var numRacesPerRacer = 3;
			var numRoundsTotal = 12;
			var res = gridding.createMagixFairRoundLineup(lineup.length, numRoundsTotal, numRacesPerRacer); //numDriversTotal, numRoundsTotal, numRacesPerRacer
			
			var racers = [];
			
			// Compile some metadata to ease the tests below
			res.forEach(function(lineup, raceNum) {
				lineup.forEach(function(racerId, position) {
					if(typeof racers[racerId] == 'undefined') racers[racerId] = { racerId: racerId, totalRounds: 0, positions: [], rounds: [], positionSummation: 0 };

					var gridPosition = position + 1;
					racers[racerId].totalRounds += 1;
					racers[racerId].positions.push(gridPosition);
					racers[racerId].rounds.push(raceNum + 1);
					racers[racerId].positionSummation += gridPosition;
				});
			});

			console.log(res);			
			console.log(racers);
			 
			it("participants should race the correct number of times", function() {
				racers.forEach(function(racer) {
					expect(racer.rounds.length).to.equal(numRacesPerRacer);
				});
      });
			 
			it("rounds should have the correct number of heats", function() {				 
				expect(res.length).to.equal(numRoundsTotal);
			});
			
			it("participants should not race back to back races", function() {
				var lastRound = null;
				racers.forEach(function(racer) {
					racer.rounds.forEach(function(currentRound) {
						if(lastRound !== null) expect(currentRound).to.be.above(lastRound + 1);
						lastRound = currentRound;
					});
					lastRound = null;
				});
			});

			it("participants should not have pole more than once", function() {
				racers.forEach(function(racer) {
					expect(racer.positions.indexOf(1)).to.equal(racer.positions.lastIndexOf(1));
				});
			});

			it("participants should have equal total starting positions", function() {
				var lastPositionSummation;
				racers.forEach(function(racer) {
					if(typeof lastPositionSummation !== 'undefined') {
						expect(lastPositionSummation).to.equal(racer.positionSummation);
					}
					lastPositionSummation = racer.positionSummation;
				});
			});

   });*/
	 
	 describe("#balanced()", function() {
			 
			  var participants = [
					{ participantId: 'Person 1', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
					{ participantId: 'Person 2', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
					{ participantId: 'Person 3', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },					
					{ participantId: 'Person 4', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
					{ participantId: 'Person 5', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 },					
					{ participantId: 'Person 6', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },					
					{ participantId: 'Person 7', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },										
					{ participantId: 'Person 8', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
					{ participantId: 'Person 9', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },					
					{ participantId: 'Person 10', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 },
					{ participantId: 'Person 11', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
					{ participantId: 'Person 12', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
					{ participantId: 'Person 13', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
					{ participantId: 'Person 14', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },					
					{ participantId: 'Person 15', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 },					
					{ participantId: 'Person 16', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
					{ participantId: 'Person 17', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
					{ participantId: 'Person 18', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
					{ participantId: 'Person 19', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
					{ participantId: 'Person 20', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 }
				 ];
			 
			 it("should create the correct lineup", function(){
           var lineup = participants.slice(0);
					 var res = gridding.balanced(lineup.length, 4);
					 expect(res).to.deep.equal([[1,6,11,16], [2,7,12,17], [3,8,13,18], [4,9,14,19], [5,10,15,20]]);
       });
			 
			 it("should create the correct lineup with an odd split", function(){
           var lineup = participants.slice(0);
					 var res = gridding.balanced(lineup.length, 7);
					 expect(res).to.deep.equal([[1,4,7,10,13,16,19], [2,5,8,11,14,17,20], [3,6,9,12,15,18]]);
       });

   });
	 
	 // TO IMPLEMENT/TEST
	 describe("#fair()", function() {});
/*			 
			  var participants = [
					{ participantId: 'Person 1', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
					{ participantId: 'Person 2', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
					{ participantId: 'Person 3', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },					
					{ participantId: 'Person 4', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
					{ participantId: 'Person 5', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 },
					
					{ participantId: 'Person 6', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },					
					{ participantId: 'Person 7', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },					
					{ participantId: 'Person 8', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
					{ participantId: 'Person 9', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },										
					{ participantId: 'Person 10', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 },
					
					{ participantId: 'Person 11', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
					{ participantId: 'Person 12', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
					{ participantId: 'Person 13', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
					{ participantId: 'Person 14', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },					
					{ participantId: 'Person 15', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 },					
					
					{ participantId: 'Person 16', points: 5, bestAverageLapTime: 31.00, bestLapTime: 35.234, startingPosition: 3, finishingPosition: 5 },
					{ participantId: 'Person 17', points: 3, bestAverageLapTime: 33.00, bestLapTime: 33.234, startingPosition: 2, finishingPosition: 4 },
					{ participantId: 'Person 18', points: 3, bestAverageLapTime: 35.00, bestLapTime: 33.536, startingPosition: 1, finishingPosition: 3 },
					{ participantId: 'Person 19', points: 2, bestAverageLapTime: 34.00, bestLapTime: 36.234, startingPosition: 4, finishingPosition: 2 },
					{ participantId: 'Person 20', points: 0, bestAverageLapTime: 32.00, bestLapTime: 31.234, startingPosition: 5, finishingPosition: 1 }
				 ];
			 
			 it("should create the correct lineup", function(){
           var lineup = participants.slice(0);
					 var res = gridding.fair(lineup.length, 4);
					 expect(res).to.deep.equal([[1,6,15,20], [2,7,14,19], [3,8,13,18], [4,9,12,17], [5,10,11,16]]);
       });
			 
			 it("should create the correct lineup with an odd split", function(){
           var lineup = participants.slice(0);
					 var res = gridding.fair(lineup.length, 7);
					 expect(res).to.deep.equal([[1,4,7,11,15,18,20], [2,5,8,10,14,17,19], [3,6,9,12,13,16]]);
       });
			 
			 it("should create the correct lineup with a very odd split", function(){
           var lineup = participants.slice(0, 10);
					 var res = gridding.fair(lineup.length, 3); // [3,3,2,2]
					 console.log(res);
					 expect(res).to.deep.equal([[1,6,10], [2,7,9], [3,8], [4,5]]);
       });

   });*/

});

describe("Gridding & Helper Methods (That probably don't belong here)", function() {
	/*describe("#gridding()", function() {
		it("should throw an error without participants", function() {
			var res = gridding.create('noGrid', {}, {});
			.to.throw('Oh no')
		});
	});*/

	
	describe("#vehicleAssignment()", function() {
	
		it("should correctly assign no karts", function(){
			var res = gridding.create('finishingPosition', participants.slice(0), { maxDrivers: 5, inverted: false, vehicleAssignmentType: 'none' });
			expect(res).not.to.have.deep.property('[0][0].vehicleId');
			expect(res).not.to.have.deep.property('[0][4].vehicleId');
		});
		
		it("should correctly assign same", function(){
			var res = gridding.create('finishingPosition', participants.slice(0), { maxDrivers: 5, inverted: false, vehicleAssignmentType: 'same' });
			expect(res).to.have.deep.property('[0][0].vehicleId', 5);
			expect(res).to.have.deep.property('[0][4].vehicleId', 1);
		});
		
		it("should correctly assign same", function(){
			var res = gridding.create('finishingPosition', participants.slice(0), { maxDrivers: 3, inverted: false, vehicleAssignmentType: 'same' });
			expect(res).to.have.deep.property('[0][0].vehicleId', 5);
			expect(res).to.have.deep.property('[1][1].vehicleId', 1);
		});
		
		it("should correctly assign same inverted, grouped", function(){
			var res = gridding.create('finishingPosition', participants.slice(0), { maxDrivers: 3, inverted: true, vehicleAssignmentType: 'same' });
			expect(res).to.have.deep.property('[0][0].vehicleId', 1);
			expect(res).to.have.deep.property('[1][1].vehicleId', 5);
		});
		
		it("should correctly assign fair", function(){
			var res = gridding.create('finishingPosition', participants.slice(0), { maxDrivers: 5, inverted: false, vehicleAssignmentType: 'fair' });
			expect(res).to.have.deep.property('[0][0].vehicleId', 1);
			expect(res).to.have.deep.property('[0][4].vehicleId', 5);
		});
		
		it("should correctly assign fair, grouped", function(){
			var res = gridding.create('finishingPosition', participants.slice(0), { maxDrivers: 3, inverted: false, vehicleAssignmentType: 'fair' });
			expect(res).to.have.deep.property('[0][0].vehicleId', 1);
			expect(res).to.have.deep.property('[1][1].vehicleId', 5);
		});
		
		it("should correctly assign random", function(){
			var res1 = gridding.create('finishingPosition', participants.slice(0), { maxDrivers: 5, inverted: false, vehicleAssignmentType: 'random' });
			var res2 = gridding.create('finishingPosition', participants.slice(0), { maxDrivers: 5, inverted: false, vehicleAssignmentType: 'random' });
			
			expect(res1).not.to.deep.equal(res2);
		});
	});
	
	describe("#balanceParticipants()", function() {
	
		it("should correctly balance participants", function() {
			var res = gridding.balanceParticipants(20, 5);
			expect(res).to.deep.equal([5,5,5,5]);
			
			var res = gridding.balanceParticipants(20, 3);
			expect(res).to.deep.equal([3,3,3,3,3,3,2]);
			
			var res = gridding.balanceParticipants(3, 10);
			expect(res).to.deep.equal([3]);
		});
	
	});
 
 describe("#createBalancedGrid()", function() {
       it("should balance out unequal grids by number of participants", function() {
           // Two groups of 5
					 var res1 = gridding.createBalancedGrid(10, 5);
           expect(res1).to.deep.equal([[1,2,3,4,5],[6,7,8,9,10]]);
					 
					 // Three groups of four
					 var res2 = gridding.createBalancedGrid(12, 5);
           expect(res2).to.deep.equal([[1,2,3,4], [5,6,7,8], [9,10,11,12]]);
					 
					 // One group of 5
					 var res3 = gridding.createBalancedGrid(5, 10);
           expect(res3).to.deep.equal([[1,2,3,4,5]]);
					 
					 // Three groups
					 var res3 = gridding.createBalancedGrid(7, 3);
           expect(res3).to.deep.equal([[1,2,3], [4,5], [6,7]]);
       });
			 
			 it("should balance out unequal grids by percentage of participants", function() {
           // Two groups of 5
					 var res1 = gridding.createBalancedGrid(10, { percent: 50 });
           expect(res1).to.deep.equal([[1,2,3,4,5],[6,7,8,9,10]]);
       });
			 
			 it("should handle no max participants per group", function() {
					 var res = gridding.createBalancedGrid(7);
           expect(res).to.deep.equal([[1,2,3,4,5,6,7]]);
       });
   });
	 
	 describe("#filterParticipantsInRound()", function() {
			 
			 var twentyParticipants = JSON.parse(JSON.stringify(participantsDefault)).concat(
			 	JSON.parse(JSON.stringify(participantsDefault)),
				JSON.parse(JSON.stringify(participantsDefault)),
				JSON.parse(JSON.stringify(participantsDefault))
				);
			 var twoParticipants = JSON.parse(JSON.stringify(participantsDefault)).slice(0,2);
			 
			 it("should limit by number", function(){
           var res = gridding.filterParticipantsInRound('topNumber', 5, twentyParticipants);
           expect(res.length).to.equal(5);
       });
			 
			 it("should limit by number (and not return more than number of participants", function(){
           var res = gridding.filterParticipantsInRound('topNumber', 5, twoParticipants);
           expect(res.length).to.equal(2);
       });
			 
			 it("should limit by percentage", function(){
           var res = gridding.filterParticipantsInRound('topPercent', 50, twentyParticipants);
           expect(res.length).to.equal(10);
       });
			 
			 it("should limit by percentage (and round)", function(){
           var res = gridding.filterParticipantsInRound('topPercent', 33, twentyParticipants);
           expect(res.length).to.equal(7);
       });
			 
			 it("should handle bad filter name", function(){
           var res = gridding.filterParticipantsInRound('missingFilter', 5, twentyParticipants);
           expect(res.length).to.equal(20);
       });
			 
			 it("should handle best lap within a percentage of the fastest (107% rule)", function(){
           var participants = JSON.parse(JSON.stringify(participantsDefault));
					 var res = gridding.filterParticipantsInRound('bestLapWithinPercentOfFastest', 107, participants);
           expect(res.length).to.equal(2);
					 expect(res[0].bestLapTime).to.be.below(33.42038);
					 expect(res[1].bestLapTime).to.be.below(33.42038);
       });

   });
	 
	 describe("#bindParticipantsToHeatLineup()", function() {
			 
			 it("should correctly combine a round and participants", function(){
				var res = gridding.bindParticipantsToHeatLineup(participants.slice(0), roundOfHeats);
				expect(res).to.have.deep.property('[0][0].participantId', 'Wes');
				expect(res).to.have.deep.property('[0][1].participantId', 'Max');
				expect(res).to.have.deep.property('[0][2].participantId', 'Shakib');
				expect(res).to.have.deep.property('[1][0].participantId', 'Glenda');
				expect(res).to.have.deep.property('[1][1].participantId', 'Tommy');
       });

   });
	 
	 describe("#assignPoints()", function() {
			 
			 it("should assign the correct points", function(){
				var scoringTemplate = [1000, 900, 800, 700];
				var res = gridding.assignPoints(participants.slice(0), scoringTemplate);
				expect(res).to.have.deep.property('[0].points', 1000);
				expect(res).to.have.deep.property('[1].points', 900);
				expect(res).to.have.deep.property('[2].points', 800);
				expect(res).to.have.deep.property('[3].points', 700);
				expect(res).to.have.deep.property('[4].points', 0);
       });

   });
	 
});