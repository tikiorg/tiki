<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class PreferencesLib
{
	private $data = array();
	private $usageArray;
	private $file = '';
	
	function PreferencesLib() {
		global $prefs;
		
		$this->file = 'temp/preference-index-' . $prefs['language'];
	}

	function getPreference( $name, $deps = true, $source = null, $get_pages = false ) {
		global $prefs;
		static $id = 0;
		$data = $this->loadData( $name );

		if( isset( $data[$name] ) ) {
			$defaults = array(
				'type' => '',
				'helpurl' => '',
				'help' => '',
				'dependencies' => array(),
				'extensions' => array(),
				'options' => array(),
				'description' => '',
				'size' => 40,
				'detail' => '',
				'warning' => '',
				'hint' => '',
				'shorthint' => '',
				'perspective' => true,
				'parameters' => array(),
			);
			$info = $data[$name];

			if( $source == null ) {
				$source = $prefs;
			}
		
			$value = $source[$name];
			if( !empty($value) && is_string( $value ) && ($value{0} == ':' || (strlen($value) > 1 && $value{1} == ':')) && false !== $unserialized = unserialize( $value ) ) {
				$value = $unserialized;
			}

			$info['preference'] = $name;
			if( isset( $info['serialize'] ) ) {
				$fnc = $info['serialize'];
				$info['value'] = $fnc( $value );
			} else {
				$info['value'] = $value;
			}

			$info['raw'] = $source[$name];
			$info['id'] = 'pref-' . ++$id;

			if( isset( $info['help'] ) && $prefs['feature_help'] == 'y' ) {
				if( preg_match('/^https?:/i', $info['help']) ) {
					$info['helpurl'] = $info['help'];
				} else {
					$info['helpurl'] = $prefs['helpurl'] . $info['help'];
				}
			}

			if( $deps && isset( $info['dependencies'] ) ) {
				$info['dependencies'] = $this->getDependencies( $info['dependencies'] );
			}

			$info['params'] = '';
			if (!empty($info['parameters'])) {
				foreach ($info['parameters'] as $param => $value) {
					$info['params'] .= ' ' . $param . '="' . $value . '"';
				}
			}

			$info['available'] = true;

			if( isset( $info['extensions'] ) ) {
				$info['available'] = $this->checkExtensions( $info['extensions'] );
			}
			$defprefs = get_default_prefs();
			$info['default_val'] = $defprefs[$name];
			
			if ($get_pages) {
				$info['pages'] = $this->getPreferenceLocations( $name );
			}
			
			$info = array_merge($defaults, $info);

			return $info;
		}

		return false;
	}

	private function checkExtensions( $extensions ) {
		$installed = get_loaded_extensions();

		foreach( $extensions as $ext ) {
			if( ! in_array( $ext, $installed ) ) {
				return false;
			}
		}

		return true;
	}

	function getMatchingPreferences( $criteria ) {
		$index = $this->getIndex();

		$results = $index->find( $criteria );

		$prefs = array();
		foreach( $results as $hit ) {
			$prefs[] = $hit->preference;
		}

		return $prefs;
	}

	function applyChanges( $handled, $data, $limitation = null ) {
		global $tikilib, $user_overrider_prefs;

		if( is_array( $limitation ) ) {
			$handled = array_intersect( $handled, $limitation );
		}

		$resets = isset( $data['lm_reset'] ) ? (array) $data['lm_reset'] : array();

		$changes = array();
		foreach( $handled as $pref ) {
			if( in_array( $pref, $resets ) ) {
				$defaults = get_default_prefs();
				$value = $defaults[$pref];
			} else {
				$value = $this->formatPreference( $pref, $data );
			}
			$realPref = in_array($pref, $user_overrider_prefs)? "site_$pref": $pref;

			if( ($old = $tikilib->get_preference( $realPref ) ) != $value ) {
				$tikilib->set_preference( $pref, $value );
				$changes[$pref] = array('new'=> $value, 'old' => $old);
			}
		}

		return $changes;
	}

	function formatPreference( $pref, $data ) {
		if( false !== $info = $this->getPreference( $pref ) ) {
			$function = '_get' . ucfirst( $info['type'] ) . 'Value';
			$value = $this->$function( $info, $data );
			return $value;
		} else {
			return $data[$pref];
		}
	}
	
	function getInput( JitFilter $filter, $preferences = array(), $environment = '' ) {
		$out = array();

		foreach( $preferences as $name ) {
			$info = $this->getPreference( $name );

			if( $environment == 'perspective' && isset( $info['perspective'] ) && $info['perspective'] === false ) {
				continue;
			}
			
			if( isset( $info['filter'] ) ) {
				$filter->replaceFilter( $name, $info['filter'] );
			}

			if( isset( $info['separator'] ) ) {
				$out[ $name ] = $filter->asArray( $name, $info['separator'] );
			} else {
				$out[ $name ] = $filter[$name];
			}
		}

		return $out;
	}

	function getExtraSortColumns() {
		global $prefs;
		if( $prefs['rating_advanced'] == 'y' ) {
			return TikiDb::get()->fetchMap( "SELECT CONCAT('adv_rating_', ratingConfigId), name FROM tiki_rating_configs" );
		} else {
			return array();
		}
	}

	private function loadData( $name ) {
		if( false !== $pos = strpos( $name, '_' ) ) {
			$file = substr( $name, 0, $pos );
		} else {
			$file = 'global';
		}

		return $this->getFileData( $file );
	}

	private function getFileData( $file ) {
		if( ! isset( $this->files[$file] ) ) {
			require_once 'lib/prefs/' . $file . '.php';
			$function = "prefs_{$file}_list";
			if( function_exists( $function ) ) {
				$this->files[$file] = $function();
			} else {
				$this->files[$file] = array();
			}
		}

		return $this->files[$file];
	}

	private function getDependencies( $dependencies ) {
		$out = array();

		foreach( (array) $dependencies as $dep ) {
			$info = $this->getPreference( $dep, false );
			if( $info ) {
				$out[] = array(
					'name' => $dep,
					'label' => $info['name'],
					'type' => $info['type'],
					'link' => 'tiki-admin.php?lm_criteria=' . urlencode($info['name']),
					'met' =>
						( $info['type'] == 'flag' && $info['value'] == 'y' )
						|| ( $info['type'] != 'flag' && ! empty( $info['value'] ) )
				);
			}
		}

		return $out;
	}

	private function getIndex() {
		global $prefs;
		if( $prefs['language'] == 'en' ) {
			Zend_Search_Lucene_Analysis_Analyzer::setDefault(
				new StandardAnalyzer_Analyzer_Standard_English() );
		}

		if( $this->indexNeedsRebuilding() ) {
			$index = Zend_Search_Lucene::create( $this->file );

			foreach( glob( 'lib/prefs/*.php' ) as $file ) {
				$file = substr( basename( $file ), 0, -4 );
				$data = $this->getFileData( $file );

				foreach( $data as $pref => $info ) {
					$doc = $this->indexPreference( $pref, $info );
					$index->addDocument( $doc );
				}
			}

			$index->optimize();
			return $index;
		}

		return Zend_Search_Lucene::open( $this->file );
	}
	
	public function indexNeedsRebuilding() {
		return !file_exists( $this->file );
	}

	public function getPreferenceLocations( $name ) {
		if( ! $this->usageArray ) {
			$this->loadPreferenceLocations();
		}

		$pages = array();
		foreach($this->usageArray as $pg => $pfs) {
			foreach ($pfs as $pf) {
				if ($pf[0] == $name) {
					$pages[] = array($pg, $pf[1]);
				}
			}
		}

		if (count($pages) == 0 && strpos($name, 'plugin') !== false) {
			$pages[] = array('textarea', 0);	// plugins are included in textarea admin dynamically
		}

		return $pages;
	}

	private function loadPreferenceLocations() {
		// check for or create array of where each pref is used
		$file = 'temp/cache/preference-usage-index';
		if ( !file_exists( $file ) ) {
			$prefs_usage_array = array();
			$fp = opendir('templates/');
			
			while(false !== ($f = readdir($fp))) {
				preg_match('/^tiki-admin_include_(.*)\.tpl$/', $f, $m);
				if (count($m) > 0) {
					$page = $m[1];
					$c = file_get_contents('templates/'.$f);
					preg_match_all('/{preference.*name=[\'"]?(\w*)[\'"]?.*}/i', $c, $m2, PREG_OFFSET_CAPTURE);
					if (count($m2[1]) > 0) {
						// count number of tabs in front of each found pref
						foreach( $m2[1] as & $found) {
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

	private function indexPreference( $pref, $info ) {
		$doc = new Zend_Search_Lucene_Document();
		$doc->addField( Zend_Search_Lucene_Field::UnIndexed('preference', $pref) );
		$doc->addField( Zend_Search_Lucene_Field::Text('name', $info['name']) );
		if (!empty($info['description'])) {
			$doc->addField( Zend_Search_Lucene_Field::Text('description', $info['description']) );
		}
		if (!empty($info['keywords'])) {
			$doc->addField( Zend_Search_Lucene_Field::Text('keywords', $info['keywords']) );
		}

		if( isset( $info['options'] ) ) {
			$doc->addField( Zend_Search_Lucene_Field::Text('options', implode( ' ', $info['options'] ) ) );
		}

		return $doc;
	}

	private function _getFlagValue( $info, $data ) {
		$name = $info['preference'];

		return isset( $data[$name] ) ? 'y' : 'n';
	}

	private function _getTextValue( $info, $data ) {
		$name = $info['preference'];

		if( isset($info['separator']) && is_string( $data[$name] )) {
			$value = explode( $info['separator'], $data[$name] );
		} else {
			$value = $data[$name];
		}

		if( isset($info['filter']) && $filter = TikiFilter::get( $info['filter'] ) ) {
			if( is_array( $value ) ) {
				return array_map( array( $filter, 'filter' ), $value );
			} else {
				return $filter->filter( $value );
			}
		} else {
			return $value;
		}
	}

	private function _getPasswordValue( $info, $data ) {
		$name = $info['preference'];

		if( isset($info['filter']) && $filter = TikiFilter::get( $info['filter'] ) ) {
			return $filter->filter( $data[$name] );
		} else {
			return $data[$name];
		}
	}

	private function _getTextareaValue( $info, $data ) {
		$name = $info['preference'];

		if( isset($info['filter']) && $filter = TikiFilter::get( $info['filter'] ) ) {
			$value = $filter->filter( $data[$name] );
		} else {
			$value = $data[$name];
		}

		if( isset( $info['unserialize'] ) ) {
			$fnc = $info['unserialize'];

			return $fnc( $value );
		} else {
			return $value;
		}
	}

	private function _getListValue( $info, $data ) {
		$name = $info['preference'];
		$value = $data[$name];

		$options = $info['options'];

		if( isset( $options[$value] ) ) {
			return $value;
		} else {
			return reset( array_keys( $options ) );
		}
	}

	private function _getMultilistValue( $info, $data ) {
		$name = $info['preference'];
		$value = (array) $data[$name];

		$options = $info['options'];
		$options = array_keys( $options );

		return array_intersect( $value, $options );
	}
	private function _getRadioValue( $info, $data ) {
		$name = $info['preference'];
		$value = isset($data[$name]) ? $data[$name]: null;

		$options = $info['options'];
		$options = array_keys( $options );

		if (in_array($value, $options)) {
			return $value;
		} else {
			return '';
		}
	}

	private function _getMulticheckboxValue( $info, $data ) {
		return $this->_getMultilistValue( $info, $data );
	}

	// for export as yaml for tiki 7

	/**
	 * @global TikiLib $tikilib
	 * @param bool $added shows current prefs not in defaults
	 * @return array (prefname => array( 'cur' => current value, 'def' => default value ))
	 */
	function getModifiedPreferences( $added = false ) {
		global $tikilib;

		$prefsTable = $tikilib->table('tiki_preferences');	// get prefs direct from db
		$res = $prefsTable->fetchAll( $prefsTable->all(), array() );
		$prefs = array();

		foreach ($res as $row) {
			$prefs[$row['name']] = $row['value'];
		}

		$defaults = get_default_prefs();
		$modified = array();

		foreach($prefs as $pref => $val) {
			if (( $added && !isset($defaults[$pref])) || (isset($defaults[$pref]) && $val !== $defaults[$pref] )) {
				if (!in_array($pref, array( 'tiki_release', 'tiki_version_last_check', 'lastUpdatePrefs',
											'case_patched' ))) {	// prefs modified by the system etc
					
					if (!in_array($pref, array( 'fgal_use_dir', 'sender_email' ))) {	// prefs with system info etc
						$modified[$pref] = array(
							'cur' => $prefs[$pref],
						);
						if (isset($defaults[$pref])) {
							$modified[$pref]['def'] = $defaults[$pref];
						}
					}
				}
			}
		}
		ksort($modified);
		
		return $modified;
	}

}

global $prefslib;
$prefslib = new PreferencesLib();
