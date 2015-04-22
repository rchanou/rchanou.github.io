var _         = require('./underscore');
var z         = require('./zana');
var config    = require('./config-provider.js');
var CONSTANTS = require('./constants.js');
var utils     = require('./utils.js');
var rpad      = utils.receipts.rpad;
var lpad      = utils.receipts.lpad;
var cpad      = utils.receipts.cpad;
var log       = utils.logging.log;
log.debug.on  = config.receipts.useDebugLogging;

var defaults = {
    "data": {
        "description"    : "",
        "amount"         : 0.0,
        "amountCurrency" : "",
        "shift"          : 0
    },
    "resources": {
        "strExpense2" : "EXPENSE",
        "strShift"    : "Shift:",
        "strTerminal" : "Terminal:"
    },
    "terminalName" : "",
    "now"          : "",
    "nowDateShort" : "",
    "nowTimeShort" : ""
};

function ExpenseTemplater() {}
ExpenseTemplater.prototype.create = function(body) {
    log.debug('----- building expense receipt -----');
    log.debug('input:\n', body);
    if (!body)
        body = {};
    var receipt   = z.extend(body, defaults);
    var resources = receipt.resources;
    var data      = receipt.data;

    // Begin the Receipt
    var output = '\n\n';

    // Print expense information
    output += cpad('###' + resources.strExpense2 + '###') + '\n';
    output += resources.strTerminal + ' ' + receipt.terminalName + '\n';
    output += receipt.nowDateShort + ' ' + receipt.nowTimeShort + '\n';
    output += resources.strShift + ' ' + data.shift + '\n';
    output += '\n';
    output += rpad(data.description, 32) + '\n' + lpad(data.amountCurrency, 10) + '\n';
    output += '\n';
    output += '\n';

    // Feed and Cut Paper
    output += '\n\n\n\n\n\n';
    output += ('\x1d\x56\x01');
  
    log.debug('output:\n', output);
    return output;
};

module.exports = new ExpenseTemplater();