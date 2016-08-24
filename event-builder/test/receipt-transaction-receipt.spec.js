/* eslint no-unused-expressions: 0 */ // for chai

"use strict";

var expect  = require("chai").expect;
var builder = require("../lib/transactionReceiptTemplate.js");
var utils   = require('../lib/utils.js');
utils.logging.log.debug.on = false;

function compare(input, expected) {
    var output = builder.create(input);
    expect(output).to.exist;
    expect(output).to.be.a('string');
    expect(output).to.not.be.empty;
    expect(output).to.equal(expected);
}

describe("Transaction Receipt Template", function() {

    it("should gracefully handle empty input", function() {
        var input = null;
        var expected = "\n\n\nReceipt Number N/A                        \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should print company logo placeholder", function() {
        var input = {
            "options": {
                "companyLogoPath": "c:\\something\\greater\\than\\0\\length.bmp"
            }
        };
        var expected = "\n\n{{CompanyLogo}}\n\nReceipt Number N/A                        \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse receipt header", function() {
        var input = {
            "resources": {
                  "receiptHeaderText1" : "HEADER 1"
                , "receiptHeaderText2" : "HEADER 2"
                , "receiptHeaderText3" : "HEADER 3"
                , "receiptHeaderText4" : "HEADER 4"
            }
        };
        var expected = "\n\n                 HEADER 1\n                 HEADER 2\n                 HEADER 3\n                 HEADER 4\n\nReceipt Number N/A                        \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should print organization number", function() {
        var input = {
            "options": {
                "organizationNumber": "1QX3T4"
            }
        };
        var expected = "\n\nOrg. #: 1QX3T4\n\nReceipt Number N/A                        \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse event information", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1,
                    "checkType": 2
                },
                "event": {
                    "createdBy"      : "Jim Bob Sr.",
                    "endTime"        : "8:00PM",
                    "numberOfRacers" : 10,
                    "startDate"      : "4/21/2015",
                    "startTime"      : "7:00PM",
                    "subject"        : "EVENT SUBJECT"
                }
            }
        };
        var expected = "\n\nEvent Information\nName: EVENT SUBJECT\n10 Racers\n4/21/2015 7:00PM to 8:00PM\nCreated by Jim Bob Sr.\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should ignore a missing event subject", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1,
                    "checkType": 2
                },
                "event": {
                    "createdBy"      : "Jim Bob Sr.",
                    "endTime"        : "8:00PM",
                    "numberOfRacers" : 10,
                    "startDate"      : "4/21/2015",
                    "startTime"      : "7:00PM"
                }
            }
        };
        var expected = "\n\nEvent Information\n10 Racers\n4/21/2015 7:00PM to 8:00PM\nCreated by Jim Bob Sr.\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should ignore a missing event end time", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1,
                    "checkType": 2
                },
                "event": {
                    "createdBy"      : "Jim Bob Sr.",
                    "numberOfRacers" : 10,
                    "startDate"      : "4/21/2015",
                    "startTime"      : "7:00PM"
                }
            }
        };
        var expected = "\n\nEvent Information\n10 Racers\n4/21/2015 7:00PM\nCreated by Jim Bob Sr.\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should ignore a missing event start time", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1,
                    "checkType": 2
                },
                "event": {
                    "createdBy"      : "Jim Bob Sr.",
                    "numberOfRacers" : 10,
                    "startDate"      : "4/21/2015"
                }
            }
        };
        var expected = "\n\nEvent Information\n10 Racers\n4/21/2015\nCreated by Jim Bob Sr.\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should ignore event end time when start time is missing", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1,
                    "checkType": 2
                },
                "event": {
                    "createdBy"      : "Jim Bob Sr.",
                    "endTime"        : "8:00PM",
                    "numberOfRacers" : 10,
                    "startDate"      : "4/21/2015"
                }
            }
        };
        var expected = "\n\nEvent Information\n10 Racers\n4/21/2015\nCreated by Jim Bob Sr.\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should skip event date line when start date is missing", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1,
                    "checkType": 2
                },
                "event": {
                    "createdBy"      : "Jim Bob Sr.",
                    "endTime"        : "8:00PM",
                    "numberOfRacers" : 10,
                    "startTime"      : "7:00PM"
                }
            }
        };
        var expected = "\n\nEvent Information\n10 Racers\nCreated by Jim Bob Sr.\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse main customer information", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1,
                    "custId": 1000001
                },
                "customers": [
                    {
                        "custId": 1000001,
                        "fullName": "Jim Bob"
                    }
                ]
            }
        };
        var expected = "\n\n\nCustomer: Jim Bob\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should gracefully skip null customers", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1,
                    "custId": 1000001
                },
                "customers": [
                    null,
                    {
                        "custId": 1000001,
                        "fullName": "Jim Bob"
                    },
                    null,
                    {
                        "custId": 1000001,
                        "fullName": "Jim Bob"
                    },
                    null
                ]
            }
        };
        var expected = "\n\n\nCustomer: Jim Bob\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse check information", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1,
                    "custId": 1000001,
                    "openedDateTime": "4/19/2015 7:00PM",
                    "openedDateShort": "4/19/2015",
                    "closedDateTime": "4/20/2015 10:00AM",
                    "closedDateShort": "4/20/2015"
                },
                "customers": [
                    {
                        "custId": 1000001,
                        "fullName": "Jim Bob"
                    }
                ]
            }
        };
        var expected = "\n\n\nCustomer: Jim Bob\nReceipt Number 1          4/19/2015 7:00PM\n                         4/20/2015 10:00AM\n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse standard products", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1
                },
                "checkDetails": [
                    {
                        "checkDetailId"               : 1,
                        "checkDetailSubtotalCurrency" : "$14.25",
                        "checkId"                     : 1,
                        "productId"                   : 1,
                        "productName"                 : "Regular Product",
                        "qty"                         : 1
                    }
                ],
                "products": [
                    {
                        "productId": 1,
                        "productType": 1
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\nRegular Product                     $14.25\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse point products", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1
                },
                "customers": [
                    {
                        "custId"   : 1000002,
                        "fullName" : "Points Customer"
                    }
                ],
                "checkDetails": [
                    {
                        "checkDetailId"               : 1,
                        "checkDetailSubtotalCurrency" : "$14.25",
                        "checkId"                     : 1,
                        "productId"                   : 2,
                        "productName"                 : "Points Product",
                        "qty"                         : 1,
                        "p_custId"                    : 1000002
                    }
                ],
                "products": [
                    {
                        "productId"   : 2,
                        "productType" : 2
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\nPoints Product                      $14.25\n  Points Customer\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse membership products", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1
                },
                "customers": [
                    {
                        "custId"   : 1000006,
                        "fullName" : "Membership Customer"
                    }
                ],
                "checkDetails": [
                    {
                        "checkDetailId"               : 1,
                        "checkDetailSubtotalCurrency" : "$14.25",
                        "checkId"                     : 1,
                        "productId"                   : 6,
                        "productName"                 : "Membership Product",
                        "qty"                         : 1,
                        "m_custId"                    : 1000006
                    }
                ],
                "products": [
                    {
                        "productId"   : 6,
                        "productType" : 6
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\nMembership Product                  $14.25\n  Membership Customer\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse food products", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1
                },
                "checkDetails": [
                    {
                        "checkDetailId"               : 1,
                        "checkDetailSubtotalCurrency" : "$14.25",
                        "checkId"                     : 1,
                        "productId"                   : 3,
                        "productName"                 : "Food Product",
                        "qty"                         : 1
                    }
                ],
                "products": [
                    {
                        "productId"   : 3,
                        "productType" : 3
                    }
                ],
                "foodSubitems": [
                    {
                        "checkDetailId": 1,
                        "description": "Subitem1"
                    },
                    {
                        "checkDetailId": 1,
                        "description": "Subitem2"
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\nFood Product                        $14.25\n Subitem1\n Subitem2\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should always parse event products", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1,
                    "checkType": 2
                },
                "checkDetails": [
                    {
                        "checkDetailId"               : 1,
                        "checkDetailSubtotalCurrency" : "$14.25",
                        "checkId"                     : 1,
                        "productId"                   : 4,
                        "productName"                 : "Reservation Product",
                        "qty"                         : 1
                    }
                ],
                "products": [
                    {
                        "productId"   : 4,
                        "productType" : 4,
                        "description" : "Event Product Description"
                    }
                ]
            },
            "options": {
                "printDetail": false
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\nEvent Product Descript...           $14.25\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse check detail level discounts", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1
                },
                "customers": [
                    {
                        "custId"   : 1000006,
                        "fullName" : "Membership Customer"
                    }
                ],
                "checkDetails": [
                    {
                          "checkDetailId"               : 1
                        , "checkDetailSubtotalCurrency" : "$16.25"
                        , "checkId"                     : 1
                        , "productId"                   : 6
                        , "productName"                 : "Membership Product"
                        , "qty"                         : 1
                        , "m_custId"                    : 1000006
                        , "discountApplied"             : 6.0
                        , "discountAppliedCurrency"     : "$6.00"
                        , "discountDesc"                : "$6 coupon"
                    }
                ],
                "products": [
                    {
                        "productId"   : 6,
                        "productType" : 6
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\nMembership Product                  $16.25\n  Membership Customer\n  $6 coupon($6.00)\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse check detail level discounts on event checks", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1,
                    "checkType": 2
                },
                "customers": [
                    {
                        "custId" : 1000006
                    }
                ],
                "checkDetails": [
                    {
                          "checkDetailId"               : 1
                        , "checkDetailSubtotalCurrency" : "$16.25"
                        , "checkId"                     : 1
                        , "productId"                   : 6
                        , "productName"                 : "Some Product"
                        , "qty"                         : 1
                        , "discountApplied"             : 6.0
                        , "discountAppliedCurrency"     : "$6.00"
                        , "discountDesc"                : "$6 coupon"
                    }
                ],
                "products": [
                    {
                        "productId"   : 6,
                        "productType" : 6
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\nSome Product                        $16.25\n  $6 coupon($6.00)\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should not parse voided check details", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1
                },
                "customers": [
                    {
                        "custId"   : 1000006,
                        "fullName" : "Membership Customer"
                    }
                ],
                "checkDetails": [
                    {
                          "checkDetailId"               : 1
                        , "checkDetailSubtotalCurrency" : "$16.25"
                        , "checkId"                     : 1
                        , "productId"                   : 6
                        , "productName"                 : "Membership Product"
                        , "qty"                         : 1
                        , "m_custId"                    : 1000006
                        , "checkDetailStatus"           : 2
                    }
                ],
                "products": [
                    {
                        "productId"   : 6,
                        "productType" : 6
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse multiple check details", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1
                },
                "customers": [
                    {
                        "custId"   : 1000006,
                        "fullName" : "Membership Customer"
                    },
                    {
                        "custId": 1000002,
                        "fullName": "Points Customer"
                    }
                ],
                "checkDetails": [
                    {
                          "checkDetailId"               : 1
                        , "checkDetailSubtotalCurrency" : "$16.25"
                        , "checkId"                     : 1
                        , "productId"                   : 6
                        , "productName"                 : "Membership Product"
                        , "qty"                         : 1
                        , "m_custId"                    : 1000006
                        , "discountApplied"             : 6.0
                        , "discountAppliedCurrency"     : "$6.00"
                        , "discountDesc"                : "$6 coupon"
                    },
                    {
                          "checkDetailId"               : 2
                        , "checkDetailSubtotalCurrency" : "$24.50"
                        , "checkId"                     : 1
                        , "productId"                   : 2
                        , "productName"                 : "Points Product"
                        , "qty"                         : 2
                        , "p_custId"                    : 1000002
                        , "discountApplied"             : 2.0
                        , "discountAppliedCurrency"     : "$2.00"
                        , "discountDesc"                : "$2 coupon"
                    },
                    {
                          "checkDetailId"               : 3
                        , "checkDetailSubtotalCurrency" : "$13.25"
                        , "checkId"                     : 1
                        , "productId"                   : 3
                        , "productName"                 : "Food Product"
                        , "qty"                         : 1
                    }
                ],
                "products": [
                    {
                        "productId"   : 6,
                        "productType" : 6
                    },
                    {
                        "productId"   : 2,
                        "productType" : 2
                    },
                    {
                        "productId"   : 3,
                        "productType" : 3
                    }
                ],
                "foodSubitems": [
                    {
                        "checkDetailId": 3,
                        "description": "Subitem1"
                    },
                    {
                        "checkDetailId": 3,
                        "description": "Subitem2"
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\nMembership Product                  $16.25\n  Membership Customer\n  $6 coupon($6.00)\n2)Points Product                    $24.50\n  Points Customer\n  $2 coupon($2.00)\nFood Product                        $13.25\n Subitem1\n Subitem2\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse multiple check details on event checks", function() {
        var input = {
            "data": {
                "check": {
                      "checkId"   : 1
                    , "checkType" : 2
                },
                "checkDetails": [
                    {
                          "checkDetailId"               : 1
                        , "checkDetailSubtotalCurrency" : "$16.25"
                        , "checkId"                     : 1
                        , "productId"                   : 4
                        , "productName"                 : "Reservation Product"
                        , "qty"                         : 1
                    },
                    {
                          "checkDetailId"               : 2
                        , "checkDetailSubtotalCurrency" : "$24.50"
                        , "checkId"                     : 1
                        , "productId"                   : 4
                        , "productName"                 : "Reservation Product"
                        , "qty"                         : 2
                    }
                ]
                , "products": [
                    {
                          "productId": 4
                        , "productType": 4
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\nReservation Product                 $16.25\n2)Reservation Product               $24.50\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse standard check totals", function() {
        var input = {
            "data": {
                "check": {
                      "checkId"               : 1
                    , "gratuity"              : 2.0
                    , "fee"                   : 1.0
                    , "checkSubtotalCurrency" : "$14.00"
                    , "feeCurrency"           : "$1.00"
                    , "gratuityCurrency"      : "$2.00"
                    , "checkTaxCurrency"      : "$1.00"
                    , "checkTotalCurrency"    : "$18.00"
                }
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                            $14.00\nTax                                  $1.00\nFee                                  $1.00\nGratuity                             $2.00\n------------------------------------------\nTotal                               $18.00\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse tax exempt check totals", function() {
        var input = {
            "data": {
                "check": {
                      "checkId"               : 1
                    , "checkSubtotalCurrency" : "$14.00"
                    , "checkTaxCurrency"      : "$0.00"
                    , "checkTotalCurrency"    : "$14.00"
                    , "isTaxExempt"           : true
                }
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                            $14.00\nTax(Exempt)                          $0.00\n------------------------------------------\nTotal                               $14.00\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse split tax check totals", function() {
        var input = {
            "data": {
                "check": {
                      "checkId"               : 1
                    , "checkSubtotalCurrency" : "$14.00"
                    , "checkGST"              : 0.75
                    , "checkGSTCurrency"      : "$0.75"
                    , "checkPSTCurrency"      : "$0.25"
                    , "checkTotalCurrency"    : "$15.00"
                }
            },
            "options": {
                "has2Taxes": true
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                            $14.00\nGST                                  $0.75\nPST                                  $0.25\n------------------------------------------\nTotal                               $15.00\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse multiple taxes", function() {
        var input = {
            "data": {
                "check": {
                      "checkId"               : 1
                    , "checkSubtotalCurrency" : "$14.00"
                    , "checkTaxCurrency"      : "$5.00"
                    , "checkTotalCurrency"    : "$19.00"
                }
                , "taxes": [
                    {
                        "taxId": 1
                        , "taxPercent": "5.0%"
                        , "taxTotal": "$2.00"
                    }
                    , {
                        "taxId": 2
                        , "taxPercent": "7.0%"
                        , "taxTotal": "$3.00"
                    }
                ]
            }
        };

        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                            $14.00\nTax 5.0%                             $2.00\nTax 7.0%                             $3.00\n------------------------------------------\nTotal                               $19.00\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse payment by gift card", function() {
        var input = {
            "data": {
                "customers": [
                    {
                          "custId"     : 1000001
                        , "crdId"      : 1001
                        , "isGiftCard" : true
                        , "fullName"   : "Gift Card #1001"
                    }
                ],
                "check": {
                    "checkId" : 1
                },
                "payments": [
                    {
                          "checkId"           : 1
                        , "custId"            : 1000001
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 4
                    }
                ],
                "giftCards": [
                    {
                          "crdId"         : 1001
                        , "custId"        : 1000001
                        , "moneyCurrency" : "$50.00"
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nGift Card Payment(4/20/2015)        $10.00\n  Gift Card #1001\n  $50.00 Balance remaining.\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should use gift card details when customer is not available", function() {
        var input = {
            "data": {
                "customers": [],
                "check": {
                    "checkId" : 1
                },
                "payments": [
                    {
                          "checkId"           : 1
                        , "custId"            : 1000001
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 4
                    }
                ],
                "giftCards": [
                    {
                          "crdId"         : 1005
                        , "custId"        : 1000001
                        , "moneyCurrency" : "$80.00"
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nGift Card Payment(4/20/2015)        $10.00\n  Gift Card #1005\n  $80.00 Balance remaining.\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should include customer full name line even when crdId is -1", function() {
        var input = {
            "data": {
                "customers": [
                    {
                          "custId"     : 1000001
                        , "crdId"      : -1
                        , "isGiftCard" : true
                        , "fullName"   : "Jim Bob"
                    }
                ],
                "check": {
                    "checkId" : 1
                },
                "payments": [
                    {
                          "checkId"           : 1
                        , "custId"            : 1000001
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 4
                    }
                ],
                "giftCards": [
                    {
                          "crdId"         : -1
                        , "custId"        : 1000001
                        , "moneyCurrency" : "$50.00"
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nGift Card Payment(4/20/2015)        $10.00\n  Jim Bob\n  $50.00 Balance remaining.\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse payment by account card", function() {
        var input = {
            "data": {
                "customers": [
                    {
                          "custId"     : 1000001
                        , "crdId"      : 1001
                        , "isGiftCard" : false
                        , "fullName"   : "Jim Bob"
                    }
                ],
                "check": {
                    "checkId" : 1
                },
                "payments": [
                    {
                          "checkId"           : 1
                        , "custId"            : 1000001
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 4
                    }
                ],
                "giftCards": [
                    {
                          "crdId"         : 1001
                        , "custId"        : 1000001
                        , "moneyCurrency" : "$50.00"
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nCustomer Payment(4/20/2015)         $10.00\n  Jim Bob\n  $50.00 Account Balance\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse payment by credit card", function() {
        var input = {
            "data": {
                "check": {
                    "checkId" : 1
                },
                "payments": [
                    {
                          "checkId"           : 1
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 2
                        , "cardType"          : "VISA"
                        , "lastFour"          : "1234"
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nVISA1234 Payment(4/20/2015)         $10.00\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse payment by check", function() {
        var input = {
            "data": {
                "check": {
                    "checkId" : 1
                },
                "payments": [
                    {
                          "checkId"           : 1
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 7
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nCheck Payment(4/20/2015)            $10.00\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse payment by cash", function() {
        var input = {
            "data": {
                "check": {
                    "checkId" : 1
                },
                "payments": [
                    {
                          "checkId"           : 1
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 1
                        , "tender"            : 15.0
                        , "tenderCurrency"    : "$15.00"
                        , "changeCurrency"    : "$5.00"
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nCash Payment(4/20/2015)             $10.00\nTendered                            $15.00\nChange                               $5.00\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse payment by external", function() {
        var input = {
            "data": {
                "check": {
                    "checkId" : 1
                },
                "payments": [
                    {
                          "checkId"           : 1
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 3
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nExternal Payment(4/20/2015)         $10.00\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse payment by voucher", function() {
        var input = {
            "data": {
                "check": {
                    "checkId" : 1
                },
                "payments": [
                    {
                          "checkId"           : 1
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 5
                        , "voucherNotes"      : "Some voucher"
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nSome voucher Voucher(4/20/2015)     $10.00\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse payment as complimentary", function() {
        var input = {
            "data": {
                "check": {
                    "checkId" : 1
                },
                "payments": [
                    {
                          "checkId"           : 1
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 6
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nComplimentary(4/20/2015)            $10.00\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse payment as debit", function() {
        var input = {
            "data": {
                "check": {
                    "checkId" : 1
                },
                "payments": [
                    {
                          "checkId"           : 1
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 9
                    }
                ]
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nDebit Payment(4/20/2015             $10.00\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse voided payment", function() {
        var input = {
            "data": {
                "check": {
                    "checkId" : 1
                },
                "payments": [
                    {
                          "checkId"           : 1
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 2
                        , "payType"           : 6
                    }
                ]
            },
            "options": {
                "printVoidedPayments": true
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\n*Voided Payments*                         \n*Complimentary(4/20/2015)*        ($10.00)\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse multiple payments", function() {
        var input = {
            "data": {
                "check": {
                    "checkId" : 1
                },
                "payments": [
                    {
                          "checkId"           : 1
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 2
                        , "payType"           : 6
                    },
                    {
                          "checkId"           : 1
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 9
                    },
                    {
                          "checkId"           : 1
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 5
                        , "voucherNotes"      : "Some voucher"
                    },
                    {
                          "checkId"           : 1
                        , "payAmount"         : 10.0
                        , "payAmountCurrency" : "$10.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 2
                        , "payType"           : 3
                    }
                ]
            },
            "options": {
                "printVoidedPayments": true
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nDebit Payment(4/20/2015             $10.00\nSome voucher Voucher(4/20/2015)     $10.00\n------------------------------------------\n*Voided Payments*                         \n*Complimentary(4/20/2015)*        ($10.00)\n*External Payment(4/20/2015)*     ($10.00)\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse check level discounts", function() {
        var input = {
            "data": {
                "check": {
                    "checkId"          : 1,
                    "discount"         : 5,
                    "discountCurrency" : "$5.00"
                },
                "discountType": {
                    "description": "Discount Desc."
                }
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nDiscount Desc.                       $5.00\n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse check balance", function() {
        var input = {
            "data": {
                "check": {
                    "checkId"                     : 1,
                    "checkRemainingTotalCurrency" : "$0.00"
                }
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                              $0.00\n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should ignore gratuity by default", function() {
        var input = {
            "data": {
                "check": {
                    "checkId" : 1,
                    "gratuity": 0
                }
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should print gratuity", function() {
        var input = {
            "data": {
                "check": {
                    "checkId" : 1,
                    "gratuity": 0
                }
            },
            "options": {
                "printGratuityLine": "all"
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n\nGratuity:      ___________________________\n\nTotal:         ___________________________\n\n\n\nSignature:     ___________________________\n\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should ignore gratuity by options", function() {
        var input = {
            "data": {
                "check": {
                    "checkId" : 1,
                    "gratuity": 0
                }
            },
            "options": {
                "printGratuityLine": "none"
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should ignore gratuity by check type", function() {
        var input = {
            "data": {
                "check": {
                    "checkId"   : 1,
                    "checkType" : 1,
                    "gratuity"  : 0
                }
            },
            "options": {
                "printGratuityLine": "eventonly"
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should ignore gratuity if already existing", function() {
        var input = {
            "data": {
                "check": {
                    "checkId"   : 1,
                    "checkType" : 1,
                    "gratuity"  : 5
                }
            },
            "options": {
                "printGratuityLine": "all"
            }
        };
        var expected = "\n\n\nReceipt Number 1                          \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse user and terminal", function() {
        var input = {
            "data": {
                "user": {
                    "userName" : "Jim Bob Sr."
                }
            },
            "terminalName": "POS1"
        };
        var expected = "\n\n\nReceipt Number N/A                        \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\nUser:                          Jim Bob Sr.\nTerminal:                             POS1\n------------------------------------------\n       Powered By www.ClubSpeed.com\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse receipt footers", function() {
        var input = {
            "resources": {
                  "receiptFooterText1" : "Some text hoorah really really long oh boy oh boy"
                , "receiptFooterText2" : "Looooooooooooooooooonger than 42 characters should be truuuuuncated"
                , "receiptFooterText3" : "Normal size text"
                , "receiptFooterText4" : "should be centered!"
            }
        };
        var expected = "\n\n\nReceipt Number N/A                        \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\nSome text hoorah really really long oh boy\nLooooooooooooooooooonger than 42 character\n             Normal size text\n           should be centered!\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should print clubspeed logo placeholder", function() {
        var input = {
            "options": {
                "clubSpeedLogoPath": "c:\\something\\greater\\than\\0\\length.bmp"
            }
        };
        var expected = "\n\n\nReceipt Number N/A                        \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{ClubSpeedLogo}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse survey information", function() {
        var input = {
            "resources": {
                  "receiptFooterSurveyText1" : "Go here for survey stuff!"
                , "receiptFooterSurveyText2" : "##SURVEYURL##"
            },
            "options": {
                  "printSurveyUrlOnReceipt" : true
                , "urlSurvey"               : "http://surveys.com/123"
                , "accessCode"              : "456"
            }
        };
        var expected = "\n\n\nReceipt Number N/A                        \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n\n\nGo here for survey stuff!                 \nhttp://surveys.com/123                    \nYour Access code is: 456                  \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse fiscal information", function() {
        var input = {
            "fiscalResponse": {
                "success"     : true,
                "serial"      : "QWERTY",
                "receiptCode" : "12345"
            }
        };
        var expected = "\n\n\nReceipt Number N/A                        \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n\nKontrollkod: QWERTY\nKontrollenhet: 12345\n\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should print emv footers", function() {
        var input = {
            "data": {
                "listCreditCardPaymentResponse": [{
                    "response": {
                        "result": {
                            "emvReceiptRequirement": "APP:AMERICAN EXPRESS\tAID:A000000025010801\tTVR:0000008000\tIAD:06720103603402\tTSI:E800\tARC:00\tMODE:ISSUER"
                        }
                    }
                }]
            }
        };
        var expected = "\n\n\nReceipt Number N/A                        \n------------------------------------------\n------------------------------------------\nSubtotal                                  \nTax                                       \n------------------------------------------\nTotal                                     \n------------------------------------------\nBalance                                   \n------------------------------------------\n------------------------------------------\n       Powered By www.ClubSpeed.com\n\n APP: AMERICAN EXPRESS\n AID: A000000025010801\n TVR: 0000008000\n IAD: 06720103603402\n TSI: E800\n ARC: 00\nMODE: ISSUER\n\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should handle standard input", function() {
        var input = {
            "data": {
                "check": {
                      "checkId"                     : 1
                    , "custId"                      : 1000001
                    , "gratuity"                    : 0
                    , "fee"                         : 1.0
                    , "feeCurrency"                 : "$1.00"
                    , "discount"                    : 5
                    , "discountCurrency"            : "$5.00"
                    , "checkSubtotalCurrency"       : "$14.00"
                    , "checkTaxCurrency"            : "$1.00"
                    , "checkTotalCurrency"          : "$16.00"
                    , "checkRemainingTotalCurrency" : "$11.00"
                    , "openedDateTime"              : "4/19/2015 7:00PM"
                    , "openedDateShort"             : "4/19/2015"
                    , "closedDateTime"              : "4/20/2015 10:00AM"
                    , "closedDateShort"             : "4/20/2015"
                },
                "discountType": {
                    "description": "Discount Desc."
                },
                "customers": [
                    {
                        "custId"   : 1000001,
                        "fullName" : "Jim Bob"
                    },
                    {
                        "custId"   : 1000006,
                        "fullName" : "Membership Customer"
                    },
                    {
                        "custId": 1000002,
                        "fullName": "Points Customer"
                    }
                ],
                "checkDetails": [
                    {
                          "checkDetailId"               : 1
                        , "checkDetailSubtotalCurrency" : "$16.25"
                        , "checkId"                     : 1
                        , "productId"                   : 6
                        , "productName"                 : "Membership Product"
                        , "qty"                         : 1
                        , "m_custId"                    : 1000006
                        , "discountApplied"             : 6.0
                        , "discountAppliedCurrency"     : "$6.00"
                        , "discountDesc"                : "$6 coupon"
                    },
                    {
                          "checkDetailId"               : 2
                        , "checkDetailSubtotalCurrency" : "$24.50"
                        , "checkId"                     : 1
                        , "productId"                   : 2
                        , "productName"                 : "Points Product"
                        , "qty"                         : 2
                        , "p_custId"                    : 1000002
                        , "discountApplied"             : 2.0
                        , "discountAppliedCurrency"     : "$2.00"
                        , "discountDesc"                : "$2 coupon"
                    },
                    {
                          "checkDetailId"               : 3
                        , "checkDetailSubtotalCurrency" : "$13.25"
                        , "checkId"                     : 1
                        , "productId"                   : 3
                        , "productName"                 : "Food Product"
                        , "qty"                         : 1
                    }
                ],
                "products": [
                    {
                        "productId"   : 6,
                        "productType" : 6
                    },
                    {
                        "productId"   : 2,
                        "productType" : 2
                    },
                    {
                        "productId"   : 3,
                        "productType" : 3
                    }
                ],
                "foodSubitems": [
                    {
                        "checkDetailId": 3,
                        "description": "Subitem1"
                    },
                    {
                        "checkDetailId": 3,
                        "description": "Subitem2"
                    }
                ],
                "payments": [
                    {
                          "checkId"           : 1
                        , "payAmount"         : 1.0
                        , "payAmountCurrency" : "$1.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 2
                        , "payType"           : 6
                    },
                    {
                          "checkId"           : 1
                        , "payAmount"         : 1.0
                        , "payAmountCurrency" : "$1.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 9
                    },
                    {
                          "checkId"           : 1
                        , "payAmount"         : 1.0
                        , "payAmountCurrency" : "$1.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 1
                        , "payType"           : 5
                        , "voucherNotes"      : "Some voucher"
                    },
                    {
                          "checkId"           : 1
                        , "payAmount"         : 1.0
                        , "payAmountCurrency" : "$1.00"
                        , "payDateShort"      : "4/20/2015"
                        , "payId"             : 1
                        , "payStatus"         : 2
                        , "payType"           : 3
                    }
                ],
                "user": {
                    "userName" : "Jim Bob Sr."
                }
            },
            "fiscalResponse": {
                  "success"     : true
                , "serial"      : "QWERTY"
                , "receiptCode" : "12345"
            },
            "options": {
                  "printSurveyUrlOnReceipt" : true
                , "urlSurvey"               : "http://surveys.com/123"
                , "accessCode"              : "456"
                , "printVoidedPayments"     : true
                , "clubSpeedLogoPath"       : "c:\\something\\greater\\than\\0\\length\\clubspeed_logo.bmp"
                , "companyLogoPath"         : "c:\\something\\greater\\than\\0\\length\\company_logo.bmp"
            },
            "resources": {
                  "receiptFooterSurveyText1"      : "Go here for survey stuff!"
                , "receiptFooterSurveyText2"      : "##SURVEYURL##"
            },
            "terminalName": "POS1"
        };
        var expected = "\n\n{{CompanyLogo}}\n\nCustomer: Jim Bob\nReceipt Number 1          4/19/2015 7:00PM\n                         4/20/2015 10:00AM\n------------------------------------------\nMembership Product                  $16.25\n  Membership Customer\n  $6 coupon($6.00)\n2)Points Product                    $24.50\n  Points Customer\n  $2 coupon($2.00)\nFood Product                        $13.25\n Subitem1\n Subitem2\n------------------------------------------\nSubtotal                            $14.00\nTax                                  $1.00\nFee                                  $1.00\n------------------------------------------\nTotal                               $16.00\n------------------------------------------\nDebit Payment(4/20/2015              $1.00\nSome voucher Voucher(4/20/2015)      $1.00\n------------------------------------------\n*Voided Payments*                         \n*Complimentary(4/20/2015)*         ($1.00)\n*External Payment(4/20/2015)*      ($1.00)\n------------------------------------------\nDiscount Desc.                       $5.00\n------------------------------------------\nBalance                             $11.00\n------------------------------------------\nUser:                          Jim Bob Sr.\nTerminal:                             POS1\n------------------------------------------\n       Powered By www.ClubSpeed.com\n{{ClubSpeedLogo}}\n\n\nGo here for survey stuff!                 \nhttp://surveys.com/123                    \nYour Access code is: 456                  \n\nKontrollkod: QWERTY\nKontrollenhet: 12345\n\n{{Barcode=1}}\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

});
