<?php
/**
 * Locale class
 */

class NTLOCALE
{
    public $locale;
    public $codeset;
    public $domains;
    public $directories;
    public $lastModified;
    public $localeDirectory;

    public function __construct()
    {
        $this->locale          = '';
        $this->codeset         = 'UTF-8';
        $this->domains         = array();
        $this->directories     = array();
        $this->lastModified    = 0;
        $this->localeDirectory = 'locale';

        @mkdir(NT_DATA_PATH.DIRECTORY_SEPARATOR.$this->localeDirectory, 0755);
    }

    public function setCodeSet(string $codeset)
    {
        $this->codeset = $codeset;
    }

    public function getDirectories(string $path)
    {
        $path = realpath($path);

        if (!is_dir($path))
            return false;

        $directories = array();

        foreach (scandir($path) as $val) {
            if (in_array($val, array('.', '..')))
                continue;

            $dir = $path.DIRECTORY_SEPARATOR.$val;

            if (is_dir($dir)) {
                $directories[] = $dir;
                $directories = array_merge($directories, $this->getDirectories($dir));
            }
        }

        return $directories;
    }

    public function getLastModified()
    {
        $mtimes = array();

        if (empty($this->directories))
            return false;

        foreach ($this->directories as $dir) {
            $files = scandir($dir);

            foreach ($files as $val) {
                if (in_array($val, array('.', '..')))
                    continue;

                $file = $dir.DIRECTORY_SEPARATOR.$val;

                if (is_file($file)) {
                    $filemtime = filemtime($file);

                    if ($filemtime)
                        $mtimes[] = $filemtime;
                }
            }
        }

        $max = max($mtimes);

        if ($this->lastModified < $max)
            $this->lastModified = $max;
    }

    public function addTextDomain(string $domain, string $directory)
    {
        $this->domains[] = array($domain, $directory);
    }

    public function deleteLocaleFile(string $dir)
    {
        $files = array_diff(scandir($dir), array('.', '..'));

        foreach ($files as $f) {
            $file = $dir.DIRECTORY_SEPARATOR.$f;

            if (is_dir($file))
                $this->deleteLocaleFile($file);
            else
                unlink($file);
        }

        rmdir($dir);
    }

    public function copyLocaleFile(string $src, string $dst)
    {
        $dir = opendir($src);

        @mkdir($dst, 0755);

        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src.DIRECTORY_SEPARATOR.$file) ) {
                    $this->copyLocaleFile($src.DIRECTORY_SEPARATOR.$file, $dst.DIRECTORY_SEPARATOR.$file);
                } else {
                    copy($src.DIRECTORY_SEPARATOR.$file, $dst.DIRECTORY_SEPARATOR.$file);
                }
            }
        }

        closedir($dir);
    }

    public function setLocale(string $locale)
    {
        $_LOCALES = $GLOBALS['_LOCALES'];

        $this->locale = $_LOCALES[$locale][0];

        if (!setlocale(LC_MESSAGES, $this->locale))
            setlocale(LC_MESSAGES, $this->locale.'.utf8');

        putenv('LANGUAGE='.$this->locale);

        foreach ($this->domains as $domain) {
            $this->directories = array_merge($this->directories, $this->getDirectories($domain[1]));
        }

        $this->getLastModified();

        $localeDir = NT_DATA_PATH.DIRECTORY_SEPARATOR.$this->localeDirectory;

        $mtime = 0;
        $files = array_diff(scandir($localeDir), array('.', '..'));
        foreach ($files as $f) {
            if (is_dir($localeDir.DIRECTORY_SEPARATOR.$f))
                $mtime = $f;
        }

        if ($this->lastModified > (int)$mtime) {
            if($mtime)
                $this->deleteLocaleFile($localeDir.DIRECTORY_SEPARATOR.$mtime);

            foreach ($this->domains as $domain) {
                $dst = $localeDir.DIRECTORY_SEPARATOR.$this->lastModified.DIRECTORY_SEPARATOR.$domain[0];
                mkdir($dst, 0755, true);

                $this->copyLocaleFile($domain[1], $dst);

                bindtextdomain($domain[0], $dst);
                bind_textdomain_codeset($domain[0], $this->codeset);
            }
        } else {
            foreach ($this->domains as $domain) {
                $dst = $localeDir.DIRECTORY_SEPARATOR.$mtime.DIRECTORY_SEPARATOR.$domain[0];

                bindtextdomain($domain[0], $dst);
                bind_textdomain_codeset($domain[0], $this->codeset);
            }
        }
    }

    public function textDomain(string $domain)
    {
        textdomain($domain);
    }
}