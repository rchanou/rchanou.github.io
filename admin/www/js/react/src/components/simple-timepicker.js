var React = require('react/addons');
var moment = require('moment');

module.exports = React.createClass({

  getDefaultProps(){
    return { value: '00:00', onChange(){}, allowNone: true };
  },

  render(){
    var optionNodes = [];

    for (var i = 0; i < 24; i = i + 0.5){
      var mo = moment({ hours: i, minutes: (i - Math.floor(i)) * 60 });

      var locale = this.props.locale || (navigator? (navigator.language || 'en-us'): 'en-us');

      optionNodes.push(
        <option value={mo.format('HH:mm')} key={i}>
          {mo.toDate().toLocaleTimeString(this.props.locale, {
            hour: 'numeric', minute: 'numeric'
          })}
        </option>
      );
    };

    if (/^[0-2]?[0-9]:[0-5][0-9]$/.test(this.props.value)){
      var value = this.props.value;
    } else {
      var value = 'none';
    }

    return <select
      value={value}
      className='form-control'
      onChange={this._onChange}
    >
      {this.props.allowNone && <option value='none' key='none'>(None)</option>}
      {optionNodes}
    </select>;
  },

  _onChange(e){
    this.props.onChange({ value: e.target.value });
  },

  componentDidUpdate(prevProps, prevState){
    if (prevProps.value === this.props.value) return;

    //this.getDOMNode().value = this.props.value;

    /*console.log('datepicker updated');
    console.log(prevProps, this.props);
    console.dir(this.getDOMNode());*/
  }

});
