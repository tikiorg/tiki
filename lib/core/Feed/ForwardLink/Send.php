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
		global $textlinkContribution, $textlinkAddedHashes;
		if (empty($textlinkContribution)) {
			$textlinkContribution = (object)array();
			$textlinkContribution->entry = array();
		}
		
		$item = (object)$item;
		if (isset($textlinkAddedHashes[$item->textlink->hash])) return;
		$textlinkAddedHashes[$item->textlink->hash] = true;
		$item->forwardlink->href = str_replace(' ', '+', $item->forwardlink->href);
		
		$textlinkContribution->entry[] = $item;
	}

	static function wikiView()
	{
		$me = new self();

		Feed_ForwardLink_Search::restoreTextLinkPhrasesInWikiPage($me->getItems(), $_REQUEST['phrase']);

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
		$sent = array();
		$feed = $me->feed();

		$result = array();
		//we send something only if we have something to send
		if (!empty($feed->feed->entry)) {
			foreach ($feed->feed->entry as $item) {
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
					    $response = $client->request(Zend_Http_Client::POST);
					    $request = $client->getLastResponse();
					    $result[] = $response->getBody();
		            } catch(Exception $e) {
			            $result[] = "";
		            }
				}
			}

			return $result;
		}
	}
}
