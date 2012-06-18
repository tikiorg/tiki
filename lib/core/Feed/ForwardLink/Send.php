<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_ForwardLink_Send extends Feed_Abstract
{
	var $type = "forwardlink_protocol_send";
	var $version = '0.1';
	var $isFileGal = false;
	var $debug = false;
	var $name = "global";

	static function wikiView()
	{
		$me = new self();
		($me->send());
	}

	public static function send()
	{
		global $tikilib;
		$me = new self();
		$sent = array();
		$textlink = new Feed_TextLink();
		$feed = $textlink->feed();

		$items = array();
		//we send something only if we have something to send
		if (!empty($feed->feed->entry)) {
			foreach ($feed->feed->entry as &$item) {
				if (empty($item->forwardlink->href) || isset($sent[$item->forwardlink->hash])) continue;

				$sent[$item->forwardlink->hash] = true;

				$client = new Zend_Http_Client($item->forwardlink->href, array('timeout' => 60));

				if (!empty($feed->feed->entry)) {
					$client->setParameterGet(
						array(
							'protocol'=> 'forwardlink',
							'contribution'=> json_encode($feed)
						)
					);

		            try {
			            if ($me->isAlreadySent($item) == false) {
				            $response = $client->request(Zend_Http_Client::POST);
						    $request = $client->getLastResponse();
						    $result = json_decode($response->getBody());

				            //Here we add the date last updated so that we don't have to send it if not needed, saving load time.
				            if (!empty($result->feed) && $result->feed == "success") {
					            $me->addItem(array(
						            'dateLastUpdated'=> $item->textlink->dateLastUpdated,
						            'textlinkHash'=> $item->textlink->hash,
						            'forwardlinkHash'=> $item->forwardlink->hash
					            ));
				            }
			            }
		            } catch(Exception $e) {}
				}
			}

			return $items;
		}
	}

	function isAlreadySent(&$newItem)
	{
		foreach($this->getContents()->entry as &$existingItem) {
			if (
				$existingItem->dateLastUpdated == $newItem->textlink->dateLastUpdated &&
				$existingItem->textlinkHash == $newItem->textlink->hash &&
				$existingItem->forwardlinkHash == $newItem->forwardlink->hash) {
				return true;
			}
		}
		return false;
	}
}
