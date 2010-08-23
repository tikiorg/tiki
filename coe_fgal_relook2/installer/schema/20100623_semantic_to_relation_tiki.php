<?php

function pre_20100623_semantic_to_relation_tiki( $installer ) {
	$results = $installer->fetchAll( 'SELECT * FROM tiki_links WHERE reltype IS NOT NULL' );

	foreach( $results as $row ) {
		$reltypes = array_filter( explode( ',', $row['reltype'] ) );

		foreach( $reltypes as $type ) {
			$installer->query( 'INSERT INTO tiki_object_relations ( relation, source_type, source_itemId, target_type, target_itemId ) VALUES( ?, ?, ?, ?, ? )', array(
				'tiki.link.' . $type,
				'wiki page',
				$row['fromPage'],
				'wiki page',
				$row['toPage'],
			) );
		}
	}
}

