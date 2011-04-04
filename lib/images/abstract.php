<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

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
	var $header = null;
	var $otherinfo = null;
	var $iptc = null;
	var $exif = null;

	function __construct($image, $isfile = false) {
		if ( ! empty($image) || $this->filename !== null ) {
			if ( is_readable( $this->filename ) && function_exists('exif_thumbnail') && in_array(image_type_to_mime_type(exif_imagetype($this->filename)), array('image/jpeg', 'image/tiff'))) {
				$this->thumb = @exif_thumbnail($this->filename, $this->width, $this->height);
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

	function _load_data() {
		if (!$this->loaded) {
			if (!empty($this->filename)) {
				$this->data = $this->get_from_file($this->filename);
				$this->loaded = true;
			} elseif (!empty($this->data)) {
				$this->loaded = true;
			}
		}
	}

	function is_empty() {
		return empty($this->data) && empty($this->filename);
	}

	function get_from_file($filename) {
		$content = NULL;
		if ( is_readable($filename) ) {
			$f = fopen($filename, 'rb');
			$size = filesize($filename);
			$content = fread($f, $size);
			fclose($f);
		}
		return $content;
	}

	function _resize($x, $y) { }

	function resize($x = 0, $y = 0) {
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

	function resizemax($max) {
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

	function resizethumb() {
		global $prefs;
		$this->resizemax($prefs['fgal_thumb_max_size']);
	}

	function scale($r) {
		$this->_load_data();
		$x0 = $this->get_width();
		$y0 = $this->get_height();
		if ( $x0 <= 0 || $y0 <= 0 || $r <= 0 ) return;
		$this->_resize($x0 * $r, $y0 * $r);
	}

	function get_mimetype() {
		return 'image/'.strtolower($this->get_format());
	}

	function set_format($format) {
		$this->format = $format;
	}

	function get_format() {
		if ( $this->format == '' ) {
			$this->set_format('jpeg');
			return 'jpeg';
		} else {
			return $this->format;
		}
	}

	function display() {
		$this->_load_data();
		return $this->data;
	}

	function convert($format) {
		if ( $this->is_supported($format) ) {
			$this->set_format($format);
			return true;
		} else {
			return false;
		}
	}

	function rotate() { }

	function is_supported($format) {
		return false;
	}

	function get_icon_default_format() {
		return 'png';
	}

	function get_icon_default_x() {
		return 16;
	}

	function get_icon_default_y() {
		return 16;
	}

	function icon($extension, $x = 0, $y = 0) {
		$keep_original = ( $x == 0 && $y == 0 );

		// This method is not necessarely called through an instance
		$class = isset($this) ? $this->classname : 'Image';
		$format = call_user_func(array($class, 'get_icon_default_format'));

		if ( ! $keep_original && class_exists($class) ) {
			$icon_format = $format;
			$class = 'Image';

			if ( call_user_func(array($class, 'is_supported'), 'svg') ) {
				$format = 'svg';
			} elseif ( call_user_func(array($class, 'is_supported'), 'png') ) {
				$format = 'png';
			} else {
				return false;
			}
		}

		$name = "lib/images/icons/$extension.$format";
		if ( ! file_exists($name) ) {
			$name = "lib/images/icons/unknown.$format";
		}

		if ( ! $keep_original ) {
			$icon = new $class($name, true);
			if ( $format != $icon_format ) {
				$icon->convert($icon_format);
			}
			$icon->resize($x, $y);

			return $icon->display();
		} else {
			return ImageAbstract::get_from_file($name);
		}

	} 

	function _get_height() { return NULL; }

	function _get_width() { return NULL; }

	function get_height() {
		if ( $this->height === NULL ) {
			$this->height = $this->_get_height();
		}
		return $this->height;
	}

	function get_width() {
		if ( $this->width === NULL ) {
			$this->width = $this->_get_width();
		}
		return $this->width;
	}

	function set_img_info($image, $isfile = true) {
		$tempfile = '';
		if (!$isfile) {
	 		$cwd = getcwd(); 								//get current working directory
	 		$tempfile = tempnam("$cwd/tmp", 'temp_image_');	//create tempfile and return the path/name (make sure you have created tmp directory under $cwd
	 		$temphandle = fopen($tempfile, 'w');			//open for writing
	 		fwrite($temphandle, $image); 					//write image to tempfile
	 		fclose($temphandle);
	 		$image = $tempfile;
		}
		$this->header = getimagesize($image, $otherinfo);
		$this->width = $this->header[0];
		$this->height = $this->header[1];
		$this->otherinfo = $otherinfo;
		$this->exif = exif_read_data($image, 0, true);
		if (!empty($tempfile)) {
			unlink($tempfile);								// remove tempfile
		}
	}
	
	function get_iptc($otherinfo) {
		if (!empty($otherinfo['APP13'])) {
			$iptc = iptcparse($otherinfo['APP13']);
			$tags = $this->get_iptc_tags();
			foreach ($iptc as $key => $value) {
				if (array_key_exists($key, $tags)) {
					trim($iptc[$key][0]);
					$iptc[$key][1] = trim($tags[$key]);
					$this->iptc = $iptc;
				} else {
					$iptc[$key][1] = '';
					$this->iptc = $iptc;
				}
			}
		} else {
			$this->iptc = null;
		}
		return $this->iptc;
	}

	function get_iptc_tags() {
		$tags = array(
			'2#000' => tra('Application Record Version'),
			'2#003' => tra('Object Type Reference'),
			'2#004' => tra('Object Attribute Reference'),
			'2#005' => tra('Object Name'),
			'2#007' => tra('Edit Status'),
			'2#008' => tra('Editorial Update'),
			'2#010' => tra('Urgency'),
			'2#012' => tra('Subject Reference'),
			'2#015' => tra('Category'),
			'2#020' => tra('Supplemental Categories'),
			'2#022' => tra('Fixture Identifier'),
			'2#025' => tra('Keywords'),
			'2#026' => tra('Content Location Code'),
			'2#027' => tra('Content Location Name'),
			'2#030' => tra('Release Date'),
			'2#035' => tra('Release Time'),
			'2#037' => tra('Expiration Date'),
			'2#038' => tra('Expiration Time'),
			'2#040' => tra('Special Instructions'),
			'2#042' => tra('Action Advised'),
			'2#045' => tra('Reference Service'),
			'2#047' => tra('Reference Date'),
			'2#050' => tra('Reference Number'),
			'2#055' => tra('Date Created'),
			'2#060' => tra('Time Created'),
			'2#062' => tra('Digital Creation Date'),
			'2#063' => tra('Digital Creation Time'),
			'2#065' => tra('Originating Program'),
			'2#070' => tra('Program Version'),
			'2#075' => tra('Object Cycle'),
			'2#080' => tra('Byline'),
			'2#085' => tra('Byline Title'),
			'2#090' => tra('City'),
			'2#095' => tra('Province/State'),
			'2#100' => tra('Country Code'),
			'2#101' => tra('Country'),
			'2#103' => tra('Original Transmission Reference'),
			'2#105' => tra('Headline'),
			'2#110' => tra('Credit'),
			'2#115' => tra('Source'),
			'2#116' => tra('Copyright String'),
			'2#120' => tra('Caption'),
			'2#121' => tra('Local Caption')
		);
		return $tags;
	}
}
