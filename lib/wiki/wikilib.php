<?php

define('PLUGINS_DIR', 'lib/wiki-plugins');

class WikiLib extends TikiLib {
	function WikiLib($db) {
		if (!$db) {
			die ("Invalid db object passed to WikiLib constructor");
		}

		$this->db = $db;
	}

	// 29-Jun-2003, by zaufi
	// The 2 functions below contain duplicate code
	// to remove <PRE> tags... (moreover I copy this code
	// from tikilib.php, and paste to artlib.php, bloglib.php
	// and wikilib.php)
	// TODO: it should be separate function to avoid
	// maintain 3 pieces... (but I don't know PHP and TIKI
	// architecture very well yet to make this :()

	//Special parsing for multipage articles
	function get_number_of_pages($data) {
		// Temporary remove <PRE></PRE> secions to protect
		// from broke <PRE> tags and leave well known <PRE>
		// behaviour (i.e. type all text inside AS IS w/o
		// any interpretation)
		$preparsed = array();

		preg_match_all("/(<[Pp][Rr][Ee]>)((.|\n)*?)(<\/[Pp][Rr][Ee]>)/", $data, $preparse);
		$idx = 0;

		foreach (array_unique($preparse[2])as $pp) {
			$key = md5($this->genPass());

			$aux["key"] = $key;
			$aux["data"] = $pp;
			$preparsed[] = $aux;
			$data = str_replace($preparse[1][$idx] . $pp . $preparse[4][$idx], $key, $data);
			$idx = $idx + 1;
		}

		$parts = explode("...page...", $data);
		return count($parts);
	}

	function get_page($data, $i) {
		// Temporary remove <PRE></PRE> secions to protect
		// from broke <PRE> tags and leave well known <PRE>
		// behaviour (i.e. type all text inside AS IS w/o
		// any interpretation)
		$preparsed = array();

		preg_match_all("/(<[Pp][Rr][Ee]>)((.|\n)*?)(<\/[Pp][Rr][Ee]>)/", $data, $preparse);
		$idx = 0;

		foreach (array_unique($preparse[2])as $pp) {
			$key = md5($this->genPass());

			$aux["key"] = $key;
			$aux["data"] = $pp;
			$preparsed[] = $aux;
			$data = str_replace($preparse[1][$idx] . $pp . $preparse[4][$idx], $key, $data);
			$idx = $idx + 1;
		}

		// Get slides
		$parts = explode("...page...", $data);

		if (substr($parts[$i - 1], 1, 5) == "<br/>")
			$ret = substr($parts[$i - 1], 6);
		else
			$ret = $parts[$i - 1];

		// Replace back <PRE> sections
		foreach ($preparsed as $pp)
			$ret = str_replace($pp["key"], "<pre>" . $pp["data"] . "</pre>", $ret);

		return $ret;
	}

	function get_creator($name) {
		$name = addslashes($name);

		return $this->getOne("select `creator` from `tiki_pages` where `pageName`=?", array($name));
	}

	function wiki_page_graph(&$str, &$graph) {
		$page = $str['name'];

		$graph->addAttributes(array(
			'nodesep' => '.3',
			'rankdir' => 'LR',
			'size' => '6',
			'bgcolor' => 'transparent',
			'URL' => 'tiki-index.php'
		));

		$graph->addNode("$page", array(
			'URL' => "tiki-index.php?page=" . addslashes($page),
			'label' => "$page",
			'fontname' => "Arial",
			'fontsize' => '9',
			'shape' => 'ellipse',
			'color' => '#AAAAAA',
			'style' => 'filled',
			'fillcolor' => '#FFFFFF',
			'width' => '.5',
			'height' => '.25'
		));

		//print("add node $page<br/>");
		foreach ($str['pages'] as $neig) {
			$this->wiki_page_graph($neig, $graph);

			$graph->addEdge(array($page => $neig['name']), array(
				'color' => '#998877',
				'style' => 'solid'
			));
		//print("add edge $page to ".$neig['name']."<br/>");
		}
	}

	function get_graph_map($page, $level) {
		$str = $this->wiki_get_link_structure($page, $level);

		$graph = new Image_GraphViz();
		$this->wiki_page_graph($str, $graph);
		return $graph->map();
	}

	function wiki_get_link_structure($page, $level) {
		$query = "select `toPage` from `tiki_links` where `fromPage`='$page'";

		$result = $this->query($query);
		$aux['pages'] = array();
		$aux['name'] = $page;

		while ($res = $result->fetchRow()) {
			if ($level) {
				$aux['pages'][] = $this->wiki_get_link_structure($res['toPage'], $level - 1);
			} else {
				$inner['name'] = $res['toPage'];

				$inner['pages'] = array();
				$aux['pages'][] = $inner;
			}
		}

		return $aux;
	}

	// This method renames a wiki page
	// If you think this is easy you are very very wrong
	function wiki_rename_page($oldName, $newName) {
		if ($this->page_exists($newName)) {
			return false;
		}

		$oldName_as = addslashes($oldName);
		$newName_as = addslashes($newName);
		// 1st rename the page in tiki_pages
		$query = "update `tiki_pages` set `pageName`='$newName_as' where `pageName`='$oldName_as'";
		$this->query($query);
		// correct pageName in tiki_history
		$query = "update `tiki_history` set `pageName`='$newName_as' where `pageName`='$oldName_as'";
		$this->query($query);
		// get pages linking to the old page
		$query = "select `fromPage` from `tiki_links` where `toPage`='$oldName_as'";
		$result = $this->query($query);

		while ($res = $result->fetchRow()) {
			$page = $res['fromPage'];

			$page_as = addslashes($page);
			$info = $this->get_page_info($page);
			//$data=addslashes(str_replace($oldName,$newName,$info['data']));
			$data = $info['data'];
			$data = preg_replace("/(?<= |\n|\t|\r|\,|\;|^)$oldName(?= |\n|\t|\r|\,|\;|$)/", $newName, $data);
			$data = preg_replace("/(?<=\(\()$oldName(?=\)\)|\|)/", $newName, $data);
			$data = addslashes($data);
			$query = "update `tiki_pages` set `data`=? where `pageName`=?";
			$this->query($query, array(
				$data,
				$page_as
			));

			$this->invalidate_cache($page);
		}

		// correct toPage and fromPage in tiki_links
		$query = "update `tiki_links` set `fromPage`=? where `fromPage`=?";
		$this->query($query, array(
			$newName_as,
			$oldName_as
		));

		$query = "update `tiki_links` set `toPage`=? where `toPage`=?";
		$this->query($query, array(
			$newName_as,
			$oldName_as
		));

		// tiki_footnotes change pageName
		$query = "update `tiki_page_footnotes` set `pageName`='$newName_as' where `pageName`='$oldName_as'";
		$this->query($query);

		// tiki_structures change page and parent
		$query = "update `tiki_structures` set `page`='$newName_as' where `page`='$oldName_as'";
		$this->query($query);
		$query = "update `tiki_structures` set `parent`='$newName_as' where `parent`='$oldName_as'";
		$this->query($query);

		// user_bookmarks_urls (url)

		// user notes (data)

		// Build objectId using 'wiki page' and the name
		$oldId = 'wiki page' + md5($oldName);
		$newId = 'wiki page' + md5($newName);

		// in tiki_categorized_objects update objId
		$newcathref = 'tiki-index.php?page=' . urlencode($newName_as);
		$query = "update `tiki_categorized_objects` set `objId`=?,`name`=?,`href`=? where `objId`=?";
		$this->query($query, array(
			$newName_as,
			$newName_as,
			$newcathref,
			$oldName_as
		));

		// old code that doesn't seem to be working
		//	$query = "update tiki_categorized_objects set objId='$newId' where objId='$oldId'";
		//    $this->query($query);	  	  	  	

		// in tiki_comments update object  
		$query = "update `tiki_comments` set `object`='$newId' where `object`='$oldId'";
		$this->query($query);

		// in tiki_mail_events by object
		$query = "update `tiki_mail_events` set `object`='$newId' where `object`='$oldId'";
		$this->query($query);

		// theme_control_objects(objId,name)
		$query = "update `tiki_theme_control_objects` set `objId`='newId',name='$newName_as' where `objId`='$oldId'";
		$this->query($query);

		$query = "update `tiki_wiki_attachments` set `page`='$newName' where `page`='$oldName'";
		$this->query($query);

		//update structures
		$query = "update `tiki_structures` set `page`='$newName' where `page`='$oldName'";
		$this->query($query);
		$query = "update `tiki_structures` set `parent`='$newName' where `parent`='$oldName'";
		$this->query($query);

		return true;
	}

	function save_notepad($user, $title, $data) {
		$data = addslashes($data);

		$title = addslashes($data);
	}

	// Methods to cache and handle the cached version of wiki pages
	// to prevent parsing large pages.
	function get_cache_info($page) {
		$query = "select `cache`,cache_timestamp from `tiki_pages` where `pageName`='$page'";

		$result = $this->query($query);
		$res = $result->fetchRow();
		return $res;
	}

	function update_cache($page, $data) {
		$now = date('U');

		$data = addslashes($data);
		$query = "update `tiki_pages` set `cache`='$data', cache_timestamp=$now where `pageName`='$page'";
		$result = $this->query($query);
		return true;
	}

	function get_attachment_owner($attId) {
		return $this->getOne("select `user` from `tiki_wiki_attachments` where `attId`=$attId");
	}

	function remove_wiki_attachment($attId) {
		global $w_use_dir;

		$path = $this->getOne("select `path` from `tiki_wiki_attachments` where `attId`=$attId");

		if ($path) {
			@unlink ($w_use_dir . $path);
		}

		$query = "delete from `tiki_wiki_attachments` where `attId`='$attId'";
		$result = $this->query($query);
	}

	function wiki_attach_file($page, $name, $type, $size, $data, $comment, $user, $fhash) {
		$data = addslashes($data);

		$page = addslashes($page);
		$name = addslashes($name);
		$comment = addslashes(strip_tags($comment));
		$now = date("U");
		$query = "insert into tiki_wiki_attachments(page,filename,filesize,filetype,data,created,downloads,user,comment,path)
    values('$page','$name',$size,'$type','$data',$now,0,'$user','$comment','$fhash')";
		$result = $this->query($query);
	}

	function list_wiki_attachments($page, $offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$mid = " where `page`=? and (`filename` like ?)"; // why braces?
			$bindvars=array($page,"%".$find."%");
		} else {
			$mid = " where `page`=? ";
			$bindvars=array($page);
		}

		$query = "select `user`,`attId`,`page`,`filename`,`filesize`,`filetype`,`downloads`,`created`,`comment` from `tiki_wiki_attachments` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_wiki_attachments` $mid";
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

	// Functions for wiki page footnotes
	function get_footnote($user, $page) {

		$count = $this->getOne("select count(*) from `tiki_page_footnotes` where `user`=? and `pageName`=?",array($user,$page));

		if (!$count) {
			return '';
		} else {
			return $this->getOne("select `data` from `tiki_page_footnotes` where `user`=? and `pageName`=?",array($user,$page));
		}
	}

	function replace_footnote($user, $page, $data) {
		$querydel = "delete from `tiki_page_footnotes` where `user`=? and `pageName`=?";
		$this->query($querydel,array($user, $page),-1,-1,false);
		$query = "insert into `tiki_page_footnotes`(`user`,`pageName`,`data`) values(?,?,?)";
		$this->query($query,array($user,$page,$data));
	}

	function remove_footnote($user, $page) {
		$query = "delete from `tiki_page_footnotes` where `user`=? and `pageName`=?";
		$this->query($query,array($user,$page));
	}

	function wiki_link_structure() {
		$query = "select `pageName` from `tiki_pages` order by pageName asc";

		$result = $this->query($query);

		while ($res = $result->fetchRow()) {
			print ($res["pageName"] . " ");

			$page = $res["pageName"];
			$query2 = "select `toPage` from `tiki_links` where `fromPage`='$page'";
			$result2 = $this->query($query2);
			$pages = array();

			while ($res2 = $result2->fetchRow()) {
				if (($res2["toPage"] <> $res["pageName"]) && (!in_array($res2["toPage"], $pages))) {
					$pages[] = $res2["toPage"];

					print ($res2["toPage"] . " ");
				}
			}

			print ("\n");
		}
	}

	// Removes last version of the page (from pages) if theres some
	// version in the tiki_history then the last version becomes the actual version
	function remove_last_version($page, $comment = '') {
		global $histlib;

		$page = addslashes($page);
		$this->invalidate_cache($page);
		$query = "select * from `tiki_history` where `pageName`='$page' order by lastModif desc";
		$result = $this->query($query);

		if ($result->numRows()) {
			// We have a version
			$res = $result->fetchRow();

			$histlib->use_version($res["pageName"], $res["version"]);
			$histlib->remove_version($res["pageName"], $res["version"]);
		} else {
			$this->remove_all_versions($page);
		}

		$action = "Removed last version";
		$t = date("U");
		$query = "insert into tiki_actionlog(action,pageName,lastModif,user,ip,comment) values('$action','$page',$t,'admin','" . $_SERVER["REMOTE_ADDR"] . "','$comment')";
		$result = $this->query($query);
	}

	// Like pages are pages that share a word in common with the current page
	function get_like_pages($page) {
		preg_match_all("/([A-Z])([a-z]+)/", $page, $words);

		// Add support to ((x)) in either strict or full modes
		preg_match_all("/(([A-Za-z]|[\x80-\xFF])+)/", $page, $words2);
		$words = array_unique(array_merge($words[0], $words2[0]));
		$exps = array();

		foreach ($words as $word) {
			$exps[] = "pageName like '%$word%'";
		}

		$exp = implode(" or ", $exps);
		$query = "select `pageName` from `tiki_pages` where $exp";
		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res["pageName"];
		}

		return $ret;
	}

	function is_locked($page) {
		$page = addslashes($page);

		$query = "select `flag` from `tiki_pages` where `pageName`='$page'";
		$result = $this->query($query);
		$res = $result->fetchRow();

		if ($res["flag"] == 'L')
			return true;

		return false;
	}

	function lock_page($page) {
		global $user;

		$page = addslashes($page);
		$query = "update `tiki_pages` set `flag`='L' where `pageName`='$page'";
		$result = $this->query($query);

		if (isset($user)) {
			$query = "update `tiki_pages` set `user`='$user' where `pageName`='$page'";

			$result = $this->query($query);
		}

		return true;
	}

	function unlock_page($page) {
		$page = addslashes($page);

		$query = "update `tiki_pages` set `flag`='' where `pageName`='$page'";
		$result = $this->query($query);
		return true;
	}

	// Returns backlinks for a given page
	function get_backlinks($page) {
		$query = "select `fromPage` from `tiki_links` where `toPage` = '$page'";

		$result = $this->query($query);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux["fromPage"] = $res["fromPage"];

			$ret[] = $aux;
		}

		return $ret;
	}

	function list_plugins() {
		$files = array();

		if (is_dir(PLUGINS_DIR)) {
			if ($dh = opendir(PLUGINS_DIR)) {
				while (($file = readdir($dh)) !== false) {
					if (preg_match("/^wikiplugin_.*\.php$/", $file))
						array_push($files, $file);
				}

				closedir ($dh);
			}
		}

		return $files;
	}

	//
	// Call 'wikiplugin_.*_description()' from given file
	//
	function get_plugin_description($file) {
		global $tikilib;

		include_once (PLUGINS_DIR . '/' . $file);
		$func_name = str_replace(".php", "", $file). '_help';
		return function_exists($func_name) ? $tikilib->parse_data($func_name()) : "";
	}
}

global $wikilib;
$wikilib = new WikiLib($dbTiki);

?>
