var React = require('react/addons');

module.exports = React.createClass({

  getDefaultProps(){
    return {
      hoverElement: <i style={{ color: '#c0392b' }} className="fa fa-times-circle pull-right" />,
      defaultElement: <i className='fa fa-times pull-right' />,
      onMouseEnter(){}, onMouseLeave(){}
    };
  },

  getInitialState(){
    return {
      hovering: false
    };
  },

  render(){
    let { hoverElement, defaultElement, children, ...otherProps } = this.props;

    otherProps.onMouseEnter = this._onMouseEnter;
    otherProps.onMouseLeave = this._onMouseLeave;

    if (this.state.hovering){
      return React.addons.cloneWithProps(
        hoverElement,
        otherProps
      );
    } else {
      return React.addons.cloneWithProps(
        defaultElement,
        otherProps
      );
    }
  },

  _onMouseEnter(e){
    this.props.onMouseEnter(e);
    this.setState({ hovering: true });
  },

  _onMouseLeave(e){
    this.props.onMouseLeave(e);
    this.setState({ hovering: false });
  }

});
