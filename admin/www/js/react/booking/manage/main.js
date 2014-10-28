/** @jsx React.DOM */


/*** CONSTANTS AND CONFIG ***/

config.apiURL = config.apiURL + '/';
if (window.location.hostname != '192.168.111.165') {
	console.log = function(){};
}


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


/*** REACT MIXINS ***/

var EventFunnel = {
	// mixin providing event-handling sugar based on an opinionated way of handling React component parent-child communication
	getDefaultProps:function(){
		return { onFunnelEvent:function(){} };
	},
	toFunnel:function(event){
		this.props.onFunnelEvent(event, this.props, this.state);
	},
	funnelJQueryEvents:function(){var events=Array.prototype.slice.call(arguments,0);
		events.forEach(function(event)  {
			$(this.getDOMNode()).on(
				event, function(e)  { this.toFunnel(e); }.bind(this)
			);
		}.bind(this));
	}
}

var jQuerify = {
	jQuerify:function(ref){
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

var ClubSpeedApi = {
	getInitialState:function(){
		return {
			//race: { starts_at: '?', race_name: '?' }
		}
	},
	loadRaceById:function(raceId){
		$.get(
			config.apiURL + 'races/' + raceId + '.json?key=' + config.apiKey,
			function(body)  {
				this.setState({ race: body.race });
			}.bind(this)
		);
	}
};

var ParseRef = {
	parseRef:function(refName){	
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

var Select = React.createClass({displayName: 'Select',
	mixins: [EventFunnel, jQuerify],
	getDefaultProps:function(){
		return {
			list: [], // list: [{value: 1, label: 'Apples'}, 'Bananas', 'Cherries'],
			selectedId: null,
			selectedItem: null,
			bound: true
		};
	},
	getInitialState:function(){
		return { open: false };
	},
	render:function(){
		var optionsFromList = this.props.list.map(function(item)  {
			return React.DOM.option({key: item.value, value: item.value}, 
				item.label
			);
		});
		
		return React.DOM.select({name: "product"}, 
			React.DOM.option({key: -1}), 
			optionsFromList, 
			this.props.children
		);
	},
	componentDidMount:function(){
		this.jQuerify()
		.select2({
			placeholder: this.props.placeholder || (this.props.bound? '(none selected)': '(any)'),
			allowClear: true
		})
		.on('change', function(e)  { this.toFunnel(e); }.bind(this))
		.on('select2-open', function(_)  {
			this.setState({ open: true });
		}.bind(this))
		.on('select2-close', function(_)  {
			this.setState({ open: false });
		}.bind(this));
		
		this.setFromProps();
	},
	componentDidUpdate:function(prevProps){
		if (!this.state.open && prevProps.selectedId != this.props.selectedId){
			this.setFromProps();
		}
	},
	setFromProps:function(){
		if (!this.props.bound){
			return;
		}
	
		this.jQuerify().val(this.props.selectedId).trigger('change');
	},
	componentWillUnmount:function(){
		this.jQuerify().select2('destroy');
	}
});

var LinkedSelect = React.createClass({displayName: 'LinkedSelect',
	mixins: [EventFunnel],
	getDefaultProps:function(){
		return {
			url: '',
			list: [],
			listProperty: 'products',
			valueProperty: 'productId',
			labelProperty: 'description',
			bound: true
		};
	},
	getInitialState:function(){
		return {
			list: this.props.list
		}
	},
	renderOptions:function(){
		return this.state.list.map(function(item)  {
			return React.DOM.option({value: item[this.props.valueProperty], key: item[this.props.valueProperty]}, 
				item[this.props.labelProperty]
			);
		}.bind(this));
	},
	render:function(){
		var props = this.props;
		return React.DOM.div(null, Select({onFunnelEvent: this.toFunnel, selectedId: this.props.selectedId, bound: this.props.bound}, 
			this.renderOptions()
		));
	},
	componentWillMount:function(){
		if (this.props.url){
			$.get(this.props.url, function(body)  {
				this.setState({ list: _.sortBy(body[this.props.listProperty], this.props.labelProperty) });
			}.bind(this));
		}
	}
});

var TrackSelect = React.createClass({displayName: 'TrackSelect',
	mixins: [EventFunnel],
	render:function(){
		return LinkedSelect({url: config.apiURL + 'tracks/index.json?key=' + config.apiKey, 
			listProperty: "tracks", valueProperty: "id", labelProperty: "name", 
			selectedId: this.props.selectedId, onFunnelEvent: this.toFunnel, bound: this.props.bound});
	}
});

var ProductSelect = React.createClass({displayName: 'ProductSelect',
	mixins: [EventFunnel],
	render:function(){
		return LinkedSelect({
			url: config.apiURL + 'products.json?key=' + config.privateKey + '&select=productId,description', 
			listProperty: "products", valueProperty: "productId", labelProperty: "description", 
			selectedId: this.props.selectedId, onFunnelEvent: this.toFunnel});
	}
});

var iCheck = React.createClass({displayName: 'iCheck',
	mixins: [EventFunnel, jQuerify],
	render:function(){
		return React.DOM.input({defaultChecked: this.props.checked, type: "checkbox"});
	},
	componentDidMount:function(){
		this.jQuerify().iCheck({
    	checkboxClass: 'icheckbox_flat-blue',
    	radioClass: 'iradio_flat-blue'
		});		
		this.setFromProps();
		this.funnelJQueryEvents('ifIndeterminate', 'ifChecked', 'ifUnchecked');
	},
	componentDidUpdate:function(){
		this.setFromProps();
	},
	setFromProps:function(){
		if (this.props.checked == null){
			this.jQuerify().iCheck('indeterminate');
		} else if (this.props.checked) {
			this.jQuerify().iCheck('check');
		} else {
			this.jQuerify().iCheck('uncheck');
		}
	},
	componentWillUnmount:function(){
		this.jQuerify().iCheck('destroy');
	}
});

var iRadio = React.createClass({displayName: 'iRadio',
	mixins: [EventFunnel],
	render:function(){
		return React.DOM.label(null, 
			React.DOM.input({type: "radio", name: this.props.name, bound: this.props.bound, ref: "radio", 
				defaultChecked: this.props.checked}), 
			this.props.item.label
		);
	},
	componentDidMount:function(){
		this.funnelJQueryEvents('ifChanged');
	},
	componentDidUpdate:function(){
		this.refs.radio.getDOMNode().checked = this.props.checked;
	}
});

var iRadioGroup = React.createClass({displayName: 'iRadioGroup',
	mixins: [EventFunnel, jQuerify],
	getDefaultProps:function(){
		return {
			inline: false,
			name: 'test',
			list: [{ label: 'A', value: 1 }, { label: 'b', value: 2 }],
			defaultSelected: 1
		};
	},
	getInitialState:function(){
		return { selected: this.props.defaultSelected, name: generateUUID() };
	},
	render:function(){
		var listNodes = [];
		
		this.props.list.forEach(function(item, i)  {
			listNodes.push(
				iRadio({key: i, item: item, name: this.state.name, checked: this.state.selected == item.value, 
					onFunnelEvent: this.handleRadioChange})
			);
			if (!this.props.inline){
				listNodes.push(React.DOM.br(null));
			}
		}.bind(this));
		
		return React.DOM.span(null, 
			listNodes
		);
	},
	handleRadioChange:function(e, props, state){
		console.log('child radio event', e, props, state);
		this.setState(
			{ selected: props.item.value },
			function(_)  {return this.toFunnel(e);}.bind(this)
		);
	}
});

var DatePicker = React.createClass({displayName: 'DatePicker',
	mixins: [EventFunnel],
	render:function(){
		// TODO: update to latest React and use spread operators
		//return <input defaultValue={this.props.defaultValue} className={this.props.className} />;
		return this.transferPropsTo(React.DOM.input(null));
	},
	componentDidMount:function(){	
		$(this.getDOMNode()).datepicker();
		this.funnelJQueryEvents('change');
	}
});

var CheckPropChange = {
	checkPropChange:function(otherProps ){var keys=Array.prototype.slice.call(arguments,1);
		keys.forEach(function(key)  {
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
		}.bind(this));		
		return false;
	}
};

/*** CHILD COMPONENT(S) ***/

var BookingRow = React.createClass({displayName: 'BookingRow',
	mixins: [EventFunnel, CheckPropChange],
	getDefaultProps:function(){
		return {
			race: {
				race_name: 'Loading...',
				starts_at: 'Loading...'
			}
		};
	},
	getInitialState:function(){
		return {
			hovering: false
		};
	},
	render:function(){
		var booking = this.props.booking;
		var start = moment(this.props.race.starts_at);
		var style = {}; //{ cursor: 'pointer' };
		if (this.props.selected){
			style.backgroundColor = 'lightblue';
		}
	
		var editBoxStyle = { cursor: 'pointer' };
		return React.DOM.tr({style: style}, 
			React.DOM.td({onClick: this.toFunnel, style: { cursor: 'pointer'}}, 
				React.DOM.i({className: this.props.selected? "fa fa-pencil-square-o": "fa fa-square-o"})
			), 
			React.DOM.td(null, start.isValid()? start.format('h:mm a'): '?'), 
			React.DOM.td(null, this.props.race.race_name), 
			React.DOM.td(null, this.props.product && this.props.product.description), 
			React.DOM.td(null, booking.isPublic == null? null :
						booking.isPublic? React.DOM.i({className: "fa fa-globe", title: "This booking is public."}) :
										 React.DOM.i({className: "fa fa-lock", title: "This booking is private."})
			), 
			React.DOM.td(null, booking.quantityTotal)
		);
	}
	/*shouldComponentUpdate(nextProps,nextState){
		return true;
	}*/
});


/*** TOP LEVEL/ROOT/MAIN/PARENT COMPONENT ***/

BookingAdmin = React.createClass({displayName: 'BookingAdmin',
	mixins: [EventFunnel, ClubSpeedApi, ParseRef, jQuerify, React.addons.PureRenderMixin],
	
	timeoutHandle: null,
	
	getDefaultProps:function(){
		return { popupTime: 3000 };
	},
	
	getInitialState:function(){	
		return {
			bookings: [],
			tracks: [],
			filterByTrackId: false,
			products: {},
			selectedBookingIds: [],
			newProductId: null,
			newChecked: null,
			popupMessage: null,
			raceDetails: {},
			creating: false,
			loading: false
		};
	},
	
	isRaceInDate:function(race, start){
		if (!start){
			start = moment();
		} else {
			start = moment(start, 'M/DD/YYYY');
		}
		start.startOf('d');
		
		var end = moment(start);
		end.add(1, 'd');
		
		var raceStart = moment(race.starts_at, 'YYYY-MM-DD H:mm:ss.SSS');		
		return (raceStart.isAfter(start) || raceStart.isSame(start)) && raceStart.isBefore(end);
	},
	
	filterBookings:function(start){		
		var relatedBookings = _(this.state.raceDetails)
			.pick(function(race)  {
				return (!this.state.filterByTrackId || race.track_id == this.state.filterByTrackId)
				&& this.isRaceInDate(race, start);
			}.bind(this))
			.mapValues(function(race)  {
				var raceBookings = _.filter(this.state.bookings, function(booking)  {
					return booking.heatId == race.id;
				});
				
				if (raceBookings.length > 0){
					return raceBookings;
				} else {
					var newBookingPlaceholder = {
						heatId: race.id, onlineBookingsId: null, productsId: null, isPublic: null, quantityTotal: null
					};
					return newBookingPlaceholder;
				}
			}.bind(this))
			.values().flatten().value();
			
		return relatedBookings;
	},
	
	render:function(){
		return React.DOM.div({className: "container-fluid"}, 
			this.renderPopup(), 
			React.DOM.div({className: "row"}, 
				this.renderBookingNav(), 
				this.renderEditForm()
			)
		);
	},
	
	handleRadioChange:function(){var all=Array.prototype.slice.call(arguments,0);
		console.log(all);
	},
	
	renderPopup:function(){
		if (!this.state.popupMessage){
			return null;
		}
		
		return React.DOM.div({
			style: { position: 'fixed', top: 0, left: 0, width: '100%', cursor: 'pointer', zIndex: 9999, textAlign: 'center'}, 
			className: "alert alert-success", onClick: this.handlePopupClick}, 
			this.state.popupMessage
		);
	},
	
	renderTrackOptions:function(){	
		return this.state.tracks.map(function(track)  {
			return React.DOM.option({value: track.id, key: track.id}, track.name);
		});
	},
	
	renderProductSelectOptions:function(){
		return _.map(this.state.products, function(product)  {
			return React.DOM.option({value: product.productId, key: product.productId}, 
				product.description
			);
		});
	},
	
	renderBookingTable:function(){
		var foundBookings = this.filterBookings(this.parseRef('date'));
	
		if (foundBookings.length == 0){
			if (this.state.loading){
				return 'Getting bookings for selected date and/or track...'
			}
	
			return 'No activities found for this date and/or track.';
		}
		
		var bookingRows = _(foundBookings)
			.map(function(booking)  {
				return { booking:booking, race: this.state.raceDetails[booking.heatId] };
			}.bind(this))
			.sortBy(function(bookingAndRace)  { return moment(bookingAndRace.race.starts_at).unix(); })
			.map(function(bookingAndRace)  {
				booking = bookingAndRace.booking;
				return BookingRow({
					key: booking.onlineBookingsId || 'race.' + booking.heatId, 
					booking: booking, race: bookingAndRace.race, /*this.state.raceDetails[booking.heatId]*/product: this.state.products[booking.productsId], 
					selected: 
						_.contains(this.state.selectedBookingIds, booking.onlineBookingsId)
						|| _.find(this.state.selectedBookingIds, { heatId: booking.heatId }), 
					
					onFunnelEvent: this.handleBookingRowEvent}
				);
			}.bind(this))
			.value();
		
		return React.DOM.div(null, 
			React.DOM.div({style: { overflowY: 'auto'}, ref: "table"}, 
				React.DOM.table({className: "table"}, 
					React.DOM.thead({className: "text-left"}, React.DOM.tr(null, 
						React.DOM.th({key: "editing"}, 
							"Editing"
						), 
						React.DOM.th({key: "time"}, "Time"), 
						React.DOM.th({key: "name"}, "Name"), 
						React.DOM.th({key: "product"}, "Product"), 
						React.DOM.th({key: "public"}, "Public?"), 
						React.DOM.th({key: "qty"}, "Qty")
					)), 
					React.DOM.tbody(null, 
						bookingRows
					)
				)
			), 
			this.state.selectedBookingIds.length > 0 &&
				React.DOM.span(null, 
					React.DOM.input({type: "button", className: "btn btn-info", onClick: this.handleDeselectClick, value: "De-select All"})
					/*<input type='button' className='btn btn-danger' onClick={this.handleDeleteClick} value='Delete Selected' />*/
				)
		);
	},
	
	renderBookingNav:function(){
		return React.DOM.div({className: "col-md-6"}, 
			React.DOM.div({className: "row"}, 
				React.DOM.h3(null, "All Activities")
			), 
			
			React.DOM.div({className: "row form-inline"}, 
				React.DOM.div({className: "form-group col-md-6"}, 
					React.DOM.label({className: 'control-label' || 'control-label col-md-1' || "col-sm-1 col-md-1 control-label"}, 
						"Date"
					), 
					DatePicker({ref: "date", className: "form-control", 
						defaultValue: moment().format('MM/DD/YYYY'), onFunnelEvent: this.handleDateChange})
				), 
				
				React.DOM.div({className: 'form-group col-md-6' || 'form-group col-md-7' || "col-sm-7 col-md-8"}, 
					React.DOM.label({className: 'control-label' || "col-sm-1 col-md-1"}, 
						"Track"
					), 
					TrackSelect({className: "form-control", onFunnelEvent: this.handleTrackSelectEvent, bound: false})
				)
			), 				
			
			React.DOM.br(null), 
			
			React.DOM.div({className: "row form-group"}, 
				this.renderBookingTable()
			)
		);		
	},
	
	renderEditForm:function(creating){
		if (this.filterBookings(this.parseRef('date')).length == 0){
			return null;
		}
		
		if (this.state.selectedBookingIds.length == 0){
			if (this.state.loading){
				return null;
			} else {
				return React.DOM.div({className: "col-md-6"}, 
					"Select one or more activities to edit them."
				);
			}
		}
	
		var title = this.state.selectedBookingIds.length > 0 && 
			React.DOM.span(null, "Editing ", this.getBookingTitle(true));
	
		return React.DOM.div({className: "col-md-6"}, 
			React.DOM.form({className: "form-horizontal"}, 
					React.DOM.legend({className: "row form-group"}, 
						title
					), 
				
				React.DOM.div({className: "row form-group"}, 
					React.DOM.label({className: "col-sm-4 col-md-4 col-lg-3 control-label"}, 
						"Product Required"
					), 
					React.DOM.div({className: "col-sm-6 col-md-6 col-lg-7"}, 
						/*<ProductSelect onFunnelEvent={this.handleProductSelectEvent} selectedId={this.state.newProductId} />*/
						Select({onFunnelEvent: this.handleProductSelectEvent, selectedId: this.state.newProductId}, 
							this.renderProductSelectOptions()
						)
					)
				), 
				
				React.DOM.div({className: "row form-group"}, 
					React.DOM.label({className: "col-sm-4 col-md-4 col-lg-3 control-label"}, 
						"Show To Public?"
					), 
					React.DOM.div({className: "col-sm-6 col-md-6 col-lg-7"}, 
						iCheck({ref: "publicCheck", onFunnelEvent: this.handlePublicCheckEvent, checked: this.state.newChecked}	)					
					)
				), 
				
				React.DOM.div({className: "row form-group"}, 
					React.DOM.label({className: "col-sm-4 col-md-4 col-lg-3 control-label"}, 
						"Qty. Available Online"
					), 
					React.DOM.div({className: "col-sm-6 col-md-6 col-lg-7"}, 
						React.DOM.input({className: "form-control", type: "number", min: "0", onBlur: this.handleQtyAvailableChange, ref: "qtyAvail"})
					)
				), 
				
				React.DOM.div({className: "row form-group"}, 
					React.DOM.div({className: "col-sm-4 col-md-4 col-lg-3"}), 
					React.DOM.div({className: "col-sm-6 col-md-6 col-lg-7"}, 
						React.DOM.input({className: "btn btn-info", type: "submit", value: "Save Changes", 
							disabled: false && !this.state.saveEnabled, title: this.state.saveEnabled? null: 'Cannot save. No changes were made yet.', 
							onClick: this.handleSaveClick})
					)
				)
			)
		);
	},
	
	getBookingById:function(id){
		return _.findWhere(this.state.bookings, { onlineBookingsId: id });
	},
	
	getBookingTitle: function(asHTML){
		var bookingTitles = [];
		
		this.state.selectedBookingIds.forEach(function(item)  {
			if (item.heatId){
				var raceName = this.state.raceDetails[item.heatId].race_name;
				if (asHTML){
					bookingTitles.push(React.DOM.span(null, raceName, React.DOM.br(null)));
				} else {
					bookingTitles.push(raceName);
				}
			} else {
				var booking = this.getBookingById(item);
				
				var raceOfBooking = this.state.raceDetails[booking.heatId];
				var bookingText = moment(raceOfBooking.starts_at).format('M/D h:mm a ') + raceOfBooking.race_name;
				
				if (asHTML){
					bookingTitles.push(React.DOM.span(null, bookingText, React.DOM.br(null)));
				} else {
					bookingTitles.push(bookingText);
				}
			}			
		}.bind(this));
		
		return asHTML? bookingTitles: bookingTitles.join(', ');
	},
	
	componentDidMount:function(){	
		// load tracks for Track Select
		$.get(
			config.apiURL + 'tracks/index.json?key=' + config.apiKey,
			function(body)  {
				this.setState({ tracks: body.tracks });
			}.bind(this)
		);
		
		// load products
		var params = { key: config.privateKey, select: 'productId,description' };
		$.get(
			config.apiURL + 'products.json?' + $.param(params),
			function(body)  {
				this.setState({ products: _.indexBy(body.products, 'productId') });
			}.bind(this)
		);
		
		this.loadBookings();
		
		$(window).resize(function(_)  {
			console.log('resize');
			if (this.refs.table){
				var tableElem = $(this.refs.table.getDOMNode());
					tableElem.height($(window).height() - tableElem.offset().top - 40);
			}
		}.bind(this));
	},
	
	upsertListInState:function(listName, item, match){
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
		console.log('new state', newState);
		this.setState(newState);
	},
	
	loadBookings:function(filterByTrackId){	
		// workaround until main branch booking filtering gets fixed
		$.get(
			config.apiURL + 'booking.json?key=' + config.privateKey,
			function(body)  {
				var bookings = _(body.bookings)
				.concat(this.state.bookings)
				.uniq('onlineBookingsId')
				.value();
				
				this.setState({ bookings:bookings });
			}.bind(this)
		);
		// end workaround
	
		this.setState({ selectedBookingIds: [], loading: true });
	
		var inputDate = this.parseRef('date');
		var start = moment(inputDate);
		var end = moment(inputDate);
		end.add(1, 'd');
		
		var params = {
			key: config.apiKey
		};
		if (start.isValid() && end.isValid()){
			params.start = start.format('YYYY-MM-DD');
			params.end = end.format('YYYY-MM-DD');
		}
		if (filterByTrackId){
			params.track_id = filterByTrackId;
		}
		
		var requestUrl = config.apiURL + 'races/races.json?' + $.param(params);

		$.get(requestUrl)
		.then(function(body)  {
			// KEEP: WILL BE RE-IMPLEMENTED ONCE MAIN API BOOKING FILTERING IS FIXED
			/*var bookingRequests = body.races.map(race => {
				var requestUrl = config.apiURL + 'booking.json?' + $.param({ key: config.privateKey, heatId: race.HeatNo });			
				return $.get(requestUrl);
			});
			
			bookingRequests.forEach(req => {
				req.then(body => {
					body.bookings.forEach(booking => {
						this.upsertListInState('bookings', booking, { onlineBookingsId: booking.onlineBookingsId });			
					});
				});
			});*/
						
			var raceDetailRequests = body.races.map(function(race)  {
				var requestUrl = config.apiURL + 'races/' + race.HeatNo + '.json?key=' + config.privateKey;
				return $.get(requestUrl);
			});
			
			raceDetailRequests.forEach(function(req)  {
				req.then(function(body)  {
					console.log('race detail', body);
					var change = {};
					change[body.race.id] = { $set: body.race };
					var raceDetails = React.addons.update(
						this.state.raceDetails,
						change
					);
					console.log(raceDetails);
					this.setState({ raceDetails:raceDetails });
				}.bind(this));
			}.bind(this));
			
			$.when.apply($, raceDetailRequests).done(function(_)  {
				this.setState({ loading: false });
			}.bind(this));			
		}.bind(this));
	},
	
	componentDidUpdate:function(prevProps, prevState){
		if (prevState.selectedBookingIds.length <= 1 && this.state.selectedBookingIds.length > 1){
			this.setState({ newProductId: null, newChecked: null });
			this.refs.qtyAvail.getDOMNode().value = '';
		} else if (prevState.selectedBookingIds.length != 1 && this.state.selectedBookingIds.length == 1){
			var firstBooking = _.findWhere(this.state.bookings, { onlineBookingsId: this.state.selectedBookingIds[0] });
			if (typeof firstBooking == 'undefined'){
				this.setState({
					newProductId: null,
					newChecked: true
				});			
				this.refs.qtyAvail.getDOMNode().value = 1;			
			} else {
				this.setState({
					newProductId: firstBooking.productsId,
					newChecked: firstBooking.isPublic
				});			
				this.refs.qtyAvail.getDOMNode().value = firstBooking.quantityTotal || 0;
			}
		}
		
		if (prevState.popupMessage != this.state.popupMessage){
			clearTimeout(this.timeoutHandle);
			this.timeoutHandle = setTimeout(function(_)  { this.setState({ popupMessage: null }); }.bind(this), this.props.popupTime);
		}
		
		/*if (this.state.selectedBookingIds.length == 0 && this.state.saveEnabled){
			this.setState({ saveEnabled: false });
		}*/
		$(window).resize();
	},
	
	handleDateChange:function(e){
		this.loadBookings();
	},
	
	handleTrackSelectEvent: function(event, props){
		if (event.added){
			this.setState({ filterByTrackId: event.val || false });
			this.loadBookings(event.val);
		}
	},
	
	handleBookingRowEvent:function(e, rowProps){
		switch (e.type) {
			case 'click':
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
						console.log('win');
						selectedBookingIds = _.reject(this.state.selectedBookingIds, { heatId: rowProps.booking.heatId });
					}
				}
				this.setState({ selectedBookingIds:selectedBookingIds });
				break;
		}		
	},
	
	handlePublicCheckEvent:function(e){		
		switch (e.type){
			case 'ifChecked':
				this.setState({ newChecked: true, saveEnabled: true });
				break;
			case 'ifUnchecked':
				this.setState({ newChecked: false, saveEnabled: true });			
				break;
		}
	},
	
	handleProductSelectEvent: function(e, props, state){
		//this.setState({ saveEnabled: true });
		if (e.added){
			this.setState({ newProductId: e.val });
		}
	},
	
	handleQtyAvailableChange:function(){
		var elem = this.jQuerify('qtyAvail');
		
		elem.val(parseInt(elem.val()));
		
		if (elem.val() < elem.attr('min')){
			elem.val(elem.attr('min'));
		}		
	},
	
	handleSaveClick:function(e){
		e.preventDefault();
		
		var editCount = this.state.selectedBookingIds.length;
		if (editCount > 1){
			var confirmed = confirm('Save changes for ' + editCount + ' bookings?');
			if (!confirmed){
				return;
			}
		}
		
		/*var change = {
			isPublic: this.parseRef('publicCheck'),
			quantityTotal: this.parseRef('qtyAvail'),
			productsId: this.state.newProductId
		};*/
		var change = {};
		if (this.state.newChecked != null){
			change.isPublic = this.state.newChecked;
		}
		if(this.state.newProductId != null){
			change.productsId = this.state.newProductId;
		}
		if(this.refs.qtyAvail.getDOMNode().value != ''){
			change.quantityTotal = this.parseRef('qtyAvail');
		}
		
		this.putChange(change);
	},
	
	putChange:function(change){
		if (this.state.selectedBookingIds.length == 0){
			return;
		}
		
		var putRequests = this.state.selectedBookingIds.forEach(function(id)  {
			if (id.heatId){
				return $.ajax({
					url: config.apiURL + 'booking?key=' + config.privateKey,
					type: 'POST',
					data: _.extend(change, {
						heatId: id.heatId,
						quantityTotal: change.quantityTotal || 1
					})
				});
			} else {
				return $.ajax({
					url: config.apiURL + 'booking/' + id + '?key=' + config.privateKey,
					type: 'PUT',
					data: change				
				});
			}
		});
		
		$.when.apply($, putRequests).then(
			function()  {var all=Array.prototype.slice.call(arguments,0);
				//console.log('success', all);
				var popupMessage = 'Booking for ' + this.getBookingTitle() + ' successfully saved! ('
					+ moment().format('h:mm:ss a') + ')';
				this.setState({ popupMessage:popupMessage });
				this.loadBookings();
			}.bind(this),
			function()  {var all=Array.prototype.slice.call(arguments,0);
				//console.log('fail', all);
				var errorMessage = 'An error occurred while trying to save changes.';
				alert(errorMessage);
			}
		);
	},
	
	handlePopupClick:function(){
		this.setState({ popupMessage: null });
	},
	
	handleDeselectClick:function(){
		this.setState({ selectedBookingIds: [] });
	},
	
	handleDeleteClick:function(){
		var confirmed = confirm('You are about to delete ');
		// TODO: finish
	}
});


/*** RENDER IT! ***/

React.render(BookingAdmin(null), document.getElementById('main'));