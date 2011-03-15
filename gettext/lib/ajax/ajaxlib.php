<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// This script may only be included - so it's better to die if called directly.
global $access;
$access->check_script($_SERVER["SCRIPT_NAME"],basename(__FILE__));
$access->check_feature('feature_ajax');

/**
 * Lib class for new ajax component loading in Tiki 7+
 */
class AjaxLib {

	/**
	 * Array of templates that are allowed to be served
	 *
	 * @access private
	 * @var    array $aTemplates
	 */
	var $templates;
	var $response;

	/**
	 * constructor.
	 *
	 * @access   public
	 * @return   void
	 */
	function __construct() {

		$this->templates = array( 'confirm.tpl', 'error.tpl' );
		$this->response = array(
			'innerHtml' => '',
			'js_includes' => array(),
			'css_includes' => array(),
		);
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
		$this->templates[] = $template;
	}

	/**
	 * Checks if a given template is registered
	 *
	 * @access  public
	 * @param   string $template
	 * @return  boolean
	 */
	function templateIsRegistered($template) {
		return in_array($template, $this->templates);
	}

	/**
	 * Outputs the rendered data
	 * Currently only for "plain" whole page loads from jqmobile
	 *
	 * @access  public
	 * @param   string $data
	 * @return  doesn't return, outputs and dies
	 */
	function processRequests( $data = '', $tpl = '' ) {
		global $access, $smarty;
		
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
			if (empty( $data ) && empty( $tpl )) {
				$access->output_serialized(tra('Error'));
				exit;
			}
			$data = $smarty->fetch('tiki_full.tpl');
			$js_ajax_include = $this->cacheJSOutput();
			//$js_ajax_include = json_encode( array( $js_ajax_include ));
			$data = preg_replace('/<div id="js_ajax_include".*?><\/div>/i', '<div id="js_ajax_include" style="display:none;">' . $js_ajax_include . '</div>', $data);
			$access->output_serialized( $data );
			exit;
		}
	}

	/**
	 * Caches JS needed for a page so far into a temp file
	 *
	 * @access  private
	 * @return	string path to the file
	 */
	private function cacheJSOutput() {
		global $tikidomainslash, $headerlib;
		// collect js from headerlib
		$jscontent = $headerlib->output_js(false);
		global $tikidomainslash;
		$tmp_jsfile = 'temp/public/'.$tikidomainslash.md5($jscontent).'.js';
		if ( ! file_exists( $tmp_jsfile) ) {
			file_put_contents( $tmp_jsfile, $jscontent );
			chmod($tmp_jsfile, 0644);
		}
		return $tmp_jsfile;
	}

	/**
	 * Unused so far - from old ajaxlib for future reference
	 * Probably won't look much like this in the end...
	 */
	private function loadComponent($template, $htmlElementId, $max_tikitabs = 0, $last_user = '') {
		global $smarty, $prefs, $user, $headerlib, $access;
		//$objResponse = new xajaxResponse();
		//$objResponse->setCharacterEncoding('UTF-8');

		
		$confirmation_text = $smarty->get_template_vars('confirmation_text');

		if ( $last_user != $user ) {

			// If the user session timed out, completely reload the page to refresh right/left modules
			$access->redirect($_SERVER['REQUEST_URI']);

		} elseif ( $this->templateIsRegistered($template) ) {

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
			//$objResponse->Assign($htmlElementId, "innerHTML", $content);

		} elseif ( $this->templateIsRegistered('confirm.tpl') && !empty($confirmation_text) ) {

//			$params = array(
//					'_tag' => 'n',
//					'_keepall' => 'y'
//					);
//
//			if ( $prefs['feature_ticketlib2'] == 'y' ) {
//				$objResponse->confirmCommands(1, $confirmation_text);
//				$params['daconfirm'] = 'y';
//				$params['ticket'] = $smarty->get_template_vars('ticket');
//			}
//
//			require_once('lib/smarty_tiki/block.self_link.php');
//			require_once('lib/smarty_tiki/modifier.escape.php');
//
//			$uri = smarty_modifier_escape(smarty_block_self_link($params, '', $smarty), 'javascript');
//			$objResponse->call("loadComponent('$uri','$template','$htmlElementId',".((int)$max_tikitabs).",'$last_user')");

		} elseif ( $this->templateIsRegistered('error.tpl') ) {

//			$content = $smarty->fetch('error.tpl');
//			$objResponse->Assign($htmlElementId, "innerHTML", $content);

		} else {
//			$objResponse->alert(sprintf(tra("Template %s not registered"),$template));
			$content = tra("Template %s not registered");
		}

		$access->output_serialized(array(
			'innerHtml' => $content,
		));
		exit;
	}


}


global $ajaxlib;
$ajaxlib = new AjaxLib();

