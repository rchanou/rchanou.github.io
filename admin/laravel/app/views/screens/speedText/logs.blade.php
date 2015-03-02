@extends('master')

@section('title')
  Speed Text Logs
@stop

@section('css_includes')
  @parent
  {{ HTML::style('css/jquery.ui.ie.css') }}
  {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
  Speed Text Logs
@stop

@section('breadcrumb')
  <a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
  <a href="#">Facebook</a>
  <a href="#" class="current">Speed Text Logs</a>
@stop

@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-xs-12">
      <div class="widget-box">
        <div class="widget-title">
          <span class="icon">
            <i class="fa fa-th"></i>
          </span>
          <h5>Speed Text Logs</h5>
        </div>
        <div class="widget-content nopadding">
          <table class="table table-bordered table-striped table-hover data-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Message</th>
                <th>Date</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@stop

@section('js_includes')
  @parent
  {{ HTML::script('js/moment.min.js') }}
  {{ HTML::script('js/jquery.dataTables.min.js') }}
  {{ HTML::script('js/dataTables.pipeline.js') }}
  <script>
    $(document).ready(function() {
      var $table = $('.data-table');

      $table.dataTable({
        jQueryUI: true,
        //autoWidth: false,
        pagingType: 'full_numbers',
        //dom: '<""l>t<"F"fp>',
        //dom: "lfrtipS",
        dom: '<lpfr>t<i>S',
        serverSide: true,
        processing: true,
        ajax: $.fn.dataTable.pipeline({
          url: 'logs/data',
          pages: 5
        }),
        order: [[0, 'desc']],
        columns: [
          { "name": "logsId" },
          { "name": "message" },
          { name: "date", width: '10%',
            render: function(data, type, full, meta){
              return moment(data, 'YYYY-MM-DDTHH:mm:ss.SS').format('MMM D h:mm a');
            }
          }
        ],
        columnDefs: [{ targets: 1, sortable: false }]
      });


      var table = $table.DataTable();

      var update_size = function() {
        $table.css({ width: $table.parent().width() });
        table.columns.adjust();
      }

      $(window).resize(function() {
        clearTimeout(window.refresh_size);
        window.refresh_size = setTimeout(function() { update_size(); }, 250);
      });
    });
  </script>
@stop
