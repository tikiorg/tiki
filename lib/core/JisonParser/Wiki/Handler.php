<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_Wiki_Handler extends JisonParser_Wiki
{
	/* parser tracking */
	private $parsing = false;
	private static $spareParsers = array();
	private $Parser;
	private static $parseDepth = 0;

	/* plugin tracking */
	public $pluginStack = array();
	public $pluginStackCount = 0;
	private $pluginEntries = array();
	private $wikiPluginParserNegotiatorClass = 'WikiPlugin_ParserNegotiator';
	public $plugins = array();
	private static $pluginIndexes = array();
	private $pluginNegotiators = array();

	/* header tracking */
	public $header;

	/* list tracking and parser */
	public $list;

	//This var is used in both protectSpecialChars and unprotectSpecialChars to simplify the html ouput process
	private $specialChars = array(
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

	private $syntaxStatingChars = array(
		"__",
		"^",
		"::",
		"~",
		"[",
		"--",
		"||",
		"==",
		"((",
		"\n!",
		"\n*",
		"\n#",
		"\n+",
		"{"
	);

	var $tikilib;
	var $user;
	var $prefs;
	var $page;

	var $isHtmlPurifying = false;

	var $option = array();
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
		$this->Parser->option['page'] = $page;

		$this->Parser->option = array_merge($this->optionDefaults, $option);
	}

	public function setWikiPluginParserNegotiatorClass($class)
	{
		$this->Parser->wikiPluginParserNegotiatorClass = $class;
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

	function __construct(JisonParser_Wiki_Handler &$Parser = null)
	{
		global $tikilib, $page, $user, $prefs;

		$this->tikilib = $tikilib;
		$this->page = $page;
		$this->user = (isset($user) ? $user : tra('Anonymous'));
		$this->prefs = $prefs;

		if (empty($Parser)) {
			$this->Parser = &$this;
		} else {
			$this->Parser = &$Parser;
		}

		if (isset($this->Parser->header) == false) {
			$this->Parser->header = new JisonParser_Wiki_Header();
		}

		if (isset($this->Parser->list) == false) {
			$this->Parser->list = new JisonParser_Wiki_List();
		}

		parent::__construct();
	}

/*
	function parser_performAction(&$thisS, $yytext, $yyleng, $yylineno, $yystate, $S, $_S, $O)
	{
		file_put_contents("temp/actions.log", $yytext . "{" . $yystate ."}" . "\n", FILE_APPEND);

		$result = parent::parser_performAction($thisS, $yytext, $yyleng, $yylineno, $yystate, $S, $_S, $O);
		return $result;
	}
*/
	function hasWikiSyntax(&$input)
	{
		foreach($this->syntaxStatingChars as $char) {
			if (strstr($input, $char)) return true;
		}
		return false;
	}

	function parse($input)
	{
		if (empty($input)) return $input;

		if ($this->parsing == true) {
			$parser = end(self::$spareParsers);
			if (!empty($parser) && $parser->parsing == false) {
				$result = $parser->parse($input);
			} else {
				self::$spareParsers[] = $parser = new JisonParser_Wiki_Handler($this->Parser);
				$result = $parser->parse($input);
			}
		} else {
			$this->parsing = true;

			if (empty($this->Parser->option)) $this->setOption();

			$this->preParse($input);

			if ($this->hasWikiSyntax($input) == true) {
				self::$parseDepth++;
				$result = parent::parse($input);
				self::$parseDepth--;
			} else {
				$result = $input;
			}

			$this->parsing = false;
			$this->postParse($result);
		}

		return $result;
	}

	function parsePlugin($input)
	{
		if (empty($input)) return "";

		if ($this->Parser->option['noparseplugins'] == false) {

			$is_html = $this->Parser->option['is_html'];

			$this->Parser->option['is_html'] = true;

			$result = $this->parse($input);

			$this->Parser->option['is_html'] = $is_html;
			return $result;
		} else {
			return $input;
		}
	}

	function preParse(&$input)
	{
		/*
		RP - php default is 100,000 which is just too much for this type of parser.  The reason for this code is the use of
		preg_* functions using pcre library.  Some of the regex needed is just too much for php to handle, so by
		limiting this for regex we speed up the parser and allow it to safely lex/parse a string
		more here: http://stackoverflow.com/questions/7620910/regexp-in-preg-match-function-returning-browser-error
		*/
		ini_set("pcre.recursion_limit", "524");

		$input = "\n" . $input . "\n"; //here we add 2 lines, so the parser doesn't have to do special things to track the first line and last, we remove these when we insert breaks

		$input = $this->protectSpecialChars($input);

		$this->Parser->list->setup($input);
	}

	function postParse(&$input)
	{
		$input = $this->unprotectSpecialChars($input, $this->Parser->option['is_html']);

		$input = rtrim(ltrim($input, "\n"), "\n"); //here we remove the fake line breaks added just before parse

		if ($this->Parser->option['parseLists'] == true || strpos($input, "\n") !== false) {
			$lists = $this->Parser->list->toHtml();

			if (!empty($lists)) {
				foreach($lists as $key => &$list) {
					$input = str_replace($key, $list, $input);
				}
			}
		}

		if ($this->Parser->option['parseSmileys']) {
			$this->parseSmileys($input);
		}

		$this->restorePluginEntities($input);
	}

	// state & plugin handlers
	function plugin(&$pluginDetails)
	{
		$negotiator = $this->getPluginNegotiator();
		$negotiator->setDetails($pluginDetails);

		if ($this->Parser->option['skipvalidation'] == false) {
			$status = $negotiator->canExecute();
		} else {
			$status = true;
		}

		if ($status === true) {
			/*$plugins is a bit different that pluginEntries, an entry will be popped later, $plugins is more for
			tracking, although their values may be the same for a time, the end result will be an empty entries, but
			$plugins will have all executed plugin in it*/
			$this->plugins[$negotiator->key] = $negotiator->body;

			$this->pluginEntries[$negotiator->key] = $this->parsePlugin($negotiator->execute());
			return $negotiator->key;
		} else {
			return $negotiator->blockFromExecution($status);
		}
	}

	private function incrementPluginIndex($name)
	{
		$name = strtolower($name);

		if (isset(self::$pluginIndexes[$name]) == false) self::$pluginIndexes[$name] = 0;

		self::$pluginIndexes[$name]++;

		return self::$pluginIndexes[$name];
	}

	function pluginKey($name)
	{
		return '§' . md5('plugin:' . $name . '_' . $this->incrementPluginIndex($name)) . '§';
	}

	function inlinePlugin($yytext)
	{
		$pluginName = $this->match('/^\{([a-z]+)/', $yytext);
		$pluginArgs = rtrim(str_replace('{'.$pluginName .' ', '', $yytext), '}');

		return array(
			'name' => $pluginName,
			'args' => $pluginArgs,
			'body' => '',
			'key' => $this->pluginKey($pluginName)
		);
	}

	function stackPlugin($yytext)
	{
		$pluginName = $this->match('/^\{([A-Z]+)/', $yytext);
		$pluginArgs = rtrim(str_replace('{' . $pluginName . '(', '', $yytext), ')}');

		$this->pluginStack[] = array(
			'name' => $pluginName,
			'args' => $pluginArgs,
			'body' => '',
			'key' => $this->pluginKey($pluginName)
		);
		$this->pluginStackCount++;
	}

	function isPlugin()
	{
		return ($this->pluginStackCount > 0);
	}

	static function getUnparsedPluginBodies($data)
	{
		$me = new self();
		$me->parse($data);
		return $me->plugins;
	}

	function getPluginNegotiator()
	{
		if (empty($this->pluginNegotiators[$this->wikiPluginParserNegotiatorClass])) {
			$this->pluginNegotiators[$this->wikiPluginParserNegotiatorClass] = new $this->wikiPluginParserNegotiatorClass($this);
		}

		return $this->pluginNegotiators[$this->wikiPluginParserNegotiatorClass];
	}

	static function deleteEntities(&$data)
	{
		$data = preg_replace('/§[a-z0-9]{32}§/','',$data);
	}


	function restorePluginEntities(&$input, $keep = false)
	{
		//use of array_reverse, jison is a reverse bottom-up parser, if it doesn't reverse jison doesn't restore the plugins in the right order, leaving the some nested keys as a result
		foreach(array_reverse($this->pluginEntries) as $key => $entity) {
			if ($this->Parser->option['stripplugins'] == true) {
				$input = str_replace($key, '', $input);
			} else {
				$input = str_replace($key, $entity, $input);
			}

			if (!$keep) {
				unset($this->pluginEntries[$key]);
			}
		}

		if (self::$parseDepth == 0) {
			$this->getPluginNegotiator()->executeAwaiting($input);
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
			$this->Parser->option['is_html'] != true
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
			$this->Parser->option['is_html'] == true
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
	function np($content)
	{
		if ($this->Parser->option['parseNps'] == true) {
			$content = substr($content, 4, -5);
		}

		return $content;
	}

	function bold($content) //__content__
	{
		if ($this->Parser->option['parseWiki'] == false) return "__" . $content . "__";

		return '<strong>' . $content . '</strong>';
	}

	function box($content) //^content^
	{
		if ($this->Parser->option['parseWiki'] == false) return "^" . $content . "^";

		return '<div class="simplebox">' . $content . '</div>';
	}

	function center($content) //::content::
	{
		if ($this->Parser->option['parseWiki'] == false) return "::" . $content . "::";

		return '<center>' . $content . '</center>';
	}

	function colortext($content)
	{
		if ($this->Parser->option['parseWiki'] == false) return "~~" . $content . "~~";

		$text = explode(':', $content);
		$color = $text[0];
		$content = $text[1];

		return '<span style="color: #' . $color . ';">' . $content . '</span>';
	}

	function italics($content) //''content''
	{
		if ($this->Parser->option['parseWiki'] == false) return "''" . $content . "''";

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
		if ($this->Parser->option['parseWiki'] == false) return str_repeat("!", $hNum) . $content;

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
				empty($this->Parser->option['print']) ||
				!$this->Parser->option['print']
			) &&
			!$this->Parser->option['suppress_icons']
		) {
			$button = $this->Parser->header->button($this->prefs['wiki_edit_icons_toggle']);
		}

		return $button . '<h' . $hNum . ' class="showhide_heading" id="' . $id . '">' . $content . '</h' . $hNum . '>';
	}

	function stackList($content)
	{
		if ($this->Parser->option['parseWiki'] == false) return $content;

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
		if ($this->Parser->option['parseWiki'] == false) return "---";

		return '<hr />';
	}

	function link($content) //[content|content]
	{
		if ($this->Parser->option['parseWiki'] == false) return "[" . $content . "]";

		$link = explode('|', $content);
		$href = (isset($link[0]) ? $link[0] : $content);
		$text = (isset($link[1]) ? $link[1] : $href);

		return '<a href="' . $href . '">' . $text . '</a>';
	}

	function smile($content)
	{
		if ($this->Parser->option['parseWiki'] == false) return "(:" . $content . ":)";

		//this needs more tlc too
		return '<img src="img/smiles/icon_' . $content . '.gif" alt="' . $content . '" />';
	}

	function strikethrough($content) //--content--
	{
		if ($this->Parser->option['parseWiki'] == false) return "--" . $content . "--";

		return '<strike>' . $content . '</strike>';
	}

	function tableParser($content) /*|| | \n | ||*/
	{
		if ($this->Parser->option['parseWiki'] == false) return "||" . $content . "||";

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
		if ($this->Parser->option['parseWiki'] == false) return "-=" . $content . "=-";

		return '<div class="titlebar">' . $content . '</div>';
	}

	function underscore($content) //===content===
	{
		if ($this->Parser->option['parseWiki'] == false) return "===" . $content . "===";

		return '<u>' . $content . '</u>';
	}

	function wikilink($content) //((content|content))
	{
		if ($this->Parser->option['parseWiki'] == false) return "((" . $content . "))";

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
