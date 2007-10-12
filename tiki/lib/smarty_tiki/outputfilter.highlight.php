<?php
// $Id: outputfilter.highlight.php,v 1.21 2007-10-12 07:55:47 nyloth Exp $
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
    global $prefs;

    $highlight = $_REQUEST['highlight'];
    if(isset($prefs['feature_referer_highlight']) && $prefs['feature_referer_highlight'] == 'y') {
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

    $matches = array();
    if ( strstr($source, 'id="rightcolumn"') ) {

	    if ( function_exists('mb_eregi') ) {
		    // UTF8 support enabled
		    mb_eregi('^(.*\s+id="centercolumn"[^>]*>)(.*)(<td[^>]*\s+id="rightcolumn".*)$', $source, $matches);
	    } else {
		    // This may not work at all with UTF8 chars
		    preg_match('~(.* id="centercolumn"[^>]*>)(.*)(<td[^>]* id="rightcolumn".*)~xsi', $source, $matches);
	    }

    } elseif ( function_exists('mb_eregi') ) {
    	if ( ! mb_eregi('^(.*\s+id="centercolumn"[^>]*>)(.*)$', $source, $matches) )
		return $source;
    } elseif ( ! preg_match('~(.* id="centercolumn"[^>]*>)(.*)~xsi', $source, $matches) ) {
    	return $source;
    } else {
	 $matches[3] = '';
    }

	// Avoid highlight parsing in unknown cases where $matches[2] is empty, which will result in an empty page.
	if ( $matches[2] != '' ) $source = preg_replace_callback(
	      '~(?:<head>.*</head>                          # head blocks
	      |<div[^>]*nohighlight.*</div><!--nohighlight--> # div with nohightlight
	      |<script[^>]+>.*</script>                     # script blocks
	      |<a[^>]*onmouseover.*onmouseout[^>]*>            # onmouseover (user popup)
	      |<[^>]*>                                      # all html tags
	      |(' . _enlightColor($highlight) . '))~xsiU',
	      '_enlightColor',  $matches[2]);

    return $matches[1].$source.$matches[3];
 }

function _enlightColor($matches) {
    static $colword = array();
    if (is_string($matches)) { // just to set the color array
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
            $colword[strtolower($word)] = 'highlight_word_'.$i%5;
			$i++;
        }
        return $seaword;
    }
    // actual replacement callback
    if (isset($matches[1])) {
        return '<span class= "'.$colword[strtolower($matches[1])].'">' . $matches[1] . '</span>';
    }
    return $matches[0];
}

 // helper function
 // q= for Google, p= for Yahoo
 function _refererhi() {
     $referer = parse_url($_SERVER['HTTP_REFERER']);
     if (empty($referer['query'])) {
         return '';
     }
     parse_str($referer['query'],$vars);
     if (isset($vars['q'])) {
         return $vars['q'];
     } else if (isset($vars['p'])) {
         return $vars['p'];
     }
     return '';
 }
?>
