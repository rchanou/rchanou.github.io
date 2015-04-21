/*jshint expr: true*/

var expect = require("chai").expect;
var builder = require("../lib/entitleItemTemplate.js");
var utils = require('../lib/utils.js');
utils.logging.log.debug.on = false;

function compare(input, expected) {
    var output = builder.create(input);
    expect(output).to.exist;
    expect(output).to.be.a('string');
    expect(output).to.not.be.empty;
    expect(output).to.equal(expected);
}

describe("Entitle Item Receipt Template", function() {

    it("should gracefully handle empty input", function() {
        var input = null;
        var expected = "\n\n";
        compare(input, expected);
    });

    it("should parse laps", function() {
        var input = {
            "data": {
                "checkDetails": [
                    {
                        "checkDetailId" : 1,
                        "qty"           : 1,
                        "s_vol"         : 1,
                        "s_saleBy"      : 0,
                        "productName"   : "Laps Product",
                        "s_laps"        : 5
                    }
                ]
            },
            "resources": {
                  "strId"        : "ID:"
                , "strLaps"      : "Laps:"
                , "strTime2"     : "Time:"
                , "strType"      : "Type:"
            },
            "nowDateShort": "4/20/2015",
            "nowTimeShort": "10:00AM"
        };
        var expected = "\n\n       ID: 1\n     Type: Laps Product\n     Laps: 5\n     Time: 4/20/2015 10:00AM\n\n\n{{BARCODE=1}}";
        compare(input, expected);
    });

    it("should parse minutes", function() {
        var input = {
            "data": {
                "checkDetails": [
                    {
                        "checkDetailId"     : 1,
                        "qty"               : 1,
                        "s_vol"             : 1,
                        "s_saleBy"          : 1,
                        "productName"       : "Minutes Product",
                        "s_minutesFixed"    : "90.50"
                    }
                ]
            },
            "resources": {
                  "strId"        : "ID:"
                , "strMinutes2"  : "Minutes:"
                , "strTime2"     : "Time:"
                , "strType"      : "Type:"
            },
            "nowDateShort": "4/20/2015",
            "nowTimeShort": "10:00AM"
        };
        var expected = "\n\n       ID: 1\n     Type: Minutes Product\n  Minutes: 90.50\n     Time: 4/20/2015 10:00AM\n\n\n{{BARCODE=1}}";
        compare(input, expected);
    });

    it("should parse race ticket lines", function() {
        var input = {
            "data": {
                "checkDetails": [
                    {
                        "checkDetailId"     : 1,
                        "qty"               : 1,
                        "s_vol"             : 1,
                        "s_saleBy"          : 1,
                        "productName"       : "Minutes Product",
                        "s_minutesFixed"    : 90.50
                    }
                ]
            },
            "resources": {
                  "strId"           : "ID:"
                , "strMinutes2"     : "Minutes:"
                , "strTime2"        : "Time:"
                , "strType"         : "Type:"
                , "raceTicketLine1" : "TICKET LINE 1"
                , "raceTicketLine2" : "TICKET LINE 2"
                , "raceTicketLine3" : "TICKET LINE 3"
                , "raceTicketLine4" : "TICKET LINE 4"
            },
            "nowDateShort": "4/20/2015",
            "nowTimeShort": "10:00AM"
        };
        var expected = "\n\n       ID: 1\n     Type: Minutes Product\n  Minutes: 90.5\n     Time: 4/20/2015 10:00AM\n\nTICKET LINE 1\nTICKET LINE 2\nTICKET LINE 3\nTICKET LINE 4\n\n{{BARCODE=1}}";
        compare(input, expected);
    });

    it("should parse qty > 1", function() {
        var input = {
            "data": {
                "checkDetails": [
                    {
                        "checkDetailId" : 1,
                        "qty"           : 2,
                        "s_vol"         : 1,
                        "s_saleBy"      : 0,
                        "productName"   : "Laps Product",
                        "s_laps"        : 5
                    }
                ]
            },
            "resources": {
                  "strId"        : "ID:"
                , "strLaps"      : "Laps:"
                , "strTime2"     : "Time:"
                , "strType"      : "Type:"
            },
            "nowDateShort": "4/20/2015",
            "nowTimeShort": "10:00AM"
        };
        var expected = "\n\n       ID: 1\n     Type: Laps Product\n     Laps: 5\n     Time: 4/20/2015 10:00AM\n\n\n{{BARCODE=1}}       ID: 1\n     Type: Laps Product\n     Laps: 5\n     Time: 4/20/2015 10:00AM\n\n\n{{BARCODE=1}}";
        compare(input, expected);
    });

    it("should parse s_vol > 1", function() {
        var input = {
            "data": {
                "checkDetails": [
                    {
                        "checkDetailId" : 1,
                        "qty"           : 1,
                        "s_vol"         : 2,
                        "s_saleBy"      : 0,
                        "productName"   : "Laps Product",
                        "s_laps"        : 5
                    }
                ]
            },
            "resources": {
                  "strId"        : "ID:"
                , "strLaps"      : "Laps:"
                , "strTime2"     : "Time:"
                , "strType"      : "Type:"
            },
            "nowDateShort": "4/20/2015",
            "nowTimeShort": "10:00AM"
        };
        var expected = "\n\n       ID: 1\n     Type: Laps Product(1)\n     Laps: 5\n     Time: 4/20/2015 10:00AM\n\n\n{{BARCODE=1}}       ID: 1\n     Type: Laps Product(2)\n     Laps: 5\n     Time: 4/20/2015 10:00AM\n\n\n{{BARCODE=1}}";
        compare(input, expected);
    });

    it("should parse multiple check details", function() {
        var input = {
            "data": {
                "checkDetails": [
                    {
                        "checkDetailId" : 1,
                        "qty"           : 1,
                        "s_vol"         : 1,
                        "s_saleBy"      : 0,
                        "productName"   : "Laps Product",
                        "s_laps"        : 5
                    },
                    {
                        "checkDetailId" : 2,
                        "qty"           : 1,
                        "s_vol"         : 1,
                        "s_saleBy"      : 0,
                        "productName"   : "Laps Product",
                        "s_laps"        : 5
                    }
                ]
            },
            "resources": {
                  "strId"        : "ID:"
                , "strLaps"      : "Laps:"
                , "strTime2"     : "Time:"
                , "strType"      : "Type:"
            },
            "nowDateShort": "4/20/2015",
            "nowTimeShort": "10:00AM"
        };
        var expected = "\n\n       ID: 1\n     Type: Laps Product\n     Laps: 5\n     Time: 4/20/2015 10:00AM\n\n\n{{BARCODE=1}}       ID: 2\n     Type: Laps Product\n     Laps: 5\n     Time: 4/20/2015 10:00AM\n\n\n{{BARCODE=2}}";
        compare(input, expected);
    });

    it("should parse all multiples", function() {
        var input = {
            "data": {
                "checkDetails": [
                    {
                        "checkDetailId" : 1,
                        "qty"           : 2,
                        "s_vol"         : 1,
                        "s_saleBy"      : 0,
                        "productName"   : "Laps Product 1",
                        "s_laps"        : 5
                    },
                    {
                        "checkDetailId" : 2,
                        "qty"           : 1,
                        "s_vol"         : 2,
                        "s_saleBy"      : 1,
                        "productName"   : "Minutes Product 2",
                        "s_minutesFixed": "33.33"
                    }
                ]
            },
            "resources": {
                  "strId"        : "ID:"
                , "strLaps"      : "Laps:"
                , "strTime2"     : "Time:"
                , "strType"      : "Type:"
            },
            "nowDateShort": "4/20/2015",
            "nowTimeShort": "10:00AM"
        };
        var expected = "\n\n       ID: 1\n     Type: Laps Product 1\n     Laps: 5\n     Time: 4/20/2015 10:00AM\n\n\n{{BARCODE=1}}       ID: 1\n     Type: Laps Product 1\n     Laps: 5\n     Time: 4/20/2015 10:00AM\n\n\n{{BARCODE=1}}       ID: 2\n     Type: Minutes Product 2(1)\n  Minutes: 33.33\n     Time: 4/20/2015 10:00AM\n\n\n{{BARCODE=2}}       ID: 2\n     Type: Minutes Product 2(2)\n  Minutes: 33.33\n     Time: 4/20/2015 10:00AM\n\n\n{{BARCODE=2}}";
        compare(input, expected);
    });
    
});