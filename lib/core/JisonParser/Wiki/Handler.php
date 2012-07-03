<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_Wiki_Handler extends JisonParser_Wiki
{
	/* parser tracking */
	var $parsing = false;
	public static $spareParsers = array();
	private $Parser;

	/* plugin tracking */
	var $pluginStack = array();
	var $pluginEntries = array();
	var $plugins = array();
	var $wikiPluginParserNegotiatorClass = 'WikiPlugin_ParserNegotiator';

	/* np tracking */
	var $npEntries = array();
	var $npCount = 0;

	/* header tracking */
	var $header;

	/* list tracking and parser */
	var $list;

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

	var $isHtmlPurifying = false;
	public static $option = array();

	var $optionDefaults = array(
		'skipvalidation'=>  false,
		'is_html'=> false,
		'absolute_links'=> false,
		'language' => '',
		'noparseplugins' => false,
		'stripplugins' => false,
		'noheaderinc' => false,
		'page' => '',
		'print' => false,
		'parseimgonly' => false,
		'preview_mode' => false,
		'suppress_icons' => false,
		'parsetoc' => true,
		'inside_pretty' => false,
		'process_wiki_paragraphs' => true,
		'min_one_paragraph' => false,
		'parseBreaks' => true,
		'parseLists' =>   true,
		'parseWiki' => true,
		'parseNps' => true,
		'parseSmileys'=> true
	);

	public function setOption($option = array())
	{
		$page = $_REQUEST['page'];
		self::$option['page'] = $page;

		self::$option = array_merge($this->optionDefaults, $option);
	}

	var $parseBreaksTracking = array(
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
		$this->Parser = &$this;

		if (isset($this->Parser->header) == false) {
			$this->Parser->header = new JisonParser_Wiki_Header();
		}

		if (isset($this->Parser->list) == false) {
			$this->Parser->list = new JisonParser_Wiki_List();
		}

		parent::__construct();
	}

	function parse($input)
	{
		if (empty($input)) return $input;

		if ($this->parsing == true) {
			$parser = end(self::$spareParsers);
			if (!empty($parser) && $parser->parsing == false) {
				$parser->Parser = &$this->Parser;
				$result = $parser->parse($input);
			} else {
				self::$spareParsers[] = $parser = new JisonParser_Wiki_Handler();
				$parser->Parser = &$this->Parser;
				$result = $parser->parse($input);
			}
		} else {
			$this->parsing = true;

			if (empty(self::$option)) $this->setOption();

			$this->preParse($input);

			$result = parent::parse($input);

			$this->parsing = false;
			$this->postParse($result);
		}

		return $result;
	}

	function parsePlugin($input)
	{
		if (self::$option['noparseplugins'] == false) {
			$is_html = self::$option['is_html'];

			$this->setOption(array(
				'is_html'=> true
			));

			$result = $this->parse($input);

			$this->setOption(array(
				'is_html'=> $is_html
			));

			return $result;
		} else {
			return $input;
		}
	}

	function preParse(&$input)
	{
		$input = "\n" . $input . "\n"; //here we add 2 lines, so the parser doesn't have to do special things to track the first line and last, we remove these when we insert breaks

		if (self::$option['parseNps'] == true) {
			$input = preg_replace_callback('/~np~(.|\n)*?~\/np~/', array(&$this, 'removeNpEntities'), $input);
		}

		$input = $this->protectSpecialChars($input);

		$this->Parser->list->setup($input);
	}

	function postParse(&$input)
	{
		$input = $this->unprotectSpecialChars($input, self::$option['is_html']);

		$input = rtrim(ltrim($input, "\n"), "\n"); //here we remove the fake line breaks added just before parse

		if (self::$option['parseBreaks'] == true) {
			$lines = explode("\n", $input);
			$skipNext = false;
			foreach($lines as &$line) {
				$this->parseBreaks($line, $skipNext);
			}
			$input = implode("\n", $lines);
		}

		if (self::$option['parseLists'] == true || strpos($input, "\n") !== false) {
			$lists = $this->Parser->list->toHtmlList();
			foreach($lists as $key => &$list) {
				$input = str_replace($key, $list, $input);
			}
		}

		if (self::$option['parseSmileys']) {
			$this->parseSmileys($input);
		}

		$this->restoreNpEntities($input);
		$this->restorePluginEntities($input);
		$this->executeAndRestoreAwaitingPlugins($input);
	}

	// state & plugin handlers
	function plugin($pluginDetails)
	{
		$plugin = new $this->wikiPluginParserNegotiatorClass($this, $pluginDetails, $this->page, $this->prefs, self::$option);

		if (!self::$option['skipvalidation']) {
			$status = $plugin->canExecute();
		} else {
			$status = true;
		}

		if ($status === true) {
			$plugin->body = $this->unprotectSpecialChars($plugin->body, true);

			/*$plugins is a bit different that pluginEntries, an entry will be popped later, $plugins is more for
			tracking, although their values may be the same for a time, the end result will be an empty entries, but
			$plugins will have all executed plugin in it*/
			$this->Parser->plugins[$plugin->key] = $plugin->body;

			$this->pluginEntries[$plugin->key] = $this->parsePlugin( $plugin->execute(true) );
		} else {
			return $plugin->blockFromExecution($status);
		}

		return $plugin->key;
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

	function isPlugin()
	{
		return (count($this->pluginStack) > 0);
	}

	static function getUnparsedPluginBodies($data)
	{
		$me = new self();
		$me->parse($data);
		return $me->plugins;
	}

	function removeNpEntities(&$matches)
	{
		$key = '§' . md5('np:'.$this->npCount) . '§';
		$this->npEntries[$key] = substr($matches[0], 4, -5);
		$this->npCount++;
		return $key;
	}

	static function deleteEntities(&$data)
	{
		$data = preg_replace('/§[a-z0-9]{32}§/','',$data);
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
			if (self::$option['stripplugins'] == true) {
				$input = str_replace($key, '', $input);
			} else {
				$input = str_replace($key, $entity, $input);
			}

			if (!$keep) {
				unset($this->pluginEntries[$key]);
			}
		}
	}

	function executeAndRestoreAwaitingPlugins(&$input, $keep = false)
	{
		if ($this->Parser->parsing == false && WikiPlugin_ParserNegotiator::$currentParserLevel == 0) {
			sort(WikiPlugin_ParserNegotiator::$parserLevels, SORT_NUMERIC);
			array_unique(WikiPlugin_ParserNegotiator::$parserLevels);

			foreach(WikiPlugin_ParserNegotiator::$parserLevels as &$level) {
				WikiPlugin_ParserNegotiator::$currentParserLevel = $level;
				foreach(WikiPlugin_ParserNegotiator::$pluginsAwaitingExecution as &$plugin) {
					if (WikiPlugin_ParserNegotiator::$currentParserLevel == $level) {

						if (empty($this->pluginEntries[$plugin->key]) == true) {
							$this->Parser->plugins[$plugin->key] = $plugin->body;
							$this->pluginEntries[$plugin->key] = $this->parsePlugin($plugin->execute());
						}

						$input = str_replace($plugin->key, $this->pluginEntries[$plugin->key], $input);

						if (!$keep) {
							unset($this->pluginEntries[$plugin->key]);
						}

						unset($plugin);
					}
				}
			}
		}
	}

	function checkToSkipLine(&$skipLine, &$lineInLowerCase, $key, $start = "", $stop = "", $skipBefore = false, $skipAfter = false)
	{
		// check if we are inside a script not insert <br />
		$opens = 0;
		if (empty($start) == false) {
			$opens = substr_count($lineInLowerCase, $start);
		}

		$closes = 0;
		if (empty($stop) == false) {
			$closes = substr_count($lineInLowerCase, $stop);
		}

		$this->parseBreaksTracking[$key] += $opens;
		$this->parseBreaksTracking[$key] -= $closes;

		if ($skipLine == true) { //if true, only one line, no need to check and set again
			return;
		}

		if ($skipBefore == true && $opens > 0 && $this->parseBreaksTracking[$key] == 0) {
			$skipLine = true;
		}

		if ($skipAfter == true && $closes > 0 && $this->parseBreaksTracking[$key] == 0) {
			$skipLine = true;
		}
	}

	function parseBreaks(&$line, &$skipNext)
	{
		$lineInLowerCase = TikiLib::strtolower($line);

		$skipLine = false;

		$this->checkToSkipLine($skipLine, $lineInLowerCase, 'inComment', "<!--", "-->");

		// check if we are inside a ~pre~ block and, if so, ignore
		// monospaced and do not insert <br />
		$this->checkToSkipLine($skipLine, $lineInLowerCase, 'inPre', "<pre", "</pre");

		// check if we are inside a table, if so, ignore monospaced and do
		// not insert <br />
		$this->checkToSkipLine($skipLine, $lineInLowerCase, 'inTable', "<table", "</table", true, true);

		// check if we are inside an ul TOC list, if so, ignore monospaced and do
		// not insert <br />
		$this->checkToSkipLine($skipLine, $lineInLowerCase, 'inTOC', "<ul class=\"toc", "</ul><!--toc-->", true, true);

		// check if we are inside a script not insert <br />
		$this->checkToSkipLine($skipLine, $lineInLowerCase, 'inScript', "<script", "</script");

		// check if we are inside a script not insert <br />
		$this->checkToSkipLine($skipLine, $lineInLowerCase, 'inDiv', "<div", "</div", false, false);

		// check if we are inside a script not insert <br />
		$this->checkToSkipLine($skipLine, $lineInLowerCase, 'inHeader', "<h", "</h", true, true);

		// check if we are inside a script not insert <br />
		if (strpos($lineInLowerCase, "</h") !== false) {
			$skipLine = true;
			$skipNext = true;
		}

		// check if we are inside a script not insert <br />
		if (strpos($lineInLowerCase, "<br") !== false || strpos($lineInLowerCase, "<div") !== false) {$skipLine = true;$skipNext = true;}

		if ($skipLine == true) {
			//we skip the line just after a header
			return;
		}

		if ($skipNext == true) {
			$skipNext = false;
			//we skip the line just after a header
			return;
		}

		if (
			$this->parseBreaksTracking['inComment'] == 0 &&
			$this->parseBreaksTracking['inPre'] == 0 &&
			$this->parseBreaksTracking['inTable'] == 0 &&
			$this->parseBreaksTracking['inTOC'] == 0 &&
			$this->parseBreaksTracking['inScript'] == 0 &&
			$this->parseBreaksTracking['inDiv'] == 0 &&
			$this->parseBreaksTracking['inHeader'] == 0
		) {
			$line = "<br />" . $line;
		}
	}

	function parseSmileys(&$input)
	{
		global $prefs;
		static $patterns;

		if ($prefs['feature_smileys'] == 'y') {
			if (! $patterns) {
				$patterns = array(
					"/\(:([^:]+):\)/" => "<img alt=\"$1\" src=\"img/smiles/icon_$1.gif\" />",

					// :) :-)
					'/(\s|^):-?\)/' => "$1<img alt=\":-)\" title=\"".tra('smiling')."\" src=\"img/smiles/icon_smile.gif\" />",
					// :( :-(
					'/(\s|^):-?\(/' => "$1<img alt=\":-(\" title=\"".tra('sad')."\" src=\"img/smiles/icon_sad.gif\" />",
					// :D :-D
					'/(\s|^):-?D/' => "$1<img alt=\":-D\" title=\"".tra('grinning')."\" src=\"img/smiles/icon_biggrin.gif\" />",
					// :S :-S :s :-s
					'/(\s|^):-?S/i' => "$1<img alt=\":-S\" title=\"".tra('confused')."\" src=\"img/smiles/icon_confused.gif\" />",
					// B) B-) 8-)
					'/(\s|^)(B-?|8-)\)/' => "$1<img alt=\"B-)\" title=\"".tra('cool')."\" src=\"img/smiles/icon_cool.gif\" />",
					// :'( :_(
					'/(\s|^):[\'|_]\(/' => "$1<img alt=\":_(\" title=\"".tra('crying')."\" src=\"img/smiles/icon_cry.gif\" />",
					// 8-o 8-O =-o =-O
					'/(\s|^)[8=]-O/i' => "$1<img alt=\"8-O\" title=\"".tra('frightened')."\" src=\"img/smiles/icon_eek.gif\" />",
					// }:( }:-(
					'/(\s|^)\}:-?\(/' => "$1<img alt=\"}:(\" title=\"".tra('evil stuff')."\" src=\"img/smiles/icon_evil.gif\" />",
					// !-) !)
					'/(\s|^)\!-?\)/' => "$1<img alt=\"(!)\" title=\"".tra('exclamation mark !')."\" src=\"img/smiles/icon_exclaim.gif\" />",
					// >:( >:-(
					'/(\s|^)\>:-?\(/' => "$1<img alt=\"}:(\" title=\"".tra('frowning')."\" src=\"img/smiles/icon_frown.gif\" />",
					// i-)
					'/(\s|^)i-\)/' => "$1<img alt=\"(".tra('light bulb').")\" title=\"".tra('idea !')."\" src=\"img/smiles/icon_idea.gif\" />",
					// LOL
					'/(\s|^)LOL(\s|$)/' => "$1<img alt=\"(".tra('LOL').")\" title=\"".tra('laughing out loud !')."\" src=\"img/smiles/icon_lol.gif\" />$2",
					// >X( >X[ >:[ >X-( >X-[ >:-[
					'/(\s|^)\>[:X]-?\(/' => "$1<img alt=\">:[\" title=\"".tra('mad')."\" src=\"img/smiles/icon_mad.gif\" />",
					// =D =-D
					'/(\s|^)[=]-?D/' => "$1<img alt=\"=D\" title=\"".tra('Mr. Green laughing')."\" src=\"img/smiles/icon_mrgreen.gif\" />",
				);
			}

			foreach ($patterns as $p => $r) {
				$input = preg_replace($p, $r, $input);
			}
		}
	}

	function SOL() //start of line
	{
		return ($this->yyloc['first_column'] == 0 ? true : false);
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

	//end state handlers
	//Wiki Syntax Objects Parsing Start
	function bold($content) //__content__
	{
		if (self::$option['parseWiki'] == false) return "__" . $content . "__";

		return '<strong>' . $content . '</strong>';
	}

	function box($content) //^content^
	{
		if (self::$option['parseWiki'] == false) return "^" . $content . "^";

		return '<div class="simplebox">' . $content . '</div>';
	}

	function center($content) //::content::
	{
		if (self::$option['parseWiki'] == false) return "::" . $content . "::";

		return '<center>' . $content . '</center>';
	}

	function colortext($content)
	{
		if (self::$option['parseWiki'] == false) return "~~" . $content . "~~";

		$text = explode(':', $content);
		$color = $text[0];
		$content = $text[1];

		return '<span style="color: #' . $color . ';">' . $content . '</span>';
	}

	function italics($content) //''content''
	{
		if (self::$option['parseWiki'] == false) return "''" . $content . "''";

		return '<i>' . $content . '</i>';
	}

	function header($content) //!content
	{
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
		if (self::$option['parseWiki'] == false) return str_repeat("!", $hNum) . $content;

		$hNum = min(6, $hNum); //html doesn't support 7+ header level
		$id = $this->Parser->header->stack($hNum, $content);
		$button = '';
		global $section, $tiki_p_edit;
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
			$button = $this->Parser->header->button($this->prefs['wiki_edit_icons_toggle']);
		}

		return $button . '<h' . $hNum . ' class="showhide_heading" id="' . $id . '">' . $content . '</h' . $hNum . '>';
	}

	function stackList($content)
	{
		if (self::$option['parseWiki'] == false) return $content;

		$level = 0;
		$headerLength = strlen($content);
		$type = '';
		$noiseLength = 0;

		for($i = 0; $i < $headerLength; $i++) {
			if ($content[$i] == "\n") {
				$noiseLength++;
				continue;
			}

			if (
				$content[$i] == "*" ||
				$content[$i] == "#" ||
				$content[$i] == "+"
			) {
				$type = $content[$i];
				$level++;
			} elseif ($i > 0 && $content[$i] == '-') {
				$type = $content[$i];
				$noiseLength++;
			} else {
				break;
			}
		}

		$content = substr($content, ($level + $noiseLength));

		return $this->Parser->list->stack($level, $content, $type);
	}

	function hr() //---
	{
		if (self::$option['parseWiki'] == false) return "---";

		return '<hr />';
	}

	function link($content) //[content|content]
	{
		if (self::$option['parseWiki'] == false) return "[" . $content . "]";

		$link = explode('|', $content);
		$href = (isset($link[0]) ? $link[0] : $content);
		$text = (isset($link[1]) ? $link[1] : $href);

		return '<a href="' . $href . '">' . $text . '</a>';
	}

	function smile($content)
	{
		if (self::$option['parseWiki'] == false) return "(:" . $content . ":)";

		//this needs more tlc too
		return '<img src="img/smiles/icon_' . $content . '.gif" alt="' . $content . '" />';
	}

	function strikethrough($content) //--content--
	{
		if (self::$option['parseWiki'] == false) return "--" . $content . "--";

		return '<strike>' . $content . '</strike>';
	}

	function tableParser($content) /*|| | \n | ||*/
	{
		if (self::$option['parseWiki'] == false) return "||" . $content . "||";

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

		return '<table class="wikitable">' . $tableContents . '</table>';
	}

	function table_tr($content)
	{
		return '<tr>' . $content . '</tr>';
	}

	function table_td($content)
	{
		return '<td class="wikicell">' . $content . '</td>';
	}

	function titlebar($content) //-=content=-
	{
		if (self::$option['parseWiki'] == false) return "-=" . $content . "=-";

		return '<div class="titlebar">' . $content . '</div>';
	}

	function underscore($content) //===content===
	{
		if (self::$option['parseWiki'] == false) return "===" . $content . "===";

		return '<u>' . $content . '</u>';
	}

	function wikilink($content) //((content|content))
	{
		if (self::$option['parseWiki'] == false) return "((" . $content . "))";

		$wikilink = explode('|', $content);
		$href = (isset($wikilink[0]) ? $wikilink[0] : $content);
		$text = (isset($wikilink[1]) ? $wikilink[1] : $href);

		return '<a href="tiki-index.php?page=' . $href . '">' . $text . '</a>';
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
