<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
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
 * @link		http://dev.tiki.org/Tracker_Query
 * @since		TIki 8
 */

class Tracker_Query
{
	private $tracker;
	private $start = 0;
	private $end = 0;
	private $itemId = 0;
	private $equals = array();
	private $search = array();
	private $fields = array();
	private $status = "opc";
	private $sort = null;
	private $limit = 100; //added limit so default wouldn't crash system
	private $offset = 0;
	private $byName = false;
	private $includeTrackerDetails = true;
	private $desc = false;
	private $render = true;
	private $excludeDetails = false;
	private $lastModif = true;
	private $delimiter = "[{|!|}]";
	private $debug = false;
	private $concat = true;
	private $filterType = array();
	private $inputDefaults = array();

	public static function tracker($tracker)
	{
		return new self($tracker);
	}

	public function start($start)
	{
		$this->start = $start;
		return $this;
	}

	public function end($end)
	{
		$this->end = $end;
		return $this;
	}

	public function itemId($itemId)
	{
		$this->itemId = (int)$itemId;
		return $this;
	}
	
	public function filter($filter = array())
	{
		$filter = array_merge(array(
			'field'=>'',
			'type'=> 'and',
			'value'=> ''
		), $filter);

		$this->fields[] = $filter['field'];
		$this->filterType[] = $filter['type']; //really only things that should be accepted are "and" and "or", woops, and "like"

		if ($filter['type'] == 'like') {
			$this->search[] = $filter['value'];
		} else {
			$this->equals[] = $filter['value'];
		}

		return $this;
	}

	public function filterFieldByValue($field, $value)
	{
		return $this->filter(array('field'=> $field, 'value'=>$value));
	}

	public function filterFieldByValueLike($field, $value)
	{
		return $this->filter(array('field'=> $field, 'value'=>$value, 'type'=> 'like'));
	}
	
	public function equals($equals = array())
	{
		$this->equals = $equals;
		return $this;
	}

	public function search($search)
	{
		$this->search = $search;
		return $this;
	}

	public function fields($fields = array())
	{
		$this->fields = $fields;
		return $this;
	}

	public function status($status)
	{
		$this->status = $status;
		return $this;
	}

	public function sort($sort)
	{
		$this->sort = $sort;
		return $this;
	}

	public function limit($limit)
	{
		$this->limit = $limit;
		return $this;
	}

	public function offset($offset)
	{
		$this->offset = $offset;
		return $this;
	}

	public function byName($byName = true)
	{
		$this->byName = $byName;
		return $this;
	}

	public function lastModif ()
	{
		$this->lastModif = true;
		return $this;
	}

	public function created()
	{
		$this->lastModif = false;
		return $this;
	}

	public function excludeDetails($excludeDetails = true)
	{
		$this->excludeDetails = $excludeDetails;
		return $this;
	}

	public function desc($desc)
	{
		$this->desc = $desc;
		return $this;
	}

	public function render($render)
	{
		$this->render = $render;
		return $this;
	}

	public function getOne()
	{
		return $this
			->limit(1)
			->query();
	}

	public function getLast()
	{
		return $this
			->desc(true)
			->limit(1)
			->query();
	}

	public function getItemId()
	{
		$query = $this->getOne();
		$key = (int)end(array_keys($query));
		$key = ($key > 0 ? $key : 0);
		return $key;
	}

	public function debug($debug = true, $concat = true)
	{
		$this->debug = $debug;
		$this->concat = $concat;
		return $this;
	}

	/* In the construct we putself(); the field options for "items list" (type 'l') into a table to be joined upon, 
	 * so instead of running a query for every row, we use simple joins to get the job done. We use a temporary
	 * table so that it is removed once the connection is closed or after the page loads.
	 */
	function __construct($tracker = '')
	{
		global $tikilib;
		$this->tracker = $tracker;

		$tikilib->query(
						"DROP TABLE IF EXISTS temp_tracker_field_options;
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
		
				SET group_concat_max_len = 4294967295;"
		);

		/*For any fields that have multi items, we use php to parse those out, there shouldn't be too many
		 */

		foreach ($tikilib->fetchAll("SELECT * FROM temp_tracker_field_options WHERE options LIKE '%|%'") as $row) {
			$option = explode(",", $row["options"]);
			$displayFieldIdsThere = explode("|", $option["3"]);
			foreach ($displayFieldIdsThere as $key => $displayFieldIdThere) {
				if ($key > 0) {
					$tikilib->query(
									"INSERT INTO temp_tracker_field_options	VALUES (?,?,?,?,?,?,?,?,?)",
									array(
										$row["trackerIdHere"],
										$row["trackerIdThere"],
										$row["fieldIdThere"],
										$row["fieldIdHere"],
										$displayFieldIdThere,
										$row["displayFieldIdHere"],
										$row["linkToItems"],
										$row["type"],
										$row["options"]
									)
					);
				}
			}
		}
	}

	/**
	 * Adds the field names to the beginning of the array of tracker items
	 *
	 */
	static function prepend_field_header(&$trackerPrimary = array(), $nameOrder = array())
	{
		global $tikilib;
		$result = $tikilib->fetchAll("SELECT fieldId, trackerId, name FROM tiki_tracker_fields");

		$header = array();

		foreach ($result as $row) {
			$header[$row['fieldId']] = $row['name'];
		}

		$joinedTrackerHeader = array();

		foreach ($trackerPrimary as $item) {
			foreach ($item as $key => $field) {
				$joinedTrackerHeader[$key] = $header[$key];
			}
		}

		if (!empty($nameOrder)) {
			$sortedHeader = array();
			$unsortedHeader = array();
			foreach ($nameOrder as $name) {
				foreach ($joinedTrackerHeader as $key => $field) {
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

	/**
	 * Simple direction parsing from string to type
	 *
	 */
	static private function sort_direction($dir)
	{
		switch ( $dir ) {
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

	static function arfsort( &$array, $fieldList )
	{
		if (!is_array($fieldList)) {
			$fieldList = explode('|', $fieldList);
			$fieldList = array(array($fieldList[0], self::sort_direction($fieldList[1])));
		} else {
			for ($i = 0, $count_fieldList = count($fieldList); $i < $count_fieldList; ++$i) {
				$fieldList[$i] = explode('|', $fieldList[$i]);
				$fieldList[$i] = array($fieldList[$i][0], self::sort_direction($fieldList[$i][1]));
			}
		}

		$GLOBALS['__ARFSORT_LIST__'] = $fieldList;
		usort($array, 'arfsort_func');
	}

	function arfsort_func($a, $b)
	{
		foreach ( $GLOBALS['__ARFSORT_LIST__'] as $f ) {

			switch ($f[1]) {
				case SORT_NUMERIC:
					$strc = ((float)$b[$f[0]] > (float)$a[$f[0]] ? -1 : 1);
					return $strc;
								break;

				default:
					$strc = strcasecmp($b[$f[0]], $a[$f[0]]);
					if ( $strc != 0 ) {
						return $strc * (!empty($f[1]) && $f[1] == SORT_DESC ? 1 : -1);
					}
			}
		}
		return 0;
	}
	
	private function concat_str($field)
	{
		if ($this->concat == false) {
			return $field;
		} else {
			return "GROUP_CONCAT(".$field." SEPARATOR '" . $this->delimiter . "')";
		}
	}
	
	private function trackerId()
	{
		if ($this->byName == true) {
			$trackerId = TikiLib::lib('trk')->get_tracker_by_name($this->tracker);
		} else {
			$trackerId = $this->tracker;
		}
		
		if (!empty($trackerId) && !is_numeric($trackerId)) { 
			throw new Exception("Opps, looks like you need to call ->byName();");
		}
		
		return $trackerId;
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

	function query()
	{
		global $tikilib, $trklib;
		$params = array();
		$fields_safe = "";
		$status_safe = "";
		$isSearch = false;

		$trackerId = $this->trackerId();

		if (empty($trackerId)) {//if we can't find a tracker, then return
			return array();
		}

		$trackerDefinition = Tracker_Definition::get($trackerId);

		$trackerFieldDefinition = $trackerDefinition->getFieldsIdKeys();

		$params[] = $trackerId;

		if (!empty($this->start) && !$this->search) {
			$params[] = $this->start;
		}

		if (!empty($this->end) && !$this->search) {
			$params[] = $this->end;
		}

		if (!empty($this->itemId) && !$this->search) {
			$params[] = $this->itemId;
		}


		/*Get field ids from names*/
		if ($this->byName == true && !empty($this->fields)) {
			$fieldIds = array();
			foreach ($this->fields as $field) {
				$fieldIds[] = $tikilib->getOne(
								"SELECT fieldId FROM tiki_tracker_fields" .
								" LEFT JOIN tiki_trackers ON (tiki_trackers.trackerId = tiki_tracker_fields.trackerId)" .
								" WHERE" .
								" tiki_trackers.name = ? AND tiki_tracker_fields.name = ?",
								array($this->tracker, $field)
				);
			}
			$this->fields = $fieldIds;
		}

		if (count($this->fields) > 0 && (count($this->equals) > 0 || count($this->search) > 0)) {
			for ($i = 0, $count_fields = count($this->fields); $i < $count_fields; $i++) {
				if (strlen($this->fields[$i]) > 0) {
					$fields_safe .= " ( search_item_fields.fieldId = ? ";
					$params[] = $this->fields[$i];

					if (strlen($this->equals[$i]) > 0) {
						$fields_safe .= " AND search_item_fields.value = ? ";
						$params[] = $this->equals[$i];
					} elseif (strlen($this->search[$i]) > 0) {
						$fields_safe .= " AND search_item_fields.value LIKE ? ";
						$params[] = '%' . $this->search[$i] . '%';
					}

					$fields_safe .= " ) ";


					if ($i + 1 < count($this->fields) && count($this->fields) > 1) $fields_safe .= " OR ";
				}
			}

			if (strlen($fields_safe) > 0) {
				$fields_safe = " AND ( $fields_safe ) ";
				$isSearch = true; 
			}
		}

		if (strlen($this->status) > 0) {
			for ($i=0, $strlen_status = strlen($this->status); $i < $strlen_status; $i++) {
				if (strlen($this->status[$i]) > 0) {
					$status_safe .= " tiki_tracker_items.status = ? ";
					if ($i + 1 < strlen($this->status) && strlen($this->status) > 1) $status_safe .= " OR ";
					$params[] = $this->status[$i];
				}
			}

			if (strlen($status_safe) > 0) {
				$status_safe = " AND ( $status_safe ) ";
			}
		}

		if ( !empty($this->limit) && is_numeric($this->limit) == false) {
			unset($this->limit);
		}

		if ( isset($this->offset) && !empty($this->offset) && is_numeric($this->offset) == false) {
			unset($this->offset);
		}

		$dateUnit = ($this->lastModif ? 'lastModif' : 'created');

		$query = 
			"SELECT
			tiki_tracker_items.status,
			tiki_tracker_item_fields.itemId,
			tiki_tracker_fields.trackerId,
			" . $this->concat_str("tiki_tracker_fields.name") ." AS fieldNames,
			" . $this->concat_str("tiki_tracker_item_fields.fieldId") . " AS fieldIds,
			" . $this->concat_str("IFNULL(items_right.value, tiki_tracker_item_fields.value)") . " AS item_values

				FROM tiki_tracker_item_fields " . ($isSearch == true ? " AS search_item_fields " : "") . "

				" . ($isSearch == true ? "
				LEFT JOIN tiki_tracker_item_fields ON 
				search_item_fields.itemId = tiki_tracker_item_fields.itemId
				" : "" ) . "
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
				tiki_trackers.trackerId = ?

				". (!empty($this->start) && !$this->search ? " AND tiki_tracker_items.$dateUnit > ? " : "") . "
				". (!empty($this->end) && !$this->search ? 	" AND tiki_tracker_items.$dateUnit < ? " : "") . "
				". (!empty($this->itemId) && !$this->search ? " AND tiki_tracker_item_fields.itemId = ? " : "") . "
				". (!empty($fields_safe) ? $fields_safe : "") . "
				". (!empty($status_safe) ? $status_safe : "") . "

				GROUP BY
				tiki_tracker_item_fields.itemId " . ($this->desc == true ? 'DESC' : 'ASC') . "
				" . ($isSearch == true ? ", search_item_fields.fieldId, search_item_fields.itemId " : "" ) . "
				ORDER BY 
				tiki_tracker_items.$dateUnit
				" . (!empty($this->limit) ? 
				" LIMIT " . (!empty($this->offset) ? $this->offset . ", " : "") . " " . $this->limit
				: ""
				);

		if ($this->debug == true) {
			$result = array($query, $params);
			print_r($result);
			print_r($tikilib->fetchAll($query, $params));
			die;
		} else {
			$result = $tikilib->fetchAll($query, $params);
		}

		$newResult = array();
		$neededMatches = count($this->fields);
		foreach ($this->fields as $i=>$field) {
			if ($this->filterType[$i] != 'and') $neededMatches--;
		}
		
		foreach ($result as $key => $row) {
			if (isset($newResult[$row['itemId']])) continue;
			
			$newRow = array();
			$fieldNames = explode($this->delimiter, $row['fieldNames']);
			$fieldIds = explode($this->delimiter, $row['fieldIds']); 
			$itemValues = explode($this->delimiter, $row['item_values']);
			
			$matchCount = 0;
			foreach ($fieldIds as $key => $fieldId) {
				$field = ($this->byName == true ? $fieldNames[$key] : $fieldId);
				$value = '';
				
				//This script attempts to narrow the results further by using an "AND" style checking of the returned result since it cannot be made at this time in mysql 
				if ($neededMatches > 0) {
					$i = array_search($fieldId, $this->fields, true);
					if ($i !== FALSE) {
						if ($this->equals[$i] == $itemValues[$key] && $this->filterType[$i] == 'and') {
							$matchCount++;
						}
					}
				}
				//End "AND" style checking of results
				
				if ($this->render == true) {
					$value = $this->render_field_value($trackerFieldDefinition[$fieldId], $itemValues[$key]);
				} else {
					$value = $itemValues[$key];
				}
				
				if (isset($newRow[$field])) {
					if (is_array($newRow[$field]) == false) {
						$newRow[$field] = array($newRow[$field]);
					}

					$newRow[$field][] = $value;
				} else {
					$newRow[$field] = $value;
				}

			}
			if ($this->excludeDetails == false) {
				$newRow['status'.$trackerId] = $row['status']; 
				$newRow['trackerId'] = $row['trackerId'];
				$newRow['itemId'] = $row['itemId'];
			}

			if ($neededMatches == 0 || $neededMatches == $matchCount) {
				$newResult[$row['itemId']] = $newRow;
			}
		}
		unset($result);

		return $newResult;
	}

	private function render_field_value($fieldDefinition, $value)
	{
		global $trklib;
		$fieldDefinition['value'] = $value;

		//if type is text, no need to render value
		switch ($fieldDefinition['type']) {
			case 't': 
				return $value;
		}

		return $trklib->field_render_value(
						array(
							'field'=> $fieldDefinition,
							'process'=> 'y',
							'list_mode'=> 'y'
						)
		);
	}

	/**
	 * Removes fields from an array of items, can use either fields to show, or fields to remove, but not both
	 *
	 */
	static function filter_fields_from_tracker_query($tracker, $fieldIdsToRemove = array(), $fieldIdsToShow = array())
	{
		if (empty($fieldIdsToShow) == false) {
			$newTracker = array();
			foreach ($tracker as $key => $item) {
				$newTracker[$key] = array();
				foreach ($fieldIdsToShow as $fieldIdToShow) {
					$newTracker[$key][$fieldIdToShow] = $tracker[$key][$fieldIdToShow];
				}
			}

			return $newTracker;
		}

		if (empty($fieldIdsToRemove) == false) {
			foreach ($tracker as $key => $item) {
				foreach ($fieldIdsToRemove as $fieldIdToRemove) {
					unset($tracker[$key][$fieldIdToRemove]);
				}
			}
		}

		return $tracker;
	}

	/**
	 * Joins tracker arrays together.
	 *
	 */
	static function join_trackers($trackerLeft, $trackerRight, $fieldLeftId, $joinType)
	{
		$joinedTracker = array();

		switch ($joinType) {
			case "outer":
				foreach ($trackerRight as $key => $itemRight) {
					$match = false;
					foreach ($trackerLeft as $itemLeft) {
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
				foreach ($trackerLeft as $key => $itemLeft) {
					if (isset($trackerRight[$itemLeft[$fieldLeftId]]) == true) {
						$joinedTracker[$key] = $itemLeft + $trackerRight[$itemLeft[$fieldLeftId]];
					} else {
						$joinedTracker[$key] = $itemLeft;
					}
				}
		}

		return $joinedTracker;
	}


	static function to_csv($array, $header = false, $col_sep = ",", $row_sep = "\n", $qut = '"', $fileName = 'file.csv')
	{

		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=".$fileName);
		header("Pragma: no-cache");
		header("Expires: 0");

		if (!is_array($array)) 
			return false;
		$output = '';

		//Header row.
		if ($header == true) {
			foreach ($array[0] as $key => $val) {
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

	/**
	 * Programmatic and simplified way of replacing or updating a tracker item, meant for api ease and accessibility
	 * Does not check permissions
	 *
	 * @param array $data example array(fieldId=>'value', fieldId=>'value') or array('fieldName'=>'value', 'fieldName'=>'value')
	 */
	public function replaceItem($data = array())
	{
		$itemData = array();

		$fields = TikiLib::lib("trk")->list_tracker_fields($this->trackerId());
		for($i = 0, $fieldCount = count($fields['data']); $i < $fieldCount; $i++) {
			if ($this->byName == true) {
				$fields['data'][$i]['value'] = $data[$fields['data'][$i]['name']];
			} else {
				$fields['data'][$i]['value'] = $data[$fields['data'][$i]['fieldId']];
			}
		}

		$itemId = TikiLib::lib("trk")->replace_item($this->trackerId(), $this->itemId, $fields);

		return $itemId;
	}

	private function getInputsForItem($itemId = 0, $includeJs)
	{
		$headerlib = TikiLib::lib("header");
		$itemId = (int)$itemId;

		if ($includeJs == true)
			$headerlibClone = clone $headerlib;

		$trackerId = $this->trackerId();
		if ($trackerId < 1) return array();

		$trackerDefinition = Tracker_Definition::get($trackerId);

		$fields = array();

		$fieldFactory = new Tracker_Field_Factory($trackerDefinition);
		$itemData = TikiLib::lib("trk")->get_tracker_item($itemId);

		foreach ($trackerDefinition->getFields() as $field) {
			$fieldKey = ($this->byName == true ? $field['name']  : $field['fieldId']);

			if ($includeJs == true)
				$headerlib->clear_js();

			$field['ins_id'] = "ins_" . $field['fieldId'];

			if ($itemId == 0 && isset($this->inputDefaults)) {
				$field['value'] = $this->inputDefaults[$fieldKey];
			}

			$fieldHandler = $fieldFactory->getHandler($field, $itemData);

			$fieldInput = $fieldHandler->renderInput();

			if ($includeJs == true)
				$fieldInput = $fieldInput . $headerlib->output_js();

			$fields[$fieldKey] = $fieldInput;
		}

		if ($includeJs == true) //restore the header to the way it was originally
			$headerlib = $headerlibClone;

		return $fields;
	}

	public function inputDefaults($defaults = array())
	{
		$this->inputDefaults = $defaults;
		return $this;
	}

	public function queryInputs($includeJs = false)
	{
		$query = $this->query();

		$items = array();
		foreach ($query as $itemId => $item) {
			$items[] = $this->getInputsForItem($itemId, $includeJs);
		}

		return $items;
	}

	public function queryInput($includeJs = false)
	{
		return $this->getInputsForItem($this->itemId, $includeJs);
	}
}
