<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/css.background.color.inc.php,v 1.1 2008-01-15 09:20:30 mose Exp $

// 'background-color' and color part of 'background' CSS properies handler

class CSSBackgroundColor extends CSSSubProperty {
  function default_value() {
    // Transparent color
    return new Color(array(0,0,0), true);
  }
  
  // Note: we cannot use parse_color_declaration here directly, as at won't process composite 'background' values
  // containing, say, both background image url and background color; on the other side, 
  // parse_color_declaration slow down if we'll put this composite-value processing there
  function parse($value) {
    $terms = preg_split("/(?![,(\s])\s+/ ",$value);

    // Note that color declaration always will contain only one word; 
    // thus, we can split out value into words and try to parse each one as color
    // if parse_color_declaration returns transparent value, it is possible not 
    // a color part of background declaration
    foreach ($terms as $term) {
      $color = parse_color_declaration($term, array(-1,-1,-1));

      if (!is_transparent($color)) { 
        return new Color($color, false);
      };
    }

    return CSSBackgroundColor::default_value();
  }

  function get_visible_background_color() {
    $owner =& $this->owner();
    
    for ($i=0; $i<count($owner->_stack); $i++) {
      if ($owner->_stack[$i][0]->color[0] >= 0) {
        return $owner->_stack[$i][0]->color;
      };
    };
    return array(255,255,255);
  }
}

?>