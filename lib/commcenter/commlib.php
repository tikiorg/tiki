<?php

class CommLib extends TikiLib {
	function CommLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to CommLib constructor");
		}

		$this->db = $db;
	}

	function accept_page($receivedPageId) {
		//create_page($name, $hits, $data, $lastModif, $comment, $user='system', $ip='0.0.0.0')
		// CODE HERE
		$info = $this->get_received_page($receivedPageId);

		if ($this->page_exists($info["pageName"]))
			return false;

		$now = date("U");
		$this->create_page($info["pageName"],
			0, $info["data"], $now, $info["comment"], $info["receivedFromUser"], $info["receivedFromSite"], $info["description"]);
		$query = "delete from tiki_received_pages where receivedPageId = $receivedPageId";
		$result = $this->query($query);
		return true;
	}

	function accept_article($receivedArticleId, $topic) {
		$info = $this->get_received_article($receivedArticleId);

		$this->replace_article($info["title"], $info["authorName"],
			$topic, $info["useImage"], $info["image_name"], $info["image_size"], $info["image_type"], $info["image_data"],
			$info["heading"], $info["body"], $info["publishDate"], $info["author"],
			0, $info["image_x"], $info["image_y"], $info["type"], $info["rating"]);
		$query = "delete from tiki_received_articles where receivedArticleId = $receivedArticleId";
		$result = $this->query($query);
		return true;
	}

	function list_received_articles($offset, $maxRecords, $sort_mode = 'publishDate_desc', $find) {
		$sort_mode = str_replace("_", " ", $sort_mode);

		if ($find) {
			$findesc = $this->qstr('%' . $find . '%');

			$findesc = $this->qstr('%' . $find . '%');
			$mid = " where (heading like $findesc or title like $findesc or body like $findesc)";
		} else {
			$mid = "";
		}

		$query = "select * from tiki_received_articles $mid order by $sort_mode limit $offset,$maxRecords";
		$query_cant = "select count(*) from tiki_received_articles $mid";
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

	function remove_received_page($receivedPageId) {
		$query = "delete from tiki_received_pages where receivedPageId=$receivedPageId";

		$result = $this->query($query);
	}

	function remove_received_article($receivedArticleId) {
		$query = "delete from tiki_received_articles where receivedArticleId=$receivedArticleId";

		$result = $this->query($query);
	}

	function rename_received_page($receivedPageId, $name) {
		$query = "update tiki_received_pages set pageName='$name' where receivedPageId=$receivedPageId";

		$result = $this->query($query);
	}

	function get_received_page($receivedPageId) {
		$query = "select * from tiki_received_pages where receivedPageId=$receivedPageId";

		$result = $this->query($query);

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		return $res;
	}

	function get_received_article($receivedArticleId) {
		$query = "select * from tiki_received_articles where receivedArticleId=$receivedArticleId";

		$result = $this->query($query);

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow(DB_FETCHMODE_ASSOC);
		return $res;
	}

	function update_received_article($receivedArticleId, $title, $authorName, $useImage, $image_x, $image_y, $publishDate, $heading,
		$body, $type, $rating) {
		$title = addslashes($title);

		$authorName = addslashes($authorName);
		$heading = addslashes($heading);
		$body = addslashes($body);
		$size = strlen($body);
		$hash = md5($title . $heading . $body);
		$query = "update tiki_received_articles set
      title = '$title',
      authorName = '$authorName',
      heading = '$heading',
      body = '$body',
      size = $size,
      hash = '$hash',
      useImage = '$useImage',
      image_x = $image_x,
      image_y = $image_y,
      publishDate = $publishDate,
      type = '$type',
      rating = $rating
      where receivedArticleId=$receivedArticleId";
		$result = $this->query($query);
	}

	function update_received_page($receivedPageId, $pageName, $data, $comment) {
		$data = addslashes($data);

		$pageName = addslashes($pageName);
		$comment = addslashes($comment);
		$query = "update tiki_received_pages set pageName='$pageName', data='$data', comment='$comment' where receivedPageId=$receivedPageId";
		$result = $this->query($query);
	}

	function receive_article($site, $user, $title, $authorName, $size, $use_image, $image_name, $image_type, $image_size, $image_x,
		$image_y, $image_data, $publishDate, $created, $heading, $body, $hash, $author, $type, $rating) {
		$title = addslashes($title);

		$authorName = addslashes($authorName);
		$image_data = addslashes($image_data);
		$heading = addslashes($heading);
		$body = addslashes($body);
		$now = date("U");
		$query
			= "delete from tiki_received_articles where title='$title' and receivedFromsite='$site' and receivedFromUser='$user'";
		$result = $this->query($query);
		$query = "insert into tiki_received_articles(receivedDate,receivedFromSite,receivedFromUser,title,authorName,size,useImage,image_name,image_type,image_size,image_x,image_y,image_data,publishDate,created,heading,body,hash,author,type,rating)
    values($now,'$site','$user','$title','$authorName',$size,'$use_image','$image_name','$image_type',$image_size,$image_x,$image_y,'$image_data',$publishDate,$created,'$heading','$body','$hash','$author','$type',$rating)";
		$result = $this->query($query);
	}

	function receive_page($pageName, $data, $comment, $site, $user, $description) {
		$data = addslashes($data);

		$pageNAme = addslashes($pageName);
		$comment = addslashes($comment);
		$description = addslashes($description);
		$now = date("U");
		// Remove previous page sent from the same site-user (an update)
		$query = "delete from tiki_received_pages where pageName='$pageName' and receivedFromsite='$site' and receivedFromUser='$user'";
		$result = $this->query($query);
		// Now insert the page
		$query = "insert into tiki_received_pages(pageName,data,comment,receivedFromSite, receivedFromUser, receivedDate,description)
              values('$pageName','$data','$comment','$site','$user',$now,'$description')";
		$result = $this->query($query);
	}

// Functions for the communication center end ////
}

$commlib = new CommLib($dbTiki);

?>