// DEPENDENCIES
var React = require('react/addons');
var csp = require('js-csp');
var _ = require('lodash');
var moment = require('moment');


// IMPORTED COMPONENTS

var ICheck = require('../../components/icheck');
var Popup = require('../../components/popup');
var Select = require('../../components/react-select2');
var Uploader = require('../../components/simple-upload-button');


// GLOBAL CONSTANTS

//var IMAGE_TYPE_ID = 2;
var NEW_SCOREBOARD_TYPE_ID = 21;
var VIDEO_URL_HTML_TYPE_ID = 18;

var DB_SLIDE_TYPE_MAP = {
  eventScreen: 'Event Screen',
  image: 'Image',
  lastWinnerwithPicture: 'Last Winner with Picture',
  mostImprovedRPMOfMonth: 'Most Improved RPM of Month',
  mostImprovedRPMOfYear: 'Most Improved RPM Of Year',
  nextRacers: 'Next Racers',
  nextNextRacers: 'Next Next Racers',
  newScoreboard: 'New Scoreboard',
  previousPreviousRaceResults: 'Previous Previous Race Results',
  previousRaceResults: 'Previous Race Results',
  schedule: 'Schedule',
  topProskill: 'Top Pro Skill',
  topTimeOfDay: 'Top Time of Day',
  topTimeOfDayWithPicture: 'Top Time of Day with Picture',
  topTimeOfMonth: 'Top Time of Month',
  topTimeOfYear: 'Top Time of Year',
  topTimeOfWeek: 'Top Time of Week',
  text: 'Text',
  video: 'Video',
  url: 'URL or HTML',
  html: 'HTML or URL'
};

var PSEUDO_INFINITE = 86400000;

var IMAGE_DIRECTORY = (window.location.hostname === '192.168.111.29'? 'https://vm-122.clubspeedtiming.com': '')
  + '/sp_admin/ScreenImages/';

var VIDEO_DIRECTORY = (window.location.hostname === '192.168.111.29'? 'https://vm-122.clubspeedtiming.com': '')
  + '/assets/videos/';


// HELPER FUNCTIONS

function getTitleFormat(text){ // e.g. converts "myVar123" to "My Var 123"
  var result = text.replace( /([A-Z]|[0-9]+)/g, " $1" );
  var words = result.split(' ');
  words = words.map(word => {
    var upperWord = word.toUpperCase();
    if (upperWord === 'ID' || upperWord === 'URL' || upperWord === 'HTML'){
      return upperWord;
    } else {
      return word;
    }
  });
  result = words.join(' ');
  return result.charAt(0).toUpperCase() + result.slice(1);
};

function isBoolish(val){
  return val === false || val === true || val === 'false' || val === 'true';
};

function isStringish(val){
  return (isNaN(val) || val === '') && !isBoolish(val);
};

function findBootstrapEnvironment(){
  var envs = ["xs", "sm", "md", "lg"],
  doc = window.document,
  temp = doc.createElement("div");

  doc.body.appendChild(temp);

  for (var i = envs.length - 1; i >= 0; i--) {
    var env = envs[i];

    temp.className = "hidden-" + env;

    if (temp.offsetParent === null) {
      doc.body.removeChild(temp);
      return env;
    }
  }
  return "";
}


// COMPONENTS

var SlideForm = React.createClass({
  getDefaultProps(){
    return {
      dragX: null,
      dragY: null,
      selectedColor: '#2ecc71',
      //height: 200,
      onMouseDown(){}, onTouchMove(){}, onOptionChange(){}, onMount(){}, onSelectChange(){}
    }
  },

  render(){
    var containerStyle = {
      position: 'absolute',
      left: this.props.x,
      top: 0,
      overflowX: 'hidden',
      overflowY: 'visible',
      borderRadius: 2
    };

    if (this.props.dragging){
      containerStyle.zIndex = 9999;
    } else {
      containerStyle.zIndex = 0;
    }

    var optionElements = [];

    if (typeof this.props.typeId === 'undefined'){
      optionElements.push(<div className='alert alert-info'>
        This slide is here because <strong>Show Scoreboard</strong> is checked in the <strong>Channel Settings</strong> tab.&nbsp;
        <a style={{ cursor: 'pointer' }}
          onClick={e => {
            e.stopPropagation();
            $('[href=#panel_tab2_channelsettings_channel' + this.props.channelId + ']').tab('show');
          }}
        >
          Click here to edit or remove it.
        </a>
      </div>);
    }

    if (this.props.selected){
      var options = _(this.props.options).transform((result, optionValue, optionName) => {
        result.push({ name: optionName, value: optionValue });
      }, [])
      .sortBy('name')
      .value();

      options.forEach(option => {
        var optionName = option.name;
        var optionValue = option.value;
        var commonProps = {
          ref: optionName,
          onChange: () => {
            var changedOption = {};
            var val = this.refs[optionName].getDOMNode().value;
            if (optionName === 'original' || optionName === 'html'){
              if (/<(.*)>/.test(val) || /(.*)\:\/\//.test(val)){ // detected HTML or URL with protocol
                changedOption[optionName] = val;
              } else { // (assume "http" if detected URL without protocol, e.g. "google.com" instead of "http://google.com")
                changedOption[optionName] = 'http://' + val;
              }
            } else if (isBoolish(val)){
              changedOption[optionName] = (val == true || val === 'true');
            } else if (isStringish(val)){
              changedOption[optionName] = val;
            } else {
              changedOption[optionName] = ~~val;
            }
            this.props.onOptionChange(changedOption);
          }
        };

        var inputElement = null;
        if (optionName === 'html'){
          inputElement = <textarea
            className='form-control' style={{ width: '69%', height: 222 }}
            {...commonProps}
            ref={optionName}
            defaultValue={optionValue}
            onMouseDown={e => { e.stopPropagation(); }}
            onTouchStart={e => { e.stopPropagation(); }}
          />;
        } else if (optionName === 'trackId'){
          inputElement = <div
            style={{ marginRight: 0.1, width: '30%', display: 'inline' }}
            onMouseDown={e => { e.stopPropagation(); }}
            onTouchStart={e => { e.stopPropagation(); }}
          >
            <Select
              list={this.props.trackList}
              selectedId={optionValue}
              allowClear={false}
              style={{ width: '69%' }}
              onFunnelEvent={e => {
                if (e.added){
                  this.props.onOptionChange({
                    trackId: ~~e.val
                  });
                }
              }}
            />
          </div>;
        } else if (optionName === 'url'){
          if (this.props.typeId === 'url'){
            inputElement = <a
              href={this.props.options.original}
              target='_blank'
              onMouseDown={e => { e.stopPropagation(); }} onTouchStart={e => { e.stopPropagation(); }}
            >
              Go to linked page in new tab
            </a>;
          } else if (this.props.typeId === 'image'){
            inputElement = <div style={{ width: '100%', cursor: 'pointer' }}>
              <img
                src={(optionValue.indexOf('/') === -1? IMAGE_DIRECTORY: '') + optionValue}
                style={{ width: '100%' }}

                onMouseDown={e => {
                  if (e.button !== 0){
                    return;
                  }
                  e.stopPropagation();
                  e.preventDefault();
                  this.handleClickOrTouch(e);
                }}

                onTouchStart={e => {
                  e.preventDefault();
                  this.handleClickOrTouch(e.touches[0]);
                }}
              />
              <div style={{ width: '42%' }} onMouseDown={e => { e.stopPropagation(); e.preventDefault(); }}>
                <Uploader accept='image/*'
                  url={'/admin/' + (window.location.hostname === '192.168.111.29' && 'www/') + 'channel/images/update'}
                  fileName={'speedscreen-slide-' + this.props.id}
                  value='Change Image'
                  onUploadStart={ e => { this.props.onUploadStart(); }}
                  onUpload={e => {
                    this.props.onOptionChange({
                      url: e.fileName
                    });
                  }}
                  onError={e => {
                    this.props.onImageUploadError();
                  }}
                />
              </div>
            </div>;
          } else {
            return;
          }
        } else if (optionName === 'originalUrl'){ // for video type
          inputElement = <div style={{ width: '42%' }} onMouseDown={e => { e.stopPropagation(); e.preventDefault(); }}>
            <Uploader accept='video/*'
              url={'/admin/' + (window.location.hostname === '192.168.111.29' && 'www/') + 'channel/videos/update'}
              fileName={'speedscreen-slide-' + this.props.id}
              value='Change Video'
              onUploadStart={ e => { this.props.onUploadStart(); }}
              onUpload={e => {
                this.props.onOptionChange({
                  originalUrl: VIDEO_DIRECTORY + e.fileName
                });
              }}
              onError={e => {
                this.props.onVideoUploadError();
              }}
            />
          </div>;
        } else if (isBoolish(optionValue)){
          inputElement = <div style={{ display: 'inline' }}>
            <ICheck
              checked={optionValue || optionValue == 'true'}
              onFunnelEvent={e => {
                var changedOption = {};
                if (e.type === 'ifChecked'){
                  changedOption[optionName] = true;
                } else if (e.type === 'ifUnchecked'){
                  changedOption[optionName] = false;
                }
                this.props.onOptionChange(changedOption);
              }}
              ref={optionName}
            />
          </div>;
        } else {
          if (isStringish(optionValue)){

          } else {
            commonProps.type = 'number';
          }
          inputElement = <input
            className='form-control' style={{ width: '69%' }}
            defaultValue={optionValue}
            {...commonProps}
            ref={optionName}
            onMouseDown={e => { e.stopPropagation(); }}
            onTouchStart={e => { e.stopPropagation(); }}
          />
        }

        optionElements.push(
          <form
            key={optionName}
            className='form-inline col-xs-12'
            style={{ marginTop: '1em' }}
            onSubmit={e => { e.preventDefault(); }}
          >
            {optionName !== 'url' && optionName !== 'originalUrl' && <label className='control-label' style={{ width: '30%' }}>
              {getTitleFormat(optionName).replace(' ID', '').replace('Original', 'URL/HTML')}
            </label>}
            {inputElement}
          </form>
        );
      });
    } else {
      _.forOwn(this.props.options, (optionValue, optionName) => {
        if (optionName === 'html'){
          return;
        }

        if (optionName === 'trackId'){
          optionElements.push(
            <div
              key={optionName}
              className='col-xs-12'
            >
              {getTitleFormat(optionName).replace(' ID', '').replace('Original', 'URL/HTML')}:&nbsp;
              {_.find(this.props.trackList, { value: optionValue })?
                _.find(this.props.trackList, { value: optionValue }).label
                : <span style={{ color: 'red' }}>
                    Not set! Click the <strong>Channel Settings</strong> tab to set the track.
                  </span>}
            </div>
          );
        } else if (optionName.toUpperCase().indexOf('URL') !== -1){
          if (this.props.typeId === 'image'){
            optionElements.push(
              <div key={optionName} className='col-xs-12'>
                <img
                  key={optionName}
                  src={(optionValue.indexOf('/') === -1? IMAGE_DIRECTORY: '') + optionValue}
                  style={{ width: '100%' }}
                />
              </div>
            );
          }
        } else if (isBoolish(optionValue)){
          if (optionValue){
            optionElements.push(
              <div
                key={optionName}
                className='col-xs-12'
              >
                {getTitleFormat(optionName)}
              </div>
            );
          }
        } else if (optionName !== 'duration' && optionName !== 'postRaceIdleTime' && optionValue !== ''){
          optionElements.push(
            <div
              key={optionName}
              className='col-xs-12'
            >
              {getTitleFormat(optionName)}:&nbsp;{optionValue}
            </div>
          );
        }
      });
    }

    return <section
      style={containerStyle}

      onMouseDown={e => {
        if (e.button !== 0 || typeof this.props.typeId === 'undefined'){
          return;
        }
        e.preventDefault();
        this.handleClickOrTouch(e);
      }}

      onTouchStart={e => {
        e.preventDefault();
        this.handleClickOrTouch(e.touches[0]);
      }}

      onTouchMove={e => {
        e.preventDefault();
      }}
    >
      <div
        className={'container form' + (typeof this.props.typeId !== 'undefined'? ' slide': '')}
        style={_.extend({
            minHeight: this.props.height,
            width: this.props.width,
            cursor: typeof this.props.typeId === 'undefined'? null: 'pointer',
            borderRadius: 5,
            border: 'thin solid grey',
            boxShadow: '2px 2px 2px grey',
            backgroundColor: 'white'
          },
          this.props.selected? { backgroundColor: 'white', border: 'thick solid ' + this.props.selectedColor }: {}
        )}
      >
        <div className='row'>
          <h4>
            {this.props.position + '. ' + (DB_SLIDE_TYPE_MAP[this.props.typeId] || 'Scoreboard')}
            <span className='pull-right'>
              {(this.props.options.duration || this.props.options.postRaceIdleTime)?
                (this.props.options.duration || this.props.options.postRaceIdleTime) + 's':
                ''}
            </span>
          </h4>
        </div>

        {this.props.typeId === 'video' &&
          <video muted
            src={this.props.options.originalUrl && this.props.options.originalUrl.replace('http:', 'https:')}
            controls={this.props.selected}
            style={_.extend(
              { width: '100%' },
              this.props.selected? { cursor: 'default' }: {}
            )}
            onMouseDown={this.props.selected? e => { e.stopPropagation(); }: null}
          />
        }

        {this.props.typeId === 'html' &&  <div className='row'>
          <iframe ref='preview'
            srcDoc={this.props.options.html}
            className='col-xs-12'
            style={{ border: 0, width: '100%' }}
            seamless='seamless'
            sandbox='allow-same-origin'
          />
          <div style={{ position: 'absolute', width: '100%', height: 188 }}/>
        </div>}

        {!_.contains(['video', 'html', 'url', 'image'], this.props.typeId) && this.props.selected && !this.props.dragging
          && <div className='row'>
            <iframe ref='preview'
              src={this.props.options.url && this.props.options.url.replace('http:', 'https:')}
              className='col-xs-12'
              style={{ border: 0, width: '100%' }}
              seamless='seamless'
              onError = {e => { console.log('err happened', e); }}
            />
            <div style={{ position: 'absolute', width: '100%', height: 188 }}/>
          </div>
        }

        <div className='row' style={this.props.selected? { cursor: 'default' }: null}
          onMouseDown={this.props.selected? e => { /*e.stopPropagation();*/ e.preventDefault(); }: null}
          onTouchStart={this.props.selected? e => { e.stopPropagation(); e.preventDefault(); }: null}
        >
          {optionElements}
        </div>

        {this.props.selected && <div className='row'><br/>
          <div className='col-xs-12 pull-right'>
            <button
              className='btn btn-danger'
              onClick={e => {
                e.preventDefault();
                var confirmed = window.confirm('Are you sure you want to delete this slide? This cannot be undone.');
                if (confirmed){
                  this.props.onDelete({ id: this.props.id });
                }
              }}
            >
              Delete Slide
            </button>
          </div>
        </div>}
      </div>
    </section>;
  },

  componentDidUpdate(prevProps, prevState){
    if (prevProps.selected !== this.props.selected){
      this.props.onSelectChange({ height: $(this.getDOMNode()).height() });
    }
  },

  handleClickOrTouch(e){
    this.props.onMoveStart({
      dragOriginX: e.pageX
    });
  },
});


module.exports = React.createClass({
  calcSlideBaseX(lineup, i){
    return _(lineup).take(i).reduce((runningX, slide, i) => {
      return runningX + this.calcSlideWidth(slide);
    }, 0);
  },

  calcSlideWidth(slide){
    return slide.selected? this.props.selectedSlideWidth: this.props.slideWidth;
  },

  getDefaultProps(){
    return {
      channel: null,
      selectedSlideWidth: 500,
      slideWidth: 270,
      dbOptionMap: {
        header: 'text0',
        line0: 'text0',
        line1: 'text1',
        line2: 'text2',
        line3: 'text3',
        line4: 'text4',
        line5: 'text5',
        line6: 'text6',
        html: 'text0',
        original: 'text0',
        originalUrl: 'text0',
        startAtPosition: 'text0',
        url: {
          key: 'text0',
          //convertFromDb: val => val.substr(val.lastIndexOf('/') + 1),
          //convertToDb: val => 'http://192.168.111.122/api/slides/' + val
        },
        speedLevel: 'text1',
        showPicture: 'text2'/* {
          key: 'text2',
          convertFromDb: val => val? 1: 0,
        }*/,
        showLineUpNumber: 'text3'/*{
          key: 'text3',
          convertFromDb: val => val? 1: 0,
        }*/,
        showKartNumber: 'text4'/*{
          key: 'text4',
          convertFromDb: val => val? 1: 0,
          convertToDb: val => val? 1: 0
        }*/,
        rowsPerPage: 'text5',
        duration: {
          key: 'timeInSecond',
          convertFromDb: val => 0.001 * val,
          convertToDb:  val => 1000 * val
        },
        postRaceIdleTime: {
          key: 'timeInSecond',
          convertFromDb: val => 0.001 * val,
          convertToDb: val => 1000 * val
        },
        trackId: 'trackNo'
      }
    };
  },

  getInitialState(){
    return {
      lineup: [],
      popup: {
        message: null,
        alertClass: null
      },
      newTypeId: 1,
      tracks: [],
      width: 800,
      slideHeight: undefined
    };
  },

  render(){
    return <div className='tab-pane container-fluid'
      style={{ overflowX: 'auto', overflowY: 'visible' }}
      onTouchMove={e => {
        csp.go(function* (){
          yield csp.put(this.userEvents, {
            type: 'mouseMove',
            touch: true,
            x: e.touches[0].pageX
          });
        }.bind(this));
      }}

      onTouchEnd={e => {
        csp.go(function* (){
          yield csp.put(this.userEvents, { type: 'mouseUp' });
        }.bind(this));
      }}
    >
      <Popup {...this.state.popup}
        onDone={e => {
          this.setState({ popup: {} });
        }}
      />

      <div className='row'>
        <div className='col-xs-12 col-lg-2'>
          <button className='btn btn-info'
            disabled={this.state.requestActive}
            onClick={() => {
              csp.go(function* (){
                yield csp.put(this.submissionEvents, { type: 'save' });
              }.bind(this));
            }}
          >
            Save Lineup
          </button>
        </div>

        <div className='col-xs-12 col-lg-5'>
          <div className='alert alert-info' >
            Click an item to edit it. Drag items left or right to change their order. Remember to save!
          </div>
        </div>

        <div className='col-xs-12 col-lg-5'>
          <button className='btn btn-success'
            disabled={this.state.requestActive}
            onClick={() => {
              csp.go(function* (){
                yield csp.put(this.submissionEvents, { type: 'create' });
              }.bind(this));
            }}
          >
            Add
          </button>

          <Select
            selectedId={this.state.newTypeId}
            allowClear={false}
            list={
              _(DB_SLIDE_TYPE_MAP)
              .transform(
                (result, text, id) => {
                  result.push({ label: text, value: id });
                },
                []
              )
              .sortBy('label')
              .uniq(true, 'label')
              .value()
            }
            onFunnelEvent={e => {
              if (e.added){
                this.setState({ newTypeId: e.val });
              }
            }}
          />
        </div>
      </div>

      <div ref='lineupContainer'
        style={{
          height: (this.state.slideHeight || 650) + 30,
          overflowX: 'auto',
          overflowY: 'visible'
        }}

        onMouseEnter={e => {
          csp.go(function* (){
            yield csp.put(this.navEvents, { type: 'hoverStart' });
          }.bind(this))
        }}

        onMouseLeave={e => {
          csp.go(function* (){
            yield csp.put(this.navEvents, { type: 'hoverEnd' });
          }.bind(this))
        }}
      >
        {this.state.showNav && this.isMounted() && this.state.lineup.length > 1 && <div
          style={{
            position: 'absolute',
            top: '50%',
            left: 0,
            zIndex: 99999,
            fontSize: '6em',
            color: '#239a55',
            cursor: 'pointer',
            WebkitUserSelect: 'none', userSelect: 'none'
          }}

          onClick={e => {
            e.preventDefault(); e.stopPropagation();
            csp.go(function* (){
              yield csp.put(this.navEvents, { type: 'arrowClick', direction: 'left' });
            }.bind(this))
          }}
        >
          <i className="fa fa-chevron-circle-left" />
        </div>}
        {this.state.showNav && this.isMounted() && this.state.lineup.length > 1 && <div
          style={{
            position: 'absolute',
            top: '50%',
            right: $(this.refs.lineup.getDOMNode()).scrollLeft(),
            zIndex: 99999,
            fontSize: '6em',
            color: '#239a55',
            cursor: 'pointer',
            WebkitUserSelect: 'none', userSelect: 'none'
          }}

          onClick={e => {
            e.preventDefault(); e.stopPropagation();
            csp.go(function* (){
              yield csp.put(this.navEvents, { type: 'arrowClick', direction: 'right' });
            }.bind(this))
          }}
        >
          <i className="fa fa-chevron-circle-right" />
        </div>}
        {this.renderLineup()}
      </div>
    </div>;
  },

  renderLineup(){
    var lineupElements = this.state.lineup.map((slide, i) => {
      return <SlideForm {...slide} trackList={this.props.trackList} channelId={this.props.screenTemplateId}
        height={this.state.slideHeight || 650}
        key={slide.key} id={slide.key} ref={slide.key}
        position={i + 1}
        width={slide.selected? this.props.selectedSlideWidth: this.props.slideWidth}
        onMoveStart={e => {
          csp.go(function* (){
            yield csp.put(this.userEvents, {
              type: 'dragStart',
              slideIndex: i,
              dragOriginX: e.dragOriginX
            });
          }.bind(this));
        }}
        onOptionChange={e => {
          var change = {};
          change[i] = {
            options: {
              $merge: e
            }
          };
          var lineup = React.addons.update(
            this.state.lineup,
            change
          );

          if (e.hasOwnProperty('url') && slide.typeId === 'image'){
            var popup = {
              message: 'Image successfully uploaded. Remember to save the lineup!',
              alertClass: 'alert-info'
            };
            this.setState({ lineup, popup });
          } else if (e.hasOwnProperty('originalUrl') && slide.typeId === 'video'){
            var popup = {
              message: 'Video successfully uploaded. Remember to save the lineup!',
              alertClass: 'alert-info'
            };
            this.setState({ lineup, popup });
          } else {
            this.setState({ lineup });
          }
        }}
        onDelete={e => {
          csp.go(function* (){
            yield csp.put(this.submissionEvents, { type: 'delete', id: e.id });
          }.bind(this));
        }}
        onImageUploadError={e => {
          this.setState({
            popup: {
              message: 'An error occurred while trying to upload the image.',
              alertClass: 'alert-danger'
            }
          });
        }}
        onUploadStart={e => {
          this.setState({
            popup: {
              message: 'Uploading...',
              alertClass: 'alert-info'
            }
          });
        }}
        onVideoUploadError={e => {
          this.setState({
            popup: {
              message: 'An error occurred while trying to upload the video. Your upload limits may need to be raised. Contact support for more info.',
              alertClass: 'alert-danger'
            }
          });
        }}
        onSelectChange={e => {
          if (typeof this.state.slideHeight === 'undefined' || e.height > this.state.slideHeight){
            this.setState({ slideHeight: e.height });
          }
        }}
      />;
    });

    return <div ref='lineup'
      onKeyDown={e => { /*console.log('hambone', e);*/ }}
      style={{ position: 'relative', overflowY: 'visible', WebkitOverflowScrolling: 'touch' }}
    >
      {lineupElements}
    </div>;
  },

  globalCspChans: [
    'submissionEvents',
    'userEvents',
    'navEvents'
    //'pointerDownEvents',
    //'pointerMoveEvents',
    //'pointerUpEvents'
  ],

  componentWillMount(){
    var self = this;

    this.globalCspChans.forEach(chan => {
      this[chan] = csp.chan();
    });
    //this.userEvents = csp.chan(csp.buffers.sliding(1));

    self.animationThrottler = csp.chan();
    var throttleAnimation = () => {
      requestAnimationFrame(() => {
        csp.go(function* (){
          yield csp.put(self.animationThrottler);
          throttleAnimation();
        });
      });
    };
    throttleAnimation();

    csp.go(function* (){
      var channelRequestEvents = csp.chan();
      var detailRequestEvents = csp.chan();

      var fireRequest = opts => {
        $.get(opts.url).then(
          response => {
            csp.go(function* (){
              yield csp.put(opts.channel, { response });
            });
          },
          response => {
            csp.go(function* (){
              yield csp.put(opts.channel, { error: true, response });
            });
          }
        );
      };

      var lineup, mostRecentSubmissionEvent = { type: 'start' };

      var allChannelsUrl = config.apiURL + 'speedscreenchannels/' + self.props.channelId + '.json?key=' + config.privateKey;

      while(true){
        if (mostRecentSubmissionEvent !== csp.CLOSED && mostRecentSubmissionEvent.type === 'delete'){
          var lineup = _.reject(self.state.lineup, { key: mostRecentSubmissionEvent.id });
        } else {
          console.log(self.props.channelId);
          fireRequest({
            url: allChannelsUrl,
            channel: channelRequestEvents
          });

          /*fireRequest({
            url: config.apiURL + 'channel/' + self.props.screenTemplateId + '.json?key=' + config.privateKey,
            channel: channelRequestEvents
          });
          fireRequest({
            url: config.apiURL + 'screenTemplateDetail.json?screenTemplateId=' + self.props.screenTemplateId + '&key=' + config.privateKey,
            channel: detailRequestEvents
          });*/

          var channelEvent = yield csp.take(channelRequestEvents);
          var channel = channelEvent.response;
          console.log(channel);
          lineup = channel.channelData.timelines.regular.slides;
          /*var detailEvent = yield csp.take(detailRequestEvents);
          var channelDetails = _(detailEvent.response.channelDetail)
            .sortBy(['seq', 'screenTemplateDetailId'])
            .reject(detail =>
              self.props.showScoreboard
              && detail.typeId === NEW_SCOREBOARD_TYPE_ID
              && detail.trackNo === self.props.trackId
            )
            .value();

          if (self.props.showScoreboard){
            channelDetails.splice(0, 0, {});
          }
          */
          var selectedSlideKey = _.result(_.find(self.state.lineup, 'selected'), 'key');

          if (!lineup){ // occurs on component unmount due to this being a csp channel
            return;
          }

          lineup.forEach((slide, i) => {
            slide.key = i;
            if (slide.key === selectedSlideKey){
              slide.selected = true;
            }
            if (slide.type === 'url'){
              slide.typeId = slide.options.type || 'url';
            } else {
              slide.typeId = slide.type || 'url';
            }
            //delete slide.options.type;

            for (var optionName in slide.options){
              var foundDbOption = self.props.dbOptionMap[optionName];
              if (foundDbOption && foundDbOption.convertFromDb){
                slide.options[optionName] = foundDbOption.convertFromDb(slide.options[optionName]);
              }
            }

            /*var detail = channelDetails[i];
            if (detail){
              slide.key = detail.screenTemplateDetailId || ('scoreboard' + i);
              if (slide.key === selectedSlideKey){
                slide.selected = true;
              }
              if (detail.typeId === VIDEO_URL_HTML_TYPE_ID){
                //console.log('v u h', slide.type, serverLineup[i].type);
                //slide.typeId = slide.type;//serverLineup[i].type;
                if (slide.options.hasOwnProperty('originalUrl')){
                  slide.typeId = 'video';
                } else {
                  slide.typeId = slide.type;
                }
              } else {
                slide.typeId = detail.typeId;
              }
            } else {
              slide.key = 'scoreboard' + i;
            }
            if (mostRecentSubmissionEvent !== csp.CLOSED && mostRecentSubmissionEvent.type === 'create'){
              slide.selected = (i === lineup.length - 1);
            } else if (mostRecentSubmissionEvent !== csp.CLOSED && mostRecentSubmissionEvent.type === 'start'){
              slide.selected = (self.props.showScoreboard? i === 1: i === 0);
            }
            slide.x = self.calcSlideBaseX(lineup, i);
            if (slide.options.postRaceIdleTime === PSEUDO_INFINITE){
              delete slide.options.postRaceIdleTime;
            }
            if (slide.options.duration === PSEUDO_INFINITE){
              delete slide.options.duration;
            }
            if (slide.options.url && slide.typeId !== IMAGE_TYPE_ID){
              //delete slide.options.url;
            }
            if (slide.options.backgroundUrl){
              delete slide.options.backgroundUrl;
            }
            delete slide.options.type;
            for (var optionName in slide.options){
              var foundDbOption = self.props.dbOptionMap[optionName];
              if (foundDbOption && foundDbOption.convertOnLoad){
                slide.options[optionName] = foundDbOption.convertFromDb(slide.options[optionName]);
              }
            }*/
          });
        }

        self.setState({ lineup });

        var submissionEvent = yield csp.take(self.submissionEvents);
        if (submissionEvent === csp.CLOSED){
          return;
        }

        var submissionRequestEvents = csp.chan();

        if (submissionEvent !== csp.CLOSED && submissionEvent.type === 'save'){
          $.get(allChannelsUrl)
          .then(res => {
            var newLineup = _.cloneDeep(self.state.lineup).map(slide => {
              var options = slide.options;

              for (var optionName in options){
                console.log('checking', optionName);
                var foundOption = self.props.dbOptionMap[optionName];
                if (typeof foundOption === 'undefined'){
                  continue;
                }

                var dbOption = foundOption.key || foundOption;
                if (foundOption.key && foundOption.convertToDb){
                  options[optionName] = foundOption.convertToDb(options[optionName]);
                } else {
                  options[optionName] = options[optionName];
                }
              }

              return {
                options: slide.options,
                type: slide.type
              };
            });

            console.log(_.isEqual(newLineup, res.channelData.timelines.regular.slides), 'fakesave');

            var diffs = require('deep-diff').diff(res.channelData.timelines.regular.slides, newLineup);
            if (diffs && diffs.length){
              console.log(diffs[0]);
            } else {
              console.log('saul good, man!', diffs);
            }

            csp.go(function* (){
              yield csp.put(submissionRequestEvents, {
                type: 'create',
                message: 'Slide successfully added!'
              });
            });
          });


          /*
          var newLineup = _.cloneDeep(self.state.lineup);
          var updateRequestEvents = csp.chan();
          var requestCount = 0;

          newLineup.forEach((slide, i) => {
            if (typeof slide.typeId === 'undefined'){ // happens if this is the old-style scoreboard slide
              return;
            }

            var options = slide.options;
            var detail = channelDetails.find(d => d.screenTemplateDetailId === slide.key);
            if (typeof detail === 'undefined'){
              return;
            }

            detail.seq = i;

            for (var optionName in options){
              var foundOption = self.props.dbOptionMap[optionName];
              if (typeof foundOption === 'undefined'){
                continue;
              }

              var dbOption = foundOption.key || foundOption;
              if (foundOption.key && !foundOption.convertOnLoad && foundOption.convertFromDb){
                detail[dbOption] = foundOption.convertFromDb(options[optionName]);
              } else {
                detail[dbOption] = options[optionName];
              }
            }

            requestCount++;

            $.ajax({
              type: 'PUT',
              url: config.apiURL + 'screenTemplateDetail/' + detail.screenTemplateDetailId + '?key=' + config.privateKey,
              data: detail
            })
            .then(
              response => {
                csp.go(function* (){
                  yield csp.put(updateRequestEvents, { response });
                });
              },
              response => {
                csp.go(function* (){
                  yield csp.put(updateRequestEvents, { error: true, response });
                });
              }
            );
          });

          var responses = [], foundError = false;

          while (responses.length < requestCount){
            var responseEvent = yield csp.take(updateRequestEvents);
            if (responseEvent.error){
              foundError = true;
            }
            responses.push(responseEvent);
          }

          if (foundError){
            csp.go(function* (){
              yield csp.put(submissionRequestEvents, { error: true, type: 'save' });
            });
          } else {
            csp.go(function* (){
              yield csp.put(submissionRequestEvents, {
                type: 'save',
                message: 'Lineup successfully saved!'
              });
            });
          }
          */
        } else {
          self.setState({ requestActive: true });

          if (submissionEvent !== csp.CLOSED && submissionEvent.type === 'create'){
            /*
            var tempLineup = React.addons.update(self.state.lineup, {
                $push: [{
                  typeId: self.state.newTypeId,
                  options: {}
                }]
              }
            );

            tempLineup.forEach((slide, i) => {
              slide.selected = (i === tempLineup.length - 1);
              slide.x = self.calcSlideBaseX(tempLineup, i);
            });
            self.setState({ lineup: tempLineup });

            var data = {
              screenTemplateId: self.props.screenTemplateId,
              seq: _(channelDetails).pluck('seq').max() + 1
            };

            switch (self.state.newTypeId){
              case 'url':
                data.typeId = VIDEO_URL_HTML_TYPE_ID;
                data.text0 = 'https://www.example.com/example';
                break;
              case 'html':
                data.typeId = VIDEO_URL_HTML_TYPE_ID;
                data.text0 = '<marquee>Insert HTML here.</marquee>';
                break;
              case 'video':
                data.typeId = VIDEO_URL_HTML_TYPE_ID;
                data.text0 = 'http://placeholder.video.url/filename.mp4';
                break;
              default:
                data.typeId = self.state.newTypeId;
            }
            */

            /*$.ajax({
              type: 'POST',
              url: config.apiURL + 'screenTemplateDetail?key=' + config.privateKey,
              data
            })
            .then(
              res => {
                csp.go(function* (){
                  yield csp.put(submissionRequestEvents, {
                    type: 'create',
                    message: 'Slide successfully added!'
                  });
                });
              },
              res => {
                csp.go(function* (){
                  yield csp.put(submissionRequestEvents, { error: true, type: 'create' });
                });
              }
            );*/
          } else if (submissionEvent !== csp.CLOSED && submissionEvent.type === 'delete'){
            var url = config.apiURL + 'screenTemplateDetail/' + submissionEvent.id + '?key=' + config.privateKey;
            $.ajax({ type: 'DELETE', url })
            .then(
              res => {
                csp.go(function* (){
                  yield csp.put(submissionRequestEvents, {
                    type: 'delete',
                    message: 'Slide successfully deleted!',
                    id: submissionEvent.id
                  });
                });
              },
              res => {
                csp.go(function* (){
                  yield csp.put(submissionRequestEvents, { error: true, type: 'delete', id: submissionEvent.id });
                });
              }
            );
          }
        }

        mostRecentSubmissionEvent = yield csp.take(submissionRequestEvents);
        if (mostRecentSubmissionEvent === csp.CLOSED){
          return;
        }

        if (mostRecentSubmissionEvent.error){
          self.setState({
            popup: {
              message: 'Action failed.',
              alertClass: 'alert-danger'
            }
          });
        } else {
          self.setState({
            popup: {
              message: mostRecentSubmissionEvent.message + ' (' + moment().format('h:mm:ss a') + ')',
              alertClass: 'alert-success'
            }
          });
        }

        self.setState({ requestActive: false });

        if (mostRecentSubmissionEvent !== csp.CLOSED && mostRecentSubmissionEvent.type === 'create'){
          var el = self.refs.lineupContainer.getDOMNode();
          $(el).scrollLeft(el.scrollWidth + 9999);
        }
      }
    });
  },

  componentDidMount(){
    var self = this;

    $(window).mousemove(e => {
      csp.go(function* (){
        yield csp.put(self.userEvents, { type: 'mouseMove', x: e.pageX });
      });
    });

    $(window).mouseup(e => {
      csp.go(function* (){
        yield csp.put(self.userEvents, { type: 'mouseUp' });
      });
    });

/*
    var keyEvents = csp.chan();

    $(window).keydown(e => {
      if (e.keyCode !== 16 || e.keyCode !== 17){
        return;
      }

      csp.go(function* (){
        yield csp.put(keyEvents, { type: 'keyDown', keyCode: e.keyCode });
      });
    });

    $(window).keyup(e => {
      if (e.keyCode !== 16 || e.keyCode !== 17){
        return;
      }

      csp.go(function* (){
        yield csp.put(keyEvents, { type: 'keyUp', keyCode: e.keyCode });
      });
    });

    csp.go(function* (){
      var e;
      while (true){
        do {
          e = yield csp.take(keyEvents);
        } while (e.type !== 'keyDown');


      }
    });
*/

    this.requestingResizeFrame = false;
    $(window).resize(e => {
      if (!this.requestingResizeFrame){
        this.requestingResizeFrame = true;
        requestAnimationFrame(() => {
          this.requestingResizeFrame = false;
          if (!this.isMounted()){
            return;
          }

          var width = this.state.width;
          if (this.refs && this.refs.lineup && $(this.getDOMNode()).is(':visible')){
            width = $(window).width() - $(this.refs.lineup.getDOMNode()).offset().left - 70;
          }
          var lineup = this.state.lineup.map((slide, i) => {
            slide.x = this.calcSlideBaseX(this.state.lineup, i);
            return slide;
          });
          this.setState({ lineup, width });
        });
      }
    });

    var el = self.refs.lineupContainer.getDOMNode();
    var jQueryEl = $(el);

    csp.go(function* (){
      var e, dragSlideIndex, moveCount, dragOriginX, lineup;

      while (true){
        do {
          //e = yield csp.take(self.animationThrottler);
          e = yield csp.take(self.userEvents);
          if (e === csp.CLOSED){
            return;
          }
        } while (e !== csp.CLOSED && e.type !== 'dragStart'); // wish I could write "until (e.type === 'dragStart')" instead...

        dragSlideIndex = e.slideIndex;
        dragOriginX = e.dragOriginX;
        var baseX = self.calcSlideBaseX(self.state.lineup, dragSlideIndex);
        moveCount = 0;

        e = yield csp.take(self.userEvents);
        if (e === csp.CLOSED){
          return;
        }

        var change = {};
        change[dragSlideIndex] = {
          held: { $set: true },
          x: { $set: e.x - dragOriginX + baseX }
        };
        lineup = React.addons.update(self.state.lineup, change);
        self.setState({ lineup });

        while (e !== csp.CLOSED && e.type === 'mouseMove'){
          moveCount++;

          lineup = _.sortBy(lineup, (slide, i) => {
            if (typeof slide.typeId === 'undefined'){
              return -99999;
            }

            if (slide.held){
              slide.x = e.x - dragOriginX + baseX;
              if (moveCount > 2){
                slide.dragging = true;
              }
            } else {
              slide.x = self.calcSlideBaseX(lineup, i);
            }
            return slide.x;
          });
          self.setState({ lineup });

          e = yield csp.take(self.userEvents);
          if (e === csp.CLOSED){
            return;
          }
        }

        lineup.forEach((slide, i) => {
          if ((slide.selected && moveCount > 2) || (moveCount <= 2 && slide.held && !slide.selected)){
            slide.selected = true;

            var newX = i * self.props.slideWidth + self.props.selectedSlideWidth;
            if (newX <= jQueryEl.scrollLeft()){
              jQueryEl.scrollLeft(newX);
            } else if (newX > jQueryEl.scrollLeft() + jQueryEl.width()){
              jQueryEl.scrollLeft(newX - jQueryEl.width());
            } else if (i === lineup.length - 1){
              jQueryEl.scrollLeft(el.scrollWidth);
            }
          } else {
            slide.selected = false;
          }
          slide.held = false;
          slide.dragging = false;
          slide.x = self.calcSlideBaseX(lineup, i);
        });

        self.setState({ lineup });
      }
    });

    csp.go(function* (){
      var event;

      while (true){
        do {
          event = yield csp.take(self.navEvents);
          if (event === csp.CLOSED){
            return;
          }
        } while (event !== csp.CLOSED && event.type !== 'hoverStart');

        if (el.scrollWidth > jQueryEl.width()){
          self.setState({ showNav: true });
        }

        while (event !== csp.CLOSED && event.type !== 'hoverEnd'){
          event = yield csp.take(self.navEvents);
          if (event === csp.CLOSED){
            return;
          }

          if (event !== csp.CLOSED && event.type === 'arrowClick'){

            switch(event.direction){
              case 'left':
                jQueryEl.scrollLeft(jQueryEl.scrollLeft() - self.props.slideWidth);
                break;

              case 'right':
                jQueryEl.scrollLeft(jQueryEl.scrollLeft() + self.props.slideWidth);
            }
          }
        }

        self.setState({ showNav: false });
      }
    });
  },

  componentWillUnmount(){
    this.globalCspChans.forEach(chanName => {
      this[chanName].close();
    });
  }
});
