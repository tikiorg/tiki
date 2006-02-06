<?php
// $Header: /cvsroot/tikiwiki/lib/html2pdf/css.pseudo.listcounter.inc.php,v 1.1.1.1 2006-02-06 15:38:26 nikchankov Exp $

class CSSPseudoListCounter extends CSSProperty {
  function CSSPseudoListCounter() { $this->CSSProperty(true, false); }
  function default_value() { return 1; }
}

register_css_property('-list-counter', new CSSPseudoListCounter);

?>