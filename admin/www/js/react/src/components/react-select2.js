var React = require('react/addons');
var EventFunnel = require('../mixins/event-funnel');

module.exports = React.createClass({
  mixins: [EventFunnel],

  propTypes: {
    list: React.PropTypes.array,
    placeholder: React.PropTypes.string,
    allowClear: React.PropTypes.bool
  },

  getDefaultProps(){
    return {
      list: [], // list: [{value: 1, label: 'Apples'}, 'Bananas', 'Cherries'],
      selectedId: null,
      placeholder: '(any)',
      allowClear: true
    };
  },

  getInitialState(){
    return { open: false };
  },

  render(){
    var optionsFromList = this.props.list.map(item =>
      <option key={item.value} value={item.value}>
      {item.label}
      </option>
    );

    return <select className={this.props.className} style={this.props.style}>
    <option key={-1} value={null} />
    {optionsFromList}
    {this.props.children}
    </select>;
  },

  getSelectOptions(){
    var { list, selectedId, ...options } = this.props;  // omit list and selectedId
    return options;
  },

  componentDidMount(){
    var data = this.props.list.map(item => (
      { id: item.value, text: item.label }
    ));

    $(this.getDOMNode())
    .select2(this.getSelectOptions())
    .on('change', this.toFunnel)
    .on('select2-open', () => 	{ this.setState({ open: true }) })
    .on('select2-close', () => {	this.setState({ open: false }); });

    this.setFromProps();
  },

  componentDidUpdate(prevProps){
    if (prevProps.list.length !== this.props.list.length && !this.state.open){ // TODO: make list equality checking more robust?
      $(this.getDOMNode()).select2(this.getSelectOptions());
    }
    if (!this.state.open && prevProps.selectedId != this.props.selectedId){
      this.setFromProps();
    }
  },

  setFromProps(){
    $(this.getDOMNode()).val(this.props.selectedId).trigger('change');
  },

  componentWillUnmount(){
    $(this.getDOMNode()).select2('destroy');
  }
});
