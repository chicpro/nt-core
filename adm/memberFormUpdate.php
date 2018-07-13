<?php
require_once './_common.php';

$token = new TOKEN();

// 토큰체크
if (!$token->verifyToken($_POST['token'], 'ss_adm_token'))
    dieJson(_('Please use it in the correct way.'));

$uid = preg_replace('#[^0-9]#', '', $_POST['uid']);

$mb_id           = trim(strip_tags($_POST['mb_id']));
$mb_name         = trim(strip_tags($_POST['mb_name']));
$mb_email        = trim(strip_tags($_POST['mb_email']));
$mb_password     = trim($_POST['mb_password']);
$mb_admin        = (int)$_POST['mb_admin'];
$mb_level        = (int)$_POST['mb_level'];
$mb_leave        = (int)$_POST['mb_leave'];
$mb_block        = (int)$_POST['mb_block'];
$mb_memo         = trim(strip_tags($_POST['mb_memo']));
$mb_2factor_auth = ((int)$_POST['mb_2factor_auth'] == 1 ? 1 : 0);

if (!$uid) {
    if (!$mb_id)
        dieJson(_('Please enter ID.'));

    if (!preg_match(NT_MEMBER_ID_PATTERN, $mb_id))
        dieJson(_('Please enter the ID format to fit.'));
}

if (!$mb_name)
    dieJson(_('Please enter Name.'));

if (!$mb_email)
    dieJson(_('Please enter Email.'));

if (!preg_match(NT_EMAIL_PATTERN, $mb_email))
    dieJson(_('Please enter the Email format to fit.'));

if (!$uid) {
    if ((int)__c('cf_password_length') && strlen($mb_password) < (int)__c('cf_password_length'))
        dieJson(sprintf(_n('Please enter your password at least %d character.', 'Please enter your password at least %d characters.', (int)__c('cf_password_length')), (int)__c('cf_password_length')));
} else {
    if ((int)__c('cf_password_length') && $mb_password && strlen($mb_password) < (int)__c('cf_password_length'))
        dieJson(sprintf(_n('Please enter your password at least %d character.', 'Please enter your password at least %d characters.', (int)__c('cf_password_length')), (int)__c('cf_password_length')));
}

// ID 중복체크
$sql = " select count(mb_uid) as cnt from `{$nt['member_table']}` where mb_id = :mb_id and mb_uid <> :mb_uid ";

$DB->prepare($sql);
$DB->bindValueArray([':mb_id' => $mb_id, ':mb_uid' => $mb_uid]);
$DB->execute();
$cnt = $DB->fetchColumn();

if ($cnt)
    dieJson(_('This ID is duplicated.'));

// Email 중복체크
$sql = " select count(mb_uid) as cnt from `{$nt['member_table']}` where mb_email = :mb_email and mb_uid <> :mb_uid ";

$DB->prepare($sql);
$DB->bindValueArray([':mb_email' => $mb_email, ':mb_uid' => $uid]);
$DB->execute();
$cnt = $DB->fetchColumn();

if ($cnt)
    dieJson(_('This email is duplicated.'));

if ($uid) {
    $mb  = getMember($uid);

    if(!$mb['mb_uid'])
        dieJson(_('Member information does not exist.'));

    if($member['mb_level'] < $mb['mb_level'])
        dieJson(_('Members of a higher level than you can not modify.'));

    if($mb_leave == 1) {
        if(isNullTime((string)$mb['mb_leave']))
            $mb_leave = NT_TIME_YMD;
        else
            $mb_leave = $mb['mb_leave'];
    } else {
        $mb_leave = NULL;
    }

    if($mb_block == 1) {
        if(isNullTime((string)$mb['mb_block']))
            $mb_block = NT_TIME_YMD;
        else
            $mb_block = $mb['mb_block'];
    } else {
        $mb_block = NULL;
    }

    if($mb_password)
        $mb_password = passwordCreate($mb_password);
    else
        $mb_password = $mb['mb_password'];

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

    $sql = " update `{$nt['member_table']}` set mb_name = :mb_name, mb_email = :mb_email, mb_password = :mb_password, mb_2factor_auth = :mb_2factor_auth, mb_2factor_secret = :mb_2factor_secret, mb_admin = :mb_admin, mb_level = :mb_level, mb_leave = :mb_leave, mb_block = :mb_block, mb_memo = :mb_memo where mb_uid = :mb_uid ";

    $DB->prepare($sql);
    $DB->bindValueArray([
        ':mb_name'           => $mb_name,
        ':mb_email'          => $mb_email,
        ':mb_password'       => $mb_password,
        ':mb_2factor_auth'   => $mb_2factor_auth,
        ':mb_2factor_secret' => $secret,
        ':mb_admin'          => $mb_admin,
        ':mb_level'          => $mb_level,
        ':mb_leave'          => $mb_leave,
        ':mb_block'          => $mb_block,
        ':mb_memo'           => $mb_memo,
        ':mb_uid'            => $uid
    ]);

    $result = $DB->execute();
} else {
    $mb_uid         = getMemberUID();
    $mb_password    = passwordCreate($mb_password);
    $mb_level       = $mb_level;
    $mb_admin       = $mb_admin;
    $mb_date        = NT_TIME_YMDHIS;

    if($member['mb_level'] < $mb_level)
        dieJson(_('Members of a higher level than you can not add.'));

    if ($mb_2factor_auth) {
        $ga = new PHPGangsta_GoogleAuthenticator();
        $secret = $ga->createSecret();
    } else {
        $secret = null;
    }

    if($mb_leave == 1)
        $mb_leave = NT_TIME_YMD;
    else
        $mb_leave = NULL;

    if($mb_block == 1)
         $mb_block = NT_TIME_YMD;
    else
        $mb_block = NULL;

    $sql = " insert into `{$nt['member_table']}` ( mb_uid, mb_id, mb_name, mb_email, mb_password, mb_2factor_auth, mb_2factor_secret, mb_admin, mb_level, mb_leave, mb_block, mb_memo, mb_date ) values ( :mb_uid, :mb_id, :mb_name, :mb_email, :mb_password, :mb_2factor_auth, :mb_2factor_secret, :mb_admin, :mb_level, :mb_leave, :mb_block, :mb_memo, :mb_date ) ";

    $DB->prepare($sql);
    $DB->bindValueArray([
        ':mb_uid'            => $mb_uid,
        ':mb_id'             => $mb_id,
        ':mb_name'           => $mb_name,
        ':mb_email'          => $mb_email,
        ':mb_password'       => $mb_password,
        ':mb_2factor_auth'   => $mb_2factor_auth,
        ':mb_2factor_secret' => $secret,
        ':mb_admin'          => $mb_admin,
        ':mb_level'          => $mb_level,
        ':mb_leave'          => $mb_leave,
        ':mb_block'          => $mb_block,
        ':mb_memo'           => $mb_memo,
        ':mb_date'           => $mb_date
    ]);

    $result = $DB->execute();
}

if (!$result)
    dieJson(_('An error occurred while adding or editing the information. Please try again.'));
else
    dieJson('');