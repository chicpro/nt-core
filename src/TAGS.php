<?php
/**
 * TAG
 */

class TAGS
{
    protected $js;
    protected $css;
    protected $selector;
    protected $delimiter;

    public function __construct()
    {
        $this->js = array(
            NT_JS_URL.DIRECTORY_SEPARATOR.'jquery.caret.min.js',
            NT_JS_URL.DIRECTORY_SEPARATOR.'jquery.tag-editor.min.js'
        );

        $this->css = array(
            NT_CSS_URL.DIRECTORY_SEPARATOR.'jquery.tag-editor.css'
        );

        $this->selector = '.tag-editor';
        $this->delimiter = ', ';
    }

    public function setJavaScript(array $js)
    {
        $this->js = $js;
    }

    public function setStyleSheet(array $css)
    {
        $this->css = $css;
    }

    public function setDelimiter(string $delimiter)
    {
        $this->delimiter = $delimiter;
    }

    public function getTags(string $type, int $no)
    {
        if (!$type || !$no)
            return;

        global $nt, $DB;

        $tags = array();

        $sql = " select tg_word from `{$nt['tags_table']}` where tg_type = :tg_type and tg_no = :tg_no ";
        $DB->prepare($sql);
        $DB->execute([':tg_type' => $type, ':tg_no' => $no]);
        $result = $DB->fetchAll();

        for ($i = 0; $row = array_shift($result); $i++) {
            $tags[] = trim($row['tg_word']);
        }

        return implode(',', $tags);
    }

    public function tagEditor()
    {
        global $html;

        if (is_array($this->css) && !empty($this->css)) {
            foreach ($this->css as $css) {
                $html->addStyleSheet($css, 'header', 10);
            }
        }

        if (is_array($this->js) && !empty($this->js)) {
            foreach ($this->js as $js) {
                $html->addJavaScript($js, 'footer', 10);
            }
        }

        $script = '
            <script>
            jQuery("'.$this->selector.'").tagEditor({
                delimiter : "'.$this->delimiter.'"
            });
            </script>
        ';

        $html->addScriptString($script, 'footer', 10);
    }

    public function insertTag(string $tags, string $type, int $no)
    {
        global $nt, $DB;

        $tags = trim($tags);

        $oldTags = array();

        $sql = " select tg_word from `{$nt['tags_table']}` where tg_type = :tg_type and tg_no = :tg_no ";
        $DB->prepare($sql);
        $DB->execute([':tg_type' => $type, ':tg_no' => $no]);
        $result = $DB->fetchAll();

        for ($i = 0; $row = array_shift($result); $i++) {
            if ($row['tg_word'])
                $oldTags[] = $row['tg_word'];
        }

        $oldTags = array_unique($oldTags);

        $tags    = array_map('trim', explode(',', $tags));
        $newTags = array();

        foreach ($tags as $tag) {
            if ($tag) {
                $newTags[] = $tag;

                $sql = " insert into `{$nt['tags_table']}` ( tg_type, tg_no, tg_word ) values ( :tg_type, :tg_no, :tg_word ) ";
                $DB->prepare($sql);
                $DB->bindValueArray(
                    [
                        ':tg_type' => $type,
                        ':tg_no'   => $no,
                        ':tg_word' => $tag
                    ]
                );
                $DB->execute();
            }
        }

        $newTags = array_unique($newTags);

        $diff = array_diff($oldTags, $newTags);

        if (!empty($diff)) {
            foreach ($diff as $tag) {
                $sql = " delete from `{$nt['tags_table']}` where tg_type = :tg_type and tg_no = :tg_no and tg_word = :tg_word ";
                $DB->prepare($sql);
                $DB->execute([':tg_type' => $type, ':tg_no' => $no, ':tg_word' => $tag]);
            }
        }
    }
}