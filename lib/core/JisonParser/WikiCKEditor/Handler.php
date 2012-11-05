<?php

/**
 *
 */
class JisonParser_WikiCKEditor_Handler extends JisonParser_Wiki_Handler
{
	/* wiki syntax type tracking */
	static $typeIndex = array();
	static $typeShorthand = array(
		"preFormattedText" =>           "pp",
		"bold" =>                       "b",
		"simpleBox" =>                  "sb",
		"center" =>                     "c",
		"noParse" =>                    "np",
		"code" =>                       "cd",
		"color" =>                      "clr",
		"italics" =>                    "i",
		"l2r" =>                        "l2r",
		"r2l" =>                        "r2l",
		"header" =>                     "hdr",
		"list" =>                       "lst",
		"line" =>                       "ln",
		"forcedLineEnd" =>              "fln",
		"unlink" =>                     "ul",
		"externalLink" =>               "el",
		"wikiLink" =>                   "wl",
		"strike" =>                     "stk",
		"doubleDash" =>                 "dd",
		"table" =>                      "tbl",
		"tableRow" =>                   "tblr",
		"tableData" =>                  "tbld",
		"titleBar" =>                   "tb",
		"underscore" =>                 "u",

	);

	function __construct()
	{
		parent::__construct();

		$this->Parser->specialCharacter = new JisonParser_WikiCKEditor_SpecialChar($this->Parser);

		$this->Parser->htmlCharacter = new JisonParser_WikiCKEditor_HtmlCharacter($this->Parser);

		$this->pluginNegotiator = new WikiPlugin_Negotiator_CKEditor($this->Parser);
	}

	function setOption($option = array())
	{
		parent::setOption($option);
	}

	//end state handlers
	//Wiki Syntax Objects Parsing Start
	/**
	 * syntax handler: noparse, ~np~$content~/np~
	 *
	 * @access  public
	 * @param   $content string parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	public function noParse($content)
	{
		return $this->createWikiTag("noParse", "span", parent::noParse($content), array(
			"class" => "noParse"
		));
	}

	/**
	 * syntax handler: double dynamic variable, %%$content%%
	 *
	 * @access  public
	 * @param   $content string parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function doubleDynamicVar($content)
	{
		return "%%" . $content . "%%";
	}

	/**
	 * syntax handler: single dynamic variable, %$content%
	 *
	 * @access  public
	 * @param   $content string parsed string found inside detected syntax
	 * @return  string  $content desired output from syntax
	 */
	function singleDynamicVar($content)
	{
		return "%" . $content . "%";
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
		return $this->createWikiTag("comment", "span", $content, array(
			"class" => "wikiComment"
		));
	}

	/**
	 * Increments the html tag
	 * are static, so that all index are unique
	 *
	 * @access  private
	 * @param   string  $name plugin name
	 * @return  string  $index
	 */
	static public function incrementTypeIndex($name)
	{
		$name = strtolower($name);

		if (isset(self::$typeIndex[$name]) == false) self::$typeIndex[$name] = 0;

		self::$typeIndex[$name]++;

		return self::$typeIndex[$name];
	}

	/**
	 * Gets wiki syntax type symbol shorthand, cuts down on information needed to send to browser, used in translating html to wiki
	 *
	 * @access  private
	 * @param   string  $name plugin name
	 * @return  string  $index
	 */
	static public function typeShorthand($name)
	{
		return self::$typeShorthand[$name];
	}

	/**
	 * tag helper creation, noise items that will be disposed
	 *
	 * @access  public
	 * @param   $syntaxType string from what syntax type
	 * @param   $tagType string what output tag type
	 * @param   $content string what is inside the tag
	 * @param   $params array what params to add to the tag, array, key = param, value = value
	 * @param   $type default is "standard", of types : standard, inline, open, close
	 * @return  string  $tag desired output from syntax
	 */
	public function createWikiHelper($syntaxType, $tagType, $content = "", $params = array(), $type = "standard")
	{
		if (!isset($params['class'])) {
			$params['class'] = "";
		}

		$params['class'] .= " jpwch"; //Jison Parser Wiki CKEditor Helper tag

		$params['class'] = trim($params['class']);

		return parent::createWikiHelper($syntaxType, $tagType, $content, $params, $type);
	}

	/**
	 * tag creation, should only be used with items that are directly related to wiki syntax, buttons etc, should use createWikiHelper
	 *
	 * @access  public
	 * @param   $syntaxType string from what syntax type
	 * @param   $tagType string what output tag type
	 * @param   $content string what is inside the tag
	 * @param   $params array what params to add to the tag, array, key = param, value = value
	 * @param   $type string the content to be ignored and for tag to close, ie <tag />
	 * @return  string  $tag desired output from syntax
	 */
	public function createWikiTag($syntaxType, $tagType, $content = "", $params = array(), $type = "standard")
	{
		$params['data-i'] = self::incrementTypeIndex($syntaxType);
		$params['data-t'] = self::typeShorthand($syntaxType);

		if (!isset($params['class'])) {
			$params['class'] = "";
		}

		$params['class'] .= " jpwc"; //Jison Parser Wiki CKEditor tag :)

		$params['class'] = trim($params['class']);

		return parent::createWikiTag($syntaxType, $tagType, $content, $params, $type);
	}
}