<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/css.list-style.inc.php,v 1.1 2008-01-15 09:20:35 mose Exp $

class ListStyleValue {
  var $image;
  var $position;
  var $type;

  function is_default() {
    return 
      $this->image    == CSSListStyleImage::default_value() &&
      $this->position == CSSListStylePosition::default_value() &&
      $this->type     == CSSListStyleType::default_value();
  }

  function to_ps() {
    return 
      "<< ".
      "/position ".CSSListStylePosition::value2ps($this->position)." ".
      "/type ".CSSListStyleType::value2ps($this->type)." ".
      ">>";
  }
}

class CSSListStyle extends CSSProperty {
  // CSS 2.1: list-style is inherited
  function CSSListStyle() { 
    $this->default_value = new ListStyleValue;
    $this->default_value->image    = CSSListStyleImage::default_value();
    $this->default_value->position = CSSListStylePosition::default_value();
    $this->default_value->type     = CSSListStyleType::default_value();

    $this->CSSProperty(true, true); 
  }

  function parse($value) { 
    $style = new ListStyleValue;
    $style->image     = CSSListStyleImage::parse($value);
    $style->position  = CSSListStylePosition::parse($value);
    $style->type      = CSSListStyleType::parse($value);

    return $style;
  }

  function default_value() { return $this->default_value; }
}

$ls = new CSSListStyle;
register_css_property('list-style', $ls);
register_css_property('list-style-image',    new CSSListStyleImage($ls,    'image'));
register_css_property('list-style-position', new CSSListStylePosition($ls, 'position'));
register_css_property('list-style-type',     new CSSListStyleType($ls,     'type'));

?>