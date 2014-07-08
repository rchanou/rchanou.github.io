@extends('master')

<!-- BEGIN CSS INCLUDES -->
@section('css_includes')
    @parent
    <link rel="stylesheet" href="js/vendors/signature-pad/assets/jquery.signaturepad.css" />
@stop
<!-- END CSS INCLUDES -->

<!-- HEADER -->
@section('backButton')
<a href="step2" class="arrow"><span>{{$strings['backButton']}}</span></a>
@stop

@section('headerTitle')
{{$strings['step3HeaderTitle']}}
@stop

@section('languagesDropdown')

@stop
<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')
{{$strings['step3PageTitle']}}
@stop
<!-- END PAGE TITLE -->

<!-- PAGE CONTENT -->
@section('content')

<!--<img src="{{--$formInput['cameraInput']--}}">-->

<div class="termsBox">
    @if (Session::get('isMinor') == "false")
        {{nl2br($settings['Waiver1'])}}
    @else
        {{nl2br($settings['Waiver2'])}}
    @endif
</div>

<!-- BEGIN SIGNING POP-UP -->
<div class="modal fade" id="signModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="height: 650px;">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel" style="color: black">{{$strings['signHere']}}</h4>
            </div>
            <div class="modal-body">
                @if(Config::get('config.minorSignatureWithParent') && Session::get('isMinor') == "true")
                <div style="position: relative; width: 0; height: 0">
                    <div style="position: absolute; top: 40px; left: 10px; color: black; font-size: 18px; z-index: 999">
                        Parent/Guardian
                    </div>
                </div>
                <div style="position: relative; width: 0; height: 0">
                    <div style="position: absolute; top: 284px; left: 10px; color: black; font-size: 18px; z-index: 999">
                        Participant
                    </div>
                </div>
                @endif
                {{ Form::open(array('action' => 'RegistrationController@postStep3', 'files' => 'true', 'style' => '', 'class' => 'sigPad')) }}
                    <ul class=sigNav>
                        <li class=clearButton><a href="#clear" class="btn btn-warning btn-lg" style="color: white; text-decoration: none">{{$strings['clearSignature']}}</a></li>
                    </ul>
                    <div class="sig sigWrapper" style="height: 483px;"> <canvas class="pad" width=850 height=478></canvas>
                        <input type=hidden name=signatureOutput class=output> </div>
                    <button type="button" data-dismiss="modal" style="width: 425px; text-align: center; background-color: #7a0000; color: white">{{$strings['cancelSigning']}}</button>
                    <button type=submit onclick="$('#loadingModal').modal();" style="width: 425px; text-align: center; background-color: #008000; color: white">{{$strings['startSigning']}}</button>
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>
<!-- END SIGNING POP-UP -->

@if(Config::get('config.minorSignatureWithParent') && Session::get('isMinor') == "true")
<input type="hidden" name="minorSignatureWithParent" id="minorSignatureWithParent" value="true">
@else
<input type="hidden" name="minorSignatureWithParent" id="minorSignatureWithParent" value="false">
@endif

<p/>

@stop
<!-- END PAGE CONTENT -->

<!-- FOOTER -->

@section('leftFooterButton')
<a href="step1" class="btn btn-danger btn-lg leftButton" onclick="$('#loadingModal').modal();">{{$strings['step3DoNotAgree']}}</a>
@stop

@section('rightFooterButton')
    @if (Session::get('isMinor') == "false" || $settings['cfgRegAllowMinorToSign'] || Config::get('config.minorSignatureWithParent'))
    <button class="btn btn-success btn-lg rightButton" data-toggle="modal" data-target="#signModal">
        {{$strings['step3Agree']}}
    </button>
    @else
{{ Form::open(array('action' => 'RegistrationController@postStep3')) }}
<button onclick="$('#loadingModal').modal();" class="btn btn-success btn-lg rightButton">
    {{$strings['step3AgreeNoSig']}}
</button>
{{ Form::close() }}
    @endif
@stop

<!-- END FOOTER -->

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
    @parent

<!--[if lt IE 9]><script src="js/vendors/signature-pad/assets/flashcanvas.js"></script><![endif]-->
<script src="js/vendors/signature-pad/jquery.signaturepad.min.js"></script>
<script src="js/vendors/signature-pad/assets/json2.min.js"></script>

<script>
    $(document).ready(function () {
        var minorSignatureWithParent = $('#minorSignatureWithParent').val();
        if (minorSignatureWithParent == 'true')
        {
            $('.sigPad').signaturePad({drawOnly:true, lineTop: 239, lineColour: '#000000', lineWidth: 10});

        }
        else
        {
            $('.sigPad').signaturePad({drawOnly:true, lineTop: 428});
        }
        //minorSignatureWithParent
    });
</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->