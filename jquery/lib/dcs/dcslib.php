<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class DCSLib extends TikiLib {
	function DCSLib($db) {
		$this->TikiLib($db);
	}

	function remove_contents($contentId) {
		$query = "delete from `tiki_programmed_content` where `contentId`=?";

		$result = $this->query($query,array($contentId));
		$query = "delete from `tiki_content` where `contentId`=?";
		$result = $this->query($query,array($contentId));
	}

	function list_content($offset = 0, $maxRecords = -1, $sort_mode = 'contentId_desc', $find = '') {

		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " WHERE (`description` LIKE ? OR `contentLabel` LIKE ?)";
			$bindvars = array($findesc, $findesc);
		} else {
			$mid = '';
			$bindvars = array();
		}

		$query = 'SELECT `tc`.*, tpcd.`data`,'
				.' COALESCE(`tpcf`.`future`,0) AS `future`,'
				.' COALESCE(`tpca`.`actual`,?) AS `actual`,'
				.' COALESCE(`tpcn`.`next`,?) AS `next`,'
				.' GREATEST(0, COALESCE(`tpco`.`old`,0) - 1) AS `old`'
			.' FROM (`tiki_content` AS `tc`'
				.' LEFT JOIN ( SELECT `contentId`, count(*) AS `future` FROM `tiki_programmed_content` WHERE `publishDate`>? GROUP BY contentId ) AS `tpcf` ON ( `tc`.`contentId` = `tpcf`.`contentId` )'
				.' LEFT JOIN ( SELECT `contentId`, max(`publishDate`) AS `actual` FROM `tiki_programmed_content` WHERE `publishDate`<=? GROUP BY contentId ) AS `tpca` ON ( `tc`.`contentId` = `tpca`.`contentId` )'
				.' LEFT JOIN ( SELECT `contentId`, min(`publishDate`) AS `next` FROM `tiki_programmed_content` WHERE `publishDate`>=? GROUP BY contentId ) AS `tpcn` ON ( `tc`.`contentId` = `tpcn`.`contentId` )'
				.' LEFT JOIN ( SELECT `contentId`, count(*) AS `old` FROM `tiki_programmed_content` WHERE `publishDate`<? GROUP BY contentId ) AS `tpco` ON ( `tc`.`contentId` = `tpco`.`contentId` )'
				.' LEFT JOIN ( SELECT `contentId`, `data`, `publishDate` FROM `tiki_programmed_content` ) AS `tpcd` ON ( `tc`.`contentId` = `tpcd`.`contentId` AND `tpcd`.`publishDate` = `tpca`.`actual` ))'
			." $mid ORDER BY ".$this->convert_sortmode($sort_mode);

		$query_cant = "select count(*) from `tiki_content` $mid";
		$result = $this->query($query, array_merge(array($this->now, $this->now, $this->now, $this->now, $this->now, $this->now), $bindvars), $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ( $res = $result->fetchRow() ) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function get_actual_content_date($contentId) {

		$query = "select max(`publishDate`) from `tiki_programmed_content` where `contentId`=? and `publishDate`<=?";
		$res = $this->getOne($query,array($contentId,$this->now));
		return $res;
	}

	function get_random_content($contentId = 0) {

		$where = ' WHERE `publishDate`<=?';
		$bindvars = array($this->now);

		if ( (int)$contentId > 0 ) {
			$bindvars[] = $contentId;
			$where .= ' AND `contentId`=?';
		}

		$querycant = 'SELECT count(*) FROM `tiki_programmed_content`'.$where;
		$cant = $this->getOne($querycant, $bindvars);

		if ( ! $cant ) {
			return '';
		}

		$x = rand(0, $cant - 1);
		$query = 'SELECT `data` FROM `tiki_programmed_content`'.$where;
		$result = $this->query($query, $bindvars, 1, $x);
		$res = $result->fetchRow();

		return $res['data'];
	}

	function get_next_content($contentId) {

		$query = "select min(`publishDate`) from `tiki_programmed_content` where `contentId`=? and `publishDate`>?";
		$res = $this->getOne($query,array($contentId,$this->now));
		return $res;
	}

	function list_programmed_content($contentId, $offset = 0, $maxRecords = -1, $sort_mode = 'publishDate_desc', $find = '') {

		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where `contentId`=? and (`data` like ?) ";
			$bindvars=array($contentId,$findesc);
		} else {
			$mid = " where `contentId`=?";
			$bindvars=array($contentId);
		}

		$query = "select * from `tiki_programmed_content` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_programmed_content` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function replace_programmed_content($pId, $contentId, $publishDate, $data) {

		if (!$pId) {
			// was replace into ...
			$query = "insert into `tiki_programmed_content`(`contentId`,`publishDate`,`data`) values(?,?,?)";

			$result = $this->query($query,array($contentId,$publishDate, $data));
			$query = "select max(`pId`) from `tiki_programmed_content` where `publishDate`=? and `data`=?";
			$id = $this->getOne($query,array($publishDate,$data));
		} else {
			$query
				= "update `tiki_programmed_content` set `contentId`=?, `publishDate`=?, `data`=? where `pId`=?";

			$result = $this->query($query,array($contentId,$publishDate,$data,$pId));
			$id = $pId;
		}

		return $id;
	}

	function remove_programmed_content($id) {
		$query = "delete from `tiki_programmed_content` where `pId`=?";

		$result = $this->query($query,array($id));
		return true;
	}

	function get_content($fieldvalue, $fieldname = 'contentId') {
		if ( $fieldname != 'contentId' && $fieldname != 'contentLabel' ) return false;

		$query = 'select * from `tiki_content` where `'.$fieldname.'`=?';
		$result = $this->query($query, array($fieldvalue));
		$res = $result->fetchRow();

		return $res;
	}

	function get_programmed_content($id) {
		$query = "select * from `tiki_programmed_content` where `pId`=?";

		$result = $this->query($query,array($id));
		$res = $result->fetchRow();
		return $res;
	}

	function replace_content($contentId, $description, $label = null) {
		$bindvars = array($description);
		if ( $label !== null ) {
			$bindvars[] = $label;
		}

		if ( $contentId > 0 ) {
			$query = 'update `tiki_content` set `description`=?'
				.( $label === null ? '' : ',`contentLabel`=?' )
				.' where `contentId`=?';

			$bindvars[] = $contentId;
			$result = $this->query($query, $bindvars);
		} else {
			$query = 'insert into `tiki_content` (`description`'
				.( $label === null ? '' : ',`contentLabel`')
				.') values(?'
				.( $label === null ? '' : ',?')
				.')';

			$result = $this->query($query, $bindvars);
			$contentId = $this->getOne('select max(`contentId`) from `tiki_content` where `description` = ? and `contentLabel` = ?', $bindvars);
		}

		return $contentId;
	}
}
global $dbTiki;
$dcslib = new DCSLib($dbTiki);

?>
