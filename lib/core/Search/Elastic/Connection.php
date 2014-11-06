<?php
// (c) Copyright 2002-2014 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Search_Elastic_Connection
{
	private $dsn;
	private $dirty = array();

	private $indices = array();

	private $bulk;

	function __construct($dsn)
	{
		$this->dsn = rtrim($dsn, '/');
	}

	function __destruct()
	{
		$this->flush();
	}

	function startBulk($size = 500)
	{
		$this->bulk = new Search_Elastic_BulkOperation(
			$size,
			function ($data) {
				$this->postBulk($data);
			}
		);
	}

	function getStatus()
	{
		try {
			$result = $this->get('/');
			if (! isset($result->ok)) {
				$result->ok = $result->status === 200;
			}

			return $result;
		} catch (Exception $e) {
			return (object) array(
				'ok' => false,
				'status' => 0,
			);
		}
	}

	function getIndexStatus($index)
	{
		try {
			return $this->get("/$index/_status");
		} catch (Exception $e) {
			return null;
		}
	}

	function deleteIndex($index)
	{
		$this->flush();

		try {
			unset($this->indices[$index]);
			return $this->delete("/$index");
		} catch (Search_Elastic_Exception $e) {
			if ($e->getCode() !== 404) {
				throw $e;
			}
		}
	}

	function search($index, array $query, array $args = [])
	{
		$indices = (array) $index;
		foreach ($indices as $index) {
			if (! empty($this->dirty[$index])) {
				$this->refresh($index);
			}
		}

		$index = implode(',', $indices);
		return $this->get("/$index/_search?" . http_build_query($args, '', '&'), json_encode($query));
	}

	function scroll($scrollId, array $args = [])
	{
		return $this->post('/_search/scroll?' . http_build_query($args, '', '&'), $scrollId);
	}

	function storeQuery($index, $name, $query)
	{
		return $this->rawIndex($index, '.percolator', $name, $query);
	}

	function unstoreQuery($index, $name)
	{
		return $this->delete("/$index/.percolator/$name");
	}

	function percolate($index, $type, $document)
	{
		if (! empty($this->dirty['_percolator'])) {
			$this->refresh('_percolator');
		}

		$type = $this->simplifyType($type);
		return $this->get("/$index/$type/_percolate", json_encode(array(
			'doc' => $document,
			'prefer_local' => false,
		)));
	}

	function index($index, $type, $id, array $data)
	{
		$type = $this->simplifyType($type);

		$this->rawIndex($index, $type, $id, $data);
	}

	function assignAlias($alias, $targetIndex)
	{
		$this->flush();

		$active = [];
		$toRemove = [];
		$current = $this->rawApi('/_aliases');
		foreach ($current as $indexName => $info) {
			if (isset($info->aliases->$alias)) {
				$active[] = $indexName;
				$toRemove[] = $indexName;
			} elseif (0 === strpos($indexName, $alias . '_') && $indexName != $targetIndex) {
				$toRemove[] = $indexName;
			}
		}
		$actions = [
			['add' => ['index' => $targetIndex, 'alias' => $alias]],
		];

		foreach ($active as $index) {
			$actions[] = ['remove' => ['index' => $index, 'alias' => $alias]];
		}

		$this->post('/_aliases', json_encode([
			'actions' => $actions,
		]));

		// Make sure the new index is fully active, then clean-up
		$this->refresh($alias);

		foreach ($toRemove as $old) {
			$this->deleteIndex($old);
		}
	}

	function isRebuilding($aliasName)
	{
		$current = $this->rawApi('/_aliases');
		foreach ($current as $indexName => $info) {
			if (0 === strpos($indexName, $aliasName . '_') && 0 === count((array) $info->aliases)) {
				// Matching name, no alias, means currently rebuilding
				return true;
			}
		}

		return false;
	}

	private function rawIndex($index, $type, $id, $data)
	{
		$this->dirty[$index] = true;

		if ($this->bulk) {
			$this->bulk->index($index, $type, $id, $data);
		} else {
			$id = rawurlencode($id);

			return $this->put("/$index/$type/$id", json_encode($data));
		}
	}

	function unindex($index, $type, $id)
	{
		$this->dirty[$index] = true;
		$type = $this->simplifyType($type);

		if ($this->bulk) {
			$this->bulk->unindex($index, $type, $id);
		} else {
			$id = rawurlencode($id);

			return $this->delete("/$index/$type/$id");
		}
	}

	function flush()
	{
		if ($this->bulk) {
			$this->bulk->flush();
		}
	}

	function refresh($index)
	{
		$this->flush();

		$this->post("/$index/_refresh", '');
		$this->dirty[$index] = false;
	}

	function document($index, $type, $id)
	{
		if (! empty($this->dirty[$index])) {
			$this->refresh($index);
		}

		$type = $this->simplifyType($type);
		$id = rawurlencode($id);

		$document = $this->get("/$index/$type/$id");

		if (isset($document->_source)) {
			return $document->_source;
		}
	}

	function mapping($index, $type, array $mapping)
	{
		$type = $this->simplifyType($type);
		$data = array($type => array(
			"properties" => $mapping,
		));

		if (empty($this->indices[$index])) {
			$this->createIndex($index);
			$this->indices[$index] = true;
		}

		$result = $this->put("/$index/$type/_mapping", json_encode($data));

		return $result;
	}

	function postBulk($data)
	{
		$this->post("/_bulk", $data);
	}

	function rawApi($path)
	{
		return $this->get($path);
	}

	private function createIndex($index)
	{
		try {
			$this->put(
				"/$index", json_encode(
					array(
						'analysis' => array(
							'analyzer' => array(
								'default' => array(
									'tokenizer' => 'standard',
									'filter' => array('standard', 'lowercase', 'asciifolding', 'tiki_stop', 'porterStem'),
								),
								'sortable' => array(
									'tokenizer' => 'keyword',
									'filter' => array('lowercase'),
								),
							),
							'filter' => array(
								'tiki_stop' => array(
									'type' => 'stop',
									'stopwords' => array ("a", "an", "and", "are", "as", "at", "be", "but", "by", "for", "if", "in", "into", "is", "it", "no", "not", "of", "on", "or", "s", "such", "t", "that", "the", "their", "then", "there", "these", "they", "this", "to", "was", "will", "with"),
								),
							),
						),
					)
				)
			);
		} catch (Search_Elastic_Exception $e) {
			// Index already exists: ignore
		}
	}

	private function get($path, $data = null)
	{
		try {
			$client = $this->getClient($path);
			if ($data) {
				$client->setRawData($data);
			}
			$response = $client->request('GET');
			return $this->handleResponse($response);
		} catch (Zend_Http_Exception $e) {
			throw new Search_Elastic_TransportException($e->getMessage());
		}
	}

	private function put($path, $data)
	{
		try {
			$client = $this->getClient($path);
			$client->setRawData($data);
			$response = $client->request('PUT');

			return $this->handleResponse($response);
		} catch (Zend_Http_Exception $e) {
			throw new Search_Elastic_TransportException($e->getMessage());
		}
	}

	private function post($path, $data)
	{
		try {
			$client = $this->getClient($path);
			$client->setRawData($data);
			$response = $client->request('POST');

			return $this->handleResponse($response);
		} catch (Zend_Http_Exception $e) {
			throw new Search_Elastic_TransportException($e->getMessage());
		}
	}

	private function delete($path)
	{
		try {
			$client = $this->getClient($path);
			$response = $client->request('DELETE');

			return $this->handleResponse($response);
		} catch (Zend_Http_Exception $e) {
			throw new Search_Elastic_TransportException($e->getMessage());
		}
	}

	private function handleResponse($response)
	{
		$content = json_decode($response->getBody());

		if ($response->isSuccessful()) {
			return $content;
		} elseif (isset($content->exists) && $content->exists === false) {
			throw new Search_Elastic_NotFoundException($content->_type, $content->_id);
		} elseif (isset($content->error)) {
			$message = $content->error;
			if (preg_match('/^MapperParsingException\[No handler for type \[(?P<type>.*)\].*\[(?P<field>.*)\]\]$/', $message, $parts)) {
				throw new Search_Elastic_MappingException($parts['type'], $parts['field']);
			} else {
				throw new Search_Elastic_Exception($message, $content->status);
			}
		} else {
			return $content;
		}
	}

	private function getClient($path)
	{
		$full = "{$this->dsn}$path";

		$tikilib = TikiLib::lib('tiki');
		return $tikilib->get_http_client($full);
	}

	private function simplifyType($type)
	{
		return preg_replace('/[^a-z]/', '', $type);
	}

	/**
	 * Store the dirty flags at the end of the request and restore them when opening the
	 * connection within a single user session so that if a modification requires re-indexing,
	 * the next page load will wait until indexing is done to show the results.
	 */
	function persistDirty(Tiki_Event_Manager $events)
	{
		if (isset($_SESSION['elastic_search_dirty'])) {
			$this->dirty = $_SESSION['elastic_search_dirty'];
			unset($_SESSION['elastic_search_dirty']);
		}

		// Before the HTTP request is closed
		$events->bind('tiki.process.redirect', function () {
			$_SESSION['elastic_search_dirty'] = $this->dirty;
		});
	}
}

