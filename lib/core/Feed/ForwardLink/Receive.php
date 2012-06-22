<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_ForwardLink_Receive extends Feed_Abstract
{
	var $type = "forwardlink";
	var $isFileGal = false;
	var $version = 0.1;
	var $showFailures = false;
	var $response = 'failure';

	static function wikiView($args)
	{
		if (isset($_GET['protocol'], $_GET['contribution']) == true && $_GET['protocol'] == 'forwardlink') {
			$me = new self($args['object']);
			$forwardLink = Feed_ForwardLink::forwardLink($args['object']);

			//here we do the confirmation that another wiki is trying to talk with this one
			$_GET['contribution'] = json_decode($_GET['contribution']);
			$_GET['contribution']->origin = $_GET['REMOTE_ADDR'];

			if ($forwardLink->addItem($_GET['contribution']) == true) {
				$me->response = 'success';
			} else {
				$me->response = 'failure';
			}

			$feed = $me->feed(TikiLib::tikiUrl() . 'tiki-index.php?page=' . $args['object']);

			if (
				$me->response == 'failure' &&
				$forwardLink == true
			) {
				$feed->reason = $forwardLink->verifications;
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
