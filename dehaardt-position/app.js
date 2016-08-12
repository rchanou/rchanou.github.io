/*
https://wiki.openrtls.com/index.php?title=Software:JsonCommands#Examples

telnet 192.168.111.223 8784

11:05:17: mDNS query for _workstation._tcp.local.
11:05:17: Ready.
11:05:19: Master DECA3930304142D9 detected at 192.168.1.8

{"command":"getLsListener"}
{"response":"getLsListener","status":"ok","mode":"unicast","ip":"192.168.111.214"}

{"command":"listNodeConfig"}
{"response":"listNodeConfig","status":"ok","nodeConfig":
[{"id":0,"mode":"twr","nodeMask":"0xDECAFFFFFFFFFFFF","msecRefreshInterval":50,"msecTsync":120000,"secExpiration":3600,"maxNodes":200,"locEngine":"none","posFilter":"ma","posFilterArg":3,"downloadRate":0},
{"id":1,"mode":"twr","nodeMask":"0xBB00FFFFFFFFFFFF","msecRefreshInterval":1000,"msecTsync":120000,"secExpiration":3600,"maxNodes":200,"locEngine":"none","posFilter":"ma","posFilterArg":3,"downloadRate":0}]}

// Note the "locEngine" location engine setting
{"response":"listNodeConfig","status":"ok","nodeConfig": [{"id":0,"mode":"twr","nodeMask":"0xDECAFFFFFFFFFFFF","msecRefreshInterval":50,"msecTsync":120000,"secExpiration":3600,"maxNodes":200,"locEngine":1,"posFilter":"none","posFilterArg":0,"downloadRate":0},{"id":1,"mode":"twr","nodeMask":"0xBB00FFFFFFFFFFFF","msecRefreshInterval":1000,"msecTsync":120000,"secExpiration":3600,"maxNodes":200,"locEngine":"none","posFilter":"ma","posFilterArg":3,"downloadRate":0}]}
09:57:54: node 0xdeca343037101323 gone inactive

{"command":"scanInfra"}
{"response":"scanInfra","status":"in progress"}
{"command":"setAutoPos","coordinates":"auto"}
{"response":"setAutoPos","status":"ok"}

{"command":"listAnchors"}
{"response":"listAnchors","status":"ok","anchors": [
{"id":"0xDECA313033600E0E","coordinates": {"x":1.820,"y":7.920,"z":0.000,"heading":0.000,"pqf":0.000}},
{"id":"0xDECA32303240150E","coordinates": {"x":5.180,"y":2.130,"z":-0.250,"heading":0.000,"pqf":0.000}},
{"id":"0xDECA363032401550","coordinates": {"x":2.740,"y":3.960,"z":-0.250,"heading":0.000,"pqf":0.000}},
{"id":"0xDECA363033001546","coordinates": {"x":5.790,"y":0.000,"z":-0.250,"heading":0.000,"pqf":0.000}},
{"id":"0xDECA373031201572","coordinates": {"x":2.130,"y":0.000,"z":-0.250,"heading":0.000,"pqf":0.000}},
{"id":"0xDECA38303260150A","coordinates": {"x":0.000,"y":2.130,"z":-0.250,"heading":0.000,"pqf":0.000}}
]}

{"command":"listTags"}
{"response":"listTags","status":"ok","tags": [{"id":"0xDECA343037101323","nodeDetails": {"hwVersion":"0x289A0760","ldrfwVersion":"0x16040600","fwVersion":"0x16040600","nodeOptions":"0x30411"},"coordinates": {"x":0.000,"y":0.000,"z":0.000,"heading":0.000,"pqf":0.000}}]}

{ "command": "setPos", "id": "0xDECA313033600E0E", "x": 7.92, "y": 1.82, "z": 0 }
{ "command": "setPos", "id": "0xDECA32303240150E", "x": 5.18, "y": 3.96, "z": -0.25 }
{ "command": "setPos", "id": "0xDECA363032401550", "x": 2.74, "y": 3.96, "z": -0.25 }
{ "command": "setPos", "id": "0xDECA363033001546", "x": 5.79, "y": 0, "z": -0.25 }
{ "command": "setPos", "id": "0xDECA373031201572", "x": 2.13, "y": 0, "z": -0.25 }
{ "command": "setPos", "id": "0xDECA38303260150A", "x": 0, "y": 2.13, "z": -0.25 }

{"command":"version"}
{"response":"version","status":"ok","id":"DECA313033600E0E","ean":"8102842401211","fwVersion":"16040600","apiVersion":"16040600"}

// FROM K1 IRVINE
{"response":"listNodeConfig","status":"ok","nodeConfig": [{"id":0,"mode":"twr","nodeMask":"0xDECAFFFFFFFFFFFF","msecRefreshInterval":50,"msecTsync":120000,"secExpiration":3600,"maxNodes":0,"locEngine":1,"posFilter":"none","posFilterArg":0,"downloadRate":0},{"id":1,"mode":"twr","nodeMask":"0xBB00FFFFFFFFFFFF","msecRefreshInterval":1000,"msecTsync":120000,"secExpiration":3600,"maxNodes":0,"locEngine":"none","posFilter":"ma","posFilterArg":3,"downloadRate":0}]}

{"response":"listAnchors","status":"ok","anchors": [{"id":"0xDECA313033600E0E","coordinates": {"x":0.000,"y":0.000,"z":0.000,"heading":0.000,"pqf":0.000}},{"id":"0xDECA32303240150E","coordinates": {"x":44.132,"y":-5.433,"z":-0.480,"heading":0.000,"pqf":0.000}},{"id":"0xDECA35303130150B","coordinates": {"x":24.223,"y":0.000,"z":0.000,"heading":0.000,"pqf":0.000}},{"id":"0xDECA363032401550","coordinates": {"x":-14.630,"y":35.427,"z":1.491,"heading":0.000,"pqf":0.000}},{"id":"0xDECA373031201572","coordinates": {"x":11.808,"y":17.412,"z":1.483,"heading":0.000,"pqf":0.000}},{"id":"0xDECA38303260150A","coordinates": {"x":-11.381,"y":16.654,"z":1.429,"heading":0.000,"pqf":0.000}},{"id":"0xDECA39303210034B","coordinates": {"x":-23.965,"y":-0.148,"z":0.008,"heading":0.000,"pqf":0.000}}]}

 mDNS query for _workstation._tcp.local

*/

var dgram = require('dgram');
var server = dgram.createSocket('udp4');
var _ = require('underscore');
var net = require('net');
var reconnect = require('reconnect-net');
var classifyPoint = require("robust-point-in-polygon");
var newConfig = require('./config.json');

var config = {
  openRtlsIp: "",
  openRtlsCommandPort: 8784,
  openRtlsListenPort: 8787,
  socketIoPort: 3000,
  pingInterval: 5000,
  pingHandle: null,
  enableMdns: false
}

// Load config overrides
if(newConfig.openRtlsIp)   config.openRtlsIp = newConfig.openRtlsIp;
if(newConfig.socketIoPort) config.socketIoPort = newConfig.socketIoPort;
if(newConfig.enableMdns)   config.enableMdns = newConfig.enableMdns;

var lastPoints = {};

var lineLength = function(x, y, x0, y0) {
    return Math.sqrt((x -= x0) * x + (y -= y0) * y);
};

// SOCKET.IO
var app = require('http').createServer(handler)
var io = require('socket.io')(app);
var fs = require('fs');

var passingEngineConfig = {
  filterInSeconds: (newConfig.passingEngineConfig && newConfig.passingEngineConfig.filterInSeconds) ? newConfig.passingEngineConfig.filterInSeconds : 5, // Throw out any passings faster than this
  boundaries: (newConfig.passingEngineConfig && newConfig.passingEngineConfig.boundaries && newConfig.passingEngineConfig.boundaries.length > 0) ? newConfig.passingEngineConfig.boundaries : [],
  callback: function(event) {
    io.emit('passing', event);
  }
  // If we do AMB timing we'll need to use the last five hex digits cast to a number -- parseInt("0xFFFFF")
};

console.log('Starting with config:', config, passingEngineConfig, '\n\n');

var passingEngine = require('./passingEngine')(passingEngineConfig);

app.listen(config.socketIoPort);

function handler (req, res) {

  // Could just pass req.url into readFile but we'd be able to read arbitary files then. :-)
  var validFiles = ['/charts.html', '/favicon.ico', '/timing.html'];
  var filename = ~validFiles.indexOf(req.url) ? req.url : '/client.html';

  fs.readFile(__dirname + filename,
  function (err, data) {
    if (err) {
      res.writeHead(404);
      return res.end('Error loading: ' + filename);
    }

    res.writeHead(200);
    res.end(data);
  });
}

io.on('connection', function (socket) {
  console.log('Socket.io connection from ' + socket.request.connection.remoteAddress);

  socket.on('config', function(data) {
    socket.emit('config', passingEngineConfig.boundaries);
  });
});

io.on('error', function(err) {
  console.log('Socket.io server error:\n' + err.stack);
});


// OPENRTLS
var re = reconnect({
  initialDelay: 1000,
  maxDelay: 10000,
  strategy: 'fibonacci',
  failAfter: Infinity,
  randomisationFactor: 0,
  immediate: false
}, function (stream) {
  console.log('When is this fired?')
  stream.on('data', function(data) {
    //console.log('OpenRTLS Received: ' + data);
  });
})
.on('connect', function (con) {
  console.log('Connected to OpenRTLS');
  con.write('{"command":"getLsListener"}\r');

  config.pingHandle = setInterval(function() {
    try {
      con.write('{"command":"getLsListener"}\r');
    } catch(e) {}
  }, config.pingInterval);
})
.on('reconnect', function (n, delay) {
  // n = current number of reconnect  
  // delay = delay used before reconnect
  console.log('OpenRTLS reconnection attempt', n, delay);
})
.on('disconnect', function (err) {
  console.log('OpenRTLS connection disconnected', err);
  clearInterval(config.pingHandle);
})
.on('error', function (err) {
  console.log('OpenRTLS error:', err);
})
.connect(config.openRtlsCommandPort, config.openRtlsIp);


// LISTEN FOR UDP
server.on('message', function(msg, rinfo) {
  //console.log('server got: ' + msg + ' from ' + rinfo.address + ':' + rinfo.port);
  // {"id":"0xDECA343037101323","timestamp":1469033913.667,"msgid":940531,"coordinates":{"x":2.202,"y":-0.400,"z":0.000,"heading":0.000,"pqf":69},"meas":[{"anchor":"0xDECA32303240150E","dist":0.878,"tqf":1,"rssi":-78.5},{"anchor":"0xDECA38303260150A","dist":4.858,"tqf":2,"rssi":-80.5},{"anchor":"0xDECA313033600E0E","dist":1.381,"tqf":1,"rssi":-80.5},{"anchor":"0xDECA373031201572","dist":4.600,"tqf":2,"rssi":-79.5},{"anchor":"0xDECA363033001546","dist":3.570,"tqf":2,"rssi":-80.0}]}
  try {
    var coordinates = msg.toString().split(/\r?\n/)

    coordinates.forEach(function(coordinate) {
      
      // Throw out empty items
      if(coordinate.length == 0) return;

      var packet = JSON.parse(coordinate);

      // Ensure we have a valid packet
      if(!packet || !packet.id || !packet.timestamp || !packet.coordinates || !packet.coordinates.x || !packet.coordinates.y) return;

      // Filter for "0" coordinates
      if(packet.coordinates.x == 0) return;

      // Filter for large jumps
      var lastPoint = null
      if(lastPoints[packet.id]) {
        lastPoint = lastPoints[packet.id];
        lastPoints[packet.id] = packet;
      } else {
        lastPoints[packet.id] = packet;
        return;
      }
      var distanceTraveled = lineLength(packet.coordinates.x, packet.coordinates.y, lastPoint.coordinates.x, lastPoint.coordinates.y);
      var timeTaken = packet.timestamp - lastPoint.timestamp;
      var metersPerSecond = distanceTraveled / timeTaken;

      /*if(metersPerSecond > 20)
        return;*/

      // Calculate degrees (needs previous plus current)
      var headingDegrees = Math.atan2(packet.coordinates.y - lastPoint.coordinates.y, packet.coordinates.x - lastPoint.coordinates.x) * 180 / Math.PI;
      lastPoints[packet.id].deg = headingDegrees; // Storing so we can filter on rapid changes later?
      // TODO -- check this math and logic. If the degree change is > 90, probably a bad passing?
      // var degreeChange = Math.min( (lastPoint.deg-headingDegrees+360)%360, (headingDegrees-lastPoint.deg+360)%360 );

      //console.log('ID: ' + packet.id + ' TIMESTAMP: ' + packet.timestamp + ' X: ' + packet.coordinates.x + ' Y: ' + packet.coordinates.y + 'Z: ' + packet.coordinates.z);
      var pointPacket = {
        id: packet.id,
        t: packet.timestamp,
        x: packet.coordinates.x,
        y: packet.coordinates.y,
        z: packet.coordinates.z,
        //distance: distanceTraveled,
        //timeTaken: timeTaken,
        mps: metersPerSecond,
        deg: headingDegrees,
        //tComparison: packet.timestamp + '-' + lastPoint.timestamp
      };

      // Stamp the location of this point if we are in a defined boundary
      passingEngineConfig.boundaries.forEach(function(boundary) {
        if(boundary.type === 'polygon' && classifyPoint(boundary.coordinates, [pointPacket.x, pointPacket.y]) <= 0) {
          pointPacket.loc = boundary.id;
        }
      });

      passingEngine.point(pointPacket);
      io.emit('point', pointPacket);  
    });
  } catch(e) {
    console.log('ERROR', e)
  }
});

server.on('listening', function() {
  var address = server.address();
  console.log('OpenRTLS server listening ' + address.address + ':' + address.port);
});

server.bind(config.openRtlsListenPort);

if(config.enableMdns) {
  // OpenRTLS location
  var mdns = require('mdns-js');
  //if you have another mdns daemon running, like avahi or bonjour, uncomment following line
  mdns.excludeInterface('0.0.0.0');

  //var browser = mdns.createBrowser();
  var browser = mdns.createBrowser(mdns.tcp("workstation"));

  browser.on('ready', function () {
      browser.discover(); 
  });

  browser.on('update', function (data) {
      console.log('Found:', data, '\n\n');
  });
}