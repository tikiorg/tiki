<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
//
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

if (strpos($_SERVER["SCRIPT_NAME"], basename(__FILE__)) !== false) {
	header("location: index.php");
	exit;
}

/**
 * Somewhat work around https://dev.tiki.org/item6014
 * @param $installer
 */
function upgrade_optional_20170801_initialize_article_nbreads_tiki($installer)
{
	// Articles
	{
		$tikilib = TikiLib::lib('tiki');
		$minimumAbnormal = $tikilib->getOne('SELECT MIN(articleId) FROM tiki_articles WHERE nbreads IS NULL');
		$maximumNormal = $tikilib->getOne('SELECT MAX(articleId) FROM tiki_articles WHERE nbreads IS NOT NULL');
	if (is_null($minimumAbnormal)) {
		return true;
	}
	if (! is_null($maximumNormal) && $minimumAbnormal < $maximumNormal) {
		throw new Exception('Some articles with a regular counter were created after articles with an irregular counter. Please manually fix the fields if this is expected.');
	}
		$tikilib->query('UPDATE tiki_articles SET nbreads=0 WHERE nbreads IS NULL');
	$tikilib = TikiLib::lib('tiki');
	}

	// Submissions
	{
		$minimumAbnormal = $tikilib->getOne('SELECT MIN(subId) FROM tiki_submissions WHERE nbreads IS NULL');
		$maximumNormal = $tikilib->getOne('SELECT MAX(subId) FROM tiki_submissions WHERE nbreads IS NOT NULL');
	if (is_null($minimumAbnormal)) {
		return true;
	}
	if (! is_null($maximumNormal) && $minimumAbnormal < $maximumNormal) {
		throw new Exception('Some article submissions with a regular counter were created after article submissions with an irregular counter. Please manually fix the fields if this is expected.');
	}
		$tikilib->query('UPDATE tiki_submissions SET nbreads=0 WHERE nbreads IS NULL');
	}
}
