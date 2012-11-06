<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_Html_Handler extends JisonParser_Html
{
	private $parserDebug = true;
	private $lexerDebug = true;

	/* html tag tracking */
	public $nonBreakingTagDepth = 0;
	public $typeIndex = array();
	public $htmlElementStack = array();
	public $htmlElementStackCount = 0;
	public $typeStack = array();
	public $typeStash = array();

	/* table tracking */
	public $tableRow = 0;
	public $tableData = 0;

	/* list tracking */
	public $isFirstLine = true;
	public $line = 0;

/*
	function parser_performAction(&$thisS, $yytext, $yyleng, $yylineno, $yystate, $S, $_S, $O)
	{
		$result = parent::parser_performAction($thisS, $yytext, $yyleng, $yylineno, $yystate, $S, $_S, $O);
		if ($this->parserDebug == true) {
			$thisS = "{" . $thisS . ":" . $yystate ."}";
		}
		return $result;
	}

	function lexer_performAction(&$yy, $yy_, $avoiding_name_collisions, $YY_START = null) {
		$result = parent::lexer_performAction($yy, $yy_, $avoiding_name_collisions, $YY_START);
		if ($this->lexerDebug == true) {
			echo "{" . $result . ":" .$avoiding_name_collisions . "}" . $yy_->yytext . "\n";
		}
		return $result;
	}

	function parseError($error, $info)
	{
		echo $error;
		print_r($info);
		die;
	}
*/

	public function parse($input)
	{
		$this->nonBreakingTagDepth = 0;
		$this->typeIndex = array();
		$this->tableRow = 0;
		$this->tableData = 0;

		//echo "parsing:" . $input;

		$parsed = parent::parse($input);

		/*if ($parsed{strlen($parsed) - 1} == "\n") {
			$parsed = substr($parsed, 0, -1);
		}*/

		return $parsed;
	}

	public function toWiki($tag, $contents = null)
	{
		switch($tag['state']) {
			case "closed":
				$element = $tag['open'] . $contents . $tag['close'];
				break;
			case "repaired":
				$element = $tag['open'] . $contents;
				break;
			case "inline":
				$element = $tag['open'];
				break;
		}

		//helpers
		if ($this->hasClass($tag['params'], "jpwch")) {
			return "";
		}

		//non wiki tags are ignored
		if (!$this->hasClass($tag['params'], "jpwc")) {
			return $element;
		}

		$result = '';

		switch($tag['type'])
		{
			//header
			case "header":
				$hCount = (int)substr($tag['name'], 1);
				$result = str_repeat("!", min($hCount, 6)) . $contents;
				break;


			//list
			case "listParent":
				foreach ($this->unStash("listParent") as $list) {
					$result .= $this->newLine() . $list;
				}
				break;
			case "listUnordered":
			case "listOrdered":
			case "listToggle":
			case "listBreak":
				$symbol = array(
					"listUnordered" => "*",
					"listOrdered" => "#",
					"listToggle" => "-",
					"listBreak" => "+"
				);

				$depth = $this->typeDepth("listParent");
				$lCount = str_repeat($symbol[$tag['type']], $depth);
				$this->stash($lCount . $contents, "listParent");
				break;


			//definition list
			case "listDefinitionParent":
				$stash = $this->unStash("listDefinitionParent");
				foreach ($stash as $list) {
					$result .= $this->newLine() . ";" . $list;
				}
				break;
			case "listDefinition":
				$this->stash($contents, "listDefinitionParent");
				break;
			case "listDefinitionDescription":
				$stash = $this->unStash("listDefinitionParent");
				$stash[count($stash) - 1] .= ":" . $contents;
				$this->replaceStash($stash, "listDefinitionParent");
				break;


			//noParse
			case "noParse":
				$result .= "~np~" . $contents . "~/np~";
				break;


			//bold
			case "bold":
				$result .= "__" . $contents . "__";
				break;


			//italics
			case "italics":
				$result .= "''" . $contents . "''";
				break;


			//table
			case "table":
				$stash = $this->unStash("table");
				$this->line += count($stash);
				$result .= "||" . implode("\n", $stash) . "||";
				break;
			case "tableRow":
				foreach ($this->unStash('tableRow') as $row) {
					$this->stash(implode("|", $row), "table");
				}
				break;
			case "tableData":
				$this->stash($contents, "tableRow", $this->typeIndex['tableRow']);
				break;


			//strike/del
			case "strike":
				$result .= "--" . $contents . "--";
				break;


			//center
			case "center":
				return "::" . $contents . "::";


			//code
			case "code":
				$result .= "-+" . $contents . "+-";
				break;


			//horizontal row
			case "horizontalRow":
				$result .= "---";
				break;


			//underline
			case "underscore":
				$result .= "===" . $contents . "===";


			//center
			case "center":
				$result .= "::" . $contents . "::"; //TODO: add in 3 ":::" if prefs need it


			//line
			case "line":
				$result .= $this->newLine();
				break;
			case "forcedLineEnd":
				$result .= "%%%";
				break;


			//simple box
			case "simpleBox":
				$result .= "^" . $contents . "^";
				break;


			//color
			case "color":
				$result .= "~~" . $this->style($tag['params'], "color") . ":" . $contents . "~~";
				break;


			//l2r
			case "l2r":
				$result .= "{l2r}" . $contents . $this->newLine();
				break;
			//r2l
			case "r2l":
				$result .= "{r2l}" . $contents . $this->newLine();
				break;



			case "unlink":
				$result .= "))" . $contents . "((";
				break;


			//links
			case "link": //TODO: finish implementation, need to handle alias description
				$result .= "((" . $contents . "))";
				break;
			case "linkExternal":
				$result .= "[" . $contents . "]";
				break;
		}

		array_pop($this->typeStack);

		return $result;
	}

	public function content($content)
	{
		return $content;
	}

	public function lineEnd($line)
	{
		return "";
	}

	public function newLine()
	{
		$this->line++;

		if ($this->isFirstLine == false) {
			$this->isFirstLine = true;
			return "";
		}

		return "\n";
	}

	private function parseElementParameters($params)
	{
		$parsedParams = array();
		if (!empty($params)) {
			$dom = new DOMDocument();
			if ($params{strlen($params) - 1} == "/") {
				$params = substr($params, 0, -1);
			}

			$dom->loadHtml("<object " . $params . " />");
			foreach ($dom->getElementsByTagName("object") as $node) {
				foreach ($node->attributes as $attribute) {
					$parsedParams[trim(strtolower($attribute->name))] = trim($attribute->value);
				}
			}
		}

		if (isset($parsedParams['style'])) {
			$styles = explode(';', $parsedParams['style']);
			$parsedParams['style'] = array();
			foreach ($styles as &$style) {
				$parts = explode(':', $style);
				if (isset($parts[0]) && isset($parts[1])) {
					$parsedParams['style'][trim($parts[0])] = trim($parts[1]);
				}
			}
		}

		if (isset($parsedParams['class'])) {
			$parsedParams['class'] = explode(' ', $parsedParams['class']);
			array_filter($parsedParams['class']);
		}

		return $parsedParams;
	}

	public function stash($whatToStash, $type, $index = -1)
	{
		$depth = $this->typeDepth($type);

		if (!isset($this->typeStash[$type . $depth])) {
			$this->typeStash[$type . $depth] = array();
		}

		if ($index > -1) {
			if (!isset($this->typeStash[$type . $depth][$index])) {
				$this->typeStash[$type . $depth][$index] = array();
			}
			$this->typeStash[$type . $depth][$index][] = $whatToStash;
		} else {
			$this->typeStash[$type . $depth][] = $whatToStash;
		}
	}

	public function replaceStash($array = array(), $type)
	{
		$this->typeStash[$type . $this->typeDepth($type)] = $array;
	}

	public function unStash($type)
	{
		$depth = $this->typeDepth($type);
		$stash = $this->typeStash[$type . $depth];
		unset($this->typeStash[$type . $depth]);
		return (isset($stash) ? $stash : array());
	}

	public function typeDepth($type)
	{
		$types = array_count_values($this->typeStack);
		return (isset($types[$type]) ? $types[$type] : 0);
	}

	public function hasClass(&$params, $class)
	{
		return (isset($params['class']) && in_array($class, $params['class']));
	}

	public function paramEquals(&$params, $param, $equals)
	{
		return (isset($params[$param]) && strtolower($params[$param]) == strtolower($equals));
	}

	public function styleEquals(&$params, $style, $equals)
	{
		return (isset($params['style'][$style]) && strtolower($params['style'][$style]) == strtolower($equals));
	}

	public function style(&$params, $style)
	{
		if (isset($params['style'][$style])) {
			return $params['style'][$style];
		}
		return '';
	}

	public function hasStyle(&$params, $style)
	{
		return isset($params['style'][$style]);
	}

	public function paramContains(&$params, $param, $contains)
	{
		return (isset($params[$param]) && strstr($params[$param], $contains) !== false);
	}

	public function stackHtmlElement($tag)
	{
		$tag = $this->tag($tag);
		$this->htmlElementStack[] = $tag;
		if (!empty($tag['type'])) {
			$this->typeStack[] = $tag['type'];
			if (!isset($this->typeIndex[$tag['type']])) {
				$this->typeIndex[$tag['type']] = 0;
			} else {
				$this->typeIndex[$tag['type']]++;
			}
		}
		$this->htmlElementStackCount++;
	}

	public function tag($tag)
	{
		$parts = preg_split("/[ >]/", substr($tag, 1)); //<tag> || <tag name="">
		$name = array_shift($parts);
		$name = strtolower(trim($name));
		$end = array_pop($parts);
		$params = implode(" ", $parts);
		$params = $this->parseElementParameters($params);

		$type = JisonParser_WikiCKEditor_Handler::typeFromShorthand(strtolower($params['data-t']));

		return array(
			"name" => $name,
			"params" => $params,
			"open" => $tag,
			"state" => "open",
			"type" => $type
		);
	}

	public function inlineTag($tag)
	{
		$tag = $this->tag($tag);
		$tag['state'] = 'inline';
		return $tag;
	}

	public function isLastInHtmlElementStack($yytext)
	{
		$htmlElement = end($this->htmlElementStack);
		$yytext = strtolower(str_replace(' ', '', $yytext));
		if ($yytext == strtolower("</" . $htmlElement['name'] . ">")) {
			return true;
		}
	}
}
