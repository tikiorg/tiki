<?php

/*
* @author: Javier Reyes Gomez (jreyes@escire.com)
* @date: 27/01/2006
* @copyright (C) 2006 Javier Reyes Gomez (eScire.com)
* @license http://www.gnu.org/copyleft/lgpl.html GNU/LGPL
*/

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

class PeriodsLib extends TikiLib {
	function PeriodsLib($db) {
		$this->TikiLib($db);
	}

	function add_period($periodTypeId, $name, $description, $startDate, $endDate, $gradeWeight) {

		$query = "insert into aulawiki_period(periodTypeId,name,description,startDate,endDate,gradeWeight,uid) values(?,?,?,?,?,?,?)";
		$uid = md5(uniqid(rand()));
		$result = $this->db->query($query, array ($periodTypeId, $name, $description, $startDate, $endDate, $gradeWeight, $uid));
		return $uid;
	}

	function get_periods_of_type($periodTypeId, $sort_mode = 'name_desc') {
		$sort_mode = $this->convert_sortmode($sort_mode);

		$query = "select * from aulawiki_period where periodTypeId=? order by $sort_mode";

		$result = $this->query($query, array ($periodTypeId));
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		return $ret;
	}

	function get_period_by_uid($uid) {
		$query = "select * from aulawiki_period where uid=?";
		$result = $this->db->query($query, array ($uid));
		$res = $result->fetchRow();
		return $res;
	}

	function get_period_by_id($periodId) {
		$query = "select * from aulawiki_period where periodId=?";
		$result = $this->db->query($query, array ($periodId));
		$res = $result->fetchRow();
		return $res;
	}

	function del_period($id) {
		$query = "delete from aulawiki_period where periodId=?";
		$result = $this->db->query($query, array ($uid));
	}

	function update_period($periodId, $periodTypeId, $name, $description, $startDate, $endDate, $gradeWeight) {
		$period = $this->get_period_by_uid($uid);
		$query = "update aulawiki_period set periodTypeId=?,name=?,description=?,startDate=?,endDate=?,gradeWeight=? where periodId=?";
		$result = $this->db->query($query, array ($periodTypeId, $name, $description, $startDate, $endDate, $gradeWeight, $periodId));
		return true;
	}

}
?>