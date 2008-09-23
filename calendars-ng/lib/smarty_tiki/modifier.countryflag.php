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
function smarty_modifier_countryflag($user)
{
  global $tikilib;
  $flag = $tikilib->get_user_preference($user,'country','Other');
  if ($flag == 'Other' || empty($flag))
      return '';
  return "<img alt='".tra($flag)."' src='img/flags/".$flag.".gif' />";
}

?>
