<?php
// (c) Copyright 2002-2016 by authors of the Tiki Wiki CMS Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require dirname(__FILE__) . '/svntools.php';

// Perform basic checks
if (! isset($_SERVER['argc']) || $_SERVER['argc'] != 2)
	error("Missing argument. Expecting branch to review as argument.\n\nExamples:\n\tbranches/experimental/plugin_ui");

$source = full($_SERVER['argv'][1]);
$trunk = full('trunk');

if (! is_experimental($source))
	error("This script is only valid to review experimental branches.");

$last = find_last_merge($source, $trunk);

if (! $last)
	error("Could not find previous merge.");

$eS = escapeshellarg($source);
$eT = escapeshellarg($trunk);
passthru("svn diff $eT@$last $eS");
