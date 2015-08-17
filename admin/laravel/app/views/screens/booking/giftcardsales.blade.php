@extends('master')

@section('title')
Gift Card Sales
@stop

@section('css_includes')
    @parent
    {{ HTML::style('css/jquery.ui.ie.css') }}
    {{ HTML::style('css/jquery-ui.css') }}
@stop

@section('pageHeader')
    Gift Card Sales
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#">Online Bookings</a>
<a href="#" class="current">Gift Card Sales</a>
@stop

@section('content')
{{ Form::open(array('action'=>'BookingController@updateGiftCardSalesSettings','files'=>false, 'class' => 'form-horizontal')) }}

    <div class="container-fluid">
      <div class="row">


          <div class="col-xs-12">
            @if ($currentOnlineBookingState == 'disabled_manually')
            <div class="alert alert-warning">
                <p>(Note: Online Booking is <strong>current disabled</strong> because the "Enable Online Booking" setting is not checked.</p>
                To access Online Booking while it's disabled (for testing), <a href="{{'https://' . $_SERVER['HTTP_HOST'] . '/booking/step1?key=' . md5(Config::get('config.privateKey'))}}">use this link</a>.
            </div>
            @endif
            @if ($currentOnlineBookingState == 'disabled_dummypayments')
            <div class="alert alert-warning">
                <p>(Note: Online Booking is <strong>current disabled</strong> because the site is using the Dummy payment processor.)</p>
                To access Online Booking while it's disabled (for testing), <a href="{{'https://' . $_SERVER['HTTP_HOST'] . '/booking/step1?key=' . md5(Config::get('config.privateKey'))}}">use this link</a>.
            </div>
            @endif
            @if ($currentOnlineBookingState == 'missing_translations')
            <div class="alert alert-warning">
                <p>(Note: Online Booking is <strong>missing some translations</strong> for the current culture. They will default to English (US).)</p>
                Please proceed to the <a href="{{URL::to('booking/translations')}}">Translations section</a> and update those translations.
            </div>
            @endif
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
                    <h5>General Settings</h5>
                </div>
                <div class="widget-content">
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 col-md-2 col-lg-2 control-label">Enable Gift Card Sales</label>
                                <div class="col-sm-10 col-md-10 col-lg-10">
                                    @if(isset($bookingSettings['giftCardSalesEnabled']))
                                    <input type="checkbox" id="giftCardSalesEnabled" name="giftCardSalesEnabled" {{$isChecked['giftCardSalesEnabled'] ? 'checked' : ''}}>
                                    <span class="help-block text-left">
                                        If checked, any gift card products enabled below become available for sale via the Online Booking website.
                                    </span>
                                    @else
                                        <span class="help-block text-left" style="color: #c20000">This setting is not currently supported by your server.</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="col-sm-12">
                            <div class="form-actions" style="margin-bottom: 10px;">
                                {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if(isset($bookingSettings['giftCardSalesEnabled']))
            <div class="widget-box">
              <div class="widget-title">
                <span class="icon">
                  <i class="fa fa-align-justify"></i>
                </span>
                <h5>Gift Cards</h5>
              </div>
              <div class="widget-content">
                  <div class="row">
                      <div class="col-sm-12">
                          @if(isset($giftCardProducts) && count($giftCardProducts) > 0)
                              <table class="table table-bordered table-striped table-hover text-center">
                                    <thead>
                                        <tr>
                                            <th>Description</th>
                                            <th style="width: 33%">Product ID</th>
                                            <th style="width: 33%">Available Online?</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($giftCardProducts as $currentProduct)
                                        <tr>
                                            <td>{{$currentProduct['description']}}</td>
                                            <td>{{$currentProduct['productId']}}</td>
                                            <td><input name="giftCard_{{$currentProduct['productId']}}" type="checkbox" {{$currentProduct['availableOnline'] ? 'checked' : ''}}></td>
                                        </tr>
                                        @endforeach
                                  </tbody>
                              </table>
                              <div class="alert alert-info text-center">
                                  Check the 'Available Online?' checkbox to make a specific gift card available for sale via the Online Booking website.<p/>
                                  Need to create more gift card products? Head on over to <a href="{{'http://' . $_SERVER['HTTP_HOST'] . '/sp_admin'}}" target="_blank">the old admin panel</a> to do so!
                              </div>
                          @else
                              <div class="alert alert-info text-center">
                                  There are currently no gift card products created on your server. Please proceed to <a href="http://{{$_SERVER['HTTP_HOST']}}/sp_admin" target="_blank">the old admin panel</a> to create some.
                              </div>
                          @endif
                      </div>
                      <div class="col-sm-12">
                           <div class="form-actions" style="margin-bottom: 10px;">
                               {{ Form::submit('Save Changes', array('class' => 'btn btn-info')) }}
                           </div>
                      </div>
                  </div>
              </div>
            </div>
          </div>
          @endif


     </div>

    {{ Form::close() }}

    </div>

@stop

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent
<script>

    $(document).ready(function () {

        window.setTimeout(function() {
          $(".fadeAway").fadeTo(500, 0).slideUp(500, function(){
              $(this).remove();
          });
        }, 5000);

    });

</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->