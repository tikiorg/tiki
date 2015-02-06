<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// File name: Receive.php
// Required path: /lib/core/FutureLink
//
// Programmer: Robert Plummer
//
// Purpose: Verify that incoming URI is requesting a FutureLink destination on this site and redirect accordingly.

Class FutureLink_ReceiveFromPast extends Feed_Abstract
{
	var $type = "futurelink";
	var $isFileGal = false;
	var $version = 0.1;
	var $showFailures = false;
	var $response = 'failure';

	static function wikiView($args)
	{
        //TODO: abstract
		if (isset($_POST['protocol']) && $_POST['protocol'] == 'futurelink' && isset($_POST['metadata'])) {
			$me = new self($args['object']);
			$futureLink = new FutureLink_FutureUI($args['object']);

			//here we do the confirmation that another wiki is trying to talk with this one
			$metadata = json_decode($_POST['metadata']);
			$metadata->origin = $_POST['REMOTE_ADDR'];

			if ($futureLink->addItem($metadata) == true) {
				$me->response = 'success';
			} else {
				$me->response = 'failure';
			}

			$feed = $me->feed(TikiLib::tikiUrl() . 'tiki-index.php?page=' . $args['object']);

			if (
				$me->response == 'failure' &&
				$futureLink == true
			) {
				$feed->reason = $futureLink->verifications;
			}

			echo json_encode($feed);
			exit();
		}
	}

	public function getContents()
	{
		$this->setEncoding($this->response);
		return $this->response;
	}
}
