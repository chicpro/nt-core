<?php
require_once './_common.php';

$tag = new TAGS();
$tag->tagEditor();

$html->setPageTitle(_('Pages'));

$html->addStyleSheet(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'grapes.min.css', 'header', 10);
$html->addStyleSheet(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'grapesjs-preset-newsletter.css', 'header', 10);
$html->addStyleSheet(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'material.css', 'header', 10);

$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'grapes.min.js', 'header', 10);
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'grapesjs-preset-newsletter.min.js', 'header', 10);
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'grapesjs-blocks-basic.min.js', 'header', 10);

require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'header.php';

$w  = substr($_REQUEST['w'], 0, 1);
$no = (int)preg_replace('#[^0-9]#', '', $_REQUEST['no']);

$column = '';
$save   = '';
$q      = '';

$qstr = array();

if (isset($_GET['c'])) {
    $c = getSearchColumn($_GET['c']);

    if ($c)
        $qstr['c'] = $c;
}

if (isset($_GET['s']))
    $s = getSearchString($_GET['s']);

if (isset($_GET['q'])) {
    $q = getSearchString($_GET['q']);

    if ($q) {
        $qstr['s'] = $q;
        $qstr['q'] = $q;
    }
}

if (isset($_GET['p'])) {
    $p = (int)preg_replace('#[^0-9]#', '', $_GET['p']);
    $qstr = array_merge(array('p' => $p), $qstr);
}

if ($w == 'u') {
    $sql = " select * from `{$nt['pages_table']}` where pg_no = :pg_no ";
    $DB->prepare($sql);
    $DB->execute([':pg_no' => $no]);
    $pages = $DB->fetch();

    if (!$pages['pg_no'])
        alert(_('The page does not exist.'));

    $tags = $tag->getTags('pages', $no);
} else {
    $pages['pg_use']    = 1;
    $pages['pg_header'] = 'header.php';
    $pages['pg_footer'] = 'footer.php';
}

// Header file
$headerFiles = array();
$footerFiles = array();

$themeDir = NT_THEME_PATH.DIRECTORY_SEPARATOR;

foreach (glob($themeDir.'header*.php') as $file) {
    $filename = basename($file);

    if (preg_match('#^header-?[a-z]*\.php$#', $filename))
        $headerFiles[] = $filename;
}

foreach (glob($themeDir.'footer*.php') as $file) {
    $filename = basename($file);

    if (preg_match('#^footer-?[a-z]*\.php$#', $filename))
        $footerFiles[] = $filename;
}

$listHref = NT_ADMIN_URL.DIRECTORY_SEPARATOR.'pages.php?'.http_build_query($qstr, '', '&amp;');
$_SESSION['grapesImages'] = array();
?>

<div class="mb-4">
    <div class="col-md-12">
        <form name="fpages" method="post" class="form-pages" action="<?php echo NT_ADMIN_URL; ?>/pagesFormUpdate.php" enctype="multipart/form-data" autocomplete="off">
            <input type="hidden" name="w" value="<?php echo $w; ?>">
            <input type="hidden" name="no" value="<?php echo $no; ?>">
            <input type="hidden" name="p" value="<?php echo $p; ?>">
            <input type="hidden" name="c" value="<?php echo getHtmlChar($c); ?>">
            <input type="hidden" name="s" value="<?php echo getHtmlChar($s); ?>">
            <input type="hidden" name="q" value="<?php echo getHtmlChar($q); ?>">

            <div class="border-bottom">
                <div class="form-group row">
                    <label for="pg_use" class="col-md-1 col-form-label"><?php echo _('Page Use'); ?></label>
                    <div class="col-md-2">
                        <select name="pg_use" id="pg_use" class="custom-select mr-sm-2" required>
                            <option value="1"<?php echo getSelected(1, $pages['pg_use']); ?>><?php echo _('Used'); ?></option>
                            <option value="0"<?php echo getSelected(0, $pages['pg_use']); ?>><?php echo _('Not used'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pg_id" class="col-md-1 col-form-label"><?php echo _('Page ID'); ?></label>
                    <div class="col-md-6">
                        <input type="text" name="pg_id" id="pg_id" value="<?php echo getHtmlChar($pages['pg_id']); ?>" class="form-control form-control-sm" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pg_subject" class="col-md-1 col-form-label"><?php echo _('Subject'); ?></label>
                    <div class="col-md-6">
                        <input type="text" name="pg_subject" id="pg_subject" value="<?php echo getHtmlChar($pages['pg_subject']); ?>" class="form-control form-control-sm" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pg_header" class="col-md-1 col-form-label"><?php echo _('Header'); ?></label>
                    <div class="col-md-3">
                        <select name="pg_header" id="pg_header" class="custom-select mr-sm-2">
                            <?php
                            foreach ($headerFiles as $file) {
                            ?>
                            <option value="<?php echo $file; ?>"<?php echo getSelected($file, $pages['pg_header']); ?>><?php echo $file; ?></option>
                            <?php
                            }
                            ?>
                            <option value=""<?php echo getSelected('', $pages['pg_header']); ?>><?php echo _('Not used'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <div id="pg-content-editor" class="col">
                        <div id="pg_content" class="border"><?php echo $pages['pg_content'] ? $pages['pg_content'] : ''; ?></div>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="pg_footer" class="col-md-1 col-form-label"><?php echo _('Footer'); ?></label>
                    <div class="col-md-3">
                        <select name="pg_footer" id="pg_footer" class="custom-select mr-sm-2">
                            <?php
                            foreach ($footerFiles as $file) {
                            ?>
                            <option value="<?php echo $file; ?>"<?php echo getSelected($file, $pages['pg_footer']); ?>><?php echo $file; ?></option>
                            <?php
                            }
                            ?>
                            <option value=""<?php echo getSelected('', $pages['pg_footer']); ?>><?php echo _('Not used'); ?></option>
                        </select>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="tags" class="col-md-1 col-form-label"><?php echo _('Tags'); ?></label>
                    <div class="col-md-10">
                        <input type="text" name="tags" id="tags" value="<?php echo $tags; ?>" class="form-control form-control-sm tag-editor">
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col text-center">
                    <button type="button" class="btn btn-primary"><?php echo _('Write'); ?></button>
                    <a href="<?php echo $listHref; ?>" class="btn btn-secondary"><?php echo _('List'); ?></a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
var editor = grapesjs.init({
    height: "800px",
    showOffsets: 1,
    noticeOnUnload: 0,
    container: '#pg_content',
    fromElement: true,
    assetManager: {
        storageType  	: '',
    	storeOnChange  : true,
    	storeAfterUpload  : true,
        upload: '<?php echo NT_ADMIN_URL.DIRECTORY_SEPARATOR; ?>pagesAssetsUpload.php',
        uploadText: 'Drop files here',
        assets: [ ],
        uploadFile: function(e) {
		    var files = e.dataTransfer ? e.dataTransfer.files : e.target.files;
            var formData = new FormData();
            for(var i in files){
                formData.append('file-'+i, files[i]) //containing all the selected images from local
            }

            jQuery.ajax({
                url: '<?php echo NT_ADMIN_URL.DIRECTORY_SEPARATOR; ?>pagesAssetsUpload.php',
                type: 'POST',
			    data: formData,
			    contentType:false,
	            crossDomain: true,
	            dataType: 'json',
	            mimeType: "multipart/form-data",
	            processData:false,
	            success: function(result) {
                    var myJSON = [];
    				jQuery.each( result['data'], function( key, value ) {
        					myJSON[key] = value;
                    });

    				var images = myJSON;
      		        editor.AssetManager.add(images); //adding images to asset manager of GrapesJS
    			}
            });
        }
    },
    plugins: ['gjs-blocks-basic', 'gjs-preset-newsletter'],
    pluginsOpts: {
        'gjs-blocks-basic': {},
        'gjs-preset-newsletter': {
            modalTitleImport: 'Import template',
            modalLabelExport: 'Copy the code and use it wherever you want',
            codeViewerTheme: 'material',
            //defaultTemplate: templateImport,
            importPlaceholder: '<table class="table"><tr><td class="cell">Hello world!</td></tr></table>',
            cellStyle: {
                'font-size': '12px',
                'font-weight': 300,
                'vertical-align': 'top',
                color: 'rgb(111, 119, 125)',
                margin: 0,
                padding: 0,
            }
        }
    },
    storageManager: { autoload: 0 },
    styleManager : {
        sectors: [
            {
                name: 'General',
                open: false,
                buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom']
            },{
                name: 'Dimension',
                open: false,
                buildProps: ['width', 'height', 'max-width', 'min-height', 'margin', 'padding'],
            },{
                name: 'Typography',
                open: false,
                buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-shadow'],
            },{
                name: 'Decorations',
                open: false,
                buildProps: ['border-radius-c', 'background-color', 'border-radius', 'border', 'box-shadow', 'background'],
            },{
                name: 'Extra',
                open: false,
                buildProps: ['transition', 'perspective', 'transform'],
            }
        ],
    },
});

var pnm = editor.Panels;

pnm.addButton('options', [
    {
        id: 'undo',
        className: 'fa fa-undo',
        attributes: {title: 'Undo'},
        command: function(){ editor.runCommand('core:undo') }
    },{
        id: 'redo',
        className: 'fa fa-repeat',
        attributes: {title: 'Redo'},
        command: function(){ editor.runCommand('core:redo') }
    },{
        id: 'clear-all',
        className: 'fa fa-trash icon-blank',
        attributes: {title: 'Clear canvas'},
        command: {
            run: function(editor, sender) {
            sender && sender.set('active', false);
            if(confirm('Are you sure to clean the canvas?')){
                editor.DomComponents.clear();
                setTimeout(function(){
                localStorage.clear()
                },0)
            }
            }
        }
    }
]);

jQuery(function() {
    jQuery(document).on("change", "#pg_id", function(e) {
        var id = jQuery(this).val();
        var w  = jQuery("input[name=w]").val();
        var no = jQuery("input[name=no]").val();

        jQuery.ajax({
            url: "./pagesGetId.php",
            data: {w: w, id: id, no: no},
            dataType: "JSON",
            success: function(data) {
                jQuery("#pg_id").val(data.id);
            },
            error: function(request, status, error) {
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            }
        })
    });

    jQuery(document).on("click", "form.form-pages button", function(e) {
        e.preventDefault();
        e.stopPropagation();

        var f  = this.form;
        var $f = jQuery(f);
        var $t = jQuery(this).parent();
        var id, subject, html, css, data;

        id      = jQuery.trim(f.pg_id.value);
        subject = jQuery.trim(f.pg_subject.value);
        html    = editor.runCommand('gjs-get-inlined-html');
        //css     = editor.getCss();

        if(id.length < 1) {
            jQuery("#pg_id").data("content", "<?php echo _('Please enter a Page ID'); ?>").popover("show");
                return false;
        }

        if(subject.length < 1) {
            jQuery("#pg_subject").data("content", "<?php echo _('Please enter a Subject'); ?>").popover("show");
                return false;
        }

        if(html.length < 1) {
            jQuery("#pg-content-editor").data("content", "<?php echo _('Please enter a Contents'); ?>").popover("show");
                return false;
        }

        setTokenValue(this.form, "adm");

        data = $f.serializeArray();
        data.push({name: "pg_content", value: html});

        $t.append("<div class=\"save_spinner col pt-2\"><img src=\"" + nt_img_url + "/spinner-2x.gif\"></div>");

        jQuery.ajax({
            url: f.action,
            method: f.method,
            data: data,
            success: function(data) {
                jQuery(".save_spinner").remove();

                if (data.error != "") {
                    $t.append("<div class=\"col mt-3 alert alert-danger alert-dismissible fade show\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" title=\"close\">&times;</a>" + data.error + "</div>");
                    return;
                }

                if(typeof(data.href) != "undefined")
                    document.location.href = data.href;
            },
            error: function(request, status, error) {
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
            dataType: "JSON"
        })
    });
});
</script>

<?php
require_once NT_ADMIN_PATH.DIRECTORY_SEPARATOR.'footer.php';
?>