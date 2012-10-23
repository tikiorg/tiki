<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Wiki Handler for the JisonParser_Wiki parser.
 *
 * @category    JisonParser_Wiki_Handler
 * @author      Robert Plummer <robert@tiki.org>
 * @version     CVS: $Id$
 */

class JisonParser_Wiki_Handler extends JisonParser_Wiki
{
	/* parser tracking */
	private $parsing = false;
	private static $spareParsers = array();
	public $parseDepth = 0;

	/* the root parser, where many variables need to be tracked from, maintained on any hierarchy of children parsers */
	public $Parser;

	/* parser debug */
	public $parserDebug = false;
	public $lexerDebug = false;

	/* plugin tracking */
	public $pluginStack = array();
	public $pluginStackCount = 0;
	public $pluginEntries = array();
	public $plugins = array();
	public static $pluginIndexes = array();
	public $pluginNegotiator;

	/* np tracking */
	public $npStack = false; //There can only be 1 active np stack

	/* pp tracking */
	public $ppStack = false; //There can only be 1 active np stack

	/* link tracking*/
	public $linkStack = false; //There can only be 1 active link stack

	/* used in block level items, should be set to true if the next line needs skipped of a <br />
	The next break sets it back to false; */
	public $skipBr = false;
	public $tableStack = array();

	/* header tracking */
	public $header;
	public $headerStack = false;

	/* list tracking and parser */
	public $list;

	/* autoLink parser */
	public $autoLink;

	/* wiki link parser */
	public $link;

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
	private $pcreRecursionLimit;

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
		'parseSmileys'=> true,
		'namespace' => null,
		'skipPageCache' => false,
	);

	/**
	 * Change options
	 *
	 * @access  public
	 * @param   array  $option an array of options, key being the option name and value being the value to be set
	 */
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

	/**
	 * Access single option
	 *
	 * @access  public
	 * @param   string  $name name/key of option
	 * @return  mixed   value of option or false if not set
	 */
	public function getOption($name = '')
	{
		if (isset($this->Parser->option[$name])) {
			return $this->Parser->option[$name];
		} else {
			return false;
		}
	}

	/**
	 * Reset all options to default value
	 *
	 * @access  public
	 */
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

	/**
	 * construct
	 *
	 * @access  public
	 * @param   JisonParser_Wiki_Handler  $Parser Filename to be used
	 */
	public function __construct(JisonParser_Wiki_Handler &$Parser = null)
	{
		global $user;

		$this->user = (isset($user) ? $user : tra('Anonymous'));

		if (empty($Parser)) {
			$this->Parser = &$this;
		} else {
			$this->Parser = &$Parser;
		}

		if (isset($this->pluginNegotiator) == false) {
			$this->pluginNegotiator = new WikiPlugin_Negotiator_Wiki($this->Parser);
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

	/**
	 * Where a parse generally starts.  Can be self-called, as this is detected, and if nested, a new parser is instantiated
	 *
	 * @access  private
	 * @param   string  $input Wiki syntax to be parsed
	 * @return  string  $output Parsed wiki syntax
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

	/**
	 * Parse a plugin's body.  public so that negotiator can use.  option 'noparseplugins' makes this function return the body without parse.
	 *
	 * @access  public
	 * @param   string  $input Plugin body
	 * @return  string  $output Parsed plugin body or $input if not parsed
	 */
	public function parsePlugin($input)
	{
		if (empty($input)) return "";

		if ($this->getOption('noparseplugins') == false) {

			$is_html = $this->getOption('is_html');

			if ($is_html == false) {
				$this->setOption(array('is_html' => true));
			}

			$output = $this->parse($input);

			if ($is_html == false) {
				$this->setOption(array('is_html' => $is_html));
			}

			return $output;
		} else {
			return $input;
		}
	}


	/**
	 * Event just before JisonParser_Wiki->parse(), used to ready parser, ensuring defaults needed for parsing are set.
	 * <p>
	 * pcre.recursion_limit is temporarily changed here. php default is 100,000 which is just too much for this type of
	 * parser. The reason for this code is the use of preg_* functions using pcre library.  Some of the regex needed is
	 * just too much for php to handle, so by limiting this for regex we speed up the parser and allow it to safely
	 * lex/parse a string more here: http://stackoverflow.com/questions/7620910/regexp-in-preg-match-function-returning-browser-error
	 *
	 * @access  private
	 * @param   string  &$input input that will be parsed
	 */
	private function preParse(&$input)
	{
		if ($this->Parser->parseDepth == 0) {
			$this->pcreRecursionLimit = ini_get("pcre.recursion_limit");
			ini_set("pcre.recursion_limit", "524");

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

	/**
	 * Event just after JisonParser_Wiki->parse(), used to ready parser, ensuring defaults needed for parsing are set.
	 * <p>
	 * pcre.recursion_limit is reset here if parser depth is 0 (ie, no nested parsing)
	 *
	 * @access  private
	 * @param   string  &$output parsed output of wiki syntax
	 */
	function postParse(&$output)
	{
		//remove comment artifacts
		$output = str_replace("<!---->", "", $output);

		//Replace the break we put at the beginning
		//$output = preg_replace("/^(([<]br [\/][>])?([\n][\r]|[\n\r]))/", "", $output);
		$output = preg_replace("/(([<]br [\/][>])?([\n][\r]|[\r][\n]|[\n\r]))$/", "", $output);

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

		if ($this->Parser->parseDepth == 0) {
			ini_set("pcre.recursion_limit", $this->pcreRecursionLimit);
			$output = $this->unprotectSpecialChars($output);
		}
	}

	/**
	 * Handles plugins directly from the wiki parser.  A plugin can be on a different level of the current parser, and
	 * if so, the execution is delayed until the parser reaches that level.
	 *
	 * @access  private
	 * @param   array  &$pluginDetails plugins details in an array
	 * @return  string  either returns $key or block from execution message
	 */
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

	/**
	 * Increments the plugin index, but on a plugin type by type basis, for example, html1, html2, div1, div2.  indexes
	 * are static, so that all index are unique
	 *
	 * @access  private
	 * @param   string  $name plugin name
	 * @return  string  $index
	 */
	private function incrementPluginIndex($name)
	{
		$name = strtolower($name);

		if (isset(self::$pluginIndexes[$name]) == false) self::$pluginIndexes[$name] = 0;

		self::$pluginIndexes[$name]++;

		return self::$pluginIndexes[$name];
	}

	/**
	 * Key of the plugin, an md5 signature of  ('§' . $name . $index . '§').  This technique is used so that line breaks
	 * can be inserted without distorting the content found in the plugin, and to limit what is parser, thus speeding
	 * the parser up, less syntax to analyse
	 *
	 * @access  private
	 * @param   string  $name plugin name
	 * @return  string  $key
	 */
	private function pluginKey($name)
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

	/**
	 * Stacks plugins for execution, since plugins can be called within each other.  Public because called directly by
	 * the lexer of the wiki parser
	 *
	 * @access  public
	 * @param   string  $yytext The analysed text from the wiki parser
	 */
	public function stackPlugin($yytext)
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

	/**
	 * Detects if we are in a state that we can call the lexed grammer 'content'.  Since the execution technique from
	 * the parser is inside-out, this helps us reverse the execution from outside-in in some cases.
	 *
	 * @access  public
	 * @param   array  $skipTypes List of different ignourable stack types found on $this, like npStack, ppStack, or lineStack
	 * @return  string  true if content is current not parse-able
	 */
	public function isContent($skipTypes = array())
	{
		//These types will be found in $this.  If any of these states are active, we should NOT parse wiki syntax
		$types = array(
			'npStack' => true,
			'ppStack' => true,
			'linkStack' => true
		);

		foreach($skipTypes as $skipType) {
			if (isset($types[$skipType])) {
				unset($types[$skipType]);
			}
		}

		//first off, if in plugin
		if ($this->pluginStackCount > 0) {
			return true;
		}

		//second, if we are not in a plugin, check if we are in content, ie, non-parse-able wiki syntax
		foreach($types as $type => $value) {
			if ($this->$type == $value)	{
				return true;
			}
		}

		//lastly, if we are not in content, return null, which allows cases to continue lexing
		return null;
	}

	/**
	 * Removed any entity (plugin, list, header) from an input
	 *
	 * @param   string  $input The analysed text from the wiki parser
	 */
	static function deleteEntities(&$input)
	{
		$input = preg_replace('/§[a-z0-9]{32}§/', '', $input);
	}

	/**
	 * restores the plugins back into the string being parsed.
	 *
	 * @access  private
	 * @param   string  $output Parsed syntax
	 */
	private function restorePluginEntities(&$output)
	{
		//use of array_reverse, jison is a reverse bottom-up parser, if it doesn't reverse jison doesn't restore the plugins in the right order, leaving the some nested keys as a result
		array_reverse($this->pluginEntries);
		$iterations = 0;
		$limit = 100;

		while (!empty($this->pluginEntries) && $iterations <= $limit) {
			$iterations++;
			foreach ($this->pluginEntries as $key => $entity) {
				if (strstr($output, $key)) {
					if ($this->getOption('stripplugins') == true) {
						$output = str_replace($key, '', $output);
					} else {
						$output = str_replace($key, $entity, $output);
					}
				}
			}
		}

		if ($this->Parser->parseDepth == 0) {
			$this->pluginNegotiator->executeAwaiting($output);
		}
	}

	/**
	 * used to protect special characters temporarily, so that they cannot be decoded or encoded.  Later we can
	 * unprotect them to what they were or to an alternate character
	 *
	 * @access  public
	 * @param   string  $input unparsed syntax
	 * @return  string  $input protected
	 */
	public function protectSpecialChars($input)
	{
		if (
			$this->isHtmlPurifying == true ||
			$this->getOption('is_html') == false
		) {
			foreach ($this->specialChars as $key => $specialChar) {
				$input = str_replace($specialChar['html'], $key, $input);
			}
		}

		return $input;
	}

	/**
	 * used to unprotect special characters possibly with an alternate character
	 *
	 * @access  public
	 * @param   string  $input unparsed syntax
	 * @param   bool  $is_html true for html context, false for non-html context
	 * @return  string  $input protected
	 */
	public function unprotectSpecialChars($input, $is_html = false)
	{
		if (
			$is_html == true ||
			$this->getOption('is_html') == true
		) {
			foreach ($this->specialChars as $key => $specialChar) {
				$input = str_replace($key, $specialChar['html'], $input);
			}
		} else {
			foreach ($this->specialChars as $key => $specialChar) {
				$input = str_replace($key, $specialChar['nonHtml'], $input);
			}
		}

		return $input;
	}


	//end state handlers
	//Wiki Syntax Objects Parsing Start
	/**
	 * syntax handler: noparse, ~np~$content~/np~
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	public function np($content)
	{
		if ( $this->getOption('parseNps') == true) {
			$content = $this->unprotectSpecialChars($content);
		}

		return $content;
	}

	/**
	 * syntax handler: pre, ~pp~$content~/pp~
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function pp($content)
	{
		return "<pre>" . $content . "</pre>";
	}

	/**
	 * syntax handler: generic html
	 * <p>
	 * Used in detecting if we need a break, and line number in some cases
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
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

	/**
	 * syntax handler: double dynamic variable, %%$content%%
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function doubleDynamicVar($content)
	{
		global $prefs;

		if ( $prefs['wiki_dynvar_style'] != 'double') {
			return $content;
		}


		return $this->Parser->dynamicVar->ui(substr($content, 2, 2),  $this->getOption('language'));
	}

	/**
	 * syntax handler: single dynamic variable, %$content%
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function singleDynamicVar($content)
	{
		global $prefs;

		if ( $prefs['wiki_dynvar_style'] != 'single') {
			return $content;
		}


		return $this->Parser->dynamicVar->ui(substr($content, 1, 1),  $this->getOption('language'));
	}

	/**
	 * syntax handler: argument variable, {{$content}}
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
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

	/**
	 * syntax handler: bold/strong, __$content__
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function bold($content) //__content__
	{
		return '<strong>' . $content . '</strong>';
	}

	/**
	 * syntax handler: simple box, ^$content^
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function box($content) //^content^
	{
		return '<div class="simplebox">' . $content . '</div>';
	}

	/**
	 * syntax handler: center, ::$content::
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function center($content) //::content::
	{
		return '<div style="text-align: center;">' . $content . '</div>';
	}

	/**
	 * syntax handler: code, -+$content+-
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function code($content)
	{
		return "<code>" . $content . "</code>";
	}

	/**
	 * syntax handler: text color, ~~$color:$content~~
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function color($content)
	{
		$text = explode(':', $content);
		$color = $text[0];
		$content = $text[1];

		return '<span style="color: ' . $color . ';">' . $content . '</span>';
	}

	/**
	 * syntax handler: italics/emphasis, ''$content''
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function italics($content) //''content''
	{
		return '<em>' . $content . '</em>';
	}

	/**
	 * syntax handler: left to right, {l2r}$content\n
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function l2r($content)
	{
		$content = substr($content, 5);
		return "<div dir='ltr'>" . $content . "</div>";
	}

	/**
	 * syntax handler: right to left, {r2l}$content\n
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function r2l($content)
	{
		$content = substr($content, 5);
		return "<div dir='rtl'>" . $content . "</div>";
	}

	/**
	 * syntax handler: header, !$content\n
	 * <p>
	 * Uses $this->Parser->header as a processor.  Is called from $this->block().
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
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
		$id = $this->Parser->header->stack($hNum, $content);
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

	/**
	 * syntax handler: list, *$content\n
	 * <p>
	 * List types: * (unordered), # (ordered), + (line break), - (expandable), ; (definition list)
	 * <p>
	 * Uses $this->Parser->list as a processor. Is called from $this->block().
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
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

	/**
	 * syntax handler: horizontal row, ---
	 *
	 * @access  public
	 * @return  string  html hr element
	 */
	function hr() //---
	{
		$this->line++;
		$this->skipBr = true;
		return '<hr />';
	}

	/**
	 * syntax handler: new line, \n
	 * <p>
	 * Detects if a line break is needed and returns it. If $this->skipBr is set to true, skips output of <br /> and
	 * sets it back to false for the next line to process
	 *
	 * @access  public
	 * @param   $ch line line character
	 * @return  string  $result of line process
	 */
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

	/**
	 * syntax handler: forced line end, %%%
	 * <p>
	 * Note: does not affect line number
	 *
	 * @access  public
	 * @return  string  html break, <br />
	 */
	function forcedLineEnd()
	{
		return '<br />';
	}

	/**
	 * syntax handler: unlink, [[$content|$content]]
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function unlink($content) //[[content|content]
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

	/**
	 * syntax handler: link, [$content|$content]
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function link($type, $content) //[content|content]
	{
		global $tikilib, $prefs;

		$parts = explode('|', $content);
		$page = (isset($parts[0]) ? $parts[0] : $content);
		array_shift($parts);
		$description = implode('|', $parts);

		if (!empty($description)) {
			$feature_wikiwords = $prefs['feature_wikiwords'];
			$prefs['feature_wikiwords'] = 'n';
			$description = $this->parse($description);
			$prefs['feature_wikiwords'] = $feature_wikiwords;
		}

		return JisonParser_Wiki_Link::page($page, $this->Parser)
			->setNamespace($this->getOption('namespace'))
			->setDescription($description)
			->setType($type)
			->setSuppressIcons($this->getOption('suppress_icons'))
			->setSkipPageCache($this->getOption('skipPageCache'))
			->parse();
	}

	/**
	 * syntax handler: smile, :)
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function smile($content)
	{
		//this needs more tlc too
		return '<img src="img/smiles/icon_' . $content . '.gif" alt="' . $content . '" />';
	}

	/**
	 * syntax handler: strike, --$content--
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function strike($content) //--content--
	{
		return '<strike>' . $content . '</strike>';
	}

	/**
	 * syntax handler: double dash, --
	 *
	 * @access  public
	 * @return  dash characters
	 */
	function doubleDash()
	{
		return ' &mdash; ';
	}

	/**
	 * syntax handler: table, ||$content|$content\n$content|$content||
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
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

	/**
	 * syntax handler table helper for tr
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	private function table_tr($content)
	{
		return '<tr>' . $content . '</tr>';
	}

	/**
	 * syntax handler table helper for td
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	private function table_td($content)
	{
		return '<td class="wikicell">' . $content . '</td>';
	}

	/**
	 * syntax handler: titlebar, -=$content=-
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function titlebar($content) //-=content=-
	{
		$this->skipBr = true;

		return '<div class="titlebar">' . $content . '</div>';
	}

	/**
	 * syntax handler: underscore, ===$content===
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function underscore($content) //===content===
	{
		return '<u>' . $content . '</u>';
	}

	/**
	 * syntax handler: wiki link, (($content)) or ))$content(( or WordWord, if surrounded by (()) or ))((, a pipe can be used at the text for the link
	 * <p>
	 * Alternate syntax: (($href|$text))
	 * Alternate syntax: ($type($href|$text))
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function wikilink($type = '', $content) //((content|content))
	{
		global $prefs;
		//DEPRICATED
		/*
		$wikilink = explode('|', $content);

		$page = (isset($wikilink[0]) ? $wikilink[0] : $content);
		$title = $content;
		$text = $content;

		if (isset($wikilink[1])) {
			array_shift($wikilink); //get rid of the beginning, which is the wiki link

			$title = implode('|', $wikilink); //prepare for parsing

			$parser = new self();
			$feature_wikiwords = $prefs['feature_wikiwords'];
			$prefs['feature_wikiwords'] = 'n';
			$text = $parser->parse($title); //NOTE: We parse the text, so we can be flexible with syntax
			$prefs['feature_wikiwords'] = $feature_wikiwords;
		}

		$title = addslashes(htmlspecialchars($title));

		$type = strtolower($type);

		return JisonParser_Wiki_Link::page($page)
			->setNameSpace($this->getOption('namespace'))
			->setType($type);*/
	}

	/**
	 * syntax handler: tiki comment, ~tc~$content~/tc~
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
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

	/**
	 * syntax handler: block, \n$content\n
	 *
	 * @access  public
	 * @param   $content parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
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

	/**
	 * helper function to detect what is at the beginning of a string
	 *
	 * @access  public
	 * @param   $haystack
	 * @param   $needle
	 * @return  bool  true if found at beginning, false if not
	 */
	function beginsWith($haystack, $needle)
	{
		return (strncmp($haystack, $needle, strlen($needle)) === 0);
	}

	/**
	 * helper function to detect a match in string
	 *
	 * @access  public
	 * @param   $pattern
	 * @param   $subject
	 * @return  bool  true if found at beginning, false if not
	 */
	function match($pattern, $subject)
	{
		preg_match($pattern, $subject, $match);

		return (!empty($match[1]) ? $match[1] : false);
	}
}
