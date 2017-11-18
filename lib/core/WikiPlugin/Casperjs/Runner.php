<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

class WikiPlugin_Casperjs_Runner
{
	const BASE_MARKER = "TIKI_BRIDGE";
	protected $casperBin;
	protected $casperInstalled = false;

	public function __construct()
	{
		$this->casperBin = TIKI_PATH . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'casperjs';
		if (file_exists($this->casperBin)) {
			$this->casperInstalled = true;
		}
	}

	public function run($script, $options = null, $casperInstance = null)
	{
		$casperScript = tempnam(false, 'casperjs-script-');

		$fullScript =
			$this->scriptPrefix() . "\n"
			. $script . "\n"
			. $this->scriptPostfix($script, $casperInstance);

		file_put_contents($casperScript, $fullScript);

		$optionsString = "";
		if (is_array($options)) {
			foreach ($options as $option => $value) {
				$optionsString .= ' --' . $option . '=' . $value;
			}
		}
		$commandLine = $this->casperBin . ' ' . $casperScript . $optionsString;

		exec($commandLine, $output);
		unlink($casperScript);

		if (empty($output)) {
			throw new \Exception('Can not execute CasperJS.');
		}

		$result = new WikiPlugin_Casperjs_Result($output, $commandLine, $fullScript);

		return $result;
	}

	protected function scriptPrefix()
	{
		$baseMarker = self::BASE_MARKER;
		$prefix = <<<EOT

var tikiBridge = function(){
    var baseMarker='{$baseMarker}';
    var results = {};

    function add(key, result){
        results[key] = result;
    };

    function addGeneric(casperInstance){
        if (typeof casperInstance === 'undefined' || typeof casperInstance.getCurrentUrl === 'undefined'){
            // do not look like a casper Instance
            return;
        }
        add('tikibridge_url', casperInstance.getCurrentUrl());
        add('tikibridge_title', casperInstance.getTitle());
        add('tikibridge_content', casperInstance.getPageContent());
        add('tikibridge_html', casperInstance.getHTML());
    };

    function done(casperInstance){
        addGeneric(casperInstance);
        console.log(baseMarker + '_EXPORT' + JSON.stringify(results));
    };

    var exports = {
        add: add,
        addGeneric: addGeneric,
        done: done
    };

    console.log(baseMarker + '_START');
    return exports;
}();
/* ********************* Tiki Bridge Prefix ********************* */

EOT;

		return $prefix;
	}

	protected function scriptPostfix($script, $casperInstance = null)
	{
		if ($casperInstance === null) {
			$casperInstance = "casper";
		}
		if (strpos($script, $casperInstance . '.run') !== false) {
			return "";
		}

		$postfix = <<<EOT

/* ********************* Tiki Bridge PostFix ********************* */
{$casperInstance}.run(function () {
    tikiBridge.done({$casperInstance});
    {$casperInstance}.exit();
});

EOT;

		return $postfix;
	}
}
