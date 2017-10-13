<?php

/**
 * Data migration script when converting 2 ItemLink fields to a Relations field with inverted relation at the other end.
 * Specify both relation names as In and Out.
 * Script will make sure all Out relations are conserved as In relations and delete unnecessary Out relations.
 * The end result should be similar to 2 ItemLinks setup but relations stored only on one side and visible/editable on both sides.
 */

$relationIn = 'correspondencein.crincrou.items';
$relationOut = 'correspondenceout.croucrin.items';

require_once ('tiki-setup.php');

$relationlib = TikiLib::lib('relation');
$tx = $relationlib->begin();

$result = $relationlib->fetchAll("select * from tiki_object_relations where relation = ?", array($relationOut));
foreach ($result as $row) {
	$relationlib->add_relation($relationIn, $row['target_type'], $row['target_itemId'], $row['source_type'], $row['source_itemId'], true);
	$relationlib->table('tiki_object_relations')->delete(array('relationId' => $row['relationId']));
}
$tx->commit();

$searchlib = TikiLib::lib('unifiedsearch');
$searchlib->processUpdateQueue(100000);
