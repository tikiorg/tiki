<?php 


require_once("setup.php");
require_once("lib/tikilib.php");
$tikilib = new TikiLib($dbTiki);

require_once("lib/userslib.php");
$userlib = new UsersLib($dbTiki);

$rememberme = $tikilib->get_preference('rememberme','disabled');
if($rememberme != 'disabled') {
if(isset($_COOKIE['tiki-user'])) {
  if(!isset($user) || !$user) {
    $user = $userlib->get_user_by_hash($_COOKIE['tiki-user']);
    $_SESSION['user']=$user;
  }
}
}

if(isset($_SESSION["user"])) {
  $user = $_SESSION["user"];  
  
} else {
  $user = false;
}

function tra($content)
{
  global $lang_use_db;
  if ($lang_use_db!='y') {
    global $lang;
    if ($content) {
      if(isset($lang[$content])) {
        return $lang[$content];  
      } else {
        return $content;        
      }
    }
  } else {
    global $tikilib;
    global $language;
    $query="select tran from tiki_language where source='".addslashes($content)."' and lang='".$language."'";
    $result=$tikilib->db->query($query);
    $res=$result->fetchRow(DB_FETCHMODE_ASSOC);
    if(DB::isError($result)) return $content;
    if(!isset($res["tran"])) {
      global $record_untranslated;
      if ($record_untranslated==true) {
	$query="insert into tiki_untranslated (source,lang) values('".addslashes($content)."','".$language."')";
        //No eror checking here
        $tikilib->db->query($query);
	}
      return $content;
    }
    return $res["tran"];
  }
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

if($tiki_p_admin_directory == 'y') {
  $perms = $userlib->get_permissions(0,-1,'permName_desc','','directory');
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

if($tiki_p_admin_trackers == 'y') {
  $perms = $userlib->get_permissions(0,-1,'permName_desc','','trackers');
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

$tikiIndex = $tikilib->get_preference("tikiIndex",'tiki-index.php');

$style = $tikilib->get_preference("style", 'jalist2.css');
$smarty->assign('style',$style);

$slide_style = $tikilib->get_preference("slide_style",'slidestyle.css');
$smarty->assign('slide_style',$slide_style);

$feature_userPreferences = $tikilib->get_preference("feature_userPreferences",'n');
$change_language = $tikilib->get_preference("change_language",'y');
$change_theme = $tikilib->get_preference("change_theme",'y');

$language = $tikilib->get_preference('language','en');

if($feature_userPreferences == 'y') {
  // Check for FEATURES for the user
  $user_style = $tikilib->get_preference("style", 'jalist2.css');
  if($user) {
    if($change_theme == 'y') {
      $user_style = $tikilib->get_user_preference($user,'theme',$style);
      if($user_style) {
        $style = $user_style;
      }
    }
    
    if($change_language == 'y') {
      $user_language = $tikilib->get_user_preference($user,'language',$language);
      if($user_language) {
        $language = $user_language;
      }
    }
  }
  $smarty->assign('style',$style);
  $smarty->assign('language',$language);
}

$stlstl=explode('.',$style);
$style_base = $stlstl[0];

//really needed? (todo: check)
//if ($lang_use_db!='y') {
global $lang;
include_once('lang/'.$language.'/language.php');
//}


?>
