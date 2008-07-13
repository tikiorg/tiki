<?php

// Page access controller library

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("Location: ../index.php");
  die;
}


class TikiAccessLib extends TikiLib {

    function TikiAccessLib() {
	    global $dbTiki;
	    $this->TikiLib($dbTiki);
    }

    // check that the user is admin or has admin permissions
    function check_admin($user,$feature_name="") {
        global $smarty, $tiki_p_admin, $prefs;
        require_once ('tiki-setup.php');
        // first check that user is logged in
        $this->check_user($user);
        if (($user != 'admin') && ($tiki_p_admin != 'y')) {
        	if ($prefs['feature_redirect_on_error'] != 'y') {
            $msg = tra("You do not have permission to use this feature");
            if ($feature_name) {
                $msg = $msg . ": " . $feature_name;
            }
            $smarty->assign('msg', $msg);
            $smarty->display("error.tpl");
            die;
        	} else {
        		$this->redirect(''.$prefs['tikiIndex']);
        		die;
        	}
        }
    }

    function check_user($user) {
        global $smarty, $prefs;
        require_once ('tiki-setup.php');
        if (!$user) {
        	if ($prefs['feature_redirect_on_error'] != 'y') {
            $title = tra("You are not logged in");
            if (isset( $prefs['feature_usability'] ) && $prefs['feature_usability'] == 'y' ) {
                $this->display_error('',$title,'402');
            } else {
                $smarty->assign('msg', $title);
                $smarty->display("error.tpl");
            }
            die;
        	} else {
        		$this->redirect(''.$prefs['tikiIndex']);
        		die;
        	}
        }
    }

    function check_page($user='y', $features=array(), $permissions=array(), $permission_name='') {
        global $smarty;
        require_once ('tiki-setup.php');
        if( $features ) {
            $this->check_feature($features);
        }
        $this->check_user($user);
        if( $permissions ) {
            $this->check_permission($permissions, $permission_name);
        }
    }

    function check_feature($features, $feature_name="") {
        global $smarty, $prefs;
        require_once ('tiki-setup.php');
	if ( ! is_array($features) ) { $features = array($features); }
        foreach ($features as $feature) {
            if ($prefs[$feature] != 'y') {
            	if ($prefs['feature_redirect_on_error'] != 'y') {
                if ($feature_name == '') { $feature_name = $feature; }
                $smarty->assign('msg', tra("This feature is disabled").": ". $feature_name);
                $smarty->display("error.tpl");
                die;
            	} else {
            		$this->redirect(''.$prefs['tikiIndex']);
            		die;
            	}
            }
        }
    }

    function check_permission($permissions, $permission_name='') {
        global $smarty;
        require_once ('tiki-setup.php');
        if ( ! is_array($permissions) ) { $permissions = array($permissions); }
        foreach ($permissions as $permission) {
            global $$permission;
            if ($$permission != 'y') {
                if (!$permission_name) { $permission_name = $permission; }
                $smarty->assign('msg', tra("You do not have permission to use this feature").": ". $permission_name);
                $smarty->display("error.tpl");
                die;
            }
        }
    }

    // check permission, where the permission is normally unset
    function check_permission_unset($permissions, $permission_name) {
        global $smarty;
        require_once ('tiki-setup.php');
        foreach ($permissions as $permission) {
            global $$permission;
            if ((isset($$permission) && $$permission == 'n')) {
                $smarty->assign('msg', tra("Permission denied").": ". $permission_name);
                $smarty->display("error.tpl");
                die;
            }
        }
    }

    // check page exists
   function check_page_exists($page) {
        global $smarty;                 
        require_once ('tiki-setup.php');
        if (!$tikilib->page_exists($page)) {
            $smarty->assign('msg', tra("Page cannot be found"));

            $smarty->display("error.tpl");
            die;
        }
    }

    /**
     *  Check whether script was called directly or included
     *  err and die if called directly
     *  Typical usage: $access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));
     * 
     *  if feature_redirect_on_error is active, then just goto the Tiki HomePage as configured
     *  in Admin->General. -- Damian
     * 
     */
    function check_script($scriptname, $page) {
      global $smarty, $prefs;
      if (basename($scriptname) == $page) {
      	if ($prefs['feature_redirect_on_error'] == 'y') {
      		$this->redirect(''.$prefs['tikiIndex']);
      		die;
      	} else {
        if( !isset($prefs['feature_usability']) || $prefs['feature_usability'] == 'n' ) {
          $msg = tra("This script cannot be called directly");                
          $this->display_error($page, $msg);
        } else { 
          $msg = tra("Page") . " '".$page."' ".tra("cannot be found");
          $this->display_error($page, $msg, "404");
        }
      	}
      }
    }

    // you must call ask_ticket('error') before calling this
    function display_error($page, $errortitle="", $errortype="") {
        global $smarty, $wikilib;
        require_once ('tiki-setup.php');
        include_once('lib/wiki/wikilib.php');
        if ( !isset($errortitle) ) {
            $errortitle = tra('unknown error');
        }
        // Display the template
        $smarty->assign('msg', $errortitle);
        if ( isset($errortype) && $errortype == "404" ) {
            $likepages = $wikilib->get_like_pages($page);
            /* if we have exactly one match, redirect to it */
            if(count($likepages) == 1 ) {
                $this->redirect("tiki-index.php?page=$likepages[0]");
                die;
            }
            $smarty->assign_by_ref('likepages', $likepages);
            header ("Status: 404 Not Found"); /* PHP3 */
            header ("HTTP/1.0 404 Not Found"); /* PHP4 */
            $smarty->assign('errortitle', $errortitle. " (404)");
            $smarty->assign('page', $page);
            $smarty->assign('errortype', $errortype);
        } else {
            $smarty->assign('errortype', $errortype);
        }
        $smarty->display("error.tpl");
        die;
    }

    function get_home_page($page='') {
	global $prefs, $tikilib, $use_best_language;

        if (!isset($page) || $page == '') {
            if ($prefs['useGroupHome'] == 'y') {
                $groupHome = $userlib->get_user_default_homepage($user);
                if ($groupHome) {
                        $page = $groupHome;
                } else {
                        $page = $prefs['wikiHomePage'];
                }
            } else {
                $page = $prefs['wikiHomePage'];
            }
            if(!$tikilib->page_exists($prefs['wikiHomePage'])) {
                $tikilib->create_page($prefs['wikiHomePage'],0,'',$this->now,'Tiki initialization');
            }
            if ($prefs['feature_best_language'] == 'y') {
                $use_best_language = true;
            }
        }
        return $page;
    }

    /**
     * Utility function redirect the browser location to another url
     *
     * @param string The target web address
     * @param string an optional message to display
     */
    function redirect( $url='', $msg='' ) {
        global $prefs;
        if( $url == '' ) $url = $prefs['tikiIndex'];
        if (trim( $msg )) {
                if (strpos( $url, '?' )) {
                        $url .= '&msg=' . urlencode( $msg );
                } else {
                        $url .= '?msg=' . urlencode( $msg );
                }
        }

        if (headers_sent()) {
                echo "<script>document.location.href='$url';</script>\n";
        } else {
                @ob_end_clean(); // clear output buffer
                header( "Location: $url" );
        }
        exit();
    }

}

$access = new TikiAccessLib($dbTiki);

?>
