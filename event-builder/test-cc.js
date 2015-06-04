var creditCard = require('./lib/creditCard.js');

var request = {
	"_name": "Default",
	"provider": {
		"type": "authorize.net",
		"options": {
			// TEST
			"apiLoginId": "6nG2tqS43e",
			"transactionKey": "673mTX96cTtG9wzq", //q
			"testMode": true
		}
	},
	"order": {
		"amount": "1.00"
	},
	"creditCard": {
		//"track1": "B4111111111111111^CardUser/John^181210100000019301000000877000000",
		//"track2": "4000000000000002=1312101193010877",
		"creditCardNumber": "4222222222222",
		"expirationMonth":"3",
		"expirationYear":"2017",
		"cvv":"123"
 	},
 "prospect": {
		"customerFirstName": "Ellen",
		"customerLastName": "Johson",
		"billingAddress": "14 Main Street",
		"billingCity": "Pecan Springs",
		"billingZip": "12312", //46282,
		"billingState": "TX",
		"billingCountry": "USA",
		"shippingFirstName": "China",
		"shippingLastName": "Bayles",
		"shippingCity": "Pecan Springs",
		"shippingZip": "44628",
		"shippingCountry": "USA"
	}
};

/*
TEST SCENARIOS
http://developer.authorize.net/tools/errorgenerationguide/
- Bad API Key (change login id or key to invalid)
- Valid Card (use this for track 1: B4111111111111111^CardUser/John^181210100000019301000000877000000)
- Expired Card (Use this: B4111111111111111^CardUser/John^131210100000019301000000877000000);
- Declined Card (Submit zip code of 46282)
- Duplicate transaction (Submit two approved transactions in rapid succession)
*/

//var error = {"ErrorResponse":{"$":{"xmlns:xsi":"http://www.w3.org/2001/XMLSchema-instance","xmlns:xsd":"http://www.w3.org/2001/XMLSchema","xmlns":"AnetApi/xml/v1/schema/AnetApiSchema.xsd"},"messages":[{"resultCode":["Error"],"message":[{"code":["E00003"],"text":["The 'AnetApi/xml/v1/schema/AnetApiSchema.xsd:cardCode' element is invalid - The value '' is invalid according to its datatype 'AnetApi/xml/v1/schema/AnetApiSchema.xsd:cardCode' - The Pattern constraint failed."]}]}]}};

creditCard.charge(request, function(err, result) {
	console.log('\n\nCHARGE Transaction Request', request, '\n\nERROR:', err, '\n\nRESULT', JSON.stringify(result));
});