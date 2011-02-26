<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class TikiWebdav_Auth_Wiki extends TikiWebdav_Auth_Default
{
	public function authorize( $user, $path, $access = self::ACCESS_READ )
	{
		global $tikilib;
		print_debug("authorize " . $user . " " . $path . " " . $access . "\n");

		if ($path === '/') {
			return true;
		}

		$page = substr($path, 1);

		$groups = $tikilib->get_user_groups( $user );
		$perms = Perms::getInstance();
		$perms->setGroups( $groups );
		$perms = $tikilib->page_exists($page) ? Perms::get( array( 'type' => 'wiki page', 'object' => substr($path, 1) ) ) : Perms::get();

		return ( $access === self::ACCESS_READ && $perms->view ) || ( $access === self::ACCESS_WRITE && $perms->edit );
	}

	public function assignLock( $user, $lockToken )
	{
	}

	public function ownsLock( $user, $lockToken )
	{
	}

	public function releaseLock( $user, $lockToken )
	{
	}
}
