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
		if (isset($_POST['protocol']) && isset($_POST['contribution'])  == true && $_POST['protocol'] == 'forwardlink') {
			$me = new self($args['object']);
			$forwardLink = Feed_ForwardLink::forwardLink($args['object']);

			//here we do the confirmation that another wiki is trying to talk with this one
			$_POST['contribution'] = json_decode($_POST['contribution']);
			$_POST['contribution']->origin = $_POST['REMOTE_ADDR'];

			if ($forwardLink->addItem($_POST['contribution']) == true ) {
				$me->response = 'success';
			} else {
				$me->response = 'failure';
			}

			echo json_encode($me->feed(TikiLib::tikiUrl() . 'tiki-index.php?page=' . $args['object']));
			exit();
		}
	}

	public function getContents()
	{
		$this->setEncoding($this->response);
		return $this->response;
	}
}
