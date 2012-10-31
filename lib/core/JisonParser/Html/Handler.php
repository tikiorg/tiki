<?php
class JisonParser_Html_Handler extends JisonParser_Html
{
	private $parserDebug = true;
	private $lexerDebug = true;

	/* html tag tracking */
	public $nonBreakingTagDepth = 0;
	public $tagIndex = 0;
	public $tagStack = array();
	public $tagStackEndings = array();

	/* content tracking */
	public $tagStackIgnore = array();

	/* table tracking */
	public $tableRow = 0;
	public $tableData = 0;

	/* list tracking */
	public $lastListType;

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
		$this->tagIndex = 0;
		$this->tableRow = 0;
		$this->tableData = 0;

		//echo "parsing:" . $input;

		return parent::parse($input);
	}

	public function htmlTag($name)
	{
		$parts = preg_split("/[ >]/", substr($name, 1)); //<tag> || <tag name="">
		$name = array_shift($parts);
		$end = array_pop($parts);
		$params = implode(" ", $parts);
		$params = $this->parseElementParameters($params);
		$name = strtolower(trim($name));

		switch($name)
		{
			//header
			case "h1": return $this->stack("h1", "!", "\n");
			case "/h1": return $this->unstack("h1");
			case "h2": return $this->stack("h2", "!!", "\n");
			case "/h2": return $this->unstack("h2");
			case "h3": return $this->stack("h3", "!!!", "\n");
			case "/h3": return $this->unstack("h3");
			case "h4": return $this->stack("h4", "!!!!", "\n");
			case "/h4": return $this->unstack("h4");
			case "h5": return $this->stack("h5", "!!!!!", "\n");
			case "/h5": return $this->unstack("h5");
			case "h6": return $this->stack("h6", "!!!!!!", "\n");
			case "/h6": return $this->unstack("h6");
			case "h7": return $this->stack("h7", "!!!!!!!", "\n");
			case "/h7": return $this->unstack("h7");


			//strike/del
			case "strike": return $this->stack("strike", "--", "--");
			case "/strike": return $this->unstack("strike");
			case "del": return $this->stack("del", "--", "--");
			case "/del": return $this->unstack("del");


			//italics/em
			case "i": return $this->stack("i", "''", "''");
			case "/i": return $this->unstack("i");
			case "em": return $this->stack("em", "''", "''");
			case "/em": return $this->unstack("em");


			//center
			case "center": return $this->stack("center", "::", "::");
			case "/center": return $this->unstack("center");


			//code
			case "code": return $this->stack("code", "-+", "+-");
			case "/code": return $this->unstack("code");


			//horizontal row
			case "hr": return "---";


			//strong/bold
			case "b": return $this->stack("b", "__", "__");
			case "/b": return $this->unstack("b");
			case "strong": return $this->stack("strong", "__", "__");
			case "/strong": return $this->unstack("strong");


			//underline
			case "u": return $this->stack("u", "===", "===");
			case "/u": return $this->unstack("u");


			//paragraph
			case "p": return $this->stack("p", "\n");
			case "/p": return $this->unstack("p");
			case "br": return "\n";


			//multi
			case "div":
				//r2l block
				if ($this->paramEquals($params, 'dir', 'ltr')) {
					return $this->stack("div", "{r2l}", "\n");
				}

				//l2r block
				if ($this->paramEquals($params, 'dir', 'rtl')) {
					return $this->stack("div", "{l2r}", "\n");
				}

				//box
				if ($this->hasClass($params, "simplebox")) {
					return $this->stack("div", "^", "^");
				}

				//center
				if ($this->styleEquals($params, 'text-align', 'center')) {
					return $this->stack("div", "::", "::");
				}

				return $this->stack("div");

			case "/div": return $this->unstack("div");


			//multi
			case "span":
				//color
				if ($this->hasStyle($params, 'color')) {
					return $this->stack("span", "~~" . $params['style']['color'] . ':', "~~");
				}
			case "/span": return $this->unstack("span");


			//lists
			case "ul": return $this->stack("ul");
			case "/ul": return $this->unstack("ul");

			case "ol": return $this->stack("ol");
			case "/ol": return $this->unstack("ol");
			//lists *#+-;
			case "li":
				if ($this->lastListType == "ul") {
					return $this->stack("li", "*", "\n"); //TODO: handle - & +
				} elseif ($this->lastListType == "ol") {
					return $this->stack("li", "#", "\n");
				}
				return $this->stack("li");
			case "/li"; return $this->unstack("li");


			//definition list
			case "dl": return $this->stack("dl");
			case "/dl": return $this->unstack("dl");

			case "dt": return $this->stack("dt", ";");
			case "/dt": return $this->unstack("dt");
			case "dd": return $this->stack("dd", ":", "\n");
			case "/dd": return $this->unstack("dd");


			//wiki link
			case "a": //TODO: need a way to find alias or wiki externals
				if ($this->paramContains($params, "id", "flipper")) {
					$this->stackAndIgnoreContent("a");
				} else if ($this->hasClass($params, "externallink")) {
					return $this->stack("a", "[", "]");
				} elseif ($this->hasClass($params, "wiki_page")) {
					return $this->stack("a", "((", "))");
				}
				return $this->stack("a");
			case "/a": return $this->unstack("a");

			//smiles
			case "img":
				if ($this->paramContains($params, "img", "img/smiles/icon_")) {
					//TODO: need to break the smiles up and return their syntax
				}


			//table
			case "table":
				$this->tableRow = 0;
				return $this->stack("table", "||", "||");
			case "/table": return $this->unstack("table");

			case "tr":
				$this->tableData = 0;
				$trBeginning = ($this->tableRow > 0 ? "\n" : "");
				$this->tableRow++;
				return $this->stack("tr", $trBeginning);
			case "/tr": return $this->unstack("tr");

			case "td":
				$tdBeginning = ($this->tableData > 0 ? "|" : "");
				$this->tableData++;
				return $this->stack("td", $tdBeginning);
			case "/td": return $this->unstack("td");
		}

		return "";
	}

	public function content($content)
	{
		if ($this->isValidContent($content)) {
			return $content;
		}

		return "";
	}

	public function lineEnd($line)
	{
		return $line;
	}

	public function capitolWord($content)
	{
		if (!$this->inStack("a")) {
			return "))" . $content . "((";
		}

		return $content;
	}

	private function parseElementParameters($params)
	{
		$parsedParams = array();
		if (!empty($params)) {
			$dom = new DOMDocument();
			$dom->loadHtml("<object " . $params . " />");
			foreach($dom->getElementsByTagName("object") as $node) {
				foreach($node->attributes as $attribute) {
					$parsedParams[trim(strtolower($attribute->name))] = trim($attribute->value);
				}
			}
		}

		if (isset($parsedParams['style'])) {
			$styles = explode(';', $parsedParams['style']);
			$parsedParams['style'] = array();
			foreach($styles as &$style) {
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

	public function stackAndIgnoreContent($name, $beginning = "", $ending = "", &$value = "")
	{
		$this->tagStackIgnore[] = $name;
		return $this->stack($name, $beginning, $ending, $value);
	}

	public function stack($name, $beginning = "", $ending = "", $value = "")
	{
		switch ($name) {
			case "ul":
			case "dl":
			case "ol":
				$this->lastListType = $name;
		}

		if (!isset($this->tagIndex[$name])) {
			$this->tagIndex[$name] = 0;
		}

		$this->tagIndex[$name]++;
		$this->nonBreakingTagDepth++;
		$this->tagStack[] = $name;
		$value .= $beginning;


		if (!isset($this->tagStackEndings[$name])) {
			$this->tagStackEndings[$name] = array();
		}
		$this->tagStackEndings[$name][] = $ending;

		return $value;
	}

	public function unstack($name, &$value = "")
	{
		$this->nonBreakingTagDepth--;

		if (isset($this->tagStackEndings[$name])) {
			$ending = array_pop($this->tagStackEndings[$name]);
			$value .= $ending;
		}

		array_pop($this->tagStack);

		return $value;
	}

	public function tagIndex($name)
	{
		if (!isset($this->tagIndex[$name])) {
			return 0;
		}

		return $this->tagIndex[$name];
	}

	public function inStack($name)
	{
		return in_array($name, $this->tagStack);
	}

	public function inStackCountName($name)
	{
		$count = 0;
		foreach($this->tagStack as &$tag) {
			if ($tag == $name) {
				$count++;
			}
		}
		return $count;
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

	public function hasStyle(&$params, $style)
	{
		return isset($params['style'][$style]);
	}

	public function paramContains(&$params, $param, $contains)
	{
		return (isset($params[$param]) && strstr($params[$param], $contains) !== false);
	}

	public function isValidContent(&$content)
	{
		return (count($this->tagStackIgnore) < 1);
	}
}