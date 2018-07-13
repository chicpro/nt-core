<?php
require_once './_common.php';
require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'board.inc.php';

$no = (int)preg_replace('#[^0-9]#', '', $_REQUEST['no']);
$fn = (int)preg_replace('#[^0-9]#', '', $_REQUEST['fn']);

if (!$_SESSION['ss_view_'.$id.'_'.$no])
    alert(_('Please use it in the correct way.'));

$sql = " select fl_name, fl_file from `{$nt['board_file_table']}` where bo_no = :bo_no and fl_no = :fl_no ";
$DB->prepare($sql);
$DB->bindValueArray([':bo_no' => $no, ':fl_no' => $fn]);
$DB->execute();
$file = $DB->fetch();

if (!$file['fl_file'])
    alert(_('File information does not exist.'));

if (!$isAdmin && $member['mb_level'] < $board['bo_view_level'])
    alert(_('You do not have permission to download files.'));

$filepath = NT_FILE_PATH.DIRECTORY_SEPARATOR.$file['fl_file'];
if (!is_file($filepath))
    alert(_('The file does not exist.'));

if (!$_SESSION['ss_download_'.$id.'_'.$no]) {
    $sql = " update `{$nt['board_file_table']}` set fl_down = fl_down + 1 where bo_no = :bo_no and fl_no = :fl_no ";
    $DB->prepare($sql);
    $DB->bindValueArray([':bo_no' => $no, ':fl_no' => $fn]);
    $DB->execute();

    $_SESSION['ss_download_'.$id.'_'.$no] = true;
}

$name = $file['fl_name'];

header("content-type: file/unknown");
header("content-length: ".filesize($filepath));
header("content-disposition: attachment; filename=\"$name\"");
header("content-description: php generated data");
header("pragma: no-cache");
header("expires: 0");
flush();

$fp = fopen($filepath, 'rb');

$rate = 10;

while(!feof($fp)) {
    print fread($fp, round($rate * 1024));
    flush();
    usleep(1000);
}
fclose ($fp);
flush();