<?php

class timer
        {
        function parseMicro($micro)
                {list($micro,$sec)=explode(' ',microtime()); return $sec+$micro;}
        function start($timer='default')
                {$this->timer[$timer]=$this->parseMicro(microtime());}
        function stop($timer='default')
                {return $this->current($timer);}
        function elapsed($timer='default')
                {return $this->parseMicro(microtime()) - $this->timer[$timer];}
        }

$tiki_timer = new timer();
$tiki_timer->start();

require_once("setup.php");
require_once("lib/tikilib.php");
require_once("lib/userslib.php");

$userlib = new UsersLib($dbTiki);
if(isset($_SESSION["user"])) {
  $user = $_SESSION["user"];  
} else {
  $user = false;
}

// The votes array stores the votes the user has made
if(!isset($_SESSION["votes"])) {
  $votes=Array();
  session_register("votes");
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

 
  
$appname="tiki";
if(!isset($_SESSION["appname"])) {
  session_register("appname");
}
$smarty->assign("appname","tiki");


$tikilib = new TikiLib($dbTiki);
if(isset($GLOBALS["PHPSESSID"])) {
  $tikilib->update_session($GLOBALS["PHPSESSID"]);
}

if(!isset($_SESSION["last_forum_visit"])) {
  $now = date("U");
  if($user) {
    $last_forum_visit = $tikilib->get_user_preference($user,'last_forum_visit',0);
    $tikilib->set_user_preference($user,'last_forum_visit',$now);
  } else {
    $last_forum_visit = $now;
  }
  $_SESSION["last_forum_visit"]=$last_forum_visit;
}

$userbreadCrumb = 4;
$wiki_spellcheck = 'n';
$cms_spellcheck = 'n';
$blog_spellcheck = 'n';
$home_blog = 0;
$home_gallery = 0;
$home_file_gallery = 0;
$home_forum = 0;
$feature_xmlrpc = 'n';
$feature_comm = 'n';
$feature_categories = 'n';
$feature_faqs = 'n';
$feature_stats = 'n';
$feature_games = 'n';
$user_assigned_modules = 'n';
$feature_user_bookmarks = 'n';
$feature_blog_rankings = 'y';
$feature_cms_rankings = 'y';
$feature_gal_rankings = 'y';
$feature_wiki_rankings = 'y';
$feature_wiki_undo = 'n';
$feature_wiki_multiprint = 'n';
$feature_forum_rankings = 'y';
$feature_lastChanges =  'y';
$feature_dump =  'y';
$feature_ranking = 'y';
$feature_listPages = 'y';
$feature_history = 'y';
$feature_backlinks = 'y';
$feature_likePages = 'y';
$feature_search = 'y';
$feature_sandbox = 'y';
$feature_userPreferences = 'n';
$feature_userVersions = 'y';
$feature_galleries = 'y';
$feature_featuredLinks = 'y';
$feature_hotwords = 'y';
$feature_banners = 'n';
$feature_top_banner = 'n';
$feature_wiki = 'y';
$feature_articles = 'n';
$feature_submissions = 'n';
$feature_blogs = 'n';
$feature_xmlrpc = 'n';
$feature_edit_templates = 'n';
$feature_dynamic_content = 'n';
$feature_chat = 'n';
$feature_polls = 'n';

$feature_wiki_comments = 'n';
$wiki_comments_default_ordering = 'points_desc';
$wiki_comments_per_page = 10;

$feature_faq_comments = 'y';
$faq_comments_default_ordering = 'points_desc';
$faq_comments_per_page = 10;

$feature_forums = 'n';
$forums_ordering = 'created_desc';
$forums_comments_per_page = 10;


$feature_image_galleries_comments = 'n';
$image_galleries_comments_default_ordering = 'points_desc';
$image_galleries_comments_per_page = 10;

$feature_file_galleries_comments = 'n';
$file_galleries_comments_default_ordering = 'points_desc';
$file_galleries_comments_per_page = 10;


$feature_poll_comments = 'n';
$poll_comments_default_ordering = 'points_desc';
$poll_comments_per_page = 10;


$feature_blog_comments = 'n';
$blog_comments_default_ordering = 'points_desc';
$blog_comments_per_page = 10;

$feature_article_comments = 'n';
$article_comments_default_ordering = 'points_desc';
$article_comments_per_page = 10;



$feature_warn_on_edit ='n';
$feature_file_galleries = 'n';
$feature_file_galleries_rankings = 'n';
$language = 'en';
$feature_left_column = 'y';
$feature_right_column = 'y';
$feature_top_bar = 'y';
$feature_bot_bar = 'y';

$rss_forums = 'y';
$rss_forum = 'y';
$rss_articles = 'y';
$rss_blogs = 'y';
$rss_image_galleries = 'y';
$rss_file_galleries = 'y';
$rss_wiki = 'y';
$rss_image_gallery = 'n';
$rss_file_gallery = 'n';
$rss_blog = 'n';

$max_rss_forums = 10;
$max_rss_forum = 10;
$max_rss_articles = 10;
$max_rss_blogs = 10;
$max_rss_image_galleries = 10;
$max_rss_file_galleries = 10;
$max_rss_wiki = 10;
$max_rss_image_gallery = 10;
$max_rss_file_gallery = 10;
$max_rss_blog = 10;

$keep_versions = 1;

$feature_custom_home = 'n';

$smarty->assign('dblclickedit','n');

$smarty->assign('feature_custom_home',$feature_custom_home);

$smarty->assign('keep_versions',$keep_versions);

$smarty->assign('wiki_spellcheck',$wiki_spellcheck);
$smarty->assign('cms_spellcheck',$cms_spellcheck);
$smarty->assign('blog_spellcheck',$cms_spellcheck);

$smarty->assign('userbreadCrumb',$userbreadCrumb);
$smarty->assign('feature_polls',$feature_polls);
$smarty->assign('feature_chat',$feature_chat);
$smarty->assign('rss_articles',$rss_articles);
$smarty->assign('rss_forum',$rss_forum);
$smarty->assign('rss_forums',$rss_forums);
$smarty->assign('rss_blogs',$rss_blogs);
$smarty->assign('rss_image_galleries',$rss_image_galleries);
$smarty->assign('rss_file_galleries',$rss_file_galleries);
$smarty->assign('rss_wiki',$rss_wiki);
$smarty->assign('rss_image_gallery',$rss_image_gallery);
$smarty->assign('rss_file_gallery',$rss_file_gallery);
$smarty->assign('rss_blog',$rss_blog);


$smarty->assign('max_rss_articles',$max_rss_articles);
$smarty->assign('max_rss_blogs',$max_rss_blogs);
$smarty->assign('max_rss_image_galleries',$max_rss_image_galleries);
$smarty->assign('max_rss_file_galleries',$max_rss_file_galleries);
$smarty->assign('max_rss_wiki',$max_rss_wiki);
$smarty->assign('max_rss_image_gallery',$max_rss_image_gallery);
$smarty->assign('max_rss_file_gallery',$max_rss_file_gallery);
$smarty->assign('max_rss_blog',$max_rss_blog);


$smarty->assign('feature_left_column',$feature_left_column);
$smarty->assign('feature_right_column',$feature_right_column);
$smarty->assign('feature_top_bar',$feature_top_bar);
$smarty->assign('feature_bot_bar',$feature_bot_bar);
$smarty->assign('feature_file_galleries',$feature_file_galleries);
$smarty->assign('feature_file_galleries_rankings',$feature_file_galleries_rankings);
$smarty->assign('language',$language);
$smarty->assign('home_blog',$home_blog);
$smarty->assign('home_forum',$home_forum);
$smarty->assign('home_gallery',$home_gallery);
$smarty->assign('home_file_gallery',$home_file_gallery);
$smarty->assign('feature_dynamic_content',$feature_dynamic_content);
$smarty->assign('feature_edit_templates',$feature_edit_templates);
$smarty->assign('feature_top_banner',$feature_top_banner);
$smarty->assign('feature_banners',$feature_banners);
$smarty->assign('feature_xmlrpc',$feature_xmlrpc);
$smarty->assign('feature_comm',$feature_comm);
$smarty->assign('feature_cms_rankings',$feature_cms_rankings);
$smarty->assign('feature_blog_rankings',$feature_blog_rankings);
$smarty->assign('feature_gal_rankings',$feature_gal_rankings);
$smarty->assign('feature_wiki_rankings',$feature_wiki_rankings);
$smarty->assign('feature_wiki_undo',$feature_wiki_undo);
$smarty->assign('feature_forum_rankings',$feature_forum_rankings);
$smarty->assign('feature_hotwords',$feature_hotwords);
$smarty->assign('feature_lastChanges',$feature_lastChanges);
$smarty->assign('feature_dump',$feature_dump);
$smarty->assign('feature_categories',$feature_categories);
$smarty->assign('feature_faqs',$feature_faqs);
$smarty->assign('feature_stats',$feature_stats);
$smarty->assign('feature_games',$feature_games);
$smarty->assign('user_assigned_modules',$user_assigned_modules);
$smarty->assign('feature_user_bookmarks',$feature_user_bookmarks);
$smarty->assign('feature_ranking',$feature_ranking);
$smarty->assign('feature_listPages', $feature_listPages);
$smarty->assign('feature_history', $feature_history);
$smarty->assign('feature_backlinks', $feature_backlinks);
$smarty->assign('feature_likePages', $feature_likePages);
$smarty->assign('feature_search', $feature_search);
$smarty->assign('feature_sandbox', $feature_sandbox);
$smarty->assign('feature_userPreferences', $feature_userPreferences);
$smarty->assign('feature_userVersions', $feature_userVersions);
$smarty->assign('feature_galleries',$feature_galleries);
$smarty->assign('feature_featuredLinks',$feature_featuredLinks);
$smarty->assign('feature_wiki',$feature_wiki);
$smarty->assign('feature_articles',$feature_articles);
$smarty->assign('feature_submissions',$feature_submissions);
$smarty->assign('feature_blogs',$feature_blogs);
$smarty->assign('feature_xmlrpc',$feature_xmlrpc);

$smarty->assign('feature_wiki_comments',$feature_wiki_comments);
$smarty->assign('wiki_comments_default_ordering',$wiki_comments_default_ordering);
$smarty->assign('wiki_comments_per_page',$wiki_comments_per_page);

$smarty->assign('feature_faq_comments',$feature_faq_comments);
$smarty->assign('faq_comments_default_ordering',$faq_comments_default_ordering);
$smarty->assign('faq_comments_per_page',$faq_comments_per_page);


$smarty->assign('feature_forums',$feature_forums);
$smarty->assign('forums_ordering',$forums_ordering);
$smarty->assign('forums_comments_per_page',$forums_comments_per_page);


$smarty->assign('feature_image_galleries_comments',$feature_image_galleries_comments);
$smarty->assign('image_galleries_comments_default_ordering',$image_galleries_comments_default_ordering);
$smarty->assign('image_galleries_comments_per_page',$image_galleries_comments_per_page);

$smarty->assign('feature_file_galleries_comments',$feature_file_galleries_comments);
$smarty->assign('file_galleries_comments_default_ordering',$file_galleries_comments_default_ordering);
$smarty->assign('file_galleries_comments_per_page',$file_galleries_comments_per_page);


$smarty->assign('feature_poll_comments',$feature_poll_comments);
$smarty->assign('poll_comments_default_ordering',$poll_comments_default_ordering);
$smarty->assign('poll_comments_per_page',$poll_comments_per_page);


$smarty->assign('feature_blog_comments',$feature_blog_comments);
$smarty->assign('blog_comments_default_ordering',$blog_comments_default_ordering);
$smarty->assign('blog_comments_per_page',$blog_comments_per_page);

$smarty->assign('feature_article_comments',$feature_article_comments);
$smarty->assign('article_comments_default_ordering',$article_comments_default_ordering);
$smarty->assign('article_comments_per_page',$article_comments_per_page);


$smarty->assign('feature_warn_on_edit',$feature_warn_on_edit);

// Other preferences
$popupLinks = $tikilib->get_preference("popupLinks",'n');
$anonCanEdit = $tikilib->get_preference("anonCanEdit",'n');
$modallgroups = $tikilib->get_preference("modallgroups",'y');
$tikiIndex = $tikilib->get_preference("tikiIndex",'tiki-index.php');
$cachepages = $tikilib->get_preference("cachepages",'y');
$cacheimages = $tikilib->get_preference("cacheimages",'y');
$allowRegister = $tikilib->get_preference("allowRegister",'n');
$validateUsers = $tikilib->get_preference("validateUsers",'n');
$forgotPass = $tikilib->get_preference("forgotPass",'n');
$title = $tikilib->get_preference("title","");
$maxRecords = $tikilib->get_preference("maxRecords",10);
$maxArticles = $tikilib->get_preference("maxArticles",10);
$smarty->assign('tikiIndex',$tikiIndex);
$smarty->assign('maxArticles',$maxArticles);
$smarty->assign('popupLinks',$popupLinks);
$smarty->assign('modallgroups',$modallgroups);
$smarty->assign('anonCanEdit',$anonCanEdit);
$smarty->assign('allowRegister',$allowRegister);
$smarty->assign('cachepages',$cachepages);
$smarty->assign('cacheimages',$cacheimages);


$prefs = $tikilib->get_all_preferences();
foreach($prefs as $name => $val) {
  $$name = $val;
  $smarty->assign("$name",$val);
}

$style = $tikilib->get_preference("style", 'subsilver.css');
$smarty->assign('style',$style);


if($feature_userPreferences == 'y') {
  // Check for FEATURES for the user
  $user_style = $tikilib->get_preference("style", 'subsilver.css');
  if($user) {
    $user_style = $tikilib->get_user_preference($user,'theme',$style);
    if($user_style) {
      $style = $user_style;
    }
    $user_language = $tikilib->get_user_preference($user,'language',$language);
    if($user_language) {
      $language = $user_language;
    }
  }
  $smarty->assign('style',$style);
  $smarty->assign('language',$language);
}

global $lang;
include_once('lang/'.$language.'/language.php');
function tra($content)
{
    global $lang;
    if ($content) {
      if(isset($lang[$content])) {
        return $lang[$content];  
      } else {
        return $content;        
      }
    }
}






$smarty->assign('user',$user);
$smarty->assign('lock',false);
$smarty->assign('title',$title);
$smarty->assign('maxRecords',$maxRecords);
include_once("tiki-modules.php");
$smarty->assign('beingEdited','n');

if($feature_warn_on_edit == 'y') {
// Check if the page is being edited
if(isset($_REQUEST["page"])) {
 $chkpage = $_REQUEST["page"];
} else {
 $chkpage = 'HomePage';
}
// Notice if a page is being edited or if it was being edited and not anymore
//print($GLOBALS["HTTP_REFERER"]);
// IF isset the referer and if the referer is editpage then unset taking the pagename from the
// query or homepage if not query

if(isset($GLOBALS["HTTP_REFERER"])) {
  if(strstr($GLOBALS["HTTP_REFERER"],'tiki-editpage')) {
    $purl = parse_url($GLOBALS["HTTP_REFERER"]);
    if(!isset($purl["query"])) {
      $purl["query"]='';
    }
    parse_str($purl["query"],$purlquery);
    if(!isset($purlquery["page"])) {
      $purlquery["page"]='HomePage';
    }
    $tikilib->semaphore_unset($purlquery["page"]);
  }
}
if(strstr($_SERVER["REQUEST_URI"],'tiki-editpage')) {
  $purl = parse_url($_SERVER["REQUEST_URI"]);
  if(!isset($purl["query"])) {
    $purl["query"]='';
  }
  parse_str($purl["query"],$purlquery);
  if(!isset($purlquery["page"])) {
    $purlquery["page"]='HomePage';
  }
  $tikilib->semaphore_set($purlquery["page"]);
}
if($tikilib->semaphore_is_set($chkpage)) {
  $smarty->assign('beingEdited','y');
  $beingedited='y';
} else {
  $smarty->assign('beingEdited','n');
  $beingedited='n';
}

}

if(isset($_REQUEST["pollVote"])) {
  if($tiki_p_vote_poll == 'y' && !$tikilib->user_has_voted($user,'poll'.$_REQUEST["polls_pollId"]) && isset($_REQUEST["polls_optionId"])) {
    $tikilib->register_user_vote($user,'poll'.$_REQUEST["polls_pollId"]);
    $tikilib->poll_vote($_REQUEST["polls_pollId"],$_REQUEST["polls_optionId"]);
  }
  $pollId=$_REQUEST["polls_pollId"];
  header("location: tiki-poll_results.php?pollId=$pollId");
}

$ownurl = 'http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
$smarty->assign('ownurl','http://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);


// Stats
if($feature_stats == 'y') {
if(!strstr($_SERVER["REQUEST_URI"],'chat')) {
  $tikilib->add_pageview();
}
}

?>