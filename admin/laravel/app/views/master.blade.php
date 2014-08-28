<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- BEGIN CSS INCLUDES -->
    @section('css_includes')
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/font-awesome.css" />
    <link rel="stylesheet" href="css/fullcalendar.css" />
    <link rel="stylesheet" href="css/jquery.jscrollpane.css" />
    <link rel="stylesheet" href="css/unicorn.css" />
    <!--[if lt IE 9]>
    <script type="text/javascript" src="js/respond.min.js"></script>
    <![endif]-->
    @show
    <!-- END CSS INCLUDES -->

	<title>@yield('title', 'Admin Panel')</title>
</head>

<body data-color="grey" class="flat">

<!-- BEGIN MAIN PAGE CONTAINER -->
<div id="wrapper">
    <div id="header">
        <h1><a href="dashboard">@yield('pageHeader','REPLACE_PAGE_TITLE')</a></h1>
        <a id="menu-trigger" href="#"><i class="fa fa-bars"></i></a>
    </div>

    <div id="user-nav">
        <ul class="btn-group">
            <li class="btn"><a href="#">Logged in as: {{Session::get('user')}}</a></li>
            <li class="btn"><a title="" href="logout"><i class="fa fa-share"></i> <span class="text">Logout</span></a></li>
        </ul>
    </div>

    <div id="sidebar">
        <div id="search">
            <input type="text" placeholder="Search here..."/><button type="submit" class="tip-right" title="Search"><i class="fa fa-search"></i></button>
        </div>
        <ul>
            <li class="active"><a href="dashboard"><i class="fa fa-home"></i> <span>Dashboard</span></a></li>
            <li class="submenu">
                <a href="#"><i class="fa fa-flask"></i> <span>Sub-Menu 1</span> <i class="arrow fa fa-chevron-right"></i></a>
                <ul>
                    <li><a href="#">Sub Item</a></li>
                    <li><a href="#">Sub Item</a></li>
                    <li><a href="#">Sub Item</a></li>
                </ul>
            </li>
            <li><a href="#"><i class="fa fa-th"></i> <span>Direct Link Item</span></a></li>
        </ul>

    </div>

    <div id="content">
        <div id="content-header" class="mini">
            <h1>@yield('pageHeader','REPLACE_PAGE_TITLE')</h1>
            <ul class="mini-stats box-3">
                <li>
                    <div class="left sparkline_bar_good"><span>2,4,9,7,12,10,12</span>+10%</div>
                    <div class="right">
                        <strong>36094</strong>
                        Visits
                    </div>
                </li>
                <li>
                    <div class="left sparkline_bar_neutral"><span>20,15,18,14,10,9,9,9</span>0%</div>
                    <div class="right">
                        <strong>1433</strong>
                        Users
                    </div>
                </li>
                <li>
                    <div class="left sparkline_bar_bad"><span>3,5,9,7,12,20,10</span>+50%</div>
                    <div class="right">
                        <strong>8650</strong>
                        Orders
                    </div>
                </li>
            </ul>
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
<script src="js/excanvas.min.js"></script>
<script src="js/jquery.min.js"></script>
<script src="js/jquery-ui.custom.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/jquery.flot.min.js"></script>
<script src="js/jquery.flot.resize.min.js"></script>
<script src="js/jquery.sparkline.min.js"></script>
<script src="js/fullcalendar.min.js"></script>

<script src="js/jquery.nicescroll.min.js"></script>
<script src="js/unicorn.js"></script>
<script src="js/unicorn.dashboard.js"></script>
@show
<!-- END JAVASCRIPT INCLUDES -->

</body>

</HTML>