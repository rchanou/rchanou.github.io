/*
INSTALL (very similar to Facebook app setup)
- Copy config.orig.json to config.json
- In newly-created config.json, put in two Auth.net keys and Club Speed api private key (found in C:\ClubSpeedApps\api/config.php)
- Go to command prompt and run "npm install" followed by "node app.js" (should get an error EADDRINUSE indicating that something is already using the IP address/port -- it's PCCharge)
- Stop (and disable) PCCharge in FireDaemon
- Install FireDaemon Service by dragging over XML. Serivce should start, verify that you see it running in Interactive Services

TESTING
- At this point you may run a test transaction by changing the config's testMode to "true" (no quotes) and restarting the service. Be sure to turn test mode back off so that transactions process successfully.
Test card is 4111111111111111 with a valid expiration and three digit CVV.
- The Interactive Services will show quite a bit of logging
*/

// Think about handling the "force" option...

var net = require('net');
var _ = require('lodash');
var request = require('request');
var async = require('async');
var parseString = require('xml2js').parseString;
var jsonfile = require('jsonfile');
var AuthNetRequest = require('auth-net-request');

// Default config (overridded by config.json)
var configFile = './config.json'
var config = {
  port: 31419,
  apiHost: '127.0.0.1',
  apiKey: null,
  AUTH_NET_API_LOGIN_ID: null,
  AUTH_NET_TRANSACTION_KEY: null,
  logging: false,
  testMode: false
};

// Load the config
try {
  var loadedConfig = jsonfile.readFileSync(configFile, { throws: true });
  config = _.defaults(loadedConfig, config);
} catch(e) {
  console.log('ERROR LOADING config.json -- does it exist and did you run "npm install"?', e)
}

console.log('Running CS-PCCharge Server with configuration:', config)

if(!config.AUTH_NET_API_LOGIN_ID)
  console.log('Invalid Authorize.net api login id given. Please set in config.json file. Value currently is: ' + config.AUTH_NET_API_LOGIN_ID)

if(!config.AUTH_NET_TRANSACTION_KEY)
  console.log('Invalid Authorize.net transaction key given. Please set in config.json file. Value currently is: ' + config.AUTH_NET_TRANSACTION_KEY)

if(!config.apiKey)
  console.log('Invalid Club Speed API Key given. Please set in config.json file. Value currently is: ' + config.apiKey)

var AuthNet = new AuthNetRequest({
  api: config.AUTH_NET_API_LOGIN_ID,
  key: config.AUTH_NET_TRANSACTION_KEY,
  //cert: '/path/to/cert.pem',
  //rejectUnauthorized: false, // true
  //requestCert: true, // false
  //agent: false // http.agent object
  sandbox: config.testMode
});

//var failureRequest = '<XML_REQUEST><USER_ID>User1</USER_ID><COMMAND>1</COMMAND><PROCESSOR_ID>CES</PROCESSOR_ID><MERCH_NUM>426193590996159873</MERCH_NUM><ACCT_NUM>440066******4242</ACCT_NUM><EXP_DATE>****</EXP_DATE><MANUAL_FLAG>1</MANUAL_FLAG><TRANS_AMOUNT>55.00</TRANS_AMOUNT><TRACK_DATA>*</TRACK_DATA><TICKET_NUM>40000783732</TICKET_NUM><CARDHOLDER>LARGENT/SARAH M</CARDHOLDER><MULTI_FLAG>1</MULTI_FLAG><PRESENT_FLAG>3</PRESENT_FLAG></XML_REQUEST>';
//var failureResponse = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD>273963</TROUTD><RESULT>Error</RESULT><AUTH_CODE>Invalid Track</AUTH_CODE><REFERENCE>Invalid Track</REFERENCE><INTRN_SEQ_NUM>273963</INTRN_SEQ_NUM></XML_REQUEST>';

var failureResponseTemplate = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD>##TROUTD##</TROUTD><RESULT>Error</RESULT><AUTH_CODE>Invalid XML</AUTH_CODE><REFERENCE>##REFERENCE##</REFERENCE><INTRN_SEQ_NUM>##INTRN_SEQ_NUM##</INTRN_SEQ_NUM></XML_REQUEST>';

function processRequest(incomingData, cb) {
    parseString(incomingData, function (err, parsedXml) {
        var seqNum = Date.now();

        if(err) {
          console.log('XML PARSING ERROR:', err);
          var formattedErrorMessage = '';
          try { formattedErrorMessage = err.toString().split('\n')[0]; } catch(e) {}
          
          var failureMessage = failureResponseTemplate
            .replace('##TROUTD##', seqNum)
            .replace('##INTRN_SEQ_NUM##', seqNum)
            .replace('##REFERENCE##', formattedErrorMessage);

          return cb(null, failureMessage);
        }

        console.log('Parsed XML:', parsedXml)

        var command = _.has(parsedXml, 'XML_REQUEST.COMMAND.0') ? parsedXml.XML_REQUEST.COMMAND[0] : null;

        switch(command) {
          case '1': // CHARGE -- CAPTURED/APPROVED = Success; Anything else is a failure
          case '8':
            processCharge(parsedXml, function(response) {
              return cb(null, response);
            });
            break;
          case '2': // REFUND -- PROCESSED/CAPTURED/APPROVED = Success; Anything else is a failure
            processRefund(parsedXml, function(response) {
              return cb(null, response);
            });
            break;
          case '3': // VOID -- CAPTURED/VOIDED = Success; Anything else is a failure
          case '6':
            processVoid(parsedXml, function(response) {
              return cb(null, response);
            });
            break;
          default:
            var formattedErrorMessage = 'Invalid command given: ' + command;
            var response = failureResponseTemplate.replace('##TROUTD##', seqNum).replace('##INTRN_SEQ_NUM##', seqNum).replace('##REFERENCE##', formattedErrorMessage);
            return cb(null, response);
        }
        
    });
}

function processCharge(xml, cb) { // CHARGE -- CAPTURED/APPROVED = Success; Anything else is a failure
  /*
  var successfulRequest = '<XML_REQUEST><USER_ID>User1</USER_ID><COMMAND>1</COMMAND><PROCESSOR_ID>FDCN</PROCESSOR_ID><MERCH_NUM>000000000000000000</MERCH_NUM><ACCT_NUM>4111111111111111</ACCT_NUM><EXP_DATE>1017</EXP_DATE><MANUAL_FLAG>0</MANUAL_FLAG><TRANS_AMOUNT>15.00</TRANS_AMOUNT><TICKET_NUM>1862</TICKET_NUM><CARDHOLDER>test</CARDHOLDER><CVV2>666</CVV2><MULTI_FLAG>1</MULTI_FLAG><STREET>test</STREET><ZIP_CODE>30115</ZIP_CODE><PRESENT_FLAG>2</PRESENT_FLAG></XML_REQUEST>';
  var failingRequest = '<XML_REQUEST><USER_ID>User1</USER_ID><COMMAND>1</COMMAND><PROCESSOR_ID>FDCN</PROCESSOR_ID><MERCH_NUM>000000000000000000</MERCH_NUM><ACCT_NUM>4111111111111111</ACCT_NUM><EXP_DATE>1017</EXP_DATE><MANUAL_FLAG>0</MANUAL_FLAG><TRANS_AMOUNT>6.66</TRANS_AMOUNT><TICKET_NUM>1862</TICKET_NUM><CARDHOLDER>test</CARDHOLDER><CVV2>666</CVV2><MULTI_FLAG>1</MULTI_FLAG><STREET>test</STREET><ZIP_CODE>30115</ZIP_CODE><PRESENT_FLAG>2</PRESENT_FLAG></XML_REQUEST>';
  var successfulResponse = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD>273987</TROUTD><RESULT>CAPTURED</RESULT><AUTH_CODE>02574D</AUTH_CODE><TRANS_DATE>060916</TRANS_DATE><TICKET>862649</TICKET><INTRN_SEQ_NUM>273987</INTRN_SEQ_NUM><TRANS_ID>586161682726626</TRANS_ID><TICODE>ZCSH</TICODE><RET>A014</RET><ACI>E</ACI><CMRCL_TYPE>N</CMRCL_TYPE><PURCH_CARD_TYPE>0</PURCH_CARD_TYPE></XML_REQUEST>'
  var declinedRequest = '<XML_REQUEST><USER_ID>User1</USER_ID><COMMAND>1</COMMAND><PROCESSOR_ID>CES</PROCESSOR_ID><MERCH_NUM>426193590996159873</MERCH_NUM><ACCT_NUM>436618******9972</ACCT_NUM><EXP_DATE>****</EXP_DATE><MANUAL_FLAG>0</MANUAL_FLAG><TRANS_AMOUNT>26.26</TRANS_AMOUNT><TICKET_NUM>861239</TICKET_NUM><MULTI_FLAG>1</MULTI_FLAG><PRESENT_FLAG>1</PRESENT_FLAG></XML_REQUEST>'
  var declinedResponse = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD>273569</TROUTD><RESULT>NOT CAPTURED</RESULT><AUTH_CODE>DECLINED</AUTH_CODE><TICKET>861239</TICKET><INTRN_SEQ_NUM>273569</INTRN_SEQ_NUM><CMRCL_TYPE>N</CMRCL_TYPE><PURCH_CARD_TYPE>0</PURCH_CARD_TYPE></XML_REQUEST>'
  */

  var successfulResponseTemplate = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD>##TROUTD##</TROUTD><RESULT>CAPTURED</RESULT><AUTH_CODE>##AUTH_CODE##</AUTH_CODE><TRANS_DATE></TRANS_DATE><TICKET>##TICKET##</TICKET><INTRN_SEQ_NUM>##INTRN_SEQ_NUM##</INTRN_SEQ_NUM><TRANS_ID>##TRANS_ID##</TRANS_ID><TICODE></TICODE><RET></RET><ACI></ACI><CMRCL_TYPE></CMRCL_TYPE><PURCH_CARD_TYPE></PURCH_CARD_TYPE></XML_REQUEST>'
  var declinedResponseTemplate   = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD></TROUTD><RESULT>NOT CAPTURED</RESULT><AUTH_CODE>##AUTH_CODE##</AUTH_CODE><TICKET>##TICKET##</TICKET><INTRN_SEQ_NUM>##INTRN_SEQ_NUM##</INTRN_SEQ_NUM><CMRCL_TYPE></CMRCL_TYPE><PURCH_CARD_TYPE></PURCH_CARD_TYPE></XML_REQUEST>'

  var transaction = {
    "refId": xml.XML_REQUEST.TICKET_NUM[0],
    "transactionRequest": {
        "transactionType": "authCaptureTransaction",
        "amount": xml.XML_REQUEST.TRANS_AMOUNT[0],
        "payment": {
            "creditCard": {
                "cardNumber": xml.XML_REQUEST.ACCT_NUM[0],
                "expirationDate": xml.XML_REQUEST.EXP_DATE[0]
            }
        },
        "order": {
          "invoiceNumber": xml.XML_REQUEST.TICKET_NUM[0],
        }
    }
  };

  // Inject CVV, if provided
  if(_.has(xml, 'XML_REQUEST.CVV2.0')) transaction.transactionRequest.payment.creditCard.cardCode = xml.XML_REQUEST.CVV2[0];

  // Inject "billTo", if provided
  if(_.has(xml, 'XML_REQUEST.STREET.0') || _.has(xml, 'XML_REQUEST.ZIP_CODE.0')) {
    transaction.transactionRequest.billTo = {};
    if(_.has(xml, 'XML_REQUEST.STREET.0')) transaction.transactionRequest.billTo.address = xml.XML_REQUEST.STREET[0];
    if(_.has(xml, 'XML_REQUEST.ZIP_CODE.0')) transaction.transactionRequest.billTo.zip = xml.XML_REQUEST.ZIP_CODE[0];
  }

  AuthNet.send('createTransaction', transaction, function(err, response) {
    console.log('\n\n\nAUTHNET PARAMS:', JSON.stringify(transaction, null, 4), '\n\n\nAUTHNET ERROR:', JSON.stringify(err, null, 4), '\n\n\nAUTHNET RESPONSE:', JSON.stringify(response, null, 4));

    if(err) {
      var errMsg = _.has(err, 'response.transactionResponse.errors.error.errorText') ? err.response.transactionResponse.errors.error.errorText : err.toString();

      pcchargeResponse = declinedResponseTemplate
        .replace('##TICKET##', xml.XML_REQUEST.TICKET_NUM[0])
        .replace('##INTRN_SEQ_NUM##', Date.now())
        .replace('##AUTH_CODE##', errMsg);
    } else {
      pcchargeResponse = successfulResponseTemplate
        .replace('##TICKET##', xml.XML_REQUEST.TICKET_NUM[0])
        .replace('##TROUTD##', response.transactionResponse.transId)
        .replace('##INTRN_SEQ_NUM##', Date.now())
        .replace('##TRANS_ID##', response.transactionResponse.transId)
        .replace('##AUTH_CODE##', response.transactionResponse.authCode);
    }

    return cb(pcchargeResponse);
  });
  
}

function processRefund(xml, cb) { // REFUND -- PROCESSED/CAPTURED/APPROVED = Success; Anything else is a failure
  /*
  var refundRequest = '<XML_REQUEST><USER_ID>User1</USER_ID><COMMAND>2</COMMAND><PROCESSOR_ID>CES</PROCESSOR_ID><MERCH_NUM>426193590996159873</MERCH_NUM><ACCT_NUM>375151*****4242</ACCT_NUM><EXP_DATE>****</EXP_DATE><MANUAL_FLAG>0</MANUAL_FLAG><TRANS_AMOUNT>10.90</TRANS_AMOUNT><TICKET_NUM>64956</TICKET_NUM><CVV2>****</CVV2><MULTI_FLAG>1</MULTI_FLAG><PRESENT_FLAG>2</PRESENT_FLAG></XML_REQUEST>'
  var refundResponse = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD>273938</TROUTD><RESULT>PROCESSED</RESULT><TICKET>862426</TICKET><INTRN_SEQ_NUM>273938</INTRN_SEQ_NUM><PURCH_CARD_TYPE>0</PURCH_CARD_TYPE></XML_REQUEST>';
  var declinedResponse = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD>273569</TROUTD><RESULT>NOT CAPTURED</RESULT><AUTH_CODE>DECLINED</AUTH_CODE><TICKET>861239</TICKET><INTRN_SEQ_NUM>273569</INTRN_SEQ_NUM><CMRCL_TYPE>N</CMRCL_TYPE><PURCH_CARD_TYPE>0</PURCH_CARD_TYPE></XML_REQUEST>'
  */

  // Get payments for this check?
  var uri = 'http://' + config.apiHost + '/api/index.php/payments?key=' + config.apiKey + '&where={"checkId":"' + xml.XML_REQUEST.TICKET_NUM[0] + '"}';

  var declinedResponseTemplate = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD>##TROUTD##</TROUTD><RESULT>NOT CAPTURED</RESULT><AUTH_CODE>##AUTH_CODE##</AUTH_CODE><TICKET>##TICKET##</TICKET><INTRN_SEQ_NUM>##INTRN_SEQ_NUM##</INTRN_SEQ_NUM><CMRCL_TYPE></CMRCL_TYPE><PURCH_CARD_TYPE></PURCH_CARD_TYPE></XML_REQUEST>'

  request.get({ uri: uri, json: true }, function(error, response, body) {

    if(error)
      return cb(declinedResponseTemplate.replace('##TICKET##', xml.XML_REQUEST.TICKET_NUM[0]).replace('##TROUTD##', '').replace('##INTRN_SEQ_NUM##', Date.now()).replace('##AUTH_CODE##', error.toString()));

    if(response.statusCode !== 200)
      return cb(declinedResponseTemplate.replace('##TICKET##', xml.XML_REQUEST.TICKET_NUM[0]).replace('##TROUTD##', '').replace('##INTRN_SEQ_NUM##', Date.now()).replace('##AUTH_CODE##', JSON.stringify(body)));

    if (!Array.isArray(body))
      return cb(declinedResponseTemplate.replace('##TICKET##', xml.XML_REQUEST.TICKET_NUM[0]).replace('##TROUTD##', '').replace('##INTRN_SEQ_NUM##', Date.now()).replace('##AUTH_CODE##', 'Could not parse the response: ' + body.toString()));

    // Loop each payment, attempt refund. NOTE: The first to success bails on the loop.
    async.eachSeries(body, function(payment, authCb) {
      // Attempt the refund. If it succeeds, pass the result as an "error" to stop further processing attempts
      var opts = _.defaults({ xml: xml, payment: payment});
      authnetRefund(opts, function(err, result) {
        if(err) {
          console.log('Not successful in refunding against this payment:', err);
          authCb();
        } else {
          console.log('SUCCESS in refunding against this payment:', result);
          authCb(result);
        }
      });
    }, function(successfulRefund) {

      var response = null;

      if(!successfulRefund) {
        response = declinedResponseTemplate
          .replace('##AUTH_CODE##', 'No payment/transactionIds found that could be refunded against!')
          .replace('##TICKET##', xml.XML_REQUEST.TICKET_NUM[0])
          .replace('##TROUTD##', '')
          .replace('##INTRN_SEQ_NUM##', Date.now());
      } else {
        response = successfulRefund;
      }

      cb(response);
    });

  });
}

function authnetRefund(opts, cb) {
  var refundResponseTemplate = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD>##TROUTD##</TROUTD><RESULT>PROCESSED</RESULT><TICKET>##TICKET##</TICKET><INTRN_SEQ_NUM>##INTRN_SEQ_NUM##</INTRN_SEQ_NUM><PURCH_CARD_TYPE></PURCH_CARD_TYPE></XML_REQUEST>';

  var transaction = {
      "refId": opts.xml.XML_REQUEST.TICKET_NUM[0],
      "transactionRequest": {
          "transactionType": "refundTransaction",
          "amount": opts.xml.XML_REQUEST.TRANS_AMOUNT[0],
          "payment": {
              "creditCard": {
                  "cardNumber": opts.xml.XML_REQUEST.ACCT_NUM[0].substr(-4),
                  "expirationDate": "XXXX"
              }
          },
          "refTransId": opts.payment.troutd
      }
  }

  if(transaction.transactionRequest.refTransId === null) {
    return cb('Payment transaction is empty');
  }

  AuthNet.send('createTransaction', transaction, function(err, response) {
    console.log('\n\n\nAUTHNET PARAMS:', JSON.stringify(transaction, null, 4), '\n\n\nAUTHNET ERROR:', JSON.stringify(err, null, 4), '\n\n\nAUTHNET RESPONSE:', JSON.stringify(response, null, 4));

    if(err) {
      var errMsg = _.has(err, 'response.transactionResponse.errors.error.errorText') ? err.response.transactionResponse.errors.error.errorText : err.toString();

      return cb(errMsg);
    } else {
      var pcchargeResponse = refundResponseTemplate
        .replace('##TICKET##', opts.xml.XML_REQUEST.TICKET_NUM[0])
        .replace('##TROUTD##', response.transactionResponse.transId)
        .replace('##INTRN_SEQ_NUM##', Date.now());

      return cb(null, pcchargeResponse);
    }
    
  });

}

function processVoid(xml, cb) { // VOID -- CAPTURED/VOIDED = Success; Anything else is a failure

  /*
  var declinedResponse = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD>273569</TROUTD><RESULT>NOT CAPTURED</RESULT><AUTH_CODE>DECLINED</AUTH_CODE><TICKET>861239</TICKET><INTRN_SEQ_NUM>273569</INTRN_SEQ_NUM><CMRCL_TYPE>N</CMRCL_TYPE><PURCH_CARD_TYPE>0</PURCH_CARD_TYPE></XML_REQUEST>'
  var voidRequest = '<XML_REQUEST><USER_ID>User1</USER_ID><COMMAND>3</COMMAND><TROUTD>273987</TROUTD><PROCESSOR_ID>FDCN</PROCESSOR_ID><MERCH_NUM>000000000000000000</MERCH_NUM><MANUAL_FLAG>0</MANUAL_FLAG><TICKET_NUM>1862</TICKET_NUM><MULTI_FLAG>1</MULTI_FLAG></XML_REQUEST>'
  var voidResponse = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD>273430</TROUTD><RESULT>VOIDED</RESULT><TICKET>860999</TICKET><INTRN_SEQ_NUM>273431</INTRN_SEQ_NUM><PURCH_CARD_TYPE>0</PURCH_CARD_TYPE></XML_REQUEST>';
  */

  var voidResponseTemplate     = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD>##TROUTD##</TROUTD><RESULT>VOIDED</RESULT><TICKET>##TICKET##</TICKET><INTRN_SEQ_NUM>##INTRN_SEQ_NUM##</INTRN_SEQ_NUM><PURCH_CARD_TYPE></PURCH_CARD_TYPE></XML_REQUEST>';
  var declinedResponseTemplate = '<XML_REQUEST><USER_ID>User1</USER_ID><TROUTD>##TROUTD##</TROUTD><RESULT>NOT CAPTURED</RESULT><AUTH_CODE>##AUTH_CODE##</AUTH_CODE><TICKET>##TICKET##</TICKET><INTRN_SEQ_NUM>##INTRN_SEQ_NUM##</INTRN_SEQ_NUM><CMRCL_TYPE></CMRCL_TYPE><PURCH_CARD_TYPE></PURCH_CARD_TYPE></XML_REQUEST>'

  var transaction = {
    "refId": xml.XML_REQUEST.TICKET_NUM[0],
    "transactionRequest": {
        "transactionType": "voidTransaction",
        "refTransId": xml.XML_REQUEST.TROUTD[0]
    }
  };

  AuthNet.send('createTransaction', transaction, function(err, response) {
    console.log('\n\n\nAUTHNET PARAMS:', JSON.stringify(transaction, null, 4), '\n\n\nAUTHNET ERROR:', JSON.stringify(err, null, 4), '\n\n\nAUTHNET RESPONSE:', JSON.stringify(response, null, 4));

    if(err) {
      var errMsg = _.has(err, 'response.transactionResponse.errors.error.errorText') ? err.response.transactionResponse.errors.error.errorText : err.toString();

      pcchargeResponse = declinedResponseTemplate
        .replace('##TICKET##', xml.XML_REQUEST.TICKET_NUM[0])
        .replace('##TROUTD##', xml.XML_REQUEST.TROUTD[0])
        .replace('##INTRN_SEQ_NUM##', Date.now())
        .replace('##AUTH_CODE##', errMsg);
    } else {
      pcchargeResponse = voidResponseTemplate
        .replace('##TICKET##', xml.XML_REQUEST.TICKET_NUM[0])
        .replace('##TROUTD##', response.transactionResponse.transId)
        .replace('##INTRN_SEQ_NUM##', Date.now());
    }

    return cb(pcchargeResponse);
  });

}

var server = net.createServer(function(socket) {
  var self = socket;
  socket.on('data', function(data) {
    console.log('\n\n\n\n\nRECEIVED', data.toString());
    
    // Take in request, process and respond
    processRequest(data, function(err, response) {
      console.log('Responding with:', response);
      if(err) console.log('ERROR FOUND:', err);
      socket.end(response);
    });    

  }).on('close', function() {
    console.log('CLOSE from ' + socket.remoteAddress)
  }).on('error', function(err) {
    console.log('Caught Server Error', err);
  });
})

// Start the server!
//server.listen({ port: config.port }, function() {
server.listen(config.port, function() { // Older style command
  address = server.address();
  console.log('Opened CS-PCCharge Server on %j', address);
}).on('connection', function (socket) {
    console.log('CONNECT from ' + socket.remoteAddress);
}).on('error', function(err) {
    console.log('ERROR', err);
});