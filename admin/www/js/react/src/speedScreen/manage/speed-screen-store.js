var Immutable = require('immutable');
let { chan, go, put, take } = require('js-csp');


/*
  Still trying to grok the Flux pattern so this is a highly simplified version
  compared to the examples out there. For one thing, I'm not spreading code out
  across many different files, so this contains the dispatcher, store(s) AND action definitions.

  - Ronny
*/

// Config Setup
if (window.location.hostname === '192.168.111.205'){
  config.apiURL = 'https://vm-122.clubspeedtiming.com/api/index.php';

  //config.apiURL = 'https://uksydney.clubspeedtiming.com/api/index.php';
  //config.privateKey = '173123as821731872!';

  //config.apiURL = 'https://ekwigan.clubspeedtiming.com/api/index.php';
  //config.privateKey = 'HX8ykkTzH3ShDjI8mUS8';
}
config.apiURL = config.apiURL + '/';


// Initial Store
/*var speedScreenStore = Immutable.fromJS({
  channels: [
  {
    speedScreen: {
      "name": "SCR1",
      "hash": "893u88ejekejasl928fdqkldljq9u03437gusgus",
      "options":
      {
        "backgroundUrl": "images/backgrounds/default.jpg"
      },
      "timelines": {
        "regular": {
          "options":
          {
            "backgroundUrl": "images/backgrounds/default.jpg"
          },
          "slides": [
          {"type":"image","options":{"url":"images/slide_during_no_race.png","duration":10000}},
          {
            "type": "url",
            "options": {
              "url": "http:\/\/192.168.111.122\/api\/slides\/video.html?videoUrl=https%3A%2F%2Fvm-122.clubspeedtiming.com%2Fassets%2Fvideos%2Fspeedscreen-slide-231.mp4",
              "originalUrl": "https:\/\/vm-122.clubspeedtiming.com\/assets\/videos\/speedscreen-slide-231.mp4",
              "duration": 30000
            }
          }
          ]
        },
        "races": {
          "options":
          {
            "backgroundUrl": "images/backgrounds/default.jpg"
          },
          "tracks": [],
          "slides": [
          {"type":"image","options":{"url":"images/slides_during_races.png","duration":16000}},
          {
            "type": "scoreboard",
            "options": {
              "trackId": 1,
              "postRaceIdleTime": 15000,
              "url": 'pages/slides/scoreboard'
            }
          }
          ]
        }
      }
    }
  }
  ]
}
);
*/


var speedScreenStore = Immutable.Map({
  fields: {
    original: {
      label: 'URL',
      convertToDbFormat(){}
    }
  }
});

var actionQueue = chan();

var fireAction = opts => {
  go(function* (){
    yield put(actionQueue, opts);
  });
};

var callbacks = [];


// Stuff Store Does to Itself
$.get(config.apiURL + 'tracks/index.json?key=' + config.apiKey)
.then(response => {
  fireAction({ type: 'handleTrackFetchSuccess', response });
});

$.get(config.apiURL + 'screenTemplate.json?deleted=false&key=' + config.privateKey)
.then(response => {
  fireAction({ type: 'handleRawChannelListFetchSuccess', response });
});


// Ghetto Dispatcher
go(function* (){

  // All changes to speedScreenStore must occur inside one of these action functions
  // so that they can properly propagate to subscribing components
  var actions = {

    handleTrackFetchSuccess(opts){
      var tracks = opts.response.tracks.map(track => ({ label: track.name, value: ~~track.id }) );
      speedScreenStore = speedScreenStore.set('tracks', tracks);
    },

    handleRawChannelListFetchSuccess(opts){
      // deleted, postRaceIdleTime, screenTemplateId, screenTemplateName, showScoreboard, startPosition, trackId
      // TODO: turn above into legit contract assertions
      speedScreenStore = speedScreenStore.set('rawChannels', Immutable.fromJS(opts.response.channels));
      speedScreenStore = speedScreenStore.set('channels', Immutable.Map());
      opts.response.channels.forEach(channel => {
        $.get(config.apiURL + 'channel/' + channel.screenTemplateId + '.json?key=' + config.privateKey)
        .then(response => {
          fireAction({ type: 'handleChannelFetchSuccess', id: channel.screenTemplateId, response });
        });

        $.get(config.apiURL + 'screenTemplateDetail.json?screenTemplateId='
          + channel.screenTemplateId + '&key=' + config.privateKey)
        .then(response => {
          fireAction({ type: 'handleRawLineupFetchSuccess', id: channel.screenTemplateId, response });
        });
      });
    },

    handleChannelFetchSuccess(opts){
      // hash, lineup[], name, options, speedScreenVersion
      speedScreenStore = speedScreenStore.setIn(
        ['channels', opts.id],
        Immutable.fromJS(opts.response)
      );
    },

    handleRawLineupFetchSuccess(opts){
      opts.response.channelDetail.forEach(slide => {
        speedScreenStore = speedScreenStore.setIn(
          ['rawSlides', slide.screenTemplateDetailId],
          Immutable.fromJS(slide)
        );
      });
    },

    upload(opts){
      if (!opts.slideId || !opts.files.length){
        return;
      }

      speedScreenStore = speedScreenStore.set('uploading', true);

      var file = opts.files[0];
      var data = new FormData();
      var ext = file.name.substr(file.name.lastIndexOf('.') + 1);
      var fileName = 'speedscreen-slide-' + opts.slideId + '.' + ext;
      data.append('filename', fileName);
      data.append('image', file);
      var url = '/admin/' + (window.location.hostname === '192.168.111.205' && 'www/')
      + 'channel/' + opts.fileType + 's/update';

      var ajaxOpts = {
        type: 'POST',
        cache: false,
        contentType: false,
        processData: false,
        data,
        url,
        xhrFields: {
          onprogress(event){
            fireAction(event);
          }
        }
      };

      $.ajax(ajaxOpts).then(
        response => {
          fireAction({ type: 'handleUploadResponse', response });
        },
        response => { // TODO: proper error handling
          fireAction({ type: 'handleUploadResponse', response });
        }
      );
    },

    handleUploadResponse(){
      speedScreenStore = speedScreenStore.set('uploading', false);
    },

    progress(opts){
      console.log('progress', opts);
    }

  };

  var prevStore;

  while(true){

    var actionOpts = yield actionQueue;

    console.log('action', actionOpts);

    var action = actions[actionOpts.type];

    if (!action || !action.call) {
      console.log('not an action', actionOpts.type);
      continue;
    }

    action.call(undefined, actionOpts);

    // if action actually changed store, fire registered callbacks of listening components
    if (prevStore !== speedScreenStore){
      //var store = speedScreenStore.toJSON? speedScreenStore.toJSON(): speedScreenStore;
      var store = speedScreenStore;
      //var storeJS = speedScreenStore.toJSON();
      callbacks.forEach(callback => {
        callback({ store });
      });
    }

    prevStore = speedScreenStore;
  }

});


// Functions To Be Used By Subscribing Components
module.exports = {

  fireAction,

  addListener(callback){
    callbacks.push(callback);
    return callbacks.length - 1;
  },

  removeListener(handle){
    delete callbacks[handle];
  },

  get(){
    return speedScreenStore;
    //return speedScreenStore.toJSON? speedScreenStore.toJSON(): speedScreenStore;
  }

};
