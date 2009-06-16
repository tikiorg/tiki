<?php

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
	);

	private static $typeMap = array(
		'wiki_page' => 'wiki page',
		'file_gallery' => 'fgal',
	);

	private $userData = false;

	public static function convertType( $type ) // {{{
	{
		if( array_key_exists( $type, self::$typeMap ) )
			return self::$typeMap[$type];
		else
			return $type;
	} // }}}

	public static function convertObject( $type, $id ) // {{{
	{
		global $tikilib;

		if( $type == 'wiki page' && is_numeric( $id ) )
			return $tikilib->get_page_name_from_id( $id );
		else
			return $id;
	} // }}}

	function __construct() // {{{
	{
		global $tikilib;

		$result = $tikilib->query( "SELECT DISTINCT domain, profile FROM tiki_profile_symbols" );
		if ( $result ) while( $row = $result->fetchRow() )
			$this->installed[Tiki_Profile::getProfileKeyFor( $row['domain'], $row['profile'] )] = true;
	} // }}}

	function setUserData( $userData ) // {{{
	{
		$this->userData = $userData;
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

	function install( Tiki_Profile $profile ) // {{{
	{
		global $cachelib;
		require_once 'lib/cache/cachelib.php';

		if( ! $profiles = $this->getInstallOrder( $profile ) )
			return false;

		foreach( $profiles as $p )
			$this->doInstall( $p );
		
		$cachelib->empty_full_cache();
		return true;
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

	private function getInstallHandler( Tiki_Profile_Object $object ) // {{{
	{
		$type = $object->getType();
		if( array_key_exists( $type, $this->handlers ) )
		{
			$class = $this->handlers[$type];
			if( class_exists( $class ) )
				return new $class( $object, $this->userData );
		}
	} // }}}

	private function doInstall( Tiki_Profile $profile ) // {{{
	{
		global $tikilib;
		
		$this->installed[$profile->getProfileKey()] = $profile;

		foreach( $profile->getObjects() as $object )
			$this->getInstallHandler( $object )->install();

		$preferences = $profile->getPreferences();
		$profile->replaceReferences( $preferences, $thus->userData );
		foreach( $preferences as $pref => $value )
			$tikilib->set_preference( $pref, $value );

		$permissions = $profile->getPermissions();
		$profile->replaceReferences( $permissions, $thus->userData );
		foreach( $permissions as $groupName => $info )
			$this->setupGroup( $groupName, $info['general'], $info['permissions'], $info['objects'] );
	} // }}}

	private function setupGroup( $groupName, $info, $permissions, $objects ) // {{{
	{
		global $userlib;

		if( ! $userlib->group_exists( $groupName ) )
			$userlib->add_group( $groupName, $info['description'], $info['home'], $info['user_tracker'], $info['group_tracker'], implode( ':', $info['registration_fields'] ), $info['user_signup'], $info['default_category'], $info['theme'], $info['user_tracker_field'], $info['group_tracker_field'] );

		if( count( $info['include'] ) )
		{
			$userlib->remove_all_inclusions( $groupName );
			
			foreach( $info['include'] as $included )
				$userlib->group_inclusion( $groupName, $included );
		}

		foreach( $permissions as $perm => $v )
			if( $v == 'y' )
				$userlib->assign_permission_to_group( $perm, $groupName );
			else
				$userlib->remove_permission_from_group( $perm, $groupName );

		foreach( $objects as $data )
			foreach( $data['permissions'] as $perm => $v )
			{
				$data['type'] = self::convertType( $data['type'] );
				$data['id'] = Tiki_Profile_Installer::convertObject( $data['type'], $data['id'] );

				if( $v == 'y' )
					$userlib->assign_object_permission( $groupName, $data['id'], $data['type'], $perm );
				else
					$userlib->remove_object_permission( $groupName, $data['id'], $data['type'], $perm );
			}
	} // }}}

	function forget( Tiki_Profile $profile ) // {{{
	{
		$key = $profile->getProfileKey();
		unset($this->installed[$key]);
		$profile->removeSymbols();
	} // }}}
}

abstract class Tiki_Profile_InstallHandler // {{{
{
	protected $obj;
	private $userData;

	function __construct( Tiki_Profile_Object $obj, $userData )
	{
		$this->obj = $obj;
		$this->userData = $userData;
	}

	abstract function canInstall();

	final function install()
	{
		$id = $this->_install();
		if( empty( $id ) )
			die( 'Handler failure: ' . get_class( $this ) . "\n" );

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
	private $data;

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
		$defaults['default_status'] = 'o';
		$defaults['modification_status'] = '';
		$defaults['list_default_status'] = 'o';
		$defaults['sort_default_order'] = 'asc';
		$defaults['sort_default_field'] = '';
		$defaults['restrict_start'] = '';
		$defaults['restrict_end'] = '';
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

		// Check for unknown fields
		$optionMap = $this->getOptionMap();
		$remain = array_diff( array_keys( $data ), array_keys( $optionMap ) );
		if( count( $remain ) )
			return false;

		// Check for mandatory fields
		if( !isset( $data['name'] ) )
			return false;

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

		return $trklib->replace_tracker( 0, $name, $description, $options );
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
				'multimedia' => 'M',
				'auto_increment' => 'q',
				'user_subscription' => 'U',
				'map' => 'G',
				'system' => 's',
				'computed' => 'C',
				'preference' => 'p',
				'attachment' => 'A',
				'page' => 'k',
			) ), // }}}
			'visible' => new Tiki_Profile_ValueMapConverter( array(
				'public' => 'n',
				'admin_only' => 'y',
				'admin_editable' => 'p',
				'creator_editable' => 'c',
			) ),
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
			0,
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
			$data['multilingual'] );
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

	private $mode = 'create_or_update';
	private $exists;

	function fetchData()
	{
		if( $this->name )
			return;

		$data = $this->obj->getData();

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
	}

	function canInstall()
	{
		$this->fetchData();
		if( empty( $this->name ) || empty( $this->content ) )
			return false;

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
			$this->mode = $this->exists ? 'update' : 'create';
			break;
		case 'create_or_append':
			$this->mode = $this->exists ? 'append' : 'create';
			break;
		default:
			throw new Exception( "Invalid mode '{$this->mode}' for wiki handler." );
		}

		return true;
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

		if( strpos( $this->content, 'wikidirect:' ) === 0 ) {
			$pageName = substr( $this->content, strlen('wikidirect:') );
			$this->content = $this->obj->getPageContent( $pageName );
		}

		if( $this->mode == 'create' ) {
			if( ! $tikilib->create_page( $this->name, 0, $this->content, time(), tra('Created by profile installer'), 'admin', '0.0.0.0', $this->description, $this->lang ) )
				return null;
		} else {
			$info = $tikilib->get_page_info( $this->name, true, true );

			if( ! $this->description )
				$this->description = $info['description'];

			if( ! $this->lang )
				$this->lang = $info['lang'];

			if( $this->mode == 'append' ) {
				$this->content = rtrim( $info['data'] ) . "\n" . trim($this->content) . "\n";
			}

			$tikilib->update_page( $this->name, $this->content, tra('Page updated by profile installer'), 'admin', '0.0.0.0', $this->description, false, $this->lang );
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
		$id = $categlib->add_category( $this->parent, $this->name, $this->description );

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
		global $filegallib;
		if( ! $filegallib ) require_once 'lib/filegals/filegallib.php';

		$input = $this->getData();
		$this->replaceReferences( $input );
		
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
		$data['position'] = ($data['position'] == 'left') ? 'l' : 'r';

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
		$result = $tikilib->query( "SELECT MAX(menuId) FROM tiki_menus" );
		$menuId = reset( $result->fetchRow() );

		foreach( $data['items'] as $item )
			$menulib->replace_menu_option( $menuId, 0, $item['name'], $item['url'], $item['type'], $item['position'], $item['section'], implode( ',', $item['permissions'] ), implode( ',', $item['groups'] ), $item['level'] );


		// Set as side menu if position and order are specified
		if( isset( $data['position'], $data['order'] ) )
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

			$modlib->replace_user_module( "menu_$menuId", $data['name'], $content );
			$modlib->assign_module( 0, "menu_$menuId", null, $column, $data['order'], $data['cache'], 10, $data['groups'], '' );
		}

		return $menuId;

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
			'use_title' => 'y',
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

		$blogId = $bloglib->replace_blog( $data['title'], $data['description'], $data['user'], $data['public'], $data['max_posts'], 0, $data['heading'], $data['use_title'], $data['use_find'], $data['allow_comments'], $data['show_avatar'] );

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

			$result = $tikilib->query( "SELECT user FROM tiki_blogs WHERE blogId = ?", array( $data['blog'] ) );

			if( $row = $result->fetchRow() ) {
				$data['user'] = $row['user'];
			}
		}

		$entryId = $bloglib->blog_post( $data['blog'], $data['content'], $data['user'], $data['title'], '', $data['private'] );

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

			$id = (int) $rsslib->getOne("SELECT MAX(rssId) FROM tiki_rss_modules");
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
			'list_topic_author' => 'n',

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
		require_once 'lib/commentslib.php';
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
			$data['is_flat']
		);

		return $id;
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
} // }}}

?>
