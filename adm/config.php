<?php
require_once './_common.php';

if (!$isSuper)
    alert(_('Only super administrators can access.'));

$html->setPageTitle(_('Configuration'));

$tag = new TAGS();
$tag->tagEditor();

require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'header.php';
?>

<div class="col">
    <form name="fconfig" method="post" class="form-ajax" action="./configUpdate.php" enctype="multipart/form-data" autocomplete="off">

        <div class="border-bottom">
            <div class="form-group row">
                <label for="cf_site_name" class="col-md-2 col-form-label"><?php echo _('Site name'); ?></label>
                <div class="col-md-4">
                    <input type="text" name="cf_site_name" id="cf_site_name" value="<?php echo $config['cf_site_name']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_site_url" class="col-md-2 col-form-label"><?php echo _('Site url'); ?></label>
                <div class="col-md-4">
                    <input type="text" name="cf_site_url" id="cf_site_url" value="<?php echo $config['cf_site_url']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_site_index" class="col-md-2 col-form-label"><?php echo _('Site index'); ?></label>
                <div class="col-md-4">
                    <select name="cf_site_index" id="cf_site_index" class="custom-select custom-select-sm mr-sm-2" required>
                        <option value="0"><?php echo _('Choose site index'); ?></option>
                        <?php
                        $sql = " select pg_no, pg_subject from `{$nt['pages_table']}` where pg_use = :pg_use order by pg_no desc ";
                        $DB->prepare($sql);
                        $DB->execute([':pg_use' => 1]);
                        $result = $DB->fetchAll();

                        for ($i = 0; $row = array_shift($result); $i++) {
                        ?>
                        <option value="<?php echo $row['pg_no']; ?>"<?php echo getSelected($row['pg_no'], $config['cf_site_index']); ?>><?php echo getHtmlChar($row['pg_subject']); ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_locale" class="col-md-2 col-form-label"><?php echo _('Site Locale'); ?></label>
                <div class="col-md-2">
                    <select name="cf_locale" id="cf_locale" class="custom-select custom-select-sm mr-sm-2" required>
                        <?php
                        foreach ($_LOCALES as $k => $v) {
                        ?>
                        <option value="<?php echo $k; ?>"<?php echo getSelected($k, $config['cf_locale']); ?>><?php echo $v[1]; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_email_name" class="col-md-2 col-form-label"><?php echo _('Email sending name'); ?></label>
                <div class="col-md-3">
                    <input type="text" name="cf_email_name" id="cf_email_name" value="<?php echo $config['cf_email_name']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_email_address" class="col-md-2 col-form-label"><?php echo _('Email sending address'); ?></label>
                <div class="col-md-3">
                    <input type="text" name="cf_email_address" id="cf_email_address" value="<?php echo $config['cf_email_address']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_theme" class="col-md-2 col-form-label"><?php echo _('Theme'); ?></label>
                <div class="col-md-2">
                    <select name="cf_theme" id="cf_theme" class="custom-select custom-select-sm mr-sm-2" required>
                        <?php
                        foreach (getThemeDir() as $dir) {
                        ?>
                        <option value="<?php echo $dir; ?>"<?php echo getSelected($dir, $config['cf_theme']); ?>><?php echo $dir; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_recaptcha_site_key" class="col-md-2 col-form-label"><?php echo _('reCAPTCHA Site key'); ?></label>
                <div class="col-md-6">
                    <input type="text" name="cf_recaptcha_site_key" id="cf_recaptcha_site_key" value="<?php echo $config['cf_recaptcha_site_key']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_recaptcha_secret_key" class="col-md-2 col-form-label"><?php echo _('reCAPTCHA secret key'); ?></label>
                <div class="col-md-6">
                    <input type="text" name="cf_recaptcha_secret_key" id="cf_recaptcha_secret_key" value="<?php echo $config['cf_recaptcha_secret_key']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_css_version" class="col-md-2 col-form-label"><?php echo _('CSS version'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="cf_css_version" id="cf_css_version" value="<?php echo $config['cf_css_version']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="rform-group row">
                <label for="cf_js_version" class="col-md-2 col-form-label"><?php echo _('JavaScript version'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="cf_js_version" id="cf_js_version" value="<?php echo $config['cf_js_version']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <hr class="mb-4">

            <div class="form-group row">
                <label for="cf_page_rows" class="col-md-2 col-form-label"><?php echo _('Lines per page'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="cf_page_rows" id="cf_page_rows" value="<?php echo $config['cf_page_rows']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_page_limit" class="col-md-2 col-form-label"><?php echo _('Number of pages'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="cf_page_limit" id="cf_page_limit" value="<?php echo $config['cf_page_limit']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_max_level" class="col-md-2 col-form-label"><?php echo _('Max member level'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="cf_max_level" id="cf_max_level" value="<?php echo $config['cf_max_level']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_member_level" class="col-md-2 col-form-label"><?php echo _('Sign Up member level'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="cf_member_level" id="cf_member_level" value="<?php echo $config['cf_member_level']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_super_admin" class="col-md-2 col-form-label"><?php echo _('Administrator level'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="cf_super_admin" id="cf_super_admin" value="<?php echo $config['cf_super_admin']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_token_time" class="col-md-2 col-form-label"><?php echo _('Token valid seconds'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="cf_token_time" id="cf_token_time" value="<?php echo $config['cf_token_time']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_password_length" class="col-md-2 col-form-label"><?php echo _('Minimum password length'); ?></label>
                <div class="col-md-2">
                    <input type="text" name="cf_password_length" id="cf_password_length" value="<?php echo $config['cf_password_length']; ?>" class="form-control form-control-sm">
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_2factor_auth" class="col-md-2 col-form-label"><?php echo _('Google 2-factor authentication'); ?></label>
                <div class="col-md-2">
                    <select name="cf_2factor_auth" id="cf_2factor_auth" class="custom-select custom-select-sm mr-sm-2" required>
                        <option value="1"<?php echo getSelected(1, $config['cf_2factor_auth']); ?>><?php echo _('Used'); ?></option>
                        <option value="0"<?php echo getSelected(0, $config['cf_2factor_auth']); ?>><?php echo _('Not used'); ?></option>
                    </select>
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_exclude_agent" class="col-md-2 col-form-label"><?php echo _('Excluded agent'); ?></label>
                <div class="col-md-8">
                    <textarea name="cf_exclude_agent" id="cf_exclude_agent" class="form-control" rows="5"><?php echo $config['cf_exclude_agent']; ?></textarea>
                    <small class="text-muted"><?php echo _('Enter the user agent to exclude from the visit log by separating it with enter.'); ?></small>
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_description" class="col-md-2 col-form-label"><?php echo _('Meta description'); ?></label>
                <div class="col-md-8">
                    <textarea name="cf_description" id="cf_description" class="form-control" rows="5"><?php echo $config['cf_description']; ?></textarea>
                </div>
            </div>

            <div class="form-group row">
                <label for="cf_keywords" class="col-md-2 col-form-label"><?php echo _('Meta Keywords'); ?></label>
                <div class="col-md-8">
                    <textarea name="cf_keywords" id="cf_keywords" class="form-control tag-editor" rows="5"><?php echo $config['cf_keywords']; ?></textarea>
                </div>
            </div>

            <div class="form-group row">
                <label for="favicon" class="col-md-2 col-form-label"><?php echo _('Favicon'); ?></label>
                <div class="col">
                    <div class="col row">
                        <div>
                            <input type="file" name="favicon" id="favicon" class="form-control-file form-control-sm pl-0">
                        </div>
                        <?php if (is_file(NT_FILE_PATH.DIRECTORY_SEPARATOR.'favicon.ico')) { ?>
                        <div class="ml-2 pt-1 form-check">
                            <input type="checkbox" name="favicon_del" id="favicon_del" class="form-check-input" value="1">
                            <label for="favicon_del" class="form-check-label"><?php echo _('Favicon delete'); ?></label>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col mt-3">
                <button type="submit" class="btn btn-primary"><?php echo _('Save'); ?></button>
            </div>
        </div>

    </form>
</div>

<?php
require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'footer.php';
?>
