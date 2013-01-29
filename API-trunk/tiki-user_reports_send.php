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

if (php_sapi_name() != 'cli') {
	$access->check_permission('tiki_p_admin');
}

$access->check_feature('feature_daily_report_watches');

$reportsManager = Reports_Factory::build('Reports_Manager');
$reportsManager->send();