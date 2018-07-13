<?php
require_once './_common.php';

use UAParser\Parser;

$html->setPageTitle(_('Visiting'));

$html->addStyleSheet('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css', 'header', 10);
$html->addJavaScript('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.min.js', 'footer', 10);
$html->addJavaScript('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/locales/bootstrap-datepicker.ko.min.js', 'footer', 10);

require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'header.php';

$fdate = '';
if (isset($_REQUEST['fdate'])) {
    if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $_REQUEST['fdate']))
        $fdate = $_REQUEST['fdate'];
}

$tdate = '';
if (isset($_REQUEST['tdate'])) {
    if (preg_match('#^\d{4}-\d{2}-\d{2}$#', $_REQUEST['tdate']))
        $tdate = $_REQUEST['tdate'];
}

$sqlSearch = array();
$sqlValue  = array();
$schQuery  = '';

if ($fdate && $tdate) {
    $sqlSearch[] = " vi_date between :fdate and :tdate ";
    $sqlValue[':fdate'] = $fdate;
    $sqlValue[':tdate'] = $tdate;
}

if($q) {
    if(!$c)
        $c = 'vi_ip';

    switch($c) {
        case 'vi_ip':
            $sqlSearch[] = " {$c} = :q ";
            $sqlValue[':q'] = $q;
            break;
        default:
            $sqlSearch[] = " {$c} like :q ";
            $sqlValue[':q'] = '%'.$q.'%';
            break;
    }

    if($q != $s)
        $p = 1;
}

if (!empty($sqlSearch))
    $schQuery = " where ".implode(' and ', $sqlSearch);

$sqlCommon = " from `{$nt['visit_table']}` ";

$sqlOrder  = " order by vi_date desc, vi_time desc ";

$sql = " select count(*) as cnt {$sqlCommon} {$schQuery} ";

$DB->prepare($sql);
$DB->execute($sqlValue);
$row = $DB->fetch();

$totalCount = (int)$row['cnt'];

$rows = __c('cf_page_rows');
$totalPage  = ceil($totalCount / $rows);
$fromRecord = ($p - 1) * $rows;

$sqlLimit  = " limit :fr, :to ";

$sqlValue[':fr'] = (int)$fromRecord;
$sqlValue[':to'] = (int)$rows;

$sql = " select * {$sqlCommon} {$schQuery} {$sqlOrder} {$sqlLimit} ";

$DB->prepare($sql);
$DB->execute($sqlValue);
$result = $DB->fetchAll();

if ($fdate)
    $qstr = array_merge(array('fdate' => $fdate), $qstr);

if ($tdate)
    $qstr = array_merge(array('tdate' => $tdate), $qstr);

$qstr = http_build_query($qstr, '', '&amp;');
?>

<div class="col mb-3">
    <form name="fsearch" method="get" autocomplete="off">
        <input type="hidden" name="p" value="<?php echo $p; ?>">
        <input type="hidden" name="s" value="<?php echo getHtmlChar($q); ?>">

        <div class="form-group row">
            <div class="row col-md-2">
                <div class="input-group">
                    <input type="text" name="fdate" value="<?php echo $fdate; ?>" class="form-control form-control-sm datepicker">
                    <div class="input-group-append">
                        <span class="input-group-text calendar-button">
                            <i data-feather="calendar"></i>
                        </span>
                    </div>
                </div>
            </div>

            <div class="row text-center ml-0 px-2 pt-2 text-muted">
                <i data-feather="arrow-right"></i>
            </div>

            <div class="row col-md-2 ml-2">
                <div class="input-group">
                    <input type="text" name="tdate" value="<?php echo $tdate; ?>" class="form-control form-control-sm datepicker">
                    <div class="input-group-append">
                        <span class="input-group-text calendar-button">
                            <i data-feather="calendar"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group row">
            <div class="row col-md-2">
                <select name="c" id="c" class="custom-select custom-select-sm mr-sm-2">
                    <option value="vi_ip"<?php echo getSelected($c, 'vi_ip'); ?>><?php echo _('IP'); ?></option>
                    <option value="vi_referer"<?php echo getSelected($c, 'vi_referer'); ?>><?php echo _('Referrer'); ?></option>
                </select>
            </div>

            <div class="row col-md-2">
                <input type="text" name="q" id="q" value="<?php echo getHtmlChar($q); ?>" class="form-control form-control-sm">
            </div>

            <div class="row col">
                <button type="submit" class="btn btn-primary ml-2 btn-sm"><?php echo _('Search'); ?></button>
            </div>
        </div>
    </form>
</div>

 <div class="col row">
    <?php echo _('Total'); ?> : <?php echo number_format($totalCount); ?>
</div>

<div class="col mt-2">
    <div class="row bg-light border py-2">
        <div class="col-1 text-center"><?php echo _('Number'); ?></div>
        <div class="col-2 text-center"><?php echo _('IP'); ?></div>
        <div class="col text-center"><?php echo _('Referrer'); ?></div>
        <div class="col-1 text-center"><?php echo _('OS'); ?></div>
        <div class="col-1 text-center"><?php echo _('Device'); ?></div>
        <div class="col-1 text-center"><?php echo _('Browser'); ?></div>
        <div class="col-2 text-center"><?php echo _('Visiting date'); ?></div>
    </div>

    <ul class="list-unstyled mb-0">
        <?php
        $parser = Parser::create();
        $num = $totalCount;

        for ($i = 0; $row = array_shift($result); $i++) {
            $os      = '';
            $device  = '';
            $browser = '';

            if ($row['vi_agent']) {
                $ua = $parser->parse($row['vi_agent']);

                $os      = $ua->os->family;
                $device  = $ua->device->family;
                $browser = $ua->ua->family;
            }

            $date = substr($row['vi_date'], 2).' '.$row['vi_time'];
        ?>
        <li class="row py-2 border-bottom">
            <div class="col-1 text-center my-auto"><?php echo $num; ?></div>
            <div class="col-2 text-center my-auto"><?php echo getHtmlChar($row['vi_ip']); ?></div>
            <div class="col text-truncate my-auto"><?php echo getHtmlChar((string)$row['vi_referer']); ?></div>
            <div class="col-1 text-center my-auto"><?php echo $os; ?></div>
            <div class="col-1 text-center my-auto"><?php echo $device; ?></div>
            <div class="col-1 text-center my-auto"><?php echo $browser; ?></div>
            <div class="col-2 text-center my-auto"><?php echo $date; ?></div>
        </li>
        <?php
            $num--;
        }

        if ($i == 0)
            echo '<li class="row d-block text-center py-5 border-bottom">'._('No data available.').'</li>';
        ?>
    </ul>
</div>

<script type="text/javascript">
jQuery(function () {
    jQuery(".calendar-button").on("click", function() {
        jQuery(this).closest(".input-group").find("input").focus();
    });

    jQuery(".datepicker").datepicker({
        format: "yyyy-mm-dd",
        language: 'ko'
    });
});
</script>

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