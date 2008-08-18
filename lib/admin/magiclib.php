<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

class MagicLib extends TikiLib {
	function MagicLib($db) {
		$this->TikiLib($db);

		global $prefs;
		$lastLoad = $prefs['magic_last_load'];
		$lastMod = filemtime( 'db/features.csv' );

		if( $lastMod > $lastLoad ) {
			$this->reload_features();
			$this->set_preference( 'magic_last_load', $lastMod );
		}
	}

	function reload_features() {
		$this->query( "DELETE FROM tiki_feature" );
		
		$fp = fopen( 'db/features.csv', 'r' );
		fgetcsv( $fp, 1024, ',', '"' );
		while( false !== $row = fgetcsv( $fp, 1024, ',', '"' ) ) {
			// Check for empty line should not happend but...
			if ($row[0] !== null) {
				while( count($row) < 11 )
					$row[] = '';
				if( count($row) != 11 )
					$row = array_slice( $row, 0, 11 );				
				$query = "INSERT INTO tiki_feature (`feature_id`, `feature_name`, `parent_id`, `status`, `setting_name`, `feature_type`, `template`, `permission`, `ordinal`, `depends_on`, `keyword`) VALUES(" . rtrim(str_repeat(' ?,', count($row)), ',') . ")";
				$this->query( $query, $row);
			}
		}
		fclose( $fp );

		$this->recursive_update( 0 );
	}

	function recursive_update( $featureid, $path = null ) {
		$features = $this->get_child_features($featureid);
		if ($features) {
			foreach($features as $feature) {
				if ($feature['feature_id'] > 0) {
					$this->update_feature_specials($feature, $path . '/' . $feature['feature_id']);
					$this->recursive_update($feature['feature_id'], $path . '/' . $feature['feature_id']);
				}
			}
		}
	}
	
	function get_child_features($parentid, $featurefilter='') {
		global $prefs;
		$bindvars = array();
		$bindvars[] = $parentid;
		$filter = '';
		if ($featurefilter != '') {
			if ($featurefilter == 'settings') {
				$filter = "and `feature_type` in ('simple', 'byref','int','flag')";
			} else if ($featurefilter == 'containers') {
				$filter = "and `feature_type` in ('container', 'feature','configurationgroup','system')";			
			} else {
				$filter = "and `feature_type`=?";
				$bindvars[] = $featurefilter;
			}
		}
		$query = "select * from `tiki_feature` where (`parent_id`=?) $filter order by `ordinal`";

		$result = $this->query($query, $bindvars);
		$allFeatures = array();
		while ($row = $result->fetchRow()) {
			$allFeatures[] = $this->_feature_post_processing($row);
		}
		return $allFeatures;
	}

	function get_feature($featureid) {
		global $prefs;
		$bindvars = array();
		$bindvars[] = $featureid;
		$query = "select * from `tiki_feature` where `feature_id`=?";

		$result = $this->query($query, $bindvars);
		$row = $result->fetchRow();

		return $this->_feature_post_processing($row);
	}
	
	function _feature_post_processing($feature) {
		global $prefs;
		if ($feature['setting_name'] != '' && in_array($feature['setting_name'], $prefs)) {
			$feature['value'] = $prefs[$feature['setting_name']];
		}
		// Dear developers,
		// Don't fuckup and make circular dependencies.
		if ($feature['depends_on'] != 0) {
			$feature['depends_on'] = $this->get_feature($feature['depends_on']);
		}
		
		return $feature;
	}
	
	function get_feature_by_template($templateName) {
		$bindvars = array();
		$bindvars[] = $templateName;

		$query = "select * from `tiki_feature` where `template`=? ";

		$result = $this->query($query, $bindvars);
		// Get the first match.
		return $this->_feature_post_processing($result->fetchRow());	
	}
	
	function update_feature_specials($feature, $path) {
		$bindvars = array();
		
		$query = 'select count(feature_id) from `tiki_feature` where parent_id=?';
		$count = $this->getOne($query, $feature['feature_id']);
		if ($count == "") $count = 0;
		
		$bindvars[] = $path;
		$bindvars[] = $count;
		$bindvars[] = $feature['feature_id'];
		
		$query = 'update `tiki_feature` set `feature_path`=?, `feature_count`=? where `feature_id`=?';
		$this->query($query, $bindvars);
	}
	
	function is_container($feature) {
		return ($feature['feature_type'] == 'container' || $feature['feature_type'] == 'configurationgroup') || $feature['feature_type'] == 'system';
	}

	function is_setting($feature) {
		return ($feature['feature_type'] == 'simple' || $feature['feature_type'] == 'byref' || $feature['feature_type'] == 'int' || $feature['feature_type'] == 'flag');
	}
	
	function is_functionality($feature) {
		return ($feature['feature_type'] == 'functionality' || $feature['feature_type'] == 'system' || $feature['feature_type'] == 'feature');
	}
}
global $dbTiki;
$magiclib = new MagicLib($dbTiki);

?>
