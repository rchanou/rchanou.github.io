"use strict";

var fs = require('fs');

// note, we could just slap the word "Template" on to each receiptType, if we really wanted
var mapper = {
      'raceTicket'          : 'raceTicketTemplate'
    , 'tillReport'          : 'tillReportTemplate'
    , 'transactionReceipt'  : 'transactionReceiptTemplate'
    , 'expense'             : 'expenseTemplate'
    , 'creditCard'          : 'creditCardTemplate'
    , 'foodOrder'           : 'foodOrderTemplate'
    , 'entitleItem'         : 'entitleItemTemplate'
	, 'creditCardReprint'   : 'creditCardReprintTemplate'
};
var templatePath = './';
var customTemplateFolder = 'c:/clubspeedapps/assets/receipts/';

/**
 * Look to see if a custom template exists, if not, use default
 */
function loadCustomTemplateOrDefault(customTemplatePath, defaultTemplatePath) {
    var obj;
    if(fs.existsSync(customTemplatePath))
        obj = require(customTemplatePath);
    else
        obj = require(defaultTemplatePath);
    if (!obj)
        throw new Error('Unable to find either custom or default template! Received:', defaultTemplatePath);
    return obj;
}

exports.create = function(receiptType, body) {
    if (!mapper[receiptType])
        throw new Error('Unsupported receiptType! Received: ' + receiptType);
    var templateFileName = mapper[receiptType] + '.js';
    var defaultFile = templatePath + templateFileName;
    var customFile = customTemplateFolder + templateFileName;
    var templater = loadCustomTemplateOrDefault(customFile, defaultFile);
    var output = templater.create(body);
    return output;
};
