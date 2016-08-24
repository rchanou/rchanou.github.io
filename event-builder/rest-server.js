var restify    = require('./lib/node_modules/restify')
  , receipt    = require('./lib/receipt.js')
  , gridding   = require('./lib/gridding.js')
  , creditCard = require('./lib/creditCard.js')
  , fiscal     = require('./lib/fiscal.js');

var debug = true;

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

	// Set timeout on REST-server side
	var timeout = 120;
	if(req.body && req.body.provider && req.body.provider.type == 'ingenico' && req.body.provider && req.body.provider.options && req.body.provider.options.timeout)  {
		timeout = req.body.provider.options.timeout;
	}
	req.connection.setTimeout(2*60*1000 + (timeout * 1000) + 2000); // 2:02 seconds more than REST -> INGENICO
	res.connection.setTimeout(2*60*1000 + (timeout * 1000) + 2000); // 2:02 seconds more than REST -> INGENICO


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
		
        case 'tokenize': // For creating a tokenized card without charging
            creditCard.tokenize(req.body, function(err, result) {
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

	console.log(req.params);

	switch(req.params.action) {			
		case 'capture':
			switch(req.params.provider.type) {
				case 'ipad':
					var foundClient = false;
					clients.forEach(function(socket, i) { // Should we limit to just one connected client for a terminal?
						var timeout = req.params.provider.options.timeout || 60;
						req.params.provider.options.terminal = req.params.provider.options.terminal ? req.params.provider.options.terminal.toLowerCase() : 'pos1'; // Defaulting for now
						if(socket.terminal && socket.terminal == req.params.provider.options.terminal.toLowerCase()) {
							foundClient = true; // This should be sync, eh?
							clients[i].emit('signature:capture', req.params, timeoutCallback((timeout * 1000) + 1000, function(data, err) {
								if(debug) console.log('signature:capture', 'err', err, 'data', data);

								if(res.headersSent) {
									console.log('Headers already sent, more than one client connected!'); // How do we get into this situation? Two+ terminals with same name.
								} else if(err) {
									res.send({ result: { message: 'Signature timeout', code: "-1", success: false, signatureJSON: null, _original: { data: data } } });
								} else if(data.length == 0) {
									res.send({ result: { message: 'Signature empty', code: "-2", success: false, signatureJSON: null, _original: { data: data } } });
								} else {
									res.send({ result: { success: true, signatureJSON: data, _original: { data: data } } });
								}
							}));
						}
					});
					if(!foundClient) res.send({ result: { message: 'Signature client not connected', code: "-3", success: false, signatureJSON: null, _original: {} } })
					break;

				case 'ingenico':
					creditCard.signature(req.body, function(err, result) { // Needs to be refactored into "Signature" class
						//console.log('\n\Signature Transaction Result', req.body, err, result);
						err ? res.send(err) : res.send({ result: result });
					});
					break;

				default:
					next(new Error('Provider not supported for signatures: ' + req.params.provider.type));
			}
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

/**
 * SOCKET.IO
 */
var http       = require('http');
var io         = require('./lib/node_modules/socket.io')();
var middleware = require('./lib/node_modules/socketio-wildcard')();
require('./lib/node_modules/socketio-auth')(io, {
  authenticate: authenticate, 
  postAuthenticate: postAuthenticate,
  timeout: 30000
});

io.use(middleware);

io.on('connection', function(socket) {
  var socketId = socket.id;
  var clientIp = socket.request.connection.remoteAddress;
  console.log('\n\nConnection from Socket.io client', socketId, clientIp);
  socket.on('*', function(data){ 
    console.log('Socket.IO Event Received', data);
    io.emit('*', data);
  });
  socket.on('publish', function(data){ 
    console.log('"publish" Event Received', data);
    if(data.EventName) io.emit(data.EventName, data);
  });
  socket.on('disconnect', function() {
    console.log('Disconnection from Socket.io client', socketId, clientIp);
  });
});

/*setInterval(function() {
    io.emit('PrintCustomerMembershipCard', {"CustID":1035431,"EventName":"PrintCustomerMembershipCard","Source":"POS1"});
}, 5000);*/

io.listen(server.server);

// Serve up socket.io listener
//server.get(/.*/, restify.serveStatic({
//  directory: './public',
//  default: 'index.html'
//}));

function authenticate(socket, data, callback) {
    authenticateUsernamePassword(data, callback);
};

function postAuthenticate(socket, data) {
    //console.log('postAuthenticate', data);
};

function authenticateUsernamePassword(opts, cb) {

    var options = {
      host: '127.0.0.1',
      path: '/api/index.php/users/login.json?is_admin=1&username='+ opts.username +'&password=' + opts.password,
      auth: opts.username + ':' + opts.password,
      method: 'GET'
    };

    var req = http.request(options, function(res) {
        if(res.statusCode === 200) {
            console.log('Successful authentication')
            return cb(null, true);
        } else {
            console.log('Failed authentication', res.statusCode, opts)
            return cb(null, false);
        }
    });

    req.on('error', function(e) {
      console.log('Problem with authentiction API request:', e.message);
      cb(e);
    });

    // write data to request body
    req.end();
}
/**
 * END SOCKET.IO
 */

server.listen(8000, function() {
  console.log('%s listening at %s', server.name, server.url);
});