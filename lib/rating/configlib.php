<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: tiki-action_calendar.php 25076 2010-02-11 15:53:20Z changi67 $

class RatingConfigLib extends TikiDb_Bridge
{
	function get_configurations() {
		return $this->fetchAll( 'SELECT * FROM `tiki_rating_configs`' );
	}

	function create_configuration( $name ) {
		$this->query( 'INSERT INTO `tiki_rating_configs` ( `name`, `formula` ) VALUES( ?, ? )',
			array( $name, '(rating-average (object type object-id))' ) );

		return $this->lastInsertId();
	}

	function update_configuration( $id, $name, $expiry, $formula ) {
		$this->query( 'UPDATE `tiki_rating_configs` SET `name` = ?, `expiry` = ?, `formula` = ? WHERE `ratingConfigId` = ?',
			array( $name, $expiry, $formula, $id ) );
	}
}

global $ratingconfiglib;
$ratingconfiglib = new RatingConfigLib;

