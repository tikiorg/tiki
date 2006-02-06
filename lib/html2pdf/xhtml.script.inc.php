<?php
// $Header: /cvsroot/tikiwiki/lib/html2pdf/xhtml.script.inc.php,v 1.1.1.1 2006-02-06 15:38:47 nikchankov Exp $

function process_script($sample_html) {
  return preg_replace("#<script.*?</script>#is","",$sample_html);
}

?>