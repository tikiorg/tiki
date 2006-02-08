<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/html2pdf/xhtml.comments.inc.php,v 1.1.1.1 2006-02-08 11:02:34 nikchankov Exp $

function remove_comments(&$html) {
  $html = preg_replace("#<!--.*?-->#is","",$html);
  $html = preg_replace("#<!.*?>#is","",$html);
}

?>