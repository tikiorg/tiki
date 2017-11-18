<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_Casperjs_Result
{
	protected $output;
	protected $result;
	protected $validOutput = false;
	protected $commandLine;
	protected $casperJsScript;

	public function __construct($commandOutput, $commandLine, $casperJsScript, $prefix = "TIKI_BRIDGE")
	{
		$this->commandLine = $commandLine;
		$this->casperJsScript = $casperJsScript;

		$this->output = [];

		$tagStart = $prefix . "_START";
		$tagStartLen = strlen($tagStart);
		$tagExport = $prefix . "_EXPORT";
		$tagExportLen = strlen($tagExport);
		foreach ($commandOutput as $line) {
			if (strncmp($tagStart, $line, $tagStartLen) == 0) {
				$this->validOutput = true;
				continue;
			}
			if (strncmp($tagExport, $line, $tagExportLen) == 0) {
				$this->result = json_decode(substr($line, $tagExportLen));
				continue;
			}
			$this->output[] = $line;
		}
	}

	public function getScriptOutput()
	{
		return $this->output;
	}

	public function getScriptResults()
	{
		return $this->result;
	}

	public function getCommandLine()
	{
		return $this->commandLine;
	}

	public function getCasperJsScript()
	{
		return $this->casperJsScript;
	}
}
