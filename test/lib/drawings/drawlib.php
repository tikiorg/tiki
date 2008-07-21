<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class DrawLib extends TikiLib {
	function DrawLib($db) {
		$this->TikiLib($db);
	}

	function replace_drawing($drawId, $name, $filename_draw, $filename_pad, $user, $version) {
		if ($drawId) {
			$query = "update `tiki_drawings` set `name`=?, `filename_draw`=?, `filename_pad`=?, `timestamp`=?, `version`=?, `user`='?  where `drawId`=?";
			$this->query($query,array($name,$filename_draw,$filename_pad,(int)$this->now,(int)$version,$user,(int)$drawId));
		} else {
			$query = "insert into `tiki_drawings`(`name`,`filename_draw`,`filename_pad`,`timestamp`,`version`,`user`) values(?,?,?,?,?,?)";
			$this->query($query,array($name,$filename_draw,$filename_pad,(int)$this->now,(int)$version,$user));
		}

		return true;
	}

	function update_drawing($name, $hash, $user) {
		global $prefs;
		$version = $this->getOne("select max(`version`) from `tiki_drawings` where `name`=?",array($name));
		if (!$version) $version = 0;
		$version = $version + 1;
		$this->replace_drawing(0, $name, '', $hash, $user, $version);
		$maxversions = $prefs['maxVersions'];
		$keep = $prefs['keep_versions'];
		$cant = $this->getOne("select count(*) from `tiki_drawings` where `name`=?",array($name));
		$oktodel = $this->now - ($keep * 24 * 3600);

		if ($cant > $maxversions) {
			$query = "select * from `tiki_drawings` where `name`=? and `timestamp` <= ? ";
			$result = $this->query($query,array($name,(int)$oktodel),-1,$maxversions);
			while ($res = $result->fetchRow()) {
				$query = "delete from `tiki_drawings` where `drawId`=?";
				$this->query($query,array($res['drawId']));
			}
		}
	}

	function set_drawing_gif($name, $hash) {
		$id = $this->getOne("select max(`drawId`) from `tiki_drawings` where `name`=?",array($name));
		if ($id) {
			$query = "update `tiki_drawings` set `filename_draw`=? where `drawId`=?";
			$this->query($query,array($hash,(int)$id));
		}
	}

	function get_drawing($drawId) {
		$query = "select * from `tiki_drawings` where `drawId`=?";
		$result = $this->query($query,array((int)$drawId));
		$res = $result->fetchRow();
		return $res;
	}

	function remove_drawing($drawId) {
		global $tikidomain;
		$path = "img/wiki";
		if ($tikidomain) {
			$path.= "/$tikidomain";
		}
		$info = $this->get_drawing($drawId);
		$f1 = "$path/" . $info['filename_draw'];
		$f2 = "$path/" . $info['filename_pad'];
		$max = $this->getOne("select count(*) from `tiki_drawings` where `name`=?",array($info['name']));
		@unlink ($f1);
		@unlink ($f2);
		if ($max == 1) {
			$f1 = "$path/$name.pad_xml";
			unlink ($f1);
			$f1 = "$path/$name.gif";
			unlink ($f1);
		}

		$query = "delete from `tiki_drawings` where `drawId`=?";
		$this->query($query,array((int)$drawId));
		$max = $this->getOne("select max(`version`) from `tiki_drawings` where `name`=?",array($info['name']));
		$query = "select * from `tiki_drawings` where `name`=? and `version`=?";
		$result = $this->query($query,array($info["name"],(int)$max));
		$res = $result->fetchRow();
		$f1 = "$path/" . $res['filename_draw'];
		$f2 = "$path/" . $res['name'] . '.gif';
		copy($f1, $f2);
		$f1 = "$path/" . $res['filename_pad'];
		$f2 = "$path/" . $res['name'] . '.pad_xml';
		copy($f1, $f2);
	}

	function remove_all_drawings($name) {
		$query = "delete from `tiki_drawings` where `name`=?";
		$this->query($query,array($name));
	}

	function list_drawings($offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array();
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " where  (`name` like ?)";
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}
		$query = "select * from `tiki_drawings` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_drawings` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$last_version = $this->getOne("select max(`version`) from `tiki_drawings` where `name`=?",array($res['name']));
			if ($res['version'] == $last_version) {
				$res['versions'] = $this->getOne("select count(*) from `tiki_drawings` where `name`=?",array($res['name']));
				$ret[] = $res;
			}
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function list_drawing_history($name, $offset, $maxRecords, $sort_mode, $find) {
		$bindvars = array($name);
		$mid = " where `name`=? ";
		if ($find) {
			$mid.= " and (`name` like ?)";
			$bindvars[] = '%'.$find.'%';
		}
		$query = "select * from `tiki_drawings` $mid order by ".$this->convert_sortmode($sort_mode).",".$this->convert_sortmode("version_desc");
		$query_cant = "select count(*) from `tiki_drawings` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$res['versions'] = $this->getOne("select count(*) from `tiki_drawings` where `name`=?",array($res['name']));
			$ret[] = $res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}
}
global $dbTiki;
$drawlib = new DrawLib($dbTiki);

?>
