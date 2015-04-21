var fs = require('fs');
var constants = require('./constants.js');

// note, we could just slap the word "Template" on to each receiptType, if we really wanted
var mapper = {
      'raceTicket'          : 'raceTicketTemplate'
    , 'tillReport'          : 'tillReportTemplate'
    , 'transactionReceipt'  : 'transactionReceiptTemplate'
    , 'expense'             : 'expenseTemplate'
    , 'creditCard'          : 'creditCardTemplate'
    , 'foodOrder'           : 'foodOrderTemplate'
    , 'entitleItem'         : 'entitleItemTemplate'
};
var templatePath = './';
var customTemplatePath = 'c:/clubspeedapps/assets/receipts/';

exports.create = function(receiptType, body) {
    if (!mapper[receiptType])
        throw new Error('Unsupported receiptType! Received: ' + receiptType);
    var templateFileName = mapper[receiptType] + '.js';
    var defaultFile = templatePath + templateFileName;
    var customFile = customTemplatePath + templateFileName;
    var templater = loadCustomTemplateOrDefault(customFile, defaultFile);
    var output = templater.create(body);
    return output;
};

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