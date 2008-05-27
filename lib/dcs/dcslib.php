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
			$findesc = '%' . $find . '%';

			$mid = " where (`description` like ?)";
			$bindvars=array($findesc);
		} else {
			$mid = '';
			$bindvars=array();
		}

		$query = "select * from `tiki_content` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_content` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			// Add actual version
			// Add number of programmed versions
			// Add next programmed version
			// Add number of old versions

			$id = $res["contentId"];
			$query = "select count(*) from `tiki_programmed_content` where `publishDate`>? and `contentId`=?";
			$res["future"] = $this->getOne($query,array($this->now,$id));
			$query = "select max(`publishDate`) from `tiki_programmed_content` where `contentId`=? and `publishDate`<=?";
			$res["actual"] = $this->getOne($query,array($id,$this->now));
			$query = "select min(`publishDate`) from `tiki_programmed_content` where `contentId`=? and `publishDate`>=?";
			$res["next"] = $this->getOne($query,array($id,$this->now));
			$query = "select count(*) from `tiki_programmed_content` where `contentId` = ? and `publishDate`<?";
			$res["old"] = $this->getOne($query,array($id,$this->now));

			if ($res["old"] > 0)
				$res["old"]--;

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

	function get_random_content($contentId) {

		$querycant = "select count(*) from `tiki_programmed_content` where `contentId`=? and `publishDate`<=?";
		$cant = $this->getOne($querycant,array($contentId,$this->now));

		if (!$cant)
			return '';

		$x = rand(0, $cant - 1);
		$query = "select `data` from `tiki_programmed_content` where `contentId`=? and `publishDate`<=?";
		$result = $this->query($query,array($contentId,$this->now),1,$x);
		$res = $result->fetchRow();
		return $res["data"];
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

	function get_content($id) {
		$query = "select * from `tiki_content` where `contentId`=?";

		$result = $this->query($query,array($id));
		$res = $result->fetchRow();
		return $res;
	}

	function get_programmed_content($id) {
		$query = "select * from `tiki_programmed_content` where `pId`=?";

		$result = $this->query($query,array($id));
		$res = $result->fetchRow();
		return $res;
	}

	function replace_content($contentId, $description) {

		if ($contentId > 0) {
			$query = "update `tiki_content` set `description`=? where `contentId`=?";

			$result = $this->query($query,array($description,$contentId));
			return $contentId;
		} else {
			$query = "insert into `tiki_content`(`description`) values(?)";

			$result = $this->query($query,array($description));
			$query = "select max(`contentId`) from `tiki_content` where `description` = ?";
			$id = $this->getOne($query,array($description));
			return $id;
		}
	}
}
global $dbTiki;
$dcslib = new DCSLib($dbTiki);

?>
