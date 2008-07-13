<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

include_once ('lib/userslib.php');
include_once ('lib/userprefs/scrambleEmail.php');
include_once ('lib/feedcreator/feedcreator.class.php');

$userslib = new Userslib($dbTiki);

global $rss_cache_time;

class RSSLib extends TikiLib {

	function RSSLib($db) {
		$this->TikiLib($db);
	}

	// ------------------------------------
	// functions for rss feeds we syndicate
	// ------------------------------------

	function get_rss_version($ver) {
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

	function get_rss_version_name($ver) {
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
	function get_current_rss_version() {
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
	function get_from_cache($uniqueid, $rss_version="9") {
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
	function put_to_cache($uniqueid, $rss_version="9", $output) {
		global $user;
		// caching rss data for anonymous users only		
		if (isset($user) && $user<>"") return;
		if ($output=="" || $output=="EMPTY") return;

		$rss_version=$this->get_current_rss_version();

		// update cache with new generated data if data not empty

		$query = "update `tiki_rss_feeds` set `cache`=?, `lastUpdated`=? where `name`=? and `rssVer`=?";
		$bindvars = array($output, (int) $this->now, $uniqueid, $rss_version);
		$result = $this->query($query,$bindvars);
	}

	function generate_feed($feed, $uniqueid, $rss_version, $changes, $itemurl, $urlparam, $id, $title, $titleId, $desc, $descId, $dateId, $authorId, $fromcache=false) {
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

		$url = htmlspecialchars($this->httpPrefix().$_SERVER["REQUEST_URI"]);
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
		$image->title = $prefs['siteTitle'];
		$image->url = $img;
		$image->link = $home;
		$image->description = sprintf(tra('Feed provided by %s. Click to visit.'), $prefs['siteTitle']);
	
		//optional
		$image->descriptionTruncSize = 500;
		$image->descriptionHtmlSyndicated = true;
		
		$rss->image = $image; 

		global $dbTiki;
        if (!isset($userslib)) $userslib = new Userslib($dbTiki);
		
		foreach ($changes["data"] as $data)  {
			$item = new FeedItem(); 
			$item->title = $data["$titleId"]; 

			// 2 parameters to replace			
			if ($urlparam<>'') {
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
	function list_rss_modules($offset, $maxRecords, $sort_mode, $find) {

		if ($find) {
			$findesc="%" . $find . "%";
			$mid = " where (`name` like ? or `description` like ?)";
			$bindvars=array($findesc,$findesc);
		} else {
			$mid = "";
			$bindvars=array();
		}

		$query = "select * from `tiki_rss_modules` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_rss_modules` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
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
	function replace_rss_module($rssId, $name, $description, $url, $refresh, $showTitle, $showPubDate) {
		//if($this->rss_module_name_exists($name)) return false; // TODO: Check the name
		$refresh = 60 * $refresh;

		if ($rssId) {
			$query = "update `tiki_rss_modules` set `name`=?,`description`=?,`refresh`=?,`url`=?,`showTitle`=?,`showPubDate`=? where `rssId`=?";
			$bindvars=array($name,$description,$refresh,$url,$showTitle,$showPubDate,$rssId);
		} else {
			// was: replace into, no clue why.
			$query = "insert into `tiki_rss_modules`(`name`,`description`,`url`,`refresh`,`content`,`lastUpdated`,`showTitle`,`showPubDate`)
                values(?,?,?,?,?,?,?,?)";
			$bindvars=array($name,$description,$url,$refresh,'',1000000,$showTitle,$showPubDate);
		}

		$result = $this->query($query,$bindvars);
		return true;
	}

	/* remove rss feed from db */
	function remove_rss_module($rssId) {
		$query = "delete from `tiki_rss_modules` where `rssId`=?";

		$result = $this->query($query,array($rssId));
		return true;
	}

	/* read rss feed data from db */
	function get_rss_module($rssId) {
		$query = "select * from `tiki_rss_modules` where `rssId`=?";

		$result = $this->query($query,array($rssId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	/* parse xml data and return it in an array */
	function parse_rss_data($data, $rssId) {
		$showPubDate = $this->get_rss_showPubDate($rssId);
		$showTitle = $this->get_rss_showTitle($rssId);

		$news = array();

		//Only include the title if the option for doing that (showTitle) has been set.
		if ($showTitle=="y") {
			// get title and link of the feed:
			preg_match("/<title>(.*?)<\/title>/i", $data, $title);
			preg_match("/<link>(.*?)<\/link>/i", $data, $link);
                        
			// set "y" if title should be shown:
			$anew["isTitle"]=$showTitle;
			$anew["title"] = "";
			if (isset($title[1])) { $anew["title"] = $title[1]; }
			$anew["link"] = "";
			if (isset($link[1])) { $anew["link"] = $link[1]; }
			$news[] = $anew;
		}

		// get all items / entries of the feed:		
		preg_match_all("/<item[^s].*?>(.*?)<\/item>/msi", $data, $items);
		if (count($items[1])<1)				
			preg_match_all("/<entry.*?>(.*?)<\/entry>/msi", $data, $items);

		for ($it = 0; $it < count($items[1]); $it++) {
			// extract all the data we need:
			preg_match_all("/<title[^>]*>(<!\[CDATA\[)?(.*?)(\]\]>)?<\/title>/msi", $items[0][$it], $titles);
	 		preg_match_all("/<link>(.*?)<\/link>/msi", $items[0][$it], $links);
			if (count($links[1]) > 0) {
				preg_match_all("/<!\[CDATA\[(.*?)\]\]>/msi", $links[1][0], $links2);
				if (count($links2[1]) > 0) {
					$links[1][0] = $links2[1][0];
				}
		 	} else {
				preg_match_all("/<link.*?href=\"(.*?)\".*?>/msi", $items[0][$it], $links);
			}
	 		preg_match_all("/<description[^>]*>(<!\[CDATA\[)?(.*?)(\]\]>)?<\/description>/msi", $items[0][$it], $description);
			if (count($description[1])<1)
		 		preg_match_all("/<dc:description>(<!\[CDATA\[)?(.*?)(\]\]>)?<\/dc:description>/i", $items[0][$it], $description);
			if (count($description[1])<1)
				preg_match_all("/<content[^>]*>(<!\[CDATA\[)?(.*?)(\]\]>)?<\/content>/msi", $items[0][$it], $description);

			$pubdate = array();
			preg_match_all("/<dc:date>(.*?)<\/dc:date>/msi", $items[0][$it], $pubdate);
			if (count($pubdate[1])<1)				
				preg_match_all("/<pubDate>(.*?)<\/pubDate>/msi", $items[0][$it], $pubdate);
			if (count($pubdate[1])<1)				
				preg_match_all("/<issued>(.*?)<\/issued>/msi", $items[0][$it], $pubdate);
			if (count($pubdate[1])<1)				
				preg_match_all("/<published>(.*?)<\/published>/msi", $items[0][$it], $pubdate);

			preg_match_all("/<author>(.*?)<\/author>/msi", $items[0][$it], $author);
			if (count($author[1])<1)				
				preg_match_all("/<dc:creator>(.*?)<\/dc:creator>/msi", $items[0][$it], $author);

				$anew["title"] = '';
				if (isset($titles[2][0])) {
					$anew["title"] = $titles[2][0]; } //Because of the CDATA-matching, the index is off by 1.
				$anew["link"] = '';
				if (isset($links[1][0])) {
					$anew["link"] = $links[1][0];
				}
				$anew["author"] = '';
				if (isset($author[1][0])) {
					$anew["author"] = $author[1][0];
				}
				$anew["description"] = '';
				if (isset($description[2][0])) {
					$anew["description"] = $description[2][0]; //Because of the CDATA-matching, the index is off by 1.
				}
				$anew["pubDate"] = '';
				if ( isset($pubdate[1][0]) && ($showPubDate == 'y') )
				{
					$anew["pubDate"] = $pubdate[1][0];
				}
				$anew["isTitle"]="n";
				$news[] = $anew;
		}
		return $news;
	}

	/* refresh content of a certain rss feed */
	function refresh_rss_module($rssId) {
		$info = $this->get_rss_module($rssId);
		if ($info) {
			if (($gotit = $this->httprequest($info['url'])) !== false) {
				$data = $this->rss_iconv($gotit);
			} else {
				return false;
			}
			$query = "update `tiki_rss_modules` set `content`=?, `lastUpdated`=? where `rssId`=?";
			$result = $this->query($query,array((string)$data,(int) $this->now, $rssId));
			return $data;
		} else {
			return false;
		}
	}

	/* check if an rss feed name already exists */
	function rss_module_name_exists($name) {
		$query = "select `name` from `tiki_rss_modules` where `name`=?";

		$result = $this->query($query,array($name));
		return $result->numRows();
	}

	/* get rss feed id by name */
	function get_rss_module_id($name) {
		$query = "select `rssId` from `tiki_rss_modules` where `name`=?";

		$id = $this->getOne($query,array($name));
		return $id;
	}

	/* check if 'showTitle' for an rss feed is enabled */
	function get_rss_showTitle($rssId) {
		$query = "select `showTitle` from `tiki_rss_modules` where `rssId`=?";

		$showTitle = $this->getOne($query,array($rssId));
		return $showTitle;
	}

	/* check if 'showPubdate' for an rss feed is enabled */
	function get_rss_showPubDate($rssId) {
		$query = "select `showPubDate` from `tiki_rss_modules` where `rssId`=?";

		$showPubDate = $this->getOne($query,array($rssId));
		return $showPubDate;
	}

	/* retrieve the content of an rss feed, first try cache, then http request (may be forced) */
	function get_rss_module_content($rssId, $refresh=false) {
		$info = $this->get_rss_module($rssId);

		// cache too old, get data from feed and update cache
		if (($info["lastUpdated"] + $info["refresh"] < $this->now) || ($info["content"]=="") || $refresh) {
			$data = $this->refresh_rss_module($rssId);
		}

		// get from cache
		$info = $this->get_rss_module($rssId);
		return $info["content"];
	}

	/* encode rss feed content */
	function rss_iconv($xmlstr, $tencod = "UTF-8") {
		if (preg_match("/<\?xml.*encoding=\"(.*)\".*\?>/", $xmlstr, $xml_head)) {
			$sencod = strtoupper($xml_head[1]);

			switch ($sencod) {
			case "ISO-8859-1":
				// Use utf8_encode a more standard function
				$xmlstr = utf8_encode($xmlstr);

				break;

			case "UTF-8":
			case "US-ASCII":
				// UTF-8 and US-ASCII don't need convertion
				break;

			default:
				// Not supported encoding, we must use iconv() or recode()
				if (function_exists('iconv')) {
					// We have iconv use it
					$new_xmlstr = @iconv($sencod, $tencod, $xmlstr);

					if ($new_xmlstr === FALSE) {
						// in_encod -> out_encod not supported, may be misspelled encoding
						$sencod = strtr($sencod, array(
							"-" => "",
							"_" => "",
							" " => ""
						));

						$new_xmlstr = @iconv($sencod, $tencod, $xmlstr);

						if ($new_xmlstr === FALSE) {
							// in_encod -> out_encod not supported, leave it
							$tencod = $sencod;

							break;
						}
					}

					$xmlstr = $new_xmlstr;
					// Fix an iconv bug, a few garbage chars beyound xml...
					$xmlstr = preg_replace("/(.*<\/rdf:RDF>).*/s", "\$1", $xmlstr);
				} elseif (function_exists('recode_string')) {
					// I don't have recode support could somebody test it?
					$xmlstr = @recode_string("$sencod..$tencod", $xmlstr);
				} else {
				// This PHP intallation don't have any EncodConvFunc...
				// somebody could create tiki_iconv(...)?
				}
			}

			// Replace header, put the new encoding
			$xmlstr = preg_replace("/(<\?xml.*)encoding=\".*\"(.*\?>)/", "\$1 encoding=\"$tencod\"\$2", $xmlstr);
		}

		return $xmlstr;
	}
}
global $dbTiki;
$rsslib = new RSSLib($dbTiki);

?>
