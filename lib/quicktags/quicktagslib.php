<?php

class QuickTagsLib extends TikiLib {
	function QuickTagsLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to AdminLib constructor");
		}
		$this->db = $db;
	}

	function list_quicktags($offset, $maxRecords, $sort_mode, $find) {
		
		$bindvars=array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`tagname` like ?)";
			$bindvars[]=$findesc;
		} else {
			$mid = "";
		}
		$query = "select * from `tiki_quicktags` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_quicktags` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$res['iconpath'] = $res['tagicon'];
			if (!is_file(TIKI_PATH.'/'.$res['tagicon'])) $res['tagicon'] = 'images/ed_html.gif';
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function replace_quicktag($tagId, $taglabel, $taginsert, $tagicon) {
		if ($tagId) {
			$bindvars=array($taglabel, $taginsert, $tagicon, $tagId);
			$query = "update `tiki_quicktags` set `taglabel`=?,`taginsert`=?,`tagicon`=? where `tagId`=?";
			$result = $this->query($query,$bindvars);
		} else {
			$bindvars=array($taglabel, $taginsert, $tagicon);
			$query = "delete from `tiki_quicktags` where `taglabel`=? and `taginsert`=? and `tagicon`=?";
			$result = $this->query($query,$bindvars);
			$query = "insert into `tiki_quicktags`(`taglabel`,`taginsert`,`tagicon`) values(?,?,?)";
			$result = $this->query($query,$bindvars);
		}
		return true;
	}

	function remove_quicktag($tagId) {
		$query = "delete from `tiki_quicktags` where `tagId`=?";
		$this->query($query,array($tagId));
		return true;
	}

	function get_quicktag($tagId) {
		$query = "select * from `tiki_quicktags` where `tagId`=?";
		$result = $this->query($query,array($tagId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

}

$quicktagslib = new QuickTagsLib($dbTiki);
?>
