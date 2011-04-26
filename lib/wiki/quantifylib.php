<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require_once 'lib/diff.php';

class QuantifyLib extends TikiLib
{

	function recordChangeSize( $pageId, $version, $oldData, $newData )
	{
		global $prefs;

		if( $prefs['quantify_changes'] != 'y' || $prefs['feature_multilingual'] != 'y' )
			return;

		list( $added, $removed, $complete ) = $this->calculateChangeSize( $oldData, $newData );

		$this->query( "INSERT INTO tiki_pages_changes (page_id, version, segments_added, segments_removed, segments_total) VALUES(?,?,?,?,?) ON DUPLICATE KEY update segments_added = ?, segments_removed = ?, segments_total = ?",
			array( $pageId, $version, $added, $removed, $complete, $added, $removed, $complete ) );
	}

	function calculateChangeSize( $oldData, $newData )
	{
		$oldData = $this->segmentData( $oldData );
		$newData = $this->segmentData( $newData );
		
		$engine = new _WikiDiffEngine( $oldData, $newData );

		$added = 0;
		$removed = 0;

		foreach( $engine->edits as $key => $modif )
		{
			if( is_array( $modif ) )
				$added += count( $modif );
			elseif( is_int( $modif ) && $modif < 0 )
				$removed -= $modif;
		}

		return array( $added, $removed, count( $newData ) );
	}

	function segmentData( $data )
	{
		$data = preg_replace( "/\.\s/", "\n", $data );
		$segments = explode( "\n", $data );
		$segments = array_map( 'trim', $segments );

		$final = array();
		foreach( $segments as $seg )
			if( ! empty( $seg ) )
				$final[] = $seg;

		return $final;
	}

	function getCompleteness( $pageId )
	{
		$value = $this->getOne( "
			SELECT
				tpc.segments_total / ( tpc.segments_total + IFNULL(SUM(valid.segments_added), 0) + IFNULL(SUM(valid.segments_removed),0)/10 )
			FROM

				tiki_pages tp
				INNER JOIN tiki_pages_changes tpc ON tp.page_id = tpc.page_id AND tp.version = tpc.version
				INNER JOIN tiki_translated_objects a ON a.objId = tpc.page_id AND a.type = 'wiki page'
				INNER JOIN tiki_translated_objects b ON b.traId = a.traId AND b.type = 'wiki page' AND b.objId <> a.objId
				LEFT JOIN (
					SELECT DISTINCT
						b1.page_id, b1.version, segments_added, segments_removed
					FROM
						tiki_pages_changes tpc2
						INNER JOIN tiki_pages_translation_bits b1 ON tpc2.page_id = b1.page_id AND tpc2.version = b1.version
						LEFT JOIN tiki_pages_translation_bits b2 ON b1.translation_bit_id = IFNULL(b2.original_translation_bit, b2.translation_bit_id) AND b2.page_id = ?
					WHERE
						b1.original_translation_bit IS NULL
						AND b2.translation_bit_id IS NULL
				) valid ON b.objId = valid.page_id
			WHERE
				tpc.page_id = ?
			GROUP BY tpc.page_id, tpc.segments_total, tpc.version
		", array( $pageId, $pageId ) );

		return floor( $value * 100 ); 
	}

	function wiki_update($arguments)
	{
		$tikilib = TikiLib::lib('tiki');
		$this->recordChangeSize($arguments['page_id'], $arguments['version'], $arguments['old_data'], $arguments['data']);
	}
}
$quantifylib = new QuantifyLib;

