<?php

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
  return "<img alt='flag' src='img/flags/".$flag.".gif' />";
}

?>
