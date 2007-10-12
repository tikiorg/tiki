<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-error.php,v 1.19 2007-10-12 07:55:27 nyloth Exp $

// Copyright (c) 2002-2007, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

ask_ticket('error');

$page='';
$type='';

if (isset( $prefs['feature_usability'] ) && $prefs['feature_usability'] == 'y' ) {
  if (!isset($_REQUEST["error"])) {
/*
    if ( !empty($_REQUEST['page'])) {
      $page = $_REQUEST['page'];
      $_REQUEST["error"] = tra("Page") . " '".$page."' ".tra("cannot be found");
      $type="404";
    } else
*/
    if ( ($_SERVER["REQUEST_URI"] && !(preg_match('/tiki-error.php/',$_SERVER["REQUEST_URI"])) ) ) {
      $page = $_SERVER["REQUEST_URI"];
      $page = substr($page,strrpos($page,'/')+1);
      $_REQUEST["error"] = tra("Page") . " '".$page."' ".tra("cannot be found");
      $type="404";
    }
  }
}

if (!isset($_REQUEST["error"])) {
  if (isset($_REQUEST["msg"])) {
    $_REQUEST["error"] = $_REQUEST["msg"];
  } else {
    $_REQUEST["error"] = tra('unknown error');
  }
}

// This can be useful for putting custom code inside error page.
// ie: in error.tpl {$referer) will hold "login" if user came from tiki-login.php
// if this gets useful we can integrate with tickets, this was just a hack to show to LarsKl
// during a chat.
if (!empty($_SERVER['HTTP_REFERER']) && preg_match('/tiki-([a-z_]+?)\.php/', $_SERVER['HTTP_REFERER'], $m)) {
    $smarty->assign('referer',$m[1]);
}

// disallow robots to index page:
$smarty->assign('metatag_robots', 'NOINDEX, NOFOLLOW');

// Display the template
$access->display_error($page, $_REQUEST["error"], $type);

?>
