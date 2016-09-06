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

class HistLib extends TikiLib
{

	/* 
		*	Removes a specific version of a page
		*
		*/
	function remove_version($page, $version, $historyId = '')
	{
		global $prefs;
		if ($prefs['feature_contribution'] == 'y') {
			$contributionlib = TikiLib::lib('contribution');
			if ($historyId == '') {
				$query = 'select `historyId` from `tiki_history` where `pageName`=? and `version`=?';
				$historyId = $this->getOne($query, array($page, $version));
			}
			$contributionlib->remove_history($historyId);
		}
		$query = "delete from `tiki_history` where `pageName`=? and `version`=?";
		$result = $this->query($query, array($page,$version));
		$logslib = TikiLib::lib('logs');
		$logslib->add_action("Removed version", $page, 'wiki page', "version=$version");
		//get_strings tra("Removed version $version")
		return true;
	}

	function use_version($page, $version, $comment = '')
	{
		$this->invalidate_cache($page);
		
		// Store the current page in tiki_history before rolling back
		if (strtolower($page) != 'sandbox') {
			$info = $this->get_hist_page_info($page);
			$old_version = $info['version'] + 1;
		    $lastModif = $info["lastModif"];
		    $user = $info["user"];
		    $ip = $info["ip"];
		    $comment = $info["comment"];
		    $data = $info["data"];
		    $description = $info["description"];
			$query = "insert into `tiki_history`(`pageName`, `version`, `version_minor`, `lastModif`, `user`, `ip`, `comment`, `data`, `description`,`is_html`) values(?,?,?,?,?,?,?,?,?,?)";
		    $this->query($query, array($page,(int) $old_version, (int) $info["version_minor"],(int) $lastModif,$user,$ip,$comment,$data,$description, $info["is_html"]));
		}
		
		$query = "select * from `tiki_history` where `pageName`=? and `version`=?";
		$result = $this->query($query, array($page,$version));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		
		global $prefs;
		// add rollback comment to existing one (after truncating if needed)
		$ver_comment = " [" . tra("rollback version ") . $version . "]";
		$too_long = 200 - strlen($res["comment"] . $ver_comment);
		if ($too_long < 0) {
			$too_long -= 4;
			$res["comment"] = substr($res["comment"], 0, $too_long) . '...';
		}
		$res["comment"] = $res["comment"] . $ver_comment; 		
		
		$query = "update `tiki_pages` set `data`=?,`lastModif`=?,`user`=?,`comment`=?,`version`=`version`+1,`ip`=?, `description`=?, `is_html`=?";
		$bindvars = array($res['data'], $res['lastModif'], $res['user'], $res['comment'], $res['ip'], $res['description'], $res['is_html']);

		// handle rolling back once page has been edited in a different editor (wiki or wysiwyg) based on is_html in history
		if ($prefs['feature_wysiwyg'] == 'y' && $prefs['wysiwyg_optional'] == 'y' && $prefs['wysiwyg_memo'] == 'y') {
			if ($res['is_html'] == 1) {
				// big hack: when you move to wysiwyg you do not come back usually -> wysiwyg should be a column in tiki_history
				$info = $this->get_hist_page_info($page);
				$bindvars[] = $info['wysiwyg'];
			} else {
				$bindvars[] = 'n';
			}
			$query .= ', `wysiwyg`=?';
		}
		$query .= ' where `pageName`=?';
		$bindvars[] = $page;
		$result = $this->query($query, $bindvars);
		$query = "delete from `tiki_links` where `fromPage` = ?";
		$result = $this->query($query, array($page));
		$this->clear_links($page);
		$pages = $this->get_pages($res["data"], true);

		foreach ($pages as $a_page => $types) {
			$this->replace_link($page, $a_page, $types);
		}

		global $prefs;
		if ($prefs['feature_actionlog'] == 'y') {
			$logslib = TikiLib::lib('logs');
			$logslib->add_action("Rollback", $page, 'wiki page', "version=$version");
		}
		//get_strings tra("Changed actual version to $version");
		return true;
	}

	// Used to see a specific version of the page
	function get_view_date($date_str)
	{
		global $tikilib;

		if (!$date_str) {
			// Date is undefined
			throw new Exception();
		}

		$view_date = $date_str;
		$tsp = explode('-', $date_str);

		if (count($tsp) == 3) {
			// Date in YYYY-MM-DD format
			$view_date = $tikilib->make_time(23, 59, 59, $tsp[1] + 1, $tsp[2], $tsp[0] + 1900);
		}

		return $view_date;
	}

	function get_user_versions($user)
	{
		$query
			= "select `pageName`,`version`, `lastModif`, `user`, `ip`, `comment` from `tiki_history` where `user`=? order by `lastModif` desc";

		$result = $this->query($query, array($user));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();

			$aux["pageName"] = $res["pageName"];
			$aux["version"] = $res["version"];
			$aux["lastModif"] = $res["lastModif"];
			$aux["ip"] = $res["ip"];
			$aux["comment"] = $res["comment"];
			$ret[] = $aux;
		}

		return $ret;
	}

	// Returns information about a specific version of a page
	function get_version($page, $version)
	{
		//fix for encoded slowly without doing it all at once in the installer upgrade script
		$wikilib = TikiLib::lib('wiki');
		$converter = new convertToTiki9();
		$converter->convertPageHistoryFromPageAndVersion($page, $version);

		$query = "select * from `tiki_history` where `pageName`=? and `version`=?";
		$result = $this->query($query, array($page,$version));
		$res = $result->fetchRow();
		return $res;
	}

	// Get page info for a specified version
	function get_hist_page_info($pageName, $version = null)
	{
		$info = parent::get_page_info($pageName);

		if (empty($version)) {
			// No version = last version
			return $info;
		}

		if (!$info) {
			// Page does not exist
			return false;
		}

		$old_info = $this->get_version($pageName, $version);

		if ($old_info == null) {
			// History does not exist
			if ($version == $this->get_page_latest_version($pageName) + 1) {
				// Last version
				return $info;
			}

			throw new Exception();
		}

		// Override parameters with versioned data
		$info['data'] = $old_info['data'];
		$info['version'] = $old_info['version'];
		$info['last_version'] = $info['version'];
		$info["user"] = $old_info["user"];
		$info["ip"] = $old_info["ip"];
		$info["description"] = $old_info["description"];
		$info["comment"] = $old_info["comment"];
		$info["is_html"] = $old_info["is_html"];
		$info['lastModif'] = $old_info["lastModif"];
		$info['page_size'] = strlen($old_info['data']);

		return $info;
	}

	// Returns all the versions for this page
	// without the data itself
	function get_page_history($page, $fetchdata=true, $offset = 0, $limit = -1)
	{
		global $prefs;

		$query = "select * from `tiki_history` where `pageName`=? order by `version` desc";
		$result = $this->query($query, array($page), $limit, $offset);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();

			$aux["version"] = $res["version"];
			$aux["lastModif"] = $res["lastModif"];
			$aux["user"] = $res["user"];
			$aux["ip"] = $res["ip"];
			if ($fetchdata==true) $aux["data"] = $res["data"];
			$aux["pageName"] = $res["pageName"];
			$aux["description"] = $res["description"];
			$aux["comment"] = $res["comment"];
			$aux["is_html"] = $res["is_html"];
			//$aux["percent"] = levenshtein($res["data"],$actual);
			if ($prefs['feature_contribution'] == 'y') {
				$contributionlib = TikiLib::lib('contribution');
				$aux['contributions'] = $contributionlib->get_assigned_contributions($res['historyId'], 'history');
				$logslib = TikiLib::lib('logs');
				$aux['contributors'] = $logslib->get_wiki_contributors($aux);
			}
			$ret[] = $aux;
		}

		return $ret;
	}
	
	// Returns one version of the page from the history
	// without the data itself (version = 0 now returns data from current version)
	function get_page_from_history($page,$version,$fetchdata=false)
	{
		$wikilib = TikiLib::lib('wiki');
		$converter = new convertToTiki9();
		$converter->convertPageHistoryFromPageAndVersion($page, $version);

		if ($fetchdata==true) {
			if ($version > 0)
				$query = "select `pageName`, `description`, `version`, `lastModif`, `user`, `ip`, `data`, `comment`, `is_html` from `tiki_history` where `pageName`=? and `version`=?";				
			else
				$query = "select `pageName`, `description`, `version`, `lastModif`, `user`, `ip`, `data`, `comment`, `is_html` from `tiki_pages` where `pageName`=?";
		} else {
			if ($version > 0)
				$query = "select `pageName`, `description`, `version`, `lastModif`, `user`, `ip`, `comment`, `is_html` from `tiki_history` where `pageName`=? and `version`=?";
			else
				$query = "select `pageName`, `description`, `version`, `lastModif`, `user`, `ip`, `comment`, `is_html` from `tiki_pages` where `pageName`=?";
		}
		if ($version > 0)
			$result = $this->query($query, array($page,$version));
		else
			$result = $this->query($query, array($page));
			
		$ret = array();

		while ($res = $result->fetchRow()) {
			$aux = array();

			$aux["version"] = $res["version"];
			$aux["lastModif"] = $res["lastModif"];
			$aux["user"] = $res["user"];
			$aux["ip"] = $res["ip"];
			if ($fetchdata==true) $aux["data"] = $res["data"];
			$aux["pageName"] = $res["pageName"];
			$aux["description"] = $res["description"];
			$aux["comment"] = $res["comment"];
			$aux["is_html"] = $res["is_html"];
			//$aux["percent"] = levenshtein($res["data"],$actual);
			$ret[] = $aux;
		}

		return empty($ret)?$ret: $ret[0];
	}

	/**
	 * note that this function returns the latest but one version in the
	 * history db table, which is one less than the current version
	 *
	 * @param string $page			page name
	 * @param string $sort_mode		default version_desc
	 * @return bool int
	 */

	function get_page_latest_version($page, $sort_mode='version_desc')
	{

		$query = "select `version` from `tiki_history` where `pageName`=? order by ".$this->convertSortMode($sort_mode);
		$result = $this->query($query, array($page), 2);
		$ret = false;
		
		if ($res = $result->fetchRow()) {
			if ($res = $result->fetchRow()) {
				$ret = $res['version'];
			}
		}

		return $ret;
	}

	function version_exists($pageName, $version)
	{

		$query = "select `pageName` from `tiki_history` where `pageName` = ? and `version`=?";
		$result = $this->query($query, array($pageName,$version));
		return $result->numRows();
	}

	// This function get the last changes from pages from the last $days days
	// if days is 0 this gets all the registers
	function get_last_changes($days, $offset = 0, $limit = -1, $sort_mode = 'lastModif_desc', $findwhat = '')
	{
	        global $user;

		$bindvars = array();
		$categories = $this->get_jail();
		if (!isset($categjoin)) $categjoin = '';
		if ($categories) {
			$categjoin .= "inner join `tiki_objects` as tob on (tob.`itemId`= ta.`object` and tob.`type`= ?) inner join `tiki_category_objects` as tc on (tc.`catObjectId`=tob.`objectId` and tc.`categId` IN(" . implode(', ', array_fill(0, count($categories), '?')) . ")) ";
			$bindvars = array_merge(array('wiki page'), $categories);
		}

		$where = "where true ";
		if ($findwhat) {
			$findstr='%' . $findwhat . '%';
			$where.= " and ta.`object` like ? or ta.`user` like ? or ta.`comment` like ?";
			$bindvars = array_merge($bindvars, array($findstr,$findstr,$findstr));
		}

		if ($days) {
			$toTime = $this->make_time(23, 59, 59, $this->date_format("%m"), $this->date_format("%d"), $this->date_format("%Y"));
			$fromTime = $toTime - (24 * 60 * 60 * $days);
			$where .= " and ta.`lastModif`>=? and ta.`lastModif`<=? ";
			$bindvars[] = $fromTime;
			$bindvars[] = $toTime;
		}

		// WARNING: This assumes the current version of each page will be found in tiki_history
		$query = "select distinct ta.`action`, ta.`lastModif`, ta.`user`, ta.`ip`, ta.`object`, thf.`comment`, thf.`version`, thf.`page_id` from `tiki_actionlog` ta 
			inner join (select th.`version`, th.`comment`, th.`pageName`, th.`lastModif`, tp.`page_id` from `tiki_history` as th LEFT OUTER JOIN `tiki_pages` tp ON tp.`pageName` = th.`pageName` AND tp.`version` = th.`version`) as thf on ta.`object`=thf.`pageName` and ta.`lastModif`=thf.`lastModif` and ta.`objectType`='wiki page' " . $categjoin . $where . " order by ta.".$this->convertSortMode($sort_mode);

		// TODO: Optimize. This fetches all records just to be able to give a count.
		$result = Perms::filter(array( 'type' => 'wiki page' ), 'object', $this->fetchAll($query, $bindvars), array( 'object' => 'object' ), 'view');
		$cant = count($result);
		$ret = array();
		
		if ($limit == -1) {
			$result = array_slice($result, $offset);
		} else {
			$result = array_slice($result, $offset, $limit);
		}
		foreach ($result as $res ) {
			$res['current'] = isset($res['page_id']);
			$res['pageName'] = $res['object'];
			$ret[] = $res;
		}

		return array('data' => $ret, 'cant' => $cant);
	}
	function get_nb_history($page)
	{
		$query_cant = "select count(*) from `tiki_history` where `pageName` = ?";
		$cant = $this->getOne($query_cant, array($page));
		return $cant;
	}
	
	// This function gets the version number of the version before or after the time specified
	// (note that current version is not included in search)
	function get_version_by_time($page, $unixtimestamp, $before_or_after = 'before', $include_minor = true)
	{
		$query = "select `version`, `version_minor`, `lastModif` from `tiki_history` where `pageName`=? order by `version` desc";
		$result = $this->query($query, array($page));
		$ret = array();
		$version = 0;
		while ($res = $result->fetchRow()) {
			$aux = array();
			$aux["version"] = $res["version"];
			$aux["version_minor"] = $res["version_minor"];
			$aux["lastModif"] = $res["lastModif"];
			$ret[] = $aux;
		}
		foreach ($ret as $ver) {
			if ($ver["lastModif"] <= $unixtimestamp && ($include_minor || $ver["version_minor"] == 0)) {
				if ($before_or_after == 'before') { 
					$version = (int) $ver["version"];
					break;
				} elseif ($before_or_after == 'after') {
					break;
				}
			}
			if ($before_or_after == 'after' && ($include_minor || $ver["version_minor"] == 0)) {
				$version = (int) $ver["version"];				
			}		
		}
		return max(0, $version);		
	}
}

/**
 * 
 * This class represents a structured view (per word) on a document. Feeding it with additional references, it can be used to generate a
 * complete view of the document including changes made over time (like the "Track changes" in some word processing programs). A statistics
 * of the different authors contributions can be generated as well
 * 
 * @author cdrwhite
 * @since 6.0
 */
class Document
{
	
	/**
	 * @var	array	a list of words and whitespaces represented by an array(word,author,deleted,diffid,[deleted_by])
	 */
	private $_document;
	
	/**
	 * @var array	array of statistical data grouped by author each represented by an array(words,deleted_words,whitespaces,deleted_whitespaces,characters,deleted_characters,printables,deleted_printables)
	 * @see getStatistics
	 */
	private $_statistics;
	
	/**
	 * @var array 	sum of all statistics for all authors, generated by getStatistics, retrieved by getTotal()
	 * @see getTotal;
	 */
	private $_total;
	
	/**
	 * @var string	filter used in getStatistics to distinguish between characters and printable characters
	 * @see getStatistics
	 */
	private $_filter;
	
	/**
	 * @var int	processing settings 
	 */
	private $_process=1;
		
	/**
	 * @var bool	should the page contents be parsed (HTML instead of WIKI text) 
	 */
	private $_parsed;
	
	/**
	 * @var bool	should the html tags be stripped from the parsed contents 
	 */
	private $_nohtml;
	
	/**
	 * @var string	start marker. If set, text before this marker (including the marker itself) will be removed
	 */
	var $startmarker='';
	
	/**
	 * @var string	end marker. If set, text after this marker (including the marker itself) will be removed
	 */
	var $endmarker='';
	
	/**
	 * @var string	regex for splitting page text into an array of words;
	 */
	private $_search="#(\[[^\[].*?\]|\(\(.*?\)\)|(~np~\{.*?\}~/np~)|<[^>]+>|[,\"':\s]+|[^\s,\"':<]+|</[^>]+>)#";
	
	/**
	 * @var array	Page info
	 */
	private $_info;
	
	/**
	 * @var array	complete page history
	 */
	private $_data;

	/**
	 * 
	 * Initializing Internal variables for getStatistics and getTotals and adding the first page to the document 
	 * @param string	$page		Name of the page to include
	 * @param int		$lastversion	>0 uses the version specified (or last page, if this is greater than the version of the last page) =0 uses the latest(current) version, <0 means a timestamp (lastModif has to be before that)
	 * @param int		$process	0 = don't parse (take original wiki text and count wiki tags/plugins), 1 = parse (take html as base), 2 = parse and strip html tags
	 * @param string	$start		start marker (all text will be skipped, including this marker which must be at the beginning of a line)
	 * @param string	$end		end marker (all text will be skipped from this marker on, including this marker which must be at the beginning of a line)
	 */
	function __construct($page, $lastversion=0, $process=1, $showpopups=true, $startmarker='', $endmarker='')
	{
		$histlib = TikiLib::lib('hist');

		$this->_document=array();
		$this->_history=false;
		$this->_filter='/([[:blank:]]|[[:cntrl:]]|[[:punct:]]|[[:space:]])/';		
		$this->_parsed=true;
		$this->_nohtml=false;
		$this->_showpopups=$showpopups;
		switch($process) {
			case 0: $this->_parsed=false;
					$this->_process=0;
   				break;
			case 2: $this->_nohtml=true;
					$this->_process=2;
   				break;
		}
		$this->startmarker=$startmarker;
		$this->endmarker=$endmarker;

		$this->_info=$histlib->get_hist_page_info($page, true);
		if ($lastversion==0) {
			$lastversion=$this->_info['version'];		
		}
		$this->_data=array();
		$this->_data=array(array(
				'version'		=> $this->_info['version'],
				'lastModif'		=> $this->_info['lastModif'],
				'user' 			=> $this->_info['user'],
				'ip' 			=> $this->_info['ip'],
				'pageName' 		=> $page,
				'description' 	=> $this->_info['description'],
				'comment' 		=> $this->_info['comment'],
				'data'			=> $this->_info['data'],
			));
		$this->_data=array_merge($this->_data, $histlib->get_page_history($page, true, 0, -1));
		$next=count($this->_data)-1;
		$author=$this->_data[$next]['user'];
		$next=$this->getLastAuthorText($author, $next, $lastversion);
		if ($next==-1) {	// all pages from the same author, no need to diff
			$index=$this->getIndex($lastversion);
		} else {
			$index=$next;
		}
		$source=$this->removeText($this->_data[$index]['data']);
		$source=preg_replace(array('/\{AUTHOR\(.+?\)\}/','/{AUTHOR\}/','/\{INCLUDE\(.+?\)\}\{INCLUDE\}/'), ' ~np~$0~/np~', $source);
		if ($this->_parsed) {
			$source=$histlib->parse_data($source, array('suppress_icons'=>true));
		}
		if ($this->_nohtml) {
			$source=strip_tags($source);
		}
		preg_match_all($this->_search, $source, $out, PREG_PATTERN_ORDER);
		$words=$out[0];
		$this->_document=$this->addWords($this->_document, $words, $author);
		if ($next==-1) {
			return;
		}
		do {
			$author=$this->_data[$next-1]['user'];
			$next=$this->getLastAuthorText($author, $next-1, $lastversion);
			if ($next==-1) {
				$index=$this->getIndex($lastversion);
			} else {
				$index=$next;
			}
			$newpage=$this->removeText($this->_data[$index]['data']);
			$this->mergeDiff($newpage, $author);
		} while ($next>0);
		$this->parseAuthorAndInclude();
		
	}
	
	/**
	 * 
	 * Removes all text before the first occurrence of start marker and after the last occurrence of the end marker
	 * This copies the original behaviour of the wikiplugin_include even though it could be done with a regex in fewer lines
	 * @param	string $text	contains the whole text
	 * @return	string			returns the text inside the markers
	 */
	private function removeText($text)
	{
		$start=($this->startmarker!='');
		$stop=($this->endmarker!='');
		if ($start || $stop) {
			$explText = explode("\n", $text);
			if ($start && $stop) {
				$state = 0;
				foreach ($explText as $i => $line) {
					if ($state == 0) {
						// Searching for start marker, dropping lines until found
						unset($explText[$i]);	// Drop the line
						if (0 == strcmp($this->startmarker, trim($line))) {
							$state = 1;	// Start retaining lines and searching for stop marker
						}
					} else {
						// Searching for stop marker, retaining lines until found
						if (0 == strcmp($this->endmarker, trim($line))) {
							unset($explText[$i]);	// Stop marker, drop the line
							$state = 0; 		// Go back to looking for start marker
						}
					}
				}
			} elseif ($start) {
				// Only start marker is set. Search for it, dropping all lines until it is found.
				foreach ($explText as $i => $line) {
					unset($explText[$i]); // Drop the line
					if (0 == strcmp($this->startmarker, trim($line))) {
						break;
					}
				}
			} else {
				// Only stop marker is set. Search for it, dropping all lines after it is found.
				$state = 1;
				foreach ($explText as $i => $line) {
					if ($state == 0) {
						// Dropping lines
						unset($explText[$i]);
					} else {
						// Searching for stop marker, retaining lines until found
						if (0 == strcmp($this->endmarker, trim($line))) {
							unset($explText[$i]);	// Stop marker, drop the line
							$state = 0; 		// Start dropping lines
						}
					}
				}
			}	
			$text = implode("\n", $explText);
		}
		return $text;
	}

	/**
	 * 
	 * get the id of the last text of the given author
	 * @param string	$author		name of the current author
	 * @param int		$start		start index
	 * @param int		$lastversion	last version to check, assuming all versions, if none is provided
	 * @return	int					id of the first text of a different author or -1 if there is none
	 * @see get_page_history_all
	 */
	private function getLastAuthorText($author, $start=-1, $lastversion=-1)
	{
		if ($start==-1) {
			return $start;
		}
		if ($start<0) {
			$start=count($this->_data)-1;	
		}
		if ($lastversion==-1) {
			$lastversion=$this->_data[0]['version'];
		}
		$i=$start;
		while ($i>=0 and $this->_data[$i]['user']==$author and $this->_data[$i]['version']<=$lastversion) {
			$i--;
		}
		$i++;
		if ($this->_data[$i]['version']>=$lastversion) {
			$i=-1;	
		}
		return $i;
	}
	
	/**
	 * 
	 * gets the index position of the requested version in the data array  
	 * @param int	$version
	 */
	private function getIndex($version)
	{
		for ($i=count($this->_data)-1;$i>=0;$i--) {
			if ($this->_data[$i]['version']==$version) {
				return $i;
			}
		}
		return -1;
	}
	
	/**
	 * 
	 * returns the history (identical to $histlib->get_page_history, but saves another fetch from database as we already have the info
	 */
	function getHistory()
	{
		return array_slice($this->_data, 1);
	}

	/**
	 * 
	 * returns the page info history (identical to $tikilib->get_page_info, but saves another fetch from database as we already have the info
	 */
	function getInfo()
	{
		return $this->_info;
	}
	
	
	/**
	 * 
	 * Generates an array of words from the internal document structure, which can be used by the diff class.
	 * The internal document structure will be modified to allow mergeDiff to integrate a new page with the current page without losing any information 
	 * @see mergeDiff
	 * @return	array	list of words in the document (no author etc.)
	 */
	function getDiffArray()
	{
		$diffarray=array();
		foreach ($this->_document as &$word) {
			if (!$word['deleted']) {
				$word['diffid']=count($diffarray);
				$diffarray[]=$word['word'];
			} else {
				$word['diffid']=-1;
			}
		}
		return $diffarray;
	}
	
	/**
	 * 
	 * Generates a statistics per author, the totals can be retrieved via getTotal
	 * @see		getTotal
	 * @param	string	$filter		regex to filter out non printable characters (difference between characters and printables)
	 * @return	array				array indexed by author containing arrays with statistics (words, deleted_words, whitespaces, deleted_whitespaces, characters, deleted_characters, printables, deleted_printables)
	 */
	function getStatistics($filter='/([[:blank:]]|[[:cntrl:]]|[[:punct:]]|[[:space:]])/')
	{
		$style=0;
		if ($this->_filter!=$filter) { //a new filter invalidates the statistics
			$this->_statistics=false;
			$this->_filter=$filter;
		}
		if ($this->_statistics!=false) return $this->_statistics; //there is already a history for the current state
		$this->_statistics=array();
		$this->_total=array(
					'words' => 0,
					'deleted_words' => 0,
					'whitespaces' => 0,
					'deleted_whitespaces' => 0,
					'characters'	=> 0,
					'deleted_characters' => 0,
					'printables' =>0,
					'deleted_printables' => 0,
				);
		
		foreach ($this->_document as $word) {
			$author=$word['author'];
			if (!isset($this->_statistics[$author])) {
				$this->_statistics[$author]=array(
					'words' => 0,
					'words_percent' => 0,
					'deleted_words' => 0,
					'deleted_words_percent' => 0,
					'whitespaces' => 0,
					'whitespaces_percent' => 0,
					'deleted_whitespaces' => 0,
					'deleted_whitespaces_percent' => 0,
					'characters'	=> 0,
					'characters_percent' => 0,
					'deleted_characters' => 0,
					'deleted_characters_percent' => 0,
					'printables' =>0,
					'printables_percent' => 0,
					'deleted_printables' => 0,
					'deleted_printables_percent' => 0,
					'style' => "author$style",
				);
				$style++;
				if ($style>15) $style=0;
			} //isset author
			if ($word['deleted']) {
				$prefix='deleted_';
			} else {
				$prefix='';
			}
			$w=$word['word'];
			if ($this->_nohtml) {
				$w=strip_tags($w);
			}
			if (trim($w)=='') {
				$this->_statistics[$author][$prefix.'whitespaces']++;
				$this->_total[$prefix.'whitespaces']++;
			} else {
				$this->_statistics[$author][$prefix.'words']++;
				$this->_total[$prefix.'words']++;
			}
			$l=mb_strlen($w);
			$this->_statistics[$author][$prefix.'characters']+=$l;
			$this->_total[$prefix.'characters']+=$l;
			$l=mb_strlen(preg_replace($this->_filter, '', $w));
			$this->_statistics[$author][$prefix.'printables']+=$l;
			$this->_total[$prefix.'printables']+=$l;
		} //foreach
		//calculate percentages
		foreach ($this->_statistics as &$author) {
			$author['words_percent']=$author['words']/$this->_total['words'];
			$author['deleted_words_percent']=($this->_total['deleted_words']!=0?$author['deleted_words']/$this->_total['deleted_words']:0);
			$author['whitespaces_percent']=$author['whitespaces']/$this->_total['whitespaces'];
			$author['deleted_whitespaces_percent']=($this->_total['deleted_whitespaces']!=0?$author['deleted_whitespaces']/$this->_total['deleted_whitespaces']:0);
			$author['characters_percent']=$author['characters']/$this->_total['characters'];
			$author['deleted_characters_percent']=($this->_total['deleted_characters']!=0?$author['deleted_characters']/$this->_total['deleted_characters']:0);
			$author['printables_percent']=$author['printables']/$this->_total['printables'];
			$author['deleted_printables_percent']=($this->_total['deleted_printables']!=0?$author['deleted_printables']/$this->_total['deleted_printables']:0);
		}
		return $this->_statistics;
	}
	
	/**
	 * 
	 * gets the totals from a previous getStatistics call
	 * @see		getStatistics
	 * @return	array with statistics (words, deleted_words, whitespaces, deleted_whitespaces, characters, deleted_characters, printables, deleted_printables)
	 */
	function getTotal()
	{
		return $this->_total;
	}
	
	/**
	 * 
	 * Retrieves the document data in different formats, 
	 * @param string $type		can be one of 'words' (array of words/whitespaces), 'text' (unformatted string), 'wiki' (string with wikiplugin AUTHOR tags to show the authors) or the default empty string '' (returns the internal document structure)
	 * @param array	 $options	array containing the filter specific options:
	 * <table>
	 * <tr><th>Type</th><th>Name</th><th>Applicable for</th><th>Purpose</th></tr>
	 * <tr><td>bool</td><td>showpopups</td><td>wiki</td><td>renders popups, defaults to true</td></tr>
	 * <tr><td>bool</td><td>escape</td><td>text/wiki</td><td>Escapes brackets and htmlspecialchars</td></tr>
	 * </table> 
	 * @return	array|string	depending on the parameter $type, a string or array containing the documents words
	 */
	function get($type='',$options=array())
	{
		switch($type) {
			case 'words':
				$words=array();
				foreach ($this->_document as $word) {
					$words[]=$word['word'];
				}
				return $words;
    			break;
			case 'text':
				$text='';
				foreach ($this->_document as $word) {
					$text.=$word['word'];
				}
				return $text;
				if ($options['escape']) {
					if (!$this->_parsed) {
						$text='~np~' . 
						      preg_replace(array('/\~np\~/', '//\~\/np\~/'), array('&#126;np&#126;','&#126;/np&#126;;'), $text) . 
						      '~/np~';
					}
					$text=preg_replace(array('/</','/>/'), array('&lt;','&gt;'), $text);					
				}
    			break;
			case 'wiki':
				$text='';
				$author='';
				$deleted=0;
				$deleted_by='';
				if (isset($options['showpopups'])) {
					$showpopups=$options['showpopups'];
				} else {
					$showpopups=true;
				}
				foreach ($this->_document as $word) {
					$skip=false;
					$d=isset($word['deleted_by'])?$word['deleted_by']:'';
					$w=$word['word'];
					if ($author!=$word['author'] or $deleted!=$word['deleted'] or $deleted_by!=$d) {
						if ($text!='') {
							if ($options['escape']) {
								$text.='~/np~';
							}
							$text.='{AUTHOR}';	
						}
						$author=$word['author'];
						$deleted=$word['deleted'];
						$deleted_by=$d;
						$text.="{AUTHOR(author=\"$author\"" . 
								($deleted?",deleted_by=\"$deleted_by\"":'') .
								',visible="1"' . 
								($showpopups?', popup="1"':'') . 
								')}';
						if ($options['escape']) {
							$text.="~np~";
						}
					}
					if (!$options['escape']) {
						if ($this->_parsed and !$this->_nohtml) { // skipping popups for links
							if (substr($w, 0, 3)=='<a ') {
								$text.='{AUTHOR}';
							}
							if (substr($w, -4)=='</a>') {
								$text.=$w . "{AUTHOR(author=\"$author\"" . 
									   ($deleted?",deleted_by=\"$deleted_by\"":'') . 
									   ',visible="1", ' .
									   ($showpopups?', popup="1"':'') .
									   ')}';
								$skip=true;
							}
						}
					} else { //escape existing tags
						if (!$this->_parsed) { 
					      	$w=preg_replace(array('/\~np\~/', '/\~\/np\~/'), array('&#126;np&#126;','&#126;/np&#126;'), $w);
						}
						$w=preg_replace(array('/</','/>/'), array('&amp;lt;','&amp;gt;'), $w); //double encode!	
					}
					if (strlen($w)==0 and !$this->_parsed) {
						$text.="\n";
					} else {				
						if (!$skip) {
							$text.=$w;	
						}
					}
				} // foreach
				if ($options['escape']) {
					$text.="~/np~";
				}
				$text.="{AUTHOR}";
				return $text;
    			break;
			default:			
				return $this->_document;			
		}
	}
	
	/**
	 * 
	 * Adds the supplied list of words to the provided document structure
	 * @param array		$doc		a list of words (arrays containing word, author, deleted, diffid, optionally deleted_by and statistical data) where the new words will be added to 
	 * @param array		$list		array of words/whitespaces to add to the document
	 * @param string	$author		name of the author to credit
	 * @return						provided document structure $doc with the words from $list appended
	 */
	private function addWords($doc, $list, $author, $deleted=false, $deleted_by='')
	{
		$newdoc=$doc;
		foreach ($list as $word) {
			$newword=array(
							'word'		=> $word,
							'author'	=> $author,
							'deleted'	=> $deleted,
							'diffid'	=> -1,
							);
			if ($deleted) {
				$newword['deleted_by']=$deleted_by;	
			}
			$newdoc[]=$newword;
		}
		return $newdoc;
	}
	
	/**
	 * 
	 * moves a nuber of words from the b eginning of this document to the provided document structure
	 * @param array 	$doc		a list of words (arrays containing word, author, deleted, diffid, optionally deleted_by and statistical data) where the new words will be appended to
	 * @param int		$pos		number of characters to move from the current documents beginning to the new list, deleted words which have a negative diff id wille be moved but not counted
	 * @param array		$list		list of words to move
	 * @param bool		$setDeleted	mark the moved words as deleted, if not already deleted
	 * @param string	$deletedBy	name of the author who deleted the words
	 */
	private function moveWords(&$doc, &$pos, $list, $deleted=false, $deleted_by='')
	{		
		$pos+=count($list);
		// get the words from the old document
		$i=0;
		while ($i<count($this->_document) and $this->_document[$i]['diffid']<$pos) {
			$word=$this->_document[$i];
			if ($deleted) {
				if (!$word['deleted']) {
					$word['deleted']=true;
					$word['deleted_by']=$deleted_by;
				}
			}
			$doc[]=$word;
			$i++;
		}
		//take care of deleted words
		while ($i<count($this->_document) and $this->_document[$i]['diffid']<0) {
			$word=$this->_document[$i];
			$doc[]=$word;
			$i++;
		}
		$this->_document=array_slice($this->_document, $i);
	}
		
	/**
	 * 
	 * Returns an indexed array containing the plugins parameters indexed by key name
	 * @param string	$pluginstr		Complete Plugin tag including brackets () containing the parameters
	 * @return	array|bool				Array containing the parameters or false if none are given
	 */
	function retrieveParams($pluginstr)
	{
		$params=array();
		$start=strpos($pluginstr, '(');
		if ($start===false) return false;
		$end=strrpos($pluginstr, ')');
		if ($end===false) return false;
		$pstr=substr($pluginstr, $start+1, $end-$start-1);
		$plist=explode(',', $pstr);
		foreach ($plist as $paramstr) {
			$p=explode('=', trim($paramstr));
			$params[strtolower(trim($p[0]))]=preg_replace('/^"|^\&quot;|"$|\&quot;$/', '', trim($p[1]));
		}
		return $params;
	}

	/**
	 * 
	 * merges a newer version of a page into the current document
	 * @param string	$newpage	a string with a later version of the page
	 * @param string	$newauthor	name of the author of the new version
	 */
	function mergeDiff($newpage, $newauthor)
	{
		$tikilib = TikiLib::lib('tiki');
		$this->_history=false;
		$author=$newauthor;
		$deleted=false;
		$deleted_by='';
		$newdoc=array();
		$page=preg_replace(array('/\{AUTHOR\(.+?\)\}/','/{AUTHOR\}/','/\{INCLUDE\(.+?\)\}\{INCLUDE\}/'), ' ~np~$0~/np~', $newpage);
		if ($this->_parsed) {
			$page=$tikilib->parse_data($page, array('suppress_icons'=>true));
			$page=preg_replace(array('/\{AUTHOR\(.+?\)\}/','/{AUTHOR\}/','/\{INCLUDE\(.+?\)\}\{INCLUDE\}/'), ' ~np~$0~/np~', $page);
		}
		if ($this->_nohtml) {
			$page=strip_tags($page);
		}
		preg_match_all($this->_search, $page, $out, PREG_PATTERN_ORDER);
		$new=$out[0];
		$z = new Text_Diff($this->getDiffArray(), $new);
		$pos=0;
		foreach ($z->getDiff() as $element) {
			if (is_a($element, 'Text_Diff_Op_copy')) {
				$this->moveWords($newdoc, $pos, $element->orig, $deleted, $deleted_by);
			} else {
				if (is_a($element, 'Text_Diff_Op_add')) {
					$newdoc=$this->addWords($newdoc, $element->final, $author, $deleted, $deleted_by);
				} else {
					if (is_a($element, 'Text_Diff_Op_delete')) {
						$this->moveWords($newdoc, $pos, $element->orig, $deleted, $author);
					} else { //change
						$newdoc=$this->addWords($newdoc, $element->final, $author, $deleted, $deleted_by);
						$this->moveWords($newdoc, $pos, $element->orig, true, $author);
					} //delete
				} // add
			} // copy
		} // foreach diff
		$this->_document=$newdoc;
	}

	/**
	 * 
	 * Kills double whitespaces in parseAuthor before/after {author} tags
	 * @param array	$newdoc	array containing the new document
	 * @param int	$index	position in the old document
	 */
	private function killDoubleWhitespaces(&$newdoc, &$index)
	{
		if (count($newdoc)>2) {
			$w1=$newdoc[count($newdoc)-1]['word'];
			$w2=$newdoc[count($newdoc)-2]['word'];
			if ($this->_nohtml) {
				$w1=strip_tags($w1);
				$w2=strip_tags($w2);
			}
			if (trim($w1)=='' and trim($w2)=='') {
				array_pop($newdoc); // kill one of the whitespaces
			}
		}
		if ($index<count($this->_document)-2) {
			$w1=$this->_document[$index+1]['word'];
			$w2=$this->_document[$index+2]['word'];
			if ($this->_nohtml) {
				$w1=strip_tags($w1);
				$w2=strip_tags($w2);
			}
			if (trim($w1)=='' and trim($w2)=='') {
				$index++; // jump over one of the whitespaces
			}			
		}
	}
	
	/**
	 * 
	 * parses the left over author/include tags and sets the author accordingly
	 */
	function parseAuthorAndInclude()
	{
		$newdoc=array();
		$author='';
		$deleted_by='';
		for ($index=0, $cdoc = count($this->_document); $index < $cdoc; $index++) {
			$word=$this->_document[$index];
			if (preg_match('/\{AUTHOR\(.+?\)\}/', $word['word'])) {
				$params=$this->retrieveParams($word['word']);
				$author=$params['author'];
				if (isset($params['deleted_by'])) {
					$deleted_by=$params['deleted_by'];
				}
				// manage double whitespace before and after
				$this->killDoubleWhitespaces($newdoc, $index);
			} elseif (preg_match('/\{AUTHOR\}/', $word['word'])) {
				$author='';
				$deleted_by='';
				$this->killDoubleWhitespaces($newdoc, $index);
			} elseif (preg_match('/\{INCLUDE\(.+?\)\}\{INCLUDE\}/', $word['word'])) {
				$params=$this->retrieveParams($word['word']);
				$start='';
				$stop='';
				if (isset($params['start'])) {
					$start=$params['start'];	
				}
				if (isset($params['stop'])) {
					$stop=$params['stop'];	
				}
				$subdoc=new Document($params['page'], 0, $this->_process, $this->_showpopups, $start, $stop);
				$newdoc=array_merge($newdoc, $subdoc->get());				
			} else { //normal word
				if ($author!='') {
					$word['author']=$author;
				}
				if ($deleted_by!='') {
					$word['deleted']=true;
					$word['deleted_by']=$deleted_by;
				}
				$newdoc[]=$word;
			}
		} //foreach
		$this->_document=$newdoc;
	}
}


function histlib_helper_setup_diff( $page, $oldver, $newver, $diff_style = '' )
{
	global $prefs;
	$smarty = TikiLib::lib('smarty');
	$histlib = TikiLib::lib('hist');
	$tikilib = TikiLib::lib('tiki');
	$prefs['wiki_edit_section'] = 'n';
	
	$info = $tikilib->get_page_info($page);

	if ($oldver == 0 || $oldver == $info["version"]) {
		$old = & $info;
		$smarty->assign_by_ref('old', $info);
	} else {
		// fetch the required page from history, including its content
		while( $oldver > 0 && ! ($exists = $histlib->version_exists($page, $oldver) ) )
			--$oldver;

		if ( $exists ) {
			$old = $histlib->get_page_from_history($page, $oldver, true);
			$smarty->assign_by_ref('old', $old);
		}
	}
	if ($newver == 0 || $newver >= $info["version"]) {
		$new =& $info;
		$smarty->assign_by_ref('new', $info);
	} else {
		// fetch the required page from history, including its content
		while( $newver > 0 && ! ($exists = $histlib->version_exists($page, $newver) ) )
			--$newver;

		if ( $exists ) {
			$new = $histlib->get_page_from_history($page, $newver, true);
			$smarty->assign_by_ref('new', $new);
		}
	}

	$oldver_mod = $oldver;
	if ($oldver == 0) {
		$oldver_mod = 1;
	}

	$query = "SELECT `comment`, `version` from `tiki_history` WHERE `pageName`=? and `version` BETWEEN ? AND ? ORDER BY `version` DESC";
	$result = $histlib->query($query, array($page,$oldver_mod,$newver));
	$diff_summaries = array();

	if ($oldver == 0) {
		$diff_summaries[] = $old['comment'];
	}

	while ($res = $result->fetchRow()) {
		$aux = array();

		$aux["comment"] = $res["comment"];
		$aux["version"] = $res["version"];
		$diff_summaries[] = $aux;
	}

	$smarty->assign('diff_summaries', $diff_summaries);
	
	if (empty($diff_style) || $diff_style == "old") {
		$diff_style = $prefs['default_wiki_diff_style'];
	}

	$smarty->assign('diff_style', $diff_style);
	if ($diff_style == "sideview") {
		$old["data"] = $tikilib->parse_data($old["data"], array('preview_mode' => true));
		$new["data"] = $tikilib->parse_data($new["data"], array('preview_mode' => true));
	} else {
		require_once('lib/diff/difflib.php');
		if ($info['is_html'] == 1 and $diff_style != "htmldiff") {
			$search[] = "~</(table|td|th|div|p)>~";
			$replace[] = "\n";
			$search[] = "~<(hr|br) />~";
			$replace[] = "\n";
			$old['data'] = strip_tags(preg_replace($search, $replace, $old['data']), '<h1><h2><h3><h4><b><i><u><span>');
			$new['data'] = strip_tags(preg_replace($search, $replace, $new['data']), '<h1><h2><h3><h4><b><i><u><span>');
		}
		if ($diff_style == "htmldiff" && $old['is_html'] != 1) {

			$parse_options = array('is_html' => ($old['is_html'] == 1), 'noheadinc' => true, 'suppress_icons' => true, 'noparseplugins' => true);
			$old["data"] = $tikilib->parse_data($old["data"], $parse_options);
			$new["data"] = $tikilib->parse_data($new["data"], $parse_options);

			$old['data'] = histlib_strip_irrelevant($old['data']);
			$new['data'] = histlib_strip_irrelevant($new['data']);
		}
		# If the user doesn't have permission to view
		# source, strip out all tiki-source-based comments
		global $tiki_p_wiki_view_source;
		if ($tiki_p_wiki_view_source != 'y') {
			$old["data"] = preg_replace(';~tc~(.*?)~/tc~;s', '', $old["data"]);
			$new["data"] = preg_replace(';~tc~(.*?)~/tc~;s', '', $new["data"]);
		}

		$html = diff2($old["data"], $new["data"], $diff_style);
		$smarty->assign_by_ref('diffdata', $html);
	}
}

function histlib_strip_irrelevant( $data )
{
	$data = preg_replace("/<(h1|h2|h3|h4|h5|h6|h7)\s+([^\\\\>]+)>/i", '<$1>', $data);
	return $data;
}

function rollback_page_to_version($page, $version, $check_key = true, $keep_lastModif = false)
{
	global $prefs;
	$histlib = TikiLib::lib('hist');
	$tikilib = TikiLib::lib('tiki');
	$access = TikiLib::lib('access');

	if ($check_key) {
		$access->check_authenticity();
	}		
	$histlib->use_version($page, $version, '', $keep_lastModif);
	
	$tikilib->invalidate_cache($page);
}
