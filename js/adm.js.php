<?php
require_once './_common.php';
?>
jQuery(function() {
    jQuery(document).on("click", "form.form-ajax button:submit, form.form-ajax input:submit, form.form-ajax input:image", function(e) {
        e.preventDefault();
        e.stopPropagation();

        var f  = this.form;
        var $f = jQuery(f);
        var $t = jQuery(this);

        setTokenValue(this.form, "adm");

        var formData = new FormData(f);

        $t.parent().append("<span class=\"pl-3 save_spinner save-spinner\"><img src=\"" + nt_img_url + "/spinner-2x.gif\"></span>");

        jQuery.ajax({
            processData: false,
            contentType: false,
            url: f.action,
            method: f.method,
            data: formData,
            success: function(data) {
                jQuery(".save_spinner").remove();

                if (data.error != "") {
                    $f.after("<div class=\"mt-3 alert alert-danger alert-dismissible fade show\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" title=\"close\">&times;</a>" + data.error + "</div>");
                    return;
                }

                if(typeof(data.href) != "undefined") {
                    document.location.href = data.href;
                    return;
                }

                $t.parent().append("<div class=\"save_result save-done\"></div>");

                setTimeout(function() {
                    jQuery(".save_result").fadeOut(750, function() { jQuery(this).remove(); });
                }, 2000);
            },
            error: function(request, status, error) {
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
            dataType: "JSON"
        });

        $t.trigger("blur");
    });

    jQuery(document).on("click", ".delete-confirm", function(e) {
        if(!confirm("<?php echo _('Are you sure you want to delete?'); ?>"))
            return false;
    });

    jQuery(document).on("click", ".page-delete", function(e) {
        e.preventDefault();
        e.stopPropagation();

        if(!confirm("<?php echo _('Are you sure you want to delete?'); ?>"))
            return false;

        var token = setTokenValue("", "adm");

        jQuery.ajax({
            url: this.href + "&token=" + token,
            async: false,
            cache: false,
            success: function(data) {
                if (data.error != "") {
                    alert(data.error);
                    return;
                }

                document.location.reload();
            },
            error: function(request, status, error) {
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
            dataType: "JSON"
        });
    });

    jQuery(document).on("click", "#create-sitemap", function(e) {
        var $t = jQuery(this);

        $t.prop("disabled", true);
        $t.parent().append("<span class=\"pl-3 save_spinner save-spinner d-block mx-auto\"><img src=\"" + nt_img_url + "/spinner-2x.gif\"></span>");

        jQuery.ajax({
            url: "./sitemapCreate.php",
            success: function(data) {
                jQuery(".save_spinner").remove();

                if (data.error != "") {
                    $t.after("<div class=\"mt-3 alert alert-danger alert-dismissible fade show\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" title=\"close\">&times;</a>" + data.error + "</div>");
                    return;
                }

                $t.prop("disabled", false);
                $t.parent().append("<div class=\"save_result save-done d-block mx-auto\"></div>");

                setTimeout(function() {
                    jQuery(".save_result").fadeOut(750, function() { jQuery(this).remove(); document.location.reload(); });
                }, 2000);
            },
            error: function(request, status, error) {
                $t.prop("disabled", false);
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
            dataType: "JSON"
        })
    });

    jQuery(document).on("click", '.edit-menu', function(e) {
        e.preventDefault();
        e.stopPropagation();

        jQuery(".nav .nav-link").removeClass("active");
        jQuery(this).addClass("active");

        PopupCenterDual(this.href, "editMenu", 1200, 800);
    });
});