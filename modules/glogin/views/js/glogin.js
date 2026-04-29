/*
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA PL MILOSZ MYSZCZUK VATEU: PL9730945634
 * @copyright 2010-2025
 * @license   This program is not free software and you can't resell and redistribute it
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */

(function () {
    var po = document.createElement('script');
    po.type = 'text/javascript';
    po.async = true;
    po.src = 'https://accounts.google.com/gsi/client';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(po, s);
})();

$(document).ready(function () {
    if (typeof gglogin_appid !== 'undefined') {
        window.onGoogleLibraryLoad = () => {
            var configString = {
                client_id: gglogin_appid,
                callback: handleAuthResult
            };
            google.accounts.id.initialize(configString);
        };
    }
});

function rungoogleloader() {
    $(".gloader").fadeIn(500);
}

function stopgoogleloader() {
    $(".gloader").fadeOut(3500);
}

function glogin_mypresta() {
    if (typeof gglogin_appid !== 'undefined') {
        rungoogleloader();
        /**
         google.accounts.id.prompt((notification) => {
        if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
            stopgoogleloader();
        }
    });
         **/
        const client = google.accounts.oauth2.initTokenClient({
            client_id: gglogin_appid,
            scope: 'openid profile email',
            callback: handleAuthResult,
        });
        client.requestAccessToken();
    }
}


function handleAuthResult(authResult) {
    if (authResult && !authResult.error) {
        var access_token = authResult.access_token;
        tryGetEmail(access_token);
    } else {
        stopgoogleloader();
    }
}

function tryGetEmail(access_token) {
    /**
    const responsePayload = access_token;
    if (200 == 200) {
        stopgoogleloader();
        $.post(ggl_loginloader, {resp: responsePayload, back: $.urlParam('back')}, function (data) {
            eval(data);
        });
    } else {
        stopgoogleloader();
    }
    **/
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.open("GET", 'https://www.googleapis.com/oauth2/v2/userinfo?access_token=' + access_token, false);
    xmlHttp.send(null);
    if (xmlHttp.status == 200) {
        var strJSON = xmlHttp.responseText;
        var objJSON = eval("(function(){return " + strJSON + ";})()");
        stopgoogleloader();
        $.post(ggl_loginloader, {resp: objJSON, back: $.urlParam('back')}, function (data) {
            eval(data);
        });
    } else {
        stopgoogleloader();
    }
}


$.urlParam = function (name) {
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    if (results == null) {
        return null;
    } else {
        return results[1] || 0;
    }
}
      