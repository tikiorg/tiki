<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Favorite_Controller
{
	function action_list($input)
	{
		global $user, $prefs;
		
		if ($prefs['user_favorites'] !== 'y') {
			throw new Services_Exception(tr('Feature disabled'), 403);
		}

		if (! $user) {
			return array();
		}

		$relationlib = TikiLib::lib('relation');
		$favorites = array();
		foreach ($relationlib->get_relations_from('user', $user, 'tiki.user.favorite') as $relation) {
			$favorites[$relation['relationId']] = $relation['type'] . ':' . $relation['itemId'];
		}

		return $favorites;
	}

	function action_toggle($input)
	{
		global $user, $prefs;

		if ($prefs['user_favorites'] !== 'y') {
			throw new Services_Exception(tr('Feature disabled'), 403);
		}

		if (! $user) {
			throw new Services_Exception(tr('Must be authenticated'), 403);
		}

		$type = $input->type->none();
		$object = $input->object->none();
		$target = $input->target->int();

		if (! $type || ! $object) {
			throw new Services_Exception(tr('Invalid input'), 400);
		}

		$relationlib = TikiLib::lib('relation');
		$relations = $this->action_list($input);

		if ($target) {
			if (! in_array("$type:$object", $relations) && $relationId = $relationlib->add_relation('tiki.user.favorite', 'user', $user, $type, $object)) {
				$relations[$relationId] = "$type:$object";
			}
		} else {
			foreach ($relations as $id => $key) {
				if ($key === "$type:$object") {
					$relationlib->remove_relation($id);
					unset($relations[$id]);
				}
			}
		}

		return array(
			'list' => $relations,
		);
	}
}

