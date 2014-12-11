var React = require('react/addons');
var Anim = require('../../components/react-transition');
var _ = require('lodash');

var Popup = require('../../components/popup');

var PRIMARY_MOUSE_BUTTON_VALUE = 0;


var MenuItem = React.createClass({
  getDefaultProps(){
    return {
      top: 0,
      left: 0,
      height: 60,
      width: '100%',
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
      onAddClick(){}
    };
  },

  getInitialState(){
    return {
      startingDrag: false,
      endingDrag: false,
      startingSelect: false
    };
  },

  render(){
    var itemForm = null;
    if (this.props.bucketId !== null){
      itemForm = <div>
        <dl className='inline-dl'>
          {this.props.url && <dt style={{ width: '6em' }}>URL:</dt>}
          {this.props.url && <dd>{this.props.url}</dd>}
          {this.props.iconUrl && <dt style={{ width: '6em' }}>Icon URL:</dt>}
          {this.props.iconUrl && <dd>{this.props.iconUrl}</dd>}
        </dl>
      </div>;
    } else if (this.props.selected && !this.state.startingSelect){
      itemForm = <form className='form'>
        <label
          className='control-label'
          style={{ width: '100%' }}
          onMouseDown={e => { e.stopPropagation(); }}
          onTouchStart={e => { e.stopPropagation(); }}
        >
          URL:
          <input
            className='form-control'
            defaultValue={this.props.url}
            onChange={this.handleChange}
            onMouseDown={e => { e.stopPropagation(); }}
            onTouchStart={e => { e.stopPropagation(); }}
            ref='url'
          />
        </label>
        <br/><br/>
        <label
          className='control-label'
          style={{ width: '100%' }}
          onMouseDown={e => { e.stopPropagation(); }}
          onTouchStart={e => { e.stopPropagation(); }}
        >
          Icon URL:
          <input
            className='form-control'
            defaultValue={this.props.iconUrl}
            onChange={this.handleChange}
            onMouseDown={e => { e.stopPropagation(); }}
            onTouchStart={e => { e.stopPropagation(); }}
            ref='iconUrl'
          />
        </label>
      </form>;
    }

    var alertClass = 'btn-info';
    var backgroundColor = 'hsl(204,50%,30%)';
    var borderColor = null;
    if (this.props.bucketId !== null){
      alertClass = 'btn-info';
      backgroundColor = 'hsl(300,50%,30%)';
      borderColor = 'hsl(300,50%,15%)';
    } else if (this.props.dragging && !this.state.startingDrag){
      alertClass = 'btn-success';
      backgroundColor = 'hsl(50,70%,50%)';
    } else if (this.props.selected){
      alertClass = 'btn-success';
      backgroundColor = 'hsl(150,50%,30%)';
    }

    var cursor = 'pointer';
    if (this.props.inBucket || (this.props.dragging && !this.state.startingDrag)){
      cursor = 'move';
    }

    return <Anim component='div' ref='item'
      className={'alert ' + alertClass}
      duration={this.props.dragging? 0: this.props.animationDuration}
      style={{
        backgroundColor,
        borderColor,
        cursor,
        position: 'absolute',
        top: this.props.top,
        left: this.props.left,
        height: this.props.height,
        width: this.props.width,
        zIndex: this.props.dragging || this.state.endingDrag? 9999: null
      }}

      onMouseDown={e => {
        if (/*!this.props.inBucket &&*/ e.button === PRIMARY_MOUSE_BUTTON_VALUE){
          e.preventDefault();
          this.handleClickOrTouch(e);
        }
      }}

      onTouchStart={e => {
        console.log('le start');
        if (true || !this.props.inBucket){
          e.preventDefault();
          this.handleClickOrTouch(e.touches[0]);
        }
      }}

      /*onClick={() => {
        if (this.props.bucketId !== null){
          this.props.onAddClick({ label: this.props.label });
        }
      }}*/
    >
      <label style={{ fontSize: '1.2em', cursor }}>
        {this.props.label}
      </label>
      <img
        className='pull-right'
        src={this.expandUrl(this.props.iconUrl)}
        style={{ maxHeight: 40 }}
      />
      {itemForm}
    </Anim>;
  },

  componentWillReceiveProps(nextProps){
    if (this.props.dragging && !nextProps.dragging){
      this.setState({ endingDrag: true });
      setTimeout(() => {
        if (this.isMounted()){
          this.setState({ endingDrag: false });
        }
      }, this.props.animationDuration);
    } else if (!this.props.dragging && nextProps.dragging){
      this.setState({ startingDrag: true });
      setTimeout(() => {
        if (this.isMounted()){
          this.setState({ startingDrag: false });
        }
      }, this.props.animationDuration);
    }

    if (!this.props.selected && nextProps.selected){
      this.setState({ startingSelect: true });
      setTimeout(() => {
        this.setState({ startingSelect: false });
      }, this.props.animationDuration);
    }
  },

  handleClickOrTouch(e){
    var offsetLeft = $(this.getDOMNode()).offset().left;
    var offsetTop = $(this.getDOMNode()).offset().top;
    //console.log(this.props.left, offsetLeft, this.props.top, offsetTop, $(this.getDOMNode()).parent());
    var container = $(this.getDOMNode()).parent();
    this.props.onDragStart({
      dragItemLabel: this.props.label,
      dragItemBucketId: this.props.bucketId,
      mouseX: e.pageX,
      mouseY: e.pageY,
      dragCursorOffsetX: e.pageX - this.props.left,
      dragCursorOffsetY: e.pageY - this.props.top
    });
  },

  handleChange(e){
    this.props.onChange({
      label: this.props.label,
      url: this.refs.url.getDOMNode().value,
      iconUrl: this.refs.iconUrl.getDOMNode().value
    });
  },

  expandUrl(url){
    if (!url){
      return null;
    }

    var fullIconUrl;
    if (url[0] === '/'){
      if (window.location.hostname === '192.168.111.165'){
        fullIconUrl = 'https://192.168.111.122' + url;
      } else {
        fullIconUrl = window.location.hostname + url;
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
      selectedItemHeight: 200,
      minDragMoves: 3,
      itemBucketX: 600,
      animationDuration: 200,
      spacerWidth: 50,
      bucketItemHeight: 150,
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
      menuItems: [] || this.props.menuItemBucket || [],
      selectedItemLabel: null,
      dragItemLabel: null,
      //dragItemX: null,
      //dragItemY: null,
      mouseX: null,
      mouseY: null,
      dragMoves: 0,
      dragCursorOffsetX: null,
      dragCursorOffsetY: null,
      settingsId: null,
      //offsetTop: 0,
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
              <div className='row'
                onTouchMove={e => {
                  this.handleMove(e.touches[0]);
                }}
                onTouchCancel={e => {
                  consol.log('touch cancel!', e);
                }}
              >
                <div className='col-xs-12 col-md-6'>
                  <h1>
                    Menu
                  </h1>
                  <Anim component='div' ref='menu'
                    duration={this.props.animationDuration}
                    style={{
                      position: 'relative',
                      top: '1.1em',
                      height: this.calcPreviewHeight() + this.props.selectedItemHeight
                    }}
                  >
                    {this.renderDragItemPlaceholder()}
                    {this.renderItems()}
                    {this.renderButtonPanel()}
                  </Anim>
                </div>

                <div className='col-xs-0 col-md-1' />

                {this.renderItemBucket()}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>;
  },

  renderItems(){
    if (this.state.menuItems.length === 0){
      return <div>Drag items here from the item bucket.</div>
    }

    return this.state.menuItems.map((item, i) => {
      var positionProps;
      if (this.state.dragItemLabel !== null && item.label === this.state.dragItemLabel){
        positionProps = {
          top: Math.min(
            this.state.mouseY - this.state.dragCursorOffsetY,
            this.calcPreviewHeight() + 1
          ),
          left: this.state.dragItemBucketId !== null? this.state.mouseX - this.state.dragCursorOffsetX: 0,
          dragging: true
        };
        /*if (this.state.dragItemBucketId !== null){
          positionProps.height = this.props.bucketItemHeight;
        } else*/
        if (this.state.selectedItemLabel !== null && this.state.selectedItemLabel === item.label){
          positionProps.height = this.props.selectedItemHeight;
        } else {
          positionProps.height = this.props.itemHeight;
        }
      } else {
        positionProps = {
          left: 0,
          width: '100%'
        };

        if (this.state.selectedItemLabel !== null){
          var selectedItemIndex = _.findIndex(this.state.menuItems, { label: this.state.selectedItemLabel });
          if (selectedItemIndex < i){
            positionProps.top = (i - 1) * this.props.itemHeight + this.props.selectedItemHeight;
            positionProps.height = this.props.itemHeight;
          } else {
            positionProps.top = i * this.props.itemHeight;
            if (this.state.selectedItemLabel === item.label){
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

      positionProps.selected = (this.state.selectedItemLabel !== null && this.state.selectedItemLabel === item.label);

      return <MenuItem
        key={(item.bucketId || '') + item.label}
        animationDuration={this.props.animationDuration}
        {...positionProps}
        {...item}

        onDragStart={this.handleDragStart}

        onChange={e => {
          var change = {};
          var changedItemIndex = _.findIndex(this.state.menuItems, { label: e.label });
          change[changedItemIndex] = { $set: e };
          var menuItems = React.addons.update(
            this.state.menuItems,
            change
          );
          this.setState({ menuItems });
        }}
      />;
    });
  },

  renderItemBucket(){
    var bucketItemElements = this.getRemainingBucketItems()
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
        key={item.label}
        height={this.props.bucketItemHeight}
        {...item}
        {...positionProps}
        onDragStart={this.handleDragStart}
      />;
    });

    return <div
      className='col-xs-12 col-md-5'
    >
      <h1>
        {this.getRemainingBucketItems().length > 0 && 'Item Bucket'}
      </h1>
      <Anim component='div' ref='bucket'
        duration={this.props.animationDuration}
        style={{
          position: 'relative',
          top: '1.1em',
          height: (this.getRemainingBucketItems().length + 1) * this.props.bucketItemHeight
        }}
      >
        {bucketItemElements}
      </Anim>
    </div>;
  },

  renderButtonPanel(){
    var removeButton = null;
    if (this.state.selectedItemLabel !== null){
      removeButton = <button
        duration={this.props.animationDuration}
        className='btn btn-danger pull-right'
        onClick={() => {
          this.removeItem(this.state.selectedItemLabel);
        }}
      >
        Remove Selected
      </button>
    }

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
          var propsToSave = ['label', 'id', 'url', 'iconUrl'];
          var url = config.apiURL + 'settings/' + this.state.settingsId + '?key=' + config.privateKey;
          var value = JSON.stringify({
            menuItems: _.map(this.state.menuItems, item => _.pick(item, propsToSave))
          });
          var request = { type: 'PUT', url, data: { value } };

          $.ajax(request)
          .then(() => {
            var message = 'Menu items successfully saved!';
            this.setState({ popup: { message, alertClass: 'alert-success' } });
          },
          (...all) => {
            console.log('error', all);
            var message = 'An error occurred while trying to save changes.';
            this.setState({ popup: { message, alertClass: 'alert-danger' } });
          });
        }}
      >
        Save All
      </button>
      {removeButton}
    </Anim>;
  },

  renderDragItemPlaceholder(){
    if (this.state.dragItemLabel === null || this.state.dragItemBucketId !== null){
      return null;
    }

    var dragItemIndex = _.findIndex(this.state.menuItems, { label: this.state.dragItemLabel });
    var selectedItemIndex = _.findIndex(this.state.menuItems, { label: this.state.selectedItemLabel });
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
        //opacity: 0.5,
        top,
        left: 0,
        width: '100%',
        height: this.state.dragItemLabel === this.state.selectedItemLabel? this.props.selectedItemHeight: this.props.itemHeight
      }}
      className='flashing'
    >

    </div>;
  },

  isDragItemInMenumenuZone(){

  },

  componentDidMount(){
    //this.setState({
    //  offsetTop: $('#breadcrumb').offset().top + $('#breadcrumb').height()
    //});

    $.get(config.apiURL + 'settings.json?namespace=mobileApp&name=menuItems&key=' + config.privateKey)
    .then(res => {
      var settingsId = res.settings[0].settingsId;
      var menuItems = JSON.parse(res.settings[0].value).menuItems;
      this.setState({ settingsId, menuItems });
    });

    $(window).mouseup(this.handleDragEnd);

    $(window).mousemove(this.handleMove);
  },

  componentDidUpdate(prevProps, prevState){
  },

  handleDragStart(e){
    e.dragMoves = 0;
    this.setState(e);
    setTimeout(() => {
      this.setState({ dragMoves: this.props.minDragMoves });
    }, this.props.animationDuration);
  },

  handleMove(e){
    console.log('move');

    var newState = {
      dragMoves: this.state.dragMoves + 1,
      mouseX: e.pageX,
      mouseY: e.pageY
    };

    if (this.state.dragItemLabel !== null){
      newState.menuItems = _(this.state.menuItems)
      .sortBy((item, i) => {
        if (item.label === this.state.dragItemLabel){
          return e.pageY - this.state.dragCursorOffsetY;
        } else {
          if (this.state.selectedItemLabel !== null){
            var selectedItemIndex = _.findIndex(this.state.menuItems, { label: this.state.selectedItemLabel });
            if (selectedItemIndex < i){
              return (i - 1) * this.props.itemHeight + this.props.selectedItemHeight;
            } else {
              return i * this.props.itemHeight;
              if (this.state.selectedItemLabel === item.label){
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
            bottom: menuOffset.top + menu.height()
          }


          if (menuZone.left < e.pageX && e.pageX < menuZone.right && menuZone.top < e.pageY && e.pageY < menuZone.bottom){
            var itemToAdd = _.omit(this.props.menuItemBucket[this.state.dragItemBucketId], 'bucketId');
            newState.menuItems.push(itemToAdd);
            newState.dragItemBucketId = null;
            newState.selectedItemLabel = itemToAdd.label;

            var bucketTop = $(this.refs.bucket.getDOMNode()).offset().top;
            //console.log(e.pageY, this.state.dragCursorOffsetY, menuZone.top, bucketTop);
            newState.dragCursorOffsetY = this.state.dragCursorOffsetY + menuZone.top - bucketTop;
            console.log(this.state, newState);
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
    console.log('end');
    var newState = {
      dragMoves: 0,
      dragItemLabel: null,
      dragItemBucketId: null,
    };

    if (this.state.dragMoves < this.props.minDragMoves){
      if (this.state.selectedItemLabel === this.state.dragItemLabel){
        newState.selectedItemLabel = null;
      } else if (this.state.dragItemLabel !== null && _.any(this.state.menuItems, { label: this.state.dragItemLabel })){
        newState.selectedItemLabel = this.state.dragItemLabel;
      }
    }

    this.setState(newState);
  },

  calcPreviewHeight(){
    var height = 0;

    if (this.state.selectedItemLabel !== null){
      height = (this.state.menuItems.length - 1) * this.props.itemHeight + this.props.selectedItemHeight;
    } else {
      height = this.state.menuItems.length * this.props.itemHeight;
    }

    return height;
  },

  getRemainingBucketItems(){
    return _(this.props.menuItemBucket)
      .forEach((item, i) => { item.bucketId = i; })
      .filter(
        item => !_.any(this.state.menuItems, { label: item.label })
      )
      .sortBy('label')
      .value();
  },

  removeItem(label){
    var newState = {
      menuItems: _.reject(this.state.menuItems, { label })
    };
    if (this.state.selectedItemLabel === label){
      newState.selectedItemLabel = null;
    }
    this.setState(newState);
  }

});


module.exports = MenuItemEditor;
