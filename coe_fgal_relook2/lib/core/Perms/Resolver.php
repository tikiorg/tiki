<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

interface Perms_Resolver
{
	function check( $permission, array $groups );

	function from();

	/**
	 * Provide the list of groups affecting the outcome.
	 */
	function applicableGroups();
}

