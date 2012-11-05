<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_Wiki_List
{
	public $stacks = array();
	public $index = 0;
	public $lineNumberLast;
	public $levelLast = 0;
	public $id;
	public $key;
	public $parser;

	function __construct(&$parser)
	{
		$this->parser = &$parser;
	}

	private function id()
	{
		return 'id' . microtime() * 1000000;
	}

	public function reset()
	{
		$this->stacks = array();
		$this->index = 0;
		$this->lineNumberLast = null;
		$this->levelLast = 0;
	}

	public function getEntity($lineNumber, $level)
	{
		if (
			$lineNumber != ($this->lineNumberLast + 1) ||
			!isset($this->lineNumberLast)
		) {
			$this->index++;
			$this->id = $this->id();
			$this->key = '§' . md5('list(id:' . $this->id . ',index:' . $this->index . ')') . '§';
			$this->stacks[$this->key] = array();

			$entity = $this->key;
		} else {
			$entity = '';
		}

		$this->lineNumberLast = $lineNumber;
		$this->levelLast = $level;

		return $entity;
	}

	public function stack($lineNumber, $level, $content, $type = '*')
	{
		$entity = $this->getEntity($lineNumber, $level);

		if ($level == 1) {
			$this->stacks[$this->key][] = array('content' => $content, 'type' => $type, 'children' => array());
		} else {
			$this->addToStack($this->stacks[$this->key], 1, $level, $content, $type);
		}

		return $entity;
	}

	private function addToStack(&$stack, $currentLevel, &$neededLevel, &$content, &$type)
	{
		if ($currentLevel < $neededLevel) {
			if (!isset($stack)) {
				$stack = array();
				$key = 0;
			} else {
				end($stack);
				$key = key($stack);
			}

			$key = max(0, $key);

			$this->addToStack($stack[$key]['children'], $currentLevel + 1, $neededLevel, $content, $type);
		} else {
			$stack[] = array('content' => $content, 'type' => $type);
		}
	}

	public function toHtml()
	{
		if (empty($this->stacks)) return;

		$lists = array();

		foreach ($this->stacks as $key => &$stack) {
			$lists[$key] = $this->toHtmlChildren($stack);
		}

		return $lists;
	}

	private function toHtmlChildren(&$stack)
	{
		global $headerlib;
		$length = count($stack);
		if ($length < 1) return ''; //if there isn't anything in this list, don't process it
		$lastParentTagType = '';
		$lastType = '';
		$id = '';
		$html = '';

		for ($i = 0; $i < $length; $i++) {
			if (isset($stack[$i]) && empty($stack[$i]['content']) == false) {

				$html .= $this->writeParentStartTag($stack[$i]['type'], $lastType, $lastParentTagType, $id);

				switch($stack[$i]['type']) {
					case '-':
						$headerlib->add_css("#" . $id . "{display: none;}");
					case '+':
					case '*':
					case '#':
						$html .= $this->parser->createWikiTag(
							"list",
							"li",
								$stack[$i]['content'] .
								$this->toHtmlChildren($stack[$i]['children']) .
								$this->advanceUntilNotType($i, $stack),
							array("class" => "tikiListItem")
						) . "\n";
						break;
					case ';':
						$parts = explode(':', $stack[$i]['content']);
						$html .= $this->parser->createWikiTag("list", "dt", $parts[0]);
						if (isset($parts[1])) {
							$html .= $this->parser->createWikiTag("list", "dd", $parts[1]);
						}
						$html .= "\n";
						break;
				}
			}
		}

		unset($stack);

		$html .= $this->writeParentEndTag($lastParentTagType);

		return $html;
	}

	private function writeParentStartTag($listType, &$lastListType, &$parentTagType, &$id)
	{
		$result = '';
		if ($listType != $lastListType && $this->lineCompatibility($listType, $lastListType) != true) {
			$preTag = '';
			if (empty($parentTagType) == false) {
				$result .= $this->writeParentEndTag($parentTagType);
			}

			$parentTagType = $this->parentTagType($listType);

			$id = $this->id();

			if ($listType == "-") {
				$preTag .= $this->parser->createWikiHelper("list", "br", "", array(), "inline");
				$preTag .= $this->parser->createWikiHelper("list", "a", "[+]", array(
					"id" => "flipper" . $id,
					"href" => "javascript:flipWithSign(\"" . $id . "\");",
					"class" => "link"
				));
			}

			$lastListType = $listType;

			$result .= $preTag . $this->parser->createWikiTag("list", $parentTagType, "", array(
				"class" => "tikiList",
				"id" => $id
			), "open");
		}

		return $result;
	}

	private function lineCompatibility($thisListType = '', $lastListType = '')
	{
		if (
			($lastListType == "*" && $thisListType == "+") ||
			($lastListType == "+" && $thisListType == "*")
		) {
			return true;
		}

		if ($lastListType == $thisListType) {
			return true;
		}

		return false;
	}

	private function writeParentEndTag($parentTagType)
	{
		return $this->parser->createWikiTag("list", $parentTagType, "", array(), "close");
	}

	private function parentTagType($listType = '')
	{
		switch ($listType) {
			case '-':
			case '+':
			case '*':
				return 'ul';
				break;
			case '#':
				return 'ol';
				break;
			case ';':
				return 'dl';
				break;
		}
	}

	private function advanceUntilNotType(&$i, &$stack, $type = "+")
	{
		$result = '';
		$i++;
		for ($length = count($stack); $i <= $length; $i++) {
			if (!isset($stack[$i]['type'])) break;
			if ($stack[$i]['type'] == $type) {
				$result .= $this->parser->createWikiHelper("list", "br", "", array(), "inline") . $stack[$i]['content'] . "\n";
			} else {
				$i--;
				break;
			}
		}

		return $result;
	}
}
