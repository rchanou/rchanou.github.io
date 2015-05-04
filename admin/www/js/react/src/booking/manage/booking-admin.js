/*** IMPORT EXTERNAL MODULES ***/

var moment = require('moment');
var _ = require('lodash');
var React = require('react/addons');


/*** EXTERNAL REACT COMPONENTS ***/

var DatePicker = require('../../components/datepicker');
var Select = require('../../components/old-react-select2');
var IRadioGroup = require('../../components/iradio-group');

/*** EXTERNAL REACT MIXINS ***/

var EventFunnel = require('../../mixins/event-funnel');


/*** CONSTANTS ***/

var PRODUCT_TYPE_RESERVATION = 4;

var NO_SELECT = {
	WebkitTouchCallout: 'none',
	WebkitUserSelect: 'none',
	KhtmlUserSelect: 'none',
	MozUserSelect: 'none',
	MsUserSelect: 'none',
	userSelect: 'none'
}


/*** HELPERS ***/

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

		if (elem.getAttribute('type') === 'number' && !elem.value){
			return elem.getAttribute('defaultValue') || elem.getAttribute('min') || 0;
		} else if (elem.getAttribute('type') === 'checkbox') {
			return elem.checked;
		} else {
			return elem.value;
		}
	}
};


/*** REACTIFIED JQUERY PLUGINS ***/

var Popup = require('../../components/popup.js');

var ICheck = require('../../components/old-icheck.js')

var TrackSelect = require('../../components/track-select');


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

	render(){
		var booking = this.props.booking;
		var start = moment(this.props.race.starts_at);

		var style = {};
		if (this.props.selected){
			style.backgroundColor = 'lightblue';
		}

		return <tr style={style}>

			<td onClick={this.toFunnel} className='text-center'>
				<ICheck checked={this.props.selected} onFunnelEvent={this.toFunnel} />
			</td>

			<td className='text-right'>
				{start.isValid()? start.format('h:mm a'): start}
			</td>

			<td>
				{this.props.race.race_name}
			</td>

			{ this.props.product? <td>{this.props.product.description}</td>
				:	<td style={{ color: 'gray' }}>(no booking)</td> }

			<td className='text-center'>{
				booking.isPublic == null? null :
				booking.isPublic? <i className="fa fa-globe" title="This booking is public."></i> :
					<i className="fa fa-lock" title="This booking is private."></i>
			}</td>

			<td className='text-right'>
				{booking.quantityTotal}
			</td>

		</tr>;
	}

});


/*** TOP LEVEL/ROOT/MAIN/PARENT COMPONENT ***/

module.exports = React.createClass({

	mixins: [EventFunnel, ParseRef, jQuerify /*, React.addons.PureRenderMixin*/],

	getDefaultProps(){
		var thisConfig = config || {
			apiURL: 'https://vm-122.clubspeedtiming.com/api/index.php',
			apiKey: 'cs-dev',
			privateKey: 'cs-dev'
		};
		if (window && window.location && (window.location.hostname === '192.168.111.205' || window.location.hostname === 'localhost')) {
			config.apiURL = 'https://vm-122.clubspeedtiming.com/api/index.php';
		} else {
			console.log = function(){};
		}
		thisConfig.apiURL += '/';

		//console.log(thisConfig);

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
			popup: { message: null, alertClass: null },
			raceDetails: {},
			filterByDate: moment(),
			requestCount: 0,
			filterByHeatTypeId: false
		};
	},

	getMomentFormat(apiFormat){
		var momentFormat = (apiFormat || this.props.config.dateFormat)
			.replace('Y', 'YYYY')
			.replace('m', 'M')
			.replace('d', 'D')
		+ ' H:mm:ss';
		return momentFormat;
	},

	isRaceInDate(race, start){
		start = moment(start);
		start.startOf('d');

		var end = moment(start);
		end.add(1, 'd');

		var raceStart = moment(race.starts_at, this.getMomentFormat());
		return (raceStart.isAfter(start) || raceStart.isSame(start)) && raceStart.isBefore(end);
	},

	filterBookings(start){
		var relatedBookings = _(this.state.raceDetails)
			.pick(race => (!this.state.filterByTrackId || race.track_id == this.state.filterByTrackId)
										&& this.isRaceInDate(race, start)
										&& (!this.state.filterByHeatTypeId || race.heat_type_id == this.state.filterByHeatTypeId))
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
		if (!this.state.popup.message){
			return null;
		}

		return <Popup
			{...this.state.popup}
			key={this.state.popup.message}
			onFadeComplete={this.handlePopupClick}
			onClick={this.handlePopupClick}
		 />;
		//	{this.state.popup.message}
		//</FadeOut>;
	},

	renderProductSelectOptions(){
		return _.map(this.state.products, product =>
			<option value={product.productId} key={product.productId}>
				{product.description}
			</option>
		);
	},

	getProductSelectListByPoints(){
		return _(this.state.products)
			.filter(product => product.productType === PRODUCT_TYPE_RESERVATION)
			.map(product => ({ value: product.productId, label: product.description }))
			.value();

		try {
			if (this.state.selectedBookingIds.length == 0){
				return _.map(this.state.products, product => ({ value: product.productId, label: product.description }));
			}

			var selectedRaces = _.map(this.state.selectedBookingIds, id => {
				if (id.heatId){
					return this.state.raceDetails[id.heatId];
				} else {
					var selectedBooking = _.findWhere(this.state.bookings, booking => booking.onlineBookingsId == id);
					if (selectedBooking && selectedBooking.heatId){
						return this.state.raceDetails[selectedBooking.heatId];
					}
				}
			});

			var minPointsNeeded = _(selectedRaces).pluck('PointsNeeded').compact().max().value();
			var filterPredicate;
			if (minPointsNeeded == 0){
				filterPredicate = (product) => !product.p_Points;
			} else {
				filterPredicate = (product) => product.p_Points >= minPointsNeeded;
			}
			return _(this.state.products)
				.filter(filterPredicate)
				.map(product => ({ value: product.productId, label: product.description }));
		} catch(ex){
			return _.map(this.state.products, product => ({ value: product.productId, label: product.description }));
		}
	},

	renderBookingNav(){
		return <div className='col-md-6' key='nav'>
			<div className="row">
				<h3>All Activities</h3>
			</div>

			<div className='row form'>
				<div className={'form-group col-xs-12 col-sm-6 col-md-12 col-lg-4'} style={{ paddingBottom: 15, minWidth: 200 }}>
					<label className='control-label'>
						Date
					</label>
					<DatePicker ref='date' className='form-control' onSelect={this.handleDateSelect} date={this.state.filterByDate} language={this.props.language} />
				</div>

				<div className='form-group col-xs-12 col-sm-6 col-md-12 col-lg-4' style={{ paddingBottom: 15, paddingRight: 15 }}>
					<label className='control-label'>
						Track
					</label>
					<div>
						<TrackSelect config={this.props.config} className='form-control' style={{ width: '100%' }} onFunnelEvent={this.handleTrackSelectEvent} />
					</div>
				</div>

				<div className='form-group col-xs-12 col-sm-6 col-md-12 col-lg-4' style={{ paddingBottom: 15 }}>
					<label className='control-label'>
						Activity Type
					</label>
					<div>
						<Select className='form-control' list={this.getHeatTypes()} selectedId={this.state.filterByHeatTypeId}
							onFunnelEvent={this.handleHeatTypeSelect} />
					</div>
				</div>
			</div>

			<div className="row form-group">
				{this.renderBookingTable()}
			</div>
		</div>;
	},

	getHeatTypes(){
		var races = _.values(this.state.raceDetails);
		return _(races).filter(race => this.isRaceInDate(race, this.state.filterByDate))
		.map(race => ({
			value: race.heat_type_id,
			label: race.race_name
		}))
		.uniq('value')
		.value();
	},

	handleHeatTypeSelect(e){
		if (e.added){
			this.setState({ filterByHeatTypeId: e.val || false, selectedBookingIds: [] });
			//this.loadBookings(event.val);
		} else if (e.val == ''){
			this.setState({ filterByHeatTypeId: false });
		}
	},

	renderBookingTable(){
		var foundBookings = this.filterBookings(this.state.filterByDate);
		var loadingMessage = 'Getting/refreshing activities for selected date and/or track...';

		if (foundBookings.length == 0){
			if (this.state.requestCount > 0){
				return loadingMessage;
			} else {
				return 'No activities found for this date, track, and/or activity type.';
			}
		}

		var bookingRows = _(foundBookings)
			.map(booking => ({ booking, race: this.state.raceDetails[booking.heatId] }) )
			.sortBy(bookingAndRace => moment(bookingAndRace.race.starts_at).unix() )
			.map(bookingAndRace => {
				var booking = bookingAndRace.booking;
				return <BookingRow
					key={booking.onlineBookingsId || 'race.' + booking.heatId}
					booking={booking} race={bookingAndRace.race} product={this.state.products[booking.productsId]}
					selected={
						_.contains(this.state.selectedBookingIds, booking.onlineBookingsId)
						|| _.any(this.state.selectedBookingIds, { heatId: booking.heatId })
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
					<thead>
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
			<span>
				<input type='button'onClick={this.handleSelectAllClick}
					className={'btn ' + (this.areAllBookingsSelected()? 'btn-warning': 'btn-info')}
					value={this.areAllBookingsSelected()? 'Deselect All': 'Select All'} />
				{!this.areOnlyNewBookingsSelected() && this.state.selectedBookingIds.length > 0 &&
					 <input type='button' className='btn btn-danger pull-right' onClick={this.handleDeleteClick} value='Delete Booking(s)' />}
			</span>
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
				return <div className='col-md-6'><br/>
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
					<div className="col-xs-8">
						<Select onFunnelEvent={this.handleProductSelectEvent} selectedId={this.state.newProductId} placeholder=' '
							list={this.getProductSelectListByPoints()} style={{width: '80%'}} />
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
						<input className='form-control' type="number" min="0" ref='qtyAvail' max={this.calcMaxQty()}
						  onBlur={this.handleQtyAvailableChange} />
						<span className='text-info'>
							{(this.state.selectedBookingIds.length > 1 && this.refs.qtyAvail && (this.refs.qtyAvail.getDOMNode().value === '')
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

	areAllBookingsSelected(){
		return this.state.selectedBookingIds.length === this.filterBookings(this.state.filterByDate).length;
	},

	calcMaxQty(){
		var arr = _(this.state.selectedBookingIds)
			.map(id => {
				if (id.heatId){
					return this.state.raceDetails[id.heatId].RacersPerHeat;
				} else {
					var booking = _.findWhere(this.state.bookings, { onlineBookingsId: id });
					if (typeof booking === 'undefined'){
						return undefined;
					} else {
						return this.state.raceDetails[booking.heatId].RacersPerHeat;
					}
				}
			})
			.min();
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
					var time = moment(raceOfBooking.starts_at);
					var bookingText = raceOfBooking.race_name;
					if (time.isValid()){
				  	bookingText = moment(raceOfBooking.starts_at).format('MMM D h:mm a ') + bookingText;
					}

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
		var params = {
				key: this.props.config.privateKey,
				select: 'productId,description,p_Points,productType',
				filter: 'deleted$eqfalse'
		};

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
		this.setState({
			selectedBookingIds: [],
			requestCount:	this.state.requestCount + 1
			//filterByHeatTypeId: false
		});

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
				var query = $.param({ key: this.props.config.privateKey, heatId: race.HeatNo });
				var requestUrl = this.props.config.apiURL + 'booking.json?' + query;
				return $.get(requestUrl);
			});

			bookingRequests.forEach(req => {
				req.then(body => {
					body.bookings.forEach(booking => {
						this.upsertListInState('bookings', booking, { onlineBookingsId: booking.onlineBookingsId });
					});
				});
			});

			var pointsNeededList = [];
			var maxRacersList = [];
			var raceDetailRequests = body.races.map((race, i) => {
				pointsNeededList[i] = race.PointsNeeded;
				maxRacersList[i] = race.RacersPerHeat;
				var requestUrl = this.props.config.apiURL + 'races/' + race.HeatNo + '.json?key=' + this.props.config.privateKey;
				return $.get(requestUrl);
			});

			raceDetailRequests.forEach((req, i) => {
				req.then(body => {
					body.race.PointsNeeded = pointsNeededList[i];
					body.race.RacersPerHeat = maxRacersList[i];
					body.race.starts_at = moment(body.race.starts_at, this.getMomentFormat());
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
				this.setState({ requestCount: this.state.requestCount - allRequests.length });
			});
		});
	},

	componentDidUpdate(prevProps, prevState){
		if ((prevState.selectedBookingIds.length !== this.state.selectedBookingIds.length) && this.state.selectedBookingIds.length > 0){
			if (this.areOnlyNewBookingsSelected()){
				if (!_.every(prevState.selectedBookingIds, 'heatId') || prevState.selectedBookingIds.length === 0){
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
			{ filterByDate: payload.date, filterByHeatTypeId: false },
			() => { this.loadBookings(); }
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
			case 'click': case 'ifChecked': case 'ifUnchecked': case 'ifIndeterminate':
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
					if (!_.any(this.state.selectedBookingIds, { heatId: rowProps.booking.heatId })){
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

	handleProductSelectEvent(e, props, state){
		if (e.added){
			this.setState({ newProductId: e.val });
		} else if (e.val == ''){
			this.setState({ newProductId: null });
		}
	},

	handleQtyAvailableChange(){
		var elem = $(this.refs.qtyAvail.getDOMNode());

		var newVal = parseInt(elem.val());

		if (newVal < elem.attr('min')){
			newVal = elem.attr('min');
		}

		if (newVal > this.calcMaxQty()){
			newVal = this.calcMaxQty();
		}

		elem.val(newVal);

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

		var errors = [];

		var putRequests = this.state.selectedBookingIds.map(id => {
			var type, url, data;

			if (id.heatId){
				type = 'POST';
				url = this.props.config.apiURL + 'booking?key=' + this.props.config.privateKey;
				data = _.extend(change, {
					heatId: id.heatId,
					quantityTotal: change.quantityTotal
				});
			} else {
				type = 'PUT';
				url = this.props.config.apiURL + 'booking/' + id + '?key=' + this.props.config.privateKey;
				data = _.omit(change, 'heatId'); // omit is temp, prevent heatId from getting passed here in first place
			}

			var putRequest = $.ajax({ type,	url, data	});

			putRequest.then(
				_ => {},
				error => {
					try {
						var race;
						if (id.heatId){
							race = this.state.raceDetails[id.heatId];
						} else {
							var booking = _.findWhere(this.state.bookings, booking => booking.onlineBookingsId == id);
							race = this.state.raceDetails[booking.heatId];
						}
						var raceStart = moment(race.starts_at, 'YYYY-MM-DD HH:mm:ss.SSS');
						var raceTitle = race.race_name;
						if (raceStart.isValid()){
							raceTitle = raceStart.format('MMM DD h:mm a') + ' ' + raceTitle;
						}
						var errorMessage = error.responseJSON.error.message.replace('Precondition Failed: ', '');
						errors.push(raceTitle + ': ' + errorMessage);
					} catch(ex){
						errors.push('Error saving record.');
					}
				}
			);

			// return simple wrapper around PUT request deferred so it will resolve as "success"
			// because $.when will break on first failure
			// and we only care when all requests complete, regardless of success or failure,
			// as we are tracking errors with the errors array
			var wrappedRequest = $.Deferred();
			putRequest.always(_ => { wrappedRequest.resolve(); });
			return wrappedRequest;
		});

		$.when.apply($, putRequests).then(
			(...all) => {
				if (errors.length == 0){
					var popup = {
						message: 'Booking for ' + this.getBookingTitle()
							+ ' successfully saved! (' + moment().format('h:mm:ss a') + ')',
						alertClass: 'alert-success'
					};
				} else {
					var errorStart = 'The following error(s) occurred while trying to save:';
					var errorEnd, alertClass;
					if (putRequests.length == errors.length){
						errorEnd = 'No bookings were successfully saved.';
						alertClass = 'alert-danger';
					} else {
						errorEnd = 'Only ' + (putRequests.length - errors.length) + ' of ' + putRequests.length + ' bookings successfully saved.';
						alertClass = 'alert-warning';
					}
					var popup = {
						message: [errorStart].concat(errors).concat([errorEnd]).join('<br/><br/>'),
						alertClass
					};
				}

				this.setState({ popup });
				this.loadBookings();
			}
		);
	},

	handlePopupClick(){
		this.setState({ popup: {message: null, alertClass: null} });
	},

	handleSelectAllClick(){
		if (this.areAllBookingsSelected()){
			this.setState({ selectedBookingIds: [] });
		} else {
			var filteredBookingIds = this.filterBookings(this.state.filterByDate).map(booking => {
				if (booking.onlineBookingsId){
					return booking.onlineBookingsId;
				} else {
					return { heatId: booking.heatId };
				}
			});

			this.setState({ selectedBookingIds: filteredBookingIds });
		}
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
				}, error => {
					console.log('error yo', error);
				});
			});

			// handle responses once all delete requests are completed
			$.when.apply($, deleteRequests).then(
				(...all) => {
					var popup = {
						message: 'Booking(s) successfully deleted! ('	+ moment().format('h:mm:ss a') + ')',
						alertClass: 'alert-success'
					};

					this.setState({ popup, selectedBookingIds: [] });
					this.loadBookings();
				},
				(...all) => {
					var popup = {
						message: 'An error occurred while trying to delete booking(s).',
						alertClass: 'alert-error'
					};

					this.setState({ popup });
					this.loadBookings();
				}
			);
		}
	}
});
