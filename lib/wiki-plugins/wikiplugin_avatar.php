<?php

// Displays the user Avatar
// Use:
// {AVATAR()}username{AVATAR}
// {AVATAR(page=>some)}username{AVATAR}
// If no avatar nothing is displayed

function wikiplugin_avatar_help() {
  return "Displays the user Avatar";
}

function wikiplugin_avatar($data,$params) {
  global $tikilib;
  global $userlib;
  
  extract($params);
  $avatar=$tikilib->get_user_avatar($data);

  if(isset($page)) {
      $avatar = "<a href='tiki-index.php?page=$page'>".$avatar.'</a>';
    } else {
      if($userlib->user_exists($data)&&$tikilib->get_user_preference($data,'user_information','public')=='public') {
      	$avatar = "<a href='tiki-user_information.php?view_user=$data'>".$avatar.'</a>';
      } 
  }
  
  return $avatar;
}


?>
