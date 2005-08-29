<?php
// $Id: outputfilter.highlight.php,v 1.9 2005-08-29 03:14:44 mose Exp $
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

   // Pull out the head block
   preg_match_all("!<head>.*?</head>!is", $source, $match);
   $_head_blocks = $match[0];
   $source = preg_replace("!<head>.*?</head>!is", '@@@==:==@@@', $source);

   // Pull out the div with nohightlight
   preg_match_all("!<div[^>]*nohighlight.*?</div>\{\*nohighlight!is", $source, $match);
   $_div_blocks = $match[0];
   $source = preg_replace("!<div[^>]*nohighlight.*?</div>\{\*nohighlight!is", '@@@=:=@@@', $source);

    // Pull out the script blocks
    preg_match_all("!<script[^>]+>.*?</script>!is", $source, $match);
    $_script_blocks = $match[0];
    $source = preg_replace("!<script[^>]+>.*?</script>!is", '@@@=====@@@', $source);

    //pull out the onmouseover (for the user popup)
    preg_match_all('!onmouseover=("[^"]*"|\'[^\']*\')!is', $source, $match);
    $_mouse_blocks = $match[0];
    $source = preg_replace('!onmouseover=("[^"]*"|\'[^\']*\')!is', '@@@ONMOUSEOVER@@@', $source);

    // pull out all html tags
    preg_match_all("'<[\/\!]*?[^<>]*?>'si", $source, $match);
    $_tag_blocks = $match[0];
    $source = preg_replace("'<[\/\!]*?[^<>]*?>'si", '@@@:=====:@@@', $source);

    // This array is used to choose colors for supplied highlight terms
    $colorArr = array('#ffff66','#ff9999','#A0FFFF','#ff66ff','#99ff99');

    // Wrap all the highlight words with tags bolding them and changing
    // their background colors
    $i = 0;
    $wordArr = split('(%20)|[\+ ]',$highlight); // htmlentities is safe but it would be better to do strip_tags() only the performance hit is too great -> htmlentities is not safe with accent
    foreach($wordArr as $word) {
			$word = preg_quote($word, '~');
			$source = preg_replace('~('.$word.')~si', '<span style=\\"color:black;background-color:'.$colorArr[$i].';">$1</span>', $source);
			$i++;
    }

    foreach($_tag_blocks as $curr_block) {
			$source = preg_replace("!@@@:=====:@@@!",$curr_block,$source,1);
    }

    foreach($_mouse_blocks as $curr_block) {
			$source = preg_replace("!@@@ONMOUSEOVER@@@!",$curr_block,$source,1);
    }

    foreach($_script_blocks as $curr_block) {
			$source = preg_replace("!@@@=====@@@!",$curr_block,$source,1);
    }


    foreach($_div_blocks as $curr_block) {
    			$source = preg_replace("!@@@=:=@@@!",$curr_block,$source,1);
   }

    foreach($_head_blocks as $curr_block) {
    			$source = preg_replace("!@@@==:==@@@!",$curr_block,$source,1);
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
