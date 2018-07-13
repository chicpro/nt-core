<?php
require_once './_common.php';
?>
jQuery(function() {
    jQuery(document).on("click", "form button:submit, form input:submit, form input:image", function(e) {
        e.preventDefault();
        e.stopPropagation();

        var f  = this.form;
        var $f = jQuery(f);
        var $b = jQuery(this);

        var id   = jQuery.trim(f.id.value);
        var pass = jQuery.trim(f.pass.value);

        if(id.length < 1) {
            jQuery("#id").data("content", "<?php echo _('Please enter your member ID or Email address.'); ?>").popover("show");
            return false;
        }

        if(pass.length < 1) {
            jQuery("#pass").data("content", "<?php echo _('Please enter Password.'); ?>").popover("show");
            return false;
        }

        if(jQuery("#2factor-auth").length > 0 && !jQuery("#2factor-auth").hasClass("invisible")) {
            var oneCode = jQuery.trim(f.onecode.value);
            if(oneCode.length < 1) {
                jQuery("#onecode").data("content", "<?php echo _('Please enter the one time password.'); ?>").popover("show");
                return false;
            }
        }

        if (typeof(grecaptcha) != "undefined") {
            if (grecaptcha.getResponse() == "") {
                jQuery("#recaptcha_area").data("content", "<?php echo _('Please check the anti-spam code.'); ?>").popover("show");
                return false;
            }
        }

        $b.after("<div class=\"my-3 save_spinner save-spinner d-block w-100\"><img src=\"" + nt_img_url + "/spinner-2x.gif\"></div>");

        jQuery.ajax({
            url: f.action,
            method: "POST",
            data: $f.serialize(),
            success: function(data) {
                jQuery(".save_spinner").remove();

                if(data.error != "") {
                    if(data.error == "2factor-auth") {
                        jQuery("#member-signin").addClass("invisible position-absolute");
                        jQuery("#2factor-auth").removeClass("invisible position-absolute");
                        jQuery("#onecode").trigger("focus");
                        return;
                    }

                    if(typeof(data.element) != "undefined") {
                        jQuery("#"+data.element).data("content", data.error).popover("show");
                    } else {
                        $b.after("<div class=\"mt-3 alert alert-danger alert-dismissible fade show\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" title=\"close\">&times;</a>" + data.error + "</div>");
                    }

                    return false;
                }

                document.location.reload();
            },
            error: function(request, status, error) {
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
            dataType: "JSON"
        });
    });
});