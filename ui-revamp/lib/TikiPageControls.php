<?php

class TikiPageControls_Element implements ArrayAccess
{
	private $text;
	private $tip;
	private $argument;
	private $link;
	private $type;
	private $iconPath;

	private $selected = false;

	function __construct( $type ) // {{{
	{
		$this->type = $type;
	} // }}}

	function setText( $text ) // {{{
	{
		$this->text = $text;

		// If no tip is set, text should be used
		if( ! $this->tip )
			$this->tip = $text;

		return $this;
	} // }}}

	function setTip( $text ) // {{{
	{
		$this->tip = $tip;

		return $this;
	} // }}}

	function setLink( $link ) // {{{
	{
		if( ! $link instanceof TikiPageControls_Link )
			throw new Exception("Expecting link instance.");

		$this->link = $link;

		return $this;
	} // }}}

	function setArgument( $value ) // {{{
	{
		$this->argument = $value;

		return $this;
	} // }}}

	function setSelected( $selected ) // {{{
	{
		$this->selected = (bool) $selected;

		return $this;
	} // }}}

	function setIcon( $path ) // {{{
	{
		$this->iconPath = $path;
		
		return $this;
	} // }}}

	function offsetGet( $name ) // {{{
	{
		switch( $name ) {
		case 'text': return $this->text;
		case 'argument': return $this->argument;
		case 'link': return $this->link;
		case 'type': return $this->type;
		case 'selected': return $this->selected;
		case 'full': return $this->getFullLink();
		case 'icon': return $this->getIconLink();
		case 'button': return $this->getButton();
		case 'iconsrc': return $this->iconPath;
		}
	} // }}}

	function offsetExists( $name ) // {{{
	{
		switch( $name ) {
		case 'text': return true;
		case 'argument': return true;
		case 'link': return true;
		case 'type': return true;
		case 'selected': return true;
		case 'full': return true;
		case 'icon': return true;
		case 'iconsrc': return true;
		default: return false;
		}
	} // }}}

	function offsetSet( $name, $value ) {}
	function offsetUnset( $name ) {}

	private function getIconLink() // {{{
	{
		if( $this->getIcon() ) {
			return $this->getLinked( $this->getIcon() );
		}
	} // }}}

	private function getFullLink() // {{{
	{
		return $this->getLinked( $this->getIcon() . $this->getText() );
	} // }}}

	private function getIcon() // {{{
	{
		if( $this->iconPath ) {
			require_once 'lib/smarty_tiki/function.icon.php';
			return smarty_function_icon( array(
				'_id' => $this->iconPath,
				'_text' => $this->tip,
				'alt' => $this->tip
			), $GLOBALS['smarty'] );
		}
	} // }}}

	private function getText() // {{{
	{
		$text = htmlentities( $this->text, ENT_QUOTES, 'UTF-8' );

		if( !is_null($this->argument) ) {
			$text .= ' <span class="argument">' . htmlentities($this->argument, ENT_QUOTES, 'UTF-8') . '</span>';
		}

		return $text;
	} // }}}

	private function getLinked( $body ) // {{{
	{
		if( $this->link ) {
			return '<a href="' . htmlentities($this->link->getHref(), ENT_QUOTES, 'UTF-8') . '">' . $body . '</a>';
		} else {
			return $body;
		}
	} // }}}

	private function getButton() // {{{
	{
		require_once 'lib/smarty_tiki/function.button.php';

		if( $this->link ) {
			return smarty_function_button( array(
				'href' => $this->link->getHref(),
				'_text' => $this->text,
			), $GLOBALS['smarty'] );
		}
	} // }}}

	function __toString() // {{{
	{
		if( $this->type == 'separator' ) {
			return '<hr/>';
		}

		return $this->getLinked( $this->getText() );
	} // }}}
}

abstract class TikiPageControls_Link implements ArrayAccess
{
	abstract function getHref();

	public static function build( $type, $object, array $arguments = array() ) // {{{
	{
		global $prefs;

		switch($type) {
		case 'wiki page':
			if( $prefs['feature_sefurl'] == 'y' )
				return new TikiPageControls_UrlLink( urlencode($object), 
					$arguments );
			else
				return new TikiPageControls_UrlLink( 'tiki-index.php',
					array_merge( $arguments, array( 'page' => $object ) ) );

		case 'structure':
			return new TikiPageControls_UrlLink( 'tiki-index.php',
				array_merge( $arguments, array( 'page_ref_id' => $object ) ) );

		case 'bloglist':
			if( $prefs['feature_sefurl'] == 'y' )
				return new TikiPageControls_UrlLink( 'blogs', $arguments );
			else
				return new TikiPageControls_UrlLink( 'tiki-list_blogs.php', $arguments );
			
		case 'blog':
			if( $prefs['feature_sefurl'] == 'y' )
				return new TikiPageControls_UrlLink( urlencode('blog'.$object), 
					$arguments );
			else
				return new TikiPageControls_UrlLink( 'tiki-view_blog.php',
					array_merge( $arguments, array( 'blogId' => $object ) ) );

		case 'url':
			return new TikiPageControls_UrlLink( $object,
				$arguments );
		
		case 'jscall':
			$func = $object;
			$args = array_map( 'json_encode', $arguments );
			return new TikiPageControls_UrlLink(
				'javascript:'
				. $func
				. '('
				. implode( ',', $args )
				. ')'
			);

		default:
			throw new Exception('Unknown link type: ' . $type);
		}
	} // }}}

	function offsetGet( $name ) // {{{
	{
		switch( $name ) {
		case 'href': return $this->getHref();
		}
	} // }}}

	function offsetExists( $name ) // {{{
	{
		switch( $name ) {
		case 'href': return true;
		default: return false;
		}
	} // }}}

	function offsetSet( $name, $value ) {}
	function offsetUnset( $name ) {}
}

class TikiPageControls_UrlLink extends TikiPageControls_Link
{
	private $base;
	private $arguments;

	function __construct( $base, array $arguments = array() ) // {{{
	{
		$this->base = $base;
		$this->arguments = $arguments;
	} // }}}

	function getHref() // {{{
	{
		if( count($this->arguments) ) {
			$query = http_build_query($this->arguments, null, '&');
			return "{$this->base}?$query";
		} else {
			return $this->base;
		}
	} // }}}
}

class TikiPageControls_WikiHelpLink extends TikiPageControls_UrlLink // {{{
{
	function __construct() // {{{
	{
		parent::__construct( 'javascript:flip(\'help_sections\')' );
	} // }}}
} // }}}

class TikiPageControls_Menu extends TikiPageControls_Element
{
	private $itemList = array();

	function __construct() // {{{
	{
		parent::__construct('menu');
	} // }}}

	function addItem( $label, $link, $permanentName = null ) // {{{
	{
		$item = new TikiPageControls_Element('menu_item');
		$item->setText( $label );
		$item->setLink( $link );

		if( is_null( $permanentName ) )
			$this->itemList[] = $item;
		else
			$this->itemList[$permanentName] = $item;

		return $item;
	} // }}}

	function addSeparator() // {{{
	{
		if( count($this->itemList) > 0 ) {
			$last = end($this->itemList);

			if( $last['type'] != 'separator' )
				$this->itemList[] = new TikiPageControls_Element('separator');
		}
	} // }}}

	function countIsBelow( $limit ) // {{{
	{
		return count($this->itemList) <= $limit;
	} // }}}

	function offsetGet( $name ) // {{{
	{
		switch( $name ) {
		case 'items': return $this->itemList;
		default: 
			if( parent::offsetExists( $name ) )
				return parent::offsetGet( $name );
			else
				return $this->itemList[$name];
		}
	} // }}}

	function offsetExists( $name ) // {{{
	{
		switch( $name ) {
		case 'items': return true;
		default: return parent::offsetExists( $name ) || isset($this->itemList[$name]);
		}
	} // }}}
}

abstract class TikiPageControls implements ArrayAccess
{
	private $heading;
	private $menus = array();
	private $tabs = array();
	private $help;
	private $user;

	private $type;
	private $objectId;
	private $objectName;

	private $mode = false;

	private $headerTemplate = 'tiki-pagecontrols.tpl';
	private $footerTemplate = 'tiki-pagecontrols-footer.tpl';

	private $commentCount;

	public abstract function build();

	function __construct( $type, $objectId, $objectName ) // {{{
	{
		global $user;
		$this->user = $user;
		$this->type = $type;
		$this->objectId = $objectId;
		$this->objectName = $objectName;
	} // }}}

	public static function factory( $objectType, $objectId, $objectName = null ) // {{{
	{
		switch( $objectType ) {
		case 'wiki page':
			global $tikilib;
			require_once('TikiPageControls_Wiki.php');
			if( is_numeric($objectId) && !empty($objectName) )
				$objectId = $objectName;

			$info = $tikilib->get_page_info( $objectId );
			return new TikiPageControls_Wiki($info);
		case 'blog':
			global $tikilib;
			require_once('TikiPageControls_Blog.php');

			$info = $tikilib->get_blog( $objectId );
			return new TikiPageControls_Blog($info);
		}
	} // }}}

	public function getUser() // {{{
	{
		return $this->user;
	} // }}}

	public function setUser( $user ) // {{{
	{
		$this->user = $user;

		return $this;
	} // }}}

	public function setHeading( $label, $link = null ) // {{{
	{
		$this->heading = new TikiPageControls_Element('heading');
		$this->heading->setText( $label );

		if( $link ) {
			$this->heading->setLink( $link );
		}

		return $this;
	} // }}}

	public function link( $type, $object, array $arguments = array() ) // {{{
	{
		return TikiPageControls_Link::build( $type, $object, $arguments );
	} // }}}

	protected function hasPerm( $permName ) // {{{
	{
		if( ! isset( $GLOBALS[$permName] ) ) {
			global $tikilib;
			$GLOBALS[$permName] = $tikilib->user_has_perm_on_object( $this->user, $this->objectId, $this->type, $permName ) ? 'y' : 'n';
		}
		
		return $GLOBALS[$permName] == 'y';
	} // }}}

	protected function hasAnyOfPerm( $permName ) // {{{
	{
		if( ! is_array($permName) )
			$permName = func_get_args();

		foreach( $permName as $p )
			if( $this->hasPerm($p) )
				return true;

		return false;
	} // }}}

	protected function hasPref( $prefName ) // {{{
	{
		global $prefs;
		
		foreach( func_get_args() as $prefName )
			if( ! isset( $prefs[$prefName] ) || $prefs[$prefName] != 'y' )
				return false;

		return true;
	} // }}}

	protected function getPref( $prefName ) // {{{
	{
		global $prefs;
		
		if( isset( $prefs[$prefName] ) )
			return $prefs[$prefName];
	} // }}}

	protected function addMenu( $permanentName, $label ) // {{{
	{
		$menu = new TikiPageControls_Menu;
		$menu->setText( $label );

		$this->menus[$permanentName] = $menu;

		return $menu;
	} // }}}

	protected function removeMenu( TikiPageControls_Menu $menu, $minimum = false ) // {{{
	{
		if( $minimum === false || $menu->countIsBelow( $minimum ) ) {
			$this->menus = array_diff( $this->menus, array( $menu ) );
		}
	} // }}}

	protected function clearTabs( $minimum = false ) // {{{
	{
		if( $minimum === false || count( $this->tabs ) <= $minimum )
			$this->tabs = array();
	} // }}}

	protected function addTab( $permanentName, $label, $link, $argument = null ) // {{{
	{
		$tab = new TikiPageControls_Element( 'tab' );
		$tab->setText( $label );
		$tab->setLink( $link );
		$tab->setArgument( $argument );

		$this->tabs[$permanentName] = $tab;

		return $tab;
	} // }}}

	protected function setHelp( $link ) // {{{
	{
		$this->help = new TikiPageControls_Element( 'help' );
		$this->help
			->setText( tra('Help') )
			->setLink( $link )
			->setIcon( 'help' );
	} // }}}

	private function renderTemplate( $template ) // {{{
	{
		global $smarty;
		$smarty->assign( 'controls', $this );

		return $smarty->fetch( $template );
	} // }}}

	function offsetGet( $name ) // {{{
	{
		switch( $name ) {
		case 'menus': return $this->menus;
		case 'tabs': return $this->tabs;
		case 'heading': return $this->heading;
		case 'help': return $this->help;
		case 'header': return $this->renderTemplate( $this->headerTemplate );
		case 'footer': return $this->renderTemplate( $this->footerTemplate );
		default: return $this->menus[$name];
		}
	} // }}}

	function offsetExists( $name ) // {{{
	{
		switch( $name ) {
		case 'menus':
		case 'tabs':
		case 'heading':
		case 'header': 
		case 'footer': 
		case 'help': 
			return true;
		default: return isset($this->menus[$name]);
		}
	} // }}}

	function setMode( $mode ) // {{{
	{
		$this->mode = $mode;

		return $this;
	} // }}}

	protected function getMode() // {{{
	{
		return $this->mode;
	} // }}}

	protected function isMode( $mode ) // {{{
	{
		if( ! is_array($mode) )
			$mode = func_get_args();

		return in_array( $this->mode, $mode );
	} // }}}

	protected function getCommentCount() // {{{
	{
		if( !is_null($this->commentCount) )
			return $this->commentCount;

		global $commentslib, $dbTiki;
		if( ! $commentslib ) {
			require_once 'lib/commentslib.php';
			$commentslib = new Comments($dbTiki);
		}

		return $this->commentCount = $commentslib->count_comments("{$this->type}:{$this->objectId}");
	} // }}}

	function eventIsWatched( $event, $type = null, $objectId = null ) // {{{
	{
		global $tikilib;
		if( ! $this->user )
			return false;

		if( ! $type )
			$type = $this->type;
		if( ! $objectId )
			$objectId = $this->objectId;

		return $tikilib->user_watches( $this->user, $event, $objectId, $type );
	} // }}}

	function addGroupWatchMenu( // {{{
		$menuName,
		$menuLabel,
		$enableLabel,
		$disableLabel,
		$event,
		$enableIcon,
		$disableIcon,
		$type,
		$object,
		$addBaseParams,
		$removeBaseParams
	)
	{
		global $tikilib, $userlib;

		$menu = $this->addMenu( $menuName, $menuLabel )
			->setIcon( $enableIcon );

		$groups = $userlib->list_all_groups();
		$watching = $tikilib->get_groups_watching( $type, $object, $event );

		foreach( $groups as $group ) {
			if( in_array( $group, $watching ) ) {
				$label = $disableLabel;
				$icon = $disableIcon;
				$params = array_merge( $removeBaseParams, array(
					'watch_group' => $group
				) );
			} else {
				$label = $enableLabel;
				$icon = $enableIcon;
				$params = array_merge( $addBaseParams, array(
					'watch_group' => $group
				) );
			}

			$link = $this->link( $type, $object, $params );
			$menu->addItem( $group, $link )
				->setIcon( $icon )
				->setTip( $label . $group );

			$this->removeMenu( $menu, 0 );
		}
	} // }}}

	function offsetSet( $name, $value ) {}
	function offsetUnset( $name ) {}
}

?>
