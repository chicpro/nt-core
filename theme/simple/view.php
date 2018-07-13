<?php
$html->setPageTitle(getHtmlChar($board['bo_title']));
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'board.js.php', 'footer', 10);
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'comment.js.php', 'footer', 10);

$html->addScriptString('<script>
    var viewId = "'.$id.'";
    var viewNo = "'.$no.'";
</script>', 'header', 10);

$html->getPageHeader();

$qry = http_build_query(array_merge($qstr, array('p' => $p)), '', '&amp;');

$listHref = NT_URL.'/'.BOARD_DIR.'/'.$id.'/p/'.$p;
if (!empty($qstr))
    $listHref .= '?'.http_build_query($qstr, '', '&amp;');

$editHref   = NT_URL.'/'.BOARD_DIR.'/'.$id.'/edit/'.$no.'?'.$qry;
$replyHref  = NT_URL.'/'.BOARD_DIR.'/'.$id.'/reply/'.$no.'?'.$qry;
$deleteHref = NT_URL.'/'.BOARD_DIR.'/'.$id.'/delete/'.$no.'?'.$qry;
$writeHref  = NT_URL.'/'.BOARD_DIR.'/'.$id.'/write';

$listButton   = '<a href="'.$listHref.'" class="btn btn-sm btn-outline-success">'._d('Lists', THEME_LOCALE_DOMAIN).'</a>';
$editButton   = '<a href="'.$editHref.'" class="btn btn-sm btn-outline-secondary">'._d('Edit post', THEME_LOCALE_DOMAIN).'</a>';
$replyButton  = '<a href="'.$replyHref.'" class="btn btn-sm btn-outline-secondary">'._d('Reply post', THEME_LOCALE_DOMAIN).'</a>';
$deleteButton = '<a href="'.$deleteHref.'" class="btn btn-sm btn-outline-secondary post-delete">'._d('Delete post', THEME_LOCALE_DOMAIN).'</a>';
$writeButton  = '<a href="'.$writeHref.'" class="btn btn-sm btn-outline-info">'._d('Write post', THEME_LOCALE_DOMAIN).'</a>';
?>

<div class="mb-4">
    <div class="clearfix my-3">
        <h1 class="h3"><?php echo $view['subject']; ?></h1>
    </div>

    <div class="border-bottom">
        <div class="pb-1">
            <?php if ($view['bo_category']) { ?>
            <span class="text-dark mr-2"><?php echo getHtmlChar($view['bo_category']); ?></span>
            <?php } ?>
            <span class="text-secondary"><?php echo _d('Writer', THEME_LOCALE_DOMAIN); ?></span>
            <span class="font-weight-bold ml-1"><?php echo $view['name']; ?></span>
            <span class="text-secondary ml-3"><?php echo _d('Posted', THEME_LOCALE_DOMAIN); ?></span>
            <span class="ml-1"><?php echo $view['date']; ?></span>
            <span class="text-secondary ml-3"><?php echo _d('Views', THEME_LOCALE_DOMAIN); ?></span>
            <span class="ml-1"><?php echo $view['view']; ?></span>
            <span class="text-secondary ml-3"><?php echo _d('Comments', THEME_LOCALE_DOMAIN); ?></span>
            <span class="ml-1" id="comment-count"><?php echo $view['comment']; ?></span>
        </div>

        <?php if (!empty($files)) { ?>
        <ul class="list-unstyled bg-light mb-0">
            <?php
            foreach ($files as $file) {
                $fileName = '<a href="'.NT_URL.'/download/'.$id.'/'.$file['bo_no'].'/'.$file['fl_no'].'" class="text-body" target="_blank">'.getHtmlChar($file['fl_name']).'</a>';
            ?>
            <li class="file-list py-1">
                <span class="icon ml-2" data-feather="download"></span>
                <span class="ml-2"><?php echo $fileName; ?></span>
                <span class="text-secondary ml-3"><?php echo _d('Downloads', THEME_LOCALE_DOMAIN); ?></span>
                <span class="ml-1"><?php echo number_format($file['fl_down']); ?></span>
            </li>
            <?php
            }
            ?>
        </ul>
        <?php } ?>

        <?php if (!empty($links)) { ?>
        <ul class="list-unstyled bg-light mb-0">
            <?php
            foreach ($links as $link) {
                $linkUrl = '<a href="'.setHttp($link).'" class="text-body" target="_blank">'.getHtmlChar(getShortenedString($link, 30, 20)).'</a>';
            ?>
            <li class="link-list py-1">
                <span class="icon ml-2" data-feather="link"></span>
                <span class="ml-2"><?php echo $linkUrl; ?></span>
            </li>
            <?php
            }
            ?>
        </ul>
        <?php } ?>
    </div>

    <div class="text-right pt-3">
        <?php
        ob_start();
        ?>
        <span><?php echo $deleteButton; ?></span>
        <span><?php echo $editButton; ?></span>
        <span><?php echo $replyButton; ?></span>
        <span><?php echo $listButton; ?></span>
        <span><?php echo $writeButton; ?></span>
        <?php
        $buttons = ob_get_contents();
        ob_end_clean();

        echo $buttons;
        ?>
    </div>

    <div class="px-2 py-4">
        <?php if (!empty($viewImages)) { ?>
        <div>
            <?php
            foreach ($viewImages as $img) {
                echo '<p><img src="'.$img.'" class="img-fluid"></p>';
            ?>
            <?php
            }
            ?>
        </div>
        <?php } ?>
        <div>
            <?php echo $view['content']; ?>
        </div>

        <?php
        if ($tags) {
            $tags  = array_map('trim', explode(',', $tags));
            $lists = array();

            foreach ($tags as $word) {
                if ($word)
                    $lists[] = '<li class="list-inline-item tag px-2">'.getHtmlChar($word).'</li>';
            }

            if (!empty($lists)) {
        ?>
        <div class="tags-list pt-3">
            <ul class="list-inline mb-0">
                <li class="list-inline-item text-muted"><i data-feather="tag"></i></li>
                <?php echo implode(PHP_EOL, $lists); ?>
            </ul>
        </div>
        <?php
            }
        }
        ?>

        <div id="comment-area"></div>
    </div>

    <div class="text-right border-top pt-3">
        <?php echo $buttons; ?>
    </div>
</div>


<?php
$html->getPageFooter();
?>