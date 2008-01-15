<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/xhtml.script.inc.php,v 1.1 2008-01-15 09:21:15 mose Exp $

function process_script($sample_html) {
  return preg_replace("#<script.*?</script>#is","",$sample_html);
}

?>