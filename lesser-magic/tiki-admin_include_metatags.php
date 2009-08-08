<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_metatags.php,v 1.8 2007-03-06 19:29:45 sylvieg Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}
if (isset($_REQUEST["metatags"])) {
	check_ticket('admin-inc-metatags');
	simple_set_value('metatag_keywords');
	simple_set_toggle('metatag_threadtitle');
	simple_set_toggle('metatag_imagetitle');
	simple_set_toggle('metatag_freetags');
	simple_set_value('metatag_description');
	simple_set_toggle('metatag_pagedesc');	
	simple_set_value('metatag_author');
	simple_set_value('metatag_geoposition');
	simple_set_value('metatag_georegion');
	simple_set_value('metatag_geoplacename');
	simple_set_value('metatag_robots');
	simple_set_value('metatag_revisitafter');
}
ask_ticket('admin-inc-metatags');
