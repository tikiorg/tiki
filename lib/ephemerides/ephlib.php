<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class EphLib extends TikiLib {
	function EphLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to EphLib constructor");
		}

		$this->db = $db;
	}

	function get_eph($ephId) {
		if (! $ephId) {
			return;
		}

		$query = "select * from `tiki_eph` where `ephId` = ?";

		$result = $this->query($query, array($ephId));
		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		return $res;
	}

	function replace_eph($ephId, $title, $filename, $filetype, $filesize, $data, $date, $textdata) {
		$now = date("U");

		if ($ephId) {
			if ($data) {
				$query = "update `tiki_eph` set
					`title` = ?,
					`filename` = ?,
					`filetype` = ?,
					`filesize` = ?,
					`data` = ?,
					`publish` = ?,
					`textdata` = ?
						where `ephId` = ?";

				$this->query($query, array($title, $filename,
							$filetype, $filesize,
							$data, $date,
							$textdata, $ephId));
			} else {
				$query
					= "update `tiki_eph` set
						`title` = ?,
						`publish` = ?,
						`textdata` = ?
							where `ephId` = ?";

				$this->query($query, array($title, $date,
							$textdata, $ephId));
			}
		} else {
			$query = "insert into `tiki_eph`(`title`, `filename`,
				`filetype`, `filesize`, `data`, `hits`,
				`publish`, `textdata`)
					values(?, ?, ?, ?, ?, ?, ?, ?)";

			$this->query($query, array($title, $filename, $filetype,
						$filesize, $data, 0, $date,
						$textdata));
		}
	}

	function remove_eph($ephId) {
		$query = "delete from `tiki_eph` where `ephId` = ?";

		$this->query($query, array($ephId));
	}

	function list_eph($offset, $maxRecords, $sort_mode, $find, $date = 0) {
		$sort_mode = $this->convert_sortmode($sort_mode);

		$bindvars = array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`filename` like ? or `title` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}

		if ($date) {
			if ($mid) {
				$mid .= " and `publish` = ? ";
			} else {
				$mid = " where `publish` = ? ";
			}
			$bindvars[] = $date;
		}

		$query = "select `ephId`, `textdata`, `title`, `filename`,
				`filetype`, `filesize`, `publish`, `hits`
					from `tiki_eph` $mid
					order by $sort_mode";
		$query_cant = "select count(*) from `tiki_eph` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);

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
$ephlib = new EphLib($dbTiki);

?>
