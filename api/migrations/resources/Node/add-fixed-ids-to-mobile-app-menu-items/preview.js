console.log('running preview');

var Promise = require('bluebird');
var constructAsync = Promise.promisify(require('./construct'));

constructAsync()
.then(function(res){
  console.log(res);
});
