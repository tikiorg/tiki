<?php

// $Id: /cvsroot/tikiwiki/tiki/tiki-mypage_types.php,v 1.2.2.1 2008-03-01 17:12:48 lphuberdeau Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');

if($prefs['feature_mypage'] != 'y') {
    $smarty->assign('msg', tra('This feature is disabled').': feature_mypage');
    $smarty->display('error.tpl');
    die;  
}

require_once ('lib/mypage/mypagelib.php');
require_once ('lib/mypage/mypagelib.php');
require_once ('lib/ajax/ajaxlib.php');

if ($tiki_p_admin != 'y') {
	$smarty->assign('msg', tra("You do not have permission to use this feature"));
	$smarty->display("error.tpl");
	die();
}

if (strlen($user) <= 0) {
    $id_users=0;
} else {
    $id_users=$userlib->get_user_id($user);
}

function mypagetypes_populate() {
    global $smarty, $id_users, $userlib;

    $lpp=25;
    $showpage=isset($_REQUEST['showpage']) ? (int)$_REQUEST['showpage'] : 0;

    $tcount=MyPage::countMypageTypes();
    $pcount=(int)(($tcount-1) / $lpp) + 1;
    $offset=$showpage * $lpp;
    $mptypes=MyPage::listMypageTypes($offset, $lpp);

    $pagesnum=array(); for($i=0; $i < $pcount; $i++) $pagesnum[$i]=$i+1;
    $smarty->assign("pagesnum", $pagesnum);
    $smarty->assign("pcount", $pcount);
    $smarty->assign("showpage", $showpage);
    $smarty->assign("mptypes", $mptypes);

    $smarty->assign("components", MyPage::getAvailableComponents());
    $smarty->assign("groups", array("Anonymous", "Registered", "Hack", "c'est ou getGroups??"));
	$mpt_users=$userlib->get_users();
	$smarty->assign("mpt_users", $mpt_users['data']);
}

function mypage_ajax_init() {
    global $ajaxlib, $smarty;

    $smarty->assign("mootools",'y');
    $smarty->assign("mootab",'y');
    $smarty->assign("mootools_windoo",'y');

    //$ajaxlib->debugOn();
    $ajaxlib->setRequestURI("tiki-mypage_ajax.php");
    $ajaxlib->registerFunction("mptype_fillinfos");
    $ajaxlib->registerFunction("mptype_delete");
    $ajaxlib->registerFunction("mptype_create");
    $ajaxlib->registerFunction("mptype_update");
    $ajaxlib->processRequests();
}

function mypagetypes_init() {
    global $smarty, $headerlib;
    
    mypagetypes_populate();
    mypage_ajax_init();
    
    $headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.css");
    $headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.aero.css");
    $headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.alphacube.css");
    $headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.aqua.css");
    $headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.nada.css");
    
    $smarty->assign("mid", "tiki-mypage_types.tpl");
    $smarty->display("tiki.tpl");
}

mypagetypes_init();

/* For the emacs weenies in the crowd.
Local Variables:
   tab-width: 4
   c-basic-offset: 4
End:
*/

?>
