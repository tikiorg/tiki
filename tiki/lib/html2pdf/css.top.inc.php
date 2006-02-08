<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/html2pdf/css.top.inc.php,v 1.1.1.1 2006-02-08 11:02:31 nikchankov Exp $

// Format of 'top' value:
// array( float, is_percentage )

class CSSTop extends CSSProperty {
  function CSSTop() { $this->CSSProperty(false, false); }

  function default_value() { return null; }

  function parse($value) {
    $value = trim($value);

    // Check if current value is percentage
    if (substr($value, strlen($value)-1, 1) === "%") {
      return array((float)$value, true);
    } else {
      return array(units2pt($value), false);
    }
  }

  function value2ps($value) {
    return "<< /value ".$value[0]." /percentage ".($value[1] ? "true" : "false")." >>";
  }
}

register_css_property('top', new CSSTop);

?>