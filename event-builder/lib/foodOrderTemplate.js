"use strict";

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
var PRODUCT_TYPE        = CONSTANTS.PRODUCT_TYPE;
var PLACEHOLDERS        = CONSTANTS.PLACEHOLDERS;

var defaults = {
    "data": {
          "check"        : {}
        , "checkDetails" : []
        , "customer"     : {}
        , "foodSubitems" : []
        , "products"     : []
        , "user"         : {}
    }
    , "resources": {
          "strCheckNo"  : "Check #"
        , "strCustomer" : "Customer"
        , "strNA"       : "N/A"
        , "strTerminal" : "Terminal"
        , "strUser"     : "User"
    }
    , "nowTimeShort": ""
};

exports.create = function(body) {
    log.debug('----- building food order receipt -----');
    log.debug('input:\n', body);
    if (!body)
        body = {};
    var receipt      = z.extend(body, defaults);
    var data         = receipt.data;
    var check        = data.check;
    var checkDetails = data.checkDetails;
    var customer     = data.customer;
    var foodSubitems = data.foodSubitems;
    var products     = data.products;
    var user         = data.user;

    var resources = receipt.resources;

    var line = '---------------------------------';

    // Begin the receipt
    var output = '\n\n';

    if (check && check.custId > 0 && customer && customer.custId === check.custId && customer.fullName)
        output += rpad(resources.strCustomer, 9) + lpad(customer.fullName, 24) + '\n';
    output += rpad(resources.strCheckNo + ' ' + (check.checkId || resources.strNA), 25) + lpad(receipt.nowTimeShort, 8) + '\n';
    output += line + '\n';

    checkDetails.forEach(function(detail) {
        if (detail.checkDetailStatus !== CHECK_DETAIL_STATUS.HAS_VOIDED) {
            var product = _.find(products, function(x) { return x.productId === detail.productId; });
            if (product && product.productType === PRODUCT_TYPE.FOOD) {
                var strProductName = detail.productName;
                if (strProductName.length >= 23) // switched to 23, not 20
                    strProductName = strProductName.substring(0, 20) + '...';
                if (detail.qty > 1)
                    strProductName = detail.qty + ')' + strProductName;
                var strTemp = rpad(strProductName, 25) + lpad(detail.checkDetailSubtotalCurrency, 8) + '\n';
                var subitems = _.filter(foodSubitems, function(x) {
                    return x.checkDetailId === detail.checkDetailId;
                });
                subitems.forEach(function(subitem) {
                    strTemp += '  ' + subitem.description + '\n';
                });
                output += strTemp;
            }
        }
    });

    output += line + '\n';
    output += rpad(resources.strUser, 5) + lpad(user.userName, 28) + '\n';
    output += rpad(resources.strTerminal, 9) + lpad(receipt.terminalName, 24) + '\n';

    // Feed and Cut Paper
    output += '\n\n\n\n\n\n';
    output += PLACEHOLDERS.CUTPAPER;
    log.debug('feed & cut');

    log.debug('output:\n', output);
    return output;
};
