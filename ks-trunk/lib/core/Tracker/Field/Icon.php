<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
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
				'description' => tr('Allows to select an image as an icon attached to the tracker item from file galleries.'),
				'prefs' => array('trackerfield_icon', 'feature_file_galleries', 'feature_search'),
				'tags' => array('advanced'),
				'help' => 'Icon Tracker Field',
				'default' => 'y',
				'params' => array(
					'galleryId' => array(
						'name' => tr('Gallery ID'),
						'description' => tr('File gallery to upload new files into.'),
						'filter' => 'int',
					),
					'default' => array(
						'name' => tr('Default image'),
						'description' => tr('Path to the default icon used.'),
						'filter' => 'url',
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
			),
			'',
			'&'
		);
	}

	function renderInput($context = array())
	{
		$filegallib = TikiLib::lib('filegal');

		$galleryId = (int) $this->getOption(0);
		$info = $filegallib->get_file_gallery_info($galleryId);

		$galleries = array(
			array('label' => $info['name'], 'url' => $this->getSearchLink($galleryId)),
		);

		$children = $filegallib->table('tiki_file_galleries')
										->fetchMap('galleryId', 'name', array('parentId' => $galleryId));
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
		return $this->renderTemplate('trackeroutput/icon.tpl', $context);
	}

	function handleSave($value, $oldValue)
	{
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

		if ($fieldId = $definition->getIconField()) {
			$value = isset($args['values'][$fieldId]) ? $args['values'][$fieldId] : null;

			if (! empty($value)) {
				$tikilib = TikiLib::lib('tiki');
				$value = $tikilib->tikiUrl($value);
			}

			$attributelib = TikiLib::lib('attribute');
			$attributelib->set_attribute($args['type'], $args['object'], 'tiki.icon.src', $value);
		}
	}

	function getDocumentPart($baseKey, Search_Type_Factory_Interface $typeFactory)
	{
		return array(
			$baseKey => $typeFactory->identifier($this->getValue()),
		);
	}
}

