<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Tracker_Field_Relation extends Tracker_Field_Abstract
{
	const OPT_RELATION = 'relation';
	const OPT_FILTER = 'filter';
	const OPT_READONLY = 'readonly';
	const OPT_INVERT = 'invert';

	static $refreshedTargets = array();

	public static function getTypes()
	{
		return array(
			'REL' => array(
				'name' => tr('Relations'),
				'description' => tr('Allows arbitrary relations to be created between the trackers and other objects in the system.'),
				'prefs' => array('trackerfield_relation'),
				'tags' => array('advanced'),
				'default' => 'n',
				'help' => 'Relations Tracker Field',
				'params' => array(
					'relation' => array(
						'name' => tr('Relation'),
						'description' => tr('Relation qualifier. Must be a three-part qualifier containing letters and separated by dots.'),
						'filter' => 'attribute_type',
						'legacy_index' => 0,
					),
					'filter' => array(
						'name' => tr('Filter'),
						'description' => tr('URL-encoded list of filters to be applied on object selection.'),
						'filter' => 'url',
						'legacy_index' => 1,
						'profile_reference' => 'search_urlencoded',
					),
					'format' => array(
						'name' => tr('Format'),
						'description' => tr('Customize display of search results of object selection. Default is {title} listing the object title. Note that including other fields in the format will make search look up exactly those fields intead of the title field.'),
						'filter' => 'text',
					),
					'readonly' => array(
						'name' => tr('Read-only'),
						'description' => tr('Only display the incoming relations instead of manipulating them.'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
						'legacy_index' => 2,
					),
					'invert' => array(
						'name' => tr('Include Invert'),
						'description' => tr('Include invert relations in the list'),
						'filter' => 'int',
						'options' => array(
							0 => tr('No'),
							1 => tr('Yes'),
						),
						'legacy_index' => 3,
					),
					'display' => array(
						'name' => tr('Display'),
						'description' => tr('Control how the relations are displayed in view mode'),
						'filter' => 'word',
						'options' => array(
							'list' => tr('List'),
							'count' => tr('Count'),
							'toggle' => tr('Count with toggle for list'),
						),
						'legacy_index' => 4,
					),
					'refresh' => array(
						'name' => tr('Force Refresh'),
						'description' => tr('Re-save related tracker items.'),
						'filter' => 'alpha',
						'options' => array(
							'' => tr('No'),
							'save' => tr('On Save'),
						),
					),
				),
			),
		);
	}

	function getFieldData(array $requestData = array())
	{
		$insertId = $this->getInsertId();

		$data = array();
		if (! $this->getOption(self::OPT_READONLY) && isset($requestData[$insertId])) {
			$selector = TikiLib::lib('objectselector');
			$entries = $selector->readMultiple($requestData[$insertId]);
			$data = array_map('strval', $entries);
		} else {
			$data = $this->getRelations($this->getOption(self::OPT_RELATION));
		}

		if ($this->getOption(self::OPT_INVERT)) {
			$inverts = array_diff(
				$this->getRelations($this->getOption(self::OPT_RELATION) . '.invert'),
				$data
			);
		} else {
			$inverts = array();
		}

		return array(
			'value' => implode("\n", $data),
			'relations' => $data,
			'inverts' => $inverts,
		);
	}

	function renderInput($context = array())
	{
		if ($this->getOption(self::OPT_READONLY)) {
			return tra('Read-only');
		}

		$labels = array();
		foreach ($this->getConfiguration('relations') as $rel) {
			list($type, $id) = explode(':', $rel, 2);
			$labels[$rel] = TikiLib::lib('object')->get_title($type, $id, $this->getOption('format'));
		}
		foreach ($this->getConfiguration('inverts') as $rel) {
			list($type, $id) = explode(':', $rel, 2);
			$labels[$rel] = TikiLib::lib('object')->get_title($type, $id, $this->getOption('format'));
		}

		$filter = $this->buildFilter();
		if (!isset($filter['object_id']) && $this->getItemId()) {
			$filter['object_id'] = 'NOT '.$this->getItemId();
		}

		return $this->renderTemplate(
			'trackerinput/relation.tpl',
			$context,
			array(
				'labels' => $labels,
				'filter' => $filter,
				'format' => $this->getOption('format')
			)
		);
	}

	function renderOutput($context = array())
	{
		if ($context['list_mode'] === 'csv') {
			return $this->getConfiguration('value');
		} else {
			$display = $this->getOption('display');
			if (! in_array($display, array('list', 'count', 'toggle'))) {
				$display = 'list';
			}

			return $this->renderTemplate(
				'trackeroutput/relation.tpl',
				$context,
				array(
					'display' => $display,
					'format' => $this->getOption('format')
				)
			);
		}
	}

	function handleSave($value, $oldValue)
	{
		if ($value) {
			$target = explode("\n", trim($value));
		} else {
			$target = array();
		}

		if ($this->getOption(self::OPT_READONLY)) {
			if ($this->getOption('refresh') == 'save') {
				$this->prepareRefreshRelated($target);
			}

			return array(
				'value' => $value,
			);
		}

		$relationlib = TikiLib::lib('relation');
		$current = $relationlib->get_relations_from('trackeritem', $this->getItemId(), $this->getOption(self::OPT_RELATION));
		$map = array();
		foreach ($current as $rel) {
			$key = $rel['type'] . ':' . $rel['itemId'];
			$id = $rel['relationId'];
			$map[$key] = $id;
		}

		$toRemove = array_diff(array_keys($map), $target);
		$toAdd = array_diff($target, array_keys($map));

		foreach ($toRemove as $v) {
			$id = $map[$v];
			$relationlib->remove_relation($id);
		}

		foreach ($toAdd as $key) {
			list($type, $id) = explode(':', $key, 2);

			$relationlib->add_relation($this->getOption(self::OPT_RELATION), 'trackeritem', $this->getItemId(), $type, $id);
		}

		if ($this->getOption('refresh') == 'save') {
			$this->prepareRefreshRelated(array_merge($target, $toRemove));
		}

		return array(
			'value' => $value,
		);
	}

	function watchCompare($old, $new)
	{
	}

	/**
	 * Restrict some field option changes that can have potentially bad side effects.
	 * E.g. changing relation to an already existing one.
	 *
	 * @param $params - array of field options
	 * @throws Services_Exception
	 */
	public function validateFieldOptions($params) {
		if (empty($params['relation'])) {
			return;
		}
		if ($params['relation'] != $this->getOption(self::OPT_RELATION)) {
			$relationlib = TikiLib::lib('relation');
			if ($relationlib->relation_exists($params['relation'], 'trackeritem')) {
				throw new Exception(tr("Relation %0 already exists", $params['relation']));
			}
		}
	}

	/**
	 * Update existing data in relations table when changing the relation name.
	 * Used when updating field options.
	 *
	 * @param $params - array of field options
	 */
	public function convertFieldOptions($params) {
		if (empty($params['relation'])) {
			return;
		}
		if ($params['relation'] != $this->getOption(self::OPT_RELATION)) {
			$relationlib = TikiLib::lib('relation');
			$relationlib->update_relation($this->getOption(self::OPT_RELATION), $params['relation']);
		}
	}

	/**
	 * When Relation field is removed, clean up the relations table.
	 */
	public function handleFieldRemove() {
		$relationlib = TikiLib::lib('relation');
		$relationlib->remove_relation_type($this->getOption(self::OPT_RELATION));
	}

	private function prepareRefreshRelated($target)
	{
		$itemId = $this->getItemId();
		// After saving the field, bind a temporary event on save to refresh child elements

		TikiLib::events()->bind('tiki.trackeritem.save', function ($args) use ($itemId, $target) {
			if ($args['type'] == 'trackeritem' && $args['object'] == $itemId) {
				$utilities = new Services_Tracker_Utilities;

				foreach ($target as $key) {
					if( in_array($key, self::$refreshedTargets) ) {
						continue;
					}
					self::$refreshedTargets[] = $key;

					list($type, $id) = explode(':', $key, 2);

					if ($type == 'trackeritem') {
						$utilities->resaveItem($id);
					}
				}
			}
		});
	}

	private function buildFilter()
	{
		parse_str($this->getOption(self::OPT_FILTER), $filter);
		return $filter;
	}

	private function getRelations($relation)
	{
		$data = array();
		
		$relations = TikiLib::lib('relation')->get_relations_from('trackeritem', $this->getItemId(), $relation);
		foreach ($relations as $rel) {
			$data[] = $rel['type'] . ':' . $rel['itemId'];
		}
		
		if (substr($relation, -7) == '.invert') {
			$relations = TikiLib::lib('relation')->get_relations_to('trackeritem', $this->getItemId(), substr($relation, 0, -7));
			foreach ($relations as $rel) {
				$data[] = $rel['type'] . ':' . $rel['itemId'];
			}
		}

		return array_unique($data);
	}

	static public function syncRelationAdded($args)
	{
		if ($args['sourcetype'] == 'trackeritem') {
			// It should be a forward relation
			$relation = $args['relation'];
			$trackerId = TikiLib::lib('trk')->get_tracker_for_item($args['sourceobject']);
			if( !$trackerId ) {
				return;
			}
			$definition = Tracker_Definition::get($trackerId);
			if( !$definition ) {
				return;
			}
			if ($fieldId = $definition->getRelationField($relation)) {
				$itemId = $args['sourceobject'];
				$value = $old_value = explode("\n", TikiLib::lib('trk')->get_item_value($trackerId, $itemId, $fieldId));
				$other = $args['type'] . ':' . $args['object'];
				if (!in_array($other, $value)) {
					$value[] = $other;
				}
				if ($value != $old_value) {
					$value = implode("\n", $value);
					TikiLib::lib('trk')->modify_field($itemId, $fieldId, $value);
				}
				// include self-related inversions which lack .invert relation field
				if ($trackerId == TikiLib::lib('trk')->get_tracker_for_item($args['object'])) {
					$field = TikiLib::lib('trk')->get_tracker_field($fieldId);
					$handler = TikiLib::lib('trk')->get_field_handler($field);
					if ($handler->getOption(self::OPT_INVERT)) {
						$itemId = $args['object'];
						$value = $old_value = explode("\n", TikiLib::lib('trk')->get_item_value($trackerId, $itemId, $fieldId));
						$other = $args['sourcetype'] . ':' . $args['sourceobject'];
						if (!in_array($other, $value)) {
							$value[] = $other;
						}
						if ($value != $old_value) {
							$value = implode("\n", $value);
							TikiLib::lib('trk')->modify_field($itemId, $fieldId, $value);
						}
					}
				}
			}
		}
		if ($args['type'] == 'trackeritem') {
			// It should be an invert relation
			$relation = $args['relation'] . '.invert';
			$trackerId = TikiLib::lib('trk')->get_tracker_for_item($args['object']);
			if( !$trackerId ) {
				return;
			}
			$definition = Tracker_Definition::get($trackerId);
			if( !$definition ) {
				return;
			}
			if ($fieldId = $definition->getRelationField($relation)) {
				$itemId = $args['object'];
				$value = $old_value = explode("\n", TikiLib::lib('trk')->get_item_value($trackerId, $itemId, $fieldId));
				$other = $args['sourcetype'] . ':' . $args['sourceobject'];
				if (!in_array($other, $value)) {
					$value[] = $other;
				}
				if ($value != $old_value) {
					$value = implode("\n", $value);
					TikiLib::lib('trk')->modify_field($itemId, $fieldId, $value);
				}
			}
		}
	}

	static public function syncRelationRemoved($args)
	{
		if ($args['sourcetype'] == 'trackeritem') {
			// It should be a forward relation
			$relation = $args['relation'];
			$trackerId = TikiLib::lib('trk')->get_tracker_for_item($args['sourceobject']);
			if( !$trackerId ) {
				return;
			}
			$definition = Tracker_Definition::get($trackerId);
			if( !$definition ) {
				return;
			}
			if ($fieldId = $definition->getRelationField($relation)) {
				$itemId = $args['sourceobject'];
				$value = $old_value =explode("\n", TikiLib::lib('trk')->get_item_value($trackerId, $itemId, $fieldId));
				$other = $args['type'] . ':' . $args['object'];
				if (in_array($other, $value)) {
					$value = array_diff($value, [$other]);
				}
				if ($value != $old_value) {
					$value = implode("\n", $value);
					TikiLib::lib('trk')->modify_field($itemId, $fieldId, $value);
				}
				// include self-related inversions which lack .invert relation field
				if ($trackerId == TikiLib::lib('trk')->get_tracker_for_item($args['object'])) {
					$field = TikiLib::lib('trk')->get_tracker_field($fieldId);
					$handler = TikiLib::lib('trk')->get_field_handler($field);
					if ($handler->getOption(self::OPT_INVERT)) {
						$itemId = $args['object'];
						$value = $old_value = explode("\n", TikiLib::lib('trk')->get_item_value($trackerId, $itemId, $fieldId));
						$other = $args['sourcetype'] . ':' . $args['sourceobject'];
						if (in_array($other, $value)) {
							$value = array_diff($value, [$other]);
						}
						if ($value != $old_value) {
							$value = implode("\n", $value);
							TikiLib::lib('trk')->modify_field($itemId, $fieldId, $value);
						}
					}
				}
			}
		}
		if ($args['type'] == 'trackeritem') {
			// It should be an invert relation
			$relation = $args['relation'] . '.invert';
			$trackerId = TikiLib::lib('trk')->get_tracker_for_item($args['object']);
			if( !$trackerId ) {
				return;
			}
			$definition = Tracker_Definition::get($trackerId);
			if( !$definition ) {
				return;
			}
			if ($fieldId = $definition->getRelationField($relation)) {
				$itemId = $args['object'];
				$value = $old_value = explode("\n", TikiLib::lib('trk')->get_item_value($trackerId, $itemId, $fieldId));
				$other = $args['sourcetype'] . ':' . $args['sourceobject'];
				if (in_array($other, $value)) {
					$value = array_diff($value, [$other]);
				}
				if ($value != $old_value) {
					$value = implode("\n", $value);
					TikiLib::lib('trk')->modify_field($itemId, $fieldId, $value);
				}
			}
		}
	}
}

