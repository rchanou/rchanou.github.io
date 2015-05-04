// this is the newer version

var React = require('react');

module.exports = React.createClass({

  render(){

    return <input {...this.props} />;

  },

  getDefaultProps(){
    return {
      options: {
        checkboxClass: 'icheckbox_flat-blue',
        radioClass: 'iradio_flat-blue'
      },
      type: 'checkbox',
      onEvent(){}, onChange(){}
    };
  },

  componentDidMount(){
    this.$me = $(this.getDOMNode());

    this.$me.iCheck(this.props.options);
    this.$me.iCheck('update');

    var events = ['ifIndeterminate', 'ifChecked', 'ifUnchecked'];

    events.forEach(event => {
      this.$me.on(event, e => {
        this.props.onEvent(e);
      });
    });
  },

  componentDidUpdate(prevProps){
    if (prevProps.checked !== this.props.checked){
      this.$me.iCheck('update');
    }
  },

  componentWillUnmount(){
    this.$me.iCheck('destroy');
  }

});
