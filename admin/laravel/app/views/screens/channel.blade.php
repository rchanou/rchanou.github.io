@extends('master')

@section('title')
Dashboard
@stop

@section('pageHeader')
Dashboard
@stop

@section('breadcrumb')
    <a href="dashboard" title="Go to Home" class="tip-bottom"><i class="fa fa-home"></i> Home</a>
    <a href="#" class="current">Speed Screen Channels</a>
@stop

@section('content')
    <div class="row">
      <div class="row">
        <div class="col-sm-12">
          <div class="tabbable inline tabs-left">
            <ul class="nav nav-tabs tab-green" id="myTab3">
              <li class="active"> <a data-toggle="tab" href="#panel_tab4_example1"> 1. Track 1 Pit </a> </li>
              <li class=""> <a data-toggle="tab" href="#panel_tab4_example2"> 2. Reception </a> </li>
              <li class=""> <a data-toggle="tab" href="#panel_tab4_example3"> 3. POS Menu </a> </li>
            </ul>
            <div class="tab-content">
              <div id="panel_tab4_example1" class="tab-pane active">
                <div class="row">
                  <div class="col-sm-10">
                    <h2>1. Track 1 Pit</h2>
                  </div>
                  <div class="col-sm-2">
                    <input type="button" class="btn btn-success btn-block" value="Preview" style="margin-top: 1.5em" onClick="alert('go to screen in new window');">
                  </div>
                </div>
                <div class="tabbable inline">
                  <ul class="nav nav-tabs tab-bricky" id="myTab">
                    <li class="active"> <a data-toggle="tab" href="#panel_tab2_example3"> Slide Lineup </a> </li>
                    <li class=""> <a data-toggle="tab" href="#panel_tab2_example1"> Deploy </a> </li>
                    <li class=""> <a data-toggle="tab" href="#panel_tab2_example2"> Channel Settings </a> </li>
                  </ul>
                  <div class="tab-content">
                    <div id="panel_tab2_example3" class="tab-pane active">
                      <div class="widget-box collapsible">
                        <div class="widget-title"> <a href="#slide1" data-toggle="collapse"> <span class="icon"><i class="fa fa-bar-chart-o"></i></span>
                          <h5>1. Next Racers</h5>
                          </a> </div>
                        <div class="collapse" id="slide1">
                          <div class="widget-content"> This box is now open </div>
                        </div>
                      </div>
                      <div class="widget-box collapsible">
                        <div class="widget-title"> <a href="#slide-2" data-toggle="collapse"> <span class="icon"><i class="fa fa-bar-chart-o"></i></span>
                          <h5>2. Scoreboard</h5>
                          </a></div>
                        <div class="collapse" id="slide-2">
                          <div class="widget-content"> This box is now open </div>
                        </div>
                      </div>
                      <div class="widget-box collapsible">
                        <div class="widget-title"> <a href="#slide-3" data-toggle="collapse"> <span class="icon"><i class="fa fa-bar-chart-o"></i></span>
                          <h5>3. Top Times of the Day</h5>
                          </a></div>
                        <div class="collapse" id="slide-3">
                          <div class="widget-content"> This box is now open </div>
                        </div>
                      </div>
                    </div>
                    <div id="panel_tab2_example1" class="tab-pane">
                      <div class="alert alert-info">
                        <p> This tool will create a downloadable applicaiton that will launch the Speed Screen Channel application with the settings below. For non-Windows devices, you may use <a href="https://www.google.com/chrome/browser/" target="_blank">Google Chrome</a> to load the Channel URL directly. </p>
                      </div>
                      <form action="#" method="get" class="form-horizontal">
                        <div class="form-group">
                          <label class="col-sm-3 col-md-3 col-lg-2 control-label">Channel URL</label>
                          <div class="col-sm-9 col-md-9 col-lg-10">
                            <input type="text" class="form-control input-sm" placeholder="http://<trackName>.clubspeedtiming.com/cs-speedscreen/#/1">
                          </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-3 col-md-3 col-lg-2 control-label">Target Monitor</label>
                          <div class="col-sm-9 col-md-9 col-lg-10">
                            <select tabindex="-1" class="select2-offscreen">
                              <option value="1">Monitor 1 (Default)</option>
                              <option value="2">Monitor 2</option>
                              <option value="3">Monitor 3</option>
                              <option value="4">Monitor 4</option>
                              <option value="5">Monitor 5</option>
                              <option value="6">Monitor 6</option>
                              <option value="7">Monitor 7</option>
                              <option value="8">Monitor 8</option>
                              <option value="9">Monitor 9</option>
                              <option value="10">Monitor 10</option>
                              <option value="11">Monitor 11</option>
                              <option value="12">Monitor 12</option>
                              <option value="13">Monitor 13</option>
                              <option value="14">Monitor 14</option>
                              <option value="15">Monitor 15</option>
                              <option value="16">Monitor 16</option>
                            </select>
                            <span class="help-block text-left">Start the channel application on this monitor (useful for multi-monitor PCs)</span> </div>
                        </div>
                        <div class="form-group">
                          <label class="col-sm-3 col-md-3 col-lg-2 control-label">Disable Animations</label>
                          <div class="col-sm-9 col-md-9 col-lg-10">
                            <label>
                              <input type="checkbox" name="checkboxes" checked />
                              Yes, disable slide animations</label>
                          </div>
                        </div>
                        <div class="form-actions">
                          <button type="submit" class="btn btn-info">Create Application</button>
                        </div>
                      </form>
                    </div>
                    <div id="panel_tab2_example2" class="tab-pane">
                      <p>Placeholder for channel-wide settings such as "Channel Name" and "Delete" button.</p>
                      <button class="btn btn-dark-red">Delete Channel</button>
                    </div>
                  </div>
                </div>
              </div>
              <div id="panel_tab4_example2" class="tab-pane">
                <div class="col-sm-10">
                  <h2>2. Receiption</h2>
                </div>
                <div class="col-sm-2">
                  <input type="button" class="btn btn-success btn-block" value="Preview" style="margin-top: 1.5em" onClick="alert('go to screen in new window');">
                </div>
                <p>Channel 2 Content</p>
              </div>
              <div id="panel_tab4_example3" class="tab-pane">
                <div class="col-sm-10">
                  <h2>3. POS Menu</h2>
                </div>
                <div class="col-sm-2">
                  <input type="button" class="btn btn-success btn-block" value="Preview" style="margin-top: 1.5em" onClick="alert('go to screen in new window');">
                </div>
                <p>Channel 3 Content</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
@stop