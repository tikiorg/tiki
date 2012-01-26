<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
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
 * Name:     kbsize
 * Purpose:  returns size in Mb, Kb or bytes.
 * -------------------------------------------------------------
 */
function smarty_modifier_kbsize($string, $bytes = false, $nb_decimals = 2)
{
  if ( $string == '' ) return '';

  // 1024 x 1024 x 1024 x 1024 = 1099511627776
  if ( $string > 1099511627776 ) { 
   $string = number_format($string/1099511627776,$nb_decimals);
   $kb_string = 'T';
  } 
  // 1024 x 1024 x 1024 = 1073741824
  elseif ( $string > 1073741824 ) { 
   $string = number_format($string/1073741824,$nb_decimals);
   $kb_string = 'G';
  } 
  // 1024 x 1024 = 1048576
  elseif ( $string > 1048576 ) { 
   $string = number_format($string/1048576,$nb_decimals);
   $kb_string = 'M';
  } 
  elseif ( $string > 1024 ) { 
   $string = number_format($string/1024,$nb_decimals);
   $kb_string = 'K';
  } 
  else { 
   $string = $string;
   $kb_string = '';
  }; 
  
  $kb_string = $kb_string.(($bytes) ? 'B' : 'b');

  return $string.'&nbsp;'.tra($kb_string);
}
