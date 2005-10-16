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

class FreetagLib extends TikiLib {

    // The fields below should be tiki preferences
    
    /* @access private
     * @param string The regex-style set of characters that are valid for normalized tags.
     */
    var $_normalized_valid_chars = 'a-zA-Z0-9';
    /**
     * @access private
     * @param string Whether to normalize tags at all.
     */
    var $_normalize_tags = 1;
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
     * @access private
     * @param string The file path to the installation of ADOdb used.
     */ 
    var $_ADODB_DIR = 'adodb/';
    
    /**
     * FreetagLib
     *
     * Constructor for the freetag class. 
     *
     */ 
    function FreetagLib($db) {
	if (!$db) {
	    die ("Invalid db object passed to FreetagLib constructor");
	}
	
	$this->db = $db;
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
    function get_objects_with_tag($tag, $user='', $offset = 0, $maxRecords = -1) {
	if(!isset($tag)) {
	    return false;
	}		
	
	$bindvals = array($tag);
	
	if(isset($user) && (!empty($user))) {
	    $mid = "AND `user` = ?";
	    $bindvals[] = $user;
	} else {
	    $mid = '';
	}
	
	$query = "SELECT DISTINCT `type`,`objId`,`user`,`tagged_on` ";
	$query_cant = "SELECT COUNT(*) ";
	
	$query_end = "FROM `tiki_freetagged_objects` o, `tiki_freetags` t WHERE o.`tagId`=t.`tagId` 
			      WHERE `tag` = ?
                              $mid
			      ";
	
	$query      .= $query_end;
	$query_cant .= $query_end;
	
	$result = $this->query($sql, $bindvals, $maxRecords, $offset);
	
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
     */
    
    function get_objects_with_tag_combo($tagArray, $user = '', $offset = 0, $maxRecords = -1) {
	if (!isset($tagArray) || !is_array($tagArray)) {
	    return false;
	}
	
	if (count($tagArray) == 0) {
	    return array('data' => array(),
			 'cant' => 0);
	}
	
	$bindvals = $tagArray;
	
	if (isset($user) && !empty($user)) {
	    $mid = "AND `user` = ?";
	    $bindvals[] = $user;
	} else {
	    $mid = '';
	}
	
	$tag_sql = "?";
	$numTags = count($tagArray);
	for ($i=1; $i<$numTags; $i++) { $tag_sql .= ",?"; }
	
	$bindvals[] = $numTags;
	
	// We must adjust for duplicate normalized tags appearing multiple times in the join by 
	// counting only the distinct tags. It should also work for an individual user.
	
	$query = "SELECT o.`objId`, o.`type`, t.`tag`, COUNT(DISTINCT `tag`) AS uniques ";
	$query_cant = "SELECT COUNT(*) ";
	
	$query_end = "
			FROM `tiki_freetagged_objects` o,
			     `tiki_freetags` t
			WHERE t.`tag` IN ($tag_sql)
                              o.`tagId` = t.`tagId`
                        $mid
			GROUP BY o.`objId`,o.`type`
			HAVING uniques = ?
			";
	
	$query      .= $query_end;
	$query_cant .= $query_end;
	
	$result = $this->query($sql, $bindvals, $maxRecords, $offset);
	
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
	
	$query = "SELECT DISTINCT `objId`, `type` ";
	$query_cant = "SELECT COUNT(*) ";
	
	$query_end = "  
			FROM `tiki_freetagged_objects` o, `tiki_freetags` t
			WHERE t.`tagId` = ?
                        $mid
			";

	$query      .= $query_end;
	$query_cant .= $query_end;

	$result = $this->query($sql, $bindvals, $maxRecords, $offset);

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
    function get_tags_on_object($objId, $type, $offset = 0, $maxRecords = -1, $user = NULL) {
	if (!isset($objId) || !isset($type) || empty($objId) || empty($type)) {
	    return false;
	}		

	$bindvals = array($objId, $type);

	if (isset($user) && (!empty($user))) {
	    $mid = "AND `user` = ?"; 
	    $bindvals[] = $user;
	} else {
	    $mid = "";
	}
	    
	$query = "SELECT DISTINCT `tag`, `raw_tag`, `user` ";
	$query_cant = "SELECT COUNT(*) ";

	$query_end = "
			FROM `tiki_freetagged_objects` o, 
                             `tiki_freetags` t
			WHERE t.`tagId` = o.`tagId`
                              o.`objId` = ? AND
                              o.`type` = ?
 			      $mid
			";

	$query      .= $query_end;
	$query_cant .= $query_end;
	    
	$result = $this->query($sql, $bindvals, $maxRecords, $offset);
	    
	$ret = array();
	while ($row = $result->fetchRow()) {
	    $ret[] = $row;
	}
	    
	$cant = $this->getOne($query_cant, $bindvals);
	    
	return array('data' => $ret,
		     'cant' => $cant);
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
     *
     * @return Returns true if successful, false otherwise. Does not operate as a transaction.
     */ 

    function safe_tag($user, $objId, $type, $tag) {
	if (!isset($user) || !isset($objId) || !isset($type) || !isset($tag) ||
	    empty($user) || empty($objId) || empty($type) || empty($tag)) {
	    die("safe_tag argument missing");
	    return false;
	}
	    
	    
	$normalized_tag = $this->normalize_tag($tag);
	$bindvals[] = array($objId, $type, $normalized_tag);
	    
	// First, check for duplicate of the normalized form of the tag on this object.
	// Dynamically switch between allowing duplication between users on the
	// constructor param 'block_multiuser_tag_on_object'.
	if ($this->_block_multiuser_tag_on_object) {
	    $mid = " AND user = ?";
	    $bindvals[] = $user;
	}
	    
	$query = "SELECT COUNT(*)
			FROM `tiki_freetagged_objects` o,
                              `tiki_freetags` t 
			WHERE o.`tagId` = t.`tagId`
			AND `objId` = ?
                        AND `type` = ?
			AND `tag` = ?
			$mid
			";
	    
	if($this->getOne($query, $bindvals) > 0) {
	    return true;
	}
	    
	// Then see if a raw tag in this form exists.
	$query = "SELECT `tagId` 
			FROM `tiki_freetags` 
			WHERE `raw_tag` = ?
			";
	    
	$result = $this->execute($query, array($tag));
	    
	if ($row = $result->fetchRow()) {
	    $tagId = $row['tagId'];
	} else {
	    // Add new tag! 
	    $query = "INSERT INTO `tiki_freetags` (`tag`, `raw_tag`) VALUES (?,?)";
	    $bindvals = array($normalized_tag, $tag);
	    $this->query($query, $bindvals);
		
	    $query = "SELECT MAX(`tagId`) FROM `tiki_freetags` WHERE `tag`=? AND `raw_tag`=?";
	    $tagId = $this->getOne($query, $bindvals);
	}
	    
	if(!($tagId > 0)) {
	    return false;
	}
	    
	$query = "INSERT INTO `tiki_freetagged_objects`
			(`tagId`, `user`, `objId`, `type`, `tagged_on`)
			VALUES (?,?,?,?,?)
			";
	$bindvals = array($tagId, $user, $objId, $type, time());
	    
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
	if ($this->_normalize_tags) {
	    $normalized_valid_chars = $this->_normalized_valid_chars;
	    $normalized_tag = preg_replace("/[^$normalized_valid_chars]/", "", $tag);
	    return strtolower($normalized_tag);
	} else {
	    return $tag;
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
     * @param string The raw string form of the tag to delete. See above for notes.
     *
     * @return string Returns the tag in normalized form.
     */ 
    function delete_object_tag($user, $objId, $type, $tag) {
	if (!isset($user) || !isset($objId) || !isset($type) || !isset($tag) ||
	    empty($user) || empty($objId) || empty($type) || empty($tag)) {
	    die("delete_object_tag argument missing");
	    return false;
	}
	    

	$tagId = $this->get_raw_tag_id($tag);

	if ( !($tagId > 0)) {
	    return false;
	} else {
		
	    $query = "DELETE FROM `tiki_freetagged_objects`
			WHERE `user` = ?
			AND `objId` = ?
                        AND `type` = ?
			AND `tagId` = ?
			LIMIT 1
			";	
	    $bindvals = array($user, $objId, $type, $tagId);

	    $this->query($query, $bindvals);
		
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
    function delete_all_object_tags_for_user($user, $objId, $type) {
	if(!isset($user) || !isset($objId) || !isset($type)
	   || empty($user) || empty($objId) || empty($type)) {
	    die("delete_all_object_tags_for_user argument missing");
	    return false;
	}
	    
	    
	if ( !($objId > 0)) {
	    return false;
	} else {
		
	    $query = "DELETE FROM `tiki_freetagged_objects`
				WHERE `user` = ?
				AND `objId` = ?
                                AND `type` = ?
				";
	    $bindvals = array($$user, $objId, $type);
		
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
			tag = ?
			LIMIT 1
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
			LIMIT 1
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
    function tag_object($user, $objId, $type, $tag_string) {
	if($tag_string == '') {
	    die("No tags found in tag_object");
	    return true;
	}
		
	// Perform tag parsing
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
	$tagArray = $newwords;
	    
	foreach($tagArray as $tag) {
	    $tag = trim($tag);
	    if(($tag != '') && (strlen($tag) <= $this->_MAX_TAG_LENGTH)) {
		if(get_magic_quotes_gpc()) {
		    $tag = addslashes($tag);
		}
		$this->safe_tag($user, $objId, $type, $tag);
	    }
	}
	    
	return true;
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

	$bindvals = array();

	if (isset($user) && (!empty($user))) {
	    $mid = "AND `user` = ?"; 
	    $bindvals[] = $user;
	} else {
	    $mid = "";
	}

	$query = "SELECT `tag`, COUNT(*) as count
			FROM `tiki_freetags` t,
                             `tiki_freetagged_objects` o
			WHERE t.`tagId` = o.`tagId`
			$mid
			GROUP BY tag
			ORDER BY count DESC, tag ASC
			";

	$result = $this->query($sql, $bindvals, $maxRecords, $offset);
	    
	$ret = array();
	while ($row = $result->fetchRow()) {
	    $ret[] = $row;
	}
	    
	return $ret;
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
		
	$query = "SELECT `tag`, COUNT(`objId`) AS quantity
			FROM `tiki_freetags` t, 
                             `tiki_freetagged_objects` o,
			WHERE t.`tagId` = o.`tagId`
			GROUP BY `tag`
			ORDER BY quantity DESC
			";

	$result = $this->query($sql, array(), $max, 0);
	    
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
	    
	$query = "SELECT t1.`tag`, COUNT( o1.`objId` ) AS quantity
			FROM `tiki_freetagged_objects` o1
			INNER JOIN `tiki_freetags` t1 ON ( t1.`tagId` = o1.`tagId` )
			INNER JOIN `tiki_freetagged_objects` o2 ON ( o1.`objId` = o2.`objId` )
			INNER JOIN `tiki_freetags` t2 ON ( t2.`tagId` = o2.`tagId` )
			WHERE t2.`tag` = ? AND t1.`tag` != ?
			GROUP BY o1.`tagId`
			ORDER BY quantity DESC
			";
	    
	$bindvals = array($tag, $tag);
	    
	$result = $this->query($sql, array(), $max, 0);
	    
	$ret = array();
	while ($row = $result->fetchRow()) {
	    $ret[] = $row;
	}
	    
	return $ret;
    }

}

global $dbTiki;
$freetaglib = new FreetagLib($dbTiki);
