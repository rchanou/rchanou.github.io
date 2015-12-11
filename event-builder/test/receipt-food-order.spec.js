/* eslint no-unused-expressions: 0 */ // for chai

"use strict";

var expect = require("chai").expect;
var builder = require("../lib/foodOrderTemplate.js");
var utils = require('../lib/utils.js');
utils.logging.log.debug.on = false;

function compare(input, expected) {
    var output = builder.create(input);
    expect(output).to.exist;
    expect(output).to.be.a('string');
    expect(output).to.not.be.empty;
    expect(output).to.equal(expected);
}

describe("Food Order Template", function() {

    it("should gracefully handle empty input", function() {
        var input = null;
        var expected = "\n\nCheck # N/A                      \n---------------------------------\n---------------------------------\nUser                             \nTerminal                         \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse check information", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1
                }
            },
            "nowTimeShort": "10:00AM"
        };
        var expected = "\n\nCheck # 1                 10:00AM\n---------------------------------\n---------------------------------\nUser                             \nTerminal                         \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse customer information", function() {
        var input = {
            "data": {
                "check": {
                    "checkId": 1,
                    "custId": 1000001
                },
                "customer": {
                    "custId": 1000001,
                    "fullName": "Jim Bob"
                }
            }
        };
        var expected = "\n\nCustomer                  Jim Bob\nCheck # 1                        \n---------------------------------\n---------------------------------\nUser                             \nTerminal                         \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse single products", function() {
        var input = {
            "data": {
                "checkDetails": [
                    {
                        "checkId": 1,
                        "productId": 1,
                        "productName": "Food Product 1",
                        "checkDetailSubtotalCurrency": "$10.00"
                    }
                ],
                "products": [
                    {
                        "productId": 1,
                        "productType": 3
                    }
                ]
            }
        };
        var expected = "\n\nCheck # N/A                      \n---------------------------------\nFood Product 1             $10.00\n---------------------------------\nUser                             \nTerminal                         \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse multiple products", function() {
        var input = {
            "data": {
                "checkDetails": [
                    {
                        "checkId": 1,
                        "checkDetailId": 1,
                        "productId": 1,
                        "productName": "Food Product 1",
                        "checkDetailSubtotalCurrency": "$10.00"
                    },
                    {
                        "checkId": 1,
                        "checkDetailId": 2,
                        "productId": 1,
                        "productName": "Food Product 1",
                        "checkDetailSubtotalCurrency": "$10.00"
                    },
                    {
                        "checkId": 1,
                        "checkDetailId": 3,
                        "productId": 2,
                        "productName": "Food Product 2",
                        "checkDetailSubtotalCurrency": "$12.00"
                    }
                ],
                "products": [
                    {
                        "productId": 1,
                        "productType": 3
                    },
                    {
                        "productId": 2,
                        "productType": 3
                    }
                ]
            }
        };
        var expected = "\n\nCheck # N/A                      \n---------------------------------\nFood Product 1             $10.00\nFood Product 1             $10.00\nFood Product 2             $12.00\n---------------------------------\nUser                             \nTerminal                         \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse qty > 1", function() {
        var input = {
            "data": {
                "checkDetails": [
                    {
                        "checkId": 1,
                        "productId": 1,
                        "productName": "Food Product 1",
                        "checkDetailSubtotalCurrency": "$10.00",
                        "qty": 2
                    }
                ],
                "products": [
                    {
                        "productId": 1,
                        "productType": 3
                    }
                ]
            }
        };
        var expected = "\n\nCheck # N/A                      \n---------------------------------\n2)Food Product 1           $10.00\n---------------------------------\nUser                             \nTerminal                         \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse food subitems", function() {
        var input = {
            "data": {
                "checkDetails": [
                    {
                        "checkId": 1,
                        "checkDetailId": 1,
                        "productId": 1,
                        "productName": "Food Product 1",
                        "checkDetailSubtotalCurrency": "$10.00",
                        "qty": 2
                    }
                ],
                "products": [
                    {
                        "productId": 1,
                        "productType": 3
                    }
                ],
                "foodSubitems": [
                    {
                        "checkDetailId": 1,
                        "description": "Subitem 1"
                    },
                    {
                        "checkDetailId": 1,
                        "description": "Subitem 2"
                    }
                ]
            }
        };
        var expected = "\n\nCheck # N/A                      \n---------------------------------\n2)Food Product 1           $10.00\n  Subitem 1\n  Subitem 2\n---------------------------------\nUser                             \nTerminal                         \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should parse user and terminal", function() {
        var input = {
            "data": {
                "user": {
                    "userName": "support"
                }
            },
            "terminalName": "POS1"
        };
        var expected = "\n\nCheck # N/A                      \n---------------------------------\n---------------------------------\nUser                      support\nTerminal                     POS1\n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

    it("should not parse non-food products", function() {
        var input = {
            "data": {
                "checkDetails": [
                    {
                        "checkId": 1,
                        "checkDetailId": 1,
                        "productId": 1,
                        "productName": "Food Product 1",
                        "checkDetailSubtotalCurrency": "$10.00"
                    },
                    {
                        "checkId": 1,
                        "checkDetailId": 2,
                        "productId": 1,
                        "productName": "Food Product 1",
                        "checkDetailSubtotalCurrency": "$10.00"
                    },
                    {
                        "checkId": 1,
                        "checkDetailId": 3,
                        "productId": 2,
                        "productName": "Food Product 2",
                        "checkDetailSubtotalCurrency": "$12.00"
                    }
                ],
                "products": [
                    {
                        "productId": 1,
                        "productType": 1
                    },
                    {
                        "productId": 2,
                        "productType": 3
                    }
                ]
            }
        };
        var expected = "\n\nCheck # N/A                      \n---------------------------------\nFood Product 2             $12.00\n---------------------------------\nUser                             \nTerminal                         \n\n\n\n\n\n\n{{CutPaper}}";
        compare(input, expected);
    });

});
