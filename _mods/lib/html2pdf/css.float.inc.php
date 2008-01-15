<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/css.float.inc.php,v 1.1 2008-01-15 09:20:34 mose Exp $

define('FLOAT_NONE',0);
define('FLOAT_LEFT',1);
define('FLOAT_RIGHT',2);

class CSSFloat extends CSSProperty {
  function CSSFloat() { $this->CSSProperty(false, false); }

  function default_value() { return FLOAT_NONE; }

  function parse($value) {
    // Convert value to lower case, as html allows values 
    // in both cases to be entered
    $value = strtolower($value);
    
    if ($value === 'left') { return FLOAT_LEFT; };
    if ($value === 'right') { return FLOAT_RIGHT; };
    return FLOAT_NONE;
  }

  function value2ps($value) {
    if ($value === FLOAT_LEFT) { return "/left"; }
    if ($value === FLOAT_RIGHT) { return "/right"; }
    return "/none";
  }
}

register_css_property('float', new CSSFloat);

?>