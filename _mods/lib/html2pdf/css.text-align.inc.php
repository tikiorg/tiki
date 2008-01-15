<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/css.text-align.inc.php,v 1.1 2008-01-15 09:20:39 mose Exp $

define('TA_LEFT',0);
define('TA_RIGHT',1);
define('TA_CENTER',2);
define('TA_JUSTIFY',3);

class CSSTextAlign extends CSSProperty {
  function CSSTextAlign() { $this->CSSProperty(true, true); }
  
  function default_value() { return TA_LEFT; }

  function parse($value) {
    // Convert value to lower case, as html allows values 
    // in both cases to be entered
    $value = strtolower($value);

    if ($value === 'left') { return TA_LEFT; }
    if ($value === 'right') { return TA_RIGHT; }
    if ($value === 'center') { return TA_CENTER; }

    // For compatibility with non-valid HTML
    //
    if ($value === 'middle') { return TA_CENTER; }

    if ($value === 'justify') { return TA_JUSTIFY; }
    return $this->default_value();
  }

  function value2ps($value) {
    switch ($value) {
    case TA_LEFT:
      return "{text-align-left}";
    case TA_RIGHT:
      return "{text-align-right}";
    case TA_CENTER:
      return "{text-align-center}";
    case TA_JUSTIFY:
      return "{text-align-justify}";
    default:
      return "{text-align-left}";
    }
  }

  function value2pdf($value) { 
    switch ($value) {
    case TA_LEFT:
      return "ta_left";
    case TA_RIGHT:
      return "ta_right";
    case TA_CENTER:
      return "ta_center";
    case TA_JUSTIFY:
      return "ta_justify";
    default:
      return "ta_left";
    }
  }

  function ps($writer) {
    $writer->write($this->value2ps($this->get())." 1 index put-text-align\n");
  }
}

register_css_property('text-align', new CSSTextAlign);

?>