<?php
require_once './_common.php';

$token = new TOKEN();

if ($isMember)
    dieJson('');

// 토큰체크
if (!$token->verifyToken($_POST['token']))
    dieJson(_('Please use it in the correct way.'));

$email = trim($_POST['email']);

if (!$email)
    dieJson(_('Please enter Email.'));

if (!preg_match(NT_EMAIL_PATTERN, $email))
    dieJson(_('Please enter the Email format to fit.'));

$sql = " select mb_uid from `{$nt['member_table']}` where mb_email = :mb_email ";

$DB->prepare($sql);
$DB->bindValue(':mb_email', $email);
$DB->execute();

$row = $DB->fetch();

if (!$row['mb_uid'])
    dieJson(_('Member information does not exist.'));

// 임시비밀번호
$len = (int)__c('cf_password_length');
if($len < 1)
    $len = 10;

$str = randomChar($len);

$pass = passwordCreate($str);

$sql = " update `{$nt['member_table']}` set mb_password = :mb_password where mb_uid = :mb_uid ";

$DB->prepare($sql);
$DB->bindValueArray([':mb_password' => $pass, ':mb_uid' => $row['mb_uid']]);
$result = $DB->execute();

if ($result) {
    $subject = __c('cf_site_name').' '._('The new password you requested.');

    $content = "";

    $content .= '<p style="padding:30px 0;font-size:16px;">'._('New password').' : <strong>'.$str.'</strong></p>';
    $content .= '<p style="padding-top: 30px;">';
    $content .= '<a href="'.NT_LINK_LOGIN.'" style="display:inline-block;background-color:#fc654e;padding:5px 20px;color:#fff;font-size:16px;font-weight:bold;">'._('Log In').' &raquo;</a>';
    $content .= '</p>';

    $mailer = new MAILER();

    $mailer->setFrom(__c('cf_email_address'));
    $mailer->setFromName(__c('cf_email_name'));
    $mailer->setAddress($email);
    $mailer->setSubject($subject);
    $mailer->setContent($content);

    $result = $mailer->send();

    dieJson(_('You have successfully sent a new password to the email you entered.'), 'success');
} else {
    dieJson(_('There was an error processing your request. please try again.'));
}