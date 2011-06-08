<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

if ( $prefs['feature_babelfish'] == 'y' ) {
	require_once('lib/Babelfish.php');
	$smarty->assign('babelfish_links', Babelfish::links($prefs['language']));
}

if ( $prefs['feature_babelfish_logo'] == 'y' ) {
	require_once('lib/Babelfish.php');
	$smarty->assign('babelfish_logo', Babelfish::logo($prefs['language']));
}

