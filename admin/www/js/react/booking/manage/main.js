/** @jsx React.DOM */


/*** CONSTANTS AND CONFIG ***/

config.apiURL = config.apiURL + '/';

/*var global = {
	momentFormat: config.dateFormat.replace('Y', 'YYYY').replace('m', 'M').replace('d', 'D'),
	datepickerFormat: config.dateFormat.replace('Y', 'yy')
};*/


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

function getLocaleFormat(){
	var formats = {
		 "ar-SA" : "DD/MM/YY",
		 "bg-BG" : "DD.M.YYYY",
		 "ca-ES" : "DD/MM/YYYY",
		 "zh-TW" : "YYYY/M/D",
		 "cs-CZ" : "D.M.YYYY",
		 "Da-DK" : "DD-MM-YYYY",
		 "De-DE" : "DD.MM.YYYY",
		 "el-GR" : "D/M/YYYY",
		 "en-US" : "M/D/YYYY",
		 "fi-FI" : "D.M.YYYY",
		 "fr-FR" : "DD/MM/YYYY",
		 "he-IL" : "DD/MM/YYYY",
		 "hu-HU" : "YYYY. MM. DD.",
		 "is-IS" : "D.M.YYYY",
		 "it-IT" : "DD/MM/YYYY",
		 "ja-JP" : "YYYY/MM/DD",
		 "ko-KR" : "YYYY-MM-DD",
		 "nl-NL" : "D-M-YYYY",
		 "nb-NO" : "DD.MM.YYYY",
		 "pl-PL" : "YYYY-MM-DD",
		 "pt-BR" : "D/M/YYYY",
		 "ro-RO" : "DD.MM.YYYY",
		 "ru-RU" : "DD.MM.YYYY",
		 "hr-HR" : "D.M.YYYY",
		 "sk-SK" : "D. M. YYYY",
		 "sq-AL" : "YYYY-MM-DD",
		 "sv-SE" : "YYYY-MM-DD",
		 "th-TH" : "D/M/YYYY",
		 "tr-TR" : "DD.MM.YYYY",
		 "ur-PK" : "DD/MM/YYYY",
		 "iD-ID" : "DD/MM/YYYY",
		 "uk-UA" : "DD.MM.YYYY",
		 "be-BY" : "DD.MM.YYYY",
		 "sl-SI" : "D.M.YYYY",
		 "et-EE" : "D.MM.YYYY",
		 "lv-LV" : "YYYY.MM.DD.",
		 "lt-LT" : "YYYY.MM.DD",
		 "fa-IR" : "MM/DD/YYYY",
		 "vi-VN" : "DD/MM/YYYY",
		 "hY-AM" : "DD.MM.YYYY",
		 "az-Latn-AZ" : "DD.MM.YYYY",
		 "eu-ES" : "YYYY/MM/DD",
		 "mk-MK" : "DD.MM.YYYY",
		 "af-ZA" : "YYYY/MM/DD",
		 "ka-GE" : "DD.MM.YYYY",
		 "fo-FO" : "DD-MM-YYYY",
		 "hi-IN" : "DD-MM-YYYY",
		 "ms-MY" : "DD/MM/YYYY",
		 "kk-KZ" : "DD.MM.YYYY",
		 "kY-KG" : "DD.MM.YY",
		 "sw-KE" : "M/D/YYYY",
		 "uz-Latn-UZ" : "DD/MM YYYY",
		 "tt-RU" : "DD.MM.YYYY",
		 "pa-IN" : "DD-MM-YY",
		 "gu-IN" : "DD-MM-YY",
		 "ta-IN" : "DD-MM-YYYY",
		 "te-IN" : "DD-MM-YY",
		 "kn-IN" : "DD-MM-YY",
		 "mr-IN" : "DD-MM-YYYY",
		 "sa-IN" : "DD-MM-YYYY",
		 "mn-MN" : "YY.MM.DD",
		 "gl-ES" : "DD/MM/YY",
		 "kok-IN" : "DD-MM-YYYY",
		 "sYr-SY" : "DD/MM/YYYY",
		 "Dv-MV" : "DD/MM/YY",
		 "ar-IQ" : "DD/MM/YYYY",
		 "zh-CN" : "YYYY/M/D",
		 "De-CH" : "DD.MM.YYYY",
		 "en-GB" : "DD/MM/YYYY",
		 "es-MX" : "DD/MM/YYYY",
		 "fr-BE" : "D/MM/YYYY",
		 "it-CH" : "DD.MM.YYYY",
		 "nl-BE" : "D/MM/YYYY",
		 "nn-NO" : "DD.MM.YYYY",
		 "pt-PT" : "DD-MM-YYYY",
		 "sr-Latn-CS" : "D.M.YYYY",
		 "sv-FI" : "D.M.YYYY",
		 "az-CYrl-AZ" : "DD.MM.YYYY",
		 "ms-BN" : "DD/MM/YYYY",
		 "uz-CYrl-UZ" : "DD.MM.YYYY",
		 "ar-EG" : "DD/MM/YYYY",
		 "zh-HK" : "D/M/YYYY",
		 "De-AT" : "DD.MM.YYYY",
		 "en-AU" : "D/MM/YYYY",
		 "es-ES" : "DD/MM/YYYY",
		 "fr-CA" : "YYYY-MM-DD",
		 "sr-CYrl-CS" : "D.M.YYYY",
		 "ar-LY" : "DD/MM/YYYY",
		 "zh-SG" : "D/M/YYYY",
		 "De-LU" : "DD.MM.YYYY",
		 "en-CA" : "DD/MM/YYYY",
		 "es-GT" : "DD/MM/YYYY",
		 "fr-CH" : "DD.MM.YYYY",
		 "ar-DZ" : "DD-MM-YYYY",
		 "zh-MO" : "D/M/YYYY",
		 "De-LI" : "DD.MM.YYYY",
		 "en-NZ" : "D/MM/YYYY",
		 "es-CR" : "DD/MM/YYYY",
		 "fr-LU" : "DD/MM/YYYY",
		 "ar-MA" : "DD-MM-YYYY",
		 "en-IE" : "DD/MM/YYYY",
		 "es-PA" : "MM/DD/YYYY",
		 "fr-MC" : "DD/MM/YYYY",
		 "ar-TN" : "DD-MM-YYYY",
		 "en-ZA" : "YYYY/MM/DD",
		 "es-DO" : "DD/MM/YYYY",
		 "ar-OM" : "DD/MM/YYYY",
		 "en-JM" : "DD/MM/YYYY",
		 "es-VE" : "DD/MM/YYYY",
		 "ar-YE" : "DD/MM/YYYY",
		 "en-029" : "MM/DD/YYYY",
		 "es-CO" : "DD/MM/YYYY",
		 "ar-SY" : "DD/MM/YYYY",
		 "en-BZ" : "DD/MM/YYYY",
		 "es-PE" : "DD/MM/YYYY",
		 "ar-JO" : "DD/MM/YYYY",
		 "en-TT" : "DD/MM/YYYY",
		 "es-AR" : "DD/MM/YYYY",
		 "ar-LB" : "DD/MM/YYYY",
		 "en-ZW" : "M/D/YYYY",
		 "es-EC" : "DD/MM/YYYY",
		 "ar-KW" : "DD/MM/YYYY",
		 "en-PH" : "M/D/YYYY",
		 "es-CL" : "DD-MM-YYYY",
		 "ar-AE" : "DD/MM/YYYY",
		 "es-UY" : "DD/MM/YYYY",
		 "ar-BH" : "DD/MM/YYYY",
		 "es-PY" : "DD/MM/YYYY",
		 "ar-QA" : "DD/MM/YYYY",
		 "es-BO" : "DD/MM/YYYY",
		 "es-SV" : "DD/MM/YYYY",
		 "es-HN" : "DD/MM/YYYY",
		 "es-NI" : "DD/MM/YYYY",
		 "es-PR" : "DD/MM/YYYY",
		 "am-ET" : "D/M/YYYY",
		 "tzm-Latn-DZ" : "DD-MM-YYYY",
		 "iu-Latn-CA" : "D/MM/YYYY",
		 "sma-NO" : "DD.MM.YYYY",
		 "mn-Mong-CN" : "YYYY/M/D",
		 "gD-GB" : "DD/MM/YYYY",
		 "en-MY" : "D/M/YYYY",
		 "prs-AF" : "DD/MM/YY",
		 "bn-BD" : "DD-MM-YY",
		 "wo-SN" : "DD/MM/YYYY",
		 "rw-RW" : "M/D/YYYY",
		 "qut-GT" : "DD/MM/YYYY",
		 "sah-RU" : "MM.DD.YYYY",
		 "gsw-FR" : "DD/MM/YYYY",
		 "co-FR" : "DD/MM/YYYY",
		 "oc-FR" : "DD/MM/YYYY",
		 "mi-NZ" : "DD/MM/YYYY",
		 "ga-IE" : "DD/MM/YYYY",
		 "se-SE" : "YYYY-MM-DD",
		 "br-FR" : "DD/MM/YYYY",
		 "smn-FI" : "D.M.YYYY",
		 "moh-CA" : "M/D/YYYY",
		 "arn-CL" : "DD-MM-YYYY",
		 "ii-CN" : "YYYY/M/D",
		 "Dsb-DE" : "D. M. YYYY",
		 "ig-NG" : "D/M/YYYY",
		 "kl-GL" : "DD-MM-YYYY",
		 "lb-LU" : "DD/MM/YYYY",
		 "ba-RU" : "DD.MM.YY",
		 "nso-ZA" : "YYYY/MM/DD",
		 "quz-BO" : "DD/MM/YYYY",
		 "Yo-NG" : "D/M/YYYY",
		 "ha-Latn-NG" : "D/M/YYYY",
		 "fil-PH" : "M/D/YYYY",
		 "ps-AF" : "DD/MM/YY",
		 "fY-NL" : "D-M-YYYY",
		 "ne-NP" : "M/D/YYYY",
		 "se-NO" : "DD.MM.YYYY",
		 "iu-Cans-CA" : "D/M/YYYY",
		 "sr-Latn-RS" : "D.M.YYYY",
		 "si-LK" : "YYYY-MM-DD",
		 "sr-CYrl-RS" : "D.M.YYYY",
		 "lo-LA" : "DD/MM/YYYY",
		 "km-KH" : "YYYY-MM-DD",
		 "cY-GB" : "DD/MM/YYYY",
		 "bo-CN" : "YYYY/M/D",
		 "sms-FI" : "D.M.YYYY",
		 "as-IN" : "DD-MM-YYYY",
		 "ml-IN" : "DD-MM-YY",
		 "en-IN" : "DD-MM-YYYY",
		 "or-IN" : "DD-MM-YY",
		 "bn-IN" : "DD-MM-YY",
		 "tk-TM" : "DD.MM.YY",
		 "bs-Latn-BA" : "D.M.YYYY",
		 "mt-MT" : "DD/MM/YYYY",
		 "sr-CYrl-ME" : "D.M.YYYY",
		 "se-FI" : "D.M.YYYY",
		 "zu-ZA" : "YYYY/MM/DD",
		 "xh-ZA" : "YYYY/MM/DD",
		 "tn-ZA" : "YYYY/MM/DD",
		 "hsb-DE" : "D. M. YYYY",
		 "bs-CYrl-BA" : "D.M.YYYY",
		 "tg-CYrl-TJ" : "DD.MM.YY",
		 "sr-Latn-BA" : "D.M.YYYY",
		 "smj-NO" : "DD.MM.YYYY",
		 "rm-CH" : "DD/MM/YYYY",
		 "smj-SE" : "YYYY-MM-DD",
		 "quz-EC" : "DD/MM/YYYY",
		 "quz-PE" : "DD/MM/YYYY",
		 "hr-BA" : "D.M.YYYY.",
		 "sr-Latn-ME" : "D.M.YYYY",
		 "sma-SE" : "YYYY-MM-DD",
		 "en-SG" : "D/M/YYYY",
		 "ug-CN" : "YYYY-M-D",
		 "sr-CYrl-BA" : "D.M.YYYY",
		 "es-US" : "M/D/YYYY"
	};

  return formats[navigator.language] || 'DD-MM-YYYY';
} 


/*** REACT MIXINS ***/

var EventFunnel = {
	// mixin providing event-handling sugar based on an opinionated way of handling React component parent-child communication
	getDefaultProps:function(){
		return { onFunnelEvent:function(){} };
	},
	toFunnel:function(event ){var other=Array.prototype.slice.call(arguments,1);	
		this.props.onFunnelEvent.apply(this, [event, this.props, this.state].concat(other));
		//this.props.onFunnelEvent(event, this.props, this.state);
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
	mixins: [EventFunnel],
	getDefaultProps:function(){
		return {
			list: [], // list: [{value: 1, label: 'Apples'}, 'Bananas', 'Cherries'],
			selectedId: null,
			placeholder: '(any)',
			allowClear: true
		};
	},
	getInitialState:function(){
		return { open: false };
	},
	render:function(){
		var optionsFromList = this.props.list.map(function(item) 
			{return React.createElement("option", {key: item.value, value: item.value}, 
				item.label
			);}
		);
		
		return React.createElement("select", null, 
			React.createElement("option", {key: -1, value: null}), 
			optionsFromList, 
			this.props.children
		);
	},
	getSelectOptions:function(){
		var $__0=      this.props,list=$__0.list,selectedId=$__0.selectedId,options=(function(source, exclusion) {var rest = {};var hasOwn = Object.prototype.hasOwnProperty;if (source == null) {throw new TypeError();}for (var key in source) {if (hasOwn.call(source, key) && !hasOwn.call(exclusion, key)) {rest[key] = source[key];}}return rest;})($__0,{list:1,selectedId:1});  // omit list and selectedId
		return options;
	},
	componentDidMount:function(){
		var data = this.props.list.map(function(item)  
			{return { id: item.value, text: item.label };}
		);
		
		$(this.getDOMNode())
		.select2(this.getSelectOptions())
		.on('change', this.toFunnel)
		.on('select2-open', function(_)  	{ this.setState({ open: true }) }.bind(this))
		.on('select2-close', function(_)  {	this.setState({ open: false }); }.bind(this));
		
		this.setFromProps();
	},
	componentDidUpdate:function(prevProps){
		if (prevProps.list.length != this.props.list.length){ // TODO: make list equality checking more robust?
			$(this.getDOMNode()).select2(this.getSelectOptions());
		}
		if (!this.state.open && prevProps.selectedId != this.props.selectedId){
			this.setFromProps();
		}
	},
	setFromProps:function(){
		$(this.getDOMNode()).val(this.props.selectedId).trigger('change');
	},
	componentWillUnmount:function(){
		$(this.getDOMNode()).select2('destroy');
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
			labelProperty: 'description'
		};
	},
	getInitialState:function(){
		return {
			list: this.props.list
		}
	},
	render:function(){
		var props = this.props;
		// must be wrapped in div to avoid wonky formatting, with BS3 at least
		return React.createElement("div", null, 
			React.createElement(Select, {onFunnelEvent: this.toFunnel, selectedId: props.selectedId, placeholder: props.placeholder, 
				list: _.map(this.state.list, function(item)  {return { value: item[props.valueProperty], label: item[props.labelProperty]};})})
		);
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
		return React.createElement(LinkedSelect, {url: config.apiURL + 'tracks/index.json?key=' + config.apiKey, 
			listProperty: "tracks", valueProperty: "id", labelProperty: "name", 
			selectedId: this.props.selectedId, onFunnelEvent: this.toFunnel});
	}
});

var ProductSelect = React.createClass({displayName: 'ProductSelect',
	mixins: [EventFunnel],
	render:function(){
		return React.createElement(LinkedSelect, {
			url: config.apiURL + 'products.json?key=' + config.privateKey + '&select=productId,description', 
			listProperty: "products", valueProperty: "productId", labelProperty: "description", placeholder: " ", 
			selectedId: this.props.selectedId, onFunnelEvent: this.toFunnel});
	}
});

var ICheck = React.createClass({displayName: 'ICheck',
	mixins: [EventFunnel],
	render:function(){
		return React.createElement("input", {defaultChecked: this.props.checked, type: "checkbox"});
	},
	componentDidMount:function(){
		$(this.getDOMNode()).iCheck({
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
			$(this.getDOMNode()).iCheck('indeterminate');
		} else if (this.props.checked) {
			$(this.getDOMNode()).iCheck('check');
		} else {
			$(this.getDOMNode()).iCheck('uncheck');
		}
	},
	componentWillUnmount:function(){
		$(this.getDOMNode()).iCheck('destroy');
	}
});

var IRadio = React.createClass({displayName: 'IRadio',
	mixins: [EventFunnel],
	render:function(){
		return React.createElement("input", {type: "radio"});
	},
	componentDidMount:function(){
		$(this.getDOMNode()).iCheck({
    	checkboxClass: 'icheckbox_flat-blue',
    	radioClass: 'iradio_flat-blue'
		})
		.on('ifClicked', this.toFunnel);
		this.setFromProps();
		//this.funnelJQueryEvents('ifChecked','ifUnchecked');
	},
	componentDidUpdate:function(){
		this.setFromProps();
	},
	setFromProps:function(){
		if (this.props.selected) {
			$(this.getDOMNode()).iCheck('check');
		} else {
			$(this.getDOMNode()).iCheck('uncheck');
		}
	}
});

var IRadioGroup = React.createClass({displayName: 'IRadioGroup',
	mixins: [EventFunnel],
	getDefaultProps:function(){
		return {
			inline: true,
			list: [{ label: 'A', value: 1 }, { label: 'b', value: 2 }],
			selected: null
		};
	},
	getInitialState:function(){
		// note: name is set by generateUUID here and should never be changed after,
		// but it is in initial state instead of default props so that a new UUID is generated for each IRadioGroup instance
		return { selected: this.props.selected, name: generateUUID() };
	},
	render:function(){
		var listNodes = [];
		
		this.props.list.forEach(function(item, i)  {
			listNodes.push(React.createElement("div", null, 
				React.createElement(IRadio, {item: item, name: this.props.name || this.state.name, selected: this.props.selected === item.value, 
					onFunnelEvent: this.handleRadioChange}), 
				React.createElement("label", {key: i, style: { position: 'relative', top: -5, left: 10}}, 
					item.label
				)
			));
		}.bind(this));
		
		return React.createElement("span", null, 
			listNodes
		);
	},
	handleRadioChange:function(e, optionProps, state){
		this.toFunnel(e, optionProps);
	}
});

var DatePicker = React.createClass({displayName: 'DatePicker',
	mixins: [EventFunnel],
	getDefaultProps:function(){
		return {
			date: moment()
		};
	},
	getInitialState:function(){
		return { date: this.props.date };
	},
	render:function(){	
		return React.createElement("div", {className: "row input-group", style: { padding: 0}}, 
			React.createElement("span", {className: 'input-group-btn'}, 
				React.createElement("button", {className: 'btn btn-default', type: "button", ref: "previousDay", onClick: this.handlePreviousClick}, '<')
			), 
			React.createElement("input", {className: "form-control", ref: "picker", onClick: this.handleClick}), 
			React.createElement("span", {className: 'input-group-btn'}, 
				React.createElement("button", {className: 'btn btn-default', type: "button", ref: "nextDay", onClick: this.handleNextClick}, '>')
			)
		);
	},
	getFormat:function(){
		var momentFormat = getLocaleFormat();
		return momentFormat.replace('YYYY', 'yy').replace('YY', 'y')
			.replace('MM','mm').replace('M','m').replace('DD','dd').replace('D','d');
	},
	componentDidMount:function(){
		$(this.refs.picker.getDOMNode())
		.datepicker({
			constrainInput: false,
			dateFormat: this.getFormat(),
			showButtonPanel: true
		})
		.datepicker('setDate', '+0')
		.datepicker('option', 'onSelect',
			function(e)  {
				this.props.onFunnelEvent(event, this.props, { date: moment(this.refs.picker.getDOMNode().value, getLocaleFormat()) });
				$(this.refs.picker.getDOMNode()).datepicker('hide');
			}.bind(this)
		);
	},
	handleClick:function(){
		$(this.refs.picker.getDOMNode()).datepicker('show');
	},
	componentWillReceiveProps:function(nextProps){
		$(this.refs.picker.getDOMNode()).datepicker('setDate', nextProps.date.format(getLocaleFormat()));
	},
	handlePreviousClick:function(){
		this.iterateDate(-1);
	},
	handleNextClick:function(){
		this.iterateDate(+1);
	},
	iterateDate:function(increment){
		var newDate = moment(this.props.date, getLocaleFormat());
		newDate.add(increment, 'd');
		this.props.onFunnelEvent(null, null, { date: newDate });
	}
});


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
		return React.createElement("tr", {style: style}, 
			React.createElement("td", {onClick: this.toFunnel}, 
				React.createElement(ICheck, {checked: this.props.selected, onFunnelEvent: this.toFunnel})
			), 
			React.createElement("td", null, start.isValid()? start.format('h:mm a'): '?'), 
			React.createElement("td", null, this.props.race.race_name), 
			this.props.product? React.createElement("td", null, this.props.product.description)
				:	React.createElement("td", {style: { color: 'gray'}}, "(no booking)"), 
			React.createElement("td", null, booking.isPublic == null? null :
						booking.isPublic? React.createElement("i", {className: "fa fa-globe", title: "This booking is public."}) :
										 React.createElement("i", {className: "fa fa-lock", title: "This booking is private."})
			), 
			React.createElement("td", null, booking.quantityTotal)
		);
	}
});


var FadeOut = React.createClass({displayName: 'FadeOut',
	timeout: null,
	getDefaultProps:function (){
		return { rate: 0.01, delay: 3000, onFadeComplete:function (){}, fading: false };
	},
	getInitialState:function (){
		return { fading: false, opacity: 1 };
	},
	render:function (){
		var change = this.props.style?
			{ $merge: { opacity: this.state.opacity } }
			: { $set: { opacity: this.state.opacity } };
	
		var newProps = React.addons.update(this.props, { style: change });
		return React.createElement(this.props.element || 'div', newProps, this.props.children);
	},
	componentDidMount:function (){
		this.resetTimeout();
	},
	resetTimeout:function (){
		if (this.timeout){
			clearTimeout(this.timeout);
		}
		
		this.timeout = setTimeout(
			function(_)  {
				if (this.isMounted()){
					this.setState({ fading: true });
				}
			}.bind(this),
			this.props.delay
		);		
	},
	componentDidUpdate:function (){
		if (this.state.fading){
			if (this.state.opacity <= 0){
				this.props.onFadeComplete(this.props);
			} else {
				requestAnimationFrame(function(_)  {
					if (this.isMounted()){
						this.setState({ opacity: this.state.opacity - this.props.rate });
					}
				}.bind(this));
			}
		}
	}
});


/*** TOP LEVEL/ROOT/MAIN/PARENT COMPONENT ***/

var BookingAdmin = React.createClass({displayName: 'BookingAdmin',
	mixins: [EventFunnel, ClubSpeedApi, ParseRef, jQuerify/*, React.addons.PureRenderMixin*/],

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
			newIsPublic: null,
			popupMessage: null,
			raceDetails: {},
			loading: false,
			filterByDate: moment(),
			requestCount: 0
		};
	},
	
	isRaceInDate:function(race, start){
		start = moment(start);
		start.startOf('d');		
		
		var end = moment(start);
		end.add(1, 'd');
		
		var raceStart = moment(race.starts_at, 'YYYY-MM-DD H:mm:ss.SSS');		
		return (raceStart.isAfter(start) || raceStart.isSame(start)) && raceStart.isBefore(end);
	},
	
	filterBookings:function(start){
		var relatedBookings = _(this.state.raceDetails)
			.pick(function(race)  {return (!this.state.filterByTrackId || race.track_id == this.state.filterByTrackId) && this.isRaceInDate(race, start);}.bind(this))
			.mapValues(function(race)  {
				var raceBookings = _.filter(this.state.bookings, function(booking)  {return booking.heatId == race.id;});
				
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
		return React.createElement("div", {className: "container-fluid"}, 
			this.renderPopup(), 
			React.createElement("div", {className: "row"}, 
				this.renderBookingNav(), 
				this.renderEditForm()
			)
		);
	},
		
	renderPopup:function(){
		if (!this.state.popupMessage){
			return null;
		}
		
		return React.createElement(FadeOut, {element: "div", onFadeComplete: this.handlePopupClick, onClick: this.handlePopupClick, key: this.state.popupMessage, 
			style: { position: 'fixed', top: 0, left: 0, width: '100%', cursor: 'pointer', zIndex: 9999, textAlign: 'center'}, 
			className: "alert alert-success"}, 
			this.state.popupMessage
		);
	},
	
	renderProductSelectOptions:function(){
		return _.map(this.state.products, function(product) 
			{return React.createElement("option", {value: product.productId, key: product.productId}, 
				product.description
			);}
		);
	},
	
	getProductSelectList:function(){
		return _.map(this.state.products, function(product)  {return { value: product.productId, label: product.description };});
	},
	
	renderBookingNav:function(){
		return React.createElement("div", {className: "col-md-6"}, 
			React.createElement("div", {className: "row"}, 
				React.createElement("h3", null, "All Activities")
			), 
			
			React.createElement("div", {className: "row form-inline"}, 
				React.createElement("div", {className: "form-group col-xs-10 col-sm-4 col-md-7 col-lg-5", style: { paddingLeft: 0, paddingBottom: 15}}, 
					React.createElement("label", {className: "control-label"}, 
						"Date"
					), 
					React.createElement(DatePicker, {ref: "date", className: "form-control", onFunnelEvent: this.handleDateChange, date: this.state.filterByDate})
				), 
				
				React.createElement("div", {className: "form-group", style: { paddingBottom: 15}}, 
					React.createElement("label", {className: "control-label"}, 
						"Track"
					), 
					React.createElement(TrackSelect, {className: "form-control", onFunnelEvent: this.handleTrackSelectEvent})
				)
			), 
			
			React.createElement("div", {className: "row form-group"}, 
				this.renderBookingTable()
			)
		);		
	},
	
	renderBookingTable:function(){
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
			.map(function(booking)  {return { booking:booking, race: this.state.raceDetails[booking.heatId] };}.bind(this) )
			.sortBy(function(bookingAndRace)  {return moment(bookingAndRace.race.starts_at).unix();} )
			.map(function(bookingAndRace)  {
				booking = bookingAndRace.booking;
				return React.createElement(BookingRow, {
					key: booking.onlineBookingsId || 'race.' + booking.heatId, 
					booking: booking, race: bookingAndRace.race, /*this.state.raceDetails[booking.heatId]*/product: this.state.products[booking.productsId], 
					selected: 
						_.contains(this.state.selectedBookingIds, booking.onlineBookingsId)
						|| _.find(this.state.selectedBookingIds, { heatId: booking.heatId }), 
					
					onFunnelEvent: this.handleBookingRowEvent}
				);
			}.bind(this))
			.value();
		
		return React.createElement("div", null, 
			React.createElement("div", null, 
				this.state.requestCount > 0?
					loadingMessage
				: foundBookings.length + (foundBookings.length > 1? ' activities': ' activity') + ' found.', React.createElement("br", null)
			), 
			React.createElement("div", {style: { overflowY: 'auto'}, ref: "table"}, 
				React.createElement("table", {className: "table"}, 
					React.createElement("thead", {className: "text-left"}, 
						React.createElement("tr", null, 
							React.createElement("th", {key: "editing"}, "Editing"), 
							React.createElement("th", {key: "time"}, "Time"), 
							React.createElement("th", {key: "name"}, "Name"), 
							React.createElement("th", {key: "product"}, "Product"), 
							React.createElement("th", {key: "public"}, "Public?"), 
							React.createElement("th", {key: "qty"}, "Qty")
						)
					), 
					React.createElement("tbody", null, 
						bookingRows
					)
				)
			), 
			this.state.selectedBookingIds.length > 0 &&
				React.createElement("span", null, 
					React.createElement("input", {type: "button", className: "btn btn-info", onClick: this.handleDeselectClick, value: "De-select All"}), 
					!this.areOnlyNewBookingsSelected() &&
						 React.createElement("input", {type: "button", className: "btn btn-danger pull-right", onClick: this.handleDeleteClick, value: "Delete Booking(s)"})
				)
		);
	},
	
	renderEditForm:function(){
		if (this.filterBookings(this.state.filterByDate).length == 0){
			return null;
		}
		
		if (this.state.selectedBookingIds.length == 0){
			if (this.state.requestCount > 0){
				return null;
			} else {
				return React.createElement("div", {className: "col-md-6"}, 
					"Select one or more activities to edit them."
				);
			}
		}
	
		var title = this.state.selectedBookingIds.length > 0 && 
			React.createElement("span", null, "Editing ", this.getBookingTitle(true));
					
		return React.createElement("div", {className: "col-md-6"}, 
			React.createElement("form", {className: "form-horizontal"}, 
					React.createElement("legend", {className: "row form-group"}, 
						title
					), 
				
				React.createElement("div", {className: "row form-group"}, 
					React.createElement("label", {className: "col-xs-4 control-label"}, 
						"Product Required"
					), 
					React.createElement("div", {className: "col-xs-4"}, 
						React.createElement(Select, {onFunnelEvent: this.handleProductSelectEvent, selectedId: this.state.newProductId, placeholder: " ", 
							list: this.getProductSelectList()}), 
						React.createElement("span", {className: "text-info"}, 
							this.state.selectedBookingIds.length > 1 && this.state.newProductId == null && !this.areOnlyNewBookingsSelected()
								&& '(multiple)'
						)
					)
				), 
				
				React.createElement("div", {className: "row form-group"}, 
					React.createElement("label", {className: "col-xs-4 control-label"}, 
						"Show To Public?"
					), 
					React.createElement("div", {className: "col-xs-3"}, 
						/*<ICheck onFunnelEvent={this.handlePublicCheckEvent} checked={this.state.newIsPublic} />*/
						React.createElement(IRadioGroup, {onFunnelEvent: this.handleRadioChange, selected: this.state.newIsPublic, 
									list: [ { label: 'Yes', value: true }, { label: 'No', value: false }]}), 
						React.createElement("span", {className: "text-info"}, 
							this.state.selectedBookingIds.length > 1 && this.state.newIsPublic == null  && !this.areOnlyNewBookingsSelected()
								&& '(multiple)'
						)
					)
				), 
				
				React.createElement("div", {className: "row form-group"}, 
					React.createElement("label", {className: "col-xs-4 control-label"}, 
						"Qty. Available Online"
					), 
					React.createElement("div", {className: "col-xs-3"}, 
						React.createElement("input", {className: "form-control", type: "number", min: "0", ref: "qtyAvail", 
						  onBlur: this.handleQtyAvailableChange}), 
						React.createElement("span", {className: "text-info"}, 
							(this.state.selectedBookingIds.length > 1 && (this.refs.qtyAvail.getDOMNode().value === '')
								 && _.countBy(this.state.selectedBookingIds, 'heatId')['undefined'] > 1)
								 && '(multiple)'
						)
					)
				), 
				
				React.createElement("div", {className: "row form-group"}, 
					React.createElement("div", {className: "col-xs-4"}), 
					React.createElement("div", {className: "col-xs-6"}, 
						React.createElement("input", {className: "btn btn-info", type: "submit", value: "Save", disabled: !this.isFormValid(), 
							style: { cursor: this.isFormValid() || 'not-allowed'}, 
							dataToggle: "tooltip", dataPlacement: "left", ref: "saveButton", 
							title: this.isFormValid() || 'Cannot save. One or more required fields are missing.', 
							onClick: this.handleSaveClick}), 
						this.isFormValid() || React.createElement("span", {className: "help-block has-warning"}, "Please fill out all fields.")
					)
				)
			)
		);
	},
	
	getBookingById:function (id){
		return _.findWhere(this.state.bookings, { onlineBookingsId: id });
	},
	
	getBookingTitle:function(asHTML){
		var bookingTitles = [];
		
		this.state.selectedBookingIds.forEach(function(item)  {
			if (item.heatId){
				var raceName = this.state.raceDetails[item.heatId].race_name;
				if (asHTML){
					bookingTitles.push(React.createElement("span", null, raceName, " (new booking)", React.createElement("br", null)));
				} else {
					bookingTitles.push(raceName);
				}
			} else {
				var booking = this.getBookingById(item);
				
				var raceOfBooking = this.state.raceDetails[booking.heatId];
				var bookingText = moment(raceOfBooking.starts_at).format('MMM D h:mm a ') + raceOfBooking.race_name;
				
				if (asHTML){
					bookingTitles.push(React.createElement("span", null, bookingText, React.createElement("br", null)));
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
		var params = { key: config.privateKey, select: 'productId, description', filter: 'deleted$eqfalse' };
		$.get(
			config.apiURL + 'products.json?' + $.param(params),
			function(body)  {
				this.setState({ products: _.indexBy(body.products, 'productId') });
			}.bind(this)
		);
		
		this.loadBookings();
				
		/*$(window).resize(_ => {
			if (this.refs.table){
				var tableElem = $(this.refs.table.getDOMNode());
					tableElem.height($(window).height() - tableElem.offset().top - 40);
			}
		});*/
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
		this.setState(newState);
	},
	
	loadBookings:function(filterByTrackId){	
		this.setState({ selectedBookingIds: [], loading: true, requestCount: this.state.requestCount + 1 });
	
		var inputDate = this.state.filterByDate;
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
			var bookingRequests = body.races.map(function(race)  {
				var requestUrl = config.apiURL + 'booking.json?' + $.param({ key: config.privateKey, heatId: race.HeatNo });			
				return $.get(requestUrl);
			});
			
			bookingRequests.forEach(function(req)  {
				req.then(function(body)  {
					body.bookings.forEach(function(booking)  {
						this.upsertListInState('bookings', booking, { onlineBookingsId: booking.onlineBookingsId });			
					}.bind(this));
				}.bind(this));
			}.bind(this));
						
			var raceDetailRequests = body.races.map(function(race)  {
				var requestUrl = config.apiURL + 'races/' + race.HeatNo + '.json?key=' + config.privateKey;
				return $.get(requestUrl);
			});
						
			raceDetailRequests.forEach(function(req)  {
				req.then(function(body)  {
					var change = {};
					change[body.race.id] = { $set: body.race };
					var raceDetails = React.addons.update(
						this.state.raceDetails,
						change
					);
					this.setState({ raceDetails:raceDetails });
				}.bind(this));
			}.bind(this));
			
			this.setState({ requestCount: this.state.requestCount + bookingRequests.length + raceDetailRequests.length - 1 });
			
			var allRequests = bookingRequests.concat(raceDetailRequests);
			$.when.apply($, allRequests).done(function(_)  {
				this.setState({ loading: false, requestCount: this.state.requestCount - allRequests.length });
			}.bind(this));
		}.bind(this));
	},
	
	componentDidUpdate:function(prevProps, prevState){
		if (prevState.selectedBookingIds.length != this.state.selectedBookingIds.length && this.state.selectedBookingIds.length > 0){
			if (this.areOnlyNewBookingsSelected()){
				if (!_.every(prevState.selectedBookingIds, 'heatId') || prevState.selectedBookingIds.length == 0){
					this.setState({ newProductId: null, newIsPublic: true });
					this.refs.qtyAvail.getDOMNode().value = 1;
				}
			} else {
				var existingBookingIds = _.reject(this.state.selectedBookingIds, 'heatId');
				var existingBookings = _.filter(this.state.bookings, function(booking)  {return _.contains(existingBookingIds, booking.onlineBookingsId);});
				var firstBooking = existingBookings[0];
				var newProductId;
				var newIsPublic;
					
				if (_.every(existingBookings, function(booking)  {return booking.quantityTotal == firstBooking.quantityTotal;})){
					this.refs.qtyAvail.getDOMNode().value = firstBooking.quantityTotal;						
				} else {
					this.refs.qtyAvail.getDOMNode().value = '';
				}				
				if (_.every(existingBookings, function(booking)  {return firstBooking.productsId == booking.productsId;})){
					newProductId = firstBooking.productsId;
				} else {
					newProductId = null;
				}					
				if (_.every(existingBookings, function(booking)  {return firstBooking.isPublic == booking.isPublic;})){
					newIsPublic = firstBooking.isPublic;
				} else {
					newIsPublic = null;
				}
				this.setState({ newProductId:newProductId, newIsPublic:newIsPublic });
				
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
	
	areOnlyNewBookingsSelected:function(){
		return _.every(this.state.selectedBookingIds, 'heatId');
	},
	
	isFormValid:function (){	
		if (_.some(this.state.selectedBookingIds, 'heatId')){
			return this.state.newProductId != null && this.state.newIsPublic != null && this.parseRef('qtyAvail') != null;
		} else {
			return true;
		}
	},
	
	handleDateChange:function(e, props, other){
		this.setState(
			{ filterByDate: other.date },
			function(_)  { this.loadBookings(); }.bind(this)
		);
	},
	
	handleTrackSelectEvent:function (event, props){
		if (event.added){
			this.setState({ filterByTrackId: event.val || false });
			this.loadBookings(event.val);
		} else if (event.val == '') {
			this.setState({ filterByTrackId: false });
		}
	},
	
	handleBookingRowEvent:function (e, rowProps){
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
				this.setState({ selectedBookingIds:selectedBookingIds });
				break;
		}		
	},
	
	handlePublicCheckEvent:function(e){		
		switch (e.type){
			case 'ifChecked':
				this.setState({ newIsPublic: true, saveEnabled: true });
				break;
			case 'ifUnchecked':
				this.setState({ newIsPublic: false, saveEnabled: true });			
				break;
		}
	},
	
	handleRadioChange:function(e, props, state, optionProps){
		this.setState({ newIsPublic: optionProps.item.value });
	},
	
	handleProductSelectEvent: function(e, props, state){
		if (e.added){
			this.setState({ newProductId: e.val });
		} else if (e.val == ''){
			this.setState({ newProductId: null });
		}
	},
	
	handleQtyAvailableChange:function(){
		var elem = this.jQuerify('qtyAvail');
		
		elem.val(parseInt(elem.val()));
		
		if (elem.val() < elem.attr('min')){
			elem.val(elem.attr('min'));
		}
		
		this.forceUpdate();
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
	
	putChange:function(change){
		if (this.state.selectedBookingIds.length == 0){
			return;
		}
		
		var putRequests = this.state.selectedBookingIds.map(function(id)  {
			if (id.heatId){
				var url = config.apiURL + 'booking?key=' + config.privateKey;
				var data = _.extend(change, {
					heatId: id.heatId,
					quantityTotal: change.quantityTotal
				});			
				
				return $.ajax({
					type: 'POST',
					url:url,
					data:data
				});
			} else {
				var url = config.apiURL + 'booking/' + id + '?key=' + config.privateKey;
				var data = _.omit(change, 'heatId'); // omit is temp, prevent heatId from getting passed here in first place
				
				return $.ajax({
					type: 'PUT',
					url:url,
					data:data				
				});
			}
		});
		
		$.when.apply($, putRequests).then(
			function()  {var all=Array.prototype.slice.call(arguments,0);
				var popupMessage = 'Booking for ' + this.getBookingTitle() + ' successfully saved! ('
					+ moment().format('h:mm:ss a') + ')';
				this.setState({ popupMessage:popupMessage });
				this.loadBookings();
			}.bind(this),
			function()  {var all=Array.prototype.slice.call(arguments,0);
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
			
			// remove bookings to be deleted from local booking cache in state
			var bookings = _.reject(this.state.bookings, function(booking)  {return _.contains(bookingsToDelete, booking.onlineBookingsId);});
			this.setState({ bookings:bookings, selectedBookingIds: [] });	
			
			// delete requests
			var deleteRequests = bookingsToDelete.map(function(id) 
				{return $.ajax({
					url: config.apiURL + 'booking/' + id + '?key=' + config.privateKey,
					type: 'DELETE'	
				});}		
			);
			
			// handle responses
			$.when.apply($, deleteRequests).then(
				function()  {var all=Array.prototype.slice.call(arguments,0);					
					var popupMessage = 'Booking(s) successfully deleted! ('
						+ moment().format('h:mm:ss a') + ')';
					this.setState({ popupMessage:popupMessage });
				}.bind(this),
				function()  {var all=Array.prototype.slice.call(arguments,0);
					var errorMessage = 'An error occurred while trying to delete booking(s).';
					alert(errorMessage);
				}
			);			
		}
	}
});


/*** RENDER IT! ***/

React.render(React.createElement(BookingAdmin, null), document.getElementById('main'));