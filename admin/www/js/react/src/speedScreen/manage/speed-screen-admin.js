var React = require('react/addons');
var csp = require('js-csp');


var getTitleCase = (text) => {
  var result = text.replace( /([A-Z])/g, " $1" );
  return result.charAt(0).toUpperCase() + result.slice(1); // capitalize the first letter - as an example.
};


var SlideForm = React.createClass({
  getDefaultProps(){
    return {
      dragX: null,
      dragY: null,
      onMouseDown(){}
    }
  },

  render(){
    var optionElements = [];
    for (var optionName in this.props.options){
      optionElements.push(<label>
        {getTitleCase(optionName)}
        <input defaultValue={this.props.options[optionName]} />
      </label>);
    }

    var style = null;
    if (this.props.dragX !== null && this.props.dragY !== null){
      style = {
        position: 'fixed', top: this.props.dragY, left: this.props.dragX
      };
    }

    return <li>
      <div style={{ height: this.props.dragX !== null && this.props.dragY !== null? 200: 0 }}></div>
      <form style={style}>
        <legend
        onMouseDown={e => {
          e.preventDefault();
          this.props.onDragStart({ ...this.props });
        }}>
          {this.props.type}
        </legend>
        {optionElements}
      </form>
    </li>;
  }
});


module.exports = React.createClass({
  events: csp.chan(),

  getDefaultProps(){
    return {
      channel: null
    };
  },

  getInitialState(){
    return {
      channels: [],
      selectedChannelHash: null,
      windowX: 0,
      windowY: 0,
      mouseX: null,
      mouseY: null
    };
  },

  componentWillMount(){
    $.get(
      'https://vm-122.clubspeedtiming.com/api/index.php/channel/1.json?key=b4b04bc152abe089f4baa22b9cf85420',
      res => {
        console.log(res);
        this.setState({ channels: [res] });
      }
    );
  },

  render(){
    return <div>
      {this.renderChannels()}
    </div>;
  },

  renderChannels(){
    console.log(this.state.channels);
    return this.state.channels.map(channel => {
      var lineupElements = this.state.channels[0].lineup.map((slide, i) => {
        var dragX, dragY;
        if (i === this.state.dragSlideIndex){
          dragX = this.state.mouseX - this.state.windowX;
          dragY = this.state.mouseY - this.state.windowY;
        }

        return <SlideForm {...slide} dragX={dragX} dragY={dragY}
           onDragStart={e => {
             csp.go(function* (){
               yield csp.put(this.events, { type: 'mouseDown', slideIndex: i });
             }.bind(this));
           }}
        />;
      });

      return <ol style={{ listStyleType: 'none' }}>
        {lineupElements}
      </ol>;
    });
  },

  componentDidMount(){
    var self = this;

    $(window).scroll(e => {
      this.setState({ windowY: $(window).scrollTop(), windowX: $(window).scrollLeft() });
    });

    $(window).mousemove(e => {
      e.preventDefault();
      csp.go(function* (){
        yield csp.put(self.events, { type: 'mouseMove', x: e.pageX, y: e.pageY });
      });
    });

    $(window).mouseup(e => {
      csp.go(function* (){
        yield csp.put(self.events, { type: 'mouseUp' });
      });
    });

    csp.go(function* (){
      var e, dragSlideIndex;

      while (true){
        do {
          e = yield csp.take(self.events);
        } while (e.type !== 'mouseDown');
        dragSlideIndex = e.slideIndex;

        e = yield csp.take(self.events);
        while (e.type === 'mouseMove'){
          self.setState({ mouseX: e.x, mouseY: e.y, dragSlideIndex });
          e = yield csp.take(self.events);
        }
        /*do {
          e = yield csp.take(self.events);
          self.setState({ mouseX: e.x, mouseY: e.y, dragSlideIndex });
        } while (e.type === 'mouseMove');*/
      }
    });
  },

  componentDidUpdate(){
    console.log(this.state.dragSlideIndex, this.state.mouseX, this.state.mouseY);
  }

});
