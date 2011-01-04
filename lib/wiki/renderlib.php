<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiRenderer
{
	private $info;
	private $structureInfo;
	private $user;
	private $page;
	
	// If you want to render some wiki markup that is not actually contained
	// in a page, then set this to the markup to be rendered.
	private $content_to_render;
	
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
	private $smartyRestore = array();

	public $canView = false;
	public $canUndo = null;
	public $trads = null;	// translated pages

	function __construct( $info, $user, $content_to_render='')
	{
		$this->info = $info;
		$this->user = $user;
		$this->page = $info['pageName'];
		$this->content_to_render = $content_to_render;
	}

	function applyPermissions() // {{{
	{
		global $userlib;
		$permDescs = $userlib->get_permissions( 0, -1, 'permName_desc', '', 'wiki' );
		$objectperms = Perms::get( array( 'type' => 'wiki page', 'object' => $this->page ) );

		$objectperms = $this->applyLocalPerms($objectperms, $permDescs);
		
		foreach( $permDescs['data'] as $name ) {
			$name = $name['permName'];
			$this->setGlobal( $name, $objectperms->$name ? 'y' : 'n' );
		}

		$this->canView = $objectperms->view;

		$this->smartyassign('page_user',$this->info['user']);

		return $objectperms;
	} // }}}

	function applyLocalPerms($objectperms, $permDescs) // {{{
	{
		// This function is a kludge until a better more generic solution is found for "user specific" checking perms
		global $prefs;
		if ( $prefs['wiki_creator_admin'] == 'y' && !empty($this->user) && $this->info['creator'] == $this->user ) {
			// to give all perms
			foreach( $permDescs['data'] as $name ) {
				$name = $name["permName"];
				$shortname = str_replace('tiki_p_', '', $name);
				$objectperms->$name = 1;
				$objectperms->$shortname = 1;
			}
		}
		if ($prefs['feature_wiki_userpage'] == 'y' && !empty($this->user) && strcasecmp($prefs['feature_wiki_userpage_prefix'], substr($this->page, 0, strlen($prefs['feature_wiki_userpage_prefix']))) == 0) {
			if (strcasecmp($this->page, $prefs['feature_wiki_userpage_prefix'].$this->user) == 0) {
				// user can edit his page
				// to give view and edit perms
				$objectperms->view = 1;
				$objectperms->tiki_p_view = 1;
				$objectperms->edit = 1;
				$objectperms->tiki_p_edit = 1;
			} else {
				// user cannot edit
				$objectperms->edit = 0;
				$objectperms->tiki_p_edit = 0;
			}
		}
		return $objectperms;
	} // }}}
	
	function restoreAll() // {{{
	{
		global $smarty, $prefs;
		foreach( $this->toRestore as $name => $value )
		{
			$GLOBALS[$name] = $value;
		}

		foreach( $this->prefRestore as $name => $value )
			$prefs[$name] = $value;

		foreach( $this->smartyRestore as $name => $value )
			$smarty->assign( $name, $value );
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

		global $structlib, $tikilib;

		$structure = 'y';
		$this->smartyassign('structure',$structure);
		$this->smartyassign('page_info', $this->structureInfo);
		$navigation_info = $structlib->get_navigation_info($this->structureInfo['page_ref_id']);
		$this->smartyassign('next_info', $navigation_info['next']);
		$this->smartyassign('prev_info', $navigation_info['prev']);
		$this->smartyassign('parent_info', $navigation_info['parent']);
		$this->smartyassign('home_info', $navigation_info['home']);
		$structure_path = $structlib->get_structure_path($this->structureInfo['page_ref_id']);
		$this->smartyassign('structure_path', $structure_path);
		// Need to have showstructs when in more than one struct - for usability reasons 
		$structs = $structlib->get_page_structures($this->page);
		$structs_with_perm = array(); 
		foreach ($structs as $t_structs) {
			if ($tikilib->user_has_perm_on_object($this->user,$t_structs['pageName'],'wiki page','tiki_p_view')) {
				$structs_with_perm[] = $t_structs;
			}
		}    	
		if ($tikilib->user_has_perm_on_object($this->user,$navigation_info['home']['pageName'],'wiki page','tiki_p_edit','tiki_p_edit_structures'))
			$this->smartyassign('struct_editable', 'y');
		else
			$this->smartyassign('struct_editable', 'n');	
		// To show position    
		if (count($structure_path) > 1) {
			$cur_pos = '';
			for ($i = 1, $count_str_path = count($structure_path); $i < $count_str_path; $i++) {
				$cur_pos .= $structure_path[$i]["pos"] . "." ;
			}
			$cur_pos = substr($cur_pos, 0, strlen($cur_pos)-1);      
		} else {
			$cur_pos = tra("Top");
		}
		$this->smartyassign('cur_pos', $cur_pos);	

		$this->smartyassign('showstructs', $structs_with_perm);
		$this->smartyassign('page_ref_id', $this->structureInfo['page_ref_id']);
	} // }}}

	private function setupContributors() // {{{
	{
		global $prefs, $wikilib;

		if( $prefs['wiki_authors_style'] != 'classic' ) {
			$contributors = $wikilib->get_contributors($this->page, $this->info['user'], false);
			$this->smartyassign('contributors',$contributors);
		}
	} // }}}

	private function setupCreator() // {{{
	{
		global $wikilib;

		if (isset($this->info['creator'])) {
			$creator = $this->info['creator'];
		} else {
			$creator = $wikilib->get_creator($this->page);
		}

		$this->smartyassign('creator',$creator);
	} // }}}

	private function setupMultilingual() // {{{
	{
		global $multilinguallib, $tikilib, $prefs;

		if ($prefs['feature_multilingual'] != 'y')
			return;

		include_once('lib/multilingual/multilinguallib.php');
		require_once('lib/core/Multilingual/MachineTranslation/GoogleTranslateWrapper.php');
		
		if( !empty($this->info['lang'])) { 
			$this->trads = $multilinguallib->getTranslations('wiki page', $this->info['page_id'], $this->page, $this->info['lang']);
			$this->smartyassign('trads', $this->trads);
			$this->smartyassign('translationsCount', count($this->trads));
			$pageLang = $this->info['lang'];
			$this->smartyassign('pageLang', $pageLang);
		}
		
		if ($prefs['feature_machine_translation'] == 'y' && !empty($this->info['lang'])) {
			$translator = new Multilingual_MachineTranslation_GoogleTranslateWrapper($this->info['lang'], $this->info['lang']);
			$langsCandidatesForMachineTranslation = $translator->getLangsCandidatesForMachineTranslation($this->trads);
			$this->smartyassign('langsCandidatesForMachineTranslation', $langsCandidatesForMachineTranslation);
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

		$this->smartyassign( 'translation_alert', $alertData );
	} // }}}

	private function setupBacklinks() // {{{
	{
		global $prefs, $wikilib, $tiki_p_view_backlink;

		if ( $prefs['feature_backlinks'] == 'y' && $tiki_p_view_backlink == 'y') {
			$backlinks = $wikilib->get_backlinks($this->page);
			$this->smartyassign('backlinks', $backlinks);
		}
	} // }}}

	private function setupActions() // {{{
	{
		global $prefs, $wikilib, $tiki_p_edit, $tiki_p_remove, $tiki_p_admin_wiki;

		// Verify lock status
		if ( $prefs['feature_wiki_usrlock'] == 'y' ) {
			if ( $wikilib->is_locked($this->page, $this->info) ) {
				$this->smartyassign('lock', true);  
			} else {
				$this->smartyassign('lock', false);
			}
		}

		$this->smartyassign('editable', $wikilib->is_editable($this->page, $this->user, $this->info));

		// If not locked and last version is user version then can undo
		$this->smartyassign('canundo', $this->canUndo() ? 'y' : 'n');

		if(!isset($this->info['is_html'])) {
			$this->info['is_html'] = false;
		}
	} // }}}

	private function setupSlideshow() // {{{
	{
		global $prefs;

		if ($prefs['wiki_uses_slides'] != 'y') {
			$this->smartyassign('show_slideshow','n');
			return;
		}

		//Let us check if slides exist in the wiki page
		$slides = preg_split('/-=[^=]+=-|![^=]+|!![^=]+!!![^=]+/',$this->info['data']);
		
		if(count($slides)>1) {
			$this->smartyassign('show_slideshow','y');
		} else {
			$slides = explode('...page...',$this->info['data']);

			$this->smartyassign('show_slideshow', ( count($slides) > 1 ) ? 'y' : 'n' );
		}
	} // }}}

	private function setupPage() // {{{
	{
		global $prefs, $tikilib, $wikilib, $user;

		$this->smartyassign( 'page', $this->page );
		$this->smartyassign('show_page','y');

		$this->smartyassign('dblclickedit','y');
		$this->smartyassign('print_page','n');
		$this->smartyassign('beingEdited','n');
		$this->smartyassign('categorypath',$prefs['feature_categorypath']);
		$this->smartyassign('categoryobjects',$prefs['feature_categoryobjects']);
		$this->smartyassign('feature_wiki_pageid', $prefs['feature_wiki_pageid']);
		$this->smartyassign('page_id',$this->info['page_id']);

		// Get the authors style for this page
		$wiki_authors_style = ( $prefs['wiki_authors_style_by_page'] == 'y' && $this->info['wiki_authors_style'] != '' ) ? $this->info['wiki_authors_style'] : $prefs['wiki_authors_style'];
		$this->smartyassign('wiki_authors_style', $wiki_authors_style);

		$this->smartyassign('cached_page','n');

		if ($prefs['flaggedrev_approval'] == 'y') {
			global $flaggedrevisionlib; require_once 'lib/wiki/flaggedrevisionlib.php';

			if ($flaggedrevisionlib->page_requires_approval($this->page)) {
				$this->smartyassign('revision_approval', true);

				if ($version_info = $flaggedrevisionlib->get_version_with($this->page, 'moderation', 'OK')) {
					$this->smartyassign('revision_approved', $version_info['version']);
					if (empty($this->content_to_render)) {
						$this->smartyassign('revision_displayed', $version_info['version']);
						$this->content_to_render = $version_info['data'];
					} else {
						$this->smartyassign('revision_displayed', $this->info['version']);
					}
				} else {
					$this->smartyassign('revision_approved', null);
					if (empty($this->content_to_render)) {
						$this->smartyassign('revision_displayed', null);
						$this->content_to_render = '^' . tra('There are no approved versions of this page.', $this->info['lang']) . '^';
					} else {
						$this->smartyassign('revision_displayed', $this->info['version']);
					}
				}
			}
		}

		if ($this->content_to_render == '') {
			$pdata = $wikilib->get_parse($this->page, $canBeRefreshed);

			if ($canBeRefreshed) {
				$this->smartyassign('cached_page','y');
			}
		} else {
			$parse_options = array(
				'is_html' => $this->info['is_html'],
				'language' => $this->info['lang']
			);

			$pdata = $wikilib->parse_data($this->content_to_render, $parse_options);
		}

		$pages = $wikilib->get_number_of_pages($pdata);
		$pdata = $wikilib->get_page($pdata,$this->pageNumber);
		$this->smartyassign('pages',$pages);

		if($pages>$this->pageNumber) {
			$this->smartyassign('next_page',$this->pageNumber+1);
		} else {
			$this->smartyassign('next_page',$this->pageNumber);
		}
		if($this->pageNumber>1) {
			$this->smartyassign('prev_page',$this->pageNumber-1);
		} else {
			$this->smartyassign('prev_page',1);
		}

		$this->smartyassign('first_page',1);
		$this->smartyassign('last_page',$pages);
		$this->smartyassign('pagenum',$this->pageNumber);

		$this->smartyassign('lastVersion',$this->info["version"]);
		if (isset($this->info['last_version'])) {
			$this->smartyassign('versioned', true);
		}

		$this->smartyassign('lastModif',$this->info["lastModif"]);
		if(empty($this->info['user'])) {
			$this->info['user']=tra('Anonymous');  
		}
		$this->smartyassign('lastUser',$this->info['user']);
		$this->smartyassign('description',$this->info['description']);

		$this->smartyassign('parsed',$pdata);
		if (!empty($this->info['keywords'])) {
			$this->smartyassign('metatag_local_keywords', $this->info['keywords']);
		}
	} // }}}

	private function setupAttachments() // {{{
	{
		global $prefs, $wikilib;
		if ( $prefs['feature_wiki_attachments'] != 'y' )
			return;

		// If anything below here is changed, please change lib/wiki-plugins/wikiplugin_attach.php as well.
		$this->smartyassign('sort_mode', $this->sortMode );
		if( $this->showAttachments !== false )
			$this->smartyassign('atts_show', $this->showAttachments);

		$atts = $wikilib->list_wiki_attachments($this->page,0,-1, $this->sortMode,'');
		$this->smartyassign('atts',$atts["data"]);
		$this->smartyassign('atts_count',count($atts['data']));
	} // }}}

	private function setupFootnotes() // {{{
	{
		global $prefs, $wikilib, $tikilib;

		$this->smartyassign('footnote','');
		$this->smartyassign('has_footnote','n');

		if($prefs['feature_wiki_footnotes'] == 'y') {
			if($this->user) {
				$footnote = $wikilib->get_footnote($this->user,$this->page);
				$this->smartyassign('footnote',$tikilib->parse_data($footnote));

				if($footnote)
					$this->smartyassign('has_footnote','y');
			}
		}

		$this->smartyassign('wiki_extras','y');
	} // }}}

	private function setupWatch() // {{{
	{
		global $prefs, $tikilib, $categlib, $userlib;
		require_once 'lib/categories/categlib.php';
		if ($prefs['feature_user_watches'] != 'y')
			return;

		$this->smartyassign('user_watching_page','n');
		$this->smartyassign('user_watching_structure','n');
		if ($this->user) {
			if ($tikilib->user_watches($this->user, 'wiki_page_changed', $this->page, 'wiki page')) {
				$this->smartyassign('user_watching_page', 'y');
			}
			if (isset($this->structureInfo) && $tikilib->user_watches($this->user, 'structure_changed', $this->structureInfo['page_ref_id'], 'structure')) {
				$this->smartyassign('user_watching_structure', 'y');
			}
		}
		// Check, if the user is watching this page by a category.    
		if ($prefs['feature_categories'] == 'y') {    
			$watching_categories_temp=$categlib->get_watching_categories($this->page,"wiki page",$this->user);	    
			$this->smartyassign('category_watched','n');
			if (count($watching_categories_temp) > 0) {
				$this->smartyassign('category_watched','y');
				$watching_categories=array();	 			 	
				foreach ($watching_categories_temp as $wct ) {
					$watching_categories[]=array("categId"=>$wct,"name"=>$categlib->get_category_name($wct));
				}		 		 	
				$this->smartyassign('watching_categories', $watching_categories);
			}    
		}    

	} // }}}

	private function setupCategories() // {{{
	{
		global $prefs, $categlib;
		require_once 'lib/categories/categlib.php';

		$cats = array();
		if ($prefs['feature_categories'] == 'y' && $categlib->is_categorized('wiki page',$this->page)) {
			$this->smartyassign('is_categorized','y');
			if ($prefs['feature_categoryobjects'] == 'y' || $prefs['feature_categorypath'] == 'y') {
				$cats = $categlib->get_object_categories('wiki page',$this->page);
			}
			if ($prefs['feature_categorypath'] == 'y') {	
				$display_catpath = $categlib->get_categorypath($cats);
				$this->smartyassign('display_catpath',$display_catpath);
			}    
			// Display current category objects or not (like {category()})    
			if ($prefs['feature_categoryobjects'] == 'y') {	    
				$display_catobjects = $categlib->get_categoryobjects($cats);
				$this->smartyassign('display_catobjects',$display_catobjects);
			}
		} else {
			$this->smartyassign('is_categorized','n');
		}
	} // }}}

	private function setupPoll() // {{{
	{
		global $prefs, $polllib, $tikilib, $tiki_p_wiki_view_ratings;

		if ($prefs['feature_polls'] !='y' || $prefs['feature_wiki_ratings'] != 'y' || $tiki_p_wiki_view_ratings != 'y')
			return;

		if (!isset($polllib) || !is_object($polllib)) include("lib/polls/polllib_shared.php");
		$ratings = $polllib->get_ratings('wiki page',$this->page, $this->user );
		$this->smartyassign('ratings',$ratings);
	} // }}}

	private function setupBreadcrumbs() // {{{
	{
		global $prefs, $crumbs;

		if ($this->structureInfo && $this->structureInfo['page_alias'] != '') {
			$crumbpage = $this->structureInfo['page_alias'];
		} else {
			$crumbpage = $this->page;
		}
		//global $description;
		$crumbsLocal[] = new Breadcrumb($crumbpage,
				$this->info['description'],
				'tiki-index.php?page='.urlencode($this->page),
				'',
				'');
		$crumbs = array_merge($crumbs, $crumbsLocal);

		$headtitle = breadcrumb_buildHeadTitle($prefs['site_title_breadcrumb'] == 'invertfull'? array_reverse($crumbsLocal): $crumbsLocal);
		$this->smartyassign('headtitle', $headtitle);
		$this->smartyassign('trail', $crumbs);
	} // }}}

	function setupStaging() // {{{
	{
		global $prefs, $tikilib, $categlib, $histlib, $tiki_p_edit;
		require_once 'lib/categories/categlib.php';
		if ($prefs['feature_wikiapproval'] != 'y')
			return;

		$cats = $categlib->get_object_categories('wiki page',$this->page);

		if ($tikilib->page_exists($prefs['wikiapproval_prefix'] . $this->page)) {
			$this->smartyassign('hasStaging', 'y');
		}
		if ($prefs['wikiapproval_approved_category'] == 0 && $tiki_p_edit == 'y' || $prefs['wikiapproval_approved_category'] > 0 && $categlib->has_edit_permission($this->user, $prefs['wikiapproval_approved_category'])) {
			$canApproveStaging = 'y';
			$this->smartyassign('canApproveStaging', $canApproveStaging);
		}		
		if ( $approved = $tikilib->get_approved_page( $this->page ) ) {
			$approvedPageName = $approved;
			$this->smartyassign('beingStaged', 'y');
			$this->smartyassign('approvedPageName', $approvedPageName);	
			$approvedPageExists = $tikilib->page_exists($approvedPageName);
			$this->smartyassign('approvedPageExists', $approvedPageExists);
		} elseif ($prefs['wikiapproval_approved_category'] > 0 && !empty($cats) && in_array($prefs['wikiapproval_approved_category'], $cats)) {
			$stagingPageName = $prefs['wikiapproval_prefix'] . $this->page;
			$this->smartyassign('needsStaging', 'y');
			$this->smartyassign('stagingPageName', $stagingPageName);	
			if ($tikilib->user_has_perm_on_object($this->user,$stagingPageName,'wiki page','tiki_p_edit')) {
				$this->smartyassign('canEditStaging', 'y');
			} 	
		} elseif ($prefs['wikiapproval_staging_category'] > 0 && !empty($cats) && in_array($prefs['wikiapproval_staging_category'], $cats) && !$tikilib->page_exists($prefs['wikiapproval_prefix'] . $this->page)) {
			$this->smartyassign('needsFirstApproval', 'y');		
		}
		if ($prefs['wikiapproval_outofsync_category'] == 0 || $prefs['wikiapproval_outofsync_category'] > 0 && in_array($prefs['wikiapproval_outofsync_category'], $cats)) {
			if (isset($approvedPageName)) $this->smartyassign('outOfSync', 'y');
			if ($canApproveStaging == 'y' && isset($approvedPageName)) {
				include_once('lib/wiki/histlib.php');
				$approvedPageInfo = $histlib->get_page_from_history($approvedPageName, 0);
				if ($approvedPageInfo && $this->info['lastModif'] > $approvedPageInfo['lastModif']) {
					$lastSyncVersion = $histlib->get_version_by_time($this->page, $approvedPageInfo['lastModif']);
					// get very first version if unable to get last sync version.
					if ($lastSyncVersion == 0) $lastSyncVersion = $histlib->get_version_by_time($this->page, 0, 'after');
					// if really not possible, just give up.
					if ($lastSyncVersion > 0) $this->smartyassign('lastSyncVersion', $lastSyncVersion );
				}
			}		
		}
	} // }}}

	private function setGlobal( $name, $value ) // {{{
	{
		if( (empty($GLOBALS[$name]) || $GLOBALS[$name] != $value) && ! array_key_exists( $name, $this->toRestore ) )
			$this->toRestore[$name] = $value;

		$GLOBALS[$name] = $value;
		$this->smartyassign( $name, $value );
	} // }}}

	private function setPref( $name, $value ) // {{{
	{
		global $prefs;
		if( $value != $prefs[$name] && ! array_key_exists( $name, $this->prefRestore ) )
			$this->prefRestore[$name] = $value;

		$prefs[$name] = $value;
	} // }}}

	private function smartyassign( $name, $value ) // {{{
	{
		global $smarty;
		if( ! array_key_exists( $name, $this->smartyRestore ) )
			$this->smartyRestore[$name] = $smarty->get_template_vars($name);

		$smarty->assign( $name, $value );
	} // }}}

	function canUndo() // {{{
	{
		if ( $this->canUndo !== null ) return $this->canUndo;

		global $tiki_p_admin_wiki, $tiki_p_remove, $tiki_p_edit;

		if ( $this->info['flag'] != 'L'
			&& (
				( $tiki_p_edit == 'y' && $this->info['user'] == $this->user ) || $tiki_p_remove == 'y'
			) )  {
			$this->canUndo = true;
		}
		if ( $tiki_p_admin_wiki == 'y' ) {
			$this->canUndo = true;
		}

		return $this->canUndo;
		
	} // }}}

	function setInfos( $infos ) // {{{
	{
		$this->info = $infos;
	} // }}}

	function setInfo( $name, $value ) // {{{
	{
		$this->info[$name] = $value;
	} // }}}

	function forceLatest() // {{{
	{
		$this->content_to_render = $this->info['data'];
	} // }}}
}
