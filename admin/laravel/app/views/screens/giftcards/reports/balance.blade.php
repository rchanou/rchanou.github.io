@extends('master')

@section('title')
Gift Card Balance Report
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
    Gift Card Balance Report
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#">Gift Cards</a>
<a href="{{URL::to('giftcards/reports')}}">Reports</a>
<a href="#" class="current">Balance Report</a>
@stop

@section('content')
    <div class="container-fluid">
      <div class="row">
          <div class="col-sm-12">
              @if (Session::has("message"))
                  <div class="alert alert-success fadeAway">
                      <p>{{ Session::get("message") }}</p>
                  </div>
              @endif
              @if (isset($message))
                  <div class="alert alert-success fadeAway">
                      <p>{{ $message }}</p>
                  </div>
              @endif
              @if (Session::has("error"))
                  <div class="alert alert-danger">
                      <p>{{ Session::get("error") }}</p>
                  </div>
              @endif
              @if (Session::has("errors"))
                  <div class="alert alert-danger"> <!-- Errors from Laravel validation -->
                      <ul>
                          @foreach ($errors->all('<li>:message</li>') as $message)
                              {{ $message }}
                          @endforeach
                      </ul>
                  </div>
              @endif
          </div>
      </div>
      <div class="row">
      <div class="col-sm-2"></div>
       <div class="col-sm-8">
        <div class="widget-box">
          <div class="widget-title">
            <span class="icon">
              <i class="fa fa-calendar"></i>
            </span>
            <h5>Gift Card Numbers</h5>
          </div>
          <div class="widget-content">
              <div class="row">
              {{ Form::open(array('action'=>'GiftCardsController@getBalanceReport','files'=>false, 'class' => 'form-horizontal')) }}
                <div class="col-12-sm">
                    <textarea rows="5" name="listOfGiftCards" class="form-control">{{Input::old('listOfGiftCards') !== null ? Input::old('listOfGiftCards') : ''}}</textarea>
                      <span class="help-block text-left">
                          A comma-separated list of desired gift card numbers for the balance report. May also be represented as ranges with a dash. Enters/newlines are not allowed.<p/><p/>
                          <strong>Example: </strong> 1354,1355,1358,1500-2001,2050<p/><p/>
                          <em>There is a limit of 2000 gift cards per report.</em>
                      </span>
                </div>
                  <div class="col-sm-12">
                       {{ Form::submit('Get Report', array('class' => 'btn btn-info')) }}
                  </div>
              </div>
              {{ Form::close() }}
          </div>
        </div>
       </div>
       <div class="col-sm-2"></div>
      </div>
      <div class="row">
        <div class="col-sm-12">

            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                    <h5>Gift Card Balance Report</h5>
                <span class="icon pull-right" style="font-size: 12px; line-height: 12px;">
                    <a href="{{URL::to('giftcards/reports/balance/csv')}}"><button type="button">Export to CSV</button></a>
                </span>
              </div>
              <div class="widget-content">
                  <div class="row" style="overflow: auto">
                    <div class="col-12-lg">
                        @if(count($report) > 0 )
                            <table class="table table-bordered table-striped table-hover text-center">
                                <thead>
                                    <tr>
                                    @foreach($report[0] as $reportColumnName => $reportColumnValue)
                                        <th>{{$reportColumnName}}</th>
                                    @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report as $currentReportColumn)
                                    <tr>
                                        @foreach($currentReportColumn as $currentReportValue)
                                        <td>{{$currentReportValue}}</td>
                                        @endforeach
                                    </tr>
                                    @endforeach
                              </tbody>
                            </table>
                        @elseif ($report === null)
                            <div class="alert alert-danger">
                            Unable to reach the Club Speed server. Please try again later.
                            </div>
                        @else
                            <div class="alert alert-info">
                            There is no data in this report. Please enter a range of gift cards above.
                            </div>
                        @endif
                    </div>
                  </div>
              </div>
            </div>

        </div>
      </div>
    </div>
@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent
@stop
<!-- END JAVASCRIPT INCLUDES -->