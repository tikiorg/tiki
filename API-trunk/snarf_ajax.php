<?php
/**
 *
 *
 * @package   Tiki
 * @copyright (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @license   LGPL. See license.txt for more details
 */
// $Id$

require_once ('tiki-setup.php');
include_once('lib/wiki-plugins/wikiplugin_snarf.php');

if ($prefs['wikiplugin_snarf'] != 'y') {
	echo tra('Feature disabled');
	die;
}
echo wikiplugin_snarf('', $_REQUEST);
