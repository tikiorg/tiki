<?php

class LayerLib extends TikiLib {

	function LayerLib($db,$db2) {
		$this->TikiLib($db);
		$this->db2= $db2;
	}

	function write_class_point($data) {
		$symbol= "square";
		$ret="CLASS\n    Name \"".$data["table"]."\"\n    STYLE\n      COLOR 0 0 0 \n      OUTLINECOLOR 27 113 201\n      SYMBOL ".$symbol."\n    END\n    LABEL\n      TYPE TRUETYPE\n      FONT \"Vera\" \n      SIZE 6 \n      COLOR 0 0 0 \n      BACKGROUNDCOLOR 245 235 235\n      BACKGROUNDSHADOWCOLOR 55 55 55 \n      BACKGROUNDSHADOWSIZE 1 1 \n      WRAP \" \" \n    END \nEND \n ";	
		return $ret;
	}
	
	function write_class_line($data) {
	
	}
	
	function write_class_polygon($data) {
	
	}
	function replace_map($mapId,$data) {
		global $map_path;
		global $tikidomain;
		global $smarty;
		global $feature_userPreferences;
		global $mapfile;
		$query="UPDATE `tiki_maps` SET `name`='".$data['name']."',`author`='".$data['author']."',`type`='".$data['type']."',`path`='".$data['path']."',`copyright`='".$data['copyright']."',`copyrightUrl`='".$data['copyrightUrl']."',`gateway`='".$data['gateway']."',`db`='".$data['db']."',`description`='".$data['description']."' WHERE mapId='".$data['mapId']."' ";
		$result = $this->query($query);

	}
		
	function replace_layergroup($layerId,$data) {
		global $map_path;
		global $tikidomain;
		global $smarty;
		global $feature_userPreferences;
		global $mapfile;
		$query="UPDATE `tiki_map_layer` SET `name`='".$data['name']."',`author`='".$data['author']."',`islayerGroup`='".$data['islayerGroup']."', `layerGroupId`='".$data['layerGroupId']."', `layerRendering`='".$data['layerRendering']."', `layerAggregate`='".$data['layerAggregate']."',`type`='NULL',`config`='NULL',`copyright`='".$data['copyright']."',`copyrightUrl`='".$data['copyrightUrl']."',`gateway`='".$data['gateway']."',`db`='NULL',`description`='".$data['description']."' WHERE layerId='".$data['layerId']."' ";
		$result = $this->query($query);


	}
	
	function replace_layer($layerId,$data) {
		global $map_path;
		global $tikidomain;
		global $smarty;
		global $feature_userPreferences;
		global $mapfile;
		$query="UPDATE `tiki_map_layer` SET `name`='".$data['name']."',`author`='".$data['author']."',`layerGroupId`='".$data['layerGroupId']."',`type`='".$data['type']."',`config`='".$data['config']."',`copyright`='".$data['copyright']."',`copyrightUrl`='".$data['copyrightUrl']."',`gateway`='".$data['gateway']."',`db`='".$data['db']."',`description`='".$data['description']."' WHERE layerId='".$data['layerId']."' ";
		$result = $this->query($query);


	}
	
	function add_layergroup($data) {
		// create a layergroup in tiki_map_layer
		global $map_path;
		global $tikidomain;
		global $smarty;
		global $feature_userPreferences;
		global $mapfile;
		$query="INSERT INTO `tiki_map_layer` (`layerId`,`name`,`mapId`,`author`,`islayerGroup`,`LayerGroupId`,`layerRendering`,`layerAggregate`,`type`,`config`,`copyright`,`copyrightUrl`,`gateway`,`db`,`table`,`description`) VALUES ('','".$data['name']."','".$data['mapId']."','".$data['author']."','".$data['islayerGroup']."','".$data['LayerGroupId']."','".$data['layerRendering']."','".$data['layerAggregate']."','".$data['type']."','".$data['config']."','".$data['copyright']."','".$data['copyrightUrl']."','".$data['gateway']."','".$data['db']."','".$data['table']."','".$data['description']."')";
		$result = $this->query($query);
		$query="SELECT layerId from `tiki_map_layer`WHERE name='".$data['name']."' order by layerId desc "; 
		$layerId=$this->getOne($query);
		return $layerId;
	}
	function add_layer($data) {
		// create a layer in tiki_map_layer
		global $map_path;
		global $tikidomain;
		global $smarty;
		global $feature_userPreferences;
		global $mapfile;
		$query="INSERT INTO `tiki_map_layer` (`layerId`,`name`,`mapId`,`author`,`type`,`config`,`copyright`,`copyrightUrl`,`gateway`,`db`,`table`,`description`) VALUES ('','".$data['name']."','".$data['mapId']."','".$data['author']."','".$data['type']."','".$data['config']."','".$data['copyright']."','".$data['copyrightUrl']."','".$data['gateway']."','".$data['db']."','".$data['table']."','".$data['description']."')";
		$result = $this->query($query);
		$query="SELECT layerId from `tiki_map_layer`WHERE name='".$data['name']."' order by layerId desc "; 
		$layerId=$this->getOne($query);
		return $layerId;
	}
	
	function add_map($data) {
		// create a layer in tiki_map_layer
		global $map_path;
		global $tikidomain;
		global $smarty;
		global $feature_userPreferences;
		global $mapfile;
		$query="INSERT INTO `tiki_maps` (`mapId`,`name`,`projectName`,`author`,`type`,`path`,`copyright`,`copyrightUrl`,`gateway`,`db`,`description`) VALUES ('','".$data['name']."','".$data['projectName']."','".$data['author']."','".$data['type']."','".$data['path']."','".$data['copyright']."','".$data['copyrightUrl']."','".$data['gateway']."','".$data['db']."','".$data['description']."')";
		$result = $this->query($query);
		$query="SELECT mapId from `tiki_maps`WHERE name='".$data['name']."' order by mapId desc "; 
		$mapId=$this->getOne($query);
		return $mapId;
	}
	
	function createtable($data) {
		// create table in db2 if a new layer is created 
		
	}
	
	function droptable($table) {
		// drop table in db2 if layer is deleted 
		
	}
	function checkdb2($table) {
		//check if the layer present in tiki_map_mayer has a table into the db2 
		
	}
	
	function cartowebInit() {
		// reintialize cartoweb for project=$Map
		
	}	
	function get_all_layers_ext($mapId) {
		global $cachelib;
		//if (!$cachelib->isCached("alllayers")) {
			$ret = array();
			$query = "select * from `tiki_map_layer` where `mapId`= ?  order by `name`";
			$bindvars=array($mapId);
			$result = $this->query($query,$bindvars);
			while ($res = $result->fetchRow()) {
				$id = $res["layerId"];
				$query = "select count(*) from `tiki_map_layer` where `layerGroupId`=? and `islayerGroup` =1";
				$res["layergroup"] = $this->getOne($query,array($id));
				$query = "select count(*) from `tiki_map_layer` where `layerGroupId`=? and `islayerGroup` =0";
				$res["layers"] = $this->getOne($query,array($id));
				$ret[] = $res;
			}
			$cachelib->cacheItem("alllayers",serialize($ret));
		//} else {
		//	$ret = unserialize($cachelib->getCached("alllayers"));
		//}
		return $ret;
		
	}
	function get_layers_respect_perms($mapId,$layerId= -1, $user, $perm) {
		global $cachelib;
		global $userlib;
		global $tiki_p_admin;
		
//		if (!$cachelib->isCached("categlayer")) {
			// get map categId 
			$mapcategId=$this->getOne("select * from `tiki_categorized_objects` where `objId`= ?  and `type`='map'",array($mapId));
			
			// get layersgroup 
			$layersgroup=$this->list_layerGroups($mapId,$layerId);
			$ret = array();                        
			if ($layerId !=-1) {
				$mid="`mapId`= ? and `layerGroupId`= ?";
			} else {
				$mid="`mapId`= ?";
			}
			$bindvars=array($mapId);
			$query = "select * from `tiki_map_layer` where $mid";
			$result = $this->query($query,$bindvars);
		
			// get sublayers



			// get leafs



			// 
			
			
			
			// get layers categId 
			// create layer tree array 
			// check rights 
			
			// return array 
			
			$ret = array();
			return $ret;
	}
	
	
	function get_layer_by_group($mapId, $layerId, $offset=0, $maxRecords=-1, $sort_mode='layerGroupId_asc', $find) {
		global $tikidomain;
		global $smarty;
		global $user;
		global $feature_phplayers;
		$layergroups=$this->list_layerGroups($mapId, $layerId, $offset, $maxRecords);
	
		for ($i=0; $i<$layergroups["cant"]; $i++) {
			if($this->layer_has_child($layergroups["data"][$i]["layerId"]) != 0) {
				$layergroups["data"][$i]["has_child"]= "y";
				$child_data=$this->list_layerGroups($mapId, $layergroups["data"][$i]["layerId"], $offset, $maxRecords);
				$layergroups["data"][$i]["child_data"]= $child_data;
			} else {
			}
		
		}
		$categId=3;	
		$ctall= $this->get_categories_respect_perms($categId, $user, 'tiki_p_view_categories');
			
		return $ctall;
	}
	
	function get_layergroup($layerId) {
		$query = "select * from `tiki_map_layer` where `layerId`=?";

		$result = $this->query($query,array($layerId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}
	
	function get_layer($layerId) {
		$query = "select * from `tiki_map_layer` where `layerId`=?";

		$result = $this->query($query,array($layerId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}
	
	function get_map($mapId) {
		$query = "select * from `tiki_maps` where `mapId`=?";

		$result = $this->query($query,array($mapId));

		if (!$result->numRows())
			return false;

		$res = $result->fetchRow();
		return $res;
	}
	// functions used for getmapicon 
	function mystrpos($str, $needle) {
  		$len=strlen($str);
  		for ($i=0; $i<$len; $i++) {
    			$v=$str[$i];
    			if (strpos($needle, $v) !== FALSE) return $i;
  		}
  		return FALSE;
	}

	function gettag(&$content) {
  		$content=ltrim($content);
  		$pos=$this->mystrpos($content, " \n\t\r");
  		if ($pos === FALSE) return FALSE;
  		$tag=substr($content, 0, $pos);
  		$content=substr($content, $pos);
  		return $tag;
		}

	function readoneparam(&$content) {
  		$remove=0;
  		$content=ltrim($content);
  		if ($content[0]=="'") {
    			$needle="'";
    			$remove=1;
 		} else if ($content[0]=="\"") {
    			$needle="\"";
    			$remove=1;
  		} else $needle=" \n\t\r";
  			if ($remove) $content=substr($content, $remove);
			$pos=$this->mystrpos($content, $needle);
  			if ($pos === FALSE) return FALSE;
  			$value=substr($content, 0, $pos);
  			$content=substr($content, $pos + $remove);
			return $value;
		}

	function readnumberlist(&$content) {
  		$iconsymbol=array();
  		do {
    			$content=ltrim($content);
    			$pos=$this->mystrpos($content, " \n\t\r");
    			if ($pos === FALSE) return FALSE;
    			$value=substr($content, 0, $pos);
    			$content=substr($content, $pos);
			if ($value != "END") $iconsymbol[]=$value;
  		} while ($value != "END");
		return $iconsymbol;
	}

	function nexttag(&$content) {
  		$iconsymbol=array();
		do {
    			$content=ltrim($content);
    			$tag=$this->gettag(&$content);
			if ($tag === FALSE) return $iconsymbol;
    			$tag=strtoupper($tag);
			switch($tag) {
				case "SYMBOL":
      					if (isset($curobj)) {
					echo "####### ERROR: opening 'SYMBOL' while it was already opened ########";
					return FALSE;
      					}
      					$curobj=array();
      				break;

    				case "END":
      					if (!isset($curobj)) {
					echo "####### ERROR: no tag 'SYMBOL' opened before '$tag' ########";
					return FALSE;
      					}
      					$iconsymbol[]=$curobj;
      					unset($curobj);
      				break;

      				// tags ayant 1 parametre
    				case "NAME":
    				case "TYPE":
    				case "FILLED":
    				case "IMAGE":
    				case "TRANSPARENT":
    				case "FONT":
    				case "CHARACTER":
    				case "ANTIALIAS":
    				case "GAP":
      					if (!isset($curobj)) {
					echo "####### ERROR: no tag 'SYMBOL' opened before '$tag' ########";
					return FALSE;
      					}
      					$newobj=array();
      					$newobj["tag"]=$tag;
      					$newobj["value"]=$this->readoneparam(&$content);
      					$curobj[]=$newobj;
      				break;

      				// tags ayant plusieurs parametre, terminé par END
    				case "POINTS":
    				case "STYLE":
      					if (!isset($curobj)) {
					echo "####### ERROR: no tag 'SYMBOL' opened before '$tag' ########";
					return FALSE;
      					}
      					$newobj=array();
      					$newobj["tag"]=$tag;
      					$newobj["value"]=$this->readnumberlist(&$content);
      					$curobj[]=$newobj;
      				break;

    				default: 
      				echo "####### ERROR: tag '$tag' unknow ! ########";
      				return FALSE;
    			}
  		} while (true);
}
	function getmapicon($filepath) {
		// load map icons from the symbol.txt file 
		$icon=array();
		$content=file_get_contents($filepath);
		$iconsymbol=$this->nexttag($content);
		// look for all images and names linked  with type PIXMAP 
			foreach($iconsymbol as $symbol) {
  				unset($name);
  				unset($image);
  				foreach($symbol as $element) {
    					if ($element["tag"] == "NAME") {
      						$name=$element["value"];
    					} else if ($element["tag"] == "IMAGE") {
      						$image=$element["value"];
					}
  				}
				if (isset($name) && isset($image)) {
					$imagename= substr($image,24);
					$imagenameonly= split("[.]",$imagename);
					$imagepath="generated/icons/Sigfreed/World/".$imagenameonly[0]."_class_0.png";
					$icon['line']=array($name,$imagepath);	
					array_push($icon, $icon['line']);
				}
			}
		return $icon;
	}
	function getlayer($layerId) {
		global $map_path;
		global $tikidomain;
		global $smarty;
		global $feature_userPreferences;
		global $mapfile;
		$query="SELECT * from tiki_map_layer where layerId=".$layerId;
		$result = $this->query($query);
		$ret = array();
		while ($res = $result->fetchRow()) {
		      $ret[] = $res;
		}
		return $ret;

	}
	

function layer_has_child($layerId=-1) {
	$query="SELECT count(*) from `tiki_map_layer` where `layerGroupId`= ?";
	$count= $this->getOne($query,array($layerId));
	return $count;
}
	function list_layerGroups( $mapId,  $layerId = 0, $offset = 0, $maxRecords = -1, $sort_mode = 'LayerGroupId_asc', $find = '') {
		if ($find) {
			$findesc = '%' . $find . '%';
			$bindvars[]=$findesc;

			if ($mid) {
				$mid .= " and `name` like ? ";
			} else {
				$mid .= "where `mapId`= ? and  `islayerGroup`=1 and  `name` like ? ";
			}
		} else {
			if($layerId == -1) {
				$mid = "where `mapId`= ? and `islayerGroup`=1";	
				$bindvars=array((int)$mapId);
			} else {
				$mid = "where `mapId`= ? and `islayerGroup`=1 and `layerGroupId`=?";	
				$bindvars=array((int)$mapId, (int)$layerId);
			}
		}
		
	
	
		$query = "select * from `tiki_map_layer` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_map_layer` $mid";
		$result = $this->query($query,array($bindvars),$maxRecords,$offset);
		$cant = $this->getOne($query_cant,array($bindvars));
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[]=$res;
		}
		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}
	
	function list_layers($offset = 0, $maxRecords = -1, $sort_mode = 'name_desc', $find = '', $mapId = 0, $layerId = 0) {
		if ($mapId == 0) {
			$mid = '';
			$bindvars=array();
		} elseif ($mapId) {
			global $user;
			$mid = "where `mapId`= ?";
			$bindvars=array($mapId);
		}
	

		if ($find) {
			$findesc = '%' . $find . '%';
			$bindvars[]=$findesc;

			if ($mid) {
				$mid .= " and `name` like ? ";
			} else {
				$mid .= " where `name` like ? ";
			}
		}
	
		if($layerId && $layerId !=0) {
			if ($mid) {
			$mid .= " and `layerId` like ".$layerId;
			} else {
			$mid .= " where `layerId`= ".$layerId;
			}
		}
		
		$query = "select * from `tiki_map_layer` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_map_layer` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}


	function list_maps($offset = 0, $maxRecords = -1, $sort_mode = 'created_desc', $find = '', $user) {
		if ($user == 'admin') {
			$mid = '';
			$bindvars=array();
		} else {
			$mid = "where `mapId` = ?";
			$bindvars=array($user);
		}


		if ($find) {
			$findesc = '%' . $find . '%';
			$bindvars[]=$findesc;

			if ($mid) {
				$mid .= " and `url` like ? ";
			} else {
				$mid .= " where `url` like ? ";
			}
		}

		$query = "select * from `tiki_maps` $mid order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_maps` $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function listlayers($offset, $maxRecords, $sort_mode, $find, $mapId) {
		// list layers where Worldmap=$Worldmap
		global $map_path;
		global $tikidomain;
		global $smarty;
		global $feature_userPreferences;
		global $mapfile;
		$bindvars=array();
		if ($find) {
		   	$findesc = '%' . $find . '%';
			$mid = " and where (`name` like ? or `description` like ?)";
			$bindvars[]=$findesc;
			$bindvars[]=$findesc;
		} else {
			$mid = "";
		}
		$query="SELECT * from tiki_map_layer where mapId=".$mapId." ".$mid." order by ".$this->convert_sortmode($sort_mode);
		$query_cant = "select count(*) from `tiki_map_layer` where mapId=".$mapId." $mid";
		$result = $this->query($query,$bindvars,$maxRecords,$offset);
		$cant = $this->getOne($query_cant,$bindvars);
		$ret = array();
		while ($res = $result->fetchRow()) {
		      $ret[] = $res;
		}
		return $ret;

	}
}
global $dbTiki;
global $dbTiki2;

global $layerlib;
$layerlib = new LayerLib($dbTiki,$dbTiki2);
?>
