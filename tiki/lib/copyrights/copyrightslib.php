<?php

class CopyrightsLib extends TikiLib {
	function CopyrightsLib($db) {
		if (!$db) {
			die ("Invalid db object passed to CopyrightsLib constructor");
		}

		$this->db = $db;
	}

	function list_copyrights($page) {
		$query = "select * from tiki_copyrights WHERE page='$page' order by copyright_order ASC";

		$query_cant = "select count(*) from tiki_copyrights WHERE page='$page'";
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

	function top_copyright_order($page) {
		$query = "select MAX(copyright_order) from tiki_copyrights where page like '$page'";

		return $this->getOne($query);
	}

	function unique_copyright($page, $title) {
		$query = "select copyrightID from tiki_copyrights where page = '$page' and title = '$title'";

		return $this->getOne($query);
	}

	function add_copyright($page, $title, $year, $authors, $user) {

		//$unique = $this->unique_copyright($page,$title);

		//if($unique != 0) {
		// security here?
		//$this->edit_copyright($unique,$title,$year,$authors,$user);
		//return;
		//}
		$top = $this->top_copyright_order($page);

		$title = addslashes($title);
		$authors = addslashes($authors);
		$order = $top + 1;
		$query = "insert tiki_copyrights (page, title, year, authors, copyright_order, userName) values ('$page','$title','$year','$authors','$order','$user')";
		$this->query($query);
		return true;
	}

	function edit_copyright($id, $title, $year, $authors, $user) {
		$title = addslashes($title);

		$authors = addslashes($authors);
		$query = "update tiki_copyrights SET year='$year', title='$title', authors='$authors', userName='$user' where copyrightId = '$id'";
		$this->query($query);
		return true;
	}

	function remove_copyright($id) {
		$query = "delete from tiki_copyrights where copyrightId = '$id'";

		$this->query($query);
		return true;
	}

	function up_copyright($id) {
		$query = "update tiki_copyrights set copyright_order=copyright_order-1 where copyrightId = '$id'";

		$result = $this->query($query);
		return true;
	}

	function down_copyright($id) {
		$query = "update tiki_copyrights set copyright_order=copyright_order+1 where copyrightId = '$id'";

		$result = $this->query($query);
		return true;
	}
}

?>