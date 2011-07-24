<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * Tracker Query Library
 *
 * \brief Functions to support reporting of the Trackers.
 *
 * @package		Tiki
 * @subpackage		Trackers
 * @author		Robert Plummer
 * @copyright		Copyright (c) 2002-2009, All Rights Reserved.
 * 			See copyright.txt for details and a complete list of authors.
 * @license		LGPL - See license.txt for details.
 * @version		SVN $Rev$
 * @filesource
 * @link		http://dev.tiki.org/Trackers
 * @since		TIki 8
 */
/**
 * This script may only be included, so it is better to die if called directly.
 */
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class TrackerQueryLib extends TikiLib
{
	
	/* In the construct we put the field options for "items list" (type 'l') into a table to be joined upon, 
	 * so instead of running a query for every row, we use simple joins to get the job done.  We use a temporary
	 * table so that it is removed once the connection is closed or after the page loads.
	 */
	function __construct() {
		global $tikilib, $tikilib;	
		$tikilib->query("
		 	DROP TABLE IF EXISTS temp_tracker_field_options;
			CREATE TEMPORARY TABLE temp_tracker_field_options (
				trackerIdHere INT,
				trackerIdThere INT,
				fieldIdThere INT,
				fieldIdHere INT,
				displayFieldIdThere INT,
				displayFieldIdHere INT,
				linkToItems INT,
				type VARCHAR(1),
				options VARCHAR(50)
			);
			
			INSERT INTO temp_tracker_field_options
			SELECT
				tiki_tracker_fields.trackerId,
				REPLACE(SUBSTRING(
					SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 1),
					LENGTH(SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 1 -1)) + 1
					),
				',', ''),
				REPLACE(SUBSTRING(
					SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 2),
					LENGTH(SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 2 -1)) + 1
					),
				',', ''),
				REPLACE(SUBSTRING(
					SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 3),
					LENGTH(SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 3 -1)) + 1
					),
				',', ''),
				REPLACE(SUBSTRING(
					SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 4),
					LENGTH(SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 4 -1)) + 1
					),
				',', ''),
				tiki_tracker_fields.fieldId,
				REPLACE(SUBSTRING(
					SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 5),
					LENGTH(SUBSTRING_INDEX(tiki_tracker_fields.options, ',', 5 -1)) + 1
					),
				',', ''),
				tiki_tracker_fields.type,
				tiki_tracker_fields.options
			FROM tiki_tracker_fields
			WHERE tiki_tracker_fields.type = 'l';
			
			SET group_concat_max_len = 4294967295;
		");
		
		/*For eany fields that have multi items, we use php to parse those out, there shouldn't be too many
		 */
		
		foreach($tikilib->fetchAll("SELECT * FROM temp_tracker_field_options WHERE options LIKE '%|%'") as $row) {
			$option = explode(",", $row["options"]);
			$displayFieldIdsThere = explode("|", $option["3"]);
			foreach($displayFieldIdsThere as $key => $displayFieldIdThere) {
				if ($key > 0) {
				$tikilib->query("
						INSERT INTO temp_tracker_field_options
						VALUES (?,?,?,?,?,?,?,?,?)
					", array(
						    $row["trackerIdHere"],
						    $row["trackerIdThere"],
						    $row["fieldIdThere"],
						    $row["fieldIdHere"],
						    $displayFieldIdThere,
						    $row["displayFieldIdHere"],
						    $row["linkToItems"],
						    $row["type"],
						    $row["options"]
					));
				}
			}
		}
	}
	
	/*Adds the field names to the beginning of the array of tracker items*/
	function prepend_field_header(&$trackerPrimary = array(), $nameOrder = array()) {
		global $tikilib;
		$result = $tikilib->fetchAll("
			SELECT fieldId, trackerId, name FROM tiki_tracker_fields
		");
		
		$header = array();
		
		foreach($result as $row) {
			$header[$row['fieldId']] = $row['name'];
		}
		
		$joinedTrackerHeader = array();
	
		foreach($trackerPrimary as $item) {
			foreach($item as $key => $field) {
				$joinedTrackerHeader[$key] = $header[$key];
			}
		}
		
		if (!empty($nameOrder)) {
			$sortedHeader = array();
			$unsortedHeader = array();
			foreach($nameOrder as $name) {
				foreach($joinedTrackerHeader as $key => $field) {
					if ($field == $name) {
						$sortedHeader[$key] = $field;
					} else {
						$unsortedHeader[$key] = $field;
					}
				}
			}
			$joinedTrackerHeader = $sortedHeader + $unsortedHeader;
		}
		
		$joinedTrackerHeader = array("HEADER" => $joinedTrackerHeader);
		
		return $joinedTrackerHeader + $trackerPrimary;
	}

	/*Simple direction parsing from string to type
	 */
	private function sort_direction($dir) {
		switch( $dir ) {
			case "asc":
				$dir = SORT_ASC;
				break;
			case "desc":
				$dir = SORT_DESC;
				break;
			case "regular":
				$dir = SORT_REGULAR;
				break;
			case "numeric":
				$dir = SORT_NUMERIC;
				break;
			case "string":
				$dir = SORT_STRING;
				break;
			default:
				$dir = SORT_ASC;
		}
		
		return $dir;
	}

	function arfsort( &$array, $fieldList ){
	    if (!is_array($fieldList)) {
	    	$fieldList = explode('|', $fieldList);
	        $fieldList = array(array($fieldList[0], $this->sort_direction($fieldList[1])));
	    } else {
	        for ($i = 0; $i < count($fieldList); ++$i) {
	            $fieldList[$i] = explode('|', $fieldList[$i]);
				$fieldList[$i] = array($fieldList[$i][0], $this->sort_direction($fieldList[$i][1]));
	        }
	    }
	    
	    $GLOBALS['__ARFSORT_LIST__'] = $fieldList;
	    usort( $array, 'arfsort_func' );
	}

	function arfsort_func( $a, $b ){
	    foreach( $GLOBALS['__ARFSORT_LIST__'] as $f ) {
	    	switch($f[1]) {
	    		case SORT_NUMERIC:
	    			$strc = ((float)$b[$f[0]] > (float)$a[$f[0]] ? -1 : 1);
	    			return $strc;
	    			break;
	    		default:
	    			$strc = strcasecmp($b[$f[0]], $a[$f[0]]);
			    	if ( $strc != 0 ){
			            return $strc * (!empty($f[1]) && $f[1] == SORT_DESC ? 1 : -1);
			        }
	    	}
	    }
	    return 0;
	}

	/*Queries & filters trackers from mysql, orders results in a way that is human understandable and can be manipulated easily
	 * The end result is a very simple array setup as follows:
	 * array( //tracker(s)
	 * 		array( //items
	 * 			[itemId] => array (
	 * 				[fieldId] => value,
	 * 				[fieldId] => array( //items list
	 * 					[0] => '',
	 * 					[1] => ''
	 * 				)
	 * 			)
	 * 		)
	 * )
	 */
	function tracker_query($tracker, $start, $end, $itemId, $equals, $search, $fields, $status = "opc", $sort, $limit, $offset, $byName, $includeTrackerDetails = true, $delimiter = "[{|!|}]") {
		global $tikilib;
		$debug = false;
		$params = array();
		$fields_safe = "";
		$status_safe = "";
		$isSearch = false;
		
		$params[] = $tracker;
		
		if (isset($start) && !empty($start) && !$search) $params[] = $start;
		if (isset($end) && !empty($end) && !$search) $params[] = $end;
		if (isset($itemId) && !empty($itemId) && !$search) $params[] = $itemId;
		
		if(isset($byName) && !empty($byName)) {
			$fieldIds = array();
			foreach($fields as $field) {
				$fieldIds[] = $tikilib->getOne("
					SELECT fieldId
					FROM tiki_tracker_fields
					LEFT JOIN tiki_trackers ON (
						tiki_trackers.trackerId = tiki_tracker_fields.trackerId
					)
					WHERE
						tiki_trackers.name = ? AND
						tiki_tracker_fields.name = ?
				", array($tracker, $field));
			}
			$fields = $fieldIds;
		}
		
		if (count($fields) > 0 && (count($equals) > 0 || count($search) > 0)) {
			for($i = 0; $i < count($fields); $i++) {
				if (strlen($fields[$i]) > 0) {
					$fields_safe .= " ( search_item_fields.fieldId = ? ";
					$params[] = $fields[$i];
					
					if (strlen($equals[$i]) > 0) {
						$fields_safe .= " AND search_item_fields.value = ? ";
						$params[] = $equals[$i];
					} elseif (strlen($search[$i]) > 0) {
						$fields_safe .= " AND search_item_fields.value LIKE ? ";
						$params[] = '%' . $search[$i] . '%';
					}
					
					$fields_safe .= " ) ";
					
					
					if ($i + 1 < count($fields) && count($fields) > 1) $fields_safe .= " OR ";
				}
			}
			
			if (strlen($fields_safe) > 0) {
				$fields_safe = " AND ( $fields_safe ) ";
				$isSearch = true; 
			}
		}
		
		if (strlen($status) > 0) {
			for($i=0; $i < strlen($status); $i++) {
				if (strlen($status[$i]) > 0) {
					$status_safe .= " tiki_tracker_items.status = ? ";
					if ($i + 1 < strlen($status) && strlen($status) > 1) $status_safe .= " OR ";
					$params[] = $status[$i];
				}
			}
			
			if (strlen($status_safe) > 0) {
				$status_safe = " AND ( $status_safe ) ";
			}
		}
		
		if ( isset($limit) && !empty($limit) && is_numeric($limit) == false) {
			unset($limit);
		}
		
		if ( isset($offset) && !empty($offset) && is_numeric($offset) == false) {
			unset($offset);
		}
		
		$query = "
			SELECT
				tiki_tracker_items.status,
				tiki_tracker_item_fields.itemId,
				tiki_tracker_fields.trackerId,
				GROUP_CONCAT(tiki_tracker_fields.name 			SEPARATOR '$delimiter') AS fieldNames,
				GROUP_CONCAT(tiki_tracker_item_fields.fieldId	SEPARATOR '$delimiter') AS fieldIds,
				GROUP_CONCAT(IFNULL(items_right.value, tiki_tracker_item_fields.value) 										SEPARATOR '$delimiter') AS item_values
						
			FROM tiki_tracker_item_fields ".($isSearch == true ? " AS search_item_fields " : "")."
			
			".($isSearch == true ? "
			LEFT JOIN tiki_tracker_item_fields ON 
				search_item_fields.itemId = tiki_tracker_item_fields.itemId
			" : "" )."
			LEFT JOIN tiki_tracker_fields ON 
				tiki_tracker_fields.fieldId = tiki_tracker_item_fields.fieldId
			LEFT JOIN tiki_trackers ON 
				tiki_trackers.trackerId = tiki_tracker_fields.trackerId
			LEFT JOIN tiki_tracker_items ON tiki_tracker_items.itemId = tiki_tracker_item_fields.itemId
			
			
			
			LEFT JOIN temp_tracker_field_options items_left_display ON
				items_left_display.displayFieldIdHere = tiki_tracker_item_fields.fieldId
			
			LEFT JOIN tiki_tracker_item_fields items_left ON (
				items_left.fieldId = items_left_display.fieldIdHere AND
				items_left.itemId = tiki_tracker_item_fields.itemId
			)
			
			LEFT JOIN tiki_tracker_item_fields items_middle ON (
				items_middle.value = items_left.value AND
				items_left_display.fieldIdThere = items_middle.fieldId
			)
			
			LEFT JOIN tiki_tracker_item_fields items_right ON (
				items_right.itemId = items_middle.itemId AND
				items_right.fieldId = items_left_display.displayFieldIdThere
			)
			 
			
			WHERE
			".(isset($byName) && !empty($byName) ? "tiki_trackers.name" : "tiki_trackers.trackerId")." = ?
			
			".(isset($start) && !empty($start) && !$search ? 								" AND tiki_tracker_items.lastModif > ? " : "")."
			".(isset($end) && !empty($end) && !$search ? 								" AND tiki_tracker_items.lastModif < ? " : "")."
			".(isset($itemId) && !empty($itemId) && !$search ? 							" AND tiki_tracker_item_fields.itemId = ? " : "")."
			".(isset($fields_safe) && !empty($fields_safe) ? $fields_safe : "")."
			".(isset($status_safe) && !empty($status_safe) ? $status_safe : "")."
			
			GROUP BY
				tiki_tracker_item_fields.itemId
			ORDER BY 
				tiki_tracker_items.lastModif
			".(isset($limit) && !empty($limit) ? 
				" LIMIT ".(isset($offset) && !empty($offset) ? "$offset, " : "")." $limit"
				: ""
			);
		
		if ($debug == true) {
			$result = array($query, $params);
			print_r( $result );
			die;
		} else {
			$result = $tikilib->fetchAll($query, $params);
		}
		
		$newResult = array();
		foreach($result as $key => $row) {
			$newRow = array();
			$fieldNames = explode($delimiter, $row['fieldNames']);
			$fieldIds = explode($delimiter, $row['fieldIds']); 
			$itemValues = explode($delimiter, $row['item_values']);
			
			foreach($fieldIds as $key => $fieldId) {
				$field = (isset($byName) ? $fieldNames[$key] : $fieldId);
				if (isset($newRow[$field])) {
					if (is_array($newRow[$field]) == false) {
						$newRow[$field] = array($newRow[$field]);
					}
					
					$newRow[$field][] = $itemValues[$key];
				} else {
					$newRow[$field] = $itemValues[$key];
				}
			}
			if (isset($includeTrackerDetails) && $includeTrackerDetails == true) {
				$newRow['status'.$trackerId] = $row['status']; 
				$newRow['trackerId'] = $row['trackerId'];
				$newRow['itemId'] = $row['itemId'];
			}
			$newResult[$row['itemId']] = $newRow;
		}
		unset($result);
		return $newResult;
	}
	
	/*Does the same thing as tracker_query, but uses tracker and field names rather than ids, a bit slower, but probably not noticed
	*/
	function tracker_query_by_names($tracker, $start, $end, $itemId, $equals, $search, $fields, $status, $sort, $limit, $offset, $includeTrackerDetails = true) {
		return $tracker = $this->tracker_query($tracker, $start, $end, $itemId, $equals, $search, $fields, $status, $sort, $limit, $offset, true, $includeTrackerDetails);
	}
	
	/*Removes fields from an array of items, can use either fields to show, or fields to remove, but not both
	 */
	function filter_fields_from_tracker_query($tracker, $fieldIdsToRemove = array(), $fieldIdsToShow = array()) {
		if (empty($fieldIdsToShow) == false) {
			$newTracker = array();
			foreach($tracker as $key => $item) {
				$newTracker[$key] = array();
				foreach($fieldIdsToShow as $fieldIdToShow) {
					$newTracker[$key][$fieldIdToShow] = $tracker[$key][$fieldIdToShow];
				}
			}
			
			return $newTracker;
		}
	
		if (empty($fieldIdsToRemove) == false) {
			foreach($tracker as $key => $item) {
				foreach($fieldIdsToRemove as $fieldIdToRemove) {
					unset($tracker[$key][$fieldIdToRemove]);
				}
			}
		}
		
		return $tracker;
	}

	/* Joins tracker arrays together.
	 */
	function join_trackers($trackerLeft, $trackerRight, $fieldLeftId, $joinType) {
		$joinedTracker = array();
		switch ($joinType) {
			case "outer":
				foreach($trackerRight as $key => $itemRight) {
					$match = false;
					foreach($trackerLeft as $itemLeft) {
						if ($key == $itemLeft[$fieldLeftId]) {
							$match = true;
							$joinedTracker[$key] = $itemLeft + $itemRight;
						} else {
							$joinedTracker[$key] = $itemLeft;
						}
					}
					
					if ($match == false) {
						$joinedTracker[$key] = $itemRight;
					}
				}
				break;
			default:
				foreach($trackerLeft as $key => $itemLeft) {
					if (isset($trackerRight[$itemLeft[$fieldLeftId]]) == true) {
						$joinedTracker[$key] = $itemLeft + $trackerRight[$itemLeft[$fieldLeftId]];
					} else {
						$joinedTracker[$key] = $itemLeft;
					}
				}
		}
			
		return $joinedTracker;
	}


	function to_csv($array, $header = false, $col_sep = ",", $row_sep = "\n", $qut = '"', $fileName = 'file.csv') {
		
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=".$fileName);
		header("Pragma: no-cache");
		header("Expires: 0");
		
		if (!is_array($array)) return false;

		//Header row.
		if ($header == true) {
			foreach ($array[0] as $key => $val)
			{
				//Escaping quotes.
				$key = str_replace($qut, "$qut$qut", $key);
				$output .= "$col_sep$qut$key$qut";
			}
			$output = substr($output, 1)."\n";
		}
		
		$cellKeys = array();
		$cellKeysSet = false;
		foreach ($array as $key => $val) {
			$tmp = '';
			
			if ($cellKeysSet == false) {
				foreach ($val as $cell_key => $cell_val) {
					$cellKeys[] = $cell_key;
				}
				$cellKeysSet = true;
			}
			
			
			foreach ($cellKeys as $cellKey) {
				//Escaping quotes.
				if (is_array($val[$cellKey]) == true) $val[$cellKey] = implode(" ", $val[$cellKey]);
				
				$cell_val = str_replace("\n", " ", $val[$cellKey]);
				$cell_val = str_replace($qut, "$qut$qut", $cell_val);
				$tmp .= "$col_sep$qut$cell_val$qut";
			}
			
			$output .= substr($tmp, 1).$row_sep;
		}
		
		return $output;
	}
}

global $trkqrylib;
$trkqrylib = new TrackerQueryLib;
