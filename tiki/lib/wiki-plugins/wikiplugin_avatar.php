<?php

// Displays the user Avatar
// Use:
// {AVATAR()}username{AVATAR}
// If no avatar nothing is displayed

function wikiplugin_avatar($data,$params) {
  global $tikilib;
  extract($params);
  $avatar=$tikilib->get_user_avatar($data);
  return $avatar;
}


?>
