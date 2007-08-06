<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-mypage_ajax.php,v 1.1 2007-08-06 13:42:59 niclone Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

require_once ('tiki-setup.php');
require_once ('lib/mypage/mypagelib.php');
require_once ('lib/ajax/ajaxlib.php');

if (0) {
    header("Content-Type: text/xml; charset=utf-8");
    header("Pragma: no-cache");
    echo '<?xml version="1.0" encoding="utf-8" ?><xjx></xjx>';
    exit(0);
}

if (0) {
        $jax=xajaxResponseManager::getInstance();
        $objResponse = new xajaxResponse();
        //$objResponse->addAlert($outp);
        $jax->append($objResponse);
        $jax->send();
}


function mypage_win_setrect($id_mypage, $id_mypagewin, $rect) {
    $objResponse = new xajaxResponse();

    $mypage=new MyPage((int)$id_mypage);
    $mywin=$mypage->getWindow((int)$id_mypagewin);

    $mywin->setRect($rect['left'], $rect['top'], $rect['width'], $rect['height']);
    $mywin->commit();

    return $objResponse;
}

function mypage_win_destroy($id_mypage, $id_mypagewin) {
    $objResponse = new xajaxResponse();

    $mypage=new MyPage((int)$id_mypage);
    $mypage->destroyWindow((int)$id_mypagewin);

    return $objResponse;
}

function mypage_win_create($id_mypage, $contenttype, $title, $content) {
    $objResponse = new xajaxResponse();

    $mypage=new MyPage((int)$id_mypage);
    $mywin=$mypage->newWindow();

    $mywin->setTitle($title);
    $mywin->setContentType($contenttype);
    $mywin->setContent($content);

    $mywin->commit();

    $objResponse->addScript($mywin->getJSCode());

    return $objResponse;
}

function mypage_ajax_init() {
    global $ajaxlib;

    //$ajaxlib->debugOn();
    $ajaxlib->registerFunction("mypage_win_setrect");
    $ajaxlib->registerFunction("mypage_win_destroy");
    $ajaxlib->registerFunction("mypage_win_create");
    $ajaxlib->processRequests();
}




mypage_ajax_init();


?>