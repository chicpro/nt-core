<?php
require_once './_common.php';
require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'board.inc.php';

// 권한 체크
if ($member['mb_level'] < $board['bo_list_level'])
    alert(_('You do not have access to the board.'));

$sqlCommon = " from `{$nt['board_table']}` ";

$sqlSearch = " where bo_id = :bo_id ";
$sValues   = array(':bo_id' => $id);
if($q) {
    if(!$c)
        $c = 'bo_subject';

    $sqlSearch .= " and {$c} like :q ";
    $sValues[':q'] = '%'.$q.'%';

    if($q != $s)
        $p = 1;
}

$sqlOrder  = " order by bo_notice desc, bo_parent desc, bo_reply asc ";

$sql = " select count(*) as cnt {$sqlCommon} {$sqlSearch} ";

$DB->prepare($sql);
$DB->bindValueArray($sValues);
$DB->execute();
$row = $DB->fetch();

$totalCount = (int)$row['cnt'];

$rows = (int)$board['bo_page_rows'];
$totalPage  = ceil($totalCount / $rows);
if ($p < 1)
    $p = 1;
$fromRecord = ($p - 1) * $rows;

$sqlLimit  = " limit :fr, :to ";

$sql = " select * {$sqlCommon} {$sqlSearch} {$sqlOrder} {$sqlLimit} ";

$DB->prepare($sql);
$DB->bindValueArray($sValues);
$DB->bindValueArray([':fr' => (int)$fromRecord, ':to' => (int)$rows]);
$DB->execute();
$result = $DB->fetchAll();

$qstr = http_build_query($qstr, '', '&amp;');

$canonical = NT_URL.'/'.BOARD_DIR.'/'.$id.'/p/'.$p;
$html->addMetaTag('canonical', $canonical);
$html->addOGTag('url', $canonical);

require_once NT_BOARD_SKIN_PATH.DIRECTORY_SEPARATOR.'list.php';