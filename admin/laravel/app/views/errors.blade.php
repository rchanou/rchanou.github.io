@if (count($errors) > 0)
<div class="alert alert-danger alert-dismissable" role="alert" style="width: 450px; margin: 1em auto 2em;">
    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    @foreach ($errors->all('<li>:message</li>') as $message)
    {{ $message }}
    @endforeach
</div>
@endif