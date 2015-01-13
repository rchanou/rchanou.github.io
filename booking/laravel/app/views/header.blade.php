
        <div class="header">
            <a href="{{URL::to('step1')}}"><img src="{{asset($images['header'])}}" width="610" height="90"></a>
        </div>
        @yield('steps')
        @if(Session::has('debug'))
                <div class="extras">
                Debug mode enabled! {{link_to('giftcards','Buy Gift Cards (TEST)')}} - {{link_to('login','Login (TEST)')}}
                </div>
        @endif
        @if(Session::get('giftCardSalesEnabled'))
                <div class="extras">
                        {{link_to('giftcards',$strings['str_buyGiftCards'])}}
                </div>
        @endif