<?php
// (c) Copyright 2002-2010 by authors of the Tiki Wiki/CMS/Groupware Project
// 
// All Rights Reserved. See copyright.txt for details and a complete list of authors.
// Licensed under the GNU LESSER GENERAL PUBLIC LICENSE. See license.txt for details.
// $Id$

require dirname(__FILE__) . '/svntools.php';

// Perform basic checks
info("Verifying...");

if (! isset($_SERVER['argc']) || $_SERVER['argc'] != 2)
	error("Missing argument. Expecting branch to merge as argument.\n\nExamples:\n\tbranches/experimental/foobar");

$local = get_info('.');

if (! isset($local->entry))
	error("Local copy not found.");

$destination = $local->entry->url;

if (! is_trunk($destination))
	error("This script is likely not to be appropriate for this working copy. This script can be used in:\n\ttrunk");

$source = full($_SERVER['argv'][1]);

if (! is_experimental($source))
	error("The provided source cannot be used to update this working copy. Only experimental branches can be used.");

if (has_uncommited_changes('.'))
	error("Working copy has uncommited changes. Revert or commit them before merging a branch.");

$revision = (int) get_info($destination)->entry->commit['revision'];
$last = find_last_merge($source, $destination);
$sDest = short($destination);
$sSource = short($source);
if ($last !== $revision)
	error("You must branchupdate $sSource from $sDest before merging.");

// Proceed to update
info("Updating...");
update_working_copy('.');

// Do merge
info("Merging...");

incorporate($destination, $source);

important("After verifications, commit using a meaningful message for this feature.");

$conflicts = get_conflicts('.');
if ($conflicts->length > 0) {
	$message = "Conflicts occurred during the merge. Fix the conflicts and start again.";
	foreach ($conflicts as $path) {
		$path = $path->parentNode->getAttribute('path');
		$message .= "\n\t$path";
	}

	error($message);
}
