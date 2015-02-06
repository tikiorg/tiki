<?php
// (c) Copyright 2002-2015 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

namespace Tiki\MailIn\Provider;
use Tiki\MailIn\Action;

class WikiMultipleProvider implements ProviderInterface
{
	function isEnabled()
	{
		global $prefs;
		return $prefs['feature_wiki'] == 'y';
	}

	function getType()
	{
		return 'wiki';
	}

	function getLabel()
	{
		return tr('Wiki (multiple actions)');
	}

	function getActionFactory(array $acc)
	{
		$wikiParams = [
			'namespace' => $acc['namespace'],
			'structure_routing' => $acc['routing'] == 'y',
		];

		return new Action\SubjectPrefixFactory(array(
			'GET:' => new Action\DirectFactory('Tiki\MailIn\Action\WikiGet', $wikiParams),
			'APPEND:' => new Action\DirectFactory('Tiki\MailIn\Action\WikiAppend', $wikiParams),
			'PREPEND:' => new Action\DirectFactory('Tiki\MailIn\Action\WikiPrepend', $wikiParams),
			'PUT:' => new Action\DirectFactory('Tiki\MailIn\Action\WikiPut', $wikiParams),
			'' => new Action\DirectFactory('Tiki\MailIn\Action\WikiPut', $wikiParams),
		));
	}
}
