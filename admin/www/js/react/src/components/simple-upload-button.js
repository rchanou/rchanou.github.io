var React = require('react');
var hash = require('json-hash');

module.exports = React.createClass({
  getDefaultProps(){
    return {
      accept: undefined,
      url: '/admin/mobileApp/images/update',
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

          var fileName = this.props.fileName;
          if (!fileName){
            // ghetto hash
            var filePropertyTextForHash = '';
            for (var key in file){
              if (typeof file[key] === 'string' || typeof file[key] === 'number'){
                filePropertyTextForHash += file[key];
              }
            }
            fileName = hash.digest(filePropertyTextForHash);
          }
          var ext = file.name.substr(file.name.lastIndexOf('.') + 1);
          fileName += '.' + ext;

          var data = new FormData();
          data.append('image', file);
          data.append('filename', fileName);

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
