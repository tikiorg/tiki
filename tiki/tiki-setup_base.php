<?
require_once("setup.php");
require_once("lib/tikilib.php");
$tikilib = new TikiLib($dbTiki);

require_once("lib/userslib.php");
$userlib = new UsersLib($dbTiki);

if(isset($_SESSION["user"])) {
  $user = $_SESSION["user"];  
} else {
  $user = false;
}


// function user_has_permission($user,$perm) 
$allperms = $userlib->get_permissions(0,-1,'permName_desc','','');
$allperms = $allperms["data"];
foreach($allperms as $vperm) {
  $perm=$vperm["permName"];
  if($user != 'admin' && (!$user || !$userlib->user_has_permission($user,'tiki_p_admin'))) {
    $$perm = 'n';
    $smarty->assign("$perm",'n');  
  } else {
    $$perm = 'y';
    $smarty->assign("$perm",'y');    
  }
}


// Permissions
// Get group permissions here
$perms = $userlib->get_user_permissions($user);
foreach($perms as $perm) {
  //print("Asignando permiso global : $perm<br/>");
  $smarty->assign("$perm",'y');  
  $$perm='y';
}

// If the user can admin file galleries then assign all the file galleries permissions
if($tiki_p_admin_file_galleries == 'y') {
  $perms = $userlib->get_permissions(0,-1,'permName_desc','','file galleries');
  foreach($perms["data"] as $perm) {
    $perm=$perm["permName"];
    $smarty->assign("$perm",'y');  
    $$perm='y';
  }
}
 
if($tiki_p_blog_admin == 'y') {
  $perms = $userlib->get_permissions(0,-1,'permName_desc','','blogs');
  foreach($perms["data"] as $perm) {
    $perm=$perm["permName"];
    $smarty->assign("$perm",'y');  
    $$perm='y';
  }
} 

if($tiki_p_admin_galleries == 'y') {
  $perms = $userlib->get_permissions(0,-1,'permName_desc','','image galleries');
  foreach($perms["data"] as $perm) {
    $perm=$perm["permName"];
    $smarty->assign("$perm",'y');  
    $$perm='y';
  }
}

if($tiki_p_admin_forum == 'y') {
  $perms = $userlib->get_permissions(0,-1,'permName_desc','','forums');
  foreach($perms["data"] as $perm) {
    $perm=$perm["permName"];
    $smarty->assign("$perm",'y');  
    $$perm='y';
  }
}

if($tiki_p_admin_wiki == 'y') {
  $perms = $userlib->get_permissions(0,-1,'permName_desc','','wiki');
  foreach($perms["data"] as $perm) {
    $perm=$perm["permName"];
    $smarty->assign("$perm",'y');  
    $$perm='y';
  }
}

if($tiki_p_admin_faqs == 'y') {
  $perms = $userlib->get_permissions(0,-1,'permName_desc','','faqs');
  foreach($perms["data"] as $perm) {
    $perm=$perm["permName"];
    $smarty->assign("$perm",'y');  
    $$perm='y';
  }
}

if($tiki_p_admin_shoutbox == 'y') {
  $perms = $userlib->get_permissions(0,-1,'permName_desc','','shoutbox');
  foreach($perms["data"] as $perm) {
    $perm=$perm["permName"];
    $smarty->assign("$perm",'y');  
    $$perm='y';
  }
}


if($tiki_p_admin_quizzes == 'y') {
  $perms = $userlib->get_permissions(0,-1,'permName_desc','','quizzes');
  foreach($perms["data"] as $perm) {
    $perm=$perm["permName"];
    $smarty->assign("$perm",'y');  
    $$perm='y';
  }
}



if($tiki_p_admin_cms == 'y') {
  $perms = $userlib->get_permissions(0,-1,'permName_desc','','cms');
  foreach($perms["data"] as $perm) {
    $perm=$perm["permName"];
    $smarty->assign("$perm",'y');  
    $$perm='y';
  }
  $perms = $userlib->get_permissions(0,-1,'permName_desc','','topics');
  foreach($perms["data"] as $perm) {
    $perm=$perm["permName"];
    $smarty->assign("$perm",'y');  
    $$perm='y';
  }
}


?>