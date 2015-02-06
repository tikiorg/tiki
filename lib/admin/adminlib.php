<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 *
 */
class AdminLib extends TikiLib
{

    /**
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    function list_dsn($offset, $maxRecords, $sort_mode, $find)
	{

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

    /**
     * @param $dsnId
     * @param $dsn
     * @param $name
     * @return bool
     */
    function replace_dsn($dsnId, $dsn, $name)
	{
		// Check the name
		if ($dsnId) {
			$query = "update `tiki_dsn` set `name`=?,`dsn`=? where `dsnId`=?";
			$bindvars=array($name,$dsn, $dsnId);
			$result = $this->query($query, $bindvars);
		} else {
			$query = "delete from `tiki_dsn`where `name`=? and `dsn`=?";
			$bindvars=array($name,$dsn);
			$result = $this->query($query, $bindvars);
			$query = "insert into `tiki_dsn`(`name`,`dsn`)
                		values(?,?)";
			$result = $this->query($query, $bindvars);
		}

		return true;
	}

    /**
     * @param $dsnId
     * @return bool
     */
    function remove_dsn($dsnId)
	{
		$info = $this->get_dsn($dsnId);

		$query = "delete from `tiki_dsn` where `dsnId`=?";
		$this->query($query, array($dsnId));
		return true;
	}

    /**
     * @param $dsnId
     * @return bool
     */
    function get_dsn($dsnId)
	{
		$query = "select * from `tiki_dsn` where `dsnId`=?";

		$result = $this->query($query, array($dsnId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

    /**
     * @param $dsnName
     * @return bool
     */
    function get_dsn_from_name($dsnName)
	{
		$query = "select * from `tiki_dsn` where `name`=?";

		$result = $this->query($query, array($dsnName));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}

    /**
     * @param $offset
     * @param $maxRecords
     * @param $sort_mode
     * @param $find
     * @return array
     */
    function list_extwiki($offset, $maxRecords, $sort_mode, $find)
	{
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
		$result = $this->fetchAll($query, $bindvars, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvars);
		$ret = array();

		$retval = array();
		$retval["data"] = $result;
		$retval["cant"] = $cant;
		return $retval;
	}

    /**
     * @param $extwikiId
     * @param $extwiki
     * @param $name
     * @return bool
     */
    function replace_extwiki($extwikiId, $extwiki, $name, $indexName = '', $groups = [])
	{
		$table = $this->table('tiki_extwiki');
		$data = [
			'name' => $name,
			'extwiki' => $extwiki,
			'indexname' => $indexName,
			'groups' => json_encode(array_values($groups)),
		];
		$withId = $data;
		$withId['extwikiId'] = $extwikiId;
		$table->insertOrUpdate($withId, $data);

		return true;
	}

    /**
     * @param $extwikiId
     * @return bool
     */
    function remove_extwiki($extwikiId)
	{
		$info = $this->get_extwiki($extwikiId);

		$query = "delete from `tiki_extwiki` where `extwikiId`=?";
		$this->query($query, array($extwikiId));
		return true;
	}

    /**
     * @param $extwikiId
     * @return bool
     */
    function get_extwiki($extwikiId)
	{
		$table = $this->table('tiki_extwiki');
		$row = $table->fetchFullRow(['extwikiId' => $extwikiId]);
		
		if (! empty($row['groups'])) {
			$row['groups'] = json_decode($row['groups']);
		}
		return $row;
	}

	function remove_unused_pictures()
	{
		global $tikidomain;

		$query = "select `data` from `tiki_pages`";
		$result = $this->query($query, array());
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
					@unlink($filename);
				}
			}
		}

		closedir($h);
	}

	function remove_orphan_images()
	{
		$merge = array();

		// Find images in tiki_pages
		$query = "select `data` from `tiki_pages`";
		$result = $this->query($query, array());

		while ($res = $result->fetchRow()) {
			preg_match_all("/src=\"([^\"]+)\"/", $res["data"], $reqs1);

			preg_match_all("/src=\'([^\']+)\'/", $res["data"], $reqs2);
			preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/", $res["data"], $reqs3);
			$merge = array_merge($merge, $reqs1[1], $reqs2[1], $reqs3[1]);
			$merge = array_unique($merge);
		}

		// Find images in Tiki articles
		$query = "select `body` from `tiki_articles`";
		$result = $this->query($query, array());

		while ($res = $result->fetchRow()) {
			preg_match_all("/src=\"([^\"]+)\"/", $res["body"], $reqs1);

			preg_match_all("/src=\'([^\']+)\'/", $res["body"], $reqs2);
			preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/", $res["body"], $reqs3);
			$merge = array_merge($merge, $reqs1[1], $reqs2[1], $reqs3[1]);
			$merge = array_unique($merge);
		}

		// Find images in tiki_submissions
		$query = "select `body` from `tiki_submissions`";
		$result = $this->query($query, array());

		while ($res = $result->fetchRow()) {
			preg_match_all("/src=\"([^\"]+)\"/", $res["body"], $reqs1);

			preg_match_all("/src=\'([^\']+)\'/", $res["body"], $reqs2);
			preg_match_all("/src=([A-Za-z0-9:\?\=\/\.\-\_]+)\}/", $res["body"], $reqs3);
			$merge = array_merge($merge, $reqs1[1], $reqs2[1], $reqs3[1]);
			$merge = array_unique($merge);
		}

		// Find images in tiki_blog_posts
		$query = "select `data` from `tiki_blog_posts`";
		$result = $this->query($query, array());

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
		$result = $this->query($query, array());

		while ($res = $result->fetchRow()) {
			$id = $res["imageId"];

			if (!in_array($id, $positives)) {
				$this->remove_image($id);
			}
		}
	}

    /**
     * @param $tag
     * @return mixed
     */
    function tag_exists($tag)
	{
		$query = "select distinct `tagName` from `tiki_tags` where `tagName` = ?";

		$result = $this->query($query, array($tag));
		return $result->numRows($result);
	}

    /**
     * @param $tagname
     * @return bool
     */
    function remove_tag($tagname)
	{
		global $prefs;

		$query = "delete from `tiki_tags` where `tagName`=?";
		$result = $this->query($query, array($tagname));
		$logslib = TikiLib::lib('logs');
		$logslib->add_log('dump', "removed tag: $tagname");
		return true;
	}

    /**
     * @return array
     */
    function get_tags()
	{
		$query = "select distinct `tagName` from `tiki_tags`";

		$result = $this->query($query, array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res["tagName"];
		}

		return $ret;
	}

	// This function can be used to store the set of actual pages in the "tags"
	// table preserving the state of the wiki under a tag name.
    /**
     * @param $tagname
     * @param string $comment
     * @return bool
     */
    function create_tag($tagname, $comment = '')
	{
		global $prefs;

		$query = "select * from `tiki_pages`";
		$result = $this->query($query, array());

		while ($res = $result->fetchRow()) {
			$data = $res["data"];
			$pageName = $res["pageName"];
			$description = $res["description"];
			$query = "delete from `tiki_tags`where `tagName`=? and `pageName`=?";
			$this->query($query, array($tagname, $pageName), -1, -1, false);
			$query = "insert into `tiki_tags`(`tagName`,`pageName`,`hits`,`data`,`lastModif`,`comment`,`version`,`user`,`ip`,`flag`,`description`)" .
               " values(?,?,?,?,?,?,?,?,?,?,?)";
			$result2 = $this->query(
				$query,
				array(
					$tagname,
					$pageName,
					$res["hits"],
					$data,
					$res["lastModif"],
					$res["comment"],
					$res["version"],
					$res["user"],
					$res["ip"],
					$res["flag"],
					$description
				)
			);
		}

		$logslib = TikiLib::lib('logs');
		$logslib->add_log('dump', "created tag: $tagname");
		return true;
	}

	// This funcion recovers the state of the wiki using a tagName from the
	// tags table
    /**
     * @param $tagname
     * @return bool
     */
    function restore_tag($tagname)
	{
		global $prefs;

		$query = "update `tiki_pages` set `cache_timestamp`=0";
		$this->query($query, array());
		$query = "select * from `tiki_tags` where `tagName`=?";
		$result = $this->query($query, array($tagname));

		while ($res = $result->fetchRow()) {

			$query = "update `tiki_pages`" .
								" set `hits`=?,`data`=?,`lastModif`=?,`comment`=?,`version`=`version`+1,`user`=?,`ip`=?,`flag`=?,`description`=?" .
								"  where `pageName`=?";

			$result2 = $this->query(
				$query,
				array(
					$res["hits"],
					$res["data"],
					$res["lastModif"],
					$res["comment"],
					$res["user"],
					$res["ip"],
					$res["flag"],
					$res["description"],
					$res["pageName"]
				)
			);
		}

		$logslib = TikiLib::lib('logs');
		$logslib->add_log('dump', "recovered tag: $tagname");
		return true;
	}

	// Dumps the database to dump/new.tar
	// changed for virtualhost support
	function dump()
	{
		global $tikidomain, $prefs;
		$parserlib = TikiLib::lib('parser');

		$dump_path = "dump";
		if ($tikidomain) {
			$dump_path.= "/$tikidomain";
		}

		@unlink("$dump_path/new.tar");
		$tar = new tar();
		$tar->addFile('styles/' . $prefs['style']);
		// Foreach page
		$query = "select * from `tiki_pages`";
		$result = $this->query($query, array());

		while ($res = $result->fetchRow()) {
			$pageName = $res["pageName"] . '.html';

			$dat = $parserlib->parse_data($res["data"]);
			// Now change index.php?page=foo to foo.html
			// and index.php to HomePage.html
			$dat = preg_replace("/tiki-index.php\?page=([^\'\"\$]+)/", "$1.html", $dat);
			$dat = preg_replace("/tiki-editpage.php\?page=([^\'\"\$]+)/", "", $dat);
			//preg_match_all("/tiki-index.php\?page=([^ ]+)/",$dat,$cosas);
			//print_r($cosas);
			$data = "<html><head><title>" .
							$res["pageName"] .
							"</title><link rel='StyleSheet' href='styles/" .
							$prefs['style']  .
							"' type='text/css'></head><body><a class='wiki' href='" .
							$prefs['wikiHomePage'] .
							".html'>home</a><br /><h1>" .
							$res["pageName"] .
							"</h1><div class='wikitext'>" .
							$dat .
							'</div></body></html>';
			$tar->addData($pageName, $data, $res["lastModif"]);
		}

		$tar->toTar("$dump_path/new.tar", FALSE);
		unset ($tar);
		$logslib = TikiLib::lib('logs');
		$logslib->add_log('dump', 'dump created');
	}

	public function getOpcodeCacheStatus()
	{
		$opcode_stats = array(
			'opcode_cache' => null,
			'stat_flag' => null,
			'warning_check' => false,
			'warning_fresh' => false,
			'warning_ratio' => false,
			'warning_starve' => false,
			'warning_low' => false,
			'warning_xcache_blocked' => false,
		);

		if ( function_exists('apc_sma_info') && ini_get('apc.enabled') ) {

			if ( $_REQUEST['apc_clear']) {
				check_ticket('admin-inc-performance');
				apc_clear_cache();
				apc_clear_cache('user');
				apc_clear_cache('opcode');
			}

			$sma = apc_sma_info();
			$mem_total = $sma['num_seg'] * $sma['seg_size'];

			$cache = apc_cache_info(null, true);
			$hit_total = $cache['num_hits'] + $cache['num_misses'];
			if (!$hit_total) {	// cheat for chart after cache clear
				$hit_total = 1;
				$cache['num_misses'] = 1;
			}

			$opcode_stats = array(
				'opcode_cache' => 'APC',
				'stat_flag' => 'apc.stat',
				'memory_used' => ( $mem_total - $sma['avail_mem'] ) / $mem_total,
				'memory_avail' => $sma['avail_mem'] / $mem_total,
				'memory_total' => $mem_total,
				'hit_hit' => $cache['num_hits'] / $hit_total,
				'hit_miss' => $cache['num_misses'] / $hit_total,
				'hit_total' => $hit_total,
				'type' => 'apc',
			);
		} elseif ( function_exists('xcache_info') && ( ini_get('xcache.cacher') == '1' || ini_get('xcache.cacher') == 'On' ) ) {
			if ( ini_get('xcache.admin.enable_auth') == '1' || ini_get('xcache.admin.enable_auth') == 'On' ) {
				$opcode_stats['warning_xcache_blocked'] = true;
			} else {
				$opcode_stats = array(
					'stat_flag' => 'xcache.stat',
					'memory_used' => 0,
					'memory_avail' => 0,
					'memory_total' => 0,
					'hit_hit' => 0,
					'hit_miss' => 0,
					'hit_total' => 0,
					'type' => 'xcache',
				);

				foreach (range(0, xcache_count(XC_TYPE_PHP) - 1) as $index) {
					$info = xcache_info(XC_TYPE_PHP, $index);

					$opcode_stats['hit_hit'] += $info['hits'];
					$opcode_stats['hit_miss'] += $info['misses'];
					$opcode_stats['hit_total'] += $info['hits'] + $info['misses'];

					$opcode_stats['memory_used'] += $info['size'] - $info['avail'];
					$opcode_stats['memory_avail'] += $info['avail'];
					$opcode_stats['memory_total'] += $info['size'];
				}

				$opcode_stats['memory_used'] /= $opcode_stats['memory_total'];
				$opcode_stats['memory_avail'] /= $opcode_stats['memory_total'];
				$opcode_stats['hit_hit'] /= $opcode_stats['hit_total'];
				$opcode_stats['hit_miss'] /= $opcode_stats['hit_total'];
			}
			$opcode_stats['opcode_cache'] = 'XCache';

		} elseif ( function_exists('wincache_ocache_fileinfo') && ( ini_get('wincache.ocenabled') == '1') ) {
			$opcode_stats = array(
				'opcode_cache' => 'WinCache',
				'stat_flag' => 'wincache.ocenabled',
				'memory_used' => 0,
				'memory_avail' => 0,
				'memory_total' => 0,
				'hit_hit' => 0,
				'hit_miss' => 0,
				'hit_total' => 0,
				'type' => 'wincache',
				);

			$info = wincache_ocache_fileinfo();
			$opcode_stats['hit_hit'] = $info['total_hit_count'];
			$opcode_stats['hit_miss'] = $info['total_miss_count'];
			$opcode_stats['hit_total'] = $info['total_hit_count'] + $info['total_miss_count'];

			$memory = wincache_ocache_meminfo();
			$opcode_stats['memory_avail'] = $memory['memory_free'];
			$opcode_stats['memory_total'] = $memory['memory_total'];
			$opcode_stats['memory_used'] = $memory['memory_total'] - $memory['memory_free'];

			$opcode_stats['memory_used'] /= $opcode_stats['memory_total'];
			$opcode_stats['memory_avail'] /= $opcode_stats['memory_total'];
			$opcode_stats['hit_hit'] /= $opcode_stats['hit_total'];
			$opcode_stats['hit_miss'] /= $opcode_stats['hit_total'];
		} elseif ( function_exists('opcache_get_status') && ini_get('opcache.enable') == '1' ) {
			$opcode_stats['opcode_cache'] = 'OpCache';
			$status = opcache_get_status();

			$opcode_stats['hit_hit'] = $status['opcache_statistics']['hits'];
			$opcode_stats['hit_miss'] = $status['opcache_statistics']['misses'];
			$opcode_stats['hit_total'] = $status['opcache_statistics']['hits'] + $status['opcache_statistics']['misses'];

			$opcode_stats['memory_avail'] = $status['memory_usage']['free_memory'];
			$opcode_stats['memory_used'] = $status['memory_usage']['used_memory'];
			$opcode_stats['memory_total'] = $status['memory_usage']['used_memory'] + $status['memory_usage']['free_memory'];

			$opcode_stats['memory_used'] /= $opcode_stats['memory_total'];
			$opcode_stats['memory_avail'] /= $opcode_stats['memory_total'];
			$opcode_stats['hit_hit'] /= $opcode_stats['hit_total'];
			$opcode_stats['hit_miss'] /= $opcode_stats['hit_total'];
		}

		// Make results easier to read
		$opcode_stats['memory_used'] = round($opcode_stats['memory_used'], 2);
		$opcode_stats['memory_avail'] = round($opcode_stats['memory_avail'], 2);
		$opcode_stats['hit_hit'] = round($opcode_stats['hit_hit'], 2);
		$opcode_stats['hit_miss'] = round($opcode_stats['hit_miss'], 2);

		if ( isset($opcode_stats['hit_total']) ) {
			$opcode_stats = array_merge(
				$opcode_stats,
				array(
					'warning_fresh' => $opcode_stats['hit_total'] < 10000,
					'warning_ratio' => $opcode_stats['hit_hit'] < 0.8,
				)
			);
		}

		if ( isset($opcode_stats['memory_total']) ) {
			$opcode_stats = array_merge(
				$opcode_stats,
				array(
					'warning_starve' => $opcode_stats['memory_avail'] < 0.2,
					'warning_low' => $opcode_stats['memory_total'] < 60*1024*1024,
				)
			);
		}

		$stat_flag = $opcode_stats['stat_flag'];
		if ( $stat_flag ) {
			$opcode_stats['warning_check'] = (bool) ini_get($stat_flag);
		}

		return $opcode_stats;
	}
}
