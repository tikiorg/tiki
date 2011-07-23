<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * FreetagLib is based in Freetag library. Code was translated to Tiki style and
 *
 * API and docs was mostly preserved:
 *
 * - the "type" variable added wherever an object id is passed.
 * - user is varchar instead of text
 * - debug_text function removed
 *
 * Translated by Luis Fagundes aka batawata
 *
 *  Gordon Luk's Freetag - Generalized Open Source Tagging and Folksonomy.
 *  Copyright (C) 2004-2005 Gordon D. Luk <gluk AT getluky DOT net>
 *
 *  Released under both BSD license and Lesser GPL library license. Whenever
 *  there is any discrepancy between the two licenses, the BSD license will
 *  take precedence. See License.txt.
 *
 *  Freetag API Implementation
 *
 *  Freetag is a generic PHP class that can hook-in to existing database
 *  schemas and allows tagging of content within a social website. It's fun,
 *  fast, and easy! Try it today and see what all the folksonomy fuss is
 *  about.
 *
 *  Contributions and/or donations are welcome.
 *
 *  Author: Gordon Luk
 *  http://www.getluky.net
 *
 *  Version: 0.231
 *  Last Updated: 10/13/2005
 *
 */

// This script may only be included - so its better to die if called directly.
if (strpos($_SERVER['SCRIPT_NAME'], basename(__FILE__)) !== false) {
	header('location: index.php');
	exit;
}

require_once('lib/objectlib.php');

class FreetagLib extends ObjectLib
{
	// The fields below should be tiki preferences

	/* @access private
	 * @param string The regex-style set of characters that are valid for normalized tags.
	 */
	var $_normalized_valid_chars = 'a-zA-Z0-9';
	/**
	 * @access private
	 * @param string The regex-style set of characters that are valid for normalized tags.
	 */
	var $_normalize_in_lowercase = 1;
	/**
	 * @access private
	 * @param string Whether to prevent multiple users from tagging the same object. By default, set to block (ala Upcoming.org)
	 */
	var $_block_multiuser_tag_on_object = 1;

	/**
	 * @access private
	 * @param int The maximum length of a tag.
	 */
	var $_MAX_TAG_LENGTH = 128;
	/**
	 * @access public
	 * @param int The number of size degrees for tags in cloud. There should be correspondent classes in css.
	 */
	var $max_cloud_text_size = 7;

	var $multilingual = false;


	/**
	 * FreetagLib
	 *
	 * Constructor for the freetag class.
	 *
	 */
	function __construct()
	{
		parent::__construct();

		global $prefs;
		if ( $prefs['freetags_lowercase_only'] != 'y' ) $this->_normalize_in_lowercase = 0;
		if ( isset($prefs['freetags_ascii_only']) && $prefs['freetags_ascii_only'] != 'y' )
			$this->_normalized_valid_chars = '';
		else
			$this->_normalized_valid_chars = $prefs['freetags_normalized_valid_chars'];

		$this->multilingual = ( $prefs['freetags_multilingual'] == 'y'
			&& $prefs['feature_multilingual'] == 'y' );
	}

	/**
	 * get_objects_with_tag
	 *
	 * Use this function to build a page of results that have been tagged with the same tag.
	 * Pass along a user to collect only a certain user's tagged objects, and pass along
	 * none in order to get back all user-tagged objects. Most of the get_*_tag* functions
	 * operate on the normalized form of tags, because most interfaces for navigating tags
	 * should use normal form.
	 *
	 * @param string - Pass the normalized tag form along to the function.
	 * @param int (Optional) - The numerical offset to begin display at. Defaults to 0.
	 * @param int (Optional) - The number of results per page to show. Defaults to 100.
	 * @param int (Optional) - The unique ID of the 'user' who tagged the object.
	 *
	 * @return An array of Object ID numbers that reference your original objects.
	 */
	function get_objects_with_tag($tag, $type = '', $user = '', $offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '')
	{
		if (!isset($tag)) {
			return false;
		}

		$bindvals = array($tag);

		$mid = '';

		if (isset($user) && (!empty($user))) {
			$mid .= ' AND `user` = ?';
			$bindvals[] = $user;
		}

		if (isset($type) && !empty($type)) {
			$mid .= ' AND `type` = ?';
			$bindvals[] = $type;
		}

		if (isset($find) && !empty($find)) {
			$findesc = '%' . $find . '%';
			$mid .= ' AND (o.`name` like ? OR o.`description` like ?)';
			$bindvals = array_merge($bindvals, array($findesc, $findesc));
		}

		$query = 'SELECT DISTINCT o.*';
		$query_cant = 'SELECT COUNT(*)';

		$query_end =  ' FROM `tiki_objects` o, `tiki_freetagged_objects` fto, `tiki_freetags` t'
								. ' WHERE fto.`tagId` = t.`tagId` AND o.`objectId` = fto.`objectId` AND `tag` = ? ' . $mid 
								. ' ORDER BY o.' . $this->convertSortMode($sort_mode)
								;

		$query      .= $query_end;
		$query_cant .= $query_end;

		$result = $this->query($query, $bindvals, $maxRecords, $offset);

		$ret = array();
		while ($row = $result->fetchRow()) {
			$ret[] = $row;
		}

		$cant = $this->getOne($query_cant, $bindvals);

		return array('data' => $ret, 'cant' => $cant);
	}

	/**
	 * get_objects_with_tag_combo
	 *
	 * Returns an array of object ID's that have all the tags passed in the
	 * tagArray parameter. Use this to provide tag combo services to your users.
	 * 
	 * @param mixed $tagArray: array of normalized form tags along to the function.
	 * @param string $type 
	 * @param string $thisUser: Restrict the result to objects tagged by a particular user
	 * @param int $offset: The numerical offset to begin display at. Defaults to 0
	 * @param int $maxRecords:  The number of results per page to show. Defaults to 100
	 * @param string $sort_mode 
	 * @param string $find 
	 * @param string $broaden 
	 * @access public
	 * @return An array of Object ID numbers that reference your original objects
	 *
	 * Notes by nkoth:
	 * 1. The reason why using two queries here is because we can't get one query to work
	 * properly to return the right count of number of objects returned with duplicated objects
	 * 2. If you can fix this with subquery that works as far back as MSSQL 4.1, may be worth
	 * doing. But my experience with subquery is that it may be slower anyway.
	 */
	function get_objects_with_tag_combo($tagArray
																		, $type = ''
																		, $thisUser = ''
																		, $offset = 0
																		, $maxRecords = -1
																		, $sort_mode = 'name_asc'
																		, $find = ''
																		, $broaden = 'n'
																		) 
	{
		global $categlib;
		include_once('lib/categories/categlib.php');

		global $tiki_p_admin, $user, $smarty;
		if (!isset($tagArray) || !is_array($tagArray)) {
			return false;
		}

		if (count($tagArray) == 0) {
			return array('data' => array(), 'cant' => 0);
		}

		$bindvals = $tagArray;

		$numTags = count($tagArray);

		if (isset($thisUser) && !empty($thisUser)) {
			$mid = ' AND `user` = ?';
			$bindvals[] = $thisUser;
		} else {
			$mid = '';
		}

		$tag_sql = ' t.`tag` IN (?';
		for ($i = 1; $i<$numTags; $i++) {
			$tag_sql .= ',?';
		}
		$tag_sql .= ')';

		if ($broaden == 'n') {
			$bindvals_t = $bindvals;
			$mid_t = '';

			if (isset($thisUser) && !empty($thisUser)) {
				$mid_t = ' AND `user` = ?';
				$bindvals_t[] = $thisUser;
			}

			if (isset($type) && !empty($type)) {
				$mid_t .= ' AND `type` = ?';
				$bindvals_t[] = $type;
 			}

			if (isset($find) && !empty($find)) {
				$findesc = '%' . $find . '%';
				$mid_t .= ' AND (o.`name` like ? OR o.`description` like ?)';
				$bindvals_t = array_merge($bindvals_t, array($findesc, $findesc));
			}

			$bindvals_t[] = $numTags;

			$query_t = 'SELECT o.`objectId`, COUNT(DISTINCT t.`tag`) AS uniques ';
			$query_end_t = ' FROM `tiki_objects` o, `tiki_freetagged_objects` fto, `tiki_freetags` t'
									. ' WHERE ' . $tag_sql
									. ' AND fto.`tagId` = t.`tagId` AND o.`objectId` = fto.`objectId` ' . $mid_t
									. ' GROUP BY o.`objectId`'
									. ' HAVING uniques = ?'
									;
			$query_t .= $query_end_t;
			$result = $this->query($query_t, $bindvals_t, -1, 0);
			$ret = array();
			while ($row = $result->fetchRow()) {
				$ret[] = $row;
			}
			if ($numCats = count($ret)) {
				$tag_sql .= ' AND o.`objectId` IN (?';
				$bindvals[] = $ret[0]['objectId'];
				for ($i = 1; $i<$numCats; $i++) {
					$tag_sql .= ',?';
					$bindvals[] = $ret[$i]['objectId'];
				}
				$tag_sql .= ')';
			} else {
				return array('data' => array(), 'cant' => 0);
			}
		}

		$mid = '';

		if (isset($thisUser) && !empty($thisUser)) {
			$mid = ' AND `user` = ?';
			$bindvals[] = $thisUser;
		}

		if (isset($type) && !empty($type)) {
			$mid .= ' AND `type` = ?';
			$bindvals[] = $type;
		}

		if (isset($find) && !empty($find)) {
			$findesc = '%' . $find . '%';
			$mid .= ' AND (o.`name` like ? OR o.`description` like ?)';
			$bindvals = array_merge($bindvals, array($findesc, $findesc));
		}

		// We must adjust for duplicate normalized tags appearing multiple times in the join by
		// counting only the distinct tags. It should also work for an individual user.

		$query = 'SELECT DISTINCT o.*';
		$query_cant = 'SELECT COUNT(DISTINCT o.`objectId`)';

		$query_end = ' FROM `tiki_objects` o, `tiki_freetagged_objects` fto, `tiki_freetags` t'
								.	' WHERE fto.`tagId` = t.`tagId` AND o.`objectId` = fto.`objectId`'
								.	' AND ' . $tag_sql 
								. $mid 
								.	' ORDER BY ' . $this->convertSortMode($sort_mode)
								;
		// note the original line was originally here to fix ambiguous 'created' column for default sort.
		// Not a neat fix the o. prefix is ugly.	So changed default order instead.

		$query .= $query_end;
		$query_cant .= $query_end;

		$result = $this->query($query, $bindvals, $maxRecords, $offset);
		$cant = $this->getOne($query_cant, $bindvals);

		$ret = array();
		$permMap = $categlib->map_object_type_to_permission();
		while ($row = $result->fetchRow()) {
			$ok = false;
			if ($tiki_p_admin == 'y') {
				$ok = true;
			} elseif ($row['type'] == 'blog post') {
				$bloglib = TikiLib::lib('blog');
				$post_info = $bloglib->get_post($row['itemId']);
				if ($this->user_has_perm_on_object($user, $post_info['blogId'], 'blog', 'tiki_p_read_blog')) {
					$ok = true;
				}
			} elseif ($this->user_has_perm_on_object($user, $row['itemId'], $row['type'], $permMap[$row['type']])) {
				$ok = true;
			}
			if ($ok) {
				global $tikilib;
				if ( ! empty( $row['description'] ) ) {
					$row['description'] = $tikilib->parse_data( $row['description'], array( 'absolute_links' => true ) );
				}
				if ($prefs['feature_sefurl'] == 'y') {
					include_once('tiki-sefurl.php');
					if ($row['type'] == 'blog post' && !empty($post_info)) {
						$row['href'] = filter_out_sefurl($row['href'], $smarty, 'blogpost', $post_info['title']);
					} else {
						$type = ($row['type'] == 'wiki page') ? 'wiki' : ($row['type'] == 'blog post'? 'blogpost': $row['type']);
						$row['href'] = filter_out_sefurl($row['href'], $smarty, $type);
					}
				}
				$ret[] = $row;
			} else {
				-- $cant;
			}
		}
		return array('data' => $ret, 'cant' => $cant);
	}

	/**
	 * get_objects_with_tag_id
	 *
	 * Use this function to build a page of results that have been tagged with the same tag.
	 * This function acts the same as get_objects_with_tag, except that it accepts a numerical
	 * tag_id instead of a text tag.
	 * Pass along a user to collect only a certain user's tagged objects, and pass along
	 * none in order to get back all user-tagged objects.
	 *
	 * @param int - Pass the ID number of the tag.
	 * @param int (Optional) - The numerical offset to begin display at. Defaults to 0.
	 * @param int (Optional) - The number of results per page to show. Defaults to 100.
	 * @param int (Optional) - The unique ID of the 'user' who tagged the object.
	 *
	 * @return An array of Object ID numbers that reference your original objects.
	 */
	function get_objects_with_tag_id($tagId, $user = '', $offset = 0, $maxRecords = -1)
	{
		if (!isset($tagId)) {
			return false;
		}

		$bindvals = array($tagId);

		if (isset($user) && empty($user)) {
			$mid = ' AND `user` = ?';
			$bindvals[] = $user;
		} else {
			$mid = '';
		}

		$query = 'SELECT DISTINCT o.* ';
		$query_cant = 'SELECT COUNT(*) ';

		$query_end = ' FROM `tiki_freetagged_objects` fto, `tiki_freetags` t, `tiki_objects` o'
								. ' WHERE t.`tagId` = ? AND fto.`tagId` = t.`tagId`'
									.	' AND o.`objectId` = fto.`objectId` '
								. $mid
								;

		$query .= $query_end;
		$query_cant .= $query_end;

		$result = $this->query($query, $bindvals, $maxRecords, $offset);

		$ret = array();
		while ($row = $result->fetchRow()) {
			$ret[] = $row;
		}

		$cant = $this->getOne($query_cant, $bindvals);

		return array('data' => $ret, 'cant' => $cant);
	}

	/**
	 * get_tags_on_object 
	 * 
	 * You can use this function to show the tags on an object. Since it supports both user-specific
	 * and general modes with the $user parameter, you can use it twice on a page to make it work
	 * similar to upcoming.org and flickr, where the page displays your own tags differently than
	 * other users' tags.
	 *
	 * @param int $itemId The unique ID of the object in question
	 * @param int $type 
	 * @param int $offset The offset of tags to return
	 * @param int $maxRecords The size of the tagset to return
	 * @param int $user The unique ID of the person who tagged the object, if user-level tags only are preferred
	 * @access public
	 *
	 * @return array Returns a PHP array with object elements ordered by object ID. Each element is an associative
	 * array with the following elements:
	 *	 - 'tag' => Normalized-form tag
	 *	 - 'raw_tag' => The raw-form tag
	 *	 - 'user' => The unique ID of the person who tagged the object with this tag.
	 */
	function get_tags_on_object($itemId, $type, $offset = 0, $maxRecords = -1, $user = NULL)
	{
		if (!isset($itemId) || !isset($type) || empty($itemId) || empty($type) || is_array($itemId) || !is_string($type)) {
			return false;
		}

		$bindvals = array($itemId, $type);

		if (isset($user) && (!empty($user))) {
			$mid = 'AND `user` = ?';
			$bindvals[] = $user;
		} else {
			$mid = '';
		}

		$query = 'SELECT DISTINCT t.`tagId`, `tag`, `raw_tag`, `user`, `lang`'
						. ' FROM `tiki_objects` o,'
						. ' `tiki_freetagged_objects` fto,'
						. ' `tiki_freetags` t'
						. ' WHERE t.`tagId` = fto.`tagId`'
							. ' AND fto.`objectId` = o.`objectId`'
							. ' AND o.`itemId` = ?'
							. ' AND o.`type` = ? ' . $mid
						;

		$result = $this->query($query, $bindvals, $maxRecords, $offset);

		$ret = array();
		$cant = 0;
		while ($row = $result->fetchRow()) {
			$ret[] = $row;
			$cant++;
		}

		return array('data' => $ret, 'cant' => $cant);
	}

	/**
	 *
	 */
	/**
	 * get_all_tags_on_object_for_language 
	 * 
	 * Derived from get_tags_on_object. The method extracts all tags for an object
	 * and attempts to find a translation in a given language. If no translation
	 * exists at this time, the original tag will be used.
	 * This method is to be used when translating a page to create the initial set
	 * of tags.
	 *
	 * @param mixed $itemId 
	 * @param mixed $type 
	 * @param mixed $lang 
	 * @access public
	 * @return
	 */
	function get_all_tags_on_object_for_language($itemId, $type, $lang )
	{
		if (!isset($itemId) || !isset($type) || empty($itemId) || empty($type)) {
			return false;
		}

		$query = 'SELECT DISTINCT tra.tag tratag, orig.tag srctag'
						. ' FROM `tiki_objects` o'
							. ' INNER JOIN tiki_freetagged_objects fo ON o.objectId = fo.objectId'
							. ' INNER JOIN tiki_freetags orig ON fo.tagId = orig.tagId'
							. ' LEFT JOIN tiki_translated_objects tos ON tos.type = \'freetag\' AND fo.tagId = tos.objId'
							. ' LEFT JOIN tiki_translated_objects tot ON tot.type = \'freetag\' AND tos.traId = tot.traId'
							. ' LEFT JOIN tiki_freetags tra ON tot.objId = tra.tagId AND tra.lang = ?'
						. ' WHERE'
							. ' o.`itemId` = ?'
							. ' AND o.`type` = ?'
						;

		$result = $this->query($query, array($lang, $itemId, $type));

		$tra = array();
		$orig = array();
		while ($row = $result->fetchRow()) {
			if ( empty( $row['tratag'] ) )
				$orig[] = $row['srctag'];
			else
				$tra[$row['srctag']] = $row['tratag'];
		}

		return array_merge(array_values( $tra ), array_diff( $orig, array_keys( $tra ) ));
	}

	/**
	 * find_or_create_tag 
	 * 
	 * @param mixed $tag 
	 * @param mixed $lang 
	 * @param mixed $anyLanguage 
	 * @access public
	 * @return tagId
	 */
	function find_or_create_tag( $tag, $lang = null, $anyLanguage = true )
	{
		$normalized_tag = $this->normalize_tag($tag);

		$mid = '';
		$bindvars = array();

		// Then see if a raw tag in this form exists.
		$mid .= ' (`raw_tag` = ? OR `tag` = ?)';
		$bindvars[] = $tag;
		$bindvars[] = $normalized_tag;

		if ($this->multilingual && $lang && ! $anyLanguage) {
			$mid .= ' AND `lang` = ?'; // null lang means universal
			$bindvars[] = $lang;
		}

		$query = 'SELECT `tagId`'
						. ' FROM `tiki_freetags`'
						. ' WHERE ' .  $mid
						. ' ORDER BY CASE WHEN lang = ? THEN 0 WHEN lang IS NULL THEN 1 ELSE 2 END'
						;

		$bindvars[] = $lang;

		$result = $this->query($query, $bindvars);

		if ($row = $result->fetchRow()) {
			$tagId = $row['tagId'];
		} else {
			// Add new tag!
			if ($this->multilingual && $lang ) {
				$query = 'INSERT INTO `tiki_freetags` (`tag`, `raw_tag`, `lang`) VALUES (?,?,?)';
				$bindvals = array($normalized_tag, $tag, $lang);
				$this->query($query, $bindvals);
			} else {
				$query = 'INSERT INTO `tiki_freetags` (`tag`, `raw_tag`) VALUES (?,?)';
				$bindvals = array($normalized_tag, $tag);
				$this->query($query, $bindvals);
			}

			$query = 'SELECT MAX(`tagId`) FROM `tiki_freetags` WHERE `tag`=? AND `raw_tag`=?';
			$tagId = $this->getOne($query, array_slice( $bindvals, 0, 2 ) );
		}

		if (!($tagId > 0)) {
			return false;
		}

		return $tagId;
	}

	/**
	 * safe_tag
	 *
	 * Pass individual tag phrases along with object and person ID's in order to
	 * set a tag on an object. If the tag in its raw form does not yet exist,
	 * this function will create it.
	 * Fails transparently on duplicates, and checks for dupes based on the
	 * block_multiuser_tag_on_object constructor param.
	 *
	 * @param int The unique ID of the person who tagged the object with this tag.
	 * @param int The unique ID of the object in question.
	 * @param string A raw string from a web form containing tags.
	 * @param string The language of the tag.
	 *
	 * @return Returns true if successful, false otherwise. Does not operate as a transaction.
	 */

	function safe_tag($user, $itemId, $type, $tag, $lang = null)
	{
		if (!isset($itemId) || !isset($type) || !isset($tag) ||
				empty($itemId) || empty($type) || empty($tag)) {
			throw new Exception('Missing safe_tag argument.');
		}

		// To be sure that the tag lenght is correct.
		// If multibyte string functions are available, it's preferable to use them.
		if ((function_exists('mb_strlen') && (mb_strlen($tag) >= $this->_MAX_TAG_LENGTH))
				|| (strlen($tag) >= $this->_MAX_TAG_LENGTH)
		) {
			return false;
		}

		$normalized_tag = $this->normalize_tag($tag);
		$bindvals = array($itemId, $type, $normalized_tag);

		$mid = '';

		// First, check for duplicate of the normalized form of the tag on this object.
		// Dynamically switch between allowing duplication between users on the
		// constructor param 'block_multiuser_tag_on_object'.
		if (!$this->_block_multiuser_tag_on_object) {
			$mid .= ' AND user = ?';
			$bindvals[] = $user;
		}

		if ($this->multilingual && $lang) {
			$mid .= ' AND (`lang` = ? OR `lang` IS NULL)'; // null lang means universal
			$bindvals[] = $lang;
		}

		$query = 'SELECT COUNT(*)'
						. ' FROM `tiki_objects` o,'
							. ' `tiki_freetagged_objects` fto,'
							. ' `tiki_freetags` t'
						. ' WHERE fto.`tagId` = t.`tagId`'
							. ' AND fto.`objectId` = o.`objectId`'
							. ' AND o.`itemId` = ?'
							. ' AND o.`type` = ?'
							. ' AND t.`tag` = ? ' . $mid
						;

		if ($this->getOne($query, $bindvals) > 0) {
			return true;
		}

		$tagId = $this->find_or_create_tag( $tag, $lang, false );

		$objectId = $this->add_object($type, $itemId);

		$query = 'INSERT INTO `tiki_freetagged_objects`'
						. ' (`tagId`, `objectId`, `user`, `created`)'
						. ' VALUES (?, ?, ?, ?)'
						;
		$bindvals = array($tagId, $objectId, $user, time());

		$this->query($query, $bindvals);

		return true;
	}

	/**
	 * normalize_tag
	 *
	 * This is a utility function used to take a raw tag and convert it to normalized form.
	 * Normalized form is essentially lowercased alphanumeric characters only,
	 * with no spaces or special characters.
	 *
	 * Customize the normalized valid chars with your own set of special characters
	 * in regex format within the option 'normalized_valid_chars'. It acts as a filter
	 * to let a customized set of characters through.
	 *
	 * After the filter is applied, the function also lowercases the characters using strtolower
	 * in the current locale.
	 *
	 * The default for normalized_valid_chars is a-zA-Z0-9, or english alphanumeric.
	 *
	 * @param tag string An individual tag in raw form that should be normalized.
	 *
	 * @return string Returns the tag in normalized form.
	 */
	function normalize_tag($tag)
	{
		if (!empty($this->_normalized_valid_chars) && $this->_normalized_valid_chars != '*') {
			$normalized_valid_chars = $this->_normalized_valid_chars;
			$tag = preg_replace("/[^$normalized_valid_chars]/", '', $tag);
		}
		
		if (function_exists('mb_strtolower')) {
			return $this->_normalize_in_lowercase ? mb_strtolower($tag, 'UTF-8') : $tag;
		} else {
			return $this->_normalize_in_lowercase ? strtolower($tag) : $tag;
		}
	}

	/**
	 * delete_object_tag
	 *
	 * Removes a tag from an object. This does not delete the tag itself from
	 * the database. Since most applications will only allow a user to delete
	 * their own tags, it supports raw-form tags as its tag parameter, because
	 * that's what is usually shown to a user for their own tags.
	 *
	 * @param int The unique ID of the person who tagged the object with this tag.
	 * @param int The ID of the object in question.
	 * @param string The raw string or the string form of the tag to delete.
	 *
	 * @return string Returns the tag in normalized form.
	 */
	function delete_object_tag($itemId, $type, $tag, $user = false)
	{
		if (!isset($itemId) || !isset($type) || !isset($tag) ||
				empty($itemId) || empty($type) || empty($tag)) {
			die('delete_object_tag argument missing');
			return false;
		}

		$objectId = $this->get_object_id($type, $itemId);
		$query = 'DELETE FROM `tiki_freetagged_objects`'
						. ' WHERE `objectId`=? AND `tagId` IN('
							.	' SELECT tagId'
							. ' FROM tiki_freetags'
							.	' WHERE raw_tag = ? OR tag = ?)'
						;

		$bindvars = array($objectId, $tag, $tag);
		if ($user) {
			$query.= ' and `user`=?';
			$bindvars[] = $user;
		}
		$this->query($query, $bindvars);

		$this->cleanup_tags();
		return true;
	}

	/**
	 * delete_all_object_tags_for_user
	 *
	 * Removes all tag from an object for a particular user. This does not
	 * delete the tag itself from the database. This is most useful for
	 * implementations similar to del.icio.us, where a user is allowed to retag
	 * an object from a text box. That way, it becomes a two step operation of
	 * deleting all the tags, then retagging with whatever's left in the input.
	 *
	 * @param int The unique ID of the person who tagged the object with this tag.
	 * @param int The ID of the object in question.
	 *
	 * @return string Returns the tag in normalized form.
	 */
	function delete_all_object_tags_for_user($user, $itemId, $type)
	{
		if (!isset($user) || !isset($itemId) || !isset($type)
				|| empty($user) || empty($itemId) || empty($type)) {
			die('delete_all_object_tags_for_user argument missing');
			return false;
		}


		if ( !($itemId > 0)) {
			return false;
		} else {

			$objectId = $this->get_object_id($type, $itemId);

			$query = 'DELETE FROM `tiki_freetagged_objects`'
							. ' WHERE `user` = ?'
							. ' AND `objectId` = ?'
							;

			$bindvals = array($$user, $objectId);

			$this->query($query, $bindvals);

			return true;
		}
	}

	/**
	 * get_tag_id
	 *
	 * Retrieves the unique ID number of a tag based upon its normal form. Actually,
	 * using this function is dangerous, because multiple tags can exist with the same
	 * normal form, so be careful, because this will only return one, assuming that
	 * if you're going by normal form, then the individual tags are interchangeable.
	 *
	 * @param string The normal form of the tag to fetch.
	 *
	 * @return string Returns the tag in normalized form.
	 */
	function get_tag_id($tag)
	{
		if (!isset($tag) || empty($tag)) {
			die('get_tag_id argument missing');
			return false;
		}

		$query = 'SELECT `tagId` FROM `tiki_freetags`'
						. ' WHERE `tag` = ?'
						;

		return $this->getOne($query, array($tag));
	}

	/**
	 * get_raw_tag_id
	 *
	 * Retrieves the unique ID number of a tag based upon its raw form. If a single
	 * unique record is needed, then use this function instead of get_tag_id,
	 * because raw_tags are unique.
	 *
	 * @param string The raw string form of the tag to fetch.
	 *
	 * @return string Returns the tag in normalized form.
	 */
	function get_raw_tag_id($tag)
	{
		if (!isset($tag) || empty($tag)) {
			die('get_tag_id argument missing');
			return false;
		}

		$query = 'SELECT `tagId` FROM `tiki_freetags`'
						. ' WHERE `raw_tag` = ?'
						;

		return $this->getOne($query, array($tag));
	}

	/**
	 * tag_object
	 *
	 * This function allows you to pass in a string directly from a form, which is then
	 * parsed for quoted phrases and special characters, normalized and converted into tags.
	 * The tag phrases are then individually sent through the safe_tag() method for processing
	 * and the object referenced is set with that tag.

	 * @param int The unique ID of the person who tagged the object with this tag.
	 * @param int The ID of the object in question.
	 * @param string The raw string form of the tag to delete. See above for notes.
	 *
	 * @return string Returns the tag in normalized form.
	 */
	function tag_object($user, $itemId, $type, $tag_string, $lang = null)
	{
		if ($tag_string == '') {
			return true;
		}

		// Perform tag parsing
		$tagArray = $this->_parse_tag($tag_string);

		$this->_tag_object_array($user, $itemId, $type, $tagArray, $lang);

		return true;
	}

	/**
	 * update_tags 
	 * 
	 * @param mixed $user 
	 * @param mixed $itemId 
	 * @param mixed $type 
	 * @param mixed $tag_string 
	 * @param mixed $old_user 
	 * @param mixed $lang 
	 * @access public
	 * @return void
	 */
	function update_tags($user, $itemId, $type, $tag_string, $old_user = false, $lang = null)
	{

		// Perform tag parsing
		$tagArray = $this->_parse_tag($tag_string);

		$oldTags = $this->get_tags_on_object($itemId, $type, 0, -1, $old_user);

		foreach ($oldTags['data'] as $tag) {
			if (!in_array($tag['raw_tag'], $tagArray)) {
				$this->delete_object_tag($itemId, $type, $tag['raw_tag'], $old_user);
			}
		}

		$this->_tag_object_array($user, $itemId, $type, $tagArray, $lang);

		return true;
	}

	/**
	 * _parse_tag 
	 * 
	 * @param mixed $tag_string 
	 * @access protected
	 * @return
	 */
	function _parse_tag($tag_string)
	{
		if (get_magic_quotes_gpc()) {
			$query = stripslashes(trim($tag_string));
		} else {
			$query = trim($tag_string);
		}

		$words = preg_split('/(")/', $query, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
		$delim = 0;
		$newwords = array();
		foreach ($words as $key => $word)
		{
			if ($word == '"') {
				$delim++;
				continue;
			}
			if (($delim % 2 == 1) && $words[$key - 1] == '"') {
				$newwords[] = $word;
			} else {
				$newwords = array_merge($newwords, preg_split('/\s+/', $word, -1, PREG_SPLIT_NO_EMPTY));
			}
		}

		return $newwords;
	}

	/**
	 * _tag_object_array 
	 * 
	 * @param mixed $user 
	 * @param mixed $itemId 
	 * @param mixed $type 
	 * @param mixed $tagArray 
	 * @param mixed $lang 
	 * @access protected
	 * @return void
	 */
	function _tag_object_array($user, $itemId, $type, $tagArray, $lang = null)
	{

		foreach($tagArray as $tag) {
			$tag = trim($tag);
			if ($tag != '') {
				if (!get_magic_quotes_gpc()) {
					$tag = addslashes($tag);
				}
				$this->safe_tag($user, $itemId, $type, $tag, $lang);
			}
		}
	}

	/**
	 * get_most_popular_tags
	 *
	 * This function returns the most popular tags in the freetag system, with
	 * offset and limit support for pagination. It also supports restricting to
	 * an individual user. Call it with no parameters for a list of 25 most popular
	 * tags.
	 *
	 * @param int The unique ID of the person to restrict results to.
	 * @param int The offset of the tag to start at.
	 * @param int The number of tags to return in the result set.
	 *
	 * @return array Returns a PHP array with tags ordered by popularity descending.
	 * Each element is an associative array with the following elements:
	 *	 - 'tag' => Normalized-form tag
	 *	 - 'count' => The number of objects tagged with this tag.
	 */

	function get_most_popular_tags($user = '', $offset = 0, $maxRecords = 25)
	{

		// get top tag popularity
		$query = 'SELECT COUNT(*) as count'
						. ' FROM `tiki_freetagged_objects` o'
						. ' GROUP BY `tagId`'
						. ' ORDER BY count DESC'
						;

		$top = $this->getOne($query);

		$bindvals = array();

		if (isset($user) && (!empty($user))) {
			$mid = 'AND `user` = ?';
			$bindvals[] = $user;
		} else {
			$mid = '';
		}

		$query = 'SELECT `tag`, COUNT(*) as count'
						. ' FROM `tiki_freetags` tf, `tiki_freetagged_objects` tfo'
						. ' WHERE tf.`tagId`= tfo.`tagId` ' . $mid
						. ' GROUP BY `tag`'
						. ' ORDER BY count DESC, tag ASC'
						;

		$result = $this->query($query, $bindvals, $maxRecords, $offset);

		$ret = array();
		$tag = array();
		$count = array();

		while ($row = $result->fetchRow()) {
			$row['size'] = ceil(1 +(1 + $row['count'] / $top) * log(1 + $row['count']));
			if ($row['size'] > $this->max_cloud_text_size) {
				$row['size'] = $this->max_cloud_text_size;
			}
			$size[] = $row['size'];
			$tag[] = $row['tag'];
			$count[] = $row['count'];

			$ret[] = $row;
		}

		array_multisort($tag, SORT_ASC, $count, SORT_DESC, $ret);

		return $ret;
	}

	/**
	 * get_tag_suggestion
	 *
	 * This function returns the a set of tags to suggest to user.
	 * While it will statistically retrieve most popular more often,
	 * it has a random factor for new patterns to emerge.
	 *
	 * @param string A string containing all tags object has, to be avoided
	 * @param int The number of tags to return in the result set.
	 *
	 * @return array Returns a PHP array with tags ordered randomly
	 */

	function get_tag_suggestion($exclude = '', $max = 10, $lang = null)
	{
		$query = 'SELECT t.* FROM `tiki_freetags` t, `tiki_freetagged_objects` o'
						. ' WHERE t.`tagId` = o.`tagId`'
						. ' AND (`lang` = ? or `lang` IS null)'
						. ' ORDER BY ' . $this->convertSortMode('random');

		$result = $this->query($query, array( $lang ));

		$tags = array();
		$index = array();
		while (count($tags) < $max && $row = $result->fetchRow()) {
			$tag = $row['tag'];
			if (!isset($index[$tag]) && !preg_match("/$tag/", $exclude)) {
				$tags[] = $tag;
				$index[$tag] = 1;
			}
		}

		return $tags;
	}

	/**
	 * count_tags
	 *
	 * Returns the total number of tag->object links in the system.
	 * It might be useful for pagination at times, but i'm not sure if I actually use
	 * this anywhere. Restrict to a person's tagging by using the $user parameter.
	 *
	 * @param int The unique ID of the person to restrict results to.
	 *
	 * @return int Returns the count
	 */
	function count_tags($user = '')
	{
		$bindvals = array();

		if (isset($user) && (!empty($user))) {
			$mid = 'AND `user` = ?';
			$bindvals[] = $user;
		} else {
			$mid = '';
		}

		$query = 'SELECT COUNT(*)'
						. ' FROM `tiki_freetags` t, `tiki_freetagged_objects` o'
						. ' WHERE o.`tagId` = t.`tagId` ' . $mid
						;

		return $this->getOne($query, $bindvals);

	}

	/**
	 * silly_list
	 *
	 * This is a function built explicitly to set up a page with most popular tags
	 * that contains an alphabetically sorted list of tags, which can then be sized
	 * or colored by popularity.
	 *
	 * Also known more popularly as Tag Clouds!
	 *
	 * Here's the example case: http://upcoming.org/tag/
	 *
	 * @param int The maximum number of tags to return.
	 *
	 * @return array Returns an array where the keys are normalized tags, and the
	 * values are numeric quantity of objects tagged with that tag.
	 */
	function silly_list($max = 100)
	{
		$query = 'SELECT `tag`, COUNT(`objectId`) AS quantity'
						. ' FROM `tiki_freetags` t, `tiki_freetagged_objects` o'
						. ' WHERE t.`tagId` = o.`tagId`'
						. ' GROUP BY `tag`'
						. ' ORDER BY quantity DESC'
						;

		$result = $this->query($query, array(), $max, 0);

		$ret = array();
		while ($row = $result->fetchRow()) {
			$ret[] = $row;
		}

		return $ret;
	}

	/**
	 * similar_tags
	 *
	 * Finds tags that are "similar" or related to the given tag.
	 * It does this by looking at the other tags on objects tagged with the tag specified.
	 * Confusing? Think of it like e-commerce's "Other users who bought this also bought,"
	 * as that's exactly how this works.
	 *
	 * Returns an empty array if no tag is passed, or if no related tags are found.
	 * Hint: You can detect related tags returned with count($retarr > 0)
	 *
	 * It's important to note that the quantity passed back along with each tag
	 * is a measure of the *strength of the relation* between the original tag
	 * and the related tag. It measures the number of objects tagged with both
	 * the original tag and its related tag.
	 *
	 * Thanks to Myles Grant for contributing this function!
	 *
	 * @param string The raw normalized form of the tag to fetch.
	 * @param int The maximum number of tags to return.
	 *
	 * @return array Returns an array where the keys are normalized tags, and the
	 * values are numeric quantity of objects tagged with BOTH tags, sorted by
	 * number of occurences of that tag (high to low).
	 */
	function similar_tags($tag, $max = 100)
	{

		if (!isset($tag) || empty($tag)) {
			return array();
		}

		$query = 'SELECT t1.`tag`, COUNT( o1.`objectId` ) AS quantity'
						. ' FROM `tiki_freetagged_objects` o1'
						. ' INNER JOIN `tiki_freetags` t1 ON ( t1.`tagId` = o1.`tagId` )'
						. ' INNER JOIN `tiki_freetagged_objects` o2 ON ( o1.`objectId` = o2.`objectId` )'
						. ' INNER JOIN `tiki_freetags` t2 ON ( t2.`tagId` = o2.`tagId` )'
						. ' WHERE t2.`tag` = ? AND t1.`tag` <> ?'
						. ' GROUP BY o1.`tagId`'
						. ' ORDER BY quantity DESC'
						;

		$bindvals = array($tag, $tag);

		$result = $this->query($query, $bindvals, $max, 0);

		$ret = array();
		while ($row = $result->fetchRow()) {
			$ret[] = $row;
		}

		return $ret;
	}

	/*
	 * nkoth: This function is for the More Like This module which find out what
	 * similar objects there are based on the number of tags they have in common.
	 * Once you have enough tags, the results are quite good. It is very organic
	 * as tagging is human-technology.
	 */
	function get_similar( $type, $objectId, $maxResults = 10, $targetType = null, $with = 'freetag', $minCommon=null )
	{
		global $prefs;
		if ($with == 'category') {
			$algorithm = $this->get_preference('category_morelikethis_algorithm', 'basic');
			if (empty($minCommon))
				$minCommon = (int) $this->get_preference( 'category_morelikethis_mincommon', 2 );
			$table = 'tiki_category_objects';
			$column = 'categId';
			$objectColumn = 'catObjectId';
		} else {
			$algorithm = $this->get_preference('morelikethis_algorithm', 'basic');
			$minCommon = (int) $this->get_preference( 'morelikethis_basic_mincommon', 2 );
			$table = 'tiki_freetagged_objects';
			$column = 'tagId';
			$objectColumn = 'objectId';
		}

		if ( is_null( $targetType ) ) {
			$targetType = $type;
		}

		$maxResults = (int) $maxResults;
		if ( $maxResults <= 0 )
			$maxResults = 10;

		$mid = ' oa.objectId <> ob.objectId	AND ob.type = ? AND oa.type = ? AND oa.itemId = ?';
		$bindvals = array($targetType, $type, $objectId);

		if ($prefs['feature_multilingual'] == 'y' && $type == 'wiki page' && $targetType == 'wiki page') {
			// make sure only same lang pages are selected
			$mid .= ' AND (pb.`lang` = pa.`lang` OR pa.`lang` IS NULL OR pb.`lang` IS NULL) ';
			$join_tiki_pages = 'INNER JOIN `tiki_pages` pa ON pa.`pageName` = oa.itemId'
							. ' INNER JOIN `tiki_pages` pb ON pb.`pageName` = ob.`itemId`'
							;
		} else {
			$join_tiki_pages = '';
		}

		switch( $algorithm )
		{
		case 'basic': // {{{
			$query = "SELECT ob.`name`, ob.`href`, COUNT(DISTINCT fb.`$column`) cnt"
							. ' FROM `tiki_objects` oa'
							. " INNER JOIN `$table` fa ON oa.`objectId` = fa.`$objectColumn`"
							. " INNER JOIN $table fb USING(`$column`)"
							. " INNER JOIN `tiki_objects` ob ON ob.`objectId` = fb.`$objectColumn` "
							. $join_tiki_pages
							. ' WHERE ' . $mid
							. ' GROUP BY ob.`itemId`'
							. ' HAVING cnt >= ?'
							. ' ORDER BY cnt DESC, RAND()'
							;
			break;
		// }}}

		case 'weighted': // {{{
			$query = "SELECT ob.`name`, ob.`href`, COUNT(DISTINCT fc.`$objectColumn`) sort_cnt, COUNT(DISTINCT fb.`$column`) having_cnt"
							. ' FROM `tiki_objects` oa'
							. " INNER JOIN $table fa ON oa.`objectId` = fa.`$objectColumn`"
							. " INNER JOIN $table fb USING(`$column`)"
							. " INNER JOIN `tiki_objects` ob ON ob.`objectId` = fb.`$objectColumn`"
							. " INNER JOIN $table fc ON fb.`$column` = fc.`$column` "
							. $join_tiki_pages
							. ' WHERE ' . $mid
							. ' GROUP BY ob.`itemId`'
							. ' HAVING having_cnt >= ?'
							. ' ORDER BY sort_cnt DESC, RAND()'
							;
			// Sort based on the global popularity of all tags in common
			break;
		// }}}
		}

		$bindvals[] = $minCommon;

		$result = $this->query( $query, $bindvals, $maxResults );
		$tags = array();
		while( $row = $result->fetchRow() )
			$tags[] = $row;

		if (empty($tags) && $prefs['category_morelikethis_mincommon_orless'] == 'y' && $with == 'category' && $minCommon > 1) {
			return $this-> get_similar( $type, $objectId, $maxResults, $targetType, $with, $minCommon-1 );
		} else {
			return $tags;
		}
	}

	/**
	 * cleanup_tags Remove all tags that are orphaned (i.e. not used)
	 * 
	 * @access public
	 * @return true
	 */
	function cleanup_tags()
	{
		$this->query('DELETE FROM `tiki_freetagged_objects` WHERE `tagId` NOT IN(SELECT `tagId` FROM `tiki_freetags`)' );
		$this->query('DELETE tfo FROM `tiki_freetagged_objects` tfo LEFT JOIN `tiki_objects` tob ON (tob.`objectId` = tfo.`objectId`) WHERE tob.`objectId` IS null');
		$this->query('DELETE FROM `tiki_freetags`'
								. ' WHERE `tagId` NOT IN(SELECT `tagId` FROM `tiki_freetagged_objects`)'
								. ' AND `tagId` NOT IN(SELECT `objId` FROM `tiki_translated_objects` WHERE type = \'freetag\')' 
								);
		return true;
	}

	/**
	 * get_object_tags_multilingual 
	 * 
	 * @param mixed $type 
	 * @param mixed $objectId 
	 * @param mixed $accept_languages 
	 * @param mixed $offset 
	 * @param mixed $maxRecords 
	 * @access public
	 * @return
	 */
	function get_object_tags_multilingual( $type, $objectId, $accept_languages, $offset, $maxRecords ) 
	{
		$mid = '';
		$bindvars = array();

		$mid .= 'o.type = ?';
		$bindvars[] = $type;

		if ($objectId) {
			$mid .= ' AND o.itemId = ?';
			$bindvars[] = $objectId;
		}

		$query = 'SELECT DISTINCT'
						. ' fo.tagId tagset, tag.tagId, tag.lang, tag.tag, traId'
						. ' FROM tiki_objects o'
						. ' INNER JOIN tiki_freetagged_objects fo ON o.objectId = fo.objectId'
						. ' INNER JOIN tiki_freetags tag ON fo.tagId = tag.tagId'
						. ' LEFT JOIN tiki_translated_objects `to` ON to.type = \'freetag\' AND to.objId = fo.tagId'
						. ' WHERE ' . $mid
						. ' AND (tag.lang IS NULL OR tag.lang IN(' 
							. implode(',', array_fill(0, count($accept_languages), '?')) 
						. ') )'
						;

		$result = $this->fetchAll( $query, array_merge( $bindvars, $accept_languages ), $maxRecords, $offset );
		$translationSets = array_map( 'end', $result );
		$translationSets = array_filter( $translationSets );

		$tags = $this->get_tag_translations( $translationSets, $accept_languages );

		$ret = array();
		$encountered = array();
		foreach( $result as $row ) {
			$group = $row['tagset'];
			$lang = $row['lang'];

			if ( array_key_exists( $row['tagId'], $encountered ) )
				continue;

			if ( !array_key_exists( $group, $ret ) )
				$ret[$group] = array();

			$ret[$group][$lang] = $row;
			$encountered[ $row['tagId'] ] = true;

			if ( $row['traId'] ) {
				foreach( $tags[ $row['traId'] ] as $tag ) {
					$ret[$group][$tag['lang']] = $tag;
					$encountered[ $tag['tagId'] ] = true;
				}
			}
		}

		return $ret;
	}

	/**
	 * get_tag_translations 
	 * 
	 * @param mixed $sets 
	 * @param mixed $languages 
	 * @access private
	 * @return
	 */
	private function get_tag_translations( $sets, $languages )
	{
		if ( count( $sets ) == 0 ) {
			return array();
		}

		$result = $this->fetchAll( 'SELECT tag.tagId, tag.lang, tag.tag, traId'
															. ' FROM tiki_freetags tag'
															. ' INNER JOIN tiki_translated_objects `to` ON to.type = \'freetag\''
															. ' AND tag.tagId = to.objId'
															. ' WHERE'
																. ' to.traId IN(' . implode( ', ', $sets ) . ' ) '
																. ' AND tag.lang IN(' . implode(',', array_fill(0, count($languages), '?')) . ')'
															, $languages 
															);

		$ret = array_fill_keys( $sets, array() );
		foreach( $result as $row ) {
			$ret[ $row['traId'] ][] = $row;
		}

		return $ret;
	}

	/**
	 * set_tag_language 
	 * 
	 * @param mixed $tagId 
	 * @param mixed $lang 
	 * @access public
	 * @return
	 */
	function set_tag_language( $tagId, $lang )
	{
		if ( ! $this->is_valid_language( $lang ) )
			return;

		$result = $this->query('SELECT tagId'
										. ' FROM tiki_freetags'
										. ' WHERE'
										. ' tag = (SELECT tag FROM tiki_freetags WHERE tagId = ?)'
										. ' AND tagId <> ?'
										. ' AND lang = ?'
										, array( $tagId, $tagId, $lang )
										);

		$equiv = array();
		while( $row = $result->fetchRow() )
			$equiv[] = $row['tagId'];

		if ( count( $equiv ) > 0 ) {
			// Target already exists, merge em

			$master = array_pop( $equiv );
			$equiv[] = $tagId;

			// Clear potential duplicates.
			$equivStr = implode( ',', $equiv );
			$result = $this->query( 'SELECT objectId'
														. ' FROM tiki_freetagged_objects'
														. ' WHERE tagId IN($equivStr) AND objectId IN(SELECT objectId'
														. ' FROM tiki_freetagged_objects WHERE tagId = ?)'
														, array( $master )
														);

			while( $row = $result->fetchRow() )
				$this->query( 'DELETE FROM tiki_freetagged_objects'
										. ' WHERE objectId = ? AND tagId IN(' . $equivStr . ')'
										, array( $row['objectId'] ) 
										);

			foreach( $equiv as $clone )
			{
				$this->query( 'UPDATE tiki_freetagged_objects SET tagId = ? WHERE tagId = ?'
										, array( $master, $clone )
										);
				$this->query( 'DELETE FROM tiki_freetags WHERE tagId = ?'
										, array( $clone )
										);
			}
		} else {
			$this->query( 'UPDATE tiki_freetags SET lang = ? WHERE tagId = ?'
									, array( $lang, $tagId ) 
									);
		}
	}

	/**
	 * translate_tag 
	 * 
	 * @param mixed $srcLang 
	 * @param mixed $srcTagId 
	 * @param mixed $dstLang 
	 * @param mixed $content 
	 * @access public
	 * @return void
	 */
	function translate_tag( $srcLang, $srcTagId, $dstLang, $content )
	{
		global $multilinguallib;
		if ( !$multilinguallib )
			die('Internal error: Multilingual library not imported.');

		if ( empty( $content ) )
			return;
		if ( !$this->is_valid_language( $srcLang )
			|| !$this->is_valid_language( $dstLang ) )
			return;

		$tagId = $this->find_or_create_tag( $content, $dstLang, false );

		$multilinguallib->insertTranslation( 'freetag', $srcTagId, $srcLang, $tagId, $dstLang );
		$this->query( 'UPDATE tiki_freetagged_objects'
									. ' SET tagId = ?'
									. ' WHERE tagId = ?'
									. ' AND objectId IN ('
										. ' SELECT objectId'
										. ' FROM tiki_objects'
										. ' INNER JOIN tiki_pages ON tiki_pages.pageName = tiki_objects.itemId'
										. ' WHERE'
										. ' tiki_objects.type = \'wiki page\''
										. ' AND tiki_pages.lang = ?'
										. ')'
										, array( $tagId, $srcTagId, $dstLang ) 
										);
	}

	/**
	 * clear_tag_language_from_id 
	 * 
	 * @param mixed $tagId 
	 * @access public
	 * @return void
	 */
	function clear_tag_language_from_id( $tagId )
	{
		$this->query( 'UPDATE tiki_freetags SET lang = NULL WHERE tagId = ?', array($tagId) );
		$this->query( 'DELETE FROM tiki_translated_objects WHERE type = \'freetag\' AND objId = ?'
								, array( $tagId ) 
								);
		$this->cleanup_tags();
	}

	/**
	 * get_tags_containing 
	 * 
	 * @param mixed $tag 
	 * @access public
	 * @return
	 */
	function get_tags_containing( $tag )
	{
		$tag = $this->normalize_tag($tag);

		if ( empty( $tag ) ) {
			return array();
		}

		$result = $this->fetchAll( 'SELECT `tag` FROM `tiki_freetags` WHERE `tag` LIKE ?'
														, array( $tag . '%' ), 10 
														);

		$tags = array();
		foreach( $result as $row ) {
			$tags[] = $row['tag'];
		}

		return $tags;
	}

	/**
	 * Used to parse the tag string when previewing an object. Simulates
	 * the final result without saving anything in the database.
	 *
	 * @param string $tagString
	 * @access public
	 * @return array tags
	 */
	function dumb_parse_tags($tagString) {
		if (!is_string($tagString) || empty($tagString)) {
			return array();
		}

		$tagArray = $this->_parse_tag($tagString);

		$tags = array();

		foreach ($tagArray as $tag) {
			$tags['data'][]['tag'] = $this->normalize_tag($tag);
		}

		$tags['cant'] = count($tags['data']);

		return $tags;
	}

	function get_cloud() {
		$query = "SELECT tag title, COUNT(*) weight, f.tagId FROM tiki_freetags f INNER JOIN tiki_freetagged_objects fo ON f.tagId = fo.tagId GROUP BY f.tagId";
		$result = $this->fetchAll($query);

		foreach ($result as &$row) {
			$row['params'] = array('url' => $row['tagId']);
		}

		return new Zend_Tag_Cloud(array(
			'tags' => $result,
		));
	}
}

$freetaglib = new FreetagLib;
