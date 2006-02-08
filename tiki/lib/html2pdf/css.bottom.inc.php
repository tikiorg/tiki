<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/html2pdf/css.bottom.inc.php,v 1.1.1.1 2006-02-08 11:02:13 nikchankov Exp $

class CSSBottom extends CSSProperty {
  function CSSBottom() { $this->CSSProperty(false, false); }
  function default_value() { return null; }
  function parse($value) { return $value; }
}

register_css_property('bottom', new CSSBottom);

?>