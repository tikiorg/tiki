<?php
// Initialization
require_once('tiki-setup.php');

if(!isset($_REQUEST["forumId"])) {
  $_REQUEST["forumId"] = 0;
}
$smarty->assign('forumId',$_REQUEST["forumId"]);

if($feature_forums != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}


$smarty->assign('individual','n');
if($userlib->object_has_one_permission($_REQUEST["forumId"],'forum')) {
  $smarty->assign('individual','y');
  if($tiki_p_admin != 'y') {
    $perms = $userlib->get_permissions(0,-1,'permName_desc','','forums');
    foreach($perms["data"] as $perm) {
      $permName=$perm["permName"];
      if($userlib->object_has_permission($user,$_REQUEST["forumId"],'forum',$permName)) {
        $$permName = 'y';
        $smarty->assign("$permName",'y');
      } else {
        $$permName = 'n';
        $smarty->assign("$permName",'n');
      }
    }
  }
}

if($tiki_p_admin_forum != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}




include_once("lib/commentslib.php");
$commentslib = new Comments($dbTiki);

if($_REQUEST["forumId"]) {
  $info = $commentslib->get_forum($_REQUEST["forumId"]);
} else {
  $info = Array();
  $info["name"]='';
  $info["description"]='';
  $info["controlFlood"]='n';
  $info["floodInterval"]=120;
  $info["moderator"]='admin';
  $info["section"]='';
  $info["mail"]='';
  $info["topicsPerPage"]='20';
  $info["useMail"]='n';
  $info["topicOrdering"]='commentDate_desc';
  $info["threadOrdering"]='commentDate_desc';
  $info["usePruneUnreplied"]='n';
  $info["pruneUnrepliedAge"]=60*60*24*30;
  $info["usePruneOld"]='n';
  $info["pruneMaxAge"]= 60*60*24*30;
}
$smarty->assign('name',$info["name"]);
$smarty->assign('description',$info["description"]);
$smarty->assign('controlFlood',$info["controlFlood"]);
$smarty->assign('floodInterval',$info["floodInterval"]);
$smarty->assign('topicOrdering',$info["topicOrdering"]);
$smarty->assign('threadOrdering',$info["threadOrdering"]);
$smarty->assign('moderator',$info["moderator"]);
$smarty->assign('section',$info["section"]);
$smarty->assign('topicsPerPage',$info["topicsPerPage"]);
$smarty->assign('mail',$info["mail"]);
$smarty->assign('useMail',$info["useMail"]);
$smarty->assign('usePruneUnreplied',$info["usePruneUnreplied"]);
$smarty->assign('pruneUnrepliedAge',$info["pruneUnrepliedAge"]);
$smarty->assign('usePruneOld',$info["usePruneOld"]);
$smarty->assign('pruneMaxAge',$info["pruneMaxAge"]);

$users = $userlib->get_users(0,-1,'login_desc', '');
$smarty->assign_by_ref('users',$users["data"]);

if(isset($_REQUEST["remove"])) {
  $commentslib->remove_forum($_REQUEST["remove"]);
}

if(isset($_REQUEST["save"])) {
  if(isset($_REQUEST["controlFlood"])&&$_REQUEST["controlFlood"]=='on') {
    $controlFlood='y';
  } else {
    $controlFlood='n';
  }
  if(isset($_REQUEST["useMail"])&&$_REQUEST["useMail"]=='on') {
    $useMail='y';
  } else {
    $useMail='n';
  }
  if(isset($_REQUEST["usePruneUnreplied"])&&$_REQUEST["usePruneUnreplied"]=='on') {
    $usePruneUnreplied='y';
  } else {
    $usePruneUnreplied='n';
  }
  if(isset($_REQUEST["usePruneOld"])&&$_REQUEST["usePruneOld"]=='on') {
    $usePruneOld='y';
  } else {
    $usePruneOld='n';
  }

  if($_REQUEST["section"]=='__new__') $_REQUEST["section"]=$_REQUEST["new_section"];
  
  $fid = $commentslib->replace_forum($_REQUEST["forumId"], $_REQUEST["name"], $_REQUEST["description"], $controlFlood,$_REQUEST["floodInterval"],$_REQUEST["moderator"], $_REQUEST["mail"], $useMail, $usePruneUnreplied, $_REQUEST["pruneUnrepliedAge"], $usePruneOld, $_REQUEST["pruneMaxAge"], $_REQUEST["topicsPerPage"], $_REQUEST["topicOrdering"], $_REQUEST["threadOrdering"], $_REQUEST["section"]);                         
  
  $cat_type='forum';
  $cat_objid = $fid;
  $cat_desc = substr($_REQUEST["description"],0,200);
  $cat_name = $_REQUEST["name"];
  $cat_href="tiki-view_forum.php?forumId=".$cat_objid;
  include_once("categorize.php");
  
  $info["name"]='';
  $info["description"]='';
  $info["controlFlood"]='n';
  $info["floodInterval"]=120;
  $info["moderator"]='admin';
  $info["topicOrdering"]='commentDate_desc';
  $info["threadOrdering"]='commentDate_desc';
  $info["mail"]='';
  $info["topicsPerPage"]='20';
  $info["useMail"]='n';
  $info["usePruneUnreplied"]='n';
  $info["pruneUnrepliedAge"]=60*60*24*30;
  $info["usePruneOld"]='n';
  $info["pruneMaxAge"]= 60*60*24*30;
  $info["forumId"] = 0;
  $smarty->assign('forumId',$info["forumId"]);
  $smarty->assign('name',$info["name"]);
  $smarty->assign('description',$info["description"]);
  $smarty->assign('controlFlood',$info["controlFlood"]);
  $smarty->assign('floodInterval',$info["floodInterval"]);
  $smarty->assign('moderator',$info["moderator"]);
  $smarty->assign('topicsPerPage',$info["topicsPerPage"]);
  $smarty->assign('mail',$info["mail"]);
  $smarty->assign('useMail',$info["useMail"]);
  $smarty->assign('usePruneUnreplied',$info["usePruneUnreplied"]);
  $smarty->assign('pruneUnrepliedAge',$info["pruneUnrepliedAge"]);
  $smarty->assign('usePruneOld',$info["usePruneOld"]);
  $smarty->assign('topicOrdering',$info["topicOrdering"]);
  $smarty->assign('threadOrdering',$info["threadOrdering"]);
  $smarty->assign('pruneMaxAge',$info["pruneMaxAge"]);

}

if(!isset($_REQUEST["sort_mode"])) {
  $sort_mode = 'name_desc'; 
} else {
  $sort_mode = $_REQUEST["sort_mode"];
} 

if(!isset($_REQUEST["offset"])) {
  $offset = 0;
} else {
  $offset = $_REQUEST["offset"]; 
}
$smarty->assign_by_ref('offset',$offset);

if(isset($_REQUEST["find"])) {
  $find = $_REQUEST["find"];  
} else {
  $find = ''; 
}
$smarty->assign('find',$find);

$smarty->assign_by_ref('sort_mode',$sort_mode);
$channels = $commentslib->list_forums($offset,$maxRecords,$sort_mode,$find);
for($i=0;$i<count($channels["data"]);$i++) {
  if($userlib->object_has_one_permission($channels["data"][$i]["forumId"],'forum')) {
    $channels["data"][$i]["individual"]='y';
    
    if($userlib->object_has_permission($user,$channels["data"][$i]["forumId"],'forum','tiki_p_forum_read')) {
      $channels["data"][$i]["individual_tiki_p_forum_read"]='y';
    } else {
      $channels["data"][$i]["individual_tiki_p_forum_read"]='n';
    }
    if($userlib->object_has_permission($user,$channels["data"][$i]["forumId"],'forum','tiki_p_forum_post')) {
      $channels["data"][$i]["individual_tiki_p_forum_post"]='y';
    } else {
      $channels["data"][$i]["individual_tiki_p_forum_post"]='n';
    }
    if($userlib->object_has_permission($user,$channels["data"][$i]["forumId"],'forum','tiki_p_forum_vote')) {
      $channels["data"][$i]["individual_tiki_p_forum_vote"]='y';
    } else {
      $channels["data"][$i]["individual_tiki_p_forum_vote"]='n';
    }
    if($userlib->object_has_permission($user,$channels["data"][$i]["forumId"],'forum','tiki_p_forum_post_topic')) {
      $channels["data"][$i]["individual_tiki_p_forum_post_topic"]='y';
    } else {
      $channels["data"][$i]["individual_tiki_p_forum_post_topic"]='n';
    }
    if($tiki_p_admin=='y' || $userlib->object_has_permission($user,$channels["data"][$i]["forumId"],'forum','tiki_p_admin_forum')) {
      $channels["data"][$i]["individual_tiki_p_forum_post_topic"]='y';
      $channels["data"][$i]["individual_tiki_p_forum_vote"]='y';
      $channels["data"][$i]["individual_tiki_p_admin_forum"]='y';
      $channels["data"][$i]["individual_tiki_p_forum_post"]='y';
      $channels["data"][$i]["individual_tiki_p_forum_read"]='y';
    } 
    
  } else {
    $channels["data"][$i]["individual"]='n';
  }
}


$cant_pages = ceil($channels["cant"] / $maxRecords);
$smarty->assign_by_ref('cant_pages',$cant_pages);
$smarty->assign('actual_page',1+($offset/$maxRecords));
if($channels["cant"] > ($offset+$maxRecords)) {
  $smarty->assign('next_offset',$offset + $maxRecords);
} else {
  $smarty->assign('next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($offset>0) {
  $smarty->assign('prev_offset',$offset - $maxRecords);  
} else {
  $smarty->assign('prev_offset',-1); 
}

$smarty->assign_by_ref('channels',$channels["data"]);

$cat_type='forum';
$cat_objid = $_REQUEST["forumId"];
include_once("categorize_list.php");

$sections = $tikilib->get_forum_sections();
$smarty->assign_by_ref('sections',$sections);

// Display the template
$smarty->assign('mid','tiki-admin_forums.tpl');
$smarty->display('tiki.tpl');
?>