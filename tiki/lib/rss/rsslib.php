<?php

class RSSLib extends TikiLib {
	function RSSLib($db) {
		# this is probably uneeded now
		if (!$db) {
			die ("Invalid db object passed to RSSLib constructor");
		}

		$this->db = $db;
	}

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

	function remove_rss_module($rssId) {
		$query = "delete from `tiki_rss_modules` where `rssId`=?";

		$result = $this->query($query,array($rssId));
		return true;
	}

	function get_rss_module($rssId) {
		$query = "select * from `tiki_rss_modules` where `rssId`=?";

		$result = $this->query($query,array($rssId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

	function startElementHandler($parser, $name, $attribs) {
		if ($this->flag) {
			// for Atom <link>s: ignore those with no HREF
			if ($name == 'link') {
			  if ((array_key_exists("href",$attribs)) && 
			     (array_key_exists("rel",$attribs)) &&
			  	 ($attribs["rel"] == "alternate")) {
			  	   $this->buffer .= '<' . $name . '>';
						 $this->buffer .= $attribs["href"];
				}
				// all tags except <link>:
			} else $this->buffer .= '<' . $name . '>';
		}

		if ($name == 'item' || $name == 'items' || $name == 'entry') {
			$this->flag = 1;
		}
	}

	function endElementHandler($parser, $name) {
		if ($name == 'item' || $name == 'items' || $name == 'entry') {
			$this->flag = 0;
		}

		if ($this->flag) {
			$this->buffer .= '</' . $name . '>';
		}
	}

	function characterDataHandler($parser, $data) {
		if ($this->flag) {
			$this->buffer .= $data;
		}
	}

	function NewsFeed($data, $rssId) {
		$news = array();

		$showPubDate = $this->get_rss_showPubDate($rssId);

		$this->buffer = '';
		$this->flag = 0;
		$this->parser = xml_parser_create("UTF-8");
		xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
		xml_set_object($this->parser, $this);
		xml_set_element_handler($this->parser, "startElementHandler", "endElementHandler");
		xml_set_character_data_handler($this->parser, "characterDataHandler");

		if (!xml_parse($this->parser, $data, 1)) {
			print ("Xml error: " . xml_error_string(xml_get_error_code($this->parser))."-".xml_get_current_line_number($this->parser). "<br />");

			return $news;
		}

		xml_parser_free ($this->parser);
		preg_match_all("/<title>(.*?)<\/title>/i", $this->buffer, $titles);
 		preg_match_all("/<link>(.*?)<\/link>/i", $this->buffer, $links);

		$pubdate = array();
		preg_match_all("/<dc:date>(.*?)<\/dc:date>/i", $this->buffer, $pubdate);
		if (count($pubdate[1])<1)				
		preg_match_all("/<pubDate>(.*?)<\/pubDate>/i", $this->buffer, $pubdate);
		if (count($pubdate[1])<1)				
		preg_match_all("/<issued>(.*?)<\/issued>/i", $this->buffer, $pubdate);

		for ($i = 0; $i < count($titles[1]); $i++) {
			$anew["title"] = $titles[1][$i];

			if (isset($links[1][$i])) {
				$anew["link"] = $links[1][$i];
			} else {
				$anew["link"] = '';
			}
			if ( isset($pubdate[1][$i]) && ($showPubDate == 'y') )
			{
				$anew["pubdate"] = $pubdate[1][$i];
			} else {
				$anew["pubdate"] = '';
			}
			$news[] = $anew;
		}
		return $news;
	}

	function parse_rss_data($rssdata, $rssId) {
		return $this->NewsFeed($rssdata, $rssId);
	}

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

	function rss_module_name_exists($name) {
		$query = "select `name` from `tiki_rss_modules` where `name`=?";

		$result = $this->query($query,array($name));
		return $result->numRows();
	}

	function get_rss_module_id($name) {
		$query = "select `rssId` from `tiki_rss_modules` where `name`=?";

		$id = $this->getOne($query,array($name));
		return $id;
	}

	function get_rss_showTitle($rssId) {
		$query = "select `showTitle` from `tiki_rss_modules` where `rssId`=?";

		$showTitle = $this->getOne($query,array($rssId));
		return $showTitle;
	}

	function get_rss_showPubDate($rssId) {
		$query = "select `showPubDate` from `tiki_rss_modules` where `rssId`=?";

		$showPubDate = $this->getOne($query,array($rssId));
		return $showPubDate;
	}

	function get_rss_module_content($rssId) {
		$info = $this->get_rss_module($rssId);
		$now = date("U");

		// cache too old, get data from feed and update cache
		if (($info["lastUpdated"] + $info["refresh"] < $now) || ($info["content"]=="")) {
			$data = $this->refresh_rss_module($rssId);
		}

		// get from cache
		$info = $this->get_rss_module($rssId);
		return $info["content"];
	}

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
