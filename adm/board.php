<?php
require_once './_common.php';

$html->setPageTitle(_('Board'));

require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'header.php';

$sqlCommon = " from `{$nt['board_config_table']}` ";

$sqlSearch = '';
$sValue = '';

if($q) {
    if(!$c)
        $c = 'bo_id';

    switch($c) {
        case 'bo_id':
            $sqlSearch = " where {$c} = :q ";
            $qValue = $q;
            break;
        default:
            $sqlSearch = " where {$c} like :q ";
            $qValue = '%'.$q.'%';
            break;
    }

    if($q != $s)
        $p = 1;
}

$sqlOrder  = " order by bo_id asc ";

$sql = " select count(*) as cnt {$sqlCommon} {$sqlSearch} ";

$DB->prepare($sql);
if($qValue)
    $DB->bindValue(':q', $qValue);
$DB->execute();
$row = $DB->fetch();

$totalCount = $row['cnt'];

$rows = __c('cf_page_rows');
$totalPage  = ceil($totalCount / $rows);
$fromRecord = ($p - 1) * $rows;

$sqlLimit  = " limit :fr, :to ";

$sql = " select * {$sqlCommon} {$sqlSearch} {$sqlOrder} {$sqlLimit} ";

$DB->prepare($sql);
if($qValue)
    $DB->bindValue(':q', $qValue);
$DB->bindValueArray([':fr' => (int)$fromRecord, ':to' => (int)$rows]);
$DB->execute();
$result = $DB->fetchAll();

$qstr1 = $qstr;
$qstr1['p'] = $p;

$qstr  = http_build_query($qstr, '', '&amp;');
$qstr1 = http_build_query($qstr1, '', '&amp;');
?>

<div class="clearfix mb-3">
    <div class="float-left pt-1 tcount">
        <?php echo _('Total'); ?> : <?php echo number_format($totalCount); ?>
        <a class="ml-2" href="./boardForm.php"><i data-feather="plus-square"></i></a>
    </div>

    <div class="float-right">
        <form name="fsearch" method="get" class="form-inline" autocomplete="off">
            <input type="hidden" name="p" value="<?php echo $p; ?>">
            <input type="hidden" name="s" value="<?php echo getHtmlChar($q); ?>">

            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="mr-3"><?php echo _('All items'); ?></a>

            <select name="c" id="c" class="custom-select custom-select-sm mr-sm-2">
                <option value="bo_id"<?php echo getSelected($c, 'bo_id'); ?>><?php echo _('Board ID'); ?></option>
                <option value="bo_title"<?php echo getSelected($c, 'bo_title'); ?>><?php echo _('Board Title'); ?></option>
            </select>

            <input type="text" name="q" id="q" value="<?php echo getHtmlChar($q); ?>" required class="form-control form-control-sm">

            <button type="submit" class="btn btn-primary ml-2 btn-sm"><?php echo _('Search'); ?></button>
        </form>
    </div>
</div>

<div class="col">
    <div class="row bg-light border py-2">
        <div class="col-1 text-center"><?php echo _('ID'); ?></div>
        <div class="col text-center"><?php echo _('Title'); ?></div>
        <div class="col-1 text-center"><?php echo _('Posts'); ?></div>
        <div class="col-1 text-center"><?php echo _('Comments'); ?></div>
        <div class="col-1 text-center"><?php echo _('Files'); ?></div>
        <div class="col-1 text-center"><?php echo _('Links'); ?></div>
        <div class="col-1 text-center"><?php echo _('Use'); ?></div>
        <div class="col-1 text-center"><?php echo _('Edit'); ?></div>
    </div>

    <ul class="list-unstyled mb-0">
    <?php
    for ($i = 0; $row = array_shift($result); $i++) {
        $boEdit = '<a href="./boardForm.php?bo_id='.$row['bo_id'].'&amp;'.$qstr1.'"><i data-feather="edit"></i></a>';

        $sql = " select count(*) as cnt from `{$nt['board_table']}` where bo_id = :bo_id ";
        $DB->prepare($sql);
        $DB->execute([':bo_id' => $row['bo_id']]);
        $postCount = $DB->fetchColumn();

        $sql = " select count(*) as cnt from `{$nt['board_comment_table']}` where bo_id = :bo_id ";
        $DB->prepare($sql);
        $DB->execute([':bo_id' => $row['bo_id']]);
        $commentCount = $DB->fetchColumn();

        $sql = " select count(*) as cnt from `{$nt['board_file_table']}` where bo_id = :bo_id ";
        $DB->prepare($sql);
        $DB->execute([':bo_id' => $row['bo_id']]);
        $fileCount = $DB->fetchColumn();

        $sql = " select count(*) as cnt from `{$nt['board_link_table']}` where bo_id = :bo_id ";
        $DB->prepare($sql);
        $DB->execute([':bo_id' => $row['bo_id']]);
        $linkCount = $DB->fetchColumn();
    ?>
        <li class="row py-2 border-bottom">
            <div class="col-1"><?php echo getHtmlChar($row['bo_id']); ?></div>
            <div class="col"><?php echo getHtmlChar($row['bo_title']); ?></div>
            <div class="col-1 text-right"><?php echo number_format($postCount); ?></div>
            <div class="col-1 text-right"><?php echo number_format($commentCount); ?></div>
            <div class="col-1 text-right"><?php echo number_format($fileCount); ?></div>
            <div class="col-1 text-right"><?php echo number_format($linkCount); ?></div>
            <div class="col-1 text-center"><?php echo getYN($row['bo_use']); ?></div>
            <div class="col-1 text-center"><?php echo $boEdit; ?></div>
        </tr>
    <?php
    }

    if ($i == 0)
        echo '<li class="row d-block text-center py-5 border-bottom">'._('No data available.').'</li>';
    ?>
    </ul>
</div>

<?php
$urlPattern = $_SERVER['SCRIPT_NAME'].'?p=(:num)';
if ($paging = getPaging($totalCount, __c('cf_page_rows'), __c('cf_page_limit'), $p, $urlPattern, $qstr)) { ?>
<nav class="mt-4">
    <?php echo $paging; ?>
</nav>
<?php } ?>

<?php
require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'footer.php';
?>
