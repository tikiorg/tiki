<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_Wiki_Handler extends JisonParser_Wiki
{
	var $parsing = false;
	var $usePrePostHandlers = true;

	var $origPluginBody = array();
	public static $hdrCount = 0;
	public static $spareParsers = array();

	var $npOn = false;
	var $pluginStack = array();
	public static $pluginCount = 0;
	var $blockLoc = array();
	var $blockLast = '';
	var $blockStack = array();
	var $olistLen = array();

	var $npEntries = array();
	var $npCount = 0;
	var $pluginEntries = array();

	public static $headerStack = array();
	public static $pluginsExecutedStack = array();

	//This var is used in both protectSpecialChars and unprotectSpecialChars to simplify the html ouput process
	var $specialChars = array(
		'≤REAL_LT≥' => array(
			'html'=>		'<',
			'nonHtml'=>		'&lt;'
		),
		'≤REAL_GT≥' => array(
			'html'=>		'>',
			'nonHtml'=>		'&gt;'
		),
		'≤REAL_NBSP≥' => array(
			'html'=>		'&nbsp;',
			'nonHtml'=>		'&nbsp;'
		),
		/*on post back the page is parsed, which turns & into &amp;
		this is done to prevent that from happening, we are just
		protecting some chars from letting the parser nab them*/
		'≤REAL_AMP≥' => array(
			'html'=>		'& ',
			'nonHtml'=>		'& '
		),
	);

	var $tikilib;
	var $user;
	var $prefs;
	var $page;

	public static $option = array();
	public function setOption($option = array())
	{
		$page = $_REQUEST['page'];

		self::$option = array_merge(array(
			'skipvalidation'=>  false,
			'is_html'=> false,
			'absolute_links'=> false,
			'language' => '',
			'noparseplugins' => false,
			'stripplugins' => false,
			'noheaderinc' => false,
			'page' => $page,
			'print' => false,
			'parseimgonly' => false,
			'preview_mode' => false,
			'suppress_icons' => false,
			'parsetoc' => true,
			'inside_pretty' => false,
			'process_wiki_paragraphs' => true,
			'min_one_paragraph' => false,
		), $option);
	}

	var $addLineBreaksTracking = array(
		'inTable' => 0,
		'inPre' => 0,
		'inComment' => 0,
		'inTOC' => 0,
		'inScript' => 0,
		'inDiv' => 0,
		'inHeader' => 0
	);

	function __construct()
	{
		global $tikilib, $page, $user, $prefs;
		$this->tikilib = $tikilib;
		$this->page = $page;
		$this->user = (isset($user) ? $user : tra('Anonymous'));
		$this->prefs = $prefs;
		parent::__construct();
	}

	function parse($input)
	{
		if ($this->parsing == true) {
			$parser = end(self::$spareParsers);
			if (!empty($parser) && $parser->parsing == false) {
				$result = $parser->parse($input);
			} else {
				self::$spareParsers[] = $parser = new JisonParser_Wiki_Handler();
				$result = $parser->parse($input);
			}
		} else {
			$this->parsing = true;

			if (empty(self::$option)) $this->setOption();

			if ($this->usePrePostHandlers == true) {
				$this->preParse($input);
			}

			$result = parent::parse($input);

			if ($this->usePrePostHandlers == true) {
				$this->postParse($result);
			}

			$this->parsing = false;
		}

		return $result;
	}

	function parsePlugin($input)
	{
		$is_html = self::$option['is_html'];
		$this->setOption(array('is_html'=> true));
		$result = $this->parse($input);
		$this->setOption(array('is_html'=> $is_html));
		return $result;
	}

	function preParse(&$input)
	{
		$input = preg_replace_callback('/~np~(.|\n)*?~\/np~/', array(&$this, 'removeNpEntities'), $input);
		$input = $this->protectSpecialChars($input);
	}

	function postParse(&$input)
	{
		$lines = explode("\n", $input);

		$ul = '';
		$listBeginnings = array();
		$listBeginnings = array();
		foreach($lines as &$line) {
			$this->parseLists($line, $listBeginnings, $ul);
			$this->addLineBreaks($line);
		}
		$input = implode("\n", $lines);

		$this->restoreNpEntities($input);
		$this->restorePluginEntities($input);
		$input = $this->unprotectSpecialChars($input, self::$option['is_html']);
	}

	// state & plugin handlers
	function plugin($pluginDetails)
	{
		global $smarty;

		$argParser = new WikiParser_PluginArgumentParser;
		$args = $argParser->parse($pluginDetails['args']);

		if (!self::$option['skipvalidation']) {
			$status = $this->pluginCanExecute(strtolower($pluginDetails['name']), $pluginDetails['body'], $args);
		} else {
			$status = true;
		}

		$key = '§' . md5('plugin:'.self::$pluginCount) . '§';
		self::$pluginCount++;

		if ($status === true) {
			$pluginDetails['body'] = $this->unprotectSpecialChars($pluginDetails['body'], true);

			$this->pluginEntries[$key] = $this->pluginExecute(
				$pluginDetails['name'],
				$args,
				$pluginDetails['body'],
				$key
			);

			$this->pluginEntries[$key] = $this->parsePlugin( $this->pluginEntries[$key] );

		} else {
			$smarty->assign('plugin_fingerprint', $status);
			$smarty->assign('plugin_name', $pluginDetails['name']);
			$smarty->assign('plugin_index', 0);

			$smarty->assign('plugin_status', $status);

			global $tiki_p_plugin_viewdetail, $tiki_p_plugin_preview, $tiki_p_plugin_approve;
			$details = $tiki_p_plugin_viewdetail == 'y' && $status != 'rejected';
			$preview = $tiki_p_plugin_preview == 'y' && $details && ! self::$option['preview_mode'];
			$approve = $tiki_p_plugin_approve == 'y' && $details && ! self::$option['preview_mode'];

			if (self::$option['inside_pretty']) {
				$smarty->assign('plugin_details', '');
			} else {
				$smarty->assign('plugin_details', $details);
			}

			$smarty->assign('plugin_preview', $preview);
			$smarty->assign('plugin_approve', $approve);

			$smarty->assign('plugin_body', $pluginDetails['body']);
			$smarty->assign('plugin_args', $args);

			$this->pluginEntries[$key] = $smarty->fetch('tiki-plugin_blocked.tpl');
		}

		return $key;
	}

	function stackPlugin($yytext)
	{
		$pluginName = $this->match('/^\{([A-Z]+)/', $yytext);
		$pluginArgs = rtrim(str_replace('{' . $pluginName . '(', '', $yytext), ')}');

		$this->pluginStack[] = array(
			'name' => $pluginName,
			'args' => $pluginArgs,
			'body' => ''
		);
	}

	function inlinePlugin($yytext)
	{
		$pluginName = $this->match('/^\{([a-z]+)/', $yytext);
		$pluginArgs = rtrim(str_replace('{'.$pluginName .' ', '', $yytext), '}');

		return array(
			'name' => $pluginName,
			'args' => $pluginArgs,
			'body' => ''
		);
	}

	function isPlugin()
	{
		return (count($this->pluginStack) > 0);
	}

	function pluginExecute($name, $args = array(), $body = "", $key)
	{
		$name = strtolower($name);
		if (!isset(self::$pluginsExecutedStack[$name])) {
			self::$pluginsExecutedStack[$name] == 0;
		}
		self::$pluginsExecutedStack[$name]++;

		$fnName = strtolower('wikiplugin_' .  $name);

		if ( $this->pluginExists($name) && function_exists($fnName) ) {

			$result =
				$fnName($body, $args, self::$pluginsExecutedStack[$name], $this).

				$this->pluginButton(
					$name,
					$args,
					$key
				);

			return $result;
		}

		return $body;
	}

	function pluginExists($name)
	{
		$phpName = 'lib/wiki-plugins/wikiplugin_';
		$phpName .= strtolower($name) . '.php';

		$exists = file_exists($phpName);

		if ( $exists ) {
			include_once $phpName;
		}

		if ( $exists ) {
			return true;
		}

		return false;
	}

	function pluginCanExecute( $name, $data = '', $args = array(), $dontModify = false )
	{
		// If validation is disabled, anything can execute
		if ( $this->prefs['wiki_validate_plugin'] != 'y' ) {
			return true;
		}

		$meta = $this->pluginInfo($name);

		if ( ! isset( $meta['validate'] ) ) {
			return true;
		}

		$fingerprint = $this->pluginFingerprint($name, $meta, $data, $args);

		$val = $this->pluginFingerprintCheck($fingerprint, $dontModify);

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
					&& $_POST['plugin_fingerprint'] == $fingerprint
				) {
					if ( $tiki_p_plugin_approve == 'y' ) {
						if ( isset( $_POST['plugin_accept'] ) ) {
							$this->pluginFingerprintStore($fingerprint, 'accept');
							$this->tikilib->invalidate_cache($this->page);
							return true;
						} elseif ( isset( $_POST['plugin_reject'] ) ) {
							$this->pluginFingerprintStore($fingerprint, 'reject');
							$this->tikilib->invalidate_cache($this->page);
							return 'rejected';
						}
					}

					if ( $tiki_p_plugin_preview == 'y'
						&& isset( $_POST['plugin_preview'] ) ) {
						return true;
					}
				}

				return $fingerprint;
		}
	}

	function pluginIsEditable( $name )
	{
		global $tiki_p_edit, $prefs, $section;
		$info = $this->pluginInfo($name);
		// note that for 3.0 the plugin editor only works in wiki pages, but could be extended later
		return $section == 'wiki page' && $info && $tiki_p_edit == 'y' && $prefs['wiki_edit_plugin'] == 'y'
			&& !$this->pluginIsInline($name);
	}

	function pluginIsInline( $name )
	{
		if ( ! $meta = $this->pluginInfo($name) )
			return true; // Legacy plugins always inline

		$inline = false;
		if ( isset( $meta['inline'] ) && $meta['inline'] )
			return true;

		$inline_pref = 'wikiplugininline_' .  $name;
		if ( isset( $this->prefs[ $inline_pref ] ) && $this->prefs[ $inline_pref ] == 'y' )
			return true;

		return false;
	}

	function pluginFingerprintStore( $fingerprint, $type )
	{
		if ( $this->page ) {
			$objectType = 'wiki page';
			$objectId = $this->page;
		} else {
			$objectType = '';
			$objectId = '';
		}

		$pluginSecurity = $this->tikilib->table('tiki_plugin_security');
		$pluginSecurity->delete(array('fingerprint' => $fingerprint));
		$pluginSecurity->insert(array(
			'fingerprint' => $fingerprint,
			'status' => $type,
			'added_by' => $this->user,
			'last_objectType' => $objectType,
			'last_objectId' => $objectId
		));
	}

	function pluginInfo( $name )
	{
		static $known = array();

		if ( isset( $known[$name] ) ) {
			return $known[$name];
		}

		if ( ! $this->pluginExists($name, true) )
			return $known[$name] = false;

		$func_name_info = "wikiplugin_{$name}_info";

		if ( ! function_exists($func_name_info) ) {
			if ( $info = $this->pluginAliasInfo($name) )
				return $known[$name] = $info['description'];
			else
				return $known[$name] = false;
		}

		return $known[$name] = $func_name_info();
	}

	function pluginAliasInfo( $name )
	{
		global $prefs;

		if (empty($name)) return false;

		$name = TikiLib::strtolower($name);
		$prefName = "pluginalias_$name";

		if ( ! isset( $prefs[$prefName] ) ) return false;

		return @unserialize($prefs[$prefName]);
	}

	function pluginFingerprint( $name, $meta, $data, $args )
	{
		$validate = (isset($meta['validate']) ? $meta['validate'] : '');

		$data = $this->unprotectSpecialChars($data, true);

		if ( $validate == 'all' || $validate == 'body' )
			$validateBody = str_replace('<x>', '', $data);	// de-sanitize plugin body to make fingerprint consistant with 5.x
		else
			$validateBody = '';

		if ( $validate == 'all' || $validate == 'arguments' ) {
			$validateArgs = $args;

			// Remove arguments marked as safe from the fingerprint
			foreach ( $meta['params'] as $key => $info ) {
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

		return "$name-$bodyHash-$argsHash-$bodyLen-$argsLen";
	}

	function pluginFingerprintCheck( $fingerprint, $dontModify = false )
	{
		$limit = date('Y-m-d H:i:s', time() - 15*24*3600);
		$result = $this->tikilib->query("
			SELECT status, if(status='pending' AND last_update < ?, 'old', '') flag
			FROM tiki_plugin_security
			WHERE fingerprint = ?
		", array( $limit, $fingerprint ));

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
			global $page;
			if ( $page ) {
				$objectType = 'wiki page';
				$objectId = $page;
			} else {
				$objectType = '';
				$objectId = '';
			}


			$pluginSecurity = $this->tikilib->table('tiki_plugin_security');
			$pluginSecurity->delete(array('fingerprint' => $fingerprint));
			$pluginSecurity->insert(array(
				'fingerprint' => $fingerprint,
				'status' => 'pending',
				'added_by' => $this->user,
				'last_objectType' => $objectType,
				'last_objectId' => $objectId
			));
		}

		return '';
	}

	function removeNpEntities(&$matches)
	{
		$key = '§' . md5('np:'.$this->npCount) . '§';
		$this->npEntries[$key] = substr($matches[0], 4, -5);
		$this->npCount++;
		return $key;
	}

	function restoreNpEntities(&$input, $keep = false)
	{
		foreach($this->npEntries as $key => $entity) {
			$input = str_replace($key, $entity, $input);

			if (!$keep) {
				unset($this->npEntries[$key]);
			}
		}
	}

	function restorePluginEntities(&$input, $keep = false)
	{
		//use of array_reverse, jison is a reverse bottom-up parser, if it doesn't reverse jison doesn't restore the plugins in the right order, leaving the some nested keys as a result
		foreach(array_reverse($this->pluginEntries) as $key => $entity) {
			$input = str_replace($key, $entity, $input);

			if (!$keep) {
				unset($this->pluginEntries[$key]);
			}
		}
	}

	function addLineBreaks(&$line)
	{
		$lineInLowerCase = TikiLib::strtolower($line);

		$this->addLineBreaksTracking['inComment'] += substr_count($lineInLowerCase, "<!--");
		$this->addLineBreaksTracking['inComment'] -= substr_count($lineInLowerCase, "-->");

		// check if we are inside a ~pre~ block and, if so, ignore
		// monospaced and do not insert <br />
		$this->addLineBreaksTracking['inPre'] += substr_count($lineInLowerCase, "<pre");
		$this->addLineBreaksTracking['inPre'] -= substr_count($lineInLowerCase, "</pre");

		// check if we are inside a table, if so, ignore monospaced and do
		// not insert <br />

		$this->addLineBreaksTracking['inTable'] += substr_count($lineInLowerCase, "<table");
		$this->addLineBreaksTracking['inTable'] -= substr_count($lineInLowerCase, "</table");

		// check if we are inside an ul TOC list, if so, ignore monospaced and do
		// not insert <br />
		$this->addLineBreaksTracking['inTOC'] += substr_count($lineInLowerCase, "<ul class=\"toc");
		$this->addLineBreaksTracking['inTOC'] -= substr_count($lineInLowerCase, "</ul><!--toc-->");

		// check if we are inside a script not insert <br />
		$this->addLineBreaksTracking['inScript'] += substr_count($lineInLowerCase, "<script");
		$this->addLineBreaksTracking['inScript'] -= substr_count($lineInLowerCase, "</script");

		// check if we are inside a script not insert <br />
		$this->addLineBreaksTracking['inDiv'] += substr_count($lineInLowerCase, "<div");
		$this->addLineBreaksTracking['inDiv'] -= substr_count($lineInLowerCase, "</div");

		// check if we are inside a script not insert <br />
		$this->addLineBreaksTracking['inHeader'] += substr_count($lineInLowerCase, "<h");
		$this->addLineBreaksTracking['inHeader'] -= substr_count($lineInLowerCase, "</h");

		if (
			$this->addLineBreaksTracking['inTable'] == 0 &&
			$this->addLineBreaksTracking['inPre'] == 0 &&
			$this->addLineBreaksTracking['inComment'] == 0 &&
			$this->addLineBreaksTracking['inTOC'] == 0 &&
			$this->addLineBreaksTracking['inScript'] == 0 &&
			$this->addLineBreaksTracking['inDiv'] == 0 &&
			$this->addLineBreaksTracking['inHeader'] == 0
		) {
			$line .= '<br />';
		}
	}

	function parseLists(&$line = "", &$listBeginnings = array(), &$data = '')
	{
		$isStart = empty($data);

		$liType = substr($line, 0, 1);

		if (
			($liType == '*' || $liType == '#') &&
			!(strlen($line)-count($listBeginnings)>4 &&
			preg_match('/^\*+$/', $line))
		) {
			$listLevel = $this->tikilib->how_many_at_start($line, $liType);
			$liClose = '</li>';
			$addRemove = 0;

			if ($listLevel < count($listBeginnings)) {
				while ($listLevel != count($listBeginnings)) {
					$data .= array_shift($listBeginnings);
				}

				if (substr(current($listBeginnings), 0, 5) != '</li>') {
					$liClose = '';
				}

			} elseif ($listLevel > count($listBeginnings)) {
				$liStyle = '';

				while ($listLevel != count($listBeginnings)) {
					array_unshift($listBeginnings, ($liType == '*' ? '</ul>' : '</ol>'));

					if ($listLevel == count($listBeginnings)) {
						$liState = substr($line, $listLevel, 1);

						if (
							($liState == '+' || $liState == '-') &&
							!(
								$liType == '*' &&
								!strstr(current($listBeginnings), '</ul>') ||
								$liType == '#' &&
								!strstr(current($listBeginnings), '</ol>')
							)
						) {
							$thisId = 'id' . microtime() * 1000000;
							$liStyle = ' id="' . $thisId . '" style="display:' . ($liState == '+' ? 'block' : 'none') . ';"';
							$addRemove = 1;
						}
					}

					$data .= ( $liType=='*' ? "<ul$liStyle>" : "<ol$liStyle>" );
				}
				$liClose='';
			}

			if (
				$liType == '*' && !strstr(current($listBeginnings), '</ul>') ||
				$liType == '#' && !strstr(current($listBeginnings), '</ol>')
			) {
				$data .= array_shift($listBeginnings);
				$liStyle = '';
				$liState = substr($line, $listLevel, 1);

				if (($liState == '+' || $liState == '-')) {
					$thisId = 'id' . microtime() * 1000000;
					$liStyle = ' id="' . $thisId . '" style="display:' . ($liState == '+' ? 'block' : 'none') . ';"';
					$addRemove = 1;
				}

				$data .= ( $liType == '*' ? "<ul$liStyle>" : "<ol$liStyle>" );
				$liClose = '';
				array_unshift($listBeginnings, ($liType == '*' ? '</li></ul>' : '</li></ol>'));
			}

			$line = $liClose . '<li>' . substr($line, $listLevel + $addRemove);

			if (substr(current($listBeginnings), 0, 5) != '</li>') {
				array_unshift($listBeginnings, '</li>' . array_shift($listBeginnings));
			}

		} elseif ($liType == '+') {
			$listLevel = TikiLib::how_many_at_start($line, $liType);
			// Close lists down to requested level
			while ($listLevel < count($listBeginnings)) {
				$data .= array_shift($listBeginnings);
			}

			// Must append paragraph for list item of given depth...
			$listLevel = TikiLib::how_many_at_start($line, $liType);
			if (count($listBeginnings)) {
				if (substr(current($listBeginnings), 0, 5) != '</li>') {
					array_unshift($listBeginnings, '</li>' . array_shift($listBeginnings));
					$liClose = '<li>';
				} else {
					$liClose = '<br />';
				}
			} else {
				$liClose = '';
			}

			$line = $liClose . substr($line, count($listBeginnings));

		} else {
			//we are either at the end of a list, or in a regular line
			$line = implode($listBeginnings) . $line;
			$listBeginnings =  array();
		}

		if ($isStart) {
			//We know we are at the start of an UL, so prepend it
			$line = $data . $line;
			$data = '';
		}
	}

	function SOL() //start of line
	{
		return ($this->yyloc['first_column'] == 0 ? true : false);
	}

	function popAllStates()
	{
		$this->conditionStackCount = 0;
		$this->conditionStack = array();
	}

	// This function handles the protection of html entities so that they are not mangled when
	// parse_htmlchar runs, and as well so they can be properly seen, be it html or non-html
	function protectSpecialChars($data)
	{
		if (
			$this->isHtmlPurifying == true ||
			self::$option['is_html'] != true
		) {
			foreach($this->specialChars as $key => $specialChar) {
				$data = str_replace($specialChar['html'], $key, $data);
			}
		}

		return $data;
	}

	// This function removed the protection of html entities so that they are rendered as expected by the viewer
	function unprotectSpecialChars($data, $is_html = false)
	{
		if (
			$is_html == true ||
			self::$option['is_html'] == true
		) {
			foreach($this->specialChars as $key => $specialChar) {
				$data = str_replace($key, $specialChar['html'], $data);
			}
		} else {
			foreach($this->specialChars as $key => $specialChar) {
				$data = str_replace($key, $specialChar['nonHtml'], $data);
			}
		}

		return $data;
	}

	function beginBlock($condition)
	{
		if ($condition != $this->blockLast) {
			if (empty($this->blockLoc[$condition])) $this->blockLoc[$condition] = 0;
			$this->blockLoc[$condition]++;
		}

		$this->blockLast = $condition;

		return parent::begin($condition);
	}

	function newLine()
	{
		return '<br />';
	}
	//end state handlers
	//Wiki Syntax Objects Parsing Start
	function bold($content)
	{
		return '<strong>' . $content . '</strong>';
	}

	function box($content)
	{
		return'<div style="border: solid 1px black;">' . $content . '</div>';
	}

	function center($content)
	{
		return '<center>' . $content . '</center>';
	}

	function colortext($content)
	{
		$text = explode(':', $content);
		$color = $text[0];
		$content = $text[1];

		return '<span style="color: #' . $color . ';">' . $content . '</span>';
	}

	function content($content)
	{
		return $content;
	}

	function italics($content)
	{
		return '<i>' . $content . '</i>';
	}

	function header($content)
	{
		include_once('lib/smarty_tiki/function.icon.php');

		$hNum = 1;
		$headerLength = strlen($content);
		for($i = 0; $i < $headerLength; $i++) {
			if ($content[$i] == '!') {
				$hNum++;
			} else {
				break;
			}
		}

		$content = substr($content, $hNum - 1);

		self::$headerStack[] = $content;

		return '<h' . $hNum . '>' . $content . $this->headerButton($hNum) . '</h' . $hNum . '>';
	}

	function headerButton($hNum)
	{
		global $smarty, $tiki_p_edit, $section;
		if (
			$this->prefs['wiki_edit_section'] === 'y' &&
			$section === 'wiki page' &&
			$tiki_p_edit === 'y' &&
			(
				$this->prefs['wiki_edit_section_level'] == 0 ||
				$hNum <= $this->prefs['wiki_edit_section_level']
			) && (
				empty(self::$option['print']) ||
				!self::$option['print']
			) &&
			!self::$option['suppress_icons']
		) {


			if ($this->prefs['wiki_edit_icons_toggle'] == 'y' && !isset($_COOKIE['wiki_plugin_edit_view'])) {
				$iconDisplayStyle = ' style="display:none;"';
			} else {
				$iconDisplayStyle = '';
			}
			$button = '<div class="icon_edit_section"' . $iconDisplayStyle . '><a href="tiki-editpage.php?';

			if (!empty($_REQUEST['page'])) {
				$button .= 'page='.urlencode($_REQUEST['page']).'&amp;';
			}

			self::$hdrCount++;
			include_once('lib/smarty_tiki/function.icon.php');
			$button .= 'hdr=' . self::$hdrCount . '">'.smarty_function_icon(array('_id'=>'page_edit_section', 'alt'=>tra('Edit Section')), $smarty).'</a></div>';

			return $button;
		}
	}

	function pluginButton($name, $args, $key)
	{
		global $headerlib, $smarty;

		$name = strtolower($name);

		if (
			$this->pluginIsEditable($name) &&
			(
				empty(self::$option['preview_mode']) ||
				!self::$option['preview_mode']
			) &&
			empty(self::$option['indexing']) &&
			(
				empty(self::$option['print']) ||
				!self::$option['print']
			) &&
			!self::$option['suppress_icons']
		) {
			$id = 'plugin-edit-' . $name . self::$pluginsExecutedStack[$name];
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
		key: "' . $key . '",
		controller: "jison",
		action: "pluginbody"
	}, function(o) {
		popup_plugin_form('
			. json_encode('editwiki')
			. ', '
			. json_encode($name)
			. ', '
			. json_encode(self::$pluginsExecutedStack[$name])
			. ', '
			. json_encode(self::$option['page'])
			. ', '
			. json_encode($args)
			. ', o.body, event.target);
	});
	return false;
});
');
			include_once('lib/smarty_tiki/function.icon.php');
			return '~np~<a id="' .$id. '" class="editplugin"'.$iconDisplayStyle.'>'.smarty_function_icon(array('_id'=>'wiki_plugin_edit', 'alt'=>tra('Edit Plugin').':'.$name), $smarty)."</a>~/np~";
		}

		return '';
	}

	function hr()
	{
		return '<hr />';
	}

	function link($content)
	{
		$link = explode('|', $content);
		$href = (isset($link[0]) ? $link[0] : $content);
		$text = (isset($link[1]) ? $link[1] : $href);

		return '<a href="' . $href . '">' . $text . '</a>';
	}

	function smile($smile)
	{ //this needs more tlc too
		return '<img src="img/smiles/icon_' . $smile . '.gif" alt="' . $smile . '" />';
	}

	function strikethrough($content)
	{
		return '<span style="text-decoration: line-through;">' . $content . '</span>';
	}

	function tableParser($content)
	{
		$tableContents = '';
		$rows = explode("\n", $content);

		for ($i = 0, $count_rows = count($rows); $i < $count_rows; $i++) {
			$row = '';

			$cells = explode('|', $rows[$i]);
			for ($j = 0, $count_cells = count($cells); $j < $count_cells; $j++) {
				$row .= $this->table_td($cells[$j]);
			}
			$tableContents .= $this->table_tr($row);
		}

		return '<table style="width: 100%;">' . $tableContents . '</table>';
	}

	function table_tr($content)
	{
		return '<tr>' . $content . '</tr>';
	}

	function table_td($content)
	{
		return '<td>' . $content . '</td>';
	}

	function titlebar($content)
	{
		return '<div class="titlebar">' . $content . '</div>';
	}

	function olist($content)
	{
		$this->olistLen[$this->blockLoc['olist']]++;
		$start =$this->olistLen[$this->blockLoc['olist']];

		return '<ol class="olgroup' . $this->blockLoc['olist'] . '" start="' . $start . '"><li>' . $content . '</li></ol>';
	}

	function ulist($content)
	{
		return '<ul><li>' . $content . '</li></ul>';
	}

	function underscore($content)
	{
		return '<u>' . $content . '</u>';
	}

	function wikilink($content)
	{
		$wikilink = explode('|', $content);
		$href = $content;

		if ($this->match('/\|/', $content)) {
			$href = $wikilink[0];
			$content = $wikilink[1];
		}

		return '<a href="' . $href . '">' . $content . '</a>';
	}

	function html($content)
	{
		return $content;
	}

	function formatContent($content)
	{
		//return nl2br($content);
		return $content;
	}

	//unified functions used inside parser
	function substring($val, $left, $right)
	{
		 return substr($val, $left, $right);
	}

	function match($pattern, $subject)
	{
		preg_match($pattern, $subject, $match);

		return (!empty($match[1]) ? $match[1] : false);
	}

	function replace($search, $replace, $subject)
	{
		return str_replace($search, $replace, $subject);
	}

	function join()
	{
		$array = func_get_args();

		return implode($array, '');
	}

	function shift($array)
	{
		if (empty($array))
			$array = array();

		array_shift($array);

		return $array;
	}
}
