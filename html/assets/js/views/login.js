function login() {
    const form = new FormData();
    form.append("username", $('#username').val());
    form.append("password", $('#password').val());

    $.ajax({
        url: "http://localhost:8181/api/v1/credentials",
        method: "POST",
        data: form,
        processData: false,
        contentType: false,
        dataType: "json",
        crossDomain: true,
        xhrFields: { withCredentials: true }
    }).done(function (data) {
        var accessToken = data.accessToken || data.access_token;
        var refreshToken = data.refreshToken || data.refresh_token;
        if (accessToken && refreshToken) {
            sessionStorage.setItem('access_token', accessToken);
            sessionStorage.setItem('refresh_token', refreshToken);
            localStorage.setItem('access_token', accessToken);
            localStorage.setItem('refresh_token', refreshToken);
            // Brief delay so storage is committed before navigation (avoids race on first load)
            setTimeout(function () {
                document.location = 'transformations.php';
            }, 50);
        } else {
            toastr.error('Login failed: no tokens received.');
        }
    }).fail(function (xhr) {
        try {
            var msg = xhr.responseJSON || (xhr.responseText ? JSON.parse(xhr.responseText) : 'Login failed');
            toastr.error(msg);
        } catch (e) {
            toastr.error('Login failed.');
        }
    });
}

function validateJwt(accessToken) {
    $.ajax({
        url: "http://localhost:8181/api/v1/jwt",
        method: "POST",
        headers: { "x-access-token": accessToken },
        crossDomain: true,
        xhrFields: { withCredentials: true }
    }).done(function () {
        document.location = 'transformations.php';
    }).fail(function () {
        sessionStorage.removeItem('access_token');
        sessionStorage.removeItem('refresh_token');
        localStorage.removeItem('access_token');
        localStorage.removeItem('refresh_token');
    });
}

$(document).ready(function () {
    let accessToken = sessionStorage.getItem('access_token') || getCookie('access_token');
    if (accessToken) {
        validateJwt(accessToken);
    }
});
