<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

Class Feed_ForwardLink_Send extends Feed_Abstract
{
	var $type = "textlink";
	var $version = "0.1";
	
	static function add($item = array())
	{
		global $textlinkContribution;
		if (empty($textlinkContribution)) {
			$textlinkContribution = (object)array();
			$textlinkContribution->entry = array();
		}
		
		$item = (object)$item;
		$item->forwardlink->href = str_replace(' ', '+', $item->forwardlink->href);
		$exists = false;
		
		foreach ($textlinkContribution as $existingItem) {
			if (isset($existingItem->textlink['id']) && isset($item->textlink['id']) && $existingItem->textlink['id'] == $item->textlink['id']) {
				$exists = true;
				
			}
		}
		
		if ($exists == false) $textlinkContribution->entry[] = $item;
	}

	static function wikiView()
	{
		$me = new self();
		($me->send());
	}
	
	public function getContents()
	{
		global $textlinkContribution;
		
		if (!empty($textlinkContribution)) {
			$this->setEncoding(TikiFilter_PrepareInput::delimiter('_')->toString($textlinkContribution));
			
			return $textlinkContribution;
		}
	
		return array();
	}
	
	public static function send()
	{
		global $tikilib;
		$me = new self();
		$entry = array();
		$lastModif = 0;

		$feed = $me->feed();
		
		foreach ($feed->feed->entry as $item) {
			if (empty($item->forwardlink->href)) continue;

			$client = new Zend_Http_Client($item->forwardlink->href, array('timeout' => 60));

			$info = $tikilib->get_page_info($item->page);

			if ($info['lastModif'] > $lastModif)
				$lastModif = $info['lastModif'];
		}

		if (!empty($feed->feed->entry)) {
			$client->setParameterGet(
							array(
								'protocol'=> 'forwardlink',
								'contribution'=> json_encode($feed)
							)
			);

            try {
			    $response = $client->request(Zend_Http_Client::POST);
			    $request = $client->getLastResponse();
			    return $response->getBody();
            } catch(Exception $e) {
                return "";
            }
		}
	}
}
