/*
  - Drop-in replacement for standard input tag
  - Enables real-time validation leveraging the native HTML5 API
  - Reverts invalid input to previous value on blur

  EXAMPLE: <SmartInput type='number' required min={0} />
          (Look, Ma! No non-standard attributes!)

  (note: IE <=11 will not show pop-up validation error messages, but will revert invalid values on blur)
*/

var React = require('react');
var _ = require('lodash');
var uuid = require('random-uuid-v4');

module.exports = React.createClass({

  render(){
    let { children, onChange, ...otherProps } = this.props;

    return <input
      form={this.formId}
      {...otherProps}
      onFocus={this._onFocus}
      onChange={this._onInput}
      onBlur={this._onBlur}
      onInput={_.noop || this._onInput}
    />;
  },

  getDefaultProps(){
    return {
      debounce: 1000,
      onInput(){}, onChange(){}, onBlur(){}
    };
  },

  getInitialState(){
    return {
      formId: uuid()
    };
  },

  componentWillMount(){
    this.formId = uuid();
    this.prevValid = true;

    this.debouncedSubmit = _.debounce(this.submit, this.props.debounce);
  },

  componentDidMount(){
    this.formNode = document.createElement('form');
    this.formNode.id = this.formId;
    this.formNode.style.display = 'none';

    this.submitNode = document.createElement('input');
    this.submitNode.type = 'submit';
    this.submitPreventer = this.formNode.addEventListener('submit', e => {
      e.preventDefault();
    });

    this.formNode.appendChild(this.submitNode);

    document.body.appendChild(this.formNode);
  },

  componentWillUnmount(){
    document.body.removeChild(this.formNode);
  },

  submit(){
    $(this.submitNode).click();
  },

  fireValidation(){
    if ("createEvent" in document) {
      var evt = document.createEvent("HTMLEvents");
      evt.initEvent("input", true, true);
      this.getDOMNode().dispatchEvent(evt);
    } else {
      this.getDOMNode().fireEvent("oninput");
    }
  },

  _onFocus(e){
    this.valueOnFocus = e.target.value;
  },

  _onInput(e){
    if (this.prevValid && !e.target.validity.valid){
      if (this.valueOnFocus === ''){
        this.debouncedSubmit();
      } else {
        this.submit();
      }
    } else {
      if (this.props.type === 'number'){
        this.submit();
      }

      this.props.onInput(e);
      this.props.onChange(e);
    }

    this.prevValid = e.target.validity.valid;

  },

  _onBlur(e){
    if (!e.target.validity.valid){
      e.target.value = this.valueOnFocus;
      this.fireValidation();
    } else if (this.props.type === 'number' && !/^\-?[0-9.,]*$/.test(e.target.value)){ //IE11 hack
      e.target.value = this.valueOnFocus;
      this.fireValidation();
    }

    this.props.onBlur(e);
  }

});
