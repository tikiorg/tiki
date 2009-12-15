<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/* Task properties:
   user, taskId, title, description, date, status, priority, completed, percentage
*/
class NotepadLib extends TikiLib {
	function NotepadLib($db) {
		$this->TikiLib($db);
	}

	function get_note($user, $noteId) {
		$query = "select * from `tiki_user_notes` where `user`=? and `noteId`=?";
		$result = $this->query($query,array($user,(int)$noteId));
		$res = $result->fetchRow();
		return $res;
	}

	function set_note_parsing($user, $noteId, $mode) {
		$query = "update `tiki_user_notes` set `parse_mode`=? where `user`=? and `noteId`=?";
		$this->query($query, array($mode,$user,(int)$noteId));
		return true;
	}

	function remove_note($user, $noteId) {
		$query = "delete from `tiki_user_notes` where `user`=? and `noteId`=?";
		$this->query($query, array($user,(int)$noteId));
	}

	function list_notes($user, $offset, $maxRecords, $sort_mode, $find) {

		$bindvars = array($user);
		if ($find) {
			$findesc = '%'.$find.'%';
			$mid = " and (`name` like ? or `data` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_user_notes` where `user`=? $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_user_notes` where `user`=? $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res['size'] = strlen($res['data']);

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}
}
global $dbTiki;
$notepadlib = new NotepadLib($dbTiki);

?>
