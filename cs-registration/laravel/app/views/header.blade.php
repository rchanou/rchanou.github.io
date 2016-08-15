<div class="row topHeader">
    <div class="col-xs-3 topLeftBackArrow">@yield('backButton')</div>
    <div class="col-xs-6 topCenterHeader">@yield('headerTitle')</div>
    <div class="col-xs-3 topRightDropdown">
        <dl id="languageDropdown" class="dropdown languageDropdown" @if(count($translations) <= 1) style="visibility: hidden;" @endif>
            <dt><a href="#"><span><span class="currentCultureString" style="display: inline-block">{{$strings["cultureNames"][$currentCulture]}}</span> <img src="{{asset("images/flags/" . strtolower(substr($currentCulture,3,2)) . ".png")}}" class="flag"></span></a></dt>
            <dd>
                <ul>
                    @foreach ($strings["cultureNames"] as $currentCultureCode => $currentCulture)
                        @if (array_key_exists($currentCultureCode,$translations))
                            <li><a href="#"><span class="currentCultureString">{{$currentCulture}}</span><img class="flag" src="{{asset("images/flags/" . strtolower(substr($currentCultureCode,3,2)) . ".png")}}" alt="" />
                            <span class="value">{{$currentCultureCode}}</span></a></li>
                        @endif
                    @endforeach
                </ul>
            </dd>
        </dl>
    </div>
</div>

<!-- BEGIN LOADING POP-UP -->
<div class="modal fade" id="loadingModal" tabindex="-1" role="dialog" aria-labelledby="loadingModalTitle" aria-hidden="true" style="z-index: 9999">
    <div class="modal-dialog modal-sm" id="loading">
        <div class="modal-content" style="background-color: black; color: white; border: 2px solid white;">
            <div class="modal-header" style=" border-color: black">
                <img src="{{asset("images/clubspeed_logo.png")}}">
            </div>
            <div class="modal-body">
                <div class="spinner">
                    <div class="rect1" style="background-color: red;"></div>
                    <div class="rect2" style="background-color: red;"></div>
                    <div class="rect3" style="background-color: red;"></div>
                    <div class="rect4" style="background-color: red;"></div>
                    <div class="rect5" style="background-color: red;"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END LOADING POP-UP -->
