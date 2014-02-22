<?php

use Types\Type;
use WikiLingo\Event;
use WikiLingo\Expression\Plugin;

class WikiLingoEvents
{
	public static $bridge = null;
	public static $toc = null;
	public static $argumentParser;

	public function __construct(WikiLingo\Parser &$wikiLingoParser)
	{
		require_once('lib/wikiLingo_tiki/TikiWikiPluginBridge.php');
		$bridge = self::$bridge = new TikiWikiPluginBridge();
		Type::Events($wikiLingoParser->events)->bind(new Event\Expression\Plugin\Exists(function(Plugin &$plugin) use ($wikiLingoParser, $bridge) {
			if (!$plugin->exists) {
				switch ($plugin->classType) {
					case "WikiLingo\\Plugin\\Maketoc":
						if (self::$toc == null) {
							self::$toc = new WikiLingo\Plugin\Toc();
						}
						$plugin->exists = true;
						$plugin->class = self::$toc;
						$wikiLingoParser->pluginInstances[$plugin->classType] = self::$toc;
						break;
					default:
						$plugin->exists = true;
						$plugin->class = $bridge;
						$wikiLingoParser->pluginInstances[$plugin->classType] = $bridge;
				}
			}
		}));
	}
}