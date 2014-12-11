<!-- Footer -->
    @if(Session::has('authenticated'))
        <div id="loggedInAs">
        {{$strings['str_youAreLoggedInAs']}} <strong>{{Session::get('authenticatedEmail')}}</strong>.<br/><a href="logout">{{$strings['str_logout']}}</a><p/><p/>
        </div>
    @endif

    <div class="text-center" style="margin-bottom: 5px; font-size: 80%">
    © CLUB SPEED, INC. 2006-{{date('Y')}}
    </div>

<!-- BEGIN LOADING POP-UP -->
<div class="modal fade loadingModal" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-sm" id="loading">
        <div class="modal-content loadingModalInner">
            <div class="modal-header loadingModalHeader">
                <img src="{{asset($images['clubspeed_logo'])}}" class="loadingModalImage">
            </div>
            <div class="modal-body">
                <div class="spinner">
                    <div class="rect1 redRectangle"></div>
                    <div class="rect2 redRectangle"></div>
                    <div class="rect3 redRectangle"></div>
                    <div class="rect4 redRectangle"></div>
                    <div class="rect5 redRectangle"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END LOADING POP-UP -->