<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
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
		'webmail' => 'Tiki_Profile_InstallHandler_Webmail',
		'sheet' => 'Tiki_Profile_InstallHandler_Sheet',
	);

	private static $typeMap = array(
		'wiki_page' => 'wiki page',
		'file_gallery' => 'fgal',
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
		if (is_array( $feed )) {
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
		if (! is_null( $index ) && $index < count($this->feedback) ) {
			return $this->feedback[ $index ];
		} else {
			return $this->feedback;
		}
	} // }}}
	
	public static function convertType( $type ) // {{{
	{
		if( array_key_exists( $type, self::$typeMap ) )
			return self::$typeMap[$type];
		else
			return $type;
	} // }}}

	public static function convertObject( $type, $id, $contextualizedInfo = array() ) // {{{
	{
		global $tikilib;

		if( $type == 'wiki page' && is_numeric( $id ) ) {
			return $tikilib->get_page_name_from_id( $id );
		} elseif( $type == 'group' && isset( $contextualizedInfo['groupMap'] ) ) {
			if( isset( $contextualizedInfo['groupMap'][$id] ) ) {
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

		$result = $tikilib->query( "SELECT DISTINCT `domain`, `profile` FROM `tiki_profile_symbols`" );
		if ( $result ) while( $row = $result->fetchRow() )
			$this->installed[Tiki_Profile::getProfileKeyFor( $row['domain'], $row['profile'] )] = true;
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
		foreach( Tiki_Profile_Object::getNamedObjects() as $o )
			$knownObjects[] = Tiki_Profile_Object::serializeNamedObject( $o );

		// Build the list of dependencies for each profile
		$short = array();
		foreach( $dependencies as $key => $profile )
		{
			$short[$key] = array();
			foreach( $profile->getRequiredProfiles() as $k => $p )
				$short[$key][] = $k;

			foreach( $profile->getNamedObjects() as $o )
				$knownObjects[] = Tiki_Profile_Object::serializeNamedObject( $o );
			foreach( $profile->getReferences() as $o )
				$referenced[] = Tiki_Profile_Object::serializeNamedObject( $o );

			if( ! $this->isInstallable( $profile ) )
				return false;
		}

		// Make sure all referenced objects actually exist
		$remain = array_diff( $referenced, $knownObjects );
		if( ! empty( $remain ) )
			throw new Exception( "Unknown objects are referenced: " . implode( ', ', $remain ) );

		// Build the list of packages that need to be installed
		$toSequence = array();
		foreach( $dependencies as $key => $profile )
			if( ! $this->isInstalled( $profile ) )
				$toSequence[] = $key;

		// Order the packages to make sure all dependencies are met
		$toInstall = array();
		$counter = 0;
		while( count( $toSequence ) )
		{
			// If all packages were tested and no order was found, exit
			// Probably means there is a circular dependency
			if( $counter++ > count( $toSequence ) * 2 )
				throw new Exception( "Profiles could not be ordered: " . implode( ", ", $toSequence ) );

			$key = reset( $toSequence );

			// Remove packages that are already scheduled or installed from dependencies
			$short[$key] = array_diff( $short[$key], array_keys( $this->installed ), $toInstall );

			$element = array_shift( $toSequence );
			if( count( $short[$key] ) )
				$toSequence[] = $element;
			else
			{
				$counter = 0;
				$toInstall[] = $element;
			}
		}

		$final = array();
		// Perform the actual install
		foreach( $toInstall as $key )
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
			if( ! $profiles = $this->getInstallOrder( $profile ) )
				return false;
	
			foreach( $profiles as $p )
				$this->doInstall( $p );
			
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
		return array_key_exists( $profile->getProfileKey(), $this->installed );
	} // }}}

	function isKeyInstalled( $domain, $profile ) // {{{
	{
		return array_key_exists( Tiki_Profile::getProfileKeyFor( $domain, $profile ), $this->installed );
	} // }}}

	function isInstallable( Tiki_Profile $profile ) // {{{
	{
		foreach( $profile->getObjects() as $object )
		{
			$handler = $this->getInstallHandler( $object );
			if( ! $handler )
				throw new Exception( "No handler found for object type {$object->getType()} in {$profile->domain}:{$profile->profile}" );

			if( ! $handler->canInstall() )
				throw new Exception( "Object (#{$object->getRef()}) of type {$object->getType()} in {$profile->domain}:{$profile->profile} does not validate" );
		}

		return true;
	} // }}}

	public function getInstallHandler( Tiki_Profile_Object $object ) // {{{
	{
		$type = $object->getType();
		if( array_key_exists( $type, $this->handlers ) )
		{
			if( $this->allowedObjectTypes !== false && ! in_array( $type, $this->allowedObjectTypes ) ) {
				return null;
			}

			$class = $this->handlers[$type];
			if( class_exists( $class ) )
				return new $class( $object, $this->userData );
		}
	} // }}}

	private function doInstall( Tiki_Profile $profile ) // {{{
	{
		global $tikilib, $prefs;

		$this->setFeedback(tra('Applying profile').': '.$profile->profile);

		$this->installed[$profile->getProfileKey()] = $profile;

		foreach( $profile->getObjects() as $object ) {
			$this->getInstallHandler( $object )->install();
			$this->setFeedback(tra('Added (or modified)').': '.$object->getDescription());
		}
		$preferences = $profile->getPreferences();
		$profile->replaceReferences( $preferences, $this->userData );
		foreach( $preferences as $pref => $value ) {
			if( $this->allowedGlobalPreferences === false || in_array( $pref, $this->allowedGlobalPreferences ) ) {
				if ($prefs[$pref] != $value) {
					$this->setFeedback(tra('Preference set').': '.$pref.'='.$value);
				}
				$tikilib->set_preference( $pref, $value );
			}
		}
		$groupMap = $profile->getGroupMap();
		$profile->replaceReferences( $groupMap, $this->userData );

		$permissions = $profile->getPermissions( $groupMap );
		$profile->replaceReferences( $permissions, $this->userData );
		foreach( $permissions as $groupName => $info ) {
			$this->setFeedback(tra('Group changed (or modified)').': '.$groupName);
			$this->setupGroup( $groupName, $info['general'], $info['permissions'], $info['objects'], $groupMap );
		}
	} // }}}

	private function setupGroup( $groupName, $info, $permissions, $objects, $groupMap ) // {{{
	{
		global $userlib;

		if( ! $userlib->group_exists( $groupName ) ) {
			$userlib->add_group( $groupName, $info['description'], $info['home'], $info['user_tracker'], $info['group_tracker'], implode( ':', $info['registration_fields'] ), $info['user_signup'], $info['default_category'], $info['theme'], $info['user_tracker_field'], $info['group_tracker_field'] );
		} else {
			$userlib->change_group($groupName, $groupName,  $info['description'], $info['home'], $info['user_tracker'], $info['group_tracker'], $info['user_tracker_field'], $info['group_tracker_field'], implode( ':', $info['registration_fields'] ), $info['user_signup'], $info['default_category'], $info['theme'] );
		}

		if( count( $info['include'] ) )
		{
			$userlib->remove_all_inclusions( $groupName );
			
			foreach( $info['include'] as $included )
				$userlib->group_inclusion( $groupName, $included );
		}

		foreach( $permissions as $perm => $v )
		{
			if( $v == 'y' )
				$userlib->assign_permission_to_group( $perm, $groupName );
			else
				$userlib->remove_permission_from_group( $perm, $groupName );
			$this->setFeedback(sprintf(tra('Modified permission %s for %s'), $perm, $groupName));
		}

		foreach( $objects as $data )
			foreach( $data['permissions'] as $perm => $v )
			{
				$data['type'] = self::convertType( $data['type'] );
				$data['id'] = Tiki_Profile_Installer::convertObject( $data['type'], $data['id'], array(
					'groupMap' => $groupMap,
				) );

				if( $v == 'y' )
					$userlib->assign_object_permission( $groupName, $data['id'], $data['type'], $perm );
				else
					$userlib->remove_object_permission( $groupName, $data['id'], $data['type'], $perm );
				$this->setFeedback(sprintf(tra('Modified permission %s on %s/%s for %s'), $perm, $data['type'], $data['id'], $groupName));
			}

		global $user;
		if( $info['autojoin'] == 'y' && $user ) {
			$userlib->assign_user_to_group( $user, $groupName );
			$this->setFeedback( tr('User %0 was added to %1', $user, $groupName) );
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

abstract class Tiki_Profile_InstallHandler // {{{
{
	protected $obj;
	private $userData;
	protected $data;

	function __construct( Tiki_Profile_Object $obj, $userData )
	{
		$this->obj = $obj;
		$this->userData = $userData;
	}

	abstract function canInstall();

	final function install()
	{
		$id = $this->_install();
		if( empty( $id ) ) {
			die( 'Handler failure: ' . get_class( $this ) . "\n" );
		}
		
		//Helper to return items that were installed - first used with cart items
		global $record_profile_items_created;
		$record_profile_items_created[] = $id;
		
		$this->obj->setValue( $id );
	}

	function replaceReferences( &$data ) // {{{
	{
		$this->obj->replaceReferences( $data, $this->userData );
	} // }}}

	abstract function _install();
} // }}}

class Tiki_Profile_InstallHandler_Tracker extends Tiki_Profile_InstallHandler // {{{
{
	private function getData() // {{{
	{
		if( $this->data )
			return $this->data;

		$data = $this->obj->getData();

		$data = Tiki_Profile::convertLists( $data, array(
			'show' => 'y',
			'allow' => 'y',
		), true );

		$data = Tiki_Profile::convertYesNo( $data );

		return $this->data = $data;
	} // }}}

	private function getOptionMap() // {{{
	{
		return array(
			'name' => '',
			'description' => '',

			'show_status' => 'showStatus',
			'show_status_admin_only' => 'showStatusAdminOnly',
			'list_default_status' => 'defaultStatus',
			'email' => 'outboundEmail',
			'email_simplified' => 'simpleEmail',
			'default_status' => 'newItemStatus',
			'modification_status' => 'modItemStatus',
			'allow_creator_modification' => 'writerCanModify',
			'allow_creator_group_modification' => 'writerGroupCanModify',
			'show_creation_date' => 'showCreatedView',
			'show_list_creation_date' => 'showCreated',
			'show_modification_date' => 'showLastModifView',
			'show_list_modification_date' => 'showLastModif',
			'creation_date_format' => 'showCreatedFormat',
			'modification_date_format' => 'showLastModifFormat',
			'sort_default_field' => 'defaultOrderKey',
			'sort_default_order' => 'defaultOrderDir',
			'allow_rating' => 'useRatings',
			'allow_comments' => 'useComments',
			'allow_attachments' => 'useAttachments',
			'restrict_start' => 'start',
			'restrict_end' =>  'end',
			'hide_list_empty_fields' => 'doNotShowEmptyField',
			'allow_one_item_per_user' => 'oneUserItem',
		);
	} // }}}

	private function getDefaults() // {{{
	{
		$defaults = array_fill_keys( array_keys( $this->getOptionMap() ), 'n' );
		$defaults['name'] = '';
		$defaults['description'] = '';
		$defaults['creation_date_format'] = '';
		$defaults['modification_date_format'] = '';
		$defaults['email'] = '';
		$defaults['outboundEmail'] = '';
		$defaults['default_status'] = 'o';
		$defaults['modification_status'] = '';
		$defaults['list_default_status'] = 'o';
		$defaults['sort_default_order'] = 'asc';
		$defaults['sort_default_field'] = '';
		$defaults['restrict_start'] = '';
		$defaults['restrict_end'] = '';
		return $defaults;
	} // }}}
	
	private function getOptionConverters() // {{{
	{
		return array(
			'restrict_start' => new Tiki_Profile_DateConverter,
			'restrict_end' => new Tiki_Profile_DateConverter,
			'sort_default_field' => new Tiki_Profile_ValueMapConverter( array( 'modification' => -1, 'creation' => -2, 'item' => -3 ) ),
			'list_default_status' => new Tiki_Profile_ValueMapConverter( array( 'open' => 'o', 'pending' => 'p', 'closed' => 'c' ), $implodeArray ),
			'default_status' => new Tiki_Profile_ValueMapConverter( array( 'open' => 'o', 'pending' => 'p', 'closed' => 'c' ) ),
			'modification_status' => new Tiki_Profile_ValueMapConverter( array( 'open' => 'o', 'pending' => 'p', 'closed' => 'c' ) ),
		);
	} // }}}

	function canInstall() // {{{
	{
		$data = $this->getData();

		// Check for mandatory fields
		if( !isset( $data['name'] ) ) {
			$ref = $this->obj->getRef();
			throw (new Exception('No name for tracker:' . (empty($ref) ? '' : ' ref=' . $ref)));
		}
		
		// Check for unknown fields
		$optionMap = $this->getOptionMap();
		$remain = array_diff( array_keys( $data ), array_keys( $optionMap ) );
		if( count( $remain ) ) {
			throw (new Exception('Cannot map object options: "' . implode('","', $remain) . '" for tracker:' . $data['name']));
		}
		
		return true;
	} // }}}

	function _install() // {{{
	{
		$values = $this->getDefaults();

		$input = $this->getData();
		$this->replaceReferences( $input );

		$conversions = $this->getOptionConverters();
		foreach( $input as $key => $value )
		{
			if( array_key_exists( $key, $conversions ) )
				$values[$key] = $conversions[$key]->convert( $value );
			else
				$values[$key] = $value;
		}

		$name = $values['name'];
		$description = $values['description'];

		unset( $values['name'] );
		unset( $values['description'] );

		$optionMap = $this->getOptionMap();

		$options = array();
		foreach( $values as $key => $value )
		{
			$key = $optionMap[$key];
			$options[$key] = $value;
		}

		global $trklib;
		if( ! $trklib )
			require_once 'lib/trackers/trackerlib.php';
		
		// using false as trackerId stops multiple trackers of same name being created
		return $trklib->replace_tracker( false, $name, $description, $options, 'y' );
	} // }}}

	function _export($trackerId) // {{{
	{
		global $trklib; require_once 'lib/trackers/trackerlib.php';
		$info = $trklib->get_tracker($trackerId);
		if (empty($info)) {
			return '';
		}
		if ($options = $trklib->get_tracker_options($trackerId)) {
			$info = array_merge($info, $options);
		}
		$optionMap = array_flip($this->getOptionMap());
		$defaults = $this->getDefaults();
		$conversions = $this->getOptionConverters();
		$ref = 'tracker_'.$trackerId;
		$res = array();
		$allow = array();
		$show = array();
		$res[] = 'objects:';
		$res[] = ' -';
		$res[] = '  type: tracker';
		$res[] = '  ref: '.$ref;
		$res[] = '  data:';
		$tab = '   ';
		$res[] = $tab.'name: '.$info['name'];
		if (!empty($info['description']))
			$res[] = $tab.'description: '.$info['description'];
		foreach ($info as $key => $value) {
			if (!empty($optionMap[$key]) && (!isset($defaults[$optionMap[$key]]) || $value != $defaults[$optionMap[$key]])) {
				if (strstr($optionMap[$key], 'allow_')) {
					$allow[] = str_replace('allow_', '', $optionMap[$key]);
				} elseif (strstr($optionMap[$key], 'show_')) {
					$show[] = str_replace('show_', '', $optionMap[$key]);
				} else {
					$res[] = $tab.$optionMap[$key].': '.$conversions[$optionMap[$key]]->reverse( $value );
				}
			}
		}
		if (!empty($allow)) {
			$res[] .= $tab.'allow: ['.implode(', ', $allow).']';
		}
		if (!empty($show)) {
			$res[] .= $tab.'show: ['.implode(', ', $show).']';
		}

		$fields = $trklib->list_tracker_fields($trackerId);
		$prof = new Tiki_Profile_InstallHandler_TrackerField();
		foreach ($fields['data'] as $field) {
			$res = array_merge($res, $prof->_export($field));
		}
		return implode("\n", $res);
	} // }}}

} // }}}

class Tiki_Profile_InstallHandler_TrackerField extends Tiki_Profile_InstallHandler // {{{
{
	private function getData() // {{{
	{
		if( $this->data )
			return $this->data;

		$data = $this->obj->getData();

		$data = Tiki_Profile::convertLists( $data, array(
			'flags' => 'y',
		) );

		$data = Tiki_Profile::convertYesNo( $data );

		return $this->data = $data;
	} // }}}

	function getDefaultValues() // {{{
	{
		return array(
			'name' => '',
			'description' => '',
			'type' => 'text_field',
			'options' => '',
			'list' => 'n',
			'link' => 'n',
			'searchable' => 'n',
			'public' => 'n',
			'visible' => 'n',
			'mandatory' => 'n',
			'multilingual' => 'n',
			'order' => 1,
			'choices' => '',   //just adding this as a placeholder
			'errordesc' => '',
			'visby' => '',     //just adding this as a placeholder for now - format seems quite complex
			'editby' => '',    //just adding this as a placeholder for now - format seems quite complex
			'descparsed' => 'n',			
		);
	} // }}}

	function getConverters() // {{{
	{
		return array(
			'type' => new Tiki_Profile_ValueMapConverter( array( // {{{
				'text_field' => 't',
				'text_area' => 'a',
				'checkbox' => 'c',
				'numeric' => 'n',
				'currency' => 'b',
				'dropdown' => 'd',
				'dropdown_other' => 'D',
				'radio' => 'R',
				'user' => 'u',
				'group' => 'g',
				'ip_address' => 'I',
				'country' => 'y',
				'datetime' => 'f',
				'calendar' => 'j',
				'image' => 'i',
				'action' => 'x',
				'header' => 'h',
				'static' => 'S',
				'category' => 'e',
				'item_link' => 'r',
				'item_list' => 'l',
				'item_list_dynamic' => 'w',
				'email' => 'm',
				'auto_increment' => 'q',
				'user_subscription' => 'U',
				'map' => 'G',
				'system' => 's',
				'computed' => 'C',
				'preference' => 'p',
				'attachment' => 'A',
				'page' => 'k',
				'in_group' => 'N',
			) ), // }}}
			'visible' => new Tiki_Profile_ValueMapConverter( array(
				'public' => 'n',
				'admin_only' => 'y',
				'admin_editable' => 'p',
				'creator_editable' => 'c',
			) ),
		);
	} // }}}
	private function getOptionMap() //{{{
	{
		return array(
			'type' => 'type',
			'order' => 'position',
			'visible' => 'isHidden',
			'description' => 'description',
			'descparsed' => 'descriptionIsParsed',
			'errordesc' => 'errorMsg',
			'list' => 'IsTblVisible',
			'link' => 'isMain',
			'searchable' => 'isSearchable',
			'public' => 'isPublic',
			'mandatory' => 'isMandatory',
			'multilingual' => 'isMultilingual',
		);
	} // }}}

	function canInstall()
	{
		$data = $this->getData();

		if( ! isset( $data['name'], $data['tracker'] ) )
			return false;

		return true;
	}

	function _install()
	{
		$data = $this->getData();
		$converters = $this->getConverters();
		$this->replaceReferences( $data );

		foreach( $data as $key => &$value )
			if( isset( $converters[$key] ) )
				$value = $converters[$key]->convert( $value );

		$data = array_merge( $this->getDefaultValues(), $data );

		global $trklib;
		if( ! $trklib )
			require_once 'lib/trackers/trackerlib.php';

		return $trklib->replace_tracker_field(
			$data['tracker'],
			false,
			$data['name'],
			$data['type'],
			$data['link'],
			$data['searchable'],
			$data['list'],
			$data['public'],
			$data['visible'],
			$data['mandatory'],
			$data['order'],
			$data['options'],
			$data['description'],
			$data['multilingual'],
			$data['choices'],
			$data['errordesc'],
			$data['visby'],
			$data['editby'],
			$data['descparsed'] );
	}

	function _export($info)
	{
		$optionMap = array_flip($this->getOptionMap());
		$defaults = $this->getDefaultValues();
		$conversions = $this->getConverters();
		$res[] = ' -';
		$refi = 'field_'.$info['fieldId'];
		$res[] = '  type: tracker_field';
		$res[] = '  ref: '. $refi;
		$res[] = '  data:';
		$res[] = '   name: '.$info['name'];
		$res[] = '   tracker: $tracker_'.$info['trackerId'];
		if (!empty($info['options'])) $res[] = '   options: '.$info['options'];
		$flag = array();
		$tab = '   ';
		foreach ($info as $key => $value) {
			if (!empty($optionMap[$key]) && (!isset($defaults[$optionMap[$key]]) || $value != $defaults[$optionMap[$key]])) {
				if (in_array($optionMap[$key], array('list', 'link', 'searchable', 'public', 'mandatory', 'multilingual'))) {
					$flag[] = $optionMap[$key];
				} elseif (!empty($conversions[$optionMap[$key]])) {
					$reverseVal = $conversions[$optionMap[$key]]->reverse( $value );
					$res[] = $tab.$optionMap[$key].': '.(empty($reverseVal)? $value: $reverseVal);
				} else {
					$res[] = $tab.$optionMap[$key].': '.$value;
				}
			}
		}
		if (!empty($flag)) {
				$res[] .= $tab.'flags: ['.implode(', ', $flag).']';
		}
		return $res;
	}
} // }}}

class Tiki_Profile_InstallHandler_TrackerItem extends Tiki_Profile_InstallHandler // {{{
{
	private function getData() // {{{
	{
		if( $this->data )
			return $this->data;

		$data = $this->obj->getData();

		return $this->data = $data;
	} // }}}

	function getDefaultValues() // {{{
	{
		return array(
			'tracker' => 0,
			'status' => 'o',
			'values' => array(),
		);
	} // }}}

	function getConverters() // {{{
	{
		return array(
			'status' => new Tiki_Profile_ValueMapConverter( array( 'open' => 'o', 'pending' => 'p', 'closed' => 'c' ) ),
		);
	} // }}}

	function canInstall()
	{
		$data = $this->getData();

		if( ! isset( $data['tracker'], $data['values'] ) )
			return false;

		if( ! is_array( $data['values'] ) )
			return false;

		foreach( $data['values'] as $row )
			if( ! is_array( $row ) || count( $row ) != 2 )
				return false;

		return true;
	}

	function _install()
	{
		$data = $this->getData();
		$converters = $this->getConverters();
		$this->replaceReferences( $data );

		foreach( $data as $key => &$value )
			if( isset( $converters[$key] ) )
				$value = $converters[$key]->convert( $value );

		$data = array_merge( $this->getDefaultValues(), $data );

		global $trklib;
		if( ! $trklib )
			require_once 'lib/trackers/trackerlib.php';

		$fields = $trklib->list_tracker_fields( $data['tracker'] );
		foreach( $data['values'] as $row )
		{
			list( $f, $v) = $row;

			foreach( $fields['data'] as $key => $entry )
				if( $entry['fieldId'] == $f)
					$fields['data'][$key]['value'] = $v;
		}

		return $trklib->replace_item(
			$data['tracker'],
			0,
			$fields,
			$data['status'] );
	}
} // }}}

class Tiki_Profile_InstallHandler_WikiPage extends Tiki_Profile_InstallHandler // {{{
{
	private $content;
	private $description;
	private $name;
	private $lang;
	private $translations;
	private $message;
	private $structure;
	private $wysiwyg;
	
	private $mode = 'create_or_update';
	private $exists;

	function fetchData()
	{
		if( $this->name )
			return;

		$data = $this->obj->getData();

		if( array_key_exists( 'message', $data ) )
			$this->message = $data['message'];

		if( array_key_exists( 'name', $data ) )
			$this->name = $data['name'];
		if( array_key_exists( 'description', $data ) )
			$this->description = $data['description'];
		if( array_key_exists( 'lang', $data ) )
			$this->lang = $data['lang'];
		if( array_key_exists( 'content', $data ) )
			$this->content = $data['content'];
		if( array_key_exists( 'mode', $data ) )
			$this->mode = $data['mode'];
		if( $this->lang
			&& array_key_exists( 'translations', $data )
			&& is_array( $data['translations'] ) )
			$this->translations = $data['translations'];
		if( array_key_exists( 'structure', $data ) )
			$this->structure = $data['structure'];
		if( array_key_exists( 'wysiwyg', $data ) )
			$this->wysiwyg = $data['wysiwyg'];
	}

	function canInstall()
	{
		$this->fetchData();
		if( empty( $this->name ) || empty( $this->content ) )
			return false;

		$this->convertMode();

		return true;
	}

	private function convertMode() {
		global $tikilib;

		$this->exists = $tikilib->page_exists($this->name);

		switch( $this->mode ) {
		case 'create':
			if( $this->exists )
				throw new Exception( "Page {$this->name} already exists and profile does not allow update." );
			break;
		case 'update':
		case 'append':
			if( ! $this->exists )
				throw new Exception( "Page {$this->name} does not exist and profile only allows update." );
			break;
		case 'create_or_update':
			return $this->exists ? 'update' : 'create';
		case 'create_or_append':
			return $this->exists ? 'append' : 'create';
		default:
			throw new Exception( "Invalid mode '{$this->mode}' for wiki handler." );
		}

		return $this->mode;
	}

	function _install()
	{
		// Normalize mode
		$this->canInstall();

		global $tikilib;
		$this->fetchData();
		$this->replaceReferences( $this->name );
		$this->replaceReferences( $this->description );
		$this->replaceReferences( $this->content );
		$this->replaceReferences( $this->lang );
		$this->replaceReferences( $this->translations );
		$this->replaceReferences( $this->message );
		$this->replaceReferences( $this->structure );
		$this->replaceReferences( $this->wysiwyg );
	
		$this->mode = $this->convertMode();

		if( strpos( $this->content, 'wikidirect:' ) === 0 ) {
			$pageName = substr( $this->content, strlen('wikidirect:') );
			$this->content = $this->obj->getProfile()->getPageContent( $pageName );
		}

		if( $this->mode == 'create' ) {
			if ( $this->wysiwyg ) {
				$this->wysiwyg = 'y';
				$is_html = true;
			} else {
				$this->wysiwyg = 'n';
				$is_html = false;
			} 
			if( ! $this->message ) {
				$this->message = tra('Created by profile installer');
			}
			if( ! $tikilib->create_page( $this->name, 0, $this->content, time(), $this->message, 'admin', '0.0.0.0', $this->description, $this->lang, $is_html, null, $this->wysiwyg ) )
				return null;
		} else {
			$info = $tikilib->get_page_info( $this->name, true, true );

			if( ! $this->wysiwyg ) {
				if ( ! empty($info['wysiwyg']) ) { 
					$this->wysiwyg = $info['wysiwyg'];
				} else {
					$this->wysiwyg = 'n';
				}
				if( isset($info['is_html']) ) {
					$is_html = $info['is_html'];
				} else {
					$is_html = false;
				} 
			} else {
				$this->wysiwyg = 'y';
				$is_html = true;
			}

			if( ! $this->description )
				$this->description = $info['description'];

			if( ! $this->lang )
				$this->lang = $info['lang'];

			if( $this->mode == 'append' ) {
				$this->content = rtrim( $info['data'] ) . "\n" . trim($this->content) . "\n";
			}

			if( ! $this->message ) {
				$this->message = tra('Page updated by profile installer');
			}

			$tikilib->update_page( $this->name, $this->content, $this->message, 'admin', '0.0.0.0', $this->description, 0, $this->lang, $is_html, null, null, $this->wysiwyg );
		}

		global $multilinguallib;
		require_once 'lib/multilingual/multilinguallib.php';

		$current = $tikilib->get_page_id_from_name( $this->name );
		foreach( $this->translations as $targetName ) {
			$target = $tikilib->get_page_info( $targetName );

			if( $target && $target['lang'] && $target['lang'] != $this->lang ) {
				$multilinguallib->insertTranslation( 'wiki page', $current, $this->lang, $target['page_id'], $target['lang'] );
			}
		}
		
		if (!empty($this->structure)) {
			global $structlib; include_once 'lib/structures/structlib.php';
			$structlib->s_create_page($this->structure, 0, $this->name, '',$this->structure);
		}

		return $this->name;
	}
} // }}}

class Tiki_Profile_InstallHandler_Category extends Tiki_Profile_InstallHandler // {{{
{
	private $name;
	private $description = '';
	private $parent = 0;
	private $items = array();

	function fetchData()
	{
		if( $this->name )
			return;

		$data = $this->obj->getData();

		if( array_key_exists( 'name', $data ) )
			$this->name = $data['name'];
		if( array_key_exists( 'description', $data ) )
			$this->description = $data['description'];
		if( array_key_exists( 'parent', $data ) )
			$this->parent = $data['parent'];
		if( array_key_exists( 'items', $data ) && is_array( $data['items'] ) )
			foreach( $data['items'] as $pair )
				if( is_array($pair) && count( $pair ) == 2 )
					$this->items[] = $pair;
	}

	function canInstall()
	{
		$this->fetchData();

		if( empty( $this->name ) )
			return false;

		return true;
	}

	function _install()
	{
		global $tikilib;
		$this->fetchData();
		$this->replaceReferences( $this->name );
		$this->replaceReferences( $this->description );
		$this->replaceReferences( $this->parent );
		$this->replaceReferences( $this->items );
		
		global $categlib;
		require_once 'lib/categories/categlib.php';
		if ($id = $categlib->exist_child_category( $this->parent, $this->name )) {
			$categlib->update_category( $id, $this->name, $this->description, $this->parent );
		} else {
			$id = $categlib->add_category( $this->parent, $this->name, $this->description );
		}

		foreach( $this->items as $item )
		{
			list( $type, $object ) = $item;

			$type = Tiki_Profile_Installer::convertType( $type );
			$object = Tiki_Profile_Installer::convertObject( $type, $object );
			$categlib->categorize_any( $type, $object, $id );
		}

		return $id;
	}
} // }}}

class Tiki_Profile_InstallHandler_FileGallery extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'owner' => 'admin',
			'public' => 'n',
			'galleryId' => null,
			'parent' => -1,
		);

		$conversions = array(
			'owner' => 'user',
			'max_rows' => 'maxRows',
			'parent' => 'parentId',
		);

		$data = $this->obj->getData();

		$data = Tiki_Profile::convertLists( $data, array(
			'flags' => 'y',
		) );

		$column = isset( $data['column'] ) ? $data['column'] : array();
		$popup = isset( $data['popup'] ) ? $data['popup'] : array();

		$both = array_intersect( $column, $popup );
		$column = array_diff( $column, $both );
		$popup = array_diff( $popup, $both );

		foreach( $both as $value )
			$data["show_$value"] = 'a';
		foreach( $column as $value )
			$data["show_$value"] = 'y';
		foreach( $popup as $value )
			$data["show_$value"] = 'o';

		unset( $data['popup'] );
		unset( $data['column'] );

		$data = array_merge( $defaults, $data );

		foreach( $conversions as $old => $new )
			if( array_key_exists( $old, $data ) )
			{
				$data[$new] = $data[$old];
				unset( $data[$old] );
			}

		unset( $data['galleryId'] );
		$this->replaceReferences($data);

		if (!empty($data['name'])) {
			global $filegallib; require_once 'lib/filegals/filegallib.php';
			$data['galleryId'] = $filegallib->getGalleryId($data['name'], $data['parentId']);
		}
		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if( ! isset( $data['name'] ) )
			return false;
		return $this->convertMode($data);
	}
	private function convertMode($data)
	{
		if (!isset($data['mode'])) {
			return true; // will duplicate if already exists
		}
		switch ($data['mode']) {
		case 'update':
			if (empty($data['galleryId'])) {
				throw new Exception(tra('File gallery does not exist').' '.$data['name']);
			}
		case 'create':
			if (!empty($data['galleryId'])) {
				throw new Exception(tra('File gallery already exists').' '.$data['name']);
			}
		}
		return true;
	}
	function _install()
	{
		global $filegallib;
		if( ! $filegallib ) require_once 'lib/filegals/filegallib.php';

		$input = $this->getData();

		return $filegallib->replace_file_gallery( $input );
	}
} // }}}

class Tiki_Profile_InstallHandler_Module extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'cache' => 0,
			'rows' => 10,
			'custom' => null,
			'groups' => array(),
			'params' => array(),
		);

		$data = array_merge(
			$defaults,
			$this->obj->getData()
		);

		$data['groups'] = serialize( $data['groups'] );

		$data = Tiki_Profile::convertYesNo( $data );
		$data['params'] = Tiki_Profile::convertYesNo( $data['params'] );
		
		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if( ! isset( $data['name'], $data['position'], $data['order'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $modlib;
		if( ! $modlib ) require_once 'lib/modules/modlib.php';

		$data = $this->getData();
		
		include_once 'lib/modules/modlib.php';	// use zones from modlib
		$module_zones = $modlib->module_zones;
		$module_zones = array_map(array($this, 'processModuleZones'), $module_zones);
		$module_zones = array_flip( $module_zones );
		$data['position'] = $module_zones[$data['position']];

		$this->replaceReferences( $data );

		$data['params'] = http_build_query( $data['params'], '', '&' );
		
		if( $data['custom'] )
		{
			$modlib->replace_user_module( $data['name'], $data['name'], (string) $data['custom'] );
		}

		if ( is_null($data['params']) )
                {
                        // Needed on some versions of php to make sure null is not passed all the way to query as a parameter, since params field in db cannot be null
                        $data['params'] = '';
                }

		return $modlib->assign_module( 0, $data['name'], null, $data['position'], $data['order'], $data['cache'], $data['rows'], $data['groups'], $data['params'] );
	}
	
	private function processModuleZones( $zone_id ) {
		return str_replace( '_modules', '', $zone_id);
	}
} // }}}

class Tiki_Profile_InstallHandler_Menu extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'description' => '',
			'collapse' => 'collapsed',
			'icon' => '',
			'groups' => array(),
			'items' => array(),
			'cache' => 0,
		);

		$data = array_merge(
			$defaults,
			$this->obj->getData()
		);

		$data['groups'] = serialize( $data['groups'] );

		$position = 0;
		foreach( $data['items'] as &$item )
			$this->fixItem( $item, $position );

		$items = array();
		$this->flatten( $data['items'], $items );
		$data['items'] = $items;

		return $this->data = $data;
	}

	function flatten( $entries, &$list ) // {{{
	{
		foreach( $entries as $item )
		{
			$children = $item['items'];
			unset( $item['items'] );

			$list[] = $item;
			$this->flatten( $children, $list );
		}
	} // }}}

	private function fixItem( &$item, &$position, $parent = null ) // {{{
	{
		$position += 10;

		if( !isset( $item['name'] ) )
			$item['name'] = 'Unspecified';
		if( !isset( $item['url'] ) )
			$item['url'] = 'tiki-index.php';
		if( !isset( $item['section'] ) )
			$item['section'] = null;
		if( !isset( $item['level'] ) )
			$item['level'] = 0;
		if( ! isset( $item['permissions'] ) )
			$item['permissions'] = array();
		if( ! isset( $item['groups'] ) )
			$item['groups'] = array();
		if( ! isset( $item['items'] ) )
			$item['items'] = array();

		$item['position'] = $position;

		$item['type'] = 's';

		if( $parent )
		{
			if( $parent['type'] === 's' )
				$item['type'] = 1;
			else
				$item['type'] = $parent['type'] + 1;

			$item['level'] = $parent['level'] + 1;

			$item['permissions'] = array_unique( 
				array_merge( $parent['permissions'], $item['permissions'] ) );
			$item['groups'] = array_unique( 
				array_merge( $parent['groups'], $item['groups'] ) );
		}

		foreach( $item['items'] as &$child )
			$this->fixItem( $child, $position, $item );

		foreach( $item['permissions'] as &$perm )
			if( strpos( $perm, 'tiki_p_' ) !== 0 )
				$perm = 'tiki_p_' . $perm;
	} // }}}

	function canInstall()
	{
		$data = $this->getData();
		if( ! isset( $data['name'] ) )
			return false;
		if( count( $data['items'] ) == 0 )
			return false;

		return true;
	}

	function _install()
	{
		global $modlib, $menulib, $tikilib;
		if( ! $modlib ) require_once 'lib/modules/modlib.php';
		if( ! $menulib ) require_once 'lib/menubuilder/menulib.php';

		$data = $this->getData();

		$this->replaceReferences( $data );
		
		$type = 'f';
		if( $data['collapse'] == 'collapsed' )
			$type = 'd';
		elseif( $data['collapse'] == 'expanded' )
			$type = 'e';

		$menulib->replace_menu( 0, $data['name'], $data['description'], $type, $data['icon'] );
		$result = $tikilib->query( "SELECT MAX(`menuId`) FROM `tiki_menus`" );
		$menuId = reset( $result->fetchRow() );

		foreach( $data['items'] as $item )
			$menulib->replace_menu_option( $menuId, 0, $item['name'], $item['url'], $item['type'], $item['position'], $item['section'], implode( ',', $item['permissions'] ), implode( ',', $item['groups'] ), $item['level'] );

		// Set module title to menu_nn if it is not set by a parameter
		if( !isset($data['title']) )
		{
		$modtitle = "menu_$menuId";
		} else {
		$modtitle = $data['title'];
		}		
		
		// Set up module only as a user module if position is set to 'none'
		if( $data['position'] == 'none' )
		{
		// but still allow module_arguments	but keep it simple and don't include the $key=
				$extra = '';
				if( isset( $data['module_arguments'] ) )
				foreach( $data['module_arguments'] as $key => $value )
					$extra .= " $value";
							
			$content = "{menu id=$menuId$extra}";
			$modlib->replace_user_module( $data['name'], $modtitle, $content );
		}

		// Set module as side menu if both position and order are specified and position is not 'none'
		elseif( isset( $data['position'], $data['order'] ) )
		{
			if( $data['position'] == 'left' )
				$column = 'l';
			else
				$column = 'r';

			$extra = '';
			if( isset( $data['module_arguments'] ) )
				foreach( $data['module_arguments'] as $key => $value )
					$extra .= " $key=$value";

			$content = "{menu id=$menuId$extra}";

			$modlib->replace_user_module( $data['name'], $modtitle, $content );
			$modlib->assign_module( 0, "menu_$menuId", null, $column, $data['order'], $data['cache'], 10, $data['groups'], '' );
		}

		return $menuId;

	}
} // }}}

class Tiki_Profile_InstallHandler_MenuOption extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'type' => 'o',
			'optionId' => 0,
			'position' => 1,
			'section' => '',
			'perm' => '',
			'groups' => array(),
			'level' => 0,
			'icon' => '',
			'menuId' => 0
		);


		$data = $this->obj->getData();

		$data = array_merge( $defaults, $data );

		$this->replaceReferences($data);

		if (!empty($data['menuId']) && !empty($data['url'])) {
		   global $menulib; require_once 'lib/menubuilder/menulib.php';
		   $data['optionId'] = $menulib->get_option($data['menuId'], $data['url']);
		}
		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if( ! isset( $data['url'] ) || ! isset( $data['menuId'] ) )
			return false;
		return true;
	}
	function _install()
	{
		global $menulib; require_once 'lib/menubuilder/menulib.php';

		$data = $this->getData();

		return $menulib->replace_menu_option( $data['menuId'], $data['optionId'], $data['name'], $data['url'], $data['type'], $data['position'], $data['section'], $data['perm'], implode(',', $data['groups']), $data['level'], $data['icon'] );
	}
} // }}}

class Tiki_Profile_InstallHandler_Blog extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'description' => '',
			'user' => 'admin',
			'public' => 'n',
			'max_posts' => 10,
			'heading' => '',
			'post_heading' => '',
			'use_find' => 'y',
			'comments' => 'n',
			'show_avatar' => 'n',
		);

		$data = array_merge(
			$defaults,
			$this->obj->getData()
		);

		$data = Tiki_Profile::convertYesNo( $data );

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if( ! isset( $data['title'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $bloglib;
		if( ! $bloglib ) require_once 'lib/blogs/bloglib.php';

		$data = $this->getData();

		$this->replaceReferences( $data );

		$blogId = $bloglib->replace_blog( $data['title'], $data['description'], $data['user'], $data['public'], $data['max_posts'], 0, $data['heading'], $data['use_author'], $data['add_date'], $data['use_find'], $data['allow_comments'], $data['show_avatar'], $data['post_heading'] );
		
		return $blogId;
	}
} // }}}

class Tiki_Profile_InstallHandler_BlogPost extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'title' => 'Title',
			'private' => 'n',
			'user' => '',
		);

		$data = array_merge(
			$defaults,
			$this->obj->getData()
		);

		$data = Tiki_Profile::convertYesNo( $data );

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if( ! isset( $data['blog'] ) )
			return false;
		if( ! isset( $data['content'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $bloglib;
		if( ! $bloglib ) require_once 'lib/blogs/bloglib.php';

		$data = $this->getData();

		$this->replaceReferences( $data );

		if( isset( $data['blog'] ) && empty( $data['user'] ) ) {
			global $bloglib, $tikilib;
			if( ! $bloglib ) require_once 'lib/blogs/bloglib.php';

			$result = $tikilib->query( "SELECT `user` FROM `tiki_blogs` WHERE `blogId` = ?", array( $data['blog'] ) );

			if( $row = $result->fetchRow() ) {
				$data['user'] = $row['user'];
			}
		}

		$entryId = $bloglib->blog_post( $data['blog'], $data['content'], $data['excerpt'], $data['user'], $data['title'], '', $data['private'] );

		return $entryId;
	}
} // }}}

class Tiki_Profile_InstallHandler_PluginAlias extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'body' => array(
				'input' => 'ignore',
				'default' => '',
				'params' => array()
			),
			'params' => array(
			),
		);

		$data = array_merge(
			$defaults,
			$this->obj->getData()
		);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if( ! isset( $data['name'], $data['implementation'], $data['description'] ) )
			return false;

		if( ! is_array($data['description']) || ! is_array($data['body']) || ! is_array($data['params']) )
			return false;

		return true;
	}

	function _install()
	{
		global $tikilib;
		$data = $this->getData();

		$this->replaceReferences( $data );

		$name = $data['name'];
		unset( $data['name'] );

		$tikilib->plugin_alias_store( $name, $data );

		return $name;
	}
} // }}}

class Tiki_Profile_InstallHandler_WebmailAccount extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'mode' => 'create',		// 'create' or 'update' account with same name (i.e. 'account')
			'account' => '',		// * required
			'pop' => '', 			// * one of pop, imap, mbox or maildir required
			'port' => 110, 			// default for pop3
			'username' => '', 
			'pass' => '', 
			'msgs' => '', 			// messages per page
			'smtp' => '', 
			'useAuth' => 'n', 		// y|n (default null? = n)
			'smtpPort' => 25, 
			'flagsPublic' => 'n',	// y|n (default n)
			'autoRefresh' => 0, 	// seconds (default 0)
			'imap' => '',			// *? see pop
			'mbox' => '', 			// *? see pop
			'maildir' => '', 		// *? see pop
			'useSSL' => 'n',			// y|n (default n)
			'fromEmail' => '',
		);

		$data = array_merge(
			$defaults,
			$this->obj->getData()
		);
		
		$data['useAuth'] = $data['useAuth'] !== 'n' ? 'y' : 'n';	// should be unecessary surely, but can't find where to stop it (looked for ages!)
		$data['flagsPublic'] = $data['flagsPublic'] !== 'n' ? 'y' : 'n';
		$data['useSSL'] = $data['useSSL'] !== 'n' ? 'y' : 'n';
		$data['overwrite'] = $data['overwrite'] !== 'n' ? 'y' : 'n';
		
		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if( ! isset( $data['account']) || (!isset($data['pop']) && !isset($data['imap']) && !isset($data['mbox']) && !isset($data['maildir'] ))) {
			return false;
		}
		
		return true;
	}

	function _install()
	{
		global $tikilib, $user;
		$data = $this->getData();

		$this->replaceReferences( $data );

		global $webmaillib; require_once 'lib/webmail/webmaillib.php';
		
		if ($data['mode'] == 'update') {
			$accountId = $webmaillib->get_webmail_account_by_name( $user, $data['account']);
		} else {
			$accountId = 0;
		}	

		$accountId = $webmaillib->replace_webmail_account($accountId, $user, $data['account'], $data['pop'], (int) $data['port'], $data['username'],
				$data['pass'], (int) $data['msgs'], $data['smtp'], $data['useAuth'], (int) $data['smtpPort'], $data['flagsPublic'],
				(int) $data['autoRefresh'], $data['imap'], $data['mbox'], $data['maildir'], $data['useSSL'], $data['fromEmail']);

		return $accountId;
	}
} // }}}

class Tiki_Profile_InstallHandler_Webmail extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'accountId' => null,	// use current account if null or empty
			'accountName' => '',	// as above
			'to' => '',
			'cc' => '',
			'bcc' => '',
			'subject' => '',
			'body' => '',
			'html' => 'y',
			'reload' => 'y',		// reload the profile to update external refs
		);

		$data = array_merge(
			$defaults,
			$this->obj->getData()
		);
				
		return $this->data = $data;
	}

	function canInstall()
	{
		global $user, $webmaillib;
		require_once 'lib/webmail/webmaillib.php';

		$data = $this->getData();
		
		if( !isset( $data['accountId']) && !isset( $data['accountName']) && !$webmaillib->get_current_webmail_accountId($user)) {
			return false;	// webmail account not specified
		}
		
		if( !isset( $data['to']) && !isset( $data['cc']) && !isset( $data['bcc']) && !isset( $data['subject']) && !isset( $data['body'])) {
			return false;	// nothing specified?
		}
				
		return true;
	}

	function _install()
	{
		global $tikilib, $user;
		$data = $this->getData();
		
		if ($data['reload']) {
			// must be fresh data as the profile may have altered stuff since canInstall was run
			$this->obj->refreshExternals();
			foreach($this->obj->getProfile()->getObjects() as $obj) {
				if ($obj->getRef() == $this->obj->getRef()) {
					$this->obj = $obj;
				}
			}
			$this->data = null;	
			$data = $this->getData();
		}
		
		$this->replaceReferences( $data );

		global $webmaillib; require_once 'lib/webmail/webmaillib.php';
		
		if (!empty($data['accountId']) && $data['accountId'] != $webmaillib->get_current_webmail_accountId($user)) {
			$webmaillib->current_webmail_account($user, $data['accountId']);
		} else if (!empty($data['accountName'])) {
			$data['accountId'] = $webmaillib->get_webmail_account_by_name($user, $data['accountName']);
			if ($data['accountId'] > 0 && $data['accountId'] != $webmaillib->get_current_webmail_accountId($user)) {
				$webmaillib->current_webmail_account($user, $data['accountId']);
			}
		}	

		if( strpos( $data['body'], 'wikidirect:' ) === 0 ) {
			$pageName = substr( $this->content, strlen('wikidirect:') );
			$data['body'] = $this->obj->getProfile()->getPageContent( $pageName );
		}
		
		if (!$data['html']) {
			$data['body'] = strip_tags($data['body']);
		}
		$data['to']      = trim(str_replace(array("\n","\r"), "", html_entity_decode(strip_tags($data['to']))), ' ,');
		$data['cc']      = trim(str_replace(array("\n","\r"), "", html_entity_decode(strip_tags($data['cc']))), ' ,');
		$data['bcc']     = trim(str_replace(array("\n","\r"), "", html_entity_decode(strip_tags($data['bcc']))), ' ,');
		$data['subject'] = trim(str_replace(array("\n","\r"), "", html_entity_decode(strip_tags($data['subject']))));
		
		$webmailUrl = $tikilib->tikiUrl('tiki-webmail.php',  array(
				'locSection' => 'compose', 'to' => $data['to'], 'cc' => $data['cc'], 'bcc' => $data['bcc'],
				'subject' => $data['subject'], 'body' => $data['body'], 'useHTML' => $data['html'] ? 'y' : 'n' ));

		header('Location: ' . $webmailUrl);
		exit;	// means this profile never gets "remembered" - a good thing?
	}
} // }}}

class Tiki_Profile_InstallHandler_Webservice extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'schema_version' => null,
			'schema_documentation' => null,
		);

		$data = array_merge(
			$defaults,
			$this->obj->getData()
		);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if( ! isset( $data['name'], $data['url'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $tikilib;
		$data = $this->getData();

		$this->replaceReferences( $data );

		require_once 'lib/webservicelib.php';

		$ws = Tiki_Webservice::create( $data['name'] );
		$ws->url = $data['url'];
		$ws->body = $data['body'];
		$ws->schemaVersion = $data['schema_version'];
		$ws->schemaDocumentation = $data['schema_documentation'];
		$ws->save();

		return $ws->getName();
	}
} // }}}

class Tiki_Profile_InstallHandler_WebserviceTemplate extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
		);

		$data = array_merge(
			$defaults,
			$this->obj->getData()
		);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if( ! isset( $data['name'], $data['engine'], $data['output'], $data['content'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $tikilib;
		$data = $this->getData();

		$this->replaceReferences( $data );

		require_once 'lib/webservicelib.php';

		$ws = Tiki_Webservice::getService( $data['webservice'] );
		$template = $ws->addTemplate( $data['name'] );
		$template->engine = $data['engine'];
		$template->output = $data['output'];
		$template->content = $data['content'];
		$template->save();

		return $template->name;
	}
} // }}}

class Tiki_Profile_InstallHandler_Rss extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$data = $this->obj->getData();
		$data = Tiki_Profile::convertLists( $data, array(
			'show' => 'y',
		), true );

		$defaults = array(
			'description' => null,
			'refresh' => 30,
			'show_title' => 'n',
			'show_publication_date' => 'n',
		);

		$data = array_merge(
			$defaults,
			$data
		);
		$data = Tiki_Profile::convertYesNo( $data );

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if( ! isset( $data['name'], $data['url'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $rsslib;
		$data = $this->getData();

		$this->replaceReferences( $data );

		require_once 'lib/rss/rsslib.php';

		if( $rsslib->replace_rss_module( 0, $data['name'], $data['description'], $data['url'], $data['refresh'], $data['show_title'], $data['show_publication_date'] ) ) {

			$id = (int) $rsslib->getOne("SELECT MAX(`rssId`) FROM `tiki_rss_modules`");
			return $id;
		}
	}
} // }}}

class Tiki_Profile_InstallHandler_Topic extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$data = $this->obj->getData();
		$data = Tiki_Profile::convertYesNo( $data );

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if( ! isset( $data['name'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $artlib;
		$data = $this->getData();

		$this->replaceReferences( $data );

		require_once 'lib/articles/artlib.php';

		$id = $artlib->add_topic( $data['name'], null, null, null, null );

		return $id;
	}
} // }}}

class Tiki_Profile_InstallHandler_ArticleType extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$data = $this->obj->getData();
		$data = Tiki_Profile::convertLists( $data, array(
			'show' => 'y',
			'allow' => 'y',
		), true );

		$defaults = array(
			'show_pre_publication' => 'n',
			'show_post_expire' => 'n',
			'show_heading_only' => 'n',
			'show_image' => 'n',
			'show_avatar' => 'n',
			'show_author' => 'n',
			'show_publication_date' => 'n',
			'show_expiration_date' => 'n',
			'show_reads' => 'n',
			'show_size' => 'n',
			'show_topline' => 'n',
			'show_subtitle' => 'n',
			'show_link_to' => 'n',
			'show_image_caption' => 'n',
			'show_language' => 'n',

			'allow_ratings' => 'n',
			'allow_comments' => 'n',
			'allow_comments_rating_article' => 'n',
			'allow_creator_edit' => 'n',
		);

		$data = array_merge( $defaults, $data );

		$data = Tiki_Profile::convertYesNo( $data );

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if( ! isset( $data['name'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $artlib;
		$data = $this->getData();

		$this->replaceReferences( $data );

		require_once 'lib/articles/artlib.php';

		$converter = new Tiki_Profile_ValueMapConverter( array( 'y' => 'on' ) );

		if( ! $artlib->get_type( $data['name'] ) ) {
			$artlib->add_type( $data['name'] );
		}
		
		$artlib->edit_type( 
			$data['name'],
			$converter->convert( $data['allow_ratings'] ),
			$converter->convert( $data['show_pre_publication'] ),
			$converter->convert( $data['show_post_expire'] ),
			$converter->convert( $data['show_heading_only'] ),
			$converter->convert( $data['allow_comments'] ),
			$converter->convert( $data['allow_comments_rating_article'] ),
			$converter->convert( $data['show_image'] ),
			$converter->convert( $data['show_avatar'] ),
			$converter->convert( $data['show_author'] ),
			$converter->convert( $data['show_publication_date'] ),
			$converter->convert( $data['show_expiration_date'] ),
			$converter->convert( $data['show_reads'] ),
			$converter->convert( $data['show_size'] ),
			$converter->convert( $data['show_topline'] ),
			$converter->convert( $data['show_subtitle'] ),
			$converter->convert( $data['show_link_to'] ),
			$converter->convert( $data['show_image_caption'] ),
			$converter->convert( $data['show_language'] ),
			$converter->convert( $data['allow_creator_edit'] )
		);

		return $data['name'];
	}
} // }}}

class Tiki_Profile_InstallHandler_Article extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$data = $this->obj->getData();

		$defaults = array(
			'author' => 'Anonymous',
			'heading' => '',
			'publication_date' => time(),
			'expiration_date' => time() + 3600*24*30,
			'type' => 'Article',
			'topline' => '',
			'subtitle' => '',
			'link_to' => '',
			'language' => 'en',
		);

		$data = array_merge( $defaults, $data );

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if( ! isset( $data['title'], $data['topic'], $data['body'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $artlib;
		$data = $this->getData();

		$this->replaceReferences( $data );

		require_once 'lib/articles/artlib.php';

		$dateConverter = new Tiki_Profile_DateConverter;

		$id = $artlib->replace_article( 
			$data['title'],
			$data['author'],
			$data['topic'],
			'n',
			null,
			null,
			null,
			null,
			$data['heading'],
			$data['body'],
			$dateConverter->convert( $data['publication_date'] ),
			$dateConverter->convert( $data['expiration_date'] ),
			'admin',
			0,
			0,
			0,
			$data['type'],
			$data['topline'],
			$data['subtitle'],
			$data['link_to'],
			null,
			$data['language']
		);

		return $id;
	}
} // }}}

class Tiki_Profile_InstallHandler_Forum extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$data = $this->obj->getData();

		$defaults = array(
			'description' => '',
			'flood_interval' => 120,
			'moderator' => 'admin',
			'per_page' => 10,
			'prune_max_age' => 3*24*3600,
			'prune_unreplied_max_age' => 30*24*3600,
			'topic_order' => 'lastPost_desc',
			'thread_order' => '',
			'section' => '',
			'inbound_pop_server' => '',
			'inbound_pop_port' => 110,
			'inbound_pop_user' => '',
			'inbound_pop_password' => '',
			'outbound_address' => '',
			'outbound_from' => '',
			'approval_type' => 'all_posted',
			'moderator_group' => '',
			'forum_password' => '',
			'attachments' => 'none',
			'attachments_store' => 'db',
			'attachments_store_dir' => '',
			'attachments_max_size' => 10000000,
			'forum_last_n' => 0,
			'comments_per_page' => '',
			'thread_style' => '',
			'is_flat' => 'n',

			'list_topic_reads' => 'n',
			'list_topic_replies' => 'n',
			'list_topic_points' => 'n',
			'list_topic_last_post' => 'n',
			'list_topic_last_post_title' => 'n',
			'list_topic_last_post_avatar' => 'n',
			'list_topic_author' => 'n',
			'list_topic_author_avatar' => 'n',

			'show_description' => 'n',

			'enable_flood_control' => 'n',
			'enable_inbound_mail' => 'n',
			'enable_prune_unreplied' => 'n',
			'enable_prune_old' => 'n',
			'enable_vote_threads' => 'n',
			'enable_outbound_for_inbound' => 'n',
			'enable_outbound_reply_link' => 'n',
			'enable_topic_smiley' => 'n',
			'enable_topic_summary' => 'n',
			'enable_ui_avatar' => 'n',
			'enable_ui_flag' => 'n',
			'enable_ui_posts' => 'n',
			'enable_ui_level' => 'n',
			'enable_ui_email' => 'n',
			'enable_ui_online' => 'n',
			'enable_password_protection' => 'n',
		);

		$data = Tiki_Profile::convertLists( $data, array(
			'enable' => 'y',
			'list' => 'y',
			'show' => 'y',
		), true );

		$data = array_merge( $defaults, $data );

		$data = Tiki_Profile::convertYesNo( $data );

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();

		if( ! isset( $data['name'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $dbTiki;
		require_once 'lib/comments/commentslib.php';
		$comments = new Comments( $dbTiki );

		$data = $this->getData();
		$this->replaceReferences( $data );

		$attConverter = new Tiki_Profile_ValueMapConverter( array(
			'none' => 'att_no',
			'everyone' => 'att_all',
			'allowed' => 'att_perm',
			'admin' => 'att_admin',
		) );

		$id = $comments->replace_forum( 
			0,
			$data['name'],
			$data['description'],
			$data['enable_flood_control'],
			$data['flood_interval'],
			$data['moderator'],
			$data['mail'],
			$data['enable_inbound_mail'],
			$data['enable_prune_unreplied'],
			$data['prune_unreplied_max_age'],
			$data['enable_prune_old'],
			$data['prune_max_age'],
			$data['per_page'],
			$data['topic_order'],
			$data['thread_order'],
			$data['section'],
			$data['list_topic_reads'],
			$data['list_topic_replies'],
			$data['list_topic_points'],
			$data['list_topic_last_post'],
			$data['list_topic_author'],
			$data['enable_vote_threads'],
			$data['show_description'],
			$data['inbound_pop_server'],
			$data['inbound_pop_port'],
			$data['inbound_pop_user'],
			$data['inbound_pop_password'],
			$data['outbound_address'],
			$data['enable_outbound_for_inbound'],
			$data['enable_outbound_reply_link'],
			$data['outbound_from'],
			$data['enable_topic_smiley'],
			$data['enable_topic_summary'],
			$data['enable_ui_avatar'],
			$data['enable_ui_flag'],
			$data['enable_ui_posts'],
			$data['enable_ui_level'],
			$data['enable_ui_email'],
			$data['enable_ui_online'],
			$data['approval_type'],
			$data['moderator_group'],
			$data['forum_password'],
			$data['enable_password_protection'],
			$attConverter->convert( $data['attachments'] ),
			$data['attachments_store'],
			$data['attachments_store_dir'],
			$data['attachments_max_size'],
			$data['forum_last_n'],
			$data['comments_per_page'],
			$data['thread_style'],
			$data['is_flat'],
			$data['list_att_nb'],
			$data['list_topic_last_post_title'],
			$data['list_topic_last_post_avatar'],
			$data['list_topic_author_avatar']
		);

		return $id;
	}
} // }}}

class Tiki_Profile_InstallHandler_Template extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'sections' => array( 'wiki' ),
			'type' => 'static',
		);

		$data = array_merge(
			$defaults,
			$this->obj->getData()
		);

		$data = Tiki_Profile::convertYesNo( $data );

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if( ! isset( $data['name'] ) )
			return false;
		if( ! isset( $data['content'] ) && ! isset( $data['page'] ) )
			return false;
		if( ! isset( $data['sections'] ) || ! is_array( $data['sections'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $templateslib;
		if( ! $templateslib ) require_once 'lib/templates/templateslib.php';

		$data = $this->getData();

		$this->replaceReferences( $data );

		if( isset( $data['page'] ) ) {
			$data['content'] = 'page:' . $data['page'];
			$data['type'] = 'page';
		}

		$templateId = $templateslib->replace_template( null, $data['name'], $data['content'], $data['type'] );
		foreach( $data['sections'] as $section ) {
			$templateslib->add_template_to_section( $templateId, $section );
		}

		return $templateId;
	}
} // }}}

class Tiki_Profile_InstallHandler_DataChannel extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'domain' => 'tiki://local',
			'groups' => array( 'Admins' ),
		);

		$data = array_merge(
			$defaults,
			$this->obj->getData()
		);

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if( ! isset( $data['name'], $data['profile'] ) )
			return false;
		if( ! is_array( $data['groups'] ) )
			return false;
		if( ! is_string( $data['domain'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $tikilib, $prefs;
		require_once 'lib/profilelib/channellib.php';
		$channels = Tiki_Profile_ChannelList::fromConfiguration( $prefs['profile_channels'] );

		$data = $this->getData();

		$this->replaceReferences( $data );

		$channels->addChannel( $data['name'], $data['domain'], $data['profile'], $data['groups'] );
		$tikilib->set_preference( 'profile_channels', $channels->getConfiguration() );

		return $data['name'];
	}
} // }}}

class Tiki_Profile_InstallHandler_Perspective extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'preferences' => array(),
		);

		$data = array_merge(
			$defaults,
			$this->obj->getData()
		);

		$data['preferences'] = Tiki_Profile::convertLists( $data['preferences'], array(
			'enable' => 'y', 
			'disable' => 'n'
		) );

		$data['preferences'] = Tiki_Profile::convertYesNo( $data['preferences'] );

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if( ! isset( $data['name'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $perspectivelib;
		require_once 'lib/perspectivelib.php';

		$data = $this->getData();

		$this->replaceReferences( $data );

		if( $persp = $perspectivelib->replace_perspective( 0, $data['name'] ) ) {
			$perspectivelib->replace_preferences( $persp, $data['preferences'] );
		}

		return $persp;
	}
} // }}}

class Tiki_Profile_InstallHandler_Transition extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$defaults = array(
			'preserve' => 'n',
			'guards' => array(),
		);

		$data = array_merge(
			$defaults,
			$this->obj->getData()
		);

		foreach( $data['guards'] as & $guard ) {
			if( is_string( $guard[2] ) ) {
				$guard[2] = reset( Horde_Yaml::load( "- " . $guard[2] ) );
			}
		}

		$data = Tiki_Profile::convertYesNo( $data );

		return $this->data = $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if( ! isset( $data['type'], $data['name'], $data['from'], $data['to'] ) )
			return false;
		if( ! is_array( $data['guards'] ) )
			return false;

		return true;
	}

	function _install()
	{
		require_once 'lib/transitionlib.php';

		$data = $this->getData();

		$this->replaceReferences( $data );

		$transitionlib = new TransitionLib( $data['type'] );
		$id = $transitionlib->addTransition( $data['from'], $data['to'], $data['name'], $data['preserve'] == 'y', $data['guards'] );

		return $id;
	}
} // }}}

class Tiki_Profile_InstallHandler_ExtWiki extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		$data = $this->obj->getData();

		return $data;
	}

	function canInstall()
	{
		$data = $this->getData();
		if( ! isset( $data['name'], $data['url'] ) )
			return false;

		return true;
	}

	function _install()
	{
		global $adminlib; require_once 'lib/admin/adminlib.php';

		$data = $this->getData();

		$this->replaceReferences( $data );

		$adminlib->replace_extwiki( null, $data['url'], $data['name'] );

		return $data['name'];
	}
} // }}}

//THIS HANDLER STILL DON'T WORK PROPERLY. USE WITH CAUTION. 
class Tiki_Profile_InstallHandler_Calendar extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;

		$data = $this->obj->getData();
		$this->replaceReferences($data);

		if (!empty($data['name'])) {
			global $calendarlib; include_once('lib/calendar/calendarlib.php');
			$data['calendarId'] = $calendarlib->get_calendarId_from_name($data['name']);
		}

		return $this->data = $data;
	}
	
	function canInstall()
	{
		$data = $this->getData();
		
		if (!isset($data['name'])) {
			return false;
		}
		return $this->convertMode($data);
	}
	private function convertMode($data)
	{
		if (!isset($data['mode'])) {
			return true; // will duplicate if already exists
		}
		switch ($data['mode']) {
		case 'update':
			if (empty($data['calendarId'])) {
				throw new Exception(tra('Calendar does not exist').' '.$data['name']);
			}
		case 'create':
			if (!empty($data['calendarId'])) {
				throw new Exception(tra('Calendar already exists').' '.$data['name']);
			}
		}
		return true;
	}
	
	function _install()
	{
		if ($this->canInstall())
		{
			global $calendarlib; if (!$calendarlib) require_once 'lib/calendar/calendarlib.php';
			
			$calendar = $this->getData();
			
			global $user;
			$customflags = isset($calendar['customflags']) ? $calendar['customflags']  : array();
			$options = isset($calendar['options']) ? $calendar['options']  : array();
			$id = $calendarlib->set_calendar($calendar['calendarId'], $user, $calendar['name'], $calendar['description'], $customflags,$options);
			return $id;
		}
	}
} // }}}

/**
 * Adding users with this handler is not recommended for production servers
 * as it may be insecure. Use for generating examples and test data only.
 * 
 * Assigning existing users to groups should be fine though...
 * 
 * Example (Tiki 6+):
 * =====================================
 
 objects:
# assign existing user to existing group
 -
  type: user
  data: 
    name: testit
    groups: [ Test Group ]

# add new user with email and initial password defaulting to username
# doesn't need to change password on first login (defaults to y)
# finally assigned to Test Group
 -
  type: user 
  data: 
    name: tester
    email: tester@example.com
    change: n
    groups: [ Test Group ]

 * =====================================
 * 
 */
class Tiki_Profile_InstallHandler_User extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;
		$data = $this->obj->getData();
		$this->replaceReferences($data);

		return $this->data = $data;
	}
	
	function canInstall()
	{
		$data = $this->getData();
		
		if (isset($data)) return true;
		else return false;
	}
	
	function _install()
	{
		if ($this->canInstall())
		{
			global $userlib; if (!$userlib) require_once 'lib/userslib.php';

			$user = $this->getData();
				
			if (!$userlib->user_exists($user['name'])) {
				$pass = isset($user['pass']) ? $user['pass'] : $user['name'];
				$email = isset($user['email']) ? $user['email'] : '';
				if (isset($user['change']) && $user['change'] === false) {
					$userlib->add_user($user['name'], $pass, $email);
				} else {
					$userlib->add_user($user['name'], $pass, $email, $pass, true);
				}
			}

			if (isset($user['groups'])) {
				foreach ($user['groups'] as $group) {
					$userlib->assign_user_to_group($user['name'], $group);
				}
			}
				
			return $userlib->get_user_id($user['name']);
		}
	}
} // }}}


interface Tiki_Profile_Converter
{
	function convert( $value );
}

class Tiki_Profile_DateConverter // {{{
{
	function convert( $value )
	{
		if( is_int( $value ) )
			return $value;

		$time = strtotime( $value );
		if( $time !== false )
			return $time;
	}
} // }}}

class Tiki_Profile_ValueMapConverter // {{{
{
	private $map;
	private $implode;

	function __construct( $map, $implodeArray = false )
	{
		$this->map = $map;
	}

	function convert( $value )
	{
		if( is_array( $value ) )
		{
			foreach( $value as &$v )
				if( isset( $this->map[$v] ) )
					$v = $this->map[$v];
			
			if( $this->implode )
				return implode( '', $value );
			else
				return $value;
		}
		else
		{
			if( isset( $this->map[$value] ) )
				return $this->map[$value];
			else
				return $value;
		}
	}
	function reverse( $key)
	{
		$tab = array_flip($this->map);
		return $tab[$key];
	}

} // }}}

class Tiki_Profile_InstallHandler_Sheet extends Tiki_Profile_InstallHandler // {{{
{
	function getData()
	{
		if( $this->data )
			return $this->data;
		$data = $this->obj->getData();
		$this->replaceReferences($data);

		return $this->data = $data;
	}
	
	function canInstall()
	{
		$data = $this->getData();
		
		if (isset($data)) return true;
		else return false;
	}
	
	function _install()
	{
		if ($this->canInstall())
		{
			global $user;
			require_once ('lib/sheet/grid.php');
			
			//here we convert the array to that of what is acceptable to the sheet lib
			$parentSheetId;
			$sheets = array();
			$nbsheets = count($this->data);	
			for ($sheetI = 0; $sheetI < $nbsheets; $sheetI++)
			{
				$title = $this->data[$sheetI]['title'];
				$title = ($title ? $title : "Untitled - From Profile Import");
				$nbdatasheetI = count($this->data[$sheetI]);	
				for ($r = 0; $r < $nbdatasheetI; $r++)
				{
					$nbdatasheetIr = count($this->data[$sheetI][$r]);
					for ($c = 0; $c < $nbdatasheetIr; $c++)
					{
						$value = "";
						$formula = "";
						$rawValue = $this->data[$sheetI][$r][$c];
						 
						if (substr($rawValue, 0, 1) == "=") {
							$formula = $rawValue;
						} else {
							$value = $rawValue;
						}
						
						$ri = 'r'.$r;
						$ci = 'c'.$c;
						
						$sheets[$sheetI]->data->$ri->$ci->formula = $formula;
						$sheets[$sheetI]->data->$ri->$ci->value = $value;
						
						$sheets[$sheetI]->data->$ri->$ci->width = 1;
						$sheets[$sheetI]->data->$ri->$ci->height = 1;
					}
				}
				
				$sheets[$sheetI]->metadata->rows = count($this->data[$sheetI]);
				$sheets[$sheetI]->metadata->columns = count($this->data[$sheetI][0]);
				$id = $sheetlib->replace_sheet(0, $title, "", $user, $parentSheetId);
				$parentSheetId = ($parentSheetId ? $parentSheetId : $id);
				
				$grid = new TikiSheet($id);
				$handler = new TikiSheetHTMLTableHandler($sheets[$sheetI]);
				$res = $grid->import($handler);
				$handler = new TikiSheetDatabaseHandler($id);
				$grid->export($handler);
			}
			
			return $parentSheetId;
		}
	}
} // }}}
