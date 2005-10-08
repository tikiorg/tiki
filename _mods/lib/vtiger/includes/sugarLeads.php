<?php
/* @version $Id: sugarLeads.php,v 1.3 2005-10-08 10:23:15 michael_davey Exp $ */

$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

class VtigerLead extends sugarCommunication {
    var $module="Leads";
    function VtigerLead(&$confObj, $portal_user='lead') {
        $this->Initialize($confObj, 'lead');
    }
    
    // Gets available Lead fields
    function getAvailableFields() {
        $fields = $this->_getModuleFields();
        
        $this->_showDebug($fields);
        
        return $fields;
    }
    
    // Create a new lead.  $lead should be an array of Contact fields and values.  Not
    // every field need be present, default values will be used for any not present.
    // Returns the id of the new lead
    function createNewLead(&$lead) {
        $tmpLead = $this->bindLead($lead);
        
        return $this->_setEntry($tmpLead);
    }
    
    function bindLead($leadArr) {
        if(empty($this->availableFields))
            $this->_getModuleFields();

        $leadFields = $this->availableFields;
        $tmpLead = array();
        
        foreach($leadFields as $field) {
            if ( array_key_exists($field, $leadArr) ) {
                $tmpLead[$field] = $leadArr[$field];
            } else {
                $tmpLead[$field] = '';
            }
        }
        
        $tmpLead['portal_name'] = $this->portal_user;
        $tmpLead['portal_app'] = $this->sugarConf->getAppName();
        
        //$this->_showDebug($tmpLead);
        
        return $tmpLead;
    }
        
}


?>
