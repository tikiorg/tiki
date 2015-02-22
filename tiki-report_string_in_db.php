<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

$inputConfiguration = array(
	array( 'staticKeyFilters' => array(
			'table' => 'word',
			'column' => 'word',
		)
	)
);

require_once ('tiki-setup.php');
$access->check_permission(array('tiki_p_admin'));

global $tikilib;
try {
	if (!empty($_POST['string_in_db_search'])) {
		$searchString = $_POST['string_in_db_search'];
		$result = searchAllDB($searchString);

		$smarty->assign('searchString', $searchString);
		$smarty->assign('searchResult', $result);

	} elseif (!empty($_POST['query'])) {
		$query = $_POST['query'];
		$table = $_POST['table'];
		sanitizeTableName($table);
		$column = $_POST['column'];
		sanitizeColumnName($column, $table);

		$headers = array();
		$sql2 = "SHOW COLUMNS FROM ".$table;
		$rs2 = $tikilib->fetchAll($sql2);
		foreach ($rs2 as $key2 => $val2) {
			$vals2 = array_values($val2);
			$colum = $vals2[0];
			$type = $vals2[1];
			$headers[] = $colum;
		}
		$smarty->assign('tableHeaders', $headers);

		$tableData = array();
		$qrySearch = '%'.$query.'%';
		$args = array($qrySearch);
		$sql = "select * from `" . $table . "` where `" . $column . "` like ?";
		$rs = $tikilib->fetchAll($sql, $args);
		foreach ($rs as $row) {
			$tableData[] = $row;
		}
		$smarty->assign('tableData', $tableData);
	}
} catch (Exception $e) {
	$smarty->assign('errorMsg', $e->getMessage());
}
$smarty->assign('mid', 'tiki-report_string_in_db.tpl');
$smarty->display('tiki.tpl');

/*
*	return array (table, attribute, occurrence count)
*/
function searchAllDB($search)
{
	global $tikilib;

	$result = array();
	$out = '';

	$sql = "show tables";
	$rs = $tikilib->fetchAll($sql);
	foreach ($rs as $key => $val) {
		$vals = array_values($val);
		$table = $vals[0];
		$sql2 = "SHOW COLUMNS FROM `$table`";
		$rs2 = $tikilib->fetchAll($sql2);
		foreach ($rs2 as $key2 => $val2) {
			$vals2 = array_values($val2);
			$colum = $vals2[0];
			$type = $vals2[1];
			if (isTextType($type)) {
				$sql_search_fields = Array();
				$qrySearch = '%'.$search.'%';
				$args = array($qrySearch);
				$sql_search_fields[] = "`" . $colum . "` like ?"; // '%" . str_replace("'", "''", $search) . "%'";
				$sql_search = "select * from `$table` where ";
				$sql_search .= implode(" OR ", $sql_search_fields);
				$rs3 = $tikilib->fetchAll($sql_search, $args);
				if (!empty($rs3)) {
					$result[] = array('table' => $table, 'column' => $colum, 'occurrences' => count($rs3));
				}
			}
		}
	}
	return $result;
}

function isTextType($type)
{
	if (strpos($type, 'char') !== false) {
		return true;
	}
	if (strpos($type, 'text') !== false) {
		return true;
	}
	return false;
}

function sanitizeTableName($table)
{
	global $tikilib;
	$validTables = $tikilib->listTables();
	if (!in_array($table, $validTables)) {
		throw new Exception(tra('Invalid table name:') . ' ' . htmlentities($table));
	}
}

function sanitizeColumnName($column, $table)
{
	global $tikilib;
	$colsinfo = $tikilib->fetchAll("SHOW COLUMNS FROM $table");
	foreach ($colsinfo as $col) {
		$colnames[] = $col['Field'];
	}
	if (!in_array($column, $colnames)) {
		throw new Exception(tra('Invalid column name:') . ' ' . htmlentities($column));
	}
}