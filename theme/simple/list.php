<?php
$html->setPageTitle(getHtmlChar($board['bo_title']));
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'board.js.php', 'footer', 10);

$html->getPageHeader();

$writeButton = '<a href="'.NT_URL.'/'.BOARD_DIR.'/'.$id.'/write" class="btn btn-sm btn-outline-info">'._d('Write a post', THEME_LOCALE_DOMAIN).'</a>';
?>

<div class="mb-4">
    <div class="clearfix my-3">
        <div class="float-left">
            <h1 class="h3"><?php echo $html->title; ?></h1>
        </div>
        <div class="float-right">
            <span class="small d-inline-block mt-3 mr-3">
                <?php echo _d('All posts', THEME_LOCALE_DOMAIN); ?> : <?php echo number_format($totalCount); ?>
            </span>
            <span>
                <?php echo $writeButton; ?>
            </span>
        </div>
    </div>

    <div class="clearfix px-3">
        <div class="row bg-light py-2 border">
            <div class="col-1"></div>
            <div class="col text-center"><?php echo _d('Subject', THEME_LOCALE_DOMAIN); ?></div>
            <div class="col-2 text-center"><?php echo _d('Writer', THEME_LOCALE_DOMAIN); ?></div>
            <div class="col-md-auto text-center"><?php echo _d('Posted', THEME_LOCALE_DOMAIN); ?></div>
            <div class="col-1 text-center"><?php echo _d('Views', THEME_LOCALE_DOMAIN); ?></div>
        </div>
    </div>

    <ul class="list-unstyled mb-0">
        <?php
        $listNo = $totalCount;
        $noticeIcon = '<span class="icon-notice text-info" data-feather="bell"></span>';

        for ($i = 0; $row = array_shift($result); $i++) {
            $subject = $board['bo_subject_len'] > 0 ? getSubstr($row['bo_subject'], $board['bo_subject_len']) : $row['bo_subject'];
            $subject = getHtmlChar($subject);
            $postLink = NT_URL.'/'.BOARD_DIR.'/'.$row['bo_id'].'/'.$row['bo_no'];
            $href = $postLink.($qstr ? '?'.$qstr : '');

            if ($row['bo_notice'])
                $noticeFont = 'text-info';
            else
                $noticeFont = 'text-body';

            if ($row['bo_reply'])
                $replyIcon = '<i class="icon mr-1" data-feather="corner-down-right"></i>';
            else
                $replyIcon = '';

            if ($row['bo_secret'])
                $secretIcon = '<i class="icon mr-1" data-feather="lock"></i>';
            else
                $secretIcon = '';

            $bo_category = (trim($row['bo_category']) ? '<span class="text-muted">'.getHtmlChar($row['bo_category']).' | </span>' : '');

            $view = $replyIcon.$secretIcon.'<a href="'.$href.'" class="'.$noticeFont.'"><h3 class="h6 mb-0">'.$subject.'</h3></a>';
        ?>
        <li class="row mx-1 py-2 border-bottom board-list">
            <div class="col-1 text-center text-secondary"><?php echo ($row['bo_notice'] ? $noticeIcon : $listNo); ?></div>
            <div class="col text-truncate"><?php echo $bo_category.$view; ?></div>
            <div class="col-2"><?php echo getHtmlChar($row['bo_name']); ?></div>
            <div class="col-md-auto text-right"><?php echo getRichTime($row['bo_date']); ?></div>
            <div class="col-1 text-right"><?php echo number_format($row['bo_view']); ?></div>
        </li>
        <?php
            $listNo--;
        }

        if ($i == 0)
            echo '<li class="col text-center py-5 border-bottom">'._d('No posts found.', THEME_LOCALE_DOMAIN).'</li>';
        ?>
    </ul>

    <div class="mt-3">
        <div class="text-center justify-content-center">
            <form name="fsearch" method="get" class="form-inline d-inline" autocomplete="off">
                <input type="hidden" name="p" value="<?php echo $p; ?>">
                <input type="hidden" name="s" value="<?php echo getHtmlChar($q); ?>">

                <select name="c" id="c" class="custom-select custom-select-sm mr-sm-2">
                    <option value="bo_subject"<?php echo getSelected($c, 'bo_subject'); ?>><?php echo _d('Subject', THEME_LOCALE_DOMAIN); ?></option>
                    <option value="bo_content"<?php echo getSelected($c, 'bo_content'); ?>><?php echo _d('Content', THEME_LOCALE_DOMAIN); ?></option>
                </select>

                <input type="text" name="q" id="q" value="<?php echo getHtmlChar($q); ?>" required class="form-control form-control-sm">

                <button type="submit" class="btn btn-primary ml-2 btn-sm"><?php echo _d('Search', THEME_LOCALE_DOMAIN); ?></button>
            </form>
        </div>
    </div>

    <div class="clearfix">
        <div class="float-right">
            <?php echo $writeButton; ?>
        </div>
    </div>

    <?php
    $urlPattern = NT_URL.'/'.BOARD_DIR.'/'.$id.'/p/(:num)';
    if ($paging = getPaging($totalCount, (int)$board['bo_page_rows'], (int)$board['bo_page_limit'], $p, $urlPattern, $qstr)) { ?>
    <nav>
        <?php echo $paging; ?>
    </nav>
    <?php } ?>
</div>

<?php
$html->getPageFooter();
?>