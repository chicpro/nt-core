<?php
if ($w == 'd')
    $commentSubmitButton = _d('Delete', THEME_LOCALE_DOMAIN);
else
    $commentSubmitButton = _d('Write', THEME_LOCALE_DOMAIN);

if ($w != '')
    $idSubpix = '_'.$w;
else
    $idSubpix = '';

switch($_REQUEST['action']) {
    case 'edit':
        $commentFormTitle = _d('Edit Comment', THEME_LOCALE_DOMAIN);
        break;
    case 'delete':
        $commentFormTitle = _d('Delete Comment', THEME_LOCALE_DOMAIN);
        break;
    case 'reply':
        $commentFormTitle = _d('Reply Comment', THEME_LOCALE_DOMAIN);
        break;
    default:
        $commentFormTitle = _d('Write Comment', THEME_LOCALE_DOMAIN);
        break;
}
?>

<div class="col">
    <div class="col h5 text-dark border-top py-3 pl-0"><?php echo $commentFormTitle; ?></div>

    <form name="fcomment" method="post" class="form-token form-comment" action="<?php echo NT_BOARD_URL; ?>/commentUpdate.php" autocomplete="off">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <input type="hidden" name="no" value="<?php echo $no; ?>">
        <input type="hidden" name="cn" value="<?php echo $cn; ?>">
        <input type="hidden" name="w"  value="<?php echo $w; ?>">

        <div>
            <?php if ($isCommentName) { ?>
            <div class="form-group row">
                <label for="cm_name<?php echo $idSubpix; ?>" class="col-sm-2 col-form-label"><?php echo _d('Name', THEME_LOCALE_DOMAIN); ?></label>
                <div class="col-sm-4">
                    <input type="text" name="cm_name" id="cm_name<?php echo $idSubpix; ?>" value="<?php echo getHtmlChar($comment['cm_name']); ?>" class="cm_name form-control form-control-sm" required<?php echo ($isGuest ? ' autofocus' : ''); ?>>
                </div>
            </div>
            <?php } ?>

            <?php if ($isCommentPassword) { ?>
            <div class="form-group row">
                <label for="cm_password<?php echo $idSubpix; ?>" class="col-sm-2 col-form-label"><?php echo _d('Password', THEME_LOCALE_DOMAIN); ?></label>
                <div class="col-sm-4">
                    <input type="password" name="cm_password" id="cm_password<?php echo $idSubpix; ?>" value="" class="cm_password form-control form-control-sm" required>
                </div>
            </div>
            <?php } ?>

            <?php if ($isCommentContent) { ?>
            <div class="form-group row">
                <label for="cm_content<?php echo $idSubpix; ?>" class="col-sm-2 col-form-label"><?php echo _d('Contents', THEME_LOCALE_DOMAIN); ?></label>
                <div class="col">
                    <textarea name="cm_content" id="cm_content<?php echo $idSubpix; ?>" class="cm_content form-control" rows="3"><?php echo $comment['cm_content']; ?></textarea>
                </div>
            </div>
            <?php } ?>

            <?php if ($isCommentCaptcha) { ?>
            <div class="form-group row">
                <label class="col-sm-2 col-form-label"><?php echo _d('Anti-spam code', THEME_LOCALE_DOMAIN); ?></label>
                <div class="col">
                    <?php $captcha->getElement(); ?>
                </div>
            </div>
            <?php } ?>
        </div>

        <div class="row mt-3">
            <div class="col text-center">
                <button type="submit" class="btn btn-primary"><?php echo $commentSubmitButton; ?></button>
            </div>
        </div>
    </form>
</div>