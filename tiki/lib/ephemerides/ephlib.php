<?php

class EphLib extends TikiLib {
	function EphLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to EphLib constructor");
		}

		$this->db = $db;
	}

	function get_eph($ephId) {
		$query = "select * from tiki_eph where ephId='$ephId'";

		$result = $this->query($query);
		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		return $res;
	}

	function replace_eph($ephId, $title, $filename, $filetype, $filesize, $data, $date, $textdata) {
		$title = addslashes($title);

		$filename = addslashes($filename);
		$data = addslashes($data);
		$textdata = addslashes($textdata);
		$now = date("U");

		if ($ephId) {
			if ($data) {
				$query = "update tiki_eph set
  		title='$title',
  		filename = '$filename',
  		filetype = '$filetype',
  		filesize = '$filesize',
		data = '$data',  		
		publish = '$date',
		textdata = '$textdata'
		where ephId=$ephId";

				$this->query($query);
			} else {
				$query
					= "update tiki_eph set
  		title='$title',
		publish = '$date',
		textdata = '$textdata'
		where ephId=$ephId";

				$this->query($query);
			}
		} else {
			$query = "insert into tiki_eph(title,filename,filetype,filesize,data,hits,publish,textdata)
    	values('$title','$filename','$filetype','$filesize','$data',0,$date,'$textdata')";

			$this->query($query);
		}
	}

	function remove_eph($ephId) {
		$query = "delete from tiki_eph where ephId=$ephId";

		$this->query($query);
	}

	function list_eph($offset, $maxRecords, $sort_mode, $find, $date = 0) {
		$sort_mode = str_replace("_desc", " desc", $sort_mode);

		$sort_mode = str_replace("_asc", " asc", $sort_mode);

		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');

			$mid = " where (filename like $findesc or title like $findesc)";
		} else {
			$mid = "";
		}

		if ($date) {
			if ($mid) {
				$mid .= " and publish=$date ";
			} else {
				$mid = " where publish=$date ";
			}
		}

		$query = "select ephId,textdata,title,filename,filetype,filesize,publish,hits from tiki_eph $mid order by $sort_mode limit $offset,$maxRecords";
		$query_cant = "select count(*) from tiki_eph $mid";
		$result = $this->query($query);
		$cant = $this->getOne($query_cant);
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

$ephlib = new EphLib($dbTiki);

?>