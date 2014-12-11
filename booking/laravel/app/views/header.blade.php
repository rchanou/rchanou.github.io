
        <div class="header">
            <a href="{{URL::to('step1')}}"><img src="{{asset($images['header'])}}" width="610" height="90"></a>
        </div>
        @yield('steps')
        @if(Session::has('debug'))
        <div class="extras">
        Extras: {{link_to('#','Buy Gift Cards')}} - {{link_to('login','Login (TEST)')}}
        </div>
        @endif