<?php

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
  $avatar = $tikilib->get_user_avatar($user);
  return $avatar;	
}

?>
