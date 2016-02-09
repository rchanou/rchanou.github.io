/* eslint no-unused-expressions: 0 */ // for chai

"use strict";

var expect = require("chai").expect;
var builder = require("../lib/raceTicketTemplate.js");
var utils = require('../lib/utils.js');
utils.logging.log.debug.on = false;

function compare(input, expected) {
    var output = builder.create(input);
    expect(output).to.exist;
    expect(output).to.be.a('string');
    expect(output).to.not.be.empty;
    expect(output).to.equal(expected);
}

describe("Race Ticket Template", function() {

    it("should gracefully handle empty input", function() {
        var input = null;
        var expected = "\n\n      Time:                               \n  Heat No.:                               \n  Duration: N/A Minutes                   \n\nExperience: New                           \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should print venue with numberOfTracks > 1", function() {
        var input = {
            "data": {
                "track": {
                    "description": "Track 2"
                }
            },
            "options": {
                "numberOfTracks": 2
            }
        };
        var expected = "\n\n     Venue: Track 2                       \n      Time:                               \n  Heat No.:                               \n  Duration: N/A Minutes                   \n\nExperience: New                           \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse as win by best lap", function() {
        var input = {
            "data": {
                "track": {
                    "trackId": 2,
                    "sportId": 1,
                    "description": "Track 2"
                },
                "heat": {
                    "winBy": 0
                }
            }
        };
        var expected = "\n\n    Win By: Best Lap                      \n      Time:                               \n  Heat No.:                               \n  Duration: N/A Minutes                   \n\nExperience: New                           \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse as win by position", function() {
        var input = {
            "data": {
                "track": {
                    "trackId": 2,
                    "sportId": 1,
                    "description": "Track 2"
                },
                "heat": {
                    "winBy": 1
                }
            }
        };
        var expected = "\n\n    Win By: Position                      \n      Time:                               \n  Heat No.:                               \n  Duration: N/A Minutes                   \n\nExperience: New                           \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should ignore the win by line when sport id is not 1", function() {
        var input = {
            "data": {
                "track": {
                    "trackId": 2,
                    "sportId": 2,
                    "description": "laz0rtag"
                },
                "heat": {
                    "winBy": 1
                }
            }
        };
        var expected = "\n\n      Time:                               \n  Heat No.:                               \n  Duration: N/A Minutes                   \n\nExperience: New                           \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should hide the scheduled time", function() {
        var input = {
            "options": {
                "showScheduledTime": false
            }
        };
        var expected = "\n\n  Heat No.:                               \n  Duration: N/A Minutes                   \n\nExperience: New                           \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should print the heat number", function() {
        var input = {
            "data": {
                "heat": {
                      "heatNumber"     : "10601"
                    , "sequenceNumber" : "09"
                }
            },
            "options": {
                "showHeatNumber": true
            }
        };
        var expected = "\n\n      Time:                               \n  Heat No.: 01                            \n  Duration: N/A Minutes                   \n\nExperience: New                           \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should print the sequence number", function() {
        var input = {
            "data": {
                "heat": {
                      "heatNumber"     : "10601"
                    , "sequenceNumber" : "09"
                }
            },
            "options": {
                "showHeatNo": false
            }
        };
        var expected = "\n\n      Time:                               \n  Heat No.: 09                            \n  Duration: N/A Minutes                   \n\nExperience: New                           \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse as race by laps", function() {
        var input = {
            "data": {
                "heat": {
                    "raceBy": 1,
                    "lapsOrMinutes": 10
                }
            },
            "options": {
                "showHeatNumber": false
            }
        };
        var expected = "\n\n      Time:                               \n  Heat No.:                               \n  Duration: 10 Laps                       \n\nExperience: New                           \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse as race by minutes", function() {
        var input = {
            "data": {
                "heat": {
                    "raceBy": 2,
                    "lapsOrMinutes": 900
                }
            },
            "options": {
                "showHeatNumber": false
            }
        };
        var expected = "\n\n      Time:                               \n  Heat No.:                               \n  Duration: 15 Minutes                    \n\nExperience: New                           \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse event information", function() {
        var input = {
            "eventName": "Event 1",
            "roundNumber": 1
        };
        var expected = "\n\n      Time:                               \n  Heat No.:                               \n  Duration: N/A Minutes                   \n\nEvent Name: Event 1                       \n Round No.: 1                             \nExperience: New                           \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse customer information", function() {
        var input = {
            "data": {
                "customer": {
                    "fullName"       : "Jim Bob",
                    "racerName"      : "Bobblehead",
                    "membershipText" : "Supermember",
                    "totalRaces"     : 5
                }
            }
        };
        var expected = "\n\n      Time:                               \n  Heat No.:                               \n  Duration: N/A Minutes                   \n\n  Customer: Jim Bob                       \n            Bobblehead                    \n            Supermember                   \nExperience: 5 sessions                    \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should skip grid by option", function() {
        var input = {
            "data": {
                "customer": {
                    "lineupPosition" : 1
                }
            },
            "options": {
                "printGridOnRaceTicket" : "false"
            }
        };
        var expected = "\n\n      Time:                               \n  Heat No.:                               \n  Duration: N/A Minutes                   \n\nExperience: New                           \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should skip age by option", function() {
        var input = {
            "data": {
                "customer": {
                    "age" : 16
                }
            },
            "options": {
                "printAgeOnRaceTicket" : "false"
            }
        };
        var expected = "\n\n      Time:                               \n  Heat No.:                               \n  Duration: N/A Minutes                   \n\nExperience: New                           \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse race ticket lines", function() {
        var input = {
            "resources": {
                  "raceTicketLine1" : "TICKET LINE 1"
                , "raceTicketLine2" : "TICKET LINE 2"
                , "raceTicketLine3" : "TICKET LINE 3"
                , "raceTicketLine4" : "TICKET LINE 4"
            }
        };
        var expected = "\n\n      Time:                               \n  Heat No.:                               \n  Duration: N/A Minutes                   \n\nExperience: New                           \n\nTICKET LINE 1\nTICKET LINE 2\nTICKET LINE 3\nTICKET LINE 4\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should stretch key length with longer resources", function() {
        var input = {
            "resources": {
                "strExperience": "EXPEEEEEEERIENCE"
            }
        };
        var expected = "\n\n            Time:                         \n        Heat No.:                         \n        Duration: N/A Minutes             \n\nEXPEEEEEEERIENCE: New                     \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should handle standard input", function() {
        var input = {
            "data": {
                "heat": {
                    "trackName"          : "Track 1",
                    "trackNumber"        : 1,
                    "heatTypeName"       : "6 Times Race",
                    "winBy"              : 0,
                    "raceBy"             : 0,
                    "lapsOrMinutes"      : 360,
                    "sequenceNumber"     : 1,
                    "scheduledTime"      : "2015-04-11T11:45:00",
                    "heatNumber"         : 35,
                    "scheduledDateShort" : "2015/4/11",
                    "scheduledTimeShort" : "11:45"
                },
                "track": {
                    "trackNo"          : 1,
                    "description"      : "Track 1",
                    "mainLoopID"       : 1,
                    "pitEnterLoopId"   : 0,
                    "assignKartLoopId" : 1,
                    "penaltyBoxLoopId" : 0,
                    "printerName"      : "ResultPrinter1",
                    "trackLength"      : 1000,
                    "unitLength"       : "yard",
                    "gridSize"         : 15,
                    "autoRun"          : false,
                    "sportId"          : 1,
                    "allowAddOnRacing" : false
                },
                "customer": {
                    "custId"         : 1000002,
                    "lastName"       : "Webb",
                    "firstName"      : "Chris",
                    "racerName"      : "Chris Webb",
                    "totalRaces"     : 7,
                    "birthDate"      : "2/2/1980",
                    "rpm"            : 1205,
                    "membershipText" : "",
                    "status1"        : 2,
                    "status2"        : 0,
                    "status3"        : 0,
                    "status4"        : 0,
                    "kartNumber"     : -1,
                    "lineupPosition" : 1,
                    "finishPosition" : -1,
                    "pointHistoryId" : 0,
                    "groupId"        : 0,
                    "cell"           : "",
                    "privacy3"       : false,
                    "custom3"        : "",
                    "fullName"       : "Chris Webb",
                    "customerAge"    : "35"
                }
            },
            "eventName"      : "Event 2",
            "roundNumber"    : 5,
            "showHeatNumber" : false,
            "type"           : "raceTicket",
            "terminalName"   : null,
            "resources": {
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
                , "raceTicketLine1" : "Please present this ticket to track"
                , "raceTicketLine2" : " staff 5 minutes before your race "
                , "raceTicketLine3" : " time above. Enjoy!!!"
                , "raceTicketLine4" : ""
            },
            "options": {
                "showScheduledTime"            : false,
                "numberOfTracks"               : 3,
                "organizationNumber"           : "",
                "printAgeOnRaceTicket"         : "false",
                "printGridOnRaceTicket"        : "true",
                "printSurveyUrlOnReceipt"      : "false",
                "printVoidedPayments"          : false,
                "showHeatNo"                   : "true",
                "showScheduleTimeOnRaceTicket" : "",
                "urlSurvey"                    : "http://ikcshenyang.clubspeedtiming.com/sp_survey/",
                "clubSpeedLogoPath"            : "C:\\Clubspeed\\Images\\SS.bmp",
                "printGratuityLine"            : "none",
                "paymentSignaturesPath"        : "C:\\Clubspeed\\PaymentSignatures\\",
                "has2Taxes"                    : false,
                "receiptHeaderAlign"           : "",
                "companyLogoPath"              : "",
                "printDetail"                  : null,
                "accessCode"                   : null,
                "useESign"                     : true
            }
        };
        var expected = "\n\n     Venue: Track 1                       \n    Win By: Best Lap                      \n  Heat No.: 35                            \n  Duration: 6 Minutes                     \n\nEvent Name: Event 2                       \n Round No.: 5                             \n  Customer: Chris Webb                    \nExperience: 7 sessions                    \n      Grid: 1                             \n\nPlease present this ticket to track\n staff 5 minutes before your race \n time above. Enjoy!!!\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

});
