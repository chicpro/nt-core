<?php
require_once './_common.php';

$email = trim($_POST['email']);

if (!$email)
    die(_('Please enter Email.'));

if (!preg_match(NT_EMAIL_PATTERN, $email))
    die(_('Please enter the Email format to fit.'));

$sql = " select count(mb_uid) as cnt from `{$nt['member_table']}` where mb_email = :mb_email and mb_uid <> :mb_uid ";

$DB->prepare($sql);
$DB->bindValueArray([':mb_email' => $email, ':mb_uid' => (string)$member['mb_uid']]);
$DB->execute();

$row = $DB->fetch();

if ($row['cnt'])
    die(_('This email is duplicated.'));

die('');