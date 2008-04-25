<?php

// $Id$
// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for
// details.

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],'tiki-setup.php')!=FALSE) {
  header('location: index.php');
  exit;
}

// Application menu
//TODO: remove from here : this is only used for mod-application_menu and tiki-map

function setDisplayMenu($name) {
	global $smarty;
	if ( getCookie($name, 'menu', isset($_COOKIE['menu']) ? null : 'o') == 'o' ) {
		$smarty->assign('mnu_'.$name, 'display:block;');
		$smarty->assign('icn_'.$name, 'o');
	} else $smarty->assign('mnu_'.$name, 'display:none;');
}

setDisplayMenu('nlmenu');
setDisplayMenu('evmenu');
setDisplayMenu('chartmenu');
setDisplayMenu('mymenu');
setDisplayMenu('wfmenu');
setDisplayMenu('usrmenu');
setDisplayMenu('friendsmenu');
setDisplayMenu('wikimenu');
setDisplayMenu('homeworkmenu');
setDisplayMenu('srvmenu');
setDisplayMenu('trkmenu');
setDisplayMenu('jukeboxmenu');
setDisplayMenu('quizmenu');
setDisplayMenu('formenu');
setDisplayMenu('dirmenu');
setDisplayMenu('admmnu');
setDisplayMenu('faqsmenu');
setDisplayMenu('galmenu');
setDisplayMenu('cmsmenu');
setDisplayMenu('blogmenu');
setDisplayMenu('filegalmenu');
setDisplayMenu('mapsmenu');
setDisplayMenu('layermenu');
setDisplayMenu('shtmenu');
setDisplayMenu('prjmenu');
