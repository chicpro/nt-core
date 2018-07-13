<?php
require_once './_common.php';
?>
jQuery(function() {
    jQuery(document).on("submit", "form#ffind", function(e) {
        e.preventDefault();
        e.stopPropagation();

        var f  = this;
        var $f = jQuery(this);
        var $b = $f.find("button.btn");

        if (f.email.length < 1) {
            jQuery("#email").data("content", "<?php echo _('Please enter Email.'); ?>").popover("show");
            return false;
        }

        if (typeof(grecaptcha) != "undefined") {
            if (grecaptcha.getResponse() == "") {
                jQuery("#recaptcha_area").data("content", "<?php echo _('Please check the anti-spam code.'); ?>").popover("show");
                return false;
            }
        }

        $b.after("<span class=\"mt-3 save_spinner save-spinner d-block w-100\"><img src=\"" + nt_img_url + "/spinner-2x.gif\"></span>");

        jQuery.ajax({
            url: f.action,
            method: "POST",
            data: $f.serialize(),
            success: function(data) {
                jQuery(".save_spinner").remove();

                if(typeof data.error != "undefined" && data.error != "") {
                    $b.after("<div class=\"mt-3 alert alert-danger alert-dismissible fade show\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" title=\"close\">&times;</a>" + data.error + "</div>");
                    return false;
                }

                if(typeof data.success != "undefined" && data.success != "") {
                    $b.after("<div class=\"mt-3 alert alert-success alert-dismissible fade show\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" title=\"close\">&times;</a>" + data.success + "</div>");

                    setTimeout(function() {
                        document.location.href = "<?php echo NT_URL; ?>";
                    }, 7000);

                    return;
                }
            },
            error: function(request, status, error) {
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
            dataType: "JSON"
        });
    });
});