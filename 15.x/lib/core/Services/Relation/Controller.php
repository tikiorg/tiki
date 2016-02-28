<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Relation_Controller
{
	function setUp()
	{

	}

	/**
	 * Function to toggle relation. Sets relation when none set and then if there is a relation, it unsets.
	 * @param $input
	 * @return array with "relationId" as param. Null if relation is removed.
	 * @throws Exception
	 * @throws Services_Exception
	 */
	function action_toggle($input)
	{
		$relation = $input->relation->none();
		$target_type = $input->target_type->none();
		$target_id = $input->target_id->none();
		$source_type = $input->source_type->none();
		$source_id = $input->source_id->none();

		// ensure the target, source, and relation info are passed to the service
		if (! $target_type || ! $target_id || ! $source_type || ! $source_id || ! $relation) {
			throw new Services_Exception(tr('Invalid input'), 400);
		}

		$relationlib = TikiLib::lib('relation');
		$tx = TikiDb::get()->begin();
		$relationId = $relationlib->get_relation_id($relation, $source_type, $source_id, $target_type, $target_id);

		// If there is not an existing relation, add the relation and trigger the add relation event.
		if (! $relationId) {
			$relationId = $relationlib->add_relation($relation, $source_type, $source_id, $target_type, $target_id);
			TikiLib::events()->trigger(
				'tiki.relation.add',
				array(
					'id' => $relationId,
					'target_type' => $target_type,
					'target_id' => $target_id,
					'source_type' => $source_type,
					'source_id' => $source_id,
					'relation' => $relation,
				)
			);
		} else {
			//if there is a relation, remove the relation, trigger the event, and set the relationId to null
			$relationlib->remove_relation($relationId);
			TikiLib::events()->trigger(
				'tiki.relation.remove',
				array(
					'id' => $relationId,
					'target_type' => $target_type,
					'target_id' => $target_id,
					'source_type' => $source_type,
					'source_id' => $source_id,
					'relation' => $relation,
				)
			);
			$relationId = null; // set the
		}

		$tx->commit();

		//return the relationId (new relation if added, null if removed)
		return array(
			'relation_id' => $relationId,
		);
	}

	/**
	 * Function to toggle relation. Sets relation when none set and then if there is a relation, it unsets.
	 * @param $input
	 * @return array with "relationId" as param. Null if relation is removed.
	 * @throws Exception
	 * @throws Services_Exception
	 */
	function action_toggle_group($input)
	{
		$relation_prefix = $input->relation_prefix->none();
		$relation = $input->relation->none();
		$target_type = $input->target_type->none();
		$target_id = $input->target_id->none();
		$source_type = $input->source_type->none();
		$source_id = $input->source_id->none();

		// ensure the target, source, and relation info are passed to the service
		if (! $target_type || ! $target_id || ! $source_type || ! $source_id || ! $relation_prefix) {
			throw new Services_Exception(tr('Invalid input'), 400);
		}

		$relationlib = TikiLib::lib('relation');
		$tx = TikiDb::get()->begin();
		$relations = $relationlib->get_relations_by_prefix($relation_prefix, $source_type, $source_id, $target_type, $target_id);

		// If there is not an existing relation, add the relation and trigger the add relation event.
		$relationWasSelected = false;
		if (! empty($relations)) {
			foreach ($relations as $rel) {
				if ($rel['relation'] == $relation) {
					//sets whether the relation was previously selected and is being toggled off
					$relationWasSelected = true;
				}
				//if there is a relation, remove the relation, trigger the event, and set the relationId to null
				$relationlib->remove_relation($rel['relationId']);
				TikiLib::events()->trigger(
					'tiki.relation.remove',
					array(
						'id' => $rel['relation_id'],
						'target_type' => $target_type,
						'target_id' => $target_id,
						'source_type' => $source_type,
						'source_id' => $source_id,
						'relation' => $relation,
					)
				);
			}
		}

		$relationId = null; // set the return id

		//only adds relation if it hadn't previously been selected. If it was selected, then the user toggled it off.
		if (! $relationWasSelected) {
			$relationId = $relationlib->add_relation($relation, $source_type, $source_id, $target_type, $target_id);
			TikiLib::events()->trigger(
				'tiki.relation.add',
				array(
					'id' => $relationId,
					'target_type' => $target_type,
					'target_id' => $target_id,
					'source_type' => $source_type,
					'source_id' => $source_id,
					'relation' => $relation,
				)
			);
		}

		$tx->commit();

		//return the relationId (new relation if added, null if removed)
		return array(
			'relation_id' => $relationId,
		);
	}
}

