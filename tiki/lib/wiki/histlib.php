<?php

class HistLib extends TikiLib {
	function HistLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to HistLib constructor");
		}

		$this->db = $db;
	}

	// Removes a specific version of a page
	function remove_version($page, $version, $comment = '') {
		$query = "delete from `tiki_history` where `pageName`=? and `version`=?";
		$result = $this->query($query,array($page,$version));
		$action = "Removed version $version";
		$t = date("U");
		$query = "insert into `tiki_actionlog`(`action`,`pageName`,`lastModif`,`user`,`ip`,`comment`) values(?,?,?,?,?,?)";
		$result = $this->query($query,array($action,$page,$t,"admin",$_SERVER["REMOTE_ADDR"],$comment));
		return true;
	}

	function use_version($page, $version, $comment = '') {
		$this->invalidate_cache($page);
		$query = "select * from `tiki_history` where `pageName`=? and `version`=?";
		$result = $this->query($query,array($page,$version));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		$query
			= "update `tiki_pages` set `data`=?,`lastModif`=?,`user`=?,`comment`=?,`version`=`version`+1,`ip`=? where `pageName`=?";
		$result = $this->query($query,array($res["data"],$res["lastModif"],$res["user"],$res["comment"],$res["ip"],$page));
		$query = "delete from `tiki_links` where `fromPage` = ?";
		$result = $this->query($query,array($page));
		$this->clear_links($page);
		$pages = $this->get_pages($res["data"]);

		foreach ($pages as $a_page) {
			$this->replace_link($page, $a_page);
		}

		//$query="delete from `tiki_history` where `pageName`='$page' and version='$version'";
		//$result=$this->query($query);
		//
		$action = "Changed actual version to $version";
		$t = date("U");
		$query = "insert into `tiki_actionlog`(`action`,`pageName`,`lastModif`,`user`,`ip`,`comment`) values(?,?,?,?,?,?)";
		$result = $this->query($query,array($action,$page,$t,'admin',$_SERVER["REMOTE_ADDR"],$comment));
		return true;
	}

	function get_user_versions($user) {
		$query
			= "select `pageName`,`version`, `lastModif`, `user`, `ip`, `comment` from `tiki_history` where `user`=? order by `lastModif` desc";

		$result = $this->query($query,array($user));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();

			$aux["pageName"] = $res["pageName"];
			$aux["version"] = $res["version"];
			$aux["lastModif"] = $res["lastModif"];
			$aux["ip"] = $res["ip"];
			$aux["comment"] = $res["comment"];
			$ret[] = $aux;
		}

		return $ret;
	}

	// Returns information about a specific version of a page
	function get_version($page, $version) {

		$query = "select * from `tiki_history` where `pageName`=? and `version`=?";
		$result = $this->query($query,array($page,$version));
		$res = $result->fetchRow();
		return $res;
	}

	// Returns all the versions for this page
	// without the data itself
	function get_page_history($page) {

		$query = "select `pageName`, `description`, `version`, `lastModif`, `user`, `ip`, `data`, `comment` from `tiki_history` where `pageName`=? order by `version` desc";
		$result = $this->query($query,array($page));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();

			$aux["version"] = $res["version"];
			$aux["lastModif"] = $res["lastModif"];
			$aux["user"] = $res["user"];
			$aux["ip"] = $res["ip"];
			$aux["data"] = $res["data"];
			$aux["pageName"] = $res["pageName"];
			$aux["description"] = $res["description"];
			$aux["comment"] = $res["comment"];
			//$aux["percent"] = levenshtein($res["data"],$actual);
			$ret[] = $aux;
		}

		return $ret;
	}

	function version_exists($pageName, $version) {

		$query = "select `pageName` from `tiki_history` where `pageName` = ? and `version`=?";
		$result = $this->query($query,array($pageName,$version));
		return $result->numRows();
	}

	// This function get the last changes from pages from the last $days days
	// if days is 0 this gets all the registers
	// function parameters modified by ramiro_v on 11/03/2002
	function get_last_changes($days, $offset = 0, $limit = -1, $sort_mode = 'lastModif_desc', $findwhat = '') {

		// section added by ramiro_v on 11/03/2002 begins here
		if ($findwhat == '') {
			$bindvars=array();
		} else {
			$findstr='%" . $findwhat . "%';
			$where = " where ta.`pageName` like ? or
			ta.`user` like ? or ta.`comment` like ? ";
			$bindvars=array($findstr,$findstr,$findstr);
		}
		// section added by ramiro_v on 11/03/2002 ends here
		if ($days) {
			$toTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));

			$fromTime = $toTime - (24 * 60 * 60 * $days);
			if (!isset($where)) {
				$where="where ";
			} else {
				$where.="and ";
			}
			$where .= " ta.`lastModif`>=? and ta.`lastModif`<=? ";
			$bindvars[]=$fromTime;
			$bindvars[]=$toTime;
		}

			if (!isset($where)) {
				$where="where
				ta.`lastModif` =
				th.`lastModif`";
			} else {
				$where.="and
				ta.lastModif =
				th.`lastModif`";
			}

		$query = "select ta.`action`, ta.`lastModif`, ta.`user`,
		ta.`ip`, ta.`pageName`,ta.`comment`, `version` from
		`tiki_actionlog` ta, `tiki_history` th " .
		$where . " order by
		".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_actionlog`
		ta, `tiki_history` th " . $where;
		$result = $this->query($query,$bindvars,$limit,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		$r = array();

		while ($res = $result->fetchRow()) {
			$r["action"] = $res["action"];

			$r["lastModif"] = $res["lastModif"];
			$r["user"] = $res["user"];
			$r["ip"] = $res["ip"];
			$r["pageName"] = $res["pageName"];
			$r["comment"] = $res["comment"];
			$r["version"] = $res["version"];
			$ret[] = $r;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}
}

$histlib = new HistLib($dbTiki);

?>
