var React = require('react/addons');
var EventFunnel = require('../mixins/event-funnel');
var generateUUID = require('random-uuid-v4');


var IRadioItem = React.createClass({

  mixins: [EventFunnel],

  render(){
    return <input type='radio' />;
  },

  componentDidMount(){
    $(this.getDOMNode()).iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      radioClass: 'iradio_flat-blue'
    })
    .on('ifClicked', this.toFunnel);
    this.setFromProps();
    //this.funnelJQueryEvents('ifChecked','ifUnchecked');
  },

  componentDidUpdate(){
    this.setFromProps();
  },

  setFromProps(){
    if (this.props.selected) {
      $(this.getDOMNode()).iCheck('check');
    } else {
      $(this.getDOMNode()).iCheck('uncheck');
    }
  }

});


var IRadioGroup = React.createClass({

  mixins: [EventFunnel],

  getDefaultProps(){
    return {
      inline: true,
      list: [{ label: 'A', value: 1 }, { label: 'b', value: 2 }],
      selected: null,
      radio: {},
      label: {}
    };
  },

  getInitialState(){
    // note: name is set by generateUUID here and should never be changed after,
    // but it is in initial state instead of default props so that a new UUID is generated for each IRadioGroup instance
    return { selected: this.props.selected, name: generateUUID() };
  },

  render(){
    var listNodes = [];

    this.props.list.forEach((item, i) => {
      listNodes.push(
        <div key={i} style={this.props.style}>
          <IRadioItem item={item}
            name={this.props.name || this.state.name}
            selected={this.props.selected === item.value}
            onFunnelEvent={this.handleRadioChange}
            {...this.props.radio}
          />

          <label
            style={{ position: 'relative', top: -5, left: 10 }}
            {...this.props.label}
          >
            {item.label}
          </label>
        </div>
      );
    });

    return <span {...this.props.container}>
      {listNodes}
    </span>;
  },

  handleRadioChange(e, optionProps, state){
    this.toFunnel(e, optionProps);
  }

});


module.exports = IRadioGroup;
