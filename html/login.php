<!DOCTYPE html>
<html>
<head>
    <title>Login Form</title>

    <link rel="stylesheet" type="text/css" href="assets/css/login.css"/>

    <script type="text/javascript" src="assets/js/jquery-3.6.0.min.js"></script>

    <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css"/>
    <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <script type="text/javascript" src="assets/js/generic/tools.js"></script>
    <script type="text/javascript" src="assets/js/views/login.js"></script>
</head>

<body>
<h2>Login Page</h2>

<br>

<div class="login">
    <label><b>User Name</b></label>

    <input type="text" id="username" placeholder="Username"/>

    <br><br>

    <label><b>Password</b></label>

    <input type="Password" id="password" placeholder="Password"/>

    <br><br>

    <input type="button" id="login-button" value="Log in here" onclick="javascript:login();"/>

    <br><br>

    <input type="checkbox" id="remember-me"/>
    <label for="remember-me">Remember me</label>

    <a href="#">Forgot password</a>
</div>
</body>
</html>
