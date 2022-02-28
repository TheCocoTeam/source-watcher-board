<?php declare(strict_types=1);
// First check if the access_token and refresh_token cookies exist

$accessToken = !empty( $_COOKIE['access_token'] ) ? $_COOKIE['access_token'] : null;
$refreshToken = !empty( $_COOKIE['refresh_token'] ) ? $_COOKIE['refresh_token'] : null;

if ( !empty( $accessToken ) ) {
    $ch = curl_init();

    curl_setopt( $ch, CURLOPT_URL, 'http://localhost/api/v1/jwt' );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, [sprintf( 'x-access-token: %s;', $accessToken )] );

    $result = curl_exec( $ch );
    $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

    curl_close( $ch );

    if ( $httpCode == 200 ) {
        // Session valid, redirect
    }
    else {
        // Attempt to refresh

        if ( !empty( $refreshToken ) ) {

        }
    }
}

//

$action = !empty( $_POST['action'] ) ? $_POST['action'] : null;

$username = !empty( $_POST['username'] ) ? $_POST['username'] : null;
$password = !empty( $_POST['password'] ) ? $_POST['password'] : null;

$errorMessage = null;

if ( !empty( $action ) ) {
    $ch = curl_init();

    curl_setopt( $ch, CURLOPT_URL, 'http://localhost/api/v1/credentials' );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
    curl_setopt( $ch, CURLOPT_POST, 1 );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, ['username' => $username, 'password' => $password] );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, ['Content-Type: multipart/form-data;'] );

    $result = curl_exec( $ch );

    if ( curl_errno( $ch ) ) {
        echo 'Error:' . curl_error( $ch );
    }
    else {
        switch ( $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE ) ) {
            case 200:
                $tokensResponse = json_decode( $result, true );

                $accessToken = $tokensResponse['accessToken'];
                $refreshToken = $tokensResponse['refreshToken'];

                break;
            default:
                $errorMessage = json_decode( $result );
                break;
        }
    }

    curl_close( $ch );

    if ( !empty( $accessToken ) && !empty( $refreshToken ) ) {
        $expiresAt = strtotime( '+1 hour' );

        setcookie( 'access_token', $accessToken, $expiresAt, '/', 'localhost', true );
        setcookie( 'refresh_token', $refreshToken, $expiresAt, '/', 'localhost', true );

        header( 'Location: transformations.php' );
    }
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Login Form</title>

        <link rel="stylesheet" type="text/css" href="css/login.css" />

        <script type="text/javascript" src="js/jquery-3.6.0.min.js"></script>

        <link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" />
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

        <script type="text/javascript">
            let errorMessage = "<?php echo $errorMessage; ?>";

            $(document).ready(function() {
                if( errorMessage !== null && errorMessage !== '' ) {
                    toastr.error( errorMessage );
                }
            });
        </script>
    </head>

    <body>
        <h2>Login Page</h2>

        <br>

        <div class="login">
            <form id="login" method="POST" action="login.php">
                <input type="hidden" id="action" name="action" value="login" />

                <label><b>User Name</b></label>

                <input type="text" name="username" id="username" placeholder="Username" />

                <br><br>

                <label><b>Password</b></label>

                <input type="Password" name="password" id="password" placeholder="Password" />

                <br><br>

                <input type="submit" name="login-button" id="login-button" value="Log In Here" />

                <br><br>

                <input type="checkbox" id="remember-me" />
                <label for="remember-me">Remember me</label>

                <br><br>

                <span>Forgot <a href="#">Password</a></span>
            </form>
        </div>
    </body>
</html>
