<?php
// Initialization
require_once('tiki-setup.php');

if($feature_forums != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display('error.tpl');
  die;  
}

if(!isset($_REQUEST["forumId"])) {
  $smarty->assign('msg',tra("No forum indicated"));
  $smarty->display('error.tpl');
  die;
}

if(isset($_REQUEST["openpost"])) {
  $smarty->assign('openpost','y');
} else {
  $smarty->assign('openpost','n');
}

$smarty->assign('forumId',$_REQUEST["forumId"]);
include_once("lib/commentslib.php");
$commentslib = new Comments($dbTiki);

$commentslib->forum_add_hit($_REQUEST["forumId"]);

$forum_info = $commentslib->get_forum($_REQUEST["forumId"]);

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
if($tiki_p_admin_forum != 'y' && $tiki_p_forum_read != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display('error.tpl');
    die;
}


// Now if the user is the moderator then give hime forum admin privs
if($forum_info["moderator"]==$user) {
  $tiki_p_admin_forum = 'y';
  $smarty->assign('tiki_p_admin_forum','y');
}


if($tiki_p_admin_forum == 'y') {
  $tiki_p_forum_post = 'y';
  $smarty->assign('tiki_p_forum_post','y');
  $tiki_p_forum_read = 'y';
  $smarty->assign('tiki_p_forum_read','y');
  $tiki_p_forum_vote = 'y';
  $smarty->assign('tiki_p_forum_vote','y');
  $tiki_p_forum_post_topic = 'y';
  $smarty->assign('tiki_p_forum_post_topic','y');
}



$smarty->assign_by_ref('forum_info',$forum_info);

$comments_per_page = $forum_info["topicsPerPage"];
$comments_default_ordering = $forum_info["topicOrdering"];
$comments_vars=Array('forumId');
$comments_prefix_var='forum';
$comments_object_var='forumId';

/******************************/
if(!isset($_REQUEST['comments_threshold'])) {
  $_REQUEST['comments_threshold']=0;
}
$smarty->assign('comments_threshold',$_REQUEST['comments_threshold']);

if(!isset($_REQUEST["comments_threadId"])) {
  $_REQUEST["comments_threadId"]=0;
}
$smarty->assign("comments_threadId",$_REQUEST["comments_threadId"]);

if(!isset($comments_prefix_var)) {
   $comments_prefix_var='';
}
if(!isset($comments_object_var) || (!$comments_object_var) || !isset($_REQUEST[$comments_object_var])){
   die("the comments_object_var variable is not set or cannot be found as a REQUEST variable");
}
$comments_objectId = $comments_prefix_var.$_REQUEST["$comments_object_var"];
// Process a post form here 
if($tiki_p_admin_forum == 'y' || $tiki_p_forum_post_topic == 'y') {
  if(isset($_REQUEST["comments_postComment"])) {
    if( (!empty($_REQUEST["comments_title"])) && (!empty($_REQUEST["comments_data"])) ){
      if($tiki_p_admin_forum=='y' || $commentslib->user_can_post_to_forum($user, $_REQUEST["forumId"])) {
        //Replace things between square brackets by links
        $_REQUEST["comments_data"]=strip_tags($_REQUEST["comments_data"]);
        if($tiki_p_admin_forum != 'y') {
          $_REQUEST["comment_topictype"]='n';
        }
        if($_REQUEST["comments_threadId"]==0) {
          if($forum_info["useMail"]=='y') {
            @mail($forum_info["mail"], tra('Tiki email notification'), 
            tra('New forum post to forum:').' '.$forum_info["title"]."\n"
            .tra('Author:').' '.$user."\n"
            .tra('Title:').' '.$_REQUEST["comments_title"]."\n"
            .tra('Body:').' '.$_REQUEST["comments_data"]."\n"
            .tra('Date:').' '.date("d-m-Y [h:i]"));
          }
          $commentslib->post_new_comment($comments_objectId, 0, $user, $_REQUEST["comments_title"], nl2br($_REQUEST["comments_data"]),$_REQUEST["comment_topictype"]);
          $commentslib->register_forum_post($_REQUEST["forumId"],0);
        } else {
          if($tiki_p_admin_forum == 'y') {
            $commentslib->update_comment($_REQUEST["comments_threadId"], $_REQUEST["comments_title"], nl2br($_REQUEST["comments_data"]),$_REQUEST["comment_topictype"]);
          }
        }
      } else {
        $smarty->assign('msg',tra("Please wait 2 minutes between posts"));
        $smarty->display('error.tpl');
        die;  
      }
    }
  }
}


if($tiki_p_admin_forum=='y' || $tiki_p_forum_vote == 'y') {
  // Process a vote here
  if(isset($_REQUEST["comments_vote"])&&isset($_REQUEST["comments_threadId"])) {
   $comments_show='y';
   if(!$tikilib->user_has_voted($user,'comment'.$_REQUEST["comments_threadId"])) {
    $commentslib->vote_comment($_REQUEST["comments_threadId"],$user,$_REQUEST["comments_vote"]);
    $tikilib->register_user_vote($user,'comment'.$_REQUEST["comments_threadId"]);
   }
   $_REQUEST["comments_threadId"]=0;
   $smarty->assign('comments_threadId',0);
  }
}


$smarty->assign('last_forum_visit',$_SESSION["last_forum_visit"]);

if($_REQUEST["comments_threadId"]>0) {
  $comment_info = $commentslib->get_comment($_REQUEST["comments_threadId"]);
  $smarty->assign('comment_title',$comment_info["title"]);
  $smarty->assign('comment_data',$comment_info["data"]);
  $smarty->assign('comment_topictype',$comment_info["type"]);
} else {
  $smarty->assign('comment_title','');
  $smarty->assign('comment_data','');
  $smarty->assign('comment_topictype','n');
}


if($tiki_p_admin_forum == 'y') {
  if(isset($_REQUEST["comments_remove"])&&isset($_REQUEST["comments_threadId"])) {
   $comments_show='y';
   $commentslib->remove_comment($_REQUEST["comments_threadId"]);
   $commentslib->register_remove_post($forumId, 0);
  }
}

// Check for settings
if(!isset($_REQUEST["comments_maxComments"])) {
 $_REQUEST["comments_maxComments"]=$comments_per_page;
}

if(!isset($_REQUEST["comments_sort_mode"])) {
 $_REQUEST["comments_sort_mode"]=$comments_default_ordering;
} else {
 $comments_show='y';
}

if(!isset($_REQUEST["comments_commentFind"])) {
 $_REQUEST["comments_commentFind"]='';
} else {
 $comments_show='y';
}


$smarty->assign('comments_maxComments',$_REQUEST["comments_maxComments"]);
$smarty->assign('comments_sort_mode',$_REQUEST["comments_sort_mode"]);
$smarty->assign('comments_commentFind',$_REQUEST["comments_commentFind"]);
//print("Show: $comments_show<br/>");
// Offset setting for the list of comments
if(!isset($_REQUEST["comments_offset"])) {
 $comments_offset = 0;
} else {
 $comments_offset = $_REQUEST["comments_offset"];
}

$smarty->assign('comments_offset',$comments_offset);

// Now check if we are displaying top-level comments or a specific comment
if(!isset($_REQUEST["comments_parentId"])) {
  $_REQUEST["comments_parentId"] = 0;
}
$smarty->assign('comments_parentId',$_REQUEST["comments_parentId"]);
$comments_coms = $commentslib->get_comments($comments_objectId,$_REQUEST["comments_parentId"],$comments_offset,$_REQUEST["comments_maxComments"],$_REQUEST["comments_sort_mode"], $_REQUEST["comments_commentFind"],$_REQUEST['comments_threshold']);
$comments_cant = $commentslib->count_comments($comments_objectId);
$smarty->assign('comments_below',$comments_coms["below"]);
$smarty->assign('comments_cant',$comments_cant);

$comments_maxRecords = $_REQUEST["comments_maxComments"];
$comments_cant_pages = ceil($comments_coms["cant"] / $comments_maxRecords);
$smarty->assign('comments_cant_pages',$comments_cant_pages);
$smarty->assign('comments_actual_page',1+($comments_offset/$comments_maxRecords));
if($comments_coms["cant"] > ($comments_offset+$comments_maxRecords)) {
$smarty->assign('comments_next_offset',$comments_offset + $comments_maxRecords);
} else {
  $smarty->assign('comments_next_offset',-1); 
}
// If offset is > 0 then prev_offset
if($comments_offset>0) {
  $smarty->assign('comments_prev_offset',$comments_offset - $comments_maxRecords);  
} else {
  $smarty->assign('comments_prev_offset',-1); 
}
$smarty->assign('comments_coms',$comments_coms["data"]);
/******************************/


// Display the template
$smarty->assign('mid','tiki-view_forum.tpl');
$smarty->display('tiki.tpl');
?>