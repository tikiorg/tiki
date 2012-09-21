<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_expandingoutline_list extends JisonParser_Wiki_List
{
	function __construct(JisonParser_Wiki_List &$parserList)
	{
		$this->setup = $parserList->setup;
		$this->stacks = $parserList->stacks;
		$this->index = $parserList->index;
		$this->lineTracking = $parserList->lineTracking;
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

	private function toHtmlChildren(&$stack, $prefix = '', $index = 1, $tier = 0)
	{
		$result = '';
		$id = 'id' . microtime() * 1000000;

		$i = 0;
		foreach ($stack as &$list) {
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

			if (empty($list['content']) == false) {

				$result .= '<tr><td class="' . $class . '">' . $label . '</td><td class="tikiListTableItem">' . $list['content'] . '</td></tr>';

				if (empty($list['children']) == false) {
					$result .= '<tr><td class="tikiUnlistTableItem" colspan="2">' . $this->toHtmlChildren($list['children'], $label, 1, $tier + 1) . '</td></tr>';
				}

			} elseif (empty($list['children']) == false) {
				$result .= '<tr><td>' . $this->toHtmlChildren($list['children'], $prefix, 1, $tier + 1) . '</td></tr>';
			}
		}

		unset($stack);

		return '<table class="tikiListTable" id="' . $id . '" data-tier=' . $tier . '><tbody>' . $result . '</tbody></table>';
	}
}
