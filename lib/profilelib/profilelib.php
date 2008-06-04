<?php
require_once( 'Horde/Yaml.php' );
require_once( 'Horde/Yaml/Loader.php' );
require_once( 'Horde/Yaml/Node.php' );
require_once( 'Horde/Yaml/Exception.php' );


class Tiki_Profile
{
	private $url;
	private $domain;
	private $profile;
	private $data = array();

	private $objects = null;

	function __construct( $url ) // {{{
	{
		$this->url = $url;

		if( $this->analyseMeta( $url ) )
			$this->loadYaml( $url );
	} // }}}

	function __get( $name ) // {{{
	{
		switch( $name )
		{
		case 'domain':
		case 'profile':
		case 'url':
			return $this->$name;
		}
	} // }}}

	private function analyseMeta( $url ) // {{{
	{
		$parts = parse_url( $url );

		if( ! isset( $parts['query'], $parts['host'], $parts['path'] ) )
			return false;

		parse_str( $parts['query'], $args );

		if( ! isset( $args['page'] ) )
			return false;

		$dir = dirname( $parts['path'] );
		$this->domain = $parts['host'] . rtrim( $dir, '/' );
		$this->profile = $args['page'];

		return true;
	} // }}}

	private function loadYaml( $url ) // {{{
	{
		$content = file_get_contents( $url );
		$content = html_entity_decode( $content );

		$base = strpos( $content, '{CODE(caption=>YAML' );
		$begin = strpos( $content, ')}', $base ) + 2;
		$end = strpos( $content, '{CODE}', $base );

		if( false === $base || false === $begin || false === $end )
			return false;

		$yaml = substr( $content, $begin, $end - $begin );

		$this->data = Horde_Yaml::load( $yaml );
	} // }}}

	function getNamedObjects() // {{{
	{
		if( ! isset( $this->data['objects'] ) )
			return array();

		$named = array();

		foreach( $this->data['objects'] as $object )
			if( isset( $object['ref'] ) )
				$named[] = array( 'domain' => $this->domain, 'profile' => $this->profile, 'object' => $object['ref'] );

		return $named;
	} // }}}

	function getReferences() // {{{
	{
		return $this->traverseForReferences( $this->data );
	} // }}}

	function getExternalReferences() // {{{
	{
		$out = array();

		foreach( $this->getReferences() as $ref )
			if( $this->domain != $ref['domain'] || $this->profile != $ref['profile'] )
				$out[] = $ref;

		return $out;
	} // }}}

	private function traverseForReferences( $value ) // {{{
	{
		$array = array();
		if( is_array( $value ) )
			foreach( $value as $v )
				$array = array_merge( $array, $this->traverseForReferences( $v ) );
		elseif( preg_match( '/^\$((([^:]+):)?(([^:]+):))?(.+)$/', $value, $parts ) )
			$array[] = $this->convertReference( $parts );

		return $array;
	} // }}}

	private function convertReference( $parts ) // {{{
	{
		list( $full, $null0, $null1, $domain, $null2, $profile, $object ) = $parts;

		if( empty( $domain ) )
			$domain = $this->domain;
		if( empty( $profile ) )
			$profile = $this->profile;

		return array( 'domain' => $domain, 'profile' => $profile, 'object' => $object );
	} // }}}

	function getRequiredProfiles( $recursive = false, $known = array() ) // {{{
	{
		$profiles = array();

		foreach( $this->getExternalReferences() as $ext )
		{
			$url = "http://{$ext['domain']}/tiki-export_wiki_pages.php?page=" . urlencode( $ext['profile'] );
			if( array_key_exists( $url, $known ) || array_key_exists( $url, $profiles ) )
				continue;

			$profiles[$url] = new self( $url );
		}

		if( $recursive )
			foreach( $profiles as $profile )
				$profiles = array_merge( $profiles, $profile->getRequiredProfiles( true, $profiles ) );

		return $profiles;
	} // }}}

	function getPreferences() // {{{
	{
		$prefs = array();

		if( array_key_exists( 'preferences', $this->data ) )
			foreach( $this->data['preferences'] as $pref => $value )
				$prefs[$pref] = is_bool( $value ) ? ($value?'y':'n') : $value;

		return $prefs;
	} // }}}

	function getObjects() // {{{
	{
		if( !is_null( $this->objects ) )
			return $this->objects;

		$objects = array();

		if( array_key_exists( 'objects', $this->data ) )
			foreach( $this->data['objects'] as $entry )
			{
				$o = new Tiki_Profile_Object( $entry, $this->domain, $this->profile );
				if( $o->isWellStructured() )
					$objects[] = $o;
			}

		$classified = array();
		$names = array();

		// Order object creations to make sure all objects are created when needed
		// Circular dependencies get dicarded
		$counter = 0;
		while( ! empty( $objects ) )
		{
			// Circular dependency found... give what we have
			if( $counter++ > count($objects) * 2 )
				break;

			$object = array_shift( $objects );
			$refs = $object->getInternalReferences();
			$refs = array_diff( $refs, $names );
			if( empty( $refs ) )
			{
				$counter = 0;
				$classified[] = $object;
				if( $object->getRef() )
					$names[] = $object->getRef();
			}
			else
				$objects[] = $object;
		}

		$this->objects = $classified;
		return $this->objects;
	} // }}}
}

class Tiki_Profile_Object
{
	private $data;
	private $domain;
	private $profile;
	private $id = false;

	private $references = null;

	public static function serializeNamedObject( $object ) // {{{
	{
		return sprintf( "http://%s/%s#%s", $object['domain'], $object['profile'], $object['object'] );
	} // }}}

	public static function getNamedObjects() // {{{
	{
		global $tikilib;
	
		$objects = array();

		$result = $tikilib->query( "SELECT domain, profile, object FROM tiki_profile_symbols WHERE named = 'y'" );
		while( $row = $result->fetchRow() )
			$objects[] = $row;

		return $objects;
	} // }}}
	
	function __construct( $data, $domain, $profile ) // {{{
	{
		$this->data = $data;
		$this->domain = $domain;
		$this->profile = $profile;
	} // }}}

	function isWellStructured() // {{{
	{
		return isset( $this->data['type'], $this->data['data'] );
	} // }}}

	function getType() // {{{
	{
		return $this->data['type'];
	} // }}}

	function getRef() // {{{
	{
		if( array_key_exists( 'ref', $this->data ) )
			return $this->data['ref'];
	} // }}}

	function getValue() // {{{
	{
		return $this->id;
	} // }}}

	function setValue( $value ) // {{{
	{
		global $tikilib;
		$this->id = $value;

		$named = 'y';
		if( ! $name = $this->getRef() )
		{
			$name = uniqid();
			$named = 'n';
		}

		$tikilib->query( "INSERT INTO tiki_profile_symbols (domain, profile, object, type, value, named) VALUES(?, ?, ?, ?, ?, ?)", 
			array( $this->domain, $this->profile, $name, $this->getType(), $this->id, $named ) );
	} // }}}

	function getInternalReferences() // {{{
	{
		if( !is_null( $this->references ) )
			return $this->references;

		return $this->references = $this->traverseForReferences( $this->data );
	} // }}}

	private function traverseForReferences( $value ) // {{{
	{
		$array = array();
		if( is_array( $value ) )
			foreach( $value as $v )
				$array = array_merge( $array, $this->traverseForReferences( $v ) );
		elseif( preg_match( '/^\$([^:]+)$/', $value, $parts ) )
			$array[] = $parts[1];

		return $array;
	} // }}}

	function __get( $name ) // {{{
	{
		if( array_key_exists( $name, $this->data['data'] ) )
			return $this->data['data'][$name];
	} // }}}


}

?>
