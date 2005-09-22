<?php
/**
 * @version $Id: vtiger_portal_bug_fields.php,v 1.2 2005-09-22 08:35:20 michael_davey Exp $
 * @package Solve
 * @copyright (C) 2005 the Tiki community
 * @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
 */

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
        global $tikilib;
        $this->TikiDBTable('vtiger_portal_bug_fields', 'id', $tikilib);
    }

    function getColumnData(&$bugApp) {
        $columnData = $bugApp->getAvailableFields();

        $results = $this->query('SELECT * FROM ' . $this->_tbl);

        while ($result = $results->fetchRow() ) {
                $columns['selected'][] = $result;
        }

        $columns['data'] = $columnData;

        return $columns;
    }

}

?>
