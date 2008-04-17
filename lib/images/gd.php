<?php

require_once('lib/images/abstract.php');

class Image extends ImageAbstract {
  var $gdinfo;
  var $gdversion;
  var $havegd;

  function __construct($image, $isfile = false) {
    parent::__construct($image, false);

    // Which GD Version do we have?
    $exts = get_loaded_extensions();
    if ( in_array('gd', $exts) && $image != '' ) {
      $this->havegd = true;
      $this->get_gdinfo();
      if ( $isfile ) {
        $this->format = strtolower(substr($image, strrpos($image, '.') + 1));
        if ( $this->is_supported($this->format) ) {
          if ( $this->format == 'jpg' ) $this->format = 'jpeg';
          $this->data = call_user_func('imagecreatefrom'.$this->format, $this->data);
        }
      } else {
        $this->data = imagecreatefromstring($this->data);
      }
    } else {
      $this->havegd = false;
      $this->gdinfo = array();
    }
  }

  function Image($image, $isfile = false) {
    Image::__construct($image, $isfile);
  }

  function _resize($x, $y) {
    $t = imagecreatetruecolor($x, $y);
    imagecopyresampled($t, $this->data, 0, 0, 0, 0, $x, $y, $this->get_width(), $this->get_height());
    $this->data = $t;
    unset($t);
  }

  function display() {
  
    ob_end_flush();
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
      default:
        ob_end_clean();
        return NULL;
    }
    $image = ob_get_contents();
    ob_end_clean();

    return $image;
  }

  function rotate($angle) {
    $this->data = imagerotate($this->data, $angle, 0);
    return true;
  }

  function get_gdinfo() {
    $gdinfo = array();
    $gdversion = '';

    if ( function_exists("gd_info") ) {
      $gdinfo = gd_info();
      preg_match("/[0-9]+\.[0-9]+/", $gdinfo["GD Version"], $gdversiontmp);
      $gdversion = $gdversiontmp[0];
    } else {
      //next try
      ob_start();
      phpinfo (INFO_MODULES);
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
  function is_supported($format) {

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
    }

    return false;
  }

  function _get_height() {
    return imagesy($this->data);
  }

  function _get_width() {
    return imagesx($this->data);
  }

}

?>
