<?php
// Initialization
require_once('tiki-setup.php');

if($feature_forums != 'y') {
  $smarty->assign('msg',tra("This feature is disabled"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

if(!isset($_REQUEST["forumId"])) {
  $smarty->assign('msg',tra("No forum indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}
if(!isset($_REQUEST["comments_parentId"])) {
  $smarty->assign('msg',tra("No thread indicated"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
}
$comments_parentId=$_REQUEST["comments_parentId"];

if(isset($_REQUEST["openpost"])) {
  $smarty->assign('openpost','y');
} else {
  $smarty->assign('openpost','n');
}


$smarty->assign('comments_parentId',$_REQUEST["comments_parentId"]);

$smarty->assign('forumId',$_REQUEST["forumId"]);
include_once("lib/commentslib.php");
$commentslib = new Comments($dbTiki);

$commentslib->comment_add_hit($_REQUEST["comments_parentId"]);

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


if($tiki_p_admin_forum != 'y' && $tiki_p_forum_read != 'y') {
    $smarty->assign('msg',tra("You dont have permission to use this feature"));
    $smarty->display("styles/$style_base/error.tpl");
    die;
}


$smarty->assign_by_ref('forum_info',$forum_info);
$thread_info = $commentslib->get_comment($_REQUEST["comments_parentId"]);
$smarty->assign_by_ref('thread_info',$thread_info);


$comments_per_page = $forum_info["topicsPerPage"];
$comments_default_ordering = $forum_info["threadOrdering"];
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
if($tiki_p_admin_forum == 'y' || $tiki_p_forum_post == 'y') {
  if($thread_info["type"]<>'l' || $tiki_p_admin_forum == 'y') {
    if(isset($_REQUEST["comments_postComment"])) {
      if( (!empty($_REQUEST["comments_title"])) && (!empty($_REQUEST["comments_data"])) ){
        if($commentslib->user_can_post_to_forum($user, $_REQUEST["forumId"])) {
          //Replace things between square brackets by links
          $_REQUEST["comments_data"]=strip_tags($_REQUEST["comments_data"]);
          if($_REQUEST["comments_threadId"]==0) {
            $commentslib->post_new_comment($comments_objectId, $_REQUEST["comments_parentId"], $user, $_REQUEST["comments_title"], nl2br($_REQUEST["comments_data"]))  ;
            if($forum_info["useMail"]=='y') {
              
              $smarty->assign('mail_forum',$forum_info["name"]);
              $smarty->assign('mail_title',$_REQUEST["comments_title"]);
              $smarty->assign('mail_date',date("u"));
              $smarty->assign('mail_message',$_REQUEST["comments_data"]);
              $smarty->assign('mail_author',$user);
              
              $mail_data = $smarty->fetch('mail/forum_post_notification.tpl');
              @mail($forum_info["mail"], tra('Tiki email notification'),$mail_data);
              
              
            }
            $commentslib->register_forum_post($_REQUEST["forumId"],$_REQUEST["comments_parentId"]);
          } else {
            // if($tiki_p_edit_comments == 'y') {
              $commentslib->update_comment($_REQUEST["comments_threadId"], $_REQUEST["comments_title"], nl2br($_REQUEST["comments_data"]));
            //}
          }
        } else {
          $smarty->assign('msg',tra("Please wait 2 minutes between posts"));
          $smarty->display("styles/$style_base/error.tpl");
          die;  
        }
      }
    }
  }
}


if($tiki_p_admin_forum == 'y' || $tiki_p_forum_vote == 'y') {
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


if($_REQUEST["comments_threadId"]>0) {
  $comment_info = $commentslib->get_comment($_REQUEST["comments_threadId"]);
  $smarty->assign('comment_title',$comment_info["title"]);
  $smarty->assign('comment_data',$comment_info["data"]);
} else {
  $smarty->assign('comment_title','');
  $smarty->assign('comment_data','');
}

if(isset($_REQUEST["quote"])) {
  $quote_info = $commentslib->get_comment($_REQUEST["quote"]);
  $quoted_lines = split("\n",$quote_info["data"]);
  $qdata = '';
  for($i=0;$i<count($quoted_lines);$i++) {
    $quoted_lines[$i]='> '.$quoted_lines[$i];
  }
  $qdata = implode("\n",$quoted_lines);
  $qdata = '> '.$quote_info["userName"].":\n".$qdata;
  $smarty->assign('comment_data',$qdata);
  $smarty->assign('openpost','y');
}

$smarty->assign('comment_preview','n');
if(isset($_REQUEST["comments_previewComment"])) {
  $smarty->assign('comments_preview_title',$_REQUEST["comments_title"]);
  $smarty->assign('comments_preview_data',nl2br($commentslib->parse_comment_data($_REQUEST["comments_data"])));
  $smarty->assign('comment_title',$_REQUEST["comments_title"]);
  $smarty->assign('comment_data',$_REQUEST["comments_data"]);
  $smarty->assign('openpost','y');
  $smarty->assign('comment_preview','y');
}


if($tiki_p_admin_forum == 'y') {
  if(isset($_REQUEST["comments_remove"])&&isset($_REQUEST["comments_threadId"])) {
   $comments_show='y';
   $commentslib->remove_comment($_REQUEST["comments_threadId"]);
   $commentslib->register_remove_post($_REQUEST["forumId"], $_REQUEST["comments_parentId"]);
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

$section='forums';
include_once('tiki-section_options.php');

if($feature_theme_control == 'y') {
	$cat_type='forum';
	$cat_objid = $_REQUEST["forumId"];
	include('tiki-tc.php');
}



// Display the template
$smarty->assign('mid','tiki-view_forum_thread.tpl');
$smarty->display("styles/$style_base/tiki.tpl");
?>