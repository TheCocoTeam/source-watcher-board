<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <title>Login Form</title>

    <link rel="stylesheet" href="assets/css/login.css">

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>

    <script src="assets/js/generic/tools.js"></script>
    <script src="assets/js/views/login.js"></script>
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
