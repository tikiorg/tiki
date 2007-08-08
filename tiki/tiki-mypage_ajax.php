<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-mypage_ajax.php,v 1.4 2007-08-08 13:50:54 niclone Exp $

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
    global $id_users;

    $objResponse = new xajaxResponse();

    $mypage=new MyPage((int)$id_mypage, $id_users);
    $mywin=$mypage->getWindow((int)$id_mypagewin);

    $mywin->setRect($rect['left'], $rect['top'], $rect['width'], $rect['height']);
    $mywin->commit();

    return $objResponse;
}

function mypage_win_destroy($id_mypage, $id_mypagewin) {
    global $id_users;

    $objResponse = new xajaxResponse();

    $mypage=new MyPage((int)$id_mypage, $id_users);
    $mypage->destroyWindow((int)$id_mypagewin);

    return $objResponse;
}

function mypage_win_create($id_mypage, $contenttype, $title, $content) {
    global $id_users;

    $objResponse = new xajaxResponse();

    $mypage=new MyPage((int)$id_mypage, $id_users);
    $mywin=$mypage->newWindow();

    $mywin->setTitle($title);
    $mywin->setContentType($contenttype);
    $mywin->setContent($content);

    $mywin->commit();

    $objResponse->addScript($mywin->getJSCode());

    return $objResponse;
}

function mypage_update($id_mypage, $name, $description, $width, $height) {
    global $id_users;

    $objResponse = new xajaxResponse();

    $mypage=new MyPage((int)$id_mypage, $id_users);
    $mypage->setParam('name', $name);
    $mypage->setParam('description', $description);
    $mypage->setParam('width', (int)$width);
    $mypage->setParam('height', (int)$height);
    $mypage->commit();

    $objResponse->addAssign('mypagespan_name_'.$id_mypage, 'innerHTML', $mypage->getParam('name'));
    $objResponse->addAssign('mypagespan_description_'.$id_mypage, 'innerHTML', $mypage->getParam('description'));
    $objResponse->addAssign('mypagespan_width_'.$id_mypage, 'innerHTML', $mypage->getParam('width'));
    $objResponse->addAssign('mypagespan_height_'.$id_mypage, 'innerHTML', $mypage->getParam('height'));

    return $objResponse;
}

function mypage_create($name, $description, $width, $height) {
    global $id_users;

    $objResponse = new xajaxResponse();

    $mypage=new MyPage(NULL, $id_users);
    $mypage->setParam('name', $name);
    $mypage->setParam('description', $description);
    $mypage->setParam('width', (int)$width);
    $mypage->setParam('height', (int)$height);
    $mypage->commit();

    $objResponse->addScript("window.location.reload()");

    return $objResponse;
}

function mypage_delete($id_mypage) {
    global $id_users;

    $objResponse = new xajaxResponse();

    $mypage=new MyPage((int)$id_mypage, $id_users);
    $mypage->destroy();

    $objResponse->addScript("window.location.reload()");

    return $objResponse;
}

function mypage_fillinfos($id_mypage) {
    global $id_users;

    $objResponse = new xajaxResponse();

    $mypage=new MyPage((int)$id_mypage, $id_users);

    $objResponse->addAssign('mypageedit_id', 'value', $id_mypage);
    $objResponse->addAssign('mypageedit_name', 'value', $mypage->getParam('name'));
    $objResponse->addAssign('mypageedit_description', 'value', $mypage->getParam('description'));
    $objResponse->addAssign('mypageedit_width', 'value', $mypage->getParam('width'));
    $objResponse->addAssign('mypageedit_height', 'value', $mypage->getParam('height'));

    return $objResponse;
}

function mypage_ajax_init() {
    global $ajaxlib;

    //$ajaxlib->debugOn();
    $ajaxlib->registerFunction("mypage_win_setrect");
    $ajaxlib->registerFunction("mypage_win_destroy");
    $ajaxlib->registerFunction("mypage_win_create");
    $ajaxlib->registerFunction("mypage_update");
    $ajaxlib->registerFunction("mypage_create");
    $ajaxlib->registerFunction("mypage_delete");
    $ajaxlib->registerFunction("mypage_fillinfos");
    $ajaxlib->processRequests();
}




mypage_ajax_init();


?>