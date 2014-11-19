// mixin providing event-handling sugar based on an opinionated way of handling React component parent-child communication

module.exports = {

  getDefaultProps(){
    return { onFunnelEvent(){} };
  },

  toFunnel(event, ...other){	
		this.props.onFunnelEvent.apply(this, [event, this.props, this.state].concat(other));
    //this.props.onFunnelEvent(event, this.props, this.state);
  },

  funnelJQueryEvents(...events){
    events.forEach(
      event => {
        $(this.getDOMNode()).on(
          event, e => { this.toFunnel(e); }
			  );
    });
  }

}