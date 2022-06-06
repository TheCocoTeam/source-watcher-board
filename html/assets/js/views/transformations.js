function parseJwt(token) {
    let base64Url = token.split('.')[1];
    let base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
    let jsonPayload = decodeURIComponent(
        atob(base64).split('').map(
            function (c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }
        ).join('')
    );

    return JSON.parse(jsonPayload);
}

function validateJwt(accessToken) {
    const settings = {
        "async": true,
        "crossDomain": true,
        "url": "http://localhost:8181/api/v1/jwt",
        "method": "POST",
        "headers": {
            "x-access-token": accessToken
        }
    };

    $.ajax(settings).done(function (response) {
        // do something specific to the transformations view, such as loading transformations per user

        setInterval(refreshTimer, 10000);
    });

    $.ajax(settings).fail(function (response) {
        document.location = 'login.php';
    });
}

function refreshTimer() {
    let inFiveMin = Math.floor((Date.now() + (5 * 60 * 1000))  / 1000);

    let accessToken = getCookie('access_token');
    let parsedJwt = parseJwt(accessToken);
    let jwtExpiresAt = parsedJwt.eat;

    // If the token expires in 5 minutes or less, refresh the token
    if (inFiveMin >= jwtExpiresAt) {
        let refreshToken = getCookie('refresh_token');

        const form = new FormData();
        form.append("access_token", accessToken);
        form.append("refresh_token", refreshToken);

        const settings = {
            "async": true,
            "crossDomain": true,
            "url": "http://localhost:8181/api/v1/refresh-token",
            "method": "POST",
            "processData": false,
            "contentType": false,
            "mimeType": "multipart/form-data",
            "data": form
        };

        $.ajax(settings).done(function (response) {
            //console.log(response);


        });
    }
    else {
        console.log('JWT cannot be refreshed yet. No 5 minute frame yet.')
    }
}

$(document).ready(function () {
    let accessToken = getCookie('access_token');
    validateJwt(accessToken);
});
