<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- BEGIN CSS INCLUDES -->
    @section('css_includes')
    {{ HTML::style('css/bootstrap.min.css') }}
    {{ HTML::style('css/font-awesome.min.css') }}
    {{ HTML::style('css/fullcalendar.css') }}
    {{ HTML::style('css/jquery.jscrollpane.css') }}
    {{ HTML::style('css/icheck/flat/blue.css') }}
    {{ HTML::style('css/select2.css') }}
    {{ HTML::style('css/unicorn.css') }}
    {{ HTML::style('css/admin.css') }}
    <!--[if lt IE 9]>
    {{ HTML::script('js/respond.min.js')}}
    <![endif]-->
    @show
    <!-- END CSS INCLUDES -->

	<title>@yield('title', 'Admin Panel')</title>
</head>

<body data-color="grey" class="flat">

<!-- BEGIN MAIN PAGE CONTAINER -->
<div id="wrapper">
    <div id="header">
        <h1><a href="{{URL::to('dashboard')}}">@yield('pageHeader','REPLACE_PAGE_TITLE')</a></h1>
        <a id="menu-trigger" href="#"><i class="fa fa-bars"></i></a>
    </div>

    <div id="user-nav">
        <ul class="btn-group">
            <li class="btn"><a href="#">Logged in as: {{Session::get('user')}}</a></li>
            <li class="btn"><a title="" href="{{action('LoginController@logout')}}"><i class="fa fa-share"></i> <span class="text">Logout</span></a></li>
        </ul>
    </div>

    <div id="sidebar">
        <!--<div id="search">
            <input type="text" placeholder="Search here..."/><button type="submit" class="tip-right" title="Search"><i class="fa fa-search"></i></button>
        </div>-->
        <ul>
            <li><a href="{{URL::to('dashboard')}}"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
            <!--<li class="submenu">
                <a href="#"><i class="fa fa-flask"></i> <span>Sub-Menu 1</span> <i class="arrow fa fa-chevron-right"></i></a>
                <ul>
                    <li><a href="#">Sub Item</a></li>
                    <li><a href="#">Sub Item</a></li>
                    <li><a href="#">Sub Item</a></li>
                </ul>
            </li>
            <li><a href="#"><i class="fa fa-th"></i> <span>Direct Link Item</span></a></li>-->
            @if (@$controller == 'ChannelController')
            <li class="active open submenu">
            @else
            <li class="submenu">
            @endif
                <a href="#"><i class="fa fa-desktop"></i> <span>Speed Screens</span> <i class="arrow fa fa-chevron-right"></i></a>
                <ul>
                    <li>{{link_to('/channel','Channels')}}</li>
                    <li>{{link_to('/channelSettings','Settings')}}</li> <!-- TODO: Rename to channel/settings -->
                    <li>{{link_to('/speedScreen/translations','Translations')}}</li>
                    <li>{{link_to('/docs/Club Speed - Speed Screen Guide.pdf','Documentation', array('target' => '_blank'))}}</li>
                </ul>
            </li>
            @if (@$controller == 'BookingController')
            <li class="active open submenu">
            @else
            <li class="submenu">
            @endif
                <a href="#"><i class="fa fa-calendar"></i> <span>Online Bookings</span> <i class="arrow fa fa-chevron-right"></i></a>
                <ul>
                    <li>{{link_to('/booking','Manage Bookings')}}</li>
                    <li>{{link_to('/booking/settings','Settings')}}</li>
                    <li>{{link_to('/booking/payments','Payment Processors')}}</li>
                    <li>{{link_to('/booking/templates','Templates')}}</li>
                    <li>{{link_to('/booking/giftcardsales','Gift Card Sales')}}</li>
                    <li>{{link_to('/booking/translations','Translations')}}</li>
										<li>{{link_to('/booking/logs','Logs')}}</li>
                    <li>{{link_to('/docs/Club Speed - Online Booking Guide.pdf','Documentation',array('target' => '_blank'))}}</li>
                </ul>
            </li>
            @if (@$controller == 'RegistrationController')
            <li class="active open submenu">
            @else
            <li class="submenu">
            @endif
                <a href="#"><i class="fa fa-tablet"></i> <span>Registrations</span> <i class="arrow fa fa-chevron-right"></i></a>
                <ul>
                    <li>{{link_to('/registration/settings','Settings')}}</li>
                    <li>{{link_to('/registration/translations', 'Translations')}}</li>
                    <li>{{link_to('/registration/waivers', 'Waivers')}}</li>
                    @if(Session::has('user') && strtolower(Session::get('user')) == 'support')
                    <li>{{link_to('/registration/create', 'Create Application')}}</li>
                    @endif
                    <li>{{link_to('/docs/Club Speed - Club Speed Registration Kiosk Guide.pdf','Documentation', array('target' => '_blank'))}}</li>
                </ul>
            </li>
            @if (@$controller == 'ReportsController')
            <li class="active open">
            @else
            <li>
            @endif
                <a href="{{URL::to('reports')}}"><i class="fa fa-file-o"></i> <span>Reports</span></a>
            </li>
            @if (@$controller == 'MobileAppController')
            <li class="active open">
            @else
            <li class="submenu">
            @endif
                <a href="#"><i class="fa fa-mobile"></i> <span>Mobile App</span> <i class="arrow fa fa-chevron-right"></i></a>
                <ul>
                    <li>{{link_to('/mobileApp/menuItems','Menu Items')}}</li>
                    <li>{{link_to('/mobileApp/settings','Settings')}}</li>
                    <li>{{link_to('/mobileApp/translations','Translations')}}</li>
                    <li>{{link_to('/mobileApp/templates','Templates')}}</li>
                    <li>{{link_to('/docs/Club Speed - iOS Application Instructions.pdf','Setup Instructions', array('target' => '_blank'))}}</li>
                </ul>
            </li>
            @if (@$controller == 'GiftCardsController')
                <li class="active open">
            @else
                <li class="submenu">
            @endif
                    <a href="#"><i class="fa fa-credit-card"></i> <span>Gift Cards</span> <i class="arrow fa fa-chevron-right"></i></a>
                    <ul>
                        <li>{{link_to('/giftcards/manage','Manage')}}</li>
                        <li>{{link_to('/giftcards/reports','Reports')}}</li>
                    </ul>
                </li>
						@if (@$controller == 'FacebookController')
							<li class="active open">
						@else
							<li class="submenu">
						@endif
							<a href="#"><i class="fa fa-facebook-square"></i> <span>Facebook</span> <i class="arrow fa fa-chevron-right"></i></a>
							<ul>
								<li>{{link_to('/facebook/after-race-settings','After Race Posting Settings')}}</li>
                <li>{{link_to('/facebook/logs','Logs')}}</li>
							</ul>
						</li>
           	@if (@$controller == 'SpeedTextController')
							<li class="active open">
						@else
							<li class="submenu">
						@endif
							<a href="#"><i class="fa fa-comments"></i> <span>SpeedText</span> <i class="arrow fa fa-chevron-right"></i></a>
							<ul>
								<li>{{link_to('/speedtext/settings','Settings')}}</li>
                <li>{{link_to('/speedtext/logs','Logs')}}</li>
							</ul>
						</li>
        </ul>

    </div>

    <div id="content">
        <div id="content-header" class="mini">
            <h1>@yield('pageHeader','REPLACE_PAGE_TITLE')</h1>
        </div>

        <div id="breadcrumb">
            @yield('breadcrumb')
        </div>

        <!-- BEGIN MAIN CONTENT -->
        <div class="container-fluid">

            @yield('content')

        </div>
        <!-- END MAIN CONTENT -->
    </div>

    <!-- START FOOTER INCLUDE -->
    @include('footer')
    <!-- END FOOTER INCLUDE -->

</div>
<!-- END MAIN PAGE CONTAINER -->

<!-- BEGIN JAVASCRIPT INCLUDES -->
@section('js_includes')
{{ HTML::script('js/excanvas.min.js') }}
{{ HTML::script('js/jquery.min.js') }}
{{ HTML::script('js/jquery-ui.custom.js') }}
{{ HTML::script('js/bootstrap.min.js') }}
{{ HTML::script('js/jquery.flot.min.js') }}
{{ HTML::script('js/jquery.flot.resize.min.js') }}
{{ HTML::script('js/jquery.sparkline.min.js') }}
{{ HTML::script('js/fullcalendar.min.js') }}
{{ HTML::script('js/jquery.icheck.min.js') }}
{{ HTML::script('js/select2.min.js') }}
{{ HTML::script('js/jquery.nicescroll.min.js') }}
{{ HTML::script('js/unicorn.js') }}
{{ HTML::script('js/unicorn.form_common.js') }}
{{ HTML::script('js/unicorn.dashboard.js') }}
{{ HTML::script('js/modernizr-latest.js') }}
<script> //Convert all HTML5 date input boxes to jQuery date pickers if there are compatibility issues
    if (!Modernizr.inputtypes.date) {
        $('input[type=date]').datepicker({
            // Consistent format with the HTML5 picker
            dateFormat: 'yy-mm-dd',
            changeYear: true,
            yearRange: "-100:+0"
        });
    }
</script>

<script>
var config =
{
    apiURL: '/api/index.php',
    //apiURL: '{{Config::get('config.apiURL')}}', //For local dev testing
    apiKey: '{{Config::get('config.apiKey')}}',
    privateKey: '{{Config::get('config.privateKey')}}',
    dateFormat: '{{Config::get('config.dateFormat')}}'
};
</script>

<script>
    $(document).ready(function () {

        window.setTimeout(function() {
          $(".fadeAway").fadeTo(500, 0).slideUp(500, function(){
              $(this).remove();
          });
        }, 5000);


        $('[data-toggle="popover"]').popover()

    });
</script>
@show
<!-- END JAVASCRIPT INCLUDES -->

</body>

</HTML>
