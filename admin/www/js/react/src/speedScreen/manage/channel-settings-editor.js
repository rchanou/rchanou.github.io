var React = require('react');
var _ = require('lodash');

var SmartInput = require('../../components/smart-input');


module.exports = React.createClass({

  getDefaultProps(){
    return {
      channel: {},
      onSave(){},
      onDelete(){},
      onError(){}
    };
  },

  getInitialState(){
    var channelData = this.props.channel.channelData;

    return {
      channelNumber: this.props.channel.channelNumber,
      name: channelData.name
    };
  },

  render(){
    return <form className='form-horizontal' style={{ overflowX: 'visible' }}>

      <div className='form-group'>
        <label className='control-label col-sm-3 col-lg-2'>Channel Name</label>
        <div className='col-sm-9 col-lg-10'>
          <SmartInput className='form-control' ref='name'
            defaultValue={this.state.name}
            onClick={e => {
              if (this.state.name === '(untitled)'){
                this.refs.name.getDOMNode().select();
              }
            }}
            onChange={e => {
              this.setState({ name: e.target.value, dirty: true });
            }}
          />
        </div>
      </div>

      <div className='form-group'>
        <label className='control-label col-sm-3 col-lg-2'>Channel Number</label>
        <div className='col-sm-9 col-lg-10'>
          <SmartInput className='form-control' type='number' ref='channelNumber'
            defaultValue={this.state.channelNumber} min={0}
            onClick={e => {
              if (this.state.channelNumber == 0){
                this.refs.channelNumber.getDOMNode().select();
              }
            }}
            onChange={e => {
              this.setState({ channelNumber: e.target.value, dirty: true });
            }}
          />
        </div>
      </div>

      <div className='form-group'>
        <div className='col-sm-3 col-lg-2' />
        <div className='col-sm-9 col-lg-10'>
          <button disabled={!this.state.dirty}
            className='btn btn-info'
            onClick={e => {
              e.preventDefault();

              $.get(config.apiURL + 'speedscreenchannels/' + this.props.channel.channelId + '.json?key=' + config.privateKey)
              .then(res => {
                var dataBeforeSave = JSON.parse(res.channelData);
                dataBeforeSave.name = this.state.name;

                var newChannel = res;
                newChannel.channelNumber = this.state.channelNumber;
                newChannel.channelData = JSON.stringify(dataBeforeSave);

                var newChannelWithParsedData = _.cloneDeep(res);
                newChannelWithParsedData.channelNumber = this.state.channelNumber;
                newChannelWithParsedData.channelData = dataBeforeSave;

                $.ajax({
                  type: 'PUT',
                  url: config.apiURL + 'speedscreenchannels/' + this.props.channel.channelId + '?key=' + config.privateKey,
                  data: newChannel
                })
                .then(
                  res => {
                    this.setState({ dirty: false });

                    var viewChannel = window.viewChannels['panel_tab4_channel' + this.props.channel.channelId];
                    viewChannel.url = 'http://' + location.hostname + (location.port ? ':' + location.port: '')
                                 + '/cs-speedscreen/#/' + this.state.channelNumber;
                    viewChannel.elems.urlInput.val(viewChannel.toString());
                    viewChannel.elems.channelPreviewLink.attr('href', viewChannel.toString());

                    $('[href=#panel_tab4_channel' + this.props.channel.channelId + ']')
                    .text(this.state.name + ' (#' + (this.state.channelNumber || this.props.channel.channelId) + ')');

                    $('#panel_tab4_channel' + this.props.channel.channelId).find('h2')
                    .text(this.state.name + ' (#' + (this.state.channelNumber || this.props.channel.channelId) + ')');

                    $('#panel_tab2_deploy_channel' + this.props.channel.channelId)
                    .find('input[name=channelNumber]').val(this.state.channelNumber);

                    this.props.onSave(newChannelWithParsedData);
                  },
                  res => {
                    this.props.onError(res);
                  }
                );
              });
            }}
          >
            Save Changed Settings
          </button>
        </div>

        <div className='col-xs-12'><br/><br/></div>
        <div className='col-sm-3 col-lg-2' />
        <div className='col-sm-9 col-lg-10'>
          <button
            className='btn btn-dark-red'
            onClick={e => {
              e.preventDefault();

              var confirmed = window.confirm('Are you sure you want to delete this channel and its lineups?');
              if (confirmed){
                $.ajax({
                  type: 'DELETE',
                  url: config.apiURL + 'speedscreenchannels/' + this.props.channel.channelId + '?key=' + config.privateKey
                })
                .then(
                  res => {
                    this.props.onDelete();
                  },
                  res => {
                    this.props.onError();
                  }
                );
              }
            }}
          >
            <i className='fa fa-trash-o' />  Delete Channel
          </button>
        </div>
      </div>
    </form>;
  },

  componentDidMount(){
    this.resizeEditor = () => {
      $(this.getDOMNode()).width($(window).width() - $(this.getDOMNode()).offset().left - 100);
    };
    $(window).resize(this.resizeEditor);
    $(document).on( 'shown.bs.tab', 'a[data-toggle="tab"]', this.resizeEditor);
  },

  componentDidUpdate(prevProps, prevState){
    for (var fieldName in this.state){
      if (this.refs[fieldName] && this.refs[fieldName].getDOMNode().value !== this.state[fieldName]){
        this.refs[fieldName].getDOMNode().value = this.state[fieldName];
      }
    }
  },

  componentWillUnmount(){
    $(window).off('resize', this.resizeEditor);
    $(document).off( 'shown.bs.tab', 'a[data-toggle="tab"]', this.resizeEditor);
  }

});
