<?php
$id = preg_replace(NT_BOARD_ID_PATTERN, '', $_REQUEST['id']);

// 게시판 체크
$board = getBoardConfig($id);

if (!$board['bo_id'] || (!$isAdmin && !$board['bo_use']))
    alert(_('The board does not exist.'));

// 게시판 스킨 경로
$skinPath = NT_THEME_PATH.DIRECTORY_SEPARATOR.BOARD_SKIN_DIR.DIRECTORY_SEPARATOR.$board['bo_skin'];
if (!$board['bo_skin'] || !is_dir($skinPath))
    alert(_('Please specify the board skin correctly.'));

define('NT_BOARD_SKIN_PATH', $skinPath);
define('NT_BOARD_SKIN_URL',  str_replace(NT_THEME_PATH, NT_THEME_URL, $skinPath));
unset($skinPath);

// 게시판 css
$html->addStyleSheet(NT_BOARD_SKIN_URL.DIRECTORY_SEPARATOR.'board.css', 'header', 10);

// 게시글 리스트
$c = '';
$s = '';
$q = '';
$p = 1;

$qstr = array();

if (isset($_REQUEST['c'])) {
    $c = getSearchColumn($_REQUEST['c']);

    if ($c)
        $qstr['c'] = $c;
}

if (isset($_REQUEST['s']))
    $s = getSearchString($_REQUEST['s']);

if (isset($_REQUEST['q'])) {
    $q = getSearchString($_REQUEST['q']);

    if ($q) {
        $qstr['s'] = $q;
        $qstr['q'] = $q;
    }
}

if (isset($_REQUEST['p']))
    $p = (int)preg_replace('#[^0-9]#', '', $_REQUEST['p']);