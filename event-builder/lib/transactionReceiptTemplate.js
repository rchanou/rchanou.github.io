/*
    Missing resources (not yet created in ClubSpeed, hard coded as English):

    strRacer
    strOrganizationNumber
    strTo (may not be necessary?)
*/

"use strict";

var _                   = require('./underscore');
var z                   = require('./zana');
var config              = require('./config-provider.js');
var CONSTANTS           = require('./constants.js');
var utils               = require('./utils.js');
var rpad                = utils.receipts.rpad;
var lpad                = utils.receipts.lpad;
var cpad                = utils.receipts.cpad;
var log                 = utils.logging.log;
log.debug.on            = config.receipts.useDebugLogging;
var CHECK_TYPE          = CONSTANTS.CHECK_TYPE;
var PRODUCT_TYPE        = CONSTANTS.PRODUCT_TYPE;
var CHECK_DETAIL_STATUS = CONSTANTS.CHECK_DETAIL_STATUS;
var PAY_STATUS          = CONSTANTS.PAY_STATUS;
var PAY_TYPE            = CONSTANTS.PAY_TYPE;
var PLACEHOLDERS        = CONSTANTS.PLACEHOLDERS;

var defaults = {
    "data":{
        "check"        : {},
        "checkDetails" : [],
        "customers"    : [],
        "payments"     : [],
        "event"        : null,
        "user"         : null,
        "foodSubitems" : [],
        "products"     : [],
        "giftCards"    : [],
        "discountType" : null
    },
    "resources":{
        "strAccBalance"            : "{0:C} Account Balance",
        "strAccessCode"            : "Your Access code is:",
        "strBalance"               : "Balance",
        "strBalanceRemaining"      : "{0:C} Balance remaining.",
        "strCash"                  : "Cash",
        "strChange"                : "Change",
        "strCheck"                 : "Check",
        "strComplimentary"         : "Complimentary",
        "strCreatedBy"             : "Created by {0}",
        "strCustomer"              : "Customer",
        "strDebit"                 : "Debit",
        "strDuration"              : "Duration",
        "strEventInfo"             : "Event Information",
        "strExempt"                : "(Exempt)",
        "strExternal"              : "External",
        "strFee"                   : "Fee",
        "strGC"                    : "Gift Card",
        "strGratuity"              : "Gratuity:",
        "strGratuity2"             : "Gratuity",
        "strGST"                   : "GST",
        "strNA"                    : "N/A",
        "strName"                  : "Name:",
        "strPayment"               : "Payment",
        "strPST"                   : "PST",
        "strReceiptNo"             : "Receipt Number",
        "strRefund"                : "Refund",
        "strSignature"             : "Signature:",
        "strSubtotal"              : "Subtotal",
        "strTender"                : "Tendered",
        "strTerminal"              : "Terminal:",
        "strTime"                  : "Time",
        "strTotal"                 : "Total:",
        "strTotal2"                : "Total",
        "strType"                  : "Type:",
        "strUser"                  : "User:",
        "strVoided"                : "VOIDED",
        "strVoidedPayment"         : "*Voided Payments*",
        "strVoucher"               : "Voucher",
        "receiptFooterText1"       : "",
        "receiptFooterText2"       : "",
        "receiptFooterText3"       : "",
        "receiptFooterText4"       : "Powered By www.ClubSpeed.com",
        "receiptFooterSurveyText1" : "",
        "receiptFooterSurveyText2" : "",
        "receiptHeaderText1"       : "",
        "receiptHeaderText2"       : "",
        "receiptHeaderText3"       : "",
        "receiptHeaderText4"       : "",
        "taxLabel"                 : "Tax"
    },
    "options":{
        "organizationNumber"           : "",
        "printSurveyUrlOnReceipt"      : "false",
        "printBarcodeOnReceipt"        : true,
        "printVoidedPayments"          : false,
        "urlSurvey"                    : "",
        "accessCode"                   : "",
        "printGratuityLine"            : "none",
        "has2Taxes"                    : false,
        "receiptHeaderAlign"           : "center",
        "companyLogoPath"              : "",
        "clubSpeedLogoPath"            : "",
        "printDetail"                  : true
    },
    "fiscalResponse" : null,
    "type"           : "transactionReceipt",
    "terminalName"   : ""
};

exports.create = function(body) {
  log.debug('----- building transaction receipt -----');
  log.debug('input:\n', body);

  if (!body)
    body = {};
  var receipt      = z.extend(body, defaults);

  var terminal     = receipt.terminalName;
  var fiscal       = receipt.fiscalResponse;
  var options      = receipt.options;
  var resources    = receipt.resources;

  var data         = receipt.data;

  var check        = data.check;
  var customers    = data.customers;
  var checkDetails = data.checkDetails;
  var discount     = data.discountType;
  var event        = data.event;
  var giftCards    = data.giftCards;
  var products     = data.products;
  var foodSubitems = data.foodSubitems;
  var user         = data.user;
  var taxes        = data.taxes;

  // Ensure no null/undefined customers are in the array
  // (potentially from poor referential integrity in CS)
  customers = _.filter(customers, function(x) { return x !== null && x !== undefined; });

  // Begin the receipt
  var output = '\n\n';
  var line = '------------------------------------------';

  // Insert company logo placeholder
  if (options.companyLogoPath && options.companyLogoPath.length > 0)
      output += '{{CompanyLogo}}\n';

  // Insert receipt header (with alignment), if exists
  var alignFunction;
  switch(options.receiptHeaderAlign) {
      case 'left':
          alignFunction = rpad;
          break;
       case 'right':
          alignFunction = lpad;
          break;
       // case 'center':
       default:
          alignFunction = cpad;
          break;
  }

  if (resources.receiptHeaderText1 && resources.receiptHeaderText1.trim().length > 0)
    output += alignFunction(resources.receiptHeaderText1, 42) + '\n';
  if (resources.receiptHeaderText2 && resources.receiptHeaderText2.trim().length > 0)
    output += alignFunction(resources.receiptHeaderText2, 42) + '\n';
  if (resources.receiptHeaderText3 && resources.receiptHeaderText3.trim().length > 0)
    output += alignFunction(resources.receiptHeaderText3, 42) + '\n';
  if (resources.receiptHeaderText4 && resources.receiptHeaderText4.trim().length > 0)
    output += alignFunction(resources.receiptHeaderText4, 42) + '\n';
  log.debug('end print receipt header');

  // Fiscal Printer Organization Number
  if(options.organizationNumber && options.organizationNumber.length > 0)
      output += 'Org. #: ' + options.organizationNumber + '\n'; // RESOURCE NEEDED

  if (check && check.checkType === CHECK_TYPE.EVENT && event != null) { // single equal (!=) on purpose
      /*
        Print Event Information
      */
      output += resources.strEventInfo + '\n';
      if (event.subject && event.subject.trim().length > 0)
        output += resources.strName + ' ' + event.subject + '\n';
      output += (event.numberOfRacers || resources.strNA) + ' ' + 'Racers' + '\n'; // RESOURCE NEEDED
      var eventDateTime = '';
      if (event.startDate) {
        eventDateTime += event.startDate;
        if (event.startTime)
          eventDateTime += ' ' + event.startTime + (event.endTime ? (' to ' + event.endTime) : '');
      }
      if (eventDateTime)
        output += eventDateTime + '\n';
      output += resources.strCreatedBy.replace('{0}', event.createdBy) + '\n'; // note the string replace -- Resources are actually being stored as String.Format with placeholders
  }
  output += '\n';
  log.debug('end print event');

  /*
    Print Customer Name
  */
  if (check && +check.custId > 0) {
    var customer = _.find(customers, function(x) { return x.custId === check.custId; });
    var fullName = (customer && customer.fullName && customer.fullName.trim().length > 0 ? customer.fullName : '');
    output += resources.strCustomer + ': ' + fullName + '\n';
  }
  log.debug('end print customer');

  /*
    Print Receipt #
  */
  output += rpad(resources.strReceiptNo + ' ' + (check.checkId || resources.strNA), 23) + lpad(check.openedDateTime, 19) + '\n';
  log.debug('end print receipt # and opened date');

  /*
    Print Closed Date
  */
  if (check.closedDateShort && check.closedDateShort !== check.openedDateShort) // different opened and closed days
    output += lpad(check.closedDateTime, 42) + '\n';
  output += line + '\n';
  log.debug('end print closed date');

  /*
    GetPrintItems
  */
  checkDetails = _.select(checkDetails, function(x) { return x.checkId === check.checkId; }); // shouldn't be necessary, but just to be safe.
  if (check && check.checkType === CHECK_TYPE.EVENT && options.printDetail === false) { // the printDetail === false matches vb logic.. seems odd.
    checkDetails.forEach(function(detail) {
      if (detail.checkDetailStatus !== CHECK_DETAIL_STATUS.HAS_VOIDED) { // new or 'cannot deleted' (permanent)
        /*
          GetPrintEventCheckDetail
        */
        // ditching the grouping logic of ProductIDs from Function GetPrintItems (intentionally)
        // in order to match the display of the POS with line items separated
        var product = _.find(products, function(x) { return x.productId === detail.productId; });
        if (product) {
          var description = product.description || '';
          if (description.length >= 25)
            description = (description.substr(0, 22) + '...');
          if (detail.qty > 1)
            description = detail.qty + ')' + description; // or actualQty? qty + cadetQty?
          output += rpad(description, 30) + lpad(detail.checkDetailSubtotalCurrency, 12) + '\n';
        }
        if (detail.discountApplied && +detail.discountApplied > 0)
          output += '  ' + detail.discountDesc + '(' + detail.discountAppliedCurrency + ')' + '\n';
        /*
          End GetPrintEventCheckDetail
        */
      }
    });
  }
  else {
    checkDetails.forEach(function(detail) {
      if (detail.checkDetailStatus !== CHECK_DETAIL_STATUS.HAS_VOIDED) { // new or 'cannot deleted' (permanent)
        /*
          GetPrintCheckDetail
        */

        /*
          Alright, product shenanigans.
          CheckDetail has the Product partially denormalized/stamped, such as in the case of product name.
          However, we do not have the product type available to use, so we need to use a product lookup in this case.
          When a product field is available on the check detail, USE THAT ONE FIRST (as it is meant to be a historical copy).
          Having to use the product records should be considered a last (but necessary) resort.
        */
        var productName = detail.productName;
        if (productName.length >= 25)
          productName = productName.substr(0, 24) + '...';
        if (detail.qty > 1)
          productName = detail.qty + ')' + productName;

        productName = rpad(productName, 31);
        output += productName + lpad(detail.checkDetailSubtotalCurrency, 11) + '\n';

        var product = _.find(products, function(x) { return x.productId === detail.productId; });
        if (product) {
          // ignore GameCard logic on purpose - no longer used.
          // note: we need to get product based on detail.productId and then use that to get product.productType
          switch (product.productType) {
            case PRODUCT_TYPE.POINT:
              if (detail.p_custId && +detail.p_custId > 0) {
                var pointsCustomer = _.find(customers, function(x) { return x.custId === detail.p_custId; });
                if (pointsCustomer != null)
                  output += '  ' + pointsCustomer.fullName + '\n';
              }
              break;
            case PRODUCT_TYPE.MEMBERSHIP:
              if (detail.m_custId && +detail.m_custId > 0) {
                var membershipCustomer = _.find(customers, function(x) { return x.custId === detail.m_custId; });
                if (membershipCustomer != null)
                  output += '  ' + membershipCustomer.fullName + '\n';
              }
              break;
            case PRODUCT_TYPE.FOOD:
              // if detail casted as CheckDetailFood, then loop through detail.SubItems (?)
              // foreach subitem: output += ' ' + subitem.Description + '\n';
              var subitems = _.select(foodSubitems, function(x) { return x.checkDetailId === detail.checkDetailId; });
              if (subitems && subitems.length > 0) {
                subitems.forEach(function(subitem) {
                  output += ' ' + subitem.description + '\n';
                });
              }
              break;
          }
        }
        else
          log('Error: Received a checkDetail with a productId that was not found on the product list! Received checkDetail.productId: ', detail.productId);
        if (detail.discountApplied && +detail.discountApplied > 0)
          output += '  ' + detail.discountDesc + '(' + detail.discountAppliedCurrency + ')' + '\n';
        /*
          End GetPrintCheckDetail
        */
      }
    });
  }
  output += line + '\n';
  log.debug('end print items');
  /*
    End GetPrintItems
  */

  /*
    Print Subtotal, Tax, Fee, Gratuity, & Total
  */
  if (check) {
    output += rpad(resources.strSubtotal, 30) + lpad(check.checkSubtotalCurrency, 12) + '\n';
    if (!options.has2Taxes) {
      if (taxes && taxes.length > 0) {
        taxes.forEach(function(tax) {
          output += rpad(resources.taxLabel + (check.isTaxExempt ? (resources.strExempt + ' ') : ' ') + tax.taxPercent, 30) + lpad(tax.taxTotal, 12) + '\n';
        });
      }
      else {
        // fall back to the old method for appending taxes
        output += rpad(resources.taxLabel + (check.isTaxExempt ? resources.strExempt : ''), 30) + lpad(check.checkTaxCurrency, 12) + '\n';
      }
    }
    else {
      if (check.checkGST && check.checkGST > 0) {
        output += rpad(resources.strGST + (check.isTaxExempt ? resources.strExempt : ''), 30) + lpad(check.checkGSTCurrency, 12) + '\n';
        output += rpad(resources.strPST + (check.isTaxExempt ? resources.strExempt : ''), 30) + lpad(check.checkPSTCurrency, 12) + '\n';
      }
    }
    if (check.fee !== 0 && check.feeCurrency)
      output += rpad(resources.strFee, 30) + lpad(check.feeCurrency, 12) + '\n';
    if (check.gratuity !== 0 && check.gratuityCurrency)
      output += rpad(resources.strGratuity2, 30) + lpad(check.gratuityCurrency, 12) + '\n';
    output += line + '\n';
    output += rpad(resources.strTotal2, 30) + lpad(check.checkTotalCurrency, 12) + '\n';
    output += line + '\n';
  }

  /*
    End Print Tax & Total Lines
  */
  log.debug('end print tax & total');

  /*
    Print Payments
  */
  var getPayment = function getPayment(payment) {
    var tempOutput = '';
    var strType = '';
    if (payment.payAmount > 0)
      strType += ' ' + resources.strPayment;
    else
      strType += ' ' + resources.strRefund;
    var voidStarBeg = '';
    var voidStarEnd = '';
    var openParen = '';
    var closedParen = '';
    var leftLength = 32;
    var rightLength = 10;
    if (payment.payStatus === PAY_STATUS.VOID) {
      voidStarBeg = '*';
      voidStarEnd = '*';
      openParen = '(';
      closedParen = ')';
      leftLength = 34;
      rightLength = 8;
    }
    switch(payment.payType) {
      case PAY_TYPE.GIFT_CARD:
        var giftCard = _.find(giftCards, function(gc) { return gc.custId === payment.custId; });
        if (giftCard) {
          var giftCardCustomer = _.find(customers, function(cst) { return cst.custId === giftCard.custId; });
          var balanceResource = '';
          var accountResource = '';
          var giftCardName = '';
          if (giftCardCustomer) {
            giftCardName = giftCardCustomer.fullName;
            if (giftCardCustomer.isGiftCard) {
              accountResource = resources.strGC;
              balanceResource = resources.strBalanceRemaining;
            }
            else {
              accountResource = resources.strCustomer;
              balanceResource = resources.strAccBalance;
            }
          }
          else {
            log.debug('received gift card payment, but no matching gift card customer');
            log.debug('falling back to printing gift card information');
            // we didn't receive the customer in the customers row
            // fall back to using information solely from the gift card
            // make some assumptions that what we received was actually a gift card,
            // since an incorrect fallback would still be better than not printing at all (6/24 - DL)
            accountResource = resources.strGC;
            balanceResource = resources.strBalanceRemaining;
            if (giftCard.crdId !== -1)
              giftCardName = 'Gift Card #' + giftCard.crdId;
          }
          tempOutput += rpad(voidStarBeg + accountResource + strType + '(' + payment.payDateShort + ')' + voidStarEnd, leftLength) + lpad(openParen + payment.payAmountCurrency + closedParen, rightLength) + '\n';
          if (giftCardName)
            tempOutput += '  ' + giftCardName + '\n';
          else
            log.debug('not printing gift card name, crdId was -1');
          tempOutput += '  ' + balanceResource.replace('{0:C}', giftCard.moneyCurrency) + '\n';
        }
        else
          log('Error: Received gift card payment, but no gift card object! payment.custId: ', payment.custId);
        break;
      case PAY_TYPE.CREDIT:
        tempOutput += rpad(voidStarBeg + (payment.cardType || '').trim() + (payment.lastFour || '') + ' ' + (payment.payAmount >= 0 ? resources.strPayment : resources.strRefund) + '(' + payment.payDateShort + ')' + voidStarEnd, leftLength) + lpad(openParen + payment.payAmountCurrency + closedParen, rightLength) + '\n';
        break;
      case PAY_TYPE.CHECK:
        tempOutput += rpad(voidStarBeg + resources.strCheck + strType + '(' + payment.payDateShort + ')' + voidStarEnd, leftLength) + lpad(openParen + payment.payAmountCurrency + closedParen, rightLength) + '\n';
        break;
      case PAY_TYPE.CASH:
        tempOutput += rpad(voidStarBeg + resources.strCash + strType + '(' + payment.payDateShort + ')' + voidStarEnd, leftLength) + lpad(openParen + payment.payAmountCurrency + closedParen, rightLength) + '\n';
        if (payment.tender > payment.payAmount && payment.payAmount > 0) {
          tempOutput += rpad(resources.strTender, leftLength) + lpad(payment.tenderCurrency, rightLength) + '\n';
          tempOutput += rpad(resources.strChange, leftLength) + lpad(payment.changeCurrency, rightLength) + '\n';
        }
        break;
      case PAY_TYPE.EXTERNAL:
        tempOutput += rpad(voidStarBeg + resources.strExternal + strType + '(' + payment.payDateShort + ')' + voidStarEnd, leftLength) + lpad(openParen + payment.payAmountCurrency + closedParen, rightLength) + '\n';
        break;
      case PAY_TYPE.VOUCHER:
        var voucherTemp = '';
        voucherTemp = payment.voucherNotes + ' ' + resources.strVoucher + ' ';
        if (payment.payAmount <= 0)
          voucherTemp += ' ' + resources.strRefund;
        voucherTemp = rpad(voucherTemp, 20); // won't this chop off the word "refund" if it's too long?
        tempOutput += rpad(voidStarBeg + voucherTemp + '(' + payment.payDateShort + ')' + voidStarEnd, leftLength) + lpad(openParen + payment.payAmountCurrency + closedParen, rightLength) + '\n';
        break;
      case PAY_TYPE.COMPLIMENTARY:
        tempOutput += rpad(voidStarBeg + resources.strComplimentary + '(' + payment.payDateShort + ')' + voidStarEnd, leftLength) + lpad(openParen + payment.payAmountCurrency + closedParen, rightLength) + '\n';
        break;
      case PAY_TYPE.DEBIT:
        tempOutput += rpad(voidStarBeg + resources.strDebit + strType + '(' + payment.payDateShort + voidStarEnd, leftLength) + lpad(openParen + payment.payAmountCurrency + closedParen, rightLength) + '\n';
        break;
    }
    return tempOutput;
  };

  var successfulPayments = _.filter(data.payments, function(x) { return x.checkId === check.checkId && x.payStatus !== PAY_STATUS.VOID; });
  var successfulPaymentsOutput = '';
  successfulPayments.forEach(function(payment) {
    successfulPaymentsOutput += getPayment(payment);
  });
  if (successfulPaymentsOutput && successfulPaymentsOutput.length > 0) {
    output += successfulPaymentsOutput;
    output += line + '\n';
  }
  if (options.printVoidedPayments) {
    var voidedPayments = _.filter(data.payments, function(x) { return x.checkId === check.checkId && x.payStatus === PAY_STATUS.VOID; });
    var voidedPaymentsOutput = '';
    voidedPayments.forEach(function(payment) {
      voidedPaymentsOutput += getPayment(payment);
    });
    if (voidedPaymentsOutput && voidedPaymentsOutput.length > 0) {
      output += resources.strVoidedPayment + "                         \n" + voidedPaymentsOutput; // seems hacky... matching previous code for now
      output += line + '\n';
    }
  }
  /*
      END Print Payments
  */
  log.debug('end print payments');


  /*
    Print Discount
  */
  if (check.discount > 0 && discount) {
    output += rpad((discount.description || ''), 30) + lpad(check.discountCurrency, 12) + '\n';
    output += line + '\n';
  }
  log.debug('end print discount');
  /*
    END Print Discount
  */

  /*
    Print Balance
  */
  output += rpad(resources.strBalance, 30) + lpad(check.checkRemainingTotalCurrency, 12) + '\n';
  output += line + '\n';
  log.debug('end print balance');
  /*
    END Print Balance
  */

  /*
    Print Gratuity
  */
  var getGratuity = function getGratuity(chk) {
    if (chk.gratuity !== 0)
      return '';
    if (options.printGratuityLine === 'none')
      return '';
    if (options.printGratuityLine === 'eventonly' && chk.checkType === CHECK_TYPE.REGULAR)
      return '';
    var tempOutput = '\n';
    tempOutput += rpad(resources.strGratuity, 10) + lpad('___________________________', 32) + '\n';
    tempOutput += '\n';
    tempOutput += rpad(resources.strTotal, 10) + lpad('___________________________', 32) + '\n';
    tempOutput += '\n';
    tempOutput += '\n';
    tempOutput += '\n';
    tempOutput += rpad(resources.strSignature, 10) + lpad('___________________________', 32) + '\n';
    tempOutput += '\n';
    return tempOutput;
  };
  output += getGratuity(check);
  /*
    End Gratuity
  */

  if (user && user.userName)
    output += rpad(resources.strUser, 12) + lpad(user.userName, 30) + '\n';
  if (terminal)
    output += rpad(resources.strTerminal, 9) + lpad(terminal, 33) + '\n';
  output += line + '\n';

  /*
    GetPrintReceiptFooter
  */
  if (resources.receiptFooterText1 && resources.receiptFooterText1.trim().length > 0)
    output += cpad(resources.receiptFooterText1, 42) + '\n';
  if (resources.receiptFooterText2 && resources.receiptFooterText2.trim().length > 0)
    output += cpad(resources.receiptFooterText2, 42) + '\n';
  if (resources.receiptFooterText3 && resources.receiptFooterText3.trim().length > 0)
    output += cpad(resources.receiptFooterText3, 42) + '\n';
  if (resources.receiptFooterText4 && resources.receiptFooterText4.trim().length > 0)
    output += cpad(resources.receiptFooterText4, 42) + '\n';
  log.debug('end print receipt footer');
  /*
    End GetPrintReceiptFooter
  */

  // Insert ClubSpeed logo placeholder
  if (options.clubSpeedLogoPath && options.clubSpeedLogoPath.length > 0)
      output += '{{ClubSpeedLogo}}\n';

  /*
    GetPrintReceiptSurveyFooter
  */
  var getPrintReceiptSurveyFooter = function() {
    if (!options.printSurveyUrlOnReceipt || options.printSurveyUrlOnReceipt.toString().toLowerCase() === 'false')
      return '';

    var tempOutput = '\n\n';
    var surveyUrl = '';
    surveyUrl = options.urlSurvey;
    if (surveyUrl && surveyUrl.charAt(surveyUrl.length - 1) === '/')
      surveyUrl = surveyUrl.slice(0, -1);

    var surveyFooter1 = resources.receiptFooterSurveyText1 ? resources.receiptFooterSurveyText1.replace('##SURVEYURL##', surveyUrl).trim() : '';
    var surveyFooter2 = resources.receiptFooterSurveyText2 ? resources.receiptFooterSurveyText2.replace('##SURVEYURL##', surveyUrl).trim() : '';
    if (!surveyFooter1.length && !surveyFooter2.length)
      return '';

    if (surveyFooter1.length)
      tempOutput += rpad(surveyFooter1, 42) + '\n';
    if (surveyFooter2.length)
      tempOutput += rpad(surveyFooter2, 42) + '\n';
    tempOutput += rpad(resources.strAccessCode + ' ' + options.accessCode, 42) + '\n';
    return tempOutput;
  };
  output += getPrintReceiptSurveyFooter();
  /*
    End GetPrintReceiptSurveyFooter
  */

  // Inject fiscal printer response (if we are not in training mode)
  if(fiscal && fiscal.success && check.checkType !== 'training') { // the checkType will never be 'training', as this should actually be the FiscalPrinterCheckType (super, super, super hacky piece on the ViewModel, not the DbModel)
      output += '\n';
      output += "Kontrollkod: " + fiscal.serial + '\n';
      output += "Kontrollenhet: " + fiscal.receiptCode + '\n';
      output += '\n';
  }

  if (options.printBarcodeOnReceipt && check.checkId) {
    output += PLACEHOLDERS.BARCODE.replace('###VAL###', check.checkId) + '\n';
  }

  // Feed and Cut Paper
  output += '\n\n\n\n\n\n';
  output += PLACEHOLDERS.CUTPAPER;

  log.debug('output:\n', output);
  return output;
};
