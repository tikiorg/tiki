<?php
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
 * -------------------------------------------------------------
 */
 function smarty_outputfilter_highlight($source, &$smarty) {
 	
    $highlight = $_REQUEST['highlight']; 
    $words = $highlight;
    if (!isset($highlight) || empty($highlight)) {
			return $source;
    }

    // Pull out the script blocks
    preg_match_all("!<script[^>]+>.*?</script>!is", $source, $match);
    $_script_blocks = $match[0];
    $source = preg_replace("!<script[^>]+>.*?</script>!is",
    '@@@SMARTY:TRIM:SCRIPT@@@', $source);

    // pull out all html tags
    preg_match_all("'<[\/\!]*?[^<>]*?>'si", $source, $match);
    $_tag_blocks = $match[0];
    $source = preg_replace("'<[\/\!]*?[^<>]*?>'si", '@@@SMARTY:TRIM:TAG@@@', $source);

    // This array is used to choose colors for supplied highlight terms
    $colorArr = array('#ffff66','#ff9999','#A0FFFF','#ff66ff','#99ff99');

    // Wrap all the highlight words with tags bolding them and changing
    // their background colors
    $wordArr = split(" ",addslashes($words));
    $i = 0;
    foreach($wordArr as $word) {
			$source = preg_replace("'($word)'si", '<span style="color:black;background-color:'.$colorArr[$i].';">$1</span>', $source); 
			$i++;
    }

    // replace script blocks
    foreach($_script_blocks as $curr_block) {
			$source = preg_replace("!@@@SMARTY:TRIM:SCRIPT@@@!",$curr_block,$source,1);
    }

    foreach($_tag_blocks as $curr_block) {
			$source = preg_replace("!@@@SMARTY:TRIM:TAG@@@!",$curr_block,$source,1);
    }

    return $source;
 }
?>
