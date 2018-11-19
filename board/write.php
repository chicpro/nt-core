<?php
require_once './_common.php';
require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'board.inc.php';

$tag = new TAGS();

$qstr['p'] = $p;

$w  = substr($_REQUEST['w'], 0, 1);
$no = (int)preg_replace('#[^0-9]#', '', $_REQUEST['no']);

$write = array();
$files = array();
$links = array();

if ($w == 'u') {
    $write = getPost($no);

    if (!$write['bo_no'])
        alert(_('The post does not exist.'));

    if (!$isAdmin && ($isGuest || $write['mb_uid'] != $member['mb_uid']) && $member['mb_level'] < $board['bo_write_level'])
        alert(_('You do not have permission to write a post.'));

    if ($write['mb_uid'])
        $mb = getMember($write['mb_uid'], 'mb_level');

    if ($member['mb_level'] < $mb['mb_level'])
        alert(_('You can not edit posts made by members of a higher level than yourself.'));

    if ($member['mb_uid']) {
        if (!$isAdmin && $member['mb_uid'] != $write['mb_uid'])
            alert(_('You can not edit it because it is not your own post.'));
    } else {
        if ($write['mb_uid']) {
            alert(_('Please log in and edit your post.'));
        } else {
            if (!$_SESSION['ss_password_'.$id.'_'.$no]) {
                $passwordHref = NT_URL.'/'.BOARD_DIR.'/'.$id.'/edit/'.$no.'/password';
                if (!empty($qstr))
                    $passwordHref .= '?'.http_build_query($qstr, '', '&amp;');

                gotoUrl($passwordHref);
            }
        }
    }

    if ($write['bo_file']) {
        $sql = " select * from `{$nt['board_file_table']}` where bo_no = :bo_no order by fl_no asc ";
        $DB->prepare($sql);
        $DB->bindValue(':bo_no', $no);
        $DB->execute();
        $files = $DB->fetchAll();
    }

    if ($write['bo_link']) {
        $sql = " select * from `{$nt['board_link_table']}` where bo_no = :bo_no order by ln_no asc ";
        $DB->prepare($sql);
        $DB->bindValue(':bo_no', $no);
        $DB->execute();
        $result = $DB->fetchAll();

        for ($i = 0; $row = array_shift($result); $i++) {
            if ($row['ln_url'])
                $links[] = $row['ln_url'];
        }
    }

    $tags = $tag->getTags('board', $no);
} else if ($w == 'r') {
    $write = getPost($no);

    if (!$write['bo_no'])
        alert(_('The post does not exist.'));

    if (!$isAdmin && $member['mb_level'] < $board['bo_reply_level'])
            alert(_('You do not have permission to reply.'));

    $write['bo_subject'] = 'Re: '.$write['bo_subject'];
    $write['bo_content'] = '';
} else {
    if (!$isAdmin && $member['mb_level'] < $board['bo_write_level'])
        alert(_('You do not have permission to write a post.'));
}

for ($i = ($board['bo_file_limit'] - count($files)); $i > 0; $i--) {
    $files[]['fl_name'] = '';
}

for ($i = ($board['bo_link_limit'] - count($links)); $i > 0; $i--) {
    $links[] = '';
}

$category = array();
if ($board['bo_category']) {
    $categories = explode(',', $board['bo_category']);
    $categories = array_map('trim', $categories);

    if (!empty($categories)) {
        foreach ($categories as $val) {
            if ($val)
                $category[] = $val;
        }
    }
}

$tag->tagEditor();

$qstr = http_build_query($qstr, '', '&amp;');

$canonical = NT_URL.'/'.BOARD_DIR.'/'.$id.'/p/'.$p;
$html->addMetaTag('canonical', $canonical);
$html->addOGTag('url', $canonical);

require_once NT_BOARD_SKIN_PATH.DIRECTORY_SEPARATOR.'write.php';