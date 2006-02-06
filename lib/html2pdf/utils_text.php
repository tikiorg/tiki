<?php
// $Header: /cvsroot/tikiwiki/lib/html2pdf/utils_text.php,v 1.1.1.1 2006-02-06 15:39:53 nikchankov Exp $

function squeeze($string) {
  return preg_replace("![ \n\t]+!"," ",trim($string));
}

?>