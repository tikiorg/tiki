<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
	private $raw = false;

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
	);

	private $toRestore = array();
	private $prefRestore = array();
	private $smartyRestore = array();

	public $canView = false;
	public $canUndo = null;
	public $trads = null;	// translated pages

	function __construct( $info, $user, $content_to_render=null)
	{
		$this->info = $info;
		$this->user = $user;
		$this->page = $info['pageName'];
		$this->content_to_render = $content_to_render;
	}

	function applyPermissions() // {{{
	{
		$userlib = TikiLib::lib('user');
		$permNames = $userlib->get_permission_names_for('wiki');
		$objectperms = Perms::get(array( 'type' => 'wiki page', 'object' => $this->page ));

		foreach ( $permNames as $name ) {
			$this->setGlobal($name, $objectperms->$name ? 'y' : 'n');
		}

		$this->canView = $objectperms->view;

		$this->smartyassign('page_user', $this->info['user']);

		return $objectperms;
	} // }}}

	function restoreAll() // {{{
	{
		global $prefs;
		$smarty = TikiLib::lib('smarty');
		foreach ( $this->toRestore as $name => $value ) {
			$GLOBALS[$name] = $value;
		}

		foreach ( $this->prefRestore as $name => $value )
			$prefs[$name] = $value;

		foreach ( $this->smartyRestore as $name => $value )
			$smarty->assign($name, $value);
	} // }}}

	function runSetups() // {{{
	{
		foreach ( $this->prep as $method )
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
		if ( ! $this->structureInfo )
			return;

		global $structure, $structure_path;
		$structlib = TikiLib::lib('struct');
		$tikilib = TikiLib::lib('tiki');

		$structure = 'y';
		$this->smartyassign('structure', $structure);
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
			if ($tikilib->user_has_perm_on_object($this->user, $t_structs['pageName'], 'wiki page', 'tiki_p_view')) {
				$structs_with_perm[] = $t_structs;
			}
		}
		if ($tikilib->user_has_perm_on_object($this->user, $navigation_info['home']['pageName'], 'wiki page', 'tiki_p_edit', 'tiki_p_edit_structures'))
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
		global $prefs;
		$wikilib = TikiLib::lib('wiki');

		if ( $prefs['wiki_authors_style'] != 'classic' ) {
			$contributors = $wikilib->get_contributors($this->page, $this->info['user']);
			$this->smartyassign('contributors', $contributors);
		}
	} // }}}

	private function setupCreator() // {{{
	{
		$wikilib = TikiLib::lib('wiki');

		if (isset($this->info['creator'])) {
			$creator = $this->info['creator'];
		} else {
			$creator = $wikilib->get_creator($this->page);
		}

		$this->smartyassign('creator', $creator);
	} // }}}

	private function setupMultilingual() // {{{
	{
		global $prefs;

		if ($prefs['feature_multilingual'] != 'y')
			return;

		$tikilib = TikiLib::lib('tiki');
		$multilinguallib = TikiLib::lib('multilingual');

		if ( !empty($this->info['lang'])) {
			$this->trads = $multilinguallib->getTranslations('wiki page', $this->info['page_id'], $this->page, $this->info['lang']);
			$this->smartyassign('trads', $this->trads);
			$this->smartyassign('translationsCount', count($this->trads));
			$pageLang = $this->info['lang'];
			$this->smartyassign('pageLang', $pageLang);
		}

		if ($prefs['feature_machine_translation'] == 'y' && $prefs['lang_machine_translate_wiki'] == 'y' && !empty($this->info['lang'])) {
			$provider = new Multilingual_MachineTranslation;
			$langsCandidatesForMachineTranslation = $provider->getAvailableLanguages($this->trads);
			$this->smartyassign('langsCandidatesForMachineTranslation', $langsCandidatesForMachineTranslation);
		}

		$bits = $multilinguallib->getMissingTranslationBits('wiki page', $this->info['page_id'], 'critical', true);

		$alertData = array();
		foreach ( $bits as $translationBit ) {
			$alertData[] = $multilinguallib->getTranslationsWithBit($translationBit, $this->info['page_id']);
		}

		$this->smartyassign('translation_alert', $alertData);
	} // }}}

	private function setupBacklinks() // {{{
	{
		global $prefs, $tiki_p_view_backlink;
		$wikilib = TikiLib::lib('wiki');

		if ( $prefs['feature_backlinks'] == 'y' && $tiki_p_view_backlink == 'y') {
			$backlinks = $wikilib->get_backlinks($this->page);
			$this->smartyassign('backlinks', $backlinks);
		}
	} // }}}

	private function setupActions() // {{{
	{
		global $prefs, $tiki_p_edit, $tiki_p_remove, $tiki_p_admin_wiki;
		$wikilib = TikiLib::lib('wiki');

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

		if (!isset($this->info['is_html'])) {
			$this->info['is_html'] = false;
		}

		$this->setupComments();
	} // }}}

	private function setupSlideshow() // {{{
	{
		global $prefs;

		if ($prefs['wiki_uses_slides'] != 'y') {
			$this->smartyassign('show_slideshow', 'n');
			return;
		}

		//Let us check if slides exist in the wiki page
		$slides = preg_split('/-=[^=]+=-|![^=]+|!![^=]+!!![^=]+/', $this->info['data']);

		if (count($slides)>1) {
			$this->smartyassign('show_slideshow', 'y');
		} else {
			$slides = explode('...page...', $this->info['data']);

			$this->smartyassign('show_slideshow', ( count($slides) > 1 ) ? 'y' : 'n');
		}
	} // }}}

	private function setupPage() // {{{
	{
		global $prefs, $user;
		$wikilib = TikiLib::lib('wiki');
		$tikilib = TikiLib::lib('tiki');

		$this->smartyassign('page', $this->page);
		$this->smartyassign('show_page', 'y');

		$this->smartyassign('dblclickedit', 'y');
		$this->smartyassign('print_page', 'n');
		$this->smartyassign('beingEdited', 'n');
		$this->smartyassign('categorypath', $prefs['feature_categorypath']);
		$this->smartyassign('categoryobjects', $prefs['feature_categoryobjects']);
		$this->smartyassign('feature_wiki_pageid', $prefs['feature_wiki_pageid']);
		$this->smartyassign('page_id', $this->info['page_id']);

		// Get the authors style for this page
		$wiki_authors_style = ( $prefs['wiki_authors_style_by_page'] == 'y' && $this->info['wiki_authors_style'] != '' ) ? $this->info['wiki_authors_style'] : $prefs['wiki_authors_style'];
		$this->smartyassign('wiki_authors_style', $wiki_authors_style);
		$this->smartyassign('revision_approval_info', null);

		$this->smartyassign('cached_page', 'n');

		if ($prefs['flaggedrev_approval'] == 'y') {
			$flaggedrevisionlib = TikiLib::lib('flaggedrevision');

			if ($flaggedrevisionlib->page_requires_approval($this->page)) {
				$this->smartyassign('revision_approval', true);

				if ($version_info = $flaggedrevisionlib->get_version_with($this->page, 'moderation', 'OK')) {
					$this->smartyassign('revision_approved', $version_info['version']);
					$revision_displayed = $this->info['version'];

					if ($this->content_to_render === null) {
						$this->content_to_render = $version_info['data'];
						$revision_displayed = $version_info['version'];
					}

					$this->smartyassign('revision_displayed', $revision_displayed);

					if ($revision_displayed == $version_info['version']) {
						$approval = $flaggedrevisionlib->find_approval_information($this->page, $revision_displayed);
						$this->smartyassign('revision_approval_info', $approval);
					}
				} else {
					$this->smartyassign('revision_approved', null);
					if ($this->content_to_render === null) {
						$this->smartyassign('revision_displayed', null);
						$this->content_to_render = '^' . tra('There are no approved versions of this page.', $this->info['lang']) . '^';
					} else {
						$this->smartyassign('revision_displayed', $this->info['version']);
					}
				}
			}
		}

		if ($this->content_to_render === null) {
			$page = $this->page;
			$pdata = new Tiki_Render_Lazy(
				function () use ($page) {
					$wikilib = TikiLib::lib('wiki');
					$smarty = TikiLib::lib('smarty');
					$parsed = $wikilib->get_parse($page, $canBeRefreshed);

					if ($canBeRefreshed) {
						$smarty->assign('cached_page', 'y');
					}

					return $parsed;
				}
			);
		} else {
			$parse_options = array(
				'is_html' => $this->info['is_html'],
				'language' => $this->info['lang'],
			);

			$content = $this->content_to_render;
			if ($this->raw) {
				$pdata = new Tiki_Render_Lazy(
					function () use ($content) {
						$parserlib = TikiLib::lib('parser');
						return $parserlib->parse_data_raw($content);
					}
				);
			} else {
				$pdata = new Tiki_Render_Lazy(
					function () use ($content, $parse_options) {
						$wikilib = TikiLib::lib('wiki');
						return $wikilib->parse_data($content, $parse_options);
					}
				);
			}
		}

		if ($prefs['wiki_pagination'] == 'y') {
			$pages = $wikilib->get_number_of_pages($pdata);
			$pdata = $wikilib->get_page($pdata, $this->pageNumber);
			$this->smartyassign('pages', $pages);

			if ($pages>$this->pageNumber) {
				$this->smartyassign('next_page', $this->pageNumber+1);
			} else {
				$this->smartyassign('next_page', $this->pageNumber);
			}
			if ($this->pageNumber>1) {
				$this->smartyassign('prev_page', $this->pageNumber-1);
			} else {
				$this->smartyassign('prev_page', 1);
			}

			$this->smartyassign('first_page', 1);
			$this->smartyassign('last_page', $pages);
			$this->smartyassign('pagenum', $this->pageNumber);
		} else {
			$this->smartyassign('pages', 1);
			$this->smartyassign('next_page', 1);
			$this->smartyassign('prev_page', 1);
			$this->smartyassign('first_page', 1);
			$this->smartyassign('last_page', 1);
			$this->smartyassign('pagenum', 1);
		}

		$this->smartyassign('lastVersion', $this->info["version"]);
		if (isset($this->info['last_version'])) {
			$this->smartyassign('versioned', true);
		}

		$this->smartyassign('lastModif', $this->info["lastModif"]);
		if (empty($this->info['user'])) {
			$this->info['user']=tra('Anonymous');
		}
		$this->smartyassign('lastUser', $this->info['user']);
		$this->smartyassign('description', $this->info['description']);

		$this->smartyassign('parsed', $pdata);
		if (!empty($this->info['keywords'])) {
			$this->smartyassign('metatag_local_keywords', $this->info['keywords']);
		}
	} // }}}

	private function setupAttachments() // {{{
	{
		global $prefs;
		$wikilib = TikiLib::lib('wiki');

		if ( $prefs['feature_wiki_attachments'] != 'y' || $prefs['feature_use_fgal_for_wiki_attachments'] == 'y' ) {
			return;
		}

		// If anything below here is changed, please change lib/wiki-plugins/wikiplugin_attach.php as well.
		$this->smartyassign('sort_mode', $this->sortMode);
		if ( $this->showAttachments !== false ) {
			$this->smartyassign('atts_show', $this->showAttachments);
		}

		$atts = $wikilib->list_wiki_attachments($this->page, 0, -1, $this->sortMode, '');
		$this->smartyassign('atts', $atts["data"]);
		$this->smartyassign('atts_count', count($atts['data']));
	} // }}}

	private function setupFootnotes() // {{{
	{
		global $prefs;
		$wikilib = TikiLib::lib('wiki');
		$tikilib = TikiLib::lib('tiki');

		$this->smartyassign('footnote', '');
		$this->smartyassign('has_footnote', 'n');

		if ($prefs['feature_wiki_footnotes'] == 'y') {
			if ($this->user) {
				$footnote = $wikilib->get_footnote($this->user, $this->page);
				$this->smartyassign('footnote', $tikilib->parse_data($footnote));

				if ($footnote)
					$this->smartyassign('has_footnote', 'y');
			}
		}

		$this->smartyassign('wiki_extras', 'y');
	} // }}}

	private function setupWatch() // {{{
	{
		global $prefs;
		$tikilib = TikiLib::lib('tiki');
		$categlib = TikiLib::lib('categ');
		$userlib = TikiLib::lib('user');
		if ($prefs['feature_user_watches'] != 'y')
			return;

		$this->smartyassign('user_watching_page', 'n');
		$this->smartyassign('user_watching_structure', 'n');
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
			$watching_categories_temp=$categlib->get_watching_categories($this->page, "wiki page", $this->user);
			$this->smartyassign('category_watched', 'n');
			if (count($watching_categories_temp) > 0) {
				$this->smartyassign('category_watched', 'y');
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
		global $prefs;
		$categlib = TikiLib::lib('categ');

		$cats = array();
		if ($prefs['feature_categories'] == 'y' && $categlib->is_categorized('wiki page', $this->page)) {
			$this->smartyassign('is_categorized', 'y');
			if ($prefs['feature_categoryobjects'] == 'y' || $prefs['feature_categorypath'] == 'y') {
				$cats = $categlib->get_object_categories('wiki page', $this->page);
			}
			if ($prefs['category_morelikethis_algorithm'] != '') {
				$freetaglib = TikiLib::lib('freetag');
				$category_related_objects = $freetaglib->get_similar('wiki page', $this->page, empty($prefs['category_morelikethis_mincommon_max'])?$prefs['maxRecords']: $prefs['category_morelikethis_mincommon_max'], null, 'category');
				$this->smartyassign('category_related_objects', $category_related_objects);
			}
			if ($prefs['feature_categorypath'] == 'y') {
				$display_catpath = $categlib->get_categorypath($cats);
				$this->smartyassign('display_catpath', $display_catpath);
			}
			// Display current category objects or not (like {category()})
			if ($prefs['feature_categoryobjects'] == 'y') {
				$display_catobjects = $categlib->get_categoryobjects($cats);
				$this->smartyassign('display_catobjects', $display_catobjects);
			}
		} else {
			$this->smartyassign('is_categorized', 'n');
		}
	} // }}}

	private function setupPoll() // {{{
	{
		global $prefs, $tiki_p_wiki_view_ratings;
		$polllib = TikiLib::lib('poll');
		$tikilib = TikiLib::lib('tiki');

		if ($prefs['feature_polls'] !='y' || $prefs['feature_wiki_ratings'] != 'y' || $tiki_p_wiki_view_ratings != 'y')
			return;

		if (!isset($polllib) || !is_object($polllib)) include("lib/polls/polllib_shared.php");
		$ratings = $polllib->get_ratings('wiki page', $this->page, $this->user);
		$this->smartyassign('ratings', $ratings);
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
		$crumbsLocal[] = new Breadcrumb(
			isset($this->info['prettyName']) ? $this->info['prettyName'] : $crumbpage,
			$this->info['description'],
			TikiLib::lib('wiki')->sefurl($this->page),
			'',
			''
		);
		$crumbs = array_merge($crumbs, $crumbsLocal);

		$headtitle = breadcrumb_buildHeadTitle($prefs['site_title_breadcrumb'] == 'invertfull'? array_reverse($crumbsLocal): $crumbsLocal);
		$this->smartyassign('headtitle', $headtitle);
		$this->smartyassign('trail', $crumbs);
	} // }}}

	private function setGlobal( $name, $value ) // {{{
	{
		if ( (empty($GLOBALS[$name]) || $GLOBALS[$name] != $value) && ! array_key_exists($name, $this->toRestore) )
			$this->toRestore[$name] = $value;

		$GLOBALS[$name] = $value;
		$this->smartyassign($name, $value);
	} // }}}

	private function setPref( $name, $value ) // {{{
	{
		global $prefs;
		if ( $value != $prefs[$name] && ! array_key_exists($name, $this->prefRestore) )
			$this->prefRestore[$name] = $value;

		$prefs[$name] = $value;
	} // }}}

	private function smartyassign( $name, $value ) // {{{
	{
		$smarty = TikiLib::lib('smarty');
		if ( ! array_key_exists($name, $this->smartyRestore) )
			$this->smartyRestore[$name] = $smarty->getTemplateVars($name);

		$smarty->assign($name, $value);
	} // }}}

	function canUndo() // {{{
	{
		if ( $this->canUndo !== null ) return $this->canUndo;

		global $tiki_p_admin_wiki, $tiki_p_remove, $tiki_p_edit;

		if ( $this->info['flag'] != 'L' 	&& (( $tiki_p_edit == 'y' && $this->info['user'] == $this->user ) || $tiki_p_remove == 'y')) {
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

	function useRaw() // {{{
	{
		$this->raw = true;
	} // }}}

	private function setupComments()
	{
		$count_comments = TikiLib::lib('comments')->count_comments('wiki page:'.$this->page, 'n');
		$this->smartyassign('count_comments', $count_comments);
	}
}
