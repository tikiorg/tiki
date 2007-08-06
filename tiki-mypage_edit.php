<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-mypage_edit.php,v 1.1 2007-08-06 19:16:14 niclone Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/mypage/mypagelib.php');
require_once ('lib/ajax/ajaxlib.php');


function mypageedit_populate() {
    global $smarty, $user, $userlib;

    if (strlen($user) <= 0) {
	die();
    } else {
	$id_users=$userlib->get_user_id($user);
    }

    $lpp=25;
    $showpage=isset($_REQUEST['showpage']) ? (int)$_REQUEST['showpage'] : 0;

    $tcount=MyPage::countPages($id_users);
    $pcount=(int)(($tcount-1) / $lpp) + 1;
    $offset=$showpage * $lpp;
    $pages=MyPage::listPages($id_users, $offset, $lpp);

    $pagesnum=array(); for($i=0; $i < $pcount; $i++) $pagesnum[$i]=$i+1;
    $smarty->assign("pagesnum", $pagesnum);
    $smarty->assign("pcount", $pcount);
    $smarty->assign("showpage", $showpage);
    $smarty->assign("mypages", $pages);
}

function mypageedit_init() {
    global $smarty;

    mypageedit_populate();

    $smarty->assign("mid", "tiki-mypage_edit.tpl");
    $smarty->display("tiki.tpl");
}

mypageedit_init();


?>