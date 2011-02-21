<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once( 'lib/Horde/Yaml.php' );
require_once( 'lib/Horde/Yaml/Loader.php' );
require_once( 'lib/Horde/Yaml/Node.php' );
require_once( 'lib/Horde/Yaml/Exception.php' );


class Tiki_Profile
{
	const SHORT_PATTERN = '/^\$((([\w\.-]+):)?((\w+):))?(\w+)$/';
	const LONG_PATTERN = '/\$profileobject:((([\w\.-]+):)?((\w+):))?(\w+)\$/';
	const INFO_REQUEST = '/\$profilerequest:([^\$\|]+)(\|(\w+))?\$([^\$]+)\$/';

	private $url;
	private $pageUrl;
	private $domain;
	private $profile;

	public $pageContent = null;
	private $data = array();

	private $feedback = array();

	private $objects = null;

	private static $known = array();
	private static $resolvePrefix = null;
	private static $developerMode = false;

	function setFeedback( $feed ) // {{{
	{
		if (is_array( $feed )) {
			$this->feedback = $feed;
		} else {
			$this->feedback[] = $feed;
		}
	} // }}}
	function getFeedback( $index = null ) // {{{
	{
		if (! is_null( $index ) && $index < count($this->feedback) ) {
			return $this->feedback[ $index ];
		} else {
			return $this->feedback;
		}
	} // }}}

	public static function enableDeveloperMode() // {{{
	{
		self::$developerMode = true;
	} // }}}

	public static function convertLists( $data, $conversion, $prependKey = false ) // {{{
	{
		foreach( $conversion as $key => $endValue )
		{
			if( ! isset( $data[$key] ) )
				continue;

			$data[$key] = (array) $data[$key];

			foreach( $data[$key] as $item )
			{
				if( $prependKey === true )
					$item = "{$key}_{$item}";
				elseif( ! empty( $prependKey ) )
					$item = $prependKey . $item;

				if( !isset( $data[$item] ) )
					$data[$item] = $endValue;
			}

			unset( $data[$key] );
		}

		return $data;
	} // }}}

	public static function convertYesNo( $data ) // {{{
	{
		$copy = $data;
		foreach( $copy as &$value )
			if( is_bool( $value ) )
				$value = $value ? 'y' : 'n';

		return $copy;
	} // }}}

	public static function getProfileKeyFor( $domain, $profile ) // {{{
	{
		return $domain . '/' . $profile;
	} // }}}

	public static function useUnicityPrefix( $prefix ) // {{{
	{
		self::$resolvePrefix = $prefix;
	} // }}}

	public static function withPrefix( $profile ) // {{{
	{
		if( self::$resolvePrefix )
			return self::$resolvePrefix . ':' . $profile;
		else
			return $profile;
	} // }}}

	private static function getObjectReference( $object, $full = true ) // {{{
	{
		// If a prefix was set, attempt to isolate the lookup to the prefix first
		if( $full ) {
			$withPrefix = $object;
			$withPrefix['profile'] = self::withPrefix( $withPrefix['profile'] );

			if( ! is_null( $ref = self::getObjectReference( $withPrefix, false ) ) )
				return $ref;
		}

		$serialized = Tiki_Profile_Object::serializeNamedObject( $object );

		if( ! isset( self::$known[$serialized] ) )
			self::$known[$serialized] = self::findObjectReference( $object );

		return self::$known[$serialized];
	} // }}}

	private static function findObjectReference( $object ) // {{{
	{
		global $tikilib;

		$result = $tikilib->query( "SELECT value FROM tiki_profile_symbols WHERE domain = ? AND profile = ? AND object = ?",
			array( $object['domain'], $object['profile'], $object['object'] ) );

		if( $row = $result->fetchRow() )
			return $row['value'];

		return null;
	} // }}}

	public static function fromUrl( $url ) // {{{
	{
		$profile = new self;
		$profile->url = $url;

		if( $profile->analyseMeta( $url ) ) {

			// Obtain the page export
			$content = TikiLib::httprequest( $url );
			$content = html_entity_decode( $content );
			$content = str_replace( "\r", '', $content );

			// Find content start (strip headers)
			$begin = strpos( $content, "\n\n" );
			if( ! $begin )
				return false;

			$content = substr( $content, $begin + 2 );

			$profile->loadYaml( $content );
		}

		return $profile;
	} // }}}

	public static function fromNames( $domain, $profile ) // {{{
	{
		if( strpos( $domain, '://' ) === false )
			$domain = "http://$domain";

		if( $domain == 'tiki://local' ) {
			return self::fromDb( $profile );
		} else {
			if (self::$developerMode) {
				$url = "$domain/tiki-export_wiki_pages.php?latest=1&page=" . urlencode( $profile );
			} else {
				$url = "$domain/tiki-export_wiki_pages.php?page=" . urlencode( $profile );
			}

			return self::fromUrl( $url );
		}
	} // }}}
	
	public static function fromDb( $pageName ) // {{{
	{
		global $tikilib, $wikilib;
		require_once 'lib/wiki/wikilib.php';

		$profile = new self;
		$profile->domain = 'tiki://local';
		$profile->profile = $pageName;
		$profile->pageUrl = $wikilib->sefurl($pageName);
		$profile->url = 'tiki://local/' . urlencode($pageName);

		$info = $tikilib->get_page_info( $pageName );
		$content = html_entity_decode( $info['data'] );
		$tikilib->parse_wiki_argvariable($content);
		$profile->loadYaml( $content );

		return $profile;
	} // }}}

	public static function fromString( $string, $name = '' ) // {{{
	{
		$profile = new self;
		$profile->domain = 'tiki://local';
		$profile->profile = $name;
		$profile->pageUrl = $name;
		$profile->url = 'tiki://local/' . $name;

		$content = html_entity_decode( $string );
		$profile->loadYaml( $content );

		return $profile;
	} // }}}

	private function __construct() // {{{
	{
	} // }}}

	function __get( $name ) // {{{
	{
		switch( $name )
		{
		case 'domain':
		case 'profile':
		case 'url':
		case 'pageUrl':
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

		$this->pageUrl = dirname( $url ) . '/' . urlencode($this->profile);

		return true;
	} // }}}
	
	public function refreshYaml() {
		$this->objects = null;
		$this->loadYaml($this->pageContent);
	}

	private function loadYaml( $content ) // {{{
	{
		$this->pageContent = $content;

		$pos = 0;

		$this->data = array();

		while( false !== $base = $this->findNextPluginStart($content, $pos) )
		{
			$begin = strpos( $content, ')}', $base ) + 2;
			$end = strpos( $content, '{CODE}', $base );
			$pos = $end + 6;

			if( false === $base || false === $begin || false === $end )
				return false;

			$yaml = substr( $content, $begin, $end - $begin );

			$data = Horde_Yaml::load( $yaml );

			foreach( $data as $key => $value )
			{
				if( array_key_exists( $key, $this->data ) )
					$this->data[$key] = $this->mergeData( $this->data[$key], $value );
				else
					$this->data[$key] = $value;
			}
		}

		$this->fetchExternals();
		$this->getObjects();
	} // }}}
	
	private function findNextPluginStart($content, $pos) {
		preg_match('/\{CODE\(\s*caption\s*=[>]?\s*[\'"]?YAML/', substr($content, $pos), $matches);
		if (count($matches) > 0) {
			$pattern = $matches[0];
		} else {
			$pattern = '{CODE(caption=>YAML';
		}
		return strpos( $content, $pattern, $pos );
	}

	private function fetchExternals() // {{{
	{
		$this->traverseForExternals( $this->data );
	} // }}}
	
	private function traverseForExternals( &$data ) // {{{
	{
		if( is_array( $data ) ) {
			foreach( $data as &$value ) {
				$this->traverseForExternals( $value );
			}
		} else if ( 0 === strpos( $data, 'wikicontent:' ) ) {
			$pageName = substr( $data, strlen('wikicontent:') );
			$data = $this->getPageContent( $pageName );
		} else if ( 0 === strpos( $data, 'wikiparsed:' ) ) {
			$pageName = substr( $data, strlen('wikiparsed:') );
			$data = $this->getPageParsed( $pageName );
		}
	} // }}}

	public function getPageContent( $pageName ) // {{{
	{
		if ($this->domain == 'tiki://local') {
			global $tikilib;
			$info = $tikilib->get_page_info($pageName);
			if (empty($info)) {
				$this->setFeedback(tra('Page cannot be found').' '.$pageName);
				return null;
			}
			return $info['data'];
		}
		$exportUrl = dirname( $this->url ) . '/tiki-export_wiki_pages.php?'
			. http_build_query( array( 'page' => $pageName ) );

		$content = TikiLib::httprequest( $exportUrl );
		$content = str_replace( "\r", '', $content );
		$begin = strpos( $content, "\n\n" );

		if( $begin !== false )
			return substr( $content, $begin + 2 );
		else
			return null;
	} // }}}

	public function getPageParsed( $pageName ) // {{{
	{
		if ($this->domain == 'tiki://local' || strpos($this->domain, 'localhost') === 0) {
			global $tikilib;
			$info = $tikilib->get_page_info($pageName, true, true);
			if (empty($info)) {
				$this->setFeedback(tra('Page cannot be found').' '.$pageName);
				return null;
			}
			return $tikilib->parse_data($info['data']);
		}
		$pageUrl = dirname( $this->url ) . '/tiki-index_raw.php?'
			. http_build_query( array( 'page' => $pageName ) );

		$content = TikiLib::httprequest( $pageUrl );
		// index_raw replaces index.php with itself, so undo that here
		$content = str_replace( 'tiki-index_raw.php', 'tiki-index.php', $content );

		return $content;
	} // }}}

	function mergeData( $old, $new ) // {{{
	{
		if( is_array( $old ) && is_array( $new ) )
		{
			foreach( $new as $key => $value )
			{
				if( is_numeric( $key ) )
					$old[] = $value;
				else
					$old[$key] = $this->mergeData( $old[$key], $value );
			}

			return $old;
		}
		else
			return $new;
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
		elseif( preg_match( self::SHORT_PATTERN, $value, $parts ) )
			$array[] = $this->convertReference( $parts );
		elseif( preg_match_all( self::LONG_PATTERN, $value, $parts, PREG_SET_ORDER ) ) {
			foreach( $parts as $row )
				$array[] = $this->convertReference( $row );
		}

		return $array;
	} // }}}

	function convertReference( $parts ) // {{{
	{
		list( $full, $null0, $null1, $domain, $null2, $profile, $object ) = $parts;

		if( empty( $domain ) )
			$domain = $this->domain;
		if( empty( $profile ) )
			$profile = $this->profile;

		return array( 'domain' => $domain, 'profile' => $profile, 'object' => $object );
	} // }}}

	function getRequiredInput() // {{{
	{
		return $this->traverseForRequiredInput( $this->data );
	} // }}}

	function traverseForRequiredInput( $value ) // {{{
	{
		$array = array();
		if( is_array( $value ) )
			foreach( $value as $v )
				$array = array_merge( $array, $this->traverseForRequiredInput( $v ) );
		elseif( preg_match( self::INFO_REQUEST, $value, $parts ) )
			$array[$parts[1]] = $parts[4];

		return $array;
	} // }}}

	function getRequiredProfiles( $recursive = false, $known = array() ) // {{{
	{
		$profiles = array();

		foreach( $this->getExternalReferences() as $ext )
		{
			$key = Tiki_Profile::getProfileKeyFor( $ext['domain'], $ext['profile'] );
			if( array_key_exists( $key, $known ) || array_key_exists( $key, $profiles ) )
				continue;

			$profiles[$key] = self::fromNames( $ext['domain'], $ext['profile'] );
		}

		if( $recursive )
			foreach( $profiles as $profile )
				$profiles = array_merge( $profiles, $profile->getRequiredProfiles( true, $profiles ) );

		return $profiles;
	} // }}}

	public function replaceReferences( &$data, $suppliedUserData = false ) // {{{
	{
		if( $suppliedUserData === false )
			$suppliedUserData = $this->getRequiredInput();

		if( is_array( $data ) ) {
			foreach( $data as &$sub )
				$this->replaceReferences( $sub, $suppliedUserData );

			$toReplace = array();
			foreach( array_keys( $data ) as $key ) {
				$newKey = $key;
				$this->replaceReferences( $newKey, $suppliedUserData );
				if( $newKey != $key )
					$toReplace[$key] = $newKey;
			}

			foreach( $toReplace as $old => $new ) {
				$data[$new] = $data[$old];
				unset( $data[$old] );
			}
		}
		else
		{
			if( preg_match( self::SHORT_PATTERN, $data, $parts ) )
			{
				$object = $this->convertReference( $parts );
				$data = self::getObjectReference( $object );
				return;
			}

			$needles = array();
			$replacements = array();

			if( preg_match_all( self::LONG_PATTERN, $data, $parts, PREG_SET_ORDER ) )
				foreach( $parts as $row )
				{
					$object = $this->convertReference( $row );

					$needles[] = $row[0];
					$replacements[] = self::getObjectReference( $object );
				}

			if( preg_match_all( self::INFO_REQUEST, $data, $parts, PREG_SET_ORDER ) )
				foreach( $parts as $row )
				{
					list( $full, $label, $junk, $filter, $default ) = $row;

					if( ! array_key_exists( $label, $suppliedUserData ) )
						$value = $default;
					else
						$value = $suppliedUserData[$label];

					if( $filter )
						$value = TikiFilter::get($filter)->filter($value);
					else
						$value = TikiFilter::get('xss')->filter($value);

					if( empty($value) )
						$value = $default;

					$needles[] = $full;
					$replacements[] = $value;
				}
			
			if( count( $needles ) )
				$data = str_replace( $needles, $replacements, $data );

			$needles = array();
			$replacements = array();

			// Replace date formats D(...) to unix timestamps
			if( preg_match_all( "/D\\(([^\\)]+)\\)/", $data, $parts, PREG_SET_ORDER ) )
				foreach( $parts as $row )
				{
					list( $full, $date ) = $row;

					if( false !== $conv = strtotime( $date ) )
					{
						$needles[] = $full;
						$replacements = $conv;
					}
				}

			if( count( $needles ) )
				$data = str_replace( $needles, $replacements, $data );
		}
	} // }}}

	function getInstructionPage() // {{{
	{
		if( isset( $this->data['instructions'] ) ) {
			return $this->data['instructions'];
		}
	} // }}}

	function getPreferences() // {{{
	{
		$prefs = array();

		if( array_key_exists( 'preferences', $this->data ) )
		{
			$prefs = Tiki_Profile::convertLists( $this->data['preferences'], array(
				'enable' => 'y', 
				'disable' => 'n'
			) );

			$prefs = Tiki_Profile::convertYesNo( $prefs );
		}

		return $prefs;
	} // }}}

	function getGroupMap() // {{{
	{
		if( ! isset( $this->data['mappings'] ) ) {
			return array();
		}

		return $this->data['mappings'];
	} // }}}

	function getPermissions( $groupMap = array() ) // {{{
	{
		if( ! array_key_exists( 'permissions', $this->data ) )
			return array();

		$groups = array();
		foreach( $this->data['permissions'] as $groupName => $data )
		{
			if( isset( $groupMap[ $groupName ] ) ) {
				$groupName = $groupMap[$groupName];
			}

			$permissions = Tiki_Profile::convertLists( $data, array( 'allow' => 'y', 'deny' => 'n' ), 'tiki_p_' );
			$permissions = Tiki_Profile::convertYesNo( $permissions );
			foreach( array_keys( $permissions ) as $key )
				if( strpos( $key, 'tiki_p_' ) !== 0 )
					unset( $permissions[$key] );

			$defaultInfo = array(
				'description' => '',
				'home' => '',
				'user_tracker' => 0,
				'user_tracker_field' => 0,
				'group_tracker' => 0,
				'group_tracker_field' => 0,
				'user_signup' => 'n',
				'default_category' => 0,
				'theme' => '',
				'registration_fields' => array(),
				'include' => array(),
				'autojoin' => 'n',
			);
			foreach( $defaultInfo as $key => $value )
				if( array_key_exists( $key, $data ) )
				{
					if( is_array( $value ) )
						$defaultInfo[$key] = (array) $data[$key];
					else
						$defaultInfo[$key] = $data[$key];
				}

			$objects = array();
			if( isset( $data['objects'] ) )
				foreach( $data['objects'] as $o )
				{
					if( !isset($o['type'], $o['id']) ) {
						$this->setFeedback(tra('Syntax error: ').tra("Permissions' object must have a field 'type' and 'id'"));
						continue;
					}

					$perms = Tiki_Profile::convertLists( $o, array( 'allow' => 'y', 'deny' => 'n' ), 'tiki_p_' );
					$perms = Tiki_Profile::convertYesNo( $perms );

					foreach( array_keys( $perms ) as $key )
						if( strpos( $key, 'tiki_p_' ) !== 0 )
							unset( $perms[$key] );

					$o['permissions'] = $perms;
					$objects[] = $o;
				}

			$groups[$groupName] = array(
				'permissions' => $permissions,
				'objects' => $objects,
				'general' => $defaultInfo,
			);
		}

		return $groups;
	} // }}}

	function getObjects() // {{{
	{
		if( !is_null( $this->objects ) )
			return $this->objects;

		$objects = array();

		if( array_key_exists( 'objects', $this->data ) )
			foreach( $this->data['objects'] as &$entry )
			{
				$o = new Tiki_Profile_Object( $entry, $this );
				if( $o->isWellStructured() ) {
					$objects[] = $o;
				} else {
					$str = '';
					foreach ($entry as $k => $v) {
						$str .= empty($str) ? '' : ', ';
						$str .= "$k: $v";
					}
					$this->setFeedback(tra('Syntax error: ').$str."\n".tra("Needs a 'type' and 'data' field"));
				}
			}

		$classified = array();
		$names = array();

		// Order object creations to make sure all objects are created when needed
		// Circular dependencies get dicarded
		$counter = 0;
		while( ! empty( $objects ) )
		{
			// Circular dependency found... give what we have
			if( $counter++ > count($objects) * 2 ) {
				$this->setFeedback( tra('Circular reference') . ': ' . implode( ', ', array_unique( $refs ) ) );
				break;
			}

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

	function removeSymbols() // {{{
	{
		global $tikilib;
		$tikilib->query( "DELETE FROM tiki_profile_symbols WHERE domain = ? AND profile = ?",
			array( $this->domain, self::withPrefix($this->profile) ) );

		$key = self::getProfileKeyFor( $this->domain, self::withPrefix($this->profile) );
		foreach( array_keys(self::$known) as $obj )
			if( strpos( $obj, $key ) === 0 )
				unset(self::$known[$obj]);
	} // }}}

	function getProfileKey() // {{{
	{
		return self::getProfileKeyFor( $this->domain, $this->withPrefix( $this->profile ) );
	} // }}}
}

class Tiki_Profile_Object
{
	private $data;
	private $profile;
	private $id = false;

	private $references = null;

	public static function serializeNamedObject( $object ) // {{{
	{
		return sprintf( "%s#%s", Tiki_Profile::getProfileKeyFor($object['domain'], $object['profile']), $object['object'] );
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
	
	function __construct( &$data, Tiki_Profile $profile ) // {{{
	{
		$this->data = &$data;
		$this->profile = $profile;
	} // }}}

	function getDescription() {
		$str = '';
		if ($this->isWellStructured()) {
			$str .= $this->getType().' ';
			$str .= '"'.isset($this->data['data']['name']) ? $this->data['data']['name'] : tra('No name').'"';
		} else {
			$str .= tra('Bad object');
		}
		return $str;
	}
	
	function isWellStructured() // {{{
	{
		$is =  isset( $this->data['type'], $this->data['data'] );
		return $is;
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
			array( $this->profile->domain, $this->profile->withPrefix($this->profile->profile), $name, $this->getType(), $this->id, $named ) );
	} // }}}

	function getInternalReferences() // {{{
	{
		if( !is_null( $this->references ) )
			return $this->references;

		$this->references = $this->traverseForReferences( $this->data );
		return $this->references;
	} // }}}

	function getData() // {{{
	{
		if( array_key_exists( 'data', $this->data ) )
			return $this->data['data'];

		return array();
	} // }}}

	public function replaceReferences( &$data, $suppliedUserData = false ) // {{{
	{
		$this->profile->replaceReferences( $data, $suppliedUserData );
	} // }}}

	public function refreshExternals() // {{{
	{
		$this->profile->refreshYaml();
	} // }}}

	private function traverseForReferences( $value ) // {{{
	{
		$array = array();
		if( is_array( $value ) )
			foreach( $value as $v )
				$array = array_merge( $array, $this->traverseForReferences( $v ) );
		elseif( preg_match( Tiki_Profile::SHORT_PATTERN, $value, $parts ) )
		{
			$ref = $this->profile->convertReference( $parts );
			if( $this->profile->domain == $ref['domain']
				&& $this->profile->profile == $ref['profile'] )
				$array[] = $ref['object'];
		}
		elseif( preg_match_all( Tiki_Profile::LONG_PATTERN, $value, $parts, PREG_SET_ORDER ) )
		{
			foreach( $parts as $row )
			{
				$ref = $this->profile->convertReference( $row );
				if( $this->profile->domain == $ref['domain']
					&& $this->profile->profile == $ref['profile'] )
					$array[] = $ref['object'];
			}
		}

		return $array;
	} // }}}

	function getProfile() // {{{
	{
		return $this->profile;
	} // }}}

	function __get( $name ) // {{{
	{
		if( array_key_exists( $name, $this->data['data'] ) )
			return $this->data['data'][$name];
	} // }}}
}
