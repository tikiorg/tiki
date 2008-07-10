<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

//include_once ('lib/notifications/notificationlib.php');
//include_once ('trackerlib.php');

/*
the goal of this library is to improve old-style trackers 
so that we can easily manipulate a table containing values
of a form outside Tikiwiki.
To achieve that, we store field values in a dedicated 
table for each tracker:

	Each column is a field 
	Each row is an item (list of values of a form).
	
	So we can avoid using `tiki_tracker_item_fields`.
	
	Tables look like this :
	
	tiki_trk_n
	+------+-------+-------+-------+...+-------+
	|itemId|field_1|field_2|field_3|...|field_n|
	+------+-------+-------+-------+...+-------+
	
	We use a convention in order to create valid names
	for table and fields.
	
	table name : $TABLE_PREFIX.trackerId
	field name : $COL_PREFIX.fieldId
	
	'trackerId' is the primary key from 'tiki_trackers'
	'fieldId' is the primary key from 'tiki_tracker_fields'
	'itemId' is the primary key from 'tiki_tracker_items'
		
	You can export a table too, with names in clear. Obviously these
	names must be valid SQL identifiers.
	
	you can also use names of columns in clear if you respect
	following conventions : 
		- the name of the tracker must be unique
		- a field name must be unique for a tracker
		- names must be a valid in SQL
	
	Tables look like this :
		
	tiki_trk_nom_tracker
	+------+----------+----------+----------+...+----------+
	|itemId|nom_champ1|nom_champ2|nom_champ3|...|nom_champn|
	+------+----------+----------+----------+...+----------+
*/

class TrkWithMirrorTablesLib extends TrackerLib {
		
	var $TABLE_PREFIX;
	var $COL_PREFIX;
	var $EXPLICIT_PREFIX;
	var $explicit;
	
	function TrkWithMirrorTablesLib($db) {
		parent::TrackerLib($db);
		
		$this->TABLE_PREFIX	= "tiki_trk_";
		$this->COL_PREFIX	= "field_";
		$this->EXPLICIT_PREFIX	= $this->TABLE_PREFIX;
	}
	
	// return a name for a tracker value table
	// that respect the naming convention.
	function get_table_id($trackerId, $explicit=false) {
		if($trackerId) {
			if($explicit) {
				$name = $this->EXPLICIT_PREFIX;
				$name.= $this->getOne("select `name` from `tiki_trackers` where `trackerId`=?", array((int)$trackerId));
				return $name;
			}
			return $this->TABLE_PREFIX.$trackerId;
		}
		return "";
	}

	// return a name for a column of a 
	// tracker value table that respect 
	// the naming convention.
	function get_col_id($fieldId, $explicit=false) {
		if($fieldId) {
			if($explicit) {
				return $this->getOne("select `name` from `tiki_tracker_fields` where `fieldId`=?", array((int)$fieldId));
			}
			return  $this->COL_PREFIX.$fieldId;
		}
		return "";
	}
	
	// extract the primary key from a name
	// created by 'get_table_id' or 'get_col_id'.
	function get_id_from_name($prefix, $name, $explicit=false, $trackerId=null) {
		if($explicit && $trackerId) {
			return $this->getOne("select `fieldId` from `tiki_tracker_fields` where `trackerId`=? and `name`=?", array((int)$trackerId, $name));
		}
		else {
			return substr($name, strlen($prefix));
		}
	}
	
	// return the good SQL type associated
	// with a field type.
	function get_sql_type($type) {
		// TODO
		return "TEXT";
	}

	/**
	 * Returns the trackerId of the tracker possessing the item ($itemId)
	 */
	function get_tracker_for_item($itemId) {
		$query = "select t1.`trackerId` from `tiki_trackers` t1, `tiki_tracker_items` t2 where t1.`trackerId`=t2.`trackerId` and `itemId`=?";		
		return $this->getOne($query,array((int) $itemId));			
	}
	
	// 
	function get_tracker_values_specs($trackerId, &$explicit, &$tableDsn) {
		
		$r = $this->getOne("select `value` from `tiki_tracker_options` where `name`='useExplicitNames' and `trackerId`=?", array((int)$trackerId));
		$tableDsn = 'tiki';
		$explicit = $r == "y" ? true : false;
	}
	
	// check_col_name and check_table_name are used
	// when using explicit names in db values tables
	
	function check_col_name($colName, $update, $trackerId) {
			
		$preg = preg_match("/^[a-zA-Z][a-zA-Z0-9_]+$/", $colName);
		//echo "<br>CHECK COL NAME : $colName : -> $preg";
		if ($preg > 0) {
			if($update == false) {
				$query = "select distinct `name` from `tiki_tracker_fields` where `trackerId`=?";
				$res = $this->query($query, array((int)$trackerId));
				while($r = $res->fetchRow()) {
					if($colName == $r['name'])
						return false;
				}
			}
			// nom de colonne ok
			//echo "TRUE<br>\n";
			return true;
		}
		elseif ($preg == 0) {
			// erreur nom de colonne
			//echo "FALSE<br>\n";
			return false;
		} 
		elseif ($preg == false) {
			// erreur sur l'expression r�guli�re
			//echo "FALSE<br>\n";
			return false;
		}
	}
	
	function check_table_name($tableName, $update) {
	
		$preg = preg_match("/^[a-zA-Z][a-zA-Z0-9_]+$/", $tableName);
		if ($preg > 0) {
			if($update == false) {
				$query = "select distinct `name` from `tiki_trackers`";
				$res = $this->query($query);
				while($r = $res->fetchRow()) {
					if($tableName == $r['name'])
						return false;
				}
			}
			// nom de table ok
			return true;
		}
		elseif ($preg == 0) {
			// erreur nom de table
			return false;
		} 
		elseif ($preg == false) {
			// erreur sur l'expression r�guli�re
			return false;
		}
	}
	
	function first_field_name($trackerId) {
		$query  = "select distinct `name` from tiki_tracker_fields where `trackerId`=?";
		$res = $this->query($query, array((int)$trackerId));
		while($r = $res->fetchRow()) {
			return $r['name'];
		}
	}
	
	// this method create the tables of values
	// associated with a newly created tracker
	function create_value_table(	$trackerId,
					$explicit = false,
					$dsn = null) {
	
		if(!$trackerId)
			return;
			
		// retrieve all field ids of the tracker
		$preQuery = "select `fieldId`, `type`, `name` from `tiki_tracker_fields` where `trackerId`=?";
		$result = $this->query($preQuery, array((int)$trackerId));

		if($result) {
			$tableName = $this->get_table_id($trackerId, $explicit);	
			$query = "create table $tableName(itemId INT NOT NULL, ";
			while ($res = $result->fetchRow()) {
			
				if($explicit) {
					if($this->check_col_name($res['name'], false, $trackerId))
						$query.= $res['name'];
					else
						return;
				}
				else {
					$query.= $this->get_col_id($res['fieldId']);
				}
				$query.= " ".$this->get_sql_type($res['type']).", ";
			}
			$query.= "PRIMARY KEY(itemId))";
		
			//echo "REQ : ".$query;
			$result = $this->query($query);
		}
	}
		
	function drop_value_table(	$trackerId,
					$explicit = false,
					$dsn = null) {
	
		if(!$trackerId)
			return;
		
		$tableName = $this->get_table_id($trackerId, $explicit);
		
		$query = "drop table $tableName";
		//echo "REQ : ".$query;
		$result = $this->query($query);
	}
	
	// create a table of values using name of fields
	// instead of the namming convention for the column names.
	/*function export_tracker($trackerId) {
	
		$this->get_tracker_values_specs($trackerId, $explicit, $dsn);
		if($explicit)
			return;
	
		if(!($trackerId && $this->getOne("select * from `tiki_trackers` where `trackerId`=?", array((int)$trackerId))))
			return;
	
		$name = $this->getOne("select `name` from `tiki_trackers` where `trackerId`=?", array($trackerId));
	
		$query = "select `name`, `fieldId`, `type`";
		$query.= " from `tiki_tracker_fields`";
		$query.= " where `trackerId`=?";
		$res = $this->query($query, array($trackerId));
		
		while($r = $res->fetchRow()) {
			$tab[$r['name']]['fieldId'] = $r['fieldId'];
			$tab[$r['name']]['type'] = $r['type'];
		}
		
		$create = "create table $name (itemId INT NOT NULL,";
		foreach($tab as $n => $v) {
			$create.= $n." ".$this->get_sql_type($v['type']).",";
		}
		$create.="PRIMARY KEY(itemId))";
		//echo "<br>CREATE : $create<br>\n";
		$this->query($create);
		
		$query = "select ttv.* from ".$this->get_table_id($trackerId)." ttv, tiki_tracker_items tti";
		$query.= " where ttv.`itemId`=tti.`itemId` and tti.`trackerId`=?";
		$res = $this->query($query, array($trackerId));
		
		$insert = "insert into $name values ";
		$first = true;
		while($r = $res->fetchRow()) {
			if(!$first)
				$insert.=",";
			else
				$first = false;
			
			$insert.= "('".$r['itemId']."'" ;
			foreach($tab as $v) {
			
				$value = $r[$this->get_col_id($v['fieldId'])];
				$value = $value == '' ? 'NULL' : $value;
				$insert.= ",'".$value."'";
			}
			$insert.=")";
		}
		//echo "<br>INSERT : $insert<br>\n";
		$this->query($insert);
	}*/
	
	// ##############################################
	// this method return an associative array containing
	// values of an item.
	// return a table |fieldId|value| 
	// ########## OK ##########
	// ##############################
	function get_tracker_item($itemId) {
	
		if(!$itemId)
			return;
	
		$query = "select `trackerId` from `tiki_tracker_items`";
		$query.= " where `itemId`=?";
		$trackerId = $this->getOne($query, array($itemId));
		
		if($trackerId) {
		
			$this->get_tracker_values_specs($trackerId, $explicit, $dsn);
			$tableName = $this->get_table_id($trackerId, $explicit);
				
			$query = "select * from $tableName";
			$query.= " where `itemId`=?";
			$result = $this->query($query, array($itemId));
			
			if($res = $result->fetchRow()) {
				foreach($res as $k => $v) {
					if($k != "itemId") {
						$tab[$this->get_id_from_name($this->COL_PREFIX, $k, $explicit, $trackerId)] = $v;
					}
				}
				return $tab;
			}
		}
		return false;
	}

	// ########## OK ##########
	// ##############################
	function get_item_id($trackerId,$fieldId,$value) {
	
		if(!$trackerId || !$fieldId)
			return;
			
		$this->get_tracker_values_specs($trackerId, $explicit, $dsn);
		$tableId = $this->get_table_id($trackerId, $explicit);
		$colId = $this->get_col_id($fieldId, $explicit);
		
		$query = "select distinct `itemId` from $tableId where $colId=?";
		return $this->getOne($query, array($value));
	}
	
	// ########## OK ##########
	// ##############################
	/* experimental shared */
	function get_item_value($trackerId,$itemId,$fieldId) {
		
		if(!$trackerId || !$itemId || !$fieldId) 
			return;
	
		$this->get_tracker_values_specs($trackerId, $explicit, $dsn);
		$tableId = $this->get_table_id($trackerId, $explicit);
		$colId = $this->get_col_id($fieldId, $explicit);
		
		$query = "select $colId from $tableId where `itemId`=?";
		return $this->getOne($query, array($itemId));
	}

	// ########## OK ##########
	// ##############################
	/* experimental shared */
	function get_items_list($trackerId,$fieldId,$value,$status='o') {
	
		if(!$trackerId || !$fieldId) 
			return;
			
		$this->get_tracker_values_specs($trackerId, $explicit, $dsn);
		$tableId = $this->get_table_id($trackerId, $explicit);
		$colId = $this->get_col_id($fieldId, $explicit);
		
		$query = "select tti.`itemId` from `tiki_tracker_items` tti, $tableId vt";
		$query.= " where tti.`itemId`=vt.`itemId` and vt.$colId=? and tti.`status`=?";
		$result = $this->query($query, array($value, $status));
		
		$itemList = array();
		while($res = $result->fetchRow()) {
			$itemList[] = $res['itemId'];
		}
		return $itemList;
	}

	// ########## OK ########## // REVOIR
	// ##############################
	function get_all_items($trackerId,$fieldId,$status='o') {
		
		if(!$trackerId || !$fieldId) 
			return;
			
		global $cachelib;
		// --
		$this->get_tracker_values_specs($trackerId, $explicit, $dsn);
		$colId = $this->get_col_id($fieldId, $explicit);
		$tableId = $this->get_table_id($trackerId, $explicit);
		$sort_mode = "$colId asc"; //"value_asc";
		// --
		$cache = md5('trackerfield'.$fieldId.$status);
		if (!$cachelib->isCached($cache)) {
			$sts = preg_split('//', $status, -1, PREG_SPLIT_NO_EMPTY);
			$mid = " and (".implode('=? or ',array_fill(0,count($sts),'tti.`status`'))."=?) ";

			$bindvars = $sts;
			
			$query = "select tti.`itemId`, $tableId.$colId from `tiki_tracker_items` tti, $tableId";
			$query.= " where tti.`itemId`=$tableId.`itemId` $mid";
			$query.= " order by $sort_mode";
			//echo "REQUEST : $query<br>\n";
			$result = $this->query($query, $bindvars);
			
			$ret = array();
			//echo "<br>GET ALL ITEMS<br>\n";
			while($res = $result->fetchRow()) {
				$id = $res['itemId'];
				$ret[$id] = $res[$colId];
			}
			$cachelib->cacheItem($cache,serialize($ret));
			return $ret;
		} else {
			return unserialize($cachelib->getCached($cache));
		}
	}
	
	// ########## OK ##########
	// ##############################
	/* experimental shared */
	function list_items(
			$trackerId, 
			$offset, 
			$maxRecords, 
			$sort_mode, 
			$listfields, 
			$filterfield='', 
			$filtervalue='', 
			$status = '', 
			$initial = '',
			$exactvalue='',
			$numsort=false) {
			
		if(!$trackerId)
			return;
			
		global $tiki_p_view_trackers_pending;
		global $tiki_p_view_trackers_closed;
		global $tiki_p_admin_trackers;
		
		$mid = " where tti.`trackerId`=? ";
		$bindvars = array((int) $trackerId);
		
		if ($status) {
			if (sizeof($status > 1)) {
				if ($tiki_p_view_trackers_pending != 'y') $status = str_replace('p','',$status);
				if ($tiki_p_view_trackers_closed != 'y') $status = str_replace('c','',$status);
				$sts = preg_split('//', $status, -1, PREG_SPLIT_NO_EMPTY);
				$mid.= " and (".implode('=? or ',array_fill(0,count($sts),'`status`'))."=?) ";
				$bindvars = array_merge($bindvars,$sts);
			} else {
				$mid.= " and tti.`status`=? ";
				$bindvars[] = $status;
			}
		}
		if (!$sort_mode) {
			$sort_mode = "lastModif_desc";
		}

		$csort_mode = '';
		$corder = "asc";
		
		$this->get_tracker_values_specs($trackerId, $explicit, $dsn);
		$tableId = $this->get_table_id($trackerId, $explicit);
		
		// if you choose a sort method
		// (selecting a column, using a filter...)
		// ------------------------------------------------------------------
		if (substr($sort_mode,0,2) == "f_" or $filtervalue or $exactvalue) {
		
			if ($exactvalue) {
				$bindvars[] = $exactvalue;
				$csort_mode = $filterfield;
				$corder = "asc";
				
				$colId = $this->get_col_id($csort_mode, $explicit);
				$mid.= "and $tableId.$colId=?";
			// if you enter a value for a field
			// that you want to filter.
			} elseif ($filtervalue && $filterfield) {
			
				if (substr($filtervalue,0,1) == '*') {
					$bindvars[] = '%'. substr($filtervalue,1);
				} elseif (substr($filtervalue,-1,1) == '*') {
					$bindvars[] = substr($filtervalue,0,strlen($filtervalue)-1). '%';
				} else {
					$bindvars[] = '%'.$filtervalue.'%';
				}
				$csort_mode = $filterfield;
				$corder = "asc";

				$colId = $this->get_col_id($csort_mode, $explicit);
				$mid.= "and $tableId.$colId like ?";
			} else {
				list($a,$csort_mode,$corder) = split('_',$sort_mode);
				$colId = $this->get_col_id($csort_mode, $explicit);
				if($colId == "") {
					$colId = $this->first_field_name($trackerId);
				}
			}
			
			if (substr($sort_mode,0,2) == "f_") {
				list($a,$asort_mode,$aorder) = split('_',$sort_mode);
			}
			
			// if you select an initial letter
			// you must have selected a filter before
			if ($initial) {
				$bindvars[] = $initial.'%';
				$mid.= "and $tableId.$colId like ?";
			}
			
			if ($numsort) {
				$query = "select tti.*, $tableId.$colId, right(lpad($tableId.$colId,40,'0'),40) as ok";
				$query.= " from `tiki_tracker_items` tti, $tableId";
				$query.= " $mid and $tableId.`itemId`=tti.`itemId`";
				$query.= " order by ".$this->convert_sortmode('ok_'.$corder);
			} else {
				$query = "select tti.*, $tableId.$colId";
				$query.= " from `tiki_tracker_items` tti, $tableId";
				$query.= " $mid and $tableId.`itemId`=tti.`itemId`";
				$query.= " order by $tableId.$colId $corder";
			}
			$query_cant = " select count(*) from `tiki_tracker_items` tti, $tableId";
			$query_cant.= " $mid and tti.`itemId`=$tableId.`itemId`";
		} else {
			$query = "select * from `tiki_tracker_items` tti $mid order by ".$this->convert_sortmode($sort_mode);
			$query_cant = "select count(*) from `tiki_tracker_items` tti $mid ";
		}
		
		//-----------------------------------------------------
		$midResult = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		
		if(isset($colId)) {
			$fid = $this->get_id_from_name($this->COL_PREFIX, $colId, $explicit, $trackerId);
			$qFid = "select `type` from `tiki_tracker_fields` where `fieldId`=$fid";
			$ftype = $this->getOne($qFid);
		}
	
		$result = array();
		while ($res = $midResult->fetchRow()) {
			if(isset($ftype) && isset($colId)) { // if we need sort
				$res['value'] = $res[$colId];
				$res['type'] = $ftype;
			}
			$result[] = $res;
		}
		
		$type = '';
		$ret = array();
		$opts = $optsl = array();
		
		$qVisible = "select `fieldId`, `isPublic` from `tiki_tracker_fields`";
		$qVisible.= " where `trackerId`=? order by `position` asc";
		$rVisible = $this->query($qVisible, array((int)$trackerId));
		while($rslt = $rVisible->fetchRow())
			$visible[] = $rslt;
		
		// for each item
		foreach ($result as $res) {
		
			$fields = array();
			$opts = array();
			
			$itid = $res["itemId"];
			$query2 = "select * from $tableId where `itemId`=?";
			$result2 = $this->query($query2, array($itid));
			
			$last = array();
			$fil = array();
			$kx = "";
			
			
			if($res2 = $result2->fetchRow()) {
			
				$needPublic = true;
				if ($tiki_p_admin_trackers == 'y')
					$needPublic = false;
					
				foreach($visible as $v) {
					
					if(($needPublic && $v['isPublic']) || !$needPublic) {
						$fid = $this->get_col_id($v['fieldId'], $explicit);
						$fil[$v['fieldId']] = $res2[$fid];
					}
				}
			}

			foreach ($listfields as $fieldId=>$fopt) {
			
				if (isset($fil[$fieldId])) {
					$fopt['value'] = $fil[$fieldId];
				} else {
					$fopt['value'] = "";
				}
				$fopt["linkId"] = '';
				if ($fopt["type"] == 'r') {
					$fopt["links"] = array();
					if (!$opts) {
						$opts = split(',',$fopt['options']);
					}
					$fopt["linkId"] = $this->get_item_id($opts[0],$opts[1],$fopt["value"]);
					
					$fopt["trackerId"] = $opts[0];
				} elseif ($fopt["type"] == 'a') {
					$fopt["pvalue"] = $this->parse_data(trim($fopt["value"]));
				} elseif ($fopt["type"] == 's') {
					$key = 'tracker.'.$trackerId.'.'.$itid;
					$fopt["numvotes"] = $this->getOne("select count(*) from `tiki_user_votings` where `id`=?",array($key));
					if ($fopt["numvotes"] > 0) {
						$voteavg = $fopt["value"]/$fopt["numvotes"];
					} else $voteavg = '0';
					$fopt["voteavg"] = $voteavg;
				} elseif ($fopt["type"] == 'e') {
					global $categlib;
					if (!is_object($categlib)) include_once 'lib/categories/categlib.php';
					$mycats = $categlib->get_child_categories($fopt['options']);
					$zcats = $categlib->get_object_categories("tracker ".$trackerId,$res["itemId"]);
					$cats = array();
					foreach ($mycats as $m) {
						if (in_array($m['categId'],$zcats)) {
							$cats[] = $m;
						}
					}
					$fopt['categs'] = $cats;	
				} elseif ($fopt["type"] == 'l') {
					$optsl = split(',',$fopt['options']);
					$fopt["links"] = array();
					$lst = $last[$optsl[2]];
					if ($lst) {
						$links = $this->get_items_list($optsl[0],$optsl[1],$lst);
						foreach ($links as $link) {
							$fopt["links"][$link] = $this->get_item_value($optsl[0],$link,$optsl[3]);
						}
						$fopt["trackerId"] = $optsl[0];
					}
				}
				if (isset($fopt["options"])) {
					$fopt["options_array"] = split(',',$fopt["options"]);
				}
				if (empty($asort_mode) || ($fieldId == $asort_mode)) {
					$kx = $fopt["value"].'.'.$itid;
				}
				$last[$fieldId] = $fopt["value"];
				$fields[] = $fopt;
			}
			$res["field_values"] = $fields;
			$res["comments"] = $this->getOne("select count(*) from `tiki_tracker_item_comments` where `itemId`=?",array((int) $itid));
			if ($kx == "") // ex: if the sort field is non visible, $kx is null
				$ret[] = $res;
			else
				$ret["$kx"] = $res;
		}
		if (isset($aorder) && $aorder == 'asc') {
			uksort($ret, 'strnatcasecmp');
		}
		
		$retval = array();
		$retval["data"] = array_values($ret);
		$retval["cant"] = $cant;
		return $retval;
	}
	
	// ########## OK ##########
	// ##############################
	function replace_item($trackerId, $itemId, $ins_fields, $status = '') {
	
		if(!$trackerId)
			return;
	
		global $user, $smarty, $notificationlib, $prefs, $cachelib;
		
		// update
		if ($itemId) {
			if ($status) {
				$query = "update `tiki_tracker_items` set `status`=?,`lastModif`=? where `itemId`=?";
				$result = $this->query($query,array($status,(int) $this->now,(int) $itemId));
			} else {
				$query = "update `tiki_tracker_items` set `lastModif`=? where `itemId`=?";
				$result = $this->query($query,array((int) $this->now,(int) $itemId));
			}
		// insert
		} else {
			if (!$status) {
				$status = $this->getOne("select `value` from `tiki_tracker_options` where `trackerId`=? and `name`=?",array((int) $trackerId,'newItemStatus'));
			}
			if (empty($status)) { $status = 'o'; }
			$query = "insert into `tiki_tracker_items`(`trackerId`,`created`,`lastModif`,`status`) values(?,?,?,?)";
			$result = $this->query($query,array((int) $trackerId,(int) $this->now,(int) $this->now,$status));
			$new_itemId = $this->getOne("select max(`itemId`) from `tiki_tracker_items` where `created`=? and `trackerId`=?",array((int) $this->now,(int) $trackerId));
		}
		$the_data = '';

		// --
		// inserts an empty item
		// if necessary
		$this->get_tracker_values_specs($trackerId, $explicit, $dsn);
		$tableId = $this->get_table_id($trackerId, $explicit);
		if ($itemId == false) {
			$query = "insert into $tableId(`itemId`)values(?)";
			$this->query($query, array((int)$new_itemId));
		}
		// --
		
		for ($i = 0; $i < count($ins_fields["data"]); $i++) {
			if (isset($ins_fields["data"][$i]["type"]) and $ins_fields["data"][$i]["type"] == 'e') {
				
			} elseif (isset($ins_fields["data"][$i]["fieldId"])) {
				$fieldId = $ins_fields["data"][$i]["fieldId"];
				if (isset($ins_fields["data"][$i]["name"])) {
					$name = $ins_fields["data"][$i]["name"];
				} else {
					$name = $this->getOne("select `name` from `tiki_tracker_fields` where `fieldId`=?",array((int)$fieldId));
				}
				if (isset($ins_fields["data"][$i]["value"]))	{
				  $value = $ins_fields["data"][$i]["value"];
				} else {
				  $value = '';
				}

				if (isset($ins_fields["data"][$i]["type"]) and $ins_fields["data"][$i]["type"] == 'q' and $itemId == false)
					$value = $this->getOne("select max(cast(field_$fieldId as UNSIGNED)) from $tableId") + 1;

				if (isset($ins_fields["data"][$i]["type"]) and ($ins_fields["data"][$i]["type"] == 'f' or $ins_fields["data"][$i]["type"] == 'j')) {
					$human_value = $this->date_format("%a, %e %b %Y %H:%M:%S %O",$ins_fields["data"][$i]["value"]);
					$the_data .= "  $name = $human_value\n";
				} else {
					$the_data .= "  $name = $value\n";
				}

				$colId = $this->get_col_id($fieldId, $explicit);
				$itId = $itemId ? $itemId : $new_itemId;
				$query = "update $tableId set $colId=? where `itemId`=?";
				//echo "UPDATE : $colId -> $value<br>\n";
				$this->query($query, array($value, $itId));

				$cachelib->invalidate(md5('trackerfield'.$fieldId.'o'));
				$cachelib->invalidate(md5('trackerfield'.$fieldId.'c'));
				$cachelib->invalidate(md5('trackerfield'.$fieldId.'p'));
				$cachelib->invalidate(md5('trackerfield'.$fieldId.'op'));
				$cachelib->invalidate(md5('trackerfield'.$fieldId.'oc'));
				$cachelib->invalidate(md5('trackerfield'.$fieldId.'pc'));
				$cachelib->invalidate(md5('trackerfield'.$fieldId.'opc'));
			}
		}
		//-------------------------------------------------------------------------

		$options = $this->get_tracker_options( $trackerId );

		include_once('lib/notifications/notificationlib.php');	

		$emails = $notificationlib->get_mail_events('tracker_modified', $trackerId);
		$emails2 = $notificationlib->get_mail_events('tracker_item_modified', $itemId, array('trackerId'=>$trackerId));

		if( array_key_exists( "outboundEmail", $options ) )
		{
			$emails3 = array( $options["outboundEmail"] );
		} else {
			$emails3 = array( );
		}

		$emails = array_merge($emails, $emails2, $emails3);

		if (!isset($_SERVER["SERVER_NAME"])) {
			$_SERVER["SERVER_NAME"] = $_SERVER["HTTP_HOST"];
		}

		if( array_key_exists( "simpleEmail", $options ) )
		{
			$simpleEmail = $options["simpleEmail"];
		} else {
			$simpleEmail = "n";
		}

		$trackerName = $this->getOne("select `name` from `tiki_trackers` where `trackerId`=?",array((int) $trackerId));

		if (count($emails) > 0) {
			if( $simpleEmail == "n" )
			{
				$smarty->assign('mail_date', $this->now);
				$smarty->assign('mail_user', $user);
				if ($itemId) {
					$mail_action = "\r\n".tra('Item Modification')."\r\n\r\n";
					$mail_action.= tra('Tracker').': '.$trackerName."\r\n";
					$mail_action.= tra('Item').': '.$itemId;
				} else {
					$mail_action = "\r\n".tra('Item creation')."\r\n\r\n";
					$mail_action.= tra('Tracker').': '.$trackerName;
				}
				$smarty->assign('mail_action', $mail_action);
				$smarty->assign('mail_data', $the_data);
				if ($itemId) {
					$smarty->assign('mail_itemId', $itemId);
				} else {
					$smarty->assign('mail_itemId', $new_itemId);
				}
				$smarty->assign('mail_trackerId', $trackerId);
				$smarty->assign('mail_trackerName', $trackerName);
				$foo = parse_url($_SERVER["REQUEST_URI"]);
				$machine = $this->httpPrefix(). $foo["path"];
				$smarty->assign('mail_machine', $machine);
				$parts = explode('/', $foo['path']);
				if (count($parts) > 1)
					unset ($parts[count($parts) - 1]);
				$smarty->assign('mail_machine_raw', $this->httpPrefix(). implode('/', $parts));


				$mail_data = $smarty->fetch('mail/tracker_changed_notification.tpl');
				$mail_subject = $smarty->fetch('mail/tracker_changed_notification_subject.tpl');

				include_once ('lib/mail/maillib.php');

				foreach ($emails as $email) {
					if ($email!='') {
						mail($email, encode_headers('['.$trackerName.'] '.$mail_subject, 'utf-8'), $mail_data, 'From: '.$prefs['sender_email']."\r\nContent-type: text/plain;charset=utf-8");
					}
				}
			} else {
			    // Use simple email

			    global $userlib;

			    $user_email = $userlib->get_user_email($user);
			    $my_sender = $user_email;
			    $smarty->assign('mail_data', $the_data);
			    $mail_subject = $smarty->fetch('mail/tracker_changed_notification_subject.tpl');
			    // Default subject
			    $subject='['.$trackerName.'] '.$mail_subject. $_SERVER["SERVER_NAME"];

			    // Try to find a Subject in $the_data
			    $subject_test = preg_match( '/^  Subject = .*$/m', $the_data, $matches );

			    if( $subject_test == 1 )
			    {
				$subject = preg_replace( '/^  Subject = /m', '', $matches[0] );
				// Remove the subject from $the_data
				$the_data = preg_replace( '/^  Subject = .*$/m', '', $the_data );
			    }

			    $the_data = preg_replace( '/^  [A-Za-z]+ = /m', '', $the_data );

			    //outbound email ->  will be sent in utf8 - from sender_email
			    include_once('lib/webmail/tikimaillib.php');
			    $mail = new TikiMail();
			    $mail->setSubject($subject);
			    $mail->setText($the_data);

			    if( ! empty( $my_sender ) )
			    {
				$mail->setHeader("From", $my_sender);
			    }

			    $mail->send( $emails );
			}
		}

		$cant_items = $this->getOne("select count(*) from `tiki_tracker_items` where `trackerId`=?",array((int) $trackerId));
		$query = "update `tiki_trackers` set `items`=?,`lastModif`=?  where `trackerId`=?";
		$result = $this->query($query,array((int)$cant_items,(int) $this->now,(int) $trackerId));

		if (!$itemId) $itemId = $new_itemId;

		if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
			require_once('lib/search/refresh-functions.php');
			refresh_index('tracker_items', $itemId);
		}

		return $itemId;
	}

	// ########## A TESTER ##########
	// ##############################
	function remove_tracker_item($itemId) {

		$trackerId = $this->getOne("select `trackerId` from `tiki_tracker_items` where `itemId`=?",array((int) $itemId));
		// --
		if(!$trackerId)
			return;
		// --
		$query = "update `tiki_trackers` set `lastModif`=? where `trackerId`=?";
		$result = $this->query($query,array((int) $this->now,(int) $trackerId));
		$query = "update `tiki_trackers` set `items`=`items`-1 where `trackerId`=?";
		$result = $this->query($query,array((int) $trackerId));
		
		$this->get_tracker_values_specs($trackerId, $explicit, $dsn);
		$tableId = $this->get_table_id($trackerId, $explicit);
		$query = "delete from $tableId where `itemId`=?";
		$this->query($query, array($itemId));
		
		$query = "delete from `tiki_tracker_items` where `itemId`=?";
		$result = $this->query($query,array((int) $itemId));
		$query = "delete from `tiki_tracker_item_comments` where `itemId`=?";
		$result = $this->query($query,array((int) $itemId));
	}

	// Inserts or updates a tracker
	// When inserting a new tracker,
	// trackerId must be set to false.
	// ########## OK ##########
	// ##############################
	function replace_tracker($trackerId, $name, $description, $options) {
		
		$explicit = $options['useExplicitNames'] == "y" ? true : false;
		$createNewTracker = $trackerId == false;
		
		if(!$explicit || $this->check_table_name($name, $trackerId == true)) {
		
			if($explicit && $trackerId) {
				$oldName = $this->get_table_id($trackerId, $explicit);
			}
			elseif(!$explicit) {
				// TODO code to change from (explicit/non explicit)
			}
		
			// -------------------
			if ($trackerId) {
				$query = "update `tiki_trackers` set `name`=?,`description`=?,`lastModif`=? where `trackerId`=?";
				$this->query($query,array($name,$description,(int)$this->now,(int) $trackerId));
			} else {
				$this->getOne("delete from `tiki_trackers` where `name`=?",array($name),false);
				$query = "insert into `tiki_trackers`(`name`,`description`,`created`,`lastModif`) values(?,?,?,?)";
				$this->query($query,array($name,$description,(int) $this->now,(int) $this->now));
				$trackerId = $this->getOne("select max(`trackerId`) from `tiki_trackers` where `name`=? and `created`=?",array($name,(int) $this->now));
			}
			$this->query("delete from `tiki_tracker_options` where `trackerId`=?",array((int)$trackerId));
			$rating = false;
			foreach ($options as $kopt=>$opt) {
				$this->query("insert into `tiki_tracker_options`(`trackerId`,`name`,`value`) values(?,?,?)",array((int)$trackerId,$kopt,$opt));
				if ($kopt == 'useRatings' and $opt == 'y') {
					$rating = true;
				} elseif ($kopt == 'ratingOptions') {
					$ratingoptions = $opt;
				} elseif ($kopt == 'showRatings') {
					$showratings = $opt;
				}
			}
			// -------------------
		
			// creation de la table des items
			// si elle n'existe pas deja.
			if($createNewTracker && $trackerId) {
				$dsn = null;
				$this->create_value_table($trackerId, $explicit, $dsn);
			}
			elseif($explicit) {
				$query = "alter table $oldName rename to ".$this->EXPLICIT_PREFIX.$name;
				$this->query($query);
			}
		
			// -------------------
			$ratingId = $this->get_field_id($trackerId,'Rating');
			if ($rating) {
				if (!$ratingId) $ratingId = 0;
				if (!isset($ratingoptions)) $ratingoptions = '';
				if (!isset($showratings)) $showratings = 'n';
				$this->replace_tracker_field($trackerId,$ratingId,'Rating','s','-','-',$showratings,'y','-','-',0,$ratingoptions);
			} else {
				$this->query('delete from `tiki_tracker_fields` where `fieldId`=?',array((int)$ratingId));
			}

			// -------------------
			global $prefs;
			if ( $prefs['feature_search'] == 'y' && $prefs['feature_search_fulltext'] != 'y' && $prefs['search_refresh_index_mode'] == 'normal' ) {
				require_once('lib/search/refresh-functions.php');
				refresh_index('trackers', $trackerId);
			}

			// -------------------
			return $trackerId;
			// -------------------
		}
		return false;
	}


	// ########## OK ##########
	// ##############################
	// if $fieldId is false, add a new field
	function replace_tracker_field($trackerId, $fieldId, $name, $type, $isMain, $isSearchable, $isTblVisible, $isPublic, $isHidden, $isMandatory, $position, $options) {
	
		if(!$trackerId)
			return;
			
		$this->get_tracker_values_specs($trackerId, $explicit, $dsn);
		if(!$explicit || $this->check_col_name($name, $fieldId == true, $trackerId)) {
		
			if ($fieldId) {
				if($explicit) {
					$oldName = $this->get_col_id($fieldId, $explicit);
					$tableName = $this->get_table_id($trackerId, $explicit);
					$sqlType = $this->get_sql_type($type);
					$query = "alter table $tableName change column $oldName $name $sqlType";
					$this->query($query);
				}
				$query = "update `tiki_tracker_fields` set `name`=? ,`type`=?,`isMain`=?,`isSearchable`=?,
					`isTblVisible`=?,`isPublic`=?,`isHidden`=?,`isMandatory`=?,`position`=?,`options`=? where `fieldId`=?";
				$bindvars=array($name,$type,$isMain,$isSearchable,$isTblVisible,$isPublic,$isHidden,$isMandatory,(int)$position,$options,(int) $fieldId);
	
				$result = $this->query($query, $bindvars);
			} else {
				$this->getOne("delete from `tiki_tracker_fields` where `trackerId`=? and `name`=?",
					array((int) $trackerId,$name),false);
				$query = "insert into `tiki_tracker_fields`(`trackerId`,`name`,`type`,`isMain`,`isSearchable`,`isTblVisible`,`isPublic`,`isHidden`,`isMandatory`,`position`,`options`)
			values(?,?,?,?,?,?,?,?,?,?,?)";
	
				$result = $this->query($query,array((int) $trackerId,$name,$type,$isMain,$isSearchable,$isTblVisible,$isPublic,$isHidden,$isMandatory,$position,$options));
				$fieldId = $this->getOne("select max(`fieldId`) from `tiki_tracker_fields` where `trackerId`=? and `name`=?",array((int) $trackerId,$name));
				
				$tableId = $this->get_table_id($trackerId, $explicit);
				$colId = $this->get_col_id($fieldId, $explicit);
				$sqlType = $this->get_sql_type($type);
				$query = "alter table $tableId add column $colId $sqlType";
				$this->query($query);
			}
			return $fieldId;
		}
		return false;
	}

	// ########## OK ##########
	// ##############################
	function replace_rating($trackerId,$itemId,$fieldId,$user,$new_rate) {
		
		if(!$trackerId || !$itemId || !$fieldId)
			return;
		
		$this->get_tracker_values_specs($trackerId, $explicit, $dsn);
		$colId = $this->get_col_id($fieldId, $explicit);
		$tableId = $this->get_table_id($trackerId, $explicit);
		
		$val = $this->getOne("select $colId from $tableId where `itemId`=?",
			array($itemId));
		
		$query = "update $tableId set $colId=? where `itemId`=?";
		
		if ($val === NULL) {			
			$newval = $new_rate;
		} else {			
			$olrate = $this->get_user_vote("tracker.$trackerId.$itemId",$user);
			if ($olrate === NULL) $olrate = 0;
			if ($new_rate === NULL) {
				$newval = $val - $olrate;
			} else {
				$newval = $val - $olrate + $new_rate;
			}
		}		
		$this->query($query,array((int)$newval,(int)$itemId));
		
		$this->register_user_vote($user, "tracker.$trackerId.$itemId", $new_rate);
		return $newval;
	}

	// ########## OK ##########
	// ##############################
	function remove_tracker($trackerId) {
	
		$this->get_tracker_values_specs($trackerId, $explicit, $dsn);
		$this->drop_value_table($trackerId, $explicit, $dsn);
	
		$bindvars=array((int) $trackerId);
		$query = "delete from `tiki_trackers` where `trackerId`=?";

		$result = $this->query($query,$bindvars);
		// Remove the fields
		$query = "delete from `tiki_tracker_fields` where `trackerId`=?";
		$result = $this->query($query,$bindvars);
		// Remove the items (Remove fields for each item for this tracker)
		$query = "select `itemId` from `tiki_tracker_items` where `trackerId`=?";
		$result = $this->query($query,$bindvars);

		while ($res = $result->fetchRow()) {
			$query2 = "delete from `tiki_tracker_item_comments` where `itemId`=?";
			$result2 = $this->query($query2,array((int) $res["itemId"]));
			$query2 = "delete from `tiki_tracker_item_attachments` where `itemId`=?";
			$result2 = $this->query($query2,array((int) $res["itemId"]));
		}

		$query = "delete from `tiki_tracker_items` where `trackerId`=?";
		$result = $this->query($query,$bindvars);
		
		$query = "delete from `tiki_tracker_options` where `trackerId`=?";
		$result = $this->query($query,$bindvars);
		
		$this->remove_object('tracker', $trackerId);
		
		return true;
	}

	// ########## OK ##########
	// ##############################
	function remove_tracker_field($fieldId,$trackerId) {
	
		if(!$trackerId || !$fieldId)
			return;
			
		$this->get_tracker_values_specs($trackerId, $explicit, $dsn);
		$tableId = $this->get_table_id($trackerId, $explicit);
		$colId = $this->get_col_id($fieldId, $explicit);
		$query = "alter table $tableId drop column $colId";
		$this->query($query);
			
		global $cachelib;
		$query = "delete from `tiki_tracker_fields` where `fieldId`=?";
		$bindvars=array((int) $fieldId);
		$result = $this->query($query,$bindvars);
		
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'o'));
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'p'));
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'c'));
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'op'));
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'oc'));
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'pc'));
		$cachelib->invalidate(md5('trackerfield'.$fieldId.'opc'));
		return true;
	}
}
?>
