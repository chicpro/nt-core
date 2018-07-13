<?php
require_once './_common.php';

if (!$isMember)
    gotoUrl(NT_LINK_LOGIN);

$html->setPageTitle(_('Password Confirm'));
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'password.js.php', 'footer', 10);

$html->getPageHeader();

unset($_SESSION['ss_password_check']);
?>

<form name="fpassword" id="fpassword" class="form-login form-token" method="post" action="<?php echo NT_MEMBER_URL; ?>/passwordCheck.php">
    <input type="hidden" name="mode" value="<?php echo $_SESSION['ss_password_mode']; ?>">

    <h1 class="h3 mb-3 font-weight-normal"><?php echo $html->title; ?></h1>

    <label for="inputEmail" class="sr-only"><?php echo _('ID or Email'); ?></label>
    <input type="text" name="id" id="inputEmail" class="form-control" placeholder="<?php echo _('ID or Email'); ?>" data-toggle="popover" data-trigger="focus" required autofocus>
    <label for="inputPassword" class="sr-only"><?php echo _('Password'); ?></label>
    <input type="password" name="pass" id="inputPassword" class="form-control" placeholder="<?php echo _('Password'); ?>" data-toggle="popover" data-trigger="focus" required>

    <div class="mt-3">
        <button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo _('Submit'); ?></button>
    </div>

</form>

<!-- Modal -->
<div class="modal fade" id="withdrawalConfirm" tabindex="-1" role="dialog" aria-labelledby="withdrawalConfirm" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _('Confirm membership withdrawal'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center leave-confirm text-danger">
                    <i class="icon" data-feather="alert-circle"></i>
                </div>
                <div class="pt-4 text-center">
                    <?php echo _('Are you sure you want to continue with membership withdrawal?'); ?>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?php echo NT_URL; ?>" class="btn btn-secondary"><?php echo _('Cancel'); ?></a>
                <a href="<?php echo NT_LINK_LEAVE; ?>" class="btn btn-primary"><?php echo _('Confirm'); ?></a>
            </div>
        </div>
    </div>
</div>

<?php
$html->getPageFooter();
?>