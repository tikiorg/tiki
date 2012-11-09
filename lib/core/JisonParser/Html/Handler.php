<?php
class JisonParser_Html_Handler extends JisonParser_Html
{
	public $parsing = false;
	public $parseDepth = 0;
	private $parserDebug = true;
	private $lexerDebug = true;

	/* html tag tracking */
	public $typeIndex = array();
	public $htmlElementStack = array();
	public $htmlElementStackCount = 0;
	public $typeStack = array();
	public $stash = array();

	public $blockSyntax = array(
		"\n!",
		"\n*",
		"\n#",
		"\n+",
		"\n;",
		"\n{r2l}",
		"\n{l2r}",
	);


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
	public function __construct(JisonParser_Html &$Parser = null)
	{
		if (empty($Parser)) {
			$this->Parser = &$this;
		} else {
			$this->Parser = &$Parser;
		}

		parent::__construct();
	}

	public function preParse(&$input)
	{
		if ($this->Parser->parseDepth == 0) {
			$this->Parser->typeIndex = array();
			$this->Parser->typeStack = array();
			$this->Parser->type = array();
		}

		$this->htmlElementStack = array();
		$this->htmlElementStackCount = 0;
	}

	public function parse($input)
	{
		if ($this->parsing == true) {
			$class = get_class($this->Parser);
			$parser = new $class($this->Parser);
			$output = $parser->parse($input);
			unset($parser);
		} else {
			$this->parsing = true;

			$this->preParse($input);

			$this->Parser->parseDepth++;
			//echo "parsing:'{$input}'";
			$output = parent::parse($input);
			$this->Parser->parseDepth--;

			$this->parsing = false;
			$this->postParse($output);
		}

		return $output;
	}

	public function postParse(&$output)
	{
		/* While parsing we add a "\n" to the beginning of all block types, but if the input started with a block char,
		 * it is also valid, but treated and restored as with "\n" just before it, here we remove that extra "\n" but
		 * only if we are a block, which are determined from $this->blockChars
		*/
		if ($this->Parser->parseDepth == 0) {
			foreach($this->blockSyntax as $syntax) {
				if (strpos($output, $syntax) === 0) {
					$output = substr($output, 1); //we only want to get rid of "\n", not the whole syntax
				}
			}
		}
	}

	public function toWiki($tag, $contents = null)
	{
		//helpers
		if ($this->hasClass($tag, "jpwch")) {
			return "";
		}

		//non wiki tags are ignored
		if (!$this->hasClass($tag, "jpwc")) {
			switch($tag['state']) {
				case "closed":
					$element = $tag['open'] . $this->parse($contents) . $tag['close'];
					break;
				case "repaired":
					$element = $tag['open'] . $this->parse($contents);
					break;
				case "inline":
					$element = $tag['open'];
					break;
			}

			return $element;
		}

		$result = '';

		if (!isset($this->Parser->typeStack[$tag['type']])) {
			$this->Parser->typeStack[$tag['type']] = 0;
		}
		$this->Parser->typeStack[$tag['type']]++;


		if (!isset($this->Parser->typeIndex[$tag['type']])) {
			$this->Parser->typeIndex[$tag['type']] = 0;
		}
		$this->Parser->typeIndex[$tag['type']]++;

		switch($tag['type'])
		{
			//plugin
			case "plugin":
				$result = urldecode($this->param($tag, 'data-syntax'));
				break;


			//start block type
			//header
			case "header":
				$hCount = $this->param($tag, 'data-count');
				$hCount = (empty($hCount) == true ? (int)substr($tag['name'], 1) : $hCount);
				$result = "\n" . str_repeat("!", $hCount) . $this->parse($contents);
				break;


			//list
			case "listParent":
				$result .= $this->parse($contents);
				$stash = $this->unStashStatic("listParent");
				end($stash);
				$lastKey = key($stash);
				reset($stash);

				foreach($stash as $key => $list) {
					$result .= "\n" . $list['symbol'] . $this->parse($list['contents']);
				}

				break;
			case "listEmpty":
				$result .= $this->parse($contents);
				break;
			case "listUnordered":
			case "listOrdered":
			case "listToggleUnordered":
			case "listToggleOrdered":
			case "listBreak":
				$symbols = array(
					"listUnordered" => "*",
					"listOrdered" => "#",
					"listToggleUnordered" => "*",
					"listToggleOrdered" => "#",
					"listBreak" => "+",
					"listEmpty" =>  ""
				);
				$depth = $this->typeDepth("listParent");

				$symbol = str_repeat($symbols[$tag['type']], $depth);

				if (strstr($tag['type'], "Toggle") !== false) {
					$symbol .= '-';
				}

				$this->stashStatic(array('symbol' => $symbol, 'contents' => ltrim($contents, "\n\r")), 'listParent');
				break;


			//definition list
			case "listDefinitionParent":
				$contents = $this->parse($contents);
				$stash = $this->unStash("listDefinitionParent");
				foreach($stash as $list) {
					if (isset($list[0])) {
						$result .= "\n;" . $list[0] . (isset($list[1]) ? ":" . $list[1] : '');
					}
				}
				break;
			case "listDefinition":
				$this->stash(array(ltrim($contents, "\n\r")), "listDefinitionParent");
				break;
			case "listDefinitionDescription":
				$stash = $this->unStash("listDefinitionParent");
				$stash[max(count($stash) - 1, 0)][] = $contents;
				$this->replaceStash($stash, "listDefinitionParent");
				break;


			//l2r
			case "l2r":
				$result = "\n{l2r}" . $this->parse($contents);
				break;
			//r2l
			case "r2l":
				$result = "\n{r2l}" . $this->parse($contents);
				break;
			//end block type


			//noParse
			case "noParse":
				$result = $this->statedSyntax($tag, "~np~", $this->parse($contents) , "~/np~");
				break;

			//comment
			case "comment":
				$result = $this->statedSyntax($tag, "~tc~", $this->parse($contents) , "~/tc~");
				break;

			//doubleDash
			case "doubleDash":
				$result = " -- ";
				break;


			//bold
			case "bold":
				$result = $this->statedSyntax($tag, "__", $this->parse($contents), "__");
				break;


			//italic
			case "italic":
				$result = $this->statedSyntax($tag, "''", $this->parse($contents), "''");
				break;


			//table
			case "table":
				$contents = $this->parse($contents);
				$rows = $this->unStashStatic("table");
				$result = $this->statedSyntax($tag, "||", implode("\n", $rows), "||");
				break;
			case "tableRow":
				$contents = $this->parse($contents);
				$columns = $this->unStashStatic('tableRow');
				$this->stashStatic(implode("|", $columns), "table");
				break;
			case "tableData":
				$contents = $this->parse($contents);
				$this->stashStatic($contents, 'tableRow');
				break;


			//strike
			case "strike":
				$result = $this->statedSyntax($tag, "--", $this->parse($contents), "--");
				break;


			//center
			case "center":
				$result = $this->statedSyntax($tag, "::", $this->parse($contents), "::");
				break;

			//code
			case "code":
				$result = $this->statedSyntax($tag, "-+", $this->parse($contents), "+-");
				break;


			//horizontal row
			case "horizontalRow":
				$result = "---";
				break;


			//underline
			case "underscore":
				$result = $this->statedSyntax($tag, "===", $this->parse($contents), "===");
				break;

			//center
			case "center":
				$result = $this->statedSyntax($tag, "::", $this->parse($contents), "::"); //TODO: add in 3 ":::" if prefs need it
				break;

			//line
			case "line":
				$this->skipNewLine = true;
				$result = $this->newLine();
				break;
			case "forcedLineEnd":
				$result = "%%%";
				break;


			//box
			case "box":
				$result = $this->statedSyntax($tag, "^", $this->parse($contents), "^");
				break;


			//color
			case "color":
				$result = $this->statedSyntax($tag, "~~", $this->style($tag, "color") . ":" . $this->parse($contents), "~~");
				break;

			//pre
			case "preFormattedText":
				$result = $this->statedSyntax($tag, "~pp~", $this->parse($contents), "~/pp~");
				break;

			//titleBar
			case "titleBar":
				$result = $this->statedSyntax($tag, "-=", $this->parse($contents), "=-");
				break;




			//links
			case "link": //TODO: finish implementation, need to handle alias description
				$page = $this->param($tag, 'data-page');
				$contents = $this->parse($contents);
				if ($page != $contents) {
					$page .= '|' . $contents;
				}
				$reltype = $this->param($tag, 'data-reltype');
				$result = $this->statedSyntax($tag, "($reltype(", $page, "))");
				break;
			case "linkWord":
				$result = trim($contents);
				break;
			case "linkNp":
				$result = $this->statedSyntax($tag, "))", $this->parse($contents), "((");
				break;
			case "linkExternal":
				$href = $this->param($tag, 'href');
				$contents = $this->parse($contents);
				if (!empty($href) && $href != $contents) {
					$href .= '|';
				} else {
					$href = '';
				}

				$result = $this->statedSyntax($tag, "[" , $href . $contents , "]");
				break;

			//unlink
			case "unlink":
				$result = $this->parse($contents);
				break;

			//unhandled
			default:
				throw new Exception("Unhandled type:" . $tag['type']);
		}

		$this->Parser->typeStack[$tag['type']]--;

		return $result;
	}

	private function statedSyntax($tag, $open, $contents, $close)
	{
		if ($this->param($tag, 'data-repair')) {
			$result = $open . $contents;
		} else {
			$result = $open . $contents . $close;
		}

		return $result;
	}

	public function content($content)
	{
		return $content;
	}

	public function lineEnd($line)
	{
		return $line;
	}

	public function newLine()
	{
		if ($this->skipNewLine == false) {
			return "\n";
		}

		$this->skipNewLine = false;
		return "";
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

	public function stashStatic($whatToStash, $id)
	{
		if (!isset($this->Parser->stash[$id])) {
			$this->Parser->stash[$id] = array();
		}

		$this->Parser->stash[$id][] = $whatToStash;
	}

	public function replaceStashStatic($array = array(), $id)
	{
		$this->Parser->stash[$id] = $array;
	}

	public function unStashStatic($id)
	{
		$stash = array();

		if (isset($this->Parser->stash[$id])) {
			$stash = $this->Parser->stash[$id];
			unset($this->Parser->stash[$id]);
		}

		return (isset($stash) ? $stash : array());
	}

	public function stash($whatToStash, $type)
	{
		$this->stashStatic($whatToStash, $type . $this->typeDepth($type));
	}

	public function replaceStash($array = array(), $type)
	{
		$this->replaceStashStatic($array, $type . $this->typeDepth($type));
	}

	public function unStash($type)
	{
		return $this->unStashStatic($type . $this->typeDepth($type));
	}

	public function typeDepth($type)
	{
		return (isset($this->Parser->typeStack[$type]) ? $this->Parser->typeStack[$type] : -1);
	}

	public function hasClass(&$tag, $class)
	{
		return (isset($tag['params']['class']) && in_array($class, $tag['params']['class']));
	}

	public function paramEquals(&$tag, &$param, $equals)
	{
		return (isset($tag['params'][$param]) && strtolower($tag['params'][$param]) == strtolower($equals));
	}

	public function param(&$tag, $param)
	{
		return (isset($tag['params'][$param]) ? $tag['params'][$param] : '');
	}

	public function styleEquals(&$tag, $style, $equals)
	{
		return (isset($tag['params']['style'][$style]) && strtolower($tag['params']['style'][$style]) == strtolower($equals));
	}

	public function style(&$tag, $style)
	{
		if (isset($tag['params']['style'][$style])) {
			return $tag['params']['style'][$style];
		}
		return '';
	}

	public function hasStyle(&$tag, $style)
	{
		return isset($tag['params']['style'][$style]);
	}

	public function paramContains(&$tag, $param, $contains)
	{
		return (isset($tag['params'][$param]) && strstr($tag['params'][$param], $contains) !== false);
	}

	public function stackHtmlElement($tag)
	{
		$tag = $this->tag($tag);
		$this->htmlElementStack[] = $tag;
		$this->htmlElementStackCount++;
	}

	public function unStackHtmlElement($ending = '')
	{
		$this->htmlElementStackCount--;
		$element = array_pop($this->htmlElementStack);
		$element['close'] = $ending;
		if ($element['state'] == 'open') {
			$element['state'] = 'closed';
		}

		if (!empty($element['type'])) {
			array_pop($this->Parser->typeIndex);
		}

		return $element;
	}

	public function tag($tag)
	{
		$parts = preg_split("/[ >]/", substr($tag, 1)); //<tag> || <tag name="">
		$name = array_shift($parts);
		$name = strtolower(trim($name));
		$end = array_pop($parts);
		$params = implode(" ", $parts);
		$params = $this->parseElementParameters($params);
		$type = "";

		if (isset($params['data-t'])) {
			$type = JisonParser_WikiCKEditor_Handler::typeFromShorthand(strtolower($params['data-t']));
		}

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

	public function compareElementClosingToYytext($tag, $yytext)
	{
		$yytext = strtolower(str_replace(' ', '', $yytext));
		if ($yytext == strtolower("</" . $tag['name'] . ">")) {
			return true;
		}
	}
}