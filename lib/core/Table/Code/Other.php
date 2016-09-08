<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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
 *Creates code for standard jQuery functions needed outside of the main Tablesorter functions
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Code_Manager
 */
class Table_Code_Other extends Table_Code_Manager
{

	public function setCode()
	{
		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_icon');
		$jq = array();
		//column selector
		if (parent::$s['colselect']['type'] === true) {
			$buttons[] = '<button id="' . parent::$s['colselect']['button']['id']
				. '" type="button" class="btn btn-default btn-sm" title="' . parent::$s['colselect']['button']['text']
				. '" style="margin-right:3px">' . smarty_function_icon(['name' => 'columns'], $smarty) . '</button>';
			$jq[] = '$(\'button#' . parent::$s['colselect']['button']['id'] . '\').popover({'
				. $this->nt2 . 'placement: \'right\','
				. $this->nt2 . 'html: true,'
				. $this->nt2 . 'content: \'<div id="' . parent::$s['colselect']['div']['id'] . '"></div>\''
				. $this->nt . '}).on(\'shown.bs.popover\', function () {'
				. $this->nt2 . '$.tablesorter.columnSelector.attachTo( $(\'' . parent::$tid
				. '\'), \'#' . parent::$s['colselect']['div']['id'] . '\');'
				. $this->nt . '});';
		}

			//reset sort button
		$sr = '';
		$x = array('reset' => '', 'savereset' => '');
		$s = parent::$s['sorts'];
		$s = isset($s['type']) && $s['type'] !== true && array_key_exists($s['type'], $x) ? $s : false;
		if ($s) {
			if ($s['type'] === 'savereset') {
				$sr = '.trigger(\'saveSortReset\')';
			}
			$jq[] = '$(\'button#' . $s['reset']['id'] . '\').click(function(){$(\'' . parent::$tid
				.'\').trigger(\'sortReset\')' . $sr . ';});';
			$buttons[] = '<button id="' . $s['reset']['id']
				. '" type="button" class="btn btn-default btn-sm tips" title=":' . $s['reset']['text']
				.  '" style="margin-right:3px">' . smarty_function_icon(['name' => 'sort'], $smarty) . '</button>';
		}

		//filters
		if (parent::$filters) {
			$f = parent::$s['filters'];
			//reset button
			if ($f['type'] === 'reset') {
				$buttons[] = '<button id="' . $f['reset']['id']
					. '" type="button" class="btn btn-default btn-sm tips" title=":' . $f['reset']['text'] . '">'
					. smarty_function_icon(['name' => 'filter'], $smarty) . '</button>';
			}
			if (isset($buttons) && count($buttons) > 0) {
				$htmlbefore[] = $this->iterate($buttons, '<div style="float:left">', '</div>', '', '', '');
			}
			//external dropdowns
			if (isset($f['external']) && is_array($f['external'])) {
				$options = array_column($f['external'], 'options');
				if (count($options) > 0) {
					foreach($f['external'] as $key => $info) {
						$xopt[] = ' value="" selected disabled>' . tr('Select a filter');
						foreach($info['options'] as $label => $val) {
							$xopt[] = ' value="' . $val . '">' . $label;
						}
						//create dropdown
						$divr[] = $this->iterate(
							$xopt,
							'<select id="' . $f['external'][$key]['id'] . '" class="form-control ts-external-select">',
							'</select>',
							'<option',
							'</option>',
							''
						);
						//trigger table update and filter when dropdown value is changed
						$jq[] = '$(\'#' . $f['external'][$key]['id'] . '\').bind(\'change\', function(e){'
							. $this->nt2 . '$(\'' . parent::$tid .'\').trigger(\'search\', [ [this.value] ]);'
							. $this->nt . '});';
						//filter-reset also clears any external dropdown filter (column filters cleared by tablesorter)
						if ($f['type'] === 'reset') {
							$reset[] = 'if ($(\'#' . $f['external'][$key]['id'] . '\').prop(\'selectedIndex\') != 0) {'
								. $this->nt3 . '$(\'#' . $f['external'][$key]['id'] . ' option\')[0].selected = true;'
								. $this->nt3 . '$(\'#' . $f['external'][$key]['id'] . '\').change();'
								. $this->nt2 . '}';
						}
					}
					unset($key, $info);
					if ($f['type'] === 'reset' && count($reset) > 0) {
						$jq[] = $this->iterate(
							$reset,
							'$(\'#' . $f['reset']['id'] . '\').click(function(){',
							$this->nt . '});',
							$this->nt2,
							'',
							''
						);
					}
					$htmlbefore[] = $this->iterate($divr, '<div style="float:right">', '</div>', '', '', '');
				}
			}
			// add custom dropdown parser
			$jq[] = $this->nt . '$.tablesorter.addParser({'
				. $this->nt2 . 'id: \'dropdown\','
				. $this->nt2 . 'is: function() {'
					. $this->nt3 . 'return false;'
				. $this->nt2 . '},'
				. $this->nt2 . 'format: function(str, table, cell) {'
					. $this->nt3 . 'var c = table.config,'
						. $this->nt4 . 'html = ( cell.innerHTML !== undefined ? cell.innerHTML : str );'
					. $this->nt3 . 'if (html) {'
						. $this->nt4 . '// remove inline editor'
						. $this->nt4 . 'try { html = ( $(html).hasClass(\'editable-inline\') || $(html).hasClass(\'editable-dialog\') ) ? $(html).html() : html }'
						. $this->nt4 . 'catch(e) {}'
						. $this->nt4 . '// remove nbsp'
						. $this->nt4 . 'html = html.replace(\'&nbsp;\', \'\')'
						. $this->nt4 . '// replace <br> and new lines with a comma'
						. $this->nt4 . 'html = html.replace(/\s*<br\s*\/?>\s*|[\r\n]/g, \',\')'
						. $this->nt4 . 'html = html.replace(/,{2,}/g, \',\')'
						. $this->nt4 . 'html = $.trim(c.ignoreCase ? html.toLocaleLowerCase() : html);'
						. $this->nt4 . 'html = c.sortLocaleCompare ? $.tablesorter.replaceAccents(html) : html;'
					. $this->nt3 . '}'
					. $this->nt3 . 'return html;'
				. $this->nt2 . '},'
				. $this->nt2 . 'type: \'text\''
			. $this->nt . '});';
		} else {
			if (isset($buttons) && count($buttons) > 0) {
				$htmlbefore[] = $this->iterate($buttons, '<div style="float:left">', '</div>', '', '', '');
			}
		}


		$p = parent::$s['pager'];
		//pager controls
		if (parent::$pager) {
			$pagerdiv = array(
				'<div class="btn-group">',
					'<div class="btn-group">',
					'	<label for="gotoPage" class="selectlabels">Page</label>',
					'	<select id="gotoPage" class="gotoPage form-control"></select>',
					'</div>',
				'</div>',
				'<div class="btn-group middle">',
				'	<span class="first tips" title=":' . tr('First page') . '">',
				'		' . smarty_function_icon(['name' => 'backward_step'], $smarty),
				'	</span>',
				'	<span class="prev tips" title=":' . tr('Previous page') . '">',
				'		' . smarty_function_icon(['name' => 'backward'], $smarty),
				'	</span>',
				'	<span class="pagedisplay">',
				'	</span>',
				'	<span class="next tips" title=":' . tr('Next page') . '">',
				'		' . smarty_function_icon(['name' => 'forward'], $smarty),
				'	</span>',
				'	<span class="last tips" title=":' . tr('Last page') . '">',
				'		' . smarty_function_icon(['name' => 'forward_step'], $smarty),
				'	</span>',
				'</div>',
			);
			foreach ($p['expand'] as $option) {
				$sel = $p['max'] === $option ? ' selected="selected"' : '';
				$opt[] = $sel . ' value="' . $option . '">' . $option;
			}
			unset($option);
			if (isset($opt)) {
				$pagerdiv[] = $this->iterate(
					$opt,
					'<div class="btn-group"><label for="pagesize" class="selectlabels">Rows</label><select id="pagesize" class="pagesize form-control">',
					'</select></div>',
					'<option',
					'</option>',
					''
				);
			}
			//put all pager controls in a div
			$pagerstring = $this->iterate(
				$pagerdiv,
				'<div id="' . $p['controls']['id'] . '" class="ts-pager ts-pager-top btn-toolbar">',
				'</div>',
				'',
				'',
				''
			);
			$htmlbefore[] = $pagerstring;
			$pagerstring = $this->iterate(
				$pagerdiv,
				'<div id="' . $p['controls']['id'] . '" class="ts-pager ts-pager-bottom btn-toolbar">',
				'</div>',
				'',
				'',
				''
			);
			$htmlafter[] = $pagerstring;
		}
		//add math total column if set
		if (!empty(parent::$s['math']['totals']['row'])) {
			foreach(parent::$s['math']['totals']['row'] as $total) {
				$class = parent::$s['ajax']['type'] !== false ? ' class="sorter-false filter-false"' : '';
				$jq[] = $this->nt . '$(\'' . parent::$tid . '\').find(\'thead tr\').append(\'<th' . $class . '>'
					. $total['label'] . '</th>\');'
					. $this->nt . '$(\'' . parent::$tid . '\').find(\'tbody tr\').append(\'<td data-tsmath="row-'
					. $total['formula'] . '"></td>\')'
					. $this->nt . '$(\'' . parent::$tid . '\').find(\'tfoot tr:not(.ts-foot-row)\').append(\'<th></th>\');';
			}
		}

		//add any reset/disable buttons just above the table
		if (isset($htmlbefore)) {
			$allhtmlbefore = $this->iterate($htmlbefore, '', '', '', '', '');
			$allhtmlafter = !empty($htmlafter) && is_array($htmlafter) ?
				$this->iterate($htmlafter, '', '', '', '', '') : '';
			$allhtmlafter = !empty($allhtmlafter) ? '.after(\'' . $allhtmlafter . '\'' . $this->nt . ');' : '';
			array_unshift($jq, '$(\'' . parent::$tid . '\').before(\'' . $allhtmlbefore . '\'' . $this->nt
				. ')' . $allhtmlafter);
		}
		if (count($jq) > 0) {
			$code = $this->iterate($jq, '', '', $this->nt, '', '');
			parent::$code[self::$level1] = $code;
		}
	}
}
