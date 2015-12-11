"use strict";

var z             = require('./zana');
var config        = require('./config-provider.js');
var CONSTANTS     = require('./constants.js');
var utils         = require('./utils.js');
var rpad          = utils.receipts.rpad;
var lpad          = utils.receipts.lpad;
var cpad          = utils.receipts.cpad;
var log           = utils.logging.log;
log.debug.on      = config.receipts.useDebugLogging;
var PAY_STATUS    = CONSTANTS.PAY_STATUS;
var CHECK_TYPE    = CONSTANTS.CHECK_TYPE;
var PLACEHOLDERS  = CONSTANTS.PLACEHOLDERS;

/*
body example

{
    "data": {
        "payment": {
            "encryptedCardNo": "encryptedCardNo",
            "cardType": "Visa",
            "expirationDate": "2015/4/20 15:43:22",
            "accountName": "AccountName",
            "responseTime": 1.0,
            "authorizationCode": "911",
            "avs": "AVS",
            "referenceNumber": "123refnumb",
            "resultCode": "123 resultcode",
            "troutD": "TroudD",
            "autAmount": 100.0,
            "lastFour": "2342",
            "checkId": 0,
            "payId": 1,
            "userId": 1,
            "payType": 2,
            "payTerminal": "POS1",
            "payAmount": 10.0,
            "payDate": "2015-04-15T15:43:22.8008023-07:00",
            "shift": 1,
            "exchangeRate": 0.98,
            "otherCurrency": "test",
            "tender": 0.0,
            "payStatus": 1,
            "voidDate": "2015-04-15T15:43:22.8008023-07:00",
            "voidUserId": 1,
            "voidTerminal": "test",
            "voidNotes": "voidPaymentNotes",
            "payAmountCurrency": "¥10.00",
            "tenderCurrency": "¥0.00",
            "payDateShort": "2015/4/15",
            "changeCurrency": "¥-10.00"
        },
        "check": {
            "checkId": 6,
            "custId": 1000002,
            "checkSubtotal": 7790.0000,
            "checkTax": 0.000000,
            "checkTotal": 7790.000000,
            "checkPaidTax": 0.00,
            "checkPaidTotal": 6470.00,
            "checkRemainingTotal": 1320.00,
            "checkRemainingTax": 0.00,
            "checkType": 1,
            "checkStatus": 0,
            "checkName": "",
            "userId": 1,
            "checkTotalApplied": 7790.00,
            "brokerName": "",
            "notes": "",
            "gratuity": 0.00,
            "fee": 0.00,
            "openedDate": "2015-03-26T17:21:24.407",
            "closedDate": null,
            "isTaxExempt": false,
            "discount": 0.00,
            "invoiceDate": null,
            "checkGST": 0.000000,
            "checkPST": 0.000000,
            "openedDateShort": "2015/3/26",
            "closedDateShort": "",
            "openedDateTime": "2015/3/26 17:21",
            "closedDateTime": "",
            "checkSubtotalCurrency": "¥7,790.00",
            "checkTotalCurrency": "¥7,790.00",
            "checkTaxCurrency": "¥0.00",
            "feeCurrency": "¥0.00",
            "totalCurrency": "¥7,790.00",
            "gratuityCurrency": "¥0.00",
            "checkGSTCurrency": "¥0.00",
            "checkRemainingTotalCurrency": "¥1,320.00",
            "checkPSTCurrency": "¥0.00",
            "discountCurrency": "¥0.00"
        },
        "customer": {
            "custId": 1000002,
            "cardId": -1,
            "lastName": "Webb",
            "firstName": "Chris",
            "racerName": "Chris Webb",
            "accountCreated": "2015-03-25T10:44:11.347",
            "lastVisited": "2015-04-08T09:11:21.197",
            "totalVisits": 5,
            "totalRaces": 7,
            "address": "",
            "city": "",
            "state": "",
            "country": "",
            "phoneNumber": "",
            "birthDate": "1980-02-02T00:00:00",
            "emailAddress": "",
            "company": "",
            "generalNotes": "",
            "zip": "",
            "licenseNumber": "",
            "issuedBy": "",
            "sourceId": 1,
            "doNotMail": false,
            "gender": 0,
            "rpm": 1205,
            "waiverId": 1,
            "originalId": 1,
            "isGiftCard": false,
            "award1": 0,
            "award2": 2,
            "hotel": "",
            "priceLevel": 1,
            "waiverId2": 7,
            "membershipStatus": 0,
            "password": "",
            "webUserName": "",
            "custom1": "",
            "custom2": "",
            "custom3": "",
            "custom4": "",
            "privacy1": false,
            "privacy2": false,
            "privacy3": false,
            "privacy4": false,
            "refId": 0,
            "industryId": 0,
            "phoneNumber2": "",
            "fax": "",
            "cell": "",
            "address2": "",
            "promotionCode": "",
            "membershipText": "",
            "membershipTextLong": "",
            "deleted": false,
            "isEmployee": false,
            "status1": 2,
            "status2": 0,
            "status3": 0,
            "status4": 0,
            "creditLimit": 0.0,
            "creditOnhold": false,
            "ignoreDOB": false,
            "cnt": 1000,
            "fullName": "Chris Webb"
        }
    },
    "type": "creditCard",
    "terminalName": null,
    "resources": {
        "strAccBalance": "{0：C}帐户余额",
        "strAccessCode": "您访问的代码是：",
        "strAge": "年龄",
        "strAmount": "量",
        "strAuthNo": "验证号＃",
        "strBalance": "平衡",
        "strBalanceRemaining": "{0：C}平衡剩余。",
        "strBestLap": "最快圈速",
        "strCash": "现金",
        "strCashInDrawer": "现金抽屉",
        "strCC": "信用卡",
        "strChange": "变化",
        "strCheck": "检查",
        "strCheckNo": "支票号码",
        "strComplimentary": "免费",
        "strCrdIssuer": "到卡发行者达成协议。",
        "strCreatedBy": "创建者{0}",
        "strCustomer": "顾客",
        "strCustomer2": "客户：",
        "strDebit": "借方",
        "strDesc": "商品说明。",
        "strDuration": "长短",
        "strEventInfo": "事件信息",
        "strEventName": "事件名称",
        "strExempt": "（免除）",
        "strExpDate": "进出口。日期",
        "strExpense2": "EXPENSE",
        "strExperience": "经验",
        "strExternal": "外部",
        "strFee": "费",
        "strGameCard": "游戏卡",
        "strGC": "礼品卡",
        "strGratuity": "小费：",
        "strGratuity2": "赏钱",
        "strGrid": "格",
        "strGST": "GST",
        "strHeatNo": "热火＃",
        "strHeatNumber": "热号",
        "strIAgree": "我同意支付根据上述数额",
        "strId": "ID：",
        "String3": "圈数：",
        "strLaps": "圈",
        "strMinutes": "分钟",
        "strMinutes2": "纪要：",
        "strNA": "N / A",
        "strName": "姓名：",
        "strNew": "新",
        "strPassword": "密码",
        "strPayment": "付款",
        "strPosition": "位置",
        "strPST": "太平洋标准时间",
        "strReceiptNo": "收据号码",
        "strRefNo": "参考编号＃",
        "strRefund": "退",
        "strRoundNo": "轮号",
        "strSessions": "会议",
        "strShift": "按住Shift键：",
        "strShiftPayment": "转移支付类型",
        "strSignature": "签名：",
        "strSubtotal": "小计",
        "strTender": "招标",
        "strTerminal": "终奌站：",
        "strTillReport": "截止本报告",
        "strTime": "时间",
        "strTime2": "时间：",
        "strTotal": "总计：",
        "strTotal2": "总",
        "strTroutD": "TroutD。＃",
        "strType": "类型：",
        "strUser": "用户：",
        "strUsername": "用户名",
        "strVenue": "地点",
        "strVoided": "作废",
        "strVoidedPayment": "*空心付款*",
        "strVoucher": "凭证",
        "strWinBy": "赢得",
        "receiptFooterText1": "",
        "receiptFooterText2": "",
        "receiptFooterText3": "",
        "receiptFooterText4": "Powered By www.ClubSpeed.com",
        "receiptFooterSurveyText1": "",
        "receiptFooterSurveyText2": "",
        "raceTicketLine1": "Please present this ticket to track",
        "raceTicketLine2": " staff 5 minutes before your race ",
        "raceTicketLine3": " time above. Enjoy!!!",
        "raceTicketLine4": "",
        "receiptHeaderText1": "",
        "receiptHeaderText2": "",
        "receiptHeaderText3": "",
        "receiptHeaderText4": "",
        "taxLabel": "Tax"
    },
    "options": {
        "showScheduledTime": false,
        "numberOfTracks": 3,
        "organizationNumber": "",
        "printAgeOnRaceTicket": "false",
        "printGridOnRaceTicket": "true",
        "printSurveyUrlOnReceipt": "false",
        "printVoidedPayments": false,
        "showHeatNo": "true",
        "showScheduleTimeOnRaceTicket": "",
        "urlSurvey": "http://ikcshenyang.clubspeedtiming.com/sp_survey/",
        "clubSpeedLogoPath": "C:\\Clubspeed\\Images\\SS.bmp",
        "printGratuityLine": "none",
        "paymentSignaturesPath": "C:\\Clubspeed\\PaymentSignatures\\",
        "has2Taxes": false,
        "receiptHeaderAlign": "",
        "companyLogoPath": "",
        "printDetail": null,
        "accessCode": null,
        "useESign": true
    }
}
*/

var defaults = {
    "data": {
        "payment"  : {},
        "check"    : {},
        "customer" : {}
    },
    "resources": {
          "strAuthNo"    : "Auth No. #"
        , "strCC"        : "Credit Card"
        , "strCrdIssuer" : "to the card issuer agreement."
        , "strCustomer"  : "Customer"
        , "strGratuity2" : "Gratuity"
        , "strIAgree"    : "I agree to pay the above amount according"
        , "strNA"        : "N/A"
        , "strPayment"   : "Payment"
        , "strReceiptNo" : "Receipt Number"
        , "strRefNo"     : "Ref No. #"
        , "strRefund"    : "Refund"
        , "strTerminal"  : "Terminal:"
        , "strTotal2"    : "Total"
        , "strTroutD"    : "TroutD. #"
        , "strVoided"    : "VOIDED"
    },
    "options": {
        "useESign": false,
        "printGratuityLine": "none"
    },
    "terminalName": ""
};

var line = '------------------------------------------';

function CreditCardReceiptTemplater() {}
CreditCardReceiptTemplater.prototype.create = function(body) {
    log.debug('----- building credit card receipt -----');
    log.debug('input:\n', body);
    if (!body)
        body = {};
    var receipt   = z.extend(body, defaults);
    var resources = receipt.resources;
    var options   = receipt.options;
    var data      = receipt.data;
    var check     = data.check;
    var payment   = data.payment;
    var customer  = data.customer;

    // Begin the Receipt
    var output = '\n\n';

    if (payment.payStatus === PAY_STATUS.VOID)
        output += cpad('### ' + resources.strVoided + ' ###') + '\n\n';
    if (check) {
        if (check && check.custId !== 0 && customer)
            output += rpad(resources.strCustomer, 9) + lpad((customer && customer.fullName ? customer.fullName : resources.strNA), 33) + '\n';
        output += rpad(resources.strReceiptNo + ' ' + (check.checkId || resources.strNA), 23) + lpad(check.openedDateTime, 19) + '\n';
        if (check.closedDateShort && check.closedDateShort !== check.openedDateShort) // different opened and closed days
            output += lpad(check.closedDateTime, 42) + '\n';
    }
    output += line + '\n';

    output += rpad(resources.strCC + ' ' + (payment && payment.amount < 0 ? resources.strRefund : resources.strPayment), 30) + lpad(payment.payAmountCurrency, 12) + '\n';
    output += '   ' + (payment.cardType || '') + ' ' + (payment.lastFour || '') + '\n';
    output += '   ' + (resources.strAuthNo || '') + ' ' + (payment.authorizationCode || '') + '\n';
    output += '   ' + (resources.strRefNo || '') + ' ' + (payment.referenceNumber || '') + '\n';
    output += '   ' + (resources.strTroutD || '') + ' ' + (payment.troutD || '') + '\n';

    if (check.gratuity === 0 && ((options.printGratuityLine === 'eventonly' && check.checkType === CHECK_TYPE.REGULAR) || options.printGratuityLine === 'all')) {
        output += '\n';
        output += rpad(resources.strGratuity2 + ' ', 30) + lpad('____________', 12) + '\n';
        output += '\n';
        output += '\n';
        output += rpad(resources.strTotal2 + ' ', 30) + lpad('____________', 12) + '\n';
    }

    output += '\n';
    output += '\n';
    output += resources.strIAgree + '\n';
    output += resources.strCrdIssuer + '\n';
    if (options.useESign)
        output += PLACEHOLDERS.SIGNATURE + '\n';
    else {
        output += '\n';
        output += '\n';
        output += '\n';
    }
    output += 'Sign here: X______________________________' + '\n';
    output += lpad(payment.accountName, 42) + '\n';
    output += '\n';
    output += rpad(resources.strTerminal, 9) + lpad(receipt.terminalName, 33) + '\n';
    output += '\n';
    if (payment.payStatus === PAY_STATUS.VOID) {
        output += cpad('### ' + resources.strVoided + ' ###') + '\n';
        output += '\n';
    }

    // Feed and Cut Paper
    output += '\n\n\n\n\n\n';
    output += PLACEHOLDERS.CUTPAPER;

    log.debug('output:\n', output);
    return output;
};

module.exports = new CreditCardReceiptTemplater();
