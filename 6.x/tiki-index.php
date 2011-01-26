<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
	array( 'staticKeyFilters' => array(
		'action' => 'word',
		//'attach_comment' => '', TODO
		// 'atts_show' => '', TODO
		'best_lang' => 'alpha',
		'bl' => 'alpha',
		//'copyrightpage' => '', TODO
		'page' => 'pagename',
		'page_id' => 'digits',
		'pagenum' => 'digits',
		'page_ref_id' => 'digits',
		'mode' => 'word',
		'removeattach' => 'digits',
		'sort_mode' => 'word',
		//'structure' => '', TODO
		'watch_action' => 'word',
		'watch_event' => 'word',
		//'watch_object' => 'word', TODO
	) ),
);

// Initialization
$section = 'wiki page';
$isHomePage = (!isset($_REQUEST['page']));
require_once('tiki-setup.php');
require_once('lib/multilingual/multilinguallib.php');
if( $prefs['feature_wiki_structure'] == 'y' ) {
	include_once('lib/structures/structlib.php');
}
include_once('lib/wiki/wikilib.php');
include_once('lib/stats/statslib.php');
if ($prefs['feature_ajax'] === 'y') {
	if ($prefs['ajax_xajax'] === 'y') {
		require_once ("lib/ajax/ajaxlib.php");
		if ($prefs['feature_wiki_save_draft'] === 'y') {
			require_once ("lib/wiki/wiki-ajax.php");
		}
	}
}
require_once ("lib/wiki/renderlib.php");

$auto_query_args = array('page','no_bl','page_id','pagenum','page_ref_id','mode','sort_mode',
                         'machine_translate_to_lang');

if ($prefs['feature_categories'] == 'y') {
	global $categlib;
	if (!is_object($categlib)) {
		include_once('lib/categories/categlib.php');
	}
}

if (!empty($_REQUEST['machine_translate_to_lang'])) {
	$smarty->assign('machine_translate_to_lang', $_REQUEST['machine_translate_to_lang']);
}

$access->check_feature( 'feature_wiki' );

if(!isset($_SESSION['thedate'])) {
    $thedate = $tikilib->now;
} else {
    $thedate = $_SESSION['thedate'];
}

// Check if a WS is active
global $perspectivelib; require_once 'lib/perspectivelib.php';
$activeWS = $perspectivelib->get_current_perspective(null);
// If there's a WS active and the WS has a homepage, then load the WS homepage
if ((!empty($activeWS)) and $isHomePage)
{
	$preferences = $perspectivelib->get_preferences($activeWS);
	if (!empty($preferences['wsHomepage']))
		$_REQUEST['page'] = $preferences['wsHomepage'];
}

// If a page have been requested, then show the page.
if (isset($_REQUEST['page_id'])) {
    $_REQUEST['page'] = $tikilib->get_page_name_from_id($_REQUEST['page_id']);
    //TODO: introduce a get_info_from_id to save a sql request
}

if ((!isset($_REQUEST['page']) || $_REQUEST['page'] == '') and !isset($_REQUEST['page_ref_id'])) {
	if ($objectperms->view) {
		$access->display_error( $page, tra('You do not have permission to view this page.'), '401');
	} else {
		$access->display_error( '', tra('No name indicated for wiki page'));
	}
}

$use_best_language = $multilinguallib->useBestLanguage();

$info = null;

$structs_with_perm = array(); 
if( $prefs['feature_wiki_structure'] == 'y' ) {
	$structure = 'n';
	$smarty->assign('structure',$structure);
	// Feature checks made in the function for structure language
	if (!$use_best_language) {
		$info = $tikilib->get_page_info($_REQUEST["page"]);
		$langContext = $info['lang'];
	} else {
		$langContext = null;
	}
	$structlib->use_user_language_preferences( $langContext );
	
	if (isset($_REQUEST['page_ref_id'])) {
		// If a structure page has been requested
		$page_ref_id = $_REQUEST['page_ref_id'];
	} else {
		// else check if page is the head of a structure 
		$page_ref_id = $structlib->get_struct_ref_if_head($_REQUEST['page']);
	}

	//If a structure page isnt going to be displayed
	if (empty($page_ref_id)) {
		//Check to see if its a member of any structures
		if (isset($_REQUEST['structure']) && !empty($_REQUEST['structure'])) {
			$struct=$_REQUEST['structure'];
		} else {
			$struct='';
		}
		//Get the structures this page is a member of
		$structs = $structlib->get_page_structures($_REQUEST["page"],$struct);
		$structs_with_perm = Perms::filter( array( 'type' => 'wiki page' ), 'object', $structs, array( 'object' => 'permName' ), 'view' );

		//If page is only member of one structure, display if requested
		$single_struct = count($structs_with_perm) == 1; 
		if ((!empty($struct) || $prefs['feature_wiki_open_as_structure'] == 'y') && $single_struct) {
			$page_ref_id=$structs_with_perm[0]['req_page_ref_id'];
			$_REQUEST['page_ref_id']=$page_ref_id;
		}

	}
} elseif (!empty($_REQUEST['page_ref_id'])) {
	$smarty->assign('msg', tra('This feature is disabled').': feature_wiki_structure');
	$smarty->display('error.tpl');
	die;
}

if (!empty($page_ref_id)) {
    $page_info = $structlib->s_get_page_info($page_ref_id);
    
    $info = null;
    // others still need a good set page name or they will get confused.
    // comments of home page were all visible on every structure page
    $_REQUEST['page'] = $page_info['pageName'];
} else {
    $page_ref_id = '';
	$smarty->assign('showstructs', $structs_with_perm);
	$smarty->assign('page_ref_id', $page_ref_id);
}

$page = $_REQUEST['page'];
$smarty->assign_by_ref('page',$page);

if ( function_exists('utf8_encode') ) {
	$pagename_utf8 = utf8_encode($page);
	if ( $page != $pagename_utf8 && ! $tikilib->page_exists($page) && $tikilib->page_exists($pagename_utf8) ) {
		$page = $_REQUEST["page"] = $pagename_utf8;
	}
}



// Get page data, if available
if (!$info) {
	$info = $tikilib->get_page_info($page);
}
	
// If the page doesn't exist then display an error
if(empty($info) && !($user && $prefs['feature_wiki_userpage'] == 'y' && strcasecmp($prefs['feature_wiki_userpage_prefix'].$user, $page) == 0)) {
	if (!empty($prefs['url_anonymous_page_not_found']) && empty($user)) {
		$access->redirect($prefs['url_anonymous_page_not_found']);
	}
	if ($user && $prefs['feature_wiki_userpage'] == 'y' && strcasecmp($prefs['feature_wiki_userpage_prefix'], $page) == 0) {
		$url = 'tiki-index.php?page='.$prefs['feature_wiki_userpage_prefix'].$user;
		if ($prefs['feature_sefurl'] == 'y') {
			include_once('tiki-sefurl.php');
			header('location: '. urlencode(filter_out_sefurl($url, $smarty, 'wiki')));
		} else {
			header("Location: $url");
		}
		die;
	}
	if ($prefs['feature_wiki_userpage'] == 'y' && strcasecmp($prefs['feature_wiki_userpage_prefix'], substr($page, 0, strlen($prefs['feature_wiki_userpage_prefix']))) == 0)
		$isUserPage = true;
	else
		$isUserPage = false;
	$likepages = $wikilib->get_like_pages($page);
	/* if we have exactly one match, redirect to it */
	if($prefs['feature_wiki_1like_redirection'] == 'y' && count($likepages) == 1  && !$isUserPage) {
		if ($prefs['feature_sefurl'] == 'y') {
			$url = $wikilib->sefurl($likepages[0]);
		} else {
			$url = 'tiki-index.php?page='.urlencode($likepages[0]);
		}
		// Process prefix alias with itemId append for pretty tracker pages
		$prefixes = explode( ',', $prefs["wiki_prefixalias_tokens"]);
		foreach ($prefixes as $p) {
			$p = trim($p);
			if (strlen($p) > 0 && strtolower(substr($page, 0, strlen($p))) == strtolower($p)) {
				$suffix = trim(substr($page, strlen($p)));
				if (!ctype_digit($suffix) && $suffix) {
					// allow escaped numerics as text
					$suffix = stripslashes($suffix);
					global $semanticlib;
					if (!is_object($semanticlib)) {
						require_once 'lib/wiki/semanticlib.php';		
					}
					$items = $semanticlib->getItemsFromTracker($likepages[0], $suffix);
					if (count($items) > 1) {
						$msg = tra("There is more than one item in the tracker with this title");
						foreach ($items as $i) {
							$msg .= '<br /><a href="tiki-index.php?page=' . urlencode($likepages[0]) . '&itemId=' . $i . '">' . $i . '</a>';
						}
						$smarty->assign('msg', $msg);
						$smarty->display('error.tpl');
						die;
					} else if (count($items)) {
						$suffix = $items[0];
					} else {
						$msg = tra("There are no items in the tracker with this title");
						$smarty->assign('msg', $msg);
						$smarty->display('error.tpl');
						die;
					}
				}
				if (ctype_digit($suffix)) {
					if ($prefs['feature_sefurl'] == 'y') {
						$url = $url . "?itemId=" . $suffix;
					} else {
						$url = $url . "&itemId=" . $suffix;
					}
				}
			}
		}
		$access->redirect( $url );
	}
	$smarty->assign_by_ref('likepages', $likepages);
	$smarty->assign('create', $isUserPage? 'n': 'y');
	$access->display_error( $page, tra('Page cannot be found'), '404' );
}


if (empty($info) && $user && $prefs['feature_wiki_userpage'] == 'y' && (strcasecmp($prefs['feature_wiki_userpage_prefix'].$user, $page) == 0 || strcasecmp($prefs['feature_wiki_userpage_prefix'], $page) == 0 )) {	
	
	header('Location: tiki-editpage.php?page='.$prefs['feature_wiki_userpage_prefix'].$user);
    	die;
}
if ($prefs['feature_multilingual'] == 'y' && $prefs['feature_sync_language'] == 'y' && !empty($info['lang'])) {
	$_SESSION['s_prefs']['language'] = $info['lang'];
	$prefs['language'] = $info['lang'];
}

$page = $info['pageName'];

//Uncomment if we decide to translate wiki markup. For now we are going 
//with translating rendered html content
//$translatedWikiMarkup = '';
//if (isset($_REQUEST['machine_translate_to_lang'])) {
//	$translatedWikiMarkup = generate_machine_translated_markup($info, $_REQUEST['machine_translate_to_lang']);
//} 

$pageRenderer = new WikiRenderer( $info, $user);
$objectperms = $pageRenderer->applyPermissions();

if ($prefs['feature_wiki_comments'] == 'y' and $objectperms->wiki_view_comments ) {
    $comments_per_page = $prefs['wiki_comments_per_page'];
    $thread_sort_mode = $prefs['wiki_comments_default_ordering'];
    $comments_vars=Array('page');
    $comments_prefix_var='wiki page:';
    $comments_object_var='page';
    include_once('comments.php');
}

require_once 'lib/cache/pagecache.php';
$pageCache = Tiki_PageCache::create()
	->disableForRegistered()
	->onlyForGet()
	->requiresPreference( 'memcache_wiki_output' )
	->addValue( 'role', 'wiki-page-output' )
	->addValue( 'page', $page )
	->addValue( 'locale', $prefs['language'] )
	->addKeys( $_REQUEST, array( 'style_mode' ) )
	->checkMeta( 'wiki-page-output-meta-timestamp', array(
		'page' => $page,
	) )
	->applyCache();

if( $page_ref_id )
	$pageRenderer->setStructureInfo( $page_info );

// Now check permissions to access this page
if( ! $pageRenderer->canView ) {
	$access->display_error( $page, tra('You do not have permission to view this page.'), '401');
}

// Convert page to structure
if (isset($_REQUEST['convertstructure']) && isset($structs) && count($structs) == 0) {
	$page_ref_id = $structlib->s_create_page(0, null, $page);
	header('Location: tiki-index.php?page_ref_id='.$page_ref_id );
	exit;
}

if(isset($_REQUEST['copyrightpage'])) {
  $smarty->assign_by_ref('copyrightpage',$_REQUEST['copyrightpage']); 
}

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


// Now increment page hits since we are visiting this page
if($prefs['count_admin_pvs'] == 'y' || $user!='admin') {
    $tikilib->add_hit($page);
}

// Check if we have to perform an action for this page
// for example lock/unlock
if ( 
	$objectperms->admin_wiki
	|| 
	($user and $objectperms->lock and ($prefs['feature_wiki_usrlock'] == 'y'))
) {
	if ( isset($_REQUEST['action']) ) {
		check_ticket('index');
		if ( $_REQUEST['action'] == 'lock' ) {
			$wikilib->lock_page($page);
			$pageRenderer->setInfo('flag', 'L');
			$info['flag'] = 'L';
		}  
	}
}

if ( 
	$objectperms->admin_wiki
	|| 
	($user and ($user == $info['user']) and $objectperms->lock and ($prefs['feature_wiki_usrlock'] == 'y'))
) {
	if ( isset($_REQUEST['action']) ) {
		check_ticket('index');
		if ( $_REQUEST['action'] == 'unlock' ) {
			$wikilib->unlock_page($page);
			$pageRenderer->setInfo('flag', 'U');
			$info['flag'] = 'U';
		}  
	}
}


// Save to notepad if user wants to
if($user 
	&& $objectperms->notepad
	&& $prefs['feature_notepad'] == 'y' 
	&& isset($_REQUEST['savenotepad'])) {
    check_ticket('index');
    $tikilib->replace_note($user,0,$page,$info['data']);
}

// Process an undo here
if ( isset($_REQUEST['undo']) ) {
	if ( $pageRenderer->canUndo() ) {
		$access->check_authenticity();

		// Remove the last version	
		$wikilib->remove_last_version($page);

		// If page was deleted then re-create
		if ( ! $tikilib->page_exists($page) ) {
			$tikilib->create_page($page, 0, '', $tikilib->now, 'Tiki initialization'); 
		}

		// Restore page information
		$info = $tikilib->get_page_info($page);
		$pageRenderer->setInfos($info);
	}	
}

if(isset($_REQUEST['refresh'])) {
    check_ticket('index');
    $tikilib->invalidate_cache($page);	
}

$cat_type = 'wiki page';
$cat_objid = $page;
include_once('tiki-section_options.php');

if( isset( $_REQUEST['pagenum'] ) && $_REQUEST['pagenum'] > 0 ) {
	$pageRenderer->setPageNumber( (int) $_REQUEST['pagenum'] );
}

if (isset($_SESSION['saved_msg']) && $_SESSION['saved_msg'] == $info['pageName'] && $info['user'] == $user ) {
	// Generate the 'Page has been saved...' message
	require_once('lib/smarty_tiki/modifier.userlink.php');
	$smarty->assign('saved_msg', sprintf( tra('Page saved (version %d).'), $info['version'] ) );
	unset($_SESSION['saved_msg']);
}

if($prefs['feature_wiki_attachments'] == 'y') {
    if(isset($_REQUEST['removeattach'])) {
	check_ticket('index');
	$owner = $wikilib->get_attachment_owner($_REQUEST['removeattach']);
	if( ($user && ($owner == $user) ) || $objectperms->wiki_admin_attachments ) {
		$access->check_authenticity();
		$wikilib->remove_wiki_attachment($_REQUEST['removeattach']);
	}
	$pageRenderer->setShowAttachments( 'y' );
    }
    if(isset($_REQUEST['attach']) && ( $objectperms->wiki_admin_attachments || $objectperms->wiki_attach_files )) {
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
			$access->display_error( '', $ret['error'] );
		}		
	}
    }

	if( isset( $_REQUEST['sort_mode'] ) )
		$pageRenderer->setSortMode( $_REQUEST['sort_mode'] );
	if( isset( $_REQUEST['atts_show'] ) )
		$pageRenderer->setShowAttachments( $_REQUEST['atts_show'] );
}

// Watches
if ($prefs['feature_user_watches'] == 'y') {
	if($user && isset($_REQUEST['watch_event']) && !isset($_REQUEST['watch_group'])) {
		check_ticket('index');
		if (($_REQUEST['watch_action'] == 'add_desc' || $_REQUEST['watch_action'] == 'remove_desc') && $objectperms->watch_structure ) {
			$access->display_error( $page, tra('Permission denied'), '403');
		}
		if($_REQUEST['watch_action']=='add') {
			$tikilib->add_user_watch($user,$_REQUEST['watch_event'],$_REQUEST['watch_object'],'wiki page',$page,"tiki-index.php?page=$page");
		} elseif($_REQUEST['watch_action'] == 'add_desc') {
			$tikilib->add_user_watch($user,$_REQUEST['watch_event'],$_REQUEST['watch_object'],'structure',$page,"tiki-index.php?page=$page&amp;structure=".$_REQUEST['structure']);
		} elseif($_REQUEST['watch_action'] == 'remove_desc') {
			$tikilib->remove_user_watch($user,$_REQUEST['watch_event'],$_REQUEST['watch_object'],'structure');
		} else {
			$tikilib->remove_user_watch($user,$_REQUEST['watch_event'],$_REQUEST['watch_object']);
		}
	}
}

$sameurl_elements=Array('pageName','page');

if ($prefs['feature_mobile'] == 'y') {
	if(isset($_REQUEST['mode']) && $_REQUEST['mode']=='mobile') {
	include_once('lib/hawhaw/hawtikilib.php');
	HAWTIKI_index($info);
	}
}

ask_ticket('index');

//add a hit
$statslib->stats_hit($page,'wiki');
if ($prefs['feature_actionlog'] == 'y') {
	$logslib->add_action('Viewed', $page);
}

// Detect if we have a PDF export mod installed
$smarty->assign('pdf_export', ($prefs['print_pdf_from_url'] != 'none') ? 'y' : 'n');

// Display the Index Template
$pageRenderer->runSetups();

//TRANSLATING HTML
$page_content = $smarty->get_template_vars('parsed');
if (!empty($_REQUEST['machine_translate_to_lang'])) {
	$page_content = generate_machine_translated_content($page_content, $info, $_REQUEST['machine_translate_to_lang']);
	$smarty->assign('parsed',$page_content);
} 

$smarty->assign('mid','tiki-show_page.tpl');

$smarty->display("tiki.tpl");

// xdebug_dump_function_profile(XDEBUG_PROFILER_CPU);
// debug: print all objects


function generate_machine_translated_markup($pageInfo, $targetLang) {	
	make_sure_machine_translation_is_enabled();	
	$pageContent = $pageInfo['data'];
	$sourceLang = $pageInfo['lang'];
	return translate_text($pageContent, $sourceLang, $targetLang);
}

function generate_machine_translated_content($pageContent, $pageInfo, $targetLang) {	
	make_sure_machine_translation_is_enabled();	
	$sourceLang = $pageInfo['lang'];	
	return translate_text($pageContent, $sourceLang, $targetLang, true);
}


function translate_text($text, $sourceLang, $targetLang, $html) {
	require_once('lib/core/Multilingual/MachineTranslation/GoogleTranslateWrapper.php');
	$translator = new Multilingual_MachineTranslation_GoogleTranslateWrapper($sourceLang,$targetLang,$html);
	$translatedText = $translator->translateText($text);
	return $translatedText;	
	
}

function make_sure_machine_translation_is_enabled() {
	global $multilinguallib, $access, $_REQUEST, $prefs;
	if ($prefs['feature_machine_translation'] != 'y') {
		require_once('lib/tikiaccesslib.php');	
		$error_msg = tra("You have requested that this page be machine translated:").
		             " <b>".$_REQUEST['page']."</b><p>".
		             tra("However, the Machine Translation feature is not enabled. Please enable this feature, or ask a site admin to do it.");
		$access->display_error($_REQUEST['page'], "Cannot machine translate this page", "", true, $error_msg);
	}
}
