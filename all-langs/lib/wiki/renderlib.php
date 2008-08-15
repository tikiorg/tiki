<?php

class WikiRenderer
{
	private $info;
	private $structureInfo;
	private $user;
	private $page;
	private $pageNumber = 1;
	private $sortMode = 'created_desc';
	private $showAttachments = 'n';

	private $hasPermissions;
	private $prep = array(
		'setupStructure',
		'setupContributors',
		'setupCreator',
		'setupMultilingual',
		'setupBacklinks',
		'setupActions',
		'setupSlideshow',
		'setupPage',
		'setupAttachments',
		'setupFootnotes',
		'setupWatch',
		'setupCategories',
		'setupPoll',
		'setupBreadcrumbs',
		'setupStaging',
	);

	private $toRestore = array();
	private $prefRestore = array();

	public $canView = false;
	public $canUndo = false;

	function __construct( $info, $user )
	{
		$this->info = $info;
		$this->user = $user;
		$this->page = $info['pageName'];
	}

	function applyPermissions() // {{{
	{
		global $tiki_p_admin, $tikilib, $userlib, $smarty, $user;

		if ($tiki_p_admin != 'y' && $userlib->object_has_one_permission($this->page, 'wiki page')) {
			$perms = $userlib->get_permissions(0, -1, 'permName_desc', '', 'wiki');
			$this->hasPermissions = true;
			if ($userlib->object_has_permission($this->user, $this->page, 'wiki page', 'tiki_p_admin_wiki')) {
				foreach ($perms["data"] as $perm) {
					$perm = $perm["permName"];

					$this->setGlobal( $perm, 'y' );
				}
			} else {
				foreach ($perms["data"] as $perm) {
					$perm = $perm["permName"];
					$value = $userlib->object_has_permission($this->user, $this->page, 'wiki page', $perm) ? 'y' : 'n';

					$this->setGlobal( $perm, $value );
				}
			}
		} else {
			$this->hasPermissions = false;
		}

		$permissions = $tikilib->get_perm_object( $this->page, 'wiki page', $this->info, false );

		foreach( $permissions as $name => $value )
			$this->setGlobal( $name, $value );

		$this->canView = $GLOBALS['tiki_p_view'] == 'y';

		$smarty->assign('page_user',$this->info['user']);
	} // }}}

	function restoreAll() // {{{
	{
		global $smarty, $prefs;
		foreach( $this->toRestore as $name => $value )
		{
			$GLOBALS[$name] = $value;
			$smarty->assign( $name, $value );
		}

		foreach( $this->prefRestore as $name => $value )
			$prefs[$name] = $value;

	} // }}}

	function runSetups() // {{{
	{
		foreach( $this->prep as $method )
			$this->$method();
	} // }}}

	function setPageNumber( $number ) // {{{
	{
		$this->pageNumber = (int) $number;
	} // }}}

	function setSortMode( $mode ) // {{{
	{
		$this->sortMode = $mode;
	} // }}}

	function setShowAttachments( $val ) // {{{
	{
		$this->showAttachments = $val;
	} // }}}

	function setStructureInfo( $info ) // {{{
	{
		$this->structureInfo = $info;
	} // }}}

	private function setupStructure() // {{{
	{
		if( ! $this->structureInfo )
			return;

		global $smarty, $structlib, $tikilib;

		$structure = 'y';
		$smarty->assign('structure',$structure);
		$smarty->assign('page_info', $this->structureInfo);
		$navigation_info = $structlib->get_navigation_info($this->structureInfo['page_ref_id']);
		$smarty->assign('next_info', $navigation_info['next']);
		$smarty->assign('prev_info', $navigation_info['prev']);
		$smarty->assign('parent_info', $navigation_info['parent']);
		$smarty->assign('home_info', $navigation_info['home']);
		$structure_path = $structlib->get_structure_path($this->structureInfo['page_ref_id']);
		$smarty->assign('structure_path', $structure_path);
		// Need to have showstructs when in more than one struct - for usability reasons 
		$structs = $structlib->get_page_structures($this->page);
		$structs_with_perm = array(); 
		foreach ($structs as $t_structs) {
			if ($tikilib->user_has_perm_on_object($this->user,$t_structs['pageName'],'wiki page','tiki_p_view')) {
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

		$smarty->assign('showstructs', $structs_with_perm);
		$smarty->assign('page_ref_id', $this->structureInfo['page_ref_id']);
	} // }}}

	private function setupContributors() // {{{
	{
		global $prefs, $smarty, $wikilib;

		if( $prefs['wiki_authors_style'] != 'classic' ) {
			$contributors = $wikilib->get_contributors($this->page, $this->info['user']);
			$smarty->assign('contributors',$contributors);
		}
	} // }}}

	private function setupCreator() // {{{
	{
		global $wikilib, $smarty;

		if (isset($this->info['creator'])) {
			$creator = $this->info['creator'];
		} else {
			$creator = $wikilib->get_creator($this->page);
		}

		$smarty->assign('creator',$creator);
	} // }}}

	private function setupMultilingual() // {{{
	{
		global $multilinguallib, $tikilib, $smarty, $prefs;

		if ($prefs['feature_multilingual'] != 'y')
			return;

		include_once('lib/multilingual/multilinguallib.php');

		if( $this->info['lang'] && $this->info['lang'] != 'NULL') { //NULL is a temporary patch
			$trads = $multilinguallib->getTranslations('wiki page', $this->info['page_id'], $this->page, $this->info['lang']);
			$smarty->assign('trads', $trads);
			$pageLang = $this->info['lang'];
			$smarty->assign('pageLang', $pageLang);
		}
		
		$stagingEnabled = (
			$prefs['feature_wikiapproval'] == 'y' 
			&& $tikilib->page_exists($prefs['wikiapproval_prefix'] . $this->page) );

		if ( $stagingEnabled ) {
			// temporary fix: simply use info of staging page to determine critical translation bits
			// TODO: better system of dealing with translation bits with approval		
			$stagingPageId = $tikilib->get_page_id_from_name($prefs['wikiapproval_prefix'] . $this->page);
			$bits = $multilinguallib->getMissingTranslationBits( 'wiki page', $stagingPageId, 'critical', true );	
		} else {
			$bits = $multilinguallib->getMissingTranslationBits( 'wiki page', $this->info['page_id'], 'critical', true );
		}
		
		$alertData = array();
		foreach( $bits as $translationBit ) {
			if ( $stagingEnabled ) {
				$alertData[] = $multilinguallib->getTranslationsWithBit( $translationBit, $stagingPageId );
			} else {
				$alertData[] = $multilinguallib->getTranslationsWithBit( $translationBit, $this->info['page_id'] );
			}
		}

		$smarty->assign( 'translation_alert', $alertData );
	} // }}}

	private function setupBacklinks() // {{{
	{
		global $wikilib, $smarty;

		$backlinks = $wikilib->get_backlinks($this->page);
		$smarty->assign_by_ref('backlinks', $backlinks);
	} // }}}

	private function setupActions() // {{{
	{
		global $wikilib, $smarty, $tiki_p_edit, $tiki_p_remove, $tiki_p_admin_wiki;

		// Verify lock status
		if($wikilib->is_locked($this->page, $this->info)) {
			$smarty->assign('lock',true);  
		} else {
			$smarty->assign('lock',false);
		}

		$smarty->assign('editable', $wikilib->is_editable($this->page, $this->user, $this->info));

		// If not locked and last version is user version then can undo
		$smarty->assign('canundo','n');	
		if(
			$this->info['flag']!='L' 
			&& ( 
				($tiki_p_edit == 'y' && $this->info['user'] == $user) 
				|| ( $tiki_p_remove == 'y' ) 
			) )  {
			$smarty->assign('canundo','y');	
			$this->canUndo = true;
		}
		if($tiki_p_admin_wiki == 'y') {
			$smarty->assign('canundo','y');		
			$this->canUndo = true;
		}

		if(!isset($this->info['is_html'])) {
			$this->info['is_html'] = false;
		}
	} // }}}

	private function setupSlideshow() // {{{
	{
		global $prefs, $smarty;

		if ($prefs['wiki_uses_slides'] != 'y') {
			$smarty->assign('show_slideshow','n');
			return;
		}

		$slides = split("-=[^=]+=-",$this->info['data']);
		if(count($slides)>1) {
			$smarty->assign('show_slideshow','y');
		} else {
			$slides = explode('...page...',$this->info['data']);

			$smarty->assign('show_slideshow', ( count($slides) > 1 ) ? 'y' : 'n' );
		}
	} // }}}

	private function setupPage() // {{{
	{
		global $smarty, $prefs, $tikilib, $wikilib;

		$smarty->assign( 'page', $this->page );
		$smarty->assign('show_page','y');

		$smarty->assign('dblclickedit','y');
		$smarty->assign('print_page','n');
		$smarty->assign('beingEdited','n');
		$smarty->assign('categorypath',$prefs['feature_categorypath']);
		$smarty->assign('categoryobjects',$prefs['feature_categoryobjects']);
		$smarty->assign('feature_wiki_pageid', $prefs['feature_wiki_pageid']);
		$smarty->assign('page_id',$this->info['page_id']);

		// Get the authors style for this page
		$wiki_authors_style = ( $prefs['wiki_authors_style_by_page'] == 'y' && $this->info['wiki_authors_style'] != '' ) ? $this->info['wiki_authors_style'] : $prefs['wiki_authors_style'];
		$smarty->assign('wiki_authors_style', $wiki_authors_style);

		$smarty->assign('cached_page','n');
		$parse_options = array(
			'is_html' => $this->info['is_html'],
			'language' => $this->info['lang']
		);

		if(isset($this->info['wiki_cache'])) {
			$this->setPref( 'wiki_cache', $this->info['wiki_cache'] );
		}

		if($prefs['wiki_cache']>0) {
			$cache_info = $wikilib->get_cache_info($this->page);
			if($cache_info['cache_timestamp']+$prefs['wiki_cache'] > $tikilib->now) {
				$pdata = $cache_info['cache'];
				$smarty->assign('cached_page','y');
			} else {
				$pdata = $tikilib->parse_data($this->info['data'], $parse_options);
				$wikilib->update_cache($this->page,$pdata);
			}
		} else {
			$pdata = $tikilib->parse_data($this->info['data'], $parse_options);
		}

		$smarty->assign_by_ref('parsed',$pdata);

		$pages = $wikilib->get_number_of_pages($pdata);
		$pdata = $wikilib->get_page($pdata,$this->pageNumber);
		$smarty->assign('pages',$pages);

		if($pages>$this->pageNumber) {
			$smarty->assign('next_page',$this->pageNumber+1);
		} else {
			$smarty->assign('next_page',$this->pageNumber);
		}
		if($this->pageNumber>1) {
			$smarty->assign('prev_page',$this->pageNumber-1);
		} else {
			$smarty->assign('prev_page',1);
		}

		$smarty->assign('first_page',1);
		$smarty->assign('last_page',$pages);
		$smarty->assign('pagenum',$this->pageNumber);

		$smarty->assign('lastVersion',$this->info["version"]);
		$smarty->assign('lastModif',$this->info["lastModif"]);
		if(empty($this->info['user'])) {
			$this->info['user']=tra('Anonymous');  
		}
		$smarty->assign_by_ref('lastUser',$this->info['user']);
		$smarty->assign_by_ref('description',$this->info['description']);
	} // }}}

	private function setupAttachments() // {{{
	{
		global $prefs, $wikilib, $smarty;
		if($prefs['feature_wiki_attachments'] != 'y')
			return;

		// If anything below here is changed, please change lib/wiki-plugins/wikiplugin_attach.php as well.
		$smarty->assign('sort_mode', $this->sortMode );
		if( $this->showAttachments !== false )
			$smarty->assign('atts_show', $this->showAttachments);

		$atts = $wikilib->list_wiki_attachments($this->page,0,-1, $this->sortMode,'');
		$smarty->assign('atts',$atts["data"]);
		$smarty->assign('atts_count',count($atts['data']));
	} // }}}

	private function setupFootnotes() // {{{
	{
		global $smarty, $prefs, $wikilib, $tikilib;

		$smarty->assign('footnote','');
		$smarty->assign('has_footnote','n');

		if($prefs['feature_wiki_footnotes'] == 'y') {
			if($this->user) {
				$footnote = $wikilib->get_footnote($this->user,$this->page);
				$smarty->assign('footnote',$tikilib->parse_data($footnote));

				if($footnote)
					$smarty->assign('has_footnote','y');
			}
		}

		$smarty->assign('wiki_extras','y');
	} // }}}

	private function setupWatch() // {{{
	{
		global $prefs, $smarty, $tikilib, $categlib;
		if ($prefs['feature_user_watches'] != 'y')
			return;

		$smarty->assign('user_watching_page','n');
		$smarty->assign('user_watching_structure','n');
		if ($this->user) {
			if ($tikilib->user_watches($this->user, 'wiki_page_changed', $this->page, 'wiki page')) {
				$smarty->assign('user_watching_page', 'y');
			}
			if (isset($this->structureInfo) && $tikilib->user_watches($this->user, 'structure_changed', $this->structureInfo['page_ref_id'], 'structure')) {
				$smarty->assign('user_watching_structure', 'y');
			}
		}
		// Check, if the user is watching this page by a category.    
		if ($prefs['feature_categories'] == 'y') {    
			$watching_categories_temp=$categlib->get_watching_categories($this->page,"wiki page",$this->user);	    
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
	} // }}}

	private function setupCategories() // {{{
	{
		global $prefs, $smarty, $categlib;

		$cats = array();
		if ($prefs['feature_categories'] == 'y' && $categlib->is_categorized('wiki page',$this->page)) {
			$smarty->assign('is_categorized','y');
			if ($prefs['feature_categoryobjects'] == 'y' || $prefs['feature_categorypath'] == 'y') {
				$cats = $categlib->get_object_categories('wiki page',$this->page);
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
	} // }}}

	private function setupPoll() // {{{
	{
		global $prefs, $smarty, $polllib, $tikilib, $tiki_p_view_ratings;

		if ($prefs['feature_polls'] !='y' || $prefs['feature_wiki_ratings'] != 'y' || $tiki_p_wiki_view_ratings != 'y')
			return;

		if( ! function_exists( 'pollnameclean' ) ) {
			function pollnameclean($s, $page) {
				if (isset($s['title'])) 
					$s['title'] = substr($s['title'], strlen($page)+2); 

				return $s;
			}	
		}

		if (!isset($polllib) || !is_object($polllib)) include("lib/polls/polllib_shared.php");
		$ratings = $polllib->get_rating('wiki page',$this->page);
		$ratings['info'] = pollnameclean($ratings['info'], $this->page);
		$smarty->assign('ratings',$ratings);
		if ($this->user) {
			$user_vote = $tikilib->get_user_vote('poll'.$ratings['info']['pollId'],$this->user);
			$smarty->assign('user_vote',$user_vote);
		}
	} // }}}

	private function setupBreadcrumbs() // {{{
	{
		global $smarty, $prefs;
		if( $prefs['feature_breadcrumbs'] != 'y' ) {
			return;
		}

		if ($this->structureInfo && $this->structureInfo['page_alias'] != '') {
			$crumbpage = $this->structureInfo['page_alias'];
		} else {
			$crumbpage = $this->page;
		}
		//global $description;
		$crumbs[] = new Breadcrumb($crumbpage,
				$this->info['description'],
				'tiki-index.php?page='.urlencode($this->page),
				'',
				'');

		$headtitle = breadcrumb_buildHeadTitle($crumbs);
		$smarty->assign('headtitle', $headtitle);
		$smarty->assign('trail', $crumbs);
	} // }}}

	private function setupStaging() // {{{
	{
		global $smarty, $prefs, $tikilib, $categlib, $histlib, $tiki_p_edit;

		if ($prefs['feature_wikiapproval'] != 'y')
			return;

		if ($tikilib->page_exists($prefs['wikiapproval_prefix'] . $this->page)) {
			$smarty->assign('hasStaging', 'y');
		}
		if ($prefs['wikiapproval_approved_category'] == 0 && $tiki_p_edit == 'y' || $prefs['wikiapproval_approved_category'] > 0 && $categlib->has_edit_permission($this->user, $prefs['wikiapproval_approved_category'])) {
			$canApproveStaging = 'y';
			$smarty->assign('canApproveStaging', $canApproveStaging);
		}		
		if (substr($this->page, 0, strlen($prefs['wikiapproval_prefix'])) == $prefs['wikiapproval_prefix']) {
			$approvedPageName = substr($this->page, strlen($prefs['wikiapproval_prefix']));	
			$smarty->assign('beingStaged', 'y');
			$smarty->assign('approvedPageName', $approvedPageName);	
			$approvedPageExists = $tikilib->page_exists($approvedPageName);
			$smarty->assign('approvedPageExists', $approvedPageExists);
		} elseif ($prefs['wikiapproval_approved_category'] > 0 && in_array($prefs['wikiapproval_approved_category'], $cats)) {
			$stagingPageName = $prefs['wikiapproval_prefix'] . $this->page;
			$smarty->assign('needsStaging', 'y');
			$smarty->assign('stagingPageName', $stagingPageName);	
			if ($tikilib->user_has_perm_on_object($this->user,$stagingPageName,'wiki page','tiki_p_edit','tiki_p_edit_categorized')) {
				$smarty->assign('canEditStaging', 'y');
			} 	
		} elseif ($prefs['wikiapproval_staging_category'] > 0 && in_array($prefs['wikiapproval_staging_category'], $cats) && !$tikilib->page_exists($prefs['wikiapproval_prefix'] . $this->page)) {
			$smarty->assign('needsFirstApproval', 'y');		
		}
		if ($prefs['wikiapproval_outofsync_category'] == 0 || $prefs['wikiapproval_outofsync_category'] > 0 && in_array($prefs['wikiapproval_outofsync_category'], $cats)) {
			if (isset($approvedPageName)) $smarty->assign('outOfSync', 'y');
			if ($canApproveStaging == 'y' && isset($approvedPageName)) {
				include_once('lib/wiki/histlib.php');
				$approvedPageInfo = $histlib->get_page_from_history($approvedPageName, 0);
				if ($approvedPageInfo && $this->info['lastModif'] > $approvedPageInfo['lastModif']) {
					$lastSyncVersion = $histlib->get_version_by_time($this->page, $approvedPageInfo['lastModif']);
					// get very first version if unable to get last sync version.
					if ($lastSyncVersion == 0) $lastSyncVersion = $histlib->get_version_by_time($this->page, 0, 'after');
					// if really not possible, just give up.
					if ($lastSyncVersion > 0) $smarty->assign('lastSyncVersion', $lastSyncVersion );
				}
			}		
		}
	} // }}}

	private function setGlobal( $name, $value ) // {{{
	{
		global $smarty;
		if( $GLOBALS[$name] != $value && ! array_key_exists( $name, $this->toRestore ) )
			$this->toRestore[$name] = $value;

		$GLOBALS[$name] = $value;
		$smarty->assign( $name, $value );
	} // }}}

	private function setPref( $name, $value ) // {{{
	{
		global $prefs;

		if( $value != $prefs[$name] && ! array_key_exists( $this->prefRestore[$name] = $value ) )
			$this->prefRestore[$name] = $value;

		$prefs[$name] = $value;
	} // }}}
}

?>
