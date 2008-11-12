<?php

require_once('lib/images/abstract.php');

class Image extends ImageAbstract {

  function __construct($image, $isfile = false) {
    if ( $isfile ) {
      $this->filename = $image;
      parent::__construct(NULL, false);
    } else {
      parent::__construct($image, false);
    }
  }

	function _load_data() {
		if (!$this->loaded) {
			if (!empty($this->filename)) {
				$this->data = new Imagick();
				$this->data->readImage($this->filename);
				$this->loaded = true;
			} elseif (!empty($this->data)) {
				$tmp = new Imagick();
				$tmp->readImageBlob($this->data);
				$this->data =& $tmp;
				$this->loaded = true;
			}	
		}
	}

  function Image($image, $isfile = false) {
    Image::__construct($image, $isfile);
  }

  function _resize($x, $y) {
    return $this->data->scaleImage($x, $y);
  }

  function resizethumb() {
    if ( $this->thumb !== null ) {
			$this->data = new Imagick();
			$this->data->readImageBlob($this->thumb);
			$this->loaded = true;
		} else { 
			$this->_load_data();
		}
    return parent::resizethumb();
  }

  function set_format($format) {
		$this->_load_data();
    $this->format = $format;
    $this->data->setFormat($format);
  }

  function get_format() {
    return $this->format;
  }

  function display() {
		$this->_load_data();
    return $this->data->getImageBlob();
  }

  function rotate($angle) {
		$this->_load_data();
    $this->data->rotateImage(-$angle);
    return true;
  }

  function is_supported($format) {
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

  function get_height() {
		$this->_load_data();
    return $this->data->getImageHeight();
  }

  function get_width() {
		$this->_load_data();
    return $this->data->getImageWidth();
  }
}

?>
