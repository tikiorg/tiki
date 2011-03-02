<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

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
