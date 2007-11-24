<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class HistLib extends TikiLib {
	function HistLib($db) {
		$this->TikiLib($db);
	}

	// Removes a specific version of a page
	function remove_version($page, $version, $comment = '', $historyId = '') {
		global $prefs;
		if ($prefs['feature_contribution'] == 'y') {
			global $contributionlib; include_once('lib/contribution/contributionlib.php');
			if ($historyId == '') {
				$query = 'select `historyId` from `tiki_history` where `pageName`=? and `version`=?';
				$historyId = $this->getOne($query, array($page, $version));
			}
			$contributionlib->remove_history($historyId);
		}
		$query = "delete from `tiki_history` where `pageName`=? and `version`=?";
		$result = $this->query($query,array($page,$version));
		global $logslib; include_once('lib/logs/logslib.php');
		$logslib->add_action("Removed version", $page, 'wiki page', "version=$version");
		//get_strings tra("Removed version $version")
		return true;
	}

	function use_version($page, $version, $comment = '') {
		$this->invalidate_cache($page);
		
		// Store the current page in tiki_history before rolling back
		if (strtolower($page) != 'sandbox') {
			$info = $this->get_page_info($page);
			$old_version = $this->get_page_latest_version($page) + 1;
		    $lastModif = $info["lastModif"];
		    $user = $info["user"];
		    $ip = $info["ip"];
		    $comment = $info["comment"];
		    $data = $info["data"];
		    $description = $info["description"];
			$query = "insert into `tiki_history`(`pageName`, `version`, `lastModif`, `user`, `ip`, `comment`, `data`, `description`)
		    			values(?,?,?,?,?,?,?,?)";
		    $this->query($query,array($page,(int) $old_version,(int) $lastModif,$user,$ip,$comment,$data,$description));
		}
		
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
		global $prefs;
		if ($prefs['feature_actionlog'] == 'y') {
			global $logslib; include_once('lib/logs/logslib.php');
			$logslib->add_action("Rollback", $page, 'wiki page', "version=$version");
		}
		//get_strings tra("Changed actual version to $version");
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
	function get_page_history($page, $fetchdata=true) {
		global $prefs;

		$query = "select * from `tiki_history` where `pageName`=? order by `version` desc";
		$result = $this->query($query,array($page));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();

			$aux["version"] = $res["version"];
			$aux["lastModif"] = $res["lastModif"];
			$aux["user"] = $res["user"];
			$aux["ip"] = $res["ip"];
			if ($fetchdata==true) $aux["data"] = $res["data"];
			$aux["pageName"] = $res["pageName"];
			$aux["description"] = $res["description"];
			$aux["comment"] = $res["comment"];
			//$aux["percent"] = levenshtein($res["data"],$actual);
			if ($prefs['feature_contribution'] == 'y') {
				global $contributionlib; include_once('lib/contribution/contributionlib.php');
				$aux['contributions'] = $contributionlib->get_assigned_contributions($res['historyId'], 'history');
				global $logslib; include_once('lib/logs/logslib.php');
				$aux['contributors'] = $logslib->get_wiki_contributors($aux);
			}
			$ret[] = $aux;
		}

		return $ret;
	}

	// Returns one version of the page from the history
	// without the data itself (version = 0 now returns data from current version)
	function get_page_from_history($page,$version,$fetchdata=false) {

		if ($fetchdata==true) {
			if ($version > 0)
				$query = "select `pageName`, `description`, `version`, `lastModif`, `user`, `ip`, `data`, `comment` from `tiki_history` where `pageName`=? and `version`=?";				
			else
				$query = "select `pageName`, `description`, `version`, `lastModif`, `user`, `ip`, `data`, `comment` from `tiki_pages` where `pageName`=?";
		} else {
			if ($version > 0)
				$query = "select `pageName`, `description`, `version`, `lastModif`, `user`, `ip`, `comment` from `tiki_history` where `pageName`=? and `version`=?";
			else
				$query = "select `pageName`, `description`, `version`, `lastModif`, `user`, `ip`, `comment` from `tiki_pages` where `pageName`=?";
		}
		if ($version > 0)
			$result = $this->query($query,array($page,$version));
		else
			$result = $this->query($query,array($page));
			
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();

			$aux["version"] = $res["version"];
			$aux["lastModif"] = $res["lastModif"];
			$aux["user"] = $res["user"];
			$aux["ip"] = $res["ip"];
			if ($fetchdata==true) $aux["data"] = $res["data"];
			$aux["pageName"] = $res["pageName"];
			$aux["description"] = $res["description"];
			$aux["comment"] = $res["comment"];
			//$aux["percent"] = levenshtein($res["data"],$actual);
			$ret[] = $aux;
		}

		return $ret[0];
	}
	
	// note that this function returns the latest version in the
	// history db table, which is one less than the current version 
	function get_page_latest_version($page) {

		$query = "select `version` from `tiki_history` where `pageName`=? order by `version` desc";
		$result = $this->query($query,array($page),1);
		$ret = array();
		
		if ($res = $result->fetchRow()) {
			$ret = $res['version'];
		} else {
			$ret = FALSE;
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
	        global $user;

		$where = "where (th.`version` != 0 or tp.`version` != 0) ";
		$bindvars = array();
		if ($findwhat) {
			$findstr='%' . $findwhat . '%';
			$where.= " and ta.`object` like ? or ta.`user` like ? or ta.`comment` like ?";
			$bindvars = array($findstr,$findstr,$findstr);
		}

		if ($days) {
			$toTime = $this->make_time(23, 59, 59, $this->date_format("%m"), $this->date_format("%d"), $this->date_format("%Y"));
			$fromTime = $toTime - (24 * 60 * 60 * $days);
			$where .= " and ta.`lastModif`>=? and ta.`lastModif`<=? ";
			$bindvars[] = $fromTime;
			$bindvars[] = $toTime;
		}

		$query = "select ta.`action`, ta.`lastModif`, ta.`user`, ta.`ip`, ta.`object`,th.`comment`, th.`version` as version, tp.`version` as versionlast from `tiki_actionlog` ta 
			left join `tiki_history` th on  ta.`object`=th.`pageName` and ta.`lastModif`=th.`lastModif` and ta.`objectType`='wiki page'
			left join `tiki_pages` tp on ta.`object`=tp.`pageName` and ta.`lastModif`=tp.`lastModif` " . $where . " order by ta.".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_actionlog` ta 
			left join `tiki_history` th on  ta.`object`=th.`pageName` and ta.`lastModif`=th.`lastModif` 
			left join `tiki_pages` tp on ta.`object`=tp.`pageName` and ta.`lastModif`=tp.`lastModif` " . $where;

		$result = $this->query($query,$bindvars,$limit,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		$retval = array();
		while ($res = $result->fetchRow()) {
		   //WYSIWYCA hack: the $limit will not be respected
		   if($this->user_has_perm_on_object($user,$res['object'],'wiki page','tiki_p_view')) {
			$res['pageName'] = $res['object'];
			$ret[] = $res;
		   }
		}
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}
	function get_nb_history($page) {
		$query_cant = "select count(*) from `tiki_history` where `pageName` = ?";
		$cant = $this->getOne($query_cant, array($page));
		return $cant;
	}
}

global $dbTiki;
$histlib = new HistLib($dbTiki);

?>
