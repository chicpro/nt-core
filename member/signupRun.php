<?php
require_once './_common.php';

$enc     = new STRENCRYPT();
$token   = new TOKEN();
$captcha = new reCAPTCHA();

// 토큰체크
if (!$token->verifyToken($_POST['token']))
    dieJson(_('Please use it in the correct way.'));

$w            = substr($_POST['w'], 0, 1);
$mb_id        = trim(strip_tags($_POST['mb_id']));
$mb_name      = trim(strip_tags($_POST['mb_name']));
$mb_email     = trim(strip_tags($_POST['mb_email']));
$mb_password  = trim($_POST['mb_password']);
$mb_password2 = trim($_POST['mb_password_re']);

$mb_uid = '';
if ($w == 'u') {
    $ss_uid = $_SESSION['ss_uid'];
    if (isset($_POST['uid']))
        $mb_uid = $enc->decrypt($_POST['uid']);

    if($ss_uid != $mb_uid)
        dieJson(_('Please use it in the correct way.'));
}

if ($w == '') {
    if (!$mb_id)
        die(json_encode(array('error' => _('Please enter ID.'), 'element' => 'mb_id')));

    if (!preg_match(NT_MEMBER_ID_PATTERN, $mb_id))
        die(json_encode(array('error' => _('Please enter the ID format to fit.'), 'element' => 'mb_id')));
}

if (!$mb_name)
    die(json_encode(array('error' => _('Please enter Name.'), 'element' => 'mb_name')));

if (!$mb_email)
    die(json_encode(array('error' => _('Please enter Email.'), 'element' => 'mb_email')));

if (!preg_match(NT_EMAIL_PATTERN, $mb_email))
    die(json_encode(array('error' => _('Please enter the Email format to fit.'), 'element' => 'mb_email')));

if ($w == '') {
    if ((int)__c('cf_password_length') && strlen($mb_password) < (int)__c('cf_password_length'))
        die(json_encode(array('error' => sprintf(_n('Please enter your password at least %d character.', 'Please enter your password at least %d characters.', (int)__c('cf_password_length')), (int)__c('cf_password_length')), 'element' => 'mb_password')));

    if ($mb_password != $mb_password2)
        die(json_encode(array('error' => _('The password you entered does not match.'), 'element' => 'mb_password_re')));
}

if ((int)__c('cf_password_length') && $w == 'u' && $mb_password && strlen($mb_password) < (int)__c('cf_password_length'))
    die(json_encode(array('error' => sprintf(_n('Please enter your password at least %d character.', 'Please enter your password at least %d characters.', (int)__c('cf_password_length')), (int)__c('cf_password_length')), 'element' => 'mb_password')));

// ID 중복체크
$sql = " select count(mb_uid) as cnt from `{$nt['member_table']}` where mb_id = :mb_id and mb_uid <> :mb_uid ";

$DB->prepare($sql);
$DB->bindValueArray([':mb_id' => $mb_id, ':mb_uid' => $mb_uid]);
$DB->execute();
$cnt = $DB->fetchColumn();

if ($cnt)
    die(json_encode(array('error' => _('This ID is duplicated.'), 'element' => 'mb_id')));

// Email 중복체크
$sql = " select count(mb_uid) as cnt from `{$nt['member_table']}` where mb_email = :mb_email and mb_uid <> :mb_uid ";

$DB->prepare($sql);
$DB->bindValueArray([':mb_email' => $mb_email, ':mb_uid' => $mb_uid]);
$DB->execute();
$cnt = $DB->fetchColumn();

if ($cnt)
    die(json_encode(array('error' => _('This email is duplicated.'), 'element' => 'mb_email')));

// reCAPTCHA 체크
if (!$captcha->checkResponse((string)$_POST['g-recaptcha-response']))
    die(json_encode(array('error' => _('Please check the anti-spam code.'), 'element' => 'recaptcha_area')));

if($w == '') {
    $mb_uid         = getMemberUID();
    $mb_password    = passwordCreate($mb_password);
    $mb_level       = __c('cf_member_level');
    $mb_admin       = 0;
    $mb_date        = NT_TIME_YMDHIS;

    $sql = " insert into `{$nt['member_table']}` ( mb_uid, mb_id, mb_name, mb_email, mb_password, mb_2factor_auth, mb_admin, mb_level, mb_date ) values ( :mb_uid, :mb_id, :mb_name, :mb_email, :mb_password, :mb_2factor_auth, :mb_admin, :mb_level, :mb_date ) ";

    $DB->prepare($sql);
    $DB->bindValueArray([
        ':mb_id'           => $mb_id,
        ':mb_uid'          => $mb_uid,
        ':mb_name'         => $mb_name,
        ':mb_email'        => $mb_email,
        ':mb_password'     => $mb_password,
        ':mb_2factor_auth' => 0,
        ':mb_admin'        => $mb_admin,
        ':mb_level'        => $mb_level,
        ':mb_date'         => $mb_date
    ]);
    $result = $DB->execute();

    if (!$result)
        die(json_encode(array('error' => _('You can not sign up for an id or email duplication or other errors.'), 'element' => 'mb_id')));

    // 세션에 uid 기록
    $_SESSION['ss_uid'] = $mb_uid;

    dieJson('');
} else if($w == 'u') {
    $mb = getMember($mb_uid, 'mb_uid, mb_password, mb_email, mb_2factor_auth, mb_2factor_secret');
    if(!$mb['mb_uid'])
        dieJson(_('Member information does not exist.'));

    if($mb_password)
        $mb_password = passwordCreate($mb_password);
    else
        $mb_password = $mb['mb_password'];

    $mb_2factor_auth = ((int)$_POST['mb_2factor_auth'] == 1 ? 1 : 0);
    if ($mb['mb_2factor_auth'] != $mb_2factor_auth) {
        if ($mb_2factor_auth) {
            $ga = new PHPGangsta_GoogleAuthenticator();
            $secret = $ga->createSecret();
        } else {
            $secret = null;
        }
    } else {
        $secret = $mb['mb_2factor_secret'];
    }

    $sql = " update `{$nt['member_table']}` set mb_name = :mb_name, mb_email = :mb_email, mb_password = :mb_password, mb_2factor_auth = :mb_2factor_auth, mb_2factor_secret = :mb_2factor_secret where mb_uid = :mb_uid ";

    $DB->prepare($sql);
    $DB->bindValueArray([
        ':mb_name'           => $mb_name,
        ':mb_email'          => $mb_email,
        ':mb_password'       => $mb_password,
        ':mb_2factor_auth'   => $mb_2factor_auth,
        ':mb_2factor_secret' => $secret,
        ':mb_uid'            => $mb_uid
    ]);
    $DB->execute();

    //unset($_SESSION['ss_password_check']);
    dieJson('');
}
