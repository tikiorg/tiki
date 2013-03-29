<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class ServiceLib
{
	private $broker;

	function getBroker()
	{
		if (! $this->broker) {
			$this->broker = new Services_Broker($this->getControllerMap());
		}

		return $this->broker;
	}

	function getUrl($params)
	{
		global $prefs;

		if ($prefs['feature_sefurl'] == 'y') {
			$url = "tiki-{$params['controller']}";

			if (isset($params['action'])) {
				$url .= "-{$params['action']}";
			}

			unset($params['controller']);
			unset($params['action']);
		} else {
			$url = 'tiki-ajax_services.php';
		}

		if (count($params)) {
			$url .= '?' . http_build_query($params, '', '&');
		}

		return $url;
	}

	private function getControllerMap()
	{
		return array(
			'auth_source' => 'Services_AuthSource_Controller',
			'bigbluebutton' => 'Services_BigBlueButton_Controller',
			'calendar' => 'Services_Calendar_Controller',
			'category' => 'Services_Category_Controller',
			'comment' => 'Services_Comment_Controller',
			'connect_server' => 'Services_Connect_Server',
			'connect' => 'Services_Connect_Client',
			'draw' => 'Services_Draw_Controller',
			'favorite' => 'Services_Favorite_Controller',
			'file_finder' => 'Services_File_FinderController',
			'file' => 'Services_File_Controller',
			'jcapture' => 'Services_JCapture_Controller',
			'jison'=> 'Services_JisonParser_WikiPlugin',
			'kaltura'=>  'Services_Kaltura_Controller',
			'object' => 'Services_Object_Controller',
			'rating'=>  'Services_Rating_Controller',
			'report' => 'Services_Report_Controller',
			'tracker_calendar' => 'Services_Tracker_CalendarController',
			'search_customsearch' => 'Services_Search_CustomSearchController',
			'tracker' => 'Services_Tracker_Controller',
			'tracker_sync' => 'Services_Tracker_SyncController',
			'tracker_todo' => 'Services_Tracker_TodoController',
			'translation' => 'Services_Language_TranslationController',
			'user' => 'Services_User_Controller',
			'wiki' => 'Services_Wiki_Controller',
			'workspace'=>  'Services_Workspace_Controller',
		);
	}
}

