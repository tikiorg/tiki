<?php
/**
 * @package tikiwiki
 */
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once ('tiki-setup.php');
$access->check_permission(array('tiki_p_admin_wiki'));

global $tikilib;
try {
	if (!empty($_REQUEST['string_in_db_search'])) {
		$searchString = $_REQUEST['string_in_db_search'];
		$result = searchAllDB($searchString);

		$smarty->assign('searchString', htmlentities($searchString));
		$smarty->assign('searchResult', $result);

	} elseif (!empty($_REQUEST['query'])) {
		$query = $_REQUEST['query'];
		$table = $_REQUEST['table'];
		sanitizeTableName($table);
		$column = $_REQUEST['column'];
		sanitizeColumnName($column);

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
		$sql2 = "SHOW COLUMNS FROM ".$table;
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
				$sql_search = "select * from " . $table . " where ";
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
	$illigalChars = '/\\.<>%\'""';	// The character set should be checked more
	if (!empty(strpbrk($table, $illigalChars))) {
		throw new Exception("Invalid table name: ".htmlentities($table));
	}
}

function sanitizeColumnName($column)
{
	$illigalChars = '/\\.<>%\'""';	// The character set should be checked more
	if (!empty(strpbrk($column, $illigalChars))) {
		throw new Exception("Invalid column name: ".htmlentities($column));
	}
}