<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id: wikiplugin_list.php 44375 2012-12-21 18:39:49Z lphuberdeau $

class Search_Formatter_Builder
{
	private $formatter;

	function __construct($formatter)
	{
		$this->formatter = $formatter;
	}

	function apply($matches)
	{
		$argumentParser = new WikiParser_PluginArgumentParser;

		foreach ($matches as $match) {
			if ($match->getName() == 'format') {
				$arguments = $argumentParser->parse($match->getArguments());

				if (isset($arguments['name'])) {
					$plugin = new Search_Formatter_Plugin_WikiTemplate($match->getBody());
					$this->formatter->addSubFormatter($arguments['name'], $plugin);
				}
			}
		}
	}
}

