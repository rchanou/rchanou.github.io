/*
  React wrapper for BS3 tooltip/popover
*/

var React = require('react');

module.exports = React.createClass({

  getDefaultProps(){
    var props = { popover: false };

    return props;
  },

  getInitialState(){
    return {
      show: false
    };
  },

  render(){
    let { element, children, popover, tooltipOptions, ...otherProps } = this.props;

    otherProps.onMouseEnter = this._mouseOver;
    otherProps.onMouseLeave = this._mouseOut;

    otherProps['data-toggle'] = this.props.popover? 'popover': 'tooltip';

    return React.createElement(
      element,
      otherProps,
      children
    );
  },

  _mouseOver(){
    this.setState({ show: true });
  },

  _mouseOut(){
    this.setState({ show: false });
  },

  componentDidMount(){
    this.$me = $(this.getDOMNode());

    this.initToolTip();
  },

  initToolTip(){
    var opts = this.props.tooltipOptions || {};

    if (!opts.template){
      opts.template =
        '<div class="tooltip" role="tooltip" style="z-index:9999999;">' +
        '  <div class="tooltip-arrow"></div>' +
        '  <div class="tooltip-inner"></div>' +
        '</div>';
    }

    this.$me[this.props.popover? 'popover': 'tooltip'](opts);
  },

  componentDidUpdate(prevProps, prevState){
    if (prevProps.title !== this.props.title){
      this.$me[this.props.popover? 'popover': 'tooltip']('destroy');
    }

    if (prevState.show !== this.state.show){
      if (this.state.show){
        this.initToolTip();
        this.$me[this.props.popover? 'popover': 'tooltip']('show');
      } else {
        this.$me[this.props.popover? 'popover': 'tooltip']('destroy');
      }
    }
  },

  componentWillUnmount(){
    this.$me[this.props.popover? 'popover': 'tooltip']('destroy');
  }

});
