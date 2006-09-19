<?php
// $Id: outputfilter.highlight.php,v 1.12 2006-09-19 16:33:23 ohertel Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     outputfilter.highlight.php
 * Type:     outputfilter
 * Name:     highlight
 * Version:  1.1
 * Date:     Sep 18, 2003
 * Version:  1.0
 * Date:     Aug 10, 2003
 * Purpose:  Adds Google-cache-like highlighting for terms in a
 *           template after its rendered. This can be used
 *           easily integrated with the wiki search functionality
 *           to provide highlighted search terms.
 * Install:  Drop into the plugin directory, call
 *           $smarty->load_filter('output','highlight');
 *           from application.
 * Author:   Greg Hinkle <ghinkl@users.sourceforge.net>
 *           patched by mose <mose@feu.org>
 *           Referer parsing by mdavey
 * -------------------------------------------------------------
 */
 function smarty_outputfilter_highlight($source, &$smarty) {
    global $feature_referer_highlight;

    $highlight = $_REQUEST['highlight'];
    if(isset($feature_referer_highlight) && $feature_referer_highlight == 'y') {
        $refererhi = _refererhi();
        if(isset($refererhi) && !empty($refererhi)) {
            if(isset($highlight) && !empty($highlight)) {
                $highlight = $highlight." ".$refererhi;
            } else {
                $highlight = $refererhi;
            }
        }
    }
    if (!isset($highlight) || empty($highlight)) {
                        return $source;
    }

   $source = preg_replace_callback(
      '~(?:<head>.*</head>                          # head blocks
      |<div[^>]*nohighlight.*</div>\{\*nohighlight  # div with nohightlight
      |<script[^>]+>.*</script>                     # script blocks
      |<a[^>]*onmouseover.*onmouseout[^>]*>            # onmouseover (user popup)
      |<[^>]*>                                      # all html tags
      |(' . _enlightColor($highlight) . '))~xsiU',
      '_enlightColor',  $source);

    return $source;
 }

function _enlightColor($matches) {
    static $colword = array();
    if (is_string($matches)) { // just to set the color array
        // This array is used to choose colors for supplied highlight terms
        $colorArr = array('#ffff66','#ff9999','#A0FFFF','#ff66ff','#99ff99');

        // Wrap all the highlight words with tags bolding them and changing
        // their background colors
        $i = 0;
        $seaword = $seasep = '';
        $wordArr = preg_split('~%20|\+|\s+~', $matches);
        foreach($wordArr as $word) {
		if ($word == '')
			continue;
            $seaword .= $seasep.preg_quote($word, '~');
            $seasep ='|';
            $colword[strtolower($word)] = $colorArr[$i%5];
			$i++;
        }
        return $seaword;
    }
    // actual replacement callback
    if (isset($matches[1])) {
        return '<span style="color:black;background-color:' . $colword[strtolower($matches[1])] . ';">' . $matches[1] . '</span>';
    }
    return $matches[0];
}

 // helper function
 // q= for Google, p= for Yahoo
 function _refererhi() {
     $referer = parse_url($_SERVER['HTTP_REFERER']);
     parse_str($referer['query'],$vars);
     if (isset($vars['q'])) {
         return $vars['q'];
     } else if (isset($vars['p'])) {
         return $vars['p'];
     }
 }
?>
