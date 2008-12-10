<?php

class TikiPageControls_Element implements ArrayAccess
{
	private $text;
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
		case 'icon': return $this->getIconLink();
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
		default: return false;
		}
	} // }}}

	function offsetSet( $name, $value ) {}
	function offsetUnset( $name ) {}

	private function getIconLink() // {{{
	{
		return '<a href="' . htmlentities($this->link->getHref(), ENT_QUOTES, 'UTF-8') . '">' . $this->getIcon() . '</a>';
	} // }}}

	private function getIcon() // {{{
	{
		return '<img src="' . htmlentities($this->iconPath, ENT_QUOTES, 'UTF-8') . '" alt="' . htmlentities($this->text, ENT_QUOTES, 'UTF-8') . '" title="' . htmlentities($this->text, ENT_QUOTES, 'UTF-8') . '" class="icon"/>';
	} // }}}

	function __toString() // {{{
	{
		if( $this->type == 'separator' ) {
			return '<hr/>';
		}

		$text = htmlentities( $this->text, ENT_QUOTES, 'UTF-8' );

		if( !is_null($this->argument) ) {
			$text .= ' <span class="argument">' . htmlentities($this->argument, ENT_QUOTES, 'UTF-8') . '</span>';
		}

		if( $this->link ) {
			$text = '<a href="' . htmlentities($this->link->getHref(), ENT_QUOTES, 'UTF-8') . '">' . $text . '</a>';
		}

		return $text;
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

		case 'url':
			return new TikiPageControls_UrlLink( $object,
				$arguments );

		default:
			throw Exception('Unknown link type: ' . $type);
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

	function isEmpty() // {{{
	{
		return count($this->itemList) == 0;
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
	private $user;

	private $mode = false;

	private $template = 'tiki-pagecontrols.tpl';

	public abstract function build();

	function __construct() // {{{
	{
		global $user;
		$this->user = $user;
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
		}
	} // }}}

	public function getUser( $user ) // {{{
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
		$this->heading->setLink( $link );

		return $this;
	} // }}}

	public function link( $type, $object, array $arguments = array() ) // {{{
	{
		return TikiPageControls_Link::build( $type, $object, $arguments );
	} // }}}

	protected function hasPerm( $permName ) // {{{
	{
		return isset( $GLOBALS[$permName] ) && $GLOBALS[$permName] == 'y';
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
		return isset( $prefs[$prefName] ) && $prefs[$prefName] == 'y';
	} // }}}

	protected function addMenu( $permanentName, $label ) // {{{
	{
		$menu = new TikiPageControls_Menu;
		$menu->setText( $label );

		$this->menus[$permanentName] = $menu;

		return $menu;
	} // }}}

	protected function removeMenu( TikiPageControls_Menu $menu ) // {{{
	{
		$this->menus = array_diff( $this->menus, array( $menu ) );
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

	function __toString() // {{{
	{
		global $smarty;
		$smarty->assign( 'controls', $this );

		return $smarty->fetch( $this->template );
	} // }}}

	function offsetGet( $name ) // {{{
	{
		switch( $name ) {
		case 'menus': return $this->menus;
		case 'tabs': return $this->tabs;
		case 'heading': return $this->heading;
		default: return $this->menus[$name];
		}
	} // }}}

	function offsetExists( $name ) // {{{
	{
		switch( $name ) {
		case 'menus': return true;
		case 'tabs': return true;
		case 'heading': return true;
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

	function offsetSet( $name, $value ) {}
	function offsetUnset( $name ) {}
}

?>
