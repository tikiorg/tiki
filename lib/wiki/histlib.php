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
		$page = addslashes($page);

		$query = "delete from tiki_history where pageName='$page' and version='$version'";
		$result = $this->query($query);
		$action = "Removed version $version";
		$t = date("U");
		$query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$page',$t,'admin','" . $_SERVER["REMOTE_ADDR"] . "','$comment')";
		$result = $this->query($query);
		return true;
	}

	function use_version($page, $version, $comment = '') {
		$page = addslashes($page);

		$this->invalidate_cache($page);
		$query = "select * from tiki_history where pageName='$page' and version='$version'";
		$result = $this->query($query);

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		$query
			= "update tiki_pages set data='" . addslashes($res["data"]). "',lastModif=" . $res["lastModif"] . ",user='" . $res["user"] . "',comment='" . $res["comment"] . "',version=version+1,ip='" . $res["ip"] . "' where pageName='$page'";
		$result = $this->query($query);
		$query = "delete from tiki_links where fromPage = '$page'";
		$result = $this->query($query);
		$this->clear_links($page);
		$pages = $this->get_pages($res["data"]);

		foreach ($pages as $a_page) {
			$this->replace_link($page, $a_page);
		}

		//$query="delete from tiki_history where pageName='$page' and version='$version'";
		//$result=$this->query($query);
		//
		$action = "Changed actual version to $version";
		$t = date("U");
		$query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$page',$t,'admin','" . $_SERVER["REMOTE_ADDR"] . "','$comment')";
		$result = $this->query($query);
		return true;
	}

	function get_user_versions($user) {
		$query
			= "select pageName,version, lastModif, user, ip, comment from tiki_history where user='$user' order by lastModif desc";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
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
		$page = addslashes($page);

		$query = "select * from tiki_history where pageName='$page' and version=$version";
		$result = $this->query($query);
		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		return $res;
	}

	// Returns all the versions for this page
	// without the data itself
	function get_page_history($page) {
		$page = addslashes($page);

		$query = "select pageName, description, version, lastModif, user, ip, data, comment from tiki_history where pageName='$page' order by version desc";
		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
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
		$pageName = addslashes($pageName);

		$query = "select pageName from tiki_history where pageName = '$pageName' and version='$version'";
		$result = $this->query($query);
		return $result->numRows();
	}

	// This function get the last changes from pages from the last $days days
	// if days is 0 this gets all the registers
	// function parameters modified by ramiro_v on 11/03/2002
	function get_last_changes($days, $offset = 0, $limit = -1, $sort_mode = 'lastModif_desc', $findwhat = '') {
		$sort_mode = str_replace("_", " ", $sort_mode);

		// section added by ramiro_v on 11/03/2002 begins here
		if ($findwhat == '') {
			$where = " where 1";
		} else {
			$where = " where pageName like '%" . $findwhat . "%' or user like '%" . $findwhat . "%' or comment like '%" . $findwhat . "%'";
		}
		// section added by ramiro_v on 11/03/2002 ends here
		if ($days) {
			$toTime = mktime(23, 59, 59, date("m"), date("d"), date("Y"));

			$fromTime = $toTime - (24 * 60 * 60 * $days);
			$where = $where . " and lastModif>=$fromTime and lastModif<=$toTime";
		}

		$query = "select action, lastModif, user, ip, pageName,comment from tiki_actionlog " . $where . " order by $sort_mode limit $offset,$limit";
		$query_cant = "select count(*) from tiki_actionlog " . $where;
		$result = $this->query($query);
		$cant = $this->getOne($query_cant);
		$ret = array();
		$r = array();

		while ($res = $result->fetchRow(DB_FETCHMODE_ASSOC)) {
			$r["action"] = $res["action"];

			$r["lastModif"] = $res["lastModif"];
			$r["user"] = $res["user"];
			$r["ip"] = $res["ip"];
			$r["pageName"] = $res["pageName"];
			$r["comment"] = $res["comment"];
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