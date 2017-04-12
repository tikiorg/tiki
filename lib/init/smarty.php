<?php
/**
 * brings Smarty functionality into Tiki
 * 
 * this script may only be included, it will die if called directly.
 *
 * @package TikiWiki
 * @subpackage lib\init
 * @copyright (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project. All Rights Reserved. See copyright.txt for details and a complete list of authors.
 * @licence Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
 */
// $Id$

// die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== FALSE) {
  header('location: index.php');
  exit;
}

require_once 'lib/setup/third_party.php';

/**
 * extends Smarty_Security
 * @package TikiWiki\lib\init
 */
class Tiki_Security_Policy extends Smarty_Security
{
	/**
	 * needs a proper description
	 * @var array $secure_dir
	 */

	public $trusted_uri = array();

	public $secure_dir = array(
		'',
		'img/',
		'img/icons',
		'img/flags',
		'img/mytiki',
		'img/smiles',
		'img/trackers',
		'img/icons/mime',
		'img/icons/large',
		'lib/ckeditor_tiki/ckeditor-icons',
	);

	/**
	 * needs a proper description
	 * @param Smarty $smarty
	 */
	function __construct($smarty)
	{
		if (class_exists("TikiLib")) {
			$tikilib = TikiLib::lib('tiki');
		}

		parent::__construct($smarty);

		$functions = array();
		$modifiers = array();
		$dirs = array();
		
		//With phpunit and command line these don't exist yet for some reason
		if (isset($tikilib) && method_exists($tikilib, "get_preference")) {
			global $url_host;
			$this->trusted_uri[] = '#' . preg_quote("http://$url_host", '$#') . '#';
			$this->trusted_uri[] = '#' . preg_quote("https://$url_host", '$#') . '#';

			$functions = array_filter($tikilib->get_preference('smarty_security_functions', array(), true));
			$modifiers = array_filter($tikilib->get_preference('smarty_security_modifiers', array(), true));
			$dirs = array_filter($tikilib->get_preference('smarty_security_dirs', array(), true));

			$cdns = preg_split('/\s+/', $tikilib->get_preference('tiki_cdn', ''));
			$cdns_ssl = preg_split('/\s+/', $tikilib->get_preference('tiki_cdn_ssl', ''));
			$cdn_uri = array_filter(array_merge($cdns, $cdns_ssl));
			foreach ($cdn_uri as $uri) {
				$this->trusted_uri[] = '#' . preg_quote($uri) . '$#';
			}
		}

		$functions = (isset($functions) ? $functions : array());
		$modifiers = (isset($modifiers) ? $modifiers : array());

		$this->php_modifiers = array_merge(array( 'nl2br','escape', 'count', 'addslashes', 'ucfirst', 'ucwords', 'urlencode', 'md5', 'implode', 'explode', 'is_array', 'htmlentities', 'var_dump', 'strip_tags', 'json_encode', 'stristr', 'trim', 'array_reverse', 'tra', 'strpos'), $modifiers);
		$this->php_functions = array_merge(array('isset', 'empty', 'count', 'sizeof', 'in_array', 'is_array', 'time', 'nl2br', 'tra', 'strlen', 'strstr', 'strtolower', 'basename', 'ereg', 'array_key_exists', 'preg_match', 'preg_match_all', 'json_encode', 'stristr', 'is_numeric', 'array', 'zone_is_empty', 'min', 'max' ), $functions);
		$this->secure_dir = array_merge($this->secure_dir, $dirs);
	}
}

/**
 * extends Smarty.
 * 
 * Centralizing overrides here will avoid problems when upgrading to newer versions of the Smarty library.
 * @package TikiWiki\lib\init
 */
class Smarty_Tiki extends Smarty
{
	/**
	 * needs a proper description
	 * @var array|null
	 */
	public $url_overriding_prefix_stack = null;
	/**
	 * needs a proper description
	 * @var null
	 */
	public $url_overriding_prefix = null;
	/**
	 * needs a proper description
	 * @var null|string
	 */
	public $main_template_dir = null;

	/**
	 * needs a proper description
	 */
	function __construct()
	{
		parent::__construct();
		global $prefs;

		$this->initializePaths();

		$this->setConfigDir(null);
		if (! isset($prefs['smarty_compilation'])) {
			$prefs['smarty_compilation'] = '';
		}
		$this->compile_check = ( $prefs['smarty_compilation'] != 'never' );
		$this->force_compile = ( $prefs['smarty_compilation'] == 'always' );
		$this->assign('app_name', 'Tiki');

		if ( ! isset($prefs['smarty_security']) || $prefs['smarty_security'] == 'y' ) {
			$this->enableSecurity('Tiki_Security_Policy');
		} else {
			$this->disableSecurity();
		}
		$this->use_sub_dirs = false;
		$this->url_overriding_prefix_stack = array();
		if (!empty($prefs['smarty_notice_reporting']) and $prefs['smarty_notice_reporting'] === 'y' ) {
			$this->error_reporting = E_ALL;
		} else {
			$this->error_reporting = E_ALL ^ E_NOTICE;
		}
		if (!empty($prefs['smarty_cache_perms'])) {
			$this->_file_perms = (int) $prefs['smarty_cache_perms'];
		}

		$this->loadFilter('pre', 'tr');
		$this->loadFilter('pre', 'jq');

		include_once('lib/smarty_tiki/resource.tplwiki.php');
		$this->registerResource('tplwiki', array('smarty_resource_tplwiki_source', 'smarty_resource_tplwiki_timestamp', 'smarty_resource_tplwiki_secure', 'smarty_resource_tplwiki_trusted'));

		include_once('lib/smarty_tiki/resource.wiki.php');
		$this->registerResource('wiki', array('smarty_resource_wiki_source', 'smarty_resource_wiki_timestamp', 'smarty_resource_wiki_secure', 'smarty_resource_wiki_trusted'));

		global $prefs;
		// Assign the prefs array in smarty, by reference
		$this->assignByRef('prefs', $prefs);

		if ( !empty($prefs['log_tpl']) && $prefs['log_tpl'] === 'y' ) {
			$this->loadFilter('pre', 'log_tpl');
		}
		if ( !empty($prefs['feature_sefurl_filter']) && $prefs['feature_sefurl_filter'] === 'y' ) {
		  require_once ('tiki-sefurl.php');
		  $this->registerFilter('output', 'filter_out_sefurl');
		}

		// restore tiki's own escape function
		$this->loadPlugin('smarty_modifier_escape');
		$this->registerPlugin('modifier', 'escape', 'smarty_modifier_escape');
	}

	/**
	 * Fetch templates from plugins (smarty plugins, wiki plugins, modules, ...) that may need to :
	 * - temporarily override some smarty vars,
	 * - prefix their self_link / button / query URL arguments
	 * 
	 * @param      $_smarty_tpl_file
	 * @param null $override_vars
	 *
	 * @return string
	 */
	function plugin_fetch($_smarty_tpl_file, &$override_vars = null)
	{
		$smarty_orig_values = array();
		if ( is_array($override_vars) ) {
			foreach ( $override_vars as $k => $v ) {
				$smarty_orig_values[ $k ] = $this->getTemplateVars($k);
				$this->assignByRef($k, $override_vars[ $k ]);
			}
		}

		$return = $this->fetch($_smarty_tpl_file);

		// Restore original values of smarty variables
		if ( count($smarty_orig_values) > 0 ) {
			foreach ( $smarty_orig_values as $k => $v ) {
				$this->assignByRef($k, $smarty_orig_values[ $k ]);
			}
		}

		unset($smarty_orig_values);
		return $return;
	}

	/**
	 * needs a proper description
	 * @param null $_smarty_tpl_file
	 * @param null $_smarty_cache_id
	 * @param null $_smarty_compile_id
	 * @param null $parent
	 * @param bool $_smarty_display
	 * @param bool $merge_tpl_vars
	 * @param bool $no_output_filter
	 * @return string
	 */
	public function fetch($_smarty_tpl_file = null, $_smarty_cache_id = null, $_smarty_compile_id = null, $parent = null, $_smarty_display = false, $merge_tpl_vars = true, $no_output_filter = false)
	{
		global $prefs, $inclusion;
		$this->muteExpectedErrors();
		$this->refreshLanguage();

		if ( ($tpl = $this->getTemplateVars('mid')) && ( $_smarty_tpl_file == 'tiki.tpl' || $_smarty_tpl_file == 'tiki-print.tpl' || $_smarty_tpl_file == 'tiki_full.tpl' ) ) {

			// Set the last mid template to be used by AJAX to simulate a 'BACK' action
			if ( isset($_SESSION['last_mid_template']) ) {
				$this->assign('last_mid_template', $_SESSION['last_mid_template']);
				$this->assign('last_mid_php', $_SESSION['last_mid_php']);
			}
			$_SESSION['last_mid_template'] = $tpl;
			$_SESSION['last_mid_php'] = $_SERVER['REQUEST_URI'];

			// set the first part of the browser title for admin pages
			if ( null === $this->getTemplateVars('headtitle') ) {
				$script_name = basename($_SERVER['SCRIPT_NAME']);
				if ($script_name === 'route.php' && !empty($inclusion)) {
					$script_name = $inclusion;
				}
				if ($script_name != 'tiki-admin.php' && strpos($script_name, 'tiki-admin') === 0) {
					$str = substr($script_name, 10, strpos($script_name, '.php') - 10);
					$str = ucwords(trim(str_replace('_', ' ', $str)));
					$this->assign('headtitle', 'Admin ' . $str);
				} else if (strpos($script_name, 'tiki-list') === 0) {
					$str = substr($script_name, 9, strpos($script_name, '.php') - 9);
					$str = ucwords(trim(str_replace('_', ' ', $str)));
					$this->assign('headtitle', 'List ' . $str);
				} else if (strpos($script_name, 'tiki-view') === 0) {
					$str = substr($script_name, 9, strpos($script_name, '.php') - 9);
					$str = ucwords(trim(str_replace('_', ' ', $str)));
					$this->assign('headtitle', 'View ' . $str);
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

		} elseif ($_smarty_tpl_file == 'confirm.tpl' || $_smarty_tpl_file == 'error.tpl' || $_smarty_tpl_file == 'error_ticket.tpl' || $_smarty_tpl_file == 'error_simple.tpl') {
			ob_end_clean(); // Empty existing Output Buffer that may have been created in smarty before the call of this confirm / error* template
			if ( $prefs['feature_obzip'] == 'y' ) {
				ob_start('ob_gzhandler');
			}

		}

		if (! defined('TIKI_IN_INSTALLER')) {
			require_once 'tiki-modules.php';
		}
		
		$_smarty_tpl_file = $this->get_filename($_smarty_tpl_file);

		return parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, $parent, $_smarty_display);
	}

	/**
	 * needs a proper description
	 * @param $var
	 * @return Smarty_Internal_Data
	 */
	function clear_assign($var)
	{
		return parent::clearAssign($var);
	}

	/**
	 * needs a proper description
	 * @param $var
	 * @param $value
	 * @return Smarty_Internal_Data
	 */
	function assign_by_ref($var,&$value)
	{
		return parent::assignByRef($var, $value);
	}

	/**
	 * fetch in a specific language  without theme consideration
	 * @param      $lg
	 * @param      $_smarty_tpl_file
	 * @param null $_smarty_cache_id
	 * @param null $_smarty_compile_id
	 * @param bool $_smarty_display
	 * @return mixed
	 */
	function fetchLang($lg, $_smarty_tpl_file, $_smarty_cache_id = null, $_smarty_compile_id = null, $_smarty_display = false)
	{
		global $prefs;

		$_smarty_tpl_file = $this->get_filename($_smarty_tpl_file);

		$lgSave = $prefs['language'];
		$prefs['language'] = $lg;
		$this->refreshLanguage();
		$res = parent::fetch($_smarty_tpl_file, $_smarty_cache_id, $_smarty_compile_id, null, $_smarty_display);
		$prefs['language'] = $lgSave; // Restore the language of the user triggering the notification
		$this->refreshLanguage();

		return preg_replace("/^[ \t]*/", '', $res);
	}

	/**
	 * needs a proper description
	 * @param null   $resource_name
	 * @param null   $cache_id
	 * @param null   $compile_id
	 * @param null   $parent
	 * @param string $content_type
	 * @return Purified|void
	 */
	function display($resource_name = null, $cache_id=null, $compile_id = null, $parent = null, $content_type = 'text/html; charset=utf-8')
	{

		global $prefs;
		$this->muteExpectedErrors();
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

		/**
		 * By default, display is used with text/html content in UTF-8 encoding
		 * If you want to output other data from smarty,
		 * - either use fetch() / fetchLang()
		 * - or set $content_type to '' (empty string) or another content type.
		 */
		if ( $content_type != '' && ! headers_sent() ) {
			header('Content-Type: '.$content_type);
		}

		if (function_exists('current_object') && $obj = current_object()) {
			$attributes = TikiLib::lib('attribute')->get_attributes($obj['type'], $obj['object']);
			if (isset($attributes['tiki.object.layout'])) {
				$prefs['site_layout'] = $attributes['tiki.object.layout'];
			}
		}

		$this->refreshLanguage();

		TikiLib::events()->trigger('tiki.process.render', []);

		if ( !empty($prefs['feature_htmlpurifier_output']) and $prefs['feature_htmlpurifier_output'] == 'y' ) {
			return $purifier->purify(parent::display($resource_name, $cache_id, $compile_id));
		} else {
			return parent::display($resource_name, $cache_id, $compile_id);

		}
	}
	/**
	 * Returns the file path associated to the template name
	 * @param $template
	 * @return string
	 */
	function get_filename($template)
	{
		if (preg_match('/^[a-z]+\:/', $template) || file_exists($template)) {
			return $template;
		}
        //get the list of template directories
        $dirs = $this->getTemplateDir();

        //go through directories in search of the template
        foreach ($dirs as $dir){
            if (file_exists($dir.$template)){
                return $dir.$template;
            }
        }
        return "";

	}

	/**
	 * needs a proper description
	 * @param $url_arguments_prefix
	 * @param $arguments_list
	 */
	function set_request_overriders( $url_arguments_prefix, $arguments_list )
	{
		$this->url_overriding_prefix_stack[] = array( $url_arguments_prefix . '-', $arguments_list );
		$this->url_overriding_prefix =& $this->url_overriding_prefix_stack[ count($this->url_overriding_prefix_stack) - 1 ];
	}

	/**
	 * needs a proper description
	 * @param $url_arguments_prefix
	 * @param $arguments_list
	 */
	function remove_request_overriders( $url_arguments_prefix, $arguments_list )
	{
		$last_override_prefix = empty( $this->url_overriding_prefix_stack ) ? false : array_pop($this->url_overriding_prefix_stack);
		if ( ! is_array($last_override_prefix) || $url_arguments_prefix . '-' != $last_override_prefix[0] ) {
			trigger_error('URL Overriding prefix stack is in a bad state', E_USER_ERROR);
		}
		$this->url_overriding_prefix =& $this->url_overriding_prefix_stack[ count($this->url_overriding_prefix_stack) - 1 ];;
	}

	function refreshLanguage()
	{
		global $tikidomain, $prefs;

		$lang = $prefs['language'];
		if (empty($lang)) {
			$lang = 'default';
		}

		if (! empty($prefs['site_layout'])) {
			$layout = $prefs['site_layout'];
		} else {
			$layout = 'classic';
		}

		$this->setCompileId("$lang-$tikidomain-$layout");
		$this->initializePaths();
	}
	
	/*
	Add smarty template paths from where tpl files should be loaded. This function also gets called from lib/setup/theme.php to initialize theme specific paths
	*/
	function initializePaths()
	{
		global $prefs, $tikidomainslash, $section;

		if (! $this->main_template_dir) {
			// First run only
			$this->main_template_dir = TIKI_PATH . '/templates/';
			$this->setCompileDir(TIKI_PATH . "/templates_c");
			$this->setPluginsDir(
				array(	// the directory order must be like this to overload a plugin
					TIKI_PATH . '/' . TIKI_SMARTY_DIR,
					SMARTY_DIR.'plugins'
				)
			);
		}

		$this->setTemplateDir([]);

		// when called from release.php TikiLib isn't initialised so we can ignore the themes and addons
		if (class_exists('TikiLib')) {
			// Theme templates
			$themelib = TikiLib::lib('theme');
			if (!empty($prefs['theme']) && !in_array($prefs['theme'], ['custom_url'])) {
				$theme_path = $themelib->get_theme_path($prefs['theme'], $prefs['theme_option'], '', 'templates'); // path to the theme options
				$this->addTemplateDir(TIKI_PATH . "/$theme_path/");
				//if theme_admin is empty, use main theme and site_layout instead of site_layout_admin
				if ($section != "admin" || empty($prefs['theme_admin'])) {
					$this->addTemplateDir(TIKI_PATH . "/$theme_path/" . 'layouts/' . $prefs['site_layout'] . '/');
				} else {
					$this->addTemplateDir(TIKI_PATH . "/$theme_path/" . 'layouts/' . $prefs['site_layout_admin'] . '/');
				}
				$this->addTemplateDir(TIKI_PATH . "/$theme_path/" . 'layouts/');

				$main_theme_path = $themelib->get_theme_path($prefs['theme'], '', '', 'templates'); // path to the main theme
				$this->addTemplateDir(TIKI_PATH . "/$main_theme_path/");
				//if theme_admin is empty, use main theme and site_layout instead of site_layout_admin
				if ($section != "admin" || empty($prefs['theme_admin'])) {
					$this->addTemplateDir(TIKI_PATH . "/$main_theme_path/" . 'layouts/' . $prefs['site_layout'] . '/');
				} else {
					$this->addTemplateDir(TIKI_PATH . "/$main_theme_path/" . 'layouts/' . $prefs['site_layout_admin'] . '/');
				}
			}
			// Tikidomain main template folder
			if (!empty($tikidomainslash)) {
				$this->addTemplateDir(TIKI_PATH . "/themes/{$tikidomainslash}templates/"); // This dir is for all the themes in the tikidomain
				$this->addTemplatedir($this->main_template_dir . '/' . $tikidomainslash); // legacy tpls just in case, for example: /templates/mydomain.ltd/
			}

			$this->addTemplateDir(TIKI_PATH . "/themes/templates/"); //This dir stores templates for all the themes

			//Addon templates
			foreach (TikiAddons::getPaths() as $path) {
				$this->addTemplateDir($path . '/templates/');
			}
		}
		
		//Layout templates
		if (!empty($prefs['site_layout']) && ($section != "admin" || empty($prefs['theme_admin']))){ //use the admin layout if in the admin section
			$this->addTemplateDir($this->main_template_dir . '/layouts/' . $prefs['site_layout'] . '/');
		} elseif (!empty($prefs['site_layout_admin'])) {
			$this->addTemplateDir($this->main_template_dir . '/layouts/' . $prefs['site_layout_admin'] . '/');
		}
		$this->addTemplateDir($this->main_template_dir.'/layouts/');
		$this->addTemplateDir($this->main_template_dir);
	}
}
