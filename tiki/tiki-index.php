<?php
// Initialization

require_once('tiki-setup.php');
include_once('lib/structures/structlib.php');
include_once('lib/wiki/wikilib.php');
include_once('lib/wiki/histlib.php');
include_once('lib/categories/categlib.php');

if($feature_wiki != 'y') {
  $smarty->assign('msg', tra("This feature is disabled").": feature_wiki");
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}


//print($GLOBALS["HTTP_REFERER"]);

// Create the HomePage if it doesn't exist
if(!$tikilib->page_exists($wikiHomePage)) {
  $tikilib->create_page($wikiHomePage,0,'',date("U"),'Tiki initialization');
}

if(!isset($_SESSION["thedate"])) {
  $thedate = date("U");
} else {
  $thedate = $_SESSION["thedate"];
}

// Get the page from the request var or default it to HomePage
if(!isset($_REQUEST["page"])) {
  $page = $_REQUEST["page"]=$wikiHomePage;
} else {
  $page = $_REQUEST["page"];
}
$smarty->assign_by_ref('page',$page);

require_once('tiki-pagesetup.php');

$creator = $wikilib->get_creator($page);
$smarty->assign('creator',$creator);

// Let creator set permissions
if($wiki_creator_admin == 'y') {
  if ($creator && $user && ($creator==$user)) {
    $tiki_p_admin_wiki = 'y';
    $smarty->assign( 'tiki_p_admin_wiki', 'y' );
  }
}

if(isset($_REQUEST["copyrightpage"])) {
  $smarty->assign_by_ref('copyrightpage',$_REQUEST["copyrightpage"]); 
}

// Get the backlinks for the page "page"
$backlinks = $wikilib->get_backlinks($page);
$smarty->assign_by_ref('backlinks', $backlinks);

// Get page data, if available
$info = $tikilib->get_page_info($page);

// If the page doesn't exist then display an error
if(empty($info)) {
  $smarty->assign('msg',tra("Page cannot be found"));
  $smarty->display("styles/$style_base/error.tpl");
  die;
} else {
  // Update the pagename with the canonical name.  This makes it
  // possible to link to a page using any case, but the page is still
  // displayed with the original capitalization.  So if there's a page
  // called 'About Me', then one can conveniently make a link to it in
  // the text as '... learn more ((about me)).'.  When the link is
  // followed, then it still says 'About Me' in the title.
  $page = $info['pageName'];
}

// Now check permissions to access this page
if($tiki_p_view != 'y') {
  $smarty->assign('msg',tra("Permission denied you cannot view this page"));
  $smarty->display("styles/$style_base/error.tpl");
  die;  
}

// BreadCrumbNavigation here
// Get the number of pages from the default or userPreferences
// Remember to reverse the array when posting the array
$anonpref = $tikilib->get_preference('userbreadCrumb',4);
if($user) {
  $userbreadCrumb = $tikilib->get_user_preference($user,'userbreadCrumb',$anonpref);
} else {
  $userbreadCrumb = $anonpref;
}
if(!isset($_SESSION["breadCrumb"])) {
  $_SESSION["breadCrumb"]=Array();
}
if(!in_array($page,$_SESSION["breadCrumb"])) {
  if(count($_SESSION["breadCrumb"])>$userbreadCrumb) {
    array_shift($_SESSION["breadCrumb"]);
  } 
  array_push($_SESSION["breadCrumb"],$page);
} else {
  // If the page is in the array move to the last position
  $pos = array_search($page, $_SESSION["breadCrumb"]);
  unset($_SESSION["breadCrumb"][$pos]);
  array_push($_SESSION["breadCrumb"],$page);
}
//print_r($_SESSION["breadCrumb"]);


// Now increment page hits since we are visiting this page
if($count_admin_pvs == 'y' || $user!='admin') {
  $tikilib->add_hit($page);
}

$smarty->assign('page_user',$info['user']);

// Check if we have to perform an action for this page
// for example lock/unlock
if( 
    ($tiki_p_admin_wiki == 'y') 
    || 
    ($user and ($tiki_p_lock == 'y') and ($feature_wiki_usrlock == 'y'))
   ) {
if(isset($_REQUEST["action"])) {
  if($_REQUEST["action"]=='lock') {
    $wikilib->lock_page($page);
    $info["flag"] = 'L';
    $smarty->assign('lock',true);  
  }  
}
}

if( 
    ($tiki_p_admin_wiki == 'y') 
    || 
    ($user and ($user == $info['user']) and ($tiki_p_lock == 'y') and ($feature_wiki_usrlock == 'y'))
   ) {
if(isset($_REQUEST["action"])) {
  if ($_REQUEST["action"]=='unlock') {
    $wikilib->unlock_page($page);
    $smarty->assign('lock',false);  
    $info["flag"] = 'U';
  }  
}
}


// Save to notepad if user wants to
if($user 
	&& $tiki_p_notepad == 'y' 
	&& $feature_notepad == 'y' 
	&& isset($_REQUEST['savenotepad'])) {
  $tikilib->replace_note($user,0,$page,$info['data']);
}

// Verify lock status
if($info["flag"] == 'L') {
  $smarty->assign('lock',true);  
} else {
  $smarty->assign('lock',false);
}

// If not locked and last version is user version then can undo
$smarty->assign('canundo','n');	
if($info["flag"]!='L' && ( ($tiki_p_edit == 'y' && $info["user"]==$user)||($tiki_p_remove=='y') )) {
   $smarty->assign('canundo','y');	
}
if($tiki_p_admin_wiki == 'y') {
  $smarty->assign('canundo','y');		
}

// Process an undo here
if(isset($_REQUEST["undo"])) {
if($tiki_p_admin_wiki == 'y' || ($info["flag"]!='L' && ( ($tiki_p_edit == 'y' && $info["user"]==$user)||($tiki_p_remove=='y')) )) {
  // Remove the last version	
  $wikilib->remove_last_version($page);
  // If page was deleted then re-create
  if(!$tikilib->page_exists($page)) {
    $tikilib->create_page($page,0,'',date("U"),'Tiki initialization'); 
  }
  // Restore page information
  $info = $tikilib->get_page_info($page);  	
}
}

$slides = split("-=[^=]+=-",$info["data"]);
if(count($slides)>1) {
	$smarty->assign('show_slideshow','y');
} else {
	$slides = explode("...page...",$info["data"]);
	
	if(count($slides)>1) {
		$smarty->assign('show_slideshow','y');
	} else {
		$smarty->assign('show_slideshow','n');
	}
}

if(isset($_REQUEST['refresh'])) {
  $tikilib->invalidate_cache($page);	
}


// Here's where the data is parsed
// if using cache
//
// get cache information
// if cache is valid then pdata is cache
// else
// pdata is parse_data 
//   if using cache then update the cache
// assign_by_ref
$smarty->assign('cached_page','n');
if(isset($info['wiki_cache']) && $info['wiki_cache']>0) {$wiki_cache=$info['wiki_cache'];}
if($wiki_cache>0) {
 $cache_info = $wikilib->get_cache_info($page);
 $now = date('U');
 if($cache_info['cache_timestamp']+$wiki_cache > $now) {
   $pdata = $cache_info['cache'];
   $smarty->assign('cached_page','y');
 } else {
   $pdata = $tikilib->parse_data($info["data"]);
   $wikilib->update_cache($page,$pdata);
 }
} else {
 $pdata = $tikilib->parse_data($info["data"]);
}


  if(!isset($_REQUEST['pagenum'])) $_REQUEST['pagenum']=1;
  $pages = $wikilib->get_number_of_pages($pdata);
  $pdata=$wikilib->get_page($pdata,$_REQUEST['pagenum']);
  $smarty->assign('pages',$pages);
  if($pages>$_REQUEST['pagenum']) {
  	$smarty->assign('next_page',$_REQUEST['pagenum']+1);
  } else {
  	$smarty->assign('next_page',$_REQUEST['pagenum']);
  }
  if($_REQUEST['pagenum']>1) {
  	$smarty->assign('prev_page',$_REQUEST['pagenum']-1);
  } else {
  	$smarty->assign('prev_page',1);
  }
  $smarty->assign('first_page',1);
  $smarty->assign('last_page',$pages);
  $smarty->assign('pagenum',$_REQUEST['pagenum']);


$smarty->assign_by_ref('parsed',$pdata);

//$smarty->assign_by_ref('lastModif',date("l d of F, Y  [H:i:s]",$info["lastModif"]));
$smarty->assign_by_ref('lastModif',$info["lastModif"]);
if(empty($info["user"])) {
  $info["user"]='anonymous';  
}
$smarty->assign_by_ref('lastUser',$info["user"]);
$smarty->assign_by_ref('description',$info["description"]);
/*
// force enable wiki comments (for development)
$feature_wiki_comments = 'y';
$smarty->assign('feature_wiki_comments','y');
*/

// Comments engine!
if($feature_wiki_comments == 'y') {
  $comments_per_page = $wiki_comments_per_page;
  $comments_default_ordering = $wiki_comments_default_ordering;
  $comments_vars=Array('page');
  $comments_prefix_var='wiki page:';
  $comments_object_var='page';
  include_once("comments.php");
}

$section='wiki';
include_once('tiki-section_options.php');



if($feature_wiki_attachments == 'y') {
  if(isset($_REQUEST["removeattach"])) {
    $owner = $wikilib->get_attachment_owner($_REQUEST["removeattach"]);
    if( ($user && ($owner == $user) ) || ($tiki_p_wiki_admin_attachments == 'y') ) {
      $wikilib->remove_wiki_attachment($_REQUEST["removeattach"]);
    }
  }
  if(isset($_REQUEST["attach"]) && ($tiki_p_wiki_admin_attachments == 'y' || $tiki_p_wiki_attach_files == 'y')) {
    // Process an attachment here
    if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
      $fp = fopen($_FILES['userfile1']['tmp_name'],"rb");
      $data = '';
      $fhash='';
      if($w_use_db == 'n') {
        $fhash = md5($name = $_FILES['userfile1']['name']);    
        $fw = fopen($w_use_dir.$fhash,"wb");
        if(!$fw) {
          $smarty->assign('msg',tra('Cannot write to this file:').$fhash);
          $smarty->display("styles/$style_base/error.tpl");
          die;  
        }
      }
      while(!feof($fp)) {
        if($w_use_db == 'y') {
          $data .= fread($fp,8192*16);
        } else {
          $data = fread($fp,8192*16);
          fwrite($fw,$data);
        }
      }
      fclose($fp);
      if($w_use_db == 'n') {
        fclose($fw);
        $data='';
      }
      $size = $_FILES['userfile1']['size'];
      $name = $_FILES['userfile1']['name'];
      $type = $_FILES['userfile1']['type'];
      $wikilib->wiki_attach_file($page,$name,$type,$size, $data, $_REQUEST["attach_comment"], $user,$fhash);
    }
  }

  $atts = $wikilib->list_wiki_attachments($page,0,-1,'created_desc','');
  $smarty->assign('atts',$atts["data"]);
  $smarty->assign('atts_count',count($atts["data"]));
}


$smarty->assign('footnote','');
$smarty->assign('has_footnote','n');
if($feature_wiki_footnotes == 'y') {
  if($user) {
    $x = $wikilib->get_footnote($user,$page);
    $footnote=$wikilib->get_footnote($user,$page);
    $smarty->assign('footnote',$tikilib->parse_data($footnote));
    if($footnote) $smarty->assign('has_footnote','y');
  }
}

$smarty->assign('wiki_extras','y');
$smarty->assign('structure','n');   
if($structlib->page_is_in_structure($page)) {   
	$smarty->assign('structure','y');
	if (isset($_REQUEST["structID"]))	{
		$prev_next_pages = $structlib->get_prev_next_pages($page, $_REQUEST["structID"]);
	}
	else {
		$prev_next_pages = $structlib->get_prev_next_pages($page);
	}
	$smarty->assign('struct_prev_next', $prev_next_pages);
}

if($feature_theme_control == 'y') {
	$cat_type='wiki page';
	$cat_objid = $page;
	include('tiki-tc.php');
}

// Watches
if($feature_user_watches == 'y') {
	if($user && isset($_REQUEST['watch_event'])) {
	  if($_REQUEST['watch_action']=='add') {
	    $tikilib->add_user_watch($user,$_REQUEST['watch_event'],$_REQUEST['watch_object'],tra('Wiki page'),$page,"tiki-index.php?page=$page");
	  } else {
	    $tikilib->remove_user_watch($user,$_REQUEST['watch_event'],$_REQUEST['watch_object']);
	  }
	}
	$smarty->assign('user_watching_page','n');
	if($user && $watch = $tikilib->get_user_event_watches($user,'wiki_page_changed',$page)) {
		$smarty->assign('user_watching_page','y');
	}
}


$sameurl_elements=Array('pageName','page');
//echo $info["data"];

if(isset($_REQUEST['mode']) && $_REQUEST['mode']=='mobile') {
/*
	require_once("lib/hawhaw/hawhaw.inc");
	require_once("lib/hawhaw/hawiki_cfg.inc");
	require_once("lib/hawhaw/hawiki_parser.inc");
	require_once("lib/hawhaw/hawiki.inc");
	error_reporting(E_ALL & ~E_NOTICE);
	$myWiki = new HAWIKI_page($info["data"],"tiki-index.php?mode=mobile&page=");

	$myWiki->set_navlink(tra("Home Page"), "tiki-index.php?mode=mobile", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
	$myWiki->set_navlink(tra("Menu"), "tiki-mobile.php", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
	$myWiki->set_smiley_dir("img/smiles");
	$myWiki->set_link_jingle("lib/hawhaw/link.wav");
	$myWiki->set_hawimconv("lib/hawhaw/hawimconv.php");

	$myWiki->display();
	die;
  */
  include_once("lib/hawhaw/hawtikilib.php");
  HAWTIKI_index($info);
}

// Check to see if page is categorized
$objId = urldecode($page);
$is_categorized = $categlib->is_categorized('wiki page',$objId);


// Display category path or not (like {catpath()})
if ($is_categorized) {
	$smarty->assign('is_categorized','y');
	if(isset($feature_categorypath) and $feature_categories == 'y') {
		if ($feature_categorypath == 'y') {
			$cats = $categlib->get_object_categories('wiki page',$objId);
			$display_catpath = $tikilib->get_categorypath($cats);
			$smarty->assign('display_catpath',$display_catpath);
		}
	}
	// Display current category objects or not (like {category()})
	if (isset($feature_categoryobjects) and $feature_categories == 'y') {
		if ($feature_categoryobjects == 'y') {
			$catids = $categlib->get_object_categories('wiki page', $objId);
			$display_catobjects = $tikilib->get_categoryobjects($catids);
			$smarty->assign('display_catobjects',$display_catobjects);
		}
	}
} else {
	$smarty->assign('is_categorized','n');
}

// Flag for 'page bar' that currently 'Page view' mode active
// so it is needed to show comments & attachments panels
$smarty->assign('show_page','y');

// Display the Index Template
$smarty->assign('dblclickedit','y');
$smarty->assign('print_page','n');
$smarty->assign('mid','tiki-show_page.tpl');
$smarty->assign('show_page_bar','y');
$smarty->assign('categorypath',$feature_categorypath);
$smarty->assign('categoryobjects',$feature_categoryobjects);
$smarty->display("styles/$style_base/tiki.tpl");

// xdebug_dump_function_profile(XDEBUG_PROFILER_CPU);

?>
