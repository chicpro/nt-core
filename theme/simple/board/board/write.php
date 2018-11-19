<?php
$editor = new TINYMCEEDITOR();
$editor->editorScript();

$html->setPageTitle(getHtmlChar($board['bo_title']));
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'board.js.php', 'footer', 10);

if (__c('cf_recaptcha_site_key') && $board['bo_captcha_use'])
    $html->addJavaScript('https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl=', 'footer', 10, '', 'async defer');

$html->getPageHeader();

if (__c('cf_recaptcha_site_key') && $board['bo_captcha_use']) {
    $captcha = new reCAPTCHA();
    $captcha->getScript();
}

$_SESSION['editorImages'] = array();
?>

<div class="mb-4">
    <div class="mt-3">
        <h1 class="h3"><?php echo $html->title; ?></h1>
    </div>

    <div class="col-md-12">
        <form name="fwrite" method="post" class="form-write form-token" action="<?php echo NT_BOARD_URL; ?>/writeUpdate.php" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="w" value="<?php echo $w; ?>">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <input type="hidden" name="no" value="<?php echo $no; ?>">
            <input type="hidden" name="p" value="<?php echo $p; ?>">
            <input type="hidden" name="c" value="<?php echo getHtmlChar($c); ?>">
            <input type="hidden" name="s" value="<?php echo getHtmlChar($s); ?>">
            <input type="hidden" name="q" value="<?php echo getHtmlChar($q); ?>">

            <div class="border-top border-bottom pt-3">
                <?php if ($isGuest && $w == '') { ?>
                <div class="form-group row">
                    <label for="bo_name" class="col-sm-2 col-form-label"><?php echo _d('Name', THEME_LOCALE_DOMAIN); ?></label>
                    <div class="col-sm-4">
                        <input type="text" name="bo_name" id="bo_name" value="<?php echo getHtmlChar($write['bo_name']); ?>" class="form-control form-control-sm" required<?php echo ($isGuest ? ' autofocus' : ''); ?>>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="bo_password" class="col-sm-2 col-form-label"><?php echo _d('Password', THEME_LOCALE_DOMAIN); ?></label>
                    <div class="col-sm-4">
                        <input type="password" name="bo_password" id="bo_password" value="" class="form-control form-control-sm" required>
                    </div>
                </div>
                <?php } ?>

                <fieldset class="form-group">
                    <div class="row">
                        <legend class="col-form-label col-sm-2 pt-0"><?php echo _d('Options', THEME_LOCALE_DOMAIN); ?></legend>
                        <div class="col-sm-4">
                            <?php if ($isAdmin) { ?>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" name="bo_notice" id="bo_notice" class="form-check-input" value="1"<?php echo getChecked(1, $write['bo_notice']); ?>>
                                <label class="form-check-label" for="bo_notice"><?php echo _d('Notice', THEME_LOCALE_DOMAIN); ?></label>
                            </div>
                            <?php } ?>
                            <div class="form-check form-check-inline">
                                <input type="checkbox" name="bo_secret" id="bo_secret" class="form-check-input" value="1"<?php echo getChecked(1, $write['bo_secret']); ?>>
                                <label class="form-check-label" for="bo_secret"><?php echo _d('Secret', THEME_LOCALE_DOMAIN); ?></label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <?php if (!empty($categories)) { ?>
                <div class="form-group row">
                    <label for="bo_category" class="col-sm-2 col-form-label"><?php echo _d('Category', THEME_LOCALE_DOMAIN); ?></label>
                    <div class="col-sm-3">
                        <select name="bo_category" id="bo_category" class="custom-select mr-sm-2" required>
                            <?php
                            foreach ($categories as $category) {
                                $category = getHtmlChar($category);
                            ?>
                            <option value="<?php echo $category; ?>"><?php echo $category; ?></option>
                            <?php
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <?php } ?>

                <div class="form-group row">
                    <label for="bo_subject" class="col-sm-2 col-form-label"><?php echo _d('Subject', THEME_LOCALE_DOMAIN); ?></label>
                    <div class="col">
                        <input type="text" name="bo_subject" id="bo_subject" value="<?php echo getHtmlChar($write['bo_subject']); ?>" class="form-control form-control-sm" required<?php echo ($isMember ? ' autofocus' : ''); ?>>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="bo_content" class="col-sm-2 col-form-label"><?php echo _d('Contents', THEME_LOCALE_DOMAIN); ?></label>
                    <div id="bo-content-editor" class="col">
                        <textarea name="bo_content" id="bo_content" class="form-control tinymce-editor" rows="10"><?php echo $write['bo_content']; ?></textarea>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="tags" class="col-sm-2 col-form-label"><?php echo _d('Tags', THEME_LOCALE_DOMAIN); ?></label>
                    <div class="col">
                        <input type="text" name="tags" id="tags" value="<?php echo $tags; ?>" class="form-control form-control-sm tag-editor">
                    </div>
                </div>

                <?php if (!empty($links)) { ?>
                <div class="form-group row">
                    <label for="bo_link_0" class="col-sm-2 col-form-label"><?php echo _d('Site Links', THEME_LOCALE_DOMAIN); ?></label>
                    <div class="col-sm-8">
                        <?php
                        $k  = 0;
                        $mt = '';
                        foreach ($links as $link) {
                        ?>
                        <div class="col row <?php echo $mt; ?>">
                            <input type="text" name="bo_link[]" id="bo_link_<?php echo $k; ?>" value="<?php echo getHtmlChar($link); ?>" class="form-control form-control-sm">
                        </div>
                        <?php
                            $k++;
                            $mt = ' mt-1"';
                        }
                        ?>
                    </div>
                </div>
                <?php } ?>

                <?php if (!empty($files)) { ?>
                <div class="form-group row">
                    <label for="bo_file_0" class="col-sm-2 col-form-label"><?php echo _d('Attachments', THEME_LOCALE_DOMAIN); ?></label>
                    <div class="col">
                        <?php
                        $k  = 0;
                        $mt = '';
                        foreach ($files as $file) {
                        ?>
                        <div class="col row<?php echo $mt; ?>">
                            <div>
                                <input type="file" name="bo_file[<?php echo $k; ?>]" id="bo_file_<?php echo $k; ?>" class="form-control-file form-control-sm pl-0">
                            </div>
                            <?php if ($w == 'u' && (isset($file['fl_name']) && $file['fl_name'])) { ?>
                            <div class="ml-2 pt-1"><?php echo getHtmlChar($file['fl_name']); ?></div>
                            <div class="ml-2 pt-1 form-check">
                                <input type="checkbox" name="bo_file_del[<?php echo $k; ?>]" id="bo_file_del_<?php echo $k; ?>" class="form-check-input" value="1">
                                <label for="bo_file_del_<?php echo $k; ?>" class="form-check-label"><?php echo _d('File delete', THEME_LOCALE_DOMAIN); ?></label>
                            </div>
                            <?php } ?>
                        </div>
                        <?php
                            $k++;
                            $mt = ' mt-1"';
                        }
                        ?>
                    </div>
                </div>
                <?php } ?>

                <?php if (__c('cf_recaptcha_site_key') && $board['bo_captcha_use']) { ?>
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
                    <button type="submit" class="btn btn-primary"><?php echo _d('Write', THEME_LOCALE_DOMAIN); ?></button>
                    <button type="button" class="btn btn-secondary history-back"><?php echo _d('Cancel', THEME_LOCALE_DOMAIN); ?></button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
$html->getPageFooter();
?>