<?php

require_once 'TikiPageControls.php';

class TikiPageControls_Wiki extends TikiPageControls
{
	// Controls base configuration
	private $page;
	private $info;

	// Flags
	private $isAllLanguage = false;

	// Data caches
	private $backlinks;
	private $trads;
	private $canEdit;
	private $canUndo;
	private $canSlideshow;
	private $structureInfo;
	private $attachmentCount;
	private $isLocked;

	function __construct( $info ) // {{{
	{
		parent::__construct( 'wiki page', $info['pageName'], $info['pageName'] );

		$this->page = $info['pageName'];
		$this->info = $info;
	} // }}}

	function build() // {{{
	{
		switch($mode) {
		case 'translate_new':
		case 'translate_update':
			$this->setHeading( tr('%0 (translation of %1)', $this->page, $this->translationSource), $this->link( 'wiki page', $this->page ) );
			break;
		default:
			$this->setHeading( $this->page, $this->link( 'wiki page', $this->page ) );
			break;
		}

		if( $this->hasPref('feature_multilingual') ) {
			$this->addLanguageMenu();
		}

		$this->addActionMenu();

		if( $this->hasPref('feature_backlinks') ) {
			$backlinksMenu = $this->addMenu( 'backlinks', tra('Backlinks') );

			foreach( $this->getBacklinks() as $back ) {
				$link = $this->link( 'wiki page', $back['fromPage'] );
				$backlinksMenu->addItem( $back['fromPage'], $link );
			}

			if( $backlinksMenu->isEmpty() )
				$this->removeMenu($backlinksMenu);
		}

		$this->addTabs();
	} // }}}

	function setTranslations( $trads ) // {{{
	{
		$this->trads = $trads;
	} // }}}

	function setBacklinks( $backlinks ) // {{{
	{
		$this->backlinks = $backlinks;
	} // }}}

	function setStructureInfo( $info ) // {{{
	{
		$this->structureInfo = $info;
	} // }}}

	function setCanEdit( $can ) // {{{
	{
		$this->canEdit = (bool) $can;
	} // }}}

	function setCanUndo( $can ) // {{{
	{
		$this->canUndo = (bool) $can;
	} // }}}

	function setCanSlideshow( $can ) // {{{
	{
		$this->canSlideshow = (bool) $can;
	} // }}}

	function setAttachmentCount( $count ) // {{{
	{
		$this->attachmentCount = (int) $count;
	} // }}}

	function isAllLanguage( $isAllLanguage ) // {{{
	{
		$this->isAllLanguage = (bool) $isAllLanguage;
	} // }}}

	function setTranslationSource( $sourcePage ) // {{{
	{
		$this->translationSource = $sourcePage;
	} // }}}

	function setIsLocked( $locked ) // {{{
	{
		$this->isLocked = (bool) $locked;
	} // }}}

	private function getBacklinks() // {{{
	{
		if( $this->backlinks )
			return $this->backlinks;

		global $wikilib; require_once 'lib/wiki/wikilib.php';
		return $this->backlinks = $wikilib->get_backlinks($this->page);
	} // }}}

	private function getTranslations() // {{{
	{
		if( $this->trads )
			return $this->trads;

		global $multilinguallib; require_once 'lib/multilingual/multilinguallib.php';
		return $this->trads = $multilinguallib->getTranslations('wiki page', $this->info['page_id'], $this->page, $this->info['lang']);
	} // }}}

	private function addLanguageMenu() // {{{
	{
		$langMenu = $this->addMenu( 'language', tra('Language') );

		if( empty( $this->info['lang'] ) ) {
			if( $this->hasPerm('tiki_p_edit') ) {
				$link = $this->link( 'url', 'tiki-edit_translation.php', array(
							'page' => $this->page,
							) );
				$langMenu->addItem( tra('Set Language'), $link, 'set_language' );
			}
		} else {
			foreach( $this->getTranslations() as $trad ) {
				$link = $this->link( 'wiki page', $trad['objName'] );
				$langMenu->addItem( $trad['langName'], $link, $this->info['lang'] )
					->setSelected( ! $this->isAllLanguage && $this->info['lang'] == $trad['lang'] );
			}

			if( $this->hasPref('feature_multilingual_one_page') ) {
				$link = $this->link( 'url', 'tiki-all_languages', array(
					'page' => $this->page,
				) );
				$langMenu->addSeparator();
				$langMenu->addItem( tra('All'), $link, 'all' )
					->setSelected( $this->isAllLanguage );
			}

			if( $this->hasPerm('tiki_p_edit') ) {
				$link = $this->link( 'url', 'tiki-edit_translation.php', array(
					'page' => $this->page,
				) );
				$langMenu->addSeparator();
				$langMenu->addItem( tra('Translate'), $link, 'translate' );
			}
		}

		if( $langMenu->isEmpty() )
			$this->removeMenu( $langMenu );
	} // }}}
	
	private function addActionMenu() // {{{
	{
		$actionMenu = $this->addMenu( 'actions', tra('Actions') );

		if( $this->hasPerm('tiki_p_rename') ) {
			$link = $this->link( 'url', 'tiki-rename_page.php', array(
				'page' => $this->page,
			) );
			$actionMenu->addItem( tra('Rename'), $link, 'rename' )
				->setSelected( $this->isMode('rename') );
		}

		if( $this->hasPerm('tiki_p_remove') ) {
			$link = $this->link( 'url', 'tiki-removepage.php', array(
				'page' => $this->page,
				'version' => 'last',
			) );
			$actionMenu->addItem( tra('Remove'), $link, 'remove' )
				->setIcon( 'pics/icons/cross.png' )
				->setSelected( $this->isMode('remove') );
		}

		if( $this->hasPref('feature_wiki_usrlock')
		 && (
		 	$this->hasPerm('tiki_p_admin_wiki')
			|| ( $this->getUser() && $this->info['user'] == $this->getUser() && $this->hasPerm('tiki_p_lock') )
		 ) ) {
			if( $this->isLocked() ) {
				$link = $this->link( 'wiki page', $this->page, array(
					'action' => 'unlock',
				) );
				$actionMenu->addItem( tra('Unlock'), $link, 'unlock' )
					->setSelected( $this->isMode('lock') );
			} else {
				$link = $this->link( 'wiki page', $this->page, array(
					'action' => 'lock',
				) );
				$actionMenu->addItem( tra('Lock'), $link, 'lock' )
					->setSelected( $this->isMode('lock') );
			}
		}

		if( $this->hasPerm('tiki_p_assign_perm_wiki_page') 
		 || $this->hasPerm('tiki_p_admin_wiki') ) {
			$link = $this->link( 'url', 'tiki-objectpermissions.php', array(
				'objectId' => $this->page,
				'objectName' => $this->page,
				'objectType' => 'wiki page',
				'permType' => 'wiki',
			) );
			$actionMenu->addItem( tra('Permissions'), $link, 'permissions' )
				->setIcon( 'pics/icons/key.png' )
				->setSelected( $this->isMode('permissions') );
		}

		if( $this->hasPref('feature_likePages') ) {
			$link = $this->link( 'url', 'tiki-likepages.php', array(
				'page' => $this->page,
			) );
			$actionMenu->addItem( tra('Similar'), $link, 'similar' )
				->setSelected( $this->isMode('similar') );
		}

		if( $this->hasPref('feature_wiki_undo') && $this->canUndo() ) {
			$link = $this->link( 'wiki page', $this->page, array(
				'undo' => 1,
			) );
			$actionMenu->addItem( tra('Undo'), $link, 'undo' )
				->setSelected( $this->isMode('undo') );
		}

		if( $this->hasPref('feature_wiki_make_structure') 
		 && $this->hasPerm('tiki_p_edit_structures')
		 && $this->canEdit()
		 && ! $this->hasStructure() ) {
			$link = $this->link( 'wiki page', $this->page, array(
				'convertstructure' => 1,
			) );
			$actionMenu->addItem( tra('Make Structure'), $link, 'structure_make' )
				->setSelected( $this->isMode('structure_make') );
		}

		if( $this->hasPref('wiki_uses_slides') ) {
			if( $this->hasSlideshow() ) {
				$link = $this->link( 'url', 'tiki-slideshow.php', array(
					'page' => $this->page,
				) );
				$actionMenu->addItem( tra('Slides'), $link, 'slide' )
					->setSelected( $this->isMode('slide') );
			}
			if( $this->hasStructure() ) {
				$link = $this->link( 'url', 'tiki-slideshow2.php', array(
					'page_ref_id' => $this->structureInfo['page_ref_id'],
				) );
				$actionMenu->addItem( tra('Structure Slides'), $link, 'slide_structure' )
					->setSelected( $this->isMode('slide_structure') );
			}
		}

		if( $this->hasPref('feature_wiki_export')
			&& $this->hasAnyOfPerm('tiki_p_admin_wiki', 'tiki_p_export_wiki')
		) {
				$link = $this->link( 'url', 'tiki-export_wiki_pages.php', array(
					'page' => $this->page,
				) );
				$actionMenu->addItem( tra('Export'), $link, 'export' )
					->setSelected( $this->isMode('export') );
		}

		if( $this->hasPref('feature_multilingual')
		 && $this->hasPerm('tiki_p_edit')
		 && ! $this->isLocked() ) {
			$link = $this->link( 'url', 'tiki-edit_translation.php', array(
				'page' => $this->page,
			) );
			$actionMenu->addItem( tra('Translate'), $link, 'translate' )
				->setSelected( $this->isMode('translate') );
		}

		if( $actionMenu->isEmpty() ) {
			$this->removeMenu( $actionMenu );
		}
	} // }}}

	private function addTabs() // {{{
	{
		$link = $this->link( 'wiki page', $this->page );
		$this->addTab( 'view', tra('View'), $link )
			->setSelected( $this->isMode('view') );

		if( $this->hasPerm('tiki_p_edit') ) {
			$link = $this->link( 'url', 'tiki-editpage.php', array(
				'page' => $this->page
			) );
			switch($this->getMode()) {
			case 'translate_new':
			case 'translate_update':
				$this->addTab( 'edit', tra('Translate'), $link )
					->setIcon( 'pics/icons/page_edit.png' )
					->setSelected( $this->isMode('translate_new', 'translate_update') );
				break;
			case 'edit_section':
				$this->addTab( 'edit', tra('Edit Section'), $link )
					->setIcon( 'pics/icons/page_edit.png' )
					->setSelected( $this->isMode('edit_section') );
				break;
			default:
				$this->addTab( 'edit', tra('Edit'), $link )
					->setIcon( 'pics/icons/page_edit.png' )
					->setSelected( $this->isMode('edit') );
				break;
			}
		}

		if( $this->hasPref('feature_categories')
		 && $this->hasPerm('tiki_p_view_categories') ) {
			// TODO : Determine where this goes
			$link = $this->link( 'url', 'tiki-index.php', array(
				'page' => $this->page
			) );
			$this->addTab( 'categories', tra('Categorize'), $link )
				->setSelected( $this->isMode('category') );
		}

		if( $this->hasPref('feature_wiki_comments')
		 && $this->hasPerm('tiki_p_wiki_view_comments') ) {
			$link = $this->link( 'url', 'tiki-view_comments.php', array(
				'objectType' => 'wiki page',
				'objectId' => $this->page,
				'objectName' => $this->page,
			) );
			$this->addTab( 'comments', tra('Comments'), $link, $this->getCommentCount() )
				->setSelected( $this->isMode('comment') );
		}

		if( $this->hasPref('feature_wiki_attachments')
		 && $this->hasPerm('tiki_p_wiki_view_attachments') ) {
			// TODO : Determine where this goes
			$link = $this->link( 'url', 'tiki-index.php', array(
				'page' => $this->page
			) );
			$this->addTab( 'attachments', tra('Attachments'), $link, $this->getAttachmentCount() )
				->setSelected( $this->isMode('attach') );
		}

		if( $this->hasPref('feature_history')
		 && $this->hasPerm('tiki_p_wiki_view_history') ) {
			$link = $this->link( 'url', 'tiki-pagehistory.php', array(
				'page' => $this->page
			) );
			$this->addTab( 'history', tra('History'), $link )
				->setIcon( 'pics/icons/page_white_stack.png' )
				->setSelected( $this->isMode('history') );
		}
	} // }}}

	private function canUndo() // {{{
	{
		if ( $this->canUndo !== null ) return $this->canUndo;

		if ( $this->info['flag'] != 'L'
			&& (
				( $this->hasPerm('tiki_p_edit') && $this->info['user'] == $this->getUser() ) 
				|| $this->hasPerm('tiki_p_remove')
			) )  {
			$this->canUndo = true;
		}
		if ( $this->hasPerm('tiki_p_admin_wiki') ) {
			$this->canUndo = true;
		}

		return $this->canUndo;
		
	} // }}}

	private function canEdit() // {{{
	{
		if( !is_null($this->canEdit) )
			return $this->canEdit;

		global $wikilib; require_once 'lib/wiki/wikilib.php';
		$this->canEdit = $wikilib->is_editable($this->page, $this->user, $this->info);
		return $this->canEdit;
	} // }}}

	private function hasStructure() // {{{
	{
		return ! is_null($this->getStructureInfo());
	} // }}}

	private function getStructureInfo() // {{{
	{
		if( $this->structureInfo ) {
			return $this->structureInfo;
		}
		
		// TODO : Fetch structure info here if not present
	} // }}}

	private function getAttachmentCount() // {{{
	{
		if( ! is_null($this->attachmentCount) )
			return $this->attachmentCount;

		global $wikilib; require_once 'lib/wiki/wikilib.php';
		$att = $wikilib->list_wiki_attachments( $this->page );
		return $this->attachmentCount = (int) $att['cant'];
	} // }}}

	private function hasSlideshow() // {{{
	{
		if( ! is_null( $this->canSlideshow ) )
			return $this->canSlideshow;

		if ($prefs['wiki_uses_slides'] != 'y') {
			return $this->canSlideshow = false;
		}

		$slides = split("-=[^=]+=-",$this->info['data']);
		if(count($slides)>1) {
			return $this->canSlideshow = true;
		} else {
			$slides = explode('...page...',$this->info['data']);

			return $this->canSlideshow = count($slides) > 1;
		}
	} // }}}

	private function isLocked() // {{{
	{
		if( ! is_null($this->isLocked) )
			return $this->isLocked;

		global $wikilib; require_once 'lib/wiki/wikilib.php';
		return $this->isLocked = $wikilib->is_locked($this->page, $this->info);
	} // }}}
}

?>
