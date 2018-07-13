<?php
require_once './_common.php';
?>
jQuery(function() {
    jQuery(document).on("click", "form.form-write button:submit, form.form-write input:submit, form.form-write input:image", function(e) {
        var f  = this.form;
        var $f = jQuery(f);
        var $b = jQuery(this);
        var w  = f.w.value;

        var name, pass, subject, content;

        subject = jQuery.trim(f.bo_subject.value);

        if(typeof(tinymce) != "undefined")
            content = jQuery.trim(tinymce.activeEditor.getContent());
        else
            content = jQuery.trim(f.bo_content.value);

        if(typeof(f.bo_name) != "undefined") {
            name = jQuery.trim(f.bo_name.value);

            if(name.length < 1) {
                jQuery("#bo_name").data("content", "<?php echo _('Please enter a Name.'); ?>").popover("show");
                return false;
            }
        }

        if(typeof(f.bo_password) != "undefined") {
            pass = jQuery.trim(f.bo_password.value);

            if(pass.length < 1) {
                jQuery("#cm_password").data("content", "<?php echo _('Please enter a Password.'); ?>").popover("show");
                return false;
            }
        }

        if(subject.length < 1) {
            jQuery("#bo_subject").data("content", "<?php echo _('Please enter a Subject.'); ?>").popover("show");
                return false;
        }

        if(content.length < 1) {
            jQuery("#bo-content-editor").data("content", "<?php echo _('Please enter a Contents.'); ?>").popover("show");
                return false;
        }

        if (typeof(grecaptcha) != "undefined") {
            if (grecaptcha.getResponse() == "") {
                $f.find(".recaptcha_area").data("content", "<?php echo _('Please check the anti-spam code.'); ?>").popover("show");
                return false;
            }
        }

         $b.parent().append("<div class=\"my-3 save_spinner save-spinner d-block w-100\"><img src=\"" + nt_img_url + "/spinner-2x.gif\"></div>");

        return true;
    });

    jQuery(document).on("click", "a.post-delete", function(e) {
        if(!confirm("<?php echo _('Are you sure you want to delete this post?'); ?>"))
            return false;
    });
});