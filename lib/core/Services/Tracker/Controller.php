<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Tracker_Controller
{
	function action_add_field($input)
	{
		if (! Perms::get()->admin_trackers) {
			throw new Services_Exception(tr('Reserved to tracker administrators'), 403);
		}

		$trklib = TikiLib::lib('trk');
		$trackerId = $input->trackerId->int();
		$definition = Tracker_Definition::get($trackerId);

		if (! $definition) {
			throw new Services_Exception(tr('Tracker does not exist'), 404);
		}

		$name = $input->name->text();
		$type = $input->type->text();
		$description = $input->description->text();
		$wikiparse = $input->description_parse->int();
		$fieldId = 0;

		$types = $this->getFieldTypes($description);

		if (empty($type)) {
			$type = 't';
		}

		if (! isset($types[$type])) {
			throw new Services_Exception(tr('Type does not exist'), 400);
		}

		if (! empty($name)) {
			if ($trklib->get_field_id($trackerId, $name)) {
				throw new Services_Exception(tr('Field name already exists in the tracker'), 409);
			}

			$fieldId = $this->createField(array(
				'trackerId' => $trackerId,
				'name' => $name,
				'type' => $type,
				'description' => $description,
				'descriptionIsParsed' => $wikiparse,
			));
		}

		return array(
			'trackerId' => $trackerId,
			'fieldId' => $fieldId,
			'name' => $name,
			'type' => $type,
			'types' => $types,
			'description' => $description,
			'descriptionIsParsed' => $wikiparse,
		);
	}

	private function getFieldTypes($description)
	{
		$factory = new Tracker_Field_Factory($description);
		return $factory->getFieldTypes();
	}

	private function createField($data)
	{
		$trklib = TikiLib::lib('trk');
		return $trklib->replace_tracker_field(
			$data['trackerId'],
			0,
			$data['name'],
			$data['type'],
			'n',
			'n',
			'n',
			'y',
			'n',
			'y',
			$trklib->get_last_position($data['trackerId']) + 10,
			'',
			$data['description'],
			'',
			null,
			'',
			null,
			null,
			$data['descriptionIsParsed'] ? 'y' : 'n');
	}
}

