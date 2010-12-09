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
/**
 * \brief Smarty modifier plugin to add user's country flag
 *
 * - type:     modifier
 * - name:     countryflag
 * - purpose:  Returns a specified user's country flag
 *
 * @author   
 * @param string
 * @return string
 *
 * Example: {$userinfo.login|countryflag}
 */

function smarty_modifier_countryflag($user)
{
  global $tikilib;
  $flag = $tikilib->get_user_preference($user,'country','Other');
  if ($flag == 'Other' || empty($flag))
      return '';
  return "<img alt='".tra(str_replace('_',' ',$flag))."' src='img/flags/".$flag.".gif' title='".tra(str_replace('_',' ',$flag))."' />";
}
