<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Icon extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return array(
			'icon' => array(
				'name' => tr('Icon'),
				'description' => tr('Provides ability to select an image as an icon attached to the tracker item from file galleries.'),
				'prefs' => array('trackerfield_icon', 'feature_file_galleries', 'feature_search'),
				'tags' => array('advanced'),
				'help' => 'Icon Tracker Field',
				'default' => 'y',
				'params' => array(
					'galleryId' => array(
						'name' => tr('Gallery ID'),
						'description' => tr('File gallery to upload new files into.'),
						'filter' => 'int',
						'legacy_index' => 0,
						'profile_reference' => 'file_gallery',
					),
					'default' => array(
						'name' => tr('Default image'),
						'description' => tr('Path to the default icon used.'),
						'filter' => 'url',
						'legacy_index' => 1,
					),
					'maxIcons' => array(
						'name' => tr('Maximum Icons'),
						'description' => tr('Number of icons to display in each gallery (default 120).'),
						'filter' => 'int',
						'default' => 120,
						'legacy_index' => 2,
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$insertId = $this->getInsertId();

		if (isset($requestData[$insertId])) {
			$value = $requestData[$insertId];
		} else {
			$value = $this->getValue();
		}

		if (! $value) {
			$value = $this->getOption('default');
		}

		return array(
			'value' => $value,
		);
	}

	private function getSearchLink($galleryId)
	{
		return 'tiki-searchindex.php?' . http_build_query(
			array(
				'filter~type' => 'file',
				'filter~gallery_id' => $galleryId,
				'filter~filetype' => 'image',
				'maxRecords' => $this->getOption('maxIcons', 120),
				'sort_mode' => 'title_asc',
			),
			'',
			'&'
		);
	}

	function renderInput($context = array())
	{
		$filegallib = TikiLib::lib('filegal');

		$galleryId = (int) $this->getOption('galleryId');
		$info = $filegallib->get_file_gallery_info($galleryId);

		$galleries = array(
			array('label' => $info['name'], 'url' => $this->getSearchLink($galleryId)),
		);

		$children = $filegallib->table('tiki_file_galleries')->fetchMap(
			'galleryId',
			'name', array('parentId' => $galleryId),
			-1,
			-1,
			array('name' => 'ASC')
		);
		foreach ($children as $galleryId => $name) {
			$galleries[] = array(
				'label' => $name,
				'url' => $this->getSearchLink($galleryId),
			);
		}

		return $this->renderTemplate(
			'trackerinput/icon.tpl',
			$context,
			array(
				'galleries' => $galleries,
			)
		);
	}

	function renderInnerOutput($context = array())
	{
		if ($context['list_mode'] === 'csv') {
			return $this->getValue();
		} else {
			return $this->renderTemplate('trackeroutput/icon.tpl', $context);
		}
	}

	function handleSave($value, $oldValue)
	{
		$value = TikiLib::makeAbsoluteLinkRelative($value);
		return array(
			'value' => $value,
		);
	}

	function watchCompare($old, $new)
	{
	}

	public static function updateIcon($args)
	{
		$definition = Tracker_Definition::get($args['trackerId']);

		if ($definition && $fieldId = $definition->getIconField()) {
			$value = isset($args['values'][$fieldId]) ? $args['values'][$fieldId] : null;

			if (! empty($value) && isset($_SERVER['REQUEST_METHOD'])) {	// leave URLs alone when run from a shell command
				$tikilib = TikiLib::lib('tiki');
				$value = $tikilib->tikiUrl($value);
			}

			$attributelib = TikiLib::lib('attribute');
			$attributelib->set_attribute($args['type'], $args['object'], 'tiki.icon.src', $value);
		}
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		$baseKey = $this->getBaseKey();
		return array(
			$baseKey => $typeFactory->identifier($this->getValue()),
		);
	}
}

