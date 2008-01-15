<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/css.pseudo.listcounter.inc.php,v 1.1 2008-01-15 09:20:38 mose Exp $

class CSSPseudoListCounter extends CSSProperty {
  function CSSPseudoListCounter() { $this->CSSProperty(true, false); }
  function default_value() { return 1; }
}

register_css_property('-list-counter', new CSSPseudoListCounter);

?>