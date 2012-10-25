<?php
// (c) Copyright 2002-2012 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class JisonParser_Wiki_HtmlCharacter
{
	private $parser;
	public $chars = array(
		'&'     => array('exp' => '/&/',      'output' => '&amp;'),
		'bs'    => array('exp' => '/~bs~/i',    'output' => '&#92;'),
		'hs'    => array('exp' => '/~hs~/i',    'output' => '&nbsp;'),
		'amp'   => array('exp' => '/~amp~/i',   'output' => '&amp;'),
		'ldq'   => array('exp' => '/~ldq~/i',   'output' => '&ldquo;'),
		'rdq'   => array('exp' => '/~rdq~/i',   'output' => '&rdquo;'),
		'lsq'   => array('exp' => '/~lsq~/i',   'output' => '&lsquo;'),
		'rsq'   => array('exp' => '/~rsq~/i',   'output' => '&rsquo;'),
		'c'     => array('exp' => '/~c~/i',     'output' => '&copy;'),
		'--'    => array('exp' => '/~--~/',     'output' => '&mdash;'),
		'lt'    => array('exp' => '/~lt~/i',    'output' => '&lt;'),
		'gt'    => array('exp' => '/~gt~/i',    'output' => '&gt;'),
		'rm'    => array('exp' => '/[{]rm[}]/i','output' => '&rlm;'),
		// HTML numeric character entities
		'num'   => array('exp' => '/~([0-9]+)~/','output' =>'&#$1;'),
	);

	function __construct(JisonParser_Wiki_Handler &$parser)
	{
		$this->parser = &$parser;
	}

	function parse(&$content)
	{
		foreach ($this->chars as &$char) {
			$content = preg_replace($char['exp'], $char['output'], $content);
		}
	}
}
