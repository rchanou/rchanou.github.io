var React = require('react/addons');
var Anim = require('../../components/react-transition');
var _ = require('lodash');


var PRIMARY_MOUSE_BUTTON_VALUE = 0;
var MAX_Z_INDEX = 16777271;


var Popup = require('../../components/popup');

function* idMaker(){
  var index = 0;
  while(true)
    yield index++;
}

var gen = idMaker();

console.log(gen.next().value); // 0
console.log(gen.next().value); // 1
console.log(gen.next().value); // 2


var MenuItem = React.createClass({
  awaitingIconUpdate: false,
  startingDrag: false,
  endingDrag: false,

  getDefaultProps(){
    return {
      top: 0,
      left: 0,
      height: 60,
      width: '100%',
      itemKey: null,
      label: null,
      url: null,
      iconUrl: null,
      dragging: false,
      selected: false,
      inBucket: false,
      bucketId: null,
      animationDuration: 200,
      onDragStart(){},
      onChange(){},
      onRemoveClick(){},
      onAddClick(){},
      onError(){},
      onIconUpload(){}
    };
  },

  getInitialState(){
    return {
      startingSelect: false,
      hovering: false
    };
  },

  render(){
    var itemForm = null;

    if (this.props.bucketId !== null){
      var fieldStyle = { width: '6em' };

      itemForm = <div>
        <dl className='inline-dl'>
          {this.props.url && <dd style={{ whiteSpace: 'nowrap' }}>URL: {this.props.url}</dd>}
          {this.props.id && <dd>ID: {this.props.id}</dd>}
        </dl>
      </div>;
    } else if (this.props.selected && !this.state.startingSelect){
      itemForm = <form
        className='form' style={{ cursor: 'default' }}
        onMouseDown={e => { e.stopPropagation(); }}
        onTouchStart={e => { e.stopPropagation(); }}
      >
        <label
          style={{ width: '100%', marginTop: 5 }}
        >
          <label className='control-label pull-left' style={{ width: '15%' }}>Label</label>
          <input
            className='form-control pull-right'
            style={{ width: '85%' }}
            defaultValue={this.props.label}
            onChange={this.handleChange}
            onMouseDown={e => { e.stopPropagation(); }}
            onTouchStart={e => { e.stopPropagation(); }}
            ref='label'
          />
        </label>
        <label
          style={{ width: '100%' }}
        >
          <label className='control-label pull-left' style={{ width: '15%' }}>
            <span style={{ fontWeight: this.hasPossibleUrl()? 'bold': null }}>URL</span>
            /
            <span style={{ fontWeight: this.hasPossibleUrl() === false? 'bold': null }}>ID</span>
          </label>
          <input
            className='form-control pull-right'
            style={{ width: '85%' }}
            defaultValue={this.props.url || this.props.id}
            onChange={this.handleChange}
            onMouseDown={e => { e.stopPropagation(); }}
            onTouchStart={e => { e.stopPropagation(); }}
            ref='url'
          />
        </label>
        <span
          style={{ width: '100%' }}
          onMouseDown={e => { e.stopPropagation(); }}
          onTouchStart={e => { e.stopPropagation(); }}
        >
          <input type='file' ref='uploader'
            style={{ visibility: 'hidden' }}
            onChange={e => {
              console.log('changed', e.target.files);
              if (e.target.files.length == 0){
                return;
              }

              var file = e.target.files[0];

              var data = new FormData();
              var uploadTime = new Date().valueOf();
              console.log(uploadTime + '.jpg');
              data.append('filename', uploadTime + '.jpg');
              data.append('image', file);

              var url = '/admin/mobileApp/images/update';
              if (window.location.hostname.indexOf('192.168.111') !== -1){
                url = '/admin/www/mobileApp/images/update';
              }

              $.ajax({
                type: 'POST',
                url, //: '/admin/www/mobileApp/images/update',
                cache: false,
                contentType: false,
                processData: false,
                data
              })
              .then(res => {
                console.log('upload response', res);
                this.props.onIconUpload();

                var iconUrl = '';
                if (window.location.hostname.indexOf('192.168.111.') !== -1){
                  iconUrl = 'https://vm-122.clubspeedtiming.com';
                }
                iconUrl += '/assets/mobileApp/icons/' + uploadTime + '.jpg';

                this.awaitingIconUpdate = true;

                this.props.onChange({
                  itemKey: this.props.itemKey,
                  iconUrl
                });
              }, res => {
                console.log('upload error', res);
                this.props.onError({ message: 'An error occurred while trying to upload the image:\r\n\r\n' + JSON.stringify(res) });
              });
            }}
          />
          <input className='btn btn-info pull-left' style={{ width: 110 }}
            onClick={() => { $(this.refs.uploader.getDOMNode()).click(); }}
            defaultValue='Change Icon'
          />
          <input type='button' defaultValue='Remove'
            className='btn btn-danger pull-right'
            onMouseDown={e => { e.stopPropagation(); }}
            onTouchStart={e => { e.stopPropagation(); }}
            onClick={e => {
              e.preventDefault();
              this.props.onRemoveClick({ itemKey: this.props.itemKey });
            }}
          />
        </span>
      </form>;
    };

    var backgroundColor = 'rgb(67,67,67)';
    if (this.props.dragging && !this.startingDrag){
      backgroundColor = 'rgb(37,37,37)';
    } else if (this.state.hovering){
      backgroundColor = 'rgb(44,44,44)';
    } else if (this.props.bucketId !== null){
      backgroundColor = 'rgb(77,77,77)';
    } else if (this.props.selected){
      backgroundColor = 'rgb(51,51,51)';
    }

    var cursor = 'pointer';
    if (this.props.inBucket || (this.props.dragging && !this.startingDrag)){
      cursor = 'move';
    }

    return <Anim component='li' ref='item'
      className={'alert btn-info'}
      duration={this.props.dragging? 0: this.props.animationDuration}
      style={{
        backgroundColor,
        borderColor: this.props.bucketId !== null? 'rgb(33,33,33)': 'hsl(0,50%,50%)',
        cursor,
        position: 'absolute',
        top: this.props.top,
        left: this.props.left,
        height: this.props.height,
        width: this.props.width,
        zIndex: this.props.dragging || this.endingDrag? MAX_Z_INDEX: null
      }}

      onMouseEnter={() => {
        this.setState({ hovering: true });
      }}

      onMouseLeave={() => {
        this.setState({ hovering: false });
      }}

      onMouseOut={() => {
        this.setState({ hovering: false });
      }}

      onMouseDown={e => {
        if (e.button === PRIMARY_MOUSE_BUTTON_VALUE){
          e.preventDefault();
          this.handleClickOrTouch(e);
        }
      }}

      onTouchStart={e => {
        e.preventDefault();
        this.handleClickOrTouch(e.touches[0]);
      }}
    >
      <label className='pull-right' style={{ fontSize: '1.2em', cursor }}>
        {this.props.label}
      </label>
      <img ref='icon'
        src={this.props.icon || this.expandUrl(this.props.iconUrl)}
        style={{ maxHeight: 40 }}
      />
      <span>{this.state.hovering}</span>
      {itemForm}
    </Anim>;
  },

  componentDidUpdate(){
    if (this.awaitingIconUpdate){
      this.awaitingIconUpdate = false;
      if (this.isMounted() && this.refs.icon){
        // hack to force browser to re-download image since icon changed even though filename/location didn't
        $(this.refs.icon.getDOMNode()).attr('src', this.props.iconUrl + '?' + new Date().valueOf());
      }
    }
  },

  componentWillReceiveProps(nextProps){
    if (this.props.dragging && !nextProps.dragging){
      this.endingDrag = true;
      setTimeout(() => {
        if (this.isMounted()){
          this.endingDrag = false;
        }
      }, this.props.animationDuration);
    } else if (!this.props.dragging && nextProps.dragging){
      this.startingDrag = true;
      setTimeout(() => {
        if (this.isMounted()){
          this.startingDrag = false;
        }
      }, 150);
    }

    if (!this.props.selected && nextProps.selected){
      this.setState({ startingSelect: true });
      setTimeout(() => {
        this.setState({ startingSelect: false });
      }, this.props.animationDuration);
    }

    if (this.props.top !== nextProps.top){
      this.setState({ hovering: false });
    }
  },

  handleClickOrTouch(e){
    this.props.onDragStart({
      dragItemKey: this.props.itemKey,
      dragItemBucketId: this.props.bucketId,
      mouseX: e.pageX,
      mouseY: e.pageY,
      dragCursorOffsetX: e.pageX - this.props.left,
      dragCursorOffsetY: e.pageY - this.props.top
    });
  },

  handleChange(e){
    var changeEvent = {
      itemKey: this.props.itemKey,
      label: this.refs.label.getDOMNode().value,
      icon: this.props.icon
    };

    if (this.hasPossibleUrl()){
      changeEvent.url = this.refs.url.getDOMNode().value;
    } else {
      changeEvent.id = this.refs.url.getDOMNode().value;
    }

    this.props.onChange(changeEvent);
  },

  hasPossibleUrl(value){
    if (typeof value === 'undefined'){
      if (this.refs.url){
        value = this.refs.url.getDOMNode().value;

        if (value === ''){
          return null;
        }
      } else {
        return null;
      }
    }

    return value.indexOf('/') !== -1;
  },

  expandUrl(url){
    if (!url){
      return null;
    }

    var fullIconUrl;
    if (url[0] === '/'){
      if (window.location.hostname === '192.168.111.165'){
        fullIconUrl = 'https://vm-122.clubspeedtiming.com' + url;
      } else {
        fullIconUrl = (window.location.origin) + url;
      }
    } else {
      fullIconUrl = url;
    }
    if (window.location.origin.indexOf('https') !== -1 && fullIconUrl.indexOf('https') === -1){
      fullIconUrl = fullIconUrl.replace('http', 'https');
    }
    return fullIconUrl;
  }
});


var MenuItemEditor = React.createClass({
  getDefaultProps(){
    return {
      itemWidth: 500,
      itemHeight: 60,
      selectedItemHeight: 210,
      minDragMoves: 3,
      itemBucketX: 600,
      animationDuration: 200,
      spacerWidth: 50,
      bucketItemHeight: 90,
      menuItemBucket: [
        {
          "label":"Club Speed",
          "url":"http://www.clubspeed.com",
          "iconUrl":"/assets/MobileApp/icons/tachometer.png"
        },
        {
          "label":"Member Card",
          "id":"profile",
          "iconUrl":"/assets/mobile/icons/credit-card.png"
        },
        {
          "label":"Track Information",
          "id":"trackInformation",
          "iconUrl":"/assets/mobile/icons/map-marker.png"
        },
        {
          "label":"My Results",
          "url":"/mobile/#/racersearch/{{customerId}}/",
          "iconUrl":"/assets/MobileApp/icons/chart.png"
        },
        {
          "label":"Top Times",
          "url":"/mobile/#/livetiming/fastestTimeByWeek",
          "iconUrl":"http://vm-122.clubspeedtiming.com/assets/MobileApp/icons/trophy.png"
        },
        {
          "label":"Online Booking",
          "url":"/booking/?login={{authToken}}",
          "iconUrl":"/assets/mobile/icons/MISSING-IMAGE-DOES-NOT-EXIST.png"
        }
      ]
    };
  },

  getInitialState(){
    return {
      menuItems: [],
      selectedItemKey: null,
      dragItemKey: null,
      mouseX: null,
      mouseY: null,
      dragMoves: 0,
      dragCursorOffsetX: null,
      dragCursorOffsetY: null,
      settingsitemKey: null,
      popup: { message: null, alertClass: null }
    };
  },

  render(){
    if (this.state.settingsId === null){
      return <div>Loading items...</div>;
    }

    var popup = null;
    if (this.state.popup.message !== null){
      popup = <Popup
        {...this.state.popup}
        key={this.state.popup.message}
        onDone={this.handlePopupDone}
        onFadeComplete={this.handlePopupDone}
        onClick={this.handlePopupDone}
      />
    }

    return <div
      className='container-fluid'
      onTouchEnd={this.handleDragEnd}
    >
      {popup}
      <div className='row'>
        <div className='col-xs-12'>
          <div className='widget-box'>
            <div className='widget-content'>
              <div className='row hidden-lg'>
                {this.renderNote()}
              </div>
              <div className='row'
                onTouchMove={e => {
                  this.handleMove(e.touches[0]);
                }}
              >
                <div className='col-xs-12 col-sm-6 col-md-6 col-lg-5'>
                  <h1>
                    Menu
                  </h1>
                  <Anim component='div'
                    duration={this.props.animationDuration}
                    style={{
                      position: 'relative',
                      top: '1.1em',
                      height: this.calcPreviewHeight() + this.props.selectedItemHeight
                    }}
                  >
                    {this.renderDragItemPlaceholder()}
                    <ol ref='menu' style={{ height: this.calcPreviewHeight(), listStyle: 'none' }}>
                      {this.renderItems()}
                    </ol>
                    {this.renderButtonPanel()}
                  </Anim>
                </div>

                <div className='hidden-xs' style={{ width: '100em' }} />

                {this.renderItemBucket()}

                <div className='hidden-xs hidden-sm hidden-md col-lg-3'>
                  <h1>&nbsp;</h1>
                  {this.renderNote()}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>;
  },

  renderNote(){
    return <ul className='alert alert-info' style={{ marginTop: 25, paddingLeft: 25 }}>
      <li>Click a menu item to edit its label, URL/ID, or icon.<br/><br/></li>
      <li>Drag items up and down to change their order.<br/><br/></li>
      <li>You may drag preset items to the left to add them to the menu.</li>
    </ul>;
  },

  renderItems(){
    if (this.state.menuItems.length === 0){
      return <div>Drag items here from the list of preset items.</div>
    }

    return this.state.menuItems.map((item, i) => {
      var positionProps;
      if (this.state.dragItemKey !== null && item.itemKey === this.state.dragItemKey){
        positionProps = {
          top: Math.min(
            this.state.mouseY - this.state.dragCursorOffsetY,
            this.calcPreviewHeight() + 1
          ),
          left: this.state.dragItemBucketId !== null? this.state.mouseX - this.state.dragCursorOffsetX: 0,
          dragging: true
        };

        if (this.state.selectedItemKey !== null && this.state.selectedItemKey === item.itemKey){
          positionProps.height = this.props.selectedItemHeight;
        } else {
          positionProps.height = this.props.itemHeight;
        }
      } else {
        positionProps = {
          left: 0,
          width: '100%'
        };

        if (this.state.selectedItemKey !== null){
          var selectedItemIndex = _.findIndex(this.state.menuItems, { itemKey: this.state.selectedItemKey });
          if (selectedItemIndex < i){
            positionProps.top = (i - 1) * this.props.itemHeight + this.props.selectedItemHeight;
            positionProps.height = this.props.itemHeight;
          } else {
            positionProps.top = i * this.props.itemHeight;
            if (this.state.selectedItemKey === item.itemKey){
              positionProps.height = this.props.selectedItemHeight;
            } else {
              positionProps.height = this.props.itemHeight;
            }
          }
        } else {
          positionProps.top = i * this.props.itemHeight;
          positionProps.height = this.props.itemHeight;
        }
      }

      return <MenuItem
        key={(item.bucketId || '') + item.itemKey}
        animationDuration={this.props.animationDuration}
        selected={this.state.selectedItemKey !== null && this.state.selectedItemKey === item.itemKey}
        {...positionProps}
        {...item}

        onDragStart={this.handleDragStart}

        onChange={e => {
          var change = {};
          var changedItemIndex = _.findIndex(this.state.menuItems, { itemKey: e.itemKey });
          change[changedItemIndex] = { $merge: e };
          var menuItems = React.addons.update(
            this.state.menuItems,
            change
          );
          this.setState({ menuItems });
        }}

        onRemoveClick={e => {
          this.removeItem(e.itemKey);
        }}

        onIconUpload={e => {
          this.setState({
            popup: {
              message: 'Icon uploaded.'
            }
          });
          /*var change = {};
          var changedItemIndex = _.findIndex(this.state.menuItems, { itemKey: e.itemKey });
          change[changedItemIndex] = { icon: { $set: e.icon } };
          var menuItems = React.addons.update(
            this.state.menuItems,
            change
          );
          this.setState({ menuItems });*/
        }}
      />;
    });
  },

  renderItemBucket(){
    var bucketItemElements = this.getRemainingBucketItems() //this.props.menuItemBucket
    .map((item, i) => {
      var positionProps;
      if (this.state.dragItemBucketId !== null && item.bucketId === this.state.dragItemBucketId){
        positionProps = {
          top: this.state.mouseY - this.state.dragCursorOffsetY,
          left: this.state.mouseX - this.state.dragCursorOffsetX,
          dragging: true
        };
      } else {
        positionProps = {
          top: i * this.props.bucketItemHeight
        };
      }

      return <MenuItem
        inBucket={true}
        bucketId={item.bucketId}
        key={item.itemKey}
        height={this.props.bucketItemHeight}
        {...item}
        {...positionProps}
        onDragStart={this.handleDragStart}
      />;
    });

    return <div
      className='col-xs-12 col-sm-6 col-md-5 col-lg-4'
      style={{ opacity: 0.85 }}
    >
      <h1>
        {this.getRemainingBucketItems().length > 0 && 'Preset Items'}
      </h1>
      <Anim component='ol' ref='bucket'
        duration={this.props.animationDuration}
        style={{
          position: 'relative',
          top: '1.1em',
          listStyle: 'none',
          height: (this.getRemainingBucketItems().length + 1) * this.props.bucketItemHeight
        }}
      >
        {bucketItemElements}
      </Anim>
    </div>;
  },

  renderButtonPanel(){
    return <Anim component='div'
      duration={this.props.animationDuration}
      style={{
        position: 'absolute',
        top: this.calcPreviewHeight() + this.props.spacerWidth,
        left: 0,
        width: '100%'
      }}
    >
      <button
        className='btn btn-info'
        onClick={() => {
          var propsToSave = ['label', 'id', 'url', 'iconUrl', 'itemKey'];
          var url = config.apiURL + 'settings/' + this.state.settingsId + '?key=' + config.privateKey;
          var value = JSON.stringify({
            menuItems: _.map(this.state.menuItems, item => _.pick(item, propsToSave))
          });
          var request = { type: 'PUT', url, data: { value } };

          $.ajax(request)
          .then(
            () => {
              var message = 'Menu items successfully saved!';
              this.setState({ popup: { message, alertClass: 'alert-success' } });
            },
            err => {
              console.log('ERROR:', err);
              var message = 'An error occurred while trying to save changes.';
              this.setState({ popup: { message, alertClass: 'alert-danger' } });
            }
          );
        }}
      >
        Save All
      </button>
      {/*<button className='btn btn-info pull-right'
        onClick={() => {
          var menuItems = React.addons.update(
            this.state.menuItems,
            { $push: [
              {
                itemKey: Math.max.apply(null, this.state.menuItems.concat(this.props.menuItemBucket)),
                label: '(new item)',
                url: '',
                iconUrl: ''
              }
            ] }
          );

          this.setState({ menuItems });
        }}
      >
        Add Item
      </button>*/}
    </Anim>;
  },

  renderDragItemPlaceholder(){
    if (this.state.dragItemKey === null || this.state.dragItemBucketId !== null){
      return null;
    }

    var dragItemIndex = _.findIndex(this.state.menuItems, { itemKey: this.state.dragItemKey });
    var selectedItemIndex = _.findIndex(this.state.menuItems, { itemKey: this.state.selectedItemKey });
    var top;
    if (selectedItemIndex === -1 || selectedItemIndex >= dragItemIndex){
      top = dragItemIndex * this.props.itemHeight;
    } else {
      top = (dragItemIndex - 1) * this.props.itemHeight + this.props.selectedItemHeight;
    }

    return <div
      style={{
        position: 'absolute',
        backgroundColor: 'hsl(180,50%,50%)',
        top,
        left: 0,
        width: '100%',
        height: this.state.dragItemKey === this.state.selectedItemKey? this.props.selectedItemHeight: this.props.itemHeight
      }}
      className='flashing'
    >

    </div>;
  },

  componentDidMount(){
    $.get(config.apiURL + 'settings.json?namespace=mobileApp&name=menuItems&key=' + config.privateKey)
    .then(
      res => {
        try {
          if (!res.settings || !res.settings.length){
            return;
          }

          var settingsId = res.settings[0].settingsId;
          var menuItems = JSON.parse(res.settings[0].value).menuItems;
          menuItems.forEach((item, i) => {
            item.itemKey = this.props.menuItemBucket.length + 1 + i;
          });
          this.setState({ settingsId, menuItems });
        } catch(ex){
          console.log('EXCEPTION:', ex);
          var message = 'An error occurred while trying to load the menu item editor.';
          this.setState({ popup: { message, alertClass: 'alert-danger' } });
        }
      }, err => {
        console.log('ERROR:', err);
        var message = 'An error occurred while trying to load the menu items.';
        this.setState({ popup: { message, alertClass: 'alert-danger' } });
      }
    );

    $(window).mouseup(this.handleDragEnd);

    $(window).mousemove(this.handleMove);
  },

  handleDragStart(e){
    e.dragMoves = 0;
    this.setState(e);
    setTimeout(() => {
      this.setState({ dragMoves: this.props.minDragMoves });
    }, this.props.animationDuration);
  },

  handleMove(e){
    var newState = {
      dragMoves: this.state.dragMoves + 1,
      mouseX: e.pageX,
      mouseY: e.pageY
    };

    if (this.state.dragItemKey !== null){
      newState.menuItems = _(this.state.menuItems)
      .sortBy((item, i) => {
        if (item.itemKey === this.state.dragItemKey){
          return e.pageY - this.state.dragCursorOffsetY;
        } else {
          if (this.state.selectedItemKey !== null){
            var selectedItemIndex = _.findIndex(this.state.menuItems, { itemKey: this.state.selectedItemKey });
            if (selectedItemIndex < i){
              return (i - 1) * this.props.itemHeight + this.props.selectedItemHeight;
            } else {
              return i * this.props.itemHeight;
              if (this.state.selectedItemKey === item.itemKey){
                return this.props.selectedItemHeight;
              } else {
                return this.props.itemHeight;
              }
            }
          } else {
            return i * this.props.itemHeight;
          }
        }
      })
      .value();

      if (this.state.dragItemBucketId !== null){
        if (typeof this.refs.menu !== 'undefined'){
          var menu = $(this.refs.menu.getDOMNode());
          var menuOffset = menu.offset();
          var menuZone = {
            left: menuOffset.left,
            right: menuOffset.left + menu.width(),
            top: menuOffset.top,
            bottom: menuOffset.top + menu.height() + this.props.itemHeight
          }

          if (menuZone.left < e.pageX && e.pageX < menuZone.right && menuZone.top < e.pageY && e.pageY < menuZone.bottom){
            var itemToAdd = _.omit(this.props.menuItemBucket[this.state.dragItemBucketId], 'bucketId', 'itemKey');
            newState.menuItems.push(itemToAdd);

            var newItemKey = Math.max.apply(null,
              _.pluck(this.state.menuItems.concat(this.props.menuItemBucket), 'itemKey')
            ) + 1;
            newState.menuItems[newState.menuItems.length - 1].itemKey = newItemKey;
            newState.dragItemKey = newItemKey;
            newState.dragItemBucketId = null;
            newState.selectedItemKey = itemToAdd.itemKey;

            var bucketTop = $(this.refs.bucket.getDOMNode()).offset().top;
            newState.dragCursorOffsetY = this.state.dragCursorOffsetY + menuZone.top - bucketTop;
          }
        }
      }
    }

    this.setState(newState);
  },

  handlePopupDone(e){
    this.setState({ popup: { message: null, alertClass: null } });
  },

  handleDragEnd(){
    var newState = {
      dragMoves: 0,
      dragItemKey: null,
      dragItemBucketId: null,
    };

    if (this.state.dragMoves < this.props.minDragMoves){
      if (this.state.selectedItemKey === this.state.dragItemKey){
        newState.selectedItemKey = null;
      } else if (this.state.dragItemKey !== null && _.any(this.state.menuItems, { itemKey: this.state.dragItemKey })){
        newState.selectedItemKey = this.state.dragItemKey;
      }
    }

    this.setState(newState);
  },

  calcPreviewHeight(){
    var height = 0;

    if (this.state.selectedItemKey !== null){
      height = (this.state.menuItems.length - 1) * this.props.itemHeight + this.props.selectedItemHeight;
    } else {
      height = this.state.menuItems.length * this.props.itemHeight;
    }

    return height;
  },

  getRemainingBucketItems(){
    return _(this.props.menuItemBucket)
      .forEach((item, i) => { item.itemKey = i; item.bucketId = i; })
      .filter(
        item => !_.any(this.state.menuItems, { label: item.label })
      )
      .sortBy('label')
      .value();
  },

  removeItem(itemKey){
    var newState = {
      menuItems: _.reject(this.state.menuItems, { itemKey })
    };
    if (this.state.selectedItemKey === itemKey){
      newState.selectedItemKey = null;
    }
    this.setState(newState);
  }
});


module.exports = MenuItemEditor;
