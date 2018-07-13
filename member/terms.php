<?php
require_once './_common.php';

if ($isMember)
    gotoUrl(NT_URL);

$html->setPageTitle(_('Sign Up'));
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'signup.js.php', 'footer', 10);

$html->getPageHeader();
?>

<div class="col my-5">
    <form name="fterms" id="form-terms" method="post" class="form-terms" action="<?php echo NT_URL; ?>/<?php echo MEMBER_DIR; ?>/signup">
        <input type="hidden" name="terms_agree" value="">
        <input type="hidden" name="privacy_agree" value="">

        <h1 class="h3 mb-5 font-weight-normal"><?php echo $html->title; ?></h1>

        <h3 class="h4"><?php echo _('Terms and conditions'); ?></h3>
        <div class="col border px-2 py-2 terms terms-conditions"><?php echo loadTextFile(NT_MEMBER_PATH.DIRECTORY_SEPARATOR.'termsConditions.txt'); ?></div>
        <div class="form-check form-check-inline col">
            <input type="checkbox" name="terms_agree" id="terms_agree" class="form-check-input" value="1" data-toggle="popover" data-trigger="focus">
            <label for="terms_agree" class="col-form-label text-muted"><?php echo _('I agree.'); ?></label>
        </div>

        <h3 class="h4 mt-4"><?php echo _('Privacy policy'); ?></h3>
        <div class="col border px-2 py-2 terms terms-privacy"><?php echo loadTextFile(NT_MEMBER_PATH.DIRECTORY_SEPARATOR.'termsPrivacy.txt'); ?></div>
        <div class="form-check form-check-inline col">
            <input type="checkbox" name="privacy_agree" id="privacy_agree" class="form-check-input" value="1" data-toggle="popover" data-trigger="focus">
            <label for="privacy_agree" class="col-form-label text-muted"><?php echo _('I agree.'); ?></label>
        </div>

        <div class="col mt-5 text-center">
            <button class="btn btn-lg btn-primary terms-button" type="submit"><?php echo _('Sign Up'); ?></button>
        </div>
    </form>
</div>

<?php
$html->getPageFooter();
?>