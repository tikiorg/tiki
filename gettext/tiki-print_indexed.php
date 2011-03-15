<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'tiki-setup.php';
require_once 'lib/categories/categlib.php';

$access->check_feature('feature_print_indexed');

$inputConfiguration = array(
	array( 'staticKeyFilters' => array(
		'list' => 'alpha',
	) ),
	array( 'staticKeyFiltersForArrays' => array(
		'languages' => 'alpha',
		'categId' => 'digits',
	) ),
	array( 'catchAllUnset' => null ),
);

if( ! isset($_GET['list']) || ! in_array( $_GET['list'], array('categorylist', 'glossary') ) ) {
	$access->display_error( 'tiki-print_indexed.php', tra('Missing object list type argument') );
}


// Classes to be extracted at some later point {{{
class ObjectList // {{{
{
	private $lastIndex = 0;
	private $customIndexes = array();
	private $renderers = array();

	function addCustomIndex( $indexKey ) {
		$this->customIndexes[ $indexKey ] = array();
	}

	function add( $type, $object, $options ) {
		if( ! isset( $dataIndex[$type] ) ) {
			$this->dataIndex[$type] = array();
		}
		
		switch( $type ) {
		case 'wiki page':
			if( array_key_exists( 'languages', $options ) ) {
				$renderer = new ObjectRenderer_MultilingualWiki( $type, $object, $options );
			} else {
				$renderer = new ObjectRenderer_Wiki( $type, $object, $options );
			}
			
			break;

		default:
			$renderer = new ObjectRenderer_TrackerItem( $type, $object, $options );
			break;
		}

		if( $renderer && $renderer->isValid() ) {
			$index = ++$this->lastIndex;
			$this->renderers[$index] = $renderer;
			
			foreach( $this->customIndexes as $key => & $data ) {
				if( $prop = $renderer->getIndexValue($key) ) {
					$prop = strtolower( $prop );

					if( isset( $data[$prop] ) ) {
						$data[$prop][] = $index;
					} else {
						$data[$prop] = array( $index );
					}
				}
			}
		}
	}

	function finalize() {
		foreach( $this->customIndexes as & $data ) {
			ksort($data);
		}
	}

	function render( $smarty, $key, $options ) {
		if( is_null( $key ) ) {
			foreach( $this->renderers as $index => $renderer ) {
				$smarty->assign( 'index', $index );

				$renderer->render( $smarty, $options );
			}
		} else {
			foreach( $this->customIndexes[$key] as $indexes ) {
				foreach( $indexes as $index ) {
					$renderer = $this->renderers[$index];
					$smarty->assign( 'index', $index );

					$renderer->render( $smarty, $options );
				}
			}
		}
	}
} // }}}

abstract class ObjectRenderer // {{{
{
	protected $objectType;
	protected $objectId;

	function __construct( $objectType, $objectId ) {
		$this->objectType = $objectType;
		$this->objectId = $objectId;
	}

	function render( $smarty, $options ) {
		$options['decorator_template'] = 'print/print-decorator_' . $options['decorator'] . '.tpl';
		$smarty->assign( 'body', $this->_render( $smarty, $options ) );
		$smarty->display( $options['decorator_template'] );
	}

	function isValid() {
		return true;
	}

	abstract function _render( $smarty, $template );

	abstract function getIndexValue( $key );
} // }}}

class ObjectRenderer_TrackerItem extends ObjectRenderer // {{{
{
	private static $trackers = array();
	private $valid = false;
	private $tracker;
	private $info;

	function __construct( $type, $object, $options = array() ) {
		parent::__construct( $type, $object, $options );
		global $trklib;

		require_once 'lib/trackers/trackerlib.php';

		$info = $trklib->get_tracker_item( $object );
		$trackerId = $info['trackerId'];

		if( ! isset( self::$trackers[$trackerId] ) ) {
			if( self::$trackers[$trackerId] = $trklib->get_tracker( $trackerId ) ) {
				$fields = $trklib->list_tracker_fields( $trackerId );
				
				self::$trackers[$trackerId]['fields'] = $fields['data'];
			} else {
				$this->valid = false;
				return;
			}
		}

		$this->tracker = self::$trackers[ $info['trackerId'] ];
		$this->info = $info;
		$this->valid = ( $type == $this->tracker['name'] );

		foreach( $this->tracker['fields'] as & $field ) {
			$field['value'] = $this->info[ $field['fieldId'] ];
		}
	}

	function isValid() {
		return $this->valid;
	}

	function _render( $smarty, $options ) {
		$smarty->assign( 'title', $this->getTitle() );
		$smarty->assign( 'tracker', $this->tracker );
		$smarty->assign( 'item', $this->info );

		$options['display_template'] = 'print/print-' . $options['display'] . '_trackeritem.tpl';
		return $smarty->fetch( $options['display_template'] );
	}

	function getIndexValue($key) {
		switch( $key ) {
		case 'title':
			return $this->getTitle();
		}
	}

	function getTitle() {
		foreach( $this->tracker['fields'] as $field ) {
			if( $field['isMain'] == 'y' ) {
				return $field['value'];
			}
		}
	}
} // }}}

class ObjectRenderer_Wiki extends ObjectRenderer // {{{
{
	private $info;

	function __construct( $objectType, $objectId ) {
		parent::__construct( $objectType, $objectId );
		global $tikilib;

		$info = $tikilib->get_page_info( $objectId );

		$info['parsed'] = $tikilib->parse_data( $info['data'], array(
			'is_html' => $info['is_html'],
			'print' => 'y',
		) );

		$this->info = $info;
	}

	function _render( $smarty, $options ) {
		$options['display_template'] = 'print/print-' . $options['display'] . '_wiki.tpl';
		$smarty->assign( 'info', $this->info );

		return $smarty->fetch( $options['display_template'] );
	}

	function getIndexValue( $key ) {
		switch( $key ) {
		case 'title':
			return $this->info['pageName'];
		}
	}
} // }}}

class ObjectRenderer_MultilingualWiki extends ObjectRenderer // {{{
{
	private $renderers = array();

	function __construct( $type, $object, $options = array() ) {
		parent::__construct( $type, $object, $options );
		global $multilinguallib, $tikilib; require_once 'lib/multilingual/multilinguallib.php';

		$languages = $options['languages'];
		$this->renderers = array_fill_keys( $languages, null );

		if( $trads = $multilinguallib->getTrads( $type, $tikilib->get_page_id_from_name($object) ) ) {
			foreach( $trads as $trad ) {
				if( in_array( $trad['lang'], $languages ) ) {
					$this->renderers[ $trad['lang'] ] = new ObjectRenderer_Wiki( $type, $tikilib->get_page_name_from_id( $trad['objId'] ), $options );
				}
			}
		} else {
			$this->renderers[ reset($languages) ] = new ObjectRenderer_Wiki( $type, $object, $options );
		}
	}

	function _render($smarty, $options) {
		$out = '';

		$languages = array_keys( $this->renderers );
		if( isset( $options['languages'] ) ) {
			$languages = $options['languages'];
		}

		foreach( $languages as $lang ) {
			if( $this->renderers[$lang] ) {
				$out .= $this->renderers[$lang]->_render( $smarty, $options );
			}
		}

		return $out;
	}

	function getIndexValue($key) {
		if( strpos( $key, 'lang_' ) === 0 ) {
			list( $key, $lang ) = explode( '_', substr( $key, 5 ), 2 );

			if( isset( $this->renderers[$lang] ) && $this->renderers[$lang] ) {
				return $this->renderers[$lang]->getIndexValue($key);
			}

			return;
		}

		return reset($this->renderers)->getIndexValue($key);
	}
} // }}}

// End of classes }}}

$objectList = new ObjectList;
$objectList->addCustomIndex( 'title' );
$indexPages = array();

switch( $_GET['list'] ) {
case 'categorylist':
	$access->check_feature('feature_categories');

	if( isset( $_GET['categId'] ) ) {
		$categId = (int) $_GET['categId'];
		$objects = $categlib->list_category_objects( $categId, 0, -1, 'name_asc', '', '', true, false );

		$indexPages[] = array(
			'key' => 'title',
			'indextitle' => tra('Index'),
			'options' => array(
				'decorator' => 'indexrow',
				'display' => 'title',
			),
		);

		foreach( $objects['data'] as $index => $values ) {
			$type = $values['type'];
			$item = $values['itemId'];
			$objectList->add( $type, $item, array() );
		}
	}
	break;

case 'glossary':
	if( isset( $_REQUEST['languages'] ) ) {
		$languages = (array)$_REQUEST['languages'];
	} else {
		$languages = array($prefs['language']);
	}

	$filterLang = reset( $languages );
	foreach($languages as $num => $code) {
		$key = 'lang_title_' . $code;

		if ($num > 0) {
			$objectList->addCustomIndex($key);
		} else {
			$key = 'title';
		}

		$indexPages[] = array(
			'key' => $key,
			'indextitle' => tra('Index (' . $code . ')'),
			'options' => array(
				'decorator' => 'indexrow',
				'display' => 'title',
				'languages' => array($code),
			),
		);
	}

	$filter = array( 'lang' => $filterLang );

	if( isset( $_GET['categId'] ) ) {
		$access->check_feature('feature_categories');
		$filter['categId'] = $_GET['categId'];
	}

	$pages = $tikilib->list_pages( 0, -1, 'pageName_asc', '', '', true, true, false, false, $filter );

	foreach($pages['data'] as $info) {
		$objectList->add( 'wiki page', $info['pageName'], array( 'languages' => $languages ) );
	}

	break;
}

$objectList->finalize();

$smarty->display('header.tpl');
$smarty->display('print/print-page_header.tpl');

foreach($indexPages as $page) {
	$smarty->assign('indextitle', $page['indextitle']);
	$smarty->display('print/print-index_header.tpl');
	$objectList->render($smarty, $page['key'], $page['options']);
	$smarty->display('print/print-index_footer.tpl');
}

// Display all data
$objectList->render( $smarty, null, array(
	'decorator' => 'indexed',
	'display' => 'object',
) );
 
$smarty->display('print/print-page_footer.tpl');
$smarty->display('footer.tpl');

