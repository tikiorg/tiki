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
	public $Parser;
	private static $parseDepth = 0;

	/* plugin tracking */
	public $pluginStack = array();
	public $pluginStackCount = 0;
	public $pluginEntries = array();
	public $plugins = array();
	public static $pluginIndexes = array();
	public $pluginNegotiators = array();

	/* np tracking */
	public $npStack = false; //There can only be 1 active np stack

	/* pp tracking */
	public $ppStack = false; //There can only be 1 active np stack

	public $skipBr = false; //used in block level items, should be set to true.  The next break sets it back to false;
	public $tableStack = array();

	/* header tracking */
	public $header;

	/* list tracking and parser */
	public $list;

	/* autoLink parser */
	public $autoLink;

	/*hotWords parser */
	public $hotWords;

	/* smiley parser */
	public $smileys;

	/* dynamic var parser */
	public $dynamicVar;

	/* html character */
	public $htmlCharacter;

	/* html tag tracking */
	public $nonBreakingTagDepth = 0;

	/* line tracking */
	private $isFirstBr = false;
	private $line = 0;

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
		'parseNps' => true,
		'parseSmileys'=> true
	);

	public function setOption($option = array())
	{
		global $prefs;
		$page = (isset($_REQUEST['page']) ? $_REQUEST['page'] : $prefs['site_wikiHomePage']);

		$this->Parser->option['page'] = $page;

		$this->Parser->option = array_merge($this->optionDefaults, $option);
	}

	public function getOption($name = '')
	{
		return $this->Parser->option[$name];
	}

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

		if (isset($this->Parser->autoLink) == false) {
			$this->Parser->autoLink = new JisonParser_Wiki_AutoLink();
		}

		if (isset($this->Parser->hotWords) == false) {
			$this->Parser->hotWords = new JisonParser_Wiki_HotWords();
		}

		if (isset($this->Parser->smileys) == false) {
			$this->Parser->smileys = new JisonParser_Wiki_Smileys();
		}

		if (isset($this->Parser->dynamicVar) == false) {
			$this->Parser->dynamicVar = new JisonParser_Wiki_DynamicVariables();
		}

		if (isset($this->Parser->htmlCharacter) == false) {
			$this->Parser->htmlCharacter = new JisonParser_Wiki_HtmlCharacter($this->Parser);
		}

		parent::__construct();
	}

/*
	function parser_performAction(&$thisS, $yytext, $yyleng, $yylineno, $yystate, $S, $_S, $O)
	{
		$result = parent::parser_performAction($thisS, $yytext, $yyleng, $yylineno, $yystate, $S, $_S, $O);
		$thisS .= "{" . $yystate ."}";
		return $result;
	}

	function lexer_performAction(&$yy, $yy_, $avoiding_name_collisions, $YY_START = null) {
		$result = parent::lexer_performAction($yy, $yy_, $avoiding_name_collisions, $YY_START);
		echo "{" . $result . ":" .$avoiding_name_collisions . "}" . $yy_->yytext . "\n";
		return $result;
	}
*/

	function parse($input)
	{
		if (empty($input)) return $input;

		if ($this->parsing == true) {
			$parser = end(self::$spareParsers);
			if (!empty($parser) && $parser->parsing == false) {
				$output = $parser->parse($input);
			} else {
				$class = get_class($this->Parser);
				self::$spareParsers[] = $parser = new $class($this->Parser);
				$output = $parser->parse($input);
			}
		} else {
			$this->parsing = true;

			if (empty($this->Parser->option)) $this->setOption();

			$this->preParse($input);

			self::$parseDepth++;
			$output = parent::parse($input);
			self::$parseDepth--;

			$this->parsing = false;
			$this->postParse($output);
		}

		return $output;
	}

	function parsePlugin($input)
	{
		if (empty($input)) return "";

		if ($this->getOption('noparseplugins') == false) {

			$is_html = $this->getOption('is_html');

			$this->setOption(array('is_html' => true));

			$result = $this->parse($input);
			$this->setOption(array('is_html' => $is_html));

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

		if (self::$parseDepth == 0) {
			$this->Parser->htmlCharacter->parse($input);
		}

		$this->line = 0;
		$this->isFirstBr = false;
		$this->skipBr = false;
		$this->tableStack = array();
		$this->Parser->nonBreakingTagDepth = 0;
		$this->npStack = false;
		$this->ppStack = false;

		$input = "\n" . $input . "\n"; //here we add 2 lines, so the parser doesn't have to do special things to track the first line and last, we remove these when we insert breaks, these are dynamically removed later

		$input = $this->protectSpecialChars($input);
	}

	function postParse(&$output)
	{
		//remove comment artifacts
		$output = str_replace("<!---->", "", $output);

		//Replace the break we put at the beginning
		$output = preg_replace("/([<]br [\/][>][\n]|[\n])$/", "", $output);

		$output = $this->unprotectSpecialChars($output, $this->Parser->option['is_html']);

		if ($this->Parser->option['parseLists'] == true || strpos($output, "\n") !== false) {
			$lists = $this->Parser->list->toHtml();

			if (!empty($lists)) {
				foreach($lists as $key => &$list) {
					$output = str_replace($key, $list, $output);
					unset($list);
				}
			}
		}

		if ($this->Parser->option['parseSmileys']) {
			$this->Parser->smileys->parse($output);
		}

		$this->restorePluginEntities($output);

		$this->Parser->autoLink->parse($output);

		$this->Parser->hotWords->parse($output);

		$this->Parser->dynamicVar->makeForum($output);
	}

	// state & plugin handlers
	function plugin(&$pluginDetails)
	{
		$negotiator = $this->getPluginNegotiator();
		$pluginDetails['body'] = $this->unprotectSpecialChars($pluginDetails['body'], true);
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

	function isContent()
	{
		return ($this->pluginStackCount > 0 || $this->npStack == true || $this->ppStack == true ? true : null);
	}

	function getPluginNegotiator()
	{
		if (empty($this->pluginNegotiators['WikiPlugin_ParserNegotiator'])) {
			$this->pluginNegotiators['WikiPlugin_ParserNegotiator'] = new WikiPlugin_ParserNegotiator($this->Parser);
		}

		return $this->pluginNegotiators['WikiPlugin_ParserNegotiator'];
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
			$content = $this->unprotectSpecialChars($content);
		}

		return $content;
	}

	function pp($content)
	{
		return "<pre>" . $content . "</pre>";
	}

	function htmlTag($content)
	{
		$parts = preg_split("/[ >]/", substr($this->unprotectSpecialChars($content, true), 1)); //<tag> || <tag name="">
		$name = strtolower(trim($parts[0]));

		switch ($name) {
			//start block level
			case 'script':
			case 'table':
			case 'div':
				$this->Parser->nonBreakingTagDepth++;
				$this->line++;
				break;

			//end block level
			case '/script':
			case '/table':
			case '/div':
				$this->Parser->nonBreakingTagDepth--;
				$this->line++;
				break;

			//skip next block level
			case 'hr':
				break;
		}

		return $content;
	}

	function doubleDynamicVar($content)
	{
		global $prefs;

		if ( $prefs['wiki_dynvar_style'] != 'double') {
			return $content;
		}


		return $this->Parser->dynamicVar->ui(substr($content, 2, 2), $this->Parser->option['language']);
	}

	function singleDynamicVar($content)
	{
		global $prefs;

		if ( $prefs['wiki_dynvar_style'] != 'single') {
			return $content;
		}


		return $this->Parser->dynamicVar->ui(substr($content, 1, 1), $this->Parser->option['language']);
	}

	function bold($content) //__content__
	{
		return '<strong>' . $content . '</strong>';
	}

	function box($content) //^content^
	{
		return '<div class="simplebox">' . $content . '</div>';
	}

	function center($content) //::content::
	{
		return '<div style="text-align: center;">' . $content . '</div>';
	}

	function code($content)
	{
		return "<code>" . $content . "</code>";
	}

	function color($content)
	{
		$text = explode(':', $content);
		$color = $text[0];
		$content = $text[1];

		return '<span style="color: ' . $color . ';">' . $content . '</span>';
	}

	function italics($content) //''content''
	{
		return '<em>' . $content . '</em>';
	}

	function directional($direction, $content)
	{
		$dir = "";

		$direction = trim($direction);

		switch ($direction) {
			case "{l2r}" :
				$dir = 'ltr';
				break;
			case "{r2l}" :
				$dir = 'rtl';
				break;
		}

		return "<div dir='" . $dir . "'>" . $content . "</div>";
	}

	function header($content) //!content
	{
		$this->line++;

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
			!$this->Parser->option['suppress_icons'] &&
			!$this->Parser->option['preview_mode']
		) {
			$button = $this->Parser->header->button($this->prefs['wiki_edit_icons_toggle']);
		}

		$this->skipBr = true;

		return $button . '<h' . $hNum . ' class="showhide_heading" id="' . $id . '">' . $content . '</h' . $hNum . '>';
	}

	function stackList($content)
	{
		$this->line++;
		$level = 0;
		$headerLength = strlen($content);
		$type = '';
		$noiseLength = 0;

		for($i = 0; $i < $headerLength; $i++) {
			if ($content{$i} == "\n" || $content{$i} == "\r" || $content{$i} == "") {
				$noiseLength++;
				continue;
			}

			if ($content{$i} == ";") {//definition list)
				$type = ";";
				$level = 1;
				break;
			} else if (
				$content{$i} == "*" ||
				$content{$i} == "#" ||
				$content{$i} == "+"
			) {
				$type = $content{$i};
				$level++;
			} elseif ($i > 0 && $content{$i} == '-') {
				$type = $content{$i};
				$noiseLength++;
			} else {
				break;
			}
		}

		$content = substr($content, ($level + $noiseLength));

		$this->skipBr = array(true, true);

		return $this->Parser->list->stack($this->line, $level, $content, $type);
	}

	function hr() //---
	{
		$this->line++;
		$this->skipBr = true;
		return '<hr />';
	}

	function line()
	{
		$this->line++;

		//The first \n was inserted just before parse
		if ($this->isFirstBr == false) {
			$this->isFirstBr = true;
			return '';
		}

		$result = "\n";

		if ($this->skipBr == false && empty($this->tableStack) && $this->Parser->nonBreakingTagDepth == 0) {
			$result = "<br />\n";
		}

		if ($this->skipBr === true) {
			$this->skipBr = false;
		} else if (is_array($this->skipBr) == true) {
			if (count($this->skipBr) > 1) {
				array_pop($this->skipBr);
			} else {
				$this->skipBr = true;
			}
		}

		return $result;
	}

	function forcedLineEnd()
	{
		$this->line++;
		return '<br />';
	}

	function unlink($content) //[content|content]
	{
		$contentLength = strlen($content);

		if ($content[$contentLength - 3] == "@" &&
			$content[$contentLength - 2] == "n" &&
			$content[$contentLength - 1] == "p"
		) {
			$content = substr($content, 0, -3);
		}

		$contentLength = strlen($content);

		if ($content[$contentLength - 1] != "]" && strstr($content, "[[")) {
			$content = substr($content, 1);
		} else if (!strstr($content, "]]")) {
			$content = substr($content, 1);
		}

		return $content;
	}

	function link($content) //[content|content]
	{
		$link = explode('|', $content);
		$href = (isset($link[0]) ? $link[0] : $content);
		$text = (isset($link[1]) ? $link[1] : $href);

		if (!strpos($href, '://')) {
			$href = 'http://' . $href;
		}

		return '<a class="wiki" href="' . $href . '">' . $text . '</a>';
	}

	function smile($content)
	{
		//this needs more tlc too
		return '<img src="img/smiles/icon_' . $content . '.gif" alt="' . $content . '" />';
	}

	function strike($content) //--content--
	{
		return '<strike>' . $content . '</strike>';
	}

	function tableParser($content) /*|| | \n | ||*/
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
		$this->skipBr = true;

		return '<div class="titlebar">' . $content . '</div>';
	}

	function underscore($content) //===content===
	{
		return '<u>' . $content . '</u>';
	}

	function wikilink($content) //((content|content))
	{
		$wikilink = explode('|', $content);
		$href = (isset($wikilink[0]) ? $wikilink[0] : $content);
		$text = (isset($wikilink[1]) ? $wikilink[1] : $href);

		return '<a class="wiki" href="tiki-index.php?page=' . $href . '">' . $text . '</a>';
	}

	function comment($content)
	{
		return '<!---->';
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
}
