<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_include_directory.php,v 1.13 2007-03-06 19:29:45 sylvieg Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

if (isset($_REQUEST["directory"])) {
	check_ticket('admin-inc-directory');
	simple_set_toggle('directory_validate_urls');
	simple_set_toggle('directory_cool_sites');
	simple_set_toggle('directory_country_flag');
	simple_set_value('directory_columns');
	simple_set_value('directory_links_per_page');
	simple_set_value('directory_open_links');
}
ask_ticket('admin-inc-directory');
?>
