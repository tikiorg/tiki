<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/utils_text.php,v 1.1 2008-01-15 09:21:14 mose Exp $

function squeeze($string) {
  return preg_replace("![ \n\t]+!"," ",trim($string));
}

?>