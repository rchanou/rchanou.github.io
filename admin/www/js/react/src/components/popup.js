var React = require('react/addons');

var FadeOut = React.createClass({

  timeout: null,

  getDefaultProps(){
		return {
      rate: 0.01,
      delay: 3000,
      //fading: false,
      onFadeComplete(){}//, onClick(){}
    };
  },

  getInitialState(){
    return { fading: false, opacity: 1 };
  },

  render(){
	  var change = this.props.style?
			{ $merge: { opacity: this.state.opacity } }
			: { $set: { opacity: this.state.opacity } };

	  var newProps = React.addons.update(
      this.props,
      { style: change }
    );

	  return React.createElement(
      this.props.element || 'div',
      newProps,
      this.props.children
    );
	},

  componentDidMount(){
	  this.resetTimeout();
    if (typeof jQuery !== 'undefined'){
		  $(this.getDOMNode()).tooltip({ title: 'Click to dismiss.', placement: 'bottom' });
    }
	},

  resetTimeout(){
	  if (this.timeout){
	    clearTimeout(this.timeout);
	  }
		if (this.props.className.indexOf('alert-success') !== -1){
			this.timeout = setTimeout(
				_ => {
					if (this.isMounted()){
						this.setState({ fading: true });
					}
				},
				this.props.delay
			);
		}
  },

  componentDidUpdate(prevProps, prevState){
		if ((this.props.className.indexOf('alert-danger') !== -1 && prevProps.className.indexOf('alert-danger') === -1)
				|| (this.props.className.indexOf('alert-warning') !== -1 && prevProps.className.indexOf('alert-warning') === -1)){
			this.setState(this.getInitialState());
    } else if (this.state.fading){
      if (this.state.opacity <= 0){
        this.props.onFadeComplete(this.props);
      } else {
        requestAnimationFrame(_ => {
          if (this.isMounted()){
            this.setState({
              opacity: this.state.opacity - this.props.rate
            });
          }
        });
      }
    }
  },

	componentWillUnmount(){
    if (typeof jQuery !== 'undefined'){
		  $(this.getDOMNode()).tooltip('destroy');
    }
	}
});


var Popup = React.createClass({

  getDefaultProps(){
    return {
      message: 'Something happened!',
      alertClass: 'alert-info',
      onClick(){}, onFadeComplete(){}, onDone(){}
    };
  },

  render(){
    let { element, alertClass, message, ...otherProps } = this.props;

    return <FadeOut
      element = 'div'
      onFadeComplete = {this.handleComplete}
      onClick = {this.handleClick}
      style = {
        { position: 'fixed', top: 0, left: 0, width: '100%', cursor: 'pointer', zIndex: 9999, textAlign: 'center' }
      }
      className = {'alert ' + this.props.alertClass}
      dangerouslySetInnerHTML = {{__html: this.props.message}}
      {...otherProps}
    />;
  },

  handleClick(e){
    this.props.onClick(e);
    this.props.onDone(e);
  },

  handleComplete(e){
    this.props.onFadeComplete(e);
    this.props.onDone(e);
  }

});

module.exports = Popup;
