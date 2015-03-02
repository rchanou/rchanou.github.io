var React = require('react/addons');

module.exports = React.createClass({
  getDefaultProps(){
    var url = '/admin/mobileApp/images/update';
    if (window.location.hostname === '192.168.111.29'){
      url = '/admin/www/mobileApp/images/update';
    }

    return {
      accept: undefined,
      url,
      className: 'btn btn-info',
      defaultValue: 'Upload Icon',
      onUpload(){}, onUploadStart(){}, onChange(){}, onError(){}
    };
  },

  render(){
    return <span>
      <input type='file' ref='uploader' accept={this.props.accept}
        style={{ visibility: 'hidden', width: 0, height: 0, position: 'absolute', display: 'none' }}
        onChange={e => {
          if (e.target.files.length == 0){
            return;
          }

          var file = e.target.files[0];

          var data = new FormData();
          var uploadTime = new Date().valueOf();
          var ext = file.name.substr(file.name.lastIndexOf('.') + 1);
          var fileName = (this.props.fileName || uploadTime) + '.' + ext;
          data.append('filename', fileName);
          data.append('image', file);

          this.props.onUploadStart({ fileName });
          $.ajax({
            type: 'POST',
            url: this.props.url,
            cache: false,
            contentType: false,
            processData: false,
            data
          })
          .then(
            res => {
              this.props.onUpload({ fileName });
              this.props.onChange({
                fileName,
                props: this.props
              });
            },
            res => {
              console.log('upload error', res);
              this.props.onError({
                fileName,
                props: this.props
              });
            }
          );
        }}
      />

      <input className={this.props.className} style={this.props.style}
        onClick={() => { $(this.refs.uploader.getDOMNode()).click(); }}
        defaultValue={this.props.value || this.props.defaultValue}
      />
    </span>;
  }
});
