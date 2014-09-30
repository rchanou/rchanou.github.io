<!-- Footer -->
    @if(Session::has('authenticated'))
        <div id="loggedInAs">
        You are logged in as <strong>{{Session::get('authenticatedEmail')}}</strong>.<br/><a href="logout">Logout</a><p/><p/>
        </div>
    @endif

    <div class="well" style="margin: 20px;">
        Thanks for checking out the Online Booking beta!<p/>

        It's currently set to <strong>test mode</strong>, and will not charge customers.<p/>

        Feedback or bug reports? Contact us at <a href="mailto:support@clubspeed.com">support@clubspeed.com</a>.
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