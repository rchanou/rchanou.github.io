var React = require('react/addons');

module.exports.Component = React.createClass({

  getDefaultProps(){
    return {
      element: 'input',
      type: 'submit',
      className: 'btn btn-info',
      value: 'Upload',
      fileSelectTriggerEvents: ['onClick'],
      fileInputProps: {},
      onEvent(){}
    };
  },

  render(){
    let { children, fileInputProps, fileSelectTriggerEvents, ...otherProps } = this.props;

    this.props.fileSelectTriggerEvents.forEach(event => {
      otherProps[event] = () => {
        this.refs.uploader.getDOMNode().click();
      };
    });

    if (!children){
      children = [];
    }

    children.push(
      <input type='file' ref='uploader' key='__uploader'
        {...fileInputProps}
        style={{ visibility: 'hidden', width: 0, height: 0, position: 'absolute', display: 'none' }}

        onChange={event => {
          this.props.onEvent({ type: 'select', files: event.target.files });
        }}
      />
    );

    return React.createElement(
      this.props.element,
      otherProps,
      children
    );
  }

});
