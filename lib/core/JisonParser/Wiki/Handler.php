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
	public $parseDepth = 0;
	public $parserDebug = false;
	public $lexerDebug = false;

	/* plugin tracking */
	public $pluginStack = array();
	public $pluginStackCount = 0;
	public $pluginEntries = array();
	public $plugins = array();
	public static $pluginIndexes = array();
	public $pluginNegotiator;

	public $headerStack = false;

	/* np tracking */
	public $npStack = false; //There can only be 1 active np stack

	/* pp tracking */
	public $ppStack = false; //There can only be 1 active np stack

	/* link tracking*/
	public $linkStack = false; //There can only be 1 active link stack

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
	public $specialChars = array(
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

	public $user;
	public $prefs;
	public $page;

	public $isHtmlPurifying = false;

	public $option = array();
	public $optionDefaults = array(
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
		global $parserlib;

		if (!empty($this->Parser->option)) {
			$this->Parser->option = array_merge($this->Parser->option, $option);
		} else {
			$this->resetOption();
			$this->Parser->option = array_merge($this->optionDefaults, $option);
		}

		if (isset($parserlib->option)) {
			$parserlib->option = $this->Parser->option;
		}
	}

	public function getOption($name = '')
	{
		if (isset($this->Parser->option[$name])) {
			return $this->Parser->option[$name];
		} else {
			return false;
		}
	}

	public function resetOption()
	{
		global $prefs, $parserlib;
		$page = (isset($_REQUEST['page']) ? $_REQUEST['page'] : $prefs['site_wikiHomePage']);

		$this->Parser->option['page'] = $page;
		$this->Parser->option = $this->optionDefaults;

		if (isset($parserlib->option)) {
			$parserlib->option = $this->Parser->option;
		}
	}

	function __construct(JisonParser_Wiki_Handler &$Parser = null)
	{
		global $user;

		$this->user = (isset($user) ? $user : tra('Anonymous'));

		if (empty($Parser)) {
			$this->Parser = &$this;
		} else {
			$this->Parser = &$Parser;
		}

		if (isset($this->pluginNegotiator) == false) {
			$this->pluginNegotiator = new WikiPlugin_Negotiator_Parser($this->Parser);
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

		if (empty($this->Parser->option) == true) {
			$this->resetOption();
		}

		parent::__construct();
	}

/*
	function parser_performAction(&$thisS, $yytext, $yyleng, $yylineno, $yystate, $S, $_S, $O)
	{
		$result = parent::parser_performAction($thisS, $yytext, $yyleng, $yylineno, $yystate, $S, $_S, $O);
		if ($this->parserDebug == true) {
			$thisS = "{" . $thisS . ":" . $yystate ."," . $this->skipBr . "}";
		}
		return $result;
	}

	function lexer_performAction(&$yy, $yy_, $avoiding_name_collisions, $YY_START = null) {
		$result = parent::lexer_performAction($yy, $yy_, $avoiding_name_collisions, $YY_START);
		if ($this->lexerDebug == true) {
			echo "{" . $result . ":" .$avoiding_name_collisions . "," . $this->skipBr . "}" . $yy_->yytext . "\n";
		}
		return $result;
	}

	function parseError($error, $info)
	{
		echo $error;
		die;
	}
*/
	function parse($input)
	{
		if (empty($input)) return $input;

		if ($this->parsing == true) {
			$class = get_class($this->Parser);
			$parser = new $class($this->Parser);
			$output = $parser->parse($input);
			unset($parser);
		} else {
			$this->parsing = true;

			$this->preParse($input);

			$this->Parser->parseDepth++;
			$output = parent::parse($input);
			$this->Parser->parseDepth--;

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

			if ($is_html == false) {
				$this->setOption(array('is_html' => true));
			}

			$result = $this->parse($input);

			if ($is_html == false) {
				$this->setOption(array('is_html' => $is_html));
			}

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

		if ($this->Parser->parseDepth == 0) {
			$this->Parser->list->reset();
			$this->Parser->htmlCharacter->parse($input);
		}

		$this->line = 0;
		$this->isFirstBr = false;
		$this->skipBr = false;
		$this->tableStack = array();
		$this->nonBreakingTagDepth = 0;
		$this->npStack = false;
		$this->ppStack = false;
		$this->linkStack = false;

		$input = "\n" . $input . "\n"; //here we add 2 lines, so the parser doesn't have to do special things to track the first line and last, we remove these when we insert breaks, these are dynamically removed later

		$input = $this->protectSpecialChars($input);
	}

	function postParse(&$output)
	{
		//remove comment artifacts
		$output = str_replace("<!---->", "", $output);

		//Replace the break we put at the beginning
		//$output = preg_replace("/^(([<]br [\/][>])?([\n][\r]|[\n\r]))/", "", $output);
		$output = preg_replace("/(([<]br [\/][>])?([\n][\r]|[\r][\n]|[\n\r]))$/", "", $output);

		$output = $this->unprotectSpecialChars($output);

		if ( $this->getOption('parseLists') == true) {
			$lists = $this->Parser->list->toHtml();
			if (!empty($lists)) {
				$lists = array_reverse($lists);
				foreach ($lists as $key => &$list) {

						$output = str_replace($key, $list, $output);
						unset($list);

				}
			}
		}

		if ($this->getOption('parseSmileys')) {
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
		$pluginDetails['body'] = $this->unprotectSpecialChars($pluginDetails['body'], true);
		$negotiator =& $this->pluginNegotiator;

		$negotiator->setDetails($pluginDetails);

		if ( $this->getOption('skipvalidation') == false) {
			$status = $negotiator->canExecute();
		} else {
			$status = true;
		}

		if ($status === true) {
			/*$plugins is a bit different that pluginEntries, an entry will be popped later, $plugins is more for
			tracking, although their values may be the same for a time, the end result will be an empty entries, but
			$plugins will have all executed plugin in it*/
			$this->plugins[$negotiator->key] = $negotiator->body;

			$executed = $negotiator->execute();

			if ($negotiator->ignored == true) {
				return $executed;
			} else {
				$this->pluginEntries[$negotiator->key] = $this->parsePlugin( $executed );
				return $negotiator->key;
			}
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
			'key' => $this->pluginKey($pluginName),
			'syntax' => $yytext,
			'closing' => ''
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
			'key' => $this->pluginKey($pluginName),
			'syntax' => $yytext,
			'closing' => '{' . $pluginName . '}'
		);
		$this->pluginStackCount++;
	}

	function isContent()
	{
		return ($this->pluginStackCount > 0 || $this->npStack == true || $this->ppStack == true ? true : null);
	}

	static function deleteEntities(&$data)
	{
		$data = preg_replace('/§[a-z0-9]{32}§/', '', $data);
	}

	function restorePluginEntities(&$input, $keep = false)
	{
		//use of array_reverse, jison is a reverse bottom-up parser, if it doesn't reverse jison doesn't restore the plugins in the right order, leaving the some nested keys as a result
		array_reverse($this->pluginEntries);
		$iterations = 0;
		$limit = 100;

		while (!empty($this->pluginEntries) && $iterations <= $limit) {
			$iterations++;
			foreach ($this->pluginEntries as $key => $entity) {
				if (strstr($input, $key)) {
					if ($this->getOption('stripplugins') == true) {
						$input = str_replace($key, '', $input);
					} else {
						$input = str_replace($key, $entity, $input);
					}

					if (!$keep) {
						unset($this->pluginEntries[$key]);
					}
				}
			}
		}

		if ($this->Parser->parseDepth == 0) {
			$this->pluginNegotiator->executeAwaiting($input);
		}
	}

	// This function handles the protection of html entities so that they are not mangled when
	// parse_htmlchar runs, and as well so they can be properly seen, be it html or non-html
	function protectSpecialChars($data)
	{
		if (
			$this->isHtmlPurifying == true ||
			$this->getOption('is_html') == false
		) {
			foreach ($this->specialChars as $key => $specialChar) {
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
			$this->getOption('is_html') == true
		) {
			foreach ($this->specialChars as $key => $specialChar) {
				$data = str_replace($key, $specialChar['html'], $data);
			}
		} else {
			foreach ($this->specialChars as $key => $specialChar) {
				$data = str_replace($key, $specialChar['nonHtml'], $data);
			}
		}

		return $data;
	}

	//end state handlers
	//Wiki Syntax Objects Parsing Start
	function np($content)
	{
		if ( $this->getOption('parseNps') == true) {
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
			case 'h1':
			case 'h2':
			case 'h3':
			case 'h4':
			case 'h5':
			case 'h6':
			case 'pre':
			case 'ul':
			case 'dl':
			case 'div':
			case 'table':
			case 'p':
				$this->skipBr = true;
			case 'script':
				$this->nonBreakingTagDepth++;
				$this->line++;
				break;

			//end block level
			case '/h1':
			case '/h2':
			case '/h3':
			case '/h4':
			case '/h5':
			case '/h6':
			case '/pre':
			case '/ul':
			case '/dl':
			case '/div':
			case '/table':
			case '/p':
				$this->skipBr = true;
			case '/script':
				$this->nonBreakingTagDepth--;
				$this->nonBreakingTagDepth = max($this->nonBreakingTagDepth, 0);
				$this->line++;
				break;

			//skip next block level
			case 'hr':
			case 'br':
				$this->skipBr = true;
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


		return $this->Parser->dynamicVar->ui(substr($content, 2, 2),  $this->getOption('language'));
	}

	function singleDynamicVar($content)
	{
		global $prefs;

		if ( $prefs['wiki_dynvar_style'] != 'single') {
			return $content;
		}


		return $this->Parser->dynamicVar->ui(substr($content, 1, 1),  $this->getOption('language'));
	}

	function argumentVar($content)
	{
		$content = substr($content, 2, -2); //{{page}}

		global $user, $page;
		$parts = explode('|', $content);
		$value = '';
		$name = '';

		if (isset($parts[0])) {
			$name = $parts[0];
		}

		if (isset($parts[1])) {
			$value = $parts[1];
		}

		switch( $name ) {
			case 'user':
				$value = $user;
				break;
			case 'page':
				$value = $this->getOption('page');
				break;
			default:
				if ( isset($_REQUEST[$name]) ) {
					$value = $_REQUEST[$name];
				}
				break;
		}

		return $value;
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

	function l2r($content)
	{
		$content = substr($content, 5);
		return "<div dir='ltr'>" . $content . "</div>";
	}

	function r2l($content)
	{
		$content = substr($content, 5);
		return "<div dir='rtl'>" . $content . "</div>";
	}

	function header($content) //!content
	{
		global $prefs;
		$hNum = 0;
		$headerLength = strlen($content);
		for ($i = 0; $i < $headerLength; $i++) {
			if ($content[$i] == '!') {
				$hNum++;
			} else {
				break;
			}
		}

		$content = substr($content, $hNum);

		$hNum = min(6, $hNum); //html doesn't support 7+ header level
		$id = $this->Parser->header->stack($hNum - 2, $content);
		$button = '';
		global $section, $tiki_p_edit;
		if (
			$prefs['wiki_edit_section'] === 'y' &&
			$section === 'wiki page' &&
			$tiki_p_edit === 'y' &&
			(
				$prefs['wiki_edit_section_level'] == 0 ||
				$hNum <= $prefs['wiki_edit_section_level']
			) &&
			! $this->getOption('print') &&
			! $this->getOption('suppress_icons') &&
			! $this->getOption('preview_mode')
		) {
			$button = $this->Parser->header->button($prefs['wiki_edit_icons_toggle']);
		}

		$this->skipBr = true;

		//expanding headers
		$expandingHeaderClose = '';
		$expandingHeaderOpen = '';

		if ($this->headerStack == true) {
			$this->headerStack = false;
			$expandingHeaderClose = '</div>';
		}

		if ($content{0} == '-') {
			$content = substr($content, 1);
			$this->headerStack = true;
			$expandingHeaderOpen = '<a href="javascript:flipWithSign(\'flip' . $id .'\')" class="link" id="flipperflip' . $id .'">[+]</a>' .
				'<div style="display: none;" class="showhide_heading" id="flip' . $id . '">';
		}

		$result =
			$expandingHeaderClose .
				$button .
				'<h' . $hNum . ' class="showhide_heading" id="' . $id . '">' .
					$content .
				'</h' . $hNum . '>' .
			$expandingHeaderOpen;

		return $result;
	}

	function stackList($content)
	{
		$level = 0;
		$headerLength = strlen($content);
		$type = '';
		$noiseLength = 0;

		for ($i = 0; $i < $headerLength; $i++) {
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

		$result = $this->Parser->list->stack($this->line, $level, $content, $type);

		if (isset($result)) {
			$this->skipBr = true;
			return $result;
		}
		return '';
	}

	function hr() //---
	{
		$this->line++;
		$this->skipBr = true;
		return '<hr />';
	}

	function line($ch)
	{
		$this->line++;
		$skipBr = $this->skipBr;
		$this->skipBr = false; //skipBr must always must be false when done processing line

		//The first \n was inserted just before parse
		if ($this->isFirstBr == false) {
			$this->isFirstBr = true;
			return '';
		}

		$result = $ch;

		if ($skipBr == false && empty($this->tableStack) && $this->nonBreakingTagDepth == 0) {
			$result = "<br />" . $ch;
		}

		return $result;
	}

	function forcedLineEnd()
	{
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
		global $tikilib, $prefs;

		$parts = explode('|', $content);
		$link = (isset($parts[0]) ? $parts[0] : $content);
		$text = (isset($parts[1]) ? $parts[1] : $link);

		$target = '';
		$class = 'wiki';
		$ext_icon = '';
		$rel = '';
		$cached = '';

		if ($prefs['popupLinks'] == 'y') {
			$target = '_blank"';
		}

		if (!strstr($link, '://')) {
			$target = '';
		} else {
			$class .= ' external';
			if ($prefs['feature_wiki_ext_icon'] == 'y' && !$this->getOption('suppress_icons')) {
				$smarty = TikiLib::lib('smarty');
				include_once('lib/smarty_tiki/function.icon.php');
				$ext_icon = smarty_function_icon(array('_id'=>'external_link', 'alt'=>tra('(external link)'), '_class' => 'externallink', '_extension' => 'gif', '_defaultdir' => 'img/icons', 'width' => 15, 'height' => 14), $smarty);
			}
			$rel='external';
			if ($prefs['feature_wiki_ext_rel_nofollow'] == 'y') {
				$rel .= ' nofollow';
			}
		}

		if ($prefs['cachepages'] == 'y' && $tikilib->is_cached($link)) {
			$cached = " <a class=\"wikicache\" target=\"_blank\" href=\"tiki-view_cache.php?url=".urlencode($link)."\">(cache)</a>";
		}

		return '<a class="' . $class . '"' .
			(!empty($target) ? ' target="' . $target . '"' : '') .
			' href="' . $link .
			(!empty($rel) ? '" rel="' . $rel : '') . '">' . $text . '</a>' . $ext_icon . $cached;
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

	function doubleDash()
	{
		return ' &mdash; ';
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

	function wikilink($type, $content) //((content|content))
	{
		$wikilink = explode('|', $content);
		$href = (isset($wikilink[0]) ? $wikilink[0] : $content);
		$text = (isset($wikilink[1]) ? $wikilink[1] : $href);

		$type = strtolower($type);

		if ($type == 'alias') {
			return '<a class="wiki wiki_page alias" title="Tiki9" href="tiki-index.php?page=' . $href . '">' . $text . '</a>';
		} else if (strlen($type)) {

		}
		return '<a class="wiki" title="' . $text . '" href="tiki-index.php?page=' . $href . '">' . $text . '</a>';
	}

	function comment($content)
	{
		return '<!---->';
	}

	public $blocks = array(
		"header" => array('!'),

		"stackList" => array('*','#','+',';'),

		"r2l" => array('{r2l}'),
		"l2r" => array('{l2r}'),
	);

	function block($content)
	{
		$this->line++;
		$this->skipBr = false;

		$content = ltrim($content, "\n\r");

		foreach ($this->blocks as $function => &$set) {
			foreach ($set as &$startsWith) {
				if ($this->beginsWith($content, $startsWith)) {
					return $this->$function($content);
				}
			}
		}

		return $content;
	}

	function beginsWith($string, $search)
	{
		return (strncmp($string, $search, strlen($search)) === 0);
	}


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
