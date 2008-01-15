<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/css.border.collapse.inc.php,v 1.1 2008-01-15 09:20:30 mose Exp $

define('BORDER_COLLAPSE', 1);
define('BORDER_SEPARATE', 2);

class CSSBorderCollapse extends CSSProperty {
  function CSSBorderCollapse() { $this->CSSProperty(true, true); }

  function default_value() { return BORDER_SEPARATE; }

  function parse($value) {
    if ($value === 'collapse') { return BORDER_COLLAPSE; };
    if ($value === 'separate') { return BORDER_SEPARATE; };
    return $this->default_value();
  }

  function value2ps($value) { 
    // Do nothing
  }
}

register_css_property('border-collapse', new CSSBorderCollapse);

?>