var React = require('react/addons');

module.exports = React.createClass({

  timeout: null,

  getDefaultProps (){
		return {
      rate: 0.01, 
      delay: 3000, 
      onFadeComplete (){}, 
      fading: false
    };
  },

  getInitialState (){
    return { fading: false, opacity: 1 };
  },
	
  render (){
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
	
  componentDidMount (){
	  this.resetTimeout();
	},
	
  resetTimeout (){
	  if (this.timeout){
	    clearTimeout(this.timeout);
	  }		
	  this.timeout = setTimeout(
			_ => {
			  if (this.isMounted()){
					this.setState({ fading: true });
        }
      },
      this.props.delay
		);		
  },

  componentDidUpdate (){
    if (this.state.fading){
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
  }

});