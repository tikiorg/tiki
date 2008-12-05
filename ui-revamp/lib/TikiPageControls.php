<?php

class TikiPageControls_Element
{
	private $text;
	private $argument;
	private $link;
	private $type;

	private $selected;

	function __construct( $type ) // {{{
	{
		$this->type = $type;
	} // }}}

	function setText( $text ) // {{{
	{
		$this->text = $text;
	} // }}}

	function setLink( $link ) // {{{
	{
		if( ! $link instanceof TikiPageControls_Link )
			throw new Exception("Expecting link instance.");

		$this->link = $link;
	} // }}}

	function setArgument( $value ) // {{{
	{
		$this->argument = $value;
	} // }}}

	function setSelected( $selected ) // {{{
	{
		$this->selected = (bool) $selected;
	} // }}}
}

abstract class TikiPageControls_Link
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
			$query = http_build_query($arguments, null, '&');
			return "{$this->base}?$query";
		} else {
			return $this->base;
		}
	} // }}}
}

class TikiPageControls_Menu
{
	function addItem()
	{
		return new TikiPageControls_Element;
	}

	function isEmpty()
	{
		return false;
	}
}

class TikiPageControls
{
	private $heading;

	public function setHeading( $label, $link = null ) // {{{
	{
		$this->heading = new TikiPageControls_Element('heading');
		$this->heading->setText( $label );
		$this->heading->setLink( $link );
	} // }}}

	public function link( $type, $object, array $arguments = array() ) // {{{
	{
		return TikiPageControls_Link::build( $type, $object, $arguments );
	} // }}}

	protected function hasPerm( $permName ) // {{{
	{
		return isset( $GLOBALS[$permName] ) && $GLOBALS[$permName] == 'y';
	} // }}}

	protected function hasPref( $prefName ) // {{{
	{
		global $prefs;
		return isset( $prefs[$prefName] ) && $prefs[$prefName] == 'y';
	} // }}}

	protected function addMenu( $label )
	{
		return new TikiPageControls_Menu;
	}

	protected function removeMenu( TikiPageControls_Menu $menu )
	{
	}

	protected function addTab( $label, $link, $argument = null )
	{
		return new TikiPageControls_Element;
	}
}

// TODO : Add menus
// TODO : Add items
// TODO : Add tab

?>
