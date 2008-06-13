<?php

class Tiki_Profile_Installer
{
	private $installed = array();
	private $handlers = array(
		'group' => 'Tiki_Profile_InstallHandler_Group',
		'tracker' => 'Tiki_Profile_InstallHandler_Tracker',
		'tracker_field' => 'Tiki_Profile_InstallHandler_TrackerField',
		'tracker_item' => 'Tiki_Profile_InstallHandler_TrackerItem',
	);

	function __construct() // {{{
	{
		global $tikilib;

		$result = $tikilib->query( "SELECT DISTINCT domain, profile FROM tiki_profile_symbols" );
		while( $row = $result->fetchRow() )
			$this->installed[sprintf( "http://%s/tiki-export_wiki_pages.php?page=%s", $row['domain'], urlencode($row['profile']) )] = true;
	} // }}}

	private function getInstallOrder( Tiki_Profile $profile ) // {{{
	{
		// Obtain the list of all required profiles
		$dependencies = $profile->getRequiredProfiles(true);
		$dependencies[$profile->url] = $profile;

		$referenced = array();
		$knownObjects = array();
		foreach( Tiki_Profile_Object::getNamedObjects() as $o )
			$knownObjects[] = Tiki_Profile_Object::serializeNamedObject( $o );

		// Build the list of dependencies for each profile
		$short = array();
		foreach( $dependencies as $url => $profile )
		{
			$short[$url] = array();
			foreach( $profile->getRequiredProfiles() as $u => $p )
				$short[$url][] = $u;

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
			return false;

		// Build the list of packages that need to be installed
		$toSequence = array();
		foreach( $dependencies as $url => $profile )
			if( ! $this->isInstalled( $profile ) )
				$toSequence[] = $url;

		// Order the packages to make sure all dependencies are met
		$toInstall = array();
		$counter = 0;
		while( count( $toSequence ) )
		{
			// If all packages were tested and no order was found, exit
			// Probably means there is a circular dependency
			if( $counter++ > count( $toSequence ) * 2 )
				return false;

			$url = reset( $toSequence );

			// Remove packages that are already scheduled or installed from dependencies
			$short[$url] = array_diff( $short[$url], array_keys( $this->installed ), $toInstall );

			$element = array_shift( $toSequence );
			if( count( $short[$url] ) )
				$toSequence[] = $element;
			else
			{
				$counter = 0;
				$toInstall[] = $element;
			}
		}

		$final = array();
		// Perform the actual install
		foreach( $toInstall as $url )
			$final[] = $dependencies[$url];

		return $final;
	} // }}}

	function install( Tiki_Profile $profile ) // {{{
	{
		global $smarty;

		if( ! $profiles = $this->getInstallOrder( $profile ) )
			return false;

		foreach( $profiles as $p )
			$this->doInstall( $p );
		
		$smarty->clear_compiled_tpl();
		return true;
	} // }}}

	function isInstalled( Tiki_Profile $profile ) // {{{
	{
		return array_key_exists( $profile->url, $this->installed );
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
				return new $class( $object );
		}
	} // }}}

	private function doInstall( Tiki_Profile $profile ) // {{{
	{
		global $tikilib;
		
		$this->installed[$profile->url] = $profile;

		foreach( $profile->getPreferences() as $pref => $value )
			$tikilib->set_preference( $pref, $value );

		foreach( $profile->getObjects() as $object )
			$this->getInstallHandler( $object )->install();
	} // }}}
}

abstract class Tiki_Profile_InstallHandler // {{{
{
	protected $obj;

	function __construct( Tiki_Profile_Object $obj )
	{
		$this->obj = $obj;
	}

	abstract function canInstall();

	final function install()
	{
		$id = $this->_install();
		if( empty( $id ) )
			die( 'Handler failure: ' . get_class( $this ) . "\n" );

		$this->obj->setValue( $id );
	}

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
		$this->obj->replaceReferences( $input );

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
		$this->obj->replaceReferences( $data );

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
		$this->obj->replaceReferences( $data );

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

class Tiki_Profile_InstallHandler_Group extends Tiki_Profile_InstallHandler // {{{
{
	function canInstall()
	{
		// TODO
		return true;
	}

	function _install()
	{
		// TODO
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
