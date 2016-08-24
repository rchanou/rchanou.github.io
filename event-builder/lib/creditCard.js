var AuthorizeNet = require('../lib/authorize-net');
var Ingenico = require('../lib/ingenico');
var Dejavoo = require('../lib/dejavoo');

// clubspeed2008 / Karm@2006
// API Login ID: 6nG2tqS43e
// Transaction Key: 673mTX96cTtG9wzq
// Secret Question: Simon
// https://test.authorize.net/gateway/transact.dll

/* 
Gives expired:
https://test.authorize.net/gateway/transact.dll?x_cpversion=1.0&x_market_type=2&x_device_type=&x_type=AUTH_CAPTURE&x_amount=0.10&x_track1=B4000000000000002^CardUser/John^131210100000019301000000877000000&x_login=6nG2tqS43e&x_tran_key=673mTX96cTtG9wzq&x_first_name=&x_last_name=&x_description=&x_test_request=true&x_invoice_num=&x_encap_char=$&x_response_format=1&x_delim_char=,&x_currency_code=USD

 Example
track 1
B4000000000000002^CardUser/John^171210100000019301000000877000000?

track 2
4000000000000002=1712101193010877?
*/

/*var opts = {
	"provider": {
		"type": "authorize.net",
		"options": {
			"apiLoginId": "6nG2tqS43e",
			"transactionKey": "673mTX96cTtG9wzq",
			"testMode": true
		}
	},
	"order": {
		"amount": "5.00",
		"checkId": "123"
	},
	"creditCard": {
		//"track1": "B4000000000000002^CardUser/John^181210100000019301000000877000000",
		//"track2": "4000000000000002=1812101193010877",
		"creditCardNumber": "4012888818888",
		"expirationMonth":"1",
		"expirationYear":"2017",
		"cvv":"666"
 	},
	"prospect": {
		"customerFirstName": "Ellen",
		"customerLastName": "Johson",
		"billingAddress": "14 Main Street",
		"billingCity": "Pecan Springs",
		"billingZip": "44628",
		"billingState": "TX",
		"billingCountry": "USA",
		"shippingFirstName": "China",
		"shippingLastName": "Bayles",
		"shippingCity": "Pecan Springs",
		"shippingZip": "44628",
		"shippingCountry": "USA"
	}
};
var AuthorizeNet = require('../lib/authorize-net');
var provider = new AuthorizeNet({
	API_LOGIN_ID: opts.provider.options.apiLoginId,
	TRANSACTION_KEY: opts.provider.options.transactionKey,
	testMode: opts.provider.options.testMode
});
provider.submitTransaction(opts.order, opts.creditCard, opts.prospect).then(function(response) {
	console.log(response);
}).catch(function(err) {
	console.log(err);
});*/

/*var opts = {
	"provider": {
		"type": "authorize.net",
		"options": {
			"apiLoginId": "6nG2tqS43e",
			"transactionKey": "673mTX96cTtG9wzq",
			"testMode": true
		}
	},
	"transactionId": "2233286515",
	"creditCardNumber": "4012888818888",
	"expirationMonth": "01",
	"expirationYear": "17",
	"amount": 2.00
};

creditCard.refund(opts, function(err, res) {
	console.log(err, res);
});*/

var getProvider = function getProvider(opts) {
	if(!opts.provider || !opts.provider.type) return new Error('No provider given!');
	
	switch(opts.provider.type) {
		case 'authorize.net':
			return new AuthorizeNet({
				API_LOGIN_ID: opts.provider.options.apiLoginId,
				TRANSACTION_KEY: opts.provider.options.transactionKey,
				testMode: opts.provider.options.testMode
			});
			break;
		case 'ingenico':
			return new Ingenico(opts);
			break;
		case 'dejavoo-spin':
			return new Dejavoo(opts);
			break;
		default:
			return new Error('Unsupported provider: ' + opts.provider.type);
	}
}

exports.signature = function(opts, callback) {
	if(opts.provider.type == 'authorize.net')      return callback(new Error('Signatures not supported'));

	var provider = getProvider(opts); // TODO Handle error status
	
	provider.signature(opts.signature).then(function(response) {
		callback(null, response);
	}).catch(function(err) {
		callback(err);
	});
};

exports.charge = function(opts, callback) {
	if(opts.provider.type == 'authorize.net' && !opts.order)      return callback(new Error('No order given!'));
	if(opts.provider.type == 'authorize.net' && !opts.creditCard) return callback(new Error('No creditCard given!'));
	if(opts.provider.type == 'authorize.net' && !opts.prospect)   return callback(new Error('No prospect given!'));

	var provider = getProvider(opts); // TODO Handle error status
	
	provider.submitTransaction(opts.order, opts.creditCard, opts.prospect, opts.other).then(function(response) {
		callback(null, response);
	}).catch(function(err) {
		callback(err);
	});
};

exports.refund = function(opts, callback) {
	if(opts.provider.type == 'authorize.net' && !opts.transactionId)    return callback(new Error('No transactionId given!'));
	if(opts.provider.type == 'authorize.net' && !opts.creditCardNumber) return callback(new Error('No creditCardNumber given!'));
	if(opts.provider.type == 'authorize.net' && !opts.expirationMonth)  return callback(new Error('No expirationMonth given!'));
	if(opts.provider.type == 'authorize.net' && !opts.expirationYear)   return callback(new Error('No expirationYear given!'));
	if(opts.provider.type == 'authorize.net' && !opts.amount)           return callback(new Error('No amount given!'));

	var provider = getProvider(opts); // TODO Handle error status
	
	// This is a kludge... refactor (the above too)
	if(opts.provider.type == 'authorize.net') {
		provider.refundTransaction(opts.transactionId, {
			creditCardNumber: opts.creditCardNumber,
			expirationMonth: opts.expirationMonth,
			expirationYear: opts.expirationYear,
			amount: opts.amount
			})
		.then(function(response) {
			callback(null, response);
		}).catch(function(err) {
			callback(err);
		});
	} else {
		provider.refundTransaction(opts.order, opts.creditCard, opts.prospect, opts.other)
		.then(function(response) {
			callback(null, response);
		}).catch(function(err) {
			callback(err);
		});
	}
};

exports.void = function(opts, callback) {
	if(opts.provider.type == 'authorize.net' && !opts.transactionId) return callback(new Error('No transactionId given!'));

	var provider = getProvider(opts); // TODO Handle error status

	if(opts.provider.type == 'dejavoo-spin') {
		provider.voidTransaction(opts.order, opts.creditCard, opts.prospect, opts.other).then(function(response) {
			return callback(null, response);
		}).catch(function(err) {
			return callback(err);
		});
	} else {
		provider.voidTransaction(opts.transactionId).then(function(response) {
			return callback(null, response);
		}).catch(function(err) {
			return callback(err);
		});
	}
	
};

exports.tokenize = function(opts, callback) {
	if(opts.provider.type == 'authorize.net' && !opts.transactionId) return callback(new Error('No transactionId given!'));

	var provider = getProvider(opts); // TODO Handle error status
	
	provider.tokenize(opts.transactionId).then(function(response) {
		callback(null, response);
	}).catch(function(err) {
		callback(err);
	});
};