var restify  = require('restify')
  , receipt  = require('./lib/receipt.js')
  , gridding = require('./lib/gridding.js');

function respondGrid(req, res, next) {
	var result = gridding.create(req.params.gridType, req.body.participants, req.body.options);
	res.send(result);
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

server.listen(8000, function() {
  console.log('%s listening at %s', server.name, server.url);
});