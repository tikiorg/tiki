<?php

class Search_ContentSource_WikiSource implements Search_ContentSource_Interface
{
	private $db;
	private $tikilib;
	private $flaggedrevisionlib;

	function __construct()
	{
		global $tikilib, $prefs;

		$this->db = TikiDb::get();
		$this->tikilib = $tikilib;

		if ($prefs['flaggedrev_approval'] == 'y') {
			global $flaggedrevisionlib; require_once 'lib/wiki/flaggedrevisionlib.php';
			$this->flaggedrevisionlib = $flaggedrevisionlib;
		}
	}

	function getDocuments()
	{
		return array_values($this->db->fetchMap('SELECT page_id, pageName FROM tiki_pages'));
	}

	function getDocument($objectId, Search_Type_Factory_Interface $typeFactory)
	{
		global $wikilib; require_once 'lib/wiki/wikilib.php';

		$info = $this->tikilib->get_page_info($objectId, true, true);

		$contributors = $wikilib->get_contributors($objectId, $info['user']);
		if (! in_array($info['user'], $contributors)) {
			$contributors[] = $info['user'];
		}

		$data = array(
			'title' => $typeFactory->sortable($info['pageName']),
			'hash' => $typeFactory->identifier($info['version']),
			'language' => $typeFactory->identifier(empty($info['lang']) ? 'unknown' : $info['lang']),
			'modification_date' => $typeFactory->timestamp($info['lastModif']),
			'description' => $typeFactory->plaintext($info['description']),
			'contributors' => $typeFactory->multivalue($contributors),

			'wiki_content' => $typeFactory->wikitext($info['data']),

			'view_permission' => $typeFactory->identifier('tiki_p_view'),
			'url' => $typeFactory->identifier($wikilib->sefurl($info['pageName'])),
		);

		$out = $data;

		if ($this->flaggedrevisionlib && $this->flaggedrevisionlib->page_requires_approval($info['pageName'])) {
			$out = array();

			// Will provide two documents: one approved and one latest
			$versionInfo = $this->flaggedrevisionlib->get_version_with($info['pageName'], 'moderation', 'OK');

			if (! $versionInfo || $versionInfo['version'] != $info['version']) {
				// No approved version or approved version differs, latest content marked as such
				$out[] = array_merge($data, array(
					'title' => $typeFactory->sortable(tr('%0 (latest)', $info['pageName'])),
					'view_permission' => $typeFactory->identifier('tiki_p_wiki_view_latest'),
					'url' => $typeFactory->identifier($wikilib->sefurl($info['pageName'], true) . 'latest'),
				));
			}

			if ($versionInfo) {
				// Approved version not latest, include approved version in index
				// Also applies when versions are equal, data would be the same
				$out[] = array_merge($data, array(
					'wiki_content' => $typeFactory->wikitext($versionInfo['data']),
					'hash' => $typeFactory->identifier($versionInfo['version']),
				));
			}
		}


		return $out;
	}

	function getProvidedFields()
	{
		return array(
			'title',
			'hash',
			'url',
			'language',
			'modification_date',
			'description',
			'contributors',

			'wiki_content',

			'view_permission',
		);
	}

	function getGlobalFields()
	{
		return array(
			'title' => true,
			'description' => true,

			'wiki_content' => false,
		);
	}
}

