<?php

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

    // Pull out the script blocks
    preg_match_all("!<script[^>]+>.*?</script>!is", $source, $match);
    $_script_blocks = $match[0];
    $source = preg_replace("!<script[^>]+>.*?</script>!is", '@@@=====@@@', $source);

    // pull out all html tags
    preg_match_all("'<[\/\!]*?[^<>]*?>'si", $source, $match);
    $_tag_blocks = $match[0];
    $source = preg_replace("'<[\/\!]*?[^<>]*?>'si", '@@@:=====:@@@', $source);

    // This array is used to choose colors for supplied highlight terms
    $colorArr = array('#ffff66','#ff9999','#A0FFFF','#ff66ff','#99ff99');

    // Wrap all the highlight words with tags bolding them and changing
    // their background colors
    $i = 0;
    $wordArr = split('(%20)|[\+ ]',(htmlentities($highlight))); // htmlentities is safe but it would be better to do strip_tags() only the performance hit is too great
    foreach($wordArr as $word) {
			$word = preg_quote($word);
			$source = preg_replace('~('.$word.')~si', '<span style="color:black;background-color:'.$colorArr[$i].';">$1</span>', $source); 
			$i++;
    }

    // replace script blocks
    foreach($_script_blocks as $curr_block) {
			$source = preg_replace("!@@@=====@@@!",$curr_block,$source,1);
    }

    foreach($_tag_blocks as $curr_block) {
			$source = preg_replace("!@@@:=====:@@@!",$curr_block,$source,1);
    }

    return $source;
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
