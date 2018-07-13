<?php
require_once './_common.php';
require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'board.inc.php';

if (!$_SESSION['ss_view_'.$id.'_'.$no] !== true)
    dieJson('');

$no = (int)preg_replace('#[^0-9]#', '', $_REQUEST['no']);
$cn = (int)preg_replace('#[^0-9]#', '', $_REQUEST['cn']);

$view = getPost($no, 'bo_no');

if (!$view['bo_no'])
    dieJson('');

$sql = " select * from `{$nt['board_comment_table']}` where bo_no = :bo_no order by cm_parent asc, cm_reply asc, cm_no asc ";

$DB->prepare($sql);
$DB->bindValue(':bo_no', $no);
$DB->execute();
$result = $DB->fetchAll();

require_once NT_THEME_PATH.DIRECTORY_SEPARATOR.'comment.php';