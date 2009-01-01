<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class SearchStatsLib extends TikiLib {
	function SearchStatsLib($db) {
		$this->TikiLib($db);
	}

	function clear_search_stats() {
		$query = "delete from tiki_search_stats";
		$result = $this->query($query,array());
	}

	function list_search_stats($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$mid = " where (`term` like ?)";
			$bindvars = array("%$find%");
		} else {
			$mid = "";
			$bindvars = array();
		}

		$query = "select * from `tiki_search_stats` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_search_stats` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}
}
global $dbTiki;
$searchstatslib = new SearchStatsLib($dbTiki);

?>
