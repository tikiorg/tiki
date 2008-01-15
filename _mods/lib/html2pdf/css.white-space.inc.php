<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/css.white-space.inc.php,v 1.1 2008-01-15 09:20:49 mose Exp $

define('WHITESPACE_NORMAL',0);
define('WHITESPACE_PRE',1);
define('WHITESPACE_NOWRAP',2);

class CSSWhiteSpace extends CSSProperty {
  function CSSWhiteSpace() { $this->CSSProperty(true, true); }

  function default_value() { return WHITESPACE_NORMAL; }

  function parse($value) {
    switch ($value) {
    case "normal": 
      return WHITESPACE_NORMAL;
    case "pre":
      return WHITESPACE_PRE;
    case "nowrap":
      return WHITESPACE_NOWRAP;
    default:
      return WHITESPACE_NORMAL;
    }
  }      

  function value2ps($value) {
    switch ($value) {
    case WHITESPACE_NORMAL:
      return '/normal';
    case WHITESPACE_PRE:
      return "/pre";
    case WHITESPACE_NOWRAP:
      return "/nowrap";
    default:
      return "/normal";
    }
  }
}

register_css_property('white-space', new CSSWhiteSpace);
  
?>