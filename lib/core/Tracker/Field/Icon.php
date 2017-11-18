<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Icon extends Tracker_Field_Abstract
{
	public static function getTypes()
	{
		return [
			'icon' => [
				'name' => tr('Icon'),
				'description' => tr('Provides ability to select an image as an icon attached to the tracker item from file galleries.'),
				'prefs' => ['trackerfield_icon', 'feature_file_galleries', 'feature_search'],
				'tags' => ['advanced'],
				'help' => 'Icon Tracker Field',
				'default' => 'y',
				'params' => [
					'galleryId' => [
						'name' => tr('Gallery ID'),
						'description' => tr('File gallery to upload new files into.'),
						'filter' => 'int',
						'legacy_index' => 0,
						'profile_reference' => 'file_gallery',
					],
					'default' => [
						'name' => tr('Default image'),
						'description' => tr('Path to the default icon used.'),
						'filter' => 'url',
						'legacy_index' => 1,
					],
					'maxIcons' => [
						'name' => tr('Maximum Icons'),
						'description' => tr('Number of icons to display in each gallery (default 120).'),
						'filter' => 'int',
						'default' => 120,
						'legacy_index' => 2,
					],
					'update' => [
						'name' => tr('Update icon event'),
						'type' => 'list',
						'description' => tr('Allow update during re-indexing. Selection of indexing is useful for changing the default icon for all items.'),
						'filter' => 'word',
						'options' => [
							'save' => tr('Save'),
							'index' => tr('Indexing'),
						],
					],
				],
			],
		];
	}

	function getFieldData(array $requestData = [])
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

		return [
			'value' => $value,
		];
	}

	private function getSearchLink($galleryId)
	{
		return 'tiki-searchindex.php?' . http_build_query(
			[
				'filter~type' => 'file',
				'filter~gallery_id' => $galleryId,
				'filter~filetype' => 'image',
				'maxRecords' => $this->getOption('maxIcons', 120),
				'sort_mode' => 'title_asc',
			],
			'',
			'&'
		);
	}

	function renderInput($context = [])
	{
		$filegallib = TikiLib::lib('filegal');

		$galleryId = (int) $this->getOption('galleryId');
		$info = $filegallib->get_file_gallery_info($galleryId);

		$galleries = [
			['label' => $info['name'], 'url' => $this->getSearchLink($galleryId)],
		];

		$children = $filegallib->table('tiki_file_galleries')->fetchMap(
			'galleryId',
			'name',
			['parentId' => $galleryId],
			-1,
			-1,
			['name' => 'ASC']
		);
		foreach ($children as $galleryId => $name) {
			$galleries[] = [
				'label' => $name,
				'url' => $this->getSearchLink($galleryId),
			];
		}

		return $this->renderTemplate(
			'trackerinput/icon.tpl',
			$context,
			[
				'galleries' => $galleries,
			]
		);
	}

	function renderInnerOutput($context = [])
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
		return [
			'value' => $value,
		];
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
				$value = TikiLib::lib('tiki')->tikiUrl($value);
				$value = TikiLib::makeAbsoluteLinkRelative($value);
			}

			$attributelib = TikiLib::lib('attribute');
			$attributelib->set_attribute($args['type'], $args['object'], 'tiki.icon.src', $value);
		}
	}

	function getDocumentPart(Search_Type_Factory_Interface $typeFactory)
	{
		if ('index' == $this->getOption('update')) {
			$value = $this->getValue();
			if (empty($value)) {
				$value = $this->getOption('default');	// value is often "" but default in getValue checks for isset
			}
			self::updateIcon([
				'trackerId' => $this->getConfiguration('trackerId'),
				'type' => 'trackeritem',
				'object' => $this->getItemId(),
				'values' => [
					$this->getConfiguration('fieldId') => $value,
				],
			]);
		}

		$baseKey = $this->getBaseKey();
		return [
			$baseKey => $typeFactory->identifier($this->getValue()),
		];
	}
}
