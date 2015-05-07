/*
    Missing resources (not yet created in ClubSpeed, hard coded as English):

    strRacer
    strOrganizationNumber
    strTo (may not be necessary?)
*/

var _                   = require('./underscore');
var z                   = require('./zana');
var config              = require('./config-provider.js');
var CONSTANTS           = require('./constants.js');
var utils               = require('./utils.js');
var rpad                = utils.receipts.rpad;
var lpad                = utils.receipts.lpad;
var cpad                = utils.receipts.cpad;
var alignLeft           = utils.receipts.alignLeft;
var alignCenter         = utils.receipts.alignCenter;
var alignRight          = utils.receipts.alignRight;
var log                 = utils.logging.log;
log.debug.on            = config.receipts.useDebugLogging;
var CHECK_TYPE          = CONSTANTS.CHECK_TYPE;
var PRODUCT_TYPE        = CONSTANTS.PRODUCT_TYPE;
var CHECK_DETAIL_STATUS = CONSTANTS.CHECK_DETAIL_STATUS;
var PAY_STATUS          = CONSTANTS.PAY_STATUS;
var PAY_TYPE            = CONSTANTS.PAY_TYPE;
var SURVEY_SOURCE       = CONSTANTS.SURVEY_SOURCE;

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
        "strAge"                   : "Age",
        "strAmount"                : "Amount",
        "strAuthNo"                : "Auth No. #",
        "strBalance"               : "Balance",
        "strBalanceRemaining"      : "{0:C} Balance remaining.",
        "strBestLap"               : "Best Lap",
        "strCash"                  : "Cash",
        "strCashInDrawer"          : "Cash In Drawer",
        "strCC"                    : "Credit Card",
        "strChange"                : "Change",
        "strCheck"                 : "Check",
        "strCheckNo"               : "Check Number",
        "strComplimentary"         : "Complimentary",
        "strCrdIssuer"             : "to the card issuer agreement.",
        "strCreatedBy"             : "Created by {0}",
        "strCustomer"              : "Customer",
        "strCustomer2"             : "Customer:",
        "strDebit"                 : "Debit",
        "strDesc"                  : "Desc.",
        "strDuration"              : "Duration",
        "strEventInfo"             : "Event Information",
        "strEventName"             : "Event Name",
        "strExempt"                : "(Exempt)",
        "strExpDate"               : "Exp. Date",
        "strExpense2"              : "EXPENSE",
        "strExperience"            : "Experience",
        "strExternal"              : "External",
        "strFee"                   : "Fee",
        "strGameCard"              : "Game Card",
        "strGC"                    : "Gift Card",
        "strGratuity"              : "Gratuity:",
        "strGratuity2"             : "Gratuity",
        "strGrid"                  : "Grid",
        "strGST"                   : "GST",
        "strHeatNo"                : "Heat#",
        "strHeatNumber"            : "Heat No.",
        "strIAgree"                : "I agree to pay the above amount according",
        "strId"                    : "ID:",
        "String3"                  : "Laps:",
        "strLaps"                  : "Laps",
        "strMinutes"               : "Minutes",
        "strMinutes2"              : "Minutes:",
        "strNA"                    : "N/A",
        "strName"                  : "Name:",
        "strNew"                   : "New",
        "strPassword"              : "Password",
        "strPayment"               : "Payment",
        "strPosition"              : "Position",
        "strPST"                   : "PST",
        "strReceiptNo"             : "Receipt Number",
        "strRefNo"                 : "Ref No. #",
        "strRefund"                : "Refund",
        "strRoundNo"               : "Round No.",
        "strSessions"              : "sessions",
        "strShift"                 : "Shift:",
        "strShiftPayment"          : "Shift  Payment Type",
        "strSignature"             : "Signature:",
        "strSubtotal"              : "Subtotal",
        "strTender"                : "Tendered",
        "strTerminal"              : "Terminal:",
        "strTillreport"            : "TILL REPORT",
        "strTime"                  : "Time",
        "strTime2"                 : "Time:",
        "strTotal"                 : "Total:",
        "strTotal2"                : "Total",
        "strTroutD"                : "TroutD. #",
        "strType"                  : "Type:",
        "strUser"                  : "User:",
        "strUsername"              : "UserName",
        "strVenue"                 : "Venue",
        "strVoided"                : "VOIDED",
        "strVoidedPayment"         : "*Voided Payments*",
        "strVoucher"               : "Voucher",
        "strWinBy"                 : "Win By",
        "receiptFooterText1"       : "",
        "receiptFooterText2"       : "",
        "receiptFooterText3"       : "",
        "receiptFooterText4"       : "Powered By www.ClubSpeed.com",
        "receiptFooterSurveyText1" : "",
        "receiptFooterSurveyText2" : "",
        "raceTicketLine1"          : "",
        "raceTicketLine2"          : "",
        "raceTicketLine3"          : "",
        "raceTicketLine4"          : "",
        "receiptHeaderText1"       : "",
        "receiptHeaderText2"       : "",
        "receiptHeaderText3"       : "",
        "receiptHeaderText4"       : "",
        "taxLabel"                 : "Tax"
    },
    "options":{
        "notShowScheduledTime"         : "true",
        "organizationNumber"           : "",
        "printAgeOnRaceTicket"         : "false",
        "printGridOnRaceTicket"        : "true",
        "printSurveyUrlOnReceipt"      : "false",
        "printVoidedPayments"          : false,
        "showHeatNo"                   : "true",
        "showScheduleTimeOnRaceTicket" : "",
        "urlSurvey"                    : "",
        "accessCode"                   : "",
        "clubSpeedLogoPath"            : "C:\\Clubspeed\\Images\\SS.bmp",
        "printGratuityLine"            : "none",
        "paymentSignaturesPath"        : "C:\\Clubspeed\\PaymentSignatures\\",
        "has2Taxes"                    : false,
        "receiptHeaderAlign"           : "center",
        "companyLogoPath"              : "",
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

  var type         = receipt.type; // unnecessary, since we are already here, hooray.
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

  // Begin the Receipt
  var output = '\n\n';
  var line = '------------------------------------------';

  // Insert Logo
  if(options.companyLogoPath.length > 0)
      output += '{{LOGO}}\n';

  // Insert receipt header (with alignment), if exists
  var alignFunction;
  switch(options.receiptHeaderAlign) {
      case 'left':
          // output += alignLeft();
          alignFunction = rpad;
          break;
       case 'right':
          // output += alignRight();
          alignFunction = lpad;
          break;
       // case 'center':
       default:
          // output += alignCenter();
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

  // output += (resources.receiptHeaderText1.trim().length > 0) ? '\n' + resources.receiptHeaderText1 + '\n' : '';
  // output += (resources.receiptHeaderText2.trim().length > 0) ? resources.receiptHeaderText2 + '\n' : '';
  // output += (resources.receiptHeaderText3.trim().length > 0) ? resources.receiptHeaderText3 + '\n' : '';
  // output += (resources.receiptHeaderText4.trim().length > 0) ? resources.receiptHeaderText4 + '\n' : '';
  // output += alignLeft(); // reset alignment

  // Fiscal Printer Organization Number
  if(options.organizationNumber && options.organizationNumber.length > 0)
      output += 'Org. #: ' + options.organizationNumber + '\n'; // RESOURCE NEEDED
  
  if (check && check.checkType === CHECK_TYPE.EVENT && event != null) { // single equal (!=) on purpose
      /*
        Print Event Information (COMPLETED)
      */
      var strDuration = (event.startTime || resources.strNA) + ' to ' + (event.endTime || resources.strNA); // RESOURCE NEEDED
      output += resources.strEventInfo + '\n';
      if (event.subject && event.subject.trim().length > 0)
        output += resources.strName + ' ' + event.subject + '\n';
      output += (event.numberOfRacers || resources.strNA) + ' ' + 'Racers' + '\n'; // RESOURCE NEEDED
      output += (event.startDate || resources.strNA) + ' ' + strDuration + '\n';
      output += resources.strCreatedBy.replace('{0}', event.createdBy) + '\n'; // note the string replace -- Resources are actually being stored as String.Format with placeholders
  }
  output += '\n';
  log.debug('end print event');
  
  /*
    Print Customer Name (COMPLETED)
  */
  if (check && +check.custId > 0) {
    var customer = _.find(customers, function(x) { return x.custId == check.custId; });
    var fullName = (customer && customer.fullName && customer.fullName.trim().length > 0 ? customer.fullName : '');
    output += resources.strCustomer + ': ' + fullName + '\n';
  }
  log.debug('end print customer');

  /*
    Print Receipt # (COMPLETED)
  */
  output += rpad(resources.strReceiptNo + ' ' + (check.checkId || resources.strNA), 23) + lpad(check.openedDateTime, 19) + '\n';
  log.debug('end print receipt # and opened date');

  /*
    Print Closed Date (COMPLETED)
  */
  if (check.closedDateShort && check.closedDateShort !== check.openedDateShort) // different opened and closed days
    output += lpad(check.closedDateTime, 42) + '\n';
  output += line + '\n';
  log.debug('end print closed date');

  /*
    GetPrintItems(COMPLETED)
  */
  checkDetails = _.select(checkDetails, function(x) { return x.checkId == check.checkId; }); // shouldn't be necessary, but just to be safe.
  if (check && check.checkType === CHECK_TYPE.EVENT && options.printDetail === false) { // the printDetail === false matches vb logic.. seems odd.
    checkDetails.forEach(function(detail) {
      if (detail.checkDetailStatus !== CHECK_DETAIL_STATUS.HAS_VOIDED) { // new or 'cannot deleted' (permanent)
        /*
          GetPrintEventCheckDetail (COMPLETED)
        */
        // ditching the grouping logic of ProductIDs from Function GetPrintItems (intentionally)
        // in order to match the display of the POS with line items separated
        var product = _.find(products, function(x) { return x.productId === detail.productId; });
        if (product) {
          var description = product.description || '';
          if (description.length >= 25)
            description = (description.substr(0, 22) + '...');
          if (detail.Qty > 1)
            description = detail.qty + ')' + description; // or actualQty? qty + cadetQty?
          output += rpad(description, 30) + lpad(detail.checkDetailSubtotalCurrency, 12) + '\n';
        }
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
          GetPrintCheckDetail (COMPLETED)
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
          productName = productName.substr(0,24) + '...';
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
    Print Tax & Total Lines (COMPLETED)
  */
  if (check) {
    output += rpad(resources.strSubtotal, 30) + lpad(check.checkSubtotalCurrency, 12) + '\n';
    if (!options.has2Taxes)
      output += rpad(resources.taxLabel + (check.isTaxExempt ? resources.strExempt : ''), 30) + lpad(check.checkTaxCurrency, 12) + '\n';
    else {
      if (check.checkGST && check.checkGST > 0) {
        output += rpad(resources.strGST + (check.isTaxExempt ? resources.strExempt : ''), 30) + lpad(check.checkGSTCurrency, 12) + '\n';
        output += rpad(resources.strPST + (check.isTaxExempt ? resources.strExempt : ''), 30) + lpad(check.checkPSTCurrency, 12) + '\n';
      }
    }
    output += line + '\n';
    output += rpad(resources.strTotal2, 30) + lpad(check.checkTotalCurrency, 12) + '\n';
    output += line + '\n';
  }
  
  /*
    End Print Tax & Total Lines
  */
  log.debug('end print tax & total');

  /*
    Print Payments (COMPLETED)
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
    if (payment.payStatus == PAY_STATUS.VOID) {
      voidStarBeg = '*';
      voidStarEnd = '*';
      openParen = '(';
      closedParen = ')';
      leftLength = 34;
      rightLength = 8;
    }
    switch(payment.payType) {
      case PAY_TYPE.GIFT_CARD:
        var giftCard = _.find(giftCards, function(x) { return x.custId === payment.custId; });
        if (giftCard) {
          var giftCardCustomer = _.find(customers, function(x) { return x.crdId === giftCard.crdId; });
          if (giftCardCustomer) {
            var balanceResource = '';
            var accountResource = '';
            if (giftCardCustomer.isGiftCard) {
              accountResource = resources.strGC;
              balanceResource = resources.strBalanceRemaining;
            }
            else {
              accountResource = resources.strCustomer;
              balanceResource = resources.strAccBalance;
            }
            tempOutput += rpad(voidStarBeg + accountResource + strType + '(' + payment.payDateShort + ')' + voidStarEnd, leftLength) + lpad(openParen + payment.payAmountCurrency + closedParen, rightLength) + '\n';
            tempOutput += '  ' + giftCardCustomer.fullName + '\n';
            tempOutput += '  ' + balanceResource.replace('{0:C}', giftCard.moneyCurrency) + '\n';
          }
          else
            log('Error: Received gift card payment, but no customer with the same crdId! giftCard.crdId: ',  giftCard.crdId);
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
    var voidedPayments = _.filter(data.payments, function(x) { return x.checkId == check.checkId && x.payStatus === PAY_STATUS.VOID; });
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
    Print Discount (COMPLETED)
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
    Print Balance (COMPLETED)
  */
  output += rpad(resources.strBalance, 30) + lpad(check.checkRemainingTotalCurrency, 12) + '\n';
  output += line + '\n';
  log.debug('end print balance');
  /*
    END Print Balance
  */


  /*
    Print Gratuity (COMPLETED)
  */
  var getGratuity = function getGratuity(check) {
    if (check.gratuity !== 0)
      return '';
    if (options.printGratuityLineSetting == 'none')
      return '';
    if (options.printGratuityLineSetting == 'eventonly' && check.checkType == CHECK_TYPE.REGULAR)
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
    output += rpad(resources.strUser, 5) + lpad(user.userName, 37) + '\n';
  if (terminal)
    output += rpad(resources.strTerminal, 9) + lpad(terminal, 33) + '\n';
  output += line + '\n';

  /*
    GetPrintReceiptFooter (COMPLETED)
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

  /*
    GetPrintReceiptSurveyFooter ()
  */
  var getPrintReceiptSurveyFooter = function() {
    if (!options.printSurveyUrlOnReceipt || options.printSurveyUrlOnReceipt.toString().toLowerCase() == 'false')
      return '';

    var tempOutput = '\n\n';
    var surveyUrl = '';
    surveyUrl = options.urlSurvey;
    if (surveyUrl && surveyUrl.charAt(surveyUrl.length-1) === '/')
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

  // Feed and Cut Paper
  output += '\n\n\n\n\n\n';
  output += ('\x1d\x56\x01');

  log.debug('output:\n', output);
  return output;
};