<?php
// $Header: /cvsroot/tikiwiki/tiki/lib/html2pdf/css.background.image.inc.php,v 1.1.1.1 2006-02-08 11:02:35 nikchankov Exp $

class CSSBackgroundImage extends CSSSubProperty {
  function default_value() { 
    return new BackgroundImage(null); 
  }

  function parse($value) {
    // 'url' value
    if (preg_match("/url\((.*[^\\\\]?)\)/is",$value,$matches)) {
      $url = $matches[1];

      global $g_baseurl;
      return new BackgroundImage(guess_url(css_remove_value_quotes($url), $g_baseurl));
    }

    // 'none' and unrecognzed values
    return new BackgroundImage(null);
  }
}

?>