var _                   = require('./underscore');
var z                   = require('./zana');
var config              = require('./config-provider.js');
var CONSTANTS           = require('./constants.js');
var utils               = require('./utils.js');
var rpad                = utils.receipts.rpad;
var lpad                = utils.receipts.lpad;
var log                 = utils.logging.log;
log.debug.on            = config.receipts.useDebugLogging;
var CHECK_DETAIL_STATUS = CONSTANTS.CHECK_DETAIL_STATUS;
var SALE_BY             = CONSTANTS.SALE_BY;

var defaults = {
    "data": {
        "checkDetails" : [],
        "customer"     : {}
    },
    "resources": {
          "strCustomer2" : "Customer:"
        , "strId"        : "ID:"
        , "strLaps"      : "Laps:"
        , "strMinutes2"  : "Minutes:"
        , "strTime2"     : "Time:"
        , "strType"      : "Type:"
    },
    "terminalName" : "",
    "now"          : "",
    "nowDateShort" : "",
    "nowTimeShort" : ""
};

function EntitleItemTemplater() {}
EntitleItemTemplater.prototype.create = function(body) {
    log.debug('----- building entitle receipt -----');
    log.debug('input:\n', body);
    if (!body)
        body = {};
    var receipt      = z.extend(body, defaults);
    var resources    = receipt.resources;
    var data         = receipt.data;
    var checkDetails = data.checkDetails;
    var customer     = data.customer;

    // Begin the Receipt
    var output = '\n\n';

    if (checkDetails) {
        checkDetails.forEach(function(detail) {
            if (detail.checkDetailStatus !== CHECK_DETAIL_STATUS.HAS_VOIDED && detail.qty > 0) {
                // assume we only have CheckDetailSalesByLapOrTime? isn't really a way to determine this from just the data, i think.
                for (var i = 0; i < detail.qty; i++) {
                    for (var k = 0; k < detail.s_vol; k++) { // type safety?
                        output += lpad(resources.strId, 10) + ' ' + detail.checkDetailId + '\n';
                        output += lpad(resources.strType, 10) + ' ' + detail.productName + (detail.s_vol > 1 ? '(' + (k+1) + ')' : '') + '\n';
                        if (detail.s_saleBy === SALE_BY.LAPS)
                            output += lpad(resources.strLaps, 10) + ' ' + detail.s_laps + '\n';
                        else
                            output += lpad(resources.strMinutes2, 10) + ' ' + detail.s_minutesFixed + '\n';
                        if (customer && customer.fullName)
                            output += lpad(resources.strCustomer2, 10) + ' ' + customer.fullName + '\n';
                        output += lpad(resources.strTime2, 10) + ' ' +  (receipt.nowDateShort || '') + ' ' + (receipt.nowTimeShort || '') + '\n';
                        output += '\n';
                        if (resources.raceTicketLine1)
                            output += resources.raceTicketLine1 + '\n';
                        if (resources.raceTicketLine2)
                            output += resources.raceTicketLine2 + '\n';
                        if (resources.raceTicketLine3)
                            output += resources.raceTicketLine3 + '\n';
                        if (resources.raceTicketLine4)
                            output += resources.raceTicketLine4 + '\n';
                        output += '\n';
                        output += "{{BARCODE=" + detail.checkDetailId + "}}"; // newline?
                    }
                }
            }
        });
    }
  
    log.debug('output:\n', output);
    return output;
};

module.exports = new EntitleItemTemplater();