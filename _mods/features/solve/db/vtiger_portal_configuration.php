<?php
/* @version $Id: vtiger_portal_configuration.php,v 1.1 2005-09-21 21:18:45 michael_davey Exp $ */

/*********************************************************************************
 ********************************************************************************/

$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

class VtigerPortalConfiguration extends TikiDBTable {
    var $id=null;
    var $name=null;
    var $value=null;
    var $module=null;
    var $meta=null;
    
    function VtigerPortalConfiguration() {
        global $database;
        $this->TikiDBTable('vtiger_portal_configuration', 'id', $database);
    }

   function setQuery($query) {
        $this->_db->setQuery($query);
    }

    function query() {
        return $this->_db->query();
    }
}

?>
