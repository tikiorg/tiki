<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class AdminLib extends TikiLib
{

	function list_dsn($offset, $maxRecords, $sort_mode, $find) {
		
		$bindvars=array();
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`dsn` like ?)";
			$bindvars[]=$findesc;
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_dsn` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_dsn` $mid";
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

	function replace_dsn($dsnId, $dsn, $name) {
		// Check the name
		if ($dsnId) {
			$query = "update `tiki_dsn` set `name`=?,`dsn`=? where `dsnId`=?";
			$bindvars=array($name,$dsn,$dsnId);
			$result = $this->query($query,$bindvars);
		} else {
			$query = "delete from `tiki_dsn`where `name`=? and `dsn`=?";
			$bindvars=array($name,$dsn);
			$result = $this->query($query,$bindvars);
			$query = "insert into `tiki_dsn`(`name`,`dsn`)
                		values(?,?)";
			$result = $this->query($query,$bindvars);
		}

		return true;
	}

	function remove_dsn($dsnId) {
		$info = $this->get_dsn($dsnId);

		$perm_name = 'tiki_p_dsn_' . $info['name'];
		$query = "delete from `users_permissions` where `permName`=?";
		$this->query($query,array($perm_name));
		$query = "delete from `tiki_dsn` where `dsnId`=?";
		$this->query($query,array($dsnId));
		return true;
	}

	function get_dsn($dsnId) {
		$query = "select * from `tiki_dsn` where `dsnId`=?";

		$result = $this->query($query,array($dsnId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function list_extwiki($offset, $maxRecords, $sort_mode, $find) {
		$bindvars=array();
		if ($find) {
			$findesc = '%' . $find . '%';

			$mid = " where (`extwiki` like ? )";
			$bindvars[]=$findesc;
		} else {
			$mid = "";
		}

		$query = "select * from `tiki_extwiki` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_extwiki` $mid";
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

	function replace_extwiki($extwikiId, $extwiki, $name) {
		// Check the name
		if ($extwikiId) {
			$query = "update `tiki_extwiki` set `extwiki`=?,`name`=? where `extwikiId`=?";
			$result = $this->query($query,array($extwiki,$name,$extwikiId));
		} else {
			$query = "delete from `tiki_extwiki` where `name`=?";
			$result = $this->query($query,array($name));
			$query = "insert into `tiki_extwiki`(`name`,`extwiki`)
                		values(?,?)";
			$result = $this->query($query,array($name, $extwiki));
		}

		// And now replace the perm if not created
		$perm_name = 'tiki_p_extwiki_' . $name;
		$query = "delete from `users_permissions`where `permName`=?";
		$this->query($query,array($perm_name));
		$query = "insert into `users_permissions`(`permName`,`permDesc`,`type`,`level`) values
    			(?,?,?,?)";
		$this->query($query,array($perm_name,'Can use extwiki $extwiki','extwiki','editor'));
		return true;
	}

	function remove_extwiki($extwikiId) {
		$info = $this->get_extwiki($extwikiId);

		$perm_name = 'tiki_p_extwiki_' . $info['name'];
		$query = "delete from `users_permissions` where `permName`=?";
		$this->query($query,array($perm_name));
		$query = "delete from `tiki_extwiki` where `extwikiId`=?";
		$this->query($query,array($extwikiId));
		return true;
	}

	function get_extwiki($extwikiId) {
		$query = "select * from `tiki_extwiki` where `extwikiId`=?";

		$result = $this->query($query,array($extwikiId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function remove_unused_pictures() {
		global $tikidomain;

		$query = "select `data` from `tiki_pages`";
		$result = $this->query($query,array());
		$pictures = array();

		while ($res = $result->fetchRow()) {
			preg_match_all("/\{(picture |img )([^\}]+)\}/ixs", $res['data'], $pics); // to be fixed: pick also the picture into ~np~

			foreach (array_unique($pics[2])as $pic) {
				if (preg_match("/(src|file)=\"([^\"]+)\"/xis", $pic, $matches))
					$pictures[] = $matches[2];
				if (preg_match("/(src|file)=&quot;([^&]+)&quot;/xis", $pic, $matches))
					$pictures[] = $matches[2];
				if (preg_match("/(src|file)=([^&\"\s,]+)/xis", $pic, $matches))
					$pictures[] = $matches[2];
				
			}
		}
		$pictures = array_unique($pictures);

		$path = "img/wiki_up";
		if ($tikidomain) {
			$path.= "/$tikidomain";
		}
		$h = opendir($path);

		while (($file = readdir($h)) !== false) {
			if (is_file("$path/$file") && $file != 'license.txt' && $file != 'index.php' && $file != '.cvsignore' && $file != 'README') {
				$filename = "$path/$file";

				if (!in_array($filename, $pictures)) {
					@unlink ($filename);
				}
			}
		}

		closedir ($h);
	}

	function remove_orphan_images() {
		$merge = array();

		// Find images in tiki_pages
		$query = "select `data` from `tiki_pages`";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			preg_match_all("/src=\"([^\"]+)\"/", $res["data"], $reqs1);

			preg_match_all("/src=\'([^\']+)\'/", $res["data"], $reqs2);
			preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/", $res["data"], $reqs3);
			$merge = array_merge($merge, $reqs1[1], $reqs2[1], $reqs3[1]);
			$merge = array_unique($merge);
		}

		// Find images in Tiki articles
		$query = "select `body` from `tiki_articles`";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			preg_match_all("/src=\"([^\"]+)\"/", $res["body"], $reqs1);

			preg_match_all("/src=\'([^\']+)\'/", $res["body"], $reqs2);
			preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/", $res["body"], $reqs3);
			$merge = array_merge($merge, $reqs1[1], $reqs2[1], $reqs3[1]);
			$merge = array_unique($merge);
		}

		// Find images in tiki_submissions
		$query = "select `body` from `tiki_submissions`";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			preg_match_all("/src=\"([^\"]+)\"/", $res["body"], $reqs1);

			preg_match_all("/src=\'([^\']+)\'/", $res["body"], $reqs2);
			preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/", $res["body"], $reqs3);
			$merge = array_merge($merge, $reqs1[1], $reqs2[1], $reqs3[1]);
			$merge = array_unique($merge);
		}

		// Find images in tiki_blog_posts
		$query = "select `data` from `tiki_blog_posts`";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			preg_match_all("/src=\"([^\"]+)\"/", $res["data"], $reqs1);

			preg_match_all("/src=\'([^\']+)\'/", $res["data"], $reqs2);
			preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/", $res["data"], $reqs3);
			$merge = array_merge($merge, $reqs1[1], $reqs2[1], $reqs3[1]);
			$merge = array_unique($merge);
		}

		$positives = array();

		foreach ($merge as $img) {
			if (strstr($img, 'show_image')) {
				preg_match("/id=([0-9]+)/", $img, $rq);

				$positives[] = $rq[1];
			}
		}

		$query = "select `imageId` from `tiki_images` where `galleryId`=0";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			$id = $res["imageId"];

			if (!in_array($id, $positives)) {
				$this->remove_image($id);
			}
		}
	}

	function tag_exists($tag) {
		$query = "select distinct `tagName` from `tiki_tags` where `tagName` = ?";

		$result = $this->query($query,array($tag));
		return $result->numRows($result);
	}

	function remove_tag($tagname) {
		global $prefs;

		$query = "delete from `tiki_tags` where `tagName`=?";
		$result = $this->query($query,array($tagname));
		global $logslib; include_once('lib/logs/logslib.php');
		$logslib->add_log('dump', "removed tag: $tagname");
		return true;
	}

	function get_tags() {
		$query = "select distinct `tagName` from `tiki_tags`";

		$result = $this->query($query,array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res["tagName"];
		}

		return $ret;
	}

	// This function can be used to store the set of actual pages in the "tags"
	// table preserving the state of the wiki under a tag name.
	function create_tag($tagname, $comment = '') {
		global $prefs;

		$query = "select * from `tiki_pages`";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			$data = $res["data"];
			$pageName = $res["pageName"];
			$description = $res["description"];
			$query = "delete from `tiki_tags`where `tagName`=? and `pageName`=?";
			$this->query($query,array($tagname,$pageName),-1,-1,false);
			$query = "insert into `tiki_tags`(`tagName`,`pageName`,`hits`,`data`,`lastModif`,`comment`,`version`,`user`,`ip`,`flag`,`description`)
                		values(?,?,?,?,?,?,?,?,?,?,?)";
			$result2 = $this->query($query,array($tagname,$pageName,$res["hits"],$data,$res["lastModif"],$res["comment"],$res["version"],$res["user"],$res["ip"],$res["flag"],$description));
		}

		global $logslib; include_once('lib/logs/logslib.php');
		$logslib->add_log('dump', "created tag: $tagname");
		return true;
	}

	// This funcion recovers the state of the wiki using a tagName from the
	// tags table
	function restore_tag($tagname) {
		global $prefs;

		$query = "update `tiki_pages` set `cache_timestamp`=0";
		$this->query($query,array());
		$query = "select * from `tiki_tags` where `tagName`=?";
		$result = $this->query($query,array($tagname));

		while ($res = $result->fetchRow()) {
			//$query="delete from `tiki_pages` where `pageName`=?";
			//$this->query($query,array($res["pageName"]),-1,-1,false);
			// update current page rather than delete and re-insert the page;
			// increment the version number so history is kept in tact
			$query
				= "update `tiki_pages` set `hits`=?,`data`=?,`lastModif`=?,`comment`=?,`version`=`version`+1,`user`=?,`ip`=?,`flag`=?,`description`=? where `pageName`=?";

			$result2 = $this->query($query,array($res["hits"],$res["data"],$res["lastModif"],$res["comment"],$res["user"],$res["ip"],$res["flag"],$res["description"],$res["pageName"]));
		}

		global $logslib; include_once('lib/logs/logslib.php');
		$logslib->add_log('dump', "recovered tag: $tagname");
		return true;
	}

	// Dumps the database to dump/new.tar
	// changed for virtualhost support
	function dump() {
		global $tikidomain, $prefs;
		$dump_path = "dump";
		if ($tikidomain) {
			$dump_path.= "/$tikidomain";
		}

		@unlink ("$dump_path/new.tar");
		$tar = new tar();
		$tar->addFile('styles/'.$prefs['style']);
		// Foreach page
		$query = "select * from `tiki_pages`";
		$result = $this->query($query,array());

		while ($res = $result->fetchRow()) {
			$pageName = $res["pageName"] . '.html';

			$dat = $this->parse_data($res["data"]);
			// Now change index.php?page=foo to foo.html
			// and index.php to HomePage.html
			$dat = preg_replace("/tiki-index.php\?page=([^\'\"\$]+)/", "$1.html", $dat);
			$dat = preg_replace("/tiki-editpage.php\?page=([^\'\"\$]+)/", "", $dat);
			//preg_match_all("/tiki-index.php\?page=([^ ]+)/",$dat,$cosas);
			//print_r($cosas);
			$data = "<html><head><title>" . $res["pageName"] . "</title><link rel='StyleSheet' href='styles/" . $prefs['style']  . "' type='text/css'></head><body><a class='wiki' href='" . $prefs['wikiHomePage'] . ".html'>home</a><br /><h1>" . $res["pageName"] . "</h1><div class='wikitext'>" . $dat . '</div></body></html>';
			$tar->addData($pageName, $data, $res["lastModif"]);
		}

		$tar->toTar("$dump_path/new.tar", FALSE);
		unset ($tar);
		global $logslib; include_once('lib/logs/logslib.php');
		$logslib->add_log('dump', 'dump created');
	}

}
$adminlib = new AdminLib;
