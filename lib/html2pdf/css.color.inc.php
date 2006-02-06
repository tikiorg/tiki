<?php
// $Header: /cvsroot/tikiwiki/lib/html2pdf/css.color.inc.php,v 1.1.1.1 2006-02-06 15:38:39 nikchankov Exp $

class CSSColor extends CSSProperty {
  function CSSColor() { $this->CSSProperty(true, true); }

  function default_value() { return new Color(array(0,0,0),false); }

  function parse($value) {
    $color = parse_color_declaration($value, $this->get());
    return new Color($color, is_transparent($color));
  }

  function value2ps($value) {
    return 
      $value[0]/255 . " " . 
      $value[1]/255 . " " . 
      $value[2]/255 . " " .
      (is_transparent($value) ? "0" : "1").
      " color-create";
  }
}

register_css_property('color', new CSSColor);

?>