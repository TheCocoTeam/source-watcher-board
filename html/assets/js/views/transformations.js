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
        "xhrFields": { "withCredentials": true },
        "url": "http://localhost:8181/api/v1/jwt",
        "method": "POST",
        "headers": {
            "x-access-token": accessToken
        }
    };

    $.ajax(settings)
        .done(function () {
            // Check every 60s whether to refresh; only refresh when token has &lt; 5 min left
            setInterval(refreshTimer, 60000);
        })
        .fail(function () {
            sessionStorage.removeItem('access_token');
            sessionStorage.removeItem('refresh_token');
            localStorage.removeItem('access_token');
            localStorage.removeItem('refresh_token');
            document.location = 'login.php';
        });
}

function refreshTimer() {
    let inFiveMin = Math.floor((Date.now() + (5 * 60 * 1000))  / 1000);

    let accessToken = getStoredAccessToken();
    let parsedJwt = parseJwt(accessToken);
    let jwtExpiresAt = parsedJwt.eat;

    // If the token expires in 5 minutes or less, refresh the token
    if (inFiveMin >= jwtExpiresAt) {
        let refreshToken = getStoredRefreshToken();

        const form = new FormData();
        form.append("access_token", accessToken);
        form.append("refresh_token", refreshToken);

        const settings = {
            "async": true,
            "crossDomain": true,
            "xhrFields": { "withCredentials": true },
            "url": "http://localhost:8181/api/v1/refresh-token",
            "method": "POST",
            "processData": false,
            "contentType": false,
            "mimeType": "multipart/form-data",
            "data": form
        };

        $.ajax(settings).done(function (response) {
            let data = typeof response === 'string' ? JSON.parse(response) : response;
            let accessToken = data.accessToken || data.access_token;
            let refreshToken = data.refreshToken || data.refresh_token;
            if (accessToken) {
                sessionStorage.setItem('access_token', accessToken);
                localStorage.setItem('access_token', accessToken);
            }
            if (refreshToken) {
                sessionStorage.setItem('refresh_token', refreshToken);
                localStorage.setItem('refresh_token', refreshToken);
            }
        });
    }
}

function getStoredAccessToken() {
    return sessionStorage.getItem('access_token') || localStorage.getItem('access_token') || getCookie('access_token');
}
function getStoredRefreshToken() {
    return sessionStorage.getItem('refresh_token') || localStorage.getItem('refresh_token') || getCookie('refresh_token');
}

function clearTokensAndRedirect() {
    sessionStorage.removeItem('access_token');
    sessionStorage.removeItem('refresh_token');
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
    document.cookie = 'access_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    document.cookie = 'refresh_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
    document.location = 'login.php';
}

function logout() {
    var accessToken = getStoredAccessToken();
    var refreshToken = getStoredRefreshToken();
    if (accessToken && refreshToken) {
        var form = new FormData();
        form.append('access_token', accessToken);
        form.append('refresh_token', refreshToken);
        $.ajax({
            url: 'http://localhost:8181/api/v1/logout',
            method: 'POST',
            data: form,
            processData: false,
            contentType: false,
            xhrFields: { withCredentials: true }
        }).always(clearTokensAndRedirect);
    } else {
        clearTokensAndRedirect();
    }
}

$(document).ready(function () {
    $('#logout-btn').on('click', function (e) {
        e.preventDefault();
        logout();
    });

    let accessToken = getStoredAccessToken();
    if (!accessToken) {
        document.location = 'login.php';
        return;
    }
    validateJwt(accessToken);
});
