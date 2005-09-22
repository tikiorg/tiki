<?php
/**
 * @version $Id: vtiger_portal_configuration.php,v 1.2 2005-09-22 08:35:20 michael_davey Exp $
 * @package Solve
 * @copyright (C) 2005 the Tiki community
 * @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
 */

$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

class VtigerPortalConfiguration extends TikiDBTable {
    var $id=null;
    var $name=null;
    var $value=null;
    var $module=null;
    var $meta=null;
    
    function VtigerPortalConfiguration() {
        global $tikilib;
        $this->TikiDBTable('vtiger_portal_configuration', 'id', $tikilib);
    }

    function getConfig($scope = 'GLOBAL') {
        $appConfig = array();

        $results = $this->query('SELECT * FROM '.$this->_tbl.' WHERE `component` LIKE \''.$scope.'\';');

        while ($result = $results->fetchRow()) {
            $configName = $result['name'];
            $appConfig[$configName] = $result['value'];
        }
        return $appConfig;
    }

    /**
     *  Check to see if we have enough config to connect to the SOAP service.
     */
    function checkConfig() {
        $results = $this->query('SELECT * FROM ' . $this->_tbl);

        $hasserver = false;
        $hasuser = false;
        $haspasswd = false;

        while ($row = $results->fetchRow()) {
            switch($row['name']) {
                case 'username':
                    $hasuser = true;
                    break;
                case 'server':
                    $hasserver = true;
                    break;
                case 'password':
                    $haspasswd = true;
                    break;
            }
        }

        return ($hasuser && $hasserver && $haspasswd) ;
    }

}

?>
