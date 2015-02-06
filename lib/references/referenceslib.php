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

class ReferencesLib extends TikiLib
{
	public function list_references($page)
	{
		$query = 'select * from `tiki_page_references` WHERE `page_id`=? ORDER BY `biblio_code`';
		$query_cant = 'select count(*) from `tiki_page_references` WHERE `page_id`=?';
		$result = $this->query($query, array($page));
		$cant = $this->getOne($query_cant, array($page));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$query_1 = 'select * from `tiki_page_references` WHERE `biblio_code`=? AND page_id IS NULL';
			$result_1 = $this->query($query_1, array($res['biblio_code']));
			$res['is_library'] = $result_1->numrows;
			$ret[] = $res;
		}

		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $cant;

		return $retval;
	}

	public function list_assoc_references($page)
	{
		$query = 'select * from `tiki_page_references` WHERE `page_id`=? ORDER BY `biblio_code`';
		$query_cant = 'select count(*) from `tiki_page_references` WHERE `page_id`=?';
		$result = $this->query($query, array($page));
		$cant = $this->getOne($query_cant, array($page));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[$res['biblio_code']] = $res;
		}

		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $cant;

		return $retval;
	}

	public function get_references_from_biblio($code)
	{
		$query = 'select * from `tiki_page_references` WHERE `biblio_code`=?';
		$query_cant = 'select count(*) from `tiki_page_references` WHERE `biblio_code`=?';
		$result = $this->query($query, array($code));
		$cant = $this->getOne($query_cant, array($code));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval['data'] = $ret;
		$retval['cant'] = $cant;

		return $retval;
	}

	public function get_reference_from_code($code)
	{
		$query = 'select * from `tiki_page_references` WHERE `biblio_code`=?';
		$result = $this->query($query, array($code));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval['data'] = $ret;
		return $retval;
	}

	public function get_reference_from_id($ref_id)
	{
		$query = 'select * from `tiki_page_references` WHERE `ref_id`=?';
		$result = $this->query($query, array($ref_id));
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval['data'] = $ret;
		return $retval;
	}

	public function get_reference_from_code_and_page($codes, $page)
	{
		$biblios = '';
		foreach ($codes as $code) {
			if (is_array($code)) {
				$biblios .= '\'' . $code['biblio_code'] . '\'' . ',';
			} else {
				$biblios .= '\'' . $code . '\'' . ',';
			}
		}
		$biblios = substr($biblios, 0, strlen($biblios)-1);

		$codes = "'first'".','.'second';
		$query = "select * from `tiki_page_references` WHERE `biblio_code` IN ($biblios) AND `page_id`=?";
		$result = $this->query($query, array($page));

		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[$res['biblio_code']] = $res;
		}

		$retval = array();
		$retval['data'] = $ret;

		return $retval;
	}

	public function list_lib_references()
	{
		global $page;

		$query = 'select * from `tiki_page_references` WHERE `page_id` IS NULL ORDER BY `biblio_code`';
		$query_cant = 'select count(*) from `tiki_page_references` WHERE `page_id` IS NULL';
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

	public function add_reference($page, $biblio_code, $author, $title,
													$part, $uri, $code, $year, $style,
													$template, $publisher, $location)
	{
		$query = 'insert `tiki_page_references`' .
							' (`page_id`, `biblio_code`, `author`, `title`, `part`, `uri`,' .
							' `code`, `year`, `style`, `template`, `publisher`, `location`)' .
							' values (?,?,?,?,?,?,?,?,?,?,?,?)';

		$this->query(
			$query,
			array(
				$page,
				$biblio_code,
				$author,
				$title,
				$part,
				$uri,
				$code,
				$year,
				$style,
				$template,
				$publisher,
				$location
			)
		);

		return $this->lastInsertId();
	}

	public function add_lib_ref_to_page($ref_id, $page)
	{

		$query = 'select * from `tiki_page_references` WHERE `ref_id`=?';
		$result = $this->query($query, array($ref_id));

		$exists = $this->check_existence($page, $result->result[0]['biblio_code']);

		if ($exists > 0) {
			return -1;
		} else {
			$query = 'insert `tiki_page_references`' .
								' (`page_id`, `biblio_code`, `author`, `title`, `part`, `uri`,' .
								' `code`, `year`, `style`, `template`, `publisher`, `location`)' .
								' values (?,?,?,?,?,?,?,?,?,?,?,?)';

			$this->query(
				$query,
				array(
					$page,
					$result->result[0]['biblio_code'],
					$result->result[0]['author'],
					$result->result[0]['title'],
					$result->result[0]['part'],
					$result->result[0]['uri'],
					$result->result[0]['code'],
					$result->result[0]['year'],
					$result->result[0]['style'],
					$result->result[0]['template'],
					$result->result[0]['publisher'],
					$result->result[0]['location']
				)
			);

			return $this->lastInsertId();
		}
	}

	public function edit_reference($ref_id, $biblio_code, $author, $title, $part, $uri,
													$code, $year, $style, $template, $publisher, $location)
	{
		$query = 'update `tiki_page_references`' .
							' SET `biblio_code`=?, `author`=?, `title`=?, `part`=?, `uri`=?,' .
							' `code`=?, `year`=?, `style`=?, `template`=?, `publisher`=?, `location`=?' .
							' where `ref_id`=?';

		$this->query(
			$query,
			array(
					$biblio_code,
					$author,
					$title,
					$part,
					$uri,
					$code,
					$year,
					$style,
					$template,
					$publisher,
					$location,
					(int) $ref_id
			)
		);

		return true;
	}

	public function remove_reference($id)
	{
		$query = 'delete from `tiki_page_references` where `ref_id`=?';
		$this->query($query, array((int) $id));
		return true;
	}

	public function check_existence($page_id, $biblio_code)
	{
		$query = 'select * from `tiki_page_references` WHERE `biblio_code`=? AND `page_id`=?';
		$result = $this->query($query, array($biblio_code, $page_id));
		return $result->numrows;
	}

	public function check_lib_existence($biblio_code)
	{
		$query = 'select * from `tiki_page_references` WHERE `biblio_code`=? AND `page_id` IS NULL';
		$result = $this->query($query, array($biblio_code));

		return $result->numrows;
	}
}
