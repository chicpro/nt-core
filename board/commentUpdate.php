<?php
require_once './_common.php';
require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'board.inc.php';

// 토큰체크
$token = new TOKEN();
if (!$token->verifyToken($_POST['token']))
    dieJson(_('Please use it in the correct way.'));

$w  = substr($_REQUEST['w'], 0, 1);
$no = (int)preg_replace('#[^0-9]#', '', $_REQUEST['no']);
$cn = (int)preg_replace('#[^0-9]#', '', $_REQUEST['cn']);

if ($w == 'd') {
    $cm = getComment($cn);

    if (!$cm['cm_no'])
        dieJson(_('The comment does not exist.'));

    if ($cm['mb_uid'])
        $mb = getMember($cm['mb_uid'], 'mb_level');

    if ($member['mb_level'] < $mb['mb_level'])
        dieJson(_('You can not delete comment made by members of a higher level than yourself.'));

    if (!$isAdmin) {
        if ($member['mb_uid']) {
            if ($member['mb_uid'] != $cm['mb_uid'])
                alert(_('You can not delete it because it is not your own comment.'));
        } else {
            if ($cm['mb_uid']) {
                dieJson(_('Please log in and delete your comment.'));
            } else {
                if (!isset($_REQUEST['cm_password']) || !$_REQUEST['cm_password'])
                    dieJson(_('Please enter a Password.'));

                if (!passwordVerify($_REQUEST['cm_password'], $cm['cm_password']))
                    dieJson(_('Comments can not be deleted because the passwords do not match.'));
            }
        }
    }

    $cnt = 0;
    if (!$cm['cm_reply']) {
        $sql = " select count(*) as cnt from `{$nt['board_comment_table']}` where cm_no <> :cm_no and cm_parent = :cm_parent and cm_reply <> 0 ";
        $DB->prepare($sql);
        $DB->execute([':cm_no' => $cn, ':cm_parent' => $cm['cm_parent']]);
        $cnt = $DB->fetchColumn();
    }

    if ($cnt > 0) {
        $content = _('The comment was deleted.');

        $sql = " update `{$nt['board_comment_table']}` set cm_content = :cm_content where cm_no = :cm_no ";
        $DB->prepare($sql);
        $DB->execute([':cm_content' => $content, ':cm_no' => $cn]);
    } else {
        $sql = " delete from `{$nt['board_comment_table']}` where cm_no = :cm_no ";
        $DB->prepare($sql);
        $DB->execute([':cm_no' => $cn]);
    }

    $sql = " select count(*) as cnt from `{$nt['board_comment_table']}` where bo_no = :bo_no ";
    $DB->prepare($sql);
    $DB->execute([':bo_no' => $no]);
    $row = $DB->fetch();

    $sql = " update `{$nt['board_table']}` set bo_comment = :bo_comment where bo_no = :bo_no ";
    $DB->prepare($sql);
    $DB->execute([':bo_comment' => (int)$row['cnt'], ':bo_no' => $no]);

    die(json_encode(['error' => '', 'count' => $row['cnt']]));
}

// reCAPTCHA 체크
if (__c('cf_recaptcha_site_key') && $board['bo_captcha_comment']) {
    $captcha = new reCAPTCHA();
    if (!$captcha->checkResponse((string)$_POST['g-recaptcha-response']))
        dieJson(_('Please check the anti-spam code.'));
}

$cm_content = trim(strip_tags($_REQUEST['cm_content']));

if (strlen($cm_content) < 1)
    dieJson(_('Please enter a Contents.'));

if ($w == 'r' || $w == 'u') {
    $cm = getComment($cn);

    if (!$cm['cm_no'])
        dieJson(_('The comment does not exist.'));
}

if ($w == '' || $w == 'u') {
    if ($w == 'u') {
        if (!$isAdmin && ($isGuest || $cm['mb_uid'] != $member['mb_uid']) && $member['mb_level'] < $board['bo_comment_level'])
            dieJson(_('You do not have permission to write a comment.'));
    } else {
        if (!$isAdmin && $member['mb_level'] < $board['bo_comment_level'])
            dieJson(_('You do not have permission to write a comment.'));
    }
} else if ($w == 'r') {
    if (!$isAdmin && $member['mb_level'] < $board['bo_comment_level'])
        dieJson(_('You do not have permission to reply comment.'));
}

if ($w == '' || $w == 'r') {
    if ($member['mb_uid']) {
        $cm_name     = $member['mb_name'];
        $cm_password = $member['mb_password'];
    } else {
        $mb_uid  = '';
        $cm_name = trim(strip_tags($_REQUEST['cm_name']));
        if (!$cm_name)
            dieJson(_('Please enter a Name.'));

        if (!isset($_REQUEST['cm_password']) || !$_REQUEST['cm_password'])
            dieJson(_('Please enter a Password.'));

        $cm_password = passwordCreate($_REQUEST['cm_password']);
    }

    if ($w == 'r') {
        $sql = " select max(cm_reply) as max_reply from `{$nt['board_comment_table']}` where cm_parent = :cm_parent ";

        $DB->prepare($sql);
        $DB->bindValue(':cm_parent', $cm['cm_parent']);
        $DB->execute();

        $row = $DB->fetch();

        $cm_reply  = $row['max_reply'] + 1;
        $cm_parent = $cm['cm_parent'];
    } else {
        $bo_reply  = 0;
        $bo_parent = 0;
    }

    $sql = " insert into `{$nt['board_comment_table']}` ( bo_id, bo_no, mb_uid, cm_name, cm_password, cm_reply, cm_parent, cm_content, cm_date, cm_ip ) values ( :bo_id, :bo_no, :mb_uid, :cm_name, :cm_password, :cm_reply, :cm_parent, :cm_content, :cm_date, :cm_ip ) ";

    $DB->prepare($sql);
    $DB->bindValueArray(
        [
            ':bo_id'        => $id,
            ':bo_no'        => $no,
            ':mb_uid'       => $mb_uid,
            ':cm_name'      => $cm_name,
            ':cm_password'  => $cm_password,
            ':cm_reply'     => $cm_reply,
            ':cm_parent'    => $cm_parent,
            ':cm_content'   => $cm_content,
            ':cm_date'      => NT_TIME_YMDHIS,
            ':cm_ip'        => $_SERVER['REMOTE_ADDR']
        ]
    );

    if (!$DB->execute())
        dieJon(_($DB->error));

    $cn = $DB->lastInsertId();

    if ($w == '') {
        $sql = " update `{$nt['board_comment_table']}` set cm_parent = :cm_parent where cm_no = :cm_no ";
        $DB->prepare($sql);
        $DB->execute([':cm_parent' => $cn, ':cm_no' => $cn]);
    }

    $sql = " select count(*) as cnt from `{$nt['board_comment_table']}` where bo_no = :bo_no ";
    $DB->prepare($sql);
    $DB->execute([':bo_no' => $no]);
    $row = $DB->fetch();

    $sql = " update `{$nt['board_table']}` set bo_comment = :bo_comment where bo_no = :bo_no ";
    $DB->prepare($sql);
    $DB->execute([':bo_comment' => (int)$row['cnt'], ':bo_no' => $no]);
} else if ($w == 'u') {
    if ($cm['mb_uid'])
        $mb = getMember($cm['mb_uid'], 'mb_level');

    if ($member['mb_level'] < $mb['mb_level'])
        dieJson(_('You can not edit comment made by members of a higher level than yourself.'));

    if (!$isAdmin) {
        if ($member['mb_uid']) {
            if ($member['mb_uid'] != $cm['mb_uid'])
                dieJson(_('You can not edit it because it is not your own comment.'));
        } else {
            if ($cm['mb_uid']) {
                dieJson(_('Please log in and edit your comment.'));
            } else {
                if (!isset($_REQUEST['cm_password']) || !$_REQUEST['cm_password'])
                    dieJson(_('Please enter a Password.'));

                if (!passwordVerify($_REQUEST['cm_password'], $cm['cm_password']))
                    dieJson(_('Comments can not be edited because the passwords do not match.'));
            }
        }
    }

    $sql = " update `{$nt['board_comment_table']}` set cm_content = :cm_content where cm_no = :cm_no ";

    $DB->prepare($sql);
    $DB->bindValueArray([':cm_content' => $cm_content, ':cm_no' => $cn]);

    if (!$DB->execute())
        dieJson(_('There was an error processing your request. Please try again.'));
} else {
    dieJson(_('Please use it in the correct way.'));
}

$sql = " select count(*) as cnt from `{$nt['board_comment_table']}` where bo_no = :bo_no ";
$DB->prepare($sql);
$DB->execute([':bo_no' => $no]);
$row = $DB->fetch();

die(json_encode(['error' => '', 'count' => $row['cnt']]));