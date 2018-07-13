<?php
require_once './_common.php';

$html->setPageTitle(_('Member'));

require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'header.php';

$sqlCommon = " from `{$nt['member_table']}` ";

$sqlSearch = '';
$sValue = '';

if($q) {
    if(!$c)
        $c = 'mb_id';

    switch($c) {
        case 'mb_uid':
        case 'mb_id':
        case 'mb_level':
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

$sqlOrder  = " order by mb_uid desc ";

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
        <a class="ml-2" href="./memberForm.php"><i data-feather="user-plus"></i></a>
    </div>

    <div class="float-right">
        <form name="fsearch" method="get" class="form-inline" autocomplete="off">
            <input type="hidden" name="p" value="<?php echo $p; ?>">
            <input type="hidden" name="s" value="<?php echo getHtmlChar($q); ?>">

            <a href="<?php echo $_SERVER['SCRIPT_NAME']; ?>" class="mr-3"><?php echo _('All items'); ?></a>

            <select name="c" id="c" class="custom-select custom-select-sm mr-sm-2">
                <option value="mb_id"<?php echo getSelected($c, 'mb_id'); ?>><?php echo _('ID'); ?></option>
                <option value="mb_email"<?php echo getSelected($c, 'mb_email'); ?>><?php echo _('Email'); ?></option>
                <option value="mb_level"<?php echo getSelected($c, 'mb_level'); ?>><?php echo _('Level'); ?></option>
                <option value="mb_uid"<?php echo getSelected($c, 'mb_uid'); ?>><?php echo _('Number'); ?></option>
            </select>

            <input type="text" name="q" id="q" value="<?php echo getHtmlChar($q); ?>" required class="form-control form-control-sm">

            <button type="submit" class="btn btn-primary ml-2 btn-sm"><?php echo _('Search'); ?></button>
        </form>
    </div>
</div>

<div class="col">
    <div class="row bg-light border py-2">
        <div class="col-1 text-center"><?php echo _('Number'); ?></div>
        <div class="col-2 text-center"><?php echo _('ID'); ?></div>
        <div class="col-2 text-center"><?php echo _('Name'); ?></div>
        <div class="col text-center"><?php echo _('Email'); ?></div>
        <div class="col-1 text-center"><?php echo _('Level'); ?></div>
        <div class="col-1 text-center"><?php echo _('Leave'); ?></div>
        <div class="col-1 text-center"><?php echo _('Block'); ?></div>
        <div class="col-1 text-center"><?php echo _('Member since'); ?></div>
        <div class="col-1 text-center"><?php echo _('Edit'); ?></div>
    </div>
    <ul class="list-unstyled mb-0">
        <?php
        $num = $totalCount;

        for ($i = 0; $row = array_shift($result); $i++) {
            $mbEdit = '<a href="./memberForm.php?uid='.$row['mb_uid'].'&amp;'.$qstr1.'"><i data-feather="edit"></i></a>';
        ?>
        <li class="row py-2 border-bottom">
            <div class="col-1 text-center"><?php echo $num; ?></div>
            <div class="col-2"><?php echo getHtmlChar($row['mb_id']); ?></div>
            <div class="col-2"><?php echo getHtmlChar($row['mb_name']); ?></div>
            <div class="col"><?php echo getHtmlChar($row['mb_email']); ?></div>
            <div class="col-1 text-right"><?php echo $row['mb_level']; ?></div>
            <div class="col-1 text-center"><?php echo getYN(!isNullTime((string)$row['mb_leave'])); ?></div>
            <div class="col-1 text-center"><?php echo getYN(!isNullTime((string)$row['mb_block'])); ?></div>
            <div class="col-1 text-center"><?php echo substr($row['mb_date'], 2, 8); ?></div>
            <div class="col-1 text-center"><?php echo $mbEdit; ?></div>
        </li>
        <?php
         $num--;
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
