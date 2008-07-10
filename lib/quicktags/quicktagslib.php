<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class QuickTagsLib extends TikiLib {
	function QuickTagsLib($db) {
		$this->TikiLib($db);
	}

	function list_quicktags($offset, $maxRecords, $sort_mode, $find, $category=null) {
		
		$bindvars=array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`taglabel` like ?)";
			$bindvars[]=$findesc;
		} else {
			$mid = "";
		}
		if ($category) {
			if ($mid) {
				$mid .= " and (`tagcategory` like ?)";
			} else {
			   $mid = " where (`tagcategory` like ?)";
			}
			$bindvars[]=$category;
	        }

		$query = "select * from `tiki_quicktags` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_quicktags` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$res['iconpath'] = $res['tagicon'];
			if (!is_file($res['tagicon'])) 
                            $res['tagicon'] = 'pics/icons/page_white_code.png';
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function replace_quicktag($tagId, $taglabel, $taginsert, $tagicon, $tagcategory) {
		if ($tagId) {
			$bindvars=array($taglabel, $taginsert, $tagicon, $tagcategory, $tagId);
			$query = "update `tiki_quicktags` set `taglabel`=?,`taginsert`=?,`tagicon`=?,`tagcategory`=? where `tagId`=?";
			$result = $this->query($query,$bindvars);
		} else {
			$bindvars=array($taglabel, $taginsert, $tagicon, $tagcategory);
			$query = "delete from `tiki_quicktags` where `taglabel`=? and `taginsert`=? and `tagicon`=? and `tagcategory`=? ";
			$result = $this->query($query,$bindvars);
			$query = "insert into `tiki_quicktags`(`taglabel`,`taginsert`,`tagicon`,`tagcategory`) values(?,?,?,?)";
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

	function list_icons($p) {
          $back = array();
		foreach($p as $path) {
			$handle = opendir($path);
			while ($file = readdir($handle)) {
				if (((strtolower(substr($file, -4, 4)) == ".gif") 
                                      or (strtolower(substr($file, -4, 4)) == ".png")) 
                                  and (ereg("^[-_a-zA-Z0-9\.]*$", $file))) 
                                {
				  $back[] = $path .'/'  .$file;
				}
			}
		}
          return $back;
	}

}
global $dbTiki;
$quicktagslib = new QuickTagsLib($dbTiki);
?>
