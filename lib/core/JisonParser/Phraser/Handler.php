<?php
class JisonParser_Phraser_Handler extends JisonParser_Phraser_Parser
{
	var $chars = array();
	var $words = array();
	var $tags = array();
	var $currentWord = -1;
	var $wordsChars = array();
	var $Parser;
	
	function __construct()
	{
		$Parser = $this;
	}
	
	function tagHandler($tag)
	{
		$this->tags[] = $tag;
		return $tag;
	}
	
	function wordHandler($word)
	{
		$this->currentWord++;
		$this->words[] = $word;
		return $word;
	}
	
	function charHandler($char)
	{
		$this->wordsChars[$this->currentWord] .= $char;
		$this->chars[] = $char;
		return $char;
	}
}
