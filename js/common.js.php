<?php
require_once './_common.php';
?>
// PopUp : center center / dual
function PopupCenterDual(url, title, w, h) {
    // Fixes dual-screen position Most browsers Firefox
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : screen.left;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : screen.top;
    width = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var left = ((width / 2) - (w / 2)) + dualScreenLeft;
    var top = ((height / 2) - (h / 2)) + dualScreenTop;
    var newWindow = window.open(url, title, 'scrollbars=yes, width=' + w + ', height=' + h + ', top=' + top + ', left=' + left);

    // Puts focus on the newWindow
    if (window.focus) {
        newWindow.focus();
    }
}

function number_format(data)
{
    var patt = new RegExp(/[0-9\.]+/g);
    if(!patt.test(data))
        return data;

    var tmp = '';
    var number = '';
    var cutlen = 3;
    var comma = ',';
    var i;
    var frt;

    data = String(data);

    var sign = data.match(/^[\+\-]/);
    if(sign) {
        data = data.replace(/^[\+\-]/, "");
    }

    var res = data.split(".");

    len = res[0].length;
    mod = (len % cutlen);
    k = cutlen - mod;
    for (i=0; i<res[0].length; i++)
    {
        number = number + res[0].charAt(i);

        if (i < res[0].length - 1)
        {
            k++;
            if ((k % cutlen) == 0)
            {
                number = number + comma;
                k = 0;
            }
        }
    }

    if(sign != null)
        number = sign+number;

    if(typeof res[1] != "undefined")
        number = number + "." + res[1];

    return number;
}

function setTokenValue(f, type)
{
    var ret = true;
    var token;

    if(f && jQuery(f).length > 0)
        ret = false;

    if (!ret && typeof f.token === "undefined")
        jQuery(f).prepend("<input type=\"hidden\" name=\"token\">");

    jQuery.ajax({
        url: nt_ajax_url + "/getToken.php",
        method: "GET",
        async: false,
        cache: false,
        data: {type: type},
        success: function(data) {
            if(ret)
                token = data;
            else
                f.token.value = data;
        },
        error: function(request, status, error) {
            console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
        }
    });

    if(ret)
        return token;
}

jQuery(function() {
    jQuery(document).on("click", "form.form-token button:submit, form.form-token input:submit, form.form-token input:image", function(e) {
        setTokenValue(this.form, "");

        return true;
    });

    jQuery(document).on("click", ".history-back", function(e) {
        history.back();
    });

    jQuery(document).on("click", ".close-popup", function(e) {
        self.close();
    });
});