<?php
   //Defined in advance here due to PHP 5.3 limitations with array dereferencing.
    $currentCulture = Session::get('currentCulture');
    $translations = Session::get('translations');
    $settings = Session::get('settings');

?>
        @if($settings['showLanguageDropdown'])
        <div class="languageDropdownContainer-responsive clearfix">
            <dl id="languageDropdown" class="dropdown languageDropdown-responsive" style="margin-bottom: 0px; float: right;">
                <dt>
                    <a href="#">
                        <span>
                            <span class="currentCultureString" style="display: inline-block">{{$strings["cultureNames"][$currentCulture]}}</span>
                            <img src="images/flags/{{strtolower(substr($currentCulture,3,2))}}.png" class="flag">
                        </span>
                    </a>
                </dt>
                <dd>
                    <ul>
                        @foreach ($strings["cultureNames"] as $currentCultureCode => $currentCulture)
                            @if (array_key_exists($currentCultureCode,$translations))
                                <li>
                                    <a href="#">
                                        <span class="currentCultureString">{{$currentCulture}}</span>
                                        <img class="flag" src="images/flags/{{strtolower(substr($currentCultureCode,3,2))}}.png" alt="" />
                                        <span class="value">{{$currentCultureCode}}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </dd>
            </dl>
        </div>

        <script>
            $(document).ready(function() {
                $(".dropdown dt a").click(function() {
                $(".dropdown dd ul").toggle();
                });

            $(".dropdown dd ul li a").click(function() {
                var text = $(this).html();
                $(".dropdown dt a span").html(text);
                $(".dropdown dd ul").hide();
                $('#loadingModal').modal();

                if ( (window.location.href).indexOf('/www') == -1)
                {
                    window.location.href = (window.location.protocol) + '//' + (window.location.hostname) + '/booking/changeLanguage/' + getSelectedValue("languageDropdown") + '/step1';
                }
                else
                {
                    window.location.href = (window.location.protocol) + '//' + (window.location.hostname) + '/booking/www/changeLanguage/' + getSelectedValue("languageDropdown") + '/step1';
                }

                });

            function getSelectedValue(id) {
                return $("#" + id).find("dt a span.value").html();
                }

            $(document).bind('click', function(e) {
                var $clicked = $(e.target);
                if (! $clicked.parents().hasClass("dropdown"))
                $(".dropdown dd ul").hide();
                });

            });
        </script>
        @endif