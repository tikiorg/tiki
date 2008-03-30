<?php
// CVS: $Id$
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $prefs;
if ($prefs['feature_ajax'] == 'y') {
    require_once("lib/ajax/xajax.inc.php");

    class TikiAjax extends xajax {
	
	/**
	 * Array of templates that are allowed to be served
	 *
	 * @access private
	 * @var    array $aTemplates
	 */
	var $aTemplates;
	var $deniedFunctions;
	
	
	/**
	 * PHP4 constructor.
	 *
	 * @access   public
	 * @return   void
	 */
	function TikiAjax() {
	    TikiAjax::__construct();
	}
	
	/**
	 * PHP 5 constructor.
	 *
	 * @access   public
	 * @return   void
	 */
	function __construct() {
	    xajax::xajax();
	    
	    $this->aTemplates = array();
	    $this->deniedFunctions = array();

	    $this->waitCursorOn();
	}
	
	/**
	 * Tells ajax engine that a given template can be retrieved with
	 * this page
	 *
	 * @access  public
	 * @param   string $name
	 * @return  void
	 */
	function registerTemplate($template) {
	    $this->aTemplates[$template] = 1;
	}

	/**
         * Sets access permission for a given function.
         * Permission MUST be set before registering the function.
	 *
	 * @access  public
	 * @param   string $functionName
	 * @param   boolean $hasPermission
	 * @return  void
         */
	function setPermission($functionName, $hasPermission) {
	    if (!$hasPermission) {
			$this->deniedFunctions[$functionName] = 1;
	    }
	}

	/**
	 * Checks if a given template is registered
	 *
	 * @access  public
	 * @param   string $template
	 * @return  boolean
	 */
	function templateIsRegistered($template) {
	    return array_key_exists($template, $this->aTemplates);
	}

	/**
         * 
         * 
         */
	function registerFunction($mFunction, $sRequestType=XAJAX_POST) {
		$functionName = is_array($mFunction) ? $mFunction[0] : $mFunction;
	    if (isset($this->deniedFunctions[$functionName])) {
			if (is_array($mFunction)) {
			    if (method_exists($mFunction[1], $mFunction[2] . 'Error')) {
					$mFunction[2] .= 'Error';
			    } else {
					$mFunction[1] &= $this;
					$mFunction[2] = 'accessDenied';
			    }
			} else {
			    if (function_exists($mFunction . 'Error')) {
					$mFunction .= 'Error';
			    } else {
					$mFunction = array($mFunction, &$this, 'accessDenied');
			    } 
			} 
	    }
	    xajax::registerFunction($mFunction, $sRequestType);
	}
	
	/*
	 * Returns default access denied error
	 * 
	 * @access public
	 * @return xajaxResponse object
	 */
	 function accessDenied() {
	 	$objResponse = new xajaxResponse();
		$objResponse->addAlert(tra("Permission denied"));
	 	return $objResponse;
	 }

	/**
	 * Assigns xajax javascript to smarty just before processing requests.
	 * this way developer can register functions in php code and don't have
         * to manually assign xajax_js.
	 *
	 * @access  public
	 * @return  void
	 */
	function processRequests() {
	    global $smarty;
	    if (isset($smarty)) {
		$smarty->assign("xajax_js",$this->getJavascript('','lib/ajax/xajax_js/xajax.js'));
	    }

	    xajax::processRequests();
	}

    }
} else {
    // dumb TikiAjax class
    class TikiAjax {
	function TikiAjax() {}
	function __construct() {}
	function registerFunction() {}
	function registerTemplate() {}
	function templateIsRegistered() { return false; }
	function processRequests() {}
	function getJavascript() { return ''; }
    }
}

global $ajaxlib;
$ajaxlib = new TikiAjax();
$ajaxlib->registerFunction("loadComponent");

function loadComponent($template, $htmlElementId) {
    global $smarty, $ajaxlib;
    $objResponse = new xajaxResponse();
    
    if ($ajaxlib->templateIsRegistered($template)) {
	$content = $smarty->fetch($template);
	$objResponse->addAssign($htmlElementId, "innerHTML", $content);
    } else {
	$objResponse->addAlert(sprintf(tra("Template %s not registered"),$template));
    }
    return $objResponse;
}

?>
