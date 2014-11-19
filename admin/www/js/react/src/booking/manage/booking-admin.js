/*** IMPORT EXTERNAL MODULES ***/

var moment = require('moment');
var _ = require('lodash');
var React = require('react/addons');


/*** IMPORT EXTERNAL REACT COMPONENTS ***/

var DatePicker = require('../../components/datepicker');


/*** STYLES ***/

var NO_SELECT = {
	WebkitTouchCallout: 'none',
	WebkitUserSelect: 'none',
	KhtmlUserSelect: 'none',
	MozUserSelect: 'none',
	MsUserSelect: 'none',
	userSelect: 'none'
}


/*** HELPERS ***/

function generateUUID(){
    var d = new Date().getTime();
    var uuid = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
        var r = (d + Math.random()*16)%16 | 0;
        d = Math.floor(d/16);
        return (c=='x' ? r : (r&0x7|0x8)).toString(16);
    });
    return uuid;
};

var CheckPropChange = {
	checkPropChange(otherProps, ...keys){
		keys.forEach(key => {
			if (Array.isArray(key)){
				var thisTraverser = this.props;
				var otherTraverser = otherProps;			
				for (var i = 0; i < key.length; i++){
					thisTraverser = thisTraverser[key[i]];
					otherTraverser = otherTraverser[key[i]];					
					var definedForThis = typeof thisTraverser != 'undefined';
					var definedForOther = typeof otherTraverser != 'undefined';
					if (!definedForThis && !definedForOther){
						return false;
					} else if ((definedForThis && !definedForOther) || (definedForOther && !definedForThis) || (thisTraverser !== otherTraverser)){
						return true;
					}
				}
			} else if (this.props[key] !== otherProps[key]){
				return true;
			}
		});		
		return false;
	}
};


/*** REACT MIXINS ***/

var EventFunnel = {
	// mixin providing event-handling sugar based on an opinionated way of handling React component parent-child communication
	getDefaultProps(){
		return { onFunnelEvent(){} };
	},
	toFunnel(event, ...other){	
		this.props.onFunnelEvent.apply(this, [event, this.props, this.state].concat(other));
		//this.props.onFunnelEvent(event, this.props, this.state);
	},
	funnelJQueryEvents(...events){
		events.forEach(event => {
			$(this.getDOMNode()).on(
				event, e => { this.toFunnel(e); }
			);
		});
	}
}

var jQuerify = {
	jQuerify(ref){
		if ($){
			if (ref){
				return $(this.refs[ref].getDOMNode());
			} else {
				return $(this.getDOMNode());
			}
		} else {
			if (ref) {
				return this.refs[ref].getDOMNode();
			} else {
				return this.getDOMNode();
			}
		}
	}
}

var ParseRef = {
	parseRef(refName){	
		if (!this.isMounted() || !this.refs[refName]){
			return null;
		}
		
		var elem = this.refs[refName].getDOMNode();
		
		if (elem.getAttribute('type') == 'number' && !elem.value){
			return elem.getAttribute('defaultValue') || elem.getAttribute('min') || 0;
		} else if (elem.getAttribute('type') == 'checkbox') {
			return elem.checked;
		} else {
			return elem.value;
		}
	}
};


/*** REACTIFIED JQUERY PLUGINS ***/

var Select = React.createClass({
	mixins: [EventFunnel],
	getDefaultProps(){
		return {
			list: [], // list: [{value: 1, label: 'Apples'}, 'Bananas', 'Cherries'],
			selectedId: null,
			placeholder: '(any)',
			allowClear: true
		};
	},
	getInitialState(){
		return { open: false };
	},
	render(){
		var optionsFromList = this.props.list.map(item =>
			<option key={item.value} value={item.value}>
				{item.label}
			</option>
		);
		
		return <select>
			<option key={-1} value={null}></option>
			{optionsFromList}
			{this.props.children}
		</select>;
	},
	getSelectOptions(){
		var { list, selectedId, ...options } = this.props;  // omit list and selectedId
		return options;
	},
	componentDidMount(){
		var data = this.props.list.map(item => (
			{ id: item.value, text: item.label }
		));
		
		$(this.getDOMNode())
		.select2(this.getSelectOptions())
		.on('change', this.toFunnel)
		.on('select2-open', _ => 	{ this.setState({ open: true }) })
		.on('select2-close', _ => {	this.setState({ open: false }); });
		
		this.setFromProps();
	},
	componentDidUpdate(prevProps){
		if (prevProps.list.length != this.props.list.length){ // TODO: make list equality checking more robust?
			$(this.getDOMNode()).select2(this.getSelectOptions());
		}
		if (!this.state.open && prevProps.selectedId != this.props.selectedId){
			this.setFromProps();
		}
	},
	setFromProps(){
		$(this.getDOMNode()).val(this.props.selectedId).trigger('change');
	},
	componentWillUnmount(){
		$(this.getDOMNode()).select2('destroy');
	}
});

var LinkedSelect = React.createClass({
	mixins: [EventFunnel],
	getDefaultProps(){
		return {
			url: '',
			list: [],
			listProperty: 'products',
			valueProperty: 'productId',
			labelProperty: 'description'
		};
	},
	getInitialState(){
		return {
			list: this.props.list
		}
	},
	render(){
		var props = this.props;
		// must be wrapped in div to avoid wonky formatting, with BS3 at least
		return <div>
			<Select onFunnelEvent={this.toFunnel} selectedId={props.selectedId} placeholder={props.placeholder}
				list={_.map(this.state.list, item => ({ value: item[props.valueProperty], label: item[props.labelProperty]}))} />
		</div>;
	},
	componentWillMount(){
		if (this.props.url){
			$.get(this.props.url, body => {
				this.setState({ list: _.sortBy(body[this.props.listProperty], this.props.labelProperty) });
			});
		}
	}
});

var TrackSelect = React.createClass({
	mixins: [EventFunnel],
	render(){
		return <LinkedSelect url={config.apiURL + 'tracks/index.json?key=' + config.apiKey}
			listProperty='tracks' valueProperty='id' labelProperty='name'
			selectedId={this.props.selectedId} onFunnelEvent={this.toFunnel} />;
	}
});

var ProductSelect = React.createClass({
	mixins: [EventFunnel],
	render(){
		return <LinkedSelect
			url={config.apiURL + 'products.json?key=' + config.privateKey + '&select=productId,description'}
			listProperty='products' valueProperty='productId' labelProperty='description' placeholder=' '
			selectedId={this.props.selectedId} onFunnelEvent={this.toFunnel} />;
	}
});

var ICheck = require('../../components/icheck.js')

var IRadio = React.createClass({
	mixins: [EventFunnel],
	render(){
		return <input type='radio' />;
	},
	componentDidMount(){
		$(this.getDOMNode()).iCheck({
    	checkboxClass: 'icheckbox_flat-blue',
    	radioClass: 'iradio_flat-blue'
		})
		.on('ifClicked', this.toFunnel);
		this.setFromProps();
		//this.funnelJQueryEvents('ifChecked','ifUnchecked');
	},
	componentDidUpdate(){
		this.setFromProps();
	},
	setFromProps(){
		if (this.props.selected) {
			$(this.getDOMNode()).iCheck('check');
		} else {
			$(this.getDOMNode()).iCheck('uncheck');
		}
	}
});

var IRadioGroup = React.createClass({
	mixins: [EventFunnel],
	getDefaultProps(){
		return {
			inline: true,
			list: [{ label: 'A', value: 1 }, { label: 'b', value: 2 }],
			selected: null
		};
	},
	getInitialState(){
		// note: name is set by generateUUID here and should never be changed after,
		// but it is in initial state instead of default props so that a new UUID is generated for each IRadioGroup instance
		return { selected: this.props.selected, name: generateUUID() };
	},
	render(){
		var listNodes = [];
		
		this.props.list.forEach((item, i) => {
			listNodes.push(<div key={i}>
				<IRadio item={item} name={this.props.name || this.state.name} selected={this.props.selected === item.value}
					onFunnelEvent={this.handleRadioChange} />
				<label style={{ position: 'relative', top: -5, left: 10 }} >
					{item.label}
				</label>
			</div>);
		});
		
		return <span>
			{listNodes}
		</span>;
	},
	handleRadioChange(e, optionProps, state){
		this.toFunnel(e, optionProps);
	}
});


/*** CHILD COMPONENT(S) ***/

var BookingRow = React.createClass({
	mixins: [EventFunnel, CheckPropChange],
	getDefaultProps(){
		return {
			race: {
				race_name: 'Loading...',
				starts_at: 'Loading...'
			}
		};
	},
	getInitialState(){
		return {
			hovering: false
		};
	},
	render(){
		var booking = this.props.booking;
		var start = moment(this.props.race.starts_at);
		var style = {}; //{ cursor: 'pointer' };
		if (this.props.selected){
			style.backgroundColor = 'lightblue';
		}
	
		var editBoxStyle = { cursor: 'pointer' };
		return <tr style={style}>
			<td onClick={this.toFunnel}>
				<ICheck checked={this.props.selected} onFunnelEvent={this.toFunnel} />
			</td>
			<td>{start.isValid()? start.format('h:mm a'): '?'}</td>
			<td>{this.props.race.race_name}</td>
			{this.props.product? <td>{this.props.product.description}</td>
				:	<td style={{ color: 'gray' }}>(no booking)</td>}
			<td>{booking.isPublic == null? null :
						booking.isPublic? <i className="fa fa-globe" title="This booking is public."></i> :
										 <i className="fa fa-lock" title="This booking is private."></i>
			}</td>
			<td>{booking.quantityTotal}</td>
		</tr>;
	}
});


var FadeOut = require('../../components/fadeout.js');


/*** TOP LEVEL/ROOT/MAIN/PARENT COMPONENT ***/

var BookingAdmin = React.createClass({
	mixins: [EventFunnel, ParseRef, jQuerify /*, React.addons.PureRenderMixin*/],

	getDefaultProps(){
		var thisConfig = config || {
			apiURL: 'https://192.168.111.122/api/index.php',
			apiKey: 'cs-dev',
			privateKey: 'cs-dev'
		};
		if (window && window.location && window.location.hostname == '192.168.111.165') {
			config.apiURL = 'https://192.168.111.122/api/index.php';
		} else {
			console.log = function(){};
		}
		thisConfig.apiURL += '/';
		
		return {
			config: thisConfig,
			language: 'en-US',
			popupTime: 3000
		};
	},
	
	getInitialState(){	
		return {
			bookings: [],
			tracks: [],
			filterByTrackId: false,
			products: {},
			selectedBookingIds: [],
			newProductId: null,
			newIsPublic: null,
			popupMessage: null,
			raceDetails: {},
			loading: false,
			filterByDate: moment(),
			requestCount: 0
		};
	},
	
	isRaceInDate(race, start){
		start = moment(start);
		start.startOf('d');		
		
		var end = moment(start);
		end.add(1, 'd');
		
		var raceStart = moment(race.starts_at, 'YYYY-MM-DD H:mm:ss.SSS');		
		return (raceStart.isAfter(start) || raceStart.isSame(start)) && raceStart.isBefore(end);
	},
	
	filterBookings(start){
		var relatedBookings = _(this.state.raceDetails)
			.pick(race => (!this.state.filterByTrackId || race.track_id == this.state.filterByTrackId) && this.isRaceInDate(race, start))
			.mapValues(race => {
				var raceBookings = _.filter(this.state.bookings, booking => booking.heatId == race.id);
				
				if (raceBookings.length > 0){
					return raceBookings;
				} else {
					var newBookingPlaceholder = {
						heatId: race.id, onlineBookingsId: null, productsId: null, isPublic: null, quantityTotal: null
					};
					return newBookingPlaceholder;
				}
			})
			.values().flatten().value();
			
		return relatedBookings;
	},
	
	render(){
		return <div className="container-fluid">
			{this.renderPopup()}
			<div className="row" key='main'>
				<div className='col-xs-12'>
					<div className='widget-box'>
						<div className='widget-title'>
							<span className='icon'>
								<i className='fa fa-align-justify' />
							</span>
							<h5>Manage Bookings</h5>
						</div>
						<div className='widget-content'>
							<div className='row'>
								{this.renderBookingNav()}
								{this.renderEditForm()}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>;
	},
		
	renderPopup(){
		if (!this.state.popupMessage){
			return null;
		}
		
		return <FadeOut element='div' onFadeComplete={this.handlePopupClick} onClick={this.handlePopupClick} key={this.state.popupMessage}
			style={{ position: 'fixed', top: 0, left: 0, width: '100%', cursor: 'pointer', zIndex: 9999, textAlign: 'center' }}
			className='alert alert-success'>
			{this.state.popupMessage}
		</FadeOut>;
	},
	
	renderProductSelectOptions(){
		return _.map(this.state.products, product =>
			<option value={product.productId} key={product.productId}>
				{product.description}
			</option>
		);
	},
	
	getProductSelectList(){
		return _.map(this.state.products, product => ({ value: product.productId, label: product.description }));
	},
	
	renderBookingNav(){
		return <div className='col-md-6' key='nav'>
			<div className="row">
				<h3>All Activities</h3>
			</div>
			
			<div className='row form-inline'>
				<div className='form-group col-xs-10 col-sm-4 col-md-7 col-lg-5' style={{ paddingLeft: 0, paddingBottom: 15 }}>
					<label className='control-label'>
						Date
					</label>
					<DatePicker ref='date' className='form-control' onSelect={this.handleDateSelect} date={this.state.filterByDate} language={this.props.language} />
				</div>
				
				<div className='form-group' style={{ paddingBottom: 15 }}>
					<label className='control-label'>
						Track
					</label>
					<TrackSelect className='form-control' onFunnelEvent={this.handleTrackSelectEvent} />
				</div>
			</div>
			
			<div className="row form-group">
				{this.renderBookingTable()}
			</div>
		</div>;		
	},
	
	renderBookingTable(){
		var foundBookings = this.filterBookings(this.state.filterByDate);
		var loadingMessage = 'Getting/refreshing activities for selected date and/or track...';
	
		if (foundBookings.length == 0){
			if (this.state.requestCount > 0){ // if (this.state.loading){
				return loadingMessage;
			} else {	
				return 'No activities found for this date and/or track.';
			}
		}
		
		var bookingRows = _(foundBookings)
			.map(booking => ({ booking, race: this.state.raceDetails[booking.heatId] }) )
			.sortBy(bookingAndRace => moment(bookingAndRace.race.starts_at).unix() )
			.map(bookingAndRace => {
				var booking = bookingAndRace.booking;
				return <BookingRow
					key={booking.onlineBookingsId || 'race.' + booking.heatId}
					booking={booking} race={bookingAndRace.race/*this.state.raceDetails[booking.heatId]*/} product={this.state.products[booking.productsId]}
					selected={
						_.contains(this.state.selectedBookingIds, booking.onlineBookingsId)
						|| _.find(this.state.selectedBookingIds, { heatId: booking.heatId })
					}
					onFunnelEvent={this.handleBookingRowEvent}
				/>;
			})
			.value();
		
		return <div key='table'>
			<div>
				{this.state.requestCount > 0 && loadingMessage}<br/>
			</div>
			<div style={{ overflowY: 'auto' }} ref='table'>
				<table className='table table-bordered'>
					<thead className='text-left'>
						<tr>
							<th key='editing'>Editing</th>
							<th key='time'>Time</th>
							<th key='name'>Name</th>
							<th key='product'>Product</th>
							<th key='public'>Public?</th>
							<th key='qty'>Qty</th>
						</tr>
					</thead>
					<tbody>
						{bookingRows}
					</tbody>
				</table>
			</div>
			{this.state.selectedBookingIds.length > 0 &&
				<span>
					<input type='button' className='btn btn-info' onClick={this.handleDeselectClick} value='De-select All' />
					{!this.areOnlyNewBookingsSelected() &&
						 <input type='button' className='btn btn-danger pull-right' onClick={this.handleDeleteClick} value='Delete Booking(s)' />}
				</span>}
		</div>;
	},
	
	renderEditForm(){
		if (this.filterBookings(this.state.filterByDate).length == 0){
			return null;
		}
		
		if (this.state.selectedBookingIds.length == 0){
			if (this.state.requestCount > 0){
				return null;
			} else {
				return <div className='col-md-6'>
					Select one or more activities to edit them.
				</div>;
			}
		}
	
		var title = this.state.selectedBookingIds.length > 0 && 
			<span>Editing {this.getBookingTitle(true)}</span>;
					
		return <div className='col-md-6' key='form'>
			<form className="form-horizontal">
					<legend className="row form-group">
						{title}
					</legend>
				
				<div className="row form-group">
					<label className="col-xs-4 control-label">
						Product Required
					</label>
					<div className="col-xs-4">
						<Select onFunnelEvent={this.handleProductSelectEvent} selectedId={this.state.newProductId} placeholder=' ' 
							list={this.getProductSelectList()} />
						<span className='text-info'>
							{this.state.selectedBookingIds.length > 1 && this.state.newProductId == null && !this.areOnlyNewBookingsSelected()
								&& '(multiple)'}
						</span>
					</div>
				</div>
				
				<div className="row form-group">
					<label className="col-xs-4 control-label">
						Show To Public?
					</label>
					<div className="col-xs-3">
						{/*<ICheck onFunnelEvent={this.handlePublicCheckEvent} checked={this.state.newIsPublic} />*/}
						<IRadioGroup onFunnelEvent={this.handleRadioChange} selected={this.state.newIsPublic}
									list={[ { label: 'Yes', value: true }, { label: 'No', value: false } ]} />
						<span className='text-info'>
							{this.state.selectedBookingIds.length > 1 && this.state.newIsPublic == null  && !this.areOnlyNewBookingsSelected()
								&& '(multiple)'}
						</span>
					</div>
				</div>
				
				<div className="row form-group">
					<label className="col-xs-4 control-label">
						Qty. Available Online
					</label>
					<div className="col-xs-3">
						<input className='form-control' type="number" min="0" ref='qtyAvail'
						  onBlur={this.handleQtyAvailableChange} />
						<span className='text-info'>
							{(this.state.selectedBookingIds.length > 1 && (this.refs.qtyAvail.getDOMNode().value === '')
								 && _.countBy(this.state.selectedBookingIds, 'heatId')['undefined'] > 1)
								 && '(multiple)'}
						</span>
					</div>
				</div>
				
				<div className="row form-group">
					<div className="col-xs-4"></div>
					<div className="col-xs-6">
						<input className="btn btn-info" type="submit" value="Save" disabled={!this.isFormValid()}
							style={{ cursor: this.isFormValid() || 'not-allowed' }}
							dataToggle='tooltip' dataPlacement='left' ref='saveButton'
							title={this.isFormValid() || 'Cannot save. One or more required fields are missing.'}
							onClick={this.handleSaveClick}/>
						{this.isFormValid() || <span className='help-block has-warning'>Please fill out all fields.</span>}
					</div>
				</div>
			</form>
		</div>;
	},
	
	getBookingById (id){
		return _.findWhere(this.state.bookings, { onlineBookingsId: id });
	},
	
	getBookingTitle(asHTML){
		var bookingTitles = [];
		
		this.state.selectedBookingIds.forEach(item => {
			if (item.heatId){
				var raceName = this.state.raceDetails[item.heatId].race_name;
				if (asHTML){
					bookingTitles.push(<span>{raceName} (new booking)<br/></span>);
				} else {
					bookingTitles.push(raceName);
				}
			} else {
				var booking = this.getBookingById(item);
				
				if (booking){
				  var raceOfBooking = this.state.raceDetails[booking.heatId];
				  var bookingText = moment(raceOfBooking.starts_at).format('MMM D h:mm a ') + raceOfBooking.race_name;
				
				  if (asHTML){
				    bookingTitles.push(<span>{bookingText}<br/></span>);
				  } else {
				    bookingTitles.push(bookingText);
				  }
				}
			}			
		});
		
		return asHTML? bookingTitles: bookingTitles.join(', ');
	},
	
	componentDidMount(){	
		// load tracks for Track Select
		$.get(
			this.props.config.apiURL + 'tracks/index.json?key=' + this.props.config.apiKey,
			body => {
				this.setState({ tracks: body.tracks });
			}
		);
		
		// load products
		var params = { key: this.props.config.privateKey, select: 'productId, description', filter: 'deleted$eqfalse' };
		$.get(
			this.props.config.apiURL + 'products.json?' + $.param(params),
			body => {
				this.setState({ products: _.indexBy(body.products, 'productId') });
			}
		);
		
		this.loadBookings();
				
		/*$(window).resize(_ => {
			if (this.refs.table){
				var tableElem = $(this.refs.table.getDOMNode());
					tableElem.height($(window).height() - tableElem.offset().top - 40);
			}
		});*/
	},
	
	upsertListInState(listName, item, match){
		var list = this.state[listName];
		var currentIndex = _.findIndex(list, match);
		var newList;
		if (currentIndex == -1){
			newList = React.addons.update(
				list,
				{ $push: [item] }
			);
		} else {
			var change = {};
			change[currentIndex] = { $set: item };
			newList = React.addons.update(
				list,
				change
			);
		}		
		var newState = {};
		newState[listName] = newList;
		this.setState(newState);
	},
	
	loadBookings(filterByTrackId){	
		this.setState({ selectedBookingIds: [], loading: true, requestCount: this.state.requestCount + 1 });
	
		var inputDate = this.state.filterByDate;
		var start = moment(inputDate);
		var end = moment(inputDate);
		end.add(1, 'd');
		
		var params = {
			key: this.props.config.apiKey
		};
		if (start.isValid() && end.isValid()){
			params.start = start.format('YYYY-MM-DD');
			params.end = end.format('YYYY-MM-DD');
		}
		if (filterByTrackId){
			params.track_id = filterByTrackId;
		}
		
		var requestUrl = this.props.config.apiURL + 'races/races.json?' + $.param(params);

		$.get(requestUrl)
		.then(body => {
			var bookingRequests = body.races.map(race => {
				var requestUrl = this.props.config.apiURL + 'booking.json?' + $.param({ key: this.props.config.privateKey, heatId: race.HeatNo });			
				return $.get(requestUrl);
			});
			
			bookingRequests.forEach(req => {
				req.then(body => {
					body.bookings.forEach(booking => {
						this.upsertListInState('bookings', booking, { onlineBookingsId: booking.onlineBookingsId });			
					});
				});
			});
						
			var raceDetailRequests = body.races.map(race => {
				var requestUrl = this.props.config.apiURL + 'races/' + race.HeatNo + '.json?key=' + this.props.config.privateKey;
				return $.get(requestUrl);
			});
						
			raceDetailRequests.forEach(req => {
				req.then(body => {
					var change = {};
					change[body.race.id] = { $set: body.race };
					var raceDetails = React.addons.update(
						this.state.raceDetails,
						change
					);
					this.setState({ raceDetails });
				});
			});
			
			this.setState({ requestCount: this.state.requestCount + bookingRequests.length + raceDetailRequests.length - 1 });
			
			var allRequests = bookingRequests.concat(raceDetailRequests);
			$.when.apply($, allRequests).done(_ => {
				this.setState({ loading: false, requestCount: this.state.requestCount - allRequests.length });
			});
		});
	},
	
	componentDidUpdate(prevProps, prevState){
		if (prevState.selectedBookingIds.length != this.state.selectedBookingIds.length && this.state.selectedBookingIds.length > 0){
			if (this.areOnlyNewBookingsSelected()){
				if (!_.every(prevState.selectedBookingIds, 'heatId') || prevState.selectedBookingIds.length == 0){
					this.setState({ newProductId: null, newIsPublic: true });
					this.refs.qtyAvail.getDOMNode().value = 1;
				}
			} else {
				var existingBookingIds = _.reject(this.state.selectedBookingIds, 'heatId');
				var existingBookings = _.filter(this.state.bookings, booking => _.contains(existingBookingIds, booking.onlineBookingsId));
				var firstBooking = existingBookings[0];
				var newProductId;
				var newIsPublic;
					
				if (_.every(existingBookings, booking => booking.quantityTotal == firstBooking.quantityTotal)){
					this.refs.qtyAvail.getDOMNode().value = firstBooking.quantityTotal;						
				} else {
					this.refs.qtyAvail.getDOMNode().value = '';
				}				
				if (_.every(existingBookings, booking => firstBooking.productsId == booking.productsId)){
					newProductId = firstBooking.productsId;
				} else {
					newProductId = null;
				}					
				if (_.every(existingBookings, booking => firstBooking.isPublic == booking.isPublic)){
					newIsPublic = firstBooking.isPublic;
				} else {
					newIsPublic = null;
				}
				this.setState({ newProductId, newIsPublic });
				
			}
		}
		
		if (this.refs.saveButton){
			if (this.isFormValid()){
				$(this.refs.saveButton.getDOMNode()).tooltip('destroy');
			} else {
				$(this.refs.saveButton.getDOMNode()).tooltip();
			}
		}
	},
	
	areOnlyNewBookingsSelected(){
		return _.every(this.state.selectedBookingIds, 'heatId');
	},
	
	isFormValid (){	
		if (_.some(this.state.selectedBookingIds, 'heatId')){
			return this.state.newProductId != null && this.state.newIsPublic != null && this.parseRef('qtyAvail') != null;
		} else {
			return true;
		}
	},
	
	handleDateSelect(payload){
		this.setState(
			{ filterByDate: payload.date },
			_ => { this.loadBookings(); }
		);
	},
	
	handleTrackSelectEvent (event, props){
		if (event.added){
			this.setState({ filterByTrackId: event.val || false });
			this.loadBookings(event.val);
		} else if (event.val == '') {
			this.setState({ filterByTrackId: false });
		}
	},
	
	handleBookingRowEvent (e, rowProps){
		switch (e.type) {
			case 'click': case 'ifChecked': case 'ifUnchecked':
				var clickedBookingId = rowProps.booking.onlineBookingsId;				
				
				var selectedBookingIds = [];
				if (clickedBookingId){				
					if (_.contains(this.state.selectedBookingIds, clickedBookingId)){
						selectedBookingIds = _.without(this.state.selectedBookingIds, clickedBookingId);
					} else {
						selectedBookingIds = React.addons.update(
							this.state.selectedBookingIds,
							{ $push: [clickedBookingId] }
						);
					}
				} else {
					if (typeof _.findWhere(this.state.selectedBookingIds, { heatId: rowProps.booking.heatId }) == 'undefined'){
						selectedBookingIds = React.addons.update(
							this.state.selectedBookingIds,
							{ $push: [{ heatId: rowProps.race.id }] }
						);
					} else {
						selectedBookingIds = _.reject(this.state.selectedBookingIds, { heatId: rowProps.booking.heatId });
					}
				}
				this.setState({ selectedBookingIds });
				break;
		}		
	},
	
	handlePublicCheckEvent(e){		
		switch (e.type){
			case 'ifChecked':
				this.setState({ newIsPublic: true, saveEnabled: true });
				break;
			case 'ifUnchecked':
				this.setState({ newIsPublic: false, saveEnabled: true });			
				break;
		}
	},
	
	handleRadioChange(e, props, state, optionProps){
		this.setState({ newIsPublic: optionProps.item.value });
	},
	
	handleProductSelectEvent: function(e, props, state){
		if (e.added){
			this.setState({ newProductId: e.val });
		} else if (e.val == ''){
			this.setState({ newProductId: null });
		}
	},
	
	handleQtyAvailableChange(){
		var elem = this.jQuerify('qtyAvail');
		
		elem.val(parseInt(elem.val()));
		
		if (elem.val() < elem.attr('min')){
			elem.val(elem.attr('min'));
		}
		
		this.forceUpdate();
	},
	
	handleSaveClick(e){
		e.preventDefault();
		
		var editCount = this.state.selectedBookingIds.length;
		if (editCount > 1){
			var confirmed = confirm('Save changes for ' + editCount + ' bookings?');
			if (!confirmed){
				return;
			}
		}
		
		var change = {};
		if (this.state.newIsPublic != null){
			change.isPublic = this.state.newIsPublic;
		}
		if(this.state.newProductId != null){
			change.productsId = this.state.newProductId;
		}
		if(this.refs.qtyAvail.getDOMNode().value != ''){
			change.quantityTotal = this.parseRef('qtyAvail');
		}
		
		this.putChange(change);
	},
	
	putChange(change){
		if (this.state.selectedBookingIds.length == 0){
			return;
		}
		
		var putRequests = this.state.selectedBookingIds.map(id => {
			console.log('put request id', id);
			if (id.heatId){
				var url = this.props.config.apiURL + 'booking?key=' + this.props.config.privateKey;
				var data = _.extend(change, {
					heatId: id.heatId,
					quantityTotal: change.quantityTotal
				});			
				
				return $.ajax({
					type: 'POST',
					url,
					data
				});
			} else {
				var url = this.props.config.apiURL + 'booking/' + id + '?key=' + this.props.config.privateKey;
				var data = _.omit(change, 'heatId'); // omit is temp, prevent heatId from getting passed here in first place
				
				return $.ajax({
					type: 'PUT',
					url,
					data				
				});
			}
		});
		
		$.when.apply($, putRequests).then(
			(...all) => {
				var popupMessage = 'Booking for ' + this.getBookingTitle() + ' successfully saved! ('
					+ moment().format('h:mm:ss a') + ')';
				this.setState({ popupMessage });
				this.loadBookings();
			},
		  (error) => {
				var responseError;
				var errorToDisplay;				
				try {				
					responseError = error.responseJSON.error.message;
					if (responseError.indexOf('require points') !== -1){
						errorToDisplay = 'Could not save record. This activity does not require points, so you cannot choose a points product.'
					} else {
						errorToDisplay = 'The following error occurred while trying to save:\n\n' + responseError;					
					}
				} catch(_) {
					errorToDisplay = 'An error occurred while trying to save the record.';
				}
					
				alert(errorToDisplay);
				this.loadBookings();
			}
		);
	},
	
	handlePopupClick(){
		this.setState({ popupMessage: null });
	},
	
	handleDeselectClick(){
		this.setState({ selectedBookingIds: [] });
	},
	
	handleDeleteClick(){
		var message = 'You are about to delete all bookings for the ';
		if (this.state.selectedBookingIds.length > 1){
			message +=  this.state.selectedBookingIds.length + ' selected activities.'
		} else {
			message += 'selected activity.'
		}
		message += ' Click OK to continue.';
		
		var confirmed = confirm(message);		
		if (confirmed){
			// remove booking placeholders for activities w/o booking (can't delete what doesn't exist)
			var bookingsToDelete = _.reject(this.state.selectedBookingIds, 'heatId');
			
			// start delete requests
			var deleteRequests = [];
			
			bookingsToDelete.forEach(id => {
					var request = $.ajax({
						url: this.props.config.apiURL + 'booking/' + id + '?key=' + this.props.config.privateKey,
						type: 'DELETE'	
					});
					
					deleteRequests.push(request);
					
					// after delete successful, remove it from local bookings cache
					request.then(response => {
						var bookings = _.reject(this.state.bookings, booking => booking.onlineBookingsId == id);											
						this.setState({ bookings });						
					});
				}
			);
			
			// handle responses once all delete requests are completed
			$.when.apply($, deleteRequests).then(
				(...all) => {
					var popupMessage = 'Booking(s) successfully deleted! ('
						+ moment().format('h:mm:ss a') + ')';						
											
					this.setState({ popupMessage, selectedBookingIds: [] });
					
					this.loadBookings();
				},
				(...all) => {
					var errorMessage = 'An error occurred while trying to delete booking(s).';
					alert(errorMessage);
				}
			);		
		}
	}
});

module.exports = BookingAdmin;