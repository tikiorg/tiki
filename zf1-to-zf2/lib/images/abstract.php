<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 *
 */
class ImageAbstract
{
	var $data = NULL;
	var $format = 'jpeg';
	var $height = NULL;
	var $width = NULL;
	var $classname = 'ImageAbstract';
	var $filename = null;
	var $thumb = null;
	var $loaded = false;
	var $metadata = null;			//to hold metadata from the FileMetadata class

    /**
     * @param $image
     * @param bool $isfile
     */
    function __construct($image, $isfile = false)
	{
		if ( ! empty($image) || $this->filename !== null ) {
			if ( is_readable($this->filename) && function_exists('exif_thumbnail') && in_array(image_type_to_mime_type(exif_imagetype($this->filename)), array('image/jpeg', 'image/tiff'))) {
				$this->thumb = @exif_thumbnail($this->filename);
				if (trim($this->thumb) == "") $this->thumb = NULL;
			}
			$this->classname = get_class($this);
			if ( $isfile ) {
				$this->filename = $image;
			} else {
				$this->data = $image;
			}
		}
	}

	function _load_data()
	{
		if (!$this->loaded) {
			if (!empty($this->filename)) {
				$this->data = $this->get_from_file($this->filename);
				$this->loaded = true;
			} elseif (!empty($this->data)) {
				$this->loaded = true;
			}
		}
	}

    /**
     * @return bool
     */
    function is_empty()
	{
		return empty($this->data) && empty($this->filename);
	}

    /**
     * @param $filename
     * @return null|string
     */
    function get_from_file($filename)
	{
		$content = NULL;
		if ( is_readable($filename) ) {
			$f = fopen($filename, 'rb');
			$size = filesize($filename);
			$content = fread($f, $size);
			fclose($f);
		}
		return $content;
	}

    /**
     * @param $x
     * @param $y
     */
    function _resize($x, $y)
	{
	}

    /**
     * @param int $x
     * @param int $y
     */
    function resize($x = 0, $y = 0)
	{
		$this->_load_data();
		if ($this->data) {
			$x0 = $this->get_width();
			$y0 = $this->get_height();

			if ( $x > 0 || $y > 0 ) {
				if ( $x <= 0 ) {
					$x = $x0 * ( $y / $y0 );
				}
				if ( $y <= 0 ) {
					$y = $y0 * ( $x / $x0 );
				}
				$this->_resize($x+0, $y+0);
			}
		}
	}

    /**
     * @param $max
     */
    function resizemax($max)
	{
		$this->_load_data();
		if ($this->data) {
			$x0 = $this->get_width();
			$y0 = $this->get_height();
			if ( $x0 <= 0 || $y0 <= 0 || $max <= 0 ) return;
			if ( $x0 > $max || $y0 > $max ) {
				$r = $max / ( ( $x0 > $y0 ) ? $x0 : $y0 );
				$this->scale($r);
			}
		}
	}

	function resizethumb()
	{
		global $prefs;
		$this->resizemax($prefs['fgal_thumb_max_size']);
	}

    /**
     * @param $r
     */
    function scale($r)
	{
		$this->_load_data();
		$x0 = $this->get_width();
		$y0 = $this->get_height();
		if ( $x0 <= 0 || $y0 <= 0 || $r <= 0 ) return;
		$this->_resize($x0 * $r, $y0 * $r);
	}

    /**
     * @return string
     */
    function get_mimetype()
	{
		return 'image/'.strtolower($this->get_format());
	}

    /**
     * @param $format
     */
    function set_format($format)
	{
		$this->format = $format;
	}

    /**
     * @return string
     */
    function get_format()
	{
		if ( $this->format == '' ) {
			$this->set_format('jpeg');
			return 'jpeg';
		} else {
			return $this->format;
		}
	}

    /**
     * @return null
     */
    function display()
	{
		$this->_load_data();
		return $this->data;
	}

    /**
     * @param $format
     * @return bool
     */
    function convert($format)
	{
		if ( $this->is_supported($format) ) {
			$this->set_format($format);
			return true;
		} else {
			return false;
		}
	}

    /**
     * @param $angle
     */
    function rotate($angle)
	{
	}

    /**
     * @param $format
     * @return bool
     */
    function is_supported($format)
	{
		return false;
	}

    /**
     * @return string
     */
    function get_icon_default_format()
	{
		return 'png';
	}

    /**
     * @return int
     */
    function get_icon_default_x()
	{
		return 16;
	}

    /**
     * @return int
     */
    function get_icon_default_y()
	{
		return 16;
	}

    /**
     * @param $extension
     * @param int $x
     * @param int $y
     * @return bool|null|string
     */
    function icon($extension, $x = 0, $y = 0)
	{
		$keep_original = ( $x == 0 && $y == 0 );

		$format = $this->get_icon_default_format();
		$icon_format = '';

		if ( ! $keep_original ) {
			$icon_format = $format;

			if ( $this->is_supported('png') ) {
				$format = 'png';
			} elseif ( $this->is_supported('svg') ) {
				$format = 'svg';
			} else {
				return false;
			}
		}

		$name = "lib/images/icons/$extension.$format";
		if ( ! file_exists($name) ) {
			$name = "lib/images/icons/unknown.$format";
		}

		if ( ! $keep_original && $format != 'svg' ) {
			$icon = new Image($name, true, $format);
			if ( $format != $icon_format ) {
				$icon->convert($icon_format);
			}
			$icon->resize($x, $y);

			return $icon->display();
		} else {
			return $this->get_from_file($name);
		}

	}

    /**
     * @return null
     */
    function _get_height()
	{
		return NULL;
	}

    /**
     * @return null
     */
    function _get_width()
	{
		return NULL;
	}

    /**
     * @return null
     */
    function get_height()
	{
		if ( $this->height === NULL ) {
			$this->height = $this->_get_height();
		}
		return $this->height;
	}

    /**
     * @return null
     */
    function get_width()
	{
		if ( $this->width === NULL ) {
			$this->width = $this->_get_width();
		}
		return $this->width;
	}

    /**
     * @param null $filename
     * @param bool $ispath
     * @param bool $extended
     * @param bool $bestarray
     * @return FileMetadata|null
     */
    function getMetadata($filename = null, $ispath = true, $extended = true, $bestarray = true)
	{
		include_once('lib/metadata/metadatalib.php');
		if ($filename === null) {
			if (!empty($this->filename)) {
				$filename = $this->filename;
				$ispath = true;
			} elseif (!empty($this->data)) {
				$filename = $this->data;
				$ispath = false;
			}
		}
		if (!is_object($this->metadata) || get_class($this->metadata) != 'FileMetadata') {
			$metadata = new FileMetadata;
			$this->metadata = $metadata->getMetadata($filename, $ispath, $extended);
		}
		return $this->metadata;
	}
}
