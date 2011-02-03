<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
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

class Smarty_Tiki extends Smarty
{
	var $url_overriding_prefix_stack = null;
	var $url_overriding_prefix = null;

	function Smarty_Tiki($tikidomain = '') {
		parent::Smarty();
		global $prefs;

		if ($tikidomain) { $tikidomain.= '/'; }
		$this->template_dir = realpath('templates/');
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
		$this->security = ( $prefs['smarty_security'] == 'y' );

		// In general, it's better that use_sub_dirs = false
		// If ever you are on a very large/complex/multilingual site and your
		// templates_c directory is > 10 000 files, (you can check at tiki-admin_system.php)
		// you can change to true and maybe you will get better performance.
		// http://smarty.php.net/manual/en/variable.use.sub.dirs.php
		//
		$this->use_sub_dirs = false;

		$this->security_settings['MODIFIER_FUNCS'] = array_merge(
			$this->security_settings['MODIFIER_FUNCS'],
			array('addslashes', 'ucfirst', 'ucwords', 'urlencode', 'md5', 'implode', 'explode', 'is_array', 'htmlentities', 'var_dump', 'strip_tags')
		);
		$this->security_settings['IF_FUNCS'] = array_merge(
			$this->security_settings['IF_FUNCS'],
			array('tra', 'strlen', 'strstr', 'strtolower', 'basename', 'ereg', 'array_key_exists', 'preg_match', 'in_array')
		);
		$secure_dirs[] = 'img/icons2';
		$this->secure_dir = $secure_dirs;
		$this->security_settings['ALLOW_SUPER_GLOBALS'] = true;

		$this->url_overriding_prefix_stack = array();
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
				$smarty_orig_values[ $k ] =& $this->get_template_vars( $k );
				$this->assign_by_ref($k, $override_vars[ $k ]);
			}
		}

		$return = $this->fetch($_smarty_tpl_file);

		// Restore original values of smarty variables
		if ( count( $smarty_orig_values ) > 0 ) {
			foreach ( $smarty_orig_values as $k => $v ) {
				$this->assign_by_ref($k, $smarty_orig_values[ $k ]);
			}
		}

		unset( $smarty_orig_values );
		return $return;
	}

	function fetch($_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false) {
		global $prefs, $style_base, $tikidomain, $zoom_templates;

		if ( ($tpl = $this->get_template_vars('mid')) && ( $_smarty_tpl_file == 'tiki.tpl' || $_smarty_tpl_file == 'tiki-print.tpl' || $_smarty_tpl_file == 'tiki_full.tpl' ) ) {

			// Set the last mid template to be used by AJAX to simulate a 'BACK' action
			if ( isset($_SESSION['last_mid_template']) ) {
				$this->assign('last_mid_template', $_SESSION['last_mid_template']);
				$this->assign('last_mid_php', $_SESSION['last_mid_php']);
			}
			$_SESSION['last_mid_template'] = $tpl;
			$_SESSION['last_mid_php'] = $_SERVER['REQUEST_URI'];

			// set the first part of the browser title for admin pages
			if (!isset($this->_tpl_vars['headtitle'])) {
				$script_name = basename($_SERVER['SCRIPT_NAME']);
				if ($script_name != 'tiki-admin.php' && strpos($script_name, 'tiki-admin') === 0) {
					$str = substr($script_name, 10, strpos($script_name, '.php') - 10);
					$str = ucwords(trim(str_replace('_', ' ', $str)));
					$this->assign('headtitle', tra('Admin ' . $str));
					// get_strings tra('Admin Calendar') tra('Admin Actionlog') tra('Admin Banners') tra('Admin Calendars') tra('Admin Categories') tra('Admin Content Templates')
					//			tra('Admin Contribution') tra('Admin Cookies') tra('Admin Dsn') tra('Admin External Wikis') tra('Admin Forums') tra('Admin Hotwords') tra('Admin Html Page Content')
					//			tra('Admin Html Pages') tra('Admin Integrator Rules') tra('Admin Integrator') tra('Admin Keywords') tra('Admin Layout') tra('Admin Links') tra('Admin Mailin')
					//			tra('Admin Menu Options') tra('Admin Menus') tra('Admin Metrics') tra('Admin Modules') tra('Admin Newsletter Subscriptions') tra('Admin Newsletters') tra('Admin Notifications')
					//			tra('Admin Poll Options') tra('Admin Polls') tra('Admin Rssmodules') tra('Admin Security') tra('Admin Shoutbox Words') tra('Admin Structures') tra('Admin Survey Questions')
					//			tra('Admin Surveys') tra('Admin System') tra('Admin Toolbars') tra('Admin Topics') tra('Admin Tracker Fields') tra('Admin Trackers')
				} else if (strpos($script_name, 'tiki-list') === 0) {
					$str = substr($script_name, 9, strpos($script_name, '.php') - 9);
					$str = ucwords(trim(str_replace('_', ' ', $str)));
					$this->assign('headtitle', tra('List ' . $str));
					// get_strings tra('List Articles') tra('List Banners') tra('List Blogs') tra('List Cache') tra('List Comments') tra('List Contents') tra('List Faqs') tra('List File Gallery')
					//			tra('List Gallery') tra('List Integrator Repositories') tra('List Kaltura Entries') tra('List Object Permissions') tra('List Posts') tra('List Quizzes') tra('List Submissions')
					//			tra('List Surveys') tra('List Trackers') tra('List Users') tra('List Pages')
				} else if (strpos($script_name, 'tiki-view') === 0) {
					$str = substr($script_name, 9, strpos($script_name, '.php') - 9);
					$str = ucwords(trim(str_replace('_', ' ', $str)));
					$this->assign('headtitle', tra('View ' . $str));
					// get_strings tra('View Articles') tra('View Banner') tra('View Blog Post Image') tra('View Blog Post') tra('View Blog') tra('View Cache') tra('View Faq') tra('View Forum Thread')
					//			 tra('View Minical Topic') tra('View Sheets') tra('View Tracker Item') tra('View Tracker More Info') tra('View Tracker')
				} else { // still not set? guess...
					$str = str_replace(array('tiki-', '.php', '_'), array('', '', ' '), $script_name);
					$str = ucwords($str);
					$this->assign('headtitle', tra($str));	// for files where no title has been set or can be reliably calculated - translators: please add comments here as you find them
				}
			}

			// Enable Template Zoom
			if ( $prefs['feature_template_zoom'] == 'y' && isset($zoom_templates) ) {
				if ( ! isset($_REQUEST['zoom']) && isset($_REQUEST['zoom_value']) && isset($_REQUEST['zoom_x']) && isset($_REQUEST['zoom_y']) ) {
					// Hack for IE6 when using an image input to submit the zoom value
					//  (IE will only send zoom_x and zoom_y params without the value instead of zoom)
					//  In this case, and if we have set a hidden field 'zoom_value', we use it's value
					//
					$_REQUEST['zoom'] = $_REQUEST['zoom_value'];
				}
				if ( isset($_REQUEST['zoom']) && is_array($zoom_templates) && in_array($_REQUEST['zoom'], $zoom_templates) ) {
					$_smarty_tpl_file = 'tiki_full.tpl';
					$tpl = $_REQUEST['zoom'].'.tpl';
					$prefs['feature_fullscreen'] = 'n';
					$this->assign('zoom_mode', 'y');
				}
			}

			if ( $_smarty_tpl_file == 'tiki-print.tpl' ) {
				$this->assign('print_page', 'y');
			}
			$data = $this->fetch($tpl, $_smarty_cache_id, $_smarty_compile_id);//must get the mid because the modules can overwrite smarty variables

			$this->assign('mid_data', $data);
			if ($prefs['feature_fullscreen'] != 'y' || empty($_SESSION['fullscreen']) || $_SESSION['fullscreen'] != 'y')
				include_once('tiki-modules.php');

		} elseif ($_smarty_tpl_file == 'confirm.tpl' || $_smarty_tpl_file == 'error.tpl' || $_smarty_tpl_file == 'error_ticket.tpl' || $_smarty_tpl_file == 'error_simple.tpl') {
			ob_end_clean(); // Empty existing Output Buffer that may have been created in smarty before the call of this confirm / error* template

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

		return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
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
		$isCompiled = $this->_is_compiled($_smarty_tpl_file, $this->_get_compile_path($_smarty_tpl_file));
		if (!$isCompiled) {
			$lgSave = $prefs['language'];
			$prefs['language'] = $lg;
		}
		$res = parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_display);
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
		return parent::is_cached($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id);
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
		return parent::clear_cache($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $_smarty_exp_time);
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
$smarty->load_filter('pre', 'tr');
$smarty->load_filter('pre', 'jq');

include_once('lib/smarty_tiki/resource.wiki.php');
$smarty->register_resource('wiki', array('smarty_resource_wiki_source', 'smarty_resource_wiki_timestamp', 'smarty_resource_wiki_secure', 'smarty_resource_wiki_trusted'));

global $prefs;
// Assign the prefs array in smarty, by reference
$smarty->assign_by_ref('prefs', $prefs);

// Define the special maxRecords global var
$maxRecords = $prefs['maxRecords'];
$smarty->assign_by_ref('maxRecords', $maxRecords);

if ($prefs['log_tpl'] == 'y') {
	$smarty->load_filter('pre', 'log_tpl');
}
if ( $prefs['feature_sefurl_filter'] == 'y' ) {
  require_once ('tiki-sefurl.php');
  $smarty->register_outputfilter('filter_out_sefurl');
}

// temp assigns for textarea row/cols that used to depend on
// defunct textareasize thing. These textareas should eventually be
// converted to use {textarea} object that have the correct defaults already
$smarty->assign('rows', 20);
$smarty->assign('cols', 80);
