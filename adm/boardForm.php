<?php
require_once './_common.php';

$html->setPageTitle(_('Board'));

require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'header.php';

$bo_id = preg_replace(NT_BOARD_ID_PATTERN, '', $_GET['bo_id']);

$board = array();

if ($bo_id) {
    $sql = " select * from `{$nt['board_config_table']}` where bo_id = :bo_id ";

    $DB->prepare($sql);
    $DB->bindValue(':bo_id', $bo_id);
    $DB->execute();

    $board = $DB->fetch();

    if (!$board['bo_id'])
        alertClose(_('Board information does not exist.'));
}

$qstr = array_merge(array('p' => $p), $qstr);
$listHref = NT_ADMIN_URL.DIRECTORY_SEPARATOR.'board.php?'.http_build_query($qstr, '', '&amp;');
?>

<div class="col my-4">

    <form name="fconfig" method="post" class="form-ajax" action="./boardFormUpdate.php" autocomplete="off">
        <input type="hidden" name="w" value="<?php echo ($bo_id ? 'u' : ''); ?>">

        <div class="border-bottom">
            <div class="form-group row">
                <label for="bo_id" class="col-md-2 col-form-label"><?php echo _('Board ID'); ?></label>
                <div class="col-md-3">
                    <input type="text" name="bo_id" id="bo_id" value="<?php echo $board['bo_id']; ?>" maxlength="20" class="form-control form-control-sm" required <?php echo ($bo_id ? 'readonly' : 'autofocus'); ?>>
                    <?php if (!$bo_id) { ?>
                    <small><?php echo _('Only alphabetic, numeric, and _ can be used'); ?></small>
                    <?php } ?>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_title" class="col-md-2 col-form-label"><?php echo _('Board title'); ?></label>
                <div class="col-md-3">
                    <input type="text" name="bo_title" id="bo_title" value="<?php echo getHtmlChar($board['bo_title']); ?>" maxlength="40" class="form-control form-control-sm" required<?php ($bo_id ? ' autofocus' : ''); ?>>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_use" class="col-md-2 col-form-label"><?php echo _('Use board'); ?></label>
                <div class="col-md-2">
                    <select name="bo_use" id="bo_use" class="custom-select mr-sm-2" required>
                        <option value="1"<?php echo getSelected(1, $board['bo_use']); ?>><?php echo _('Used'); ?></option>
                        <option value="0"<?php echo getSelected(0, $board['bo_use']); ?>><?php echo _('Not used'); ?></option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_category" class="col-md-2 col-form-label"><?php echo _('Board category'); ?></label>
                <div class="col-md-6">
                    <input type="text" name="bo_category" id="bo_category" value="<?php echo gethtmlChar($board['bo_category']); ?>" class="form-control form-control-sm">
                    <small><?php echo _('Input example : Category1,Category2'); ?></small>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_subject_len" class="col-md-2 col-form-label"><?php echo _('Subject length'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="bo_subject_len" id="bo_subject_len" value="<?php echo $board['bo_subject_len']; ?>" class="form-control form-control-sm" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_page_rows" class="col-md-2 col-form-label"><?php echo _('Lines per page'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="bo_page_rows" id="bo_page_rows" value="<?php echo $board['bo_page_rows']; ?>" class="form-control form-control-sm" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_page_limit" class="col-md-2 col-form-label"><?php echo _('Number of pages'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="bo_page_limit" id="bo_page_limit" value="<?php echo $board['bo_page_limit']; ?>" class="form-control form-control-sm" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_list_level" class="col-md-2 col-form-label"><?php echo _('List view permissions'); ?></label>
                <div class="col-md-2">
                    <select name="bo_list_level" id="bo_list_level" class="custom-select mr-sm-2" required>
                        <?php
                        for ($i = 1; $i <= __c('cf_max_level'); $i++) {
                            $optLevel = 'Lv. ' . $i;
                        ?>
                        <option value="<?php echo $i; ?>"<?php echo getSelected($i, $board['bo_list_level']); ?>><?php echo $optLevel; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_view_level" class="col-md-2 col-form-label"><?php echo _('Content view permissions'); ?></label>
                <div class="col-md-2">
                    <select name="bo_view_level" id="bo_view_level" class="custom-select mr-sm-2" required>
                        <?php
                        for ($i = 1; $i <= __c('cf_max_level'); $i++) {
                            $optLevel = 'Lv. ' . $i;
                        ?>
                        <option value="<?php echo $i; ?>"<?php echo getSelected($i, $board['bo_view_level']); ?>><?php echo $optLevel; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_write_level" class="col-md-2 col-form-label"><?php echo _('Posts written permissions'); ?></label>
                <div class="col-md-2">
                    <select name="bo_write_level" id="bo_write_level" class="custom-select mr-sm-2" required>
                        <?php
                        for ($i = 1; $i <= __c('cf_max_level'); $i++) {
                            $optLevel = 'Lv. ' . $i;
                        ?>
                        <option value="<?php echo $i; ?>"<?php echo getSelected($i, $board['bo_write_level']); ?>><?php echo $optLevel; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_comment_level" class="col-md-2 col-form-label"><?php echo _('Comments written permissions'); ?></label>
                <div class="col-md-2">
                    <select name="bo_comment_level" id="bo_comment_level" class="custom-select mr-sm-2" required>
                        <?php
                        for ($i = 1; $i <= __c('cf_max_level'); $i++) {
                            $optLevel = 'Lv. ' . $i;
                        ?>
                        <option value="<?php echo $i; ?>"<?php echo getSelected($i, $board['bo_comment_level']); ?>><?php echo $optLevel; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_reply_level" class="col-md-2 col-form-label"><?php echo _('Permission to create replies'); ?></label>
                <div class="col-md-2">
                    <select name="bo_reply_level" id="bo_reply_level" class="custom-select mr-sm-2" required>
                        <?php
                        for ($i = 1; $i <= __c('cf_max_level'); $i++) {
                            $optLevel = 'Lv. ' . $i;
                        ?>
                        <option value="<?php echo $i; ?>"<?php echo getSelected($i, $board['bo_reply_level']); ?>><?php echo $optLevel; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_file_limit" class="col-md-2 col-form-label"><?php echo _('Number of attachments'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="bo_file_limit" id="bo_file_limit" value="<?php echo $board['bo_file_limit']; ?>" class="form-control form-control-sm" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_link_limit" class="col-md-2 col-form-label"><?php echo _('Number of links'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="bo_link_limit" id="bo_link_limit" value="<?php echo $board['bo_link_limit']; ?>" class="form-control form-control-sm" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_captcha_use" class="col-md-2 col-form-label"><?php echo _('Use Captcha'); ?></label>
                <div class="col-md-2">
                    <select name="bo_captcha_use" id="bo_captcha_use" class="custom-select mr-sm-2" required>
                        <option value="1"<?php echo getSelected(1, $board['bo_captcha_use']); ?>><?php echo _('Used'); ?></option>
                        <option value="0"<?php echo getSelected(0, $board['bo_captcha_use']); ?>><?php echo _('Not used'); ?></option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="bo_captcha_comment" class="col-md-2 col-form-label"><?php echo _('Use Comment Captcha'); ?></label>
                <div class="col-md-2">
                    <select name="bo_captcha_comment" id="bo_captcha_comment" class="custom-select mr-sm-2" required>
                        <option value="1"<?php echo getSelected(1, $board['bo_captcha_comment']); ?>><?php echo _('Used'); ?></option>
                        <option value="0"<?php echo getSelected(0, $board['bo_captcha_comment']); ?>><?php echo _('Not used'); ?></option>
                    </select>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col mt-3">
                <button type="submit" class="btn btn-primary"><?php echo _('Save'); ?></button>
                <a href="<?php echo $listHref; ?>" class="btn btn-secondary"><?php echo _('List'); ?></a>
            </div>
        </div>

    </form>
</div>

<?php
require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'footer.php';
?>
