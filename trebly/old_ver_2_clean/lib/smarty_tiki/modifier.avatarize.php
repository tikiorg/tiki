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
 * Name:     capitalize
 * Purpose:  capitalize words in the string
 * -------------------------------------------------------------
 */
function smarty_modifier_avatarize($user)
{
  global $tikilib;
  global $userlib;
  $avatar = $tikilib->get_user_avatar($user);
  if ( $avatar != '' && $tikilib->get_user_preference($user, 'user_information', 'public') == 'public' ) {
	$id = $userlib->get_user_id($user);
  	$avatar = "<a title=\"$user\" href=\"tiki-user_information.php?userId=$id\">".$avatar.'</a>';
  } 
  return $avatar;	
}
