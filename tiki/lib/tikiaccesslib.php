<?php

// Page access controller library

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("Location: ../index.php");
  die;
}


class TikiAccessLib extends TikiLib {

    function TikiAccessLib() {
    }

    function check_user($user) {
        global $smarty;
        require_once ('tiki-setup.php');
        if (!$user) {
            $smarty->assign('msg', tra("You are not logged in"));
            $smarty->display("error.tpl");
            die;
        }
    }

    function check_page($user, $feature, $feature_name, $permission, $permission_name) {
        global $smarty;
        require_once ('tiki-setup.php');
        check_user($user);
        check_feature($feature, $feature_name);
        check_permission($permission, $permission_name);
    }

    function check_feature($feature, $feature_name) {
        global $smarty;
        require_once ('tiki-setup.php');
        if ($feature != 'y') {
            $smarty->assign('msg', tra("This feature is disabled").": ". $feature_name);
            $smarty->display("error.tpl");
            die;
        }
    }

    function check_permission($permission, $permission_name) {
        global $smarty;
        require_once ('tiki-setup.php');
        if ($permission != 'y') {
            $smarty->assign('msg', tra("Permission denied").": ". $permission_name);
            $smarty->display("error.tpl");
            die;
        }
    }
}

?>
