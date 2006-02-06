<?php
// $Header: /cvsroot/tikiwiki/lib/html2pdf/css.left.inc.php,v 1.1.1.1 2006-02-06 15:38:38 nikchankov Exp $

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