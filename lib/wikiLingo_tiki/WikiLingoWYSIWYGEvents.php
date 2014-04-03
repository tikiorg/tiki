<?php

use Types\Type;
use WikiLingo\Event;
use WikiLingo\Expression\Plugin;

class WikiLingoWYIWYGEvents
{
	public static $bridge;
	public static $argumentParser;

	public function __construct(WikiLingoWYSIWYG\Parser &$wikiLingoParser)
	{
		require_once('lib/wikiLingo_tiki/WikiPluginStub.php');
		$stub = self::$bridge = new WikiPluginStub();
		Type::Events($wikiLingoParser->events)->bind(new Event\Expression\Plugin\Exists(function(Plugin &$plugin) use ($wikiLingoParser, $stub) {
			if (!$plugin->exists) {
				$plugin->exists = true;
				$plugin->class = $stub;
				$wikiLingoParser->pluginInstances[$plugin->classType] = $stub;
			}
		}));
	}
}