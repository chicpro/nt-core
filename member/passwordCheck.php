<?php
require_once './_common.php';

unset($_SESSION['ss_password_check']);

$token = new TOKEN();

// 토큰체크
if (!$token->verifyToken((string)$_POST['token']))
    dieJson(_('Please use it in the correct way.'));

    $id   = trim($_POST['id']);
    $pass = trim($_POST['pass']);

    if (!$id)
        dieJson(_('Please enter your member ID or Email address.'));

    if (!$pass)
        dieJson(_('Please enter a Password.'));

    if (strpos($id, '@') !== false) {
        if (!preg_match(NT_EMAIL_PATTERN, $id))
            dieJson(_('Please enter the Email format to fit.'));

        $searchQuery = " mb_email = :mb_email ";
        $searchValue = array(':mb_email' => $id);
    } else {
        if (!preg_match(NT_MEMBER_ID_PATTERN, $id))
            dieJson(_('Please enter the ID format to fit.'));

        $searchQuery = " mb_id = :mb_id ";
        $searchValue = array(':mb_id' => $id);
    }

    $sql = " select mb_uid, mb_password from `{$nt['member_table']}` where {$searchQuery} ";

    $DB->prepare($sql);
    $DB->execute($searchValue);

    $row = $DB->fetch();

    if (!$row['mb_uid'])
        dieJson(_('Member information does not exist.'));

    if ($member['mb_uid'] != $row['mb_uid'] || !passwordVerify($pass, $row['mb_password']))
        dieJson(_('The member information does not match.'));

$_SESSION['ss_password_check'] = $_SESSION['ss_password_mode'];

$href = '';
switch ($_SESSION['ss_password_mode']) {
    case 'leave':
        $href = NT_LINK_LEAVE;
        break;
    default:
        $href = NT_LINK_ACCOUNT;
        break;
}

unset($_SESSION['ss_password_mode']);
die(json_encode(array('error'=>'', 'href'=>$href)));