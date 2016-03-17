<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class PreferencesLib
{
	private $data = array();
	private $usageArray;
	private $file = '';
	private $files = array();
	// Fake preferences controlled by the system
	private $system_modified = array( 'tiki_release', 'tiki_version_last_check');
	// prefs with system info etc
	private $system_info = array( 'fgal_use_dir', 'sender_email' );

	function getPreference( $name, $deps = true, $source = null, $get_pages = false )
	{
		global $prefs, $systemConfiguration;
		static $id = 0;
		$data = $this->loadData($name);

		if ( ! isset( $data[$name] ) ) {
			return false;
		}
		$defaults = array(
			'type' => '',
			'helpurl' => '',
			'help' => '',
			'dependencies' => array(),
			'extensions' => array(),
			'dbfeatures' => array(),
			'options' => array(),
			'description' => '',
			'size' => 40,
			'detail' => '',
			'warning' => '',
			'hint' => '',
			'shorthint' => '',
			'perspective' => true,
			'parameters' => array(),
			'admin' => '',
			'module' => '',
			'permission' => '',
			'plugin' => '',
			'view' => '',
			'public' => false,
		);
		if ($data[$name]['type'] === 'textarea') {
			$defaults['size'] = 10;
		}

		$info = array_merge($defaults, $data[$name]);

		if ( $source == null ) {
			$source = $prefs;
		}

		$value = isset($source[$name]) ? $source[$name] : null;
		if ( !empty($value) && is_string($value) && (strlen($value) > 1 && $value{1} == ':') && false !== $unserialized = @unserialize($value) ) {
			$value = $unserialized;
		}

		$info['preference'] = $name;
		if ( isset( $info['serialize'] ) ) {
			$fnc = $info['serialize'];
			$info['value'] = $fnc($value);
		} else {
			$info['value'] = $value;
		}

		if (! isset($info['tags'])) {
			$info['tags'] = array('advanced');
		}

		$info['tags'][] = $name;
		$info['tags'][] = 'all';

		$info['notes'] = array();

		$info['raw'] = isset($source[$name]) ? $source[$name] : null;
		$info['id'] = 'pref-' . ++$id;

		if ( !empty($info['help']) && isset($prefs['feature_help']) && $prefs['feature_help'] == 'y' ) {
			if ( preg_match('/^https?:/i', $info['help']) ) {
				$info['helpurl'] = $info['help'];
			} else {
				$info['helpurl'] = $prefs['helpurl'] . $info['help'];
			}
		}

		if ( $deps && isset( $info['dependencies'] ) ) {
			$info['dependencies'] = $this->getDependencies($info['dependencies']);
		}

		$info['available'] = true;

		if (! $this->checkExtensions($info['extensions']) ) {
			$info['available'] = false;
			$info['notes'][] = tr('Unmatched system requirement. Missing PHP extension among %0', implode(', ', $info['extensions']));
		}

		if (! $this->checkDatabaseFeatures($info['dbfeatures']) ) {
			$info['available'] = false;
			$info['notes'][] = tr('Unmatched system requirement. The database you are using does not support this feature.');
		}

		if (!isset($info['default'])) {	// missing default in prefs definition file?
			$info['modified'] = false;
			trigger_error(tr('Missing default for preference "%0"', $name), E_USER_WARNING);
		} else {
			$info['modified'] = str_replace("\r\n", "\n", $info['value']) != $info['default'];
		}

		if ($get_pages) {
			$info['pages'] = $this->getPreferenceLocations($name);
		}

		if ( isset( $systemConfiguration->preference->$name ) ) {
			$info['available'] = false;
			$info['notes'][] = tr('Configuration forced by host.');
		}

		if ( $this->preferenceDisabled($info['tags']) ) {
			$info['available'] = false;
			$info['notes'][] = tr('Disabled by host.');
		}

		if ( ! $info['available'] ) {
			$info['tags'][] = 'unavailable';
		}

		if ($info['modified'] && $info['available']) {
			$info['tags'][] = 'modified';
		}

		$info['tagstring'] = implode(' ', $info['tags']);

		$info = array_merge($defaults, $info);

		if (!empty($info['permission'])) {
			$info['permission']['show_disabled_features'] = 'y';
			$info['permission'] = 'tiki-objectpermissions.php?' . http_build_query($info['permission'], '', '&');
		}

		if (!empty($info['admin'])) {
			if (preg_match('/^\w+$/', $info['admin'])) {
				$info['admin'] = 'tiki-admin.php?page=' . urlencode($info['admin']);
			}
		}

		if (!empty($info['module'])) {
			$info['module'] = 'tiki-admin_modules.php?cookietab=3&textFilter=' . urlencode($info['module']);
		}

		if (!empty($info['plugin'])) {
			$info['plugin'] = 'tiki-admin.php?page=textarea&amp;cookietab=2&textFilter=' . urlencode($info['plugin']);
		}

		$smarty = TikiLib::lib('smarty');
		$smarty->loadPlugin('smarty_function_icon');

		if (!empty($info['admin']) || !empty($info['permission']) || !empty($info['view']) || !empty($info['module']) || !empty($info['plugin'])) {

			$info['popup_html'] = '<ul class="list-unstyled">';

			if (!empty($info['admin'])) {
				$icon = smarty_function_icon(array( 'name' => 'settings'), $smarty);
				$info['popup_html'] .= '<li><a class="icon" href="'.$info['admin'].'">' . $icon . ' ' . tra('Settings') .'</a></li>';
			}
			if (!empty($info['permission'])) {
				$icon = smarty_function_icon(array( 'name' => 'permission'), $smarty);
				$info['popup_html'] .= '<li><a class="icon" href="'.$info['permission'].'">' . $icon . ' ' . tra('Permissions').'</a></li>';
			}
			if (!empty($info['view'])) {
				$icon = smarty_function_icon(array( 'name' => 'view'), $smarty);
				$info['popup_html'] .= '<li><a class="icon" href="'.$info['view'].'">' . $icon . ' ' . tra('View').'</a></li>';
			}
			if (!empty($info['module'])) {
				$icon = smarty_function_icon(array( 'name' => 'module'), $smarty);
				$info['popup_html'] .= '<li><a class="icon" href="'.$info['module'].'">' . $icon . ' ' . tra('Modules').'</a></li>';
			}
			if (!empty($info['plugin'])) {
				$icon = smarty_function_icon(array( 'name' => 'plugin'), $smarty);
				$info['popup_html'] .= '<li><a class="icon" href="'.$info['plugin'].'">' . $icon . ' ' . tra('Plugins').'</a></li>';
			}
			$info['popup_html'] .= '</ul>';
		}

		if (isset($prefs['connect_feature']) && $prefs['connect_feature'] === 'y') {
			$connectlib = TikiLib::lib('connect');
			$currentVote = $connectlib->getVote($info['preference']);

			$info['voting_html'] = '';

			if (!in_array('like', $currentVote)) {
				$info['voting_html'] .= smarty_function_icon($this->getVoteIconParams($info['preference'], 'like', tra('Like')), $smarty);
			} else {
				$info['voting_html'] .= smarty_function_icon($this->getVoteIconParams($info['preference'], 'unlike', tra("Don't like")), $smarty);
			}
//				if (!in_array('fix', $currentVote)) {
//					$info['voting_html'] .= smarty_function_icon($this->getVoteIconParams($info['preference'], 'fix', tra('Fix me')), $smarty);
//				} else {
//					$info['voting_html'] .= smarty_function_icon($this->getVoteIconParams($info['preference'], 'unfix', tra("Don't fix me")), $smarty);
//				}
//				if (!in_array('wtf', $currentVote)) {
//					$info['voting_html'] .= smarty_function_icon($this->getVoteIconParams($info['preference'], 'wtf', tra("What's this for?")), $smarty);
//				} else {
//					$info['voting_html'] .= smarty_function_icon($this->getVoteIconParams($info['preference'], 'unwtf', tra("What's this for?")), $smarty);
//				}
		}

		if (! $info['available']) {
			$info['parameters']['disabled'] = 'disabled';
		}

		$info['params'] = '';
		if (!empty($info['parameters'])) {
			foreach ($info['parameters'] as $param => $value) {
				$info['params'] .= ' ' . $param . '="' . $value . '"';
			}
		}

		/**
		 * If the unified index is enabled, replace simple object selection preferences with object selectors
		 */
		if ($info['type'] == 'text' && ! empty($info['profile_reference']) && $prefs['feature_search'] == 'y') {
			$objectlib = TikiLib::lib('object');
			$type = $objectlib->getSelectorType($info['profile_reference']);

			if ($type) {
				$info['selector_type'] = $type;

				if (empty($info['separator'])) {
					$info['type'] = 'selector';
				} else {
					$info['type'] = 'multiselector';
				}
			}
		}

		return $info;
	}

	private function getVoteIconParams( $pref, $vote, $label )
	{
		$iconname = [
			'like' => 'thumbs-up',
			'unlike' => 'thumbs-down'
		];
		return array(
			'name' => $iconname[$vote],
			'title' => $label,
			'href' => '#', 'onclick' => 'connectVote(\'' . $pref . '\', \''. $vote .'\', this);return false;',
			'class' => '',
			'iclass' => 'icon connectVoter',
			'istyle' => 'display:none',
		);
	}

	private function preferenceDisabled($tags)
	{
		static $rules = null;

		if (is_null($rules)) {
			global $systemConfiguration;
			$rules = $systemConfiguration->rules->toArray();
			krsort($rules, SORT_NUMERIC);

			foreach ($rules as & $rule) {
				$parts = explode(' ', $rule);
				$type = array_shift($parts);
				$rule = array($type, $parts);
			}
		}


		foreach ($rules as $rule) {
			$intersect = array_intersect($rule[1], $tags);

			if (count($intersect)) {
				return $rule[0] == 'deny';
			}
		}

		return false;
	}

	private function checkExtensions( $extensions )
	{
		if (count($extensions) == 0) {
			return true;
		}

		$installed = get_loaded_extensions();

		foreach ( $extensions as $ext ) {
			if ( ! in_array($ext, $installed) ) {
				return false;
			}
		}

		return true;
	}

	private function checkDatabaseFeatures($features)
	{
		if (in_array('mysql_fulltext', $features)) {
			return TikiDb::get()->isMySQLFulltextSearchSupported();
		}

		return true;
	}

	function getMatchingPreferences( $criteria, $filters = null )
	{
		$index = $this->getIndex();

		$query = new Search_Query($criteria);
		if ($filters) {
			$this->buildPreferenceFilter($query, $filters);
		}
		$results = $query->search($index);

		$prefs = array();
		foreach ( $results as $hit ) {
			$prefs[] = $hit['object_id'];
		}

		return $prefs;
	}

	function applyChanges( $handled, $data, $limitation = null )
	{
		global $user_overrider_prefs;
		$tikilib = TikiLib::lib('tiki');

		if ( is_array($limitation) ) {
			$handled = array_intersect($handled, $limitation);
		}

		$resets = isset( $data['lm_reset'] ) ? (array) $data['lm_reset'] : array();

		$changes = array();
		foreach ( $handled as $pref ) {
			if ( in_array($pref, $resets) ) {
				$tikilib->delete_preference($pref);
				$changes[$pref] = array('type'=> 'reset');
			} else {
				$value = $this->formatPreference($pref, $data);
				$realPref = in_array($pref, $user_overrider_prefs)? "site_$pref": $pref;
				$old = $this->formatPreference($pref, array($pref => $tikilib->get_preference($realPref)));
				if ( $old != $value ) {
					if ($tikilib->set_preference($pref, $value)) {
						$changes[$pref] = array('type'=> 'changed', 'new'=> $value, 'old' => $old);
					}
				}
			}
		}

		return $changes;
	}

	function formatPreference( $pref, $data )
	{
		if ( false !== $info = $this->getPreference($pref) ) {
			$function = '_get' . ucfirst($info['type']) . 'Value';
			$value = $this->$function($info, $data);
			return $value;
		} else {
			if (isset($data[$pref]))
				return $data[$pref];
			return null;
		}
	}

	function getInput( JitFilter $filter, $preferences = array(), $environment = '' )
	{
		$out = array();

		foreach ( $preferences as $name ) {
			$info = $this->getPreference($name);

			if ( $environment == 'perspective' && isset( $info['perspective'] ) && $info['perspective'] === false ) {
				continue;
			}

			if ( isset( $info['filter'] ) ) {
				$filter->replaceFilter($name, $info['filter']);
			}

			if ( isset( $info['separator'] ) ) {
				$out[ $name ] = $filter->asArray($name, $info['separator']);
			} else {
				$out[ $name ] = $filter[$name];
			}
		}

		return $out;
	}

	function getExtraSortColumns()
	{
		global $prefs;
		if ( isset($prefs['rating_advanced']) && $prefs['rating_advanced'] == 'y' ) {
			return TikiDb::get()->fetchMap("SELECT CONCAT('adv_rating_', ratingConfigId), name FROM tiki_rating_configs");
		} else {
			return array();
		}
	}

	private function loadData( $name )
	{
		if (in_array($name, $this->system_modified)) return null;
		if ( substr($name, 0, 3) == 'ta_' ) {
			$midpos = strpos($name, '_', 3);
			$pos = strpos($name, '_', $midpos + 1);
			$file = substr($name, 0, $pos);
		} elseif ( false !== $pos = strpos($name, '_') ) {
			$file = substr($name, 0, $pos);
		} elseif ( file_exists(__DIR__ . "/prefs/{$name}.php") ) {
			$file = $name;
		} else {
			$file = 'global';
		}

		return $this->getFileData($file);
	}

	private function getFileData( $file, $partial = false )
	{
		if ( ! isset( $this->files[$file] ) ) {
   			$this->realLoad($file, $partial);
		}

		$ret = array();
		if (isset($this->files[$file])) {
			$ret = $this->files[$file];
		}

		if ($partial) {
			unset($this->files[$file]);
		}

		return $ret;
	}

	private function realLoad($file, $partial)
	{
		$inc_file = __DIR__ . "/prefs/{$file}.php";
		if (substr($file, 0, 3) == "ta_") {
			$paths = TikiAddons::getPaths();
			$package = str_replace('_', '/', substr($file, 3));
			$inc_file = $paths[$package] .  "/prefs/{$file}.php";
		}
		if (file_exists($inc_file)) {
			require_once $inc_file;
			$function = "prefs_{$file}_list";
			if ( function_exists($function) ) {
				$this->files[$file] = $function($partial);
			} else {
				$this->files[$file] = array();
			}
		}
	}

	private function getDependencies( $dependencies )
	{
		$out = array();

		foreach ( (array) $dependencies as $key => $dep ) {
			$info = $this->getPreference($dep, false);
			if ( $info ) {
				$out[] = array(
					'name' => $dep,
					'label' => $info['name'],
					'type' => $info['type'],
					'link' => 'tiki-admin.php?lm_criteria=' . urlencode($info['name']),
					'met' =>
						( $info['type'] == 'flag' && $info['value'] == 'y' )
						|| ( $info['type'] != 'flag' && ! empty( $info['value'] ) )
				);
			} elseif ($key == 'profiles') {
				foreach ( (array) $dep as $profile) {
					$out[] = array(
						'name' => $profile,
						'label' => $profile,
						'type' => 'profile',
						'link' => 'tiki-admin.php?page=profiles&list=List&profile=' . urlencode($profile),
						'met' =>
						( $info['type'] == 'flag' && $info['value'] == 'y' )
							|| ( $info['type'] != 'flag' && ! empty( $info['value'] ) )
					);
				}
			}
		}

		return $out;
	}

	public function rebuildIndex()
	{
		$index = TikiLib::lib('unifiedsearch')->getIndex('preference');
		$index->destroy();

		$typeFactory = $index->getTypeFactory();

		foreach ($this->getAvailableFiles() as $file) {
			$data = $this->getFileData($file);

			foreach ( $data as $pref => $info ) {
				$info = $this->getPreference($pref);
				$doc = $this->indexPreference($typeFactory, $pref, $info);
				$index->addDocument($doc);
			}
		}

		return $index;
	}

	private function getIndex()
	{
		$index = TikiLib::lib('unifiedsearch')->getIndex('preference');

		if (! $index->exists()) {
			$index = null;
			return $this->rebuildIndex();
		}

		return $index;
	}

	function indexNeedsRebuilding()
	{
		$index = TikiLib::lib('unifiedsearch')->getIndex('preference');
		return ! $index->exists();
	}

	public function getPreferenceLocations( $name )
	{
		if ( ! $this->usageArray ) {
			$this->loadPreferenceLocations();
		}

		$pages = array();
		foreach ($this->usageArray as $pg => $pfs) {
			foreach ($pfs as $pf) {
				if ($pf[0] == $name) {
					$pages[] = array($pg, $pf[1]);
				}
			}
		}

		if (strpos($name, 'wikiplugin_') === 0 || strpos($name, 'wikiplugininline_') === 0) {
			$pages[] = array('textarea', 2);	// plugins are included in textarea admin dynamically
		}
		if (strpos($name, 'trackerfield_') === 0) {
			$pages[] = array('trackers', 3);	// trackerfields are also included in tracker admin dynamically
		}

		return $pages;
	}

	private function loadPreferenceLocations()
	{
		// check for or create array of where each pref is used
		$file = 'temp/cache/preference-usage-index';
		if ( !file_exists($file) ) {
			$prefs_usage_array = array();
			$fp = opendir('templates/admin/');

			while (false !== ($f = readdir($fp))) {
				preg_match('/^include_(.*)\.tpl$/', $f, $m);
				if (count($m) > 0) {
					$page = $m[1];
					$c = file_get_contents('templates/admin/'.$f);
					preg_match_all('/{preference.*name=[\'"]?(\w*)[\'"]?.*}/i', $c, $m2, PREG_OFFSET_CAPTURE);
					if (count($m2[1]) > 0) {
						// count number of tabs in front of each found pref
						foreach ( $m2[1] as & $found) {
							$tabs = preg_match_all('/{\/tab}/i', substr($c, 0, $found[1]), $m3);
							if ($tabs === false) {
								$tabs = 0;
							} else {
								$tabs++;
							}
							$found[1] = $tabs;	// replace char offset with tab number
						}
						$prefs_usage_array[$page] = $m2[1];
					}
				}
			}
			file_put_contents($file, serialize($prefs_usage_array));

		} else {
			$prefs_usage_array = unserialize(file_get_contents($file));
		}

		$this->usageArray = $prefs_usage_array;
	}

	private function indexPreference( $typeFactory, $pref, $info )
	{
		$contents = array(
			$info['preference'],
			$info['name'],
			isset($info['description']) ? $info['description'] : '',
			isset($info['keywords']) ? $info['keywords'] : '',
		);

		if (isset($info['options'])) {
			$contents = array_merge($contents, $info['options']);
		}

		return array(
			'object_type' => $typeFactory->identifier('preference'),
			'object_id' => $typeFactory->identifier($pref),
			'contents' => $typeFactory->plaintext(implode(' ', $contents)),
			'tags' => $typeFactory->plaintext(implode(' ', $info['tags'])),
		);
	}

	private function _getFlagValue( $info, $data )
	{
		$name = $info['preference'];
		if(isset( $data[$name] )&& !empty($data[$name]) && $data[$name] != 'n') {
			$ret = 'y';
		} else {
			$ret = 'n';
		}

		return $ret;
	}

	private function _getSelectorValue( $info, $data )
	{
		$name = $info['preference'];
		if (! empty($data[$name])) {
			$value = $data[$name];

			if ( isset($info['filter']) && $filter = TikiFilter::get($info['filter']) ) {
				return $filter->filter($value);
			} else {
				return $value;
			}
		}
	}

	private function _getMultiselectorValue( $info, $data )
	{
		$name = $info['preference'];

		if (isset($data[$name])) {
			if (! is_array($data[$name])) {
				$value = explode($info['separator'], $data[$name]);
			} else {
				$value = $data[$name];
			}
		} else {
			$value = array();
		}

		if (isset($info['filter']) && $filter = TikiFilter::get($info['filter'])) {
			return array_map(array( $filter, 'filter' ), $value);
		} else {
			return $value;
		}
	}

	private function _getTextValue( $info, $data )
	{
		$name = $info['preference'];

		if ( isset($info['separator']) && is_string($data[$name])) {
			if(!empty($data[$name])) { $value = explode($info['separator'], $data[$name]); } else { $value = array(); }
		} else {
			$value = $data[$name];
		}

		if ( isset($info['filter']) && $filter = TikiFilter::get($info['filter']) ) {
			if ( is_array($value) ) {
				return array_map(array( $filter, 'filter' ), $value);
			} else {
				return $filter->filter($value);
			}
		} else {
			return $value;
		}
	}

	private function _getPasswordValue( $info, $data )
	{
		$name = $info['preference'];

		if ( isset($info['filter']) && $filter = TikiFilter::get($info['filter']) ) {
			return $filter->filter($data[$name]);
		} else {
			return $data[$name];
		}
	}

	private function _getTextareaValue( $info, $data )
	{
		$name = $info['preference'];

		if ( isset($info['filter']) && $filter = TikiFilter::get($info['filter']) ) {
			$value = $filter->filter($data[$name]);
		} else {
			$value = $data[$name];
		}
		$value = str_replace("\r", "", $value);

		if ( isset( $info['unserialize'] ) ) {
			$fnc = $info['unserialize'];

			return $fnc($value);
		} else {
			return $value;
		}
	}

	private function _getListValue( $info, $data )
	{
		$name = $info['preference'];
		$value = isset ($data[$name]) ? $data[$name]: null;

		$options = $info['options'];

		if ( isset( $options[$value] ) ) {
			return $value;
		} else {
			$keys = array_keys($options);
			return reset($keys);
		}
	}

	private function _getMultilistValue( $info, $data )
	{
		$name = $info['preference'];
		$value = isset($data[$name])? (array) $data[$name] : array();

		$options = $info['options'];
		$options = array_keys($options);

		return array_intersect($value, $options);
	}

	private function _getRadioValue( $info, $data )
	{
		$name = $info['preference'];
		$value = isset($data[$name]) ? $data[$name]: null;

		$options = $info['options'];
		$options = array_keys($options);

		if (in_array($value, $options)) {
			return $value;
		} else {
			return '';
		}
	}

	private function _getMulticheckboxValue( $info, $data )
	{
		return $this->_getMultilistValue($info, $data);
	}

	// for export as yaml

	/**
	 * @global TikiLib $tikilib
	 * @param bool $added shows current prefs not in defaults
	 * @return array (prefname => array( 'current' => current value, 'default' => default value ))
	 */
	// NOTE: tikilib contains a similar method called getModifiedPreferences
	function getModifiedPrefsForExport( $added = false )
	{
		$tikilib = TikiLib::lib('tiki');

		$prefs = $tikilib->getModifiedPreferences();

		$defaults = get_default_prefs();
		$modified = array();

		foreach ($prefs as $pref => $value) {
			if (( $added && !isset($defaults[$pref])) || (isset($defaults[$pref]) && $value !== $defaults[$pref] )) {
				if (!in_array($pref, $this->system_modified) && !in_array($pref, $this->system_info)) {	// prefs modified by the system and with system info etc
					$preferenceInformation = $this->getPreference($pref);
					$modified[$pref] = array(
						'current' => array('serial' => $value, 'expanded' => $preferenceInformation['value']),
					);
					if (isset($defaults[$pref])) {
						$modified[$pref]['default'] = $defaults[$pref];
					}
				}
			}
		}
		ksort($modified);

		return $modified;
	}

	function getDefaults()
	{
		$defaults = array();

		foreach ($this->getAvailableFiles() as $file) {
			$data = $this->getFileData($file, true);

			foreach ($data as $name => $info) {
				if (isset($info['default'])) {
					$defaults[$name] = $info['default'];
				} else {
					$defaults[$name] = '';
				}
			}
		}

		return $defaults;
	}

	private function getAvailableFiles()
	{
		$files = array();
		foreach ( glob(__DIR__ . '/prefs/*.php') as $file ) {
			if (basename($file) === "index.php")
				continue;
			$files[] = substr(basename($file), 0, -4);
		}
		foreach (TikiAddons::getPaths() as $path) {
			foreach ( glob( $path . '/prefs/*.php') as $file ) {
				if (basename($file) === "index.php")
					continue;
				$files[] = substr(basename($file), 0, -4);
			}
		}
		return $files;
	}

	function setFilters($tags)
	{
		global $user;
		$tikilib = TikiLib::lib('tiki');
		$tikilib->set_user_preference($user, 'pref_filters', implode(',', $tags));
	}

	private function getEnabledFilters()
	{
		global $user;
		$tikilib = TikiLib::lib('tiki');
		$filters = $tikilib->get_user_preference($user, 'pref_filters', 'basic');
		$filters = explode(',', $filters);
		return $filters;
	}

	function getFilters($filters = null)
	{
		if (! $filters) {
			$filters = $this->getEnabledFilters();
		}

		$out = array(
			'basic' => array(
				'label' => tra('Basic'),
				'type' => 'positive',
			),
			'advanced' => array(
				'label' => tra('Advanced'),
				'type' => 'positive',
			),
			'experimental' => array(
				'label' => tra('Experimental'),
				'type' => 'negative',
			),
			'unavailable' => array(
				'label' => tra('Unavailable'),
				'type' => 'negative',
			),
		);

		foreach ($out as $key => & $info) {
			$info['selected'] = in_array($key, $filters);
		}

		return $out;
	}

	private function buildPreferenceFilter($query, $input = null)
	{
		$filters = $this->getFilters($input);

		foreach ($filters as $tag => $info) {
			if ($info['selected']) {
				$positive[] = $tag;
			} elseif ($info['type'] == 'negative') {
				$query->filterContent("NOT $tag", 'tags');
			}
		}

		if (count($positive)) {
			$query->filterContent(implode(' OR ', $positive), 'tags');
		}

		return $query;
	}

	/***
	 * Store 10 most recently changed prefs for quickadmin module menu
	 *
	 * @param $name			preference name
	 * @param null $auser	optional user
	 */

	public function addRecent($name, $auser = null)
	{
		global $user;

		if (!$auser) {
			$auser = $user;
		}

		$list = (array) $this->getRecent($auser);
		array_unshift($list, $name);
		$list = array_unique($list);
		$list = array_slice($list, 0, 10);

		TikiLib::lib('tiki')->set_user_preference($auser, 'admin_recent_prefs', serialize($list));
	}

	/***
	 * Get recent prefs list
	 *
	 * @param null $auser	option user
	 * @return array		array of pref names
	 */

	public function getRecent($auser = null)
	{
		global $user;
		$tikilib = TikiLib::lib('tiki');

		if (!$auser) {
			$auser = $user;
		}

		$recent = $tikilib->get_user_preference($auser, 'admin_recent_prefs');

		if (empty($recent)) {
			return array();
		} else {
			return unserialize($recent);
		}
	}

	public function exportPreference(Tiki_Profile_Writer $writer, $preferenceName)
	{
		global $prefs;

		if ($info = $this->getPreference($preferenceName)) {
			if (isset($info['profile_reference'])) {
				$writer->setPreference($preferenceName, $writer->getReference($info['profile_reference'], $info['value']));

				return true;
			} else {
				$writer->setPreference($preferenceName, $info['value']);
				return true;
			}
		}

		return false;
	}

	public function getAddonPrefs()
	{
		global $prefs;
		$ret = array();
		foreach (array_keys($prefs) as $prefName) {
			if (substr($prefName, 0, 3) == 'ta_' && substr($prefName, -3) == '_on') {
				$ret[] = $prefName;
			}
		}
		return $ret;
	}
}

