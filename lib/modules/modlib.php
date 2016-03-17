<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

//this script may only be included - so its better to die if called directly.
if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
  header("location: index.php");
  exit;
}

/**
 *
 */
class ModLib extends TikiLib
{

	public $pref_errors = array();

	// additional module zones added to this array will be exposed to tiki.tpl
	// TODO change modules user interface to enable additional zones
	public $module_zones = array(
		'top' => 'top_modules',
		'topbar' => 'topbar_modules',
		'pagetop' => 'pagetop_modules',
		'left' => 'left_modules',
		'right' => 'right_modules',
		'pagebottom' => 'pagebottom_modules',
		'bottom' => 'bottom_modules',
	);

	public $cssfiles  = array(
		'calendar_new'	=> array (
			'csspath'	=> 'themes/base_files/feature_css/calendar.css',
			'rank'		=> 20,
		),
		'action_calendar'	=> array (
			'csspath'	=> 'themes/base_files/feature_css/calendar.css',
			'rank'		=> 20,
		),
	);

	function __construct()
	{
		global $prefs;

		if (! empty($prefs['module_zone_available_extra'])) {
			foreach (array_filter((array) $prefs['module_zone_available_extra']) as $name) {
				$this->module_zones[$name] = $name . '_modules';
			}
		}
	}

    /**
     * @param $name
     * @param $title
     * @param $data
     * @param null $parse
     * @return bool
     */
    function replace_user_module($name, $title, $data, $parse=NULL)
	{
        global $prefs;

		if ((!empty($name)) && (!empty($data))) {
			$query = "delete from `tiki_user_modules` where `name`=?";
			$result = $this->query($query, array($name), -1, -1, false);
			$query = "insert into `tiki_user_modules`(`name`,`title`,`data`, `parse`) values(?,?,?,?)";

            //BEGIN wikiLingo integration
            if ($prefs['feature_wikilingo'] == 'y') {
                $this->query("DELETE FROM `tiki_output` WHERE `objectType` = ? AND `entityId` = ?", array("userModule", $name));

                switch (strtolower($parse))
                {
                    case 'wikilingo':
                        $this->query("INSERT INTO `tiki_output` (`objectType`, `entityId`, `version`) VALUES  (?, ?, ?)", array("userModule", $name, 1));
                        $parse = 'y';
                }
            }
            //END wikiLingo integration
			$result = $this->query($query, array($name,$title,$data,$parse));

			$cachelib = TikiLib::lib('cache');
			$cachelib->invalidate("user_modules_$name");

			$wikilib = TikiLib::lib('wiki');
			$converter = new convertToTiki9();
			$converter->saveObjectStatus($name, 'tiki_user_modules', 'new9.0+');

			return true;
		}
	}

    /**
     * @param int $moduleId
     * @param $name
     * @param $title
     * @param $position
     * @param $order
     * @param int $cache_time
     * @param int $rows
     * @param null $groups
     * @param null $params
     * @param null $type
     * @return bool
     */
    function assign_module($moduleId=0, $name, $title, $position, $order, $cache_time = 0, $rows = 10, $groups = null, $params = null,$type = null)
	{
		//check for valid values
		$cache_time = is_numeric($cache_time) ? $cache_time : 0;
		$rows = is_numeric($rows) ? $rows : 10;

		if ( is_array($params) ) {
			$params = $this->serializeParameters($name, $params);
		}

		if ($moduleId) {
			$query = "update `tiki_modules` set `name`=?,`title`=?,`position`=?,`ord`=?,`cache_time`=?,`rows`=?,`groups`=?,`params`=?,`type`=? where `moduleId`=?";
			$result = $this->query($query, array($name,$title,$position,(int) $order,(int) $cache_time,(int) $rows,$groups,$params,$type, $moduleId));
		} else {
			$query = "delete from `tiki_modules` where `name`=? and `position`=? and `ord`=? and `params`=?";
			$this->query($query, array($name, $position, (int)$order, $params));
			$query = "insert into `tiki_modules`(`name`,`title`,`position`,`ord`,`cache_time`,`rows`,`groups`,`params`,`type`) values(?,?,?,?,?,?,?,?,?)";
			$result = $this->query($query, array($name,$title,$position,(int) $order,(int) $cache_time,(int) $rows,$groups,$params,$type));
			$moduleId = $this->lastInsertId(); //to return the recently created module
			if ($type == "D" || $type == "P") {
				$query = 'select `moduleId` from `tiki_modules` where `name`=? and `title`=? and `position`=? and `ord`=? and `cache_time`=? and `rows`=? and `groups`=? and `params`=? and `type`=?';
				$moduleId = $this->getOne($query, array($name,$title,$position,(int) $order,(int) $cache_time,(int) $rows,$groups,$params,$type));
			}
		}
		if ($type == "D" || $type == "P") {
			$usermoduleslib = TikiLib::lib('usermodules');
			$usermoduleslib->add_module_users($moduleId, $name, $title, $position, $order, $cache_time, $rows, $groups, $params, $type);
		}
		return $moduleId;
	}

	/* Returns the requested module assignation. A module assignation is represented by an array similar to a tiki_modules record. The groups field is unserialized in the module_groups key, a spaces-separated list of groups. */
    /**
     * @param $moduleId
     * @return mixed
     */
    function get_assigned_module($moduleId)
	{
		$query = "select * from `tiki_modules` where `moduleId`=?";
		$result = $this->query($query, array($moduleId));
		$res = $result->fetchRow();

		if ($res["groups"]) {
			$grps = unserialize($res["groups"]);

			$res["module_groups"] = implode(' ', $res['groups']);
		}

		return $res;
	}

    /**
     * @param $moduleId
     * @return bool
     */
    function unassign_module($moduleId)
	{
		$query = "delete from `tiki_modules` where `moduleId`=?";
		$result = $this->query($query, array($moduleId));
		$query = "delete from `tiki_user_assigned_modules` where `moduleId`=?";
		$result = $this->query($query, array($moduleId));
		return true;
	}

    /**
     * @param $name
     * @return int
     */
    function get_rows($name)
	{
		$query = "select `rows` from `tiki_modules` where `name`=?";

		$rows = $this->getOne($query, array($name));

		if ($rows == 0)
			$rows = 10;

		return $rows;
	}

    /**
     * @param $moduleId
     * @return bool
     */
    function module_up($moduleId)
	{
		$query = "update `tiki_modules` set `ord`=`ord`-1 where `moduleId`=?";
		$result = $this->query($query, array($moduleId));
		return true;
	}

    /**
     * @param $moduleId
     * @return bool
     */
    function module_down($moduleId)
	{
		$query = "update `tiki_modules` set `ord`=`ord`+1 where `moduleId`=?";
		$result = $this->query($query, array($moduleId));
		return true;
	}

    /**
     * @param $moduleId
     * @return bool
     */
    function module_left($moduleId)
	{
		$query = "update `tiki_modules` set `position`='left' where `moduleId`=?";
		$result = $this->query($query, array($moduleId));
		return true;
	}

    /**
     * @param $moduleId
     * @return bool
     */
    function module_right($moduleId)
	{
		$query = "update `tiki_modules` set `position`='right' where `moduleId`=?";
		$result = $this->query($query, array($moduleId));
		return true;
	}

	/**
	 * Reset all module ord's according to supplied array or by displayed order
	 * @param array $module_order[zone][moduleId] (optional)
	 */
	function reorder_modules($module_order = array())
	{
		global $user;
		$all_modules = $this->get_modules_for_user($user, $this->module_zones);
		if (empty($module_order)) {	// rewrite module order as displayed
			foreach ($all_modules as $zone => $contents) {
				$module_order[$zone] = array();
	    		foreach ($contents as $index => $module) {
	    			$module_order[$zone][$index] = (int) $module['moduleId'];
	    		}
			}
		}
		$section_map = array_flip($this->module_zones);
		$bindvars = array();
		$query = 'UPDATE `tiki_modules` SET `ord`=?, `position`=? WHERE `moduleId`=?;';
		foreach ($module_order as $zone => $contents) {
    		$section_initial = $section_map[$zone];
    		foreach ($contents as $index => $moduleId) {
    			if ($moduleId) {
	    			if ($all_modules[$zone][$index]['moduleId'] != $moduleId || ($all_modules[$zone][$index]['ord'] != $index + 1 || $all_modules[$zone][$index]['position'] != $section_initial)) {
						$bindvars = array(
							$index + 1,
							$section_initial,
							$moduleId,
						);
						$this->query($query, $bindvars);
	    			}
    			}
    		}
		}
	}

    /**
     * @return array
     */
    function get_all_modules()
	{
		$user_modules = $this->list_user_modules();

		$all_modules = array();

		foreach ($user_modules["data"] as $um) {
			$all_modules[] = $um["name"];
		}

		// Now add all the system modules
		$h = opendir("templates/modules");
		while (($file = readdir($h)) !== false) {
			if (substr($file, 0, 4) == 'mod-' && preg_match("/\.tpl$/", $file)) {
				if (!strstr($file, "nocache")) {
					$name = substr($file, 4, strlen($file) - 8);

					$all_modules[] = $name;
				}
			}
		}
		closedir($h);
		return $all_modules;
	}

    /**
     * @param $name
     * @return bool
     */
    function remove_user_module($name)
	{

		$query = "delete from `tiki_modules` where `name`=?";
		$result = $this->query($query, array($name));

		$query = " delete from `tiki_user_modules` where `name`=?";
		$result = $this->query($query, array($name));

		$cachelib = TikiLib::lib('cache');
		$cachelib->invalidate('user_modules');

		return true;
	}

    /**
     * @param string $sort_mode
     * @return array
     */
    function list_user_modules($sort_mode='name_asc')
	{
		$query = "select * from `tiki_user_modules` order by ".$this->convertSortMode($sort_mode);

		$result = $this->query($query, array());
		$query_cant = "select count(*) from `tiki_user_modules`";
		$cant = $this->getOne($query_cant, array());
		$ret = array();

		while ($res = $result->fetchRow()) {
			$ret[] = $res;
		}

		$retval = array();
		$retval["data"] = $ret;
		$retval["cant"] = $cant;
		return $retval;
	}

	function clear_cache()
	{
		global $tikidomain;
		$dircache = "modules/cache";
		if ($tikidomain) {
			$dircache.= "/$tikidomain";
		}
		$h = opendir($dircache);
		while (($file = readdir($h)) !== false) {
			if (substr($file, 0, 3) == 'mod') {
				$file = "$dircache/$file";
				unlink($file);
			}
		}
		closedir($h);
	}
	/* @param module_info = info of a module
	 * @param user_groups = list of groups of a user
	 * @param user = the user
	 * @return string 'y' = ok, 'n' = not ok
	 */
	function check_groups($module_info, $user, $user_groups)
	{
		global $prefs, $tiki_p_admin;
		if ( empty( $user ) ) {
			$user_groups = array( 'Anonymous' );
		}
		$pass = 'y';
		if ($tiki_p_admin == 'y' && $prefs['modhideanonadmin'] == 'y' && $module_info['groups'] == serialize(array('Anonymous')) &&
				strpos($_SERVER["SCRIPT_NAME"], 'tiki-admin_modules.php') === false) {
			$pass = 'n';
		} elseif ($tiki_p_admin != 'y' && $prefs['modallgroups'] != 'y') {
			if ($module_info['groups']) {
				if (!is_array($module_info['groups'])) {
					$module_groups = unserialize($module_info['groups']);
				} else {
					$module_groups = $module_info['groups'];
				}
			} else {
				$module_groups = array();
			}
			if (!empty($module_groups)) {	// if no groups are set show to all users (modules revamp [MOD] in Tiki 7)
				$pass = 'n';
				if ($prefs['modseparateanon'] !== 'y') {
					foreach ($module_groups as $mod_group) {
						if (in_array($mod_group, $user_groups)) {
							$pass = 'y';
							break;
						}
					}
				} else {
					if (!$user) {
						if (in_array('Anonymous', $module_groups)) {
							$pass = 'y';
						}
					} else {
						foreach ($module_groups as $mod_group) {
							if ($mod_group === 'Anonymous') {
								continue;
							}
							if (in_array($mod_group, $user_groups)) {
								$pass = 'y';
								break;
							}
						}
					}
				}
			}
		}
		return $pass;
	}

    /**
     * @param $user
     * @param array $module_zones
     * @return array
     */
    function get_modules_for_user( $user, array $module_zones = array())
	{
		if (empty($module_zones)) {
			$module_zones = $this->module_zones;
		}
		$list = $this->get_raw_module_list_for_user($user, $module_zones);

		foreach ( $list as & $partial ) {
			$partial = array_map(array( $this, 'augment_module_parameters' ), $partial);
			if (!$this->is_admin_mode(true)) {
				$partial = array_values(array_filter($partial, array( $this, 'filter_active_module' )));
			}
		}

		return $list;
	}

    /**
     * @param $module
     * @return mixed
     */
    function augment_module_parameters( $module )
	{
		global $prefs;

		TikiLib::parse_str($module['params'], $module_params);
		$default_params = array(
			'decorations' => 'y',
			'overflow' => 'n',
			'nobox' => 'n',
			'notitle' => 'n',
			'error' => '',
			'flip' => ( $prefs['user_flip_modules'] == 'module' ) ? 'n' : $prefs['user_flip_modules'],
		);

		if (!is_array($module_params)) {
			$module_params = array();
		}

		$module_params = array_merge($default_params, $module_params);

		$module_params['module_position'] = $module['position'];
		$module_params['module_ord'] = $module['ord'];

		if ($module['name'] == 'addon' && !empty($module_params['otherparams'])) {
			parse_str($module_params['otherparams'], $other_params);
			if (is_array($other_params)) {
				$module_params = $module_params + $other_params;
			}
		}

		if ( $prefs['user_flip_modules'] === 'n' ) {
			$module_params['flip'] = 'n';
		}

		if ( isset( $module_params['section'] ) && $module_params['section'] == 'wiki' ) {
			$module_params['section'] = 'wiki page';
		}

		$module['params'] = $module_params;

		return $module;
	}

    /**
     * @param $module
     * @return bool
     */
    function filter_active_module( $module )
	{
		global $section, $page, $prefs, $user;
		$tikilib = TikiLib::lib('tiki');
		// Validate preferences
		$module_info = $this->get_module_info($module['name']);
		$params = $module['params'];

		if ( $prefs['feature_perspective'] == 'y' ) {
			$perspectivelib = TikiLib::lib('perspective');
			$persp = $perspectivelib->get_current_perspective($prefs);
			if (empty($persp)) {
				$persp = 0;
			}
			if ( isset( $params['perspective'] ) && !in_array($persp, (array) $params['perspective'])) {
				return false;
			}
		}

		if ( isset( $params["lang"] ) && ! in_array($prefs['language'], (array) $params["lang"])) {
			return false;
		}

		if ( isset( $params['section'] ) && ( !isset($section)  || !in_array($section, (array) $params['section']))) {
			return false;
		}

		if ( isset( $params['nopage'] ) && isset( $page ) && isset( $section ) && $section == 'wiki page' ) {
			if ( in_array($page, (array) $params['nopage']) ) {
				return false;
			}
		}

		if ( isset( $params['page'] ) ) {
			if ( ! isset($section) || $section != 'wiki page' || ! isset( $page ) ) { // must be in a page
				return false;
			} elseif ( ! in_array($page, (array) $params['page']) ) {
				return false;
			}
		}

		if ( isset( $params['theme'] ) ) {
			global $tc_theme;

			$ok = false;
			foreach ((array) $params['theme'] as $t) {
				if ( $t{0} != '!' ) { // usual behavior
					if ( !empty($tc_theme) && $t === $tc_theme ) {
						$ok = true;
					} elseif ( $t === $prefs['style'] && empty($tc_theme)) {
						$ok = true;
					}
				} else { // negation behavior
					$excluded_theme = substr($t, 1);
					$ok = true;
					if ( !empty($tc_theme) && $excluded_theme === $tc_theme ) {
						return false;
					} elseif ( $excluded_theme === $prefs['style'] && empty( $tc_theme )) {
						return false;
					}
				}
			}
			if (!$ok) {
				return false;
			}
		}

		if (! Perms::get()->admin) {
			$user_groups = Perms::get()->getGroups();
		} else {
			$user_groups = array();
		}

		if ( 'y' != $this->check_groups($module, $user, $user_groups) ) {
			return false;
		}

		if ( isset( $params['creator'] ) && $section == 'wiki page' && isset( $page ) ) {
			if ( ! $page_info = $tikilib->get_page_info($page) ) {
				return false;
			} elseif ( $params['creator'] == 'y' && $page_info['creator'] != $user) {
				return false;
			} elseif ( $params['creator'] == 'n' && $page_info['creator'] == $user ) {
				return false;
			}
		}

		if ( isset( $params['contributor'] ) && $section == 'wiki page' && isset( $page ) ) {
			if ( ! $page_info = $tikilib->get_page_info($page) ) {
				return false;
			} else {
				$wikilib = TikiLib::lib('wiki');
				$contributors = $wikilib->get_contributors($page);
				$contributors[] = $page_info['creator'];
				$in = in_array($user, $contributors);

				if ( $params['contributor'] == 'y' && ! $in ) {
					return false;
				} elseif ( $params['contributor'] == 'n' && $in ) {
					return false;
				}
			}
		}

		if ($module['name'] == 'login_box' && (basename($_SERVER['SCRIPT_NAME']) == 'tiki-login_scr.php' || basename($_SERVER['SCRIPT_NAME']) == 'tiki-login_openid.php')) {
			return false;
		}

		if ( $prefs['feature_categories'] == 'y' ) {
			if ( $this->is_hidden_by_category($params)) {
				return false;
			}

			if ( $this->is_hidden_by_no_category($params)) {
				return false;
			}
		}

		if ( $prefs['cookie_consent_feature'] == 'y' ) {		// check if consent required to show
			if (!empty($params['cookie_consent']) && $params['cookie_consent'] === 'y') {
				global $feature_no_cookie;
				if ($feature_no_cookie) {
					return false;
				}
			}
		}

		foreach ( $module_info['prefs'] as $p ) {
			if ( $prefs[$p] != 'y' ) {
				$this->add_pref_error($module['name'], $p);
				return false;
			}
		}

		return true;
	}

    /**
     * @param $params
     * @return bool
     */
    private function is_hidden_by_category($params)
	{
		global $cat_type, $cat_objid;
		if ( empty( $params['category'])) {
			return false;
		}

		if ( empty($cat_type) || empty($cat_objid)) {
			return true;
		}

		$catIds = TikiLib::lib('categ')->get_object_categories($cat_type, $cat_objid);

		if (empty($catIds)) {
			return true;
		}

		$categories = (array) $params['category'];

		return ! $this->matches_any_in_category_list($categories, $catIds, ! empty($params['subtree']));
	}

    /**
     * @param $params
     * @return bool
     */
    private function is_hidden_by_no_category($params)
	{
		global $cat_type, $cat_objid;
		if ( empty( $params['nocategory'])) {
			return false;
		}

		if ( empty($cat_type) || empty($cat_objid)) {
			return false;
		}

		$catIds = TikiLib::lib('categ')->get_object_categories($cat_type, $cat_objid);

		if (empty($catIds)) {
			return false;
		}

		$categories = (array) $params['nocategory'];

		return $this->matches_any_in_category_list($categories, $catIds, ! empty($params['subtree']));
	}

    /**
     * @param $desiredList
     * @param $categoryList
     * @param bool $deep
     * @return bool
     */
    private function matches_any_in_category_list($desiredList, $categoryList, $deep = false)
	{
		if (empty($categoryList)) {
			return false;
		}

		$allcats = TikiLib::lib('categ')->getCategories();	// gets all categories (cached)

		foreach ($desiredList as $category) {
			if (is_numeric($category)) {
				if (in_array($category, $categoryList)) {
					return true;
				}
			} else {
				foreach ($categoryList as $id) {
					if (isset($allcats[$id]) && $allcats[$id]['name'] == $category) {
						return true;
					}
				}
			}
		}

		if ($deep) {
			$nextList = array();
			foreach ($categoryList as $id) {
				if (isset($allcats[$id]) && $allcats[$id]['parentId']) {
					$nextList[] = $allcats[$id]['parentId'];
				}
			}

			return $this->matches_any_in_category_list($desiredList, $nextList, $deep);
		}

		return false;
	}

    /**
     * @param $user
     * @param array $module_zones
     * @return array
     */
    private function get_raw_module_list_for_user( $user, array $module_zones )
	{
		global $prefs, $tiki_p_configure_modules;
		$usermoduleslib = TikiLib::lib('usermodules');

		$out = array_fill_keys(array_values($module_zones), array());

		if ( ! empty($prefs['module_file']) ) {
			$out = array_merge($out, $this->read_module_file($prefs['module_file']));
		} elseif ( $prefs['user_assigned_modules'] == 'y'
			&& $tiki_p_configure_modules == 'y'
			&& $user
			&& $usermoduleslib->user_has_assigned_modules($user) ) {

			foreach ( $module_zones as $zone => $zone_name ) {
				$out[$zone_name] = $usermoduleslib->get_assigned_modules_user($user, $zone);
			}
		} else {
			$modules_by_position = $this->get_assigned_modules(null, 'y');
			foreach ( $module_zones as $zone => $zone_name ) {
				if ( isset($modules_by_position[$zone]) ) {
					$out[$zone_name] = $modules_by_position[$zone];
				}
			}
		}

		return $out;
	}

    /**
     * @return array
     */
    function list_module_files()
	{
		$files = array();
		if (is_dir('modules')) {
			if ($dh = opendir('modules')) {
				while (($file = readdir($dh)) !== false) {
					if (preg_match("/^mod-func-.*\.php$/", $file)) {
						array_push($files, $file);
					}
				}
				closedir($dh);
			}
		}
		sort($files);
		return $files;
	}

    /**
     * @param $module
     * @return array|mixed
     */
    function get_module_info( $module )
	{
		if ( is_array($module) ) {
			$moduleName = $module['name'];
		} else {
			$moduleName = $module;
		}

		global $prefs;

		$cachelib = TikiLib::lib('cache');
		$cacheKey = 'module.' . $moduleName . $prefs['language'];
		$info = $cachelib->getSerialized($cacheKey, 'module');

		if ($info) {
			if (! isset($info['cachekeygen'])) {
				$info['cachekeygen'] = array( $this, 'createDefaultCacheKey' );
			}
			return $info;
		}

		$phpfuncfile = 'modules/mod-func-' . $moduleName . '.php';
		$info_func = "module_{$moduleName}_info";
		$info = array();

		if ( file_exists($phpfuncfile) ) {
			include_once $phpfuncfile;

			if ( function_exists($info_func) ) {
				$info = $info_func();
				if (!empty($info['params'])) {
					foreach ($info['params'] as &$p) {
						$p['section'] = 'module';
					}
				}
			}

			$info['type'] = 'function';
		}

		$defaults = array(
			'name' => $moduleName,
			'description' => tra('Description not available'),
			'type' => 'include',
			'prefs' => array(),
			'params' => array(),
		);

		$info = array_merge($defaults, $info);

		$info['params'] = array_merge(
			$info['params'],
			array(
				'title' => array(
					'name' => tra('Module Title'),
					'description' => tra('Title to display at the top of the box.'),
					'filter' => 'striptags',
					'section' => 'appearance',
				),
				'nobox' => array(
					'name' => tra('No Box'),
					'description' => 'y|n '.tra('Show only the content'),
					'section' => 'appearance',
				),
				'decorations' => array(
					'name' => tra('Title, background, etcs'),
					'description' => 'y|n '. tra('Show module decorations'),
					'section' => 'appearance',
				),
				'notitle' => array(
					'name' => tra('No Title'),
					'description' => 'y|n '.tra('Hide module title'),
					'filter' => 'alpha',
					'section' => 'appearance',
				),
				'category' => array(
					'name' => tra('Category'),
					'description' => tra('Module displayed depending on category. Separate multiple category IDs or names by semi-colons.'),
					'section' => 'visibility',
					'separator' => ';',
					'filter' => 'alnum',
					'profile_reference' => 'category',
				),
				'nocategory' => array(
					'name' => tra('No Category'),
					'description' => tra('Module is hidden depending on category. Separate multiple category IDs or names by semi-colons. This takes precedence over the category parameter above.'),
					'section' => 'visibility',
					'separator' => ';',
					'filter' => 'alnum',
					'profile_reference' => 'category',
				),
				'subtree' => array(
					'name' => tra('Category subtrees'),
					'description' => tra('Consider child categories of the categories listed in "category" and "no category" to be part of those categories. (0 or 1)'),
					'section' => 'visibility',
					'filter' => 'int',
				),
				'perspective' => array(
					'name' => tra('Perspective'),
					'description' => tra('Module is displayed only in the listed perspective ID(s). Separate multiple perspective IDs by semi-colons.'),
					'separator' => ';',
					'filter' => 'digits',
					'section' => 'visibility',
					'profile_reference' => 'perspective',
				),
				'lang' => array(
					'name' => tra('Language'),
					'description' => tra('Module is displayed only when the specified language(s) in use. Designate languages by two-character language codes. Separate multiple languages by semi-colons.'),
					'separator' => ';',
					'filter' => 'lang',
					'section' => 'visibility',
				),
				'section' => array(
					'name' => tra('Section'),
					'description' => tra('Module is displayed only in the specified sections. Separate multiple sections by semi-colons. Choose from: blogs; calendar; categories; cms (for "articles"); contacts; directory; faqs; featured_links; file_galleries; forums; galleries (for "image galleries"); gmaps; html_pages; maps; mytiki; newsletters; poll; quizzes; surveys; trackers; user_messages; webmail; wiki page'),
					'separator' => ';',
					'filter' => 'striptags',
					'section' => 'visibility',
				),
				'page' => array(
					'name' => tra('Page Filter'),
					'description' => tra('Module is displayed only on the specified page(s). Separate multiple page names by semi-colons.'),
					'separator' => ';',
					'filter' => 'pagename',
					'section' => 'visibility',
					'profile_reference' => 'wiki_page',
				),
				'nopage' => array(
					'name' => tra('No Page'),
					'description' => tra('Module is not displayed on the specified page(s). Separate multiple page names by semi-colons.'),
					'separator' => ';',
					'filter' => 'pagename',
					'section' => 'visibility',
					'profile_reference' => 'wiki_page',
				),
				'theme' => array(
					'name' => tra('Theme'),
					'description' => tra('Module is displayed or not displayed depending on the theme. (Enter the theme\'s file name, for example, "thenews.css".) Prefix the theme name with "!" for the module to not display. Separate multiple theme names by semi-colons.'),
					'separator' => ';',
					'filter' => 'themename',
					'section' => 'visibility',
				),
				'creator' => array(
					'name' => tra('Creator'),
					'description' => tra('Module only available based on the relationship of the user with the wiki page. Either only creators (y) or only non-creators (n) will see the module.'),
					'filter' => 'alpha',
					'section' => 'visibility',
				),
				'contributor' => array(
					'name' => tra('Contributor'),
					'description' => tra('Module only available based on the relationship of the user with the wiki page. Either only contributors (y) or only non-contributors (n) will see the module.'),
					'filter' => 'alpha',
					'section' => 'visibility',
				),
				'flip' => array(
					'name' => tra('Flip'),
					'description' => tra('Users can open and close the module.'),
					'filter' => 'alpha',
					'section' => 'appearance',
				),
				'style' => array(
					'name' => tra('Style'),
					'description' => tra('CSS style for positioning the module, etc.'),
					'section' => 'appearance',
				),
				'class' => array(
					'name' => tra('Class'),
					'description' => tra('Custom CSS class.'),
					'section' => 'appearance',
				),
				'topclass' => array(
					'name' => tra('Containing Class'),
					'description' => tra('Custom CSS class of div around the module.'),
					'section' => 'appearance',
				),
			)
		);

		if ($prefs['cookie_consent_feature'] === 'y') {
			$info['params']['cookie_consent'] = array(
				'name' => tra('Cookie Consent'),
				'description' => 'n|y '.tra('Show only if consent to accept cookies has been granted.'),
				'filter' => 'alpha',
				'section' => 'visibility',
			);

		}

		// Parameters common to several modules, but not all
		$common_params = array(
			'nonums' => array(
				'name' => tra('No Numbers'),
				'description' => tra('If set to "y", the module will not number list items.'),
				'section' => 'appearance',
			),
			'rows' => array(
				'name' => tra('Rows'),
				'description' => tra('Number of rows, or items, to display.') . ' ' . tra('Default: 10.'),
				'section' => 'appearance',
			)
		);

		if ($info['type'] == 'function') {
			foreach ($common_params as $key => $common_param) {
				$info['params'][$key] = $common_param;
			}
		}

		// Parameters are not required, unless specified.
		if (!empty($info['params'])) {
			foreach ($info['params'] as &$param)
				if (!isset($param['required']))
					$param['required'] = false;
		}

		$cachelib->cacheItem($cacheKey, serialize($info), 'module');

		if (! isset($info['cachekeygen'])) {
			$info['cachekeygen'] = array( $this, 'createDefaultCacheKey' );
		}

		return $info;
	}

    /**
     * @param $mod_reference
     * @return string
     */
    function createDefaultCacheKey( $mod_reference )
	{
		global $prefs;
		return $mod_reference['moduleId'] . '-' . $mod_reference['name'] . '-'. $prefs['language'] . '-' .
			   serialize($mod_reference['params']) . (isset($_SESSION['current_perspective']) ? '-p' . $_SESSION['current_perspective'] : '');
	}

    /**
     * @param $mod_reference
     * @return bool|mixed|string
     */
    function execute_module( $mod_reference )
	{
		global $user, $prefs, $tiki_p_admin;
		$smarty = TikiLib::lib('smarty');
		$tikilib = TikiLib::lib('tiki');

		try {
			$defaults = array(
				'style' => '',
				'nonums' => 'n',
			);
			$module_params = isset($mod_reference['params']) ? (array) $mod_reference['params'] : array();
			$module_params = array_merge($defaults, $module_params); // not sure why style doesn't get set sometime but is used in the tpl

			$mod_reference = array_merge(array('moduleId' => null, 'ord' => 0, 'position' => 0, 'rows' => 10), $mod_reference);

			$module_rows = $mod_reference["rows"];

			$info = $this->get_module_info($mod_reference);
			$cachefile = $this->get_cache_file($mod_reference, $info);

			foreach ((array) $info['prefs'] as $preference) {
				if ($prefs[$preference] != 'y') {
					$smarty->loadPlugin('smarty_block_remarksbox');

					return smarty_block_remarksbox(
						array(
							'type' => 'warning',
							'title' => tr('Failed to execute "%0" module', $mod_reference['name']),
						),
						tr('Missing dependencies'),
						$smarty,
						$repeat
					);
				}
			}

			if ( ! $cachefile || $this->require_cache_build($mod_reference, $cachefile) || $this->is_admin_mode() ) {

				if ($this->is_admin_mode()) {
					require_once ('lib/setup/timer.class.php');
					$timer = new timer('module');
					$timer->start('module');
				}
				if ( $info['type'] == "function") // Use the module name as default module title. This can be overriden later. A module can opt-out of this in favor of a dynamic default title set in the TPL using clear_assign in the main module function. It can also be overwritten in the main module function.
					$smarty->assign('tpl_module_title', tra($info['name']));

				$smarty->assign('nonums', $module_params['nonums']);

				if ( $info['type'] == 'include' ) {
					$phpfile = 'modules/mod-' . $mod_reference['name'] . '.php';

					if ( file_exists($phpfile) ) {
						include $phpfile;
					}
				} elseif ( $info['type'] == 'function' ) {
					$function = 'module_' . $mod_reference['name'];
					$phpfuncfile = 'modules/mod-func-' . $mod_reference['name'] . '.php';

					if (file_exists($phpfuncfile)) {
						include_once $phpfuncfile;
					}

					if ( function_exists($function) ) {
						$function( $mod_reference, $module_params );
					}
				}

				$ck = getCookie('mod-'.$mod_reference['name'].$mod_reference['position'].$mod_reference['ord'], 'menu', 'o');
				$smarty->assign('module_display', ($prefs['javascript_enabled'] == 'n' || $ck == 'o'));

				$smarty->assign_by_ref('module_rows', $mod_reference['rows']);
				$smarty->assign_by_ref('module_params', $module_params); // module code can unassign this if it wants to hide params
				$smarty->assign('module_ord', $mod_reference['ord']);
				$smarty->assign('module_position', $mod_reference['position']);
				$smarty->assign('moduleId', $mod_reference['moduleId']);
				if ( isset( $module_params['title'] ) ) {
					$smarty->assign('tpl_module_title', tra($module_params['title']));
				}
				$smarty->assign('tpl_module_name', $mod_reference['name']);

				$tpl_module_style = empty($mod_reference['module_style']) ? '' : $mod_reference['module_style'];

				if ($tiki_p_admin == 'y' && $this->is_admin_mode() && (!$this->filter_active_module($mod_reference) ||
							$prefs['modhideanonadmin'] == 'y' && (empty($mod_reference['groups']) || $mod_reference['groups'] == serialize(array('Anonymous'))))) {
					$tpl_module_style .= 'opacity: 0.5;';
				}
				if (isset($module_params['overflow']) && $module_params['overflow'] === 'y') {
					$tpl_module_style .= 'overflow:visible !important;';
				}
				$smarty->assign('tpl_module_style', $tpl_module_style);

				$template = 'modules/mod-' . $mod_reference['name'] . '.tpl';

				if (file_exists('templates/'.$template)) {
					$data = $smarty->fetch($template);
				} else {
					$data = $this->get_user_module_content($mod_reference['name'], $module_params);
				}
				$smarty->clear_assign('module_params'); // ensure params not available outside current module
				$smarty->clear_assign('tpl_module_title');
				$smarty->clear_assign('tpl_module_name');
				$smarty->clear_assign('tpl_module_style');

				if ($this->is_admin_mode() && $timer) {
					$elapsed = round($timer->stop('module'), 3);
					$data = preg_replace('/<div /', '<div title="Module Execution Time ' . $elapsed . 's" ', $data, 1);
				}

				if (!empty($cachefile) && !$this->is_admin_mode()) {
					file_put_contents($cachefile, $data);
				}
			} else {
				$data = file_get_contents($cachefile);
			}

			return $data;
		} catch (Exception $e) {
			$smarty->loadPlugin('smarty_block_remarksbox');
			if ($tiki_p_admin == 'y') {
				$message = $e->getMessage();
			} else {
				$message = tr('Contact the system administrator');
			}
			$repeat = false;
			return smarty_block_remarksbox(
				array(
					'type' => 'warning',
					'title' => tr('Failed to execute "%0" module', $mod_reference['name']),
				),
				html_entity_decode($message),
				$smarty,
				$repeat
			);
		}
	}

	/**
	 * Returns true if on the admin modules page
	 *
	 * @param bool $ifShowingHiddenModules	 - check for $_REQUEST['show_hidden_modules'] as well
	 *
	 * @return bool
	 */
	function is_admin_mode( $ifShowingHiddenModules = false)
	{
		global $tiki_p_admin_modules;

		$ok = true;
		if ($ifShowingHiddenModules && empty($_REQUEST['show_hidden_modules'])) {
			$ok = false;
		}
		return $ok && $tiki_p_admin_modules === 'y' &&
				strpos($_SERVER["SCRIPT_NAME"], 'tiki-admin_modules.php') !== false;
	}

    /**
     * @param $name
     * @param $module_params
     * @return mixed
     */
    function get_user_module_content( $name, $module_params )
	{
		$smarty = TikiLib::lib('smarty');
		$tikilib = TikiLib::lib('tiki');
		$smarty->assign('module_type', 'module');
		$info = $this->get_user_module($name);
		if (!empty($info)) {
			// test if we have a menu
			if (strpos($info['data'], '{menu ') === 0 and strpos($info['data'], "css=n") === false) {
				$smarty->assign('module_type', 'cssmenu');
			}

            $info = $this->parse($info);

			// re-assign module_params for the custom module in case a module plugin is used inside it
			$smarty->assign_by_ref('module_params', $module_params);
			$smarty->assign('user_title', tra($info['title']));
			$smarty->assign_by_ref('user_data', $info['data']);
			$smarty->assign_by_ref('user_module_name', $info['name']);

			return $smarty->fetch('modules/user_module.tpl');
		}
	}

    function parse($info)
    {
        global $prefs, $headerlib;
		$tikilib = TikiLib::lib('tiki');
        //allow for wikiLingo parsing, will only return 'y' if turned on AND enabled for this particular module
        if (isset($info['wikiLingo']) && $info['wikiLingo'] == 'y' && $prefs['feature_wikilingo'] == 'y') {
	        //TODO: correct the paths for scripts and output them to the header
	        $scripts = new WikiLingo\Utilities\Scripts();
	        $parser = new WikiLingo\Parser($scripts);
	        $info['data'] = $parser->parse($info['data']);
	        $info['title'] = $parser->parse($info['title']);

	        /* output css from wikiLingo in a literal so smarty doesn't throw up.
	         * NOTE: this is not added to headerlib because it has already passed the opportunity to get more css
	         */
	        $info['data'] = '{literal}' . $scripts->renderCss() . '{/literal}' . $info['data'];

	        //output js to headerlib, because js is at bottom and has not yet been output
	        foreach ( $scripts->scriptLocations as $scriptLocation ) {
		        $headerlib->add_jsfile($scriptLocation);
	        }
	        $headerlib->add_js(implode($scripts->scripts));

        } else if (isset($info['parse']) && $info['parse'] == 'y') {
            $info['data'] = $tikilib->parse_data($info['data'], array('is_html' => true, 'suppress_icons' => true));
            $info['title'] = $tikilib->parse_data($info['title'], array('noparseplugins' => true, 'is_html' => true));
        }

        return $info;
    }

    /**
     * @param $mod_reference
     * @param $info
     * @return null|string
     */
    function get_cache_file( $mod_reference, $info )
	{
		global $tikidomain, $user;
		$nocache = 'templates/modules/mod-' . $mod_reference["name"] . '.tpl.nocache';

		// Uncacheable
		if ( ! empty( $user ) || $mod_reference['cache_time'] <= 0 || file_exists($nocache) ) {
			return null;
		}

		$cb = $info['cachekeygen'];

		$cachefile = 'modules/cache/';
		if ($tikidomain) {
			$cachefile.= "$tikidomain/";
		}

		$cachefile .= 'mod-' . md5(call_user_func($cb, $mod_reference));

		return $cachefile;
	}

	// Returns whether $cachefile needs to be [re]built
    /**
     * @param $mod_reference
     * @param $cachefile
     * @return bool
     */
    function require_cache_build( $mod_reference, $cachefile )
	{
		$tikilib = TikiLib::lib('tiki');
		return ! file_exists($cachefile)
			|| ( $tikilib->now - filemtime($cachefile) ) >= $mod_reference['cache_time'];
	}

    /**
     * @param $input
     * @param $params
     */
    function dispatchValues( $input, & $params )
	{
		if ( is_string($input) ) {
			TikiLib::parse_str($input, $module_params);
		} else {
			$module_params = $input;
		}

		foreach ( $params as $name => & $inner ) {
			if ( isset( $module_params[$name] ) ) {
				if ( isset( $inner['separator'] ) ) {
					$inner['value'] = implode($inner['separator'], (array) $module_params[$name]);
				} else {
					$inner['value'] = $module_params[$name];
				}
			} else {
				$inner['value'] = null;
			}
		}
		// resort params into sections
		$reorderedparams = array();
		foreach ($params as $k => $p) {
			if (!isset($reorderedparams[$p['section']])) {
				$reorderedparams[$p['section']] = array();
			}
			$reorderedparams[$p['section']][$k] = $p;
		}
		$params = $reorderedparams;
	}

    /**
     * @param $name
     * @param $params
     * @return string
     */
    function serializeParameters( $name, $params )
	{
		$info = $this->get_module_info($name);
		$expanded = array();

		foreach ( $info['params'] as $name => $def ) {
			if ( isset( $def['filter'] ) ) {
				$filter = TikiFilter::get($def['filter']);
			} else {
				$filter = null;
			}

			if ( isset( $params[$name] ) && $params[$name] !== '' ) {
				if ( isset( $def['separator'] ) && strpos($params[$name], $def['separator']) !== false ) {
					$parts = explode($def['separator'], $params[$name]);

					if ( $filter ) {
						foreach ( $parts as & $single ) {
							$single = $filter->filter($single);
							$single = trim($single);
						}
					}
				} else {
					$parts = $params[$name];
					if ( $filter ) {
						$parts = $filter->filter($parts);
					}
				}

				$expanded[$name] = $parts;
			}
		}
		if (empty($expanded)) {
			return '';// http_build_query return NULL or '' depending on system
		}

		return http_build_query($expanded, '', '&');
	}

    /**
     * @param $module_name
     * @param $preference_name
     */
    function add_pref_error($module_name, $preference_name)
	{
		$this->pref_errors[] = array('mod_name' => $module_name, 'pref_name' => $preference_name);
	}


	/* Returns all module assignations for a certain position, or all positions (by default). A module assignation
	is represented by an array similar to a tiki_modules record. The groups field is unserialized in the module_groups key, a spaces-separated list of groups.
	If asking for a specific position, returns an array of module assignations. If not, returns an array of arrays of modules assignations indexed by positions. For example: array("l" -> array("module assignation"))
	TODO: Document $displayed's effect */
    /**
     * @param null $position
     * @param string $displayed
     * @return array
     */
    function get_assigned_modules($position = null, $displayed="n")
	{

		$filter = '';
		$bindvars = array();

		if ( $position !== null ) {
			$filter .= 'where `position`=?';
			$bindvars[] = $position;
		}

		if ( $displayed != 'n' ) {
			$filter .= ( $filter == '' ? 'where' : 'and' ) . " (`type` is null or `type` != ?)";
			$bindvars[] = 'y';
		}

		$query = "select * from `tiki_modules` $filter order by ".$this->convertSortMode("ord_asc");

		$result = $this->fetchAll($query, $bindvars);

		$ret = array();
		foreach ( $result as $res ) {
			if ($res["groups"] && strlen($res["groups"]) > 1) {
				$grps = @unserialize($res["groups"]);

				$res["module_groups"] = '';
				if (is_array($grps)) {
					foreach ($grps as $grp) {
						$res["module_groups"] .= " $grp ";
					}
				}
			} else {
				$res["module_groups"] = '&nbsp;';
			}
			if ( $position === null ) {
				if ( ! isset($ret[$res['position']]) ) {
					$ret[$res['position']] = array();
				}
				$ret[$res['position']][] = $res;
			} else {
				$ret[] = $res;
			}
		}
		return $ret;
	}

    /**
     * @param $name
     * @return mixed
     */
    function is_user_module($name)
	{
		return $this->table('tiki_user_modules')->fetchCount(array('name' => $name));
	}

    /**
     * @param $name
     * @return mixed
     */
    function get_user_module($name)
	{
        global $prefs;

		$info = $this->table('tiki_user_modules')
            ->fetchFullRow(array('name' => $name));

        if ($prefs['feature_wikilingo'] == 'y') {
            if ($this->getOne('SELECT 1 FROM `tiki_output` WHERE `objectType` = ? AND `entityId` = ?', array('userModule', $name)) != null) {
                $info['wikiLingo'] = 'y';
            }
        }

        return $info;
	}

	/**
	 * @global TikiLib $tikilib
	 * @param bool $added shows current prefs not in defaults
	 * @return array (prefname => array( 'current' => current value, 'default' => default value ))
	 */
	function getModulesForExport()
	{
		$export = array();
		$assigned_modules = $this->get_assigned_modules();

		foreach ( $assigned_modules as $zone => $modules ) {
			foreach ($modules as $pos => $module) {
				$modtogo['type'] = 'module';
				$modtogo['data'] = array();

				$modtogo['data']['name'] = $module['name'];
				TikiLib::parse_str($module['params'], $modtogo['data']['params']);
				$modtogo['data']['groups'] = unserialize($module['groups']);
				$modtogo['data']['order'] = $module['ord'];

				$modtogo['data']['position'] = str_replace('_modules', '', $this->module_zones[$module['position']]);

				if ($this->is_user_module($module['name'])) {
					$um = $this->get_user_module($module['name']);
					if (preg_match("/^\!*\{.*\}$/", trim($um['data']), $matches)) {	// start and end with { and } makes yaml parser think it's a serialized value
						$um['data'] = $um['data'] . "\n";							// so force it to be a literal block
					}
					$modtogo['data']['custom'] = $um['data'];		// the yaml dumper copes with linefeeds etc as a literal block
					$modtogo['data']['parse'] = empty($um['parse']) ? 'n' : $um['parse'];
				}

				$export[] = $modtogo;
			}
		}
		return $export;
	}

    /**
     * @param $filename
     * @return array|mixed
     */
    private function read_module_file($filename)
	{
		$cachelib = TikiLib::lib('cache');

		$expiry = filemtime($filename);
		if ($modules = $cachelib->getSerialized($filename, 'modules', $expiry)) {
			return $modules;
		}

		$content = file_get_contents($filename);
		if (!$content) {
			TikiLib::lib('errorreport')->report(tr('Module file "%0" not found.', $filename));
			return '';
		}

		$profile = Tiki_Profile::fromString("{CODE(caption=>YAML)}$content{CODE}");

		$out = array_fill_keys(array_values($this->module_zones), array());
		foreach ($profile->getObjects() as $object) {
			if ($object->getType() == 'module') {
				$handler = new Tiki_Profile_InstallHandler_Module($object, array());

				$data = $handler->getData();
				$object->replaceReferences($data);
				$data = $handler->formatData($data);

				$data['groups'] = unserialize($data['groups']);
				$position = $data['position'];
				$zone = $this->module_zones[$position];
				$out[$zone][] = $data;
			}
		}

		$cachelib->cacheItem($filename, serialize($out), 'modules');
		return $out;
	}

}

/**
 * Function made available in the template files to behave differently depending on if a zone is empty or not.
 */
function zone_is_empty($zoneName)
{
	$smarty = TikiLib::lib('smarty');
	$moduleZones = $smarty->getTemplateVars('module_zones');

	$key = $zoneName . '_modules';
	if (empty($moduleZones[$key])) {
		return true;
	}

	foreach ($moduleZones[$key] as $module) {
		$data = (string) (isset($module['data']) ? $module['data'] : '');
		if (! empty($data)) {
			return false;
		}
	}

	return true;
}

