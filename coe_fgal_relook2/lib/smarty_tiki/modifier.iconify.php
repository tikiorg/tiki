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

/**
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     modifier
 * Name:     iconify
 * Purpose:  Returns a filetype icon if the filetype is known and there's an icon in pics/icons/mime. Returns a default file type icon in any other case
 * -------------------------------------------------------------
 */
require_once $smarty->_get_plugin_filepath('function', 'icon');

function smarty_modifier_iconify($string, $filetype = null)
{
  global $smarty;
  $ext = strtolower(substr($string, strrpos($string, '.') + 1));
  $icon = file_exists("pics/icons/mime/$ext.png") ? $ext : 'default';

  return smarty_function_icon(array(
    '_id' => 'pics/icons/mime/'.$icon.'.png',
    'alt' => ( $filetype === null ? $icon : $filetype ),
    'class' => ''
  ), $smarty);

}
