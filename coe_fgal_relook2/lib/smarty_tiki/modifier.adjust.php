<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     adjust
 * Purpose:  Adjust a string to a certain length if necessary,
 *           optionally splitting in the middle of a word, and 
 *           appending the $etc string or padding the string
 *			 using $pad as filler.
 * -------------------------------------------------------------
 */
function smarty_modifier_adjust($string, $length = 80, 
                                  $pad = '&nbsp;', 
							      $etc = '...',
                                  $break_words = false)
{
    if ($length == 0)
        return '';

    if (strlen($string) > $length) {
        $length -= strlen($etc);
        $fragment = substr($string, 0, $length+1);
        if ($break_words)
            $fragment = substr($fragment, 0, -1);
        else
            $fragment = preg_replace('/\s+(\S+)?$/', '', $fragment);
        return $fragment.$etc;
    } elseif(strlen($string)<$length) {
        return $string.str_repeat($pad,$length-strlen($string));
    } else {
    	return $string;
    }
     
}
