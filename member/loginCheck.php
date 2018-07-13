<?php
require_once './_common.php';

$token   = new TOKEN();
$captcha = new reCAPTCHA();

// 토큰체크
if (!$token->verifyToken($_POST['token']))
    dieJson(_('Please use it in the correct way.'));

$id   = trim($_POST['id']);
$pass = trim($_POST['pass']);

if (!$id)
    die(json_encode(array('error' => _('Please enter your member ID or Email address.'), 'element' => 'id')));

if (!$pass)
    die(json_encode(array('error' => _('Please enter a Password.'), 'element' => 'pass')));

if (strpos($id, '@') !== false) {
    if (!preg_match(NT_EMAIL_PATTERN, $id))
        die(json_encode(array('error' => _('Please enter the Email format to fit.'), 'element' => 'id')));

    $searchQuery = " mb_email = :mb_email ";
    $searchValue = array(':mb_email' => $id);
} else {
    if (!preg_match(NT_MEMBER_ID_PATTERN, $id))
        die(json_encode(array('error' => _('Please enter the ID format to fit.'), 'element' => 'id')));

    $searchQuery = " mb_id = :mb_id ";
    $searchValue = array(':mb_id' => $id);
}

$sql = " select mb_uid, mb_password, mb_2factor_auth, mb_2factor_secret, mb_leave, mb_block from `{$nt['member_table']}` where {$searchQuery} ";

$DB->prepare($sql);
$DB->execute($searchValue);

$row = $DB->fetch();

if (!$row['mb_uid'])
    dieJson(_('Member information does not exist.'));

if (!passwordVerify($pass, $row['mb_password']))
    dieJson(_('The member information does not match.'));

if (!isNullTime((string)$row['mb_leave']))
    dieJson(_('I am a member who has left.'));

if (!isNullTime((string)$row['mb_block']))
    dieJson(_('Member access is blocked.'));

// Google 2-factor authentication
if (__c('cf_2factor_auth') && $row['mb_2factor_auth'] && $row['mb_2factor_secret']) {
    if (!isset($_SESSION['ss_2factor_auth_login']) || !$_SESSION['ss_2factor_auth_login']) {
        $_SESSION['ss_2factor_auth_login'] = true;
        dieJson('2factor-auth');
    }

    $ga = new PHPGangsta_GoogleAuthenticator();

    $oneCode = isset($_POST['onecode']) ? $_POST['onecode'] : '';

    $checkResult = $ga->verifyCode($row['mb_2factor_secret'], $oneCode, 2);    // 2 = 2*30sec clock tolerance
    if (!$checkResult)
        die(json_encode(array('error' => _('Please enter the one time password correctly.'), 'element' => 'onecode')));

    unset($_SESSION['ss_2factor_auth_login']);
}

// reCAPTCHA 체크
if (!$captcha->checkResponse((string)$_POST['g-recaptcha-response']))
    dieJson(_('Please check the anti-spam code.'));

// 세션에 uid 기록
$_SESSION['ss_uid'] = $row['mb_uid'];

dieJson('');