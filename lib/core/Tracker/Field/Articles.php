<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: Files.php 50223 2014-03-05 19:11:54Z lphuberdeau $

class Tracker_Field_Articles extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		$db = TikiDb::get();
		$topics = $db->table('tiki_topics')->fetchMap('topicId', 'name', array(), -1, -1, 'name_asc');
		$types = $db->table('tiki_article_types')->fetchColumn('type', array());
		$types = array_combine($types, $types);

		$options = array(
			'articles' => array(
				'name' => tr('Articles'),
				'description' => tr('Attach articles to the tracker item.'),
				'prefs' => array('trackerfield_articles', 'feature_articles'),
				'tags' => array('advanced'),
				'help' => 'Articles Tracker Field',
				'default' => 'n',
				'params' => array(
					'topicId' => array(
						'name' => tr('Topic'),
						'description' => tr('Default article topic'),
						'filter' => 'int',
						'profile_reference' => 'article_topic',
						'options' => $topics,
					),
					'type' => array(
						'name' => tr('Article Type'),
						'description' => tr('Default article type'),
						'filter' => 'text',
						'profile_reference' => 'article_type',
						'options' => $types,
					),
				),
			),
		);
		return $options;
	}

	function getFieldData(array $requestData = array())
	{
		global $prefs;
		$ins_id = $this->getInsertId();
		if (isset($requestData[$ins_id])) {
			if (is_string($requestData[$ins_id])) {
				$articleIds = explode(',', $requestData[$ins_id]);
			} else {
				$articleIds = $requestData[$ins_id];
			}

			$articleIds = array_filter(array_map('intval', $articleIds));
			$value = implode(',', $articleIds);
		} else {
			$value = $this->getValue();

			// Obtain the information from the database for display
			$articleIds = array_filter(explode(',', $value));
		}

		return array(
			'value' => $value,
			'articleIds' => $articleIds,
		);
	}

	function renderInput($context = array())
	{
		$articleIds = $this->getConfiguration('articleIds');

		return $this->renderTemplate('trackerinput/articles.tpl', $context, array(
			'filter' => ['type' => 'article'],
			'labels' => array_combine(
				$articleIds,
				array_map(function ($id) {
					return TikiLib::lib('object')->get_title('article', $id);
				}, $articleIds)
			),
		));
	}

	function renderOutput($context = array())
	{
		return $this->renderTemplate('trackeroutput/articles.tpl', $context, array(
		));
	}

	function handleSave($value, $oldValue)
	{
		$new = array_diff(explode(',', $value), explode(',', $oldValue));
		$remove = array_diff(explode(',', $oldValue), explode(',', $value));

		$itemId = $this->getItemId();

		$relationlib = TikiLib::lib('relation');
		$relations = $relationlib->get_relations_from('trackeritem', $itemId, 'tiki.article.attach');
		foreach ($relations as $existing) {
			if ($existing['type'] != 'article') {
				continue;
			}

			if (in_array($existing['itemId'], $remove)) {
				$relationlib->remove_relation($existing['relationId']);
			}
		}

		foreach ($new as $articleId) {
			$relationlib->add_relation('tiki.article.attach', 'trackeritem', $itemId, 'article', $articleId);
		}

		return array(
			'value' => $value,
		);
	}
}

