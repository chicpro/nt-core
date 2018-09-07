<?php
require_once './_common.php';

$tag = new TAGS();
$tag->tagEditor();

$html->setPageTitle(_('Pages'));

$html->addStyleSheet(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'toastr.min.css', 'header', 10);
$html->addStyleSheet(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'grapes.min.css', 'header', 10);
$html->addStyleSheet(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'grapesjs-preset-webpage.min.css', 'header', 10);
$html->addStyleSheet(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'css'.DIRECTORY_SEPARATOR.'tooltip.css', 'header', 10);

$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'toastr.min.js', 'header', 10);
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'grapes.min.js', 'header', 10);
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'grapesjs-preset-webpage.min.js', 'header', 10);
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'grapesjs-lory-slider.min.js', 'header', 10);
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'grapesjs-tabs.min.js', 'header', 10);
$html->addJavaScript(NT_JS_URL.DIRECTORY_SEPARATOR.'grapesjs'.DIRECTORY_SEPARATOR.'grapesjs-custom-code.min.js', 'header', 10);

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
                        <div id="pg_content" class="border">
                            <?php
                            if (trim($pages['pg_css'])) {
                                echo '<style type="text/css">';
                                echo $pages['pg_css'];
                                echo '</style>';
                            }

                            echo $pages['pg_content'] ? $pages['pg_content'] : '';
                            ?>
                        </div>
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

<?php
// 내용 중 이미지
if ($w == 'u') {
    $editorImages = getEditorImages($pages['pg_content']);
    $images = implode('", "', $editorImages[1]);
    if ($images)
        $images = '["'.$images.'"]';
    else
        $images = '[]';
} else {
    $images = '[]';
}
?>

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
        assets: <?php echo $images; ?>,
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
    plugins: ['gjs-preset-webpage', 'grapesjs-lory-slider', 'grapesjs-tabs', 'grapesjs-custom-code'],
    pluginsOpts: {
        'grapesjs-lory-slider': {
            sliderBlock: {
                category: 'Extra'
            }
        },
        'grapesjs-tabs': {
            tabsBlock: {
                category: 'Extra'
            }
        },
        'gjs-preset-webpage': {
            modalImportTitle: 'Import Template',
            modalImportLabel: '<div style="margin-bottom: 10px; font-size: 13px;">Paste here your HTML/CSS and click Import</div>',
            modalImportContent: function(editor) {
                return editor.getHtml() + '<style>'+editor.getCss()+'</style>'
            },
            aviaryOpts: false,
            blocksBasicOpts: { flexGrid: 1 },
            customStyleManager: [{
                name: 'General',
                buildProps: ['float', 'display', 'position', 'top', 'right', 'left', 'bottom'],
                properties:[{
                    name: 'Alignment',
                    property: 'float',
                    type: 'radio',
                    defaults: 'none',
                    list: [
                    { value: 'none', className: 'fa fa-times'},
                    { value: 'left', className: 'fa fa-align-left'},
                    { value: 'right', className: 'fa fa-align-right'}
                    ],
                },
                { property: 'position', type: 'select'}
                ],
            },{
                name: 'Dimension',
                open: false,
                buildProps: ['width', 'flex-width', 'height', 'max-width', 'min-height', 'margin', 'padding'],
                properties: [{
                    id: 'flex-width',
                    type: 'integer',
                    name: 'Width',
                    units: ['px', '%'],
                    property: 'flex-basis',
                    toRequire: 1,
                },{
                    property: 'margin',
                    properties:[
                    { name: 'Top', property: 'margin-top'},
                    { name: 'Right', property: 'margin-right'},
                    { name: 'Bottom', property: 'margin-bottom'},
                    { name: 'Left', property: 'margin-left'}
                    ],
                },{
                    property  : 'padding',
                    properties:[
                    { name: 'Top', property: 'padding-top'},
                    { name: 'Right', property: 'padding-right'},
                    { name: 'Bottom', property: 'padding-bottom'},
                    { name: 'Left', property: 'padding-left'}
                    ],
                }],
                },{
                name: 'Typography',
                open: false,
                buildProps: ['font-family', 'font-size', 'font-weight', 'letter-spacing', 'color', 'line-height', 'text-align', 'text-decoration', 'text-shadow'],
                properties:[
                    { name: 'Font', property: 'font-family'},
                    { name: 'Weight', property: 'font-weight'},
                    { name:  'Font color', property: 'color'},
                    {
                    property: 'text-align',
                    type: 'radio',
                    defaults: 'left',
                    list: [
                        { value : 'left',  name : 'Left',    className: 'fa fa-align-left'},
                        { value : 'center',  name : 'Center',  className: 'fa fa-align-center' },
                        { value : 'right',   name : 'Right',   className: 'fa fa-align-right'},
                        { value : 'justify', name : 'Justify',   className: 'fa fa-align-justify'}
                    ],
                    },{
                    property: 'text-decoration',
                    type: 'radio',
                    defaults: 'none',
                    list: [
                        { value: 'none', name: 'None', className: 'fa fa-times'},
                        { value: 'underline', name: 'underline', className: 'fa fa-underline' },
                        { value: 'line-through', name: 'Line-through', className: 'fa fa-strikethrough'}
                    ],
                    },{
                    property: 'text-shadow',
                    properties: [
                        { name: 'X position', property: 'text-shadow-h'},
                        { name: 'Y position', property: 'text-shadow-v'},
                        { name: 'Blur', property: 'text-shadow-blur'},
                        { name: 'Color', property: 'text-shadow-color'}
                    ],
                }],
                },{
                name: 'Decorations',
                open: false,
                buildProps: ['opacity', 'background-color', 'border-radius', 'border', 'box-shadow', 'background'],
                properties: [{
                    type: 'slider',
                    property: 'opacity',
                    defaults: 1,
                    step: 0.01,
                    max: 1,
                    min:0,
                },{
                    property: 'border-radius',
                    properties  : [
                    { name: 'Top', property: 'border-top-left-radius'},
                    { name: 'Right', property: 'border-top-right-radius'},
                    { name: 'Bottom', property: 'border-bottom-left-radius'},
                    { name: 'Left', property: 'border-bottom-right-radius'}
                    ],
                },{
                    property: 'box-shadow',
                    properties: [
                    { name: 'X position', property: 'box-shadow-h'},
                    { name: 'Y position', property: 'box-shadow-v'},
                    { name: 'Blur', property: 'box-shadow-blur'},
                    { name: 'Spread', property: 'box-shadow-spread'},
                    { name: 'Color', property: 'box-shadow-color'},
                    { name: 'Shadow type', property: 'box-shadow-type'}
                    ],
                },{
                    property: 'background',
                    properties: [
                    { name: 'Image', property: 'background-image'},
                    { name: 'Repeat', property:   'background-repeat'},
                    { name: 'Position', property: 'background-position'},
                    { name: 'Attachment', property: 'background-attachment'},
                    { name: 'Size', property: 'background-size'}
                    ],
                },],
                },{
                name: 'Extra',
                open: false,
                buildProps: ['transition', 'perspective', 'transform'],
                properties: [{
                    property: 'transition',
                    properties:[
                    { name: 'Property', property: 'transition-property'},
                    { name: 'Duration', property: 'transition-duration'},
                    { name: 'Easing', property: 'transition-timing-function'}
                    ],
                },{
                    property: 'transform',
                    properties:[
                    { name: 'Rotate X', property: 'transform-rotate-x'},
                    { name: 'Rotate Y', property: 'transform-rotate-y'},
                    { name: 'Rotate Z', property: 'transform-rotate-z'},
                    { name: 'Scale X', property: 'transform-scale-x'},
                    { name: 'Scale Y', property: 'transform-scale-y'},
                    { name: 'Scale Z', property: 'transform-scale-z'}
                    ],
                }]
                },{
                name: 'Flex',
                open: false,
                properties: [{
                    name: 'Flex Container',
                    property: 'display',
                    type: 'select',
                    defaults: 'block',
                    list: [
                    { value: 'block', name: 'Disable'},
                    { value: 'flex', name: 'Enable'}
                    ],
                },{
                    name: 'Flex Parent',
                    property: 'label-parent-flex',
                    type: 'integer',
                },{
                    name      : 'Direction',
                    property  : 'flex-direction',
                    type    : 'radio',
                    defaults  : 'row',
                    list    : [{
                            value   : 'row',
                            name    : 'Row',
                            className : 'icons-flex icon-dir-row',
                            title   : 'Row',
                            },{
                            value   : 'row-reverse',
                            name    : 'Row reverse',
                            className : 'icons-flex icon-dir-row-rev',
                            title   : 'Row reverse',
                            },{
                            value   : 'column',
                            name    : 'Column',
                            title   : 'Column',
                            className : 'icons-flex icon-dir-col',
                            },{
                            value   : 'column-reverse',
                            name    : 'Column reverse',
                            title   : 'Column reverse',
                            className : 'icons-flex icon-dir-col-rev',
                            }],
                },{
                    name      : 'Justify',
                    property  : 'justify-content',
                    type    : 'radio',
                    defaults  : 'flex-start',
                    list    : [{
                            value   : 'flex-start',
                            className : 'icons-flex icon-just-start',
                            title   : 'Start',
                            },{
                            value   : 'flex-end',
                            title    : 'End',
                            className : 'icons-flex icon-just-end',
                            },{
                            value   : 'space-between',
                            title    : 'Space between',
                            className : 'icons-flex icon-just-sp-bet',
                            },{
                            value   : 'space-around',
                            title    : 'Space around',
                            className : 'icons-flex icon-just-sp-ar',
                            },{
                            value   : 'center',
                            title    : 'Center',
                            className : 'icons-flex icon-just-sp-cent',
                            }],
                },{
                    name      : 'Align',
                    property  : 'align-items',
                    type    : 'radio',
                    defaults  : 'center',
                    list    : [{
                            value   : 'flex-start',
                            title    : 'Start',
                            className : 'icons-flex icon-al-start',
                            },{
                            value   : 'flex-end',
                            title    : 'End',
                            className : 'icons-flex icon-al-end',
                            },{
                            value   : 'stretch',
                            title    : 'Stretch',
                            className : 'icons-flex icon-al-str',
                            },{
                            value   : 'center',
                            title    : 'Center',
                            className : 'icons-flex icon-al-center',
                            }],
                },{
                    name: 'Flex Children',
                    property: 'label-parent-flex',
                    type: 'integer',
                },{
                    name:     'Order',
                    property:   'order',
                    type:     'integer',
                    defaults :  0,
                    min: 0
                },{
                    name    : 'Flex',
                    property  : 'flex',
                    type    : 'composite',
                    properties  : [{
                            name:     'Grow',
                            property:   'flex-grow',
                            type:     'integer',
                            defaults :  0,
                            min: 0
                        },{
                            name:     'Shrink',
                            property:   'flex-shrink',
                            type:     'integer',
                            defaults :  0,
                            min: 0
                        },{
                            name:     'Basis',
                            property:   'flex-basis',
                            type:     'integer',
                            units:    ['px','%',''],
                            unit: '',
                            defaults :  'auto',
                        }],
                },{
                    name      : 'Align',
                    property  : 'align-self',
                    type      : 'radio',
                    defaults  : 'auto',
                    list    : [{
                            value   : 'auto',
                            name    : 'Auto',
                            },{
                            value   : 'flex-start',
                            title    : 'Start',
                            className : 'icons-flex icon-al-start',
                            },{
                            value   : 'flex-end',
                            title    : 'End',
                            className : 'icons-flex icon-al-end',
                            },{
                            value   : 'stretch',
                            title    : 'Stretch',
                            className : 'icons-flex icon-al-str',
                            },{
                            value   : 'center',
                            title    : 'Center',
                            className : 'icons-flex icon-al-center',
                            }],
                }]
                }
            ],
        },
    },
    storageManager: { autoload: 0 }
});

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
        html    = editor.getHtml();
        css     = editor.getCss();

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
        data.push({name: "pg_css", value: css});

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