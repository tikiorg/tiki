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


class tablesorterHelper
{
	public $params = array();
	public $totalcols = '';
	public $html = '';
	private $options = array();
	private $widgets = array();
	private $widgetOptions = array();
	private $filter_functions = array();
	private $filter_formatter = array();
	private $defaultph = array(
		'text' => 'Type here to filter...',
		'' => 'Type here to filter...',
		'dropdown' => 'Select a value'
	);

	public $code = array(
		'buttons' => array(),
		'columns' => array(),
		'jq' => array(),
		'div' => '',
	);

	function createParams()
	{
		$this->params = array(
			'sortable' => array(
				'required' => false,
				'name' => tra('Column Sort'),
				'description' => tr(
					'Enter %0y%1 to allow sorting and %0n%1 to disallow (n is the default). Enter type:%0save%1
					to allow sorts to be saved between page refreshes. Enter type:%0reset%1;text:***** to allow sorting and
					show an unsort button with custom text. Enter %0type:savereset%1;text:buttontext to allow the same for saved sorts.',
					'<b>', '</b>'
				),
				'default' => 'n',
				'filter' => 'striptags',
				'subparams' => array(
					'y'	=> '',
					'n'	=> '',
					'type' => array(
						'save'	=> '',
						'reset'	=> array('text' => tra('Reset Sort')),
						'savereset'	=> array('text' => tra('Reset Sort')),
					),
				),
			),
			'sortList' => array(
				'required' => false,
				'name' => tra('Pre-sorted Columns'),
				'description' => tra(
					'Bracketed numbers for column number (first column = 0) and sort direction
					(0 = ascending, 1 = descending), for example: [0,0],[1,0]'
				),
				'default' => '',
				'filter' => 'striptags',
			),
			'tsfilters' => array(
				'required' => false,
				'name' => tra('Column Filters'),
				'description' => tra(
					'Enter y to have a text filter on all columns. Or set custom column filters
					separated by | for each column for the following filter choices and parameters:'
				)
					. '<br> <b>Text - </b>type:text;placeholder:xxxx<br>
					<b>Dropdown - </b>type:dropdown;placeholder:****;option:****;option:****;option:**** <br>' .
					tra('(options generated automatically if not set)') . '<br>
					<b>' . tra('Date range - ') . '</b>type:date;format:yyyy-mm-dd;from:2013-06-30;to:2013-12-31<br>' .
					tra('(from and to values set defaults for these fields when user clicks on the input field)') . '<br>
					<b>' . tra('Numeric range - ') . '</b>type:range;from:0;to:50<br>
					<b>' . tra('No filter - ') . '</b>type:nofilter<br>' .
					tra(
						'For example: tsfilters="type:dropdown;placeholder:Type to filter..." would result in a dropdown
						filter on the first column with all unique values in that column in the dropdown list.'
					),
				'default' => '',
				'filter' => 'striptags',
				'subparams' => array(
					'y'	=> '',
					'type' => array(
						'text'	=> array(
							'placeholder' => tra('Type to filter...'),
						),
						'dropdown'	=> array(
							'placeholder' => tra('Select a value'),
						),
						'date'	=> array(
							'format' => 'yy-mm-dd',
							'from' => '',
							'to' => '',
						),
						'range'	=> array(
							'from' => 0,
							'to' => 100,
							'style' => '',
						),
						'nofilter'	=> '',
					),
				),
			),
			'tsfilteroptions' => array(
				'required' => false,
				'name' => tra('Filter Options'),
				'description' => tr(
					'The following options are available: %0reset%1 (adds button to take off filters), and %0hide%1
					(Filters are revealed upon mouseover. Hide doesn\'t work when date and range filters are used.). To use both, set
					tsfilteroptions="type:reset;text:button text;style:hide"', '<b>', '</b>'
				),
				'default' => '',
				'filter' => 'striptags',
				'subparams' => array(
					'type' => array(
						'reset'	=> '',
						'hide'	=> '',
					),
					'text' => tra('Reset Filter'),
				)
			),
			'tspaginate' => array(
				'required' => false,
				'name' => tra('Paginate'),
				'description' => tra(
					'Enter y to set default values: 20 rows max and expand dropdown with values from
				 	10-200. Set custom values as in the following example: '
				) .
					'<b>max</b>:40;<b>expand</b>:60;expand:100;expand:140',
				'default' => '',
				'filter' => 'striptags',
				'subparams' => array(
					'y'		=> '',
					'max'	=> 20,
					'expand'	=> array(10, 20, 30, 50, 100, 150, 200),
				),
			),
		);
	}

	function createCode ($id = null, $sortable = 'y', $sortList = null, $tsfilters = null,
						 $tsfilteroptions = null, $tspaginate = null)
	{
		global $prefs;
		if (($prefs['disableJavascript'] == 'n' && $prefs['feature_jquery_tablesorter'] == 'y')) {
			if (empty($id)) {
				static $static = 0;
				++$static;
				$id = 'tstable_' . $static;
			}
			$this->createParams();
			$this->widgets[] = 'zebra';

			//set jquery for sort reset buttons
			$sp = $this->parseParam($sortable);
			$spsub = $this->params['sortable']['subparams'];
			$sp = $sp[0];
			if ($sp != 'n') {
				if (array_key_exists($sp['type'], $spsub['type'])) {
					if ($sp['type'] == 'reset' || $sp['type'] == 'savereset') {
						$tempfunc = '$(\'button#sort-reset-' . $id . '\').click(function(){$(\'#' . $id
							.'\').trigger(\'sortReset\')';
						if ($sp['type'] == 'savereset') {
							$tempfunc .= '.trigger(\'saveSortReset\')';
						}
						$tempfunc .= ';});';
						$this->code['jq'][] = $tempfunc;
						$btext = isset($sp['text']) ? $sp['text'] : $spsub['type'][$sp['type']]['text'];
						$this->code['buttons'][0] = '<button id="sort-reset-' . $id . '">' . $btext . '</button>';
					}
					if ($sp['type'] == 'savereset' || $sp['type'] == 'save') {
						$this->widgets[] = 'saveSort';
						$this->widgetOptions[] = 'saveSort : true';
					}
				}
			}

			//set sortList jquery
			if (!empty($sortList)) {
				if (strpos($sortList, 'n') === false) {
					$liststring = $sortList;
				} else {
					//n means no sort, so set sort-false class for that column
					$crop = substr($sortList, 1);
					$crop = substr($crop, 0, -1);
					$slarray = explode('],[', $crop);
					foreach ($slarray as $l) {
						if (strpos($l, 'n') === false) {
							$newlist[] = '[' . $l . ']';
						} else {
							$lpieces = explode(',', $l);
							$this->code['columns'][$lpieces[0]]['classes'][] = 'sorter-false';
						}
					}
					$liststring = implode(',', $newlist);
				}
				$this->options[] = 'sortList : [' . $liststring . ']';
			}

			//set paginate code
			if (!empty($tspaginate) && $tspaginate != 'n') {
				$tsp = $this->parseParam($tspaginate);
				$tspsub = $this->params['tspaginate']['subparams'];
				$tsp = $tsp[0];
				$max = is_array($tsp) && isset($tsp['max']) ? $tsp['max'] : $tspsub['max'];
				$this->code['jq'][] = '$(function(){var pagerOptions = {size: ' . $max . ', removeRows: false,
											output: \'{startRow} to {endRow} ({totalRows})\',
											container: $(".pager"),
											};';
				$this->code['jq'][] = "\t" . '$(\'.toggle\').click(function(){
											var mode = /Disable/.test( $(this).text() );
											$(\'#' . $id . '\').trigger( (mode ? \'disable\' : \'enable\') + \'.pager\');
											$(this).text( (mode ? \'Enable\' : \'Disable\') + \'Pager\');
											});
										$(\'#' . $id . '\').bind(\'pagerChange\', function(){
										// pager automatically enables when table is sorted.
										$(\'.toggle\').text(\'Disable Pager\');
										});';
				$this->code['buttons'][3] = '<button class="toggle">Disable Pager</button>';
				$this->options[] = 'headerTemplate : \'{content} {icon}\'';

				//create div
				if (is_array($tsp) && isset($tsp['expand'])) {
					foreach ($tsp['expand'] as $key => $value) {
						$select[] = $value;
					}
					sort($select);
				} else {
					$select = $tspsub['expand'];
				}
				$div = '<div class="pager tablesorter-pager">
						Page: <select class="gotoPage"></select>
						<span class="first arrow">mg</span>
						<span class="prev arrow">img</span>
						<span class="pagedisplay"></span> <!-- this can be any element, including an input -->
						<span class="next arrow">img</span>
						<span class="last arrow">mg</span>
						<select class="pagesize">';
				foreach ($select as $option) {
					$div .= '<option';
					if ($max == $option) {
						$div .= ' selected="selected"';
					}
					$div .= ' value="' . $option . '">' . $option . '</option>';
				}
				$div .= '</select></div>';
				$this->code['div'] = $div;
			}


			//parse filters param
			if (!empty($tsfilters) && $tsfilters != 'n') {
				$tsf = $this->parseParam($tsfilters);
				$tsfsub = $this->params['tsfilters']['subparams'];
				//set static filter options
				$this->widgets[] = 'filter';
				$this->widgetOptions[] = 'filter_cssFilter : \'tablesorter-filter\'';
				$this->widgetOptions[] = 'filter_searchDelay : 300';
				if (is_array($tsf)) {
					foreach ($tsf as $key => $filter) {
						$def = '';
						switch($filter['type']) {
							case 'nofilter' :
								$this->code['columns'][$key]['classes'][] = 'filter-false';
								break;
							case 'dropdown' :
								//add any dropdown options set by user, which start at the [1] index
								if (isset($filter['option'])) {
									foreach ($filter['option'] as $num => $val) {
										$this->filter_functions[$key][] = '\'' . $val . '\' : function(e, n, f, i) { return /' .
											$val . '/.test(e); }';
									}
								} else {
									$this->filter_functions[$key][] = 'true';
								}
							case 'text';
							case '' :
								$def = array_merge($tsfsub['type'][$filter['type']], $filter);
								//set placeholder text for text and dropdown filters, which is in the [0] index of the array
								$this->code['columns'][$key]['placeholder'] = 'data-placeholder="' . htmlspecialchars($def['placeholder']) . '"';
								break;
							case 'range' :
								$def = array_merge($tsfsub['type'][$filter['type']], $filter);
								//min is at [0] array index; max is at [1] and popup is at [2]
								$valuetoheader = $def['style'] == 'popup' ? 'false' : 'true';
								$range = ' ' . $key . ' : function($cell, indx){return $.tablesorter.filterFormatter.uiRange( $cell, indx, {';
								$range .= 'values: [' . $def['from'] . ', ' . $def['to'] . '],';
								$range .= ' min: ' . $def['from'] . ',';
								$range .= ' max: ' . $def['to'] . ',';
								$range .= ' delayed: true,';
								$range .= ' valueToHeader: ' . $valuetoheader . ',';
								$range .= ' exactMatch: true});},';
								$this->filter_formatter[$key] = $range;
								break;
							case 'date' :
								$def = array_merge($tsfsub['type'][$filter['type']], $filter);
								//from date is at [0] array index; to date is at [1]
								$date = ' ' . $key . ' : function($cell, indx){return $.tablesorter.filterFormatter.uiDatepicker( $cell, indx, {';
								$date .= 'from : \'' . $def['from'] . '\',';
								$date .= ' to : \'' . $def['to'] . '\',';
								$date .= ' dateFormat: \'' . $def['format'] . '\',';
								$date .= ' changeMonth: true,';
								$date .= ' changeYear: true});},';
								$this->filter_formatter[$key] = $date;
								break;
						}
					}
				}

				//parse filteroptions param
				if (isset($tsfilteroptions)) {
					$tsfo = $this->parseParam($tsfilteroptions);
					$tsfo = $tsfo[0];
					$tsfosub = $this->params['tsfilteroptions']['subparams'];
					if (isset($tsfo['type']) && $tsfo['type'] == 'reset') {
						$text = !empty($tsfo['text']) ? $tsfo['text'] : $tsfosub['text'];
						$this->code['buttons'][2] = '<button id="filter-reset-' . $id . '">' . htmlspecialchars($text) . '</button>';
						$this->widgetOptions[] = 'filter_reset : \'#filter-reset-' . $id . '\'';
					}
					if (isset($tsfo['style']) && $tsfo['style'] == 'hide') {
						$this->widgetOptions[] = 'filter_hideFilters : true';
					}
				}
			}

			//create jquery
			$jq = "\t" . '$("#' . $id . '").tablesorter({' . "\n\t\t";
			//add widgets
			if (is_array($this->widgets) && count($this->widgets) > 0) {
				$jq .= 'widgets : [';
				foreach ($this->widgets as $widget) {
					$jq .= '"' . $widget . '",';
				}
				//take off the last comma
				$jq = substr($jq, 0, -1);
				$jq .= '],' . "\n\t\t";
			}
			//add options
			if (is_array($this->options) && count($this->options) > 0) {
				foreach ($this->options as $option) {
					$jq .= $option . ',' . "\n\t\t";
				}
			}
			//add widget options
			if (is_array($this->widgetOptions) && count($this->widgetOptions) > 0) {
				$jq .= 'widgetOptions : {';
				foreach ($this->widgetOptions as $widgetopt) {
					$jq .= ' ' . $widgetopt . ',';
				}
				//add filter functions
				if (is_array($this->filter_functions) && count($this->filter_functions) > 0) {
					$jq .= "\n\t\t\t" . 'filter_functions: { ';
					foreach ($this->filter_functions as $col => $functions) {
						$jq .= $col . ' : ';
						if (isset($functions[0]) && $functions[0] != 'true') {
							$jq .= '{';
						}
						foreach ($functions as $func) {
							$jq .= $func . ',';
						}
						//take off the last comma and close column
						$jq = substr($jq, 0, -1);
						if (isset($functions[0]) && $functions[0] != 'true') {
							$jq .= '}';
						}
					}
					//close filter_functions
					$jq .= '},';
				}
				//add filter_formatter options
				if (is_array($this->filter_formatter) && count($this->filter_formatter) > 0) {
					$jq .= "\n\t\t\t" . 'filter_formatter: { ';
					foreach ($this->filter_formatter as $col => $formats) {
						$jq .= $formats;
					}
					//take off the last comma and close column
					$jq = substr($jq, 0, -1);
					//close filter_formatter
					$jq .= "\n\t\t\t" . '}';
				}
				//take off the last comma
				if (substr($jq, -1) == ',') {
					$jq = substr($jq, 0, -1);
				}
				//close widget options
				$jq .= "\n\t\t" . '}';
			}
			//take off last comma
			if (substr($jq, -1) == ',') {
				$jq = substr($jq, 0, -1);
			}
			$jq .= "\n\t" . '})';
			$jq .= !empty($tspaginate) && $tspaginate != 'n' ? '.tablesorterPager(pagerOptions);' . "\n" . '});' : ';';
			$this->code['jq'][] = $jq;
			ksort($this->code['buttons']);
		} else {
			$this->code = false;
		}
	}

	function parseParam ($param)
	{
		if (!empty($param)) {
			$ret = explode('|', $param);
			foreach ($ret as $key => $pipe) {
				$ret[$key] = strpos($pipe, ';') !== false ? explode(';', $pipe) : $pipe;
				if (!is_array($ret[$key])) {
					if (strpos($ret[$key], ':') !== false) {
						$colon = explode(':', $ret[$key]);
						unset($ret[$key]);
						$ret[$key][$colon[0]] = $colon[1];
					}
				} elseif (is_array($ret[$key])) {
					foreach ($ret[$key] as $key2 => $subparam) {
						if (strpos($subparam, ':') !== false) {
							$colon = explode(':', $subparam);
							unset($ret[$key][$key2]);
							if ($colon[0] == 'expand' || $colon[0] == 'option') {
								$ret[$key][$colon[0]][] = $colon[1];
							} else {
								$ret[$key][$colon[0]] = $colon[1];
							}
						}
					}
				}
			}
			ksort($ret);
			return $ret;
		} else {
			return $param;
		}
	}
}
