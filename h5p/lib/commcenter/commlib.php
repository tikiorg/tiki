<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class CommLib extends TikiLib
{

	function accept_page($receivedPageId)
	{
		$info = $this->get_received_page($receivedPageId);

		if ($info['structureName'] == $info['pageName']) {
			$tikilib = TikiLib::lib('tiki');
			$structlib = TikiLib::lib('struct');
			$pages = $tikilib->list_received_pages(0, -1, 'pageName_asc', '', 's', $info['structureName']);

			foreach ($pages['data'] as $page) {
				$names[] = $page['pageName'];
			}

			if (empty($names))
				return true;

			$query = "select count(*) from `tiki_pages` where `pageName` in (" . implode(',', array_fill(0, count($names), '?')) .")";

			if ($this->getOne($query, $names))
				return false;

			foreach ($pages['data'] as $key=>$page) {
				$parent_id = null;
				$after_ref_id = 0;
				if ($page['parentName']) {
					foreach ($pages['data'] as $p) {
						if ($p['pageName'] == $page['parentName']) {
							$parent_id = $p['page_ref_id'];
						}
						if ($p['pageName'] == $page['pageName']) {
							break;
						}
						if ($p['parentName'] == $page['parentName']) {
							$after_ref_id = $p['page_ref_id'];
						}
					}
				}

				if ($parent_id)
					$this->create_page(
						$page['pageName'],
						0,
						$page['data'],
						$this->now,
						$page['comment'],
						$page['receivedFromUser'],
						$page['receivedFromSite'],
						$page['description']
					);

				$pages['data'][$key]['page_ref_id'] = $structlib->s_create_page($parent_id, $after_ref_id, $page['pageName'], $page['page_alias']);

				if (!$parent_id)
					$this->update_page(
						$page['pageName'],
						$page['data'],
						$page['comment'],
						$page['receivedFromUser'],
						$page['receivedFromSite'],
						$page['description'],
						true
					);
			}
			$query = "delete from `tiki_received_pages` where `structureName`=?";
			$this->query($query, $info['structureName']);
		} elseif (empty($info['structureName'])) {
			if ($this->page_exists($info["pageName"]))
				return false;
			$this->create_page(
				$info["pageName"],
				0,
				$info["data"],
				$this->now,
				$info["comment"],
				$info["receivedFromUser"],
				$info["receivedFromSite"],
				$info["description"]
			);
			$query = "delete from `tiki_received_pages` where `receivedPageId`=?";
			$this->query($query, array((int)$receivedPageId));
		}

		return true;
	}

	function accept_article($receivedArticleId, $topic)
	{
		$artlib = TikiLib::lib('art');
		$info = $this->get_received_article($receivedArticleId);

		$artlib->replace_article(
			$info["title"],
			$info["authorName"],
			$topic,
			$info["useImage"],
			$info["image_name"],
			$info["image_size"],
			$info["image_type"],
			$info["image_data"],
			$info["heading"],
			$info["body"],
			$info["publishDate"],
			$info["expireDate"],
			$info["author"],
			0,
			$info["image_x"],
			$info["image_y"],
			$info["type"],
			$info["rating"]
		);
		$query = "delete from `tiki_received_articles` where `receivedArticleId`=?";
		$result = $this->query($query, array((int)$receivedArticleId));
		return true;
	}

	function list_received_articles($offset, $maxRecords, $sort_mode = 'publishDate_desc', $find = '')
	{
		$bindvars = array();
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where (`heading` like ? or `title` like ? or `body` like ?)";
			$bindvars[] = $findesc;
			$bindvars[] = $findesc;
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_received_articles` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_received_articles` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function remove_received_page($receivedPageId)
	{
		$info = $this->get_received_page($receivedPageId);

		if ($info['structureName'] == $info['pageName']) {
			$query = "delete from `tiki_received_pages` where `structureName`=?";
			$this->query($query, array($info['structureName']));
		} elseif (empty($info['structureName'])) {
			$query = "delete from `tiki_received_pages` where `receivedPageId`=?";
			$this->query($query, array((int)$receivedPageId));
		}
	}

	function remove_received_article($receivedArticleId)
	{
		$query = "delete from `tiki_received_articles` where `receivedArticleId`=?";
		$result = $this->query($query, array((int)$receivedArticleId));
	}

	function get_received_page($receivedPageId)
	{
		$query = "select * from `tiki_received_pages` where `receivedPageId`=?";
		$result = $this->query($query, array((int)$receivedPageId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();

		return $res;
	}

	function get_received_article($receivedArticleId)
	{
		$query = "select * from `tiki_received_articles` where `receivedArticleId`=?";
		$result = $this->query($query, array((int)$receivedArticleId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function update_received_article(
					$receivedArticleId,
					$title,
					$authorName,
					$useImage,
					$image_x,
					$image_y,
					$publishDate,
					$expireDate,
					$heading,
					$body,
					$type,
					$rating
		)
	{
		$size = strlen($body);
		$hash = md5($title . $heading . $body);

		$query = "update `tiki_received_articles` set `title`=?, `authorName`=?, `heading`=?, `body`=?, `size`=?, `hash`=?, `useImage`=?, `image_x`=?, ";
		$query.= " `image_y`=?, `publishDate`=?, `expireDate`=?, `type`=?, `rating`=?  where `receivedArticleId`=?";

		$result = $this->query(
			$query,
			array(
				$title,
				$authorName,
				$heading,
				$body,
				(int)$size,
				$hash,
				$useImage,
				(int)$image_x,
				(int)$image_y,
				(int)$publishDate,
				$expireDate,
				$type,
				(int)$rating,
				(int)$receivedArticleId
			)
		);
	}

	function update_received_page($receivedPageId, $pageName, $data, $comment)
	{
		$info = $this->get_received_page($receivedPageId);
		if ($info['pageName'] != $pageName && !empty($info['structureName'])) {
			if ($info['pageName'] == $info['structureName']) {
				$query = "update `tiki_received_pages` set `structureName`=? where `structureName`=?";
				$this->query($query, array($pageName, $info['pageName']));
			}
			$query = "update `tiki_received_pages` set `parentName`=? where `parentName`=?";
			$this->query($query, array($pageName, $info['pageName']));
		}
		$query = "update `tiki_received_pages` set `pageName`=?, `data`=?, `comment`=? where `receivedPageId`=?";
		$this->query($query, array($pageName, $data, $comment, (int)$receivedPageId));
	}

	function receive_article(
				$site,
				$user,
				$title,
				$authorName,
				$size,
				$use_image,
				$image_name,
				$image_type,
				$image_size,
				$image_x,
				$image_y,
				$image_data,
				$publishDate,
				$expireDate,
				$created,
				$heading,
				$body,
				$hash,
				$author,
				$type,
				$rating
		)
	{
		$query = "delete from `tiki_received_articles` where `title`=? and `receivedFromsite`=? and `receivedFromUser`=?";
		$result = $this->query($query, array($title, $site, $user));

		$query = "insert into `tiki_received_articles`(`receivedDate`,`receivedFromSite`," .
							" `receivedFromUser`,`title`,`authorName`,`size`, `useImage`,`image_name`," .
							" `image_type`,`image_size`,`image_x`,`image_y`,`image_data`,`publishDate`," .
							" `expireDate`,`created`,`heading`,`body`,`hash`,`author`,`type`,`rating`) "
							;
    $query.= " values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

		$result = $this->query(
			$query,
			array(
				(int)$this->now,
				$site,
				$user,
				$title,
				$authorName,
				(int)$size,
				$use_image,
				$image_name,
				$image_type,
				$image_size,
				$image_x,
				$image_y,
				$image_data,
				(int)$publishDate,
				(int)$expireDate,
				(int)$created,
				$heading,
				$body,
				$hash,
				$author,
				$type,
				(int)$rating
			)
		);
	}

	function receive_page($pageName, $data, $comment, $site, $user, $description)
	{
		// Remove previous page sent from the same site-user (an update)
		$query = "delete from `tiki_received_pages` where `pageName`=? and `receivedFromsite`=? and `receivedFromUser`=? and `structureName`=?";
		$result = $this->query($query, array($pageName, $site, $user, ''));

		// Now insert the page
		$query = "insert into `tiki_received_pages`(`pageName`,`data`,`comment`,`receivedFromSite`," .
							" `receivedFromUser`, `receivedDate`,`description`) values(?,?,?,?,?,?,?)";

		$result = $this->query($query, array($pageName, $data, $comment, $site, $user, (int)$this->now, $description));
	}

	function receive_structure_page($pageName, $data, $comment, $site, $user, $description, $structureName, $parentName, $pos, $alias)
	{
		global $tikilib;
		$query = "delete from `tiki_received_pages` where `pageName`=? and `receivedFromsite`=? and `receivedFromUser`=? and `structureName`=?";
		$this->query($query, array($pageName, $site, $user, $structureName));

		$query = "insert into `tiki_received_pages` (`pageName`,`data`,`comment`,`receivedFromSite`," .
							" `receivedFromUser`, `receivedDate`,`description`,`structureName`, `parentName`," .
							" `page_alias`, `pos`) values(?,?,?,?,?,?,?,?,?,?,?)";
		$this->query(
			$query,
			array(
				$pageName,
				$data,
				$comment,
				$site,
				$user,
				(int)$tikilib->now,
				$description,
				$structureName,
				$parentName,
				$alias,
				$pos
			)
		);
	}

	function rename_structure_pages($pages, $prefix, $postfix)
	{
		$bindvars[] = $prefix;
		$bindvars[] = $postfix;
		$bindvars = array_merge($bindvars, $pages);

		$query = 'update `tiki_received_pages` set `pageName`= concat(?,`pageName`,?) where `pageName` in (' .
							implode(',', array_fill(0, count($pages), '?')) . ")";
		$this->query($query, $bindvars);

		$query = 'update `tiki_received_pages` set `parentName`= concat(?,`parentName`,?) where `parentName` in (' .
							implode(',', array_fill(0, count($pages), '?')) . ")";
		$this->query($query, $bindvars);

		$query = 'update `tiki_received_pages` set `structureName`= concat(?,`structureName`,?) where `structureName` in (' .
							implode(',', array_fill(0, count($pages), '?')) . ")";

		$this->query($query, $bindvars);
	}

}
$commlib = new CommLib;
