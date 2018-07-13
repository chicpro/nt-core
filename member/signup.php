<?php
require_once './_common.php';

$w = substr($_GET['w'], 0, 1);

if ($w == '' && $isMember)
    gotoUrl(NT_URL);

$mb_uid = '';

if ($w == 'u') {
    if (!$isMember)
        gotoUrl(NT_LINK_LOGIN);

    if (!$_SESSION['ss_password_check']) {
        $_SESSION['ss_password_mode'] = 'modify';
        gotoUrl(NT_LINK_PASSWORD);
    }

    $enc = new STRENCRYPT();
    $mb_uid = $enc->encrypt($member['mb_uid']);
} else {
    if (!$_REQUEST['terms_agree'])
        alert(_('Please accept the terms and conditions.'));

    if (!$_REQUEST['privacy_agree'])
        alert(_('Please accept the privacy policy.'));
}

$captcha = new reCAPTCHA();

if ($w == 'u')
    $html->setPageTitle(_('My Account'));
else
    $html->setPageTitle(_('Sign Up'));

$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'signup.js.php', 'header', 10);

if (__c('cf_recaptcha_site_key'))
    $html->addJavaScript('https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl=', 'footer', 10, '', 'async defer');

$html->getPageHeader();

if (__c('cf_recaptcha_site_key'))
    $captcha->getScript();

$submitButton = _('Sign Up');
if ($w == 'u')
    $submitButton = _('Edit');
?>

<div class="col-md-6 mx-auto">
    <form name="fsignup" id="fsignup" class="form-signup" method="post" action="<?php echo NT_MEMBER_URL; ?>/signupRun.php" autocomplete="off">
        <input type="hidden" name="w" value="<?php echo $w; ?>">
        <input type="hidden" name="uid" value="<?php echo $mb_uid; ?>">

        <h1 class="h3 mb-3 font-weight-normal"><?php echo $html->title; ?></h1>

        <div class="form-group row">
            <label for="mb_id" class="col-md-3 col-form-label"><?php echo _('ID'); ?></label>
            <div class="col">
                <input type="text" name="mb_id" id="mb_id" class="form-control" value="<?php echo getHtmlChar($member['mb_id']); ?>" data-toggle="popover" data-trigger="focus" <?php echo ($w == '' ? 'required autofocus' : 'readonly'); ?>>
            </div>
        </div>

        <div class="form-group row">
            <label for="mb_name" class="col-md-3 col-form-label"><?php echo _('Name'); ?></label>
            <div class="col">
                <input type="text" name="mb_name" id="mb_name" class="form-control" value="<?php echo getHtmlChar($member['mb_name']); ?>" data-toggle="popover" data-trigger="focus" required<?php echo ($w == 'u' ? ' autofocus' : ''); ?>>
            </div>
        </div>

        <div class="form-group row">
            <label for="mb_email" class="col-md-3 col-form-label"><?php echo _('Email'); ?></label>
            <div class="col">
                <input type="email" name="mb_email" id="mb_email" class="form-control" value="<?php echo getHtmlChar($member['mb_email']); ?>" data-toggle="popover" data-trigger="focus" required>
            </div>
        </div>

        <div class="form-group row">
            <label for="mb_password" class="col-md-3 col-form-label"><?php echo _('Password'); ?></label>
            <div class="col">
                <input type="password" name="mb_password" id="mb_password" class="form-control" data-toggle="popover" data-trigger="focus"<?php echo ($w != 'u' ? ' required' : ''); ?>>
            </div>
        </div>

        <?php if ($w != 'u') { ?>
        <div class="form-group row">
            <label for="mb_password_re" class="col-md-3 col-form-label"><?php echo _('Re-enter password'); ?></label>
            <div class="col">
                <input type="password" name="mb_password_re" id="mb_password_re" class="form-control" data-toggle="popover" data-trigger="focus" required>
            </div>
        </div>
        <?php } ?>

        <?php
        if ($w == 'u' && __c('cf_2factor_auth')) {
        ?>
        <div class="form-group row">
            <label for="mb_2factor_auth" class="col-md-3 col-form-label"><?php echo _('Google 2-factor authentication'); ?></label>
            <div class="col">
                <select name="mb_2factor_auth" id="mb_2factor_auth" class="custom-select" required>
                    <option value="1"<?php echo getSelected(1, $member['mb_2factor_auth']); ?>><?php echo _('Used'); ?></option>
                    <option value="0"<?php echo getSelected(0, $member['mb_2factor_auth']); ?>><?php echo _('Not used'); ?></option>
                </select>
            </div>
        </div>

        <?php
        if ($member['mb_2factor_secret']) {
            $ga = new PHPGangsta_GoogleAuthenticator();
            $qrCodeUrl = $ga->getQRCodeGoogleUrl($member['mb_id'], $member['mb_2factor_secret'], parse_url(NT_URL, PHP_URL_HOST));
        ?>

        <div class="form-group row">
            <div class="col-md-3"></div>
            <div class="col">
                <img src="<?php echo $qrCodeUrl; ?>" class="d-block border p-3">
            </div>
        </div>
        <?php
            }
        }
        ?>

        <?php if (__c('cf_recaptcha_site_key')) { ?>
        <div class="form-group row">
            <label class="col-md-3 col-form-label"><?php echo _('Anti-spam code'); ?></label>
            <div class="col">
                <?php $captcha->getElement(); ?>
            </div>
        </div>
        <?php } ?>

        <div class="form-group row pl-3">
            <button class="btn btn-lg btn-primary btn-block mt-4" type="submit"><?php echo $submitButton; ?></button>
        </div>
    </form>
</div>

<!-- Modal -->
<div class="modal fade" id="signupSuccess" tabindex="-1" role="dialog" aria-labelledby="signupSuccess" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?php echo _('Sign Up'); ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="text-center signup-success text-success">
                    <i class="icon" data-feather="check-circle"></i>
                </div>
                <div class="pt-4 text-center">
                    <?php echo _('Your account has been created.'); ?>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?php echo NT_URL; ?>" class="btn btn-primary"><?php echo _('Go Home'); ?></a>
            </div>
        </div>
    </div>
</div>

<?php
$html->getPageFooter();
?>