<?php
require_once './_common.php';
?>
function loadComment()
{
    jQuery("#comment-area").load(nt_url+"/<?php echo BOARD_DIR; ?>/"+viewId+"/"+viewNo+"/comment");
}

jQuery(function() {
    if(document.getElementById("comment-area"))
        loadComment();

    jQuery(document).on("click", "form.form-comment button:submit, form.form-comment input:submit, form.form-comment input:image", function(e) {
        e.preventDefault();
        e.stopPropagation();

        var f  = this.form;
        var $f = jQuery(f);
        var $b = jQuery(this);
        var w  = f.w.value;

        var name, pass, content;

        if(w != "d")
            content = jQuery.trim(f.cm_content.value);

        if(typeof(f.cm_name) != "undefined") {
            name = jQuery.trim(f.cm_name.value);

            if(name.length < 1) {
                $f.find(".cm_name").data("content", "<?php echo _('Please enter a Name.'); ?>").popover("show");
                return false;
            }
        }

        if(typeof(f.cm_password) != "undefined") {
            pass = jQuery.trim(f.cm_password.value);

            if(pass.length < 1) {
                $f.find(".cm_password").data("content", "<?php echo _('Please enter a Password.'); ?>").popover("show");
                return false;
            }
        }

        if(w != "d" && content.length < 1) {
            $f.find(".cm_content").data("content", "<?php echo _('Please enter a Contents.'); ?>").popover("show");
                return false;
        }

        if (typeof(grecaptcha) != "undefined") {
            if (grecaptcha.getResponse() == "") {
                $f.find(".recaptcha_area").data("content", "<?php echo _('Please check the anti-spam code.'); ?>").popover("show");
                return false;
            }
        }

        if(w == "d") {
            if(!confirm("<?php echo _('Are you sure you want to delete the comment?'); ?>"))
                return false;
        }

        $b.after("<div class=\"my-3 save_spinner save-spinner d-block w-100\"><img src=\"" + nt_img_url + "/spinner-2x.gif\"></div>");

        jQuery.ajax({
            url: f.action,
            method: "POST",
            data: $f.serialize(),
            success: function(data) {
                jQuery(".save_spinner").remove();

                if(data.error != "") {
                    $b.after("<div class=\"mt-3 alert alert-danger alert-dismissible fade show\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" title=\"close\">&times;</a>" + data.error + "</div>");

                    return false;
                }

                if(document.getElementById("comment-count"))
                    jQuery("#comment-count").text(number_format(data.count));

                loadComment();
            },
            error: function(request, status, error) {
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
            dataType: "JSON"
        });
    });

    jQuery(document).on("click", ".comment-button", function(e) {
        e.preventDefault();
        e.stopPropagation();

        var $this  = jQuery(this);
        var href   = this.href;
        var action = href.split("/").pop();

        if(action == "delete") {
            if(!confirm("<?php echo _('Are you sure you want to delete the comment?'); ?>"))
                return false;
        }

        jQuery.ajax({
            url: href,
            success: function(data) {
                if(data.error != "") {
                    alert(data.error);
                    return false;
                }

                if(typeof data.form != "undefined") {
                    jQuery(".comment-form-wrap").remove();
                    $this.parent().after("<div class=\"comment-form-wrap pb-3\">"+data.form+"</div>");
                } else {
                    if(action == "delete")
                        loadComment();
                }
            },
            error: function(request, status, error) {
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
            dataType: "JSON"
        });
    });
});