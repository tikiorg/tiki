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
 * Class Table_Code_WidgetOptions
 *
 * Creates the code for the widget options portion of the Tablesorter jQuery code
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Code_Manager
 */
class Table_Code_WidgetOptions extends Table_Code_Manager
{

	public function setCode()
	{
		$wo[] = 'stickyHeaders : \'tablesorter-stickyHeader\'';

		if (isset($this->s['sort']['group']) && $this->s['sort']['group'] !== false) {
			$wo[] = 'group_collapsible : true';
			$wo[] = 'group_count : \' ({num})\'';
		}
		//saveSort
		if (isset($this->s['sort']['type']) && strpos($this->s['sort']['type'], 'save') !== false) {
			$wo[] = 'saveSort : true';
		}

		//plain filter
		if ($this->filters) {
			$wo[] = 'filter_cssFilter : \'tablesorter-filter\'';
			$wo[] = 'filter_searchDelay : 300';
		}

		//hide filters
		if (isset($this->s['filters']['hide']) && $this->s['filters']['hide'] === true) {
			$wo[] = 'filter_hideFilters : true';
		}

		//filter reset
		if ($this->filters && $this->s['filters']['type'] === 'reset') {
			$wo[] = 'filter_reset : \'button#' . $this->s['filters']['id'] . '\'';
		}

		//filter_functions and filter_formatter
		if ($this->filters && isset($this->s['filters']['columns']) && is_array($this->s['filters']['columns'])) {
			$ffunc = '';
			$fform = '';
			foreach ($this->s['filters']['columns'] as $col => $info) {
				switch($info['type']) {
					case 'dropdown' :
						$o = '';
						if (array_key_exists('options', $info)) {
							foreach ($info['options'] as $key => $val) {
								$label = is_numeric($key) ? $val : $key;
								$o[] = '\'' . $label . '\' : function(e, n, f, i) { return /' . $val . '/.test(e);}';
							}
							$options = $this->iterate($o, '{', $this->nt4 . '}', $this->nt5, '', ',');
							$ffunc[] = $col . ' : ' . $options;
						} else {
							$ffunc[] = $col . ' : true';
						}
						break;
					case 'range' :
						$min = isset($info['from']) ? $info['from'] : 0;
						$max = isset($info['to']) ? $info['to'] : 100;
						$valtohead = isset($info['style']) && $info['style'] == 'popup' ? 'false' : 'true';
						$fform[] = $col . ' : function($cell, indx){return $.tablesorter.filterFormatter.uiRange('
								. '$cell, indx, {values: [' . $min . ', ' . $max . '], min: ' . $min . ', max: ' . $max
								. ', delayed: false, valueToHeader: ' . $valtohead . ', exactMatch: true});}';
						break;
					case 'date' :
						$fm = isset($info['from']) ? $info['from'] : '';
						$to = isset($info['to']) ? $info['to'] : '';
						$format = isset($info['format']) ? $info['format'] : 'yy-mm-dd';
						$fform[] = $col . ' : function($cell, indx){return $.tablesorter.filterFormatter.uiDatepicker('
							. '$cell, indx, {from: \'' . $fm . '\', to: \'' . $to . '\', dateFormat: \'' . $format
							. '\', changeMonth: true, changeYear: true});}';
						break;
				}
			}
			if (is_array($ffunc)) {
				$wo[] = $this->iterate($ffunc, 'filter_functions : {', $this->nt3 . '}', $this->nt4, '');
			}
			if (is_array($fform)) {
				$wo[] = $this->iterate($fform, 'filter_formatter : {', $this->nt3 . '}', $this->nt4, '');
			}
		}
		if (count($wo) > 0) {
			$code = $this->iterate($wo, $this->nt2 . 'widgetOptions : {', $this->nt2 . '}', $this->nt3, '');
			parent::$code[self::$level1][self::$level2] = $code;
		}
	}

}