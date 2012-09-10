<?php

class JisonParser_WikiCKEditor_Handler extends JisonParser_Wiki_Handler
{
	function __construct()
	{
		if (isset($this->Parser->htmlCharacter) == false) {
			$this->Parser->htmlCharacter = new JisonParser_WikiCKEditor_HtmlCharacter($this->Parser);
		}

		parent::__construct();
	}

	function setOption($option = array())
	{
		$this->Parser->option['is_html'] = true;

		parent::setOption($option);
	}

	function getPluginNegotiator()
	{
		if (empty($this->pluginNegotiators['WikiPlugin_CKEditorNegotiator'])) {
			$this->pluginNegotiators['WikiPlugin_CKEditorNegotiator'] = new WikiPlugin_CKEditorNegotiator($this->Parser);
		}

		return $this->pluginNegotiators['WikiPlugin_CKEditorNegotiator'];
	}
}