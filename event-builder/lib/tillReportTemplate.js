"use strict";

var z             = require('./zana');
var config        = require('./config-provider.js');
var utils         = require('./utils.js');
var rpad          = utils.receipts.rpad;
var lpad          = utils.receipts.lpad;
var buildFullLine = utils.receipts.buildFullLine;
var log           = utils.logging.log;
log.debug.on      = config.receipts.useDebugLogging;
var CONSTANTS     = require('./constants.js');
var PLACEHOLDERS  = CONSTANTS.PLACEHOLDERS;

var defaults = {
    "data": {
        "payments": []
    }
    , "resources": {
          "strAmount"       : "Amount"
        , "strCashInDrawer" : "Cash in Drawer"
        , "strShiftPayment" : "Shift  Payment Type"
        , "strTerminal"     : "Terminal"
        , "strTillReport"   : "TILL REPORT"
    }
    , "totalCash"           : null
    , "totalCashCurrency"   : ""
    , "shift"               : 1
    , "terminalName"        : ""
    , "timestamp"           : ""
    , "nowDateLong"         : ""
    , "nowTimeShort"        : ""
};

function TillReportTemplater() {}
TillReportTemplater.prototype.create = function(body) {
  log.debug('----- building till report receipt -----');
  log.debug('input:\n', body);
  if (!body)
    body = {};
  var receipt   = z.extend(body, defaults);
  var data      = receipt.data;
  var payments  = data.payments;
  var resources = receipt.resources;

  // Begin the receipt
  var output = '\n\n';

  output += buildFullLine('           ### ' + resources.strTillReport + ' ###           ');
  output += buildFullLine(resources.strTerminal + ': ' + (receipt.terminalName || ''));
  output += buildFullLine(((receipt.nowDateLong || '') + ' ' + (receipt.nowTimeShort || '').trim()));
  output += '\n';
  log.debug('till report header');

  // Header and Line Items for Payment Types
  output += buildFullLine(rpad(resources.strShiftPayment, 31) + lpad(resources.strAmount, 10));
  if(payments !== null && Array.isArray(payments)) {
    payments.forEach(function(payment) {
        output += buildFullLine(rpad(receipt.shift, 7) + rpad(payment.paymentType, 22) + lpad(payment.paymentAmountCurrency, 12));
    });
  }
  log.debug('payments');

  // Cash in Drawer Line
  output += buildFullLine(rpad(resources.strCashInDrawer, 31) + lpad(receipt.totalCashCurrency, 10));
  log.debug('feed & cut');

  // Feed and Cut Paper
  output += '\n\n\n\n\n\n';
  output += PLACEHOLDERS.CUTPAPER;

  log.debug('output:\n', output);
  return output;
};

module.exports = new TillReportTemplater();
