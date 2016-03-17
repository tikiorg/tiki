<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
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

		ZendSearch\Lucene\Search\Query\Wildcard::setMinPrefixLength(0);
		ZendSearch\Lucene\Lucene::setResultSetLimit(25);	// TODO during dev

		$results = $index->find($criteria);

		$ret = array();
		foreach ($results as $hit) {
			$res = array();
			$res['created'] = $hit->created;
			try {
				$res['title'] = $hit->title;
			} catch (ZendSearch\Lucene\Exception\ExceptionInterface $e) {
				$res['title'] = '';
			}
			try {
				$res['url'] = $hit->url;
			} catch (ZendSearch\Lucene\Exception\ExceptionInterface $e) {
				$res['url'] = '';
			}
			try {
				$res['keywords'] = $hit->keywords;
			} catch (ZendSearch\Lucene\Exception\ExceptionInterface $e) {
				$res['keywords'] = '';
			}
			try {
				$res['language'] = $hit->language;
			} catch (ZendSearch\Lucene\Exception\ExceptionInterface $e) {
				$res['language'] = '';
			}
			try {
				$res['geo_lat'] = $hit->geo_lat;
			} catch (ZendSearch\Lucene\Exception\ExceptionInterface $e) {
				$res['geo_lat'] = '';
			}
			try {
				$res['geo_lon'] = $hit->geo_lon;
			} catch (ZendSearch\Lucene\Exception\ExceptionInterface $e) {
				$res['geo_lon'] = '';
			}
			try {
				$res['geo_zoom'] = $hit->geo_zoom;
			} catch (ZendSearch\Lucene\Exception\ExceptionInterface $e) {
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
			$index = ZendSearch\Lucene\Lucene::create($this->indexFile);

			foreach ($this->getReceivedDataLatest() as $connection) {
				$data = json_decode($connection['data'], true);

				if ($data) {
					$doc = $this->indexConnection($connection['created'], $data);
					$index->addDocument($doc);
				}
			}

			$index->optimize();
			return $index;
		}

		return ZendSearch\Lucene\Lucene::open($this->indexFile);
	}

	public function indexNeedsRebuilding()
	{
		return !file_exists($this->indexFile);
	}

	private function indexConnection($created, $data)
	{
		$doc = new ZendSearch\Lucene\Document();
		$doc->addField(ZendSearch\Lucene\Document\Field::Keyword('created', $created));
		$doc->addField(ZendSearch\Lucene\Document\Field::Text('version', $data['version']));

		if (!empty($data['site'])) {
			if (!empty($data['site']['connect_site_title'])) {
				$doc->addField(ZendSearch\Lucene\Document\Field::Text('title', $data['site']['connect_site_title']));
			}
			if (!empty($data['site']['connect_site_url'])) {
				$doc->addField(ZendSearch\Lucene\Document\Field::Keyword('url', $data['site']['connect_site_url']));
			}
			if (!empty($data['site']['connect_site_email'])) {
				$doc->addField(ZendSearch\Lucene\Document\Field::Keyword('email', $data['site']['connect_site_email']));	// hmm
			}
			if (!empty($data['site']['connect_site_keywords'])) {
				$doc->addField(ZendSearch\Lucene\Document\Field::Text('keywords', $data['site']['connect_site_keywords']));
			}
			if (!empty($data['site']['connect_site_location'])) {
				$loc = TikiLib::lib('geo')->parse_coordinates($data['site']['connect_site_location']);
				if (count($loc) > 1) {
					$doc->addField(ZendSearch\Lucene\Document\Field::Keyword('geo_lat', $loc['lat']));
					$doc->addField(ZendSearch\Lucene\Document\Field::Keyword('geo_lon', $loc['lon']));
					if (count($loc) > 2) {
						$doc->addField(ZendSearch\Lucene\Document\Field::Keyword('geo_zoom', $loc['zoom']));
					}
				}
			}
		} else {
			$doc->addField(ZendSearch\Lucene\Document\Field::Text('title', tra('Anonymous')));
		}
		if (!empty($data['tables'])) {
			$doc->addField(ZendSearch\Lucene\Document\Field::UnIndexed('tables', serialize($data['tables'])));
		}
		if (!empty($data['prefs'])) {
			$doc->addField(ZendSearch\Lucene\Document\Field::UnIndexed('prefs', serialize($data['prefs'])));
			if (!empty($data['prefs']['language'])) {
				$langLib = TikiLib::lib('language');
				$languages = $langLib->get_language_map();
				$doc->addField(ZendSearch\Lucene\Document\Field::Text('language', $languages[$data['prefs']['language']]));
			}
		}
		if (!empty($data['server'])) {
			$doc->addField(ZendSearch\Lucene\Document\Field::UnIndexed('server', serialize($data['server'])));
		}
		if (!empty($data['votes'])) {
			$doc->addField(ZendSearch\Lucene\Document\Field::UnIndexed('votes', serialize($data['votes'])));
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
