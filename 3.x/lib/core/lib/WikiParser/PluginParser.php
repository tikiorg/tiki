<?php

class WikiParser_PluginParser
{
	private $argumentParser;
	private $pluginRunner;

	function parse( $text )
	{
		if( ! $this->argumentParser || ! $this->pluginRunner )
			return $text;


	}

	function setArgumentParser( /* WikiParser_PluginArgumentParser */ $parser )
	{
		$this->argumentParser = $parser;
	}

	function setPluginRunner( /* WikiParser_PluginRunner */ $runner )
	{
		$this->pluginRunner = $runner;
	}
}

?>
