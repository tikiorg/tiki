<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

/**
 * @file
 * Customised version of HTMLPurifier.func.php for easy use in Tiki
 * This overrides the HTMLPurifier() function in HTMLPurifier.func.php
 *
 * Defines a function wrapper for HTML Purifier for quick use.
 * @note ''HTMLPurifier()'' is NOT the same as ''new HTMLPurifier()''
 */

/**
 * Purify HTML.
 * @param $html String HTML to purify
 * @param $config Configuration to use, can be any value accepted by
 *        HTMLPurifier_Config::create()
 */

require_once('lib/htmlpurifier/HTMLPurifier.auto.php');

function HTMLPurifier($html, $config = null) {
	static $purifier = false;
	if (!$purifier) {
		if ($config == null) {	// mod for tiki temp files location
			$config = getHTMLPurifierTikiConfig();
    	}
		$purifier = new HTMLPurifier();
	}
	return $purifier->purify($html, $config);
}

function getHTMLPurifierTikiConfig() {
	global $tikipath, $prefs;

	$d = $tikipath.'temp/cache/HTMLPurifierCache';
	if (!is_dir($d)) {
		if (!mkdir($d)) {
			$d = $tikipath.'temp/cache';
		}
	}
    $conf = HTMLPurifier_Config::createDefault();
    $conf->set('Cache.SerializerPath', $d);
    if ($prefs['feature_wysiwyg'] == 'y' || $prefs['popupLinks'] == 'y') {
        $conf->set('HTML.DefinitionID', 'allow target');
        $conf->set('HTML.DefinitionRev', 1);
    	$conf->set('HTML.Doctype', 'XHTML 1.0 Transitional');
    	$conf->set('HTML.TidyLevel', 'light');
        $def = $conf->getHTMLDefinition(true);
        $def->addAttribute('a', 'target', 'Enum#_blank,_self,_target,_top');
    }
	return $conf;
}

// vim: et sw=4 sts=4
