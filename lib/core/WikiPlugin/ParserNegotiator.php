<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_ParserNegotiator
{
	var $parser;
	var $name;
	var $className;
	var $class;
	var $parserLevel = 0;
	var $argParser;
	var $args;
	var $body;
	var $info;
	var $fingerprint;
	var $page;
	var $exists;
	var $prefs;
	var $parserOption;
	var $index;
	var $key;
	var $result;

	static $pluginIndexes = array();
	static $parserLevels = array();
	static $currentParserLevel = 0;
	static $pluginsAwaitingExecution = array();

	function __construct(& $parser, & $pluginDetails, & $page, & $prefs, & $parserOption)
	{
		$this->parser = & $parser;
		$this->name = strtolower($pluginDetails['name']);
		$this->className = 'WikiPlugin_' . $this->name;

		if (@class_exists($this->className)) {
			$this->class = new $this->className;
		}

		$this->argParser = new WikiParser_PluginArgumentParser;
		$this->args = $this->argParser->parse($pluginDetails['args']);
		$this->body = & $pluginDetails['body'];
		$this->info = $this->info();
		$this->fingerprint = $this->fingerprint();
		$this->page = & $page;
		$this->exists = $this->exists();
		$this->prefs = & $prefs;
		$this->parserOption = & $parserOption;

		$this->index = $this->incrementIndex();

		$this->key = '§' . md5('plugin:' . $this->name . '_' . $this->index) . '§';
	}

	private function incrementIndex()
	{
		if (isset(self::$pluginIndexes[$this->name]) == false) self::$pluginIndexes[$this->name] = 0;

		self::$pluginIndexes[$this->name]++;

		return self::$pluginIndexes[$this->name];
	}

	function execute()
	{
		$output = '';
		if ($this->enabled($output) == false) {
			return $output->toHtml();
		}

		if (empty($this->result) == false) return $this->result;

		if (isset($this->class)) {
			if (isset($this->class->parserLevel)) {
				$this->parserLevel = $this->class->parserLevel;

				if($this->class->parserLevel > self::$currentParserLevel) {
					$this->addWaitingPlugin();
					return $this->key;
				} else {

					$this->applyFilters();
					$this->result = $this->class->exec($this->body, $this->args, $this->index, $this->parser, $this->button(false));
					return $this->result;
				}
			}
		}

		$fnName = strtolower('wikiplugin_' .  $this->name);

		if ( $this->exists && function_exists($fnName) ) {

			$this->result = $fnName($this->body, $this->args, $this->index, $this) . $this->button();

			return $this->result;
		}

		return $this->body;
	}

	private function addWaitingPlugin()
	{
		self::$parserLevels[] = $this->class->parserLevel;
		self::$pluginsAwaitingExecution[$this->key] = $this;
	}

	private function exists()
	{
		$phpName = 'lib/wiki-plugins/wikiplugin_';
		$phpName .= strtolower($this->name) . '.php';

		$exists = file_exists($phpName);

		if ( $exists ) {
			include_once $phpName;
		}

		if ( $exists ) {
			return true;
		}

		return false;
	}

	function canExecute( $dontModify = false )
	{
		global $tikilib;
		// If validation is disabled, anything can execute
		if ( $this->parser->prefs['wiki_validate_plugin'] != 'y' ) {
			return true;
		}

		if ( ! isset( $this->info['validate'] ) ) {
			return true;
		}

		$val = $this->fingerprintCheck($dontModify);

		switch($val) {
			case 'accept':
				return true;
				break;
			case 'reject':
				return 'rejected';
				break;
			default:
				global $tiki_p_plugin_approve, $tiki_p_plugin_preview;
				if (
					isset($_SERVER['REQUEST_METHOD'])
					&& $_SERVER['REQUEST_METHOD'] == 'POST'
					&& isset( $_POST['plugin_fingerprint'] )
					&& $_POST['plugin_fingerprint'] == $this->fingerprint
				) {
					if ( $tiki_p_plugin_approve == 'y' ) {
						if ( isset( $_POST['plugin_accept'] ) ) {
							$this->fingerprintStore('accept');
							$tikilib->invalidate_cache($this->page);
							return true;
						} elseif ( isset( $_POST['plugin_reject'] ) ) {
							$this->fingerprintStore('reject');
							$tikilib->invalidate_cache($this->page);
							return 'rejected';
						}
					}

					if ( $tiki_p_plugin_preview == 'y'
						&& isset( $_POST['plugin_preview'] ) ) {
						return true;
					}
				}

				return $this->fingerprint;
		}
	}

	function enabled(& $output)
	{
		if ( ! $this->info )
			return true; // Legacy plugins always execute

		global $prefs;

		$missing = array();

		if ( isset( $this->info['prefs'] ) ) {
			foreach ( $this->info['prefs'] as $pref ) {
				if ( $prefs[$pref] != 'y' ) {
					$missing[] = $pref;
				}
			}
		}

		if ( count($missing) > 0 ) {
			$output = WikiParser_PluginOutput::disabled($this->name, $missing);
			return false;
		}

		return true;
	}

	function isEditable()
	{
		global $tiki_p_edit, $prefs, $section;

		return (
			$section == 'wiki page' &&
			isset($this->info) &&
			$tiki_p_edit == 'y' &&
			$prefs['wiki_edit_plugin'] == 'y' &&
			!$this->isInline()
		);
	}

	private function isInline()
	{
		if ( ! $this->info )
			return true; // Legacy plugins always inline

		if ( isset( $this->info['inline'] ) && $this->info['inline'] )
			return true;

		$inline_pref = 'wikiplugininline_' .  $this->name;
		if ( isset( $this->prefs[ $inline_pref ] ) && $this->prefs[ $inline_pref ] == 'y' )
			return true;

		return false;
	}

	private function fingerprintStore( $type )
	{
		global $tikilib;

		if ( $this->page ) {
			$objectType = 'wiki page';
			$objectId = $this->page;
		} else {
			$objectType = '';
			$objectId = '';
		}

		$pluginSecurity = $tikilib->table('tiki_plugin_security');
		$pluginSecurity->delete(array('fingerprint' => $this->fingerprint));
		$pluginSecurity->insert(array(
			'fingerprint' => $this->fingerprint,
			'status' => $type,
			'added_by' => $this->user,
			'last_objectType' => $objectType,
			'last_objectId' => $objectId
		));
	}

	public function info()
	{
		static $known = array();

		if ( isset( $known[$this->name] ) ) {
			return $known[$this->name];
		}

		if (isset($this->class)) {
			$known[$this->name] = $this->class->info();
			$known[$this->name]['perams'] = array_merge($known[$this->name]['perams'], $this->class->style());
		}

		if ( ! $this->exists )
			return $known[$this->name] = false;

		$func_name_info = "wikiplugin_{$this->name}_info";

		if ( ! function_exists($func_name_info) ) {
			if ( $info = $this->aliasInfo() ) {
				return $known[$this->name] = $info['description'];
			} else {
				return $known[$this->name] = false;
			}
		}

		return $known[$this->name] = $func_name_info();
	}

	function aliasInfo()
	{
		global $prefs;

		if (empty($this->name)) return false;

		$prefName = "pluginalias_" . $this->name;

		if ( ! isset( $prefs[$prefName] ) ) return false;

		return @unserialize($prefs[$prefName]);
	}

	private function fingerprint()
	{
		$validate = (isset($this->info['validate']) ? $this->info['validate'] : '');

		if ( $validate == 'all' || $validate == 'body' )
			$validateBody = str_replace('<x>', '', $this->body);	// de-sanitize plugin body to make fingerprint consistant with 5.x
		else
			$validateBody = '';

		if ( $validate == 'all' || $validate == 'arguments' ) {
			$validateArgs = $this->args;

			// Remove arguments marked as safe from the fingerprint
			foreach ( $this->info['params'] as $key => $info ) {
				if ( isset( $validateArgs[$key] )
					&& isset( $info['safe'] )
					&& $info['safe']
				) {
					unset($validateArgs[$key]);
				}
			}
			// Parameter order needs to be stable
			ksort($validateArgs);

			if (empty($validateArgs)) {
				$validateArgs = array( '' => '' );	// maintain compatibility with pre-Tiki 7 fingerprints
			}
		} else {
			$validateArgs = array();
		}

		$bodyLen = str_pad(strlen($validateBody), 6, '0', STR_PAD_RIGHT);
		$serialized = serialize($validateArgs);
		$argsLen = str_pad(strlen($serialized), 6, '0', STR_PAD_RIGHT);

		$bodyHash = md5($validateBody);
		$argsHash = md5($serialized);

		return "$this->name-$bodyHash-$argsHash-$bodyLen-$argsLen";
	}

	private function fingerprintCheck( $dontModify = false )
	{
		global $tikilib;
		$limit = date('Y-m-d H:i:s', time() - 15*24*3600);
		$result = $tikilib->query("
			SELECT status, if(status='pending' AND last_update < ?, 'old', '') flag
			FROM tiki_plugin_security
			WHERE fingerprint = ?
		", array( $limit, $this->fingerprint ));

		$needUpdate = false;

		if ( $row = $result->fetchRow() ) {
			$status = $row['status'];
			$flag = $row['flag'];

			if ( $status == 'accept' || $status == 'reject' ) {
				return $status;
			}

			if ( $flag == 'old' ) {
				$needUpdate = true;
			}
		} else {
			$needUpdate = true;
		}

		if ( $needUpdate && !$dontModify ) {
			if ( $this->page ) {
				$objectType = 'wiki page';
				$objectId = $this->page;
			} else {
				$objectType = '';
				$objectId = '';
			}


			$pluginSecurity = $tikilib->table('tiki_plugin_security');
			$pluginSecurity->delete(array('fingerprint' => $this->fingerprint));
			$pluginSecurity->insert(array(
				'fingerprint' => $this->fingerprint,
				'status' => 'pending',
				'added_by' => $this->user,
				'last_objectType' => $objectType,
				'last_objectId' => $objectId
			));
		}

		return '';
	}

	private function applyFilters()
	{
		global $tikilib;

		$default = TikiFilter::get(isset( $this->info['defaultfilter'] ) ? $this->info['defaultfilter'] : 'xss');

		// Apply filters on the body
		$filter = isset($this->info['filter']) ? TikiFilter::get($this->info['filter']) : $default;
		$this->body = $filter->filter($this->body);

		if (isset($this->option) && (!empty($this->option['is_html']) && (!$this->option['is_html']))) {
			$noparsed = array('data' => array(), 'key' => array());
			$this->strip_unparsed_block($this->body, $noparsed);
			$body = str_replace(array('<', '>'), array('&lt;', '&gt;'), $this->body);
			foreach ($noparsed['data'] as &$instance) {
				$instance = '~np~' . $instance . '~/np~';
			}
			unset($instance);
			$this->body = str_replace($noparsed['key'], $noparsed['data'], $body);
		}

		// Make sure all arguments are declared
		$params = & $this->info['params'];
		if ( ! isset( $this->info['extraparams'] ) && is_array($params) ) {
			$this->args = array_intersect_key($this->args, $params);
		}

		// Apply filters on values individually
		if (!empty($this->args)) {
			foreach ( $this->args as $argKey => &$argValue ) {
				$paramInfo = $params[$argKey];
				$filter = isset($paramInfo['filter']) ? TikiFilter::get($paramInfo['filter']) : $default;
				$argValue = TikiLib::htmldecode($argValue);

				if ( isset($paramInfo['separator']) ) {
					$vals = $tikilib->array_apply_filter($tikilib->multi_explode($paramInfo['separator'], $argValue), $filter);

					$argValue = array_values($vals);
				} else {
					$argValue = $filter->filter($argValue);
				}
			}
		}
	}

	function button($wrapInNp = true)
	{
		global $headerlib, $smarty;

		if (
			$this->isEditable() &&
			(
				empty($this->parserOption['preview_mode']) ||
				!$this->parserOption['preview_mode']
			) &&
			empty($this->parserOption['indexing']) &&
			(
				empty($this->parserOption['print']) ||
				!$this->parserOption['print']
			) &&
			!$this->parserOption['suppress_icons']
		) {
			$id = 'plugin-edit-' . $this->name . $this->index;
			$iconDisplayStyle = '';
			if (
				$this->prefs['wiki_edit_icons_toggle'] == 'y' &&
				(
					$this->prefs['wiki_edit_plugin'] == 'y' || $this->prefs['wiki_edit_section'] == 'y'
				)
			) {
				if (!isset($_COOKIE['wiki_plugin_edit_view'])) {
					$iconDisplayStyle = ' style="display:none;"';
				}
			}

			$headerlib->add_jsfile('tiki-jsplugin.php?language='.$this->prefs['language'], 'dynamic');
			if ($this->prefs['wikiplugin_module'] === 'y' && $this->prefs['wikiplugininline_module'] === 'n') {
				$headerlib->add_jsfile('tiki-jsmodule.php?language='.$this->prefs['language'], 'dynamic');
			}
			$headerlib->add_jq_onready('
$("#' . $id . '").click( function(event) {
	$.getJSON("tiki-ajax_services.php", {
		page: "' .$this->page. '",
		key: "' . $this->key . '",
		controller: "jison",
		action: "pluginbody"
	}, function(o) {
		popup_plugin_form('
				. json_encode('editwiki')
				. ', '
				. json_encode($this->name)
				. ', '
				. json_encode($this->index)
				. ', '
				. json_encode($this->page)
				. ', '
				. json_encode($this->args)
				. ', o.body, event.target);
	});
	return false;
});
');
			include_once('lib/smarty_tiki/function.icon.php');

			$button = '<a id="' .$id. '" class="editplugin"'.$iconDisplayStyle.'>'.smarty_function_icon(array('_id'=>'wiki_plugin_edit', 'alt'=>tra('Edit Plugin').':'.$this->name), $smarty)."</a>";

			if ($wrapInNp == false) return $button;

			return '~np~' . $button . '~/np~';
		}

		return '';
	}

	function blockFromExecution($status = '')
	{
		global $smarty;
		$smarty->assign('plugin_fingerprint', $status);
		$smarty->assign('plugin_name', $this->name);
		$smarty->assign('plugin_index', 0);
		$smarty->assign('plugin_status', $status);

		global $tiki_p_plugin_viewdetail, $tiki_p_plugin_preview, $tiki_p_plugin_approve;
		$details = $tiki_p_plugin_viewdetail == 'y' && $status != 'rejected';
		$preview = $tiki_p_plugin_preview == 'y' && $details && ! $this->parserOption['preview_mode'];
		$approve = $tiki_p_plugin_approve == 'y' && $details && ! $this->parserOption['preview_mode'];

		if ($this->parserOption['inside_pretty']) {
			$smarty->assign('plugin_details', '');
		} else {
			$smarty->assign('plugin_details', $details);
		}

		$smarty->assign('plugin_preview', $preview);
		$smarty->assign('plugin_approve', $approve);

		$smarty->assign('plugin_body', $this->body);
		$smarty->assign('plugin_args', $this->args);

		return $smarty->fetch('tiki-plugin_blocked.tpl');
	}
}