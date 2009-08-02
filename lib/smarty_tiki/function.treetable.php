<?php
// (c) Copyright 2002-2009 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
//
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
 * 				if undefined it tries to guess (?)
 * 
 * _valueColumnIndex = 0	:	 index of the col in the array above to use as the unique index
 * 
 * _treeColumn	: col to nest trees by
 * 
 * _checkbox = 'treechecks_1'	: name of checkbox (auto-incrementing)
 * 
 * _listFilter = 'y'	: include dynamic text filter
 * 
 * _filterMinRows = 12	: don't show filter box if less than this number of rows
 * 
 * class = 'treetable'	: class of the table - will add 'sortable' if feature_jquery_sortable = y
 * id = 'treetable1'	: id of the table (auto-incrementing)
 * 
 * _rowClasses = array('odd','even')	:	classes to cycle through for rows (tr's and td's)
 * 											can be a string for same class on each row
 * 											or empty string for not
 * 
 * _emptyDataMessage = tra('No rows found')	: message if there are no rows
 * 
 * _treeColumn = ''	:	column to organise tree by (actually row key = e.g. 'type')
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
	
	$html = '';
	$nl = "\n";
	
	// some defaults
	$_listFilter = empty($_listFilter) ? 'y' : $_listFilter;
	$_filterMinRows = empty($_filterMinRows) ? 12 : $_filterMinRows;
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
				$_columns[$key] = htmlentities($key);
			}
		}
	}
	$_valueColumnIndex = empty($_valueColumnIndex) ? 0 : $_valueColumnIndex;
	$_treeColumn = empty($_treeColumn) ? '' : $_treeColumn;
	
	if ($_treeColumn) {
		$headerlib->add_jq_onready('$jq("#'.$id.'").treeTable({clickableNodeNames:true});');	// {treeColumn:3}
	}
	
	$class = empty($class) ? '' : $class;	// treetable
	
	if ($prefs['feature_jquery_tablesorter'] == 'y' && strpos($class, 'sortable') === false) {
		 //$class .= ' sortable';
	}
	$_checkbox = empty($_checkbox) ? 'treechecks_'. $tree_table_id : $_checkbox;
	
	if ($_listFilter == 'y' && count($_data) > $_filterMinRows) {
		include_once('lib/smarty_tiki/function.listfilter.php');
		$html .= smarty_function_listfilter(array('selectors' => "#$id tr"));
	}
	
	// start writing the table
	$html .= $nl.'<table id="'.$id.'" class="'.$class.'">'.$nl;
	
	// write the table header
	$html .= '<thead><tr>';
	$html .= '<th>';
	include_once('lib/smarty_tiki/function.select_all.php');
	$html .= smarty_function_select_all(array('checkbox_names'=>$_checkbox));
	$html .= '</th>';
	
	foreach ($_columns as $column => $columnName) {
		$html .= '<th>';
		$html .= htmlentities($columnName);
		$html .= '</th>';
	}
	$html .= '</tr></thead>'.$nl;
	$html .= '<tbody>'.$nl;
	
	$treeColumnsAdded = array();
	
		// for each row
	foreach ($_data as &$row) {
		// set up tree hierarchy
		if ($_treeColumn) {
			$treeType = htmlentities($row[$_treeColumn]);
			if (!in_array($treeType, $treeColumnsAdded)) {
				$html .= '<tr id="'.$id.'_'.$treeType.'"><td>';	//  colspan="'.(count($_columns) + 1).'"
				$html .= $treeType.'</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>'.$nl;
				$treeColumnsAdded[] = $treeType;
			}
			$childRowClass = ' child-of-'.$id.'_'.$treeType;
		} else {
			$rowId = '';
		}
		
		// work out row class (odd/even etc)
		if ($rowCounter > -1) {
			$rowClass = ' class="'.$_rowClasses[$rowCounter].$childRowClass.'"';
			$rowCounter++;
			if ($rowCounter >= count($_rowClasses)) { $rowCounter = 0; }
		} else {
			$rowClass = ' class="'.$childRowClass.'"';
		}
		// get row's "value"
		$rowVal = htmlentities($row[$_columns[$_valueColumnIndex]]);
		
		$html .= '<tr'.$rowClass.'>';
		// add the checkbox
		$html .= '<td'.$rowClass.'>';
		$html .= '<input type="checkbox" name="'.$_checkbox.'" value="'.$rowVal.'" title="'.$rowVal.'"/>';
		$html .= '</td>';
		
		foreach ($_columns as $column => $columnName) {
			$html .= '<td>';
			$html .= htmlentities($row[$column]);
			$html .= '</td>'.$nl;
		}
		$html .= '</tr>'.$nl;					
	}
	$html .= '</tbody></table>'.$nl;
		
	return $html;
	
//
//{cycle print=false values="even,odd"}
//{section name=prm loop=$perms}
//<tr class="{cycle advance=true}">
//  <td class="{cycle advance=false}" title="{$perms[prm].permName|escape}">
//    <input type="checkbox" name="perm[]" value="{$perms[prm].permName|escape}" title="{$perms[prm].permName|escape}"/>
//  </td>
//  <td class="{cycle advance=false}">
//    {$perms[prm].permName|escape}
//  </td>
//  <td class="{cycle advance=false}">
//    <div class="subcomment">{tr}{$perms[prm].permDesc|escape}{/tr}</div>
//  </td>
//  </tr>
//{/section}
//</table>
	
}