<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/html2pdf/xhtml.script.inc.php,v 1.1.1.1 2006-02-08 11:02:15 nikchankov Exp $

function process_script($sample_html) {
  return preg_replace("#<script.*?</script>#is","",$sample_html);
}

?>