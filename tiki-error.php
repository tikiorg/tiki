<?php

// $Header: /cvsroot/tikiwiki/tiki/tiki-error.php,v 1.15 2005-08-25 20:50:04 michael_davey Exp $

// Copyright (c) 2002-2005, Luis Argerich, Garland Foster, Eduardo Polidor, et. al.
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.

// Initialization
require_once ('tiki-setup.php');

ask_ticket('error');

$page='';
$type='';

if (isset( $feature_usability ) && $feature_usability == 'y' ) {
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
    } else  {
      $_REQUEST["error"] = tra('unknown error');
    }
  }
} else {
  if (!isset($_REQUEST["error"])) $_REQUEST["error"] = tra('unknown error');
}

// This can be useful for putting custom code inside error page.
// ie: in error.tpl {$referer) will hold "login" if user came from tiki-login.php
// if this gets useful we can integrate with tickets, this was just a hack to show to LarsKl
// during a chat.
if (!empty($_SERVER['HTTP_REFERER']) && preg_match('/tiki-([a-z_]+?)\.php/', $_SERVER['HTTP_REFERER'], $m)) {
    $smarty->assign('referer',$m[1]);
}

// Display the template
$access->display_error($page, $_REQUEST["error"], $type);
?>
