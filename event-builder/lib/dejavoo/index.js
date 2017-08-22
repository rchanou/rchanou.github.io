var Promise = require("bluebird");
var request = require('request');
var toJson = require('xml2js').parseString;
var xml2js = require('xml2js');
var _ = require('lodash');
var querystring = require('querystring');
Promise.longStackTraces();
var opts;

function Dejavoo (opts) {
	this.opts = opts || {};
	this.opts.debug   = opts.provider.options.debug                               || false;
	this.opts.spinApiUrl = opts.provider.options.spinApiUrl                       || 'https://spinpos.net:443/spin/cgi.html';
	this.opts.authorizationKey = opts.provider.options.authorizationKey;
	this.opts.dejavooSpinRegisterId = opts.provider.options.dejavooSpinRegisterId;
	if (opts.provider.options.referenceId != null ) {
	    this.opts.referenceId = opts.provider.options.referenceId;
	}
	this.opts.timeout = opts.provider.options.timeout                             || 240;

	// Variables Justin/VB code expects to always exist.
	this.opts.defaultHoistedVars = {
		success: false,
		transactionId: null,
        resultCode: null,
		requestAmount: null,
		authorizeAmount: null,
		creditCardAccount: null,
		creditCardType: null,
		creditCardAuthorizationCode: null,
		creditCardExpirationDate: null,
		resultCode: null,
		troutD: null,
		emvReceiptRequirement: null,
		error: null,
		code: null,
        message: null,
		_original: null
	}

	request = request.defaults({
		url: this.opts.spinApiUrl,
		timeout: 7*60*1000 + (this.opts.timeout * 1000)
		});
}

Dejavoo.prototype.signature = function signature(signature) {
	throw new Error('SIGNATURE is not supported by Dejavoo terminals');

	/*var failureMessage = {
		'message': result.TRANRESP.TRANRESPMESSAGE[0],
		'code': result.TRANRESP.hasOwnProperty('TRANRESPERRCODE') ? result.TRANRESP.TRANRESPERRCODE[0] : null,
		'success': false,
		'signatureJSON': null,
		'_original': result
		};
	var successMessage = {
		'success': true,
		'signatureJSON': signatureJSON,
		'_original': result
		};*/
}

Dejavoo.prototype.submitTransaction = function submitTransaction(order, creditCard, prospect, other) {
	/*
	// Request
	HTTPS://spinpos.net:443/spin/cgi.html?TerminalTransaction=<request><PaymentType>Credit</PaymentType><TransType>Sale</TransType><Amount>1.00</Amount><Tip>0.00</Tip><Frequency>OneTime</Frequency><InvNum>123</InvNum><RefId>3</RefId><RegisterId>540048</RegisterId><AuthKey>IpEJM96lMa</AuthKey><PrintReceipt>No</PrintReceipt></request>


	// Cancelled transaction
	<xmp>
	<response>
	<RefId>2</RefId>
	<RegisterId>540048</RegisterId>
	<ResultCode>1</ResultCode>
	<Message>Canceled</Message>
	<PaymentType>Credit</PaymentType>
	<TransType>Sale</TransType>
	<SN>000118110027051</SN>
	<ExtData>InvNum=0,CardType=EBT,BatchNum=254,Tip=0.00,CashBack=0.00,Fee=0.00,AcntLast4=,Name=,SVC=0.00,TotalAmt=1.00,DISC=0.00,Donation=0.00,SHFee=0.00,RwdPoints=0,RwdBalance=0,RwdIssued=,EBTFSLedgerBalance=,EBTFSAvailBalance=,EBTFSBeginBalance=,EBTCashLedgerBalance=,EBTCashAvailBalance=,EBTCashBeginBalance=,RewardCode=,AcqRefData=,ProcessData=,RefNo=,RewardQR=,Language=English,EntryType=Swipe,table_num=0,clerk_id=,ticket_num=,ControlNum=,TaxCity=0.00,TaxState=0.00</ExtData>
	</response>
	</xmp>


	// Approval
	<xmp>
	<response>
	<RefId>4</RefId>
	<RegisterId>540048</RegisterId>
	<InvNum>123</InvNum>
	<ResultCode>0</ResultCode>
	<RespMSG>APPROVAL%20AXS156</RespMSG>
	<Message>Approved</Message>
	<AuthCode>AXS156</AuthCode>
	<PNRef>620418500343</PNRef>
	<PaymentType>Credit</PaymentType>
	<TransType>Sale</TransType>
	<SN>000118110027051</SN>
	<ExtData>InvNum=123,CardType=AMEX,BatchNum=254,Tip=0.00,CashBack=0.00,Fee=0.00,AcntLast4=5005,Name=RATCLIFF%2fWESLY%20R%20%20%20%20%20%20%20%20%20%20,SVC=0.00,TotalAmt=1.00,DISC=0.00,Donation=0.00,SHFee=0.00,RwdPoints=0,RwdBalance=0,RwdIssued=,EBTFSLedgerBalance=,EBTFSAvailBalance=,EBTFSBeginBalance=,EBTCashLedgerBalance=,EBTCashAvailBalance=,EBTCashBeginBalance=,RewardCode=,AcqRefData=,ProcessData=,RefNo=,RewardQR=,Language=English,EntryType=CHIP,table_num=0,clerk_id=,ticket_num=,ControlNum=,TaxCity=0.00,TaxState=0.00</ExtData>
	<EMVData>AID=A000000025010801,AppName=AMERICAN EXPRESS,TVR=0000008050,TSI=FC00</EMVData>
	<Sign>Qk1CBgAAAAAAAD4AAAAoAAAAjwAAAE0AAAABAAEAAAAAAAQGAADEDgAAxA4AAAAAAAAAAAAAAAAAAP///wD////////AP/////////////4AAP//////+AA//////////////gAA//////4AAD/////////////+AAD/////+AAOH/////////////4AAP/////gA/4f/////////////gAA/////8B//x/////////////+AAD/////Af//H/////////////4AAP////gH//8P/////////////gAA////wA///w/////////////+AAD///8AP///j/////////////4AAP///gH///+P/////////////gAA///8D////4f////////////+AAD///w/////h/////////////4AAP//+H/////H/////////////gAA///wf////8f/////AAAf///+AAD//+D/////x/+AAAAAAAAH//4AAP//wf/////D/gAAAAAAAAD//gAA//+D/////8P+AAAAP/+AAAf+AAD//4f/////4/4f///////gAf4AAP//j//////j/w////////8A/gAA//8P/////+H/D////////+B+AAD//w/////+If+H////////+H4AAP//H/////wB/4f////////8fgAA//8f////8AA/w/////////h+AAD//h/////gAA/B////////8H4AAP/+H////8DwB+H////////A/gAA//4/////A/AB8P///////wH+AAD//j////4H+MDw///////8A/4AAP/8P////A/44Dh///////AP/gAA//w////wP/h4GH//////wAP+AAD//H///+B/+HwMP/////8AAH4AAP/4f///wP/8fwwf////+AAAPgAA//h///8D//x/hh////+AABgeAAD/8P///gf//D+DD///4AAH/g4AAP/w///8D//8P8EP//wAAP//BgAA/+H///g///4/4Yf/gAAH//+CAAD/4f//4H///j/wh+AAAB///8AAAP/D///A///+P/DAAAAAf///4AAA/8P//4H///4f+AAAGAP////wAAD/x//+B////h/gAAeAH////8AAAP+H//wP////HAAA8AB/////gAAA/4f/+B////8AADAAAf////4CAAD/j//gf///4AAGAAAf/////A4AAP8P/8D///wAAP4AA//////wHgAA/w//gf//AAAAAAA//////8B+AAD+H/4H//gAB4AAAAf////+AP4AAP4f/A//+AD/AAAAB/////AD/gAA/D/4H//4MAAABAAH////AA/+AAD8P+B///wAAAD/ww////gAf/4AAPx/wP///AAAP//DB///4AP//gAA+H+B////A////+OH//wAP//+AAD4fwf/////////4cP/AAH///4AAPD8D//////////hwcAAB////gAA8Pgf//////////HgAAD////+AADh8D//////////8AAAP/////4AAOHA///////////wAA///////gAA44H///////j///AAf//////+AADjA///////+Af/8Ph///////4AAOAP///////4AP/4/D///////gAAwB////////4AAPh8H//////+AADAP/////////AAGH4f//////4AAMD//////////wAEPw///////gAAgf///////////AA/D//////+AAAD////////////gAGH//////4AAA/////////////gAAf//////gAAH/////////////4AAAf////+AAD//////////////xgAAD////4AAP//////////////DwAAAf///gAA//////////////8PB+AB///+AAD//////////////44f/4H///4AAP//////////////jB///////gAA//////////////+AP//////+AAD//////////////4B///////4AAP//////////////wP///////gAA///////////////B///////+AAD//////////////+H///////4AAP//////////////4////////gAA</Sign>
	</response>
	</xmp>


	// Sample input from Club Speed
	POST /creditCard/charge HTTP/1.1
	Host: localhost:8000
	content-type: application/json
	Cache-Control: no-cache

	{
	    "provider": {
	        "type": "dejavoo-spin",
	        "options": {
	            "authorizationKey": "IpEJM96lMa",
	            "dejavooSpinRegisterId": "540048"
	        }
	    },
	    "order": {
	        "amount": 1.00,
	        "checkId": 123
	    },
	    "creditCard": null,
	    "prospect": null
	}
	*/

	var self = this;

    var referenceId  = this.opts.referenceId || order.checkId + '-' + Date.now();
	var qs = {
		'TerminalTransaction': '<request>' +
			'<PaymentType>Credit</PaymentType>' +
			'<TransType>Sale</TransType>' +
			'<Amount>' + convertToDecimal(order.amount) + '</Amount>' +
			'<Tip>0.00</Tip>' +
			'<Frequency>OneTime</Frequency>' +
			'<InvNum>' + order.checkId + '</InvNum>' + 
			'<RefId>' + referenceId + '</RefId>' +
			'<RegisterId>' + this.opts.dejavooSpinRegisterId + '</RegisterId>' +
			'<AuthKey>' + this.opts.authorizationKey + '</AuthKey>' +
			'<PrintReceipt>No</PrintReceipt>' +
			'</request>'
	};
	console.log('REQUEST', qs);

	return new Promise(function(resolve, reject) {
		request({ qs: qs }, function(err, response, body) {
			if(err) {
				console.log('CHARGE REQUEST ERROR', err, self.opts);
				var response = _.merge(self.opts.defaultHoistedVars, { message: 'Could not contact the Spin Service device at: ' + self.opts.spinApiUrl, error: err })
				return reject(_.merge({ result: response }, response));
			}
			if(self.opts.debug) console.log('RAW BODY', body);

			toJson(body, function (err, result) {
				if(self.opts.debug) console.log('PARSED BODY', JSON.stringify(result));
				if(err) {
					console.log('CHARGE PARSING ERROR', err);
					var response = _.merge(self.opts.defaultHoistedVars, { message: err.code, error: err })
					return reject(_.merge({ result: response }, response));
				}

				var isSuccessful = _.get(result, 'xmp.response[0].ResultCode[0]') == '0' || false;
				
				var extData = {};
				if(_.get(result, 'xmp.response[0].ExtData[0]')) {
					var extDataAsQuery = _.get(result, 'xmp.response[0].ExtData[0]').replace(/,/g, '&');
					extData = querystring.parse(extDataAsQuery);
				}

			    // Combine messages (if both exist)
				var message = "";
				if (_.get(result, 'xmp.response[0].RespMSG[0]') && _.get(result, 'xmp.response[0].Message[0]')) {
				    //MTM - Checking for Duplicate Reference Id to convert to a more user friendly message
				    if (_.get(result, 'xmp.response[0].RespMSG[0]') == "Duplicate%20Reference%20Id") {
				        message = "Duplicate Transaction was detected and was not charged again. Please contact the bank for details. " + _.get(result, 'xmp.response[0].Message[0]');
				    }
				    else {
				        message = _.get(result, 'xmp.response[0].Message[0]') + " " + _.get(result, 'xmp.response[0].RespMSG[0]');
				    }
				} else {
				    message = _.get(result, 'xmp.response[0].RespMSG[0]') || _.get(result, 'xmp.response[0].Message[0]') || null;
				}

				var hoistedVars = {
					transactionId: _.get(result, 'xmp.response[0].RefId[0]') || null,
                    resultCode: _.get(result, 'xmp.response[0].ResultCode[0]'),
					requestAmount: order.amount.toFixed(2) || null,
					authorizeAmount: extData.TotalAmt || null,
					creditCardAccount:  extData.AcntLast4 || null,
					creditCardType: extData.CardType || null,
					creditCardAuthorizationCode: _.get(result, 'xmp.response[0].AuthCode[0]') || null,
					creditCardExpirationDate: null,
					troutD: _.get(result, 'xmp.response[0].RefId[0]') || null,
					emvReceiptRequirement: _.get(result, 'xmp.response[0].EMVData[0]') || null,
					code: _.get(result, 'xmp.response[0].ResultCode[0]') || null,
                    message: message,
					_original: result
				};

				if(hoistedVars.message) hoistedVars.message = decodeURIComponent(hoistedVars.message);

				return resolve(_.merge(self.opts.defaultHoistedVars, { success: isSuccessful }, hoistedVars));

			});
		});
	});
}


Dejavoo.prototype.refundTransaction = function refundTransaction(order, creditCard, prospect, other) {
	/*
	// Request
	HTTPS://spinpos.net:443/spin/cgi.html?TerminalTransaction=<request><PaymentType>Credit</PaymentType><TransType>Return</TransType><Amount>1.00</Amount><Tip>0.00</Tip><Frequency>OneTime</Frequency><InvNum>123</InvNum><RefId>125</RefId><RegisterId>540048</RegisterId><AuthKey>IpEJM96lMa</AuthKey><PrintReceipt>No</PrintReceipt></request>


	// Success
	<xmp>
	<response>
	<RefId>125</RefId>
	<RegisterId>540048</RegisterId>
	<InvNum>123</InvNum>
	<ResultCode>0</ResultCode>
	<RespMSG>Approved%20Offline</RespMSG>
	<Message>Approved</Message>
	<PaymentType>Credit</PaymentType>
	<TransType>Return</TransType>
	<SN>000118110027051</SN>
	<ExtData>InvNum=123,CardType=AMEX,BatchNum=254,Tip=0.00,CashBack=0.00,Fee=0.00,AcntLast4=5005,Name=RATCLIFF%2fWESLY%20R%20%20%20%20%20%20%20%20%20%20,SVC=0.00,TotalAmt=1.00,DISC=0.00,Donation=0.00,SHFee=0.00,RwdPoints=0,RwdBalance=0,RwdIssued=,EBTFSLedgerBalance=,EBTFSAvailBalance=,EBTFSBeginBalance=,EBTCashLedgerBalance=,EBTCashAvailBalance=,EBTCashBeginBalance=,RewardCode=,AcqRefData=,ProcessData=,RefNo=,RewardQR=,Language=English,EntryType=CHIP,table_num=0,clerk_id=,ticket_num=,ControlNum=,TaxCity=0.00,TaxState=0.00</ExtData>
	<EMVData>AID=A000000025010801,AppName=AMERICAN EXPRESS,TVR=0000008000,TSI=E800</EMVData>
	<Sign>Qk1uAQAAAAAAAD4AAAAoAAAAFAAAAEwAAAABAAEAAAAAADABAADEDgAAxA4AAAAAAAAAAAAAAAAAAP///wAf//AAH//wAB//8AD4//AA+P/wAPj/8AD///AA///wAP//8AD///AA/8fwAP/H8AD/w/AA/8PwAP/j8AD/4/AA/+HwAP/h8AD/8fAA//HwAP/x8AD/8fAA//HwAP/x8AD/8fAA//HwAP/h8AD/4fAA/+PwAP/j8AD/4/AA/+PwAP/j8AD/w/AA/wPwAP4D8AD+D/AA/h/wAP8P8AD/D/AA/4fwAP+H8AD/w/AA/8PwAP/h8AD/4fAA//HwAP/x8AD/8PAA//DwAP/4cAD/+HAA//wwAP/8MAD//hAA//4QAP//EAD//xAA//8AAP//AAD//4AA//+AAP//gAD//4AA//+AAP//gAD//4AA//+AAP//gAD//4AA//+AAP//gAD//4AA//+AAP//gAD//4AA</Sign>
	</response>
	</xmp>


	// Failure
	<xmp>
	<response>
	<RefId>127</RefId>
	<RegisterId>540048</RegisterId>
	<InvNum>123</InvNum>
	<ResultCode>1</ResultCode>
	<Message>Canceled</Message>
	<PaymentType>Credit</PaymentType>
	<TransType>Return</TransType>
	<SN>000118110027051</SN>
	<ExtData>InvNum=123,CardType=VISA,BatchNum=254,Tip=0.00,CashBack=0.00,Fee=0.00,AcntLast4=5667,Name=RATCLIFF%20JR%2fWESLY%20R,SVC=0.00,TotalAmt=1.00,DISC=0.00,Donation=0.00,SHFee=0.00,RwdPoints=0,RwdBalance=0,RwdIssued=,EBTFSLedgerBalance=,EBTFSAvailBalance=,EBTFSBeginBalance=,EBTCashLedgerBalance=,EBTCashAvailBalance=,EBTCashBeginBalance=,RewardCode=,AcqRefData=,ProcessData=,RefNo=,RewardQR=,Language=English,EntryType=CHIP,table_num=0,clerk_id=,ticket_num=,ControlNum=,TaxCity=0.00,TaxState=0.00</ExtData>
	<EMVData>AID=,AppName=,TVR=,TSI=</EMVData>
	</response>
	</xmp>


	// Sample input from Club Speed
	POST /creditCard/refund HTTP/1.1
	Host: localhost:8000
	content-type: application/json
	Cache-Control: no-cache

	{
	    "provider": {
	        "type": "dejavoo-spin",
	        "options": {
	            "authorizationKey": "IpEJM96lMa",
	            "dejavooSpinRegisterId": "540048"
	        }
	    },
	    "order": {
	    "transactionId": "ABC-1469217692843",
	    "amount": 1.00,
	    "checkId": "123"
	    }
	}
	*/

	var self = this;

	var qs = {
		'TerminalTransaction': '<request>' +
			'<PaymentType>Credit</PaymentType>' +
			'<TransType>Return</TransType>' +
			'<Amount>' + convertToDecimal(order.amount) + '</Amount>' +
			'<Tip>0.00</Tip>' +
			'<Frequency>OneTime</Frequency>' +
			'<InvNum>' + order.checkId + '</InvNum>' +
			'<RefId>' + order.transactionId + '</RefId>' +
			'<RegisterId>' + this.opts.dejavooSpinRegisterId + '</RegisterId>' +
			'<AuthKey>' + this.opts.authorizationKey + '</AuthKey>' +
			'<PrintReceipt>No</PrintReceipt>' +
			'</request>'
	}
	console.log('REQUEST', qs);

	return new Promise(function(resolve, reject) {
		request({ qs: qs }, function(err, response, body) {
			if(err) {
				console.log('REFUND REQUEST ERROR', err, self.opts);
				var response = _.merge(self.opts.defaultHoistedVars, { message: 'Could not contact the Spin Service device at: ' + self.opts.spinApiUrl, error: err })
				return reject(_.merge({ result: response }, response));
			}
			if(self.opts.debug) console.log('RAW BODY', body);

			toJson(body, function (err, result) {
				if(self.opts.debug) console.log('PARSED BODY', JSON.stringify(result));
				if(err) {
					console.log('VOID PARSING ERROR', err);
					var response = _.merge(self.opts.defaultHoistedVars, { message: err.code, error: err })
					return reject(_.merge({ result: response }, response));
				}

				var isSuccessful = _.get(result, 'xmp.response[0].ResultCode[0]') == '0' || false;
				
				var extData = {};
				if(_.get(result, 'xmp.response[0].ExtData[0]')) {
					extData = querystring.parse(_.get(result, 'xmp.response[0].ExtData[0]'));
				}

				var hoistedVars = {
					transactionId: _.get(result, 'xmp.response[0].RefId[0]') || null,
                    resultCode: _.get(result, 'xmp.response[0].ResultCode[0]'),
					requestAmount: order.amount.toFixed(2) || null,
					authorizeAmount: order.amount.toFixed(2) || null,
					creditCardAccount:  extData.AcntLast4 || null,
					creditCardType: extData.CardType || null,
					creditCardAuthorizationCode: _.get(result, 'xmp.response[0].AuthCode[0]') || null,
					creditCardExpirationDate: null,
					troutD: _.get(result, 'xmp.response[0].RefId[0]') || null,
					emvReceiptRequirement: _.get(result, 'xmp.response[0].EMVData[0]') || null,
					code: _.get(result, 'xmp.response[0].ResultCode[0]') || null,
                    message: _.get(result, 'xmp.response[0].RespMSG[0]') || _.get(result, 'xmp.response[0].Message[0]') || null,
					_original: result
				};

				if(hoistedVars.message) hoistedVars.message = decodeURIComponent(hoistedVars.message);

				return resolve(_.merge(self.opts.defaultHoistedVars, { success: isSuccessful }, hoistedVars));

			});
		});
	});
}

Dejavoo.prototype.voidTransaction = function voidTransaction(order, creditCard, prospect, other) {
	/*
	// Request
	HTTPS://spinpos.net:443/spin/cgi.html?TerminalTransaction=<request><PaymentType>Credit</PaymentType><TransType>Void</TransType><Amount>1.00</Amount><Tip>0.00</Tip><Frequency>OneTime</Frequency><InvNum>123</InvNum><RefId>9</RefId><RegisterId>540048</RegisterId><AuthKey>IpEJM96lMa</AuthKey><PrintReceipt>No</PrintReceipt></request>


	// Approval
	<xmp>
	<response>
	<RefId>123-1469215334304</RefId>
	<RegisterId>540048</RegisterId>
	<InvNum>123</InvNum>
	<ResultCode>0</ResultCode>
	<RespMSG>Approved%20Offline</RespMSG>
	<Message>Approved</Message>
	<PaymentType>Credit</PaymentType>
	<TransType>Void Sale</TransType>
	<SN>000118110027051</SN>
	<ExtData>InvNum=123,CardType=AMEX,BatchNum=254,Tip=0.00,CashBack=0.00,Fee=0.00,AcntLast4=5005,Name=,SVC=0.00,TotalAmt=1.00,DISC=0.00,Donation=0.00,SHFee=0.00,RwdPoints=0,RwdBalance=0,RwdIssued=,EBTFSLedgerBalance=,EBTFSAvailBalance=,EBTFSBeginBalance=,EBTCashLedgerBalance=,EBTCashAvailBalance=,EBTCashBeginBalance=,RewardCode=,AcqRefData=,ProcessData=,RefNo=,RewardQR=,Language=English,EntryType=Manual,table_num=0,clerk_id=,ticket_num=,ControlNum=,TaxCity=0.00,TaxState=0.00</ExtData>
	<Sign>Qk0OBgAAAAAAAD4AAAAoAAAAewAAAF0AAAABAAEAAAAAANAFAADEDgAAxA4AAAAAAAAAAAAAAAAAAP///wAf///////////////////gD///////////////////4AP//////////////////+CD///////////////////g4///////////////////4P///////////////////+D////////////////////g////////////////////4P///////////////////+D////////////////////g////////////////////4P///////////////////+D////////////////////g////////////////////4P///////////////////+D////////////////////g///////j////////////4P//////4f///////////+D//////+D////////////g///////w////////////4P//////+Hw//////////+D///////hwP//////////g///////8AD//////////4P///////AH//////////+D///////4P/////x/////g///////+H/////8f////4P//////////////H////+D//////////////x/////g//////////////8P////4P//////////////D////+D//////////////4/////g//////////////+P////4P//////////////j////+D//////////////4/////g//////////////+P////4P//////////////j////+D//////////////4/////g//////////////+P////4P//////////////j////+D//////////////4/////g//////////////+P////4P//////////////j////+D//////////////4/////g//////////////+P////4P//////////////j////+D//////////////4/////g//////////////+P////4P//////////////j////+D//////////////4/////g//////////////+P////4P//////////////j////+D//////////////4/////g//////////////+P////4P//////////////j////+D//////////////4/////g//////////////+P////4P//////////////j////+D////////////////////g////////////////////4P///////////////////+D////////////////////g////////////////////4P///////////////////+D////////////////////g////////////////////4P///////////////////+D////////////////////g////////////////////4P///////////////////+D////////////////////g////////////////////4P///////////////////+D////////////////////g////////////////////4P///////////////////+D////////////////////g////////////////////4P///////////////////+D////////////////////g////////////////////4P///////////////////+D////////////////////g///////////////////+AP//////////////////+AD///////////////////AA///////////////////AYP//////////////////AeD//////////////////gPg//////////////////gP4P////////////////+AP+D/////////////////AH/g/////////////////wH/4P////////////////8f/+A=</Sign>
	</response>
	</xmp>


	// Failure
	<xmp>
	<response>
	<RefId>9</RefId>
	<RegisterId>540048</RegisterId>
	<InvNum>123</InvNum>
	<ResultCode>1</ResultCode>
	<RespMSG>Transaction%20not%20found</RespMSG>
	<Message>Canceled</Message>
	<PaymentType>Credit</PaymentType>
	<TransType>Void Sale</TransType>
	<SN>000118110027051</SN>
	<ExtData>InvNum=123,CardType=AMEX,BatchNum=254,Tip=0.00,CashBack=0.00,Fee=0.00,AcntLast4=5005,Name=,SVC=0.00,TotalAmt=1.00,DISC=0.00,Donation=0.00,SHFee=0.00,RwdPoints=0,RwdBalance=0,RwdIssued=,EBTFSLedgerBalance=,EBTFSAvailBalance=,EBTFSBeginBalance=,EBTCashLedgerBalance=,EBTCashAvailBalance=,EBTCashBeginBalance=,RewardCode=,AcqRefData=,ProcessData=,RefNo=,RewardQR=,Language=English,EntryType=Manual,table_num=0,clerk_id=,ticket_num=,ControlNum=,TaxCity=0.00,TaxState=0.00</ExtData>
	</response>
	</xmp>


	// Sample input from Club Speed
	POST /creditCard/void HTTP/1.1
	Host: localhost:8000
	content-type: application/json
	Cache-Control: no-cache

	{
	    "provider": {
	        "type": "dejavoo-spin",
	        "options": {
	            "authorizationKey": "IpEJM96lMa",
	            "dejavooSpinRegisterId": "540048"
	        }
	    },
	    "order": {
	    "transactionId": "ABC-1469217692843",
	    "amount": 1.00,
	    "checkId": "123"
	    }
	}
	*/

	var self = this;

	var qs = {
		'TerminalTransaction': '<request>' +
			'<PaymentType>Credit</PaymentType>' +
			'<TransType>Void</TransType>' +
			'<Amount>' + order.amount + '</Amount>' +
			'<Tip>0.00</Tip>' +
			'<Frequency>OneTime</Frequency>' +
			'<InvNum>' + order.checkId + '</InvNum>' +
			'<RefId>' + order.transactionId + '</RefId>' +
			'<RegisterId>' + this.opts.dejavooSpinRegisterId + '</RegisterId>' +
			'<AuthKey>' + this.opts.authorizationKey + '</AuthKey>' +
			'<PrintReceipt>No</PrintReceipt>' +
			'</request>'
	}
	console.log('REQUEST', qs);

	return new Promise(function(resolve, reject) {
		request({ qs: qs }, function(err, response, body) {
			if(err) {
				console.log('VOID REQUEST ERROR', err, self.opts);
				var response = _.merge(self.opts.defaultHoistedVars, { message: 'Could not contact the Spin Service device at: ' + self.opts.spinApiUrl, error: err })
				return reject(_.merge({ result: response }, response));
			}
			if(self.opts.debug) console.log('RAW BODY', body);

			toJson(body, function (err, result) {
				if(self.opts.debug) console.log('PARSED BODY', JSON.stringify(result));
				if(err) {
					console.log('VOID PARSING ERROR', err);
					var response = _.merge(self.opts.defaultHoistedVars, { message: err.code, error: err })
					return reject(_.merge({ result: response }, response));
				}

				var isSuccessful = _.get(result, 'xmp.response[0].ResultCode[0]') == '0' || false;
				
				var extData = {};
				if(_.get(result, 'xmp.response[0].ExtData[0]')) {
					extData = querystring.parse(_.get(result, 'xmp.response[0].ExtData[0]'));
				}

				var hoistedVars = {
					transactionId: _.get(result, 'xmp.response[0].RefId[0]') || null,
                    resultCode: _.get(result, 'xmp.response[0].ResultCode[0]'),
					requestAmount: order.amount.toFixed(2) || null,
					authorizeAmount: order.amount.toFixed(2) || null,
					creditCardAccount:  extData.AcntLast4 || null,
					creditCardType: extData.CardType || null,
					creditCardAuthorizationCode: _.get(result, 'xmp.response[0].AuthCode[0]') || null,
					creditCardExpirationDate: null,
					troutD: _.get(result, 'xmp.response[0].RefId[0]') || null,
					emvReceiptRequirement: _.get(result, 'xmp.response[0].EMVData[0]') || null,
					code: _.get(result, 'xmp.response[0].ResultCode[0]') || null,
                    message: _.get(result, 'xmp.response[0].RespMSG[0]') || _.get(result, 'xmp.response[0].Message[0]') || null,
					_original: result
				};

				if(hoistedVars.message) hoistedVars.message = decodeURIComponent(hoistedVars.message);

				return resolve(_.merge(self.opts.defaultHoistedVars, { success: isSuccessful }, hoistedVars));

			});
		});
	});
}

function convertToDecimal(amount) {
	return parseFloat(Math.round(amount * 100) / 100).toFixed(2);
}

module.exports = Dejavoo;