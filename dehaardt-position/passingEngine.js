/**
 * Module exports.
 */

module.exports = Server;

var opts = {};
var lastPoints = {};
var lastPassings = {};

function Server(config){
  if (!(this instanceof Server)) return new Server(config);
  opts = config || {};

  opts.boundaries.forEach(function(loop) {
    if(loop.type !== 'line') return;

    lastPassings[loop.id] = {};
  });
}

Server.prototype.point = function(point) {

  // Find the last point for this id
  var lastPoint = null
  if(lastPoints[point.id]) {
    lastPoint = lastPoints[point.id];
  } else {
    lastPoints[point.id] = point;
    return;
  }

  opts.boundaries.forEach(function(loop) {

    if(loop.type !== 'line') return;

    var loopLine = [[loop.coordinates[0].x, loop.coordinates[0].y], [loop.coordinates[1].x, loop.coordinates[1].y]]
    var vehicleLine = [[lastPoint.x, lastPoint.y], [point.x, point.y]]
    
    var intersectionPoint = getIntersection(loopLine, vehicleLine);
    //il.coordinates(loopLine, vehicleLine, function(points) {
    if(intersectionPoint === false)
      return;

    var totalLineLength   = lineLength(point.x, point.y, lastPoint.x, lastPoint.y);
    var passingLineLength = lineLength(point.x, point.y, intersectionPoint.x, intersectionPoint.y);
    //console.log('PASSING!', isIntersection, point, lastPoint, passingLineLength/totalLineLength);

    var t = point.t + ((point.t - lastPoint.t) * (passingLineLength/totalLineLength));

    // Filter duplicate passings
    if(!lastPassings[loop.id][point.id] || (lastPassings[loop.id][point.id] && lastPassings[loop.id][point.id]["timestamp"] && (t - lastPassings[loop.id][point.id]["timestamp"]) > opts.filterInSeconds)) {
      var passingMsg = {
        type: 'passing',
        timestamp: t,
        transponderId: point.id,
        loopId: loop.id,
        meta: {
          laptime: lastPassings[loop.id][point.id] ? t - lastPassings[loop.id][point.id]["timestamp"].toFixed(3) : null,
          pointCrossed: intersectionPoint,
          loopLine: loopLine,
          vehicleLine: vehicleLine,
        }
      };

      if(typeof opts.callback === 'function') {
        opts.callback(passingMsg);
        console.log(passingMsg.transponderId + ' ' + passingMsg.loopId + ' ' + passingMsg.timestamp + ' ' + passingMsg.meta.laptime);
      }

      lastPassings[loop.id][point.id] = passingMsg;
    }

  });

  lastPoints[point.id] = point;
};

function getIntersection(line1, line2) {
  var p0 = {x: line1[0][0], y: line1[0][1]}
  var p1 = {x: line1[1][0], y: line1[1][1]}
  var p2 = {x: line2[0][0], y: line2[0][1]}
  var p3 = {x: line2[1][0], y: line2[1][1]}
  return getCollisionPt(p0, p1, p2, p3)
}

var lineLength = function(x, y, x0, y0) {
    return Math.sqrt((x -= x0) * x + (y -= y0) * y);
};

/*
http://stackoverflow.com/questions/563198/how-do-you-detect-where-two-line-segments-intersect
*/

function getCollisionPt(p0, p1, p2, p3) {
    var s1, s2;
    s1 = {x: p1.x - p0.x, y: p1.y - p0.y};
    s2 = {x: p3.x - p2.x, y: p3.y - p2.y};

    var s10_x = p1.x - p0.x;
    var s10_y = p1.y - p0.y;
    var s32_x = p3.x - p2.x;
    var s32_y = p3.y - p2.y;

    var denom = s10_x * s32_y - s32_x * s10_y;

    if(denom == 0) {
        return false;
    }

    var denom_positive = denom > 0;

    var s02_x = p0.x - p2.x;
    var s02_y = p0.y - p2.y;

    var s_numer = s10_x * s02_y - s10_y * s02_x;

    if((s_numer < 0) == denom_positive) {
        return false;
    }

    var t_numer = s32_x * s02_y - s32_y * s02_x;

    if((t_numer < 0) == denom_positive) {
        return false;
    }

    if((s_numer > denom) == denom_positive || (t_numer > denom) == denom_positive) {
        return false;
    }

    var t = t_numer / denom;

    var p = {x: p0.x + (t * s10_x), y: p0.y + (t * s10_y)};
    return p;
}