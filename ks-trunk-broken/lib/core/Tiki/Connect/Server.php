<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tiki_Connect_Server extends Tiki_Connect_Abstract
{
	private $indexFile;

	public function __construct()
	{
		parent::__construct();
		$this->indexFile = 'temp/connect_server-index';

	}

	function getMatchingConnections( $criteria )
	{
		$index = $this->getIndex();

		Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength(0);
		Zend_Search_Lucene::setResultSetLimit(25);	// TODO during dev

		$results = $index->find($criteria);

		$ret = array();
		foreach ($results as $hit) {
			$res = array();
			$res['created'] = $hit->created;
			try {
				$res['title'] = $hit->title;
			} catch (Zend_Search_Lucene_Exception $e) {
				$res['title'] = '';
			}
			try {
				$res['url'] = $hit->url;
			} catch (Zend_Search_Lucene_Exception $e) {
				$res['url'] = '';
			}
			try {
				$res['keywords'] = $hit->keywords;
			} catch (Zend_Search_Lucene_Exception $e) {
				$res['keywords'] = '';
			}
			try {
				$res['language'] = $hit->language;
			} catch (Zend_Search_Lucene_Exception $e) {
				$res['language'] = '';
			}
			try {
				$res['geo_lat'] = $hit->geo_lat;
			} catch (Zend_Search_Lucene_Exception $e) {
				$res['geo_lat'] = '';
			}
			try {
				$res['geo_lon'] = $hit->geo_lon;
			} catch (Zend_Search_Lucene_Exception $e) {
				$res['geo_lon'] = '';
			}
			try {
				$res['geo_zoom'] = $hit->geo_zoom;
			} catch (Zend_Search_Lucene_Exception $e) {
				$res['geo_zoom'] = '';
			}

			$res['class'] = 'tablename';
			$res['metadata'] = '';

			if ($res['geo_lat'] && $res['geo_lon']) {
				$res['class'] .= ' geolocated connection';
				$res['metadata'] = " data-geo-lat=\"{$res['geo_lat']}\" data-geo-lon=\"{$res['geo_lon']}\"";

				if (isset($res['geo_zoom'])) {
					$res['metadata'] .= " data-geo-zoom=\"{$res['geo_zoom']}\"";
				}
				$res['metadata'] .= ' data-icon-name="tiki"';
			}

			$ret[] = $res;
		}

		return $ret;
	}

	function rebuildIndex()
	{
		$this->getIndex(true);
	}

	private function getIndex($rebuld = false)
	{

		if ($rebuld || $this->indexNeedsRebuilding()) {
			$index = Zend_Search_Lucene::create($this->indexFile);

			foreach ($this->getReceivedDataLatest() as $connection) {
				$data = unserialize($connection['data']);

				if ($data) {
					$doc = $this->indexConnection($connection['created'], $data);
					$index->addDocument($doc);
				}
			}

			$index->optimize();
			return $index;
		}

		return Zend_Search_Lucene::open($this->indexFile);
	}

	public function indexNeedsRebuilding()
	{
		return !file_exists($this->indexFile);
	}

	private function indexConnection($created, $data)
	{
		$doc = new Zend_Search_Lucene_Document();
		$doc->addField(Zend_Search_Lucene_Field::Keyword('created', $created));
		$doc->addField(Zend_Search_Lucene_Field::Text('version', $data['version']));

		if (!empty($data['site'])) {
			if (!empty($data['site']['connect_site_title'])) {
				$doc->addField(Zend_Search_Lucene_Field::Text('title', $data['site']['connect_site_title']));
			}
			if (!empty($data['site']['connect_site_url'])) {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('url', $data['site']['connect_site_url']));
			}
			if (!empty($data['site']['connect_site_email'])) {
				$doc->addField(Zend_Search_Lucene_Field::Keyword('email', $data['site']['connect_site_email']));	// hmm
			}
			if (!empty($data['site']['connect_site_keywords'])) {
				$doc->addField(Zend_Search_Lucene_Field::Text('keywords', $data['site']['connect_site_keywords']));
			}
			if (!empty($data['site']['connect_site_location'])) {
				$loc = TikiLib::lib('geo')->parse_coordinates($data['site']['connect_site_location']);
				if (count($loc) > 1) {
					$doc->addField(Zend_Search_Lucene_Field::Keyword('geo_lat', $loc['lat']));
					$doc->addField(Zend_Search_Lucene_Field::Keyword('geo_lon', $loc['lon']));
					if (count($loc) > 2) {
						$doc->addField(Zend_Search_Lucene_Field::Keyword('geo_zoom', $loc['zoom']));
					}
				}
			}
		} else {
			$doc->addField(Zend_Search_Lucene_Field::Text('title', tra('Anonymous')));
		}
		if (!empty($data['tables'])) {
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('tables', serialize($data['tables'])));
		}
		if (!empty($data['prefs'])) {
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('prefs', serialize($data['prefs'])));
			if (!empty($data['prefs']['language'])) {
				$languages = TikiLib::get_language_map();
				$doc->addField(Zend_Search_Lucene_Field::Text('language', $languages[$data['prefs']['language']]));
			}
		}
		if (!empty($data['server'])) {
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('server', serialize($data['server'])));
		}
		if (!empty($data['votes'])) {
			$doc->addField(Zend_Search_Lucene_Field::UnIndexed('votes', serialize($data['votes'])));
		}


		return $doc;
	}

	function recordConnection($status, $guid, $data = '', $server = false)
	{
		$created = parent::recordConnection($status, $guid, $data, $server);

		$this->indexConnection($created, $data);
	}


	/**
	 * Gets a summary of connections
	 *
	 * @return array
	 */

	function getReceivedDataStats()
	{
		global $prefs;

		$ret = array();

		$ret['received'] = $this->connectTable->fetchCount(
			array(
				'type' => 'received',
				'server' => 1,
			)
		);

		// select distinct guid from tiki_connect where server=1;
		$res = TikiLib::lib('tiki')->getOne('SELECT COUNT(DISTINCT `guid`) FROM `tiki_connect` WHERE `server` = 1 AND `type` = \'received\';');

		$ret['guids'] = $res;

		return $ret;
	}

	function getReceivedDataLatest()
	{

		// select distinct guid from tiki_connect where server=1;
		$res = TikiLib::lib('tiki')->fetchAll('SELECT * FROM (SELECT * FROM `tiki_connect` WHERE `server` = 1 AND `type` = \'received\' ORDER BY `created` DESC) as `tc` GROUP BY `guid` ORDER BY `created` DESC;');

		return $res;
	}

	/**
	 * test if a guid is pending
	 * Connect Server
	 *
	 * @param string $guid
	 * @return string
	 */

	function isPendingGuid( $guid )
	{
		$res = $this->connectTable->fetchOne(
			'data',
			array(
				'type' => 'pending',
				'server' => 1,
				'guid' => $guid,
			)
		);
		return $res;
	}

	/**
	 * text if a guid is confirmed here
	 * Connect Server
	 *
	 * @param string $guid
	 * @return bool
	 */

	function isConfirmedGuid( $guid )
	{
		$res = $this->connectTable->fetchCount(
			array(
				'type' => 'confirmed',
				'server' => 1,
				'guid' => $guid,
			)
		);
		return $res > 0;
	}
}
