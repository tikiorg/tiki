<?php

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
 */
/**
 *  Gordon Luk's Freetag - Generalized Open Source Tagging and Folksonomy.
 *  Copyright (C) 2004-2005 Gordon D. Luk <gluk AT getluky DOT net>
 *
 *  Released under both BSD license and Lesser GPL library license.  Whenever
 *  there is any discrepancy between the two licenses, the BSD license will
 *  take precedence. See License.txt.  
 *
 */
/**
 *  Freetag API Implementation
 *
 *  Freetag is a generic PHP class that can hook-in to existing database
 *  schemas and allows tagging of content within a social website. It's fun,
 *  fast, and easy!  Try it today and see what all the folksonomy fuss is
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

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

require_once("lib/objectlib.php");

class FreetagLib extends ObjectLib {

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
    var $_MAX_TAG_LENGTH = 30;
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
	function FreetagLib($db) {
		$this->ObjectLib($db);

		// update private vars with tiki preferences
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
    function get_objects_with_tag($tag, $type='', $user='', $offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '') {
	if(!isset($tag)) {
	    return false;
	}
	
	$bindvals = array($tag);
	
	$mid = '';

	if(isset($user) && (!empty($user))) {
	    $mid .= " AND `user` = ?";
	    $bindvals[] = $user;
	}

	if (isset($type) && !empty($type)) {
	    $mid .= " AND `type` = ?";
	    $bindvals[] = $type;
	}

	if (isset($find) && !empty($find)) {
		$findesc = '%' . $find . '%';
		$mid .= " AND (o.`name` like ? OR o.`description` like ?)";
		$bindvals = array_merge($bindvals, array($findesc, $findesc));
	}
	
	$query = "SELECT DISTINCT o.* ";
	$query_cant = "SELECT COUNT(*) ";
	
	$query_end = "FROM `tiki_objects` o, `tiki_freetagged_objects` fto, `tiki_freetags` t WHERE fto.`tagId`=t.`tagId` AND o.`objectId` = fto.`objectId` AND `tag` = ? $mid ORDER BY o.". $this->convert_sortmode($sort_mode);

	
	
	$query      .= $query_end;
	$query_cant .= $query_end;
	
	$result = $this->query($query, $bindvals, $maxRecords, $offset);
	
	$ret = array();
	while ($row = $result->fetchRow()) {
	    $ret[] = $row;
	}
	
	$cant = $this->getOne($query_cant, $bindvals);
	
	return array('data' => $ret,
		     'cant' => $cant);
    }
    
    /**
     * get_objects_with_tag_combo
     *
     * Returns an array of object ID's that have all the tags passed in the
     * tagArray parameter. Use this to provide tag combo services to your users.
     *
     * @param string - Pass an array of normalized form tags along to the function.
     * @param int (Optional) - The numerical offset to begin display at. Defaults to 0.
     * @param int (Optional) - The number of results per page to show. Defaults to 100.
     * @param int (Optional) - Restrict the result to objects tagged by a particular user.
     *
     * @return An array of Object ID numbers that reference your original objects.
     *      
     * 
     * Notes by nkoth:
     * 1. Updated because original version simply does not work right.
     * 2. The reason why I am using two queries here is because I can't get one query to work
     * properly to return the right count of number of objects returned with duplicated objects
     * 3. If you can fix this with subquery that works as far back as MSSQL 4.1, may be worth
     * doing. But my experience with subquery is that it may be slower anyway.     
     * 
     */
    
function get_objects_with_tag_combo($tagArray, $type='', $user = '', $offset = 0, $maxRecords = -1, $sort_mode = 'name_asc', $find = '', $broaden = 'n') {
	if (!isset($tagArray) || !is_array($tagArray)) {
	    return false;
	}
	
	if (count($tagArray) == 0) {
	    return array('data' => array(),
			 'cant' => 0);
	}
	
	$bindvals = $tagArray;
	
	$numTags = count($tagArray);
		
	if (isset($user) && !empty($user)) {
	    $mid = "AND `user` = ?";
	    $bindvals[] = $user;
	} else {
	    $mid = '';
	}
	
	$tag_sql = "t.`tag` IN (?";
	for ($i=1; $i<$numTags; $i++) {
		$tag_sql .= ",?";			 
	}
	$tag_sql .= ")";

    if ($broaden == 'n') {
		$bindvals_t = $bindvals;		
		$mid_t = '';

		if (isset($user) && !empty($user)) {
	    	$mid_t = "AND `user` = ?";
	    	$bindvals_t[] = $user;	
		}	
	
		if (isset($type) && !empty($type)) {
	    	$mid_t .= " AND `type` = ?";
	    	$bindvals_t[] = $type;
		}

		if (isset($find) && !empty($find)) {
			$findesc = '%' . $find . '%';
			$mid_t .= " AND (o.`name` like ? OR o.`description` like ?)";
			$bindvals_t = array_merge($bindvals_t, array($findesc, $findesc));
		}
    	
		$bindvals_t[] = $numTags;		
		
		$query_t = "SELECT o.`objectId`, COUNT(DISTINCT t.`tag`) AS uniques ";			
		$query_end_t = "
			FROM `tiki_objects` o,
                `tiki_freetagged_objects` fto, `tiki_freetags` t
                WHERE $tag_sql
				AND fto.`tagId`=t.`tagId` AND o.`objectId` = fto.`objectId`				
				 $mid_t
			GROUP BY o.`objectId`
			HAVING uniques = ?
			";
		$query_t .= $query_end_t;
		$result = $this->query($query_t, $bindvals_t, -1, 0);		
		$ret = array();
		while ($row = $result->fetchRow()) {
	    	$ret[] = $row;
		}
		if ($numCats = count($ret)) {
			$tag_sql .= " AND o.`objectId` IN (?";
			$bindvals[] = $ret[0]["objectId"];			
			for ($i=1; $i<$numCats; $i++) {
				$tag_sql .= ",?";
				$bindvals[] = $ret[$i]["objectId"];				
			}			
			$tag_sql .= ")";
		} else {
			return array('data' => array(),
			 'cant' => 0);
		}
	} 
	
	$mid = '';

	if (isset($user) && !empty($user)) {
	    $mid = "AND `user` = ?";
	    $bindvals[] = $user;	
	}	
	
	if (isset($type) && !empty($type)) {
	    $mid .= " AND `type` = ?";
	    $bindvals[] = $type;
	}

	if (isset($find) && !empty($find)) {
		$findesc = '%' . $find . '%';
		$mid .= " AND (o.`name` like ? OR o.`description` like ?)";
		$bindvals = array_merge($bindvals, array($findesc, $findesc));
	}
	
	global $prefs;
	if ($prefs['feature_wikiapproval'] == 'y') {
		$mid .= " AND o.`itemId` not like ?";
		$bindvals[] = $prefs['wikiapproval_prefix'] . '%';
	}
	// We must adjust for duplicate normalized tags appearing multiple times in the join by 
	// counting only the distinct tags. It should also work for an individual user.
	
	$query = "SELECT DISTINCT o.* ";
	$query_cant = "SELECT COUNT(DISTINCT o.`objectId`) ";
	
	$query_end = "FROM `tiki_objects` o, `tiki_freetagged_objects` fto, `tiki_freetags` t 
	WHERE fto.`tagId`=t.`tagId` AND o.`objectId` = fto.`objectId` AND $tag_sql $mid ORDER BY ". $this->convert_sortmode($sort_mode);
	// note the original line was originally here to fix ambiguous 'created' column for default sort. Not a neat fix the o. prefix is ugly.	So changed default order instead.
	
	$query      .= $query_end;
	$query_cant .= $query_end;	
	
	$result = $this->query($query, $bindvals, $maxRecords, $offset);
	
	$ret = array();
	while ($row = $result->fetchRow()) {
	    $ret[] = $row;
	}
	
	$cant = $this->getOne($query_cant, $bindvals);
	
	return array('data' => $ret,
		     'cant' => $cant);
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
    function get_objects_with_tag_id($tagId, $user = '', $offset = 0, $maxRecords = -1) {
	if(!isset($tagId)) {
	    return false;
	}		
	
	$bindvals = array($tagId);
	
	if(isset($user) && empty($user)) {
	    $mid = "AND `user` = ?";
	    $bindvals[] = $user;
	} else {
	    $mid = "";
	}
	
	$query = "SELECT DISTINCT o.* ";
	$query_cant = "SELECT COUNT(*) ";
	
	$query_end = "  
			FROM `tiki_freetagged_objects` fto, `tiki_freetags` t, `tiki_objects` o
			WHERE t.`tagId` = ? AND fto.`tagId`=t.`tagId` AND o.`objectId`=fto.`objectId`
                        $mid
			";

	$query      .= $query_end;
	$query_cant .= $query_end;

	$result = $this->query($query, $bindvals, $maxRecords, $offset);

	$ret = array();
	while ($row = $result->fetchRow()) {
	    $ret[] = $row;
	}

	$cant = $this->getOne($query_cant, $bindvals);

	return array('data' => $ret,
		     'cant' => $cant);
    }


    /**
     * get_tags_on_object
     *
     * You can use this function to show the tags on an object. Since it supports both user-specific
     * and general modes with the $user parameter, you can use it twice on a page to make it work
     * similar to upcoming.org and flickr, where the page displays your own tags differently than
     * other users' tags.
     *
     * @param int The unique ID of the object in question.
     * @param int The offset of tags to return
     * @param int The size of the tagset to return
     * @param int The unique ID of the person who tagged the object, if user-level tags only are preferred.
     *
     * @return array Returns a PHP array with object elements ordered by object ID. Each element is an associative
     * array with the following elements:
     *   - 'tag' => Normalized-form tag
     *	 - 'raw_tag' => The raw-form tag
     *	 - 'user' => The unique ID of the person who tagged the object with this tag.
     */ 
    function get_tags_on_object($itemId, $type, $offset = 0, $maxRecords = -1, $user = NULL) {
	if (!isset($itemId) || !isset($type) || empty($itemId) || empty($type)) {
	    return false;
	}

	$bindvals = array($itemId, $type);

	if (isset($user) && (!empty($user))) {
	    $mid = "AND `user` = ?"; 
	    $bindvals[] = $user;
	} else {
	    $mid = "";
	}

	$query = "SELECT DISTINCT t.`tagId`, `tag`, `raw_tag`, `user`, `lang`
			FROM `tiki_objects` o,
                             `tiki_freetagged_objects` fto, 
                             `tiki_freetags` t
			WHERE t.`tagId` = fto.`tagId` AND
                              fto.`objectId` = o.`objectId` AND 
                              o.`itemId` = ? AND
                              o.`type` = ?
 			      $mid
			";
	    
	$result = $this->query($query, $bindvals, $maxRecords, $offset);
	    
	$ret = array();
	$cant = 0;
	while ($row = $result->fetchRow()) {
		$ret[] = $row;
		$cant++;
	}
	    
	return array('data' => $ret,
		     'cant' => $cant);
    }

	function find_or_create_tag( $tag, $lang = null )
	{
		$normalized_tag = $this->normalize_tag($tag);

		// Then see if a raw tag in this form exists.
		$query = "SELECT `tagId` 
				FROM `tiki_freetags` 
				WHERE `raw_tag` = ? OR `tag` = ?
				";
			
		$result = $this->query($query, array($tag, $normalized_tag));
			
		if ($row = $result->fetchRow()) {
			$tagId = $row['tagId'];
		} else {
			// Add new tag! 
			if ($this->multilingual && $lang ) {
				$query = "INSERT INTO `tiki_freetags` (`tag`, `raw_tag`, `lang`) VALUES (?,?,?)";
				$bindvals = array($normalized_tag, $tag, $lang);
				$this->query($query, $bindvals);
			} else {
				$query = "INSERT INTO `tiki_freetags` (`tag`, `raw_tag`) VALUES (?,?)";
				$bindvals = array($normalized_tag, $tag);
				$this->query($query, $bindvals);
			}
			
			$query = "SELECT MAX(`tagId`) FROM `tiki_freetags` WHERE `tag`=? AND `raw_tag`=?";
			$tagId = $this->getOne($query, array_slice( $bindvals, 0, 2 ) );
		}
			
		if(!($tagId > 0)) {
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

    function safe_tag($user, $itemId, $type, $tag, $lang = null) {
	if (!isset($user) || !isset($itemId) || !isset($type) || !isset($tag) ||
	    empty($user) || empty($itemId) || empty($type) || empty($tag)) {
	    die("safe_tag argument missing");
	    return false;
	}
	    
	$normalized_tag = $this->normalize_tag($tag);
	$bindvals = array($itemId, $type, $normalized_tag);
	 
	$mid = '';

	// First, check for duplicate of the normalized form of the tag on this object.
	// Dynamically switch between allowing duplication between users on the
	// constructor param 'block_multiuser_tag_on_object'.
	if (!$this->_block_multiuser_tag_on_object) {
	    $mid .= " AND user = ?";
	    $bindvals[] = $user;
	}

	$query = "SELECT COUNT(*)
			FROM `tiki_objects` o,
                             `tiki_freetagged_objects` fto,
                             `tiki_freetags` t 
			WHERE fto.`tagId` = t.`tagId`
                        AND fto.`objectId` = o.`objectId`
			AND o.`itemId` = ?
                        AND o.`type` = ?
			AND t.`tag` = ?
			$mid
			";
	    
	if($this->getOne($query, $bindvals) > 0) {
	    return true;
	}
	    
	$tagId = $this->find_or_create_tag( $tag, $lang );

	$objectId = $this->add_object($type, $itemId);

	$query = "INSERT INTO `tiki_freetagged_objects`
			(`tagId`, `objectId`, `user`, `created`)
			VALUES (?,?,?,?)
			";
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
     * @param string An individual tag in raw form that should be normalized.
     *
     * @return string Returns the tag in normalized form.
     */ 
    function normalize_tag($tag) {
	if (!empty($this->_normalized_valid_chars) && $this->_normalized_valid_chars != '*') {
	    $normalized_valid_chars = $this->_normalized_valid_chars;
	    $tag = preg_replace("/[^$normalized_valid_chars]/", "", $tag);
	}
	if (function_exists('mb_strtolower')) {
		return $this->_normalize_in_lowercase ? mb_strtolower($tag,'UTF-8') : $tag;
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
	 * @param bool The tag is the raw or the normalized form
     *
     * @return string Returns the tag in normalized form.
     */ 
    function delete_object_tag($itemId, $type, $tag, $user=false, $raw=false) {
	if (!isset($itemId) || !isset($type) || !isset($tag) ||
	    empty($itemId) || empty($type) || empty($tag)) {
	    die("delete_object_tag argument missing");
	    return false;
	}

	$tagId = $raw? $this->get_raw_tag_id($tag): $this->get_tag_id($tag);

	if ( !($tagId > 0)) {
	    return false;
	} else {

	    $objectId = $this->get_object_id($type, $itemId);
	    $query = "delete from `tiki_freetagged_objects` where `objectId`=? and `tagId`=?";
			$bindvars = array($objectId, $tagId);
			if ($user) {
				$query.= " and `user`=?";
				$bindvars[] = $user;
			}
	    $this->query($query, $bindvars);
		
			$this->cleanup_tags();
	    return true;

	} 
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
    function delete_all_object_tags_for_user($user, $itemId, $type) {
	if(!isset($user) || !isset($itemId) || !isset($type)
	   || empty($user) || empty($itemId) || empty($type)) {
	    die("delete_all_object_tags_for_user argument missing");
	    return false;
	}
	    
	    
	if ( !($itemId > 0)) {
	    return false;
	} else {

	    $objectId = $this->get_object_id($type, $itemId);

	    $query = "DELETE FROM `tiki_freetagged_objects`
				WHERE `user` = ?
                                  AND `objectId` = ?
				";

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
    function get_tag_id($tag) {
	if(!isset($tag) || empty($tag)) {
	    die("get_tag_id argument missing");
	    return false;
	}
		
	$query = "SELECT `tagId` FROM `tiki_freetags`
			WHERE 
			`tag` = ?
			";

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

    function get_raw_tag_id($tag) {
	if(!isset($tag) || empty($tag)) {
	    die("get_tag_id argument missing");
	    return false;
	}
		
	$query = "SELECT `tagId` FROM `tiki_freetags`
			WHERE 
			`raw_tag` = ?
			";

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
    function tag_object($user, $itemId, $type, $tag_string, $lang = null) {
    if($tag_string == '') {
	    return true;
	}
	
	// Perform tag parsing
	$tagArray = $this->_parse_tag($tag_string);
	
	$this->_tag_object_array($user, $itemId, $type, $tagArray, $lang);
	    
	return true;
    }

    function update_tags($user, $itemId, $type, $tag_string, $old_user = false, $lang = null) {

	// Perform tag parsing
	$tagArray = $this->_parse_tag($tag_string);
 	
	$oldTags = $this->get_tags_on_object($itemId, $type, 0, -1, $old_user);

	foreach ($oldTags['data'] as $tag) {
	    if (!in_array($tag['raw_tag'], $tagArray)) {
		$this->delete_object_tag($itemId, $type, $tag['raw_tag'],$old_user);
	    }
	}

	$this->_tag_object_array($user, $itemId, $type, $tagArray, $lang);
	    
	return true;
    }

    function _parse_tag($tag_string) {
	if(get_magic_quotes_gpc()) {
	    $query = stripslashes(trim($tag_string));
	} else {
	    $query = trim($tag_string);
	}
	$words = preg_split('/(")/', $query,-1,PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
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
    
    function _tag_object_array($user, $itemId, $type, $tagArray, $lang = null) {

	foreach($tagArray as $tag) {
	    $tag = trim($tag);
	    if(($tag != '') && (strlen($tag) <= $this->_MAX_TAG_LENGTH)) {
		if(get_magic_quotes_gpc()) {
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
     *   - 'tag' => Normalized-form tag
     *	 - 'count' => The number of objects tagged with this tag.
     */

    function get_most_popular_tags($user = '', $offset = 0, $maxRecords = 25) {

	// get top tag popularity
	$query = "SELECT COUNT(*) as count
			FROM `tiki_freetagged_objects` o
			GROUP BY `tagId`
			ORDER BY count DESC
			";

	$top = $this->getOne($query);

	$bindvals = array();

	if (isset($user) && (!empty($user))) {
	    $mid = "AND `user` = ?"; 
	    $bindvals[] = $user;
	} else {
	    $mid = "";
	}

	$query = "SELECT `tag`, COUNT(*) as count
			FROM `tiki_freetags` tf,  `tiki_freetagged_objects` tfo 
			WHERE tf.`tagId`= tfo.`tagId`
			$mid
			GROUP BY `tag`
			ORDER BY count DESC, tag ASC
			";

	$result = $this->query($query, $bindvals, $maxRecords, $offset);

	$ret = array();
	$tag = array();
	$count = array();

	while ($row = $result->fetchRow()) {
	    $size[] = $row['size'] = ceil($this->max_cloud_text_size * $row['count'] / $top);
	    $tag[] = $row['tag'];
	    $count[] = $row['count'];

	    $ret[] = $row;
	}
	
	// this should get out of here, function should return in order of
	// popularity
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

    function get_tag_suggestion($exclude = '', $max = 10, $lang = null) {
	$query = "select t.* from `tiki_freetags` t, `tiki_freetagged_objects` o where t.`tagId`=o.`tagId` and (`lang` = ? or `lang` is null) order by " . $this->convert_sortmode('random');
	$result = $this->query($query, array( $lang ));

	$tags = array();
	$index = array();
	while (sizeof($tags) < $max && $row = $result->fetchRow()) {
	    $tag = $row['tag'];
	    if (!isset($index[$tag]) && !preg_match("/$tag/",$exclude)) {
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
    function count_tags($user = '') {
	    
	$bindvals = array();

	if (isset($user) && (!empty($user))) {
	    $mid = "AND `user` = ?"; 
	    $bindvals[] = $user;
	} else {
	    $mid = "";
	}
	    
	$query = "SELECT COUNT(*)
			FROM `tiki_freetags` t, 
                             `tiki_freetagged_objects` o
			WHERE o.`tagId` = t.`tagId`
			$mid
			";

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

    function silly_list($max = 100) {
		
	$query = "SELECT `tag`, COUNT(`objectId`) AS quantity
			FROM `tiki_freetags` t, 
                             `tiki_freetagged_objects` o,
			WHERE t.`tagId` = o.`tagId`
			GROUP BY `tag`
			ORDER BY quantity DESC
			";

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

    function similar_tags($tag, $max = 100) {

	if(!isset($tag) || empty($tag)) {
	    return array();
	}
	    
	// This query was written using a double join for PHP. If you're trying to eke
	// additional performance and are running MySQL 4.X, you might want to try a 
	// subselect and compare perf numbers.
	    
	$query = "SELECT t1.`tag`, COUNT( o1.`objectId` ) AS quantity
			FROM `tiki_freetagged_objects` o1
			INNER JOIN `tiki_freetags` t1 ON ( t1.`tagId` = o1.`tagId` )
			INNER JOIN `tiki_freetagged_objects` o2 ON ( o1.`objectId` = o2.`objectId` )
			INNER JOIN `tiki_freetags` t2 ON ( t2.`tagId` = o2.`tagId` )
			WHERE t2.`tag` = ? AND t1.`tag` <> ?
			GROUP BY o1.`tagId`
			ORDER BY quantity DESC
			";
	    
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
    
	function get_similar( $type, $objectId, $maxResults = 10 ) {

		$algorithm = $this->get_preference('morelikethis_algorithm', 'basic');

		$maxResults = (int) $maxResults;
		if( $maxResults <= 0 )
			$maxResults = 10;

		$mid = " oa.objectId <> ob.objectId	AND ob.type = ? AND oa.type = ? AND oa.itemId = ?";
		$bindvals = array($type, $type, $objectId);
			
		global $prefs;
		if ($prefs['feature_wikiapproval'] == 'y') {
			$mid .= " AND ob.`itemId` not like ?";
			$bindvals[] = $prefs['wikiapproval_prefix'] . '%';
		}
		
		switch( $algorithm )
		{
		case 'basic': // {{{
			$minCommon = (int) $this->get_preference( 'morelikethis_basic_mincommon', 2 );
				
			$query = "
				SELECT
					ob.itemId pageName, COUNT(DISTINCT fb.tagId) cnt
				FROM
					tiki_objects oa
					INNER JOIN tiki_freetagged_objects fa ON oa.objectId = fa.objectId
					INNER JOIN tiki_freetagged_objects fb USING(tagId)
					INNER JOIN tiki_objects ob ON ob.objectId = fb.objectId
				WHERE" . $mid .					
				"GROUP BY
					ob.itemId
				HAVING
					cnt >= ?
				ORDER BY
					cnt DESC, RAND()
				LIMIT ?
				";
			
		// }}}

		case 'weighted': // {{{
			$minCommon = (int) $this->get_preference( 'morelikethis_basic_mincommon', 2 );

			$query = "
				SELECT
					ob.itemId pageName, COUNT(DISTINCT fc.objectId) sort_cnt, COUNT(DISTINCT fb.tagId) having_cnt
				FROM
					tiki_objects oa
					INNER JOIN tiki_freetagged_objects fa ON oa.objectId = fa.objectId
					INNER JOIN tiki_freetagged_objects fb USING(tagId)
					INNER JOIN tiki_objects ob ON ob.objectId = fb.objectId
					INNER JOIN tiki_freetagged_objects fc ON fb.tagId = fc.tagId
				WHERE " . $mid . 
				" GROUP BY
					ob.itemId
				HAVING
					having_cnt >= ?
				ORDER BY					
					sort_cnt DESC, RAND()
				LIMIT ?
				";	
			// Sort based on the global popularity of all tags in common
		// }}}
		}
		
		$bindvals[] = $minCommon;
		$bindvals[] = $maxResults;
		
		$result = $this->query( $query, $bindvals );
		$tags = array();
		while( $row = $result->fetchRow() )
			$tags[] = $row;

		return $tags;
	}
	
	/**
     * cleanup_tags
     *
     * This function added by Nelson to remove all tags that are orphaned (i.e. not used)
  	 *
     */
    
	function cleanup_tags() {
		$query = "DELETE FROM `tiki_freetags` t USING `tiki_freetags` t LEFT JOIN `tiki_freetagged_objects` fto ON (t.`tagId` = fto.`tagId`) WHERE fto.`tagId` IS NULL";
		$this->query($query);
	    return true;
	 }

	function get_object_tags_multilingual( $type, $objectId, $accept_languages )
	{
		$query = "
			SELECT DISTINCT
				fo.tagId tagset,
				(SELECT lang FROM tiki_freetags WHERE tagId = tagset) rootlang,
				tag.tagId,
				tag.lang,
				tag.tag
			FROM
				tiki_objects o
				INNER JOIN tiki_freetagged_objects fo ON o.objectId = fo.objectId
				LEFT JOIN tiki_translated_objects to_a ON to_a.type = 'freetag' AND to_a.objId = fo.tagId
				LEFT JOIN tiki_translated_objects to_b ON to_b.type = 'freetag' AND to_a.traId = to_b.traId
				LEFT JOIN tiki_freetags tag ON to_b.objId = tag.tagId OR tag.tagId = fo.tagId
			WHERE
				o.type = ?
				AND o.itemId = ?
				AND (tag.lang IS NULL OR tag.lang IN(" . implode(',', array_fill(0,count($accept_languages),'?')) . ") )
				";

		$result = $this->query( $query, array_merge( array( $type, $objectId ), $accept_languages ) );

		$ret = array();
		while( $row = $result->fetchRow() ) {
			$group = $row['tagset'];
			$lang = $row['lang'];

			if( !array_key_exists( $group, $ret ) )
				$ret[$group] = array();

			$ret[$group][$lang] = $row;
		}

		return $ret;
	}

	function set_tag_language( $tagId, $lang )
	{
		if( ! $this->is_valid_language( $lang ) )
			return;

		$this->query( "UPDATE tiki_freetags SET lang = ? WHERE tagId = ?", array( $lang, $tagId ) );
	}

	function translate_tag( $srcLang, $srcTagId, $dstLang, $content )
	{
		global $multilinguallib;
		if( !$multilinguallib )
			die("Internal error: Multilingual library not imported.");

		if( empty( $content ) )
			return;
		if( !$this->is_valid_language( $srcLang )
			|| !$this->is_valid_language( $dstLang ) )
			return;

		$tagId = $this->find_or_create_tag( $content, $dstLang );

		$multilinguallib->insertTranslation( 'freetag', $srcTagId, $srcLang, $tagId, $dstLang );
		$this->query( "
			UPDATE tiki_freetagged_objects 
			SET tagId = ? 
			WHERE 
				tagId = ?
				AND objectId IN(
					SELECT objectId
					FROM
						tiki_objects
						INNER JOIN tiki_pages ON tiki_pages.pageName = tiki_objects.itemId
					WHERE
						tiki_objects.type = 'wiki page'
						AND tiki_pages.lang = ?
				)", array( $tagId, $srcTagId, $dstLang ) );
	}
}

global $dbTiki;
$freetaglib = new FreetagLib($dbTiki);
?>
