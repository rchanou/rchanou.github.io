var rest = require('restling');
var Promise = require('bluebird');
var utils = Promise.promisifyAll(require('../clubspeed-utils/api'));

module.exports = function(callback){
  try{

    var privateKey;

    utils.getPrivateKeyAsync()
    .then(function(key){
      privateKey = key;
      return key;
    })
    .then(utils.createMenuItemRequestUrl)
    .then(rest.get)
    .then(function(res){
      var updateParams = utils.getMenuItemWithFixedIdUpdateRequestFromResponse(privateKey, res);
      callback(null, updateParams);
    });

  } catch(err){
    callback(err, res);
  }
};
