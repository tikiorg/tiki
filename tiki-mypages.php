<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-mypages.php,v 1.13 2007-08-24 00:09:51 niclone Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/mypage/mypagelib.php');
require_once ('lib/ajax/ajaxlib.php');

if (strlen($user) <= 0) {
	$id_users=0;
} else {
	$id_users=$userlib->get_user_id($user);
}


function mypageedit_populate() {
	global $smarty, $id_users;
	
	$mypage_type=isset($_REQUEST['type']) ? $_REQUEST['type'] : NULL;

	$lpp=25;
	$showpage=isset($_REQUEST['showpage']) ? (int)$_REQUEST['showpage'] : 0;

	$tcount=MyPage::countPages($id_users, $mypage_type);
	$pcount=(int)(($tcount-1) / $lpp) + 1;
	$offset=$showpage * $lpp;
	$pages=MyPage::listPages($id_users, $mypage_type, $offset, $lpp);

	$pagesnum=array(); for($i=0; $i < $pcount; $i++) $pagesnum[$i]=$i+1;
	$smarty->assign("pagesnum", $pagesnum);
	$smarty->assign("pcount", $pcount);
	$smarty->assign("showpage", $showpage);
	$smarty->assign("mypages", $pages);
}

function mypage_ajax_init() {
	global $ajaxlib, $smarty;

	$smarty->assign("mootools",'y');
	$smarty->assign("mootab",'y');
	$smarty->assign("mootools_windoo",'y');

	//$ajaxlib->debugOn();
	$ajaxlib->setRequestURI("tiki-mypage_ajax.php");
	$ajaxlib->registerTemplate("tiki-mypages.tpl");
	$ajaxlib->registerFunction("mypage_update");
	$ajaxlib->registerFunction("mypage_create");
	$ajaxlib->registerFunction("mypage_delete");
	$ajaxlib->registerFunction("mypage_fillinfos");
    $ajaxlib->registerFunction("mypage_isNameFree");
	$ajaxlib->processRequests();
}

function mypageedit_init() {
	global $smarty, $headerlib;

	mypageedit_populate();
	mypage_ajax_init();

	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.css");
	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.aero.css");
	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.alphacube.css");
	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.aqua.css");
	$headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.nada.css");

	$mptypes=MyPage::listMyPageTypes();
	foreach($mptypes as $k => $v) $mptypes_by_id[$v['id']]=$v;
	$smarty->assign("mptypes", $mptypes_by_id);

	$mptypes_js=array();
	foreach($mptypes as $v) {
		$mptypes_js[$v['id']]=$v;
	}
	$mptypes_js=phptojsarray($mptypes_js);
	$smarty->assign('mptypes_js', $mptypes_js);

	$id_types=0;
	if (isset($_REQUEST['type'])) {
		$mptype_name=$_REQUEST['type'];
		foreach($mptypes as $mptype) {
			if ($mptype['name']==$mptype_name) {
				$id_types=$mptype['id'];
				break;
			}
		}
	}

	$smarty->assign('id_types', $id_types);

	$smarty->assign("mid", "tiki-mypages.tpl");
	$smarty->display("tiki.tpl");
}

mypageedit_init();

/* For the emacs weenies in the crowd.
Local Variables:
   tab-width: 4
   c-basic-offset: 4
End:
*/

?>