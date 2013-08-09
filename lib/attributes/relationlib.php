<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * RelationLib
 *
 * @uses TikiDb_Bridge
 */
class RelationLib extends TikiDb_Bridge
{
	private $table;

	function __construct()
	{
		$this->table = $this->table('tiki_object_relations');
	}

	/**
	 * Obtains the list of relations with a given object as the source.
	 * Optionally, the relation searched for can be specified. If the
	 * relation ends with a dot, it will be used as a wildcard.
	 */
	function get_relations_from( $type, $object, $relation = null, $orderby = '', $max = -1 )
	{
		if ( substr($relation, -7) === '.invert' ) {
			return $this->get_relations_to($type, $object, substr($relation, 0, -7), $orderby, $max);
		}

		$cond = array(
			'source_type' => $type,
			'source_itemId' => $object
		);

		$fields = array(
			'relationId',
			'relation',
			'type' => 'target_type',
			'itemId' => 'target_itemId',
		);

		$cond = $this->apply_relation_condition($relation, $cond);
		return $this->table->fetchAll($fields, $cond, $max, -1, $orderBy);
	}

    /**
     * @param $type
     * @param $object
     * @param null $relation
     * @return mixed
     */
    function get_relations_to( $type, $object, $relation = null, $orderBy = '', $max = -1)
	{
		if ( substr($relation, -7) === '.invert' ) {
			return $this->get_relations_from($type, $object, substr($relation, 0, -7), $orderBy, $max);
		}

		$cond = array(
			'target_type' => $type,
			'target_itemId' => $object
		);

		$fields = array(
			'relationId',
			'relation',
			'type' => 'source_type',
			'itemId' => 'source_itemId',
		);

		$cond = $this->apply_relation_condition($relation, $cond);
		return $this->table->fetchAll($fields, $cond, $max, -1, $orderBy);
	}

	/**
	 * The relation must contain at least two dots and only lowercase letters.
	 * NAMESPACE management and relation naming.
	 * Please see http://dev.tiki.org/Object+Attributes+and+Relations for guidelines on
	 * relation naming, and document new tiki.*.* names that you add.
	 * (also grep "add_relation" just in case there are undocumented names already used)
	 */
	function add_relation( $relation, $src_type, $src_object, $target_type, $target_object )
	{
		$relation = TikiFilter::get('attribute_type')->filter($relation);

		if ( substr($relation, -7) === '.invert' ) {
			return $this->add_relation(substr($relation, 0, -7), $target_type, $target_object, $src_type, $src_object);
		}

		if ( $relation ) {
			if (! $id = $this->get_relation_id($relation, $src_type, $src_object, $target_type, $target_object)) {
				$id = $this->table->insert(array(
					'relation' => $relation,
					'source_type' => $src_type,
					'source_itemId' => $src_object,
					'target_type' => $target_type,
					'target_itemId' => $target_object,
				));
			}
		} else {
			return 0;
		}
	}

    /**
     * @param $relation
     * @param $src_type
     * @param $src_object
     * @param $target_type
     * @param $target_object
     * @return int
     */
    function get_relation_id( $relation, $src_type, $src_object, $target_type, $target_object )
	{
		$relation = TikiFilter::get('attribute_type')->filter($relation);

		if ( substr($relation, -7) === '.invert' ) {
			return $this->get_relation_id(substr($relation, 0, -7), $target_type, $target_object, $src_type, $src_object);
		}

		$id = 0;
		if ( $relation ) {
			$id = $this->table->fetchOne('relationId', array(
				'relation' => $relation,
				'source_type' => $src_type,
				'source_itemId' => $src_object,
				'target_type' => $target_type,
				'target_itemId' => $target_object,
			));
		}
		return $id;
	}

    /**
     * @param $id
     * @return mixed
     */
    function get_relation( $id )
	{
		return $this->table->fetchFullRow(array(
			'relationId' => $id,
		));
	}

    /**
     * @param $id
     */
    function remove_relation( $id )
	{
		$this->table->delete(array(
			'relationId' => $id,
		));
		$this->table('tiki_object_attributes')->deleteMultiple(array(
			'type' => 'relation',
			'itemId' => $id,
		));
	}

    /**
     * @param $relation
     * @param $cond
     * @param $vars
     */
    private function apply_relation_condition( $relation, $cond )
	{
		$relation = TikiFilter::get('attribute_type')->filter($relation);

		if ( $relation ) {
			if ( substr($relation, -1) == '.' ) {
				$relation .= '%';
			}

			$cond['relation'] = $this->table->like($relation);
		}

		return $cond;
	}
}

global $relationlib;
$relationlib = new RelationLib;
