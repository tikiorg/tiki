<?php
// $Header: /cvsroot/tikiwiki/lib/html2pdf/css.right.inc.php,v 1.1.1.1 2006-02-06 15:37:53 nikchankov Exp $

class CSSRight extends CSSProperty {
  function CSSRight() { $this->CSSProperty(false, false); }

  function default_value() { return null; }

  function parse($value) {
    return $value;
  }

  function ps($writer) {
    $writer->write(
                   ps_units($this->get()) . " neg 1 index put-right\n".
                   "dup get-position-dict /Right ".ps_units($this->get())." put\n"
                   );
  }

  function pdf() {
    return $this->get() === null ? null : units2pt($this->get());
  }
}

register_css_property('right', new CSSRight);

?>