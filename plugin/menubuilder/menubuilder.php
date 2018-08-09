<?php
/**
 * Drag And Drop Mega Menu Builder For Bootstrap - Menu Editor
 * https://www.jqueryscript.net/menu/Drag-Drop-Menu-Builder-For-Bootstrap.html
 * https://www.jqueryscript.net/demo/Drag-Drop-Menu-Builder-For-Bootstrap/
 */

$html->addStyleSheet('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css', 'header', 0);
$html->addStyleSheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css', 'header', 0);
$html->addStyleSheet(NT_PLUGIN_URL.'/menubuilder/bs-iconpicker/css/bootstrap-iconpicker.min.css', 'header', 0);
$html->addStyleSheet(NT_CSS_URL.DIRECTORY_SEPARATOR.'common.css', 'header', 0, __c('cf_css_version'));

$html->addJavaScript('https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js', 'header', 0);
$html->addJavaScript('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', 'footer', 0, '', 'integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"');
$html->addJavascript(NT_PLUGIN_URL.'/menubuilder/jquery-menu-editor.min.js', 'footer', 10);
$html->addJavascript(NT_PLUGIN_URL.'/menubuilder/bs-iconpicker/js/iconset/iconset-fontawesome-4.7.0.min.js', 'footer', 10);
$html->addJavascript(NT_PLUGIN_URL.'/menubuilder/bs-iconpicker/js/bootstrap-iconpicker.js', 'footer', 10);

$menus = __c('cf_menus');
if (!$menus)
    $menus = '[]';

$html->addScriptString('
<script>
jQuery(function () {
    // menu items
    var strjson = '.$menus.';
    //icon picker options
    var iconPickerOptions = {searchText: "", labelHeader: "{0} de {1} Pags."};
    //sortable list options
    var sortableListOptions = {
        placeholderCss: {"background-color": "cyan"}
    };

    var editor = new MenuEditor("myEditor", {listOptions: sortableListOptions, iconPicker: iconPickerOptions, labelEdit: "Edit"});
    editor.setForm($("#frmEdit"));
    editor.setUpdateButton($("#btnUpdate"));

    editor.setData(strjson);

    jQuery("#btnSave").on("click", function () {
        var str = editor.getString();
        var $t = jQuery(this);

        jQuery.ajax({
            url: "./menuUpdate.php",
            method: "POST",
            data: { menus : str },
            success: function(data) {
                if (data.error != "") {
                    $f.after("<div class=\"mt-3 alert alert-danger alert-dismissible fade show\"><a href=\"#\" class=\"close\" data-dismiss=\"alert\" title=\"close\">&times;</a>" + data.error + "</div>");
                    return;
                }

                $t.parent().append("<div class=\"save_result save-done\"></div>");

                setTimeout(function() {
                    jQuery(".save_result").fadeOut(750, function() { jQuery(this).remove(); });
                }, 2000);
            },
            error: function(request, status, error) {
                console.log("code:"+request.status+"\n"+"message:"+request.responseText+"\n"+"error:"+error);
            },
            dataType: "JSON"
        });
    });

    jQuery("#btnUpdate").click(function(){
        editor.update();
    });

    jQuery("#btnAdd").click(function(){
        editor.add();
    });
});
</script>', 'footer', 10);