<?php
// $Header: /cvsroot/tikiwiki/lib/html2pdf/css.width.inc.php,v 1.1.1.1 2006-02-06 15:38:27 nikchankov Exp $

define('WIDTH_AUTO',-1);
define('WIDTH_INHERIT',-2);

class CSSWidth extends CSSProperty {
  function CSSWidth() { $this->CSSProperty(false, false); }

  function default_value() { return new WCNone; }

  function parse($value) { 
    // Check if user specified empty value
    if ($value === "") { return new WCNone; };

    // Check if this value is 'auto' - default value of this property
    if ($value === 'auto') {
      return new WCNone;
    };

    if ($value === 'inherit') {
      return new WCFraction(1);
    };

    if (substr($value,strlen($value)-1,1) == "%") {
      // Percentage 
      return new WCFraction(((float)$value)/100);
    } else {
      // Constant
      return new WCConstant(trim($value));
    }
  }
}

register_css_property('width', new CSSWidth);

?>