var React = require('react/addons');
var EventFunnel = require('../mixins/event-funnel');
var LinkedSelect = require('./linked-select');

module.exports = React.createClass({

  mixins: [EventFunnel],

  render(){
    return <LinkedSelect
      url={this.props.config.apiURL + 'tracks/index.json?key=' + this.props.config.apiKey}
      listProperty='tracks'
      valueProperty='id'
      labelProperty='name'
      className={this.props.className}
      style={this.props.style}
      selectedId={this.props.selectedId}
      onFunnelEvent={this.toFunnel}
    />;
  }

});
