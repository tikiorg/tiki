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

	private function toHtmlChildren(&$stack, $html = "")
	{
		$result = '';
		$style = '';
		$parentTagType = 'ul';
		$id = $this->id();
		$lastListType = '';

		for ($i = 0, $length = count($stack); $i < $length; $i++) {
			if (isset($stack[$i]) && empty($stack[$i]['content']) == false) {
				if (empty($style)) {
					switch($stack[$i]['type']) {
						case '-':
							$style = 'display: none;';
							$html = '<br />' . "\n" . '<a id="flipper' . $id . '" href="javascript:flipWithSign(\'' . $id . '\');" class="link">[+]</a>';
						case '+':
							if ($lastListType == '') {
								$lastListType = "*";
								$stack[$i]['type'] = "*";
							}
						case '*':
							$result .= '<li class="tikiListItem">' . $stack[$i]['content'];
							if (!empty($stack[$i]['children'])) {
								$result .= $this->toHtmlChildren($stack[$i]['children']);
							}
							$result .= $this->advanceUntilNotType($i, $stack);
							$result .= '</li>' . "\n";
							break;
						case '#':
							$parentTagType = 'ol';
							$result .= '<li class="tikiListItem">' . $stack[$i]['content'];
							if (!empty($stack[$i]['children'])) {
								$result .= $this->toHtmlChildren($stack[$i]['children']);
							}
							$result .= $this->advanceUntilNotType($i, $stack);
							$result .= '</li>' . "\n";
							break;
						case ';':
							$parentTagType = 'dl';
							$parts = explode(':', $stack[$i]['content']);
							$result .= '<dt>'  . $parts[0] . '</dt>';
							if (isset($parts[1])) {
								$result .= '<dd>'  . $parts[1] . '</dd>';
							}
							$result .= "\n";
							break;
					}
				}
			}

			if (isset($stack[$i]['type'])) {
				$lastListType = $stack[$i]['type'];
			}
		}

		unset($stack);

		return $html . '<' . $parentTagType . ' class="tikiList" id="' . $id . '" style="' . $style . '">' . $result . '</' . $parentTagType . '>';
	}

	private function advanceUntilNotType(&$i, &$stack, $type = "+", $wrapping = array("<br />", "\n"))
	{
		$result = '';
		$i++;
		for ($length = count($stack); $i <= $length; $i++) {
			if (!isset($stack[$i]['type'])) break;
			if ($stack[$i]['type'] == $type) {
				$result .= $wrapping[0] . $stack[$i]['content'] . $wrapping[1];
			} else {
				$i--;
				break;
			}
		}

		return $result;
	}
}
