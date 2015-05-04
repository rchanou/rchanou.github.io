var React = require('react');

module.exports = React.createClass({

  propTypes: {
    list: React.PropTypes.array,
    options: React.PropTypes.object,
    onEvent: React.PropTypes.func
  },

  getDefaultProps(){
    return {
      list: [], // list: [{value: 1, label: 'Apples'}, 'Bananas', 'Cherries'],
      selectedId: null,
      options: {},
      onEvent(){}
    };
  },

  getInitialState(){
    return { open: false };
  },

  render(){
    let { list, ...otherProps, children } = this.props;

    var optionsFromList = list.map(item =>
      <option key={item.value} value={item.value}>
        {item.label}
      </option>
    );

    return <select {...otherProps}>
      <option key={-1} value={null} />
      {optionsFromList}
      {children}
    </select>;
  },

  componentDidMount(){
    this.$me = $(this.getDOMNode());

    var data = this.props.list.map(item => (
      { id: item.value, text: item.label }
    ));

    this.$me.select2(this.props.options)
    .on('change', this.props.onEvent)
    .on('select2-open', () => 	{ this.setState({ open: true }) })
    .on('select2-close', () => {	this.setState({ open: false }); });

    this.$me.select2('val', this.props.selectedId);//.trigger('change');
  },

  componentDidUpdate(prevProps){
    if (this.state.open){
      return;
    }

    if (prevProps.list.length != this.props.list.length){ // TODO: make list equality checking more robust?
      this.$me.select2(this.props.options);
    }

    if (prevProps.selectedId !== this.props.selectedId){
      this.$me.select2('val', this.props.selectedId);//.trigger('change');
    }
  },

  componentWillUnmount(){
    this.$me.select2('destroy');
  }
});
