<?php
@ini_set('memory_limit', '-1');

class THUMBNAIL
{
    public $width;
    public $height;

    protected $source;
    protected $target;
    protected $create;
    protected $crop;
    protected $cropMode;
    protected $pngCompress;
    protected $jpgQuality;
    protected $prefix;

    public function __construct()
    {
        $this->create      = false;
        $this->crop        = true;
        $this->cropMode    = 'center';
        $this->pngCompress = 5;
        $this->jpgQuality  = 90;
        $this->prefix      = 'thumb_';
    }

    public function setValue(string $var, $val)
    {
        $this->{$var} = $val;
    }

    public function isAnimatedGif($file) {
        if (!($fh = @fopen($file, 'rb')))
            return false;

        $count = 0;

        // http://www.php.net/manual/en/function.imagecreatefromgif.php#104473
        // an animated gif contains multiple "frames", with each frame having a
        // header made up of:
        // * a static 4-byte sequence (\x00\x21\xF9\x04)
        // * 4 variable bytes
        // * a static 2-byte sequence (\x00\x2C) (some variants may use \x00\x21 ?)

        // We read through the file til we reach the end of the file, or we've found
        // at least 2 frame headers
        while (!feof($fh) && $count < 2) {
            $chunk = fread($fh, 1024 * 100); //read 100kb at a time
            $count += preg_match_all('#\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)#s', $chunk, $matches);
        }
        fclose($fh);

        return $count > 1;
    }

    public function thumbnail(string $source = '', int $width = 0, int $height = 0, string $target = '')
    {
        if ($width > 0)
            $this->width = $width;

        if ($height > 0)
            $this->height = $height;

        if(!$this->width && !$this->height)
            return false;

        if ($source)
            $this->source = $source;

        if (!is_file($this->source))
            return false;

        $size = @getimagesize($this->source);
        if ($size[2] < 1 || $size[2] > 3)
            return false;

        $thumbName = pathinfo($this->source, PATHINFO_FILENAME);
        if (!$target)
            $this->target = str_replace($thumbName, $this->prefix."{$thumbName}_{$this->width}x{$this->height}", $this->source);
        else
            $this->target = $target;

        $targetDir = dirname($this->target);
        if (!is_dir($targetDir))
            mkdir($targetDir, 0755, true);

        if (!(is_dir($targetDir) && is_writable($targetDir)))
            return false;

        if($size[2] == 1) {
            if($this->isAnimatedGif($source))
                return $source;
        }

        $thumbTime  = @filemtime($this->target);
        $sourceTime = @filemtime($this->source);

        if (is_file($this->target)) {
            if ($this->create == false && $sourceTime < $thumbTime) {
                return $this->target;
            }
        }

        $src = null;
        $degree = 0;

        if ($size[2] == 1) {
            $src = @imagecreatefromgif($this->source);
            $transparency = @imagecolortransparent($src);
        } else if ($size[2] == 2) {
            $src = @imagecreatefromjpeg($this->source);

            if (function_exists('exif_read_data')) {
                $exif = @exif_read_data($this->source);

                if (!empty($exif['Orientation'])) {
                    switch($exif['Orientation']) {
                        case 8:
                            $degree = 90;
                            break;
                        case 3:
                            $degree = 180;
                            break;
                        case 6:
                            $degree = -90;
                            break;
                    }

                    if ($degree) {
                        $src = imagerotate($src, $degree, 0);

                        if ($degree == 90 || $degree == -90) {
                            $tmp = $size;
                            $size[0] = $tmp[1];
                            $size[1] = $tmp[0];
                        }
                    }
                }
            }
        } else if ($size[2] == 3) {
            $src = @imagecreatefrompng($this->source);
            @imagealphablending($src, true);
        } else {
            return;
        }

        if (!$src)
            return;

        $isLarge = true;

        if ($this->width) {
            if (!$this->height) {
                $this->height = round(($this->width * $size[1]) / $size[0]);
            } else {
                if ($size[0] < $this->width || $size[1] < $this->height)
                    $isLarge = false;
            }
        } else {
            if ($this->height)
                $this->width = round(($this->height * $size[0]) / $size[1]);
        }

        $dst_x = 0;
        $dst_y = 0;
        $src_x = 0;
        $src_y = 0;
        $dst_w = $this->width;
        $dst_h = $this->height;
        $src_w = $size[0];
        $src_h = $size[1];

        $ratio = $dst_h / $dst_w;

        if ($isLarge) {
            if ($this->crop) {
                switch ($this->cropMode)
                {
                    case 'center':
                        if($size[1] / $size[0] >= $ratio) {
                            $src_h = round($src_w * $ratio);
                            $src_y = round(($size[1] - $src_h) / 2);
                        } else {
                            $src_w = round($size[1] / $ratio);
                            $src_x = round(($size[0] - $src_w) / 2);
                        }
                        break;
                    default:
                        if($size[1] / $size[0] >= $ratio) {
                            $src_h = round($src_w * $ratio);
                        } else {
                            $src_w = round($size[1] / $ratio);
                        }
                        break;
                }

                $dst = imagecreatetruecolor($dst_w, $dst_h);

                if ($size[2] == 3) {
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                } else if ($size[2] == 1) {
                    $palletsize = imagecolorstotal($src);
                    if($transparency >= 0 && $transparency < $palletsize) {
                        $transparentColor   = imagecolorsforindex($src, $transparency);
                        $currentTransparent = imagecolorallocate($dst, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);
                        imagefill($dst, 0, 0, $currentTransparent);
                        imagecolortransparent($dst, $currentTransparent);
                    }
                }
            } else {
                $dst = imagecreatetruecolor($dst_w, $dst_h);
                $bgColor = imagecolorallocate($dst, 255, 255, 255);
                if ($src_w > $src_h) {
                    $tmp_h = round(($dst_w * $src_h) / $src_w);
                    $dst_y = round(($dst_h - $tmp_h) / 2);
                    $dst_h = $tmp_h;
                } else {
                    $tmp_w = round(($dst_h * $src_w) / $src_h);
                    $dst_x = round(($dst_w - $tmp_w) / 2);
                    $dst_w = $tmp_w;
                }

                if($size[2] == 3) {
                    $bgColor = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                    imagefill($dst, 0, 0, $bgColor);
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                } else if($size[2] == 1) {
                    $palletsize = imagecolorstotal($src);
                    if($transparency >= 0 && $transparency < $palletsize) {
                        $transparentColor   = imagecolorsforindex($src, $transparency);
                        $currentTransparent = imagecolorallocate($dst, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);
                        imagefill($dst, 0, 0, $currentTransparent);
                        imagecolortransparent($dst, $currentTransparent);
                    } else {
                        imagefill($dst, 0, 0, $bgColor);
                    }
                } else {
                    imagefill($dst, 0, 0, $bgColor);
                }
            }
        } else {
            $dst = imagecreatetruecolor($dst_w, $dst_h);
            $bgColor = imagecolorallocate($dst, 255, 255, 255);

            if($src_w < $dst_w) {
                if($src_h >= $dst_h) {
                    if( $src_h > $src_w ){
                        $tmp_w = round(($dst_h * $src_w) / $src_h);
                        $dst_x = round(($dst_w - $tmp_w) / 2);
                        $dst_w = $tmp_w;
                    } else {
                        $dst_x = round(($dst_w - $src_w) / 2);
                        $src_h = $dst_h;
                    }
                } else {
                    $dst_x = round(($dst_w - $src_w) / 2);
                    $dst_y = round(($dst_h - $src_h) / 2);
                    $dst_w = $src_w;
                    $dst_h = $src_h;
                }
            } else {
                if($src_h < $dst_h) {
                    if( $src_w > $dst_w ){
                        $tmp_h = round(($dst_w * $src_h) / $src_w);
                        $dst_y = round(($dst_h - $tmp_h) / 2);
                        $dst_h = $tmp_h;
                    } else {
                        $dst_y = round(($dst_h - $src_h) / 2);
                        $dst_h = $src_h;
                        $src_w = $dst_w;
                    }
                }
            }

            if($size[2] == 3) {
                $bgColor = imagecolorallocatealpha($dst, 0, 0, 0, 127);
                imagefill($dst, 0, 0, $bgColor);
                imagealphablending($dst, false);
                imagesavealpha($dst, true);
            } else if($size[2] == 1) {
                $palletsize = imagecolorstotal($src);
                if($transparency >= 0 && $transparency < $palletsize) {
                    $transparentColor   = imagecolorsforindex($src, $transparency);
                    $currentTransparent = imagecolorallocate($dst, $transparentColor['red'], $transparentColor['green'], $transparentColor['blue']);
                    imagefill($dst, 0, 0, $currentTransparent);
                    imagecolortransparent($dst, $currentTransparent);
                } else {
                    imagefill($dst, 0, 0, $bgColor);
                }
            } else {
                imagefill($dst, 0, 0, $bgColor);
            }
        }

        imagecopyresampled($dst, $src, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

        if ($size[2] == 1) {
            imagegif($dst, $this->target);
        } else if ($size[2] == 3) {
            imagepng($dst, $this->target, $this->pngCompress);
        } else {
            imagejpeg($dst, $this->target, $this->jpgQuality);
        }

        chmod($this->target, 0644);

        imagedestroy($src);
        imagedestroy($dst);

        return $this->target;
    }

    public function delete(string $source)
    {
        if (!$source)
            return;

        $dir  = dirname($source);
        $name = pathinfo($source, PATHINFO_FILENAME);
        $ext  = pathinfo($source, PATHINFO_EXTENSION);

        foreach (glob($dir.DIRECTORY_SEPARATOR.$this->prefix.'*'.$name.'*.'.$ext) as $f) {
            @unlink($f);
        }
    }
}