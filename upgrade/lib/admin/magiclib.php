<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
include_once ('magic-enumerations.php');
include_once('tikilib.php');
include_once ('lib/categories/categlib.php');

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

				$row = array(
					(int) $row[0],
					(string) $row[1],
					(int) $row[2],
					(string) $row[3],
					(string) $row[4],
					(string) $row[5],
					(string) $row[6],
					(string) $row[7],
					(int) $row[8],
					(int) $row[9],
					(string) $row[10],
				);

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
		global $prefs, $enumerations, $tikilib, $categlib;
		if ($feature['setting_name'] != '' && in_array($feature['setting_name'], $prefs)) {
			$feature['value'] = $prefs[$feature['setting_name']];
		}
		// Dear developers,
		// Don't fuckup and make circular dependencies.
		if ($feature['depends_on'] != 0) {
			$feature['depends_on'] = $this->get_feature($feature['depends_on']);
		}

		if ($feature['feature_type'] == 'limitcategory' || $feature['feature_type'] == 'selectcategory') {
			$catree = $categlib->get_all_categories();
			$feature['enumeration'] = $catree;
		}
		if ($feature['feature_type'] == 'languages') {
			$languages = array();
			$languages = $tikilib->list_languages(false,null,true);
			$feature['enumeration'] = $languages;
		}
		if ($feature['feature_type'] == 'timezone') {
			$feature['enumeration'] = TikiDate::getTimeZoneList();
		}
		if ($feature['feature_type'] == 'sitestyle') $enumerations['sitestyle'] = $tikilib->list_styles(); 
		if ($feature['feature_type'] == 'slideshowstyle') $enumerations['slideshowstyle'] = $this->get_slideshowstyles();

		if (array_key_exists($feature['feature_type'], $enumerations)) {
			$feature['enumeration'] = $enumerations[$feature['feature_type']];
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
	
	// These are helper functions, pretty much as-ganked from tiki-admin.php

	function simple_set_toggle($feature) {
		global $_POST, $tikilib, $smarty, $tikifeedback, $prefs;
		$setting = $feature;
		if (isset($_POST[$setting]) && $_POST[$setting] == "on") {
			if ((!isset($prefs[$setting]) || $prefs[$setting] != 'y')) {
				// not yet set at all or not set to y
				$tikilib->set_preference($setting, 'y');
				$prefs[$setting] = 'y';
				$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("%s enabled"),$feature));
			}
		} else {
			if ((!isset($prefs[$setting]) || $prefs[$setting] != 'n')) {
				// not yet set at all or not set to n
				$tikilib->set_preference($feature, 'n');
				$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("%s disabled"),$feature));
			}
		}
	}
	
	function simple_set_value($feature, $pref = '', $isMultiple = false) {
		global $_POST, $tikilib, $prefs;
		
		if (isset($_POST[$feature])) {
			if ( $pref != '' ) {
				$tikilib->set_preference($pref, $_POST[$feature]);
				$prefs[$feature] = $_POST[$feature];
			} else {
				$tikilib->set_preference($feature, $_POST[$feature]);
			}
		} else if( $isMultiple ) {
			// Multiple selection controls do not exist if no item is selected.
			// We still want the value to be updated.
			if ( $pref != '' ) {
				$tikilib->set_preference($pref, array());
				$prefs[$feature] = $_POST[$feature];
			} else {
				$tikilib->set_preference($feature, array());
			}
		}
	}
	
	function simple_set_int($feature) {
		global $_POST, $tikilib;
		if (isset($_POST[$feature]) && is_numeric($_POST[$feature])) {
			$tikilib->set_preference($feature, $_POST[$feature]);
		}
	}
	
	function byref_set_value($feature, $pref = "") {
		global $_POST, $tikilib;
		simple_set_value($feature, $pref);
	}
	
	function get_slideshowstyles() {
		$slide_styles = array();
		$h = opendir("styles/slideshows");
		while ($file = readdir($h)) {
			if (strstr($file, "css")) {
				$slide_styles[] = $file;
			}
		}
		closedir ($h);
				
		return $slide_styles;
	}
}
global $dbTiki;
$magiclib = new MagicLib($dbTiki);

?>
