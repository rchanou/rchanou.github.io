var rest = require('restling');
var Promise = require('bluebird');
var constructAsync = Promise.promisify(require('./construct'));
var utils = Promise.promisifyAll(require('../clubspeed-utils/api'));

constructAsync()
.then(utils.putJsonAsync)
.then(function(res){
  console.log(res);
});
