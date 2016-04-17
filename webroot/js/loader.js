/**
 * Created by Darkweizer on 06/02/2016.
 */

function doesConnectionExist(url) {
    var xhr = new XMLHttpRequest();
    var file = url;
    var randomNum = Math.round(Math.random() * 10000);

    xhr.open('HEAD', file + "?rand=" + randomNum, false);

    try {
        xhr.send();

        if (xhr.status >= 200 && xhr.status < 304) {
            return true;
        } else {
            return false;
        }
    } catch (e) {
        return false;
    }
}

if(!window.jQuery) {
    document.write('<script type="text/javascript" src="' + base_url + 'webroot/js/jquery-2.2.0.min.js"><\/script>\')</script>');
}

if(!doesConnectionExist("https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js")) {
    document.write('<script type="text/javascript" src="' + base_url + 'webroot/js/materialize.min.js"></script>');
} else {
    document.write('<script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.6/js/materialize.min.js"></script>');
}