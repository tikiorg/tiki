<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/xhtml.style.inc.php,v 1.1 2008-01-15 09:21:16 mose Exp $

function process_style(&$html) {
  // Remove HTML comment bounds inside the <style>...</style> 
  $html = preg_replace("#(<style[^>]*>)\s*<!--#is","\\1",$html); 
  $html = preg_replace("#-->\s*(</style>)#is","\\1",$html);

  // Remove CSS comments
  while (preg_match("#(<style[^>]*>.*)/\*.*?\*/.*(</style>)#is",$html)) {
    $html = preg_replace("#(<style[^>]*>.*)/\*.*\*/(.*</style>)#is","\\1\\2",$html);
  };
}

?>