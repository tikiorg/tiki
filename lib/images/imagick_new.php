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
			parent::__construct(NULL, false);
		} else {
			parent::__construct($image, false);
		}
	}

	function _load_data()
	{
		if (!$this->loaded) {
			if (!empty($this->filename)) {
				$this->data = new Imagick();
				try {
					$this->data->readImage($this->filename);
					$this->loaded = true;
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
			return parent::resizethumb();
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
}
