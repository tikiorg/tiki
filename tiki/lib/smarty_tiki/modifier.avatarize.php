<?php

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
  if($userlib->user_exists($user)&&$tikilib->get_user_preference($user,'user_information','public')=='public') {
  	$avatar = "<a title='$user' href='tiki-user_information.php?view_user=$user'>".$avatar.'</a>';
  } 
  return $avatar;	
}

?>
