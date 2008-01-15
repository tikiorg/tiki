<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/xhtml.comments.inc.php,v 1.1 2008-01-15 09:21:15 mose Exp $

function remove_comments(&$html) {
  $html = preg_replace("#<!--.*?-->#is","",$html);
  $html = preg_replace("#<!.*?>#is","",$html);
}

?>