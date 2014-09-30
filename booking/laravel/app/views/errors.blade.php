
@if (count($errors) > 0)

<div class="alert alert-danger alert-dismissable bookingError" role="alert">
    <button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
    @foreach ($errors->all() as $message)
    {{ $message }}<br/>
    @endforeach
</div>

@endif