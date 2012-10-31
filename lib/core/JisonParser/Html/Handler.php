<?php
class JisonParser_Html_Handler extends JisonParser_Html
{
	/* html tag tracking */
	public $nonBreakingTagDepth = 0;

	/* table tracking */
	public $tableRow = 0;
	public $tableData = 0;

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

			//multi
			case "div":
				//r2l
				if (isset($params['dir']) && strtolower($params['dir']) == "ltr") {
					return $this->stack("div", "{r2l}", "\n");
				}

				if (isset($params['dir']) && strtolower($params['dir']) == "rtl") {
					return $this->stack("div", "{l2r}", "\n");
				}

				return $this->stack("div");
				break;

			case "/div":
				return $this->unstack("div");
				break;

			case "table":
				$this->tableRow = 0;
				return $this->stack("table", "||", "||");
			case "tr":
				$this->tableData = 0;
				$trBeginning = ($this->tableRow > 0 ? "\n" : "");
				$this->tableRow++;
				return $this->stack("tr", $trBeginning);
			case "td":
				$tdBeginning = ($this->tableData > 0 ? "|" : "");
				$this->tableData++;
				return $this->stack("td", "||", "||");
			case "/td": return $this->unstack("td");
			case "/tr": return $this->unstack("tr");
			case "/table": return $this->unstack("table");
		}

		return "";
	}

	public function content($content)
	{
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
					$parsedParams[strtolower($attribute->name)] = $attribute->value;
				}
			}
		}

		if (isset($parsedParams['style'])) {
			$styles = explode(';', $parsedParams['style']);
			$parsedParams['style'] = array();
			foreach($styles as &$style) {
				$parts = explode(':', $style);
				if (isset($parts[0]) && isset($parts[1])) {
					$parsedParams['style'][$parts[0]] = $parts[1];
				}
			}
		}

		return $parsedParams;
	}

	public $htmlTagStack = array();
	public $htmlTagStackEndings = array();

	public function stack($name, $beginning = "", $ending = "", &$value = "")
	{
		$this->nonBreakingTagDepth++;
		$this->htmlTagStack[] = $name;
		$value .= $beginning;
		$this->htmlTagStackEndings[] = $ending;

		return $value;
	}

	public function unstack($name, &$value = "")
	{
		if ($this->htmlTagStack[count($this->htmlTagStackEndings) - 1] == $name) {
			$this->nonBreakingTagDepth--;
			$ending = array_pop($this->htmlTagStackEndings);
			$value .= $ending;
		}

		return $value;
	}

	function inStack($name)
	{
		return in_array($name, $this->htmlTagStack);
	}
}