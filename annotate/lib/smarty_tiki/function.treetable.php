<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Tree Table Smarty func - smarty_function_treetable()
 * Renders a tree table (for use with http://plugins.jquery.com/project/treeTable)
 * 
 * Params
 * 
 * _data	:	array of data rows	 - 	e.g . with perms for now
 * 
 * 	array(
 * 		array('permName'=>'tiki_p_admin_newsletters', 'permDesc' => 'Can admin newsletters','level' => 'admin', 'type' => 'newsletters' etc...),
 * 		array('permName'=>'tiki_p_blahblah', etc...),
 * 	...)
 * 
 * _columns	: array of columns and headers array('permName' => tra('Permission Name'), 'permDesc' => tra('Permission Description'), etc
 * 				or a string like: '"permName"="Permission Name", "permDesc"="Description", etc'
 * 				if undefined it tries to guess (?)
 * 
 * _valueColumnIndex = 0	:	index (or name) of the col in the _data array above to use as the unique index
 * 
 * _sortColumn = ''			:	column to organise tree by (actually row key = e.g. 'type')
 * 
 * _sortColumnDelimiter = '':	if set (e.g. to ',') sorting will be nested accoding to this delimiter
 * 								e.g. if the _sortColumn value is 'gran-parent,parent,child' the 'child' section will be nested 3 levels deep
 * 
 * _checkbox = ''			: 	name of checkbox (auto-incrementing) - no checkboxes if not set
 * 								if comma delimited list (or array) then makes multiple checkboxes
 * 
 * _checkboxColumnIndex = 0	:	index (or name) of the col in the _data array above to use as the checkbox value
 * 								comma delimeted list (or array - of ints) for multiple checkboxes as set above
 * 								if set needs to match number of checkboxes defines in _checkbox (or if not set uses 0,1,2 etc)
 * 
 * _checkboxTitles = ''		:	Comma delimited list (or array) of header titles for checkboxes (optional, but needs to match number of checkboxes above)
 * 
 * _listFilter = 'y'		:	include dynamic text filter
 * 
 * _filterMinRows = 12		:	don't show filter box if less than this number of rows
 * 
 * _collapseMaxSections = 4 :	collapse tree sections of more than this number of sections showing on page load
 * 
 * class = 'treeTable'		:	class of the table - will add 'sortable' if feature_jquery_sortable = y
 * id = 'treetable1'		:	id of the table (auto-incrementing)
 * 
 * _rowClasses = array('odd','even')	:	classes to cycle through for rows (tr's and td's)
 * 											can be a string for same class on each row
 * 											or empty string for not
 * 
 * _columnsContainHtml = 'n':	Column data gets html encoded (by default)
 * 
 * _emptyDataMessage = tra('No rows found')	: message if there are no rows
 * 
 * _openall					: show folder button to open all areas (y/n default=n)
 * 
 * _showSelected			: checkbox to show only selected (y/n default=n)
 */

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

function smarty_function_treetable($params, &$smarty) {
	global $headerlib, $tree_table_id, $prefs;
	
	extract($params);
	
	$_emptyDataMessage = empty($_emptyDataMessage) ? tra('No rows found') : $_emptyDataMessage;
	if (empty($_data)) {
		return $_emptyDataMessage;
	}
	
	$_checkbox = empty($_checkbox) ? '' : $_checkbox;
	$_checkboxTitles = empty($_checkboxTitles) ? '' : $_checkboxTitles;
	$_openall = isset($_openall) ? $_openall : 'n';
	$_showSelected = isset($_showSelected) ? $_showSelected : 'n';
	
	if (is_string($_checkbox) && strpos($_checkbox, ',') !== false) {
		$_checkbox = preg_split('/,/', trim($_checkbox));
	}
	if (!empty($_checkboxColumnIndex)) {
		if (is_string($_checkboxColumnIndex) && strpos($_checkboxColumnIndex, ',') !== false) {
			$_checkboxColumnIndex = preg_split('/,/', trim($_checkboxColumnIndex));
		}
		if (count($_checkbox) != count($_checkboxColumnIndex)) {
			return tra('Number of items in _checkboxColumnIndex doesn not match items in _checkbox');
		}
	}
	if (!empty($_checkboxTitles)) {
		if (is_string($_checkboxTitles)) {
			if (strpos($_checkboxTitles, ',') !== false) {
				$_checkboxTitles = preg_split('/,/', trim($_checkboxTitles));
			} else {
				$_checkboxTitles = array(trim($_checkboxTitles));
			}
		}
		if (count($_checkbox) != count($_checkboxTitles)) {
			return tra('Number of items in _checkboxTitles doesn not match items in _checkbox');
		}
	}
	$_checkboxColumnIndex = empty($_checkboxColumnIndex) ? 0 : $_checkboxColumnIndex;
	$_valueColumnIndex = empty($_valueColumnIndex) ? 0 : $_valueColumnIndex;
	
	if (!empty($_checkbox) && !is_array($_checkbox)) {
			$_checkbox = array($_checkbox);
			$_checkboxColumnIndex = array($_checkboxColumnIndex);
	}
	
	$_columnsContainHtml = isset($_columnsContainHtml) ? $_columnsContainHtml : 'n';
	
	$html = '';
	$nl = "\n";
	
	// some defaults
	$_listFilter = empty($_listFilter) ? 'y' : $_listFilter;
	$_filterMinRows = empty($_filterMinRows) ? 12 : $_filterMinRows;
	$_collapseMaxSections = empty($_collapseMaxSections) ? 4 : $_collapseMaxSections;
	
	$_rowClasses = !isset($_rowClasses) ? array('odd','even') : 
		(is_array($_rowClasses) ? $_rowClasses : array($_rowClasses));
	
	if (!empty($_rowClasses)) {
		$rowCounter = 0;
	} else {
		$rowCounter = -1;
	}
	
	// auto-increment val for unique id's etc
	if (empty($id)) {
		if (!isset($tree_table_id)) {
			$tree_table_id = 1;
		} else {
			$tree_table_id++;
		}
		$id = 'treetable_'. $tree_table_id;
	} else {
		$id;
	}
	// TODO - check this? add key/val pairs?
	if (empty($_columns)) {
		$keys =  array_keys($_data[0]);
		$_columns = array();
		foreach($keys as $key) {
			if (!is_numeric($key)) {
				$_columns[$key] = htmlspecialchars($key);
			}
		}
	} else if (is_string($_columns)) {
		$ar = preg_split('/,/', $_columns);
		$_columns = array();
		foreach ($ar as $str) {
			$ar2 = preg_split('/=/', trim($str));
			$_columns[trim($ar2[0],' "')] = trim($ar2[1],' "');
		}
		unset($ar, $ar2);
	}
	
	$_sortColumn = empty($_sortColumn) ? '' : $_sortColumn;
	$_groupColumn = empty($_groupColumn) ? '' : $_groupColumn;
	
	if ($_sortColumn) {
		sort2d($_data, $_sortColumn);
	} elseif ($_groupColumn) {
		sort2d($_data, $_groupColumn, false);
		$_sortColumn = $_groupColumn;
	}
	
	$class = empty($class) ? 'treeTable' : $class;	// treetable

/*	
	if ($prefs['feature_jquery_tablesorter'] == 'y' && strpos($class, 'sortable') === false) {
		 //$class .= ' sortable';
	}
*/

	if ($_listFilter == 'y' && count($_data) > $_filterMinRows) {
		require_once($smarty->_get_plugin_filepath('function', 'listfilter'));
		$html .= smarty_function_listfilter(
			array('id' => $id.'_filter',
				  'selectors' => "#$id tbody tr:not(.parent)",
				  'parentSelector' => "#$id tbody .parent",
				  'exclude' => ".subHeader"),  $smarty);
	}

	if ($_openall == 'y') {
		require_once($smarty->_get_plugin_filepath('function', 'icon'));
		$html .= '&nbsp;' . smarty_function_icon(
			array('_id' => 'folder',
				'id' => $id.'_openall',
				'title' => tra('Toggle sections')),	$smarty) .
			' ' . tra('Toggle sections');
		
		$headerlib->add_jq_onready('
$("#'.$id.'_openall").click( function () {
	if (this.src.indexOf("ofolder.png") > -1) {
		
		$(".expanded .expander").eachAsync({
			delay: 20,
			bulk: 0,
			loop: function () {
				$(this).click();
			}
		});
		this.src = this.src.replace("ofolder", "folder");
	} else {
		$(".collapsed .expander").eachAsync({
			delay: 20,
			bulk: 0,
			loop: function () {
				$(this).click();
			}
		});
		this.src = this.src.replace("folder", "ofolder");
	}
	return false;
});');
	}
	
	if ($_showSelected == 'y') {
		require_once($smarty->_get_plugin_filepath('function', 'icon'));
		$html .= ' <input type="checkbox" id="'.$id.'_showSelected" title="'.tra('Show only selected').'" />';
		$html .= ' ' . tra('Show only selected');
				
		$headerlib->add_jq_onready('
$("#'.$id.'_showSelected").click( function () {
	if (!$(this).attr("checked")) {
		$("#treetable_1 tr td.checkBoxCell input:checkbox").parent().parent().show()
	} else {
		$("#treetable_1 tr td.checkBoxCell input:checkbox").parent().parent().hide()
		$("#treetable_1 tr td.checkBoxCell input:checked").parent().parent().show()
	}
});');
	}
	
	// start writing the table
	$html .= $nl.'<table id="'.$id.'" class="'.$class.'">'.$nl;
	
	// write the table header
	$html .= '<thead><tr>';
	if (!empty($_checkbox)) {
		include_once('lib/smarty_tiki/function.select_all.php');
		for ($i = 0, $icount_checkbox = count($_checkbox); $i < $icount_checkbox; $i++) {
			$html .= '<th class="checkBoxHeader">';
			$html .= smarty_function_select_all(
						array('checkbox_names'=>array($_checkbox[$i].'[]'),
							  'label' => empty($_checkboxTitles) ? '' : htmlspecialchars($_checkboxTitles[$i])), $smarty);
			$html .= '</th>';
		}
	}
	
	foreach ($_columns as $column => $columnName) {
		$html .= '<th>';
		$html .= htmlspecialchars($columnName);
		$html .= '</th>';
	}
	$html .= '</tr></thead>'.$nl;
	$html .= '<tbody>'.$nl;
	
	$treeSectionsAdded = array();
	
		// for each row
	foreach ($_data as &$row) {
		// set up tree hierarchy
		if ($_sortColumn) {
			$treeType = htmlspecialchars(trim($row[$_sortColumn]));
			$treeTypeId = '';
			$childRowClass = '';
			if (!empty($_sortColumnDelimiter)) {	// nested
				$parts = array_reverse(explode($_sortColumnDelimiter, $treeType));
				for ($i = 0, $icount_parts = count($parts); $i < $icount_parts; $i++) {
					$part = preg_replace('/\s+/', '_', $parts[$i]);
					if (in_array($part, $treeSectionsAdded) && $i > 0) {
						$treeParentId = preg_replace('/\s+/', '_', $parts[$i]);
						$childRowClass = ' child-of-'.$id.'_'.$treeParentId;
						$treeTypeId = preg_replace('/\s+/', '_', $parts[$i - 1]);
						$treeType = $parts[$i - 1];
						break;
					}
				}
				if (empty($treeTypeId)) {
					$treeTypeId = preg_replace('/\s+/', '_', $part);
				}
				$treeSectionsAdded[] = $treeTypeId;
				$rowId = ' id="'.$id.'_'.$treeTypeId.'"';
				
				//$childRowClass = ' child-of-'.$id.'_'.$treeTypeId;
			} else {
				$treeTypeId = preg_replace('/\s+/', '_', $treeType);
				$childRowClass = ' child-of-'.$id.'_'.$treeTypeId;
				$rowId = '';
				
				if (!empty($treeType) && !in_array($treeTypeId, $treeSectionsAdded)) {
					$html .= '<tr id="'.$id.'_'.$treeTypeId.'"><td colspan="'.(count($_columns) + count($_checkbox)).'">';
					$html .= $treeType.'</td></tr>'.$nl;
					
					// Courtesy message to help category perms configurators
					if ($treeType == 'category') {
						$html .= '<tr class="subHeader'.$childRowClass.'"><td colspan="'.(count($_columns) + count($_checkbox)).'">';
						$html .= tra('You might want to also set the tiki_p_modify_object_categories permission under the tiki section') . '</td></tr>'.$nl;
					}
					$treeSectionsAdded[] = $treeTypeId;
					
					// write a sub-header
					$html .= '<tr class="subHeader'.$childRowClass.'">';
					if (!empty($_checkbox)) {
						for ($i = 0, $icount_checkbox = count($_checkbox); $i < $icount_checkbox; $i++) {
							$html .= '<td class="checkBoxHeader">';
							$html .= empty($_checkboxTitles) ? '' : htmlspecialchars($_checkboxTitles[$i]);
							$html .= '</td>';
						}
					}
					foreach ($_columns as $column => $columnName) {
						$html .= '<td>';
						$html .= htmlspecialchars($columnName);
						$html .= '</td>';
					}
					$html .= '</tr>'.$nl;
				}
			}
		} else {
			$rowId = '';
			$childRowClass = '';
		}
		
		// work out row class (odd/even etc)
		if ($rowCounter > -1) {
			$rowClass = $_rowClasses[$rowCounter].$childRowClass;
			$rowCounter++;
			if ($rowCounter >= count($_rowClasses)) { $rowCounter = 0; }
		} else {
			$rowClass = $childRowClass;
		}
		
		$html .= '<tr class="'.$rowClass.'"'.$rowId.'>';
		// add the checkbox
		if (!empty($_checkbox)) {
			for ($i = 0, $icount_checkbox = count($_checkbox); $i < $icount_checkbox; $i++) {
				// get checkbox's "value"
				$cbxVal = htmlspecialchars($row[$_checkboxColumnIndex[$i]]);
				$rowVal = htmlspecialchars($row[$_valueColumnIndex]);
				$cbxTit = empty($_checkboxTitles) ? $cbxVal : htmlspecialchars($_checkboxTitles[$i]);
				$html .= '<td class="checkBoxCell">';
				$html .= '<input type="checkbox" name="'.htmlspecialchars($_checkbox[$i]).'[]" value="'.$rowVal.'"'.($cbxVal=='y' ? ' checked="checked"' : '').' title="'.$cbxTit.'" />';
				if ($cbxVal == 'y') {
					$html .= '<input type="hidden" name="old_'.htmlspecialchars($_checkbox[$i]).'[]" value="'.$rowVal.'" />';
				}
				$html .= '</td>';
			}
		}
		
		foreach ($_columns as $column => $columnName) {
			$html .= '<td>';
			if ($_columnsContainHtml != 'y') {
				$html .= htmlspecialchars($row[$column]);
			} else {
				$html .= $row[$column];
			}
			$html .= '</td>'.$nl;
		}
		$html .= '</tr>'.$nl;					
	}
	$html .= '</tbody></table>'.$nl;
	
	// add jq code to initial treeetable
	$expanable = empty($_sortColumnDelimiter) ? 'true' : 'false';	// when nested, clickableNodeNames is really annoying
	if (count($treeSectionsAdded) < $_collapseMaxSections) {
		$headerlib->add_jq_onready('$("#'.$id.'").treeTable({clickableNodeNames:'.$expanable.',initialState: "expanded"});');
	} else {
		$headerlib->add_jq_onready('$("#'.$id.'").treeTable({clickableNodeNames:'.$expanable.',initialState: "collapsed"});');
	}
	// TODO refilter when .parent is opened - seems to prevent the click propagating
//		$headerlib->add_jq_onready('$("tr.parent").click(function(event) {
//if ($("#'.$id.'_filter").val()) {
//	$("#'.$id.'_filter").trigger("keyup");
//	if (event.isPropagationStopped() || event.isImmediatePropagationStopped()) {
//		$(this).trigger("click");
//	}
//}
//		});');
		
	return $html;

}


// TODO - move this somewhere sensible
// from http://uk.php.net/sort?

// $sort used as variable function--can be natcasesort, for example
// WARNING: $sort must be associative
function sort2d( &$arrIn, $index = null, $sort = 'asort') {
	// pseudo-secure--never allow user input into $sort
	$arrTemp = Array();
	$arrOut = Array();
	foreach ( $arrIn as $key=>$value ) {
		$arrTemp[$key] = is_null($index) ? reset($value) : $value[$index];
	}

	if ($sort) {
		if (strpos($sort, 'sort') === false) {$sort = 'asort';}
		$sort($arrTemp);
	}

	foreach ( $arrTemp as $key=>$value ) {
		$arrOut[$key] = $arrIn[$key];
	}
	$arrIn = $arrOut;
}
