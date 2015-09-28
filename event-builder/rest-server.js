var restify  = require('restify')
  , receipt  = require('./lib/receipt.js')
  , gridding = require('./lib/gridding.js')
  , creditCard = require('./lib/creditCard.js')
	, fiscal     = require('./lib/fiscal.js');

function respondGrid(req, res, next) {
	var result = gridding.create(req.params.gridType, req.body.participants, req.body.options);
	res.send(result);
}

function respondFiscalPrint(req, res, next) {
	console.log('calling respondFiscalPrint');
	var result = fiscal.print(req.params, function(err, result) {
		if(err) {
			console.log(err);
			result = err;
		}

		res.send(result);
	});
}

function respondFiscalOpenDrawer(req, res, next) {
	console.log('calling respondFiscalOpenDrawer');
	var result = fiscal.openDrawer(req.params, function(err, result) {
		if(err) {
			console.log(err);
			result = err;
		}

		res.send(result);
	});
}

function respondCreditCard(req, res, next) {

	switch(req.params.action) {
		case 'charge':
			creditCard.charge(req.body, function(err, result) {
				//console.log('\n\nCHARGE Transaction Result', req.body, err, result);
				err ? res.send(err) : res.send({ result: result });
			});
			break;

		case 'refund':
			creditCard.refund(req.body, function(err, result) {
				//console.log('\n\nREFUND Transaction Result', req.body, err, result);
				err ? res.send(err) : res.send({ result: result });
			});
			break;

		case 'void':
			creditCard.void(req.body, function(err, result) {
				//console.log('\n\nVOID Transaction Result', req.body, err, result);
				err ? res.send(err) : res.send({ result: result });
			});
			break;
			
		default:
			next(new Error('Action not supported: ' + req.params.action));
			break;
	}
}

function respondSignature(req, res, next) {

	switch(req.params.action) {			
		case 'capture':
			creditCard.signature(req.body, function(err, result) { // Needs to be refactored into "Signature" class
				//console.log('\n\Signature Transaction Result', req.body, err, result);
				err ? res.send(err) : res.send({ result: result });
			});
			break;

		default:
			next(new Error('Action not supported: ' + req.params.action + ' ("/signature/capture" is supported)'));
			break;
	}
}

function respondReceipt(req, res, next) {
	var body = req.body;
	var result = receipt.create(req.params.receiptType, body);
	res.send(result);
}

var server = restify.createServer({
    name : "Club Speed Services"
});

server.use(restify.queryParser());
server.use(restify.jsonp());
server.use(restify.bodyParser());
server.use(restify.CORS());

server.post('/grid/:gridType', respondGrid);
server.post('/receipt/:receiptType', respondReceipt);
server.post('/creditCard/:action', respondCreditCard);
server.post('/signature/:action', respondSignature);

// Fiscal printer methods
server.post('/fiscal/print',      respondFiscalPrint);
server.post('/fiscal/openDrawer', respondFiscalOpenDrawer);

server.listen(8000, function() {
  console.log('%s listening at %s', server.name, server.url);
});