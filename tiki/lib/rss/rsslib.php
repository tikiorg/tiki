<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
}

class RSSLib extends TikiLib {
	function RSSLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to RSSLib constructor");
		}

		$this->db = $db;
	}

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

		// get title and link of the feed:
		preg_match("/<title>(.*?)<\/title>/i", $data, $title);
 		preg_match("/<link>(.*?)<\/link>/i", $data, $link);

		// set "y" if title should be shown:
		$anew["isTitle"]=$showTitle;
		$anew["title"] = $title[1];
		$anew["link"] = "";
		if (isset($link[1])) { $anew["link"] = $link[1]; }
		$news[] = $anew;
 		
		// get all items / entries of the feed:		
		preg_match_all("/<item[^s].*?>(.*?)<\/item>/ms", $data, $items);
		if (count($items[1])<1)				
			preg_match_all("/<entry.*?>(.*?)<\/entry>/ms", $data, $items);

		// get data from all items:
		for ($it = 0; $it < count($items[1]); $it++) {
		
			preg_match_all("/<title>(.*?)<\/title>/i", $items[0][$it], $titles);
	 		preg_match_all("/<link>(.*?)<\/link>/i", $items[0][$it], $links);
			if (count($links[1])<1)
		 		preg_match_all("/<link.*?href=\"(.*?)\".*?>/i", $items[0][$it], $links);

			$pubdate = array();
			preg_match_all("/<dc:date>(.*?)<\/dc:date>/i", $items[0][$it], $pubdate);
			if (count($pubdate[1])<1)				
				preg_match_all("/<pubDate>(.*?)<\/pubDate>/i", $items[0][$it], $pubdate);
			if (count($pubdate[1])<1)				
				preg_match_all("/<issued>(.*?)<\/issued>/i", $items[0][$it], $pubdate);

				$anew["title"] = $titles[1][0];
				$anew["link"] = '';
				if (isset($links[1][0])) {
					$anew["link"] = $links[1][0];
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
			$data = $this->rss_iconv($this->httpRequest($info['url']));
			$now = date("U");
			$query = "update `tiki_rss_modules` set `content`=?, `lastUpdated`=? where `rssId`=?";
			$result = $this->query($query,array((string)$data,(int) $now, (int) $rssId));
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
		$now = date("U");

		// cache too old, get data from feed and update cache
		if (($info["lastUpdated"] + $info["refresh"] < $now) || ($info["content"]=="") || $refresh) {
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

$rsslib = new RSSLib($dbTiki);

?>
