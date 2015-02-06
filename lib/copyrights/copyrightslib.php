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

class CopyrightsLib extends TikiLib
{
	function list_copyrights($page)
	{
		$query = 'select * from `tiki_copyrights` WHERE `page`=? order by ' . $this->convertSortMode('copyright_order_asc');
		$query_cant = 'select count(*) from `tiki_copyrights` WHERE `page`=?';
		$result = $this->query($query, array($page));
		$cant = $this->getOne($query_cant, array($page));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $cant;
		return $retval;
	}

	function top_copyright_order($page)
	{
		$query = 'select MAX(`copyright_order`) from `tiki_copyrights` where `page` like ?';
		return $this->getOne($query, array($page));
	}

	function unique_copyright($page, $title)
	{
		$query = 'select `copyrightID` from `tiki_copyrights` where `page`=? and `title`=?';
		return $this->getOne($query, array($page, $title));
	}

	function add_copyright($page, $title, $year, $authors, $copyrightHolder, $user) 
	{
		$top = $this->top_copyright_order($page);
		$order = $top + 1;
		$query = 'insert `tiki_copyrights` (`page`, `title`, `year`, `authors`, `holder`, `copyright_order`, `userName`) values (?,?,?,?,?,?,?)';
		$this->query($query, array($page, $title, $year, $authors, $copyrightHolder, $order, $user));
		return true;
	}

	function edit_copyright($id, $title, $year, $authors, $copyrightHolder, $user) 
	{
		$query = 'update `tiki_copyrights` SET `year`=?, `title`=?, `authors`=?, `holder`=?, `userName`=? where `copyrightId`=?';
		$this->query($query, array($year, $title, $authors, $copyrightHolder, $user, (int)$id));
		return true;
	}

	function remove_copyright($id)
	{
		$query = 'delete from `tiki_copyrights` where `copyrightId`=?';
		$this->query($query, array((int)$id));
		return true;
	}

	function up_copyright($id)
	{
		$query = 'update `tiki_copyrights` set `copyright_order`=`copyright_order`-1 where `copyrightId`=?';
		$result = $this->query($query, array((int)$id));
		return true;
	}

	function down_copyright($id)
	{
		$query = 'update `tiki_copyrights` set `copyright_order`=`copyright_order`+1 where `copyrightId`=?';
		$result = $this->query($query, array((int)$id));
		return true;
	}
}
