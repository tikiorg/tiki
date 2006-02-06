<?php
// $Header: /cvsroot/tikiwiki/lib/html2pdf/css.bottom.inc.php,v 1.1.1.1 2006-02-06 15:38:41 nikchankov Exp $

class CSSBottom extends CSSProperty {
  function CSSBottom() { $this->CSSProperty(false, false); }
  function default_value() { return null; }
  function parse($value) { return $value; }
}

register_css_property('bottom', new CSSBottom);

?>