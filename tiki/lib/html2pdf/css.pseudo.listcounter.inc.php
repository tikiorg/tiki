<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/html2pdf/css.pseudo.listcounter.inc.php,v 1.1.1.1 2006-02-08 11:02:07 nikchankov Exp $

class CSSPseudoListCounter extends CSSProperty {
  function CSSPseudoListCounter() { $this->CSSProperty(true, false); }
  function default_value() { return 1; }
}

register_css_property('-list-counter', new CSSPseudoListCounter);

?>