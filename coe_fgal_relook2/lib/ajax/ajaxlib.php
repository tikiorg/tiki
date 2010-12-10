<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so it's better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
	header("location: index.php");
	die;
}

global $prefs;
if ($prefs['ajax_xajax'] === 'y') {
	require_once("lib/ajax/xajax/xajax_core/xajaxAIO.inc.php");
	if (!defined ('XAJAX_GET')) define ('XAJAX_GET', 0);

class TikiAjax extends xajax
{

		/**
		 * Array of templates that are allowed to be served
		 *
		 * @access private
		 * @var    array $aTemplates
		 */
		var $aTemplates;
		var $deniedFunctions;


		/**
		 * PHP 5 constructor.
		 *
		 * @access   public
		 * @return   void
		 */
		function __construct() {
			parent::__construct();

			$this->aTemplates = array( 'confirm.tpl' => 1, 'error.tpl' => 1);
			$this->deniedFunctions = array();

			$this->configure('waitCursor',true);
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
		 * Register a JavaScript function
		 * 
		 * @access	public
		 * @param	string|array $mFunction - JS function name OR array e.g. array('myFunctionName', array('callback' => 'myCallbackVarName')
		 * @param	int $sRequestType {XAJAX_GET = 0}
		 * @return	void
		 */
		function registerFunction($mFunction, $sRequestType=XAJAX_GET) {
			$functionName = is_array($mFunction) ? $mFunction[0] : $mFunction;
			$this->setDefaultMethod($sRequestType);
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
			if (is_array($mFunction) && count($mFunction) > 1) {
				xajax::register(XAJAX_FUNCTION,$functionName, $mFunction[1]);
			} else {
				xajax::register(XAJAX_FUNCTION,$mFunction);
			}
		}

		/*
		 * Returns default access denied error
		 * 
		 * @access public
		 * @return xajaxResponse object
		 */
		function accessDenied() {
			$objResponse = new xajaxResponse();
			$objResponse->Alert(tra("Permission denied"));
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
				$smarty->assign("xajax_js",$this->getJavascript('lib/ajax/xajax/'));
			}

			xajax::processRequest();
		}

	}
} else {
	// dumb TikiAjax class
	class TikiAjax
{
		function TikiAjax() {}
#		function __construct() {} // commented out because it causes PHP notice "constructor already defined for class TikiAjax" (no idea, where and why it even goes through this file when ajax feature is not enabled)
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

function loadComponent($template, $htmlElementId, $max_tikitabs = 0, $last_user = '') {
	global $smarty, $ajaxlib, $prefs, $user, $headerlib;
	global $js_script;
	$objResponse = new xajaxResponse();
	$objResponse->setCharacterEncoding('UTF-8');
	$confirmation_text = $smarty->get_template_vars('confirmation_text');

	if ( $last_user != $user ) {

		// If the user session timed out, completely reload the page to refresh right/left modules
		$objResponse->Redirect($_SERVER['REQUEST_URI'], 0);

	} elseif ( $ajaxlib->templateIsRegistered($template) ) {

		$content = '';
		if ($smarty->get_template_vars('mid') == $template) {
			$content = $smarty->get_template_vars('mid_data');
		}
		if (empty($content)) {
			$content = $smarty->fetch($template);
		}
		// Help
		require_once $smarty->_get_plugin_filepath('function', 'show_help');
		$content .= smarty_function_show_help(null,$smarty); 
		// Handle TikiTabs in order to display only the current tab in the XAJAX response
		// This has to be done here, since it is tikitabs() is usually called when loading the <body> tag
		//   which is not done again when replacing content by the XAJAX response
		//

		// take out javascript from the html response because it needs to be sent specifically as javascript
		// using $objResponse->script($s) below

		$js_script = $headerlib->getJsFromHTML( $content, true );
		
		// do included files too...
		$js_files = array();
		preg_match_all('/<script[^>]*src=[\'"]??(.*)[\'"]??>\s*<\/script>/Umis', $content, $jsarr);
		if (count($jsarr) > 1 && is_array($jsarr[1])) {
			$js =  $jsarr[1];
			$js_files = array_merge($js_files, $js);
		}

		// now remove all the js from the source
		$content = preg_replace('/\s*<script.*javascript.*>.*\/script>\s*/Umis', '', $content);
		// attach the cleaned xhtml to the response
		$objResponse->Assign($htmlElementId, "innerHTML", $content);

	} elseif ( $ajaxlib->templateIsRegistered('confirm.tpl') && !empty($confirmation_text) ) {

		$params = array(
				'_tag' => 'n',
				'_keepall' => 'y'
				);

		if ( $prefs['feature_ticketlib2'] == 'y' ) {
			$objResponse->confirmCommands(1, $confirmation_text);
			$params['daconfirm'] = 'y';
			$params['ticket'] = $smarty->get_template_vars('ticket');
		}

		require_once('lib/smarty_tiki/block.self_link.php');
		require_once('lib/smarty_tiki/modifier.escape.php');

		$uri = smarty_modifier_escape(smarty_block_self_link($params, '', $smarty), 'javascript');
		$objResponse->call("loadComponent('$uri','$template','$htmlElementId',".((int)$max_tikitabs).",'$last_user')");

	} elseif ( $ajaxlib->templateIsRegistered('error.tpl') ) {

		$content = $smarty->fetch('error.tpl');
		$objResponse->Assign($htmlElementId, "innerHTML", $content);

	} else {
		$objResponse->alert(sprintf(tra("Template %s not registered"),$template));
	}

	$js_files[] = array();

	if (count($js_files)) {
		foreach($js_files as $f) {
			if (trim($f) != '') {
				$objResponse->includeScript($f);
			}
		}
	}

	if( $prefs['tiki_minify_javascript'] == 'y' ) {
		ksort($headerlib->jsfiles);
		$hjsfiles = $headerlib->getMinifiedJs();
		if( isset($prefs['javascript_cdn']) && $prefs['javascript_cdn'] == 'google' ) {
			$hjsfiles[] = 'http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js';
			if( $prefs['feature_jquery_ui'] == 'y' ) {
				$hjsfiles[] = 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.5/jquery-ui.min.js';
			}
		}
	} else {
		$hjsfiles = $headerlib->getJsFilesList();
	}
	foreach($hjsfiles as $f) {
		foreach ($f as $jsf) {
			if (trim($jsf) != '') {
				$objResponse->includeScriptOnce($jsf);
			}
		}
	}

	if (strpos($content, '<input type="hidden" name="wysiwyg" value="y" />') !== false) {
		$ck_reset = "window.CKEDITOR = null;$.getScript('lib/ckeditor/ckeditor.js');";
	} else {
		$ck_reset = '';
	}
	$objResponse->script('xajax.config.requestURI="'.$ajaxlib->getRequestURI().'";' . $ck_reset);
	
	$max_tikitabs = (int)$max_tikitabs;
	if ( $max_tikitabs > 0 && $prefs['feature_tabs'] == 'y' ) {
		global $cookietab;
		$tab = ( $cookietab != '' ) ? (int)$cookietab : 1;
		$headerlib->add_jq_onready("tikitabs($tab,$max_tikitabs);");
	}
	// collect js from headerlib
	$jscontent = $headerlib->output_js(false);
	global $tikidomainslash;
	$tmp_jsfile = 'temp/public/'.$tikidomainslash.md5($jscontent).'.js';
	if ( ! file_exists( $tmp_jsfile) ) {
		file_put_contents( $tmp_jsfile, $jscontent );
		chmod($tmp_jsfile, 0644);
	}
	$objResponse->includeScript($tmp_jsfile);
	
	if ($prefs['ajax_autosave'] == 'y') {
		$objResponse->call("auto_save");
	}
	
	return $objResponse;
}

if ($prefs['ajax_autosave'] === 'y') {
	require_once("lib/ajax/autosave.php");
}

