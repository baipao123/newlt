function Bp123GetUpToken(url) {
    var token = localStorage.getItem("bp-upToken");
    var expire = localStorage.getItem("bp-upToken-Expire");
    if (token === null || token === "" || expire < new Date().getTime())
        token = Bp123GetQiNiuUpTokenFromUrl(url);
    return token;
}

/**
 * @return {string}
 */
function Bp123GetQiNiuUpTokenFromUrl(url) {
    var token = "";
    $.ajax({
        url: url,
        async: false,
        contentType : 'application/json',
        success: function (res) {
            if (typeof res !== "object")
                return "";
            token = res.token;
            localStorage.setItem("bp-upToken", token);
            localStorage.setItem("bp-upToken-Expire", new Date().getTime() + 3600000);
        }
    });
    return token;
}
