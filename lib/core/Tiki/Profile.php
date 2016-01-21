<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile
{
	const SHORT_PATTERN = '/^\$((([\w\.\/-]+):)?((\w+):))?(\w+)$/';
	const LONG_PATTERN = '/\$profileobject:((([\w\.\/-]+):)?((\w+):))?(\w+)\$/';
	const INFO_REQUEST = '/\$profilerequest:([^\$\|]+)(\|(\w+))?\$([^\$]*)\$/';
	const PREFERENCE_PATTERN = '/\$preference:(\w+)\$/';

	private $transport;
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
		if (is_array($feed)) {
			$this->feedback = $feed;
		} else {
			$this->feedback[] = $feed;
		}
	} // }}}
	function getFeedback( $index = null ) // {{{
	{
		if (! is_null($index) && $index < count($this->feedback) ) {
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
		foreach ( $conversion as $key => $endValue ) {
			if ( ! isset($data[$key]) )
				continue;

			$data[$key] = (array) $data[$key];

			foreach ( $data[$key] as $item ) {
				if ( $prependKey === true )
					$item = "{$key}_{$item}";
				elseif ( ! empty( $prependKey ) )
					$item = $prependKey . $item;

				if ( !isset($data[$item]) )
					$data[$item] = $endValue;
			}

			unset( $data[$key] );
		}

		return $data;
	} // }}}

	public static function convertYesNo( $data ) // {{{
	{
		$copy = (array) $data;
		foreach ( $copy as &$value )
			if ( is_bool($value) )
				$value = $value ? 'y' : 'n';

		return $copy;
	} // }}}

	public static function getProfileKeyfor ( $domain, $profile ) // {{{
	{
		if ( strpos($domain, '://') === false ) {
			if ( is_dir($domain) ) {
				$domain = "file://" . $domain;
			} else {
				$domain = "http://" . $domain;
			}
		}
		return $domain . '/' . $profile;
	} // }}}

	public static function useUnicityPrefix( $prefix ) // {{{
	{
		self::$resolvePrefix = $prefix;
	} // }}}

	public static function withPrefix( $profile ) // {{{
	{
		if ( self::$resolvePrefix )
			return self::$resolvePrefix . ':' . $profile;
		else
			return $profile;
	} // }}}

	private static function getObjectReference( $object, $full = true ) // {{{
	{
		// If a prefix was set, attempt to isolate the lookup to the prefix first
		if ( $full ) {
			$withPrefix = $object;
			$withPrefix['profile'] = self::withPrefix($withPrefix['profile']);

			if ( ! is_null($ref = self::getObjectReference($withPrefix, false)) )
				return $ref;
		}

		$serialized = Tiki_Profile_Object::serializeNamedObject($object);

		if ( ! isset(self::$known[$serialized]) )
			self::$known[$serialized] = self::findObjectReference($object);

		return self::$known[$serialized];
	} // }}}

	private static function findObjectReference($object) // {{{
	{
		global $tikilib;

		if ( strpos($object['domain'], '://') === false ) {
			if ( is_dir($object['domain']) ) {
				$object['domain'] = "file://" . $object['domain'];
			} else {
				$object['domain'] = "http://" . $object['domain'];
			}
		}
		$shortdomain = substr($object['domain'], strpos($object['domain'], '://') + 3);

		$result = $tikilib->query(
			"SELECT value FROM tiki_profile_symbols WHERE (domain = ? || domain = ?) AND profile = ? AND object = ?",
			array( $object['domain'], $shortdomain, $object['profile'], $object['object'] )
		);

		if ( $row = $result->fetchRow() )
			return $row['value'];

		return null;
	} // }}}

	public static function fromUrl( $url ) // {{{
	{
		$profile = new self;
		$profile->transport = new Tiki_Profile_Transport_Repository($url);

		if ( $profile->analyseMeta($url) ) {

			// Obtain the page export
			$content = TikiLib::lib('tiki')->httprequest($url);
			$content = html_entity_decode($content);
			$content = str_replace("\r", '', $content);

			// Find content start (strip headers)
			$begin = strpos($content, "\n\n");
			if ( ! $begin ) {
				return false;
			}

			$content = trim(substr($content, $begin + 2));

			if (empty($content)) {
				return false;
			}

			$profile->loadYaml($content);
		}

		return $profile;
	} // }}}

	public static function fromNames( $domain, $profile ) // {{{
	{
		if ( strpos($domain, '://') === false) {
			if ( is_dir($domain) ) {
				$domain = "file://$domain";
			} else {
				$domain = "http://$domain";
			}
		}

		if ( $domain == 'tiki://local' ) {
			return self::fromDb($profile);
		} elseif (strpos($domain, 'file://') === 0) {
			$path = substr($domain, strlen('file://'));

			return self::fromFile($path, $profile);
		} else {
			if (self::$developerMode) {
				$url = "$domain/tiki-export_wiki_pages.php?latest=1&page=" . urlencode($profile);
			} else {
				$url = "$domain/tiki-export_wiki_pages.php?page=" . urlencode($profile);
			}

			return self::fromUrl($url);
		}
	} // }}}

	public static function fromDb( $pageName ) // {{{
	{
		$tikilib = TikiLib::lib('tiki');
		$wikilib = TikiLib::lib('wiki');
		$parserlib = TikiLib::lib('parser');

		$profile = new self;
		$profile->domain = 'tiki://local';
		$profile->profile = $pageName;
		$profile->pageUrl = $wikilib->sefurl($pageName);
		$profile->transport = new Tiki_Profile_Transport_Local;

		if ($info = $tikilib->get_page_info($pageName)) {
			$content = html_entity_decode($info['data']);
			$parserlib->parse_wiki_argvariable($content);
			$profile->loadYaml($content);

			return $profile;
		}

		return false;
	} // }}}

	public static function fromString( $string, $name = '' ) // {{{
	{
		$profile = new self;
		$profile->domain = 'tiki://local';
		$profile->profile = $name;
		$profile->pageUrl = $name;
		$profile->transport = new Tiki_Profile_Transport_Local;

		$content = html_entity_decode($string);
		$profile->loadYaml($content);

		return $profile;
	} // }}}

	public static function fromFile($path, $name) // {{{
	{
		$path = rtrim($path, '/');
		$ymlPath = "$path/$name.yml";

		// Make paths to the local install relative
		$tikiRoot = realpath(__DIR__ . '/../../../') . '/';
		if (strpos($path, $tikiRoot) === 0) {
			$path = substr($path, strlen($tikiRoot));
		}

		$profile = new self;
		$profile->domain = "file://$path";
		$profile->profile = $name;
		$profile->pageUrl = $name;
		$profile->transport = new Tiki_Profile_Transport_File($path, $name);

		if (file_exists($ymlPath)) {
			$profile->data = Horde_Yaml::load(file_get_contents($ymlPath));

			$profile->fetchExternals();
			$profile->getObjects();

			return $profile;
		}

		return false;
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
		$parts = parse_url($url);

		if ( ! isset($parts['query'], $parts['host'], $parts['path']) ) {
			return false;
		}

		parse_str($parts['query'], $args);

		if ( ! isset($args['page']) ) {
			return false;
		}

		$dir = dirname($parts['path']);
		$this->domain = $parts['host'] . rtrim($dir, '/');
		$this->profile = $args['page'];

		$this->pageUrl = dirname($url) . '/' . urlencode($this->profile);

		return true;
	} // }}}

	private function loadYaml( $content ) // {{{
	{
		$this->pageContent = $content;

		$pos = 0;

		$this->data = array();

		$matches = WikiParser_PluginMatcher::match($content);
		$parser = new WikiParser_PluginArgumentParser;

		foreach ($matches as $match) {
			$arguments = $parser->parse($match->getArguments());
			if ( ($match->getName() == 'code' && isset($arguments['caption']) && strtoupper($arguments['caption']) == 'YAML' )
				|| $match->getName() == 'profile' ) {
				$yaml = $match->getBody();

				$data = Horde_Yaml::load($yaml);

				foreach ( $data as $key => $value ) {
					if ( array_key_exists($key, $this->data) )
						$this->data[$key] = $this->mergeData($this->data[$key], $value);
					else
						$this->data[$key] = $value;
				}
			}
		}

		$this->fetchExternals();
		$this->getObjects();
	} // }}}

	public function getData(){
		return $this->data;
	}

	public function setData($data){
		$this->data = $data;
	}

	public function fetchExternals() // {{{
	{
		$this->traverseForExternals($this->data);
	} // }}}

	private function traverseForExternals( &$data ) // {{{
	{
		if ( is_array($data) ) {
			foreach ( $data as &$value ) {
				$this->traverseForExternals($value);
			}
		} else if ( 0 === strpos($data, 'wikicontent:') ) {
			$pageName = substr($data, strlen('wikicontent:'));
			$data = $this->getPageContent($pageName);
		} else if ( 0 === strpos($data, 'wikiparsed:') ) {
			$pageName = substr($data, strlen('wikiparsed:'));
			$data = $this->getPageParsed($pageName);
		}
	} // }}}

	public function getPageContent( $pageName ) // {{{
	{
		$content = $this->transport->getPageContent($pageName);
		if (! $content) {
			$this->setFeedback(tra('Page cannot be found').' '.$pageName);
		}

		return $content;
	} // }}}

	public function getPageParsed( $pageName ) // {{{
	{
		$content = $this->transport->getPageParsed($pageName);
		if (! $content) {
			$this->setFeedback(tra('Page cannot be found').' '.$pageName);
		}

		return $content;
	} // }}}

	function mergeData( $old, $new ) // {{{
	{
		if ( is_array($old) && is_array($new) ) {
			foreach ( $new as $key => $value ) {
				if ( is_numeric($key) )
					$old[] = $value;
				else
					$old[$key] = $this->mergeData(isset($old[$key]) ? $old[$key] : null, $value);	
			}

			return $old;
		}
		else
			return $new;
	} // }}}

	function getNamedObjects() // {{{
	{
		if ( ! isset($this->data['objects']) )
			return array();

		$named = array();

		foreach ( $this->data['objects'] as $object ) {
			if ( isset($object['ref']) ) {
				$named[] = array( 'domain' => $this->domain, 'profile' => $this->profile, 'object' => trim($object['ref']) );
			}
		}

		return $named;
	} // }}}

	function getReferences() // {{{
	{
		return $this->traverseForReferences($this->data);
	} // }}}

	function getExternalReferences() // {{{
	{
		$out = array();

		foreach ( $this->getReferences() as $ref ) {
			if ( $this->domain != $ref['domain'] || $this->profile != $ref['profile'] ) {
				$out[] = $ref;
			}
		}

		return $out;
	} // }}}

	private function traverseForReferences( $value ) // {{{
	{
		$array = array();
		if ( is_array($value) )
			foreach ( $value as $v )
				$array = array_merge($array, $this->traverseForReferences($v));
		elseif ( preg_match(self::SHORT_PATTERN, $value, $parts) )
			$array[] = $this->convertReference($parts);
		elseif ( preg_match_all(self::LONG_PATTERN, $value, $parts, PREG_SET_ORDER) ) {
			foreach ( $parts as $row )
				$array[] = $this->convertReference($row);
		}

		$array = array_unique($array, SORT_REGULAR);

		return $array;
	} // }}}

	public function containsReferences($value) // {{{
	{
		$refs = $this->traverseForReferences($value);
		return count($refs) > 0;
	} // }}}

	function convertReference( $parts ) // {{{
	{
		list($full, $null0, $null1, $domain, $null2, $profile, $object) = $parts;

		if ( empty($domain) )
			$domain = $this->domain;
		if ( empty($profile) )
			$profile = $this->profile;

		return array( 'domain' => $domain, 'profile' => $profile, 'object' => $object );
	} // }}}

	function getRequiredInput() // {{{
	{
		return $this->traverseForRequiredInput($this->data);
	} // }}}

	function traverseForRequiredInput( $value ) // {{{
	{
		$array = array();
		if ( is_array($value) )
			foreach ( $value as $v )
				$array = array_merge($array, $this->traverseForRequiredInput($v));
		elseif ( preg_match(self::INFO_REQUEST, $value, $parts) )
			$array[$parts[1]] = $parts[4];

		return $array;
	} // }}}

	function getRequiredProfiles( $recursive = false, $known = array() ) // {{{
	{
		$profiles = array();

		foreach ( $this->getExternalReferences() as $ext ) {
			$key = Tiki_Profile::getProfileKeyfor($ext['domain'], $ext['profile']);
			if ( array_key_exists($key, $known) || array_key_exists($key, $profiles) )
				continue;

			$profiles[$key] = self::fromNames($ext['domain'], $ext['profile']);
		}

		if ( $recursive ) {
			foreach ( $profiles as $profile ) {
				if (is_object($profile)) {
					$profiles = array_merge($profiles, $profile->getRequiredProfiles(true, $profiles));
				}
			}
		}

		return $profiles;
	} // }}}

	public function replaceReferences( &$data, $suppliedUserData = false, $leaveUnknown = false ) // {{{
	{
		if ( $suppliedUserData === false ) {
			$suppliedUserData = $this->getRequiredInput();
		}

		if ( is_array($data) ) {
			foreach ( $data as &$sub ) {
				$this->replaceReferences($sub, $suppliedUserData, $leaveUnknown);
			}

			$toReplace = array();
			foreach ( array_keys($data) as $key ) {
				$newKey = $key;
				$this->replaceReferences($newKey, $suppliedUserData, $leaveUnknown);
				if ( $newKey != $key ) {
					$toReplace[$key] = $newKey;
				}
			}

			foreach ( $toReplace as $old => $new ) {
				$data[$new] = $data[$old];
				unset($data[$old]);
			}
		} else {
			if ( preg_match(self::SHORT_PATTERN, $data, $parts) ) {
				$object = $this->convertReference($parts);

				$value = self::getObjectReference($object);
				if (! is_null($value) || ! $leaveUnknown) {
					$data = $value;
				}
				return;
			}

			$needles = array();
			$replacements = array();

			if ( preg_match_all(self::LONG_PATTERN, $data, $parts, PREG_SET_ORDER) ) {
				foreach ( $parts as $row ) {
					$object = $this->convertReference($row);

					$value = self::getObjectReference($object);
					if (! is_null($value) || ! $leaveUnknown) {
						$needles[] = $row[0];
						$replacements[] = $value;
					}
				}
			}

			if ( preg_match_all(self::INFO_REQUEST, $data, $parts, PREG_SET_ORDER) ) {
				foreach ( $parts as $row ) {
					list($full, $label, $junk, $filter, $default) = $row;

					if ( ! array_key_exists($label, $suppliedUserData) ) {
						$value = $default;
					} else {
						$value = $suppliedUserData[$label];
					}

					if ( $filter ) {
						$value = TikiFilter::get($filter)->filter($value);
					} else {
						$value = TikiFilter::get('xss')->filter($value);
					}

					if ( empty($value) ) {
						$value = $default;
					}

					$needles[] = $full;
					$replacements[] = $value;
				}
			}

			if ( count($needles) ) {
				$data = str_replace($needles, $replacements, $data);
			}

			$needles = array();
			$replacements = array();

			// Replace date formats D(...) to unix timestamps
			if ( preg_match_all("/D\\(([^\\)]+)\\)/", $data, $parts, PREG_SET_ORDER) ) {
				foreach ( $parts as $row ) {
					list($full, $date) = $row;

					if ( false !== $conv = strtotime($date) ) {
						$needles[] = $full;
						$replacements = $conv;
					}
				}
			}

			if ( preg_match_all(self::PREFERENCE_PATTERN, $data, $parts, PREG_SET_ORDER) ) {
				foreach ( $parts as $row ) {
					$preferenceName = $row[1];
					$definition = TikiLib::lib('prefs')->getPreference($preferenceName);

					if (! empty($definition)) {
						$needles[] = $row[0];
						$replacements[] = $definition['value'];
					}
				}
			}

			if ( count($needles) ) {
				$data = str_replace($needles, $replacements, $data);
			}
		}
	} // }}}

	function getInstructionPage() // {{{
	{
		if ( isset($this->data['instructions']) ) {
			return $this->data['instructions'];
		}
	} // }}}

	function getPreferences() // {{{
	{
		$prefs = array();

		if ( array_key_exists('preferences', $this->data) && is_array($this->data['preferences']) ) {
			$prefs = Tiki_Profile::convertLists($this->data['preferences'], array('enable' => 'y', 'disable' => 'n'));
			$prefs = Tiki_Profile::convertYesNo($prefs);
		}

		return $prefs;
	} // }}}

	function getGroupMap() // {{{
	{
		if ( ! isset($this->data['mappings']) ) {
			return array();
		}

		return $this->data['mappings'];
	} // }}}

	function getPermissions( $groupMap = array() ) // {{{
	{
		if ( ! array_key_exists('permissions', $this->data) )
			return array();

		$groups = array();
		foreach ( $this->data['permissions'] as $groupName => $data ) {
			if ( isset($groupMap[ $groupName ]) ) {
				$groupName = $groupMap[$groupName];
			}

			$permissions = Tiki_Profile::convertLists($data, array( 'allow' => 'y', 'deny' => 'n' ), 'tiki_p_');
			$permissions = Tiki_Profile::convertYesNo($permissions);
			foreach ( array_keys($permissions) as $key )
				if ( strpos($key, 'tiki_p_') !== 0 )
					unset($permissions[$key]);

			if (TikiLib::lib('user')->group_exists($groupName)) {
				$groupInfo = TikiLib::lib('user')->get_group_info($groupName);
			} else {
				$groupInfo = array();
			}
			$defaultInfo = array(
				'description' => !empty($groupInfo['groupDesc']) ? $groupInfo['groupDesc'] : '',
				'home' => !empty($groupInfo['groupHome']) ? $groupInfo['groupHome'] : '',
				'user_tracker' => !empty($groupInfo['usersTrackerId']) ? $groupInfo['usersTrackerId'] : 0,
				'user_tracker_field' => !empty($groupInfo['usersFieldId']) ? $groupInfo['usersFieldId'] : 0,
				'group_tracker' => !empty($groupInfo['groupTrackerId']) ? $groupInfo['groupTrackerId'] : 0,
				'group_tracker_field' => !empty($groupInfo['groupFieldId']) ? $groupInfo['groupFieldId'] :0,
				'user_signup' => !empty($groupInfo['userChoice']) ? $groupInfo['userChoice'] : 'n',
				'default_category' => !empty($groupInfo['groupDefCat']) ? $groupInfo['groupDefCat'] : 0,
				'theme' => !empty($groupInfo['groupTheme']) ? $groupInfo['groupTheme'] : '',
				'registration_fields' => !empty($groupInfo['registrationUsersFieldIds']) ? explode(':', $groupInfo['registrationUsersFieldIds']) : array(),
				'include' => array(),
				'autojoin' => 'n',
			);
			foreach ( $defaultInfo as $key => $value )
				if ( array_key_exists($key, $data) ) {
					if ( is_array($value) )
						$defaultInfo[$key] = (array) $data[$key];
					else
						$defaultInfo[$key] = $data[$key];
				}

			$objects = array();
			if ( isset($data['objects']) )
				foreach ( $data['objects'] as $o ) {
					if ( !isset($o['type'], $o['id']) ) {
						$this->setFeedback(tra('Syntax error: ').tra("Permissions' object must have a field 'type' and 'id'"));
						continue;
					}

					$perms = Tiki_Profile::convertLists($o, array( 'allow' => 'y', 'deny' => 'n' ), 'tiki_p_');
					$perms = Tiki_Profile::convertYesNo($perms);

					foreach ( array_keys($perms) as $key )
						if ( strpos($key, 'tiki_p_') !== 0 )
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

	/**
	 * Gets the objects that have already been loaded from the profile or have been installed, otherwise
	 * it loads it from the profile itself.
	 * @return array|null
	 */
	function getLoadedObjects() // {{{
	{
		if ( !is_null($this->objects) ) {
			return $this->objects;
                } else {
			return $this->getObjects();
		}
	} // }}}

	/**
	 * Loads objects for the profile for the purpose of installation or the steps before the installation.
	 * Should not be called after installation is complete as it will reload it from the profile causing things
	 * like the reference IDs to be lost.
	 * @return array|null
	 */
	function getObjects() // {{{
	{
		// Note this function needs to be called each time the objects need to be refreshed after YAML replacements

		$objects = array();

		if ( array_key_exists('objects', $this->data) )
			foreach ( $this->data['objects'] as &$entry ) {
				$o = new Tiki_Profile_Object($entry, $this);
				if ( $o->isWellStructured() ) {
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
		$refs = array();
		while ( ! empty($objects) ) {
			// Circular dependency found... give what we have
			if ( $counter++ > count($objects) * 2 ) {
				$this->setFeedback(tra('Circular reference') . ': ' . implode(', ', array_unique($refs)));
				break;
			}

			$object = array_shift($objects);
			$refs = $object->getInternalReferences();
			$refs = array_diff($refs, $names);
			if ( empty($refs) ) {
				$counter = 0;
				$classified[] = $object;
				if ( $object->getRef() ) {
					$names[] = $object->getRef();
				}
			}
			else {
				$objects[] = $object;
			}
		}

		$this->objects = $classified;
		return $this->objects;
	} // }}}

	function removeSymbols() // {{{
	{
		global $tikilib;
		$tikilib->query(
			"DELETE FROM tiki_profile_symbols WHERE domain = ? AND profile = ?",
			array( $this->domain, self::withPrefix($this->profile) )
		);

		$key = self::getProfileKeyfor($this->domain, self::withPrefix($this->profile));
		foreach ( array_keys(self::$known) as $obj )
			if ( strpos($obj, $key) === 0 )
				unset(self::$known[$obj]);
	} // }}}

	function setSymbol($type, $name, $value, $named = 'y') // {{{
	{
		$symbols = TikiDb::get()->table('tiki_profile_symbols');
		$symbols->insert(
			array(
				'domain' => $this->domain,
				'profile' => $this->withPrefix($this->profile),
				'object' => $name,
				'type' => $type,
				'value' => $value,
				'named' => $named,
			)
		);
	} // }}}

	function getProfileKey($prefix = true) // {{{
	{
		if (!$prefix) {
			return self::getProfileKeyfor($this->domain, $this->profile);
		} else {
			return self::getProfileKeyfor($this->domain, $this->withPrefix($this->profile));
		}
	} // }}}
	
	function getObjectSymbolDetails($type, $value) // Based on an objectType (eg: menu) and an objectId (eg: Id of a menu) query tiki_profile_symbols table and return domain, profile and object information
	{
		global $tikilib;
				
		if (!$type) {
			return false;
		}
		elseif (!$value) {
			return false;
		}
		else {
			$query = 'select * from `tiki_profile_symbols` where `type`=? and `value`=?';
			$result = $tikilib->fetchAll($query, array($type, $value));
		
			if (empty ($result)) {
				return null;
			} else {
				$symbolDetails = array (
					'domain' => $result["0"]["domain"],
					'profile' => $result["0"]["profile"],
					'object' => $result["0"]["object"],
					);
				return $symbolDetails;
			}
		}
	}

	function getPath()
	{
		$domain = $this->domain;
		$profile = $this->profile;
		if ( strpos($domain, '://') === false ) {
			if ( is_dir($domain) ) {
				$domain = "file://" . $domain;
			} else {
				$domain = "http://" . $domain;
			}
		}
		if ( substr($domain, 0, 7) == "file://" ) {
			return TIKI_PATH . '/' . substr($domain, 7);
		} else {
			return $domain;
		}
	} // }}}
}

