// DEPENDENCIES
var React = require('react/addons');
var _ = require('lodash');
var moment = require('moment');
var csp = require('js-csp');
var koalaesce = require('koalaesce');
var jsonHash = require('json-hash');

// IMPORTED CONSTANTS

var SLIDE_MAP = require('./slide-map');
var OPTION_MAP = require('./option-map');


// IMPORTED COMPONENTS

var TimePicker = require('../../components/simple-timepicker');
var DatePicker = require('../../components/datepicker');
var Tooltip = require('../../components/bootstrap-tooltip');
var Popup = require('../../components/popup');
var Select = require('../../components/select2');
var Uploader = require('../../components/simple-upload-button');
var IRadioGroup = require('../../components/iradio-group');
var Modal = require('../../components/bootstrap-modal');
var XButton = require('../../components/x-button');
var SmartInput = require('../../components/smart-input');
var Check = require('../../components/icheck');


// GLOBAL CONSTANTS AND FUNCTIONS

var DAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
var MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];

var RACE_EVENTS = {
  onRaceStart: 'Race Starting',
  onFirstLapStart: 'First Lap Started',
  onFirstLapCompleted: 'First Lap Completed',
  onRaceEnd: 'Race Finishing'
};

var IMAGE_PATH = '/assets/cs-speedscreen/images/';
var VIDEO_PATH = '/assets/videos/';

var DELETE_DURATION = 1000;
var TRANSITION_DURATION = 300;

var BOOTSTRAP_BLUE = '#428bca';
var SELECTED_COLOR = 'hsl(120,50%,90%)';

var RANGE_MAP = {
  day: 'Today', week: 'This Week', month: 'This Month', year: 'This Year'
};

var global = {
  windowHeight: $(window).height(),
  bootstrapSize: findBootstrapEnvironment()
};

function* idGenerator(){
  var index = 0;
  while (true){
    yield index++;
  }
}
var createSlideId = idGenerator();

function getOrdinalArray(first = 1, last = 31){
  var nums = [];
  for (var i = first; i <= last; i++){
    switch (i % 10){
      case 1:
        nums.push(i + (i === 11? 'th': 'st'));
      break;

      case 2:
        nums.push(i + (i === 12? 'th': 'nd'));
      break;

      case 3:
        nums.push(i + (i === 13? 'th': 'rd'));
      break;

      default:
        nums.push(i + 'th');
      break;
    }
  }
  return nums;
}

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

var getSlideTypeSelectList = _.memoize(includeScoreboard => {
  return _(SLIDE_MAP)
    .transform(
      (result, slide, id) => {
        if (slide.disabled){
          return;
        }

        if (includeScoreboard){
          if (id !== 'url'){
            result.push({ label: slide.label, value: id });
          }
        } else {
          if (id !== 'scoreboard' && id !== 'raceUrl'){
            result.push({ label: slide.label, value: id });
          }
        }
      },
      []
    )
    .sortBy('label')
    .uniq(true, 'label')
    .value();
});


// COMPONENTS

var SlideCard = React.createClass({
  getDefaultProps(){
    return {
      dragX: null,
      dragY: null,
      onMouseDown(){}, onTouchMove(){}, onOptionChange(){}, onMount(){}, onSelectChange(){}
    }
  },

  getInitialState(){
    return { entering: true, deleting: false };
  },

  render(){
    var containerStyle = {
      position: 'absolute',
      left: this.props.x,
      top: 0,
      overflowX: 'hidden',
      overflowY: 'visible',
      opacity: (this.state.deleting || this.state.entering)? 0: 1,
      transitionProperty: 'left, opacity',
      transitionTimingFunction: 'ease-out'
    };

    if (this.props.lastHeld){
      containerStyle.zIndex = 998;
    }
    if (this.props.held){
      containerStyle.zIndex = 999;
    } else {
      containerStyle.transition = 'left ' + (TRANSITION_DURATION / 3000)
        + 's, opacity ' + (DELETE_DURATION / 1000)
        + 's, border-width 1s, border-color 1s';
    }

    var optionElements = [];

    if (this.props.typeId === 'scoreboard'){
      var shownOptionLimit = 7;
      var options = _.pick(
        this.props.options,
        (optionValue, optionKey) => optionKey !== 'url' && OPTION_MAP[optionKey] && OPTION_MAP[optionKey].type !== 'boolean'
      );
      var nonDefaultOptions = _.pick(options, (optionValue, optionKey) => {
        return optionValue != koalaesce.get(OPTION_MAP, optionKey, 'default');
      });
      var nonDefaultOptionSet = _.take(_.keys(nonDefaultOptions), shownOptionLimit);
      if (shownOptionLimit - nonDefaultOptionSet.length > 0){
        var remainingOptionSet = _(_.keys(options))
          .difference(nonDefaultOptionSet)
          .take(shownOptionLimit - nonDefaultOptionSet.length)
          .value();
      } else {
        var remainingOptionSet = [];
      }

      var optionsToShow = nonDefaultOptionSet.concat(remainingOptionSet);
    } else {
      var optionsToShow = koalaesce.get(SLIDE_MAP, this.props.typeId, 'optionSet') || [];//SLIDE_MAP[this.props.typeId].optionSet;
    }

    _.forOwn(this.props.options, (optionValue, optionKey) => {
      if ((!OPTION_MAP[optionKey] || !_.contains(optionsToShow, optionKey))
          && (optionKey !== 'showConditions' && optionKey !== 'eventSlides')){
        return;
      }

      var showForSlide = koalaesce.get(OPTION_MAP, optionKey, 'showForSlide');
      if (showForSlide && !showForSlide(this.props)){
        //console.log('no show', this.props);
        return;
      }

      var convert = OPTION_MAP[optionKey].convertFromDb;
      optionValue = convert? convert(optionValue): optionValue;

      if (optionKey === 'showConditions' && !_.isEmpty(optionValue)){
        optionElements.push(<div key={optionKey}>
          <i className='fa fa-clock-o' /> Has Condition(s)
        </div>);
      } else if (optionKey === 'eventSlides' && !_.isEmpty(optionValue)){
        optionElements.push(<div key={optionKey}>
          <i className='fa fa-flag-checkered' /> Has Custom Race Event Slide(s)
        </div>);
      } else if (optionKey === 'trackId' || optionKey === 'track'){
        optionElements.push(
          <div key={optionKey}>
            {'Track: '}
            {_.find(this.props.trackList, { value: optionValue })?
              _.find(this.props.trackList, { value: optionValue }).label
              : <span style={{ color: 'red' }}>
                  Not set! Click the <strong>Channel Settings</strong> tab to set the track.
                </span>}
          </div>
        );
      } else if (optionKey === 'range'){
        optionElements.push(<div key={optionKey}>
          {'Range: ' + RANGE_MAP[optionValue]}
        </div>);
      } else if (optionKey === 'theme'){
        optionElements.push(<div key={optionKey}>
          {'Theme: ' + (optionValue === 'big'? 'Big': 'Classic')}
        </div>);
      } else if (optionKey === 'backgroundUrl' && optionValue){
        optionElements.push(<div key={optionKey}>
          {'Background: '}
          <img
            key={optionKey}
            src={optionValue}
            style={{ maxHeight: '2em' }}
          />
        </div>);
      } else if (optionKey === 'url' && optionValue && this.props.typeId === 'image'){
        optionElements.push(
          <div key={optionKey}>
            <img
              key={optionKey}
              src={optionValue}
              style={{ width: '100%', maxHeight: 150 }}
            />
          </div>
        );
      } else if (OPTION_MAP[optionKey].type === 'boolean'){
        if (optionValue){
          optionElements.push(
            <div key={optionKey}>
              <i className='fa fa-check' />{'  '}
              {OPTION_MAP[optionKey].label || OPTION_MAP[optionKey]}
            </div>
          );
        }
      } else if (optionKey !== 'type' && optionValue !== ''){
        optionElements.push(
          <div key={optionKey}>
            {OPTION_MAP[optionKey].label || OPTION_MAP[optionKey]}:{' '}{optionValue}
          </div>
        );
      }
    });

    return <section title={this.state.deleting? 'Click to cancel deleting.': null}
      style={containerStyle} key={this.props.id} tabIndex={0}
      onMouseDown={this.onMouseDown}
      onTouchStart={this.onTouchStart}
      onTouchMove={this.onTouchMove}
      onKeyDown={e => {
        console.log('card key down', e);
        if (this.props.selected){
          e.preventDefault();
        }
      }}
    >
      <div tabIndex={0}
        onKeyDown={e => { console.log('card key down div', e); }}
        className={'container form'
          + (typeof this.props.typeId !== 'undefined'? ' slide': '')}
        style={{
          position: 'relative',
          height: this.props.height,
          width: this.props.width,
          cursor: this.props.cursor,
          overflow: 'hidden',
          boxSizing: 'border-box',
          borderRadius: 8,
          border: this.props.held? 'thick solid hsl(120,50%,70%)': 'thin solid grey',
          backgroundColor: this.props.selected? SELECTED_COLOR: 'white'
        }}
      >
        {!this.state.deleting && <Tooltip element={XButton}
          tooltipOptions={{ title: 'Remove Slide', placement: 'bottom' }}
          style={{ marginTop: 8, fontSize: '1.5em', float: 'right' }}
          onMouseDown={e => {
            this.setState({ deleting: true });
            setTimeout(() => { // deletion state can be cancelled before timeout; see this.onMouseDown callback that sets deleting back to false
              if (this.state.deleting){
                this.props.onDelete({ id: this.props.id });
              }
            }, DELETE_DURATION);
            e.stopPropagation();
            e.preventDefault();
          }}
        />}

        <div style={{ marginTop: 8 }}
          onMouseDown={e => {
            if (this.props.selected){
              e.preventDefault();
            }
          }}
          onTouchStart={this.props.selected? e => { e.stopPropagation(); e.preventDefault(); }: null}
        >
          <div style={{ fontWeight: 'bold' }}>
            {koalaesce.get(SLIDE_MAP, this.props.typeId, 'label')}
          </div>

          {optionElements}
        </div>
      </div>

      {this.props.showRightDrag && <div
        style={{ position: 'absolute', bottom: 10, left: 10, fontWeight: 'bold' }}
      >
        <i className='fa fa-arrow-right' />
      </div>}

      <div style={{ position: 'absolute', bottom: 10, right: 10, fontWeight: 'bold' }}>
        {this.props.showLeftDrag? <i className='fa fa-arrow-left' />
         : this.props.dragging? <i className='fa fa-arrows-h' />
         : this.props.position}
      </div>
    </section>;
  },

  componentDidMount(){
    this.setState({ entering: false });
  },

  onMouseDown(e){
    if (e.button !== 0 || typeof this.props.typeId === 'undefined'){
      return;
    }
    e.preventDefault();
    if (this.state.deleting){
      this.setState({ deleting: false });
    } else {
      this.handleClickOrTouch(e);
    }
  },

  onTouchStart(e){
    e.preventDefault();
    this.handleClickOrTouch(e.touches[0]);
  },

  onTouchMove(e){
    e.preventDefault();
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


var SlideForm = React.createClass({
  getDefaultProps(){
    return { onOptionChange(){} };
  },

  getInitialState(){
    return { selectedTab: 'settings' };
  },

  shouldComponentUpdate(nextProps){
    return !nextProps.delayUpdate;
  },

  render(){
    return <nav className='tabbable inline'>
      <ul className='nav nav-tabs tab-bricky'>
        <li className={this.state.selectedTab === 'settings' && 'active'} dataToggle='tab'
          onClick={this.onSettingsTabClick}
          style={{
            cursor: this.state.selectedTab !== 'settings'? 'pointer': null,
            marginBottom: -2
          }}
        >
          <a><i className="fa fa-gear" />{' '}Settings</a>
        </li>

        <li className={this.state.selectedTab === 'conditions' && 'active'} dataToggle='tab'
          onClick={this.onConditionsTabClick}
          style={{
            cursor: this.state.selectedTab !== 'conditions'? 'pointer': null,
            marginBottom: -2
          }}
        >
          <a><i className="fa fa-clock-o" />{' '}Conditions</a>
        </li>

        {this.props.typeId === 'scoreboard' && <li
          className={this.state.selectedTab === 'advanced' && 'active'} dataToggle='tab'
          onClick={this.onAdvancedTabClick}
          style={{
            cursor: this.state.selectedTab !== 'advanced'? 'pointer': null,
            marginBottom: -2
          }}
        >
          <a><i className="fa fa-flag-checkered" />{' '}Advanced</a>
        </li>}
      </ul>

      {this.renderPanel(this.state.selectedTab)}
    </nav>;
  },

  renderPanel(selectedTab){
    switch (this.state.selectedTab){
      case 'settings':
        var optionElements = { 1: [], 2: [] };

        var optionSet = SLIDE_MAP[this.props.typeId].optionSet;
        var optionsToIgnore = ['showConditions', 'eventSlides', 'type', 'subType', 'originalUrl'];
        optionSet.forEach((optionKey, i) => {
          //var optionKey = field;
          var column = (i < (optionSet.length / 2)? 1: 2);

          if (_.contains(optionsToIgnore, optionKey)){
            return;
          }

          var showForSlide = koalaesce.get(OPTION_MAP, optionKey, 'showForSlide');
          if (showForSlide && !showForSlide(this.props)){
            return;
          }

          var optionValue;
          var convert = koalaesce.get(OPTION_MAP, optionKey, 'convertFromDb');
          optionValue = convert? convert(this.props.options[optionKey]): this.props.options[optionKey];

          var commonProps = {
            ref: optionKey,
            onChange: e => {
              console.log('el chango', e, e.nativeEvent, e.target.validity.valid, e.target.value);
              var changedOption = {};

              if (e.target.validity.valid){
                var val = this.refs[optionKey].getDOMNode().value;
                var convert = koalaesce.get(OPTION_MAP, optionKey, 'convertToDb');
                changedOption[optionKey] = convert? convert(val): val;
              } else {
                var val = '?';
              }

              this.props.onOptionChange(changedOption);
            },
            style: {}
          };

          var inputElement = null;
          if (optionKey === 'html'){
            inputElement = <textarea spellCheck={false}
              className='form-control' style={{ width: '69%', height: 222 }}
              {...commonProps}
              defaultValue={optionValue}
            />;
          } else if (optionKey === 'theme'){
            inputElement = <IRadioGroup ref={optionKey} style={{ display: 'inline', marginRight: 30 }}
              selected={optionValue}
              list={[ { label: 'Classic', value: 'classic' }, { label: 'Big', value: 'big' } ]}
              onFunnelEvent={(e, props, state, optionProps) => {
                var change = {};
                change[optionKey] = optionProps.item.value;
                this.props.onOptionChange(change);
              }}
            />;
          } else if (optionKey === 'range') {
            inputElement = <IRadioGroup ref={optionKey} style={{ marginRight: 30 }}
              selected={optionValue}
              list={_.transform(RANGE_MAP, (result, label, key) => {
                result.push({ label, value: key });
              }, [])}
              onFunnelEvent={(e, props, state, optionProps) => {
                var change = {};
                change[optionKey] = optionProps.item.value;
                this.props.onOptionChange(change);
              }}
            />;
          } else if (optionKey === 'gender'){
            inputElement = <IRadioGroup ref={optionKey} style={{ display: 'inline', marginRight: 30 }}
              selected={optionValue}
              list={[ { label: 'Male', value: 'male' }, { label: 'Female', value: 'female' }, { label: 'Either', value: '' } ]}
              onFunnelEvent={(e, props, state, optionProps) => {
                var change = {};
                change[optionKey] = optionProps.item.value;
                this.props.onOptionChange(change);
              }}
            />;
          } else if (optionKey === 'trackId' || optionKey === 'track'){
            inputElement = <div
              style={{ marginRight: 0.1, width: '30%', display: 'inline' }}
            >
              <Select
                list={this.props.trackList}
                options={{ allowClear: false }}
                selectedId={optionValue}
                style={{ width: '69%' }}
                onEvent={e => {
                  var change = {};
                  change[optionKey] = ~~e.val;
                  this.props.onOptionChange(change);
                }}
              />
            </div>;
          } else if (optionKey === 'backgroundUrl'){
            inputElement = <span>
              {optionValue? <img style={{ height: '3em' }} src={unescape(optionValue)} />: null}{'  '}
              <Uploader accept='image/*'
                url={'/admin/channel/images/update'}
                value={optionValue? 'Change Background': 'Upload Background Image'}
                onUploadStart={ e => { this.props.onUploadStart(); }}
                onUpload={e => {
                  this.props.onOptionChange({
                    backgroundUrl: window.location.origin + IMAGE_PATH + e.fileName
                  });
                }}
                onError={e => {
                  this.props.onImageUploadError();
                }}
              />
            </span>;
          } else if (optionKey === 'fastestRacerColor' || optionKey === 'textLabelsColor' || optionKey === 'textDataColor'){
            // TODO: native colorpicker and/or JS one (stupid IE)
            inputElement = <div>
              <SmartInput className='form-control' spellCheck={false}
                defaultValue={optionValue}
                {...commonProps}
                style={{ display: 'inline-block', width: '70%' }}
                pattern='^[0-9a-fA-F]{3,6}$'
                ref={optionKey}
              />
              <div style={{
                display: 'inline-block',
                width: '25%',
                backgroundColor: '#111111',
                color: '#' + optionValue,
                textAlign: 'center',
                fontFamily: 'Noto Sans, sans-serif'
              }}>
                PREVIEW
              </div>
            </div>
          } else if (false && optionKey === 'locale') { // TODO: dropdown for locale
            inputElement = <div
              style={{ marginRight: 0.1, width: '30%', display: 'inline' }}
            >
              <Select
                list={[{ value: 'en-US' }]}
                selectedId={optionValue}
                options={{ allowClear: true }}
                style={{ width: '69%' }}
                onEvent={e => {
                  this.props.onOptionChange({
                    trackId: ~~e.val
                  });
                }}
              />
            </div>;
          } else if (optionKey === 'url'){
            if (this.props.typeId === 'image'){
              inputElement = <span>
                <Uploader accept='image/*'
                  url={'/admin/channel/images/update'}
                  value={this.props.options.url? 'Change Image': 'Upload Image'}
                  onUploadStart={ e => { this.props.onUploadStart(); }}
                  onUpload={e => {
                    this.props.onOptionChange({
                      url: window.location.origin + IMAGE_PATH + e.fileName
                    });
                  }}
                  onError={e => {
                    this.props.onImageUploadError();
                  }}
                />
                {optionValue? <img style={{ width: '100%' }} src={unescape(optionValue)} />: null}
              </span>;
            } else {
              inputElement = <SmartInput type='url'
                pattern='.+\:\/\/.+\..+'
                spellCheck={false}
                className='form-control' style={{}/*{ width: '69%', height: 222 }*/}
                {...commonProps}
                defaultValue={optionValue}
              />;
            }
          } else if (OPTION_MAP[optionKey].type === 'boolean'){
            inputElement = <div style={{ display: 'inline' }}>
              <Check
                checked={optionValue || optionValue == 'true'}
                onEvent={e => {
                  var changedOption = {};
                  if (e.type === 'ifChecked'){
                    changedOption[optionKey] = 1;
                  } else if (e.type === 'ifUnchecked'){
                    changedOption[optionKey] = 0;
                  }
                  this.props.onOptionChange(changedOption);
                }}
                onFunnelEvent={e => {
                  var changedOption = {};
                  if (e.type === 'ifChecked'){
                    changedOption[optionKey] = 1;
                  } else if (e.type === 'ifUnchecked'){
                    changedOption[optionKey] = 0;
                  }
                  this.props.onOptionChange(changedOption);
                }}
                ref={optionKey}
              />
            </div>;
          } else {
            if (OPTION_MAP[optionKey].type === 'number'){
              commonProps.type = 'number';
              commonProps.min = OPTION_MAP[optionKey].min || 0;
              commonProps.style.width = '49%';
            }

            commonProps.pattern = OPTION_MAP[optionKey].pattern;

            inputElement = <SmartInput
              className='form-control'
              defaultValue={optionValue}
              {...commonProps}
              ref={optionKey}
            />
          }

          optionElements[column].push(
            <div key={optionKey}
              className='col-xs-12 form-group'
              style={{ marginTop: '1em', fontWeight: 'bold' }}
              onSubmit={e => { e.preventDefault(); }}
            >

              {optionKey !== 'originalUrl' && optionKey !== 'type'
              && <label popover={true} className='col-xs-12 col-sm-4 control-label' style={{ width: '30%' }}>
                {(optionKey === 'url' && this.props.typeId === 'image')? 'Image': (OPTION_MAP[optionKey].label || OPTION_MAP[optionKey])}
              </label>}

              <div className='col-xs-12 col-sm-8'>
                {inputElement}
                {!(this.props.type === 'image' && optionKey === 'url') && <span className='help-block text-left' style={{ fontWeight: 'normal' }}
                  dangerouslySetInnerHTML={{__html: koalaesce.getOrDefault(OPTION_MAP, () => '', optionKey, 'tip')(this.props)}}
                />}
              </div>

            </div>
          );
        });

        return <div className='tab-content row' style={{ overflowX: 'hidden' }}>
          <h3 className='col-xs-12'>
            Settings for Slide #{this.props.index+1} ({SLIDE_MAP[this.props.typeId].label || ''}){'  '}
            <Tooltip element='a' href={this.props.options.url} target='_blank' title='Preview Slide'>
              <i className='fa fa-external-link-square' />
            </Tooltip>
          </h3>
          <form className='form-horizontal col-xs-12 col-lg-6'>
            {optionElements[1]}

            {this.props.typeId === 'image' && <div style={{ width: '42%' }}>

            </div>}

            {this.props.typeId === 'video' && <div style={{ width: '42%' }}>
              <Uploader accept='video/*'
                url={'/admin/channel/videos/update'}
                fileName={'speedscreen-slide-' + this.props.channelId + '-' + this.props.lineup + '-' + this.props.index}
                value='Change Video'
                onUploadStart={ e => { this.props.onUploadStart(); }}
                onUpload={e => {
                  this.props.onOptionChange({
                    originalUrl: config.origin + VIDEO_PATH + e.fileName
                  });
                }}
                onError={e => {
                  this.props.onVideoUploadError();
                }}
              />
            </div>}
          </form>

          <form className='form-horizontal col-xs-12 col-lg-6'>
            {optionElements[2]}
          </form>
        </div>;
      break;

      case 'conditions':
        var bs = global.bootstrapSize;
        var wW = global.windowWidth;
        var checkboxHeight = 25;

        return <div className='row tab-content'>
          <div className='col-xs-12'>
            <h3 className='pull-left' style={{ width: '100%' }}>
              Conditions for Slide #{this.props.index+1} ({SLIDE_MAP[this.props.typeId].label || ''})

              {!_.isEmpty(koalaesce.get(this.props, 'options', 'showConditions')) && <button className='btn btn-warning pull-right'
                onClick={() => {
                  var confirmed = window.confirm('Are you sure you want to clear all conditions?');
                  if (confirmed){
                    this.props.onOptionChange({ showConditions: {} });
                  }
                }}
              >
                Clear All Conditions
              </button>}
            </h3>

          </div>

          <div className='col-xs-12 col-lg-6'>
            <h4 style={{ fontWeight: 'bold' }}>Repeating</h4>

            <div className='row'>
              <div className='col-lg-6'>
                <label className='control-label' style={{ fontWeight: 'bold' }}>Start Time  </label>
                <TimePicker onChange={this.onRepeatableStartChange}
                  value={koalaesce.get(this.props.options, 'showConditions', 'repeatable', 'startTime')}
                /><br/>
              </div>

              <div className='col-lg-6'>
                <label className='control-label' style={{ fontWeight: 'bold' }}>End Time  </label>
                <TimePicker onChange={this.onRepeatableEndChange}
                  value={koalaesce.get(this.props.options, 'showConditions', 'repeatable', 'endTime')}
                /><br/>
              </div>


              <div className='col-xs-12 col-lg-6'>
                <h5 style={{ fontWeight: 'bold' }}>Only for These Days of the Week</h5>
                <div
                  style={{
                    display: 'flex', flexFlow: 'column wrap',
                    height: checkboxHeight * (bs !== 'xs'? 4: 7)
                  }}
                >
                  {this.createCheckboxSet(DAYS, { height: checkboxHeight }, 'daysOfTheWeek')}
                </div><br/>
              </div>

              <div className='col-xs-12 col-lg-6'>
                <h5 style={{ fontWeight: 'bold' }}>Only for These Months</h5>
                <div
                  style={{
                    display: 'flex', flexFlow: 'column wrap',
                    height: checkboxHeight * (bs !== 'xs'? 6: 12)
                  }}
                >
                  {this.createCheckboxSet(MONTHS, { height: checkboxHeight }, 'months')}
                </div><br/>
              </div>

              <div className='col-xs-12'>
                <h5 style={{ fontWeight: 'bold' }}>Only for These Days of the Month</h5>
                <div
                  style={{
                    display: 'flex', flexFlow: 'column wrap',
                    height: checkboxHeight * (bs === 'lg'? 8: bs === 'md'? 11: bs === 'sm'? 11: 31)//(_.contains(['md', 'lg'], findBootstrapEnvironment()) && 140)
                  }}
                >
                  {this.createCheckboxSet(getOrdinalArray(), { height: checkboxHeight }, 'daysOfTheMonth')}
                </div>
              </div>
            </div>
          </div>

          <div className='hidden-xs hidden-sm hidden-md col-lg-1' />
          <div className='col-xs-12 hidden-lg' style={{ height: '2.5em' }}></div>

          <div className='col-xs-12 col-lg-5'>
            <h4 style={{ fontWeight: 'bold' }}>One-Time Range</h4>

            <div className='row col-xs-12'>
              <h5 style={{ fontWeight: 'bold', fontSize: 16 }} className='control-label'>Start</h5>
              <div className='row form-horizontal'>
                <label className='control-label col-xs-2'>Date</label>
                <div className='col-xs-10'>
                  <DatePicker defaultToday={false}
                    onSelect={this.onSpecificStartDateChange}
                    date={koalaesce.get(this.props.options, 'showConditions', 'specific', 'startDate')}
                    language={navigator.language}
                  />
                </div>

                <label className='control-label col-xs-2'>Time</label>
                <div className='col-xs-10'>
                  <TimePicker onChange={this.onSpecificStartTimeChange}
                    value={moment(koalaesce.getOrDefault(this.props.options, '00:00', 'showConditions', 'specific', 'startDate')).format('HH:mm')}
                  />
                </div><br/>
              </div>

              <br/>

              <h5 style={{ fontWeight: 'bold', fontSize: 16 }} className='control-label'>End</h5>
              <div className='row form-horizontal'>
                <label className='control-label col-xs-2'>Date</label>
                <div className='col-xs-10'>
                  <DatePicker defaultToday={false} onSelect={this.onSpecificEndDateChange}
                    date={koalaesce.get(this.props.options, 'showConditions', 'specific', 'endDate')}
                    language={navigator.language}
                  />
                </div>

                <label className='control-label col-xs-2'>Time</label>
                <div className='col-xs-10'>
                  <TimePicker onChange={this.onSpecificEndTimeChange}
                    value={moment(koalaesce.getOrDefault(this.props.options, '00:00', 'showConditions', 'specific', 'endDate')).format('HH:mm')}
                  />
                </div>

                {/*<div className='col-xs-12 alert alert-info'>
                  If Repeatable options are set, this slide will only shown during the selected time period, days of the week, months, and/or days of the month.
                  <br/><br/>
                  If a One-Time date/time range is set, the slide will only be shown during the inputted range.
                </div>*/}
              </div>
            </div>
          </div>
        </div>;
      break;

      case 'advanced':
        var eventSlides = this.props.options.eventSlides || {};
        var rows = [];
        _.forOwn(RACE_EVENTS, (eventLabel, eventKey) => {
          var slide = eventSlides[eventKey] || {};
          rows.push(<tr key={eventKey}>
            <td>{eventLabel}</td>
            <td>
              <div style={{ display: 'table', width: '100%' }}>
                <div style={{ display: 'table-cell',  width: 'auto' }}>
                  <SmartInput type='url' ref={eventKey + 'Url'} value={slide.url}
                    className='form-control'
                    onChange={e => {
                      slide.url = this.refs[eventKey + 'Url'].getDOMNode().value;
                      eventSlides[eventKey] = slide;
                      this.props.onOptionChange({ eventSlides });
                    }}
                  />
                </div>

                {slide.url && <div style={{ display: 'table-cell', width: 1, textAlign: 'right' }}>
                  <Tooltip element='a' href={slide.url} target='_blank' title='Preview Slide'
                    style={{ position: 'relative', top: 5, fontSize: 25 }}
                  >
                    <i className='fa fa-external-link-square' />
                  </Tooltip>
                </div>}
              </div>
            </td>
            <td>
              <SmartInput ref={eventKey + 'Duration'} type='number' className='form-control' min={0}
                defaultValue={slide.duration === ''? '': slide.duration? (slide.duration / 1000): slide.duration}
                onChange={e => {
                  var val = e.target.value;
                  if (val === ''){
                    slide.duration = '';
                  } else {
                    slide.duration = val * 1000;
                  }
                  eventSlides[eventKey] = slide;
                  this.props.onOptionChange({ eventSlides });
                }}
              />
            </td>
          </tr>);
        });

        return <div className='tab-content row'><div className='col-xs-12'>
          <h3>Advanced Settings for Slide #{this.props.index+1} ({SLIDE_MAP[this.props.typeId].label || ''})</h3>

          <br/>

          <h4 style={{ fontWeight: 'bold' }}>Race Event Slides</h4>
          <table className='table table-bordered'>
            <colgroup>
              <col span={1} style={{ width: '30%' }} />
              <col span={1} style={{ width: '55%' }} />
              <col span={1} style={{ width: '15%' }} />
            </colgroup>
            <thead>
              <th>Race Event</th>
              <th>Slide URL</th>
              <th>Duration (seconds)</th>
            </thead>
            <tbody>{rows}</tbody>
          </table>
          {/*<div className='alert alert-info'>
            Here, you may set custom slides to show when specific events occur within a single race.
          </div>*/}
        </div></div>;
      break;
    }
  },

  onSettingsTabClick(){
    this.setState({ selectedTab: 'settings' });
  },

  onConditionsTabClick(){
    this.setState({ selectedTab: 'conditions' });
  },

  onAdvancedTabClick(){
    this.setState({ selectedTab: 'advanced' });
  },

  onRepeatableStartChange(e){
    var showConditions = this.props.options.showConditions || {};
    showConditions.repeatable = showConditions.repeatable || {};
    if (e.value == 'none'){
      delete showConditions.repeatable.startTime;
    } else {
      showConditions.repeatable.startTime = e.value || '00:00';
    }
    this.props.onOptionChange({ showConditions });
  },

  onRepeatableEndChange(e){
    var showConditions = this.props.options.showConditions || {};
    showConditions.repeatable = showConditions.repeatable || {};
    showConditions.repeatable.endTime = e.value || '00:00';
    this.props.onOptionChange({ showConditions });
  },

  onSpecificStartDateChange(e){
    var showConditions = this.props.options.showConditions || {};
    showConditions.specific = showConditions.specific || {};
    if (e.date){
      if (showConditions.specific.startDate){
        var newDate = moment(showConditions.specific.startDate);
        newDate.year(e.date.year());
        newDate.month(e.date.month());
        newDate.date(e.date.date());
      } else {
        var newDate = moment({ year: e.date.year(), month: e.date.month(), date: e.date.date() });
      }
      showConditions.specific.startDate = newDate.format('YYYY-MM-DDTHH:mm');
    } else {
      delete showConditions.specific.startDate;
    }
    this.props.onOptionChange({ showConditions });
  },

  onSpecificStartTimeChange(e){
    var showConditions = this.props.options.showConditions || {};
    showConditions.specific = showConditions.specific || {};
    var newDate = moment(showConditions.specific.startDate);

    if (e.value == 'none'){
      showConditions.specific.startDate = newDate
        .format('YYYY-MM-DDTHH:mm').substr(0, 11) + '00:00';
    } else {
      showConditions.specific.startDate = newDate
        .format('YYYY-MM-DDTHH:mm').substr(0, 11) + e.value;
    }
    this.props.onOptionChange({ showConditions });
  },

  onSpecificEndDateChange(e){
    var showConditions = this.props.options.showConditions || {};
    showConditions.specific = showConditions.specific || {};
    if (e.date){
      if (showConditions.specific.endDate){
        var newDate = moment(showConditions.specific.endDate);
        newDate.year(e.date.year());
        newDate.month(e.date.month());
        newDate.date(e.date.date());
      } else {
        var newDate = moment({ year: e.date.year(), month: e.date.month(), date: e.date.date() });
      }
      showConditions.specific.endDate = newDate.format('YYYY-MM-DDTHH:mm');
    } else {
      delete showConditions.specific.endDate;
    }
    this.props.onOptionChange({ showConditions });
  },

  onSpecificEndTimeChange(e){
    var showConditions = this.props.options.showConditions || {};
    showConditions.specific = showConditions.specific || {};
    var newDate = moment(showConditions.specific.endDate);

    if (e.value == 'none'){
      showConditions.specific.endDate = newDate
        .format('YYYY-MM-DDTHH:mm').substr(0, 11) + '00:00';
    } else {
      showConditions.specific.endDate = newDate
        .format('YYYY-MM-DDTHH:mm').substr(0, 11) + e.value;
    }
    this.props.onOptionChange({ showConditions });
  },

  createCheckboxSet(array, style, optionKey){
    var arrayWithValues = array.map((label, i) => ({ label, value: i + 1 }) );
    style.display = 'flex';
    style.textOverflow = 'ellipsis';
    return arrayWithValues.map(item =>
      <div key={item.label + item.value} style={style}>
        <Check
          checked={(koalaesce.get(this.props.options, 'showConditions', 'repeatable', optionKey)
            && _.contains(this.props.options.showConditions.repeatable[optionKey], item.value)) || false}
          onEvent={e => {
            var showConditions = this.props.options.showConditions || {};
            showConditions.repeatable = showConditions.repeatable || {};
            showConditions.repeatable[optionKey] = showConditions.repeatable[optionKey] || [];
            if (e.type === 'ifChecked'){
              if (!_.contains(showConditions.repeatable[optionKey], item.value)){
                showConditions.repeatable[optionKey].push(item.value);
              }
            } else if (e.type === 'ifUnchecked'){
              showConditions.repeatable[optionKey] = _.without(showConditions.repeatable[optionKey], item.value);
            }
            if (!koalaesce.get(showConditions, 'repeatable', optionKey, 'length')){
              delete showConditions.repeatable[optionKey];
            }
            this.props.onOptionChange({ showConditions });
          }}
        />
        <label className='control-label'
          style={{ marginLeft: 2 }}
        >
          {item.label}
        </label>
      </div>
    );

    var groups = _.chunk(arrayWithValues, itemsPerColumn);
    var nodes = groups.map((group, i) => {
      var subNodes = group.map(item =>
        <div className='row' key={item.value}>
          <div className='col-xs-2' key={item.label}>
            <Check
              checked={koalaesce.get(this.props.options, 'showConditions', 'repeatable', optionKey)
                && _.contains(this.props.options.showConditions.repeatable[optionKey], item.value)}
              onEvent={e => {
                var showConditions = this.props.options.showConditions || {};
                showConditions.repeatable = showConditions.repeatable || {};
                showConditions.repeatable[optionKey] = showConditions.repeatable[optionKey] || [];
                if (e.type === 'ifChecked'){
                  if (!_.contains(showConditions.repeatable[optionKey], item.value)){
                    showConditions.repeatable[optionKey].push(item.value);
                  }
                } else if (e.type === 'ifUnchecked'){
                  showConditions.repeatable[optionKey] = _.without(showConditions.repeatable[optionKey], item.value);
                }
                if (!koalaesce.get(showConditions, 'repeatable', optionKey, 'length')){
                  delete showConditions.repeatable[optionKey];
                }
                this.props.onOptionChange({ showConditions });
              }}
            />
          </div>
          <label className='col-xs-8 control-label'
            style={{
              fontWeight: (!koalaesce.get(this.props.options, 'showConditions', 'repeatable', optionKey, 'length')
                          || _.contains(this.props.options.showConditions.repeatable[optionKey], item.value))?
                          'bold': null
            }}
          >
            {item.label}
          </label>
        </div>
      );

      return <div className='col-xs-6' key={i}>
        {subNodes}
      </div>;
    });

    return nodes;
  }
});


module.exports = React.createClass({
  calcSlideBaseX(lineup, i, lineupName){
    return _(lineup).take(i).reduce((runningX, slide, i) => {
      return runningX + slide.width;
    }, 0);
  },

  getDefaultProps(){
    return {
      channel: null,
      slideWidth: 199,
      scoreboardSlideWidth: 288
    };
  },

  getInitialState(){
    return {
      lineup: [],
      raceLineup: [],
      selectedLineup: 'lineup',
      popup: {
        message: null,
        alertClass: null
      },
      newTypeId: 'url',
      tracks: [],
      width: 800,
      showLeftNav: false,
      showRightNav: true,
      rightNavPos: 0,
      slideHeight: undefined,
      edgeDragDirection: null
    };
  },

  render(){
    return <div className='tab-pane container-fluid'
      style={{ overflowY: 'visible' }}
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
      {this.renderAddSlideModal()}

      <Popup {...this.state.popup}
        onDone={e => {
          this.setState({ popup: {} });
        }}
      />

      <nav className='row tabbable inline' style={{ marginBottom: 0, whiteSpace: 'nowrap' }}>
        <ul className='nav nav-tabs nav-justified tab-bricky'>
          <li
            title={(this.state.selectedLineup === 'lineup'? 'You are viewing ': 'Click to view and edit')
              + ' the lineup shown when no races are running.'}
            tooltipOptions={{ placement: 'right' }}
            className={this.state.selectedLineup === 'lineup'? ' active': ''}
            style={{
              cursor: this.state.selectedLineup !== 'lineup'? 'pointer': 'default',
              marginBottom: 0
            }}
            onClick={() => {
              this.setState({ selectedLineup: 'lineup', newTypeId: 'url' });
              this.resetLineupView();
            }}
          >
            <a
              style={{ fontSize: '1.5em', marginBottom: 0,
                backgroundColor: this.state.selectedLineup === 'lineup'? SELECTED_COLOR: undefined
              }}
            >
              <i className="fa fa-bed" />
              {'    '}No Race Running
            </a>
          </li>

          <li
            title={(this.state.selectedLineup === 'raceLineup'? 'You are viewing the ': 'Click to view and edit')
              + ' the lineup shown when a race is running.'}
            tooltipOptions={{ placement: 'right' }}
            className={this.state.selectedLineup === 'raceLineup'? ' active': ''}
            style={{
              cursor: this.state.selectedLineup !== 'raceLineup'? 'pointer': 'default',
              marginBottom: 0
            }}
            onClick={() => {
              this.setState({ selectedLineup: 'raceLineup', newTypeId: 'scoreboard' });
              this.resetLineupView();
            }}
          >
            <a
              style={{ fontSize: '1.5em', marginBottom: 0,
                backgroundColor: this.state.selectedLineup === 'raceLineup'? SELECTED_COLOR: undefined
              }}
            >
              <i className="fa fa-flag-checkered" />
              {'    '}
              During Race
            </a>
          </li>
        </ul>
      </nav>

      <div className='tab-content row'>
        <span
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
          <div ref='lineupContainer' className='col-xs-11'
            style={{
              height: 249,
              overflowX: 'auto',
              overflowY: 'hidden',
              border: 'solid ' + BOOTSTRAP_BLUE,
              borderRadius: 4,
              backgroundColor: 'white',
              boxSizing: 'border-box'
            }}
          >
            {this.renderLineup(this.state.selectedLineup)}
          </div>

          {this.state.showNav && this.state.showLeftNav /*&& this.isMounted()*/
            && this.state[this.state.selectedLineup].length > 1 && <div
            className='fa-stack'
            style={{
              position: 'absolute',
              top: 55,
              height: 0,
              width: 0,
              left: 19,
              zIndex: 9999999,
              fontSize: '6em',
              cursor: 'pointer',
              WebkitUserSelect: 'none', userSelect: 'none'
            }}
          >
            <a
              onClick={e => {
                e.preventDefault(); e.stopPropagation();
                csp.go(function* (){
                  yield csp.put(this.navEvents, { type: 'arrowClick', direction: 'left' });
                }.bind(this))
              }}
            >
              <i className='fa fa-circle fa-stack-1x' style={{ color: 'white' }}/>
              <i className="fa fa-chevron-circle-left fa-stack-1x" />
            </a>
          </div>}

          {this.state.showNav && this.state.showRightNav /*&& this.isMounted()*/
            && this.state[this.state.selectedLineup].length > 1 && <div
            className='fa-stack'
            style={{
              position: 'absolute',
              top: 55,
              height: 0,
              width: 0,
              right: this.state.rightNavPos + 91,
              zIndex: 9999999,
              fontSize: '6em',
              cursor: 'pointer',
              WebkitUserSelect: 'none', userSelect: 'none'
            }}
          >
            <a
              onClick={e => {
                e.preventDefault(); e.stopPropagation();
                csp.go(function* (){
                  yield csp.put(this.navEvents, { type: 'arrowClick', direction: 'right' });
                }.bind(this))
              }}
            >
              <i className='fa fa-circle fa-stack-1x' style={{ color: 'white' }}/>
              <i className="fa fa-chevron-circle-right fa-stack-1x" />
            </a>
          </div>}
        </span>

        <Tooltip element='div' className='col-xs-1' style={{ margin: 0, padding: 0 }} ref='add'
          title='Add Slide' tooltipOptions={{ placement: 'left' }}
        >
          <button className='btn btn-info'
            style={{ width: '100%', height: 249, position: 'relative', top: -2 }}
            onClick={() => {
              this.setState({ showAddSlideModal: true });
            }}
          >
            <i className="fa fa-plus fa-2x"/>
          </button>
        </Tooltip>

        <div className='col-xs-12' style={{ padding: 0 }}><br/><br/>
          {(() => {
            var selectedLineup = this.state[this.state.selectedLineup];
            if (selectedLineup.length){
              var selectedSlideIndex = _.findIndex(selectedLineup, 'selected');
              if (selectedSlideIndex === -1){
                return <div className='alert alert-info'>Select a slide to edit it. Drag slides to change their order.</div>;
              }

              var selectedSlide = selectedLineup[selectedSlideIndex];
              return this.renderForm(selectedSlide, selectedSlideIndex);
            }
          })()}
        </div>
      </div>

      <br/>

      <div className='col-xs-12'><Tooltip element='button' className='btn btn-info'
        tooltipOptions={{
          title: 'Save both lineups for this channel only.',
          placement: 'right'
        }}
        disabled={this.state.requestActive}
        onClick={() => {
          csp.go(function* (){
            yield csp.put(this.submissionEvents, { type: 'save' });
          }.bind(this));
        }}
      >
        {/*<i className="fa fa-floppy-o" /> */}Save Lineups
      </Tooltip></div>
    </div>;
  },

  renderAddSlideModal(){
    var slideList = getSlideTypeSelectList(this.state.selectedLineup === 'raceLineup');

    var radioHeight =(100 / (slideList.length + 1) * 2) + '%';

    return <Modal element='div' show={this.state.showAddSlideModal} className='modal fade'>
      <div className='modal-dialog modal-lg'>
        <div className='modal-content' style={{ height: '80vh' }}>
          <h2 className='modal-title' style={{ padding: 20 }}>
            Add Slide to {this.props.initialChannelData.name}<br/>
            ({this.state.selectedLineup === 'raceLineup'? 'During Race': 'No Race Running'})
          </h2>

          <div className='modal-body' style={{ height: '50vh' }}>
            <IRadioGroup
              container={{ style: {
                display: 'flex', flexFlow: 'column wrap', height: '50vh'
              } }}
              selected={this.state.newTypeId}
              style={{ height: radioHeight }}
              list={slideList}
              onFunnelEvent={(e, props, state, optionProps) => {
                this.setState({ newTypeId: optionProps.item.value });
              }}
            />
          </div>

          <div className='modal-footer'>
            <button className='btn btn-success'
              onClick={this.addSlide}
            >
              Add Slide
            </button>
            <button className='btn btn-warning'
              onClick={() => { this.setState({ showAddSlideModal: false }); }}
            >
              Cancel
            </button>
          </div>
        </div>
      </div>
    </Modal>;
  },

  renderLineup(lineupName){
    if (!this.state[lineupName].length){
      return <div><br/><div className='alert alert-info'>This lineup is empty. Click the + icon to add a slide.</div></div>;
    }

    var lineupElements = this.state[lineupName].map((slide, i) => {
      return <SlideCard {...slide} trackList={this.props.trackList} channelId={this.props.screenTemplateId}
        height={this.state.slideHeight || 220}
        cursor={slide.held? 'ew-resize': 'pointer'}
        key={slide.id} id={slide.id} ref={slide.id}
        position={i + 1}
        width={slide.width}
        showLeftDrag={slide.held && this.state.edgeDragDirection === 'left'}
        showRightDrag={slide.held && this.state.edgeDragDirection === 'right'}
        onMoveStart={e => {
          csp.go(function* (){
            yield csp.put(this.userEvents, {
              type: 'dragStart',
              slideIndex: i,
              dragOriginX: e.dragOriginX
            });
          }.bind(this));
        }}

        onDelete={e => {
          var newLineup = _.reject(
            this.state[this.state.selectedLineup],
            slide => slide.id === e.id
          );
          var newState = {};
          newState[this.state.selectedLineup] = this.prepRawLineupForRender(newLineup, null, this.state.selectedLineup);
          this.setState(newState);
          csp.go(function* (){
            yield csp.put(this.userEvents, { type: 'lineupChange' });
          }.bind(this));
        }}
      />;
    });

    return <div ref='lineup'
      style={{
        position: 'relative',
        overflowY: 'visible',
        WebkitOverflowScrolling: 'touch',
        width: this.state.dragMode? _.reduce(
                 this.state[this.state.selectedLineup],
                 (sum, slide) => sum + slide.width, 0
               ): undefined
      }}
    >
      {lineupElements}
    </div>;
  },

  renderForm(slide, index){
    if (typeof slide === 'undefined'){
      return <div>Select a slide to edit it.</div>;
    }

    return <span>
      <SlideForm {...slide} key={slide.id} index={index} delayUpdate={this.state.dragMode || this.state.showNav}
        channelId={this.props.channelId} trackList={this.props.trackList} lineup={this.state.selectedLineup}
        onOptionChange={e => {
          var index = _.findIndex(this.state[this.state.selectedLineup], 'selected');

          this.debounceUpdateUrl(index);

          var newState = {};

          var change = {};
          change[index] = {};

          if ((e.url && slide.typeId === 'image') || e.backgroundUrl){
            change[index].cacheBusterId = { $set: Math.random() };

            if ((e.backgroundUrl && e.backgroundUrl != slide.options.backgroundUrl) || (e.url && e.url != slide.options.url)){
              newState.popup = {
                message: 'Image successfully uploaded!',
                alertClass: 'alert-success'
              };
            } else {
              newState.popup = {
                message: 'Image successfully changed!',
                alertClass: 'alert-success'
              };
            }
          } else if (e.originalUrl && slide.typeId === 'video'){
            newState.popup = {
              message: 'Video successfully uploaded!',
              alertClass: 'alert-info'
            };
          }

          if (e.isCustomScoreboard){
            if (!koalaesce.get(slide, 'options', 'trackId')){
              e.trackId = koalaesce.get(OPTION_MAP, 'trackId', 'default');
            }
          }

          change[index].options = { $merge: e };

          var lineup = React.addons.update(
            this.state[this.state.selectedLineup],
            change
          );

          newState[this.state.selectedLineup] = lineup;

          this.setState(newState);
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
      />
    </span>;
  },

  addSlide(){
    var newType = this.state.newTypeId;
    var lineup = this.state.selectedLineup;

    if (newType === 'scoreboard' && lineup !== 'raceLineup'){
      return;
    }

    var newSlide = {
      type: (newType !== 'scoreboard' && newType !== 'image')? 'url': newType,
      typeId: newType,
      options: {}
    };

    if (newType !== 'scoreboard' && newType !== 'image'){
      newSlide.subType = newType;
    }

    SLIDE_MAP[newType].optionSet.forEach(optionKey => {
      var defaultValue = koalaesce.get(OPTION_MAP, optionKey, 'default');
      if (defaultValue !== null && typeof defaultValue !== 'undefined'){
        newSlide.options[optionKey] = defaultValue;
      } /*else {
        newSlide.options[optionKey] = '';
      }*/
    });

    var hiddenOpts = SLIDE_MAP[newType].fixedOptions;
    if (hiddenOpts){
      newSlide.options = _.extend(newSlide.options, hiddenOpts);
    }

    // video is stored as url type in db
    if (newSlide.type === 'video') {
      newSlide.options.originalUrl = 'none.mp4';
      newSlide.type = 'url';
    }

    var newState = { showAddSlideModal: false };
    newState[lineup] = this.prepRawLineupForRender(this.state[lineup].concat([newSlide]), lineup.length, lineup);
    var length = newState[lineup].length;
    newState[lineup].forEach((slide, i) => {
      slide.selected = i === length - 1;
    });

    this.setState(newState, () => {
      var prevScrollX = this.$lineup.scrollLeft();
      var move = () => {
        this.$lineup.scrollLeft(prevScrollX + (5 * newState[lineup].length));
        var newScrollX = this.$lineup.scrollLeft();
        if (prevScrollX !== newScrollX){
          requestAnimationFrame(move);
        }
        prevScrollX = newScrollX;
      };
      move();
    });
  },

  prepRawLineupForRender(rawLineup, selectedSlideIndex, lineupName){
    var preppedLineup = _.cloneDeep(rawLineup);
    preppedLineup.forEach((slide, i) => {
      if (!slide.cacheBusterId) slide.cacheBusterId = Math.random();
      if (!slide.options) slide.options = {};

      if (i === selectedSlideIndex){
        slide.selected = true;
      }

      if (slide.type === 'html' || koalaesce.get(slide, 'options', 'html')){
        slide.type = 'url';
        slide.subType = 'url';
        slide.typeId = 'url';
        if (slide.options && slide.options.html){
          delete slide.options.html;
        }
      }

      if (!slide.id){
        slide.id = createSlideId.next().value;


        // this is a bit messy because it's checking and correcting for past JSON format mistakes
        if (slide.subType){
          slide.typeId = slide.subType;
          if (slide.subType === 'scoreboard' || slide.subType === 'image'){
            delete slide.subType;
          }
        } else if (slide.options.subType){
          slide.subType = slide.options.subType;
          slide.typeId = slide.options.subType;
          delete slide.options.subType;
        } else if (slide.type === 'scoreboard' || slide.type === 'image'){
          slide.typeId = slide.type;
          delete slide.options.type;
        } else if (slide.type === 'url'){
          if (slide.options.type === 'scoreboard'){
            slide.type = 'scoreboard';
            slide.typeId = 'scoreboard';
            delete slide.options.type;
          } else if (slide.options.type === 'image'){
            slide.type = 'image';
            slide.typeId = 'image';
            delete slide.options.type;
          } else if (slide.options.type){
            slide.typeId = slide.options.type;
            slide.options.subType = slide.options.type;
            delete slide.options.type;
          } else if (slide.options && /\.((mp4)|(m4v)|(webm)|(ogv))$/.test(slide.options.originalUrl)){
            // ^ originalUrl ends in supported video format, strongly indicating it's a video
            slide.type = 'url';
            slide.options.subType = 'video';
            slide.typeId = 'video';
          } else {
            slide.type = 'url';
            slide.typeId = 'url';
          }
        } else {
          if (slide.options.type === 'scoreboard'){
            slide.type = 'scoreboard';
            slide.typeId = 'scoreboard';
            slide.options.subType = slide.options.type;
            delete slide.options.type;
          } else if (slide.options.type === 'image'){
            slide.type = 'image';
            slide.typeId = 'image';
            slide.options.subType = slide.options.type;
            delete slide.options.type;
          } else {
            if (slide.options.type){
              slide.typeId = slide.options.type || slide.type || 'url';
              slide.options.subType = slide.options.type;
              delete slide.options.type;
            } else {
              slide.typeId = slide.type;
              slide.options.subType = slide.type;
            }

            slide.type = 'url';
          }
        }

        this.updateUrl(slide);
      }

      if (slide.type === 'scoreboard'){
        slide.width = this.props.scoreboardSlideWidth;
      } else {
        slide.width = this.props.slideWidth;
      }

      slide.x = this.calcSlideBaseX(preppedLineup, i, lineupName);

    });

    return preppedLineup;
  },

  updateUrl(slide){
    var opts = slide.options;

    var getBaseUrl = koalaesce.get(SLIDE_MAP, slide.typeId, 'getBaseUrl');

    if (!getBaseUrl){
      return;
    }

    var url = getBaseUrl(slide);

    var starting = true;
    var optKeysToIgnore = ['url', 'showConditions', 'eventSlides', 'subType', 'type', 'showPicture'];
    for (var optKey in opts){
      if (_.contains(optKeysToIgnore, optKey) || opts[optKey] === null || opts[optKey] === ''
          || typeof opts[optKey] === 'undefined'){
        continue;
      }

      url += (starting? '?': '&') + optKey + '=' + opts[optKey];

      starting = false;
    }

    slide.options.url = url;
  },

  globalCspChans: [
    'submissionEvents',
    'userEvents',
    'navEvents'
  ],

  getSaveableChannelData(){
    var self = this;

    var prepLineupForSave = lineup => {
      return _.cloneDeep(lineup).map(slide => {
        if (slide.typeId === 'scoreboard'){
          var eventSlides = koalaesce.get(slide, 'options', 'eventSlides');
          if (eventSlides){
            _.forOwn(eventSlides, (eSlide, key) => {
              if (!eSlide.url){
                delete eventSlides[key];
              }
            });

            if (_.every(eventSlides, slide => !slide.url)){
              delete slide.options.eventSlides;
            }
          }
        }

        if (slide.typeId === 'raceUrl'){
          if (slide.options.isCustomScoreboard){
            slide.type = 'scoreboard';
          } else {
            slide.type = 'url';
          }
        }

        var showConditions = slide.options.showConditions;
        if (showConditions){
          var repeatable = slide.options.showConditions.repeatable;
          if (repeatable){
            if (repeatable.daysOfTheWeek && _.isEmpty(repeatable.daysOfTheWeek)){
              delete repeatable.daysOfTheWeek;
            }
            if (repeatable.daysOfTheMonth && _.isEmpty(repeatable.daysOfTheMonth)){
              delete repeatable.daysOfTheMonth;
            }
            if (repeatable.months && _.isEmpty(repeatable.months)){
              delete repeatable.months;
            }

            if (_.isEmpty(repeatable)){
              delete showConditions.repeatable;
            }
          }

          if (showConditions.specific && _.isEmpty(showConditions.specific)){
            delete showConditions.specific;
          }

          if (_.isEmpty(showConditions)){
            delete slide.options.showConditions;
          }
        }

        if (SLIDE_MAP[slide.typeId].optionSet){
          SLIDE_MAP[slide.typeId].optionSet.forEach(optionKey => {
            var showForSlide = koalaesce.get(OPTION_MAP, optionKey, 'showForSlide');
            if (showForSlide && !showForSlide(slide)){
              delete slide.options[optionKey];
            }
          });
        }

        self.updateUrl(slide);
        return _.pick(slide, ['type', 'subType', 'options']);
      });
    };

    var newLineup = prepLineupForSave(self.state.lineup);
    var newRaceLineup = prepLineupForSave(self.state.raceLineup);

    var channelDataAfterSave = _.extend(_.cloneDeep(self.channelDataBeforeSave), {
      timelines: {
        regular: {
          slides: newLineup
        },
        races: {
          slides: newRaceLineup
        }
      }
    });

    channelDataAfterSave.hash = jsonHash.digest(_.omit(channelDataAfterSave, ['name', 'hash']));

    return channelDataAfterSave;
  },

  resetLineupView(){
    var change = {
      lineup: {},
      raceLineup: {}
    };
    var lineupKeys = ['lineup', 'raceLineup'];
    var oldLineupState = _.pick(this.state, lineupKeys);
    lineupKeys.forEach(key => {
      oldLineupState[key].forEach((slide, i) => {
        change[key][i] = { selected: { $set: false } };
      });
    });
    var newState = React.addons.update(oldLineupState, change);
    this.setState(newState);

    $(this.refs.lineupContainer.getDOMNode()).scrollLeft(0);
    this.updateNav();
  },

  componentWillMount(){
    var self = this;

    this.debounceUpdateUrl = _.debounce(slideIndex => {
      var lineup = this.state[this.state.selectedLineup];
      var slide = lineup[slideIndex];
      this.updateUrl(slide);
      var newState = {};
      newState[this.state.selectedLineup] = lineup;
      this.setState(newState);
    }, 1111);

    this.globalCspChans.forEach(chan => {
      this[chan] = csp.chan();
    });

    csp.go(function* (){
      var channelRequestEvents = csp.chan();
      var detailRequestEvents = csp.chan();

      var lineup, raceLineup;

      var mostRecentSubmissionEvent = { type: 'none' };

      var channelUrl = config.apiURL + 'speedscreenchannels/' + self.props.channelId + '.json?key=' + config.privateKey;

      var selectedSlideIndex, selectedRaceSlideIndex;

      while(true){
        if (mostRecentSubmissionEvent.type === 'none' && self.props.initialChannelData) {
          self.channelDataBeforeSave = self.props.initialChannelData;
        } else {
          $.get(channelUrl).then(
            response => {
              csp.go(function* (){
                yield csp.put(channelRequestEvents, { response });
              });
            },
            response => {
              csp.go(function* (){
                yield csp.put(channelRequestEvents, { error: true, response });
              });
            }
          );

          var channelEvent = yield csp.take(channelRequestEvents); if (channelEvent === csp.CLOSED) return;

          self.channelDataBeforeSave = JSON.parse(channelEvent.response.channelData);

          selectedSlideIndex = _.findIndex(self.state.lineup, 'selected');// || 0;
          selectedRaceSlideIndex = _.findIndex(self.state.raceLineup, 'selected');// || 0;
        }

        var rawLineup = self.channelDataBeforeSave.timelines.regular.slides;
        lineup = self.prepRawLineupForRender(rawLineup, selectedSlideIndex, 'lineup');

        var rawRaceLineup = self.channelDataBeforeSave.timelines.races.slides;
        raceLineup = self.prepRawLineupForRender(rawRaceLineup, selectedRaceSlideIndex, 'raceLineup');

        var updateURL = config.apiURL + 'speedscreenchannels/' + self.props.channelId + '?key=' + config.privateKey;

        var initEvents = csp.chan();

        self.setState({ lineup, raceLineup });

        csp.go(function* (){
          yield csp.put(self.userEvents, { type: 'lineupChange' });
        });

        if (mostRecentSubmissionEvent.type === 'none'){
          // kludge: poll state until updated due to above callback not firing in setState above
          var initEvent = false;
          while (!initEvent){
            var timer = csp.timeout(888);
            initEvent = yield csp.alts([timer, initEvents]); if (initEvent === csp.CLOSED) return;
            if (initEvent.channel === timer || !initEvent.value){
              var origLineup = koalaesce.get(self, 'channelDataBeforeSave', 'timelines', 'regular', 'slides');
              if (self.state.lineup.length === origLineup.length){
                initEvent = true;
              }
            }
          }

          // save on first time ever loading this new speed screen admin to get it in sync
          var initChannelData = self.getSaveableChannelData();
          if (initChannelData.hash !== self.channelDataBeforeSave.hash){
            //console.log('will change hash', self.channelDataBeforeSave, initChannelData);

            self.channelDataBeforeSave = initChannelData;

            var initDataCleanup = csp.chan();

            $.ajax({
              type: 'PUT',
              url: updateURL,
              data: { channelData: JSON.stringify(initChannelData) }
            })
            .then(response => { csp.go(function *(){ yield csp.put(initDataCleanup); }); });
            yield initDataCleanup; if (initDataCleanup === csp.CLOSED) return;
          }
        }

        var submissionEvent = yield self.submissionEvents; if (submissionEvent === csp.CLOSED) return;

        var submissionRequestEvents = csp.chan();

        if (submissionEvent !== csp.CLOSED && submissionEvent.type === 'save'){
          self.setState({ requestActive: true });

          var channelDataAfterSave = self.getSaveableChannelData();

          var channelData = JSON.stringify(channelDataAfterSave);
          $.ajax({
            type: 'PUT',
            url: updateURL,
            data: { channelData }
          })
          .then(
            response => {
              csp.go(function* (){
                yield csp.put(submissionRequestEvents, {
                  type: 'save',
                  message: 'Lineups successfully saved!',
                  autoFade: true
                });
              });
            },
            response => {
              csp.go(function* (){
                yield csp.put(submissionRequestEvents, { error: true, type: 'save' });
              });
            }
          );
        }

        mostRecentSubmissionEvent = yield csp.take(submissionRequestEvents); if (mostRecentSubmissionEvent === csp.CLOSED) return;

        if (mostRecentSubmissionEvent.error){
          self.setState({
            requestActive: false,
            popup: {
              message: 'Action failed.',
              alertClass: 'alert-danger'
            }
          });
        } else {
          self.setState({
            popup: {
              requestActive: false,
              message: mostRecentSubmissionEvent.message + ' (' + moment().format('h:mm:ss a') + ')',
              alertClass: 'alert-success'
            }
          });
        }

        self.setState({ requestActive: false });

        if (mostRecentSubmissionEvent !== csp.CLOSED && mostRecentSubmissionEvent.type === 'create'){
          var el = self.refs.lineupContainer.getDOMNode();
          $(el).scrollLeft(el.scrollWidth + 9999); // scroll to the end where the new slide will be
        }
      }
    });
  },

  componentDidMount(){
    var self = this;

    $(window).bind('beforeunload', () => {
      var latestChannelData = self.getSaveableChannelData();
      if (latestChannelData.hash !== self.channelDataBeforeSave.hash){
        return 'One or more channels have unsaved lineup changes.';
      }
    });

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

    var readWindowState = _.debounce(() => {
      global.windowWidth = $(window).width();
      global.windowHeight = $(window).height();
      global.bootstrapSize = findBootstrapEnvironment();
      //this.forceUpdate();
      if (this.refs.add){
        this.setState({ rightNavPos: $(this.refs.add.getDOMNode()).width() });
      }
    }, 101);
    readWindowState();
    $(window).resize(readWindowState);

    this.lineupNode = this.refs.lineupContainer.getDOMNode();
    this.$lineup = $(this.lineupNode);

    // CHANNELS FOR AUTO-SCROLL
    this.autoScrollRaw = csp.chan(csp.buffers.sliding(97));
    this.autoScrollEvents = csp.operations.unique(this.autoScrollRaw);
    this.autoScrollInit = csp.chan(1);
    this.autoScrollEnd = csp.chan(1);

    // DRAG-DROP-SELECT LOOP
    var dragOriginX;

    csp.go(function* (){
      var e, dragSlideIndex, moveCount , lineup;

      while (true){
        lineup = self.state[self.state.selectedLineup];

        do {
          e = yield csp.take(self.userEvents); if (e === csp.CLOSED) return;

          if (e.type === 'lineupChange'){
            lineup = self.state[self.state.selectedLineup];
          }
        } while (e !== csp.CLOSED && e.type !== 'dragStart'); // wish I could write "until (e.type === 'dragStart')" instead...

        dragSlideIndex = e.slideIndex;
        dragOriginX = e.dragOriginX;
        var baseX = self.calcSlideBaseX(lineup, dragSlideIndex);
        moveCount = 0;

        e = yield csp.take(self.userEvents); if (e === csp.CLOSED) return;

        if (window.getSelection) {
          if (window.getSelection().empty) {  // Chrome
            window.getSelection().empty();
          } else if (window.getSelection().removeAllRanges) {  // Firefox
            window.getSelection().removeAllRanges();
          }
        } else if (document.selection) {  // IE?
          document.selection.empty();
        }

        var change = {};
        change[dragSlideIndex] = {
          held: { $set: true },
          x: { $set: e.x - dragOriginX + baseX }
        };
        lineup = React.addons.update(self.state[self.state.selectedLineup], change);

        var newState = { dragMode: true };
        newState[self.state.selectedLineup] = lineup;
        self.setState(newState);

        var leftX = self.$lineup.offset().left;
        var rightX = leftX + self.$lineup.width();

        while (e !== csp.CLOSED && (e.type === 'mouseMove' || e.type === 'dragSlideUpdate')){
          moveCount++;

          if (e.x < leftX + 33){
            csp.go(function* (){
              yield csp.put(self.autoScrollRaw, 'left');
            });
          } else if (e.x > rightX){
            if (!_.last(lineup).held){
              csp.go(function* (){
                yield csp.put(self.autoScrollRaw, 'right');
              });
            }
          } else {
            csp.go(function* (){
              yield csp.put(self.autoScrollRaw, 'end');
            });
          }

          lineup = _.sortBy(self.state[self.state.selectedLineup], (slide, i) => {
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

          newState = { dragMode: true };
          newState[self.state.selectedLineup] = lineup;
          self.setState(newState);

          e = yield csp.take(self.userEvents); if (e === csp.CLOSED) return;
        }

        csp.go(function* (){
          yield csp.put(self.autoScrollRaw, 'end');
        });

        lineup = _.cloneDeep(self.state[self.state.selectedLineup]);
        lineup.forEach((slide, i) => {
          if ((slide.selected && moveCount > 2) || (moveCount <= 2 && slide.held && !slide.selected)){
            slide.selected = true;
          } else {
            slide.selected = false;
          }

          if (slide.held){
            slide.lastHeld = true;
          } else {
            slide.lastHeld = false;
          }

          slide.held = false;
          slide.dragging = false;
          slide.x = self.calcSlideBaseX(self.state[self.state.selectedLineup], i);
        });

        newState = { dragMode: true };
        newState[self.state.selectedLineup] = lineup;
        self.setState(newState);
        setTimeout(() => {
          self.setState({ dragMode: false });
        }, TRANSITION_DURATION);
      }
    });

    // AUTO-SCROLL WHEN MOVING DRAGGED SLIDE TO EDGES
    csp.go(function* (){
      var moveUnit = self.props.slideWidth / 33;
      var event = yield self.autoScrollEvents; if (event === csp.CLOSED) return;

      while (true){
        event = yield self.autoScrollEvents; if (event === csp.CLOSED) return;
        if (event !== 'end'){
          self.setState({ edgeDragDirection: event });
          var startScrollWidth = self.lineupNode.scrollWidth;

          var timer = csp.timeout(444);
          var result = yield csp.alts([timer, self.autoScrollEvents]);

          while (result.channel === timer){
            var prevX = self.$lineup.scrollLeft();

            if (event === 'left'){
              self.$lineup.scrollLeft(self.$lineup.scrollLeft() - moveUnit);
            } else if (event === 'right' && self.$lineup.scrollLeft() < startScrollWidth){
              self.$lineup.scrollLeft(self.$lineup.scrollLeft() + moveUnit);
            }

            dragOriginX += prevX - self.$lineup.scrollLeft();

            timer = csp.timeout(17);
            result = yield csp.alts([timer, self.autoScrollEvents]); if (result === csp.CLOSED) return;
          }
        }

        self.setState({ edgeDragDirection: null });
      }
    });


    // LEFT/RIGHT SCROLL BUTTON NAV LOOP
    this.updateNav = _.debounce(() => {
      if (!this.refs.add || !this.refs.lineup){
        return;
      }

      var scrollX = this.$lineup.scrollLeft();
      var scrollWidth = this.lineupNode.scrollWidth - this.$lineup.width() - $(this.refs.add.getDOMNode()).width();
      if (scrollX !== 0){
        this.setState({ showLeftNav: true });
      }
      if (scrollX === 0){
        this.setState({ showLeftNav: false });
      }
      if (scrollX < scrollWidth){
        this.setState({ showRightNav: true });
      }
      if (scrollX >= scrollWidth){
        this.setState({ showRightNav: false });
      }
      if (this.refs.lineup && this.refs.lineup.getDOMNode().scrollWidth <= this.$lineup.width()){
        this.setState({ showLeftNav: false, showRightNav: false });
      }
    }, 100);
    this.updateNav();
    this.$lineup.scroll(this.updateNav);
    this.$lineup.resize(this.updateNav);

    csp.go(function* (){
      var event;

      while (true){
        do {
          event = yield csp.take(self.navEvents); if (event === csp.CLOSED) return;
        } while (event !== csp.CLOSED && event.type !== 'hoverStart');

        if (self.lineupNode.scrollWidth > self.$lineup.width()){
          self.setState({ showNav: true });
        }

        while (event !== csp.CLOSED && event.type !== 'hoverEnd'){
          event = yield csp.take(self.navEvents); if (event === csp.CLOSED) return;

          self.setState({ dragMode: true });

          if (event !== csp.CLOSED && event.type === 'arrowClick'){
            var moveUnit = 30;
            var prevX = self.$lineup.scrollLeft();

            switch(event.direction){
              case 'left':
                var goal = Math.max(0, self.$lineup.scrollLeft() - self.props.slideWidth);
                var move = () => {
                  var dist = self.$lineup.scrollLeft() - goal;
                  if (dist > 0){
                    var distToMove = Math.min(moveUnit, dist);
                    self.$lineup.scrollLeft(self.$lineup.scrollLeft() - distToMove);
                    if (Math.abs(prevX - self.$lineup.scrollLeft()) > 1){
                      prevX = self.$lineup.scrollLeft();
                      requestAnimationFrame(move);
                    } else {
                      self.setState({ dragMode: false });
                    }
                  }
                };
              break;

              case 'right':
                var goal = self.$lineup.scrollLeft() + self.props.slideWidth;
                var move = () => {
                  var dist = goal - self.$lineup.scrollLeft();
                  if (dist > 0){
                    var distToMove = Math.min(moveUnit, dist);
                    self.$lineup.scrollLeft(self.$lineup.scrollLeft() + distToMove);
                    if (Math.abs(prevX - self.$lineup.scrollLeft()) > 1){
                      prevX = self.$lineup.scrollLeft();
                      requestAnimationFrame(move);
                    } else {
                      self.setState({ dragMode: false });
                    }
                  }
                };
              break;
            }
            move();
          }
        }

        self.setState({ showNav: false, dragMode: false });
      }
    });
  },

  componentWillUnmount(){
    this.globalCspChans.forEach(chanName => {
      this[chanName].close();
    });
  }
});
