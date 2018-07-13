<?php
require_once './_common.php';

if ($isMember)
    gotoUrl(NT_URL);

$captcha = new reCAPTCHA();

$html->setPageTitle(_('Find Password'));
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'findPassword.js.php', 'footer', 10);

if (__c('cf_recaptcha_site_key'))
    $html->addJavaScript('https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl=', 'footer', 10, '', 'async defer');

$html->getPageHeader();

if (__c('cf_recaptcha_site_key'))
    $captcha->getScript();
?>

<form name="ffind" id="ffind" class="form-find form-token" method="post" action="<?php echo NT_MEMBER_URL; ?>/findPassword2.php" autocomplete="off">

    <div class="text-center mb-3 find-password text-info">
        <i class="icon" data-feather="help-circle"></i>
    </div>

    <h5 class="mb-5"><?php echo _('Enter your e-mail address and we will send you a new password.'); ?></h5>

    <div class="mb-3">
        <label for="email"><?php echo _('Email'); ?></label>
        <input type="email" name="email" id="email" class="form-control" data-toggle="popover" data-trigger="focus" required autofocus>
    </div>

    <?php if (__c('cf_recaptcha_site_key')) { ?>
    <div class="mb-3">
        <?php $captcha->getElement(); ?>
    </div>
    <?php } ?>

    <div class="mt-3">
        <button class="btn btn-lg btn-primary btn-block" type="submit"><?php echo _('Find Password'); ?></button>
    </div>

</form>

<?php
$html->getPageFooter();
?>
