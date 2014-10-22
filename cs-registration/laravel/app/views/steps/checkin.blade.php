@extends('master')

<!-- HEADER -->
@section('backButton')
<a href="{{Session::has('ipcam') ? 'step1' . '?&terminal=' . Session::get('ipcam') : 'step1' }}" class="arrow" onclick="$('#loadingModal').modal();"><span>{{$strings['str_backButton']}}</span></a>

@stop

@section('headerTitle')
{{$strings['str_checkIn']}}
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

{{ Form::open(array('action' => 'CheckInController@postCheckIn', 'files' => 'true', 'style' => '')) }}

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


<div class="row formArea">
    <div class="col-sm-4">
        <div class="centered">
            {{ Form::label('firstname', $strings['str_firstname'] . ':') }}
            <span class="requiredAsterisk">*</span><br/>
            {{ Form::text('firstname','',array('class'=>'required','maxlength'=>'50')) }}<p/>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="centered">
            {{ Form::label('lastname', $strings['str_lastname'] . ':') }}
        <span class="requiredAsterisk">*</span><br/>
        {{ Form::text('lastname','',array('class'=>'required','maxlength'=>'50')) }}<p/>
        </div>
    </div>
    <div class="col-sm-4">
        <div class="centered">
            {{ Form::label('birthdate', $strings['str_birthdate'] . ':') }}
            <span class="requiredAsterisk">*</span><br/>
            <input style="line-height: normal !important" type="date" name="birthdate" id="birthdate" class="required" value=""><p/>
        </div>
    </div>
</div>
@stop
<!-- END PAGE CONTENT -->

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
    @parent

    <!-- LIVE VALIDATION SCRIPT -->
    <script>
    $(document).ready(function() {
            if ($('#firstname').length > 0 && $('#firstname').hasClass('required'))
            {
                var firstname = new LiveValidation('firstname');
                firstname.add( Validate.Presence,
                    { failureMessage: "{{$strings['str_required']}}" } );
            }

            if ($('#lastname').length > 0 && $('#lastname').hasClass('required'))
            {
                var lastname = new LiveValidation('lastname');
                lastname.add( Validate.Presence,
                    { failureMessage: "{{$strings['str_required']}}" } );

            }

    });
    </script>
@stop

<!-- FOOTER -->

@section('leftFooterButton')
{{ Form::reset($strings['str_checkInClear'], array('class'=>'leftButton btn btn-danger btn-lg', 'id'=>'resetButton')) }}
@stop

@section('rightFooterButton')
{{ Form::submit($strings['str_checkInSubmit'], array('class'=>'rightButton btn btn-success btn-lg', 'id'=>'submitButton')) }}
{{ Form::close() }}
@stop

<!-- END FOOTER -->