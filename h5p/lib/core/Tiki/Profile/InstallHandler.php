<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

abstract class Tiki_Profile_InstallHandler
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
		if ( empty($id) ) {
			die( 'Handler failure: ' . get_class($this) . "\n" );
		}
		
		//Helper to return items that were installed - first used with cart items
		global $record_profile_items_created;
		$record_profile_items_created[] = $id;
		
		$this->obj->setValue($id);
	}

	function replaceReferences( &$data ) // {{{
	{
		$this->obj->replaceReferences($data, $this->userData);
	} // }}}

	abstract function _install();
}
