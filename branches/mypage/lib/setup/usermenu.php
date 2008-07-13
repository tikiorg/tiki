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

// User menus
if ( $user ) {
    if ( ! isset($_SESSION['usermenu']) ) {
        include_once('lib/usermenu/usermenulib.php');
        $user_menus = $usermenulib->list_usermenus($user, 0, -1, 'position_asc', '');
        $smarty->assign('usr_user_menus', $user_menus['data']);
        $_SESSION['usermenu'] = $user_menus['data'];
    } else {
        $user_menus = $_SESSION['usermenu'];
        $smarty->assign('usr_user_menus', $user_menus);
    }
}
