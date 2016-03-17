<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

    /**
     * @param $image
     * @param bool $isfile
     * @param string $format
     */
    function __construct($image, $isfile = false, $format = 'jpeg')
	{
		if ( $isfile ) {
			$this->filename = $image;
			parent::__construct(null, false);
		} else {
			parent::__construct($image, false);
		}
	}

	function _load_data()
	{
		if (!$this->loaded) {
			if (!empty($this->filename)) {
				$this->data = imagick_readimage($this->filename);
				$this->loaded = true;
			} elseif (!empty($this->data)) {
				$this->data = imagick_blob2image($this->data);
				$this->loaded = true;
			}
			if ( $this->loaded && ($t = imagick_failedreason($this->data))) {
				$this->data = NULL;
			}
		}
	}

    /**
     * @param $x
     * @param $y
     * @return mixed
     */
    function _resize($x, $y)
	{
		if ($this->data) {
			return imagick_scale($this->data, $x, $y);
		}
	}

	function resizethumb()
	{
		if ( $this->thumb !== null ) {
			$this->data = imagick_blob2image($this->thumb);
			$this->loaded = true;
		} else {
			$this->_load_data();
		}
		if ($this->data) {
			return parent::resizethumb();
		}
	}

    /**
     * @return mixed
     */
    function get_mimetype()
	{
		$this->_load_data();
		if ($this->data) {
			return imagick_getmimetype($this->data);
		}
	}

    /**
     * @param $format
     */
    function set_format($format)
	{
		$this->_load_data();
		$this->format = $format;
		if ($this->data) {
			imagick_convert($this->data, strtoupper(trim($format)));
		}
	}

    /**
     * @return string
     */
    function get_format()
	{
		return $this->format;
	}

    /**
     * @return mixed
     */
    function display()
	{
		$this->_load_data();
		if ($this->data) {
			return imagick_image2blob($this->data);
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
			imagick_rotate($this->data, -$angle);
			return true;
		} else {
			return false;
		}
	}

    /**
     * @param $format
     * @return bool
     */
    function is_supported($format)
	{
		// not handled yet: html, mpeg, pdf
		return in_array(
			strtolower($format),
			array(
				'art',
				'avi',
				'avs',
				'bmp',
				'cin',
				'cmyk',
				'cur',
				'cut',
				'dcm',
				'dcx',
				'dib',
				'dpx',
				'epdf',
				'fits',
				'gif',
				'gray',
				'ico',
				'jng',
				'jpg',
				'jpeg',
				'mat',
				'miff',
				'mono',
				'mng',
				'mpc',
				'msl',
				'mtv',
				'mvg',
				'otb',
				'p7',
				'palm',
				'pbm',
				'pcd',
				'pcds',
				'pcl',
				'pcx',
				'pdb',
				'pfa',
				'pfb',
				'pgm',
				'picon',
				'pict',
				'pix',
				'png',
				'pnm',
				'ppm',
				'psd',
				'ptif',
				'pwp',
				'rgb',
				'rgba',
				'rla',
				'rle',
				'sct',
				'sfw',
				'sgi',
				'sun',
				'tga',
				'tim',
				'txt',
				'uil',
				'uyvy',
				'vicar',
				'viff',
				'wbmp',
				'wpg',
				'xbm',
				'xcf',
				'xpm',
				'xwd',
				'yuv'
			)
		);
	}

    /**
     * @return mixed
     */
    function get_height()
	{
		$this->_load_data();
		if ($this->data) {
			return imagick_getheight($this->data);
		}
	}

    /**
     * @return mixed
     */
    function get_width()
	{
		$this->_load_data();
		if ($this->data) {
			return imagick_getwidth($this->data);
		}
	}
}
