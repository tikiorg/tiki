<?php
//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  die("This script cannot be called directly");
}

if( !defined( 'PLUGINS_DIR' ) ) {
	define('PLUGINS_DIR', 'lib/wiki-plugins');
}

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
	return $this->getOne("select `creator` from `tiki_pages` where `pageName`=?", array($name));
    }

    function wiki_page_graph(&$str, &$graph, $garg) {
	$page = $str['name'];

	$graph->addAttributes(array(
		    'nodesep' => (isset($garg['att']['nodesep']))?$garg['att']['nodesep']:".1",
		    'rankdir' => (isset($garg['att']['rankdir']))?$garg['att']['rankdir']:'LR',
		    'size' => (isset($garg['att']['size']))?$garg['att']['size']:'6',
		    'bgcolor' => (isset($garg['att']['bgcolor']))?$garg['att']['bgcolor']:'transparent',
		    'URL' => 'tiki-index.php'
		    ));

	$graph->addNode("$page", array(
		    'URL' => "tiki-index.php?page=" . urlencode(addslashes($page)),
		    'label' => "$page",
		    'fontname' => (isset($garg['node']['fontname']))?$garg['node']['fontname']:"Arial",
		    'fontsize' => (isset($garg['node']['fontsize']))?$garg['node']['fontsize']:'9',
		    'shape' => (isset($garg['node']['shape']))?$garg['node']['shape']:'ellipse',
		    'color' => (isset($garg['node']['color']))?$garg['node']['color']:'#AAAAAA',
		    'style' => (isset($garg['node']['style']))?$garg['node']['style']:'filled',
		    'fillcolor' => (isset($garg['node']['fillcolor']))?$garg['node']['fillcolor']:'#FFFFFF',
		    'width' => (isset($garg['node']['width']))?$garg['node']['width']:'.5',
		    'height' => (isset($garg['node']['height']))?$garg['node']['height']:'.25'
		    ));

	//print("add node $page<br/>");
	foreach ($str['pages'] as $neig) {
	    $this->wiki_page_graph($neig, $graph, $garg);

	    $graph->addEdge(array("$page" => $neig['name']), array(
			'color' => '#998877',
			'style' => 'solid'
			));
	    //print("add edge $page to ".$neig['name']."<br/>");
	}
    }

    function get_graph_map($page, $level, $garg) {
	$str = $this->wiki_get_link_structure($page, $level);
	$graph = new Image_GraphViz();
	$this->wiki_page_graph($str, $graph, $garg);
	return $graph->map();
    }

    function wiki_get_link_structure($page, $level) {
	$query = "select `toPage` from `tiki_links` where `fromPage`=?";

	$result = $this->query($query,array($page));
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
		// if page already exists, stop here
		if ($this->page_exists($newName)) {
			// if it is a case change of same page: allow it, else stop here
			if (strcasecmp($oldName, $newName) <> 0) return false;
		}

		// Since page link have HTML chars fixed;
		$newName = htmlspecialchars( $newName );
		$tmpName = "TmP".$newName."TmP";

		// 1st rename the page in tiki_pages, using a tmpname inbetween for
		// rename pages like ThisTestpage to ThisTestPage
		$query = "update `tiki_pages` set `pageName`=? where `pageName`=?";
		$this->query($query, array( $tmpName, $oldName ) );

		$query = "update `tiki_pages` set `pageName`=? where `pageName`=?";
		$this->query($query, array( $newName, $tmpName ) );

		// correct pageName in tiki_history, using a tmpname inbetween for
		// rename pages like ThisTestpage to ThisTestPage
		$query = "update `tiki_history` set `pageName`=? where `pageName`=?";
		$this->query($query, array( $tmpName, $oldName ) );

		$query = "update `tiki_history` set `pageName`=? where `pageName`=?";
		$this->query($query, array( $newName, $tmpName ) );
		
		// get pages linking to the old page
		$query = "select `fromPage` from `tiki_links` where `toPage`=?";
		$result = $this->query($query, array( $oldName ) );

		while ($res = $result->fetchRow()) {
		    $page = $res['fromPage'];
	
		    $info = $this->get_page_info($page);
		    //$data=addslashes(str_replace($oldName,$newName,$info['data']));
		    $data = $info['data'];
		    $oldName = quotemeta( $oldName );
		    $data = preg_replace("/(?<= |\n|\t|\r|\,|\;|^)$oldName(?= |\n|\t|\r|\,|\;|$)/", $newName, $data);
		    $data = preg_replace("/(?<=\(\()$oldName(?=\)\)|\|)/", $newName, $data);
		    $query = "update `tiki_pages` set `data`=?,`page_size`=? where `pageName`=?";
		    $this->query($query, array( $data,(int) strlen($data), $page));
		    $this->invalidate_cache($page);
		}

		// correct toPage and fromPage in tiki_links
		$query = "update `tiki_links` set `fromPage`=? where `fromPage`=?";
		$this->query($query, array( $newName, $oldName));
	
		$query = "update `tiki_links` set `toPage`=? where `toPage`=?";
		$this->query($query, array( $newName, $oldName));
	
		// tiki_footnotes change pageName
		$query = "update `tiki_page_footnotes` set `pageName`=? where `pageName`=?";
		$this->query($query, array( $newName, $oldName ));
	
		// Build objectId using 'wiki page' and the name
		$oldId = 'wiki page' + md5($oldName);
		$newId = 'wiki page' + md5($newName);
	
		// in tiki_categorized_objects update objId
		$newcathref = 'tiki-index.php?page=' . urlencode($newName);
		$query = "update `tiki_categorized_objects` set `objId`=?,`name`=?,`href`=? where `objId`=?";
		$this->query($query, array( $newName, $newName, $newcathref, $oldName));
	
		// old code that doesn't seem to be working
		//	$query = "update tiki_categorized_objects set objId='$newId' where objId='$oldId'";
		//    $this->query($query);	  	  	  	
	
		// in tiki_comments update object  
		$query = "update `tiki_comments` set `object`=? where `object`=?";
		$this->query($query, array( $newName, $oldName ) );
	
		// in tiki_mail_events by object
		$query = "update `tiki_mail_events` set `object`=? where `object`=?";
		$this->query($query, array( $newId, $oldId ) );

		// user watches
		$query = "update `tiki_user_watches` set `object`=?, `title`=? where `object`=? and `type` = 'Page Wiki'";
		$this->query($query, array( $newName, $newName, $oldName ) );

		// theme_control_objects(objId,name)
		$query = "update `tiki_theme_control_objects` set `objId`=?, `name`=? where `objId`=?";
		$this->query($query, array( $newId, $newName, $oldId ) );
	
		$query = "update `tiki_wiki_attachments` set `page`=? where `page`=?";
		$this->query($query, array( $newName, $oldName ) );

		// group home page
		$query = "update `users_groups` set `groupHome`=? where `groupHome`=?";
		$this->query($query, array( $newName, $oldName ) );

		return true;
	}

	function set_page_cache($page,$cache) {
		$query = "update `tiki_pages` set `wiki_cache`=? where `pageName`=?";
		$this->query($query, array( $cache, $page));
	}

	// huho why that fuction is empty ??
	function save_notepad($user, $title, $data) {
	}

	// Methods to cache and handle the cached version of wiki pages
	// to prevent parsing large pages.
	function get_cache_info($page) {
		$query = "select `cache`,`cache_timestamp` from `tiki_pages` where `pageName`=?";

		$result = $this->query($query, array( $page ) );
		$res = $result->fetchRow();
		return $res;
	}

	function update_cache($page, $data) {
		$now = date('U');

		$query = "update `tiki_pages` set `cache`=?, cache_timestamp=$now where `pageName`=?";
		$result = $this->query($query, array( $data, $page ) );
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
		$comment = strip_tags($comment);
		$now = date("U");
		$query = "insert into `tiki_wiki_attachments`(`page`,`filename`,`filesize`,`filetype`,`data`,`created`,`downloads`,`user`,`comment`,`path`) values(?,?,?,?,?,?,0,?,?,?)";
		$result = $this->query($query,array("$page","$name", (int) $size,"$type","$data", (int) $now,"$user","$comment","$fhash"));
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
	function list_all_attachements($offset=0, $maxRecords=-1, $sort_mode='created_desc', $find='') {
		if ($find) {
			$findesc = '%' . $find . '%';
			$mid = " where `filename` like ?";
			$bindvars=array($findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}
		$query = "select `user`,`attId`,`page`,`filename`,`filesize`,`filetype`,`downloads`,`created`,`comment`,`path` ";
		$query.= " from `tiki_wiki_attachments` $mid order by ".$this->convert_sortmode($sort_mode);
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
		
	function file_to_db($path,$attId) {
		if (is_file($path)) {
			$fp = fopen($path,'rb');
			$data = '';
			while (!feof($fp)) {
				$data .= fread($fp, 8192 * 16);
			}
			fclose ($fp);
			$query = "update `tiki_wiki_attachments` set `data`=?,`path`=? where `attId`=?";
			if ($this->query($query,array($data,'',(int)$attId))) {
				unlink($path);
			}
		}
	}

	function db_to_file($path,$attId) {
		$fw = fopen($path,'wb');
		$data = $this->getOne("select `data` from `tiki_wiki_attachments` where `attId`=?",array((int)$attId));
		if ($data) {
			fwrite($fw, $data);
		}
		fclose ($fw);
		if (is_file($path)) {
			$query = "update `tiki_wiki_attachments` set `data`=?,`path`=? where `attId`=?";
			$this->query($query,array('',basename($path),(int)$attId));
		}
	}

	function get_item_attachment($attId) {
		$query = "select * from `tiki_wiki_attachments` where `attId`=?";
		$result = $this->query($query,array((int) $attId));
		if (!$result->numRows()) return false;
		$res = $result->fetchRow();
		return $res;
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
	$query = "select `pageName` from `tiki_pages` order by ".$this->convert_sortmode("pageName_asc");

	$result = $this->query($query);

	while ($res = $result->fetchRow()) {
	    print ($res["pageName"] . " ");

	    $page = $res["pageName"];
	    $query2 = "select `toPage` from `tiki_links` where `fromPage`=?";
	    $result2 = $this->query($query2, array( $page ) );
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

	$this->invalidate_cache($page);
	$query = "select * from `tiki_history` where `pageName`=? order by ".$this->convert_sortmode("lastModif_desc");
	$result = $this->query($query, array( $page ) );

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
	$query = "insert into `tiki_actionlog`( `action`, `pageName`, `lastModif`, `user`, `ip`, `comment`) values( ?, ?, ?, ?, ?, ?)";
	$result = $this->query($query, array( $action, $page, $t, "admin", $_SERVER["REMOTE_ADDR"], $comment ) );
    }

    // Like pages are pages that share a word in common with the current page
    function get_like_pages($page) {
	preg_match_all("/([A-Z])([a-z]+)/", $page, $words);

	// Add support to ((x)) in either strict or full modes
	preg_match_all("/(([A-Za-z]|[\x80-\xFF])+)/", $page, $words2);
	$words = array_unique(array_merge($words[0], $words2[0]));
	$exps = array();
	$bindvars=array();
	foreach ($words as $word) {
	    $exps[] = "`pageName` like ?";
	    $bindvars[] = "%$word%";
	}

	$exp = implode(" or ", $exps);
	$query = "select `pageName` from `tiki_pages` where $exp";
	$result = $this->query($query,$bindvars);
	$ret = array();

	while ($res = $result->fetchRow()) {
	    $ret[] = $res["pageName"];
	}

	return $ret;
    }

    function is_locked($page) {
	$query = "select `flag` from `tiki_pages` where `pageName`=?";
	$result = $this->query($query, array( $page ) );
	$res = $result->fetchRow();

	if ($res["flag"] == 'L')
	    return true;

	return false;
    }

    function lock_page($page) {
	global $user;

	$query = "update `tiki_pages` set `flag`=? where `pageName`=?";
	$result = $this->query($query, array( "L",$page ) );

	if (isset($user)) {
	    $query = "update `tiki_pages` set `user`=? where `pageName`=?";

	    $result = $this->query($query, array( $user, $page ) );
	}

	return true;
    }

    function unlock_page($page) {
	$query = "update `tiki_pages` set `flag`='' where `pageName`=?";
	$result = $this->query($query, array( $page ) );
	return true;
    }

    // Returns backlinks for a given page
    function get_backlinks($page) {
	$query = "select `fromPage` from `tiki_links` where `toPage` = ?";
	$result = $this->query($query, array( $page ));
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
global $dbTiki;
$wikilib = new WikiLib($dbTiki);

?>
