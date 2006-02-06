<?php
// $Header: /cvsroot/tikiwiki/lib/html2pdf/xhtml.comments.inc.php,v 1.1.1.1 2006-02-06 15:39:40 nikchankov Exp $

function remove_comments(&$html) {
  $html = preg_replace("#<!--.*?-->#is","",$html);
  $html = preg_replace("#<!.*?>#is","",$html);
}

?>