<?php

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"],basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}
include_once ('magic-enumerations.php');
include_once('lib/tikilib.php');
include_once ('lib/categories/categlib.php');
include_once ('lib/userslib.php');
include_once ("lib/commentslib.php");
include_once ("lib/logs/logslib.php");

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
				while( count($row) < 12)
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
					(string) $row[11]
				);

				$query = "INSERT INTO tiki_feature (`feature_id`, `feature_name`, `parent_id`, `status`, `setting_name`, `feature_type`, `template`, `permission`, `ordinal`, `depends_on`, `keyword`, `tip`) VALUES(" . rtrim(str_repeat(' ?,', count($row)), ',') . ")";
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
		global $prefs, $enumerations, $tikilib, $categlib, $userlib, $dbTiki;
		
		if ($feature == null) return null;

		if ($feature['setting_name'] != '' && in_array($feature['setting_name'], $prefs)) {
			$feature['value'] = $prefs[$feature['setting_name']];
			// Slightly odd special case.  The value needs to be stored in 'language'; however the language
			// preference for 'language' contains the user language.
			if ($feature['setting_name'] == 'language') {
				$feature['value'] = $prefs['site_language'];
			}
		}
		// Dear developers,
		// Don't fuckup and make circular dependencies.
		if ($feature['depends_on'] != 0) {
			$feature['depends_on'] = $this->get_feature($feature['depends_on']);
		}

		if ($feature['feature_type'] == 'grouplist') {
			$groups = array();
			$groups[] = "";
			$feature['enumeration'] = array_merge($groups, $userlib->list_all_groups());
		}
		if ($feature['feature_type'] == 'limitcategory' || $feature['feature_type'] == 'selectcategory') {
			$feature['enumeration'] = $categlib->get_all_categories();
		}
		if ($feature['feature_type'] == 'languages' || $feature['feature_type'] == 'availablelanguages' ) {
			$feature['enumeration'] = array();
			foreach($tikilib->list_languages(false,null,true) as $language) {
				$feature['enumeration'][$language['value']] = $language['name'];
			}
		}
		if ($feature['feature_type'] == 'forumselection') {
			$commentslib = new Comments($dbTiki);
			$all_forums = $commentslib->list_forums(0, -1, 'name_asc', '');
			if (count($all_forums['data']) == 0) {
				$empty = array();
				$empty[''] = tra('No forums');
				$feature['enumeration'] = $empty;
			} else {
				$feature['enumeration'] = $all_forums["data"];
			}
		}
		if ($feature['feature_type'] == 'timezone') {
			$feature['enumeration'] = TikiDate::getTimeZoneList();
		}
		if ($feature['feature_type'] == 'sitestyle') $enumerations['sitestyle'] = $tikilib->list_styles(); 
		if ($feature['feature_type'] == 'slideshowstyle') $enumerations['slideshowstyle'] = $this->get_slideshowstyles();

		if (array_key_exists($feature['feature_type'], $enumerations)) {
			$feature['enumeration'] = $enumerations[$feature['feature_type']];
		}
		
		// If there are a couple of other things which should allow mutliple select, add 'em here.
		// If there are lots, add it to the tiki_features data table.
		if ($feature['feature_type'] == 'availablelanguages' ) {
			$feature['multiple'] = 'on';
		}

		if ($feature['template'] !== '' && strpos($feature['template'], '.php') === false) {
			$feature['smartytemplate'] = $feature['template'] . '.tpl';
			$feature['featureinclude'] = $feature['template'] . '.php';
			$feature['pageurl'] = $feature['template'] . '.php';
		} else if ($feature['template'] !== '' && strpos($feature['template'], '.php') > 1) {
			$feature['pageurl'] = $feature['template'];
		} else {
			$feature['pageurl'] = 'tiki-magic.php?featurechain=' . $feature['feature_path'];
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
		return !is_container($feature) && !is_functionality($feature);
	}
	
	function is_functionality($feature) {
		return ($feature['feature_type'] == 'functionality' || $feature['feature_type'] == 'system' || $feature['feature_type'] == 'feature' || $feature['feature_type'] == 'subfeature');
	}
	
	// These are helper functions, pretty much as-ganked from tiki-admin.php

	function simple_set_toggle($feature) {
		global $_POST, $tikilib, $smarty, $tikifeedback, $prefs, $logslib;
		if ($feature != '') {	
			$setting = $feature;
			if (isset($_POST[$setting]) && $_POST[$setting] == "on") {
				if ((!isset($prefs[$setting]) || $prefs[$setting] != 'y')) {
					// not yet set at all or not set to y
					$tikilib->set_preference($setting, 'y');
					$prefs[$setting] = 'y';
					$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("%s enabled"),$feature));
					$logslib->add_log('Configuration', tra("$feature is turned on"));
				}
			} else {
				if ((!isset($prefs[$setting]) || $prefs[$setting] != 'n')) {
					// not yet set at all or not set to n
					$tikilib->set_preference($feature, 'n');
					$tikifeedback[] = array('num'=>1,'mes'=>sprintf(tra("%s disabled"),$feature));
					$logslib->add_log('Configuration', tra("$feature is turned off"));
				}
			}
		}
	}
	
	function simple_set_value($feature, $pref = '', $isMultiple = false) {
		global $_POST, $tikilib, $prefs, $logslib;
		if (is_array($_POST[$feature])) {
			$featureStr = implode($_POST[$feature], ',');	
		} else {
			$featureStr = $_POST[$feature];
		}
		if ($feature != '') {
			if (isset($_POST[$feature])) {
				if ( $pref != '' ) {
					$tikilib->set_preference($pref, $_POST[$feature]);
					$prefs[$feature] = $_POST[$feature];
					$logslib->add_log('Configuration', tra("$feature set to " . $featureStr));
				} else {
					$tikilib->set_preference($feature, $_POST[$feature]);
					$logslib->add_log('Configuration', tra("$feature set to " . $featureStr));
				}
			} else if( $isMultiple ) {
				// Multiple selection controls do not exist if no item is selected.
				// We still want the value to be updated.
				if ( $pref != '' ) {
					$tikilib->set_preference($pref, array());
					$prefs[$feature] = $_POST[$feature];
					$logslib->add_log('Configuration', tra("$feature set to nothing"));
				} else {
					$tikilib->set_preference($feature, array());
					$logslib->add_log('Configuration', tra("$feature set to nothing"));
				}
			}
		}
	}
	
	function simple_set_int($feature) {
		global $_POST, $tikilib;
		if (isset($_POST[$feature]) && is_numeric($_POST[$feature])) {
			// This will cause logging / popups / any additional post processing to occur.
			$this->simple_set_value($feature, $_POST[$feature]);
		}
	}
	
	function byref_set_value($feature, $pref = "") {
		global $_POST, $tikilib;
		$this->simple_set_value($feature, $pref);
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
