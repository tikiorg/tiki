<?php
// $Id: /cvsroot/tikiwiki/tiki/tiki-index.php,v 1.198.2.22 2008-03-12 15:10:01 ricks99 Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
$section = 'wiki page';
require_once('tiki-setup.php');
include_once('lib/structures/structlib.php');
include_once('lib/wiki/wikilib.php');
include_once('lib/stats/statslib.php');
include_once('lib/ajax/ajaxlib.php');
require_once ("lib/wiki/wiki-ajax.php");

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
}

if($prefs['feature_wiki'] != 'y') {
    $smarty->assign('msg', tra('This feature is disabled').': feature_wiki');
    $smarty->display('error.tpl');
    die;  
}

if(!isset($_SESSION['thedate'])) {
    $thedate = $tikilib->now;
} else {
    $thedate = $_SESSION['thedate'];
}

if (isset($_REQUEST['page_id'])) {
    $_REQUEST['page'] = $tikilib->get_page_name_from_id($_REQUEST['page_id']);
    //TODO: introduce a get_info_from_id to save a sql request
}

$use_best_language = false;

if ((!isset($_REQUEST['page']) || $_REQUEST['page'] == '') and !isset($_REQUEST['page_ref_id'])) {
	$_REQUEST['page'] = $userHomePage = $userlib->get_user_default_homepage2($user);
	
	// Create the HomePage if it doesn't exist
	if ( ! empty($userHomePage) ) {
		if ( ! $tikilib->page_exists($userHomePage)) {
			$tikilib->create_page($userHomePage,0,'!Congratulations
This is the default ))HomePage(( for your Tiki. If you are seeing this page, your installation was successful.

You can change this page after logging in. Please review the [http://doc.tikiwiki.org/wiki+syntax|wiki syntax] for editing details.


!!{img src=pics/icons/star.png alt="Star"} Get started.
To begin configuring your site:
#Enable specific Tiki features.
#Configure the features.


!!{img src=pics/icons/help.png alt="Help"} Need help?
For more information:
*[http://info.tikiwiki.org/Learn+More|Learn more about TikiWiki].
*[http://info.tikiwiki.org/Help+Others|Get help], including the [http://doc.tikiwiki.org|official documentation] and [http://www.tikiwiki.org/forums|support forums].
*[http://info.tikiwiki.org/Join+the+community|Join the TikiWiki community].
',$tikilib->now,'Tiki initialization');
			header('Location: tiki-index.php?page='.$userHomePage);
		}
	} else {
		$smarty->assign('msg', tra('No name indicated for wiki page'));
		$smarty->display('error.tpl');
		die;
	}
	if ($prefs['feature_best_language'] == 'y') {
		$use_best_language = true;
	}
}

$structure = 'n';
$smarty->assign('structure',$structure);
// Feature checks made in the function for structure language
$structlib->use_user_language_preferences();

if (isset($_REQUEST['page_ref_id'])) {
    // If a structure page has been requested
    $page_ref_id = $_REQUEST['page_ref_id'];
} else {
    // else check if page is the head of a structure 
    $page_ref_id = $structlib->get_struct_ref_if_head($_REQUEST['page']);
}

//If a structure page isnt going to be displayed
if (!isset($page_ref_id)) {
    //Check to see if its a member of any structures
    if (isset($_REQUEST['structure']) && !empty($_REQUEST['structure'])) {
      $struct=$_REQUEST['structure'];
    } else {
      $struct='';
    }
    //Get the structures this page is a member of
    $structs = $structlib->get_page_structures($_REQUEST["page"],$struct);
	$structs_with_perm = array(); 
	foreach ($structs as $t_structs) {
		if ($tikilib->user_has_perm_on_object($user,$t_structs['pageName'],'wiki page','tiki_p_view')) {
			$structs_with_perm[] = $t_structs;
		}
	}
    //If page is only member of one structure, display if requested
    $single_struct = count($structs_with_perm) == 1; 
    if ((!empty($struct) || $prefs['feature_wiki_open_as_structure'] == 'y') && $single_struct) {
      $page_ref_id=$structs_with_perm[0]['req_page_ref_id'];
      $_REQUEST['page_ref_id']=$page_ref_id;
    }

}

if(isset($page_ref_id)) {
    $structure = 'y';
    $smarty->assign('structure',$structure);
    $page_info = $structlib->s_get_page_info($page_ref_id);
    $smarty->assign('page_info', $page_info);
    $navigation_info = $structlib->get_navigation_info($page_ref_id);
    $smarty->assign('next_info', $navigation_info['next']);
    $smarty->assign('prev_info', $navigation_info['prev']);
    $smarty->assign('parent_info', $navigation_info['parent']);
    $smarty->assign('home_info', $navigation_info['home']);
    $page = $page_info['pageName'];
    $info = null;
    // others still need a good set page name or they will get confused.
    // comments of home page were all visible on every structure page
    $_REQUEST['page']=$page;
    $structure_path = $structlib->get_structure_path($page_ref_id);
    $smarty->assign('structure_path', $structure_path);
    // Need to have showstructs when in more than one struct - for usability reasons 
	$structs = $structlib->get_page_structures($page);
	$structs_with_perm = array(); 
	foreach ($structs as $t_structs) {
		if ($tikilib->user_has_perm_on_object($user,$t_structs['pageName'],'wiki page','tiki_p_view')) {
			$structs_with_perm[] = $t_structs;
		}
	}    	
    if ($tikilib->user_has_perm_on_object($user,$navigation_info['home']['pageName'],'wiki page','tiki_p_edit','tiki_p_edit_categorized'))
		$smarty->assign('struct_editable', 'y');
	else
		$smarty->assign('struct_editable', 'n');	
	// To show position    
    if (count($structure_path) > 1) {		
		for ($i = 1; $i < count($structure_path); $i++) {
			$cur_pos .= $structure_path[$i]["pos"] . "." ;
		}
		$cur_pos = substr($cur_pos, 0, strlen($cur_pos)-1);      
	} else {
		$cur_pos = tra("Top");
	}
	$smarty->assign('cur_pos', $cur_pos);	
} else {
    $page_ref_id = '';
}

$smarty->assign('showstructs', $structs_with_perm);
$page = $_REQUEST['page'];
$smarty->assign_by_ref('page',$page);
$smarty->assign('page_ref_id', $page_ref_id);



if (!$tikilib->page_exists($page) && $tikilib->page_exists(utf8_encode($page))) {
    $page = $_REQUEST["page"] = utf8_encode($page);
}

$use_best_language = $use_best_language || isset($_REQUEST['bl']) || isset($_REQUEST['best_lang']);

$info = null;
if ($prefs['feature_multilingual'] == 'y' && $use_best_language) { // chose the best language page
	global $multilinguallib;
	include_once('lib/multilingual/multilinguallib.php');
	$info = $tikilib->get_page_info($page);
	$bestLangPageId = $multilinguallib->selectLangObj('wiki page', $info['page_id']);
	if ($info['page_id'] != $bestLangPageId) {
		$page = $tikilib->get_page_name_from_id($bestLangPageId);
//TODO: introduce a get_info_from_id to save a sql request
		$info = null;
	} elseif ($info['lang'] != $prefs['language'] && $prefs['feature_homePage_if_bl_missing'] == 'y') {
		if (!isset($userPageName))
			$userPageName = $userlib->get_user_default_homepage2($user);
		$page = $userPageName;
		$info = $tikilib->get_page_info($page);
		$bestLangPageId = $multilinguallib->selectLangObj('wiki page', $info['page_id']);
		if ($info['page_id'] != $bestLangPageId) {
			$page = $tikilib->get_page_name_from_id($bestLangPageId);
			$info = null;
		}
	}
}



// Get page data, if available
if (!$info)
	$info = $tikilib->get_page_info($page);

// If the page doesn't exist then display an error
if(empty($info) && !($user && $prefs['feature_wiki_userpage'] == 'y' && strcasecmp($prefs['feature_wiki_userpage_prefix'].$user, $page) == 0)) {
  if ($user && $prefs['feature_wiki_userpage'] == 'y' && strcasecmp($prefs['feature_wiki_userpage_prefix'], $page) == 0) {
    header('Location: tiki-index.php?page='.$prefs['feature_wiki_userpage_prefix'].$user);
    die;
  }
  if ($prefs['feature_wiki_userpage'] == 'y' && strcasecmp($prefs['feature_wiki_userpage_prefix'], substr($page, 0, strlen($prefs['feature_wiki_userpage_prefix']))) == 0)
	$isUserPage = true;
  else
	$isUserPage = false;
  $likepages = $wikilib->get_like_pages($page);
  /* if we have exactly one match, redirect to it */
  if(count($likepages) == 1  && !$isUserPage) {
    header ("Status: 302 Found"); /* PHP3 */
    header ("HTTP/1.0 302 Found"); /* PHP4 */
    header("Location: tiki-index.php?page=$likepages[0]");
    die;
  }
  $smarty->assign_by_ref('likepages', $likepages);
  $smarty->assign('msg',tra('Page cannot be found'));
  header ('Status: 404 Not Found'); /* PHP3 */
  header ('HTTP/1.0 404 Not Found'); /* PHP4 */
  $smarty->assign('headtitle',tra('Page cannot be found'));
  $smarty->assign('errortitle',tra('Page cannot be found').' (404)');
  $smarty->assign('errortype', '404');
  $smarty->assign('create', $isUserPage? 'n': 'y');
  $smarty->display("error.tpl");
  die;
}


if (empty($info) && $user && $prefs['feature_wiki_userpage'] == 'y' && (strcasecmp($prefs['feature_wiki_userpage_prefix'].$user, $page) == 0 || strcasecmp($prefs['feature_wiki_userpage_prefix'], $page) == 0 )) {
	header('Location: tiki-editpage.php?page='.$prefs['feature_wiki_userpage_prefix'].$user);
    	die;
}

/*Wiki SECURITY warning to optimizers : Although get_page_info is currently
called even if permission is denied, we must still get page's real name
(case-sensitive) before tiki-pagesetup.php is included. Bug #990242 for
details */
// Update the pagename with the canonical name.  This makes it
// possible to link to a page using any case, but the page is still
// displayed with the original capitalization.  So if there's a page
// called 'About Me', then one can conveniently make a link to it in
// the text as '... learn more ((about me)).'.  When the link is
// followed, then it still says 'About Me' in the title.
$page = $info['pageName'];

// Get the authors style for this page
$wiki_authors_style = ( $info['wiki_authors_style'] != '' ) ? $info['wiki_authors_style'] : $prefs['wiki_authors_style'];
$smarty->assign('wiki_authors_style', $wiki_authors_style);

// Get the contributors for this page
if (isset($prefs['wiki_authors_style']) && $prefs['wiki_authors_style'] != 'classic') {
	$contributors = $wikilib->get_contributors($page, $info['user']);
	$smarty->assign('contributors',$contributors);
}

$creator = $wikilib->get_creator($page);
$smarty->assign('creator',$creator);

require_once('tiki-pagesetup.php');

$tikilib->get_perm_object($page, 'wiki page', $info, true);

// Now check permissions to access this page
if($tiki_p_view != 'y') {
	$smarty->assign('errortype', 401);
	$smarty->assign('msg',tra('Permission denied you cannot view this page'));
	$smarty->display('error.tpl');
	die;  
}

// Convert page to structure
if (isset($_REQUEST['convertstructure']) && isset($structs) && count($structs) == 0) {
	$page_ref_id = $structlib->s_create_page(0, null, $page);
	header('Location: tiki-index.php?page_ref_id='.$page_ref_id );
}

// Get translated page
if ($prefs['feature_multilingual'] == 'y') {
	global $multilinguallib;
	include_once('lib/multilingual/multilinguallib.php');

	if( $info['lang'] && $info['lang'] != 'NULL') { //NULL is a temporary patch
		$trads = $multilinguallib->getTranslations('wiki page', $info['page_id'], $page, $info['lang']);
		$smarty->assign('trads', $trads);
		$pageLang = $info['lang'];
		$smarty->assign('pageLang', $pageLang);
	}
	
	if ($prefs['feature_wikiapproval'] == 'y' && $tikilib->page_exists($prefs['wikiapproval_prefix'] . $page)) {
	// temporary fix: simply use info of staging page to determine critical translation bits
	// TODO: better system of dealing with translation bits with approval		
		$bits = $multilinguallib->getMissingTranslationBits( 'wiki page', $stagingPageId = $tikilib->get_page_id_from_name($prefs['wikiapproval_prefix'] . $page), 'critical', true );	
	} else {
		$bits = $multilinguallib->getMissingTranslationBits( 'wiki page', $info['page_id'], 'critical', true );
	}
	
	$alertData = array();
	foreach( $bits as $translationBit ) {
		if ($prefs['feature_wikiapproval'] == 'y' && $tikilib->page_exists($prefs['wikiapproval_prefix'] . $page)) {
			$alertData[] = $multilinguallib->getTranslationsWithBit( $translationBit, $stagingPageId );
		} else {
			$alertData[] = $multilinguallib->getTranslationsWithBit( $translationBit, $info['page_id'] );
		}
	}

	$smarty->assign( 'translation_alert', $alertData );
}

if(isset($_REQUEST['copyrightpage'])) {
  $smarty->assign_by_ref('copyrightpage',$_REQUEST['copyrightpage']); 
}

// Get the backlinks for the page "page"
$backlinks = $wikilib->get_backlinks($page);
$smarty->assign_by_ref('backlinks', $backlinks);

// BreadCrumbNavigation here
// Remember to reverse the array when posting the array

if(!isset($_SESSION['breadCrumb'])) {
    $_SESSION['breadCrumb']=Array();
}
if(!in_array($page,$_SESSION['breadCrumb'])) {
    if(count($_SESSION['breadCrumb'])>$prefs['userbreadCrumb']) {
	array_shift($_SESSION['breadCrumb']);
    } 
    array_push($_SESSION['breadCrumb'],$page);
} else {
    // If the page is in the array move to the last position
    $pos = array_search($page, $_SESSION['breadCrumb']);
    unset($_SESSION['breadCrumb'][$pos]);
    array_push($_SESSION['breadCrumb'],$page);
}
//print_r($_SESSION["breadCrumb"]);


// Now increment page hits since we are visiting this page
if($prefs['count_admin_pvs'] == 'y' || $user!='admin') {
    $tikilib->add_hit($page);
}

$smarty->assign('page_user',$info['user']);

// Check if we have to perform an action for this page
// for example lock/unlock
if( 
	($tiki_p_admin_wiki == 'y') 
	|| 
	($user and ($tiki_p_lock == 'y') and ($prefs['feature_wiki_usrlock'] == 'y'))
  ) {
    if(isset($_REQUEST['action'])) {
	check_ticket('index');
	if($_REQUEST['action']=='lock') {
	    $wikilib->lock_page($page);
	    $info['flag'] = 'L';
	    $smarty->assign('lock',true);  
	}  
    }
}

if( 
	($tiki_p_admin_wiki == 'y') 
	|| 
	($user and ($user == $info['user']) and ($tiki_p_lock == 'y') and ($prefs['feature_wiki_usrlock'] == 'y'))
  ) {
    if(isset($_REQUEST['action'])) {
	check_ticket('index');
	if ($_REQUEST['action']=='unlock') {
	    $wikilib->unlock_page($page);
	    $smarty->assign('lock',false);  
	    $info['flag'] = 'U';
	}  
    }
}


// Save to notepad if user wants to
if($user 
	&& $tiki_p_notepad == 'y' 
	&& $prefs['feature_notepad'] == 'y' 
	&& isset($_REQUEST['savenotepad'])) {
    check_ticket('index');
    $tikilib->replace_note($user,0,$page,$info['data']);
}

// Verify lock status
if($wikilib->is_locked($page, $info)) {
    $smarty->assign('lock',true);  
} else {
    $smarty->assign('lock',false);
}
$smarty->assign('editable', $wikilib->is_editable($page, $user, $info));

// If not locked and last version is user version then can undo
$smarty->assign('canundo','n');	
if($info['flag']!='L' && ( ($tiki_p_edit == 'y' && $info['user']==$user)||($tiki_p_remove=='y') )) {
    $smarty->assign('canundo','y');	
}
if($tiki_p_admin_wiki == 'y') {
    $smarty->assign('canundo','y');		
}

// Process an undo here
if(isset($_REQUEST['undo'])) {
    if($tiki_p_admin_wiki == 'y' || ($info['flag']!='L' && ( ($tiki_p_edit == 'y' && $info['user']==$user)||($tiki_p_remove=='y')) )) {
	$area = 'delundopage';
	if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
	    key_check($area);

	    // Remove the last version	
	    $wikilib->remove_last_version($page);
	    // If page was deleted then re-create
	    if(!$tikilib->page_exists($page)) {
		$tikilib->create_page($page,0,'',$tikilib->now,'Tiki initialization'); 
	    }
	    // Restore page information
	    $info = $tikilib->get_page_info($page);  	
	} else {
	    key_get($area);
	}
    }	
}

if ($prefs['wiki_uses_slides'] == 'y') {
    $slides = split("-=[^=]+=-",$info['data']);
    if(count($slides)>1) {
	$smarty->assign('show_slideshow','y');
    } else {
	$slides = explode('...page...',$info['data']);
	if(count($slides)>1) {
	    $smarty->assign('show_slideshow','y');
	} else {
	    $smarty->assign('show_slideshow','n');
	}
    }
} else {
    $smarty->assign('show_slideshow','n');
}

if(isset($_REQUEST['refresh'])) {
    check_ticket('index');
    $tikilib->invalidate_cache($page);	
}

if(!isset($info['is_html'])) {
    $info['is_html']=false;
}

$cat_type = 'wiki page';
$cat_objid = $page;
include_once('tiki-section_options.php');

$smarty->assign('cached_page','n');
if(isset($info['wiki_cache'])) {$prefs['wiki_cache']=$info['wiki_cache'];}
if($prefs['wiki_cache']>0) {
    $cache_info = $wikilib->get_cache_info($page);
    if($cache_info['cache_timestamp']+$prefs['wiki_cache'] > $tikilib->now) {
	$pdata = $cache_info['cache'];
	$smarty->assign('cached_page','y');
    } else {
	$pdata = $tikilib->parse_data($info['data'],$info['is_html']);
	$wikilib->update_cache($page,$pdata);
    }
} else {
    $pdata = $tikilib->parse_data($info['data'],$info['is_html']);
}

$smarty->assign_by_ref('parsed',$pdata);

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

$smarty->assign_by_ref('lastVersion',$info["version"]);
$smarty->assign_by_ref('lastModif',$info["lastModif"]);
if(empty($info['user'])) {
    $info['user']=tra('Anonymous');  
}
$smarty->assign_by_ref('lastUser',$info['user']);
$smarty->assign_by_ref('description',$info['description']);

if ( isset($_REQUEST['saved_msg']) && $info['user'] == $user ) {
	// Generate the 'Page has been saved...' message
	require_once('lib/smarty_tiki/modifier.userlink.php');
	$smarty->assign('saved_msg',
		sprintf(
			tra('%s - Version %d of this page has been saved by %s.'),
			TikiLib::date_format($prefs['long_date_format'].' '.$prefs['long_time_format'], $info['lastModif']),
			$info['version'], smarty_modifier_userlink($info['user'])
		)
	);
}

// Comments engine!
if ($prefs['feature_wiki_comments'] == 'y' and $tiki_p_wiki_view_comments == 'y') {
    $comments_per_page = $prefs['wiki_comments_per_page'];
    $thread_sort_mode = $prefs['wiki_comments_default_ordering'];
    $comments_vars=Array('page');
    $comments_prefix_var='wiki page:';
    $comments_object_var='page';
    include_once('comments.php');
}

if($prefs['feature_wiki_attachments'] == 'y') {
    if(isset($_REQUEST['removeattach'])) {
	check_ticket('index');
	$owner = $wikilib->get_attachment_owner($_REQUEST['removeattach']);
	if( ($user && ($owner == $user) ) || ($tiki_p_wiki_admin_attachments == 'y') ) {
		$area = 'removeattach';
	    if ($prefs['feature_ticketlib2'] != 'y' or (isset($_POST['daconfirm']) and isset($_SESSION["ticket_$area"]))) {
			key_check($area);
			$wikilib->remove_wiki_attachment($_REQUEST['removeattach']);
		} else {
			key_get($area);
		}
	}
    $smarty->assign('atts_show', 'y');
    }
    if(isset($_REQUEST['attach']) && ($tiki_p_wiki_admin_attachments == 'y' || $tiki_p_wiki_attach_files == 'y')) {
	check_ticket('index');
	// Process an attachment here
	if(isset($_FILES['userfile1'])&&is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
	    $ret = $tikilib->attach_file($_FILES['userfile1']['name'], $_FILES['userfile1']['tmp_name'], $prefs['w_use_db']== 'y'? 'db': 'dir');	
	    if ($ret['ok']) {
	    	// Set "data" field only if we're using db
	    	if( $prefs['w_use_db'] == 'y' )
		{
		    $wikilib->wiki_attach_file($page, $_FILES['userfile1']['name'], $_FILES['userfile1']['type'], $_FILES['userfile1']['size'], $ret['data'], $_REQUEST['attach_comment'], $user, $ret['fhash']);
		} else {
		    $wikilib->wiki_attach_file($page, $_FILES['userfile1']['name'], $_FILES['userfile1']['type'], $_FILES['userfile1']['size'], '', $_REQUEST['attach_comment'], $user, $ret['fhash']);
		}
	    } else {
				$smarty->assign('msg', $ret['error']);
				$smarty->display('error.tpl');
				die();
		}		
	}
    }

    // If anything below here is changed, please change lib/wiki-plugins/wikiplugin_attach.php as well.
    if (!isset($_REQUEST['sort_mode']))
       $_REQUEST['sort_mode'] = 'created_desc';
    $smarty->assign('sort_mode', $_REQUEST['sort_mode']);
    if (isset($_REQUEST['atts_show']))
	 $smarty->assign('atts_show', $_REQUEST['atts_show']);
    $atts = $wikilib->list_wiki_attachments($page,0,-1, $_REQUEST['sort_mode'],'');
    $smarty->assign('atts',$atts["data"]);
    $smarty->assign('atts_count',count($atts['data']));
}

$smarty->assign('footnote','');
$smarty->assign('has_footnote','n');
if($prefs['feature_wiki_footnotes'] == 'y') {
    if($user) {
	$x = $wikilib->get_footnote($user,$page);
	$footnote=$wikilib->get_footnote($user,$page);
	$smarty->assign('footnote',$tikilib->parse_data($footnote));
	if($footnote) $smarty->assign('has_footnote','y');
    }
}

$smarty->assign('wiki_extras','y');

// Watches
if ($prefs['feature_user_watches'] == 'y') {
	if($user && isset($_REQUEST['watch_event'])) {
		check_ticket('index');
		if (($_REQUEST['watch_action'] == 'add_desc' || $_REQUEST['watch_action'] == 'del_desc') && $tiki_p_watch_structure != 'y') {
			$smarty->assign('errortype', 401);
			$smarty->assign('msg',tra('Permission denied'));
			$smarty->display('error.tpl');
			die;
		}
		if($_REQUEST['watch_action']=='add') {
			$tikilib->add_user_watch($user,$_REQUEST['watch_event'],$_REQUEST['watch_object'],'wiki page',$page,"tiki-index.php?page=$page");
		} elseif($_REQUEST['watch_action'] == 'add_desc') {
			$tikilib->add_user_watch($user,$_REQUEST['watch_event'],$_REQUEST['watch_object'],'structure',$page,"tiki-index.php?page=$page&amp;structure=$struct");
		} else {
			$tikilib->remove_user_watch($user,$_REQUEST['watch_event'],$_REQUEST['watch_object']);
		}
	}
	$smarty->assign('user_watching_page','n');
	$smarty->assign('user_watching_structure','n');
	if ($user) {
		if ($tikilib->user_watches($user, 'wiki_page_changed', $page, 'wiki page')) {
			$smarty->assign('user_watching_page', 'y');
		}
		if (isset($page_info) && $tikilib->user_watches($user, 'structure_changed', $page_info['page_ref_id'], 'structure')) {
			$smarty->assign('user_watching_structure', 'y');
		}
	}
    // Check, if the user is watching this page by a category.    
	if ($prefs['feature_categories'] == 'y') {    
	    $watching_categories_temp=$categlib->get_watching_categories($page,"wiki page",$user);	    
	    $smarty->assign('category_watched','n');
	 	if (count($watching_categories_temp) > 0) {
	 		$smarty->assign('category_watched','y');
	 		$watching_categories=array();	 			 	
	 		foreach ($watching_categories_temp as $wct ) {
	 			$watching_categories[]=array("categId"=>$wct,"name"=>$categlib->get_category_name($wct));
	 		}		 		 	
	 		$smarty->assign('watching_categories', $watching_categories);
	 	}    
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
       $myWiki = new HAWIKI_page($info["data"],"tiki-index.php?mode=mobile&page=");

       $myWiki->set_navlink(tra("Home Page"), "tiki-index.php?mode=mobile", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
       $myWiki->set_navlink(tra("Menu"), "tiki-mobile.php", HAWIKI_NAVLINK_TOP | HAWIKI_NAVLINK_BOTTOM);
       $myWiki->set_smiley_dir("img/smiles");
       $myWiki->set_link_jingle("lib/hawhaw/link.wav");
       $myWiki->set_hawimconv("lib/hawhaw/hawimconv.php");

       $myWiki->display();
       die;
     */
    include_once('lib/hawhaw/hawtikilib.php');
    HAWTIKI_index($info);
}

// Display category path or not (like {catpath()})
$cats = array();
if ($prefs['feature_categories'] == 'y' && $categlib->is_categorized('wiki page',$page)) {
    $smarty->assign('is_categorized','y');
    if ($prefs['feature_categoryobjects'] == 'y' || $prefs['feature_categorypath'] == 'y') {
		$cats = $categlib->get_object_categories('wiki page',$page);
    }
	if ($prefs['feature_categorypath'] == 'y') {	
	    $display_catpath = $categlib->get_categorypath($cats);
	    $smarty->assign('display_catpath',$display_catpath);
	}    
    // Display current category objects or not (like {category()})    
	if ($prefs['feature_categoryobjects'] == 'y') {	    
	    $display_catobjects = $categlib->get_categoryobjects($cats);
	    $smarty->assign('display_catobjects',$display_catobjects);
	}    
} else {
    $smarty->assign('is_categorized','n');
}

if ($prefs['feature_polls'] =='y' and $prefs['feature_wiki_ratings'] == 'y' && $tiki_p_wiki_view_ratings == 'y') {
	function pollnameclean($s) { global $page; if (isset($s['title'])) $s['title'] = substr($s['title'],strlen($page)+2); return $s; }	
	if (!isset($polllib) or !is_object($polllib)) include("lib/polls/polllib_shared.php");
	$ratings = $polllib->get_rating($cat_type,$cat_objid);
	$ratings['info'] = pollnameclean($ratings['info']);
	$smarty->assign('ratings',$ratings);
	if ($user) {
		$user_vote = $tikilib->get_user_vote('poll'.$ratings['info']['pollId'],$user);
		$smarty->assign('user_vote',$user_vote);
	}
							
}

// Flag for 'page bar' that currently 'Page view' mode active
// so it is needed to show comments & attachments panels
$smarty->assign('show_page','y');
ask_ticket('index');

  if (isset($structure) && $structure == 'y' && isset($page_info['page_alias']) && $page_info['page_alias'] != '') {
    $crumbpage = $page_info['page_alias'];
  } else {
    $crumbpage = $page;
  }
  //global $description;
  $crumbs[] = new Breadcrumb($crumbpage,
                              $info['description'],
                              'tiki-index.php?page='.urlencode($page),
                              '',
                              '');

  $headtitle = breadcrumb_buildHeadTitle($crumbs);
  $smarty->assign_by_ref('headtitle', $headtitle);
  $smarty->assign('trail', $crumbs);

//add a hit
$statslib->stats_hit($page,'wiki');
if ($prefs['feature_actionlog'] == 'y') {
	include_once('lib/logs/logslib.php');
	$logslib->add_action('Viewed', $page);
}

if ($prefs['feature_wikiapproval'] == 'y') {
	if ($tikilib->page_exists($prefs['wikiapproval_prefix'] . $page)) {
		$smarty->assign('hasStaging', 'y');
	}
	if ($prefs['wikiapproval_approved_category'] == 0 && $tiki_p_edit == 'y' || $prefs['wikiapproval_approved_category'] > 0 && $categlib->has_edit_permission($user, $prefs['wikiapproval_approved_category'])) {
		$canApproveStaging = 'y';
		$smarty->assign('canApproveStaging', $canApproveStaging);
	}		
	if (substr($page, 0, strlen($prefs['wikiapproval_prefix'])) == $prefs['wikiapproval_prefix']) {
		$approvedPageName = substr($page, strlen($prefs['wikiapproval_prefix']));	
		$smarty->assign('beingStaged', 'y');
		$smarty->assign('approvedPageName', $approvedPageName);	
		$approvedPageExists = $tikilib->page_exists($approvedPageName);
		$smarty->assign('approvedPageExists', $approvedPageExists);
	} elseif ($prefs['wikiapproval_approved_category'] > 0 && in_array($prefs['wikiapproval_approved_category'], $cats)) {
		$stagingPageName = $prefs['wikiapproval_prefix'] . $page;
		$smarty->assign('needsStaging', 'y');
		$smarty->assign('stagingPageName', $stagingPageName);	
		if ($tikilib->user_has_perm_on_object($user,$stagingPageName,'wiki page','tiki_p_edit','tiki_p_edit_categorized')) {
			$smarty->assign('canEditStaging', 'y');
		} 	
	} elseif ($prefs['wikiapproval_staging_category'] > 0 && in_array($prefs['wikiapproval_staging_category'], $cats) && !$tikilib->page_exists($prefs['wikiapproval_prefix'] . $page)) {
		$smarty->assign('needsFirstApproval', 'y');		
	}
	if ($prefs['wikiapproval_outofsync_category'] == 0 || $prefs['wikiapproval_outofsync_category'] > 0 && in_array($prefs['wikiapproval_outofsync_category'], $cats)) {
		if (isset($approvedPageName)) $smarty->assign('outOfSync', 'y');
		if ($canApproveStaging == 'y' && isset($approvedPageName)) {
			include_once('lib/wiki/histlib.php');
			$approvedPageInfo = $histlib->get_page_from_history($approvedPageName, 0);
			if ($approvedPageInfo && $info['lastModif'] > $approvedPageInfo['lastModif']) {
				$lastSyncVersion = $histlib->get_version_by_time($page, $approvedPageInfo['lastModif']);
				// get very first version if unable to get last sync version.
				if ($lastSyncVersion == 0) $lastSyncVersion = $histlib->get_version_by_time($page, 0, 'after');
				// if really not possible, just give up.
				if ($lastSyncVersion > 0) $smarty->assign('lastSyncVersion', $lastSyncVersion );
			}
		}		
	}
}

$ajaxlib->processRequests();

// Detect if we have a PDF export mod installed
$smarty->assign('pdf_export', file_exists('lib/mozilla2ps/mod_urltopdf.php') ? 'y' : 'n');

// Display the Index Template
$smarty->assign('dblclickedit','y');
$smarty->assign('print_page','n');
$smarty->assign('beingEdited','n');
$smarty->assign('mid','tiki-show_page.tpl');
$smarty->assign('categorypath',$prefs['feature_categorypath']);
$smarty->assign('categoryobjects',$prefs['feature_categoryobjects']);
$smarty->assign('feature_wiki_pageid', $prefs['feature_wiki_pageid']);
$smarty->assign('page_id',$info['page_id']);
$smarty->display("tiki.tpl");

// xdebug_dump_function_profile(XDEBUG_PROFILER_CPU);
// debug: print all objects

?>
