var runner = require('child_process');
var rest = require('restling');

var HOSTNAME = 'localhost';

module.exports = {

  getPrivateKey: function(callback){
    runner.exec(
      'C:\\xampp\\php\\php -r \"include(\'C:\\clubspeedapps\\api\\config.php\'); print json_encode($privateKey);\"',
      function (err, stdout, stderr) {
        var privateKey = JSON.parse(stdout);
        callback(err || null, privateKey);
      }
    );
  },


  createMenuItemRequestUrl: function(privateKey){
    return 'http://' + HOSTNAME + '/api/index.php/settings.json?'
    + 'namespace=mobileApp&name=menuItems&key=' + privateKey;
  },


  requestMenuItems: function(privateKey, callback){
    rest.get(utils.getMenuItemRequestUrl(privateKey))
    .then(
      function(res){
        callback(null, res);
      },
      function(err){
        callback(err);
      }
    );
  },


  getMenuItemWithFixedIdUpdateRequestFromResponse: function(privateKey, res){
    var setting = res.data.settings[0];
    var menuItemsValue = JSON.parse(setting.value);//.menuItems;

    var idMap = {
      'TOP TIMES': 'toptimes',
      'TRACK INFORMATION': 'trackinfo',
      'VENUE': 'trackinfo',
      'TRACK INFO': 'trackinfo',
      'CLUB SPEED': 'clubspeed',
      'CLUBSPEED': 'clubspeed',
      'PRO SKILL': 'proskill',
      'PROSKILL': 'proskill',
      'MY RESULTS': 'proskill',
      'RESULTS': 'proskill',
      'E-CARD': 'membercard',
      'MEMBER CARD': 'membercard',
      'MEMBERSHIP': 'membercard',
      'ONLINE BOOKING': 'booking',
      'BOOKING': 'booking',
      'EXTRA': 'extra',
      'PROMOTIONS': 'promotions'
    }

    menuItemsValue.menuItems.forEach(function(item){
      var matchedFixedId = idMap[item.label.toUpperCase()];
      if (!matchedFixedId){
        console.error('No matching Fixed ID found!', item.label);
        return;
      }

      item.fixedId = matchedFixedId;
    });

    var data = { value: JSON.stringify(menuItemsValue) };

    var url = 'http://' + HOSTNAME + '/api/index.php/settings/'
    + setting.settingsId + '?key=' + privateKey;

    return { data: data, url: url };
  },


  putJson: function(opts, callback){
    rest.putJson(opts.url, opts.data)
    .then(
      function(res){
        callback(null, res);
      },
      function(err){
        callback(err);
      }
    );
  }

}
