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

        let parsedJwt = parseJwt(accessToken);
        console.dir(parsedJwt);
    });

    $.ajax(settings).fail(function (response) {
        document.location = 'login.php';
    });
}

$(document).ready(function () {
    let accessToken = getCookie('access_token');
    validateJwt(accessToken);
});
