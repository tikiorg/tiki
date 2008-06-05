<?php

class Tiki_Profile_Installer
{
	private $installed = array();
	private $handlers = array(
		'group' => 'Tiki_Profile_InstallHandler_Group',
		'tracker' => 'Tiki_Profile_InstallHandler_Tracker',
		'tracker_item' => 'Tiki_Profile_InstallHandler_TrackerItem',
	);

	function __construct()
	{
		global $tikilib;

		$result = $tikilib->query( "SELECT DISTINCT domain, profile FROM tiki_profile_symbols" );
		while( $row = $result->fetchRow() )
			$this->installed[sprintf( "http://%s/tiki-export_wiki_pages.php?page=%s", $row['domain'], urlencode($row['profile']) )] = true;
	}

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
		
		echo "Installing {$profile->domain}:{$profile->profile}\n";
		$this->installed[$profile->url] = $profile;

		foreach( $profile->getPreferences() as $pref => $value )
			switch( $pref )
			{
			case 'enable':
				$value = (array) $value;
				foreach( $value as $name )
					$tikilib->set_preference( $name, 'y' );
				break;
			case 'disable':
				$value = (array) $value;
				foreach( $value as $name )
					$tikilib->set_preference( $name, 'n' );
				break;
			default:
				$tikilib->set_preference( $pref, $value );
				break;
			}

		foreach( $profile->getObjects() as $object )
			$this->getInstallHandler( $object )->install();
	} // }}}
}

abstract class Tiki_Profile_InstallHandler // {{{
{
	private $obj;

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

class Tiki_Profile_InstallHandler_TrackerItem extends Tiki_Profile_InstallHandler // {{{
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

?>
