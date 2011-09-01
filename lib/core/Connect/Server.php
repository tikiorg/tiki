<?php
// (c) Copyright 2002-2011 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: TikiConnect.php 36763 2011-09-01 15:29:33Z jonnybradley $

require_once('lib/core/Connect/Abstract.php');

class Connect_Server extends Connect_Abstract
{

	/**
	 * Gets a summary of connections
	 * 
	 * @return array
	 */

	function getReceivedDataStats() {
		global $prefs;

		$ret = array();

		if ($prefs['connect_server_mode'] === 'y') {
			$ret['received'] = $this->connectTable->fetchCount(
				array(
					'type' => 'received',
					'server' => 1,
				)
			);
		}

		// select distinct guid from tiki_connect where server=1;
		$res = TikiLib::lib('tiki')->getOne('SELECT COUNT(DISTINCT `guid`) FROM `tiki_connect` WHERE `server` = 1 AND `type` = \'received\';');

		$ret['guids'] = $res;
		
		return $ret;
	}

	/**
	 * test if a guid is pending
	 * Connect Server
	 *
	 * @param string $guid
	 * @return string
	 */

	function isPendingGuid( $guid ) {
		$res = $this->connectTable->fetchOne(
			'data',
			array(
				'type' => 'pending',
				'server' => 1,
				'guid' => $guid,
			)
		);
		return $res;
	}

	/**
	 * text if a guid is confirmed here
	 * Connect Server
	 *
	 * @param string $guid
	 * @return bool
	 */

	function isConfirmedGuid( $guid ) {
		$res = $this->connectTable->fetchCount(
			array(
				'type' => 'confirmed',
				'server' => 1,
				'guid' => $guid,
			)
		);
		return $res > 0;
	}
}
