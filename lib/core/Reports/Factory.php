<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @package Tiki
 * @subpackage Reports
 * 
 * Factory for reports classes
 */
class Reports_Factory
{
	static public function build($className)
	{
		$db = TikiDb::get();
		
		switch($className) {
			case 'Reports_Users':
				return new Reports_Users($db, new DateTime, new Reports_Cache($db));
			case 'Reports_Cache':
				return new Reports_Cache($db);
		}		
	} 
}