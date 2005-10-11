<?php
/**
 * @version $Id: configuration.php,v 1.3 2005-10-11 23:21:24 michael_davey Exp $
 * @package Solve
 * @copyright (C) 2005 the Tiki community
 * @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
 */

$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

require_once( 'lib/solve/db/vtiger_portal_configuration.php' );

class SolveConfiguration {
    /**
     * name of the SOAP client application
     * @access private
     */
    var $_appname = 'TikiWiki';

    /**
     * the sort criteria
     * @access private
     */
    var $_sortBy = array();

    /**
     * the db table
     * @access private
     */
    var $dbtable;

    var $option = null;  // HTTP request option variable

    /**
     * TikiWiki global database variable
     * @access private
     */
    var $_dbConfig = null;

    var $presentation = null; // TikiWiki presentation layer
    var $appConfig = array(); // Map of configuration directives for portal to vtiger
    var $availableFields = array();  // Fields available to Contacts and Leads in the vtiger database
                        
    // class constructor
    function SolveConfiguration($appname, $presentation=false, $option=_MYNAMEIS, $task='', $sortBy='number,desc', $dbtable=null ) {
        global $tmpDir;

        if( $dbtable ) {
            $this->dbtable = $dbtable;
        }

        $this->_dbConfig = new VtigerPortalConfiguration();

        $this->_getConfig('GLOBAL');

        $this->appname = $appname;
        $this->presentation = $presentation;
        $this->option = $option;
        $this->task = $task;

        // setup the sort.  We'll use this on any page that sorts.  The order_by var in
        // the querystring should look like:
        //       number,desc
        //       priority,asc
        //       etc.
        if(isset($sortBy)) {
            $this->_sortBy = array();
            $tmpGet = urldecode($sortBy);
            list($sortColumn,$sortOrder) = explode(',',$tmpGet);
            $this->_sortBy[$sortColumn] = $sortOrder;
        }


        $this->appConfig['file_storage'] = $tmpDir;
    }
    
    function _getAppConfig($scope) {
        if( array_key_exists($scope,$this->appConfig) ) {
            return $this->appConfig[$scope];
        } else {
            $this->_getConfig($scope);
            if( array_key_exists($scope,$this->appConfig) ) {
                return $this->appConfig[$scope];
            } else {
                 return array();
            }
        }
    }
    
    function _getConfig($scope = 'GLOBAL') {
        $this->appConfig[$scope] = $this->_dbConfig->getConfig($scope);
    }
    
    /**
     *  Returns the SOAP clients' application name
     *  @return string "TikiWiki"
     */
    function getAppName() {
        return $this->_appname;
    }

    /**
     *  Returns the URL of the CRM servers' SOAP interface
     */
    function getServer() {
        $tmpConfig = $this->_getAppConfig('GLOBAL');
        return $tmpConfig['server'];
    }

    /**
     *  Returns the sorting criteria
     */
    function getSortBy() {
        return $this->_sortBy;
    }

    function getAuth() {
        return array('user_name'=>$this->appConfig['GLOBAL']['username'],
                     'password'=>$this->appConfig['GLOBAL']['password'],
                     'version'=>".01");
    }
    
    // get array of all directive names--*not* values
    function getAllDirectives() {
        $tmpList = array();
        foreach( $this->appConfig as $scope=>$var ) {
            $tmpList += array_keys($var);
        }
/*
        $this->setQuery('SELECT * FROM vtiger_portal_configuration');
        $results = $this->query();
        
        $retrievedConfig = array();
        
        while ($result = $results->fetchRow() ) {
            list( $scope, $value, $name ) = array($result['component'],$result['value'],$result['name']);
            
            $retrievedConfig[$scope][$result['name']]['name'] = $name;
            $retrievedConfig[$scope][$result['name']]['value'] = $value;
        }
        return $retrievedConfig;
*/
        return $tmpList;
    }
    
    function getBrokeMessage() {
        $msg = '
        <p><strong>The webpage you are trying to access is currently
           unavailable.  Please try again later.</strong></p>';
           
        return $msg;
    }
    
    // Just check to see if there's a soap config.  All others are setup, the soap config
    // is stuff we need from the user.
    function checkConfig() {
        return $this->_dbConfig->checkConfig();
    }
    
    /**
     *  run some sanity tests to make sure we can connect to vtiger
     */
    // Diagnostics and various checks to make sure vtiger is running
    function checkSoap($config) {
        // XXX @TODO: use the simple soap test here, too
        
        $vtigerSanityClient = new VtigerLead($config);

        if( $vtigerSanityClient->err == true) {
            return false;
        }
        
        return true;
    }
            
}
?>
