/*jshint expr: true*/

var expect = require("chai").expect;
var builder = require("../lib/expenseTemplate.js");
var utils = require('../lib/utils.js');
utils.logging.log.debug.on = false;

function compare(input, expected) {
    var output = builder.create(input);
    expect(output).to.exist;
    expect(output).to.be.a('string');
    expect(output).to.not.be.empty;
    expect(output).to.equal(expected);
}

describe("Expense Receipt Template", function() {

    it("should gracefully handle empty input", function() {
        var input = null;
        var expected = "\n\n              ###EXPENSE###\nTerminal: \n \nShift: 0\n\n                                \n          \n\n\n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it("should handle standard input", function() {
        var input = {
            "data": {
                  "description"    : "Some Description"
                , "amount"         : 1200.34
                , "amountCurrency" : "$1,200.34"
                , "shift"          : 1
            },
            "resources": {
                "strExpense2" : "EXPENSE",
                "strShift"    : "Shift:",
                "strTerminal" : "Terminal:"
            },
            "terminalName"   : "POS1"
            , "now"          : "2015-04-13T14:38:58.72153-07:00"
            , "nowDateShort" : "04/13/2015"
            , "nowTimeShort" : "2:18PM"
        };
        var expected = "\n\n              ###EXPENSE###\nTerminal: POS1\n04/13/2015 2:18PM\nShift: 1\n\nSome Description                \n $1,200.34\n\n\n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

});