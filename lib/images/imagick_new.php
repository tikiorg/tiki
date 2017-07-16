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
			parent::__construct($image, $isfile);
		} else {
			parent::__construct($image, $isfile);
		}
		$this->format = $format;
	}

	function _load_data()
	{
		if (!$this->loaded) {
			if (!empty($this->filename)) {
				$this->data = new Imagick();
				try {
					$this->data->readImage($this->filename);
					$this->loaded = true;
					$this->filename = null;
				}
				catch (ImagickException $e) {
					$this->loaded = true;
					$this->data = null;
				}
			} elseif (!empty($this->data)) {
				$tmp = new Imagick();
				try {
					$tmp->readImageBlob($this->data);
					$this->data =& $tmp;
					$this->loaded = true;
				}
				catch (ImagickException $e) {
					$this->data = null;
				}
			}
			if ($this->data) {
				$this->data->setImageFormat($this->format);
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
			return $this->data->scaleImage($x, $y);
		}
	}

	function resizethumb()
	{
		if ( $this->thumb !== null ) {
			$this->data = new Imagick();
			try {
				$this->data->readImageBlob($this->thumb);
				$this->loaded = true;
			}
			catch (ImagickException $e) {
				$this->loaded = true;
				$this->data = null;
			}
		} else {
			$this->_load_data();
		}
		if ($this->data) {
			parent::resizethumb();
		}
	}

    /**
     * @param $format
     */
    function set_format($format)
	{
		$this->_load_data();
		if ($this->data) {
			$this->format = $format;
			$this->data->setFormat($format);
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
			return $this->data->getImageBlob();
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
			$this->data->rotateImage(-$angle);
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
		$image = new Imagick();
		$format = strtoupper(trim($format));

		// Theses formats have pb if multipage document
		switch ($format) {
			case 'PDF':
			case 'PS':
			case 'HTML':
				return false;
		}
		return in_array($format, $image->queryFormats());
	}

    /**
     * @return mixed
     */
    function get_height()
	{
		$this->_load_data();
		if ($this->data)
			return $this->data->getImageHeight();
	}

    /**
     * @return mixed
     */
    function get_width()
	{
		$this->_load_data();
		if ($this->data)
			return $this->data->getImageWidth();
	}

	/**
	 * Allow adding text as overlay to a image
	 * @param $text
	 * @return string
	 */
	function addTextToImage($text)
	{
		$this->_load_data();

		if (! $this->data) {
			return false;
		}

		$font = dirname(dirname(__DIR__)) . '/lib/captcha/DejaVuSansMono.ttf';

		$padLeft = 20;
		$padBottom = 20;

		$image = new Imagick();
		$image->readImageBlob($this->data);
		$height = $image->getimageheight();

		$draw = new ImagickDraw();
		$draw->setFillColor('#000000');
		$draw->setStrokeColor(new ImagickPixel('#000000'));
		$draw->setStrokeWidth(3);
		$draw->setFont($font);
		$draw->setFontSize(12);
		$image->annotateImage($draw, $padLeft, $height - $padBottom, 0, $text);

		$draw = new ImagickDraw();
		$draw->setFillColor('#ffff00');
		$draw->setFont($font);
		$draw->setFontSize(12);
		$image->annotateImage($draw, $padLeft, $height - $padBottom, 0, $text);

		$this->data = $image;
		return $image->getImageBlob();
	}
	
}
