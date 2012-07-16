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

	var $output = 'list';
	var $setup = false;

	public function setup($input = "")
	{
		if ($this->setup == true) return;
		$this->setup = true;

		if (
			strpos($input, "\n*") < 0 &&
			strpos($input, "\n#") < 0 &&
			strpos($input, "\n+") < 0 &&
			strpos($input, "\n-") < 0
		) {
			return;
		}

		$lines = explode("\n", $input);
		$lastLine = 0;

		foreach($lines as $i => &$line) {
			if (
				isset($line[0]) &&
				(
					$line[0] == "*" ||
					$line[0] == "#" ||
					$line[0] == "+"
				)
			) {
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

	public function setOutput($output = 'list')
	{
		$this->output = $output;
	}

	public function toHtml()
	{
		if (empty($this->stacks)) return '';

		if ($this->output == 'list') {
			return $this->toHtmlList();
		}

		if ($this->output == 'outline') {
			return $this->toHtmlOutline();
		}
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
							$listParentTagType = 'ol';
							break;
						case '*':
							break;
					}
				}

				if ($wrapInLi == true) {
					$result .= '<li class="tikiListItem">' . $list['content'];
				} else {
					$result .= '<div class="tikiUnlistItem">' . $list['content'] . '</div>';
				}

				if(empty($list['children']) == false) {
					$result .= $this->toHtmlListChildren($list['children']) . $html;
				}

				if ($wrapInLi == true) {
					$result .= '</li>';
				}

			} elseif (empty($list['children']) == false) {
				$result .= $this->toHtmlListChildren($list['children']);
			}
		}

		return $html . '<' . $listParentTagType . ' class="tikiList" id="' . $id . '" style="' . $style . '">' . $result . '</' . $listParentTagType . '>';
	}

	public function toHtmlOutline()
	{
		$lists = array();
		foreach($this->stacks as $key => &$stack) {
			$lists[$key] = $this->toHtmlOutlineChildren($stack);
		}

		return $lists;
	}

	private function toHtmlOutlineChildren(&$stack, $prefix = '', $index = 1, $tier = 0) {
		$result = '';
		$id = 'id' . microtime() * 1000000;

		$i = 0;
		foreach($stack as &$list){
			$hasLabel = true;
			if (empty($style)) {
				switch($list['type']) {
					case '+':
						$hasLabel = false;
						break;
				}
			}

			$listIndex = ($index + $i);
			$label = (!empty($prefix) ? $prefix . '.' . $listIndex : ($index + $i));
			$class = 'tikiListTableLabel';

			if ($hasLabel == false) {
				$label = '';
				$class = 'tikiListTableBlank';
			} else {
				$i++;
			}

			if(empty($list['content']) == false){

				$result .= '<tr><td class="' . $class . '">' . $label . '</td><td class="tikiListTableItem">' . $list['content'] . '</td></tr>';

				if(empty($list['children']) == false) {
					$result .= '<tr><td class="tikiUnlistTableItem" colspan="2">' . $this->toHtmlOutlineChildren($list['children'], $label, 1, $tier + 1) . '</td></tr>';
				}

			} elseif (empty($list['children']) == false) {
				$result .= '<tr><td>' . $this->toHtmlOutlineChildren($list['children'], $prefix, 1, $tier + 1) . '</td></tr>';
			}
		}

		return '<table class="tikiListTable" id="' . $id . '" data-tier=' . $tier . '><tbody>' . $result . '</tbody></table>';
	}
}