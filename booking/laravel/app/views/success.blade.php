@extends('master')

<!-- HEADER -->

<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')
{{$strings['str_successTitle']}}
@stop
<!-- END PAGE TITLE -->


<!-- PAGE CONTENT -->
@section('steps')
<div class="steps">
    <div>{{link_to('step1',$strings['str_seeTheLineup'])}} > {{$strings['str_chooseARace']}} > {{$strings['str_reviewYourOrder']}}
        @if(Session::has('authenticated') && Session::has('cart') && count(Session::get('cart')) > 0)
        > {{link_to('checkout',$strings['str_checkout'])}}
        @else
        > {{$strings['str_checkout']}}
        @endif
    </div>
</div>
@stop

@section('content')

<div class="mainBodyContent">
    <div class="centered">
        <h2>{{$strings['str_success']}}</h2>
        <img src="{{asset($images['success'])}}"><p/>
        <h4>{{$strings['str_thankYouForYourOrder']}}<br/>
            {{$strings['str_weWillSeeYouOnTheTrack']}}</h4>

            {{$strings['str_yourPaymentConfirmationNumberIs']}} {{$checkId}}.<p/>

            {{$strings['str_pleasePrintThisPageForYourRecords']}}<br/>
    </div>


</div>

@stop
<!-- END PAGE CONTENT -->

<!-- FOOTER -->

<!-- END FOOTER -->

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
@parent

@stop
<!-- END JAVASCRIPT INCLUDES -->

