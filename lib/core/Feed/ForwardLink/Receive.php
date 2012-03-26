<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_ForwardLink_Receive extends Feed_Abstract
{
	var $type = "forwardlink";
	var $name = "";
	var $isFileGal = true;
	var $version = "0.1";
	var $showFailures = false;
	var $response = 'failure';

	static function forwardLink($name)
	{
		$me = new self();
		$me->name = $name;
		return $me;
	}

	static function wikiView($args)
	{
		if (isset($_REQUEST['protocol'], $_REQUEST['contribution']) && $_REQUEST['protocol'] == 'forwardlink') {
			$me = Feed_ForwardLink_Receive::forwardLink($args['object']);

			//here we do the confirmation that another wiki is trying to talk with this one
			$_REQUEST['contribution'] = json_decode($_REQUEST['contribution']);
			$_REQUEST['contribution']->origin = $_SERVER['REMOTE_ADDR'];

			if ($me->addItem($_REQUEST['contribution']) == true ) {
				$me->response = 'success';
			}

			echo json_encode($me->feed(TikiLib::tikiUrl() . 'tiki-index.php?page=' . $args['object']));
			exit();
		}
	}

	public function name()
	{
		return $this->type . "_" . $this->name;
	}
	
	function appendToContents(&$contents, $item)
	{
		global $prefs, $_REQUEST;
		$replace = false;		
		
		//lets remove the newentry if it has already been accepted in the past
		foreach ($contents->entry as $i => $existingEntry) {
			foreach ($item->feed->entry as $j => $newEntry) {
				if (
					$existingEntry->textlink->text == $newEntry->textlink->text &&
					$existingEntry->textlink->href == $newEntry->textlink->href
				) {
					unset($item->feed->entry[$j]);
				}
			}
		}
		
		//lets check if the hash is correct and that the phrase actually exists within the wiki page
		foreach ($item->feed->entry as $i => $newEntry) {
			
			if ($this->showFailures) {
				print_r(
								array(
									"hashIncluded"=>	$newEntry->forwardlink->hash,
									"hashCalculated"=> 	hash_hmac("md5", htmlspecialchars($prefs['browsertitle']), $newEntry->forwardlink->text),
									"metadata"=> 		($newEntry->forwardlink->websiteTitle != $prefs['browsertitle']),
									"hasPhrase"=> 		(JisonParser_Phraser_Handler::hasPhrase(TikiLib::lib("wiki")->get_parse($_REQUEST['page']), $newEntry->forwardlink->text)),
									"page"=>			$_REQUEST['page']
					)
				);
			}
			
			if (
				$newEntry->forwardlink->hash != hash_hmac("md5", htmlspecialchars($prefs['browsertitle']), $newEntry->forwardlink->text) ||
				$newEntry->forwardlink->websiteTitle != $prefs['browsertitle'] ||
				JisonParser_Phraser_Handler::hasPhrase(TikiLib::lib("wiki")->get_parse($_REQUEST['page']), $newEntry->forwardlink->text) != true
			) {
				unset($item->feed->entry[$i]);
			}
		}
		
		if (count($item->feed->entry) > 0) {
			$replace = true;
			$contents->entry += $item->feed->entry;
		}
		
		return $replace;
	}
}
