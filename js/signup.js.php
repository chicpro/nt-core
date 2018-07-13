<?php
require_once './_common.php';
?>
jQuery(function() {
    jQuery(document).on("click", ".terms-button", function(e) {
        e.preventDefault();
        e.stopPropagation();

        if(!jQuery("#terms_agree").is(":checked")) {
            jQuery("#terms_agree").data("content", "<?php echo _('Please accept the terms and conditions.'); ?>").popover("show");
                return false;
        }

        if(!jQuery("#privacy_agree").is(":checked")) {
            jQuery("#privacy_agree").data("content", "<?php echo _('Please accept the privacy policy.'); ?>").popover("show");
                return false;
        }

        jQuery("input[name='terms_agree']").val(1);
        jQuery("input[name='privacy_agree']").val(1);

        jQuery("#form-terms").attr("action", this.href).submit();
    });

    // ID check
    jQuery(document).on("change", "form#fsignup #mb_id", function() {
        var $this = jQuery(this);
        var mb_id = $this.val();
        var w = jQuery("input[name=w]").val();

        if (mb_id.lenght < 1)
            return;

        $this.popover("dispose");

        jQuery.ajax({
            url: nt_ajax_url + "/memberId.php",
            type: "POST",
            async: true,
            cache: false,
            data: {id: mb_id},
            success: function(data) {
                if (data != "") {
                    $this.data("content", data).popover("show");
                    return false;
                }
            },
            error: function(request, status, error) {
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
    });

    // Email check
    jQuery(document).on("change", "form#fsignup #mb_email", function() {
        var $this = jQuery(this);
        var mb_email = $this.val();
        var w = jQuery("input[name=w]").val();

        if (mb_email.lenght < 1)
            return;

        jQuery.ajax({
            url: nt_ajax_url + "/memberEmail.php",
            type: "POST",
            async: true,
            cache: false,
            data: {email: mb_email},
            success: function(data) {
                if (data != "") {
                    $this.data("content", data).popover("show");
                    return false;
                }
            },
            error: function(request, status, error) {
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        });
    });

    jQuery(document).on("click", "form#fsignup button:submit, form#fsignup input:submit, form#fsignup input:image", function(e) {
        e.preventDefault();
        e.stopPropagation();

        var f  = this.form;
        var $f = jQuery(f);
        var w  = f.w.value;
        var $b = jQuery(this);

        var mb_id, mb_name, mb_email, mb_password, mb_password_re;

        mb_id       = jQuery.trim(f.mb_id.value);
        mb_name     = jQuery.trim(f.mb_name.value);
        mb_email    = jQuery.trim(f.mb_email.value);
        mb_password = jQuery.trim(f.mb_password.value);
        if (w != "u")
            mb_password_re = jQuery.trim(f.mb_password_re.value);

        if (mb_id.length < 1) {
            jQuery("#mb_id").data("content", "<?php echo _('Please enter ID.'); ?>").popover("show");
            return false;
        }

        if (mb_name.length < 1) {
            jQuery("#mb_name").data("content", "<?php echo _('Please enter Name.'); ?>").popover("show");
            return false;
        }

        if (mb_email.length < 1) {
            jQuery("#mb_email").data("content", "<?php echo _('Please enter Email.'); ?>").popover("show");
            return false;
        }

        if (w == "") {
            <?php if ((int)__c('cf_password_length') > 0) { ?>
            if (mb_password.length < <?php echo (int)__c('cf_password_length'); ?>) {
                jQuery("#mb_password").data("content", "<?php echo sprintf(_n('Please enter your password at least %d character.', 'Please enter your password at least %d characters.', (int)__c('cf_password_length')), (int)__c('cf_password_length')); ?>").popover("show");
                return false;
            }

            <?php } ?>
            if (mb_password != mb_password_re) {
                jQuery("#mb_password_re").data("content", "<?php echo _('The password you entered does not match.'); ?>").popover("show");
                return false;
            }
        }

        <?php if ((int)__c('cf_password_length') > 0) { ?>
        if (w == "u" && mb_password && mb_password.length < <?php echo (int)__c('cf_password_length'); ?>) {
            jQuery("#mb_password").data("content", "<?php echo sprintf(_n('Please enter your password at least %d character.', 'Please enter your password at least %d characters.', (int)__c('cf_password_length')), (int)__c('cf_password_length')); ?>").popover("show");
            return false;
        }

        <?php } ?>
        if (typeof(grecaptcha) != "undefined") {
            if (grecaptcha.getResponse() == "") {
                jQuery("#recaptcha_area").data("content", "<?php echo _('Please check the anti-spam code.'); ?>").popover("show");
                return false;
            }
        }

        setTokenValue(f, "");

        var data = $f.serializeArray();

        $b.after("<span class=\"mt-3 save_spinner save-spinner d-block w-100\"><img src=\"" + nt_img_url + "/spinner-2x.gif\"></span>");

        jQuery.ajax({
            url: f.action,
            method: "POST",
            async: true,
            cache: false,
            data: data,
            success: function(data) {
                jQuery(".save_spinner").remove();

                if(data.error != "") {
                    jQuery("#"+data.element).data("content", data.error).popover("show");
                    return;
                }

                if (w != "") {
                    $b.after("<div class=\"mt-3 save_result save-done d-block w-100 text-center\"></div>");
                    setTimeout(function() {
                        document.location.reload();
                    }, 1000);
                } else {
                    jQuery("#signupSuccess").modal("show");

                    jQuery(document).on("hidden.bs.modal", "#signupSuccess", function(e) {
                        document.location.href = "<?php echo NT_URL; ?>";
                    });

                    setTimeout(function() {
                        jQuery("#signupSuccess").modal("hide");
                    }, 5000)
                }

                setTimeout(function() {
                    jQuery(".save_result").fadeOut(750, function() { jQuery(this).remove(); document.location.href = "<?php echo NT_URL; ?>"; });
                }, 2000);
            },
            error: function(request, status, error) {
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
            dataType: "JSON"
        });
    });
});