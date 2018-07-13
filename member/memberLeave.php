<?php
require_once './_common.php';

if (!$isMember)
    gotoUrl(NT_LINK_LOGIN);

if ($member['mb_admin'] && $isSuper)
    alert(_('Administrators can not leave.'), NT_URL);

if (!$_SESSION['ss_password_check'] != 'leave') {
    $_SESSION['ss_password_mode'] = 'leave';
    gotoUrl(NT_LINK_PASSWORD);
}

$mb = getMember($member['mb_uid']);
if (!$mb['mb_uid']) {
    unset($member);
    unset($_SESSION['ss_uid']);
    alert(_('Member information does not exist.'), NT_URL);
}

if (!isNullTime((string)$mb['mb_leave']))
    alert(_('I am a member who has left.'), NT_URL);

// 탈퇴일자
$ymd = NT_TIME_YMD;
$level = 1;
$sql = " update `{$nt['member_table']}` set mb_leave = :mb_leave, mb_level = :mb_level where mb_uid = :mb_uid ";

$DB->prepare($sql);
$DB->bindValueArray([':mb_leave' => $ymd, ':mb_level' => $level, ':mb_uid' => $member['mb_uid']]);
$DB->execute();

unset($member);
unset($_SESSION['ss_uid']);
unset($_SESSION['ss_password_check']);

gotoUrl(NT_URL);