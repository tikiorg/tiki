<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/css.overflow.inc.php,v 1.1 2008-01-15 09:20:36 mose Exp $

define('OVERFLOW_VISIBLE',0);
define('OVERFLOW_HIDDEN',1);

class CSSOverflow extends CSSProperty {
  function CSSOverflow() { $this->CSSProperty(false, false); }

  function default_value() { return OVERFLOW_VISIBLE; }

  function parse($value) {
    switch ($value) {
    case 'hidden':
    case 'scroll':
    case 'auto':
      return OVERFLOW_HIDDEN;
    case 'visible':
    default:
      return OVERFLOW_VISIBLE;
    };
  }

  function value2ps($value) {
    if ($value == OVERFLOW_VISIBLE) { return "/visible"; };
    return "/hidden";
  }

  function pdf() {
    return $this->get();
  }
}

register_css_property('overflow', new CSSOverflow);

?>
