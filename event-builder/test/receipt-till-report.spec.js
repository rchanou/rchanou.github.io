/*jshint expr: true*/

var expect  = require("chai").expect;
var builder = require("../lib/tillReportTemplate.js");
var utils   = require('../lib/utils.js');
utils.logging.log.debug.on = false;

function compare(input, expected) {
    var output = builder.create(input);
    expect(output).to.exist;
    expect(output).to.be.a('string');
    expect(output).to.not.be.empty;
    expect(output).to.equal(expected);
}

describe("Till Report Template", function() {

    it("should gracefully handle empty input", function() {
        var input = null;
        var expected = "\n\n           ### TILL REPORT ###           \nTerminal:                                \n                                         \n\nShift  Payment Type                Amount\nCash in Drawer                           \n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it("should parse till report header", function() {
        var input = {
            "nowDateLong"  : "April 20, 2015",
            "nowTimeShort" : "12:00PM",
            "terminalName" : "POS2"
        };
        var expected = "\n\n           ### TILL REPORT ###           \nTerminal: POS2                           \nApril 20, 2015 12:00PM                   \n\nShift  Payment Type                Amount\nCash in Drawer                           \n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it("should parse a payment", function() {
        var input = {
            "data": {
                "payments": [
                    {
                        "paymentType": "Cash",
                        "paymentAmountCurrency": "$10.00"
                    }
                ]
            }
        };
        var expected = "\n\n           ### TILL REPORT ###           \nTerminal:                                \n                                         \n\nShift  Payment Type                Amount\n1      Cash                        $10.00\nCash in Drawer                           \n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it("should parse multiple payments", function() {
        var input = {
            "data": {
                "payments": [
                    {
                        "paymentType": "Expense",
                        "paymentAmountCurrency": "$10.00"
                    },
                    {
                        "paymentType": "Credit Card",
                        "paymentAmountCurrency": "$100.00"
                    }
                ]
            }
        };
        var expected = "\n\n           ### TILL REPORT ###           \nTerminal:                                \n                                         \n\nShift  Payment Type                Amount\n1      Expense                     $10.00\n1      Credit Card                $100.00\nCash in Drawer                           \n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it("should handle standard input", function() {
        var input = {
            "data": {
                "payments": [
                    {
                        "paymentType": "Cash",
                        "paymentAmount": 5,
                        "paymentAmountCurrency": "¥5.00"
                    },
                    {
                        "paymentType": "Expense",
                        "paymentAmount": -23,
                        "paymentAmountCurrency": "¥-23.00"
                    }
                ]
            },
            "shift": 1,
            "day": "2015-04-14T16:17:01.594212-07:00",
            "timestampISO": "2015-04-14T16:17:01.6098425-07:00",
            "totalCash": "¥120.00",
            "nowDateLong": "2015年4月14日",
            "nowTimeShort": "16:17",
            "type": "tillReport",
            "terminalName": "POS1",
            "resources": {
                "strTillReport"   : "TILL REPORT",
                "strCashInDrawer" : "Cash in Drawer",
                "strShiftPayment" : "Shift  Payment Type",
                "strAmount"       : "Amount",
                "strTerminal"     : "Terminal"
            }
        };
        var expected = "\n\n           ### TILL REPORT ###           \nTerminal: POS1                           \n2015年4月14日 16:17                      \n\nShift  Payment Type                Amount\n1      Cash                        ¥5.00\n1      Expense                   ¥-23.00\nCash in Drawer                           \n\n\n\n\n\n\n\u001dV\u0001";
        output = builder.create(input);
        compare(input, expected);
    });

});