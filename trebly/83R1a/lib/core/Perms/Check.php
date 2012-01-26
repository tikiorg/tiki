<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Check.php 34408 2011-05-13 19:29:31Z lphuberdeau $

interface Perms_Check
{
	function check( Perms_Resolver $resolver, array $context, $name, array $groups );

	function applicableGroups( Perms_Resolver $resolver );
}
