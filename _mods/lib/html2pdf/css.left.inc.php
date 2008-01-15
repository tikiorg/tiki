<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/css.left.inc.php,v 1.1 2008-01-15 09:20:34 mose Exp $

class CSSLeft extends CSSProperty {
  function CSSLeft() { $this->CSSProperty(false, false); }

  function default_value() { return null; }

  function parse($value) {
    return units2pt($value);
  }

  function value2ps($value) {
    return $value;
  }
}

register_css_property('left', new CSSLeft);

?>