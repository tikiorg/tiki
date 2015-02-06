<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

/**
 *
 */
class HtmlPagesLib extends TikiLib
{
    /**
     * @param $pageName
     * @return bool
     */
    function remove_html_page($pageName)
	{
		$query = 'delete from `tiki_html_pages` where binary `pageName`=?';
		$result = $this->query($query, array($pageName));

		return true;
	}

    /**
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    function list_html_pages($offset, $maxRecords, $sort_mode, $find)
	{
		$bindvars = array();

		if ($find) {
			$mid = ' where (`pageName` like ? or `content` like ?)';
			$bindvars[] = "%$find%";
			$bindvars[] = "%$find%";
		} else {
			$mid = '';
		}

		$query = "select `pageName`,`refresh`,`created`,`type` from `tiki_html_pages` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_html_pages` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $cant;

		return $retval;
	}

    /**
     * @param $pageName
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    function list_html_page_content($pageName, $offset, $maxRecords, $sort_mode, $find)
	{
		$bindvars = array($pageName);
		$mid = ' where binary `pageName`=? ';

		if ($find) {
			$mid = ' and (`pageName` like ? or `content` like ?)';
			$bindvars[] = "%$find%";
			$bindvars[] = "%$find%";
		}

		$query = "select * from `tiki_html_pages_dynamic_zones` $mid order by " . $this->convertSortMode($sort_mode);
		$query_cant = "select count(*) from `tiki_html_pages_dynamic_zones` $mid";
		$result = $this->query($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $cant;

		return $retval;
	}

    /**
     * @param $pageName
     * @param $data
     * @return mixed
     */
    function parse_html_page($pageName, $data)
	{
		global $tikilib; // only required for parsing <wiki>...</wiki> tags

		// match and replace dynamic content
		//The data is needed because we may be previewing a page...
		preg_match_all('/\{t?ed id=([^\}]+)\}/', $data, $eds);

		for ($i = 0, $icount_eds = count($eds[0]); $i < $icount_eds; $i++) {
			$cosa = $this->get_html_page_content($pageName, $eds[1][$i]);
			$data = str_replace($eds[0][$i], '<span id="' . $eds[1][$i] . '">' . $cosa['content'] . '</span>', $data);
		}

		// match and parse text in <wiki>...</wiki> tags
		preg_match_all('/<wiki>(.*?)<\/wiki>/si', $data, $wikis); // ? for ungreedy and /s to include \n in .
		for ($i = 0, $icount_wikis = count($wikis[0]); $i < $icount_wikis; $i++) {
			$parsed = substr($tikilib->parse_data($wikis[1][$i]), 0, -7); // remove <br /> appended by parser
			$data = str_replace($wikis[0][$i], $parsed, $data);
		}

		return $data;
	}

    /**
     * @param $pageName
     * @param $type
     * @param $content
     * @param $refresh
     * @return mixed
     */
    function replace_html_page($pageName, $type, $content, $refresh)
	{
		$query = 'delete from `tiki_html_pages` where binary `pageName`=?';
		$this->query($query, array($pageName), -1, -1, false);
		$query = 'insert into `tiki_html_pages`(`pageName`,`content`,`type`,`created`,`refresh`) values(?,?,?,?,?)';
		$result = $this->query($query, array($pageName, $content, $type, (int)$this->now, (int)$refresh));
		// For dynamic pages update the zones into the dynamic pages zone
		preg_match_all('/\{ed id=([^\}]+)\}/', $content, $eds);
		preg_match_all('/\{ted id=([^\}]+)\}/', $content, $teds);
		$all_eds = array_merge($eds[1], $teds[1]);

		$query = 'select `zone` from `tiki_html_pages_dynamic_zones` where binary `pageName`=?';
		$result = $this->query($query, array($pageName));

		while ($res = $result->fetchRow()) {
			if (!in_array($res['zone'], $all_eds)) {
				$query2 = 'delete from `tiki_html_pages_dynamic_zones` where binary `pageName`=? and `zone`=?';
				$result2 = $this->query($query2, array($pageName, $res['zone']));
			}
		}

		for ($i = 0, $icount_eds = count($eds[0]); $i < $icount_eds; $i++) {
			if (!$this->getOne('select count(*) from `tiki_html_pages_dynamic_zones` where binary `pageName`=? and `zone`=?', array($pageName, $eds[1][$i]))) {
				$this->query('delete from `tiki_html_pages_dynamic_zones` where binary `pageName`=? and `zone`=?', array($pageName, $eds[1][$i]));
				$query = 'insert into `tiki_html_pages_dynamic_zones`(`pageName`,`zone`,`type`) values(?,?,?)';
				$result = $this->query($query, array($pageName, $eds[1][$i], 'tx'));
			}
		}

		for ($i = 0, $icount_teds = count($teds[0]); $i < $icount_teds; $i++) {
			if (!$this->getOne('select count(*) from `tiki_html_pages_dynamic_zones` where binary `pageName`=? and zone=?', array($pageName, $teds[1][$i]))) {
				$this->query('delete from `tiki_html_pages_dynamic_zones` where binary `pageName`=? and `zone`=?', array($pageName, $teds[1][$i]));
				$query = 'insert into `tiki_html_pages_dynamic_zones`(`pageName`,`zone`,`type`) values(?,?,?)';
				$result = $this->query($query, array($pageName, $teds[1][$i], 'ta'));
			}
		}

		return $pageName;
	}

    /**
     * @param $pageName
     * @param $zone
     * @param $content
     * @return mixed
     */
    function replace_html_page_content($pageName, $zone, $content)
	{
		$query = 'update `tiki_html_pages_dynamic_zones` set `content`=? where binary `pageName`=? and `zone`=?';
		$result = $this->query($query, array($content, $pageName, $zone));

		return $zone;
	}

    /**
     * @param $pageName
     * @param $zone
     * @return bool
     */
    function remove_html_page_content($pageName, $zone)
	{
		$query = 'delete from `tiki_html_pages_dynamic_zones` where binary `pageName`=? and `zone`=?';
		$result = $this->query($query, array($pageName, $zone));

		return true;
	}

    /**
     * @param $pageName
     * @return bool
     */
    function get_html_page($pageName)
	{
		$query = 'select * from `tiki_html_pages` where binary `pageName`=?';
		$result = $this->query($query, array($pageName));
		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();

		return $res;
	}

    /**
     * @param $pageName
     * @param $zone
     * @return bool
     */
    function get_html_page_content($pageName, $zone)
	{
		$query = 'select * from `tiki_html_pages_dynamic_zones` where binary `pageName`=? and `zone`=?';
		$result = $this->query($query, array($pageName, $zone));
		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();

		return $res;
	}
}
$htmlpageslib = new HtmlPagesLib;
