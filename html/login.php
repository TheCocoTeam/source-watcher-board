<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="favicon.svg" type="image/svg+xml">
    <title>Log in · Source Watcher</title>
    <link rel="stylesheet" href="assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <script src="assets/js/generic/tools.js"></script>
    <script src="assets/js/views/login.js"></script>
</head>
<body>
    <header class="login-header">
        <span class="login-header-title">Source Watcher</span>
    </header>
    <main class="login-main">
        <div class="login-card">
            <h1 class="login-card-title">Log in</h1>
            <form id="login-form" class="login-form" onsubmit="return false;">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Username" autocomplete="username">
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" autocomplete="current-password">
                </div>
                <button type="button" id="login-button" class="login-button">Log in</button>
                <div class="login-options">
                    <label class="login-remember">
                        <input type="checkbox" id="remember-me" name="remember">
                        Remember me
                    </label>
                    <a href="#" class="login-forgot">Forgot password?</a>
                </div>
            </form>
        </div>
    </main>
    <footer class="login-footer">
        Source Watcher
    </footer>
</body>
</html>
