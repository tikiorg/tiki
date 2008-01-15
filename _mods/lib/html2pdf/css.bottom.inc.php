<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/css.bottom.inc.php,v 1.1 2008-01-15 09:20:33 mose Exp $

class CSSBottom extends CSSProperty {
  function CSSBottom() { $this->CSSProperty(false, false); }
  function default_value() { return null; }
  function parse($value) { return $value; }
}

register_css_property('bottom', new CSSBottom);

?>