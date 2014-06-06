<?php
// (c) Copyright 2002-2013 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_Suite_Controller
{
	function setUp()
	{
		Services_Exception_Disabled::check('suite_jitsi_provision');
	}

	public static function getJitsiUrl()
	{
		$url = TikiLib::lib('service')->getUrl(
			array(
				'controller' => 'suite',
				'action' => 'jitsi'
			));
		return TikiLib::tikiUrl($url) . '?username=${username}&password=${password}';
	}

	function action_jitsi($input)
	{
		global $prefs;
		$config = $prefs['suite_jitsi_configuration'];
		$config = str_replace(array('${username}', '${password}'),
			array(
				$input->username->none(),
				$input->password->none()
			),
			$config);
		die($config); // FIXME: we need to do this or in order to omit tiki.tpl
		return array(
			'configuration' => $config
		);
	}
}

