<?php
require_once './_common.php';
require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'board.inc.php';

$tag = new TAGS();

// 권한 체크
if ($member['mb_level'] < $board['bo_view_level'])
    alert(_('You do not have permission to view posts.'));

$no = (int)preg_replace('#[^0-9]#', '', $_REQUEST['no']);

$view = getPost($no);

if (!$view['bo_no'])
    alert(_('No posts found.'));

if (!$isAdmin && $view['bo_secret']) {
    if ($view['mb_uid'])
        $mb = getMember($view['mb_uid'], 'mb_level');

    if ($member['mb_uid']) {
        if (!$isAdmin && $member['mb_uid'] != $view['mb_uid'])
            alert(_('You can not read it because it is not your own post.'));
    } else {
        if ($view['mb_uid']) {
            alert(_('Please log in and read your post.'));
        } else {
            if (!$_SESSION['ss_password_'.$id.'_'.$no]) {
                $passwordHref = NT_URL.'/'.BOARD_DIR.'/'.$id.'/read/'.$no.'/password';
                if (!empty($qstr))
                    $passwordHref .= '?'.http_build_query($qstr, '', '&amp;');

                gotoUrl($passwordHref);
            }
        }
    }
}

$files  = array();
$links  = array();
$images = array();

if ($view['bo_file']) {
    $sql = " select * from `{$nt['board_file_table']}` where bo_no = :bo_no order by fl_no asc ";
    $DB->prepare($sql);
    $DB->bindValue(':bo_no', $no);
    $DB->execute();
    $result = $DB->fetchAll();

    for ($i = 0; $row = array_shift($result); $i++) {
        if ($row['fl_file']) {
            if (preg_match('#^image/.+$#i', $row['fl_type']))
                $images[] = $row;
            else
                $files[] = $row;
        }
    }

    $viewImages = array();
    if (!empty($images)) {
        foreach ($images as $img) {
            $src = NT_FILE_PATH.DIRECTORY_SEPARATOR.$img['fl_file'];
            if (is_file($src)) {
                $viewImages[] = str_replace(NT_DATA_PATH, NT_DATA_URL, $src);
            }
        }
    }
}

if ($view['bo_link']) {
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

$view['subject'] = getHtmlChar($view['bo_subject']);
$view['content'] = getContent($view['bo_content']);

$view['name']    = getHtmlChar($view['bo_name']);
$view['date']    = substr($view['bo_date'], 2, -3);
$view['view']    = number_format($view['bo_view']);
$view['comment'] = number_format($view['bo_comment']);

if (!$_SESSION['ss_view_'.$id.'_'.$no]) {
    $sql = " update `{$nt['board_table']}` set bo_view = bo_view + 1 where bo_no = :bo_no ";
    $DB->prepare($sql);
    $DB->bindValue(':bo_no', $no);
    $DB->execute();

    $_SESSION['ss_view_'.$id.'_'.$no] = true;
}

$tags = $tag->getTags('board', $no);

$canonical = NT_URL.'/'.BOARD_DIR.'/'.$id.'/'.$no;
$ogImage = '';

$html->addMetaTag('canonical', $canonical);

if (!$view['bo_secret'] && $member['mb_level'] >= $board['bo_view_level']) {
    $html->addMetaTag('description', $view['bo_content']);
    $html->addMetaTag('keywords', $tags);
    $html->addOGTag('description', $view['bo_content']);
}

$html->addOGTag('type', 'article');
$html->addOGTag('url', $canonical);
if (!empty($viewImages)) {
    $ogImage = $viewImages[0];
} else {
    $contentImages = getEditorImages($view['bo_content']);
    if (!empty($contentImages[1]))
        $ogImage = array_shift($contentImages[1]);
}
if ($ogImage)
    $html->addOGTag('image', $ogImage);

require_once NT_THEME_PATH.DIRECTORY_SEPARATOR.'view.php';