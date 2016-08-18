<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once('lib/webmail/tikimaillib.php');

/**
 * Factory for reports classes
 *
 * @package Tiki
 * @subpackage Reports
 */
class Reports_Factory
{
	static public function build($className, DateTime $dt = null, TikiMail $tm = null, TikiLib $tikilib = null, $tikiPrefs = null)
	{
		$db = TikiDb::get();

		if (is_null($dt)) {
			$dt = new DateTime;
		}

		if (is_null($tm)) {
			$tm = new TikiMail;
		}

		if (is_null($tikilib)) {
			$tikilib = TikiLib::lib('tiki');
		}

		if (is_null($tikiPrefs)) {
			global $prefs;
			$tikiPrefs = $prefs;
		}

		switch($className) {
			case 'Reports_Users':
				return new Reports_Users($db, $dt);
			case 'Reports_Cache':
				return new Reports_Cache($db, $dt);
			case 'Reports_Manager':
				$userlib = TikiLib::lib('user');
				return new Reports_Manager(
					Reports_Factory::build('Reports_Users', $dt, $tm, $tikilib),
					Reports_Factory::build('Reports_Cache', $dt, $tm, $tikilib),
					Reports_Factory::build('Reports_Send', $dt, $tm, $tikilib),
					$userlib
				);
			case 'Reports_Send':
				global $prefs;
				return new Reports_Send($dt, $tm, Reports_Factory::build('Reports_Send_EmailBuilder', $dt, $tm, $tikilib), $tikiPrefs);
			case 'Reports_Send_EmailBuilder':
				return new Reports_Send_EmailBuilder($tikilib, new Reports_Send_EmailBuilder_Factory);
			default:
				throw new Exception("Unknown class $className");
		}
	}
}
