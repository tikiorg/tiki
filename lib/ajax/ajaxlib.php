<?php
// CVS: $Id: ajaxlib.php,v 1.1 2006-03-20 16:36:13 lfagundes Exp $
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

global $feature_ajax;
if ($feature_ajax == 'y') {
    require_once("lib/ajax/xajax.inc.php");

    class TikiAjax extends xajax {
	
	/**
	 * Array of templates that are allowed to be served
	 *
	 * @access private
	 * @var    array $aTemplates
	 */
	var $aTemplates;
	
	
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
	 * Assigns xajax javascript to smarty just before processing requests.
	 * this way developer can register functions in php code and don't have
         * to manually assign xajax_js.
	 *
	 * @access  public
	 * @return  void
	 */
	function processRequests() {
	    global $smarty;
	    $smarty->assign("xajax_js",$this->getJavascript('','lib/ajax/xajax_js/xajax.js'));

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

global $ajaxlib, $smarty;
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