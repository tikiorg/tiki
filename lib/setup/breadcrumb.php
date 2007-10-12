<?php

// $Header: /cvsroot/tikiwiki/tiki/lib/setup/breadcrumb.php,v 1.2 2007-10-12 07:55:46 nyloth Exp $
// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

// setup initial breadcrumb
$crumbs = array();
$crumbs[] = new Breadcrumb($prefs['siteTitle'], '', $prefs['tikiIndex']);
$smarty->assign_by_ref('crumbs', $crumbs);
