<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login || Club Speed Administration</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    {{ HTML::style('css/bootstrap.min.css') }}
    {{ HTML::style('css/unicorn-login.css') }}
    {{ HTML::style('css/font-awesome.css') }}
    <!--[if lt IE 9]>
    <script type="text/javascript" src="js/respond.min.js"></script>
    <![endif]-->
</head>

<body data-color="grey" class="flat"> <!-- Improve template for these style pages -->
<div id="logo">
    <img src="img/logo.png" alt="" />
</div>

@include('errors', array('errors' => $errors))


<div id="loginbox">
    <form id="loginform" action="{{action('LoginController@loginSubmit')}}" method="POST">
        <p>Enter username and password to continue.</p>
        <div class="input-group input-sm">
            <span class="input-group-addon"><i class="fa fa-user"></i></span><input class="form-control" type="text" name="username" id="username" placeholder="Username" />
        </div>
        <div class="input-group">
            <span class="input-group-addon"><i class="fa fa-lock"></i></span><input class="form-control" type="password" name="password" id="password" placeholder="Password" />
        </div>
        <div class="form-actions clearfix">
            <!--<div class="pull-left">
                <a href="#registerform" class="flip-link to-register blue">Create new account</a>
            </div>
            <div class="pull-right">
                <a href="#recoverform" class="flip-link to-recover grey">Lost password?</a>
            </div>-->
            <input type="submit" class="btn btn-block btn-primary btn-default" value="Login" />
        </div>
        <!--<div class="footer-login">
            <div class="pull-left text">
                Login with
            </div>
            <div class="pull-right btn-social">
                <a class="btn btn-facebook" href="#"><i class="fa fa-facebook"></i></a>
                <a class="btn btn-twitter" href="#"><i class="fa fa-twitter"></i></a>
                <a class="btn btn-google-plus" href="#"><i class="fa fa-google-plus"></i></a>
            </div>
        </div>-->
    </form>
</div>

{{ HTML::script('js/jquery.min.js') }}
{{ HTML::script('js/jquery-ui.custom.js') }}
{{ HTML::script('js/bootstrap.min.js') }}
<!--<script src="js/unicorn.login.js"></script> TODO: Find out why this was breaking the form-->
</body>
</html>