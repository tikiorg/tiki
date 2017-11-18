<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_Casperjs_Render
{
	/**
	 * @param WikiPlugin_Casperjs_Result $result
	 * @return string
	 */
	public static function resultAsHTML($result)
	{
		$executionResult = "<h3>CasperJs Execution Details</h3>";
		$executionResult .= "<strong>CasperJS Script</strong>";
		$executionResult .= "<pre>";
		$executionResult .= $result->getCasperJsScript();
		$executionResult .= "</pre>";
		$executionResult .= "<strong>Console Output</strong>";
		$executionResult .= "<pre>";
		$executionResult .= "$ " . $result->getCommandLine() . "\n";
		if (is_array($result->getScriptOutput()) && count($result->getScriptOutput()) > 0) {
			foreach ($result->getScriptOutput() as $line) {
				$executionResult .= $line;
			}
		}
		$executionResult .= "</pre>";
		$executionResult .= "<strong>Tiki Bridge Variables</strong>";
		$executionResult .= "<pre>";
		foreach ($result->getScriptResults() as $key => $value) {
			if (! is_string($value)) {
				$value = print_r($value, true);
			}
			$valueToPrint = str_replace('=&gt;', '=>', htmlspecialchars($value));
			if (strlen($valueToPrint) > 200) {
				$valueToPrint = substr($valueToPrint, 0, 200);
				$valueToPrint .= " ...";
			}
			$executionResult .= $key . " => " . $valueToPrint . "\n";
		}
		$executionResult .= "</pre>";

		return $executionResult;
	}
}
