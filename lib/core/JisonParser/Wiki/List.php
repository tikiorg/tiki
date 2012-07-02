<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_Wiki_List
{
	var $stacks = array();
	var $index = 0;
	var $lineTracking = array();

	var $isSetup = false;
	var $listIds = array();

	var $setup = false;

	public function setup($input = "")
	{
		if ($this->setup == true) return;
		$this->setup = true;

		$lines = explode("\n", $input);
		$lastLine = 0;

		foreach($lines as $i => &$line) {
			if ($line[0] == "*" || $line[0] == "#" || $line[0] == "+") {
				$this->lineTracking[] = $lastLine;
				continue;
			}

			$lastLine = $i;
		}
	}

	public function stack($level, $content, $type = '*')
	{
		$key = '§' . md5('list:' . $this->lineTracking[$this->index]) . '§';

		$returnKey = false;
		if (isset($this->stacks[$key]) == false) {
			$returnKey = true;
			$this->stacks[$key] = array();
		}

		if ($level == 1) {
			$this->stacks[$key][] = array('content' => $content, 'type' => $type, 'children' => array());
		} else {
			$this->addToStack($this->stacks[$key], 1, $level, $content, $type);
		}

		$this->index++;

		if ($returnKey == true) {
			return $key;
		}

		return '';
	}

	public function toHtmlList()
	{
		$lists = array();
		foreach($this->stacks as $key => &$stack) {
			$lists[$key] = $this->toHtmlListChildren($stack);
		}

		return $lists;
	}

	private function toHtmlListChildren(&$stack) {
		$result = '';
		$style = '';
		$html = '';
		$listParentTagType = 'ul';
		$id = 'id' . microtime() * 1000000;

		foreach($stack as &$list){
			$wrapInLi = true;
			if(empty($list['content']) == false){
				if (empty($style)) {
					switch($list['type']) {
						case '-':
							$style = 'display: none;';
							$html = "<a id='flipper$id' href='javascript:flipWithSign(\"". $id . "\");' class='link'>[+]</a>";
							break;
						case '+':
							$wrapInLi = false;
							break;
						case '#':
							$style = '';
							$listParentTagType = 'ol';
							break;
						case '*':
							$style = '';
							break;
					}
				}

				if ($wrapInLi == true) {
					$result .= '<li>' . $list['content'] . '</li>';
				} else {
					$result .= '<br />' . $list['content'];
				}

				if(empty($list['children']) == false) {
					$result .= $this->toHtmlListChildren($list['children'], $style, $html) . $html;
				}

				if ($wrapInLi == true) {
					$result .= '</li>';
				}

			} elseif (empty($list['children']) == false) {
				$result .= $this->toHtmlListChildren($list['children'], $style, $html);
			}
		}

		return $html . '<' . $listParentTagType . ' id="' . $id . '" style="' . $style . '">' . $result . '</' . $listParentTagType . '>';
	}

	private function addToStack(&$stack, $currentLevel, &$neededLevel, &$content, &$type)
	{
		if ($currentLevel < $neededLevel && $currentLevel < 7) {
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
}