<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-admin_banners.php,v 1.13.2.1 2007-11-04 22:08:04 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

/*
if($user != 'admin') {
  if($tiki_p_admin != 'y') {
	$smarty->assign('msg',tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die;
  }
}
*/

// Display the template
$smarty->assign('mid', 'tiki-edit_banner.tpl');
$smarty->display("tiki.tpl");

?>