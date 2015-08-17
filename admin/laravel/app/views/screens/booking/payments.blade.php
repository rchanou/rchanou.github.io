@extends('master')

@section('title')
Payment Processors
@stop

@section('css_includes')
    @parent

@stop

@section('pageHeader')
Payment Processors
@stop

@section('breadcrumb')
<a href="{{URL::to('dashboard')}}" title="Go to the Dashboard" class="tip-bottom"><i class="fa fa-home"></i> Dashboard</a>
<a href="#" class="current">Online Bookings</a>
<a href="#" class="current">Payment Processors</a>
@stop

@section('content')

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
                 <div class="col-sm-12">
                     <div class="widget-box">
                            <div class="widget-title">
                                  <span class="icon">
                                    <i class="fa fa-align-justify"></i>
                                  </span>
                                  <h5>Payment Processor Settings</h5>
                            </div>
                            <div>
                                <div class="widget-content">
                                    <div class="row">
                                        <div class="col-sm-12">
                                        <label>Payment processor: </label>
                                        <select name="paymentProcessorDropdown" id="paymentProcessorDropdown">
                                        @foreach($supportedPaymentTypes as $paymentType)
                                            <option value="{{$paymentType->name}}" @if($paymentType->name == $currentPaymentType)selected="selected"@endif>
                                            {{$paymentType->name}} @if($paymentType->name == $currentPaymentType)<em>(current)</em>@endif
                                            </option>
                                        @endforeach
                                        </select><p/>
                                        @foreach($supportedPaymentTypes as $paymentType)
                                        {{ Form::open(array('action'=>'BookingController@updatePaymentSettings','files' => false, 'class' => 'form-horizontal')) }}
                                            <div id="{{$paymentType->name}}_box" class="paymentOptionsBox">
                                            @if ($paymentType->options != null)
                                                <table class="table table-bordered table-striped table-hover text-center">
                                                    <thead>
                                                        <tr>
                                                            <th>Setting</th>
                                                            <th>Value</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                 @foreach($paymentType->options as $option)
                                                     <tr>
                                                        <td><label>{{$option}}</label></td><td><input type="text" class="text-center" name="{{$option}}" value="{{isset($currentSavedSettings[$paymentType->name]['options'][$option]) ? $currentSavedSettings[$paymentType->name]['options'][$option]: ""}}"></td>
                                                     </tr>
                                                 @endforeach
                                                        </tbody>
                                                    </table>
                                             @else
                                                <div class="alert alert-info">No custom settings are required for this payment processor.</div>
                                             @endif
                                             <input type="hidden" name="paymentProcessor" value="{{$paymentType->name}}">
                                            {{ Form::submit('Apply Changes', array('class' => 'btn btn-info')) }}
                                            {{ Form::close() }}
                                            </div>
                                        @endforeach
                                        </div>
                                    </div>
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
    <script>
    $(document).ready(function(){
        $('.paymentOptionsBox').hide(); //Hide all payment options boxes
        var boxToShow = $( "#paymentProcessorDropdown option:selected" ).val();
        $('#' + boxToShow + '_box').show();

        $('#paymentProcessorDropdown').change(function()
        {
            $('.paymentOptionsBox').hide(); //Hide all payment options boxes

            //Show the currently selected box
            var boxToShow = $( "#paymentProcessorDropdown option:selected" ).val();
            $('#' + boxToShow + '_box').show();
        });

        window.setTimeout(function() {
          $(".fadeAway").fadeTo(500, 0).slideUp(500, function(){
              $(this).remove();
          });
        }, 5000);
    });
    </script>
@stop
<!-- END JAVASCRIPT INCLUDES -->