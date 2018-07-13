<?php
require_once './_common.php';
require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'board.inc.php';

// 토큰체크
$token = new TOKEN();
if (!$token->verifyToken($_POST['token']))
    alert(_('Please use it in the correct way.'));

$qstr['p'] = $p;

if (!$_REQUEST['pass'])
    alert(_('Please enter a Password.'));

$no = (int)preg_replace('#[^0-9]#', '', $_REQUEST['no']);

$write = getPost($no);

if (!$write['bo_no'])
    alert(_('No posts found.'));

unset($_SESSION['ss_password_'.$id.'_'.$no]);

if (!passwordVerify($_REQUEST['pass'], $write['bo_password']))
    alert(_('Passwords do not match.'));

switch ($_REQUEST['action']) {
    case 'edit':
    case 'delete':
        $href = NT_URL.'/'.BOARD_DIR.'/'.$id.'/'.$_REQUEST['action'].'/'.$no;
        if (!empty($qstr))
            $href .= '?'.http_build_query($qstr, '', '&amp;');
        break;
    case 'read':
        $href = NT_URL.'/'.BOARD_DIR.'/'.$id.'/'.$no;
        if (!empty($qstr))
            $href .= '?'.http_build_query($qstr, '', '&amp;');
        break;
    default:
        alert(_('Please use it in the correct way.'));
        break;
}

$_SESSION['ss_password_'.$id.'_'.$no] = true;

gotoUrl($href);