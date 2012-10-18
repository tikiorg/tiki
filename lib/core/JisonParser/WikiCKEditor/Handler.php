<?php

class JisonParser_WikiCKEditor_Handler extends JisonParser_Wiki_Handler
{
	function __construct()
	{
		parent::__construct();

		if (isset($this->Parser->htmlCharacter) == false) {
			$this->Parser->htmlCharacter = new JisonParser_WikiCKEditor_HtmlCharacter($this->Parser);
		}

		$this->pluginNegotiator = new WikiPlugin_Negotiator_CKEditor($this->Parser);
	}

	function setOption($option = array())
	{
		$this->Parser->option['is_html'] = true;

		parent::setOption($option);
	}
}