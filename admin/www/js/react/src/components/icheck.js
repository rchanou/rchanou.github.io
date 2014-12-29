// React wrapper for iCheck jQuery plugin

var React = require('react/addons');
var EventFunnel = require('../mixins/event-funnel');

module.exports = React.createClass({

  mixins: [EventFunnel],

  render(){
		return <input defaultChecked={this.props.checked} type='checkbox' />;
  },

  componentDidMount(){
    $(this.getDOMNode()).iCheck({
      checkboxClass: 'icheckbox_flat-blue',
      radioClass: 'iradio_flat-blue'
    });
    this.setFromProps();
  },

  componentDidUpdate(prevProps, prevState){
    this.setFromProps();
  },

	setFromProps(){
    $(this.getDOMNode()).off();

	  if (this.props.checked === null){
	    $(this.getDOMNode()).iCheck('indeterminate');
	  } else if (this.props.checked) {
	    $(this.getDOMNode()).iCheck('check');
	  } else {
	    $(this.getDOMNode()).iCheck('uncheck');
	  }

    this.funnelJQueryEvents('ifIndeterminate', 'ifChecked', 'ifUnchecked');
	},

	componentWillUnmount(){
	  $(this.getDOMNode()).iCheck('destroy');
	}

});
