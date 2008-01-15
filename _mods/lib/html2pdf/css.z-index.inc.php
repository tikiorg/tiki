<?php

class CSSZIndex extends CSSProperty {
  function CSSZIndex() { $this->CSSProperty(false, false); }

  function default_value() { return 0; }

  function parse($value) {
    return (int)$value;
  }

  function value2ps($value) { return $value; }
}

register_css_property('z-index', new CSSZIndex);

?>