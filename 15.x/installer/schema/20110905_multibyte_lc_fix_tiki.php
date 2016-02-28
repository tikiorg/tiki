<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * @param $installer
 */
function upgrade_20110905_multibyte_lc_fix_tiki($installer)
{
	if (function_exists('mb_strtolower')) {
		$pages = $installer->table('tiki_pages')->fetchColumn('pageName', array());
		$objectperms = $installer->table('users_objectpermissions');

		foreach ($pages as $originalName) {
			$lowercase = strtolower($originalName);
			$mblowercase = mb_strtolower($originalName, 'UTF-8');

			if ($lowercase != $mblowercase) {
				$old = md5('wiki page' . $lowercase);
				$new = md5('wiki page' . $mblowercase);

				$objectperms->updateMultiple(
					array(
						'objectId' => $new,
					),
					array(
						'objectType' => 'wiki page',
						'objectId' => $old,
					)
				);
			}
		}
	}
}

