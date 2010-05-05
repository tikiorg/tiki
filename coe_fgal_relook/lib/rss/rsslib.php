<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once ('lib/userslib.php');
include_once ('lib/userprefs/scrambleEmail.php');
include_once ('lib/feedcreator/feedcreator.class.php');

global $dbTiki;
$userslib = new Userslib($dbTiki);

global $rss_cache_time;

class RSSLib extends TikiLib
{

	// ------------------------------------
	// functions for rss feeds we syndicate
	// ------------------------------------

	function get_rss_version($ver)
	{
		global $prefs;
		if ($ver=='') {
			// get default rss feed version from database or set to 0.91 if none in there
			$ver = $prefs['rssfeed_default_version'];
		}

		$rss_version=$ver;
		
		// valid format strings are: RSS0.91, RSS1.0, RSS2.0, PIE0.1, MBOX, OPML, ATOM0.3, HTML, JS, RSS0.9, PODCAST
		// valid format ids        :    9   ,   1   ,    2  ,   3   ,  4  ,   6 ,    5   ,  7  ,  8,   a,       i
		switch ($ver) {
			case "ATOM0.3":
			   $rss_version=5;
			   break;
			case "HTML":
			   $rss_version=7;
			   break;
			case "JS":
			   $rss_version=8;
			   break;
			case "MBOX":
			   $rss_version=4;
			   break;
			case "OPML":
			   $rss_version=6;
			   break;
			case "PODCAST":
			   $rss_version="i";
			   break;
			case "PIE0.1":
			   $rss_version=3;
			   break;
			case "RSS0.9":
			   $rss_version="a";
			   break;
			case "RSS0.91":
			   $rss_version=9;
			   break;
			case "RSS1.0":
			   $rss_version=1;
			   break;
			case "RSS2.0":
			   $rss_version=2;
			   break;
		}
		return $rss_version;
	}

	function get_rss_version_name($ver)
	{
		global $prefs;
		if ($ver=='') {
			// get default rss feed version from database or set to 0.91 if none in there
			$ver = $prefs['rssfeed_default_version'];
		}

		$rss_version_name=$ver;

		// valid format strings are: RSS0.91, RSS1.0, RSS2.0, PIE0.1, MBOX, OPML, ATOM0.3, HTML, JS, RSS0.9, PODCAST
		// valid format ids        :    9   ,   1   ,    2  ,   3   ,  4  ,   6 ,    5   ,  7  ,  8,   a,       i
		switch ($ver) {
			case "1":
			   $rss_version_name="RSS1.0";
			   break;
			case "2":
			   $rss_version_name="RSS2.0";
			   break;
			case "3":
			   $rss_version_name="PIE0.1";
			   break;
			case "4":
			   $rss_version_name="MBOX";
			   break;
			case "5":
			   $rss_version_name="ATOM0.3";
			   break;
			case "6":
			   $rss_version_name="OPML";
			   break;
			case "7":
			   $rss_version_name="HTML";
			   break;
			case "8":
			   $rss_version_name="JS";
			   break;
			case "9":
			   $rss_version_name="RSS0.91";
			   break;
			case "a":
			   $rss_version_name="RSS0.9";
			   break;
			case "i":
			   $rss_version_name="PODCAST";
			   break;
		}
		return $rss_version_name;
	}

	/* return the rss version we currently have to use (user param or default value) */
	function get_current_rss_version()
	{
		global $rss_version;
		if ($rss_version=='') {
			// override version if set as request parameter
			if (isset($_REQUEST["ver"])) {
				$ver = $_REQUEST["ver"];
				$rss_version = $this->get_rss_version($ver);
			} else {
				$rss_version = 9; // default to RSS 0.91
			}
		} else $rss_version = $this->get_rss_version($rss_version);
		return $rss_version;
	}

	/* check for cached rss feed data */
	function get_from_cache($uniqueid, $rss_version="9")
	{
		global $user;
		global $rss_cache_time;

		$rss_version=$this->get_current_rss_version();
		
		$output = array();
		$output["content-type"] = "application/xml";
		$output["encoding"] = "UTF-8";

		$output["data"] = "EMPTY";

		// caching rss data for anonymous users only
		if (isset($user) && $user<>"") return $output;

		$query = "select * from `tiki_rss_feeds` where `name`=? and `rssVer`=?";
		$bindvars=array($uniqueid, $rss_version);
		$result = $this->query($query, $bindvars);
		if (!$result->numRows()) {
		  // nothing found, then insert empty row for this feed+rss_ver
		  $query = "insert into `tiki_rss_feeds`(`name`,`rssVer`,`refresh`,`lastUpdated`,`cache`) values(?,?,?,?,?)";
		  $bindvars=array($uniqueid, $rss_version,(int) $rss_cache_time , 1, "-");
		  $result = $this->query($query, $bindvars);
		} else {
		  // entry found in db:
		  $res = $result->fetchRow();
		  $output["data"] = $res["cache"];
		  // $refresh = $res["refresh"]; // global cache time currently
		  $refresh = $rss_cache_time; // global cache time currently
		  $lastUpdated = $res["lastUpdated"];
		  // up to date? if not, then set trigger to reload data:
		  if ($this->now - $lastUpdated >= $refresh ) { $output["data"]="EMPTY"; }
		}
		return $output;
	}

	/* put to cache */
	function put_to_cache($uniqueid, $rss_version="9", $output)
	{
		global $user;
		// caching rss data for anonymous users only		
		if (isset($user) && $user<>"") return;
		if ($output=="" || $output=="EMPTY") return;

		$rss_version=$this->get_current_rss_version();

		// update cache with new generated data if data not empty

		$query = "update `tiki_rss_feeds` set `cache`=?, `lastUpdated`=? where `name`=? and `rssVer`=?";
		$bindvars = array($output, (int) $this->now, $uniqueid, $rss_version);
		$result = $this->query($query, $bindvars);
	}

	function generate_feed($feed, $uniqueid, $rss_version, $changes, $itemurl
		, $urlparam, $id, $title, $titleId, $desc, $descId, $dateId, $authorId
		, $fromcache=false
	) {
		global $prefs, $userslib, $rss_cache_time;
		
		$rss_version=$this->get_current_rss_version();

		if ($rss_cache_time < 1) $fromcache=false;

		// only get cache data if rss cache is enabled
		if ($fromcache) {
			$output = $this->get_from_cache($uniqueid, $rss_version);
			if ($output["data"]<>"EMPTY") return $output;
		}

		$urlarray = parse_url($_SERVER["REQUEST_URI"]);

		/* 
                   this gets the correct directory name aka dirname
                   when tikiwiki is on the main directory, i mean
                   when ur site is www.yoursite.com, the dirname of your site
                   is "/" and when tikiwiki is not on main directory, i mean
                   www.yoursite.com/tiki, the dirname returns "/tiki".
                   so, on URLs, we just need to add a extra slash when the
                   tikiwiki isnt on the main directory, what means,
                   dirname($urlarray["path"]) equals to "/tiki", otherwise
                   we can ommit them.

                   This is a quick hack to solve the infamous double-slash 
                   problem, which was introduced somewhen after 1.9.0 release 
                   http://dev.tikiwiki.org/tiki-view_tracker_item.php?trackerId=5&itemId=291


		*/

		$dirname = (dirname($urlarray["path"]) != "/" ? "/" : "");

		if ($prefs['index_rss_'.$feed]!='') {
			$url = $prefs['index_rss_'.$feed];
		} else {
			$url = htmlspecialchars($this->httpPrefix().$_SERVER["REQUEST_URI"]);
		}

		$home = htmlspecialchars($this->httpPrefix().dirname( $urlarray["path"] ).$dirname.$prefs['tikiIndex']);
		$img = htmlspecialchars($this->httpPrefix().dirname( $urlarray["path"] ).$dirname.$prefs['rssfeed_img']);

		$title = htmlspecialchars($title);
		$desc = htmlspecialchars($desc);
		$read = $this->httpPrefix().dirname($urlarray["path"]).$dirname.$itemurl;

		// different stylesheets for atom and rss	
		$cssStyleSheet = "";
		$xslStyleSheet = "";

		$encoding = "UTF-8";
		$contenttype = "application/xml";

		// valid format strings are: RSS0.91, RSS1.0, RSS2.0, PIE0.1, MBOX, OPML, ATOM0.3, HTML, JS, RSS0.9, PODCAST
		// valid format ids        :    9   ,   1   ,    2  ,   3   ,  4  ,   6 ,    5   ,  7  ,  8,   a,       i

		switch ($rss_version) {
			case "1": // RSS 1.0
				$cssStyleSheet = $this->httpPrefix().dirname( $urlarray["path"] ).$dirname."lib/rss/rss-style.css";
			break;
			case "2": // RSS 2.0
				$cssStyleSheet = $this->httpPrefix().dirname( $urlarray["path"] ).$dirname."lib/rss/rss-style.css";
				$xslStyleSheet = $this->httpPrefix().dirname( $urlarray["path"] ).$dirname."lib/rss/rss20.xsl";
			break;
			case "3": // PIE 0.1
				// plain RDF file
			break;
			case "4": // MBOX
				$contenttype = "text/plain";
			break;
			case "5": // ATOM0.3
				$cssStyleSheet = $this->httpPrefix().dirname( $urlarray["path"] ).$dirname."lib/rss/atom-style.css";
			break;
			case "6": // OPML
				$xslStyleSheet = $this->httpPrefix().dirname( $urlarray["path"] ).$dirname."lib/rss/opml.xsl";
			break;
			case "7": // HTML
				$contenttype = "text/plain";
			break;
			case "8": // JS
				$contenttype = "text/javascript";
			break;
			case "9": // RSS 0.91
				$cssStyleSheet = $this->httpPrefix().dirname( $urlarray["path"] ).$dirname."lib/rss/rss-style.css";
			break;
			case "a": // RSS 0.9
				// plain RDF file
			break;
			case "i": // PODCAST
				// plain RDF file
			break;
		}

		$rss = new UniversalFeedCreator(); 
		$rss->title = $title;
		$rss->description = $desc;
		
		//optional
		$rss->descriptionTruncSize = 500;
		$rss->descriptionHtmlSyndicated = true;
		$rss->cssStyleSheet = htmlspecialchars($cssStyleSheet);
		$rss->xslStyleSheet = htmlspecialchars($xslStyleSheet);
		$rss->encoding = $encoding;
		
		$rss->language = $prefs['rssfeed_language'];
		$rss->editor = $prefs['rssfeed_editor'];
		$rss->webmaster = $prefs['rssfeed_webmaster'];
		
		$rss->link = $url;
		$rss->feedURL = $url;
		
		$image = new FeedImage();
		$image->title = $prefs['browsertitle'];
		$image->url = $img;
		$image->link = $home;
		$image->description = sprintf(tra('Feed provided by %s. Click to visit.'), $prefs['browsertitle']);
	
		//optional
		$image->descriptionTruncSize = 500;
		$image->descriptionHtmlSyndicated = true;
		
		$rss->image = $image; 

		global $dbTiki;
		if (!isset($userslib)) 
			$userslib = new Userslib($dbTiki);
		
		foreach ($changes["data"] as $data)  {
			$item = new FeedItem(); 
			$item->title = $data["$titleId"]; 
			if (isset($data['sefurl'])) {
				$item->link = $this->httpPrefix().dirname($urlarray["path"]).$dirname.$data['sefurl'];
			} elseif ($urlparam<>'') {			// 2 parameters to replace
				$item->link = sprintf($read, urlencode($data["$id"]), urlencode($data["$urlparam"]));
			} else {
				$item->link = sprintf($read, urlencode($data["$id"]));
			}

			if (isset($data["$descId"])) {			
				$item->description = $data["$descId"]; 
			} else {
				$item->description = ""; 
			}

			// for file galleries and podcasts
			if (isset($data["filesize"])) {
				$item->size = $data["filesize"]; 
			} else {
				$item->size = 0;
			}
			// for file galleries and podcasts
			if (isset($data["filetype"])) {
				$item->mimetype = $data["filetype"]; 
			} else {
				$item->mimetype = "";
			}
	
			//optional
			//item->descriptionTruncSize = 500;
			$item->descriptionHtmlSyndicated = true;
			
			$item->date = (int) $data["$dateId"]; 
	
			$item->source = $url; 

			$item->author = "";
			if ($authorId<>"") {
				if ($prefs['showAuthor_rss_'.$feed] == 'y') {
					if ($userslib->user_exists($data["$authorId"])) {
						$item->author = $data["$authorId"];
						// only use realname <email> if existing and
						$tmp = "";
						if ($this->get_user_preference($data["$authorId"], 'user_information', 'private')=='public') {
							$tmp = $this->get_user_preference($data["$authorId"], "realName");
						}
						$epublic = $this->get_user_preference($data["$authorId"], 'email is public', 'n');
						if ($epublic!='n') {
							$res = $userslib->get_user_info($data["$authorId"], false);
							if ($tmp<>"") $tmp .= ' ';
							$tmp .= "<".scrambleEmail($res['email'], $epublic).">";
						}
						if ($tmp<>"") $item->author = $tmp;
					} else $item->author = $data["$authorId"];
				}
			}
			$rss->addItem($item); 
		} 
		$data = $rss->createFeed($this->get_rss_version_name($rss_version));
		$this->put_to_cache($uniqueid, $rss_version, $data);
		$output = array();
		$output["data"] = $data;
		$output["content-type"] = $contenttype;
		$output["encoding"] = $encoding;
		return $output;
	}

	// --------------------------------------------
	// functions for rss feeds syndicated by others
	// --------------------------------------------
	
	/* get (a part of) the list of existing rss feeds from db */
	function list_rss_modules($offset, $maxRecords, $sort_mode, $find)
	{

		if ($find) {
			$findesc="%" . $find . "%";
			$mid = " where (`name` like ? or `description` like ?)";
			$bindvars=array($findesc, $findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		$query = "select * from `tiki_rss_modules` $mid order by ".$this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_rss_modules` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$res["minutes"] = $res["refresh"] / 60;

			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	/* replace rss feed in db */
	function replace_rss_module($rssId, $name, $description, $url, $refresh, $showTitle, $showPubDate, $noUpdate = false)
	{
		//if($this->rss_module_name_exists($name)) return false; // TODO: Check the name
		$refresh = 60 * $refresh;

		if ($rssId) {
			$query = "update `tiki_rss_modules` set `name`=?,`description`=?,`refresh`=?,`url`=?,`showTitle`=?,`showPubDate`=? where `rssId`=?";
			$bindvars=array($name, $description, $refresh, $url, $showTitle, $showPubDate, (int)$rssId);
			$result = $this->query($query, $bindvars);
		} else {
			// was: replace into, no clue why.
			$query = "insert into `tiki_rss_modules`(`name`,`description`,`url`,`refresh`,`lastUpdated`,`showTitle`,`showPubDate`)
                values(?,?,?,?,?,?,?)";
			$bindvars=array($name, $description, $url, $refresh, 1000000, $showTitle, $showPubDate);

			$result = $this->query($query, $bindvars);
			$rssId = $this->lastInsertId();
		}

		if (!$noUpdate) {
			// Updating is normally required, except for cases where we know it will be updated later (e.g. after article generation is set, so that articles are created immediately) 
			$this->update_feeds( array( $rssId ), true );
		}
		return $rssId;
	}

	/* remove rss feed from db */
	function remove_rss_module($rssId)
	{
		$query = "delete from `tiki_rss_modules` where `rssId`=?";

		$result = $this->query($query, array((int)$rssId));
		return true;
	}

	/* read rss feed data from db */
	function get_rss_module($rssId)
	{
		$query = "select * from `tiki_rss_modules` where `rssId`=?";

		$result = $this->query($query, array((int)$rssId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function refresh_rss_module($rssId ) {
		$this->update_feeds( array( $rssId ), true );
	}

	/* check if an rss feed name already exists */
	function rss_module_name_exists($name)
	{
		$query = "select `name` from `tiki_rss_modules` where `name`=?";

		$result = $this->query($query, array($name));
		return $result->numRows();
	}

	/* get rss feed id by name */
	function get_rss_module_id($name)
	{
		$query = "select `rssId` from `tiki_rss_modules` where `name`=?";

		$id = $this->getOne($query, array($name));
		return $id;
	}

	/* check if 'showTitle' for an rss feed is enabled */
	function get_rss_showTitle($rssId)
	{
		$query = "select `showTitle` from `tiki_rss_modules` where `rssId`=?";

		$showTitle = $this->getOne($query, array((int)$rssId));
		return $showTitle;
	}

	/* check if 'showPubdate' for an rss feed is enabled */
	function get_rss_showPubDate($rssId)
	{
		$query = "select `showPubDate` from `tiki_rss_modules` where `rssId`=?";

		$showPubDate = $this->getOne($query, array((int)$rssId));
		return $showPubDate;
	}

	function get_feed_items( $feeds, $count = 10 ) {
		$feeds = (array) $feeds;

		$this->update_feeds( $feeds );

		$bindvars = array();
		$query = 'SELECT * FROM `tiki_rss_items` WHERE ' . $this->in( 'rssId', $feeds, $bindvars ) . ' ORDER BY publication_date DESC';

		return $this->fetchAll( $query, $bindvars, $count );
	}

	private function update_feeds( $feeds, $force = false ) {

		if( $force ) {
			$bindvars = array();
			$result = $this->fetchAll( 'SELECT `rssId`, `url`, `actions` FROM `tiki_rss_modules` WHERE ' . $this->in( 'rssId', $feeds, $bindvars ), $bindvars );
		} else {
			$bindvars = array( $this->now );
			$result = $this->fetchAll( 'SELECT `rssId`, `url`, `actions` FROM `tiki_rss_modules` WHERE (`lastUpdated` < ? - `refresh`) AND ' . $this->in( 'rssId', $feeds, $bindvars ), $bindvars );
		}

		foreach( $result as $row ) {
			$this->update_feed( $row['rssId'], $row['url'], $row['actions'] );
		}
	}

	private function update_feed( $rssId, $url, $actions ) {
		require_once 'Zend/Feed/Reader.php';

		$filter = new DeclFilter;
		$filter->addStaticKeyFilters( array(
			'url' => 'url',
			'title' => 'striptags',
			'author' => 'striptags',
			'description' => 'striptags',
			'content' => 'purifier',
		) );

		$guidFilter = TikiFilter::get('url');

		try {
			$feed = Zend_Feed_Reader::import( $url );
		} catch( Zend_Exception $e ) {
			$this->query( 'UPDATE `tiki_rss_modules` SET `lastUpdated` = ?, `sitetitle` = ?, `siteurl` = ? WHERE `rssId` = ?',
				array( $this->now, 'N/A', '#', $rssId ) );
			return;
		}
		$siteTitle = TikiFilter::get('striptags')->filter( $feed->getTitle() );
		$siteUrl = TikiFilter::get('url')->filter( $feed->getLink() );

		$this->query( 'UPDATE `tiki_rss_modules` SET `lastUpdated` = ?, `sitetitle` = ?, `siteurl` = ? WHERE `rssId` = ?',
			array( $this->now, $siteTitle, $siteUrl, $rssId ) );

		foreach( $feed as $entry ) {
			$guid = $guidFilter->filter( $entry->getId() );

			if( $this->getOne( 'SELECT COUNT(*) FROM `tiki_rss_items` WHERE `rssId` = ? AND `guid` = ?', array( $rssId, $guid ) ) == 0 ) {
				$authors = $entry->getAuthors();

				$data = $filter->filter( array(
					'title' => $entry->getTitle(),
					'url' => $entry->getLink(),
					'description' => $entry->getDescription(),
					'content' => $entry->getContent(),
					'author' => $authors ? implode( ', ', $authors->getValues() ) : '', 
				) );

				$data['guid'] = $guid;
				if( method_exists( $entry, 'getDateCreated' ) ) {
					$data['publication_date'] = $entry->getDateCreated()->get( Zend_Date::TIMESTAMP );
				} else {
					global $tikilib;
					$data['publication_date'] = $tikilib->now;
				}

				$this->insert_item( $rssId, $data, $actions );
			}
		}
	}

	private function insert_item( $rssId, $data, $actions ) {
		$query = 'INSERT INTO `tiki_rss_items` ( `rssId`, `guid`, `url`, `publication_date`, `title`, `author`, `description`, `content` ) VALUES( ?, ?, ?, ?, ?, ?, ?, ? )';
		$this->query( $query, array(
			$rssId,
			$data['guid'],
			$data['url'],
			$data['publication_date'],
			$data['title'],
			$data['author'],
			$data['description'],
			$data['content'],
		) );
		
		$actions = json_decode( $actions, true );

		foreach( $actions as $action ) {
			$method = 'process_action_' . $action['type'];
			unset( $action['type'] );

			if( $action['active'] ) {
				$this->$method( $action, $data );
			}
		}
	}

	private function process_action_article( $configuration, $data ) {
		global $artlib; require_once 'lib/articles/artlib.php';
		$publication = $data['publication_date'];

		if( $configuration['future_publish'] > 0 ) {
			$publication = $this->now + $configuration['future_publish']*60;
		}

		$expire = $publication + 3600*24*$configuration['expiry'];

		$id = $artlib->replace_article( $data['title'], $data['author'], $configuration['topic'], 'n', '', 0, '', '', $data['description'], '~np~' . $data['content'] . '~/np~', $publication, $expire, 'admin', 0, 0, 0, $configuration['atype'], '', '', $data['url'], '', '', $configuration['rating'] );

		if( count( $configuration['categories'] ) ) {
			global $categlib; require_once 'lib/categories/categlib.php';
			$objectId = $categlib->add_categorized_object( 'article', $id, $data['title'], $data['title'], 'tiki-read_article.php?articleId=' . $id );

			foreach( $configuration['categories'] as $categId ) {
				$categlib->categorize( $objectId, $categId );
			}
		}
	}

	function set_article_generator( $rssId, $configuration ) {
		$configuration['type'] = 'article';

		if( $module['actions'] ) {
			$actions = json_decode( $module['actions'], true );
		} else {
			$actions = array();
		}

		$out = array();
		foreach( $actions as $action ) {
			if( $action['type'] != 'article' ) {
				$out[] = $action;
			}
		}

		$out[] = $configuration;

		$this->query( 'UPDATE `tiki_rss_modules` SET `actions` = ? WHERE `rssId` = ?', array( json_encode( $out ), $rssId ) );
	}

	function get_article_generator( $rssId ) {
		$module = $this->get_rss_module( $rssId );

		if( $module['actions'] ) {
			$actions = json_decode( $module['actions'], true );
		} else {
			$actions = array();
		}

		$default = array(
			'active' => false,
			'expiry' => 365,
			'atype' => 'Article',
			'topic' => 0,
			'future_publish' => -1,
			'categories' => array(),
			'rating' => 5,
		);

		foreach( $actions as $action ) {
			if( $action['type'] == 'article' ) {
				unset( $action['type'] );
				return array_merge( $default, $action );
			}
		}

		return $default;
	}
}
global $rsslib;
$rsslib = new RSSLib;
