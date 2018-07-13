<?php
require_once './_common.php';
require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'board.inc.php';

$no = (int)preg_replace('#[^0-9]#', '', $_REQUEST['no']);

$write = getPost($no);

if (!$write['bo_no'])
    alert(_('The post does not exist.'));

if ($write['mb_uid'])
    $mb = getMember($write['mb_uid'], 'mb_level');

if ($member['mb_level'] < $mb['mb_level'])
    alert(_('You can not delete posts made by members of a higher level than yourself.'));

if ($member['mb_uid']) {
    if (!$isAdmin && $member['mb_uid'] != $write['mb_uid'])
        alert(_('You can not delete it because it is not your own post.'));
} else {
    if ($write['mb_uid']) {
        alert(_('Please log in and delete your post.'));
    } else {
        if (!$_SESSION['ss_password_'.$id.'_'.$no]) {
            $passwordHref = NT_URL.'/'.BOARD_DIR.'/'.$id.'/delete/'.$no.'/password';
            if (!empty($qstr))
                $passwordHref .= '?'.http_build_query($qstr, '', '&amp;');

            gotoUrl($passwordHref);
        }
    }
}

// Link delete
$sql = " delete from `{$nt['board_link_table']}` where bo_no = :bo_no ";
$DB->prepare($sql);
$DB->execute([':bo_no' => $no]);

// File delete
$sql = " select fl_file from `{$nt['board_file_table']}` where bo_no = :bo_no ";
$DB->prepare($sql);
$DB->execute([':bo_no' => $no]);
$files = $DB->fetchAll();

if (!empty($files)) {
    foreach ($files as $file) {
        if ($file['fl_file']) {
            $fp = NT_FILE_PATH.DIRECTORY_SEPARATOR.$file['fl_file'];

            if (is_file($fp))
                unlink($fp);
        }
    }
}

$sql = " delete from `{$nt['board_file_table']}` where bo_no = :bo_no ";
$DB->prepare($sql);
$DB->execute([':bo_no' => $no]);

// Editor image
$imgs = getEditorImages($write['bo_content']);

foreach ($imgs[1] as $src) {
    if (!preg_match('#^'.preg_quote(NT_DATA_URL).'.+$#', $src))
        continue;

    $file = str_replace(NT_DATA_URL, '', $src);

    $sql = " select count(*) as cnt from `{$nt['board_table']}` where bo_content like :file and bo_no <> :bo_no ";
    $DB->prepare($sql);
    $DB->execute([':file' => '%'.$file.'%', ':bo_no' => $no]);
    $cnt = $DB->fetchColumn();

    if ($cnt > 0)
        continue;

    unlink(NT_DATA_PATH.DIRECTORY_SEPARATOR.$file);
}

// Check reply
$cnt = 0;
if (!$write['bo_reply']) {
    $sql = " select count(*) as cnt from `{$nt['board_table']}` where bo_no <> :bo_no and bo_parent = :bo_parent and bo_reply <> 0 ";
    $DB->prepare($sql);
    $DB->bindValueArray([':bo_no' => $no, ':bo_parent' => $write['bo_parent']]);
    $DB->execute();
    $cnt = $DB->fetchColumn();
}

if ($cnt > 0 || $write['bo_comment']) {
    $content = _('The post was deleted.');

    $sql = " update `{$nt['board_table']}` set bo_subject = :bo_subject, bo_content = :bo_content, bo_file = 0, bo_link = 0 where bo_no = :bo_no ";

    $DB->prepare($sql);
    $DB->bindValueArray([
        ':bo_subject' => $content,
        ':bo_content' => $content,
        ':bo_no'      => $no
    ]);
    $DB->execute();
} else {
    $sql = " delete from `{$nt['board_table']}` where bo_no = :bo_no ";
    $DB->prepare($sql);
    $DB->execute([':bo_no' => $no]);
}

$listHref = NT_URL.'/'.BOARD_DIR.'/'.$id.'/p/'.$p;
if (!empty($qstr))
    $listHref .= '?'.http_build_query($qstr, '', '&amp;');

gotoUrl($listHref);