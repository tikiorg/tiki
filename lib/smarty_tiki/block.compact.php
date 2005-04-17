<?php
/*
 * $Header: /cvsroot/tikiwiki/tiki/lib/smarty_tiki/block.compact.php,v 1.1 2005-04-17 13:46:43 zaufi Exp $
 *
 * Smarty plugin to make result HTML code smaller
 * In opposite to {strip} this plugin can be used ONCE at top level template
 * to strip all HTML at once... And it have no nasty BUG which is incorrectly
 * join some words together...
 */

function smarty_block_compact($params, $content, &$smarty)
{
    // Tags with uncompactable content...
    $nct = array('textarea', 'pre');
    // Replace uncompactable content with unique marks
    $ncc = array();
    $num = 0;
    foreach ($nct as $tag)
    {
        if (preg_match('/<\s*'.$tag.'.*>(.*)<\/\s*'.$tag.'\s*>/Usi', $content, $ucb) != 0)
        {
            $mark = md5($ucb[1].$num++.microtime());
            $ncc[$mark] = $ucb[1];
            $content = str_replace($ucb[1], $mark, $content);
        }
    }
    // Compact the text
    $content = str_replace('> <', '><', preg_replace('/\s+/', ' ', $content));
    // Insert back all saved tags content
    $ncc = array_reverse($ncc);
    foreach ($ncc as $mark => $text)
        $content = str_replace($mark, $text, $content);
    //
    return $content;
}

?>
