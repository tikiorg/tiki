<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 * Class Table_Code_Other
 *
 *Creates code for the functions needed outside of the main Tablesorter sections
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Code_Manager
 */
class Table_Code_Other extends Table_Code_Manager
{

	public function setCode()
	{
		$jq = '';
		$code = '';
		$sr = '';
		//reset sort button
		$x = array('reset' => '', 'savereset' => '');
		$s = $this->s['sort'];
		$s = isset($s['type']) && array_key_exists($s['type'], $x) ? $s : false;
		if ($s) {
			if ($s['type'] == 'savereset') {
				$sr = '.trigger(\'saveSortReset\')';
			}
			$jq[] = '$(\'button#' . $s['id'] . '\').click(function(){$(\'table#' . $this->id
				.'\').trigger(\'sortReset\')' . $sr . ';});';
			$html[] = '<button id="' . $s['id'] . '" type="button">' . $s['text'] . '</button>';
		}

		//filters
		if ($this->filters) {
			$f = $this->s['filters'];
			//reset button
			if ($this->s['filters']['type'] == 'reset') {
				$html[] = '<button id="' . $f['id'] . '" type="button">' . $f['text'] . '</button>';
			}
			//placeholders
			$cols = isset($f['columns']) ? $f['columns'] : false;
			if ($cols !== false) {
				foreach($cols as $col => $colinfo) {
					if (isset($colinfo['placeholder'])) {
						$jq[] = '$(\'table#' . $this->id . ' th:eq(' . $col . ')\').data(\'placeholder\', \'' .
							$colinfo['placeholder'] . '\');';
					}
				}
			}
		}

		$p = $this->s['pager'];
		//pager controls
		if (isset($p['type']) && $p['type'] !== false) {
			$div = array(
				'Page: <select class="gotoPage"></select>',
				'<span class="first arrow">mg</span>',
				'<span class="prev arrow">img</span>',
				'<span class="pagedisplay"></span>',
				'<span class="next arrow">img</span>',
				'<span class="last arrow">mg</span>',
			);
			foreach ($p['expand'] as $option) {
				$sel = $p['max'] == $option ? ' selected="selected"' : '';
				$opt[] = $sel . ' value="' . $option . '">' . $option;
			}
			if (isset($opt)) {
				$div[] = $this->iterate($opt, '<select class="pagesize">', '</select>', '<option',
					'</option>', '');
			}
			//put all pager controls in a div
			$html[] = $this->iterate($div, '<div id="' . $this->s['pagercontrols']['id'] . '" class="tablesorter-pager">',
				'</div>', '', '', '') ;
		}

		//TODO - doesn't seem to work with ajax. Don't set tables to 'disable' until this is fixed
		//disable pager button
/*		if (isset($p['type']) && $p['type'] == 'disable' && $p['type'] !== true) {
			$b = array(
				'var mode = /Disable/.test( $(this).text() );',
				'$(\'table#' . $this->id . '\').trigger( (mode ? \'disable\' : \'enable\') + \'.pager\');',
				'$(this).text( (mode ? \'' . $p['text']['enable'] . '\' : \'' . $p['text']['disable'] . '\'));',
			);
			$jq[] = $this->iterate($b, '$(\'button#' . $p['id'] . '\').click(function(){',
				$this->nt . '});', $this->nt2, '', '');

			$b2 = array(
				'$(\'button#' . $p['id'] . '\').text(\'' . $p['text']['disable'] . '\');'
			);
			$jq[] = $this->iterate($b2, '$(\'table#' . $this->id . '\').bind(\'paperChange\', function(){',
				$this->nt . '});', $this->nt2, '', '');
			$html[] = '<button id="' . $p['id'] . '" type="button">Disable Pager</button>';
		}*/

		//add any reset/disable buttons just above the table
		if (isset($html)) {
			$allhtml = $this->iterate($html, '', '', '', '', '');
			array_unshift($jq, '$(\'table#' . $this->id . '\').before(\'' . $allhtml . '\'' . $this->nt . ');');
		}

		//bind to ajax event to show processing
		$bind = '';
		if (isset($this->s['pager']['ajax']) && $this->s['pager']['ajax'] !== false) {
			$bind = array(
				'if (e.type === \'ajaxSend\') {',
				'	$(\'table#' . $this->id . ' tbody\').css(\'opacity\', 0.5);',
				'}',
				'if (e.type === \'ajaxComplete\') {',
				'	$(\'table#' . $this->id . ' tbody\').css(\'opacity\', 1);',
				'}'
			);
			$jq[] = $this->iterate($bind, '$(document).bind(\'ajaxSend ajaxComplete\', function(e){',
				$this->nt . '});', $this->nt2, '', '');
		}

		$code = $this->iterate($jq, '', '', $this->nt, '', '');
		parent::$code[self::$level1] = $code;
	}
}