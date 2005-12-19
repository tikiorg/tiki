<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class CommLib extends TikiLib {
	function CommLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to CommLib constructor");
		}
		$this->db = $db;
	}

	function accept_page($receivedPageId) {
		$info = $this->get_received_page($receivedPageId);

		if ($this->page_exists($info["pageName"]))
			return false;

		$now = date("U");
		$this->create_page($info["pageName"], 0, $info["data"], $now, $info["comment"], $info["receivedFromUser"], $info["receivedFromSite"], $info["description"]);
		$query = "delete from `tiki_received_pages` where `receivedPageId`=?";
		$result = $this->query($query,array((int)$receivedPageId));
		return true;
	}

	function accept_article($receivedArticleId, $topic) {
		$info = $this->get_received_article($receivedArticleId);

		$this->replace_article($info["title"], $info["authorName"],
			$topic, $info["useImage"], $info["image_name"], $info["image_size"], $info["image_type"], $info["image_data"],
			$info["heading"], $info["body"], $info["publishDate"], $info["expireDate"], $info["author"],
			0, $info["image_x"], $info["image_y"], $info["type"], $info["rating"]);
		$query = "delete from `tiki_received_articles` where `receivedArticleId`=?";
		$result = $this->query($query,array((int)$receivedArticleId));
		return true;
	}

	function list_received_articles($offset, $maxRecords, $sort_mode = 'publishDate_desc', $find) {
		$bindvars = array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`heading` like ? or `title` like ? or `body` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_received_articles` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_received_articles` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function remove_received_page($receivedPageId) {
		$query = "delete from `tiki_received_pages` where `receivedPageId`=?";
		$result = $this->query($query,array((int)$receivedPageId));
	}

	function remove_received_article($receivedArticleId) {
		$query = "delete from `tiki_received_articles` where `receivedArticleId`=?";
		$result = $this->query($query,array((int)$receivedArticleId));
	}

	function rename_received_page($receivedPageId, $name) {
		$query = "update `tiki_received_pages` set `pageName`=? where `receivedPageId`=?";
		$result = $this->query($query,array($name,(int)$receivedPageId));
	}

	function get_received_page($receivedPageId) {
		$query = "select * from `tiki_received_pages` where `receivedPageId`=?";
		$result = $this->query($query,array((int)$receivedPageId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function get_received_article($receivedArticleId) {
		$query = "select * from `tiki_received_articles` where `receivedArticleId`=?";
		$result = $this->query($query,array((int)$receivedArticleId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
	}

	function update_received_article($receivedArticleId, $title, $authorName, $useImage, $image_x, $image_y, $publishDate, $expireDate, $heading, $body, $type, $rating) {
		$size = strlen($body);
		$hash = md5($title . $heading . $body);
		$query = "update `tiki_received_articles` set `title`=?, `authorName`=?, `heading`=?, `body`=?, `size`=?, `hash`=?, `useImage`=?, `image_x`=?, ";
		$query.= " `image_y`=?, `publishDate`=?, `expireDate`=?, `type`=?, `rating`=?  where `receivedArticleId`=?";
		$result = $this->query($query,
			array($title,$authorName,$heading,$body,(int)$size,$hash,$useImage,(int)$image_x,(int)$image_y,(int)$publishDate,$expireDate,$type,(int)$rating,(int)$receivedArticleId));
	}

	function update_received_page($receivedPageId, $pageName, $data, $comment) {
		$query = "update `tiki_received_pages` set `pageName`=?, `data`=?, `comment`=? where `receivedPageId`=?";
		$result = $this->query($query,array($pageName,$data,$comment,(int)$receivedPageId));
	}

	function receive_article($site, $user, $title, $authorName, $size, $use_image, $image_name, $image_type, $image_size, $image_x,
		$image_y, $image_data, $publishDate, $expireDate, $created, $heading, $body, $hash, $author, $type, $rating) {
		$now = date("U");
		$query = "delete from `tiki_received_articles` where `title`=? and `receivedFromsite`=? and `receivedFromUser`=?";
		$result = $this->query($query,array($title,$site,$user));
		$query = "insert into `tiki_received_articles`(`receivedDate`,`receivedFromSite`,`receivedFromUser`,`title`,`authorName`,`size`, ";
		$query.= " `useImage`,`image_name`,`image_type`,`image_size`,`image_x`,`image_y`,`image_data`,`publishDate`,`expireDate`,`created`,`heading`,`body`,`hash`,`author`,`type`,`rating`) ";
    $query.= " values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
		$result = $this->query($query,array((int)$now,$site,$user,$title,$authorName,(int)$size,$use_image,$image_name,$image_type,$image_size,
		                              $image_x,$image_y,$image_data,(int)$publishDate,(int)$expireDate,(int)$created,$heading,$body,$hash,$author,$type,(int)$rating));
	}

	function receive_page($pageName, $data, $comment, $site, $user, $description) {
		$now = date("U");
		// Remove previous page sent from the same site-user (an update)
		$query = "delete from `tiki_received_pages` where `pageName`=? and `receivedFromsite`=? and `receivedFromUser`=?";
		$result = $this->query($query,array($pageName,$site,$user));
		// Now insert the page
		$query = "insert into `tiki_received_pages`(`pageName`,`data`,`comment`,`receivedFromSite`, `receivedFromUser`, `receivedDate`,`description`) values(?,?,?,?,?,?,?)";
		$result = $this->query($query,array($pageName,$data,$comment,$site,$user,(int)$now,$description));
	}

// Functions for the communication center end ////
}
global $dbTiki;
$commlib = new CommLib($dbTiki);

?>
