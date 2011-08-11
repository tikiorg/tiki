<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'],basename(__FILE__)) !== FALSE) {
  header('location: index.php');
  exit;
}

require_once 'lib/setup/third_party.php';
require_once (defined('SMARTY_DIR') ? SMARTY_DIR : 'lib/smarty/libs/') . 'Smarty.class.php';

class Tiki_Security_Policy extends Smarty_Security {
		public $php_modifiers = array( 'nl2br','escape', 'count', 'addslashes', 'ucfirst', 'ucwords', 'urlencode', 'md5', 'implode', 'explode', 'is_array', 'htmlentities', 'var_dump', 'strip_tags', 'json_encode', 'stristr' );
		public $php_functions = array('isset', 'empty', 'count', 'sizeof', 'in_array', 'is_array', 'time', 'nl2br', 'tra', 'strlen', 'strstr', 'strtolower', 'basename', 'ereg', 'array_key_exists', 'preg_match', 'json_encode', 'stristr', 'is_numeric', 'array' );
		public $secure_dir = array(
			'img/icons',
			'img/icons2',
			'img/flags',
			'img/trackers',
			'images/',
			'pics/',
			'pics/icons',
			'pics/icons/mime',
			'pics/large',
		);
}

class Smarty_Tiki extends Smarty
{
	var $url_overriding_prefix_stack = null;
	var $url_overriding_prefix = null;

	function Smarty_Tiki($tikidomain = '') {
		parent::__construct();
		global $prefs;

		if ($tikidomain) { $tikidomain.= '/'; }
		$this->template_dir = array(realpath('templates/'));
		$this->compile_dir = realpath("templates_c/$tikidomain");
		$this->config_dir = realpath('configs/');
		$this->cache_dir = realpath("templates_c/$tikidomain");
		$this->caching = 0;
		$this->compile_check = ( $prefs['smarty_compilation'] != 'never' );
		$this->force_compile = ( $prefs['smarty_compilation'] == 'always' );
		$this->assign('app_name', 'Tiki');
		$this->plugins_dir = array(	// the directory order must be like this to overload a plugin
			TIKI_SMARTY_DIR,
			SMARTY_DIR.'plugins'
		);

		if ( $prefs['smarty_security'] == 'y' ) {
			$this->enableSecurity('Tiki_Security_Policy');
		} else {
			$this->disableSecurity();
		}
		$this->use_sub_dirs = false;
		$this->url_overriding_prefix_stack = array();
		if (!empty($prefs['smarty_notice_reporting']) and $prefs['smarty_notice_reporting'] === 'y' ) {
			$this->error_reporting = E_NOTICE;
		} else {
			$this->error_reporting = 0;
		}
	}

	function _smarty_include($params) {
		global $style_base, $tikidomain;

		if (isset($style_base)) {
			if ($tikidomain and file_exists("templates/$tikidomain/styles/$style_base/".$params['smarty_include_tpl_file'])) {
				$params['smarty_include_tpl_file'] = "$tikidomain/styles/$style_base/".$params['smarty_include_tpl_file'];
			} elseif ($tikidomain and file_exists("templates/$tikidomain/".$params['smarty_include_tpl_file'])) {
				$params['smarty_include_tpl_file'] = "$tikidomain/".$params['smarty_include_tpl_file'];
			} elseif (file_exists("templates/styles/$style_base/".$params['smarty_include_tpl_file'])) {
				$params['smarty_include_tpl_file'] = "styles/$style_base/".$params['smarty_include_tpl_file'];
			}
		}
		return parent::_smarty_include($params);
	}

	// Fetch templates from plugins (smarty plugins, wiki plugins, modules, ...) that may need to :
	//   - temporarily override some smarty vars,
	//   - prefix their self_link / button / query URL arguments
	//
	function plugin_fetch($_smarty_tpl_file, &$override_vars = null) {
		$smarty_orig_values = array();
		if ( is_array( $override_vars ) ) {
			foreach ( $override_vars as $k => $v ) {
				$smarty_orig_values[ $k ] =& $this->getTemplateVars( $k );
				$this->assignByRef($k, $override_vars[ $k ]);
			}
		}

		$return = $this->fetch($_smarty_tpl_file);

		// Restore original values of smarty variables
		if ( count( $smarty_orig_values ) > 0 ) {
			foreach ( $smarty_orig_values as $k => $v ) {
				$this->assignByRef($k, $smarty_orig_values[ $k ]);
			}
		}

		unset( $smarty_orig_values );
		return $return;
	}

	function fetch($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $parent = null, $_smarty_display = false) {
		global $prefs, $style_base, $tikidomain, $zoom_templates;

		if ( ($tpl = $this->getTemplateVars('mid')) && ( $_smarty_tpl_file == 'tiki.tpl' || $_smarty_tpl_file == 'tiki-print.tpl' || $_smarty_tpl_file == 'tiki_full.tpl' ) ) {

			// Set the last mid template to be used by AJAX to simulate a 'BACK' action
			if ( isset($_SESSION['last_mid_template']) ) {
				$this->assign('last_mid_template', $_SESSION['last_mid_template']);
				$this->assign('last_mid_php', $_SESSION['last_mid_php']);
			}
			$_SESSION['last_mid_template'] = $tpl;
			$_SESSION['last_mid_php'] = $_SERVER['REQUEST_URI'];

			// set the first part of the browser title for admin pages
			if (!isset($this->getGlobal['headtitle'])) {
				$script_name = basename($_SERVER['SCRIPT_NAME']);
				if ($script_name != 'tiki-admin.php' && strpos($script_name, 'tiki-admin') === 0) {
					$str = substr($script_name, 10, strpos($script_name, '.php') - 10);
					$str = ucwords(trim(str_replace('_', ' ', $str)));
					$this->assign('headtitle', 'Admin ' . $str);
					// get_strings tra('Admin Calendar') tra('Admin Actionlog') tra('Admin Banners') tra('Admin Calendars') tra('Admin Categories') tra('Admin Content Templates')
					//			tra('Admin Contribution') tra('Admin Cookies') tra('Admin Dsn') tra('Admin External Wikis') tra('Admin Forums') tra('Admin Hotwords') tra('Admin Html Page Content')
					//			tra('Admin Html Pages') tra('Admin Integrator Rules') tra('Admin Integrator') tra('Admin Keywords') tra('Admin Layout') tra('Admin Links') tra('Admin Mailin')
					//			tra('Admin Menu Options') tra('Admin Menus') tra('Admin Metrics') tra('Admin Modules') tra('Admin Newsletter Subscriptions') tra('Admin Newsletters') tra('Admin Notifications')
					//			tra('Admin Poll Options') tra('Admin Polls') tra('Admin Rssmodules') tra('Admin Security') tra('Admin Shoutbox Words') tra('Admin Structures') tra('Admin Survey Questions')
					//			tra('Admin Surveys') tra('Admin System') tra('Admin Toolbars') tra('Admin Topics') tra('Admin Tracker Fields') tra('Admin Trackers')
				} else if (strpos($script_name, 'tiki-list') === 0) {
					$str = substr($script_name, 9, strpos($script_name, '.php') - 9);
					$str = ucwords(trim(str_replace('_', ' ', $str)));
					$this->assign('headtitle', 'List ' . $str);
					// get_strings tra('List Articles') tra('List Banners') tra('List Blogs') tra('List Cache') tra('List Comments') tra('List Contents') tra('List Faqs') tra('List File Gallery')
					//			tra('List Gallery') tra('List Integrator Repositories') tra('List Kaltura Entries') tra('List Object Permissions') tra('List Posts') tra('List Quizzes') tra('List Submissions')
					//			tra('List Surveys') tra('List Trackers') tra('List Users') tra('List Pages')
				} else if (strpos($script_name, 'tiki-view') === 0) {
					$str = substr($script_name, 9, strpos($script_name, '.php') - 9);
					$str = ucwords(trim(str_replace('_', ' ', $str)));
					$this->assign('headtitle', 'View ' . $str);
					// get_strings tra('View Articles') tra('View Banner') tra('View Blog Post Image') tra('View Blog Post') tra('View Blog') tra('View Cache') tra('View Faq') tra('View Forum Thread')
					//			 tra('View Minical Topic') tra('View Sheets') tra('View Tracker Item') tra('View Tracker More Info') tra('View Tracker')
				} else { // still not set? guess...
					$str = str_replace(array('tiki-', '.php', '_'), array('', '', ' '), $script_name);
					$str = ucwords($str);
					$this->assign('headtitle', tra($str));	// for files where no title has been set or can be reliably calculated - translators: please add comments here as you find them
				}
			}

			if ( $_smarty_tpl_file == 'tiki-print.tpl' ) {
				$this->assign('print_page', 'y');
			}
			$data = $this->fetch($tpl, $_smarty_cache_id, $_smarty_compile_id, $parent);//must get the mid because the modules can overwrite smarty variables

			$this->assign('mid_data', $data);

			// Enable AJAX
			if ( $prefs['feature_ajax'] === 'y' && $prefs['mobile_feature'] === 'y' && $_smarty_display ) {
				global $ajaxlib; require_once( 'lib/ajax/ajaxlib.php' );
				$ajaxlib->registerTemplate( $tpl );
				$ajaxlib->processRequests( $data, $tpl );
			}

			include_once('tiki-modules.php');

		} elseif ($_smarty_tpl_file == 'confirm.tpl' || $_smarty_tpl_file == 'error.tpl' || $_smarty_tpl_file == 'error_ticket.tpl' || $_smarty_tpl_file == 'error_simple.tpl') {
			ob_end_clean(); // Empty existing Output Buffer that may have been created in smarty before the call of this confirm / error* template
			if ( $prefs['feature_obzip'] == 'y' ) {
				ob_start('ob_gzhandler');
			}

			// Enable AJAX
			if ( $prefs['feature_ajax'] === 'y' && $prefs['mobile_feature'] === 'y' && $_smarty_display ) {
				global $ajaxlib; require_once('lib/ajax/ajaxlib.php');
				$ajaxlib->registerTemplate($_smarty_tpl_file);
				$ajaxlib->processRequests();
			}

			include_once('tiki-modules.php');

		}
		if (isset($style_base)) {
			if ($tikidomain and file_exists("templates/$tikidomain/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/styles/$style_base/$_smarty_tpl_file";
			} elseif ($tikidomain and file_exists("templates/$tikidomain/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/$_smarty_tpl_file";
			} elseif (file_exists("templates/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "styles/$style_base/$_smarty_tpl_file";
			}
		}

		$_smarty_cache_id = $prefs['language'] . $_smarty_cache_id;
		$_smarty_compile_id = $prefs['language'] . $_smarty_compile_id;

		return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $parent, $_smarty_display);
	}

	function clear_assign($var) {
		return parent::clearAssign($var);
	}

	function assign_by_ref($var,&$value) {
		return parent::assignByRef($var,$value);
	}

	/* fetch in a specific language  without theme consideration */
	function fetchLang($lg, $_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false)  {
		global $prefs, $lang, $style_base, $tikidomain;

                if (isset($prefs['style']) && isset($style_base)) {
			if ($tikidomain and file_exists("templates/$tikidomain/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/styles/$style_base/$_smarty_tpl_file";
			} elseif ($tikidomain and file_exists("templates/$tikidomain/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/$_smarty_tpl_file";
			} elseif (file_exists("templates/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "styles/$style_base/$_smarty_tpl_file";
			}
		}

		$_smarty_cache_id = $lg . $_smarty_cache_id;
		$_smarty_compile_id = $lg . $_smarty_compile_id;
		$this->_compile_id = $lg . $_smarty_compile_id; // not pretty but I don't know how to change id for get_compile_path
		$isCompiled = $this->getCompiledFilepath($_smarty_tpl_file);
		if (!$isCompiled) {
			$lgSave = $prefs['language'];
			$prefs['language'] = $lg;
		}
		$res = parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, null, $_smarty_display);
		if (!$isCompiled) {
			$prefs['language'] = $lgSave;
		}

		return preg_replace("/^[ \t]*/", '', $res);
	}
	function is_cached($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null) {
		global $prefs, $style_base, $tikidomain;

		if (isset($prefs['style']) && isset($style_base)) {
			if ($tikidomain and file_exists("templates/$tikidomain/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/styles/$style_base/$_smarty_tpl_file";
			} elseif ($tikidomain and file_exists("templates/$tikidomain/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/$_smarty_tpl_file";
			} elseif (file_exists("templates/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "styles/$style_base/$_smarty_tpl_file";
			}
		}
		$_smarty_cache_id = $prefs['language'] . $_smarty_cache_id;
		$_smarty_compile_id = $prefs['language'] . $_smarty_compile_id;
		return parent::isCached($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id);
	}
	function clear_cache($_smarty_tpl_file = null, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_exp_time=null) {
		global $prefs, $style_base, $tikidomain;

		if (isset($prefs['style']) && isset($style_base) && isset($_smarty_tpl_file)) {
			if ($tikidomain and file_exists("templates/$tikidomain/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/styles/$style_base/$_smarty_tpl_file";
			} elseif ($tikidomain and file_exists("templates/$tikidomain/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "$tikidomain/$_smarty_tpl_file";
			} elseif (file_exists("templates/styles/$style_base/$_smarty_tpl_file")) {
				$_smarty_tpl_file = "styles/$style_base/$_smarty_tpl_file";
			}
		}
		$_smarty_cache_id = $prefs['language'] . $_smarty_cache_id;
		$_smarty_compile_id = $prefs['language'] . $_smarty_compile_id;
		return parent::clearCache($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_exp_time);
	}
	function display($resource_name, $cache_id=null, $compile_id = null, $content_type = 'text/html; charset=utf-8') {

		global $prefs;

		if ( !empty($prefs['feature_htmlpurifier_output']) and $prefs['feature_htmlpurifier_output'] == 'y' ) {
			static $loaded = false;
			static $purifier = null;
			if (!$loaded) {
				require_once('lib/htmlpurifier_tiki/HTMLPurifier.tiki.php');
				$config = getHTMLPurifierTikiConfig();
				$purifier = new HTMLPurifier($config);
				$loaded = true;
			}
		}

		//
		// By default, display is used with text/html content in UTF-8 encoding
		// If you want to output other data from smarty,
		//   - either use fetch() / fetchLang()
		//   - or set $content_type to '' (empty string) or another content type.
		//
		if ( $content_type != '' && ! headers_sent() ) {
			header('Content-Type: '.$content_type);
		}
		if ( !empty($prefs['feature_htmlpurifier_output']) and $prefs['feature_htmlpurifier_output'] == 'y' ) {
			return $purifier->purify(parent::display($resource_name, $cache_id, $compile_id));
		} else {
			return parent::display($resource_name, $cache_id, $compile_id);

		}
	}
	// Returns the file name associated to the template name
	function get_filename($template) {
		global $tikidomain, $style_base;
		if (!empty($tikidomain) && is_file($this->template_dir.'/'.$tikidomain.'/styles/'.$style_base.'/'.$template)) {
    			$file = "/$tikidomain/styles/$style_base/";
  		} elseif (!empty($tikidomain) && is_file($this->template_dir.'/'.$tikidomain.'/'.$template)) {
    			$file = "/$tikidomain/";
  		} elseif (is_file($this->template_dir.'/styles/'.$style_base.'/'.$template)) {
			$file = "/styles/$style_base/";
  		} else {
    			$file = '/';
  		}
		return $this->template_dir.$file.$template;
	}

	function set_request_overriders( $url_arguments_prefix, $arguments_list ) {
		$this->url_overriding_prefix_stack[] = array( $url_arguments_prefix . '-', $arguments_list );
		$this->url_overriding_prefix =& $this->url_overriding_prefix_stack[ count( $this->url_overriding_prefix_stack ) - 1 ];
	}

	function remove_request_overriders( $url_arguments_prefix, $arguments_list ) {
		$last_override_prefix = empty( $this->url_overriding_prefix_stack ) ? false : array_pop($this->url_overriding_prefix_stack);
		if ( ! is_array($last_override_prefix) || $url_arguments_prefix . '-' != $last_override_prefix[0] ) {
			trigger_error( 'URL Overriding prefix stack is in a bad state', E_USER_ERROR );
		}
		$this->url_overriding_prefix =& $this->url_overriding_prefix_stack[ count( $this->url_overriding_prefix_stack ) - 1 ];;
	}
}

if (!isset($tikidomain)) { $tikidomain = ''; }
$smarty = new Smarty_Tiki($tikidomain);
$smarty->loadFilter('pre', 'tr');
$smarty->loadFilter('pre', 'jq');

include_once('lib/smarty_tiki/resource.tplwiki.php');
$smarty->registerResource('tplwiki', array('smarty_resource_tplwiki_source', 'smarty_resource_tplwiki_timestamp', 'smarty_resource_tplwiki_secure', 'smarty_resource_tplwiki_trusted'));

include_once('lib/smarty_tiki/resource.wiki.php');
$smarty->registerResource('wiki', array('smarty_resource_wiki_source', 'smarty_resource_wiki_timestamp', 'smarty_resource_wiki_secure', 'smarty_resource_wiki_trusted'));

global $prefs;
// Assign the prefs array in smarty, by reference
$smarty->assignByRef('prefs', $prefs);

// Define the special maxRecords global var
$maxRecords = $prefs['maxRecords'];
$smarty->assignByRef('maxRecords', $maxRecords);

if ($prefs['log_tpl'] == 'y') {
	$smarty->loadFilter('pre', 'log_tpl');
}
if ( $prefs['feature_sefurl_filter'] == 'y' ) {
  require_once ('tiki-sefurl.php');
  $smarty->registerFilter('output','filter_out_sefurl');
}

// temp assigns for textarea row/cols that used to depend on
// defunct textareasize thing. These textareas should eventually be
// converted to use {textarea} object that have the correct defaults already
$smarty->assign('rows', 20);
$smarty->assign('cols', 80);
