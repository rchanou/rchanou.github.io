/*jshint expr: true*/

var expect = require('chai').expect;
var builder = require('../lib/creditCardTemplate.js');
var utils = require('../lib/utils.js');
utils.logging.log.debug.on = false;

function compare(input, expected) {
    var output = builder.create(input);
    expect(output).to.exist;
    expect(output).to.be.a('string');
    expect(output).to.not.be.empty;
    expect(output).to.equal(expected);
}

describe('Credit Card Report Template', function() {

    it('should gracefully handle empty input', function() {
        var input = null;
        var expected = "\n\nCustomer                               N/A\nReceipt Number N/A                        \n------------------------------------------\nCredit Card Payment                       \n    \n   Auth No. # \n   Ref No. # \n   TroutD. # \n\n\nI agree to pay the above amount according\nto the card issuer agreement.\n\n\n\nSign here: X______________________________\n                                          \n\nTerminal:                                 \n\n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it('should parse void payment status', function() {
        var input = {
            "data": {
                "payment": {
                    "payStatus": 2
                }
            },
            "resources": {
                "strVoid": "VOID"
            }
        };
        var expected = "\n\n             ### VOIDED ###\n\nCustomer                               N/A\nReceipt Number N/A                        \n------------------------------------------\nCredit Card Payment                       \n    \n   Auth No. # \n   Ref No. # \n   TroutD. # \n\n\nI agree to pay the above amount according\nto the card issuer agreement.\n\n\n\nSign here: X______________________________\n                                          \n\nTerminal:                                 \n\n             ### VOIDED ###\n\n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it('should parse customer information', function() {
        var input = {
            "data": {
                "check": {
                    "custId": 1
                },
                "customer": {
                    "custId": 1,
                    "fullName": "Jim Bob"
                }
            },
            "resources": {
                "strCustomer": "Customer"
            }
        };
        var expected = "\n\nCustomer                           Jim Bob\nReceipt Number N/A                        \n------------------------------------------\nCredit Card Payment                       \n    \n   Auth No. # \n   Ref No. # \n   TroutD. # \n\n\nI agree to pay the above amount according\nto the card issuer agreement.\n\n\n\nSign here: X______________________________\n                                          \n\nTerminal:                                 \n\n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it('should parse check information', function() {
        var input = {
            "data": {
                "check": {
                    "checkId"         : "12345",
                    "openedDateShort" : "4/19/2015",
                    "openedDateTime"  : "4/19/2015 7:00PM",
                    "closedDateShort" : "4/20/2015",
                    "closedDateTime"  : "4/20/2015 10:00AM",
                    "gratuity"        : 0
                }
            },
            "resources": {
                "strReceiptNo": "Receipt Number"
            }
        };
        var expected = "\n\nCustomer                               N/A\nReceipt Number 12345      4/19/2015 7:00PM\n                         4/20/2015 10:00AM\n------------------------------------------\nCredit Card Payment                       \n    \n   Auth No. # \n   Ref No. # \n   TroutD. # \n\n\nI agree to pay the above amount according\nto the card issuer agreement.\n\n\n\nSign here: X______________________________\n                                          \n\nTerminal:                                 \n\n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it('should parse payment information', function() {
        var input = {
            "data": {
                "payment": {
                    "accountName"       : "JimBobInc.",
                    "amount"            : 10.45,
                    "authorizationCode" : "AUTH123",
                    "cardType"          : "VISA",
                    "lastFour"          : "1234",
                    "payAmountCurrency" : "$10.45",
                    "referenceNumber"   : "REF123",
                    "troutD"            : "TROUT123"
                }
            },
            "resources": {
                  "strPayment"  : "Payment"
                , "strTroutD"   : "TroutD. #"
                , "strRefNo"    : "Ref No. #"
                , "strAuthNo"   : "Auth No. #"
            }
        };
        var expected = "\n\nCustomer                               N/A\nReceipt Number N/A                        \n------------------------------------------\nCredit Card Payment                 $10.45\n   VISA 1234\n   Auth No. # AUTH123\n   Ref No. # REF123\n   TroutD. # TROUT123\n\n\nI agree to pay the above amount according\nto the card issuer agreement.\n\n\n\nSign here: X______________________________\n                                JimBobInc.\n\nTerminal:                                 \n\n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it('should ignore gratuity lines by default', function() {
        var input = {
            "data": {
                "check": {
                    "gratuity": 0
                }
            }
        };
        var expected = "\n\nCustomer                               N/A\nReceipt Number N/A                        \n------------------------------------------\nCredit Card Payment                       \n    \n   Auth No. # \n   Ref No. # \n   TroutD. # \n\n\nI agree to pay the above amount according\nto the card issuer agreement.\n\n\n\nSign here: X______________________________\n                                          \n\nTerminal:                                 \n\n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it('should ignore gratuity lines when check gratuity is non-zero', function() {
        var input = {
            "data": {
                "check": {
                    "gratuity": 1
                }
            },
            "options": {
                "printGratuityLine": "all"
            }
        };
        var expected = "\n\nCustomer                               N/A\nReceipt Number N/A                        \n------------------------------------------\nCredit Card Payment                       \n    \n   Auth No. # \n   Ref No. # \n   TroutD. # \n\n\nI agree to pay the above amount according\nto the card issuer agreement.\n\n\n\nSign here: X______________________________\n                                          \n\nTerminal:                                 \n\n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it('should include gratuity lines', function() {
        var input = {
            "data": {
                "check": {
                    "gratuity": 0
                }
            },
            "options": {
                "printGratuityLine": "all"
            }
        };
        var expected = "\n\nCustomer                               N/A\nReceipt Number N/A                        \n------------------------------------------\nCredit Card Payment                       \n    \n   Auth No. # \n   Ref No. # \n   TroutD. # \n\nGratuity                      ____________\n\n\nTotal                         ____________\n\n\nI agree to pay the above amount according\nto the card issuer agreement.\n\n\n\nSign here: X______________________________\n                                          \n\nTerminal:                                 \n\n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it('should include gratuity lines with eventonly and event check', function() {
        var input = {
            "data": {
                "check": {
                    "gratuity": 0,
                    "checkType": 1
                }
            },
            "options": {
                "printGratuityLine": "eventonly"
            }
        };
        var expected = "\n\nCustomer                               N/A\nReceipt Number N/A                        \n------------------------------------------\nCredit Card Payment                       \n    \n   Auth No. # \n   Ref No. # \n   TroutD. # \n\nGratuity                      ____________\n\n\nTotal                         ____________\n\n\nI agree to pay the above amount according\nto the card issuer agreement.\n\n\n\nSign here: X______________________________\n                                          \n\nTerminal:                                 \n\n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it('should ignore gratuity lines with eventonly and regular check', function() {
        var input = {
            "data": {
                "check": {
                    "gratuity": 0,
                    "checkType": 0
                }
            },
            "options": {
                "printGratuityLine": "eventonly"
            }
        };
        var expected = "\n\nCustomer                               N/A\nReceipt Number N/A                        \n------------------------------------------\nCredit Card Payment                       \n    \n   Auth No. # \n   Ref No. # \n   TroutD. # \n\n\nI agree to pay the above amount according\nto the card issuer agreement.\n\n\n\nSign here: X______________________________\n                                          \n\nTerminal:                                 \n\n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it('should insert signature split with useESign', function() {
        var input = {
            "options": {
                "useESign": true
            }
        };
        var expected = "\n\nCustomer                               N/A\nReceipt Number N/A                        \n------------------------------------------\nCredit Card Payment                       \n    \n   Auth No. # \n   Ref No. # \n   TroutD. # \n\n\nI agree to pay the above amount according\nto the card issuer agreement.\n{{SIGNATURE_SPLIT}}Sign here: X______________________________\n                                          \n\nTerminal:                                 \n\n\n\n\n\n\n\n\u001dV\u0001";
        compare(input, expected);
    });

    it('should handle standard input', function() {
        input = {
            "data": {
                "payment": {
                    "encryptedCardNo": "encryptedCardNo",
                    "cardType": "Visa",
                    "expirationDate": "2015/4/20 15:43:22",
                    "accountName": "AccountName",
                    "responseTime": 1.0,
                    "authorizationCode": "911",
                    "avs": "AVS",
                    "referenceNumber": "123refnumb",
                    "resultCode": "123 resultcode",
                    "troutD": "TroudD",
                    "autAmount": 100.0,
                    "lastFour": "2342",
                    "checkId": 0,
                    "payId": 1,
                    "userId": 1,
                    "payType": 2,
                    "payTerminal": "POS1",
                    "payAmount": 10.0,
                    "payDate": "2015-04-15T15:43:22.8008023-07:00",
                    "shift": 1,
                    "exchangeRate": 0.98,
                    "otherCurrency": "test",
                    "tender": 0.0,
                    "payStatus": 2,
                    "voidDate": "2015-04-15T15:43:22.8008023-07:00",
                    "voidUserId": 1,
                    "voidTerminal": "test",
                    "voidNotes": "voidPaymentNotes",
                    "payAmountCurrency": "¥10.00",
                    "tenderCurrency": "¥0.00",
                    "payDateShort": "2015/4/15",
                    "changeCurrency": "¥-10.00"
                },
                "check": {
                    "checkId": 6,
                    "custId": 1000002,
                    "checkSubtotal": 7790.0000,
                    "checkTax": 0.000000,
                    "checkTotal": 7790.000000,
                    "checkPaidTax": 0.00,
                    "checkPaidTotal": 6470.00,
                    "checkRemainingTotal": 1320.00,
                    "checkRemainingTax": 0.00,
                    "checkType": 1,
                    "checkStatus": 0,
                    "checkName": "",
                    "userId": 1,
                    "checkTotalApplied": 7790.00,
                    "brokerName": "",
                    "notes": "",
                    "gratuity": 0.00,
                    "fee": 0.00,
                    "openedDate": "2015-03-26T17:21:24.407",
                    "closedDate": null,
                    "isTaxExempt": false,
                    "discount": 0.00,
                    "invoiceDate": null,
                    "checkGST": 0.000000,
                    "checkPST": 0.000000,
                    "openedDateShort": "2015/3/26",
                    "closedDateShort": "",
                    "openedDateTime": "2015/3/26 17:21",
                    "closedDateTime": "",
                    "checkSubtotalCurrency": "¥7,790.00",
                    "checkTotalCurrency": "¥7,790.00",
                    "checkTaxCurrency": "¥0.00",
                    "feeCurrency": "¥0.00",
                    "totalCurrency": "¥7,790.00",
                    "gratuityCurrency": "¥0.00",
                    "checkGSTCurrency": "¥0.00",
                    "checkRemainingTotalCurrency": "¥1,320.00",
                    "checkPSTCurrency": "¥0.00",
                    "discountCurrency": "¥0.00"
                },
                "customer": {
                    "custId": 1000002,
                    "cardId": -1,
                    "lastName": "Webb",
                    "firstName": "Chris",
                    "racerName": "Chris Webb",
                    "accountCreated": "2015-03-25T10:44:11.347",
                    "lastVisited": "2015-04-08T09:11:21.197",
                    "totalVisits": 5,
                    "totalRaces": 7,
                    "address": "",
                    "city": "",
                    "state": "",
                    "country": "",
                    "phoneNumber": "",
                    "birthDate": "1980-02-02T00:00:00",
                    "emailAddress": "",
                    "company": "",
                    "generalNotes": "",
                    "zip": "",
                    "licenseNumber": "",
                    "issuedBy": "",
                    "sourceId": 1,
                    "doNotMail": false,
                    "gender": 0,
                    "rpm": 1205,
                    "waiverId": 1,
                    "originalId": 1,
                    "isGiftCard": false,
                    "award1": 0,
                    "award2": 2,
                    "hotel": "",
                    "priceLevel": 1,
                    "waiverId2": 7,
                    "membershipStatus": 0,
                    "password": "",
                    "webUserName": "",
                    "custom1": "",
                    "custom2": "",
                    "custom3": "",
                    "custom4": "",
                    "privacy1": false,
                    "privacy2": false,
                    "privacy3": false,
                    "privacy4": false,
                    "refId": 0,
                    "industryId": 0,
                    "phoneNumber2": "",
                    "fax": "",
                    "cell": "",
                    "address2": "",
                    "promotionCode": "",
                    "membershipText": "",
                    "membershipTextLong": "",
                    "deleted": false,
                    "isEmployee": false,
                    "status1": 2,
                    "status2": 0,
                    "status3": 0,
                    "status4": 0,
                    "creditLimit": 0.0,
                    "creditOnhold": false,
                    "ignoreDOB": false,
                    "cnt": 1000,
                    "fullName": "Chris Webb"
                }
            },
            "type": "creditCard",
            "terminalName": null,
            "resources": {
                  "strCC"        : "Credit Card"
                , "strCrdIssuer" : "to the card issuer agreement."
                , "strGratuity2" : "Gratuity"
                , "strIAgree"    : "I agree to pay the above amount according"
                , "strNA"        : "N/A"
                , "strPayment"   : "Payment"
                , "strReceiptNo" : "Receipt Number"
                , "strRefund"    : "Refund"
                , "strTerminal"  : "Terminal:"
                , "strTotal2"    : "Total"
                , "strType"      : "Type:"
                , "strVoided"    : "VOIDED"
            },
            "options": {
                "useESign": true,
                "printGratuityLine": "all"
            }
        };
        expected = "\n\n             ### VOIDED ###\n\nCustomer                        Chris Webb\nReceipt Number 6           2015/3/26 17:21\n------------------------------------------\nCredit Card Payment                ¥10.00\n   Visa 2342\n   Auth No. # 911\n   Ref No. # 123refnumb\n   TroutD. # TroudD\n\nGratuity                      ____________\n\n\nTotal                         ____________\n\n\nI agree to pay the above amount according\nto the card issuer agreement.\n{{SIGNATURE_SPLIT}}Sign here: X______________________________\n                               AccountName\n\nTerminal:                                 \n\n             ### VOIDED ###\n\n\n\n\n\n\n\n\u001dV\u0001";
        output = builder.create(input);
        expect(output).to.exist;
        expect(output).to.be.a('string');
        expect(output).to.not.be.empty;
        expect(output).to.equal(expected);
    });

});