<?php

function smarty_modifier_userlink($user,$class='link')
{
 global $tikilib;
 global $userlib;
 if($userlib->user_exists($user)&&$tikilib->get_user_preference($user,'user_information','public')=='public') {
   return "<a class='$class' href='tiki-user_information.php?view_user=$user'>$user</a>";
 } else {
   return "<span class='$class'>$user</span>";
 }
}

/* vim: set expandtab: */

?>
