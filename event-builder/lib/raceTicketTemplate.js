var _         = require('./underscore');
var z         = require('./zana');
var config    = require('./config-provider.js');
var CONSTANTS = require('./constants.js');
var utils     = require('./utils.js');
var rpad      = utils.receipts.rpad;
var lpad      = utils.receipts.lpad;
var cpad      = utils.receipts.cpad;
var buildLine = utils.receipts.buildLine;
var log       = utils.logging.log;
log.debug.on  = config.receipts.useDebugLogging;
var RACE_BY   = CONSTANTS.RACE_BY;
var WIN_BY    = CONSTANTS.WIN_BY;

var defaults = {
    "data": {
          "heat"        : {}
        , "customer"    : {}
        , "track"       : {}
    }
    , "resources": {
          "strAge"          : "Age"
        , "strBestLap"      : "Best Lap"
        , "strCustomer"     : "Customer"
        , "strDuration"     : "Duration"
        , "strEventName"    : "Event Name"
        , "strExperience"   : "Experience"
        , "strGrid"         : "Grid"
        , "strHeatNumber"   : "Heat No."
        , "strLaps"         : "Laps"
        , "strMinutes"      : "Minutes"
        , "strNA"           : "N/A"
        , "strNew"          : "New"
        , "strPosition"     : "Position"
        , "strRoundNo"      : "Round No."
        , "strSessions"     : "sessions"
        , "strTime"         : "Time"
        , "strVenue"        : "Venue"
        , "strWinBy"        : "Win By"
        , "raceTicketLine1" : ""
        , "raceTicketLine2" : ""
        , "raceTicketLine3" : ""
        , "raceTicketLine4" : ""
    }
    , "options": {
          "showScheduledTime"       : true
        , "printGridOnRaceTicket"   : true
        , "printAgeOnRaceTicket"    : true
        , "showHeatNo"              : true
        , "useHeatNumber"           : true
        , "numberOfTracks"          : 1
    }
    , "roundNumber"     : null
    , "eventName"       : null
};

function RaceTicketTemplate() {}
RaceTicketTemplate.prototype.create = function(body) {
  log.debug('----- building race ticket -----');
  log.debug('input:\n', body);
  if (!body)
      body = {};
  var receipt   = z.extend(body, defaults);
  var data      = receipt.data;
  var heat      = data.heat;
  var track     = data.track;
  var customer  = data.customer;
  var resources = receipt.resources;
  var options   = receipt.options;

  // log('receipt:', receipt);

  // Begin the receipt
  var output = '\n\n';

  // Show track name if we have more than one track
  if(options.numberOfTracks > 1)
    output += buildLine(resources.strVenue, track.description);
  log.debug('number of tracks');

  // log('output:', output);

  // If we are karting (sportId = 1), show "Win By" line
  if(track && track.sportId == 1) {
    var winByValue = (heat.winBy === WIN_BY.TIME ? resources.strBestLap : resources.strPosition);
    output += buildLine(resources.strWinBy, winByValue);
  }
  log.debug('win by');

  // Print the scheduled time
  if(options.showScheduledTime) {
    var scheduledDateTimeShort = ((heat.scheduledDateShort || '') + ' ' + (heat.scheduledTimeShort || '')).trim();
    output += buildLine(resources.strTime, scheduledDateTimeShort);
  }
  log.debug('scheduled time');

  // Print the heat number or sequence number
  if(options.showHeatNo && options.showHeatNo.toString().toLowerCase() !== 'false')
      output += buildLine(resources.strHeatNumber,  heat.heatNumber.toString().substr(-2));
  else
      output += buildLine(resources.strHeatNumber, heat.sequenceNumber); // not really part of the heat -- it's actually part of the TimeslotHeat ViewModel..
  log.debug('show heat number');

  // Print the duration
  if(heat.raceBy === RACE_BY.LAPS)
      output += buildLine(resources.strDuration, heat.lapsOrMinutes + ' ' + resources.strLaps);
  else {
      // RACE_BY.TIME
      var durationInMinutes = Math.round(parseInt(heat.lapsOrMinutes) / 60);
      if (isNaN(durationInMinutes) || !durationInMinutes)
        durationInMinutes = resources.strNA; // bad idea? looks less awkward than "NaN" on a receipt.
      output += buildLine(resources.strDuration, durationInMinutes + ' ' + resources.strMinutes);
  }
  log.debug('duration');

  output += '\n';

  // Print the event name
  if(receipt.eventName && receipt.eventName.toString().trim().length > 0) {
      output += buildLine(resources.strEventName, receipt.eventName);
      output += buildLine(resources.strRoundNo, receipt.roundNumber);
  }
  log.debug('event name');

  // Add customer name
  if (customer) {
    if (customer.fullName) {
      output += buildLine(resources.strCustomer, customer.fullName, null, 10);
      // Add racer name (if racer name exists and not the same as their full name)
      if (customer.racerName && customer.racerName.trim().length > 0 && customer.racerName.toLowerCase() !== customer.fullName.toLowerCase())
        output += buildLine('', customer.racerName, '  ');
    }
    // Add membership text
    if (customer.membershipText && customer.membershipText.trim().length > 0)
      output += buildLine('', customer.membershipText, '  ');
    customer.totalRaces = +customer.totalRaces;
    if (isNaN(customer.totalRaces))
      customer.totalRaces = 0;
    var experienceValue = customer.totalRaces === 0 ? resources.strNew : (customer.totalRaces + ' ' + resources.strSessions);
    output += buildLine(resources.strExperience, experienceValue);
    if(options.printGridOnRaceTicket && parseInt(customer.lineupPosition) > 0)
      output += buildLine(resources.strGrid, customer.lineupPosition);
    if(options.printAgeOnRaceTicket && customer.age)
      output += buildLine(resources.strAge, customer.age, null, 10);
  }
  log.debug('customer info');

  // Print customer-supplied footer lines
  if (resources.raceTicketLine1 && resources.raceTicketLine1.trim().length > 0)
    output += '\n' + resources.raceTicketLine1 + '\n';
  if (resources.raceTicketLine2 && resources.raceTicketLine2.trim().length > 0)
    output += resources.raceTicketLine2 + '\n';
  if (resources.raceTicketLine3 && resources.raceTicketLine3.trim().length > 0)
    output += resources.raceTicketLine3 + '\n';
  if (resources.raceTicketLine4 && resources.raceTicketLine4.trim().length > 0)
    output += resources.raceTicketLine4 + '\n';
  log.debug('footer');

  // Feed and Cut Paper
  output += '\n\n\n\n\n\n';
  output += ('\x1d\x56\x01');
  log.debug('feed & cut');

  log.debug('output:\n', output);
  return output;
};

module.exports = new RaceTicketTemplate();