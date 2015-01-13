var React = require('react/addons');
var csp = require('js-csp');
var Immutable = require('immutable');
var _ = require('lodash');


var Anim = require('../../components/react-transition');

var ICheck = require('../../components/icheck');

var Popup = require('../../components/popup');

var Select = require('../../components/react-select2');

var DB_SLIDE_TYPE_MAP = {
  20: 'Event Screen',
  2: 'Image',
  15: 'Last Winner with Picture',
  8: 'Most Improved RPM of Month',
  9: 'Most Improved RPM Of Year',
  13: 'Next Racers',
  17: 'Next Racers',
  21: 'New Scoreboard',
  23: 'Previous Previous Race Results',
  22: 'Previous Race Results',
  10: 'Schedule',
  14: 'Schedule',
  7: 'Top Pro Skill',
  3: 'Top Time of Day',
  16: 'Top Time of Day with Picture',
  5: 'Top Time of Month',
  6: 'Top Time of Year',
  4: 'Top Time of Week',
  1: 'URL',
  18: 'Video'
};


var globalTrackList = [];

var getTitleFormat = text => { // e.g. converts "myVar123" to "My Var 123"
  var result = text.replace( /([A-Z]|[0-9]+)/g, " $1" );
  var words = result.split(' ');
  words = words.map(word => {
    var upperWord = word.toUpperCase();
    if (upperWord === 'ID' || upperWord === 'URL'){
      return upperWord;
    } else {
      return word;
    }
  });
  result = words.join(' ');
  return result.charAt(0).toUpperCase() + result.slice(1);
};


var SlideForm = React.createClass({
  propTypes: {
    screenTemplateId: React.PropTypes.number,
    screenTemplateName: React.PropTypes.string,
    showScoreboard: React.PropTypes.bool,
    postRaceIdleTime: React.PropTypes.number,
    trackId: React.PropTypes.number,
    startPosition: React.PropTypes.number,
    sizeX: React.PropTypes.number,
    sizeY: React.PropTypes.number
  },

  getDefaultProps(){
    return {
      dragX: null,
      dragY: null,
      onMouseDown(){}, onTouchMove(){}, onOptionChange(){}
    }
  },

  render(){
    var heightStyle = {
      height: this.props.height,
      maxHeight: this.props.height
    };

    var style = {
      position: 'absolute',
      left: 0,
      top: this.props.y
    };

    if (this.props.dragging){
      style.zIndex = 9999;
    } else {
      style.zIndex = 0;
    }

    var optionElements = [];
    if (this.props.selected){
      var options = this.props.options;

      const isBoolish = val => {
        return val === false || val === true || val === 'false' || val === 'true';
      };

      const isStringish = val => {
        return (isNaN(val) || val === '') && !isBoolish(val);
      };

      _.forOwn(options, (optionValue, optionName) => {
        var optionValue = options[optionName];
        var commonProps = {
          ref: optionName,
          onChange: function(){
            var changedOption = {};
            var val = this.refs[optionName].getDOMNode().value;
            if (isBoolish(val)){
              changedOption[optionName] = (val == true || val === 'true');
            } else if (isStringish(val)){
              changedOption[optionName] = val;
            } else {
              changedOption[optionName] = ~~val;
            }
            this.props.onOptionChange(changedOption);
          }.bind(this)
        };

        var inputElement = null;
        if (optionName === 'trackId'){
          inputElement = <div style={{ marginRight: 0.1 }}>
            <Select
              list={globalTrackList}
              selectedId={optionValue}
              allowClear={false}
              style={{ width: '100%' }}
              onFunnelEvent={e => {
                if (e.added){
                  this.props.onOptionChange({
                    trackId: ~~e.val
                  });
                }
              }}
            />
          </div>;
        } else if (isBoolish(optionValue)){
          inputElement = <div style={{ height: 34 }}>
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
            /*commonProps.style = {
              width: 80
            };*/
          }
          inputElement = <input
            className='form-control'
            defaultValue={options[optionName]}
            {...commonProps}
            ref={optionName}
          />
        }
        optionElements.push(
          <div
            key={optionName}
            className={'control-group col-xs-' + (isStringish(optionValue)? '6': '3')}
            style={{ marginTop: '1em' }}
          >
            <div className='control-label'>
              {getTitleFormat(optionName)}
            </div>
            {inputElement}
          </div>
        );
        //optionElements.push();
      });
      //}
    }

    return <div duration={this.props.dragging? 0: 300} style={style}>
      <div
        className={'alert container form ' + (this.props.selected? 'alert-success': 'alert-info')}
        style={heightStyle}
      >
        <div className='row'>
          <h3 style={{ cursor: 'pointer' }}
            onMouseDown={e => {
              if (e.button !== 0){
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
            {DB_SLIDE_TYPE_MAP[this.props.typeId] || 'Old Scoreboard'}
            <span className='pull-right'>
              {(this.props.options.duration || this.props.options.postRaceIdleTime)?
                (this.props.options.duration || this.props.options.postRaceIdleTime) + 's':
                ''}
            </span>
            {this.props.selected && <button className='btn btn-danger pull-right' style={{ marginRight: 5 }}
              onClick={e => {
                e.preventDefault();
                var confirmed = window.confirm('Are you sure you want to delete this slide?');
                if (confirmed){
                  this.props.onDelete({ id: this.props.id });
                }
              }}
            >
              Delete
            </button>}
            &nbsp;&nbsp;
          </h3>
        </div>
        <div className='row'>
          {optionElements}
        </div>
      </div>
    </div>;
  },

  handleClickOrTouch(e){
    this.props.onMoveStart({
      dragOriginY: e.pageY
    });
  },

  /*componentDidMount(){
    this.inputEvents = csp.chan();

    csp.go(function* (){
      while (true){
        yield csp.take(this.inputEvents);
      }
    }.bind(this));
  }*/
});


module.exports = React.createClass({
  calcSlideBaseY(lineup, i){
    return Immutable.List(lineup).take(i).reduce((runningY, slide, i) => {
      return runningY + (slide.selected? this.props.selectedSlideHeight: this.props.slideHeight);
    }, 0);
  },

  calcLineupHeight(){
    return this.state.lineup.length * this.props.slideHeight + this.props.selectedSlideHeight;
  },

  getDefaultProps(){
    return {
      channel: null,
      slideHeight: 69,
      selectedSlideHeight: 420,
      dbOptionMap: {
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
        url: 'text0',
        speedLevel: 'text1',
        showPicture: { key: 'text2', conversionFunction: val => val? 1: 0, convertOnLoad: false },
        showLineUpNumber: { key: 'text3', conversionFunction: val => val? 1: 0, convertOnLoad: false },
        showKartNumber: { key: 'text4', conversionFunction: val => val? 1: 0, convertOnLoad: false },
        rowsPerPage: 'text5',
        duration: { key: 'timeInSecond', conversionFunction: val => 0.001 * val, convertOnLoad: true },
        postRaceIdleTime: { key: 'timeInSecond', conversionFunction: val => 0.001 * val, convertOnLoad: true },
        trackId: 'trackNo'
      }
    };
  },

  getInitialState(){
    return {
      lineup: [],//Immutable.List(),
      popup: {
        message: null,
        alertClass: null
      },
      newTypeId: 1,
      tracks: []
    };
  },

  render(){
    return <div
      onTouchMove={e => {
        csp.go(function* (){
          yield csp.put(this.userEvents, {
            type: 'mouseMove',
            touch: true,
            x: e.touches[0].pageX,
            y: e.touches[0].pageY
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

      <button className='btn btn-info'
        disabled={this.state.requestActive}
        onClick={() => {
          csp.go(function* (){
            yield csp.put(this.submissionEvents, { type: 'save' });
          }.bind(this));
        }}
      >
        Save
      </button>

      <label className='pull-right'>
        Add Slide:

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
            .value()
          }
          onFunnelEvent={e => {
            if (e.added){
              this.setState({ newTypeId: e.val });
            }
          }}
        />

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
      </label>

      <div style={{ height: this.calcLineupHeight() }}>
        {this.renderLineup()}
      </div>
    </div>;
  },

  renderLineup(){
    var lineupElements = this.state.lineup.map((slide, i) => {
      return <SlideForm {...slide}
        key={slide.key}
        id={slide.key}
        height={slide.selected? this.props.selectedSlideHeight: this.props.slideHeight}
        onMoveStart={e => {
          csp.go(function* (){
            yield csp.put(this.userEvents, {
              type: 'dragStart',
              slideIndex: i,
              dragOriginY: e.dragOriginY
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
          this.setState({ lineup });
        }}
        onDelete={e => {
          csp.go(function* (){
            yield csp.put(this.submissionEvents, { type: 'delete', id: e.id });
          }.bind(this));
        }}
      />;
    });

    return <div style={{ position: 'relative', listStyleType: 'none' }} ref='lineup'>
      {lineupElements}
    </div>;
  },

  componentWillMount(){
    $.get(config.apiURL + 'tracks/index.json?key=' + config.apiKey)
    .then(
      res => {
        globalTrackList = res.tracks.map(track => ({ label: track.name, value: track.id }) );
      },
      res => {
        console.log('track list error', res);
      }
    );

    const self = this;

    const globalCspChans = [
      'userEvents',
      'submissionEvents',
      //'pointerDownEvents',
      //'pointerMoveEvents',
      //'pointerUpEvents'
    ];
    globalCspChans.forEach(chan => {
      this[chan] = csp.chan();
    });
/* TODO: FINISH ANIMATION THROTTLING FOR BETTER MOBILE PERFORMANCE
    const animationThrottler = csp.chan();
    const throttleAnimation = () => {
      requestAnimationFrame(() => {
        csp.go(function* (){
          yield csp.put(animationThrottler);
          throttleAnimation();
        });
      });
    };
*/
    csp.go(function* (){
      const channelRequestEvents = csp.chan();
      const detailRequestEvents = csp.chan();

      const fireRequest = opts => {
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

      while(true){
        fireRequest({
          url: config.apiURL + 'channel/' + self.props.screenTemplateId + '.json?key=' + config.privateKey,
          channel: channelRequestEvents
        });
        fireRequest({
          url: config.apiURL + 'screenTemplateDetail.json?screenTemplateId=' + self.props.screenTemplateId + '&key=' + config.privateKey,
          channel: detailRequestEvents
        });

        var channelEvent = yield csp.take(channelRequestEvents);
        var channel = channelEvent.response;
        // TODO: get remaining channel props
        var serverLineup = channel.lineup;
        var detailEvent = yield csp.take(detailRequestEvents);
        var channelDetails = _.sortBy(detailEvent.response.channelDetail, 'seq');//Immutable.List(detailEvent.response.channelDetail).sortBy(channel => channel.seq);
        if (serverLineup.length === channelDetails.length + 1){
          channelDetails.splice(0, 0, {});
        }
        if (mostRecentSubmissionEvent.type === 'delete'){
          lineup = serverLineup;
        } else {
          lineup = _.merge(self.state.lineup, serverLineup);
        }
        lineup.forEach((slide, i) => {
          var detail = channelDetails[i];
          if (detail){
            slide.key = detail.screenTemplateDetailId;
            slide.typeId = detail.typeId;
          } else {
            slide.key = 'scoreboard' + i;
          }
          if (mostRecentSubmissionEvent.type === 'create'){
            slide.selected = (i === lineup.length - 1);
          } else if (mostRecentSubmissionEvent.type === 'start'){
            slide.selected = (i === 0);
          }
          slide.y = self.calcSlideBaseY(lineup, i);
          delete slide.options.type;
          if (slide.options.postRaceIdleTime === 86400000){
            delete slide.options.postRaceIdleTime;
          }
          for (var optionName in slide.options){
            var foundDbOption = self.props.dbOptionMap[optionName];
            if (foundDbOption && foundDbOption.convertOnLoad){
              slide.options[optionName] = foundDbOption.conversionFunction(slide.options[optionName]);
            }
          }
        });
        self.setState({ lineup });  //self.setState({ lineup: Immutable.List(lineup) });

        var submissionEvent = yield csp.take(self.submissionEvents);
        //selectedIndex = _.findIndex(self.state.lineup, 'selected');

        var submissionRequestEvents = csp.chan();

        if (submissionEvent.type === 'save'){
          var newLineup = _.cloneDeep(self.state.lineup);//self.state.lineup.toArray();
          var updateRequestEvents = csp.chan();

          newLineup.forEach((channel, i) => {
            var options = channel.options;
            for (var optionName in options){
              var foundOption = self.props.dbOptionMap[optionName];
              if (typeof foundOption === 'undefined'){
                continue;
              }

              var dbOption = foundOption.key || foundOption;
              var detail = channelDetails.find(d => d.screenTemplateDetailId === channel.key);
              if (typeof detail === 'undefined'){
                return;
              }

              detail.seq = i;

              if (foundOption.key && !foundOption.convertOnLoad && foundOption.conversionFunction){
                detail[dbOption] = foundOption.conversionFunction(options[optionName]);
                /*if (foundOption.conversionFunction === 'boolToInt'){
                  detail[dbOption] = options[optionName]? 1: 0;
                } else {
                  detail[dbOption] = options[optionName] * foundOption.conversionFunction;
                }*/
              } else {
                detail[dbOption] = options[optionName];
              }

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
            }
          });

          var responses = [], foundError = false;
          while (responses.length < newLineup.length){
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
              yield csp.put(submissionRequestEvents, { type: 'save' });
            });

            /*console.log('work complete', event);

            self.setState({
              popup: {
                message: 'Lineup successfully saved!',
                alertClass: 'alert-success'
              }
            });*/
          }
        } else {
          self.setState({ requestActive: true });

          if (submissionEvent.type === 'create'){
            $.ajax({
              type: 'POST',
              url: config.apiURL + 'screenTemplateDetail?key=' + config.privateKey,
              data: {
                screenTemplateId: 1,
                typeId: self.state.newTypeId,
                seq: _(channelDetails).pluck('seq').max() + 1
              },
            })
            .then(
              res => {
                csp.go(function* (){
                  yield csp.put(submissionRequestEvents, { type: 'create' });
                });
              },
              res => {
                csp.go(function* (){
                  yield csp.put(submissionRequestEvents, { error: true, type: 'create' });
                });
              }
            );
          } else if (submissionEvent.type === 'delete'){
            var url = config.apiURL + 'screenTemplateDetail/' + submissionEvent.id + '?key=' + config.privateKey;
            $.ajax({ type: 'DELETE', url })
            .then(
              res => {
                csp.go(function* (){
                  yield csp.put(submissionRequestEvents, { type: 'delete' });
                });
              },
              res => {
                csp.go(function* (){
                  yield csp.put(submissionRequestEvents, { error: true, type: 'delete' });
                });
              }
            );
          }
        }

        csp.go(function* (){
          mostRecentSubmissionEvent = yield csp.take(submissionRequestEvents);
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
                message: 'Success!',
                alertClass: 'alert-success'
              }
            });
          }
        });

        // temp kludge to fix what appears to be API race condition
        // (I think it reports a success before it actually completes its operation)
        yield csp.take(csp.timeout(500));
        self.setState({ requestActive: false });
      }
    });
  },

  componentDidMount(){
    var self = this;

    $(window).mousemove(e => {
      csp.go(function* (){
        yield csp.put(self.userEvents, { type: 'mouseMove', x: e.pageX, y: e.pageY });
      });
    });

    $(window).mouseup(e => {
      csp.go(function* (){
        yield csp.put(self.userEvents, { type: 'mouseUp' });
      });
    });

    var thisEl = $(this.getDOMNode());
    $(window).resize(e => {
      if (thisEl.width() + thisEl.offset().left > $(window).width()){
        console.log('happenin');
      }
    });

    csp.go(function* (){
      var e, dragSlideIndex, moveCount, dragOriginY, lineup;

      while (true){
        do {
          e = yield csp.take(self.userEvents);
        } while (e.type !== 'dragStart'); // wish I could write "until (e.type === 'dragStart')" instead...

        dragSlideIndex = e.slideIndex;
        dragOriginY = e.dragOriginY;
        var baseY = self.calcSlideBaseY(self.state.lineup, dragSlideIndex);
        moveCount = 0;

        e = yield csp.take(self.userEvents);

        var change = {};
        change[dragSlideIndex] = {
          dragging: { $set: true },
          y: { $set: e.y - dragOriginY + baseY }
        };
        lineup = React.addons.update(self.state.lineup, change);
        self.setState({ lineup });

        while (e.type === 'mouseMove'){
          moveCount++;

          lineup = _.sortBy(lineup, (slide, i) => {
            if (slide.dragging){
              slide.y = e.y - dragOriginY + baseY;
            } else {
              slide.y = self.calcSlideBaseY(lineup, i);
            }
            return slide.y;
          });
          self.setState({ lineup });

          e = yield csp.take(self.userEvents);
        }

        lineup.forEach((slide, i) => {
          if ((slide.selected && moveCount > 2) || (moveCount <= 2 && slide.dragging && !slide.selected)){
            slide.selected = true;
          } else {
            slide.selected = false;
          }
          slide.dragging = false;
          slide.y = self.calcSlideBaseY(lineup, i);
        });

        self.setState({ lineup });
      }
    });
  },

  componentDidUpdate(){
    //console.log('selected', slide.y);
  }
  /*
  shouldComponentUpdate(nextProps, nextState){
    return (this.state.lineup !== nextState.lineup) || (!this.state.popup && nextState.popup) || (!this.state.popup && nextState.popup);
  }
  */
});
