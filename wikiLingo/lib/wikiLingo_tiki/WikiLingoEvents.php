<?php

use Types\Type;
use WikiLingo\Event;
use WikiLingo\Expression\Plugin;

class WikiLingoEvents
{
	public static $bridge;
	public static $argumentParser;

	public function __construct(WikiLingo\Parser &$wikiLingoParser)
	{
		require_once('lib/wikiLingo_tiki/TikiWikiPluginBridge.php');
		$bridge = self::$bridge = new TikiWikiPluginBridge();
		Type::Events($wikiLingoParser->events)->bind(new Event\Expression\Plugin\Exists(function(Plugin &$plugin) use ($wikiLingoParser, $bridge) {
			if (!$plugin->exists) {
				$plugin->exists = true;
				$plugin->class = $bridge;
				$wikiLingoParser->pluginInstances[$plugin->classType] = $bridge;
			}
		}));
	}
}