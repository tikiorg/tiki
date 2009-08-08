<?php

class PreferencesLib
{
	private $data = array();

	function getPreference( $name ) {
		static $id = 0;
		$data = $this->loadData( $name );

		if( isset( $data[$name] ) ) {
			$info = $data[$name];

			global $prefs;
			$info['preference'] = $name;
			$info['value'] = $prefs[$name];
			$info['id'] = 'pref-' . ++$id;
			if( isset( $info['help'] ) && $prefs['feature_help'] == 'y' ) {
				$info['helpurl'] = $prefs['helpurl'] . $info['help'];
			}

			return $info;
		}

		return false;
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
		global $tikilib;

		if( is_array( $limitation ) ) {
			$handled = array_intersect( $handled, $limitation );
		}

		foreach( $handled as $pref ) {
			$info = $this->getPreference( $pref );
			$function = '_get' . ucfirst( $info['type'] ) . 'Value';
			$value = $this->$function( $info, $data );

			$tikilib->set_preference( $pref, $value );
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

	private function getIndex() {
		global $prefs;
		$file = 'temp/cache/preference-index-' . $prefs['language'];

		require_once 'Zend/Search/Lucene.php';
		if( ! file_exists( $file ) ) {
			$index = Zend_Search_Lucene::create( $file );

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

		return Zend_Search_Lucene::open( $file );
	}

	private function indexPreference( $pref, $info ) {
		$doc = new Zend_Search_Lucene_Document();
		$doc->addField( Zend_Search_Lucene_Field::UnIndexed('preference', $pref) );
		$doc->addField( Zend_Search_Lucene_Field::Text('name', $info['name']) );
		$doc->addField( Zend_Search_Lucene_Field::Text('description', $info['description']) );

		return $doc;
	}

	private function _getFlagValue( $info, $data ) {
		$name = $info['preference'];

		return isset( $data[$name] ) ? 'y' : 'n';
	}
}

global $prefslib;
$prefslib = new PreferencesLib;

?>
