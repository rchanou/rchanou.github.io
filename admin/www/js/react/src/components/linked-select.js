var React = require('react/addons');
var EventFunnel = require('../mixins/event-funnel');
var Select = require('./react-select2');

module.exports = React.createClass({

  mixins: [EventFunnel],

  getDefaultProps(){
    return {
      url: '',
      list: [],
      listProperty: 'products',
      valueProperty: 'productId',
      labelProperty: 'description'
    };
  },

  getInitialState(){
    return {
      list: this.props.list
    }
  },

  render(){
    var props = this.props;
    // must be wrapped in div to avoid wonky formatting, with BS3 at least
    return <Select onFunnelEvent={this.toFunnel}
      selectedId={props.selectedId}
      placeholder={props.placeholder}
      className={this.props.className}
      style={this.props.style}
      list={_.map(this.state.list, item => ({ value: item[props.valueProperty], label: item[props.labelProperty]}))}
    />;
  },

  componentWillMount(){
    if (this.props.url){
      $.get(this.props.url, body => {
        this.setState({ list: _.sortBy(body[this.props.listProperty], this.props.labelProperty) });
      });
    }
  }

});
