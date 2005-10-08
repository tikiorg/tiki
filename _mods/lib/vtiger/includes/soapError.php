<?php
/* @version $Id: soapError.php,v 1.2 2005-10-08 10:23:15 michael_davey Exp $ */

/*********************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version
 * 1.1.2 ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an "AS IS" basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied.  See the License
 * for the specific language governing rights and limitations under the
 * License.
 *
 * The Original Code is: SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) 2005 SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 ********************************************************************************/

$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));

define('_ALL_ERRORS', 0);
define('_NO_ERROR', 0);
define('_NUSOAP_ERROR', 1);
define('_SOAP_ERROR', 2);
define('_SHOWERRORS', 1);
define('_SHOWDEBUG', 2);
define('_SHOWSOAP', 3);

// soapError is the class that holds error information for the communication layer
// Pass it an instance of a sugarClientProxy and the error part of the results you
//   got from your method call to the sugarClientProxy.  soapError will process that
//   into a useful error message, hopefully.
class soapError {
	var $clientDebugText = '';
	var $soapErrorNumber = 0;
	
    function soapError($client=false, $sugar=false) {
        // Set these to false, default values indicating no errors
        $this->clientError = false;
        $this->soapError = false;
       
        
        
        // If there's an error in the nusoap connection, we catch it here
        if($client) {
            if($client->fault) {
                $this->clientError = true;
            }
            $genericError = $client->getError();
            if($genericError) {
                $this->clientError = true;
                $this->clientErrorText = $genericError;
                $this->clientDebugText = $client->debug_str . $client->responseData . $client->response;
            }
        }
        
        // If nusoap is working fine, there may be an error server-side within vtiger
        // Catch it here
        // For some reason not all errors are caught here.
        if($sugar) {
            if($sugar['number'] != '0') {
                $this->soapError = true;
                $this->soapErrorNumber = $sugar['number'];
                $this->soapErrorName = $sugar['name'];
                $this->soapErrorText = $sugar['description'];
            }
        }
    }
    
    function hasError() {
        if($this->getErrorType() != _NO_ERROR) return true;
        
        return false;
    }
    
    function getErrorText($errorType = _ALL_ERRORS) {
        switch($errorType) {
            case _NUSOAP_ERROR:
                return $this->_getNusoapError();
                break;
            case _SOAP_ERROR:
                return $this->_getSoapError();
                break;
            case _ALL_ERRORS:
            default:
                return $this->_getNusoapError() . $this->_getSoapError();
                break;
        }
    }
    
    function getDebugText($errorType = _ALL_ERRORS) {
        switch($errorType) {
            case _NUSOAP_ERROR:
                return $this->_getNusoapDebug();
                break;
            case _SOAP_ERROR:
                return $this->_getSoapDebug();
                break;
            case _ALL_ERRORS:
            default:
                return $this->_getNusoapDebug() . $this->_getSoapDebug();
                break;
        }
    }
    
    function getErrorType() {
        if($this->clientError) return _NUSOAP_ERROR;
        if($this->soapError) return _SOAP_ERROR;
        
        return _NO_ERROR;
    }
    
    function _getNusoapDebug() {
        $debugText = '<div style="text-align: left;">
            <pre>' . $this->clientDebugText . '</pre></div>';
        
        return $debugText;
    }
    
    function _getNusoapError() {
        if($this->clientError) {
            $errorText = '<div style="text-align: left;">
                        <p><b>Connection Error</b></p>';
            $errorText .= '<p style="font-size: smaller;"><b>' . $this->clientErrorText . '</b></p>';
            
            $errorText .= '</div>';
            
            return $errorText;
        } else {
            return '';
        }
    }

    function _getSoapDebug() {
        return '';
    }
        
    function _getSoapError() {
        if($this->soapError) {
            $errorText = '<div style="text-align: left;">
                <p><b>Soap Error</b></p>';
            $errorText .= '<dl><dt>' . $this->soapErrorName . '</dt>';
            $errorText .= '<dd>' . $this->soapErrorText . '</dd></dl></div>';
            
            return $errorText;
        } else {
            return '';
        }
    }
}

?>
