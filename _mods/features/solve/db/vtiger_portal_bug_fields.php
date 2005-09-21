<?php
/* @version $Id: vtiger_portal_bug_fields.php,v 1.1 2005-09-21 21:18:45 michael_davey Exp $ */

/*********************************************************************************
 ********************************************************************************/

$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));
 
class VtigerBugFields extends TikiDBTable {
    var $id=null;
    var $field=null;
    var $type=null;
    var $name=null;
    var $show=null;
    var $size=null;
    var $canedit=null;
    var $inlist=null;
    var $default=null;
    var $searchable=null;
    var $parameters=null;
    
    function VtigerBugFields() {
        global $database;
        $this->TikiDBTable('vtiger_portal_bug_fields', 'id', $database);
    }

}

?>
