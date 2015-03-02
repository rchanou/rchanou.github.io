var React = require('react/addons');
//let { chan, go, put, take } = require('js-csp');

var SpeedScreenStore = require('./speed-screen-store');

var Uploader = require('../../components/uploader').Component;
var Select = require('../../components/react-select2');
var IW = require('../../components/immutable-wrapper');
var MagicMove = require('react-magic-move');

var Slide = React.createClass({

  getDefaultProps(){
    return {
      fields: []
    };
  },

  render(){
    return <section>

    </section>;
  }

});


var Test = React.createClass({
  render(){
    return <div>
      {JSON.stringify(this.props)}
    </div>;
  }
});


function shuffle(array) {
  var currentIndex = array.length, temporaryValue, randomIndex ;

  // While there remain elements to shuffle...
  while (0 !== currentIndex) {

    // Pick a remaining element...
    randomIndex = Math.floor(Math.random() * currentIndex);
    currentIndex -= 1;

    // And swap it with the current element.
    temporaryValue = array[currentIndex];
    array[currentIndex] = array[randomIndex];
    array[randomIndex] = temporaryValue;
  }

  return array;
}


module.exports = React.createClass({

  getInitialState(){
    return {
      store: SpeedScreenStore.get(),
      test: ['blah','tsrats','fart']
    };
  },

  render(){
    if (!this.state.store.has('channels')) return null;
    console.log('render', this.state.store.get('channels'), this.state.store.get('channels').toJSON());
    //var store = this.state.store.toJSON();

    var channelNodes = this.state.store.get('channels').map(channel => {
      return <div key={Math.random()}>
        {channel.name}
      </div>;
      //return <IW element={Test} value={channel} />;
    })
    .toJSON();

    var testNodes = this.state.test.map((test, i) => {
      return <div key={test}
        style={{ transition: 'all 500ms ease', boxSizing: 'border-box' }}
      >
        {test}
      </div>;
    });

    return <div>
      <MagicMove>
        {testNodes}
      </MagicMove>
      {this.state.store}
    </div>;

    return <div>
      {store.uploading? 'Uploading': 'Sitting'}

      <Uploader
        fileInputProps={{ accept: 'image/*' }}

        onEvent={event => {
          if (event.type !== 'select') return;

          SpeedScreenStore.fireAction({
            type: 'upload',
            files: event.files,
            fileType: 'image',
            slideId: 1
          });
        }}
      />

      <Uploader
        fileInputProps={{ accept: 'video/*' }}

        onEvent={event => {
          if (event.type !== 'select') return;

          SpeedScreenStore.fireAction({
            type: 'upload',
            files: event.files,
            fileType: 'video',
            slideId: 1
          });
        }}
      />

      <Select list={store.tracks} />
    </div>;
  },

  componentDidMount(){
    this.storeListener = SpeedScreenStore.addListener(event => {
      this.setState(event);
    });

    setInterval(() => {
      this.setState({ test: shuffle(this.state.test) });
    }, 2000);
  },

  componentDidUpdate(){
    console.log('update', this.state.store.toJSON());
  },

  componentWillUnmount(){
    SpeedScreenStore.removeListener(this.storeListener);
  }

});
