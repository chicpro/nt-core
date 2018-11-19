<?php
if (!$isAdmin && !$view['bo_comment'] && $member['mb_level'] < $board['bo_comment_level'])
    return '';

if (__c('cf_recaptcha_site_key') && $board['bo_captcha_use']) {
    $captcha = new reCAPTCHA();
    $captcha->getScript();
}
?>

<div class="mt-5 mb-4 border bg-light pl-3 pb-3">
    <div class="mt-3">
        <h4 class="h4"><?php echo _d('Comments', THEME_LOCALE_DOMAIN); ?></h4>
    </div>

    <?php if (!empty($result)) { ?>
    <ul class="list-unstyled col mt-3">
        <?php
        for($i = 0; $row = array_shift($result); $i++) {
            $editButton   = '<a href="'.NT_URL.'/'.BOARD_DIR.'/'.$id.'/'.$no.'/comment/'.$row['cm_no'].'/edit" class="comment-button text-muted" target="_blank"><i class="icon" data-feather="edit"></i></a>';
            $replyButton = '<a href="'.NT_URL.'/'.BOARD_DIR.'/'.$id.'/'.$no.'/comment/'.$row['cm_no'].'/reply" class="ml-2 comment-button text-muted" target="_blank"><i class="icon" data-feather="plus-square"></i></a>';
            $deleteButton = '<a href="'.NT_URL.'/'.BOARD_DIR.'/'.$id.'/'.$no.'/comment/'.$row['cm_no'].'/delete" class="ml-2 comment-button text-muted" target="_blank"><i class="icon" data-feather="x-square"></i></a>';

            if ($row['cm_reply'] > 0) {
                $replyClass = ' class="pl-4"';
                $replyIcon  = '<i class="reply-icon" data-feather="corner-down-right"></i>';
            } else {
                $replyClass = '';
                $replyIcon  = '';
            }
        ?>
        <li id="c-<?php echo $row['cm_no']; ?>" class="comment-list border-top mx-1 pt-2 pb-1">
            <div<?php echo $replyClass; ?>>
                <?php echo $replyIcon; ?>
                <div class="pl-0 mb-2">
                    <span class="font-weight-bold text-dark"><?php echo getHtmlChar($row['cm_name']); ?></span>
                    <span class="ml-2 text-muted"><?php echo getRichTime($row['cm_date']); ?></span>
                </div>
                <div class="pl-0"><?php echo getContent(nl2br($row['cm_content'])); ?></div>
                <div class="text-right">
                    <?php echo $editButton.$replyButton.$deleteButton; ?>
                </div>
            </div>
        </li>
        <?php
        }
        ?>
    </ul>
    <?php } ?>

    <?php
    if ($isAdmin || $member['mb_level'] >= $board['bo_comment_level'])
        require_once NT_BOARD_PATH.DIRECTORY_SEPARATOR.'commentForm.php';
    ?>

</div>

<script>
feather.replace();
</script>