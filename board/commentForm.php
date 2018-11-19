<?php
$isCommentName     = false;
$isCommentPassword = false;
$isCommentContent  = true;
$isCommentCaptcha  = (__c('cf_recaptcha_site_key') && $board['bo_captcha_comment']) ? true : false;

if ($_REQUEST['action'] == 'edit') {
    if ($isGuest)
        $isCommentPassword = true;
} else if ($_REQUEST['action'] == 'reply') {
    if ($isGuest) {
        $isCommentName     = true;
        $isCommentPassword = true;
    }

    $comment['cm_name']    = '';
    $comment['cm_content'] = '';
} else if ($_REQUEST['action'] == 'delete') {
    if ($isGuest)
        $isCommentPassword = true;

    $isCommentContent = false;
    $isCommentCaptcha = false;
} else {
    if ($isGuest) {
        $isCommentName     = true;
        $isCommentPassword = true;
    }
}

if ($isCommentCaptcha) {
    $captcha = new reCAPTCHA();
    $captcha->getScript();
}

require_once NT_BOARD_SKIN_PATH.DIRECTORY_SEPARATOR.'commentForm.php';

if ($isCommentCaptcha)
    echo '<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl=" async defer></script>';