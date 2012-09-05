<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_Wiki_List
{
	static $stacks = array();
	var $index = 0;
	var $lineNumberLast = 0;
	var $id;
	var $key;

	private function id()
	{
		return 'id' . microtime() * 1000000;
	}

	public function stack($lineNumber, $level, $content, $type = '*')
	{
		$returnKey = false;

		/*the line number is +2 rather than just 1 because we insert a \n at the end of the line after we detect the list item using unset, this is so we can detect the next line as well*/
		if ($lineNumber != ($this->lineNumberLast + 2) || $this->lineNumberLast == 0) {
			$this->index++;
			$this->id = $this->id();
			$this->key = '§' . md5('list(id:' . $this->id . ',index:' . $this->index . ')') . '§';
			$returnKey = true;
			$this->stacks[$this->key] = array();
		}

		if ($level == 1) {
			$this->stacks[$this->key][] = array('content' => $content, 'type' => $type, 'children' => array());
		} else {
			$this->addToStack($this->stacks[$this->key], 1, $level, $content, $type);
		}

		$this->lineNumberLast = $lineNumber;

		if ($returnKey == true) {
			return $this->key;
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

	public function toHtml()
	{
		if (empty($this->stacks)) return;

		$lists = array();

		foreach($this->stacks as $key => &$stack) {
			$lists[$key] = $this->toHtmlChildren($stack);
		}

		return $lists;
	}

	private function toHtmlChildren(&$stack) {
		$result = '';
		$style = '';
		$html = '';
		$listParentTagType = 'ul';
		$id = $this->id();

		foreach($stack as &$list){
			$wrapInLi = true;
			if(empty($list['content']) == false){
				if (empty($style)) {
					switch($list['type']) {
						case '-':
							$style = 'display: none;';
							$html = '<a id="flipper' . $id . '" href="javascript:flipWithSign(\'' . $id . '\');" class="link">[+]</a>';
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
					$result .= $this->toHtmlChildren($list['children']) . $html;
				}

				if ($wrapInLi == true) {
					$result .= '</li>';
				}

			} elseif (empty($list['children']) == false) {
				$result .= $this->toHtmlChildren($list['children']);
			}
		}

		unset($stack);

		return $html . '<' . $listParentTagType . ' class="tikiList" id="' . $id . '" style="' . $style . '">' . $result . '</' . $listParentTagType . '>';
	}
}