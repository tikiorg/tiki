<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
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
 * Class Table_Code_WidgetOptionsFilter
 *
 * Creates the code for the filter widget options portion of the Tablesorter jQuery code
 *
 * @package Tiki
 * @subpackage Table
 * @uses Table_Code_WidgetOptions
 */
class Table_Code_WidgetOptionsFilter extends Table_Code_WidgetOptions
{

	protected function getOptionArray()
	{
		if (parent::$filters) {
			$wof[] = 'filter_cssFilter : \'form-control\'';
			//allows for different label versus value in dropdowns
			$wof[] = 'filter_selectSourceSeparator : \'|\'';
			//server side filtering
			if (parent::$ajax) {
				$wof[] = 'filter_serversideFiltering : true';
			}
			//hide filters
			if (isset(parent::$s['filters']['hide']) && parent::$s['filters']['hide'] === true) {
				$wof[] = 'filter_hideFilters : true';
			}
			//filter reset
			if (isset(parent::$s['filters']['type']) && parent::$s['filters']['type'] === 'reset') {
				$wof[] = 'filter_reset : \'button#' . parent::$s['filters']['reset']['id'] . '\'';
			}
			//filter_functions and filter_formatter
			if (parent::$filtercol) {
				$ffunc = '';
				$fform = '';
				foreach (parent::$s['columns'] as $col => $info) {
					$info = !empty($info['filter']) ? $info['filter'] : [];
					$colpointer =  parent::$usecolselector ? (string) '\'' . $col . '\'' : (int) $col;
					if (!empty($info['type'])) {
						switch($info['type']) {
							case 'dropdown' :
								$o = [];
								if (array_key_exists('options', $info)) {
									foreach ($info['options'] as $key => $val) {
										$label =  addcslashes(is_numeric($key) ? $val : $key,"'/");
										if (parent::$ajax) {
											$o[] = '\'' . $label . '\' : function() {}';
										} else {
											$o[] = '\'' . $label . '\' : function(e, n, f, i) { return /' . $val
													. '/.test(e);}';
										}
									}
									if (count($o) > 0) {
										$options = $this->iterate($o, '{', $this->nt4 . '}', $this->nt5, '', ',');
										$ffunc[] = $colpointer . ' : ' . $options;
									}
								} elseif (!parent::$ajax) {
									$ffunc[] = $colpointer . ' : true';
								}
								break;
							case 'range' :
								$min = isset($info['from']) ? $info['from'] : 0;
								$max = isset($info['to']) ? $info['to'] : 100;
								$valtohead = isset($info['style']) && $info['style'] == 'popup' ? 'false' : 'true';
								$fform[] = $colpointer . ' : function($cell, indx){return $.tablesorter.filterFormatter.uiRange('
										. '$cell, indx, {values: [' . $min . ', ' . $max . '], min: ' . $min . ', max: ' . $max
										. ', delayed: false, valueToHeader: ' . $valtohead . ', exactMatch: true});}';
								break;
							case 'date' :
								$fm = isset($info['from']) ? $info['from'] : '';
								$to = isset($info['to']) ? $info['to'] : '';
								$format = isset($info['format']) ? $info['format'] : 'yy-mm-dd';
								$fform[] = $colpointer . ' : function($cell, indx){return $.tablesorter.filterFormatter.uiDatepicker('
										. '$cell, indx, {from: \'' . $fm . '\', to: \'' . $to . '\', dateFormat: \'' . $format
										. '\', changeMonth: true, changeYear: true});}';
								break;
						}
					}
				}
				unset($col, $info);
				if (is_array($ffunc)) {
					$wof[] = $this->iterate($ffunc, 'filter_functions : {', $this->nt3 . '}', $this->nt4, '');
				}
				if (is_array($fform)) {
					$wof[] = $this->iterate($fform, 'filter_formatter : {', $this->nt3 . '}', $this->nt4, '');
				}
			}
			if (count($wof) > 0) {
				return $wof;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}