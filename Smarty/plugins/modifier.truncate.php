<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     truncate
 * Purpose:  Truncate a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and 
 *           appending the $etc string.
 * -------------------------------------------------------------
 */
function smarty_modifier_truncate($string, $length = 80, $etc = '...',
                                  $break_words = false)
{
    if ($length == 0)
        return '';

    if (strlen($string) > $length) {
        $length -= strlen($etc);
        if ($break_words) // break the word
        	$fragment = mb_substr($string, 0, $length);
        else {
            $fragment = mb_substr($string, 0, $length + 1);
            $fragment = preg_replace('/\s+(\S+)?$/', '', $fragment);
        }
        return $fragment.$etc;
    } else
        return $string;
}

/* vim: set expandtab: */

?>
