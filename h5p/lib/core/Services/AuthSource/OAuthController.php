<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class Services_AuthSource_OAuthController
{
	function action_request($input)
	{
		$oauthlib = TikiLib::lib('oauth');

		$oauthlib->request_token($input->provider->word());

		// Previous line is expected to redirect
		throw new Services_Exception_NotFound('Provider does not exist');
	}

	function action_callback($input)
	{
		$oauthlib = TikiLib::lib('oauth');

		// Restore $_GET to original state for processing by OAuth Consumer
		global $jitGet;
		$_GET = $jitGet->none();

		$oauthlib->request_access($input->oauth_callback->word());
		$access = TikiLib::lib('access');
		$access->redirect('');
	}
}

