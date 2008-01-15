<?php
// $Header: /cvsroot/tikiwiki/_mods/lib/html2pdf/css.background.image.inc.php,v 1.1 2008-01-15 09:20:30 mose Exp $

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