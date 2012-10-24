<?php

class JisonParser_WikiCKEditor_Handler extends JisonParser_Wiki_Handler
{
	function __construct()
	{
		parent::__construct();

		$this->Parser->specialCharacter = new JisonParser_WikiCKEditor_SpecialChar();

		$this->Parser->htmlCharacter = new JisonParser_WikiCKEditor_HtmlCharacter($this->Parser);

		$this->pluginNegotiator = new WikiPlugin_Negotiator_CKEditor($this->Parser);
	}

	function setOption($option = array())
	{
		parent::setOption($option);
	}
}