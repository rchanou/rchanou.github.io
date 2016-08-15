@extends('master')

<!-- HEADER -->
@section('backButton')
<a href="{{URL::to("checkin")}}" class="arrow" onclick="$('#loadingModal').modal();"><span>{{$strings['str_backButton']}}</span></a>

@stop

@section('headerTitle')
{{$strings['str_connectFacebook']}}
@stop

@section('languagesDropdown')

@stop
<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')

@stop
<!-- END PAGE TITLE -->


<!-- PAGE CONTENT -->
@section('content')


@if (count($errors) > 0)

    <script>
        var errorMessages = {{$errors->toJson()}};
        var errorString = "";
        for (var key in errorMessages) {
            if (errorMessages.hasOwnProperty(key)) {
                errorString = errorString + errorMessages[key] + "\n";
            }
        }
        alert(errorString);
    </script>

@endif


<div class="row" style="margin-bottom: 50px; margin-top: 50px; font-size: 18px;">
    <div class="col-sm-3"></div>
    <div class="col-sm-6 text-center">
    {{$strings['str_connectFacebookDisclaimer']}}
    </div>
    <div class="col-sm-3"></div>
</div>
<div class="row formArea">
        @if ($settings['Reg_EnableFacebook'])
        <div class="col-sm-6 text-center">
            <a href="{{URL::to("checkinconfirm")}}" style="font-size: 20px;">
                <img src="{{asset($images['checkIn'])}}"><br/>
                {{$strings['str_connectFacebookNo']}}
            </a>
        </div>
        <div class="col-sm-6 text-center" style="font-size: 20px;">
            <a href="https://www.facebook.com/dialog/oauth?client_id=296582647086963&redirect_uri={{str_replace('checkin','checkinconfirm',Request::url())}}&scope=public_profile,email,user_birthday,publish_actions">
                <img src="{{asset($images['createAccountFacebook'])}}"><br/>
            {{$strings['str_connectFacebookYes']}}
            </a>
        </div>
        @else
        <div class="col-sm-12 text-center">
            <a href="{{URL::to("checkinconfirm")}}" style="font-size: 20px;">
                <img src="{{asset($images['checkIn'])}}"><br/>
                {{$strings['str_connectFacebookNo']}}
            </a>
        </div>
        @endif
</div>
@stop
<!-- END PAGE CONTENT -->

<!-- FOOTER -->

@section('leftFooterButton')
@stop

@section('rightFooterButton')

@stop

<!-- END FOOTER -->