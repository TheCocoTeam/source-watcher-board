<?php
require_once(dirname(__FILE__) . '/admin/prepend.inc.php');
$user = User::myself();
$account = Account::myself();
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        pre {
            white-space: pre-wrap;
        }
    </style>
</head>
<body>
<h3>Refresh Token Test</h3>

<p>Calls /auth/refresh every 10 secs</p>

<pre id="demo"></pre>

</body>
<script>
    let params = '{"accountId":"<?php echo $account->id; ?>","userId":"<?php echo $user->id; ?>"}';

    refreshTimer();
    let myVar = setInterval(refreshTimer, 10000);
    function refreshTimer() {
        var http = new XMLHttpRequest();
        http.addEventListener("load", reqListener);
        http.open("POST", "/auth/refresh");
        http.setRequestHeader('Content-type', 'application/json');
        http.send(params);
    }

    function listCookies() {
        var theCookies = document.cookie.split(';');
        var aString = '';
        for (var i = 1 ; i <= theCookies.length; i++) {
            aString += i + ' ' + theCookies[i-1] + "\n";
        }
        return aString;
    }

    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for(let i = 0; i <ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) == 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    function parseJwt (token) {
        var base64Url = token.split('.')[1];
        var base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
        var jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
            return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
        }).join(''));

        return JSON.parse(jsonPayload);
    };

    function reqListener () {
        const d = new Date();
        const jwt = JSON.stringify(parseJwt(getCookie("ac")), null, 4);
        document.getElementById("demo").innerHTML = d.toUTCString() + "\n\n"
            + "Request: POST " + params + "\n\n"
            + "Response: " + this.responseText + "\n\n"
            + "JWT: \n" + jwt + "\n\n"
            + "Cookies: \n"+ listCookies() + "\n\n";
        console.log(this.responseText);
    }


</script>
</html>