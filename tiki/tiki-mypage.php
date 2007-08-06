<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-mypage.php,v 1.1 2007-08-06 13:13:37 niclone Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/mypage/mypagelib.php');
require_once ('lib/ajax/ajaxlib.php');

function mypage_ajax_init() {
    global $ajaxlib;

    //$ajaxlib->debugOn();
    $ajaxlib->setRequestURI("tiki-mypage_ajax.php");
    $ajaxlib->registerFunction("mypage_win_setrect");
    $ajaxlib->registerFunction("mypage_win_destroy");
    $ajaxlib->registerFunction("mypage_win_create");
    $ajaxlib->processRequests();
}

function mypage_init() {
    global $smarty;
    global $headerlib;

    $headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.css");
    $headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.aero.css");
    $headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.alphacube.css");
    $headerlib->add_cssfile("lib/mootools/extensions/windoo/themes/windoo.aqua.css");


    $id_mypage=1;
    $mypage=new MyPage($id_mypage);
    $smarty->assign('mypagejswindows', $mypage->getJSCode());
    $smarty->assign('id_mypage', $id_mypage);

    mypage_ajax_init();

    $smarty->assign("mid", "tiki-mypage.tpl");
    $smarty->display("tiki.tpl");

}


mypage_init();

?>