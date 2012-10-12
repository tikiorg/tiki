<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_expandingoutline_list extends JisonParser_Wiki_List
{
	public $labelTracker = array();
	public $typeTracking = array();

	function __construct(JisonParser_Wiki_List &$parserList)
	{
		$this->stacks = $parserList->stacks;
		$this->index = $parserList->index;
	}

	public function toHtml()
	{
		if (empty($this->stacks)) return;

		$lists = array();

		foreach ($this->stacks as $key => &$stack) {
			$id = 'id' . microtime() * 1000000;

			$lists[$key] = '<table class="tikiListTable" id="' . $id . '">';
			$this->labelTracker = array();
			$lists[$key] .= $this->toHtmlChildren($stack);

			$lists[$key] .= '</table>';
		}

		return $lists;
	}

	private function toHtmlChildren(&$stack, $tier = 0)
	{
		$result = '';

		if (!isset($stack)) {
			return $result;
		}

		$i = 0;
		foreach ($stack as &$list) {

			switch($list['type']) {
				case '*':
					$class = 'tikiListTableLabel';
					$i++;
					break;
				case '+':
					$class = 'tikiListTableBlank';
					break;
			}

			$this->labelTracker[] = $i;

			switch($list['type']) {
				case '*':
					$label = implode('.', $this->labelTracker);
					break;
				case '+':
					$label = '';
					break;
			}

			$trail = $this->labelTracker;
			$trail = implode('_', $trail);

			if (empty($list['content']) == false) {

				$result .=
					'<tr>' .
						'<td>' .
							'<table>' .
								'<tr>' .
									'<td id="" class="' . $class . ' tier' . $tier . '" data-trail="' . $trail . '" style="width:' . ((count($this->labelTracker) * 30) + 30) . 'px; text-align: right;">' .
										(empty($list['children']) == false ? '<img class="listImg" src="img/toggle-expand-dark.png" data-altImg="img/toggle-collapse-dark.png" />' : '').
										$label .
									'</td>' .
									'<td class="tikiListTableItem">' . $list['content'] .'</td>' .
								'</tr>';

				if (empty($list['children']) == false) {
					$result .= '<tr class="parentTrail' . $trail . ' tikiListTableChild"><td colspan="2"><table>';
				}
			}

			$result .= $this->toHtmlChildren($list['children'], $tier + 1);

			if (empty($list['content']) == false) {

				if (empty($list['children']) == false) {
					$result .= '</table></td></tr>';
				}

				$result .=
							'</table>' .
						'</td>' .
					'</tr>';
			}

			array_pop($this->labelTracker);
		}

		return $result;
	}
}
