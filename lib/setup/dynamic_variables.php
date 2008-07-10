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

// Check for an update of dynamic vars

if ( isset($tiki_p_edit_dynvar) && $tiki_p_edit_dynvar == 'y' ) {
    if ( isset($_REQUEST['_dyn_update']) ) {
        foreach ( $_REQUEST as $name => $value ) {
            if ( substr($name,0,4) == 'dyn_' and $name != '_dyn_update' ) {
                $tikilib->update_dynamic_variable(substr($name,4), $_REQUEST[$name]);
            }
        }
    }
}
