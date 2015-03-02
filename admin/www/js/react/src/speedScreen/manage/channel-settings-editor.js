var React = require('react/addons');
var _ = require('lodash');

var Select = require('../../components/react-select2');
var ICheck = require('../../components/icheck.js');

module.exports = React.createClass({

  fieldsToSave: ['screenTemplateName', 'showScoreboard', 'postRaceIdleTime', 'trackId'],

  getDefaultProps(){
    return {
      channel: {},
      onSave(){},
      onDelete(){},
      onError(){}
    };
  },

  getInitialState(){
    var state = {};
    this.fieldsToSave.forEach(field => {
      state[field] = this.props.channel[field];
    });

    return state;
  },

  render(){
    return <form className='form-horizontal' style={{ overflowX: 'visible' }}>

      <div className='form-group'>
        <label className='control-label col-sm-3 col-lg-2'>Channel Name</label>
        <div className='col-sm-9 col-lg-10'>
          <input className='form-control' ref='screenTemplateName'
            defaultValue={this.state.screenTemplateName}
            onClick={e => {
              if (this.state.screenTemplateName === '(untitled)'){
                this.refs.screenTemplateName.getDOMNode().select();
              }
            }}
            onChange={e => {
              this.setState({ screenTemplateName: e.target.value });

              $('[href=#panel_tab4_channel' + this.props.channel.screenTemplateId + ']')
              .text(this.props.channel.screenTemplateId + '. ' + e.target.value);

              $('#panel_tab4_channel' + this.props.channel.screenTemplateId).find('h2')
              .text(this.props.channel.screenTemplateId + '. ' + e.target.value);
            }}
          />
          <span className='help-block text-left'>
            This is simply for identification purposes and is not displayed anywhere on the screen.
          </span>
        </div>
      </div>

      <div className='form-group'>
        <label className='control-label col-sm-3 col-lg-2'>Show Scoreboard</label>
        <div className='col-sm-9 col-lg-10'>
          <ICheck checked={this.state.showScoreboard}
            onFunnelEvent={e => {
              if (e.type === 'ifChecked'){
                this.setState({ showScoreboard: true });
              } else if (e.type === 'ifUnchecked'){
                this.setState({ showScoreboard: false });
              }
            }}
          />
          <span className='help-block text-left'>
            If checked, when a race is running for the selected track, a scoreboard will be displayed on this channel.
          </span>
        </div>
      </div>

      <div className='form-group'>
        <label className='control-label col-sm-3 col-lg-2'>Post Race Idle Time (seconds)</label>
        <div className='col-sm-9 col-lg-10'>
          <input className='form-control' ref='postRaceIdleTime' type='number'
            defaultValue={this.state.postRaceIdleTime}
            onClick={() => {
              if (!this.state.postRaceIdleTime){
                this.refs.postRaceIdleTime.getDOMNode().select();
              }
            }}
            onChange={e => {
              this.setState({ postRaceIdleTime: ~~e.target.value });
            }}
          />
          <span className='help-block text-left'>
            If "Show Scoreboard" is checked, this determines the number of seconds the scoreboard will
             remain on screen after a race is finished. After this, the non-scoreboard lineup will resume.
          </span>
        </div>
      </div>

      <div className='form-group'>
        <label className='control-label col-sm-3 col-lg-2'>Track</label>
        <div className='col-sm-9 col-lg-10'>
          <Select style={{ width: '100%' }}
            list={this.props.trackList}
            selectedId={this.state.trackId}
            allowClear={false}
            placeholder='(Not Set!)'
            onFunnelEvent={e => {
              if (e.added){
                this.setState({ trackId: e.val });
              }
            }}
          />
          <span className='help-block text-left'>
            If "Show Scoreboard" is checked and a race is running on this track, the scoreboard slide will appear on this channel.
          </span>
        </div>
      </div>

      <div className='form-actions col-lg-6'>
        <button
          className='btn btn-info'
          onClick={e => {
            e.preventDefault();

            var data = {};
            this.fieldsToSave.forEach(field => {
              data[field] = this.state[field];
            });

            $.ajax({
              type: 'PUT',
              url: config.apiURL + 'screenTemplate/' + this.props.channel.screenTemplateId + '?key=' + config.privateKey,
              data
            })
            .then(
              res => {
                this.props.onSave(
                  _.extend(
                    _.omit(this.props.channel, _.isFunction),
                    this.state
                  )
                );
              },
              res => {
                this.props.onError();
              }
            );
          }}
        >
          Save Settings
        </button>
      </div>

      <div className='form-actions col-lg-6'>
        <button
          className='btn btn-dark-red'
          onClick={e => {
            e.preventDefault();

            var confirmed = window.confirm('Are you sure you want to delete this channel?');
            if (confirmed){
              $.ajax({
                type: 'DELETE',
                url: config.apiURL + 'screenTemplate/' + this.props.channel.screenTemplateId + '?key=' + config.privateKey
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
          Delete Channel
        </button>
      </div>
    </form>;
  },

  componentDidMount(){
    var resizeEditor = () => {
      $(this.getDOMNode()).width($(window).width() - $(this.getDOMNode()).offset().left - 100);
    };
    $(window).resize(resizeEditor);
    $(document).on( 'shown.bs.tab', 'a[data-toggle="tab"]', resizeEditor);
  },

  componentDidUpdate(prevProps, prevState){
    for (var fieldName in this.state){
      if (this.refs[fieldName] && this.refs[fieldName].getDOMNode().value !== this.state[fieldName]){
        this.refs[fieldName].getDOMNode().value = this.state[fieldName];
      }
    }
  }

});
