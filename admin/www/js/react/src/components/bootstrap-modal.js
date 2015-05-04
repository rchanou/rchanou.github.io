var React = require('react');

module.exports = React.createClass({

  getDefaultProps(){
    return {
      show: false,
      opts: {
        backdrop: 'static',
        show: false
      },
      onEvent(){}
    };
  },

  render(){
    let { element, children, ...otherProps } = this.props;

    return React.createElement(element, otherProps, children);
  },

  componentDidMount(){
    this.$me = $(this.getDOMNode());

    var events = ['show.bs.modal', 'shown.bs.modal', 'hide.bs.modal', 'hidden.bs.modal', 'loaded.bs.modal'];

    events.forEach(event => {
      this.$me.on(event, e => {
        this.props.onEvent(e);
      });
    });

    this.$me.modal(this.props.opts);
    if (this.props.show){
      this.$me.modal('show');
    } else {
      this.$me.modal('hide');
    }
  },

  componentDidUpdate(){
    if (this.props.show){
      this.$me.modal('show');
    } else {
      this.$me.modal('hide');
    }
  }

});
