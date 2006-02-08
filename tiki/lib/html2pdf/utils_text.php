<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/html2pdf/utils_text.php,v 1.1.1.1 2006-02-08 11:02:40 nikchankov Exp $

function squeeze($string) {
  return preg_replace("![ \n\t]+!"," ",trim($string));
}

?>