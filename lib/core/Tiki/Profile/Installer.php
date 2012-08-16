<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_Installer
{
	private $installed = array();
	private $handlers = array(
		'tracker' => 'Tiki_Profile_InstallHandler_Tracker',
		'tracker_field' => 'Tiki_Profile_InstallHandler_TrackerField',
		'tracker_item' => 'Tiki_Profile_InstallHandler_TrackerItem',
		'wiki_page' => 'Tiki_Profile_InstallHandler_WikiPage',
		'category' => 'Tiki_Profile_InstallHandler_Category',
		'file_gallery' => 'Tiki_Profile_InstallHandler_FileGallery',
		'module' => 'Tiki_Profile_InstallHandler_Module',
		'menu' => 'Tiki_Profile_InstallHandler_Menu',
		'menu_option' => 'Tiki_Profile_InstallHandler_MenuOption',
		'blog' => 'Tiki_Profile_InstallHandler_Blog',
		'blog_post' => 'Tiki_Profile_InstallHandler_BlogPost',
		'plugin_alias' => 'Tiki_Profile_InstallHandler_PluginAlias',
		'webservice' => 'Tiki_Profile_InstallHandler_Webservice',
		'webservice_template' => 'Tiki_Profile_InstallHandler_WebserviceTemplate',
		'rss' => 'Tiki_Profile_InstallHandler_Rss',
		'topic' => 'Tiki_Profile_InstallHandler_Topic',
		'article_type' => 'Tiki_Profile_InstallHandler_ArticleType',
		'article' => 'Tiki_Profile_InstallHandler_Article',
		'forum' => 'Tiki_Profile_InstallHandler_Forum',
		'template' => 'Tiki_Profile_InstallHandler_Template',
		'perspective' => 'Tiki_Profile_InstallHandler_Perspective',
		'users' => 'Tiki_Profile_InstallHandler_User',
		// keeping 'users' as well as 'user' that was the previous behaviour (up to Tiki 6)
		// so as to support existing profiles
		'user' => 'Tiki_Profile_InstallHandler_User',
		'datachannel' => 'Tiki_Profile_InstallHandler_DataChannel',
		'transition' => 'Tiki_Profile_InstallHandler_Transition',
		'calendar' => 'Tiki_Profile_InstallHandler_Calendar',
		'extwiki' => 'Tiki_Profile_InstallHandler_ExtWiki',
		'webmail_account' => 'Tiki_Profile_InstallHandler_WebmailAccount',
		'sheet' => 'Tiki_Profile_InstallHandler_Sheet',
		'rating_config' => 'Tiki_Profile_InstallHandler_RatingConfig',
	);

	private static $typeMap = array(
		'wiki_page' => 'wiki page',
		'file_gallery' => 'fgal',
		'tracker_item' => 'trackeritem',
	);

	private $userData = false;
	private $debug = false;
	
	private $feedback = array();	// Let users know what's happened

	private $allowedGlobalPreferences = false;
	private $allowedObjectTypes = false;

	/**
	 * @param $feed - (strings append, array replaces) lines of feedback text
	 * @return none
	 */
	function setFeedback( $feed ) // {{{
	{
		if (is_array($feed)) {
			$this->feedback = $feed;
		} else {
			$this->feedback[] = $feed;
		}
	} // }}}
	
	/**
	 * @param $index - (int) index of feedback string to return if present
	 * @return string or whole array if no index specified 
	 */
	function getFeedback( $index = null ) // {{{
	{
		if (! is_null($index) && $index < count($this->feedback) ) {
			return $this->feedback[ $index ];
		} else {
			return $this->feedback;
		}
	} // }}}
	
	public static function convertType( $type ) // {{{
	{
		if ( array_key_exists($type, self::$typeMap) )
			return self::$typeMap[$type];
		else
			return $type;
	} // }}}

	public static function convertObject( $type, $id, $contextualizedInfo = array() ) // {{{
	{
		global $tikilib;

		if ($type == 'wiki page' && is_numeric($id)) {
			return $tikilib->get_page_name_from_id($id);
		} elseif ( $type == 'group' && isset( $contextualizedInfo['groupMap'] ) ) {
			if ( isset( $contextualizedInfo['groupMap'][$id] ) ) {
				return $contextualizedInfo['groupMap'][$id];
			} else {
				return $id;
			}
		} else {
			return $id;
		}
	} // }}}

	function __construct() // {{{
	{
		global $tikilib;

		$result = $tikilib->query("SELECT DISTINCT `domain`, `profile` FROM `tiki_profile_symbols`");
		if ( $result ) while ( $row = $result->fetchRow() )
			$this->installed[Tiki_Profile::getProfileKeyFor($row['domain'], $row['profile'])] = true;
	} // }}}

	function setUserData( $userData ) // {{{
	{
		$this->userData = $userData;
	} // }}}

	function setDebug( ) // {{{
	{
		$this->debug = true;
	} // }}}

	function getInstallOrder( Tiki_Profile $profile ) // {{{
	{
		// Obtain the list of all required profiles
		$dependencies = $profile->getRequiredProfiles(true);
		$dependencies[$profile->getProfileKey()] = $profile;

		$referenced = array();
		$knownObjects = array();
		foreach ( Tiki_Profile_Object::getNamedObjects() as $o )
			$knownObjects[] = Tiki_Profile_Object::serializeNamedObject($o);

		// Build the list of dependencies for each profile
		$short = array();
		foreach ( $dependencies as $key => $profile ) {
			$short[$key] = array();
			foreach ( $profile->getRequiredProfiles() as $k => $p )
				$short[$key][] = $k;

			foreach ( $profile->getNamedObjects() as $o )
				$knownObjects[] = Tiki_Profile_Object::serializeNamedObject($o);
			foreach ( $profile->getReferences() as $o )
				$referenced[] = Tiki_Profile_Object::serializeNamedObject($o);

			if ( ! $this->isInstallable($profile) )
				return false;
		}

		// Make sure all referenced objects actually exist
		$remain = array_diff($referenced, $knownObjects);
		if ( ! empty( $remain ) )
			throw new Exception("Unknown objects are referenced: " . implode(', ', $remain));

		// Build the list of packages that need to be installed
		$toSequence = array();
		foreach ( $dependencies as $key => $profile )
			if ( ! $this->isInstalled($profile) )
				$toSequence[] = $key;

		// Order the packages to make sure all dependencies are met
		$toInstall = array();
		$counter = 0;
		while ( count($toSequence) ) {
			// If all packages were tested and no order was found, exit
			// Probably means there is a circular dependency
			if ( $counter++ > count($toSequence) * 2 )
				throw new Exception("Profiles could not be ordered: " . implode(", ", $toSequence));

			$key = reset($toSequence);

			// Remove packages that are already scheduled or installed from dependencies
			$short[$key] = array_diff($short[$key], array_keys($this->installed), $toInstall);

			$element = array_shift($toSequence);
			if ( count($short[$key]) )
				$toSequence[] = $element;
			else
			{
				$counter = 0;
				$toInstall[] = $element;
			}
		}

		$final = array();
		// Perform the actual install
		foreach ( $toInstall as $key )
			$final[] = $dependencies[$key];

		return $final;
	} // }}}

	/**
	 * Install a profile
	 * 
	 * @param Tiki_Profile $profile		Profile object
	 * @param string $empty_cache		all|templates_c|temp_cache|temp_public|modules_cache|prefs (default all)
	 */
	function install( Tiki_Profile $profile, $empty_cache = 'all' ) // {{{
	{
		global $cachelib, $tikidomain, $tikilib;
		require_once 'lib/cache/cachelib.php';

		try {
			if ( ! $profiles = $this->getInstallOrder($profile) )
				return false;
	
			foreach ( $profiles as $p )
				$this->doInstall($p);
			
			if (count($this->getFeedback()) == count($profiles)) {
				$this->setFeedback(tra('Nothing was changed, please check profile for errors'));
			}
			$cachelib->empty_cache($empty_cache, 'profile');
			return true;
		
		} catch(Exception $e) {
			$this->setFeedback(tra('An error occurred: ') . $e->getMessage());
			return false;
		}

	} // }}}

	function isInstalled( Tiki_Profile $profile ) // {{{
	{
		return array_key_exists($profile->getProfileKey(), $this->installed);
	} // }}}

	function isKeyInstalled( $domain, $profile ) // {{{
	{
		return array_key_exists(Tiki_Profile::getProfileKeyFor($domain, $profile), $this->installed);
	} // }}}

	function isInstallable( Tiki_Profile $profile ) // {{{
	{
		foreach ( $profile->getObjects() as $object ) {
			$handler = $this->getInstallHandler($object);
			if ( ! $handler )
				throw new Exception("No handler found for object type {$object->getType()} in {$profile->domain}:{$profile->profile}");

			if ( ! $handler->canInstall() )
				throw new Exception("Object (#{$object->getRef()}) of type {$object->getType()} in {$profile->domain}:{$profile->profile} does not validate");
		}

		return true;
	} // }}}

	public function getInstallHandler( Tiki_Profile_Object $object ) // {{{
	{
		$type = $object->getType();
		if ( array_key_exists($type, $this->handlers) ) {
			if ( $this->allowedObjectTypes !== false && ! in_array($type, $this->allowedObjectTypes) ) {
				return null;
			}

			$class = $this->handlers[$type];
			if ( class_exists($class) )
				return new $class($object, $this->userData);
		}
	} // }}}

	private function doInstall( Tiki_Profile $profile ) // {{{
	{
		global $tikilib, $prefs;

		$this->setFeedback(tra('Applying profile').': '.$profile->profile);

		$this->installed[$profile->getProfileKey()] = $profile;

		$preferences = $profile->getPreferences();
		$profile->replaceReferences($preferences, $this->userData);
		foreach ( $preferences as $pref => $value ) {
			if ($this->allowedGlobalPreferences === false || in_array($pref, $this->allowedGlobalPreferences)) {
				global $prefslib; include_once('lib/prefslib.php');
				$pinfo = $prefslib->getPreference($pref);
				if (!empty($pinfo['separator']) && !is_array($value)) {
					$value = explode($pinfo['separator'], $value);
				}

				if ($prefs[$pref] != $value) {
					$this->setFeedback(tra('Preference set').': '.$pref.'='.$value);
				}
				$tikilib->set_preference($pref, $value);
			}
		}

		require_once 'lib/setup/events.php';
		tiki_setup_events();

		foreach ( $profile->getObjects() as $object ) {
			$this->getInstallHandler($object)->install();
			$this->setFeedback(tra('Added (or modified)').': '.$object->getDescription());
		}
		$groupMap = $profile->getGroupMap();
		$profile->replaceReferences($groupMap, $this->userData);

		$permissions = $profile->getPermissions($groupMap);
		$profile->replaceReferences($permissions, $this->userData);
		foreach ( $permissions as $groupName => $info ) {
			$this->setFeedback(tra('Group changed (or modified)').': '.$groupName);
			$this->setupGroup($groupName, $info['general'], $info['permissions'], $info['objects'], $groupMap);
		}

		tiki_setup_events();
	} // }}}

	private function setupGroup( $groupName, $info, $permissions, $objects, $groupMap ) // {{{
	{
		global $userlib;

		if ( ! $userlib->group_exists($groupName) ) {
			$userlib->add_group($groupName, $info['description'], $info['home'], $info['user_tracker'], $info['group_tracker'], implode(':', $info['registration_fields']), $info['user_signup'], $info['default_category'], $info['theme'], $info['user_tracker_field'], $info['group_tracker_field']);
		} else {
			$userlib->change_group($groupName, $groupName, $info['description'], $info['home'], $info['user_tracker'], $info['group_tracker'], $info['user_tracker_field'], $info['group_tracker_field'], implode(':', $info['registration_fields']), $info['user_signup'], $info['default_category'], $info['theme']);
		}

		if ( count($info['include']) ) {
			$userlib->remove_all_inclusions($groupName);
			
			foreach ( $info['include'] as $included )
				$userlib->group_inclusion($groupName, $included);
		}

		foreach ( $permissions as $perm => $v ) {
			if ( $v == 'y' )
				$userlib->assign_permission_to_group($perm, $groupName);
			else
				$userlib->remove_permission_from_group($perm, $groupName);
			$this->setFeedback(sprintf(tra('Modified permission %s for %s'), $perm, $groupName));
		}

		foreach ( $objects as $data )
			foreach ( $data['permissions'] as $perm => $v ) {
				$data['type'] = self::convertType($data['type']);
				$data['id'] = Tiki_Profile_Installer::convertObject($data['type'], $data['id'], array( 'groupMap' => $groupMap	));

				if ( $v == 'y' )
					$userlib->assign_object_permission($groupName, $data['id'], $data['type'], $perm);
				else
					$userlib->remove_object_permission($groupName, $data['id'], $data['type'], $perm);
				$this->setFeedback(sprintf(tra('Modified permission %s on %s/%s for %s'), $perm, $data['type'], $data['id'], $groupName));
			}

		global $user;
		if ( $info['autojoin'] == 'y' && $user ) {
			$userlib->assign_user_to_group($user, $groupName);
			$this->setFeedback(tr('User %0 was added to %1', $user, $groupName));
		}
	} // }}}

	function forget( Tiki_Profile $profile ) // {{{
	{
		$key = $profile->getProfileKey();
		unset($this->installed[$key]);
		$profile->removeSymbols();
	} // }}}

	function limitGlobalPreferences( array $allowedPreferences ) // {{{
	{
		$this->allowedGlobalPreferences = $allowedPreferences;
	} // }}}

	function limitObjectTypes( array $objectTypes ) // {{{
	{
		$this->allowedObjectTypes = $objectTypes;
	} // }}}
}

