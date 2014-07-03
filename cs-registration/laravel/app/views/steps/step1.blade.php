@extends('master')

<!-- HEADER -->
@section('backButton')

@stop

@section('headerTitle')
<h2>{{$strings['step1HeaderTitle']}}</h2>
@stop

@section('languagesDropdown')

@stop
<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')
{{$strings['step1PageTitle']}}
@stop
<!-- END PAGE TITLE -->

<!-- PAGE CONTENT -->
@section('content')

<!-- VENUE LOGO AND HEADER TEXT -->
<div class="row">
    <div class="col-xs-12 text-center"><img src="{{$images['venueLogo']}}"></div>
</div>
<div class="row" style="margin-top: 50px; font-size: 20px;">
    <div class="col-xs-12 text-center">{{$strings['welcomeMessage']}}</div>
</div>
<!-- END VENUE LOGO AND HEADER TEXT -->

<!-- REGISTRATION OPTIONS  -->
<div>
    <div class="row step1RegistrationHeader">
        <div class="col-xs-12 text-center"><h1>{{$strings['registerHeader']}}</h1></div>
    </div>
    <div class="row step1RegistrationBody">
        @if ($settings['Reg_EnableFacebook'])
        <div class="col-sm-6 text-center">
            <a href="step2" style="font-size: 20px;">
                <img src="{{$images['createAccount']}}"><br/>
                {{$strings['newAccount']}}
            </a>
        </div>
        <div class="col-sm-6 text-center" style="font-size: 20px;">
            <a href="https://www.facebook.com/dialog/oauth?client_id=296582647086963&redirect_uri={{str_replace('step1','step2',Request::url())}}&scope=public_profile,email,user_birthday">

                <img src="{{$images['createAccountFacebook']}}"><br/>
            {{$strings['facebook']}}
            </a>
        </div>
        @else
        <div class="col-sm-12 text-center" style="font-size: 20px;">
            <a href="step2">
                <img src="{{$images['createAccount']}}"><br/>
                {{$strings['newAccount']}}
            </a>
        </div>
        @endif
    </div>
    <div class="row">
        <div class="col-sm-12 centered">
            <img src="{{$images['poweredByClubSpeed']}}" style="padding-top: 30px;">
        </div>
    </div>
</div>
<!-- END REGISTRATION OPTIONS  -->

@stop
<!-- END PAGE CONTENT -->