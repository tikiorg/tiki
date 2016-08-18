<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Profile_Installer
{
	public static function exportGroup(Tiki_Profile_Writer $writer, $group, $categories = false, $objects = false) // {{{
	{
		$userlib = \TikiLib::lib('user');
		$info = $userlib->get_group_info($group);

		if (empty($info['id'])) {
			return false;
		}

		$data = array(
			'description' => $info['groupDesc'],
			'home' => $info['groupHome'],
			'user_tracker' => $writer->getReference('tracker', $info['userTrackerId']),
			'group_tracker' => $writer->getReference('tracker', $info['groupTrackerId']),
			'user_tracker_field' => $writer->getReference('tracker_field', $info['userTrackerFieldId']),
			'group_tracker_field' => $writer->getReference('tracker_field', $info['groupTrackerFieldId']),
			'registration_fields' => $writer->getReference('tracker_field', array_filter(explode(':', $info['registrationUsersFieldIds']))),
			'user_signup' => $info['userChoice'],
			'default_category' => $writer->getReference('category', $info['groupDefCat']),
			'theme' => $info['groupTheme'],
			'allow' => [],
			'objects' => [],
		);

		foreach ($info['perms'] as $perm) {
			// Skip tiki_p_
			$data['allow'][] = substr($perm, 7);
		}

		if ($categories) {
			$data['objects'] = self::getPermissionList($writer, 'category', $group);
		}

		if ($objects) {
			$data['objects'] = array_merge(
				$data['objects'],
				self::getPermissionList($writer, 'wiki page', $group),
				self::getPermissionList($writer, 'tracker', $group),
				self::getPermissionList($writer, 'forum', $group)
			);
		}

		// Clean and store
		$data = array_filter($data);
		$writer->addPermissions($group, $data);

		return true;
	} // }}}

	private static function getPermissionList($writer, $objectType, $group) // {{{
	{
		switch ($objectType) {
		case 'category':
			$sub = "SELECT MD5(CONCAT('category', categId)) hash, categId objectId FROM tiki_categories";
			break;
		case 'forum':
			$sub = "SELECT MD5(CONCAT('forum', forumId)) hash, forumId objectId FROM tiki_forums";
			break;
		case 'tracker':
			$sub = "SELECT MD5(CONCAT('tracker', trackerId)) hash, trackerId objectId FROM tiki_trackers";
			break;
		case 'wiki page':
			$sub = "SELECT MD5(CONCAT('wiki page', LOWER(pageName))) hash, pageName objectId FROM tiki_pages";
			break;
		default:
			return array();
		}

		$db = TikiDb::get();
		$result = $db->fetchAll("
		SELECT i.objectId, permName
		FROM users_objectpermissions p
			INNER JOIN ($sub) i ON i.hash = p.objectId
		WHERE p.objectType = ? AND p.groupName = ?
		", array($objectType, $group));

		$map = [];
		foreach ($result as $row) {
			$id = $row['objectId'];
			if (! isset($map[$id])) {
				$map[$id] = array(
					'type' => $objectType,
					'id' => $writer->getReference($objectType, $id),
					'allow' => [],
				);
			}

			// Strip tiki_p_
			$map[$id]['allow'][] = substr($row['permName'], 7);
		}
		return array_values($map);
	} // }}}

	private $installed = array();
	private $handlers = array(
		'tracker' => 'Tiki_Profile_InstallHandler_Tracker',
		'tracker_field' => 'Tiki_Profile_InstallHandler_TrackerField',
		'tracker_item' => 'Tiki_Profile_InstallHandler_TrackerItem',
		'tracker_option' => 'Tiki_Profile_InstallHandler_TrackerOption',
		'wiki_page' => 'Tiki_Profile_InstallHandler_WikiPage',
		'category' => 'Tiki_Profile_InstallHandler_Category',
		'categorize' => 'Tiki_Profile_InstallHandler_Categorize',
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
		'article_topic' => 'Tiki_Profile_InstallHandler_ArticleTopic',
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
		'webmail' => 'Tiki_Profile_InstallHandler_Webmail',
		'sheet' => 'Tiki_Profile_InstallHandler_Sheet',
		'rating_config' => 'Tiki_Profile_InstallHandler_RatingConfig',
		'rating_config_set' => 'Tiki_Profile_InstallHandler_RatingConfigSet',
		'area_binding' => 'Tiki_Profile_InstallHandler_AreaBinding',
		'activity_stream_rule' => 'Tiki_Profile_InstallHandler_ActivityStreamRule',
		'activity_rule_set' => 'Tiki_Profile_InstallHandler_ActivityRuleSet',
		'goal' => 'Tiki_Profile_InstallHandler_Goal',
		'goal_set' => 'Tiki_Profile_InstallHandler_GoalSet',
	);

	private static $typeMap = array(
		'wiki_page' => 'wiki page',
		'file_gallery' => 'file gallery',
		'tracker_item' => 'trackeritem',
	);

	private static $typeMapInvert = array(
		'wiki page' => 'wiki_page',
		'wiki' => 'wiki_page',
		'fgal' => 'file_gallery',
		'file gallery' => 'file_gallery',
		'trackeritem' => 'tracker_item',
		'tracker item' => 'tracker_item',
	);

	private $userData = false;
	private $debug = false;
	private $prefixDependencies = true;

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
	 * @return mixed string or whole array if no index specified
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
		if (isset(self::$typeMap[$type])) {
			return self::$typeMap[$type];
		} else {
			return $type;
		}
	} // }}}

	/**
	 * Converts a Tiki object type to a profile object type.
	 */
	public static function convertTypeInvert( $type ) // {{{
	{
		$typeMap = self::$typeMapInvert;

		if (isset($typeMap[$type])) {
			return $typeMap[$type];
		} else {
			return $type;
		}
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

		$result = $tikilib->fetchAll("SELECT DISTINCT `domain`, `profile` FROM `tiki_profile_symbols`");
		foreach ($result as $row) {
			$this->installed[Tiki_Profile::getProfileKeyFor($row['domain'], $row['profile'])] = true;
		}
	} // }}}

	function setUserData( $userData ) // {{{
	{
		$this->userData = $userData;
	} // }}}

	function setDebug( ) // {{{
	{
		$this->debug = true;
	} // }}}

	function disablePrefixDependencies( ) // {{{
	{
		$this->prefixDependencies = false;
	} // }}}

	function enablePrefixDependencies( ) // {{{
	{
		$this->prefixDependencies = true;
	} // }}}

	function getInstallOrder( Tiki_Profile $profile ) // {{{
	{
		if ($profile == null) {
			return false;
		}
		
		// Obtain the list of all required profiles
		$dependencies = $profile->getRequiredProfiles(true);
		$dependencies[$profile->getProfileKey()] = $profile;

		$referenced = array();
		$knownObjects = array();
		foreach ( Tiki_Profile_Object::getNamedObjects() as $o )
			$knownObjects[] = Tiki_Profile_Object::serializeNamedObject($o);

		// Build the list of dependencies for each profile
		$short = array();
		foreach ( $dependencies as $key => $prf ) {
			if ( empty( $prf ) ) {
				throw new Exception("Unknown objects are referenced: " . $key);
			}

			$short[$key] = array();
			foreach ( $prf->getRequiredProfiles() as $k => $p )
				$short[$key][] = $k;

			foreach ( $prf->getNamedObjects() as $o )
				$knownObjects[] = Tiki_Profile_Object::serializeNamedObject($o);
			foreach ( $prf->getReferences() as $o )
				$referenced[] = Tiki_Profile_Object::serializeNamedObject($o);

			if ( ! $this->isInstallable($prf) )
				return false;
		}

		// Make sure all referenced objects actually exist
		$remain = array_diff($referenced, $knownObjects);
		if ( ! empty( $remain ) )
			throw new Exception("Unknown objects are referenced: " . implode(', ', $remain));

		// Build the list of packages that need to be installed
		$toSequence = array();
		foreach ( $dependencies as $key => $prf )
			if ( ! $this->isInstalled($prf, $key == $profile->getProfileKey() || $this->prefixDependencies) )
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
		global $tikidomain;
		$cachelib = TikiLib::lib('cache');
		$tikilib = TikiLib::lib('tiki');

		try {

			// Apply directives, note Directives should be and are a runtime thing
			$yamlDirectives = new Yaml_Directives(new Yaml_Filter_ReplaceUserData($profile, $this->userData), $profile->getPath());
			$data = $profile->getData();
			$yamlDirectives->process($data);
			$profile->setData($data);
			$profile->fetchExternals(); // there might be new externals as a result of the directives processing


			$profile->getObjects(); // need to be refreshed before installation in case any have changed due to replacements

			if ( ! $profiles = $this->getInstallOrder($profile) ) {
				return false;
			}
	
			foreach ( $profiles as $p ) {
				$this->doInstall($p);
			}
			
			if (count($this->getFeedback()) == count($profiles)) {
				$this->setFeedback(tra('Nothing was changed. Please check the profile for errors'));
			}
			$cachelib->empty_cache($empty_cache, 'profile');
			return true;
		
		} catch(Exception $e) {
			$this->setFeedback(tra('An error occurred: ') . $e->getMessage());
			return false;
		}

	} // }}}

	function isInstalled( Tiki_Profile $profile, $prefix = true ) // {{{
	{
		return array_key_exists($profile->getProfileKey($prefix), $this->installed);
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
		$this->setFeedback(tra('Applying profile').': '.$profile->profile);

		$this->installed[$profile->getProfileKey()] = $profile;

		$preferences = $profile->getPreferences();
		$leftovers = $this->applyPreferences($profile, $preferences, true);

		require_once 'lib/setup/events.php';
		tiki_setup_events();

		$userhandlers = array();
		foreach ( $profile->getObjects() as $object ) {
			$installer = $this->getInstallHandler($object);
			if ($installer instanceof Tiki_Profile_InstallHandler_User) {
				// postpone installation of users till after groups/perms are set
				$description = $object->getDescription();
				$userhandlers[$description] = $installer;
				continue;
			}
			$installer->install();
			$description = $object->getDescription();
			$installer->replaceReferences($description);
			$this->setFeedback(tra('Added (or modified)').': '.$description);
		}
		$groupMap = $profile->getGroupMap();
		$profile->replaceReferences($groupMap, $this->userData);

		$permissions = $profile->getPermissions($groupMap);
		$profile->replaceReferences($permissions, $this->userData);
		foreach ( $permissions as $groupName => $info ) {
			$this->setFeedback(tra('Group changed (or modified)').': '.$groupName);
			$this->setupGroup($groupName, $info['general'], $info['permissions'], $info['objects'], $groupMap);
		}

		foreach ($userhandlers as $description => $installer) {
			$installer->install();
			$this->setFeedback(tra('Added (or modified)').': '.$description);
		}

		$this->applyPreferences($profile, $leftovers);
		tiki_setup_events();
	} // }}}

	private function applyPreferences($profile, $preferences, $leaveUnknown = false)
	{
		global $prefs;
		$tikilib = TikiLib::lib('tiki');

		$profile->replaceReferences($preferences, $this->userData, $leaveUnknown);
		$leftovers = array();

		foreach ( $preferences as $pref => $value ) {
			if ($leaveUnknown && $profile->containsReferences($value)) {
				$leftovers[$pref] = $value;
				continue;
			}

			if ($this->allowedGlobalPreferences === false || in_array($pref, $this->allowedGlobalPreferences)) {
				$prefslib = TikiLib::lib('prefs');
				$pinfo = $prefslib->getPreference($pref);
				if (!empty($pinfo['separator']) && !is_array($value)) {
					$value = explode($pinfo['separator'], $value);
				}

				if (!isset($prefs[$pref]) || $prefs[$pref] != $value) {
					$this->setFeedback(tra('Preference set').': '.$pref.'='.$value);
				}
				$tikilib->set_preference($pref, $value);
			}
		}

		return $leftovers;
	}

	private function setupGroup( $groupName, $info, $permissions, $objects, $groupMap ) // {{{
	{
		$userlib = TikiLib::lib('user');

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
				$data['id'] = trim($data['id']);
				$data['type'] = self::convertType($data['type']);
				$data['id'] = Tiki_Profile_Installer::convertObject($data['type'], $data['id'], array( 'groupMap' => $groupMap));

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

