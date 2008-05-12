<?php
if (! defined('JIMW_IMAGE_THUMBNAIL'))
    define('JIMW_IMAGE_THUMBNAIL', 'thumbnails/');
if (! defined('JIMW_IMAGE_THUMBNAIL_MAXWIDTH'))
    define('JIMW_IMAGE_THUMBNAIL_MAXWIDTH', 90);
if (! defined('JIMW_IMAGE_THUMBNAIL_MAXHEIGHT'))
    define('JIMW_IMAGE_THUMBNAIL_MAXHEIGHT', 90);
class Jimw_Image
{
    private $file = '';
    private $baseurl = '';
    public function __construct ($filename, $baseurl = '')
    {
        $this->setFile($filename);
        $this->setBaseurl($baseurl);
    }
    /**
     * @return unknown
     */
    public function getFile ()
    {
        return $this->file;
    }
    /**
     * @param unknown_type $file
     */
    public function setFile ($filename)
    {
        if (file_exists($filename)) {
            $this->file = $filename;
        } else {
            throw new Jimw_Exception('Picture didn\'t exist');
        }
    }
    /**
     * @return unknown
     */
    public function getBaseurl ()
    {
        return $this->baseurl;
    }
    /**
     * @param unknown_type $baseurl
     */
    public function setBaseurl ($baseurl)
    {
        $this->baseurl = rtrim($baseurl, '/');
    }
    /**
     * Get the file url
     */
    public function getUrl ()
    {
        return $this->baseurl . '/' . $this->getFilename();
    }
    /**
     * Get the image size : getimagesize
     */
    public function getImageSize ()
    {
        return @getimagesize($this->file);
    }
    /**
     * Get the type of the image
     */
    public function getType ()
    {
        $type = $this->getImageSize();
        return $type[3];
    }
    /**
     * Get the extension of the file
     */
    public function getExtension ()
    {
        $pos = strrpos($this->file, '.');
        if (pos > 0) {
            return substr($this->file, $pos + 1);
        }
        return false;
    }
    /**
     * Get the name of the file
     */
    public function getFilename ()
    {
        $pos = strrpos($this->file, '/');
        if ($pos >= 0) {
            return substr($this->file, $pos + 1);
        }
        return $this->file;
    }
    /**
     * Get the directory of the file
     */
    public function getDirname ()
    {
        return dirname($this->file);
    }
    /**
     * Get the thumbnails file name
     */
    public function getThumbnailsFilename ()
    {
        return $this->getDirname() . '/' . JIMW_IMAGE_THUMBNAIL_DIR . $this->getFilename();
    }
    /**
     * Get the thumbnails file name
     */
    public function getThumbnailsUrl ()
    {
        return $this->baseurl . '/' . JIMW_IMAGE_THUMBNAIL_DIR . $this->getFilename();
    }
    /**
     * Return true if the thumbnails exist
     */
    public function hasThumbnails ($create = false)
    {
        if (file_exists($this->getThumbnailsFilename())) {
            return true;
        } else if ($create) {
            return self::createThumbnails($this->file, $this->getThumbnailsFilename(), JIMW_IMAGE_THUMBNAIL_MAXWIDTH, JIMW_IMAGE_THUMBNAIL_MAXHEIGHT);
        }
        return false;
    }
    public static function createThumbnails ($file, $thumbnails, $maxwidth = JIMW_IMAGE_THUMBNAIL_MAXWIDTH, $maxheight = JIMW_IMAGE_THUMBNAIL_MAXHEIGHT)
    {
        @ini_set('memory_limit', '256M');
        $dir = dirname($thumbnails);
        if (!is_dir($dir)) {
            if (file_exists($dir)) return false;
            mkdir($dir);
        }
        list($width, $height, $type) = getimagesize($file);
        list($tbwidth, $tbheight) = self::constrainDimensions($width, $height, $maxwidth, $maxheight);
        if ($type == IMAGETYPE_GIF) {
            $image_orig = imagecreatefromgif($file);
            $image_new = imagecreatetruecolor($tbwidth, $tbheight);
            $src_tc_idx = imagecolortransparent($image_orig);
            $src_tc = imagecolorsforindex($image_orig, $src_tc_idx);
            $tgt_tc_idx = imagecolorallocate($image_new, $src_tc['red'], $src_tc['green'], $src_tc['blue']);
            imagefill($image_new, 0, 0, $tgt_tc_idx);
            imagecolortransparent($image_new, $tgt_tc_idx);
        }
        else {
            if($type == IMAGETYPE_JPEG) $image_orig = imagecreatefromjpeg($file);
		    elseif($type == IMAGETYPE_PNG) $image_orig = imagecreatefrompng($file);
		    else return false;
		    $image_new = imagecreatetruecolor($tbwidth, $tbheight);
        }
        imagecopyresampled($image_new, $image_orig, 0, 0, 0, 0, $tbwidth, $tbheight, $width, $height);
        if($type == IMAGETYPE_JPEG) imagejpeg($image_new, $thumbnails, 80);
		elseif($type == IMAGETYPE_PNG) imagepng($image_new, $thumbnails, 80);
		elseif($type == IMAGETYPE_GIF) imagegif($image_new, $thumbnails, 80);
		else return false;
		return file_exists($thumbnails);
    }
    
    public static function constrainDimensions ($current_width, $current_height, $max_width = 0, $max_height = 0)
    {
        if (! $max_width and ! $max_height)
            return array($current_width , $current_height);
        $width_ratio = $height_ratio = 1.0;
        if ($max_width > 0 && $current_width > $max_width)
            $width_ratio = $max_width / $current_width;
        if ($max_height > 0 && $current_height > $max_height)
            $height_ratio = $max_height / $current_height;
            // the smaller ratio is the one we need to fit it to the constraining box
        $ratio = min($width_ratio, $height_ratio);
        return array(intval($current_width * $ratio) , intval($current_height * $ratio));
    }
}
?>
