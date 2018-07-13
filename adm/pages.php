<?php
require_once './_common.php';

$html->setPageTitle(_('Pages'));

require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'header.php';

$sqlCommon = " from `{$nt['pages_table']}` ";

$sqlSearch = '';
$sValue = '';

if($q) {
    if(!$c)
        $c = 'pg_id';

    switch($c) {
        case 'pg_no':
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

$sqlOrder  = " order by pg_no desc ";

$sql = " select count(*) as cnt {$sqlCommon} {$sqlSearch} ";

$DB->prepare($sql);
if($qValue)
    $DB->bindValue(':q', $qValue);
$DB->execute();
$row = $DB->fetch();

$totalCount = $row['cnt'];

$rows = __c('cf_page_rows');
$totalPage  = ceil($totalCount / $rows);
if ($p < 1)
    $p = 1;
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
        <a class="ml-2" href="./pagesForm.php"><i data-feather="plus-square"></i></a>
    </div>

    <div class="float-right">
        <form name="fsearch" method="get" class="form-inline" autocomplete="off">
            <input type="hidden" name="p" value="<?php echo $p; ?>">
            <input type="hidden" name="s" value="<?php echo getHtmlChar($q); ?>">

            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="mr-3"><?php echo _('All items'); ?></a>

            <select name="c" id="c" class="custom-select custom-select-sm mr-sm-2">
                <option value="pg_id"<?php echo getSelected($c, 'pg_id'); ?>><?php echo _('Page ID'); ?></option>
                <option value="pg_subject"<?php echo getSelected($c, 'pg_subject'); ?>><?php echo _('Page Subject'); ?></option>
                <option value="pg_content"<?php echo getSelected($c, 'pg_content'); ?>><?php echo _('Page Content'); ?></option>
            </select>

            <input type="text" name="q" id="q" value="<?php echo getHtmlChar($q); ?>" required class="form-control form-control-sm">

            <button type="submit" class="btn btn-primary ml-2 btn-sm"><?php echo _('Search'); ?></button>
        </form>
    </div>
</div>

<div class="col">
    <div class="row bg-light border py-2">
        <div class="col-3 text-center"><?php echo _('ID'); ?></div>
        <div class="col text-center"><?php echo _('Subject'); ?></div>
        <div class="col-2 text-center"><?php echo _('Page Created'); ?></div>
        <div class="col-1 text-center"><?php echo _('View'); ?></div>
        <div class="col-1 text-center"><?php echo _('Use'); ?></div>
        <div class="col-1 text-center"><?php echo _('Edit'); ?></div>
    </div>

    <ul class="list-unstyled mb-0">
    <?php
    for ($i = 0; $row = array_shift($result); $i++) {
        $pgView   = '<a href="'.NT_URL.'/'.urlencode($row['pg_id']).'" class="text-body" target="_blank">'.getHtmlChar($row['pg_id']).'</a>';
        $pgEdit   = '<a href="./pagesForm.php?w=u&amp;no='.$row['pg_no'].'&amp;'.$qstr1.'"><i data-feather="edit"></i></a>';
        $pgDelete = '<a href="./pagesFormUpdate.php?w=d&amp;no='.$row['pg_no'].'" class="ml-1 page-delete"><i data-feather="delete"></i></a>';
    ?>
        <li class="row py-2 border-bottom">
            <div class="col-3 text-truncate"><?php echo $pgView; ?></div>
            <div class="col text-truncate"><?php echo getHtmlChar($row['pg_subject']); ?></div>
            <div class="col-2 text-center"><?php echo substr($row['pg_date'], 2, -3); ?></div>
            <div class="col-1 text-right"><?php echo number_format($row['pg_views']); ?></div>
            <div class="col-1 text-center"><?php echo getYN($row['pg_use']); ?></div>
            <div class="col-1 text-center"><?php echo $pgEdit.$pgDelete; ?></div>
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
