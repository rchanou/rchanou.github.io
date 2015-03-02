var React = require('react/addons');
var Imm = require('immutable');
var moment = require('moment');
var jsonp = require('jsonp');

var _ = require('lodash');


var TableHeader = React.createClass({

  render(){
    return <th {...this.props}
      onClick={this._onClick}
      style={{ cursor: 'pointer', width: this.props.width, userSelect: 'none', WebkitUserSelect: 'none' }}
    >
      {this.props.label || this.props.id}&nbsp;
      {this.props.caret && <i className={"fa fa-caret-" + this.props.caret}></i>}
    </th>;
  },

  getDefaultProps(){
    return {
      onClick(){}
    };
  },

  _onClick(event){
    this.props.onClick({ id: this.props.id || this.props.label });
  }

});


var TableCell = React.createClass({

  render(){
    /*var style = this.props.style? _.cloneDeep(this.props.style): {};
    if (this.state.hovering){
      style.backgroundColor = 'hsl(180,50%,80%)';
      style.overflow = 'visible';
    }*/

    var style = this.props.style;

    return <td onMouseOver={this._onMouseOver} onMouseOut={this._onMouseOut}
      style={style} title={this.props.text}
    >
      {this.props.text}
    </td>;
  },

  getInitialState(){
    return { hovering: false };
  },

  /*componentDidMount(){
    $(this.getDOMNode()).tooltip({
      placement: 'top'
    });
  },*/

  _onMouseOver(){
    this.setState({ hovering: true });
  },

  _onMouseOut(){
    this.setState({ hovering: false });
  },

  componentWillUnmount(){

  }

});


module.exports = React.createClass({

  render(props, state){
    var headerCells = this.props.columns.map(col =>
      <TableHeader
        {...col}
        key={col.id + col.label}
        onClick={this._onHeaderClick}
        caret={this.state.sortBy === col.id? this.state.sortAscending? 'up': 'down': null}
      >
        {col.label}
      </TableHeader>
    );

    var logRows = this.getFilteredLogs().map((log, i) => {
      var cells = this.props.columns.map(col => {
        var id = col.id || col.label;
        var detail = col.format(log.get(col.source) || log.get(col.id));
        return <TableCell
          key={col.id + col.label}
          style={{ width: col.width, maxWidth: col.width, height: 100, textOverflow: 'ellipsis' }}
          text={detail}
        />;
      });

      return <tr key={'l' + log.get('logsId')}>
        {cells}
      </tr>;
    })
    .toJS();

    return <div className='widget-content container-fluid'>
      <div className='row'>
        <div className='form-group col-xs-12'>
          <div className='row'>
            <label className='control-label col-xs-2 col-lg-1'>Filter:</label>
            <div className='col-xs-10'>
              <input className='form-control' onChange={this._onFilterChange} />
            </div>
          </div>
        </div>
      </div>
      <div className='row'>
        <div className='col-xs-12'>
          <table className="table table-bordered table-striped table-hover table-fixedheader">
            <thead>
              <tr>
                {headerCells}
              </tr>
            </thead>
            <tbody ref='tbody'
              style={{ height: this.state.tableHeight, overflowY: 'scroll' }}
            >
              {logRows}
              <tr key='loading' style={{ width: '100%' }}>
                <td colSpan={this.props.columns.length}
                  style={{ display: 'table-cell', width: '100%', textAlign: 'center' }}>
                  <img src={'/admin/img/spinner.gif'} />
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>;
  },

  _onHeaderClick(e){
    if (e.id !== this.state.sortBy){
      this.setState({ sortBy: e.id });
    } else {
      this.setState({ sortAscending: !this.state.sortAscending });
    }
  },

  getDefaultProps(){
    return {
      triggerHeight: 40,
      limitPerPage: 10,
      loadUrl: 'https://vm-122.clubspeedtiming.com/admin/facebook/log-entries?where[terminal]=Facebook&where[message][$has]=&limit=10&page=',
      columns: [
        {
          id: 'date',
          label: 'Date',
          format: raw => raw.format('MMM d'),
          sortLogsWith: log => log.get('date').valueOf(),
          width: '15%'
        },
        {
          id: 'time',
          source: 'date',
          label: 'Time',
          format: raw => raw.format('H:mm a'),
          sortLogsWith: log => log.get('date').valueOf(),
          width: '15%'
        },
        {
          id: 'message',
          label: 'Message',
          format: _.identity,
          sortLogsWith: log => log.get('message'),
          width: '70%'
        }
      ]
    };
  },

  getInitialState(){
    return {
      logs: new Imm.Map(),
      filter: '',
      nextPage: 0,
      loading: false,
      sortBy: 'date',
      sortAscending: true
    };
  },

  getFilteredLogs(){
    var logs = this.state.logs.toList().filter(
      log => (log.get('message') + log.get('date').format('dddd, MMMM Do YYYY, h:mm:ss a'))
        .toUpperCase().indexOf(this.state.filter) !== -1
    );

    var before = logs;
    console.log('b4 sort', before.toJS());

    logs = logs.sort((logA, logB) => {



      //console.log('no sort', logA.toJS(), logB.toJS(), logA, logB);

      var sortBy = this.state.sortBy;
      var cols = this.props.columns;
      var colMatch = _.find(cols, c => c.id === this.state.sortBy);
      var logSource = colMatch.source || colMatch.id;

      var sortWith = colMatch.sortLogsWith;

      var aVal = sortWith(logA);
      var bVal = sortWith(logB);

      console.log(aVal, bVal);

      return (aVal < bVal? -1: aVal > bVal? 1: 0) * (this.state.sortAscending? 1: -1);

      /*
      console.log(source);

      return 0;

      var aVal = logA.get(source);
      var bVal = logB.get(source);

      //console.log('sorting', aVal, bVal, logA, logB, source);

      var dir = this.state.sortAscending? -1: 1;

      var aOf = logA.hasIn([source, 'valueOf'])? logA.getIn([source, 'valueOf']).toJS(): logA.get(source).toJS();
      var bOf = logB.hasIn([source, 'valueOf'])? logB.getIn([source, 'valueOf']).toJS(): logB.get(source).toJS();
      if (aOf && bOf){
        return (aOf() < bOf())? dir: (aOf() > bOf())? (-1 * dir): 0;
      } else {
        return (aVal < bVal)? dir: (aVal > bVal)? (-1 * dir): 0;
      }*/
    });

    var after = logs;
    console.log('after sort', after.toJS());

    //console.assert((before.size === 0 && after.size === 0) || before !== after, 'tsratrs');

    return logs;
  },

  getDocHeight(){
    var D = document;
    return Math.max(
      D.body.scrollHeight, D.documentElement.scrollHeight,
      D.body.offsetHeight, D.documentElement.offsetHeight,
      D.body.clientHeight, D.documentElement.clientHeight
    );
  },

  loadNextPage(callback){
    this.setState({ loading: true });

    jsonp(this.props.loadUrl + this.state.nextPage, null, (err, res) => {

      var logsToMerge = new Imm.Map();
      res.forEach(rawLog => {
        rawLog.date = moment(rawLog.date);
        var logsId = rawLog.logsId;
        var immLog = Imm.fromJS(rawLog);
        logsToMerge = logsToMerge.set(logsId, immLog);
      });

      var mergedLogs = this.state.logs.mergeDeep(logsToMerge);

      this.setState({
          nextPage: this.state.nextPage + 1,
          loading: false,
          logs: mergedLogs
        },
        callback
      );
    });
  },

  componentDidMount(){
    var me = this.refs.tbody.getDOMNode();
    var $me = $(me);

    var initLoad = () => {
      //if (this.getDocHeight() < $(window).height() + this.props.triggerHeight){
      if ((me.scrollHeight - me.scrollTop - 10 < me.clientHeight)){
        this.loadNextPage(initLoad);
      }
    };
    initLoad();

    $(me).scroll(() => {
      //if($(window).scrollTop() + $(window).height() + this.props.triggerHeight >= this.getDocHeight()) {
      if ((me.scrollHeight - me.scrollTop - 10 < me.clientHeight)){
        this.loadNextPage();
      }
      //}
    });

    $(window).resize(() => {
      //console.log('resize', $(window).height(), $(this.refs.table.getDOMNode()).offset().top);
      //console.log($(window).height(), $(this.getDOMNode()).height(), $(document).height());
      this.setState({
        tableHeight: $(window).height() - $(this.getDOMNode()).offset().top - 188
      });
    });
    $(window).resize();

    this.loadNextPage();
  },

  shouldComponentUpdate(nextProps, nextState){
    return true;

    for (var key in this.state){
      if (this.state[key] !== nextState[key]){
        return true;
      }
    }
    return false;
  },

  _onFilterChange(e){
    this.setState({ filter: e.target.value.toUpperCase() });
  }

});
