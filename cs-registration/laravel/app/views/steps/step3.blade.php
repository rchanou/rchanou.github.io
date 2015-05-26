@extends('master')

<!-- BEGIN CSS INCLUDES -->
@section('css_includes')
    @parent
    <link rel="stylesheet" href="js/vendors/signature-pad/assets/jquery.signaturepad.css" />
@stop
<!-- END CSS INCLUDES -->

<!-- HEADER -->
@section('backButton')
<a href="step2" class="arrow"><span>{{$strings['str_backButton']}}</span></a>
@stop

@section('headerTitle')
{{$strings['str_step3HeaderTitle']}}
@stop

@section('languagesDropdown')

@stop
<!-- END HEADER -->

<!-- PAGE TITLE -->
@section('title')
{{$strings['str_step3PageTitle']}}
@stop
<!-- END PAGE TITLE -->

@section('facebook_integration')
@stop

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
<div class="termsCheckboxArea">
    <input type="checkbox" name="iagree" id="iagree" class="iAgreeCheckbox">{{$strings['str_termsAndConditionsCheckBox']}}
</div>
<!-- BEGIN SIGNING POP-UP -->
<div class="modal" id="signModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content modalContent">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel" style="color: black">{{$strings['str_signHere']}}</h4>
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
                {{ Form::open(array('action' => 'Step3Controller@postStep3', 'files' => 'true', 'style' => '', 'class' => 'sigPad')) }}
                    <ul class="sigNav sigNavInner">
                        <li class=clearButton><a href="#clear" class="btn btn-warning btn-lg clearButtonStyle" style="color: white; text-decoration: none">{{$strings['str_clearSignature']}}</a></li>
                    </ul>
                    <div class="sig sigWrapper" height="483">
                    @if (Session::get('screenSize') == 'small')
                    <canvas class="pad innerSigPad" width=280 height=200></canvas>
                    @else
                    <canvas class="pad innerSigPad" width=850 height=478></canvas>
                    @endif
                        <input type=hidden name=signatureOutput class=output> </div>
                    <button type="button" data-dismiss="modal" style="text-align: center; background-color: #7a0000; color: white" class="cancelSigningButton">{{$strings['str_cancelSigning']}}</button>
                    <button type=submit style="text-align: center; background-color: #008000; color: white" class="startSigningButton" id="startSigningButton">{{$strings['str_startSigning']}}</button>
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

@stop
<!-- END PAGE CONTENT -->

<!-- FOOTER -->

@section('leftFooterButton')
<a href="{{$step1URL}}" id="idisagreeButton" class="btn btn-danger btn-lg leftButton" onclick="$('#loadingModal').modal();">{{$strings['str_step3DoNotAgree']}}</a>
@stop

@section('rightFooterButton')
    @if ($settings['CfgRegUseMsign'] && (Session::get('isMinor') == "false" || $settings['cfgRegAllowMinorToSign'] || Config::get('config.minorSignatureWithParent')) )
    <button id="iagreeButton" class="btn btn-success btn-lg rightButton disabled" data-toggle="modal" data-target="#signModal">
        {{$strings['str_buttonDisabledText']}}
    </button>
    <input type="hidden" name="signatureRequired" id="signatureRequired" value="true">
    @else
{{ Form::open(array('action' => 'Step3Controller@postStep3')) }}
<button id="iagreeButton" onclick="$('#loadingModal').modal(); $('#iagreeButton').addClass('disabled');" class="btn btn-success btn-lg rightButton disabled">
    {{$strings['str_buttonDisabledText']}}
</button>
    <input type="hidden" name="signatureRequired" id="signatureRequired" value="false">
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
            $('.sigPad').signaturePad({drawOnly:true, lineTop: 239, lineColour: '#000000', lineWidth: 10
            ,onDrawEnd: function(){
                $('#startSigningButton').prop('disabled', false);
            }
            ,onBeforeValidate: function(){
                 $('#startSigningButton').prop('disabled', true);
            }

            });

        }
        else
        {
            $('.sigPad').signaturePad({drawOnly:true, lineTop: 428
            ,onDrawEnd: function(){
                $('#startSigningButton').prop('disabled', false);
            }
            ,onBeforeValidate: function(){
                 $('#startSigningButton').prop('disabled', true);
            }
            });
        }


        $('input:checkbox').prop('checked', false);

    });

    $(function() {
        $('#iagree').click(function() {
            var signatureRequired = $('#signatureRequired').val();
            if ($(this).is(':checked')) {
                $('#iagreeButton').removeClass('disabled');
                if (signatureRequired == "true")
                {
                    $('#iagreeButton').html("{{$strings['str_step3Agree']}}");
                }
                else
                {
                    $('#iagreeButton').html("{{$strings['str_step3AgreeNoSig']}}");
                }
            } else {
                $('#iagreeButton').addClass('disabled');
                $('#iagreeButton').html("{{$strings['str_buttonDisabledText']}}");
            }
        });
    });
</script>
@stop
<!-- END JAVASCRIPT INCLUDES -->