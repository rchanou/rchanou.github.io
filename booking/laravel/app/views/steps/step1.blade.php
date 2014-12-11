@extends('master')

<!-- PAGE TITLE -->
@section('title')
    {{$strings['str_step1Title']}}
@stop
<!-- END PAGE TITLE -->

<!-- PAGE CONTENT -->
@section('steps')
<div class="steps">
    <em>{{$strings['str_seeTheLineup']}}</em> >
        @if(Session::has('lastSearch'))
            {{link_to('step2',$strings['str_chooseARace'])}} >
        @else
            {{$strings['str_chooseARace']}} >
        @endif

        @if(Session::has('authenticated'))
            {{link_to('cart',$strings['str_reviewYourOrder'])}}
        @else
            {{$strings['str_reviewYourOrder']}}
        @endif
        @if(Session::has('authenticated') && Session::has('cart') && count(Session::get('cart')) > 0)
        > {{link_to('checkout',$strings['str_checkout'])}}
        @else
        > {{$strings['str_checkout']}}
        @endif
</div>
@stop

@section('content')
<div class="mainBodyContent">
    <form action="step2" method="post" class="contentBox">
        <div class="step1form">
            <div class="inputRow">
                  <span class="inputLabel">
                        {{$strings['str_chooseYourDate']}}
                  </span>
                  <span class="inputData">
                        <input type="date" name="start" id="raceDatePicker" onchange="updateRaceTypesAvailable()">
                  </span>
            </div>
            <div class="inputRow">
                  <span class="inputLabel">
                        {{$strings['str_howManyDrivers']}}
                  </span>
                  <span class="inputData">
                        <select name="numberOfParticipants" id="numberOfParticipants" onchange="updateRaceTypesAvailable()">
                            @for($i=1; $i < $maxRacers+1; ++$i)
                                @if ($i == 1)
                                    <option value="{{$i}}" selected="selected">{{$i}}</option>
                                @else
                                    <option value="{{$i}}">{{$i}}</option>
                                @endif
                            @endfor
                        </select>
                  </span>
            </div>
            <div class="inputRow">
                  <span class="inputLabel">
                        {{$strings['str_whatTypeOfRace']}}
                  </span>
                  <span class="inputData">
                      <select name="heatType" id="heatTypeDropdown">
                          @if($heatTypes != null)
                              @foreach($heatTypes as $heatType)
                                <option value="{{$heatType['heatTypeId']}}">{{$heatType['name']}}</option>
                              @endforeach
                          @endif
                      </select>
                  </span>
            </div>
            <div class="rightAligned">
                <button type="submit" class="formButton">{{$strings['str_search']}}</button>
            </div>
        </div>
    </form>
</div>
@stop
<!-- END PAGE CONTENT -->

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
    @parent
<script>
    /**
     * This function allows a Date object to convert itself to the same
     * date format expected by an HTML5 date input.
     * @type {Function}
     */
    Date.prototype.toDateInputValue = (function() {
        var local = new Date(this);
        local.setMinutes(this.getMinutes() - this.getTimezoneOffset());
        return local.toJSON().slice(0,10);
    });

    //When the document loads, default the datepicker to today's date
    $(document).ready(function() {
        $('#raceDatePicker').val(new Date().toDateInputValue());

        Date.prototype.yyyymmdd = function() {
            var yyyy = this.getFullYear().toString();
            var mm = (this.getMonth()+1).toString(); // getMonth() is zero-based
            var dd  = this.getDate().toString();
            return yyyy + '-' + (mm[1]?mm:"0"+mm[0]) + '-' + (dd[1]?dd:"0"+dd[0]); // padding
        };

    });

    /**
     * This function is called whenever a new date is selected and the user clicks anywhere else.
     * It issues a call to the Club Speed API to update the dropdown of available race types for the new
     * selected date.
     */
    function updateRaceTypesAvailable()
    {
        var newDate = $('#raceDatePicker').val();
        var today = new Date().yyyymmdd();
        var numOfRacers = parseInt($('#numberOfParticipants option:selected').val());

        <?php $apiURL = str_replace('http://','https://', Config::get('config.apiURL')); ?>

        var apiURL = '{{$apiURL}}/bookingavailability/range.json?key={{Config::get('config.privateKey')}}' + (newDate == today ? '' : '&start=' + newDate);

        jQuery.getJSON(apiURL, function(data){

            $('#heatTypeDropdown option[value != -1]').remove();
            if (data.hasOwnProperty("bookings"))
            {
                var onlineBookings = data["bookings"];

                $.each(onlineBookings, function(index, currentOnlineBooking) {
                    var bookingType = currentOnlineBooking["heatTypeId"];
                    if(currentOnlineBooking['heatSpotsAvailableOnline'] >= numOfRacers && currentOnlineBooking['isPublic'] && $("#heatTypeDropdown option[value='" + bookingType + "']").length == 0)
                    {
                        $('#heatTypeDropdown')
                            .append($("<option></option>")
                                .attr("value", currentOnlineBooking["heatTypeId"])
                                .text(currentOnlineBooking["heatDescription"]));
                    }
                });
            }
        });
    }
</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->

