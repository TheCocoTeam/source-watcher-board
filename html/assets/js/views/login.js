function login() {
    const form = new FormData();
    form.append("username", $('#username').val());
    form.append("password", $('#password').val());

    const settings = {
        "async": true,
        "crossDomain": true,
        "url": "http://localhost:8181/api/v1/credentials",
        "method": "POST",
        "headers": {},
        "processData": false,
        "contentType": false,
        "mimeType": "multipart/form-data",
        "data": form
    };

    $.ajax(settings).done(function (response) {
        let responseArray = $.parseJSON(response);

        let accessToken = responseArray.access_token;
        let refreshToken = responseArray.refresh_token;

        document.location = 'transformations.php';
    });

    $.ajax(settings).fail(function (response) {
        let errorMessage = JSON.parse(response.responseText);
        toastr.error(errorMessage);
    });
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
        document.location = 'transformations.php';
    });

    $.ajax(settings).fail(function (response) {
        let errorMessage = JSON.parse(response.responseText);
        toastr.error(errorMessage);
    });
}

$(document).ready(function () {
    let accessToken = getCookie('access_token');
    validateJwt(accessToken);
});
