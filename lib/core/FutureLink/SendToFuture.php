<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

// File name: Send.php
// Required path: /lib/core/FutureLink
//
// Programmer: Robert Plummer
//
// Purpose: Send a pastlink to a futurelink

Class FutureLink_SendToFuture extends Feed_Abstract
{
	var $type = 'futurelink';
	var $version = 0.1;
	var $isFileGal = false;

	static function sendAll()
	{
		$me = new self();
		return($me->send());
	}

	public static function send()
	{
		$me = new self("global");
		$sent = array();
		$pastlink = new FutureLink_PastUI();
		$feed = $pastlink->feed();

		$items = array();
		//we send something only if we have something to send
		if (empty($feed->feed->entry) == false) {
			foreach ($feed->feed->entry as &$item) {
				if (empty($item->futurelink->href) || isset($sent[$item->futurelink->hash])) continue;

				$sent[$item->futurelink->hash] = true;
				$client = new Zend\Http\Client($item->futurelink->href, array('timeout' => 60));

				if (!empty($feed->feed->entry)) {
					$client->setParameterPost(
						array(
							'protocol'=> 'futurelink',
							'metadata'=> json_encode($feed)
						)
					);

						try {
							$client->setMethod(Zend\Http\Request::METHOD_POST);
							$response = $client->send();
							$request = $client->getLastResponse();
			                $result = $response->getBody();
							$resultJson = json_decode($response->getBody());

			            //Here we add the date last updated so that we don't have to send it if not needed, saving load time.
			            if (!empty($resultJson->feed) && $resultJson->feed == "success") {
								$me->addItem(
									array(
						            'dateLastUpdated'=> $item->pastlink->dateLastUpdated,
						            'pastlinklinkHash'=> $item->pastlink->hash,
						            'futurelinkHash'=> $item->futurelink->hash
					            )
								);
			            }

			            $items[$item->pastlink->text] = $result;

						} catch(Exception $e) {
						}
				}
			}

			return $items;
		}
	}
}
