var restify = require('restify')
  //, mongoose = require('mongoose')
  , gridding = require('./lib/gridding.js');
  //, skill = require('./lib/skill.js');

/*mongoose.connect('mongodb://localhost/clubspeedRest');
var db = mongoose.connection
	, Log;
db.on('error', console.error.bind(console, 'connection error:'));
db.once('open', function callback () {
	var logSchema = mongoose.Schema({
		source: { type: mongoose.Schema.Types.Mixed },
		level: { type: String },
		message: { type: String },
		meta: { type: mongoose.Schema.Types.Mixed },
		createdAt: { type: Date }
	});
	Log = mongoose.model('Log', logSchema);
});*/

function respondGrid(req, res, next) {
	var result = gridding.create(req.params.gridType, req.body.participants, req.body.options);
	res.send(result);
}

/*function respondSkill(req, res, next) {
	var result = skill.calculate(req.body.participants, req.body.options);
	res.send(result);
}

function respondLog(req, res, next) {
	var logEntry = new Log({ source: req.body.source, level: req.body.level, message: req.body.message, meta: req.body.meta, createdAt: Date.now() });
	console.error(logEntry);
	logEntry.save(function (err, logEntry) {
		if (err) return console.error(err);
	});
	res.send({ success: true });
}*/

var server = restify.createServer({
    name : "Club Speed Services"
});

server.use(restify.queryParser());
server.use(restify.jsonp());
server.use(restify.bodyParser());
server.use(restify.CORS());

server.post('/grid/:gridType', respondGrid);
//server.post('/skill', respondSkill);
//server.post('/log', respondLog);

server.listen(8000, function() {
  console.log('%s listening at %s', server.name, server.url);
});

/*
curl -i -X POST -H "Content-Type: application/json" -d '{ "participants": [{"name":"Wes","points":5,"bestAverageLaptime":31,"bestLaptime":35.234,"startingPosition":3,"finishingPosition":5},{"name":"Glenda","points":3,"bestAverageLaptime":33,"bestLaptime":33.234,"startingPosition":2,"finishingPosition":4},{"name":"Max","points":3,"bestAverageLaptime":35,"bestLaptime":33.536,"startingPosition":1,"finishingPosition":3},{"name":"Tommy","points":2,"bestAverageLaptime":34,"bestLaptime":36.234,"startingPosition":4,"finishingPosition":2},{"name":"Shakib","points":0,"bestAverageLaptime":32,"bestLaptime":31.234,"startingPosition":5,"finishingPosition":1}], "options": { "maxDrivers": 3 } }' http://192.168.111.103:8000/grid/bestLapTime

curl -i -X POST -H "Content-Type: application/json" -d '{"participants": [{"id": 123, "skill": [25, 8], "rank": 1}, {"id": 456, "skill": [25, 8], "rank": 2}], "options": {}}' http://127.0.0.1:8000/skill
*/