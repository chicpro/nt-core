<?php
require_once './_common.php';
require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'board.inc.php';

$no = (int)preg_replace('#[^0-9]#', '', $_REQUEST['no']);
$cn = (int)preg_replace('#[^0-9]#', '', $_REQUEST['cn']);

if ($_REQUEST['action'] == 'edit' || $_REQUEST['action'] == 'reply') {
    $comment = getComment($cn);

    if (!$comment['cm_no'])
        dieJson(_('The comment does not exist.'));

    if ($_REQUEST['action'] == 'edit') {
        if ($comment['mb_uid'])
            $mb = getMember($comment['mb_uid'], 'mb_level');

        if ($member['mb_level'] < $mb['mb_level'])
            dieJson(_('You can not edit comment made by members of a higher level than yourself.'));

        if (!$isAdmin) {
            if ($member['mb_uid']) {
                if ($member['mb_uid'] != $comment['mb_uid'])
                    dieJson(_('You can not edit it because it is not your own comment.'));
            } else {
                if ($comment['mb_uid'])
                    dieJson(_('Please log in and edit your comment.'));
            }
        }
    }

    if ($_REQUEST['action'] == 'reply') {
        if (!$isAdmin && $member['mb_level'] < $board['bo_comment_level'])
            dieJson(_('You do not have permission to reply comment.'));
    }

    if ($_REQUEST['action'] == 'edit')
        $w = 'u';
    else
        $w = 'r';

    ob_start();
    require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'commentForm.php';
    $form = ob_get_contents();
    ob_end_clean();

    die(json_encode(['error' => '', 'form' => $form]));
} else if ($_REQUEST['action'] == 'delete') {
    $comment = getComment($cn);

    if (!$comment['cm_no'])
        dieJson(_('The comment does not exist.'));

    if ($comment['mb_uid'])
        $mb = getMember($comment['mb_uid'], 'mb_level');

    if ($member['mb_level'] < $mb['mb_level'])
        dieJson(_('You can not delete comment made by members of a higher level than yourself.'));

    if (!$isAdmin) {
        if ($member['mb_uid']) {
            if ($member['mb_uid'] != $comment['mb_uid'])
                dieJson(_('You can not delete it because it is not your own comment.'));
        } else {
            if ($comment['mb_uid'])
                dieJson(_('Please log in and delete your comment.'));
        }
    }

    if ($isGuest) {
        $w = 'd';

        ob_start();
        require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'commentForm.php';
        $form = ob_get_contents();
        ob_end_clean();

        die(json_encode(['error' => '', 'form' => $form]));
    } else {
        $token = new TOKEN();
        $t = $token->getToken();

        $_REQUEST['w']  = 'd';
        $_POST['token'] = $t;

        require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'commentUpdate.php';
    }
} else {
    dieJson(_('Please use it in the correct way.'));
}