<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/css.text-decoration.inc.php,v 1.1 2008-01-15 09:20:39 mose Exp $

class CSSTextDecoration extends CSSProperty {
  function CSSTextDecoration() { $this->CSSProperty(false, true); }

  // Inherit text-decoration from inline element (so that <a><i>TEXT</i></a> constructs have TEXT underlined)
  function inherit() {
    $handler =& get_css_handler('display');
    $parent_display = $handler->get_parent();

    $this->push(is_inline_element($parent_display) ? $this->get() : $this->default_value());    
  }

  function default_value() { return array("U"=>false, "O"=>false, "T"=>false);; }

  function parse($value) {
    $parsed = $this->default_value();
    if (strstr($value,"overline")     !== false) { $parsed['O'] = true; };
    if (strstr($value,"underline")    !== false) { $parsed['U'] = true; };
    if (strstr($value,"line-through") !== false) { $parsed['T'] = true; };
    return $parsed;
  }

  function value2ps($value) {
    return 
      "<< ".
      "/underline ".($value['U'] ? "true" : "false")." ".
      "/overline ".($value['O'] ? "true" : "false")." ".
      "/line-through ".($value['T'] ? "true" : "false")." ".
      ">>";
  }
}

register_css_property("text-decoration", new CSSTextDecoration);

?>
