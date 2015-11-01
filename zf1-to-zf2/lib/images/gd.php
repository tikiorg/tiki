<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/images/abstract.php');

/**
 *
 */
class Image extends ImageAbstract
{
	var $gdinfo;
	var $gdversion;
	var $havegd = false;

    /**
     * @param $image
     * @param bool $isfile
     * @param string $format
     */
    function __construct($image, $isfile = false, $format = 'jpeg')
	{

		// Which GD Version do we have?
		$exts = get_loaded_extensions();
		if ( in_array('gd', $exts) && ! empty($image) ) {
			$this->havegd = true;
			$this->get_gdinfo();
			if ($isfile) {
				$this->filename = $image;
				parent::__construct(NULL, false);
				$this->loaded = false;
			} else {
				parent::__construct($image, false);
				$this->format = $format;
				$this->loaded = false;
			}
		} else {
			$this->havegd = false;
			$this->gdinfo = array();
		}
	}

	function _load_data() 
	{
		if (!$this->loaded && $this->havegd) {
			if (!empty($this->filename) && is_file($this->filename)) {
				$this->format = strtolower(substr($this->filename, strrpos($this->filename, '.') + 1));
				list($this->width, $this->height, $type) = getimagesize($this->filename);
				if (function_exists("image_type_to_extension")) {
					$this->format = image_type_to_extension($type, false);
				} else {
					$tmp = image_type_to_mime_type($type);
					$this->format = strtolower(substr($tmp, strrpos($tmp, "/")+1));
				}
				if ( $this->is_supported($this->format) ) {
					if ( $this->format == 'jpg' ) $this->format = 'jpeg';
					$this->data = call_user_func('imagecreatefrom'.$this->format, $this->filename);
					$this->loaded = true;
				}
			} elseif (
				!empty($this->data) &&
				$this->data != 'REFERENCE' &&
				preg_match('/^[<]svg/', $this->data) == false //In some cases, an svg will be recognized as an alternate picture type, here we simply check the beginning for "<svg" and if it is found, it is an svg
			) {
				$this->data = imagecreatefromstring($this->data);
				$this->loaded = true;
			} else {
				parent::_load_data();
			}
		}
	}

    /**
     * @param $x
     * @param $y
     */
    function _resize($x, $y)
	{
		if ($this->data) {
			if ($this->format == 'svg') {
				$svgAttributes = ' width="' . $x . '" height="' . $y . '" viewBox="0 0 ' . $this->width . ' ' . $this->height . '" preserveAspectRatio="xMinYMin meet"';
				$this->data = preg_replace('/width="'.$this->width.'" height="'.$this->height.'"/', $svgAttributes, $this->data);
			} else {
				$t = imagecreatetruecolor($x, $y);
				// trick to have a transparent background for png instead of black
				imagesavealpha($t, true);
				$trans_colour = imagecolorallocatealpha($t, 0, 0, 0, 127);
				imagefill($t, 0, 0, $trans_colour);

				imagecopyresampled($t, $this->data, 0, 0, 0, 0, $x, $y, $this->get_width(), $this->get_height());
				$this->data = $t;
				unset($t);
			}
		}
	}

	function resizethumb() 
	{
		if ( $this->thumb !== null ) {
			$this->data = imagecreatefromstring($this->thumb);
			$this->loaded = true;
		} else {
			$this->_load_data();
		}
		return parent::resizethumb();
	}

    /**
     * @return null|string
     */
    function display()
	{

		$this->_load_data();
		if ($this->data) {
			//@ob_end_flush();	// ignore E_NOTICE if no buffer
			ob_start();
			switch ( strtolower($this->format) ) {
				case 'jpeg':
				case 'jpg':
					imagejpeg($this->data);
    				break;
				case 'gif':
					imagegif($this->data);
    				break;
				case 'png':
					imagepng($this->data);
    				break;
				case 'wbmp':
					imagewbmp($this->data);
    				break;
				case 'svg':
					echo $this->data;
    				break;
				default:
					ob_end_clean();
					return NULL;
			}
			$image = ob_get_contents();
			ob_end_clean();

			return $image;
		} else {
			return NULL;
		}
	}

    /**
     * @param $angle
     * @return bool
     */
    function rotate($angle)
	{
		$this->_load_data();
		if ($this->data) {
			$this->data = imagerotate($this->data, $angle, 0);
			return true;
		} else {
			return false;
		}
	}

    /**
     * @return array
     */
    function get_gdinfo()
	{
		$gdinfo = array();
		$gdversion = '';

		if ( function_exists("gd_info") ) {
			$gdinfo = gd_info();
			preg_match("/[0-9]+\.[0-9]+/", $gdinfo["GD Version"], $gdversiontmp);
			$gdversion = $gdversiontmp[0];
		} else {
			//next try
			ob_start();
			phpinfo(INFO_MODULES);
			$gdversion = preg_match('/GD Version.*2.0/', ob_get_contents()) ? '2.0' : '1.0';
			$gdinfo["JPG Support"] = preg_match('/JPG Support.*enabled/', ob_get_contents());
			$gdinfo["PNG Support"] = preg_match('/PNG Support.*enabled/', ob_get_contents());
			$gdinfo["GIF Create Support"] = preg_match('/GIF Create Support.*enabled/', ob_get_contents());
			$gdinfo["WBMP Support"] = preg_match('/WBMP Support.*enabled/', ob_get_contents());
			$gdinfo["XBM Support"] = preg_match('/XBM Support.*enabled/', ob_get_contents());
			ob_end_clean();
		}

		if ( isset($this) ) {
			$this->gdinfo = $gdinfo;
			$this->gdversion = $gdversion;
		} 
		return $gdinfo;
	}

	// This method do not need to be called on an instance
    /**
     * @param $format
     * @return bool|int
     */
    function is_supported($format)
	{

		if ( ! function_exists('imagetypes') ) {
			$gdinfo = isset($this) ? $this->gdinfo : Image::get_gdinfo();
		}

		switch ( strtolower($format) ) {
			case 'jpeg':
			case 'jpg':
				if ( isset($gdinfo) && $gdinfo['JPG Support'] ) {
					return true;
				} else {
					return ( imagetypes() & IMG_JPG );
				}
			case 'png':
				if ( isset($gdinfo) && $gdinfo['PNG Support'] ) {
					return true;
				} else {
					return ( imagetypes() & IMG_PNG );
				}
			case 'gif':
				if ( isset($gdinfo) && $gdinfo['GIF Create Support'] ) {
					return true;
				} else {
					return ( imagetypes() & IMG_GIF );
				}
			case 'wbmp':
				if ( isset($gdinfo) && $gdinfo['WBMP Support']) {
					return true;
				} else {
					return ( imagetypes() & IMG_WBMP );
				}
			case 'xpm':
				if ( isset($gdinfo) && $gdinfo['XPM Support']) {
					return true;
				} else {
					return ( imagetypes() & IMG_XPM );
				}
			case 'svg':
				return true;
		}

		return false;
	}

    /**
     * @return int|null
     */
    function _get_height()
	{
		if ($this->loaded && $this->data) {
			if ($this->format == 'svg') {
				if (preg_match('/height="(\d+)"/', $this->data, $match)) {
					return $match[1];
				}
			} else {
				return @imagesy($this->data);
			}
		} else if ($this->height) {
			return $this->height;
		} else if ($this->filename && is_readable($this->filename)) {
			list($this->width, $this->height, $type) = getimagesize($this->filename);
			if ($this->height) {
				return $this->height;
			}
		}
		if (!$this->loaded || !$this->data) {
			$this->_load_data();
		}
		if ($this->data) {
			return @imagesy($this->data);
		}
	}

    /**
     * @return int|null
     */
    function _get_width()
	{
		if ($this->loaded && $this->data) {
			if ($this->format == 'svg') {
				if (preg_match('/width="(\d+)"/', $this->data, $match)) {
					return $match[1];
				}
			} else {
				return @imagesx($this->data);
			}
		} else if ($this->width) {
			return $this->width;
		} else if ($this->filename && is_readable($this->filename)) {
			list($this->width, $this->height, $type) = getimagesize($this->filename);
			if ($this->width) {
				return $this->width;
			}
		}
		if (!$this->loaded || !$this->data) {
			$this->_load_data();
		}
		if ($this->data) {
			return @imagesx($this->data);
		}
	}

}
