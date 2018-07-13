<?php
/**
 * Tinymce editor
 */

class TINYMCEEDITOR
{
    protected $js;
    protected $height;
    protected $plugins;
    protected $toolbar;
    protected $selector;
    protected $language;
    protected $uploadUrl;
    protected $imageClass;
    protected $relativeUrl;
    protected $imageDimensions;
    protected $removeScriptHost;

    public function __construct()
    {
        $this->js               = NT_TINYMCE_URL.DIRECTORY_SEPARATOR.'tinymce.min.js';
        $this->height           = 350;
        $this->plugins          = '"advlist autolink lists link image charmap print preview anchor",
                                    "searchreplace visualblocks code fullscreen",
                                    "insertdatetime media table contextmenu paste imagetools wordcount"';
        $this->toolbar          = 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | code';
        $this->selector         = '.tinymce-editor';
        $this->language         = '';
        $this->uploadUrl        = NT_BOARD_URL.'/imageUpload.php';
        $this->imageClass       = '{title: "Responsive", value:"img-fluid"}';
        $this->relativeUrl      = false;
        $this->imageDimensions  = false;
        $this->removeScriptHost = false;
    }

    public function setValue(string $var, $val)
    {
        $this->{$var} = $val;
    }

    public function editorScript()
    {
        global $html;

        $html->addJavaScript($this->js, 'header', 10);

        $this->script = '
        <script>
        tinymce.init({
            selector: "'.$this->selector.'",
            height: '.$this->height.',
            language: "'.$this->language.'",
            plugins: [ '.$this->plugins. ' ],
            toolbar: "'.$this->toolbar.'",
            relative_urls: '.($this->relativeUrl ? 'true' : 'false').',
            remove_script_host: '.($this->removeScriptHost ? 'true' : 'false').',
            images_upload_url: "'.$this->uploadUrl.'",
            image_dimensions: '.($this->imageDimensions ? 'true' : 'false').',
            image_class_list: [ '.$this->imageClass.' ]
        });
        </script>';

        $html->addScriptString($this->script, 'footer', 10);
    }
}