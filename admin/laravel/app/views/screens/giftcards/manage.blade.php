@extends('master')

@section('title')
Manage Gift Cards
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
Manage Gift Cards
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#">Gift Cards</a>
<a href="#" class="current">Manage</a>
@stop

@section('content')
{{ Form::open(array('action'=>'GiftCardsController@updateBalance','files' => false, 'class' => 'form-horizontal')) }}

    <div class="container-fluid">
      <div class="row">
          <div class="col-xs-12">
            @if (Session::has("message"))
            <div class="alert alert-success fadeAway">
                <p>{{ Session::get("message") }}</p>
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

            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                <h5>Update Gift Card Balances</h5>
              </div>
              <div class="widget-content">
                  <div class="row">
                      <div class="col-sm-12">
                          <div class="form-group">
                              <label class="col-sm-2 col-md-2 col-lg-2 control-label">Gift Card Numbers</label>
                              <div class="col-sm-10 col-md-10 col-lg-10">
                                  <textarea rows="5" name="giftCardsToUpdate" class="form-control">{{Input::old('giftCardsToUpdate') !== null ? Input::old('giftCardsToUpdate') : ''}}</textarea>
                                  <span class="help-block text-left">
                                      A comma-separated list of gift card numbers to update. May also be represented as ranges with a dash. Enters/newlines are not allowed.<p/><p/>
                                      <strong>Example: </strong> 1354,1355,1358,1500-2001,2050
                                      <div class="alert alert-info text-center">
                                      Gift cards are capable of holding both a <strong>Points</strong> balance and a <strong>
                                      Money</strong> balance. Any values entered below will overwrite <strong>both</strong>
                                      of the previous balances for all cards in the list above.
                                      </div>
                                  </span>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="row">
                      <div class="col-sm-6">
                          <div class="form-group">
                              <label class="col-sm-4 col-md-4 col-lg-4 control-label">New Points Balance</label>
                              <div class="col-sm-8 col-md-8 col-lg-8">
                                  <input class="form-control" type="text" name="newPointsBalance" value="{{Input::old('newPointsBalance') !== null ? Input::old('newPointsBalance') : ''}}">
                                  <span class="help-block text-left">
                                      The new points balance to apply to <strong>all</strong> of the cards above. Leave blank if no changes are desired.<p/><p/>
                                      <strong>Example: </strong> 1000
                                  </span>
                              </div>
                          </div>
                      </div>
                      <div class="col-sm-6">
                          <div class="form-group">
                              <label class="col-sm-4 col-md-4 col-lg-4 control-label">New Money Balance</label>
                              <div class="col-sm-8 col-md-8 col-lg-8">
                                  <input class="form-control" type="text" name="newCashBalance" value="{{Input::old('newCashBalance') !== null ? Input::old('newCashBalance') : ''}}">
                                  <span class="help-block text-left">
                                      The new money balance to apply to <strong>all</strong> of the cards above. Leave blank if no changes are desired.<p/><p/>
                                      <strong>Example: </strong> 1000.50 <br/>(Money balances must be in the U.S. decimal notation.)
                                  </span>
                              </div>
                          </div>
                      </div>
                      <div class="col-sm-12">
                          <div class="form-group">
                              <label class="col-sm-2 col-md-2 col-lg-2 control-label">Notes</label>
                              <div class="col-sm-10 col-md-10 col-lg-10">
                                  <textarea rows="1" name="notes" class="form-control">{{Input::old('notes') !== null ? Input::old('notes') : ''}}</textarea>
                              <span class="help-block text-left">
                                  Notes to include about this gift card balance update. Maximum length of 400 characters. Optional.
                              </span>
                              </div>
                          </div>
                      </div>
                      <div class="col-sm-12">
                           <div class="form-actions" style="margin-bottom: 10px;">
                               {{ Form::submit('Update Balance(s)', array('class' => 'btn btn-info')) }}
                           </div>
                      </div>
                  </div>
              </div>
            </div>

          </div>


     </div>

    {{ Form::close() }}

    </div>

@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent

@stop
<!-- END JAVASCRIPT INCLUDES -->